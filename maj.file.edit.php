<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

$translator =& TslManager::getInstance();
$langpile = $translator->getLanguages();

$sXML = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>
<class name=\"image\" libelle=\"Images\" prefix=\"img\" display=\"src\" abstract=\"titre\">
<item name=\"titre\" libelle=\"Titre\" type=\"varchar\" length=\"64\" list=\"true\" order=\"true\" translate=\"reference\"/>
<item name=\"metadata\" libelle=\"meta-données\" type=\"int\" length=\"11\" list=\"true\" order=\"true\" translate=\"reference\" option=\"textarea\" />  
<item name=\"titre\" libelle=\"Titre\" type=\"varchar\" length=\"64\" list=\"true\" order=\"true\" translate=\"reference\" option=\"link\"/>
</class> ";
 
echo $sXML ; 
xmlClassParse($sXML);

$classeName = $stack[0]["attrs"]["NAME"];

if (is_get('titre')){
	$classeLibelle = $_GET['titre'];
}
else{
	if (isset($stack[0]["attrs"]["LIBELLE"]) && ($stack[0]["attrs"]["LIBELLE"] != ""))
		$classeLibelle = stripslashes($stack[0]["attrs"]["LIBELLE"]);
	else	$classeLibelle = $classeName;
}




$classePrefixe = $stack[0]["attrs"]["PREFIX"];
$aNodeToSort = $stack[0]["children"];



 
$classeName = "cms_diapo"; 
$classePrefixe = "img"; 
pre_dump($_POST);

if (isset($_POST["operation"]) && $_POST["operation"] == "INSERT") {
	// on enregistre les traductions
	
	/* ["fImg_titre_FR"]=>
	  string(2) "fd"
	  ["fImg_titre_EN"]=>
	  string(6) "fdfdsf"
	  ["fImg_metadata"]=>
	  string(2) "-1"
	  ["fImg_texte_FR"]=>
	  string(3) "fdf"
	  ["fImg_texte_EN"]=>
	  string(6) "fdsfds"
	  ["fImg_url_FR"]=>
	  string(3) "fds"
	  ["fImg_url_EN"]=>
  */
  
	
	
}
 
?>
 
<form name="add_<?php echo $classePrefixe; ?>_form" id="add_<?php echo $classePrefixe; ?>_form" enctype="multipart/form-data" method="post">
<input type="hidden" name="classeName" id="classeName"  value="<?php echo $classeName; ?>" />
<span class="arbo2">MODULE >&nbsp;</span><span class="arbo3">Images des diaporamas&nbsp;>&nbsp;
Ajouter</span><br><br>
<script src="/backoffice/cms/js/validForm.js" type="text/javascript"></script>
<script src="/backoffice/cms/js/jquery.mycolorpicker.js" type="text/javascript"></script> 

<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/jquery.mycolorpicker.css" />   
  
