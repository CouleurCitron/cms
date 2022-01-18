<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	
*****************************************************
*** Ajout automatique et récursif d'une brique    ***
***                                               ***
*** Récupéré du fichier pageEditor3.php           ***
*****************************************************

*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/surveyGen.php');

activateMenu('gestionpage');  //permet de dérouler le menu contextuellement

$buffer=1024;   // buffer de lecture de 1Ko
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
<p class="arbo2"><strong>Paramétrage de la brique</strong></p>
<form name="assemble" method="post" action="pageAutoEditor3.php?name=<?php echo $_GET['name']; ?>&file=<?php echo $_GET['file']; ?>&id=<?php echo $_GET['id']; ?>">
<table border="0" cellpadding="0" cellspacing="0">
<tr><td class="arbo">
<?php

$selectedComposant = $_SESSION['selectedComposant'];
$composant = getComposantById($selectedComposant);

	$tampon = '<div id="space" class="space">
<div id="content" class="content">';

	$tampon.= "<script language=\"javascript\">var divArray=new Array(); divArray.push(".$selectedComposant.");</script><div style=\"border-width: 1px; border-style: dotted;z-index: 3;\" id=\"".$composant['id']."\" class=\"".$composant['id']."\">\n";
	$tampon.= $composant['html'];
	$tampon.="\n</div>\n";
	$tampon.='  </div>
</div>';
echo $tampon;
$styles='
<style type="text/css">

.remoteCellControl{
	background-color: #ffffff;
	font-family: arial;
	font-size: 11px;
}

.remoteCellControlTable{
	background-color: #000000;
}

.remoteCellControlTitle{
	background-color: #fcd4a5;
	font-family: arial;
	font-size: 11px;
	text-align: center;
}
';

	$styles.='
.'.$selectedComposant.' {
	position: relative;
	overflow-x: auto;
	overflow-y: auto;
	overflow: auto;
	text-align: left;
};

	';

$styles.='
</style>
<script type="text/javascript">
	var tmpStyle = null;
';

	$styles.='
	tmpStyle = document.getElementById("'.$composant['id'].'").style;
	tmpStyle.width="'.$composant['width'].'px";
	tmpStyle.height="'.$composant['height'].'px";
	tmpStyle.top="0px";
	tmpStyle.left="0px";
	tmpStyle.filter="alpha(opacity=100)";
	tmpStyle.MozOpacity="1";
	tmpStyle.zIndex="1";
	tmpStyle.background="#dddddd";
	tmpStyle.visibility="hidden";
	';

$styles.='
</script>
';
echo $styles;
?>
</td>
</tr>
<tr>
<td>
<div id="buttons" style="position:relative;border-style: solid;border-width: 1px; margin-top:20px">
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" class="arbo">
<tr class="arbo"><td colspan="3">
<table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#E7E7E7">
<tr height="23">
 <td width="23"></td>
 <td align="center" class="arbo">&nbsp;<strong>Informations sur la brique&nbsp;</strong></td>
</tr>
</table>
</td></tr>
<tr class="arbo">
 <td colspan="2">
  <table border="1" align="center" cellpadding="3" cellspacing="0" bordercolor="#d2d2d2" class="arbo">
  <tr bgcolor="EEEEEE" class="arbo">
     <td class="arbo">&nbsp;Nom&nbsp;</td>
     <td colspan="2" class="arbo">&nbsp;Dimensions&nbsp;</td>
     <td class="arbo">&nbsp;Transparence&nbsp;</td>
     <td class="arbo">&nbsp;Superposition&nbsp;</td>
     <td class="arbo">&nbsp;Position&nbsp;Haut&nbsp;</td>
     <td class="arbo">&nbsp;Position&nbsp;Gauche&nbsp;</td>
  </tr>
<?php
		$composant = getComposantById($selectedComposant);
