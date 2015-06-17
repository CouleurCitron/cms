<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 

// id du sondage
$id = $_GET['id'];
if ($id == "")
	$id = $_POST['id'];


//Translation engine is needed for the survey module
$translator =& TslManager::getInstance();
$langpile = $translator->getLanguages();

$libelleAsk = '';

// modif d'un composant
if ($id > 0) {
	
	// la brique
	$oContent = new Cms_content($id);
	// un content de type "formulaireHTML" est forcément un content lié à la table cms_form
	// le formulaire
	$oAsk = new Cms_survey_ask($oContent->getObj_id_content()); 
	$nomComposant = $oContent->getName_content();
	$hauteurComposant = $oContent->getHeight_content();
	$largeurComposant = $oContent->getWidth_content();
	$SURVEYid = $oContent->getObj_id_content();
	$libelleAsk = $translator->getByID($oAsk->get_libelle());
	//foreach ($langpile as $lang_id => $lang_props)
	//	$libelleAsk .= $translator->getByID($oAsk->get_libelle(), $lang_id)."&nbsp; (".$lang_props['libellecourt'].")\n"; 
} else {
	$hauteurComposant = "180";
	$largeurComposant = "180";
}

 
 
// this part determines the physical root of your website
// it's up to you how to do this
if (!ereg('/$', $HTTP_SERVER_VARS['DOCUMENT_ROOT']))
  $_root = $HTTP_SERVER_VARS['DOCUMENT_ROOT'].'/';
else
  $_root = $HTTP_SERVER_VARS['DOCUMENT_ROOT'];

define('DR', $_root);
unset($_root);

activateMenu('gestioncontenu');  //permet de dérouler le menu contextuellement
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
	document.forms["SURVEYform"].elements["SURVEYsubmitType"].value = submitType;
	if(validate_form() == true){ //Validation des champs texte
		//if(document.forms["SURVEYform"].elements["SURVEYtype"][0].checked || document.forms["SURVEYform"].elements["SURVEYtype"][1].checked){
			if(document.forms["SURVEYform"].elements["SURVEYid"].value == ""){
				alert("Vous devez sélectionner le sondage que vous désirez promouvoir");
				returnVal = false;
			}else{
				returnVal = true;
			}
		//}else{
			//returnVal = true;
		//}
	} else {
		returnVal = false;
	}
}

function submitForm(){
	submitType = document.forms['SURVEYform'].elements['SURVEYsubmitType'].value;
	if(submitType == 'submit'){
    document.forms['SURVEYform'].target='';
	}else{
		document.forms['SURVEYform'].target='_blank';
	}
	return returnVal;
}

//-->
</script>
<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<script src="<?php echo  $URL_ROOT; ?>/backoffice/cms/js/openBrWindow.js" type="text/javascript"></script>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>
<script type="text/javascript">
	function disableSurvey () {
		var frm = document.forms['SURVEYform'];
		frm.elements['SURVEYid'].value = -1;
		frm.elements['libelle_ask'].value = '';
		frm.elements['libelle_ask'].disabled = true;
	}
</script>

<form action="surveyCompiler.php" method="post" name="SURVEYform" onSubmit="return submitForm()">
<span class="arbo2">BRIQUE >&nbsp;</span><span class="arbo3">Sondage</span><br><br>
<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" bgcolor="D2D2D2" class="arbo">
<tr>
<td>
<input type="hidden" name="SURVEYid" value="<?php echo $SURVEYid; ?>">
<input type="hidden" name="SURVEYsubmitType" value="">
<input type="hidden" name="SURVEYtype" value="1">
<input type="hidden" name="SURVEYcontentId" value="<?php echo $id; ?>">
Générateur de composant sondage :
Nom du composant : <input name="SURVEYname" type="text" class="arbo" id="SURVEYname" value="<?php echo $nomComposant; ?>" size="20" maxlength="30" pattern="^.+$" errorMsg="Vous devez saisir un nom au composant.">
&nbsp;Hauteur du composant : <input name="SURVEYheight" type="text" class="arbo" id="SURVEYheight" value="<?php echo $hauteurComposant; ?>" size="8" maxlength="3" pattern="^.+$" errorMsg="Vous devez saisir une hauteur au composant.">
&nbsp;Largeur du composant : <input name="SURVEYwidth" type="text" class="arbo" id="SURVEYwidth" value="<?php echo $largeurComposant; ?>" size="8" maxlength="3" pattern="^.+$" errorMsg="Vous devez saisir une hauteur au composant.">
<hr class="arbo">
<br/>
&gt; Pour désactiver la brique sondage <a href="#tof" class="arbo" onClick="disableSurvey();" >cliquez ici</a>
<br/><br/>
&gt; Pour sélectionner un sondage à promouvoir <a href="#tof" class="arbo" onClick="openBrWindow('surveyList.php','survey',600,400,'scrollbars=auto','true')" >cliquez ici</a>
<br/><br/>&nbsp;&nbsp;Question sélectionnée :
&nbsp;&nbsp;<br>
<br>
<textarea name="libelle_ask" cols="60" rows="8" class="arbo"><?php echo $libelleAsk; ?></textarea>
<br>
<br>
<br>
<div align="center"><input name="submit" type="submit" class="arbo" onClick="validForm('submit')" value="Enregistrer">
&nbsp;&nbsp;<input name="preview" type="submit" class="arbo" onCLick="validForm('preview')" value="Visualiser" >
</div>
</td>
</tr>
</table>
</form>