<script type="text/javascript">

	///////////////////////////////////////////////////
	// contrôle la validité du formulaire
	///////////////////////////////////////////////////	
	
	function ifFormValid(){
		return true;
	}

	///////////////////////////////////////////////////
	// validation du formulaire
	///////////////////////////////////////////////////
	
	function validerForm(){	
		if (validate_form("add_<?php echo $classePrefixe; ?>_form") &&  validerChampsOblig()){ 
			document.add_<?php echo $classePrefixe; ?>_form.operation.value = "INSERT";
			document.add_<?php echo $classePrefixe; ?>_form.action = "/backoffice/cms/maj.file.edit.php?id=-1"; 
			document.add_<?php echo $classePrefixe; ?>_form.target = "_self";
			document.add_<?php echo $classePrefixe; ?>_form.submit(); 
		}		
	}
	
	function annulerForm(){
					if (window.name.indexOf("if")==0){	// ifframe fancybox	
				window.parent.$.fancybox.close();
			}
			else{
				history.back();
			}
			}
	
	function loadIframe(ifId, id){		
		url = document.getElementById("ifUrl"+ifId).value;
		if(id!=undefined){
			url = url.replace(/id=-1/,'id='+id);
		}
		if (document.getElementById("fDia_diaporama_type") != null){ //cas diaporama
			url += "&setDiaporamaType="+document.getElementById("fDia_diaporama_type").value;
		}
		document.getElementById("if"+ifId).setAttribute('src', url);	
	}
	
	function editAssoItem(fld){
		// fld : fAssoClasseIN_ClassOUT_ID		
		var matches = fld.match(/^f(.+)_([0-9]*)+$/);
		ifId = matches[1];		
		id = matches[2]; 
		loadIframe(ifId, id);
	}
	
	///////////////////////////////////////////////////
	// validation du mot de passe
	///////////////////////////////////////////////////
	function checkPwd(id){
		saisie = document.getElementById(id).value;
		confirmation = document.getElementById(id+"conf").value;
		if (saisie != confirmation){
			alert("Les valeurs saisies pour le mot de passe et sa confirmation ne correspondent pas.\nVeuillez corriger votre saisie");
		}
	}
	
	function resetField(){
		for(i in arguments){
			document.getElementById(arguments[i]).value = '';
		}
		
	}

</script>
<!-- MODE EDITION -->

<input type="hidden" name="operation" id="operation" value="INSERT" />
<input type="hidden" name="urlRetour" id="urlRetour" value="" />
<input type="hidden" name="id" id="id" value="-1" />
<input type="hidden" name="actiontodo" id="actiontodo" value="SAUVE" />
<input type="hidden" name="sChamp" id="sChamp" value="" />


<?php

include_once ("include/cms-inc/autoClass/maj.translation.php"); 

?>

<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
<input type="hidden" name="MAX_FILE_SIZE" value="20971520"><script type="text/javascript">
function validerChampsOblig() {
erreur=0;
lib="";
if (erreur == 0) {
  return true; 
}
else{
alert("Les champs suivants sont obligatoires : \n"+lib);	return false;
}
}</script>


<tr>
<td width="141" align="right" bgcolor="#E6E6E6" class="arbo">&nbsp;<u><b>Titre</b></u></td>
<td width="535" align="left" bgcolor="#EEEEEE" class="arbo">

<?php
foreach ($langpile as $lang_id => $lang_props) {
	if ($operation == 'UPDATE')
		$eTslValue = str_replace('"', '&quot;', $translator->getByID($eKeyValue, $lang_id));
	elseif ($operation == 'INSERT')
		$eTslValue = '';					
	
	$field_name = 'titre'; 	 
	echo "<input type=\"text\" id=\"f".ucfirst($classePrefixe)."_".$field_name."_".$lang_props['libellecourt']."\" class=\"".$lang_props['libellecourt']."\" name=\"f".ucfirst($classePrefixe)."_".$field_name."_".$lang_props['libellecourt']."\" value=\"".$eTslValue."\" size=\"69\"/>";
	if (sizeof($langpile) > 1)
		echo "<span class='".$lang_props['libellecourt']."'>&nbsp;".$lang_props['libellecourt']."</span>";
 
}
?>
</td>
</tr>
<!-- fin des champs de la classe -->


<tr>
<td width="141" align="right" bgcolor="#E6E6E6" class="arbo">&nbsp;<u><b>Meta-données</b></u></td>
<td width="535" align="left" bgcolor="#EEEEEE" class="arbo"><input type="hidden" id="fImg_metadata" name="fImg_metadata" value="-1" />

