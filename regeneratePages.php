<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 
/*
	$Id: regeneratePages.php,v 1.1 2013-09-30 09:24:15 raphael Exp $
	$Author: raphael $

	$Log: regeneratePages.php,v $
	Revision 1.1  2013-09-30 09:24:15  raphael
	*** empty log message ***

	Revision 1.18  2013-03-25 14:04:09  pierre
	*** empty log message ***

	Revision 1.17  2013-03-01 10:28:04  pierre
	*** empty log message ***

	Revision 1.16  2012-07-31 14:24:48  pierre
	*** empty log message ***

	Revision 1.15  2012-04-13 07:33:12  pierre
	*** empty log message ***

	Revision 1.14  2012-04-13 07:18:38  pierre
	*** empty log message ***

	Revision 1.13  2012-04-12 18:05:29  pierre
	*** empty log message ***

	Revision 1.12  2011-03-15 13:37:44  pierre
	*** empty log message ***

	Revision 1.11  2010-06-04 15:40:25  pierre
	*** empty log message ***

	Revision 1.10  2010-03-08 12:10:28  pierre
	syntaxe xhtml

	Revision 1.9  2009-09-24 08:53:12  pierre
	*** empty log message ***

	Revision 1.8  2008-11-28 15:14:19  pierre
	*** empty log message ***

	Revision 1.7  2008-07-30 12:38:50  thao
	*** empty log message ***

	Revision 1.6  2008-07-16 10:48:36  pierre
	*** empty log message ***

	Revision 1.5  2007/11/15 18:08:49  pierre
	*** empty log message ***
	
	Revision 1.6  2007/11/06 15:33:25  remy
	*** empty log message ***
	
	Revision 1.4  2007/09/03 15:32:23  thao
	*** empty log message ***
	
	Revision 1.3  2007/09/03 12:41:06  thao
	*** empty log message ***
	
	Revision 1.2  2007/08/08 14:55:16  thao
	*** empty log message ***
	
	Revision 1.1  2007/08/08 14:26:20  thao
	*** empty log message ***
	
	Revision 1.4  2007/08/08 14:16:14  thao
	*** empty log message ***
	
	Revision 1.3  2007/08/08 13:56:04  thao
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

// $id est l'id de la brique qui vient d'être modifiée. Elle vient de $_GET['id']

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');


	// pas de regénération demandée
	if(!($id > 0 || $id_gabarit!="" || sizeof($aIdGab))) exit("");


	// REGENERATION :: 2 cas :: on regénère à la demande de :
	//
	// 1. la modif d'un gabarit
	// 		cas simple : on fait la liste des pages qui dépendent de lui et on génère
	// 2. la modif d'une brique fixe
	//		cas compliqué : il faut d'abord générer le gabarit contenant cette brique fixe
	// 		puis générer la page car sinon la page toute seule ne contient pas la brique fixe modifiée 
	//		(elle est dans le gabarit)

	// dans tous les cas on ne génère que les pages qui ont une version en ligne (dans CMS_ARCHI_CONTENT)
	// ET on génère avec le contenu de CMS_ARCHI_CONTENT et non pas celui de CMS_CONTENT
	$bDebug=0;
	if (!sizeof($aIdGab) && $id_gabarit==""){ 
		$listPage = getPageUsingComposant($idSite, $id); 
		$aIdGab = array();
		foreach($listPage as $k => $idPage) {
			$oPage = new Cms_page($idPage);
			if ($oPage->getIsgabarit_page() == 0) {
				$idGabarit = $oPage->getGabarit_page();
				if (!in_array ($idGabarit, $aIdGab)) {
					$aIdGab[] = $idGabarit;
				}
			}
		}
		if ($bDebug) print("<br />3");		
	}

	
	if (sizeof($aIdGab)) { // on doit trouver toutes les pages liées à X gabarits
		$listPage = getPagesFromXGabarits($aIdGab, $idSite);
		if ($bDebug) print("<br />1");		
	} else if($id_gabarit!="") { // on doit trouver les pages liées à un gabarit fraîchement modifié
		$oGab = new Cms_page($id_gabarit);
		$listPage = $oGab->getPagesFromGabarit();
		if ($bDebug) print("<br />2");		
	}
	else { // on doit trouver toutes les pages et gabarits liés à la brique fraîchement modifiée
		$listPage = getPageUsingComposant($idSite, $id); 
		if ($bDebug) print("<br />3");		
	}
	 
	// pas de pages à regénérer
	if(!(is_array($listPage) && sizeof($listPage))) exit("");
	
	
	// pages liées
	$aPageToGenerateAfterGabarit = array();
		
	foreach($listPage as $k => $idPage) {
		// si c'est un gabarit, on cherche les pages liées
		// et on les mémorise => à régénérer plus tard
		$oPage = new Cms_page($idPage);
			
		if ($oPage->getIsgabarit_page() == 1) {
			$aPagesliees = $oPage->getPagesFromGabarit();
			if($aPagesliees) $aPageToGenerateAfterGabarit = array_merge($aPageToGenerateAfterGabarit, $aPagesliees);
		}
	}
	 

	$listAllPage_all = array_unique(array_merge($listPage, $aPageToGenerateAfterGabarit));
 

//var_dump($listAllPage_all);

	// ne prendre que les pages qui peuvent être générées
	// cad celles qui ont des contenus en ligne (une version dans cms_archi_content)
	$listAllPage_page = array();
	$listAllPage_gab = array();
	foreach($listAllPage_all as $idpage) {
		$oPage = new Cms_page($idpage);
		// la page est à REgénérer si elle existe déjà dans une version en ligne OU si c'est un gabarit
		// rem : si toutes ses briques sont pretes elle a déjà été mise en ligne...
		// la GEnération n'arrive qu'à la gestion des statuts quand on passe au statut en ligne		
		// le RE-génération arrive à la maj d'une brique, la maj d'une page ou la maj d'un gabarit

		if ($oPage->getExisteligne_page() == 1) {
	
			$listAllPage_page[] = $idpage;
		}
		if ($oPage->getIsgabarit_page()) {
	
			$listAllPage_gab[] = $idpage;
		}
	} 
	// attention les gabarits sont à générer D'ABORD
	// car les pages sont générées en loadant le gabarit (qui doit avoir été mis à jour avant donc) 
	$listAllPage = array_unique(array_merge($listAllPage_gab, $listAllPage_page)); 
	
	
	// la liste des pages finale est épurée des pages à ne pas regénérer
	if (sizeof($listAllPage)) {

?>
<link href="/backoffice/cms/css/bo.css" rel="stylesheet" type="text/css">
<script src="/backoffice/cms/js/validForm.js" type="text/javascript"></script>
<script type="text/javascript">
var unecasecochee=false;

function verif_case() {
	unecasecochee=false
	for(var i=0;i<document.updatePage.elements.length;i++) {
		unecasecochee=document.updatePage.elements[i].checked
		if(unecasecochee) break;
	}
}

function alert_case() {
	verif_case()
	if(unecasecochee) document.updatePage.submit()
	else alert("Vous n'avez pas sélectionné de page à régénérer")
}

function stripAll() {
	for(var i=0;i<document.updatePage.elements.length;i++) {
		if (document.updatePage.elements[i].name!='checkAll') {
			document.updatePage.elements[i].checked=document.updatePage.checkAll.checked;
		}
	}
}

</script>

<form name="updatePage" method="post" action="/backoffice/cms/updatePageFromBrique.php">
<span class="arbo"><strong><u><?php $translator->echoTransByCode('Selectionner_les_pages_a_regenerer'); ?></u>
</strong></span>
<ul>
<li class="arbo">
<input type="checkbox" name="checkAll" id="checkAll" title="<?php $translator->echoTransByCode('Cocher_decocher'); ?>" onclick="stripAll();" checked />&nbsp;&nbsp;&nbsp;&nbsp;<?php $translator->echoTransByCode('Cocher_decocher'); ?>
<?php
		$Pages = "";
 
		foreach($listAllPage as $idpage) {
			$page = getPageById($idpage);
			$oCms_page = new Cms_page($idpage);

			$urlDisplay = ( ($oCms_page->getIsgabarit_page()!="1") ? $oCms_page->getPageDirectory() : "" ) .$oCms_page->getName_page();
			$url = rawurlencode($urlDisplay);
			$url = preg_replace('/\%2F/','/',$url);

			if($Pages=="") $Pages=$idpage;
			else $Pages.=",".$idpage;

?>
</li>

<li class="arbo">
  <input type="checkbox" name="togenerate[]" id="togenerate[]"  value="<?php echo $idpage; ?>" onclick="verif_case()" checked />
  &nbsp;&nbsp;&nbsp;&nbsp;<?php echo  ($oCms_page->getIsgabarit_page()==1) ? $translator->echoTransByCode('Gabarit') : "Page" ?><?php if(in_array($idpage,$aPageToGenerateAfterGabarit)&&!in_array($idpage,$listPage)) echo " liée" ?>&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo $page['titre']; ?></b>&nbsp;(&nbsp;<?php echo  $urlDisplay ; ?>.php&nbsp;)&nbsp;&nbsp;&nbsp;<a href="<?php echo  ($oCms_page->getIsgabarit_page()==1) ? "/backoffice/cms/site/previewGabarit.php?id=".$idpage : "/content".$url.".php" ?>" target="_blank" class="arbo"> <?php echo  ($oCms_page->getIsgabarit_page()==1) ? $translator->echoTransByCode('Voir_le_gabarit') : $translator->echoTransByCode('Voir_la_page') ?></a></li>
<span class="arbo">
<?php
		} // fin foreach($listAllPage as $idpage) {
?>
</span>
</ul>
<input type="hidden" name="pageDisplayed" id="pageDisplayed" value="<?php echo $Pages;?>" />
<input type="hidden" name="connectSite" id="connectSite" value="<?php echo $_SESSION['idSite_travail'];?>" />

<input name="envoyer" id="boutonvalid" type="button" class="arbo" value="<?php $translator->echoTransByCode('Regenerer_les_pages_selectionnees'); ?>" onclick="alert_case()">
</form>
<?php
	} // fin if (sizeof($listAllPage)) {

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/append.php');
?>