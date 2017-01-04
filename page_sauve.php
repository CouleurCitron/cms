<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/gabarit.lib.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/procedures.php');

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newcontent_write.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newgabarit_write.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newpage_write.php');

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
 include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


	activateMenu('gestionpage');  //permet de dérouler le menu contextuellement


	// gabarit
	$oGab = new Cms_page($_GET['idGab']);


	/////////////////////
	// div array
	// rendre le code plus propre un de ces 4 là c illisible...

	$divArray = array();
	$datatxt = $_POST['serializedData'];
	
	foreach (explode('##', $datatxt) as $k => $v ) {
		if ($k==0)
			$v = preg_replace('/^#/','',$v);
		if ($k==(sizeof(explode('##', $datatxt))-1))
			$v = preg_replace('/#$/','',$v);
		$t = explode('#',$v);
		$id = $t[0];
		$attr = $t[1];
		$tmparray = array(
			'id' => $id
		);
		foreach(explode(',',$attr) as $kk => $vv) {
			$t = explode('=',$vv);
			if(strlen($t[0]) >0)
				$tmparray[$t[0]] = $t[1];
			if(strlen($t[2]) >0)
				$tmparray[$t[0]] = $t[1].'='.$t[2];
		}
		$divArray[$k] = $tmparray;
	}
	$divArray['gabarit'] = $oGab->getId_page();
	$divArray['valid'] = 1;
	$div_array=$divArray;
	/////////////////////

	// site sur lequel on est positionné
	$idSite = $_SESSION['idSite_travail'];
	if ($idSite == "") die ("Appel incorrect de la fonctionnalité");
	$oSite = new Cms_site($idSite);
	
	// affichage du site de travail
	putAfficheSite();

	$generateFile = false;
	$error=0;

