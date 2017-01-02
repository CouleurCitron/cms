<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: updatePageFromBrique.php,v 1.1 2013-09-30 09:24:16 raphael Exp $
	$Author: raphael $

	$Log: updatePageFromBrique.php,v $
	Revision 1.1  2013-09-30 09:24:16  raphael
	*** empty log message ***

	Revision 1.17  2013-03-01 10:28:04  pierre
	*** empty log message ***

	Revision 1.16  2012-07-31 14:24:48  pierre
	*** empty log message ***

	Revision 1.15  2012-04-13 07:33:12  pierre
	*** empty log message ***

	Revision 1.14  2012-04-12 18:05:29  pierre
	*** empty log message ***

	Revision 1.13  2012-03-30 15:40:14  pierre
	*** empty log message ***

	Revision 1.12  2011-07-04 15:52:20  pierre
	*** empty log message ***

	Revision 1.11  2011-04-01 15:32:59  pierre
	*** empty log message ***

	Revision 1.10  2011-03-15 15:14:19  pierre
	*** empty log message ***

	Revision 1.9  2011-01-06 15:23:37  pierre
	*** empty log message ***

	Revision 1.8  2008-11-06 12:03:53  pierre
	*** empty log message ***

	Revision 1.7  2008-07-16 10:48:36  pierre
	*** empty log message ***

	Revision 1.6  2007/11/15 18:08:49  pierre
	*** empty log message ***
	
	Revision 1.7  2007/11/06 15:33:25  remy
	*** empty log message ***
	
	Revision 1.5  2007/09/03 15:40:11  thao
	*** empty log message ***
	
	Revision 1.4  2007/08/28 15:55:28  pierre
	*** empty log message ***
	
	Revision 1.3  2007/08/24 15:12:13  pierre
	*** empty log message ***
	
	Revision 1.2  2007/08/08 14:55:16  thao
	*** empty log message ***
	
	Revision 1.1  2007/08/08 14:26:20  thao
	*** empty log message ***
	
	Revision 1.5  2007/08/08 14:16:14  thao
	*** empty log message ***
	
	Revision 1.4  2007/08/08 13:56:04  thao
	*** empty log message ***
	
	Revision 1.3  2007/08/07 10:24:58  thao
	*** empty log message ***
	
	Revision 1.1  2006/12/15 12:30:11  pierre
	*** empty log message ***
	
	Revision 1.1.1.1  2006/01/25 15:14:32  pierre
	projet CCitron AWS 2006 Nouveau Website
	
	Revision 1.2  2005/10/28 07:53:14  sylvie
	*** empty log message ***
	
	Revision 1.1.1.1  2005/10/20 13:10:53  pierre
	Espace V2
	
	Revision 1.1.1.1  2005/04/18 13:53:29  pierre
	again
	
	Revision 1.1.1.1  2005/04/18 09:04:21  pierre
	oremip new
	
	Revision 1.1.1.1  2004/11/03 13:49:54  ddinside
	lancement du projet - import de adequat
	
	Revision 1.3  2004/06/16 15:23:19  ddinside
	inclusion corrections
	
	Revision 1.2  2004/04/26 08:07:09  melanie
	*** empty log message ***
	
	Revision 1.1.1.1  2004/04/01 09:20:27  ddinside
	Création du projet CMS Couleur Citron nom de code : tipunch
	
	Revision 1.1  2004/02/12 15:56:16  ddinside
	mise à jour plein de choses en fait, mais je sais plus quoi parce que ça fait longtemps que je l'avais pas fait.
	Mea Culpa...
	

*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newcontent_write.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newgabarit_write.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newpage_write.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/gabarit.lib.php');

activateMenu('gestionpage');  //permet de dérouler le menu contextuellement
$generationIsOk = true;

unset($_SESSION['BO']['CACHE']);

function RegeneratePages($aPages) {

$result = array();

	foreach($aPages as $k => $idPage) {
		// on regénère chacune des pages cochée précédemment.

		$oPage = new Cms_page($idPage);
		
		
		$oInfos_page = new Cms_infos_page();
		$oInfos_page->getCmsinfospages_with_pageid($oPage->id_page);

		// site de la page
		$oCms_site = new Cms_site($oPage->getId_site());

		$result[$idPage] = array(
			'generated' => 'Génération OK',
			'id_page' => $idPage,
			'node_id' => $oPage->getNodeid_page(),
			'name' => $oPage->getName_page(),
			'titre' => stripTitre($oInfos_page->getPage_titre()),
			'isgabarit_page' => $oPage->getIsgabarit_page(),
			
			// si la page est à la racine d'un mini site
			// son chemin est minisite/ et non /
			'url_base_page' => $oPage->getPageDirectory()
		);
		
		if ($oPage->get_iscustom()==0){
			$oPage->cms_page_regenerate();
			
			if( ! $oPage->cms_page_write() ){
				$generationIsOk = false;
				$result[$idPage]['generated'] = 'ERREUR';
			}
		}
		else{
			$generationIsOk = false;
			$result[$idPage]['generated'] = 'PAGE CUSTOM';
			
			if ($oPage->get_isgabarit_page()==1){ // gabarit custom: on charge la page physique dans la base
				if (is_file($_SERVER['DOCUMENT_ROOT'].'/custom/gabarit/'.$_SESSION['rep_travail'].'/'.$oPage->get_name_page().'.php')){
					$sSrc = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/custom/gabarit/'.$_SESSION['rep_travail'].'/'.$oPage->get_name_page().'.php');
					$oPage->set_html_page($sSrc);
					dbSauve($oPage);
				}			
			}
		}


		
	}
	
	return $result;

}

