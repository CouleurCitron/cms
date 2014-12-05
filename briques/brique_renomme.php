<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
	// sponthus 27/10/2005
	// renommage de brique

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

	if ($operation == "RENOMME") 
	{
		$oContent->setName_content($_POST['sNewName']);
		$oContent->updateNom();

?><script>
	document.location = "arbo_browse.php";
</script><?php
	}

?>
<script type="text/javascript">document.title="Arbo - Briques";</script>
<script>

	// validation du formulaire : renommage
	function valider()
	{
		document.renommeBriqueForm.operation.value = "RENOMME";
		document.renommeBriqueForm.action = "brique_renomme.php";
		document.renommeBriqueForm.submit();
	}

	// annulation du formulaire : retour
	function annuler()
	{
		document.renommeBriqueForm.action = "arbo_browse.php";
		document.renommeBriqueForm.submit();
	}

</script>
<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<form name="renommeBriqueForm" method="post">
<input type="hidden" name="operation" value="<?php echo $operation; ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<table  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="130"><img src="../../backoffice/cms/img/tt_gestion_brique.gif" width="127" height="18"></td>
      <td class="arbo" style="vertical-align:middle"> &gt; renommage de brique</td>
    </tr>
  </table>
<br />
<table cellpadding="5" cellspacing="0" class="arbo">
	<tr>
		<td align="right">ancien nom</td>
		<td><?php echo $oContent->getName_content(); ?></td>
	</tr>
	<tr>
		<td align="left">nouveau nom</td>
		<tD><input type="text" name="sNewName" value="<?php echo $oContent->getName_content(); ?>" class="arbo" size="60"></tD>
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