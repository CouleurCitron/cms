<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/*
	$Author: pierre $
	$Id: pageLiteEditor5.php,v 1.3 2014-07-16 13:06:01 pierre Exp $

	$Log: pageLiteEditor5.php,v $
	Revision 1.3  2014-07-16 13:06:01  pierre
	*** empty log message ***

	Revision 1.2  2013-10-07 15:09:51  quentin
	*** empty log message ***

	Revision 1.1  2013-09-30 09:43:16  raphael
	*** empty log message ***

	Revision 1.8  2013-03-01 10:28:20  pierre
	*** empty log message ***

	Revision 1.7  2012-12-12 10:45:04  pierre
	*** empty log message ***

	Revision 1.6  2012-12-12 10:26:48  pierre
	*** empty log message ***

	Revision 1.5  2012-07-31 14:24:50  pierre
	*** empty log message ***

	Revision 1.4  2011-07-04 14:01:32  pierre
	*** empty log message ***

	Revision 1.3  2010-11-26 14:05:12  pierre
	*** empty log message ***

	Revision 1.2  2010-08-25 16:09:23  pierre
	cms_theme

	Revision 1.1  2010-08-23 08:29:52  pierre
	*** empty log message ***

	Revision 1.14  2010-06-04 15:40:25  pierre
	*** empty log message ***

	Revision 1.13  2009-09-24 08:53:14  pierre
	*** empty log message ***

	Revision 1.12  2009-03-04 15:56:37  thao
	*** empty log message ***

	Revision 1.11  2009-03-02 07:36:33  thao
	*** empty log message ***

	Revision 1.10  2008-11-28 14:09:56  pierre
	*** empty log message ***

	Revision 1.9  2008-11-27 11:34:35  pierre
	*** empty log message ***

	Revision 1.8  2008-11-12 08:33:36  pierre
	*** empty log message ***

	Revision 1.7  2008-11-06 12:03:54  pierre
	*** empty log message ***

	Revision 1.6  2008-08-29 13:16:02  pierre
	*** empty log message ***

	Revision 1.5  2008-07-16 10:48:36  pierre
	*** empty log message ***

	Revision 1.4  2007/11/26 16:58:27  thao
	*** empty log message ***
	
	Revision 1.3  2007/11/16 13:57:28  remy
	*** empty log message ***
	
	Revision 1.2  2007/08/28 15:55:28  pierre
	*** empty log message ***
	
	Revision 1.1  2007/08/08 14:26:26  thao
	*** empty log message ***
	
	Revision 1.6  2007/08/08 14:16:15  thao
	*** empty log message ***
	
	Revision 1.5  2007/08/08 13:56:04  thao
	*** empty log message ***
	
	Revision 1.4  2007/08/07 10:20:35  thao
	*** empty log message ***
	
	Revision 1.2  2007/03/27 15:10:39  pierre
	*** empty log message ***
	
	Revision 1.1  2006/12/15 12:30:11  pierre
	*** empty log message ***
	
	Revision 1.1.1.1  2006/01/25 15:14:32  pierre
	projet CCitron AWS 2006 Nouveau Website
	
	Revision 1.3  2005/12/20 09:12:10  sylvie
	application des styles
	
	Revision 1.2  2005/10/24 15:10:07  pierre
	arbo.lib.php >> arbominisite.lib.php
	
	Revision 1.1.1.1  2005/10/24 13:37:05  pierre
	re import fusion espace v2 et ADW v2
	
	Revision 1.1.1.1  2005/10/20 13:10:53  pierre
	Espace V2
	
	Revision 1.1.1.1  2005/04/18 13:53:29  pierre
	again
	
	Revision 1.1.1.1  2005/04/18 09:04:21  pierre
	oremip new
	
	Revision 1.1.1.1  2004/11/03 13:49:54  ddinside
	lancement du projet - import de adequat
	
	Revision 1.4  2004/06/18 14:10:21  ddinside
	corrections diverses en vu de la demo prevention routiere
	
	Revision 1.3  2004/06/16 15:23:19  ddinside
	inclusion corrections
	
	Revision 1.2  2004/04/26 08:07:09  melanie
	*** empty log message ***
	
	Revision 1.1.1.1  2004/04/01 09:20:27  ddinside
	Création du projet CMS Couleur Citron nom de code : tipunch
	
	Revision 1.4  2004/01/27 12:12:50  ddinside
	application de dos2unix sur les scripts SQL
	modification des scripts d'ajout pour correction bug lors d'une modif de page
	ajout foncitonnalité de modification de page
	ajout visu d'une page si créée
	
	Revision 1.3  2004/01/21 12:33:51  ddinside
	integration de la DTD XHTML Transitionnelle
	intégration des titresd, description et mots clefs des pages
	
	Revision 1.2  2004/01/12 09:40:38  ddinside
	ajout config auto selon gabarit dans la creation de page
	ajout flashs home
	ajout calendrier
	ajout doc class fileupload
	
	Revision 1.1  2004/01/07 18:29:22  ddinside
	jout derniers fonctionnalites
	
	Revision 1.2  2003/11/26 13:08:42  ddinside
	nettoyage des fichiers temporaires commités par erreur
	ajout config spaw
	corrections bug menu et positionnement des divs
	
*/
	$_SESSION['pageIsSaved']=false;
	
//	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php'); inutilisé
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/gabarit.lib.php');	
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');



	// site sur lequel on est positionné
	$idSite = $_SESSION['idSite_travail'];
	if ($idSite == "") die ("Appel incorrect de la fonctionnalité");
	$oSite = new Cms_site($idSite);

	activateMenu('gestionpage');  //permet de dérouler le menu contextuellement
	
	if (!is_post('name')){ // si pas de nom saisie, default = index
		$_POST['name'] = "index";
	}

	// gabarit
	$oGab = new Cms_page($_GET['idGab']);

	// répertoire des gabarits pour ce site
	$DIR_GABARITS = getRepGabarit($idSite);



	$div_array = unserialize($_SESSION['page']);	


