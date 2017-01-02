<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

include('cms-inc/autoClass/show.php');

$submit_URL = $_SERVER['PHP_SELF'];
if (!empty($_SERVER['QUERY_STRING']))
	$submit_URL .= '?'.$_SERVER['QUERY_STRING'];

// handle POST actions and data
switch ($_POST['operation']) {
	case	'ADD_TRANSLATION'	:	$translation = new cms_chaine_traduite();
						$translation->set_id_reference($_POST['ref']);
						$translation->set_id_langue($_POST['lang']);
						$translation->set_chaine($_POST['field_libelle_'.$_POST['lang']]);
						$id = dbInsertWithAutoKey($translation);
						break;

	case	'EDIT_TRANSLATION'	:	$translation = new cms_chaine_traduite($_POST['field_id_'.$_POST['lang']]);
						$translation->set_chaine($_POST['field_libelle_'.$_POST['lang']]);
						$id = dbUpdate($translation);
						break;

	case	'DEL_TRANSLATION'	:	$translation = new cms_chaine_traduite($_POST['field_id_'.$_POST['lang']]);
						$result = dbDelete($translation);
						//echo "test delete : ".$delete."<br/>";
						break;
}

?>

<br/><br/>
<span class="arbo2">MODULE Traduction >&nbsp;</span><span class="arbo3">Chaines traduites</span>
<br/><br/>
<script type="text/javascript">

	// affiche l'outil de formulaire
	function showTranslation(index) {
		document.getElementById('lang_libelle_on_'+index).style.display = 'inline';
		document.getElementById('lang_libelle_off_'+index).style.display = 'none';
	}	
	
	// ajout d'un enregistrement
	function addTranslation(index){
		//alert(document.translation_form);
		if (document.translation_form['field_libelle_'+index].value != '') {
			document.translation_form.operation.value = "ADD_TRANSLATION";
			document.translation_form.lang.value = index;
			document.translation_form.submit();
		} else {
			alert("Vous ne pouvez donner une valeur de traduction vide !");
			document.translation_form['field_libelle_'+index].focus();
		}
	}
	
	// modification de l'enregistrement
	function editTranslation(index) {
		if (document.translation_form['field_libelle_'+index].value != '') {
			document.translation_form.operation.value = "EDIT_TRANSLATION";
			document.translation_form.lang.value = index;
			document.translation_form.submit();
		} else {
			alert("Vous ne pouvez donner une valeur de traduction vide !");
			document.translation_form['field_libelle_'+index].focus();
		}
	}

	// suppression de l'enregtistrement
	function delTranslation(index)	{
		sMessage = "Etes vous sur(e) de vouloir supprimer cet enregistrement ?";
  		if (confirm(sMessage)) {
			document.translation_form.operation.value = "DEL_TRANSLATION";
			document.translation_form.lang.value = index;
			document.translation_form.submit();
		}
	}	

</script>
<div align="center" class="arbo">

<form name="translation_form" id="translation_form" action="<?php echo $submit_URL; ?>" method="post">

<input type="hidden" name="ref" id="ref" value="<?php echo $_GET['id'] ?>" />
<input type="hidden" name="lang" id="lang" value="" />
<input type="hidden" name="operation" id="operation" value="" />

<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo" width="100%">
<?php

// chaines traduites

//$translator =& TslManager::getInstance();
//$langpile = $translator->getLanguages();

// on reinitialise la table stack
eval("$"."oRes = new cms_chaine_traduite();");
unset($stack);
$stack = array();
$sXML = $oRes->XML;
xmlStringParse($sXML);

$classeName = $stack[0]["attrs"]["NAME"];
$classePrefixe = $stack[0]["attrs"]["PREFIX"];
$aNodeToSort = $stack[0]["children"];
//pre_dump($stack[0]);

// obtention de la requete
//$sql = "SELECT ".$classeName.".* ";
//$sql.= dbMakeRequeteWithCriteres2($classeName, $aRecherche, $aGetterOrderBy, $aGetterSensOrderBy);
$aListe_res = dbGetObjectsFromFieldValue("cms_chaine_traduite", array("get_id_reference"),  array($_GET['id']), NULL);

