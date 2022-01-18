<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/*
sponthus 07/06/2005

création d'un gabarit
reprise du process de création des pages et adaptation au gabarit	
*/


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');


activateMenu('gestiongabarit');  //permet de dérouler le menu contextuellement

?>

<div class="ariane"><span class="arbo2">GABARIT&nbsp;>&nbsp;</span>
<span class="arbo3">Créer un gabarit - Etape 1</span></div>
<?php

// nombre de zones editables
$nbZoneEdit = $_POST['nbZoneEdit'];
if ($nbZoneEdit == "") $nbZoneEdit = 1;


?>
<script type="text/javascript">document.title="Choix des zones éditables";</script>
<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<script type="text/javascript">

// varification du formulaire
function checkForm()
{
	erreur=0;
	sMessage = "Valeur(s) incorrecte(s) : \n\n";
	
	// le champ nbZoneEdit doit être numérique
	if (isNaN(document.gabaritForm.nbZoneEdit.value)) {
		sMessage+="- Le nombre de zones éditables doit être numérique\n";
		erreur++;
	}
	
	if (document.gabaritForm.nbZoneEdit.value!=document.gabaritForm.enumero.value && document.gabaritForm.nbZoneEdit.value!=0) {
		sMessage+="- Vous devez choisir les dimensions de toutes vos zones éditables.\nPour cela, cliquez sur le bouton AFFICHER";
		erreur++;
	}
	
	// affichage des messages d'erreur
	if (erreur > 0) {
		alert(sMessage);
		return false;
	}
	else return true;

}

// suite de création des gabarits
function suite()
{
	if (checkForm())
	{
		document.gabaritForm.action = "gabaritEditor2.php";
		document.gabaritForm.submit();
	}
}

// rechargement de la première page pour saisir 
// les noms des zones editables + les tailles par défaut
function affichZonedit()
{
	document.gabaritForm.action = "gabaritEditor.php";
	document.gabaritForm.submit();
}

</script>
<form name="gabaritForm" id="gabaritForm" method="post">
<table border="0" cellpadding="5" cellspacing="0" class="arbo">
  <tr><td>
Vous allez créer un gabarit (un modèle de page).<br>
<br></td></tr>
<tr>
  <td bgcolor="#FFFFFF">
1/ Dans ce gabarit vous allez <strong>définir des éléments qui présentent des caractéristiques communes à différentes pages.</strong></td>
</tr>
<tr><td bgcolor="#FFFFFF">
<table border="0" cellpadding="3" cellspacing="3" class="arbo">
    <tr>
	    <td>&nbsp;</td>
	    <td>Largeur</td>
	    <td>Hauteur</td>
    </tr>
    <tr>
	    <td>Taille de l'espace &eacute;ditable</td>
	    <td><input type="text" class="arbo" name="fWidth_toutelazone" id="fWidth_toutelazone" value="400" size="4" maxlength="4" /></td>
	    <td><input type="text" class="arbo" name="fHeight_toutelazone" id="fHeight_toutelazone" value="400" size="4" maxlength="4" /></td>
    </tr>
</table>
</td></tr>
<tr><td><br>
<br></td></tr>
<tr><td bgcolor="#FFFFFF">

2/ 
Vous allez aussi <strong>définir des zones éditables de contenu</strong> qui seront <strong>modifiables sur chaque page</strong></td>
</tr>
<tr><td bgcolor="#FFFFFF">
Combien de zones éditables voulez vous créer dans ce gabarit ?
<input type="text" class="arbo" id="nbZoneEdit" name="nbZoneEdit" value="<?php echo $nbZoneEdit; ?>" size="5" />&nbsp;
<input type="button" class="arbo" value="Afficher" id="Afficher" name="Afficher" onclick="javascript:affichZonedit()" />
</td></tr>
<tr><td bgcolor="#FFFFFF">
<table border="0" cellpadding="3" cellspacing="3">
<?php
if ($nbZoneEdit != 0) {
?>
	<tr>
		<tD>&nbsp;</tD>
		<td>Nom</td>
		<td>Largeur</td>
		<td>Hauteur</td>
	</tr>
<?php
}
for ($t=0; $t < $nbZoneEdit; $t++)
{
	$eNumero = $t + 1;
?>
	<tr>
		<td>zone <?php echo $eNumero; ?><input type="hidden" name="fId_<?php echo $t; ?>" id="fId_<?php echo $t; ?>" value="" /></td>
		<td><input type="text" name="fName_<?php echo $t; ?>" id="fName_<?php echo $t; ?>" value="<?php echo DEF_ZONEDIT; ?>" class="arbo" size="40" /></td>
		<td><input type="text" name="fWidth_<?php echo $t; ?>" id="fWidth_<?php echo $t; ?>" value="<?php echo DEF_ZONEDITWIDTH; ?>" class="arbo" size="4" maxlength="4" /></td>
		<td><input type="text" name="fHeight_<?php echo $t; ?>" id="fHeight_<?php echo $t; ?>" value="<?php echo DEF_ZONEDITHEIGHT; ?>" class="arbo" size="4" maxlength="4" /></td>
	</tr>
<?php
}
?><input type="hidden" id="enumero" name="enumero" value="<?php echo $eNumero; ?>" />
</table>
</td></tr>
<tr><td align="center">
<br>
<input type="button" class="arbo" value="Suite" name="Suite" id="Suite" onclick="suite()" /></td>
</tr>
</table>
</form>