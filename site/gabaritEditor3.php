<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 
sponthus 07/06/2005
*/

// include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php'); Ménage semble inutile
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/gabarit.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_gabarits.php');
//include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/surveyGen.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newcontent_write.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newgabarit_write.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newpage_write.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
 include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


// include the control file "spaw"
///include DEF_SPAW_ROOT.'spaw_control.class.php';

activateMenu('gestiongabarit');  //permet de dérouler le menu contextuellement

// site sur lequel on est positionné (site de travail)
$idSite = $_SESSION['idSite_travail'];
if ($idSite == "") die ("Appel incorrect de la fonctionnalité");
$oSite = new Cms_site($idSite);

// composants
$composants = $_SESSION[$_SESSION['GabaritName']];

// objet gabarit
$oGab = new Cms_page($_GET['idGab']);

// nom gabarit
$gabarit = $oGab->getName_page().".php";

// répertoire gabarit
$repGab = getRepGabarit($_SESSION['idSite_travail']);

if ($oGab->getName_page() == "init"){
	$repGab=$_SERVER['DOCUMENT_ROOT']."/backoffice/cms/site/";
	$gabarit = "<script language=\"JavaScript\" type=\"text/javascript\">";
	$gabarit.= "function init(){";
	$gabarit.= "checkIframe();";
	$gabarit.= "}";
	$gabarit.= "window.onload = init;";
	$gabarit.= "</script>";
	$gabarit.= "<!--DEBUTCMS-->";
	$gabarit.= "<!--FINCMS-->";
}

//$gabarit = $repGab.$gabarit;
//$tmpfile = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.md5(uniqid(rand())).'.php';

//$tmpfilehandle = fopen($tmpfile,'w') or (error_log("Soit ecriture impossible dans $tmpfile") && die('Erreur irrécupérable. Veuillez contacter l\'administrateur.'));
//$gabfilehandle = fopen($gabarit,'r') or (error_log("Soit lecture impossible dans $gabarit") && die('Erreur irrécupérable. Veuillez contacter l\'administrateur.'));

$gabfilehandle = $gabarit;

// taille du gabarit
//$widthGab = $oGab->getWidth_page();
//$heightGab = $oGab->getHeight_page();

$widthGab = $_GET['widthGab'];
$heightGab = $_GET['heightGab'];

