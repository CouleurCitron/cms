<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 

// this part determines the physical root of your website
// it's up to you how to do this
if (!ereg('/$', $HTTP_SERVER_VARS['DOCUMENT_ROOT']))
  $_root = $HTTP_SERVER_VARS['DOCUMENT_ROOT'].'/';
else
  $_root = $HTTP_SERVER_VARS['DOCUMENT_ROOT'];

define('DR', $_root);
unset($_root);

activateMenu('gestioncontenu');  //permet de dérouler le menu contextuellement

// set $spaw_root variable to the physical path were control resides
// don't forget to modify other settings in config/spaw_control.config.php
// namely $spaw_dir and $spaw_base_url most likely require your modification
//$spaw_root = DR.'spaw/';
$spaw_root = 'spaw/';

// include the control file
include $spaw_root.'spaw_control.class.php';

// here we add some styles to styles dropdown
$spaw_dropdown_data['style']['default'] = 'No styles';
$spaw_dropdown_data['style']['style1'] = 'Style no. 1';
$spaw_dropdown_data['style']['style2'] = 'Style no. 2';
?>
<style type="text/css">
  pre {
    background : #cccccc; 
    padding : 5 5 5 5;
  }
</style>
<script language="JavaScript" type="text/javascript">
<!--

var returnVal = false;

function validForm(submitType){
	document.forms['CHATform'].elements['CHATsubmitType'].value = submitType;
	returnVal = validate_form();
}

function submitForm(){
	submitType = document.forms['CHATform'].elements['CHATsubmitType'].value;
	
	if(submitType == 'submit'){
    document.forms['CHATform'].target='';
	}else{
		document.forms['CHATform'].target='_blanck';
	}
	if(returnVal == true){
		if(document.forms['CHATform'].elements['CHATcontent'].value == '' && !document.forms['CHATform'].elements['CHATtype'][2].checked){
			alert("Vous devez saisir un texte pour le chat.");
			returnVal = false;
		}else{
			returnVal = true;
		}
	}
	return returnVal;
}
//-->
</script>
<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style><form action="chatCompiler.php" method="post" name="CHATform" onSubmit="return submitForm()">
<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" bgcolor="D2D2D2" class="arbo">
<tr>
<td><input type="hidden" name="CHATsubmitType" value="">
Générateur de composant chat :
Nom du composant : <input name="CHATname" type="text" class="arbo" id="CHATname" value="" size="20" maxlength="30" pattern="^.+$" errorMsg="Vous devez saisir un nom au composant."><hr class="arbo2">
<input type="radio" name="CHATtype" value="1" checked>Home Page : Picto + texte (vert)<hr class="arbo2">
<input type="radio" name="CHATtype" value="3">Portail : Picto + texte (jaune)<hr class="arbo2">
<input type="radio" name="CHATtype" value="2" onClick="">Portail : picto uniquement (vert)
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="CHATbdVerte" value="left" checked>Bande verte à gauche&nbsp;<input type="radio" name="CHATbdVerte" value="right">Bande verte à droite
<div align="center">
<?php
$sw = new SPAW_Wysiwyg('CHATcontent' /*name*/,stripslashes($HTTP_POST_VARS['CHATcontent']) /*value*/,
											'fr' /*language*/, 'mini' /*toolbar mode*/, '' /*theme*/,
			                '650px' /*width*/, '200px' /*height*/);
$sw->show();
?>
<br />
<br />
</div>
<div align="center"><input name="submit" type="submit" class="arbo" onClick="validForm('submit')" value="Enregistrer">&nbsp;&nbsp;<input name="preview" type="submit" class="arbo" onCLick="validForm('preview')" value="Visualiser" ></div>
</td>
</tr>
</table>
</form>
