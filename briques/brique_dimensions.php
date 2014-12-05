<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
	// sponthus 02/11/2005
	// changement des dimensions de la brique

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
 include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

	activateMenu('gestionbrique');  //permet de dérouler le menu contextuellement

	///////////////////////////////////////
	// récupération des valeurs postées

	// objet brique
	$id = $_POST['id'];

	// action à entreprendre
	$operation = $_POST['operation'];
	///////////////////////////////////////

	$oContent = new Cms_content($id);

	if ($operation == "DIMENSIONS") 
	{
		$oContent->setWidth_content($_POST['fWidth_content']);
		$oContent->setHeight_content($_POST['fHeight_content']);

		$retour = updateDimensions($oContent);

		if ($retour == true) {
?><script>
	document.location = "arbo_browse.php";
</script><?php
		}
	}

?>
<script type="text/javascript">document.title="Arbo - Briques";</script>
<script>

	// validation du formulaire : modification des dimensions
	function valider()
	{
		document.dimensionsBriqueForm.operation.value = "DIMENSIONS";
		document.dimensionsBriqueForm.action = "brique_dimensions.php";
		document.dimensionsBriqueForm.submit();
	}

	// annulation du formulaire : retour
	function annuler()
	{
		document.dimensionsBriqueForm.action = "arbo_browse.php";
		document.dimensionsBriqueForm.submit();
	}

</script>
<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<form name="dimensionsBriqueForm" method="post">
<input type="hidden" name="operation" value="<?php echo $operation; ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<table  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="130"><img src="../../backoffice/cms/img/tt_gestion_brique.gif" width="127" height="18"></td>
      <td class="arbo" style="vertical-align:middle"> &gt; dimensions de la brique</td>
    </tr>
  </table>
<br />
<table cellpadding="5" cellspacing="0" class="arbo">
	<tr>
		<td align="right">Largeur</td>
		<td><input type="text" name="fWidth_content" id="fWidth_content" value="<?php echo $oContent->getWidth_content(); ?>" class="arbo" size="5" maxlength="4">&nbsp;pixels</td>
	</tr>
	<tr>
		<td align="left">Hauteur</td>
		<tD><input type="text" name="fHeight_content" id="fHeight_content" value="<?php echo $oContent->getHeight_content(); ?>" class="arbo" size="5" maxlength="4"> 
		pixels </tD>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td align="center" colspan="2">
<input type="button" onClick="valider()" value="<?php $translator->echoTransByCode('btValider'); ?>" class="arbo">&nbsp;
<input type="button" onClick="annuler()" value="<?php $translator->echoTransByCode('btAnnuler'); ?>" class="arbo">
</td>
	</tr>
</table>
</form>