?><tr class="arbo">
    <td class="arbo">&nbsp;&nbsp;<b><?php echo $composant['name']; ?><b>&nbsp;&nbsp;</td>
    <td class="arbo" nowrap>&nbsp;<input name="Width<?php echo $selectedComposant; ?>" id="Width<?php echo $selectedComposant; ?>" type="text" class="arbo" onChange="javascript:updateSize('width','<?php echo $selectedComposant; ?>',this);" value="<?php echo $composant['width']; ?>" size="3" maxlength="4"> px&nbsp;</td>
    <td class="arbo" nowrap>&nbsp;<input name="Height<?php echo $selectedComposant; ?>" id="Height<?php echo $selectedComposant; ?>" type="text" class="arbo" onChange="javascript:updateSize('height','<?php echo $selectedComposant; ?>',this);" value="<?php echo $composant['height']; ?>" size="3" maxlength="4"> px&nbsp;</td>
    <td class="arbo" nowrap>&nbsp;<input name="Opacity<?php echo $selectedComposant; ?>" id="Opacity<?php echo $selectedComposant; ?>" type="text" class="arbo" onChange="javascript:updateOpacity('<?php echo $selectedComposant; ?>',this);" value="100" size="2" maxlength="3"> %&nbsp;</td>
    <td class="arbo" nowrap>&nbsp;<input name="Index<?php echo $selectedComposant; ?>" id="Index<?php echo $selectedComposant; ?>" type="text" class="arbo" onChange="javascript:updateIndex('<?php echo $selectedComposant; ?>',this);" value="1" size="2" maxlength="4"> &nbsp;</td>
    <td class="arbo" nowrap>&nbsp;<input name="H" id="H" type="text" class="arbo" value="11" size="2" maxlength="3"> px&nbsp;&nbsp;</td>
    <td class="arbo" nowrap>&nbsp;<input name="G" id="G" type="text" class="arbo" value="22" size="2" maxlength="3"> px&nbsp;&nbsp;</td>
  </tr>
  </table>
 </td>
 </tr>
 <tr class="arbo">
 <td nowrap valign="bottom" align="center">
 	<input name="button" type="button" class="arbo" onClick="javascript:saveAll()" value="Suivant (choix dossier) >>">
	<input type="hidden" id="serializedData" name="serializedData" value="">
 </td>
</tr>
</table>
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
		eval("currentDivStyle."+coord+"='"+obj.value+"px'");
	}
	
	function updateOpacity(nameId,obj) {
		toggleComposant(nameId);
		mozTaux = obj.value/100;
		currentDivStyle.filter='alpha(opacity='+obj.value+')';
		currentDivStyle.MozOpacity=obj.value/100;
	}

	function updateIndex(nameId,obj) {
		toggleComposant(nameId);
		currentDivStyle.zIndex=obj.value;
	}

	function toggleComposant(nameId) {
		if(currentDivStyle!=null) {
			currentDivStyle.background="#ffffff";
			currentDivStyle.borderStyle="dotted";
			currentDivStyle.borderWidth="1px";
		}
		currentDivStyle = document.getElementById(nameId).style;
		currentDivStyle.background="#dddddd";
		currentDivStyle.visibility="visible";
		currentDivStyle.borderStyle="dotted";
		currentDivStyle.borderWidth="1px";
		updateFieldsXY();
	}

	function cleanPosString(str) {
		return str.replace(/px/i,'');
	}
	
	function updateFieldsXY() {
		if(currentDivStyle==null) {
			return;
		}
		tmpX=currentDivStyle.left;
		tmpY=currentDivStyle.top;
		leftX.value=cleanPosString(tmpX);
		topY.value=cleanPosString(tmpY);
	}

	function saveAll() {
		sf = document.getElementById("serializedData");
		sf.value='';
		for(i=0;i<divArray.length;i++) {
			tmpDiv = document.getElementById(divArray[i]);
			tmpDivStyle = tmpDiv.style;
			sf.value = sf.value + '#' + divArray[i] + '#';
			sf.value = sf.value + 'top='          + document.assemble.H.value          + ',';
			sf.value = sf.value + 'left='         + document.assemble.G.value         + ',';
			sf.value = sf.value + 'width='        + tmpDivStyle.width        + ',';
			sf.value = sf.value + 'height='       + tmpDivStyle.height       + ',';
			sf.value = sf.value + 'filter='       + tmpDivStyle.filter       + ',';
			sf.value = sf.value + '-moz-opacity='  + tmpDivStyle.MozOpacity + ',';
			sf.value = sf.value + 'zIndex='        + tmpDivStyle.zIndex       + ',';
			sf.value = sf.value + '#' + divArray[i] + '#';
		}
		document.forms[0].submit();
	}
	
toggleComposant('<?php echo $selectedComposant; ?>');
--></script>
</form>