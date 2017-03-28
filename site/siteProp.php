<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
sponthus 07/06/2005

Gestion des propriétés du site

*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


activateMenu('gestiondusite');  //permet de dérouler le menu contextuellement


if (!isset($_POST['id'])) {
	$id = 1;
}
else {
	// par défaut le site de base est id=1
	$id = $_POST['id'];
}

$oSite = new Cms_site($id);
?>
<style type="text/css">
	.ligne {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 12px;
		text-decoration: none;
		font-weight: normal;
		letter-spacing: normal;
	}
</style>
<?php
if ($bResult) {
?>
<script>
	// validation du formulaire
	function valider(id, operation)
	{
		document.siteForm.action = "validerSite.php";
		document.siteForm.id.value = id;		
		document.siteForm.submit();	
	}
</script>
<form name="siteForm" method="post">

<input type="hidden" name="id" value="<?php echo $_POST['id']; ?>">

<span class="arbo2"><b>Propriétés du site</b></span>

<table align="center" border="0" cellpadding="3" cellspacing="3" class="arbo">
	<tr class="ligne">
		<td class="arbo" >Nom</td>
		<td class="arbo"><?php echo $oSite->get_name(); ?>&nbsp;</td>
	</tr>
	<tr class="ligne">
		<td class="arbo">URL</td>
		<td class="arbo"><?php echo $oSite->getUrl_site(); ?>&nbsp;</td>
	</tr>
	<tr class="ligne">
		<td class="arbo">Fonctionnalités</td>
		<td class="arbo">
<?php
// les fonctionnalités du site
$aFonct = explode(";", $oSite->getFonct_site());

for ($a=0; $a<sizeof($aFonct); $a++)
{
	$sLibFonct = getLibelleDefineWithCode($aFonct[$a]);
?>
	<?php echo $sLibFonct; ?><?php if ($a != sizeof($aFonct)-1) { ?><br><?php } ?>
<?php
}
?>		
		&nbsp;</td>
	</tr>
</table>

<br>
<div align="center" class="arbo"><input type="button" class="arbo" value="Modifier" onClick="javascript:valider(<?php echo $oSite->get_id(); ?>, 'UPDATE')"></div>

</form>
<?php
} else {
?><div align="center" class="arbo">Les propriétés du site ne sont pas définies</div>
<?php
}
?>