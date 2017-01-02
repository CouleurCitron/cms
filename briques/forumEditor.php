<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 

// this part determines the physical root of your website
// it's up to you how to do this
if (!preg_match('/\/$/msi', $HTTP_SERVER_VARS['DOCUMENT_ROOT']))
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
	document.forms["FORUMform"].elements["FORUMsubmitType"].value = submitType;
	if(validate_form() == true){ //Validation des champs texte
		if(document.forms["FORUMform"].elements["FORUMtype"][0].checked || document.forms["FORUMform"].elements["FORUMtype"][1].checked){
			if(document.forms["FORUMform"].elements["FORUMtopic"].value == ""){
				alert("Vous devez sélectionner le forum que vous désirez promouvoir");
				returnVal = false;
			}else{
				returnVal = true;
			}
		}else{
			returnVal = true;
		}
	}else{
		returnVal = false;
	}
}

function submitForm(){
	submitType = document.forms['FORUMform'].elements['FORUMsubmitType'].value;
	if(submitType == 'submit'){
    document.forms['FORUMform'].target='';
	}else{
		document.forms['FORUMform'].target='_blanck';
	}
	if(returnVal == true){
		// Contrôle si une phrase de promotion du forum a été saisie
		if(document.forms['FORUMform'].elements['FORUMcontent'].value == '' && !document.forms['FORUMform'].elements['FORUMtype'][2].checked){
			alert("Vous devez saisir un texte de promotion du forum.");
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
<?php
$strListe = "<select name=\"FORUMtopic\" class=\"arbo\">"
	."<option value=\"\">Choisissez un forum :</option>";
	
$sqlCateg = "SELECT cat_id, cat_title FROM forum_categories ORDER BY cat_title";
if (DEF_BDD != "ORACLE") $sql.=";";

$rsCateg = $db->Execute($sqlCateg);

// Sélection des catégories
if($rsCateg == false){
	error_log(" plantage lors de l'execution de la requete ".$sqlCateg);
	error_log($db->ErrorMsg());
}else{
	// Boucle sur les catégories de forum
	while(!$rsCateg->EOF){
		// Sélection des Forums en fonction de cat_id sur laquelle on est.
		$sqlForum = "SELECT forum_id, forum_name FROM forum_forums WHERE cat_id = ".$rsCateg->fields[n('cat_id')]." ORDER BY forum_name";
		if (DEF_BDD != "ORACLE") $sql.=";";		
		$rsForum = $db->Execute($sqlForum);
		if($rsForum==false){
			error_log(" plantage lors de l'execution de la requete ".$sqlForum);
		  error_log($db->ErrorMsg());
		}else{
			// On affiche la catégorie et ses forums s'il y en a
			if(!$rsForum->EOF){
				$strListe.= "<option value=\"\">".$rsCateg->fields[n('cat_title')]."</option>";
				// Boucle sur les forums sélectionnés
				while(!$rsForum->EOF){
					$strListe .= "<option value=\"".$rsForum->fields[n('forum_id')]."\">&nbsp;&nbsp;&nbsp;&nbsp;- ".$rsForum->fields[n('forum_name')]."</option>";
					$rsForum->MoveNext();
				}
			}
		}
		$rsCateg->MoveNext();
	}
}
$strListe .= "</select>";
?>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style><form action="forumCompiler.php" method="post" name="FORUMform" onSubmit="return submitForm()">
<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" bgcolor="D2D2D2" class="arbo">
<tr>
<td class="arbo">
<input type="hidden" name="FORUMsubmitType" value="">
Générateur de composant forum :
Nom du composant : <input name="FORUMname" type="text" class="arbo" id="FORUMname" value="" size="20" maxlength="30" pattern="^.+$" errorMsg="Vous devez saisir un nom au composant.">
<hr class="arbo2">
<input type="radio" name="FORUMtype" value="1" checked>Home Page : Picto + texte (vert)<hr class="arbo2">
<input type="radio" name="FORUMtype" value="3">Portail : Picto + texte (jaune)<hr class="arbo2">
<input type="radio" name="FORUMtype" value="2" onClick="">Portail : picto uniquement (vert)
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="FORUMbdVerte" value="left" checked>Bande verte à gauche&nbsp;<input type="radio" name="FORUMbdVerte" value="right">Bande verte à droite<hr class="arbo2">
Sélectionner le forum que vous désirez promouvoir : <span class="arbo"><?php echo $strListe ?></span>
<div align="center">
<?php
$sw = new SPAW_Wysiwyg('FORUMcontent' /*name*/,stripslashes($HTTP_POST_VARS['FORUMcontent']) /*value*/,
											'fr' /*language*/, 'mini' /*toolbar mode*/, '' /*theme*/,
			                '650px' /*width*/, '200px' /*height*/);
$sw->show();
?>
<br />
<br />
</div>
<div align="center"><input name="submit" type="submit" class="arbo" onClick="validForm('submit')" value="Enregistrer">
&nbsp;&nbsp;<input name="preview" type="submit" class="arbo" onCLick="validForm('preview')" value="Visualiser" >
</div></td>
</tr>
</table>
</form>