// a voir sponthus
// ici, génération automatique

	$_POST['mepNow'] = true;

	if($_POST['mepNow'] == true)
		$generateFile = true;
	else
	$generateFile = false;
	$id_page = 0;

	if(!$_SESSION['pageIsSaved']) 
	{

		// cms_page
		//====================
		$oPage = new Cms_page();
	
		$oPage->setName_page($_POST['nomPage']);
		$oPage->setGabarit_page($div_array['gabarit']);
		$oPage->setIsgenerated_page($generateFile);
		$oPage->setValid_page(true);
		$oPage->setNodeid_page($_POST['nodeidPage']);
		$oPage->setIsgabarit_page(0);
		$oPage->setWidth_page($oGab->getWidth_page());
		$oPage->setHeight_page($oGab->getHeight_page());

		if (DEF_BDD == "ORACLE" || DEF_BDD == "POSTGRES") {
	
			$datemep = date("Y/m/d/H:m:s");
			$datemep = explode('/', $datemep);
			$datemep = "to_date('".$datemep[2]."/".$datemep[1]."/".$datemep[0]."', 'dd/mm/yyyy')";
	
		} else if (DEF_BDD == "MYSQL") {
	
			$datemep = "str_to_date('".getDateNow()."', '%d/%m/%Y')";
		}
		$oPage->setDateadd_page($datemep);
		$oPage->setDatemep_page($datemep);
		$oPage->setDateupd_page('');
		$oPage->setDatedlt_page('');
		$oPage->setOptions_page('');
	
		$oPage->setId_site($idSite);
		// par défaut à la création de la page aucun de ses contenus n'est en ligne
		$oPage->setToutenligne_page(0);

		// cms_infos_pages
		//====================
			
		$oInfopage = new Cms_infos_page();
	
		$oInfopage->setPage_titre($_POST['pagetitle']);
		$oInfopage->setPage_motsclefs($_POST['pagekeywords']);
		$oInfopage->setPage_description($_POST['description']);		


		// sauvegarde de la page
		if($_GET['id'] > 0) {
			$oPage->setId_page($_GET['id']);
		}


		// sauvegarde de la page
		// savePage2 fait un generatePage qui remplace le contenu des divarray par les contenus EN LIGNE
		$id_page = savePage2($div_array, $oPage, $oInfopage);

		// réobtention de l'objet page après savePage2
		$oPage = new Cms_page($id_page);

		// taille du div_array pour cette page
		// ensemble des briques de cette page
		// le nombre de briques du divarray est sizeof(div_array[0]['id'])
		if ($div_array[0]['id'] != "") $eIdDivarray = 1;
		else $eIdDivarray = 0;
		
		// sauvegarde de la structure
		if ($eIdDivarray > 0) $bSaveStruct = saveStruct($id_page, $div_array);
		else $bSaveStruct = true;
		
		// sauvegarde des infos de la page
		$bStorePage = storeInfosPage($id_page, $oInfopage);

		// maj de l'attribut "tout en ligne"
		$bToutenligne_page = updateToutenligne($oPage->getId_page());
		$oPage->setToutenligne_page($bToutenligne_page);

		// maj de l'attribut "existe version ligne"
		$bExisteligne_page = updateExisteligne($oPage->getId_page());
		$oPage->setExisteligne_page($bExisteligne_page);

		// maj briques editables + génération du fichier
		if ($id_page > 0 and $bSaveStruct and $bStorePage) 
		{

			// si le div_array de cette page n'est pas vide
			if ($eIdDivarray > 0) {

				// maj des briques editables noeud -2 => node page
				$divArray = buildDivArrayPreview($div_array);
	
				foreach($divArray as $k => $v) {
					if (is_int($k)) {

						$idContent=$v['id'];
	
						$oContent = new Cms_content($idContent);

						// si on est sur une brique éditable
						if ($oContent->getIsbriquedit_content() == 1) {
						
							// Rajout d'un cas particulier lié à l'édition d'une page
							// si on a une brique éditable dont il faut réinitialiser le contenu HTML
							// avec le contenu HTML de la zone éditable

							if($v["HTMLdefaut"]!="") {
	
								$oContentHTMLDefault = new Cms_content($v["zonedit"]);
								$oContent->setHtml_content( $oContentHTMLDefault->getHtml_content() );
								$oContent->updateHTML();
							}

							// les briques editables créées sont pour l'instant à noeud -2
							if ($oContent->getNodeid_content() == -2) {
							
								// si brique editable -> noeud de la page
								$oContent->updateNoeud($oPage->getNodeid_page());
		
								// maj du nom de la brique editable
								// renommage
								updateNomBriqueEdit($oContent->getId_content(), $oPage->getId_page());
		
							} // fin  noeud
							
						} // fin test brique editable
					}
				}

				// enregistrement des briques editables effacées noeud => -2
				$_POST['BeToSave'] = preg_replace("/^[;]*/","",$_POST['BeToSave']);
				foreach(split(";",$_POST['BeToSave']) as $k => $v) { 
					if ($v!="") {
	
						$oContent = new Cms_content($v);
	
						// si brique editable -> -2
						// Ce sont des briques éditables qui ont été remplacées par des fixes
						$oContent->updateNoeud(-2);
					}
				}

				$_SESSION['pageIsSaved'] = true;
			
?><link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
	<body class="arbo"><ul>
	<li>Les paramètres de la page ont été correctement sauvegardée.</li>
<?php
			} // Fin divarray=0
			
		} else { // PB maj briques editables + génération du fichier
			$error++;
			$generateFile=false;
			die('<b>Un problème est survenu durant l\'enregistrement de la page.</b><br />');	
		}

		//----------------------------------
		// génération du fichier
		if ($generateFile) {

			// teste si toutes les briques sont en ligne
			$bTousEnLigne = $oPage->getToutenligne_page();

			if ($bTousEnLigne) {
				// PAGE GENEREE
				$arrayresult = generateFile($oPage);

				$sMessage_generate = "La page a été générée car tous ses contenus sont en ligne";
			} else {
				// PAGE NON GENEREE			
				$sMessage_generate = "<font color=red>La page n'a pas été générée car tous ses contenus ne sont pas en ligne</font>";
				$sMessage_generate.= "<br />Si vous souhaitez que cette page soit générée, allez dans le menu 'Gestion des contenus'";
				$sMessage_generate.= "<br />Lorsque tous les contenus de cette page seront en ligne, la page sera générée";
			}
			//----------------------------------
			
			// message de génération
			if (is_array($arrayresult)) {

			$sUrl = getRepPage($oPage->getId_site(), $oPage->getNodeid_page(), $arrayresult['path']);
?>
	<br /><br />
	<li><?php echo $sMessage_generate; ?></li>
	<br /><br />
	<li>Le fichier <?php echo $arrayresult['filename']; ?> a été créé dans le dossier <?php echo $arrayresult['foldername']; ?>.</li><br /><br />
	<li>chemin complet : <b><?php echo $_SERVER['DOCUMENT_ROOT']."/content".$sUrl.$arrayresult['filename']; ?></b></li></ul>

<?php
			} else {
				// message de non génération
?>	
	<br /><br />
	<li><?php echo $sMessage_generate; ?></li>
<?php
			}
			
			// visualiser la page : version de brouillon et version en ligne
?>
	<br /><br />
	Visualiser la page <?php echo $oPage->getName_page(); ?>.php
<?php			
global $db;
$aNodeInfos = getNodeInfos($db, $oPage->getNodeid_page());
$sUrl = getRepPage($oPage->getId_site(), $oPage->getNodeid_page(), $aNodeInfos['path']);

// affichage des icones de preview des pages
$idContent = -1;
$sNature = "CMS_CONTENT";
$idPage = $oPage->getId_page();
$sNomPage = $oPage->getName_page();
$bToutenligne_page = $oPage->getToutenligne_page();
$bExisteligne_page = $oPage->getExisteligne_page();
$sUrlPageLigne = "/content".$sUrl.$oPage->getName_page().".php";

afficheIconePreviewPage($idContent, $sNature, $idPage, $sNomPage, $bToutenligne_page, $bExisteligne_page, $sUrlPageLigne);

		} else {

			if($error==0) {
?>
	<li>A votre demande, le fichier correspondant n'a pas été généré.</li>
	<li>La mise en production a été planifiée pour le <?php echo $_POST['dateMep']; ?> entre 00h01 et 06h00.</li></ul>
<?php
			} else {
				die('<b>Un problème est survenu durant l\'enregistrement de la page.</b><br />');
			}
		}
		//----------------------------------
		
	} else {
		// la page a déjà été enregistrée...
?>
<ul>
 <li>Le rafraîchissement peut provoquer des perturbations sur cette page. Cette fonctionnalité est donc désactivée momentanément.</li>
</ul>
<?php
	}	
	
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/append.php');
?>