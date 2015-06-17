<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	
*****************************************************
*** Ajout automatique et récursif d'une brique    ***
***                                               ***
*** Récupéré du fichier pageEditor6.php           ***
*****************************************************
*/

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
	activateMenu('gestionpage');  //permet de dérouler le menu contextuellement
	
	$DIR_GABARITS = $_SERVER['DOCUMENT_ROOT']."/backoffice/cms/gabarits";
	
	$serializedData = $_POST['serializedData'];
	$virtualpath = $_POST['v_comp_path'];

	if($serializedData=="" || $virtualpath=="")
		die('<b>Un problème est survenu, il manque des informations (arguments manquants).</b><br />');

	function unserialize_JS($datatxt) { // décomprime un chaine comprimée en JavaScript
		$divArray = array();
		foreach (split('##', $datatxt) as $k => $v ) {
			if ($k==0)
				$v = preg_replace('/^#/','',$v);
			if ($k==(sizeof(split('##', $datatxt))-1))
				$v = preg_replace('/#$/','',$v);
			$t = split('#',$v);
			$id = $t[0];
			$attr = $t[1];
			$tmparray = array(
				'id' => $id
			);
			foreach(split(',',$attr) as $kk => $vv) {
				$t = split('=',$vv);
				if(strlen($t[0]) >0)
					$tmparray[$t[0]] = $t[1];
				if(strlen($t[2]) >0)
					$tmparray[$t[0]] = $t[1].'='.$t[2];
			}
			$divArray[$k] = $tmparray;
		}
		return $divArray;
	}


	function modif_page($id_page,$new_brique) {
	// Avec l'id page fourni et la nouvelle brique
	// on ajoute à la page existante la nouvelle brique
	// cette modif se fait dans la base et dans le fichier physique
		$div_array = getPageStructure($id_page);
		$infopage=getPageById($id_page);
		// Rajoute le contenu HTML
		$new_brique['content']=getComposantById($new_brique['id']);
		// Ajustement des paramètres de la brique (supprime les px)
		$new_brique['width'] = preg_replace("/[^0-9]/","",$new_brique['width']);
		$new_brique['height'] = preg_replace("/[^0-9]/","",$new_brique['height']);

		// Si la brique existe déjà dans la page, on en rajoute pas!
		$trouve = false;
		foreach($div_array as $key => $val) {
			if($val['id']==$new_brique['id']) {
				$trouve=true;
				break;
			}
		}
		if($trouve)	return;
		else array_unshift($div_array,$new_brique);

		$id_page = savePage($div_array,$infopage['node_id'],$infopage['name'],"","true","true",null,$id_page,$div_array['titre'],$div_array['motscles'],$div_array['description']);
		if($id_page>0 && saveStruct($id_page,$div_array) && storeInfosPage($id_page, $div_array['titre'], $div_array['motscles'], $div_array['description'], "")) {

		//saveStruct($id_page,$divArray)
		//if(updatePage($div_array,$div_array['titre'],$div_array['motscles'],$div_array['description'],$id_page)) {
		
				// enregistrement en base OK
				$generateFile = true;
?><link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
	<body class="arbo">
	<li>Les paramètres de la page <?php echo  $div_array['titre'] ; ?> ont été correctement sauvegardée.</li>
<?php
			} else {
				$error++;
				$generateFile=false;
				die('<b>Un problème est survenu durant l\'enregistrement de la page '.$div_array['titre'].'.</b><br />');	
			}
			if ($generateFile) { // Génération du fichier physique
				$arrayresult = generateFile($id_page,$div_array['titre'], $div_array['motscles'], $div_array['description']);
				if (is_array($arrayresult)) {
?>
	<li>Le fichier <?php echo $arrayresult['filename']; ?> a été créé dans le dossier <?php echo $arrayresult['foldername']; ?>.</li>
	<li>chemin complet : <b><?php echo $_SERVER['DOCUMENT_ROOT']."/content".$arrayresult['path'].$arrayresult['filename']; ?></b></li></ul>
	<a href="/content<?php echo $arrayresult['path'].$arrayresult['filename']; ?>" target="_blank" class="arbo">Visualiser</a> la page générée.
<?php
				}
			} else {
				// On tente de continuer meme si erreur sur ce fichier
				echo '<b>Un problème est survenu durant l\'enregistrement de la page '.$div_array['titre'].'.</b><br />';
			}
	}

	function getPageListRecursDir($VPath) {
	// Retourne tous les ID des pages du dossier courant et de ses sous dossiers
		global $db;
		$childs = getNodeChildren($db,$VPath);
		$Arraychilds = Array();
		if (count($childs)!=0) {
			// Il existe encore des sous répertoires
			// => on relance la fonction pour chaque enfant
			foreach($childs as $key => $child) {
				$pagelistchild = getPageListRecursDir($VPath.",".$child['id']);
				if($pagelistchild)
					$Arraychilds = array_merge( $Arraychilds, $pagelistchild );
			}
		}
		$listpages = Array();
		$listpages = getFolderPages($VPath);
		$listpagesid = Array();
		if($listpages) {
			foreach($listpages as $k => $page) {
				array_push($listpagesid, $page['id']);
			}
		}
		return  array_merge( $listpagesid , $Arraychilds);
	}
	
	// Récupération des pages du dossier en cours
	$listpages = getPageListRecursDir($virtualpath);
	$brique = array_pop(unserialize_JS($serializedData));

	if($listpages) {
		foreach($listpages as $k => $page_id) {
			modif_page($page_id,$brique);
		}
	}
	else {
		die('<b>Problème: le dossier sélectionné ne contient pas de pages!!</b><br />');	
	}
?>