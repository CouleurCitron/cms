<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/*
sponthus 07/06/2005

gestion des gabarits	
*/
// 	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php'); semble inutile ici

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/gabarit.lib.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/procedures.php');

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newcontent_write.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newgabarit_write.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newpage_write.php');		

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


	// site sur lequel on est positionné
	$idSite = $_SESSION['idSite_travail'];
	if ($idSite == "") die ("Appel incorrect de la fonctionnalité");

		
	activateMenu('gestionpage');  //permet de dérouler le menu contextuellement
	
	$DIR_GABARITS = $_SERVER['DOCUMENT_ROOT']."/backoffice/cms/gabarits";

	$div_array = unserialize($_SESSION['page']);
	$generateFile = false;
	$error=0;
	if($_POST['mepNow']==true)
		$generateFile = true;
	else
	$generateFile = false;
	$id_page = 0;
	if(!$_SESSION['pageIsSaved']) {


	// objet cms_page
	$oPage = new Cms_page();
		
	// alimentation objet
	$oPage->setId_page($_GET['id']);
	$oPage->setId_site($idSite);

	// objet gabarit
	$oGab = new Cms_page($_GET['idGab']);
	
	if (DEF_BDD == "ORACLE" || DEF_BDD == "POSTGRES") {

		$datemep = date("Y/m/d/H:m:s");
		$datemep = explode('/', $datemep);
		$datemep = "to_date('".$datemep[2]."/".$datemep[1]."/".$datemep[0]."', 'dd/mm/yyyy')";

	} else if (DEF_BDD == "MYSQL") {

		$datemep = "str_to_date('".getDateNow()."', '%d/%m/%Y')";

	}

	$oPage->setName_page($_POST['nomPage']);
	// un gabarit est toujours créé à partir du gabarit de départ
	$oPage->setGabarit_page($oGab->getId_page());
	$oPage->setDatemep_page($datemep);
	$oPage->setIsgenerated_page(1); // a_voir sponthus
	$oPage->setValid_page(true);
	$oPage->setNodeid_page($_POST['nodeidPage']);
	//$oPage->setOptions_page($optionsPage);

	//$oPage->setHtml_page("to_char(".$db->qstr($content).")");
	$oPage->setIsgabarit_page($_POST['isgabarit_page']);

	$oPage->setWidth_page($_GET['widthGab']);
	$oPage->setHeight_page($_GET['heightGab']);

	// objet cms_infos_page
	$oInfos_page = new Cms_infos_page();
		
	// alimentation objet
	$oInfos_page->setPage_titre($_POST['pagetitle']);
	$oInfos_page->setPage_motsclefs($_POST['pagekeywords']);
	$oInfos_page->setPage_description($_POST['description']);
	$oInfos_page->setPage_thumb($_POST['pagethumb']);
		
// sauvegarde en BDD

//var_dump($div_array);

	if($_GET['id'] > 0)
		$id_page = savePage2($div_array, $oPage, $oInfos_page);
	else
		$id_page = savePage2($div_array, $oPage, $oInfos_page);

	$oPage->setId_page($id_page);

		if($id_page>0 and saveStruct($id_page, $div_array) and storeInfosPage($id_page, $oInfos_page)) 
		{
			// maj des zone editables noeud -2 => -1
			$divArray = buildDivArrayPreview($div_array);

			foreach($divArray as $k => $v) {
				if (is_int($k)) {
					$idContent=$v['id'];

					$oContent = new Cms_content($idContent);

					// si zone editable -> noeud -1
					if ($oContent->getIszonedit_content()) 	$oContent->updateNoeud(-1);
				}
			}
			
		$_SESSION['pageIsSaved'] = true;
?><link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<script src="/backoffice/cms/js/preview.js" type="text/javascript"></script>
	<body class="arbo"><ul>
	<li>Les paramètres de la page ont été correctement sauvegardée.</li>
<?php
		} else {
			$error++;
			$generateFile=false;
			die('<b>Un problème est survenu durant l\'enregistrement de la page.</b><br>');	
		}
		if ($generateFile) {
		// suppression de la creation de fichier
			/*$arrayresult = generateFile($oPage);
			//if (is_array($arrayresult)) {
?>
	<li>Le fichier <?php echo $arrayresult['filename']; ?> a été créé dans le dossier <?php echo $arrayresult['foldername']; ?>.</li>
<?php
		$sRep = getRepGabarit($oPage->getId_site());
?>
	<li>chemin complet : <b><?php echo $sRep.$arrayresult['filename']; ; ?></b></li><?php */?></ul>
	<a href="javascript:preview_gabarit(<?php echo  $oPage->getId_page() ; ?>)" class="arbo">Visualiser</a> le gabarit généré.
<?php

	// test des pages qui utilisent la brique modifiée.
	if( newSizeOf($oPage->getPagesFromGabarit()) > 0) {
?>
</span>
<hr align="center" width="80%" class="arbo2" height="1">
<br><br>
<span class="arbo"> La modification d'un gabarit utilisé dans des pages enregistrées nécessite une régénération des pages pour que les changements soient visibles.<br>
<br>
<?php
			$id_gabarit = $oPage->getId_page(); // Variable envoyée par effet de bord à regeneratePages.php
			include_once('../regeneratePages.php');
	}





			//}
		} else {
			/*if($error==0) {
?>
	<li>A votre demande, le fichier correspondant n'a pas été généré.</li>
	<li>La mise en production a été planifiée pour le <?php echo $_POST['dateMep']; ?> entre 00h01 et 06h00.</li></ul>
<?php
			} else {
				die('<b>Un problème est survenu durant l\'enregistrement de la page.</b><br>');
			}*/
		}
	} else {
		// la page a déjà été enregistrée...
?>
<?php
		$sDebut_Div = "<div style=\"font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 10px;\">";
		$sFin_Div = "</div>";
?>
<ul>
 <li>Le rafraîchissement peut provoquer des perturbations sur cette page. <br><br>
 Cette fonctionnalité est donc désactivée momentanément.</li>
</ul>
<?php
	}	
?>