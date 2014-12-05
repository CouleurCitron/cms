<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	
*****************************************************
*** Régénération automatique de pages             ***
***                                               ***
*** Récupéré du fichier pageAutoEditor4.php       ***
*****************************************************
*/

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
	activateMenu('gestionpage');  //permet de dérouler le menu contextuellement
	
	$DIR_GABARITS = $_SERVER['DOCUMENT_ROOT']."/backoffice/cms/gabarits";
	
	$virtualpath = $_POST['v_comp_path'];

	if($virtualpath=="")
		die('<b>Un problème est survenu, il manque des informations (arguments manquants).</b><br />');


	function modif_page($id_page) {
	// Avec l'id page fourni on régénère la page
	// cette modif se fait dans la base et dans le fichier physique
		$div_array = getPageStructure($id_page);
		$infopage = getPageById($id_page);

		$id_page = savePage($div_array,$infopage['node_id'],$infopage['name'],"","true","true",null,$id_page,$div_array['titre'],$div_array['motscles'],$div_array['description']);
		if($id_page>0 && saveStruct($id_page,$div_array) && storeInfosPage($id_page, $div_array['titre'], $div_array['motscles'], $div_array['description'], "")) {

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

	if($listpages) {
		foreach($listpages as $k => $page_id) {
			modif_page($page_id);
		}
	}
	else {
		die('<b>Problème: le dossier sélectionné ne contient pas de pages!!</b><br />');	
	}
?>