?>
<table border="0" align="center" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo" width="100%">
<tr class="col_titre">
<td align="center" nowrap>&nbsp;<b>Actions</b>&nbsp;</td>
<?php

for ($i=0;$i<count($aNodeToSort);$i++) {
	if ($aNodeToSort[$i]["name"] == "ITEM") {
		if (($aNodeToSort[$i]["attrs"]["LIST"] == "true")) {
			echo "<td align=\"center\" nowrap>&nbsp;<b>";
			if (isset($aNodeToSort[$i]["attrs"]["LIBELLE"]) && ($aNodeToSort[$i]["attrs"]["LIBELLE"] != ""))
				echo stripslashes($aNodeToSort[$i]["attrs"]["LIBELLE"]);
			else	echo $aNodeToSort[$i]["attrs"]["NAME"];
			echo "</b>&nbsp;</td>\n";
		}
	}
}

?>
</tr>
<?php

if (count($langpile) > 0) {
	foreach ($langpile as $lang_id => $lang_props) {
		if ($lang_id == DEF_APP_LANGUE)
			continue;
		$found_it = false;
		for ($k=0; $k<sizeof($aListe_res); $k++) {
			$oRes = $aListe_res[$k];
			if ($oRes->get_id_langue() == $lang_id) {
				$found_it = true;
				break;
			}
		}

?>
<tr class="<?php echo htmlImpairePaire($k);?>">
	<td align="left" nowrap class="arbo" style="padding-left:15px;">
<?php
		if ($found_it) {
?>
		<a href="javascript:showTranslation(<?php echo $lang_id; ?>)" title="Modifier"><img src="/backoffice/cms/img/2013/icone/modifier.png" border="0" alt="Modifier" align="top" /></a>&nbsp;
		<a href="javascript:delTranslation(<?php echo $lang_id; ?>)" title="Supprimer"><img src="/backoffice/cms/img/2013/icone/supprimer.png" border="0" alt="Supprimer" align="top" /></a>&nbsp;
<?php
		} else {
?>
		<a href="javascript:showTranslation(<?php echo $lang_id; ?>)" title="Ajouter"><img src="/backoffice/cms/img/ajouter.gif" border="0" alt="Ajouter" align="top" /></a>&nbsp;
<?php
		}
?>
	</td>
<?php
   
		if ($found_it) {
			echo "	<td align=\"center\" class=\"arbo\">&nbsp;".$oRes->get_id()."</td>
				<td align=\"center\" class=\"arbo\">&nbsp;{$lang_props['libellecourt']}</td>
				<td align=\"center\" class=\"arbo\" width=\"60%\">&nbsp;
					<div style=\"display: inline;\" id=\"lang_libelle_off_{$lang_id}\">".$oRes->get_chaine()."</div>
					<div style=\"display: none;\" id=\"lang_libelle_on_{$lang_id}\">
						<textarea cols=\"60\" rows=\"6\" id=\"field_libelle_{$lang_id}\" name=\"field_libelle_{$lang_id}\">".$oRes->get_chaine()."</textarea>
						<input type=\"hidden\" id=\"field_id_{$lang_id}\" name=\"field_id_{$lang_id}\" value=\"".$oRes->get_id()."\" />&nbsp;&nbsp;
						<input type=\"button\" value=\" Enregistrer \" onclick=\"editTranslation({$lang_id})\" />
					</div>
				</td>";
		} else {
			echo "	<td align=\"center\" class=\"arbo\">&nbsp;---</td>
				<td align=\"center\" class=\"arbo\">&nbsp;{$lang_props['libellecourt']}</td>
				<td align=\"center\" class=\"arbo\" width=\"60%\">&nbsp;
					<div style=\"display: inline;\" id=\"lang_libelle_off_{$lang_id}\">(vide)</div>
					<div style=\"display: none;\" id=\"lang_libelle_on_{$lang_id}\">
						<textarea cols=\"60\" rows=\"6\" id=\"field_libelle_{$lang_id}\" name=\"field_libelle_{$lang_id}\"></textarea>
						&nbsp;&nbsp;
						<input type=\"button\" value=\" Enregistrer \" onclick=\"addTranslation({$lang_id})\" />
					</div>
				</td>";
		}
?>
</tr>
<?php
	}
?>
</table>
</form>
<?php
}
?>

</div>