<?php
foreach ($langpile as $lang_id => $lang_props) {
	if ($operation == 'UPDATE')
		$eTslValue = str_replace('"', '&quot;', $translator->getByID($eKeyValue, $lang_id));
	elseif ($operation == 'INSERT')
		$eTslValue = '';					
	
	$field_name = 'texte'; 	
		 
	echo "<textarea id=\"f".ucfirst($classePrefixe)."_".$field_name."_".$lang_props['libellecourt']."\" class=\"".$lang_props['libellecourt']."\" name=\"f".ucfirst($classePrefixe)."_".$field_name."_".$lang_props['libellecourt']."\" cols=\"60\" rows=\"4\" style=\"font-size:11px\">".$eTslValue."</textarea>";
	if (sizeof($langpile) > 1)
		echo "<span class='".$lang_props['libellecourt']."'>&nbsp;".$lang_props['libellecourt']."</span>";
	echo " <a class='".$lang_props['libellecourt']."' href=\"javascript:openWYSYWYGWindow('http://".$_SERVER['HTTP_HOST']."/backoffice/cms/utils/popup_wysiwyg.php', 'wysiwyg', 600, 600, 'scrollbars=yes', 'true','f".ucfirst($classePrefixe)."_".$field_name."_".$lang_props['libellecourt']."', 'add_".$classePrefixe."_form');\" title=\"HTML editor\"><img src=\"/backoffice/cms/img/bt_popup_wysiwyg.gif\" id=\"wysiwyg\" style=\"cursor: pointer; border: 1px solid red;\" alt=\"HTML editor\" onmouseover=\"this.style.background='red';\" onmouseout=\"this.style.background=''\" /></a><br />\n";
 
}
?>



</td>
</tr>

<tr>
<td width="141" align="right" bgcolor="#E6E6E6" class="arbo">&nbsp;<u><b>URL</b></u></td>
<td width="535" align="left" bgcolor="#EEEEEE" class="arbo"><input type="hidden" id="fImg_metadata" name="fImg_metadata" value="-1" />

<?php
foreach ($langpile as $lang_id => $lang_props) {
	if ($operation == 'UPDATE')
		$eTslValue = str_replace('"', '&quot;', $translator->getByID($eKeyValue, $lang_id));
	elseif ($operation == 'INSERT')
		$eTslValue = '';					
	
	$field_name = 'url'; 	
		 
	echo "<input type=\"text\" id=\"f".ucfirst($classePrefixe)."_".$field_name."_".$lang_props['libellecourt']."\" class=\"".$lang_props['libellecourt']."\" name=\"f".ucfirst($classePrefixe)."_".$field_name."_".$lang_props['libellecourt']."\" value=\"".$eTslValue."\" size=\"69\"/>";
	if (sizeof($langpile) > 1)
		echo "<span class='".$lang_props['libellecourt']."'>&nbsp;".$lang_props['libellecourt']."</span>";
 	echo "&nbsp;<a class='".$lang_props['libellecourt']."' href=\"javascript:openLinkWindow('/backoffice/cms/utils/popup/dir.php', 'links', 600, 600, 'scrollbars=yes', 'true','f".ucfirst($classePrefixe)."_".$field_name."_".$lang_props['libellecourt']."', 'add_".$classePrefixe."_form');\" title=\"Link picker\"><img src=\"/backoffice/cms/img/bt_popup_url.png\" id=\"wysiwyg\" style=\"cursor: pointer; border: 1px solid red;\" alt=\"Link picker\" onmouseover=\"this.style.background='red';\" onmouseout=\"this.style.background=''\" /></a>\n"; 
}
?>



</td>
</tr>

 

 
  
  <tr>
   <td bgcolor="#E6E6E6"  align="right" class="arbo">&nbsp;</td>
   <td bgcolor="#EEEEEE"  align="left" class="arbo">(* champs obligatoires)</td>
 </tr>
 <tr>
   <td bgcolor="#E6E6E6"  align="right" class="arbo">&nbsp;</td>
   <td bgcolor="#EEEEEE"  align="left" class="arbo">&nbsp;</td>
 </tr>
 <tr>
  <td bgcolor="#D2D2D2" colspan="2"  align="center" class="arbo">
  <input name="button" type="button" class="arbo" onclick="annulerForm();" value="<< Retour">&nbsp;
  <input class="arbo" type="button" name="Ajouter" value="Enregistrer >>" onclick="validerForm()"></td>
 </tr>
</table>
<br /><br />
</form>