if ($_GET['idGab'] != "") {
	// objet Cms_infos_pages du gabarit
	$oInfos_pages = new Cms_infos_page();
	$oInfos_pages->getCmsinfospages_with_pageid($_GET['idGab']);

	$titre = $oInfos_pages->getPage_titre();
	$mots = $oInfos_pages->getPage_motsclefs();
	$description = $oInfos_pages->getPage_description();
	$thumb = $oInfos_pages->getPage_thumb();
}

$sTitre = "Créer";

if($_GET['id']>0) {
	$pageInfos = getPageStructure($_GET['id']);

	$titre = $pageInfos['titre'];
	$mots = $pageInfos['motscles'];
	$description = $pageInfos['description'];
	$thumb = $pageInfos['thumb'];
	

$sTitre = "Modifier";

}
$date = date("d/m/Y");
?>
<script type="text/javascript"><!--

function updateDateMep() {
	box = document.getElementById("mepNow");
	field = document.getElementById("dateMep");
	if (box.checked==true) {
		field.value="<?php echo $date; ?>";
	}
}

function updateCheckBox() {
	box = document.getElementById("mepNow");
	field = document.getElementById("dateMep");
	if (field.value=="<?php echo $date; ?>") {
		box.checked=true;
	} else {
		box.checked=false;
	}
}

--></script>

<div class="ariane"><span class="arbo2">PAGE >&nbsp;</span><span class="arbo3"><?php $translator->echoTransByCode('Creer_une_page'); ?>&nbsp;>&nbsp;
<?php $translator->echoTransByCode('Etape_4'); ?></span></div>

<form action="pageLiteEditor6.php?id=<?php echo $_GET['id']; ?>&idGab=<?php echo $_GET['idGab']?>&idThe=<?php echo $_GET['idThe']?>" method="post" name="savePage" class="arbo">
<input type="hidden" name="nomPage" id="nomPage" value="<?php echo $_POST['name']; ?>"/>
<input type="hidden" name="nodeidPage" id="nodeidPage" value="<?php echo $_POST['node_id']; ?>"/>
<input type="hidden" name="BeToSave" id="BeToSave" value="<?php echo  $_POST['BeToSave'] ; ?>"/>
<input type="hidden" name="idGab" id="idGab" value="<?php echo $_GET['idGab']; ?>"/>
<input type="hidden" name="idThe" id="idThe" value="<?php echo $_GET['idThe']; ?>"/>
<input type="hidden" name="dateMep" id="dateMep" value="<?php echo $date;?>" />
<div id="virginie">
<!--Veuillez saisir une date de mise en production&nbsp;:&nbsp;<input name="dateMep" type="text" class="arbo" id="dateMep" style="CURSOR: hand;" onFocus="this.blur();(popFrame.fPopCalendar(this,this,popCal) && updateCheckBox());" onClick="this.blur();" value="<?php echo $date;?>" size="11" maxlength="10">
&nbsp;&nbsp;--&nbsp;&nbsp;immédiate&nbsp;<input name="mepNow" type="checkbox" class="arbo" id="mepNow" onClick="updateDateMep();" value="true" checked>
<br>
<br>
Veuillez saisir une date de péremption&nbsp;:&nbsp;<input name="datePeremption" type="text" class="arbo" id="datePeremption" style="CURSOR: hand;" onFocus="this.blur();(popFrame.fPopCalendar(this,this,popCal)); document.getElementById('neverPerempt').checked=false;" onClick="this.blur();" value="" size="11" maxlength="10">
&nbsp;&nbsp;--&nbsp;&nbsp;aucune&nbsp;<input name="neverPerempt" type="checkbox" class="arbo" id="neverPerempt" onClick="if(this.checked) {document.getElementById('datePeremption').value=''}" value="true">
<br>
<br>-->
<?php $translator->echoTransByCode('titre_de_la_page'); ?>&nbsp;
<br>
<input name="pagetitle" id="pagetitle" type="text" class="arbo" maxlength="255" value="<?php echo stripslashes($titre); ?>" />
<br>
<br>
<?php $translator->echoTransByCode('mots_cles'); ?>&nbsp;
<br>
<input name="pagekeywords" id="pagekeywords" type="text" class="arbo" value="<?php echo stripslashes($mots); ?>" size="100" maxlength="768" />
<br>
<br>
<?php $translator->echoTransByCode('Description'); ?><br> 
<textarea name="description" id="description" class="arbo textareaEdit"><?php echo stripslashes($description); ?></textarea>
<br>
<br>
<?php $translator->echoTransByCode('Vignette'); ?>&nbsp;
<br>
<script type="text/javascript">
function SetUrl(fileUrl){
	document.getElementById('pagethumb').value=fileUrl;
}
</script>
<input name="pagethumb" id="pagethumb" type="text" class="arbo" value="<?php echo stripslashes($thumb); ?>" size="100" maxlength="768" />
<a href="javascript:openBrWindow('/backoffice/cms/lib/FCKeditor/editor/filemanager/browser/default/browser.html?Connector=connectors/php/connector.php&Type=Image','pickImg',700,600)"><?php $translator->echoTransByCode('choisir'); ?></a><br>
<br>
</div>
<input name="Enregistrer" type="button" class="arbo" onClick="if(validate_form()){ submit();}" value="Suite >>" id="bt_suite">

</form>
<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoappend.php');
?>