?>
<script type="text/javascript">document.title="Pages BB";</script><style type="text/css">
<!--
body, html {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>
<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<div class="ariane"><span class="arbo2">GABARIT&nbsp;>&nbsp;</span>
<span class="arbo3">Etape 3 : Assemblage du gabarit&nbsp;>&nbsp;</span></div>
<?php
// site de travail
//if (DEF_MENUS_MINISITES == "ON") print(putAfficheSite());
?>

<style type="text/css">
.dragDiv{
	position:absolute;
	cursor:hand;
}
</style>
<script type="text/javascript">
<!--
	temp1=0;
	temp2=0;
	myX=0;
	myY=0;
	myBrowser=navigator.appName;
	myVersion=navigator.appVersion;
	var version45=(myVersion.indexOf("4.")!=-1||myVersion.indexOf("5.")!=-1)
	var NS=(myBrowser.indexOf("Netscape")!=-1 && version45)
	var IE=(myBrowser.indexOf("Explorer")!=-1 && version45)
	if(!NS&&!IE)alert("This file is intended to run under Netscape versions 4.0+ or Internet Explorer versions 4.0+, and may not run properly on earlier versions")
	if(IE){var hide="hidden"; var show="visible"}
	if(NS){var hide="hide"; var show="show"}

	var divArray = new Array();
	var dragapproved=false;
	var z,x,y,t;
	z=null;
	function move()
	{
		if (event.button==1 &&  dragapproved)
		{
			//z.style.pixelLeft=temp1+event.clientX-x;
			myX = temp1+event.clientX-x;
			t.setAttribute('left', myX +'px');
			//z.style.pixelTop=temp2+event.clientY-y;
			myY = temp2+event.clientY-y;
			t.setAttribute('top', myY + 'px');
			return false
		}
	}
	
	function drags()
	{
		if (!document.all)
			return
		if (event.srcElement.className=='dragDiv')
		{
			dragapproved=true;
			z=event.srcElement;
			//temp1=z.style.pixelLeft;
			//temp2=z.style.pixelTop;
			x=event.clientX;
			y=event.clientY;

			document.onmousemove=move;
		}
	}

	function infos() {
		dragapproved=false;
		temp1 = myX;
		temp2 = myY;
	}

	if(IE) {
		document.onmousedown=drags
		document.onmouseup=infos
	}
-->
</script>


<table border="0" cellpadding="0" cellspacing="0"  style="height:<?php echo  $heightGab ; ?>px;">
<tr><td class="arbo">

<?php

// objet page
$oCms_page = new Cms_page();

// gabarit créé à partir du gabarit par défaut
$oCms_page->initValues($oGab->getId_page());

// nouveau gabarit à créer
$oCms_page->setName_page("");

// objet Cms_infos_pages
$oCms_infos_pages = new Cms_infos_page();
$oCms_infos_pages->getCmsinfospages_with_pageid($oGab->getId_page());


// création du gabarit à include 
$contenuGab = getInclude_gabarit($gabarit, $gabfilehandle, $tmpfilehandle, $composants, $oGab, $widthGab, $heightGab);
echo "<!--debut-->";
echo $contenuGab ;
echo "<!--fin-->";
// inclusion du gabarit
//include "$tmpfile";

//unlink($tmpfile);
?>

</td>
</tr>
<tr>
<td>


<div id="buttons" style="position:relative; border-style: solid;border-width: 1px;">
<form name="assemble" id="assemble" method="post" action="gabaritEditor4.php?idGab=<?php echo $_GET['idGab']; ?>&id=<?php echo $_GET['id']; ?>&widthGab=<?php echo $widthGab;?>&heightGab=<?php echo $heightGab;?>">



<input type="hidden" name="widthGab" id="widthGab" value="<?php echo $widthGab; ?>">
<input type="hidden" name="heightGab" id="heightGab" value="<?php echo $heightGab; ?>">
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" class="arbo">
<tr class="arbo"><td colspan="3">
<table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#E7E7E7">
<tr height="23">
 <td width="23"><img src="/backoffice/cms/img/move.gif" class="dragDiv"></td>
 <td align="center" class="arbo">&nbsp;<strong>Barre d'outils&nbsp;</strong></td>
</tr>
</table>
</td></tr>
<tr class="arbo">
<td width="20%">
<!-- boutons de deplacement... -->
<table cellpadding="2" cellspacing="0" border="0" align="center" valign="center">
<tr>
 <td>&nbsp;</td>
 <td align="center" valign="bottom" style="align:center"><a href="javascript:moveDiv('haut');"><img src="/backoffice/cms/img/flechehaut.gif" border="0"></a></td>
 <td>&nbsp;</td>
</tr>
<tr>
 <td align="center" valign="middle" style="vertical-align:middle"><a href="javascript:moveDiv('gauche');"><img src="/backoffice/cms/img/flechegauche.gif" border="0"></a></td>
 <td align="center" valign="middle"><input name="pxStep" type="text" class="arbo" id="pxStep" value="1" size="4" maxlength="4"></td>
 <td align="center" valign="middle" style="vertical-align:middle"><a href="javascript:moveDiv('droite');"><img src="/backoffice/cms/img/flechedroite.gif" border="0"></a></td>
</tr>
<tr>
 <td>&nbsp;</td>
 <td align="center" style="align:center" ><a href="javascript:moveDiv('bas');">
   <img src="/backoffice/cms/img/flechebas.gif" border="0"></a></td>
 <td>&nbsp;</td>
</tr>
</table>
<!-- fin boutons de deplacement... -->
</td>
<td>&nbsp;
</td>
<td width="20%" align="left" valign="middle" class="arbo">
H&nbsp;:&nbsp;<input name="H" type="text" class="arbo" id="H" onChange="updateDivPosition()" size="4" maxlength="4">&nbsp;px<br>
G&nbsp;:&nbsp;<input name="G" type="text" class="arbo" id="G" onChange="updateDivPosition()" size="4" maxlength="4">&nbsp;px<br>
</td>
</tr>
<tr class="arbo">
 <td colspan="2">
  <table border="1" align="center" cellpadding="3" cellspacing="0" bordercolor="#d2d2d2" class="arbo">
  <tr>
     <td colspan="5" bgcolor="E7E7E7" class="arbo">&nbsp;Gestion des briques&nbsp;</td>
  </tr>
  <tr bgcolor="EEEEEE" class="arbo">
     <td class="arbo">&nbsp;Nom&nbsp;</td>
     <td colspan="2" class="arbo">&nbsp;Dimensions&nbsp;</td>
     <td class="arbo">&nbsp;Transparence&nbsp;</td>
     <td class="arbo">&nbsp;Superposition&nbsp;</td>
  </tr>
<?php
	foreach($composants as $k => $v) {
		$composant = getComposantById($v);

?><tr class="arbo">
    <td class="arbo">&nbsp;&nbsp;<b><a class="arbo" href="#<?php echo $v; ?>" onClick="toggleComposant('<?php echo $v; ?>');"><?php echo $composant['name']; ?></a></b>&nbsp;&nbsp;</td>
    <td class="arbo">&nbsp;<input name="Width<?php echo $v; ?>" id="Width<?php echo $v; ?>" type="text" class="arbo" onChange="javascript:updateSize('width','<?php echo $v; ?>',this);" value="<?php echo $composant['width']; ?>" size="3" maxlength="4">
    &nbsp;px&nbsp;</td>
    <td class="arbo">&nbsp;<input name="<?php echo $v; ?>" id="Height<?php echo $v; ?>" type="text" class="arbo" onChange="javascript:updateSize('height','<?php echo $v; ?>',this);" value="<?php echo $composant['height']; ?>" size="3" maxlength="4">
    &nbsp;px&nbsp;</td>
    <td class="arbo">&nbsp;<input name="Opacity<?php echo $v; ?>" id="Opacity<?php echo $v; ?>" type="text" class="arbo" onChange="javascript:updateOpacity('<?php echo $v; ?>',this);" value="100" size="2" maxlength="3">
    &nbsp;%&nbsp;</td>
    <td class="arbo">&nbsp;<input name="Index<?php echo $v; ?>" id="Index<?php echo $v; ?>" type="text" class="arbo" onChange="javascript:updateIndex('<?php echo $v; ?>',this);" value="1" size="2" maxlength="4">
    &nbsp;&nbsp;</td>
  </tr>
<?php
  	}
?>
  </table>
 </td>
 <td nowrap valign="bottom">
 	<input name="button" type="button" class="arbo" onClick="javascript:saveAll()" value="Sauver tout >>">
	<input type="hidden" id="serializedData" name="serializedData" value="">
 </td>
</tr>
</table>
</form>
</div>
</td>
</tr>
</table>

<script type="text/javascript"><!--
	var currentDivStyle = null;
	var leftX=document.getElementById("G");
	var topY=document.getElementById("H");
	var step=document.getElementById("pxStep");

	function updateSize(coord,nameId,obj) {
		toggleComposant(nameId);
		currentDivStyle[coord] = obj.value;
	}
	
	function updateOpacity(nameId,obj) {
		toggleComposant(nameId);
		obj = document.getElementById('Opacity'+nameId);
		mozTaux = obj.value/100;
		currentDivStyle["filter"] = 'alpha(opacity='+obj.value+')';
		currentDivStyle["-moz-opacity"] = mozTaux;
	}

	function updateIndex(nameId,obj) {
		toggleComposant(nameId);
		currentDivStyle['zIndex'] = obj.value;
	}

	function toggleComposant(nameId) {
		if(currentDivStyle!=null) {
			currentDivStyle["background"] = "#ffffff";
			currentDivStyle["style"] = "border-style:dotted; border-width: 1px;";
		}
		currentDivStyle = document.getElementById(nameId).style;

		currentDivStyle["background"] = "#dddddd";
		currentDivStyle["visibility"] = "visible";
		currentDivStyle["style"] = "border-style:dotted; border-width: 1px;";
		updateFieldsXY();
	}

	function cleanPosString(str) {
		return str.replace(/px/i,'');
	}
	
	function updateFieldsXY() {
		if(currentDivStyle==null) {
			window.alert("Veuillez sélectionner un composant à positionner");
			return;
		}
		tmpX=currentDivStyle['left'];
		tmpY=currentDivStyle['top'];
		leftX.value=cleanPosString(tmpX);
		topY.value=cleanPosString(tmpY);
	}

	function moveDiv(direction) {
		if(currentDivStyle==null) {
			window.alert("Veuillez sélectionner un composant à positionner");
			return;
		}
		if (direction=="bas") {
			eval("topY.value = " + topY.value + "+" + step.value);
		}
		if (direction=="haut") {
			topY.value-=step.value;
		}
		if (direction=="droite") {
			eval("leftX.value = "+ leftX.value + "+" + step.value);
		}
		if (direction=="gauche") {
			leftX.value-=step.value;
		}
		updateDivPosition();
	}

	function updateDivPosition() {
		if(currentDivStyle==null) {
			window.alert("Veuillez sélectionner un composant à positionner");
			return;
		}
		currentDivStyle['left'] = leftX.value+'px';
		currentDivStyle['top'] = topY.value+'px';
		height = currentDivStyle['height'] + currentDivStyle['top'];
		globalDivContent = document.getElementById('content').style;
		myH = globalDivContent['height'];
	}
	
	function saveAll() {
		sf = document.getElementById("serializedData");
		isOk = true;
		sf.value='';
		for(i=0;i<divArray.length;i++) {
			updateOpacity(divArray[i], 'Opacity'+divArray[i]);
			tmpDiv = document.getElementById(divArray[i]);
			tmpDivStyle = tmpDiv.style;
			sf.value = sf.value + '#' + divArray[i] + '#';
			sf.value = sf.value + 'top='          + tmpDivStyle["top"]          + ',';
			sf.value = sf.value + 'left='         + tmpDivStyle["left"]         + ',';
			sf.value = sf.value + 'width='        + tmpDivStyle["width"]        + ',';
			sf.value = sf.value + 'height='       + tmpDivStyle["height"]       + ',';
			sf.value = sf.value + 'filter='       + tmpDivStyle["filter"]       + ',';
			sf.value = sf.value + '-moz-opacity='  + tmpDivStyle["-moz-opacity"] + ',';
			sf.value = sf.value + 'zIndex='        + tmpDivStyle["zIndex"]       + ',';
			sf.value = sf.value + '#' + divArray[i] + '#';

			if (tmpDivStyle.visibility == 'hidden') {
				isOk = false;
			}
		}
		if (isOk){
			document.assemble.submit();
		} else
			window.alert('Vous n\'avez pas positionné toutes les briques');
	}
	
	var t = document.getElementById("buttons").style;

<?php
	// positionement des div dont on a déjà l'information
	if(is_array($_SESSION['divArray'])) {

		foreach($_SESSION['divArray'] as $id => $param) {
			if(is_int($id) and in_array($id,$_SESSION[$_SESSION['GabaritName']])) {
?>
//width
field = document.getElementById('Width<?php echo $id; ?>');
field.value = <?php echo str_replace('px','',$param['width']);?>;
updateSize('width','<?php echo $id; ?>',field);
//height
field = document.getElementById('Height<?php echo $id; ?>');
field.value = <?php echo str_replace('px','',$param['height']);?>;
updateSize('height','<?php echo $id; ?>',field);
//top left
toggleComposant('<?php echo $id; ?>');
field = document.getElementById('H');
field.value = <?php echo str_replace('px','',$param['top']);?>;
field = document.getElementById('G');
field.value = <?php echo str_replace('px','',$param['left']);?>;
updateDivPosition();
//opacity
field = document.getElementById('Opacity<?php echo $id; ?>');
field.value = <?php echo ($param['-moz-opacity']*100);?>;
updateOpacity('<?php echo $id; ?>',field);
//opacity
field = document.getElementById('Index<?php echo $id; ?>');
field.value = <?php echo ($param['zIndex']);?>;
updateIndex('<?php echo $id; ?>',field);

<?php
			}
		}
	}
?>
--></script>
<style type="text/css">
body, html {
	overflow: auto;
}
</style>
<style>
DIV.space DIV.content DIV DIV { top:0px !important; left:0px !important }
DIV.space DIV.content DIV { overflow: auto !important; overflow-y: auto !important; overflow-x: none !important; }
</style>