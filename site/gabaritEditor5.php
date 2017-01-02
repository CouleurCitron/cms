<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/*
sponthus 07/06/2005

gestion des gabarits	
*/

$_SESSION['pageIsSaved']=false;

// include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php'); semble inutile
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


activateMenu('gestiongabarit');  //permet de dérouler le menu contextuellement
	
// site sur lequel on est positionné
$idSite = $_SESSION['idSite_travail'];
$oSite = new Cms_site($idSite);

if ($idSite == "") die ("Appel incorrect de la fonctionnalité");


$DIR_GABARITS = $_SERVER['DOCUMENT_ROOT']."/backoffice/cms/gabarits";
$div_array = unserialize($_SESSION['page']);	
$titre="";
$mots="";
$description="";
if($_GET['id']>0) {
	$pageInfos = getPageStructure($_GET['id']);
	
	// objet Cms_infos_pages
	$oInfos_pages = new Cms_infos_page();
	$oInfos_pages->getCmsinfospages_with_pageid($_GET['id']);
	
	$titre = $oInfos_pages->getPage_titre();
	$mots = $oInfos_pages->getPage_motsclefs();
	$description = $oInfos_pages->getPage_description();
	$thumb = $oInfos_pages->getPage_thumb();
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
<div class="ariane"><span class="arbo2">GABARIT&nbsp;>&nbsp;</span>
<span class="arbo3">Etape 5 : Propriétés du gabarit</span></div>
<?php
// site de travail
//if (DEF_MENUS_MINISITES == "ON") print(putAfficheSite());
?>

<form action="gabaritEditor6.php?idGab=<?php echo $_GET['idGab']; ?>&id=<?php echo $_GET['id']; ?>&widthGab=<?php echo $_GET['widthGab']; ?>&heightGab=<?php echo $_GET['heightGab']?>" method="post" name="savePage" class="arbo">

<input type="hidden" name="nomPage" id="nomPage" value="<?php echo $_POST['name']; ?>">
<input type="hidden" name="nodeidPage" id="nodeidPage" value="<?php echo $_POST['node_id']; ?>">

<input type="hidden" name="isgabarit_page" id="isgabarit_page" value="1">
<input type="hidden" name="dateMep" id="dateMep" value="<?php echo $date;?>" />
<!--Veuillez saisir une date de mise en production&nbsp;:&nbsp;<input name="dateMep" type="text" class="arbo" id="dateMep" value="<?php echo $date;?>" size="11" maxlength="10">
&nbsp;&nbsp;--&nbsp;&nbsp;immédiate&nbsp;<input name="mepNow" type="checkbox" class="arbo" id="mepNow" onClick="updateDateMep();" value="true" checked;>
<br>
<br>
Veuillez saisir une date de péremption&nbsp;:&nbsp;<input name="datePeremption" type="text" class="arbo" id="datePeremption"  value="" size="11" maxlength="10">
&nbsp;&nbsp;--&nbsp;&nbsp;aucune&nbsp;<input name="neverPerempt" type="checkbox" class="arbo" id="neverPerempt" onClick="if(this.checked) {document.getElementById('datePeremption').value=''}" value="true">
<br>
<br>-->
Titre de la page :&nbsp;
<br>
<input name="pagetitle" id="pagetitle" type="text" class="arbo" maxlength="255" value="<?php echo stripslashes($titre); ?>" />
<br>
<br>
Mots clefs <small>(séparés par des virgules)</small>:&nbsp;
<br>
<input name="pagekeywords" id="pagekeywords" type="text" class="arbo" value="<?php echo stripslashes($mots); ?>" size="100" maxlength="768" />
<br>
<br>
Description :<br> 
<textarea name="description" id="description" class="arbo textareaEdit"><?php echo stripslashes($description); ?></textarea>
<br>
<br>
Vignette :&nbsp;
<br>
<script type="text/javascript">
function SetUrl(fileUrl){
	document.getElementById('pagethumb').value=fileUrl;
}
</script>
<input name="pagethumb" id="pagethumb" type="text" class="arbo" value="<?php echo stripslashes($thumb); ?>" size="100" maxlength="768" />
<a href="javascript:openBrWindow('/backoffice/cms/lib/FCKeditor/editor/filemanager/browser/default/browser.html?Connector=connectors/php/connector.php&Type=Image','pickImg',700,600)">choisir</a><br>
<br>
<input name="Enregistrer" id="Enregistrer" type="button" class="arbo" onClick="if(validate_form()){  submit();}" value="Suite >>">
</form>