$idSite = $_SESSION['idSite_travail'];
$DIR_GABARITS = getRepGabarit($idSite);

if (isset($_POST['pageDisplayed']) || isset($_GET['pageDisplayed'])) {
	
	if (isset($_GET['pageDisplayed'])) {
		$pageDisplayed =  $_GET['pageDisplayed'];
		$pageToGenerate = array();
		$pageToGenerate[] = $_GET['pageDisplayed'];
	}
	else  {
		$pageToGenerate = $_POST['togenerate'];
		$pageDisplayed =  $_POST['pageDisplayed'];
		$pageDisplayed = split(',',$_POST['pageDisplayed']);
	}
	
		

	// Génération des gabarits et pages sélectionnées
	$result = RegeneratePages($pageToGenerate);

?>
<link href="/backoffice/cms/css/bo.css" rel="stylesheet" type="text/css">
<body class="arbo">
<span class="arbo"><u><b>Fichiers regénérés&nbsp;:&nbsp;</b></u>
</span>
<ul>
<?php
	foreach ($result as $k => $aPageRegenere) {

		$urlDisplay = ( ($aPageRegenere['isgabarit_page']!="1") ? $aPageRegenere['url_base_page'] : "" ) .$aPageRegenere['name'];
		$url = rawurlencode($urlDisplay);
		$url = preg_replace('/\%2F/','/',$url);
?>
  <li>
  <?php echo ($aPageRegenere['isgabarit_page']=="1") ? "Gabarit : " : "Page : "; ?>
  [<?php echo $aPageRegenere['generated']; ?>] &lt;= <a href="<?php echo  ($aPageRegenere['isgabarit_page']==1) ? "/backoffice/cms/site/previewGabarit.php?id=".$aPageRegenere['id_page'] : "/content".$url.".php" ?>" target="_blank" class="arbo"><?php echo $aPageRegenere['titre']; ?>&nbsp;(<?php echo $urlDisplay.'.php'; ?>)</a></li>
<?php
	}
?>
</ul>
<?php if (!isset($_GET['pageDisplayed'])) { ?>
<u><b class="arbo">Fichiers non regénérés&nbsp;:&nbsp;</b></u>
<ul>
<?php
	foreach ($pageDisplayed as $k=> $pageId) {
		if (!in_array($pageId,$pageToGenerate)) {
			$page = getPageById($pageId);
			$folder = getFolderInfos($page['node_id']);
			
			if($folder["id"]=="0") // C'est la racine => ajout du minisite
			$folder["path"]= $folder["path"].$_SESSION['site_travail']."/";
			
			$urlDisplay = ( ($page['isgabarit_page']!="1") ? $folder['path'] : "" ) .$page['name'];			
			$url = rawurlencode($urlDisplay);
			$url = preg_replace('/\%2F/','/',$url);
			
?>
 <li>
<?php
echo ($page['isgabarit_page']=="1") ? "Gabarit : " : "Page : ";
echo $page['titre']; ?>&nbsp;(<?php echo $urlDisplay.'.php'; ?>)&nbsp;&nbsp;<small><a href="<?php

if($page['isgabarit_page']=="1")
	echo "site/gabaritModif.php?id=".$pageId;
else
	echo "pageModif.php?id=". $pageId. "&idGab=". $page['gabarit']; 
 
 ?>" target="_blank" class="arbo">Edition manuelle</a></small>&nbsp;|&nbsp;<small><a href="<?php echo  ($page['isgabarit_page']==1) ? "/backoffice/cms/site/previewGabarit.php?id=".$pageId : "/content".$url.".php" ?>" target="_blank" class="arbo">Visualiser</a></small>
<?php
		}
	}
?>
</ul>
<?php
}
else { // si la génération se fait via modif d'infos de pages
?><script type="text/javascript">
document.location.href="arboPage_browse.php";
</script>	
<?php
	}
}
?>