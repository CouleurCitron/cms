<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
// sponthus 27/09/05

// remunérotation des petites annonces

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/PetitesAnnonces/include_pa.php');

activateMenu('gestionPA');  //permet de dérouler le menu contextuellement


// compte le nombre de petites annonces à renuméroter
$sql = " SELECT count(*) FROM pa_annonces";
$ePA = dbGetUniqueValueFromRequete($sql);


// renumérotation de toutes les petites annonces
if ($_POST['actiontodo'] == "RENUMEROTATION") {

$sResultat = renumerote();

}

?>
<script>
	function renumerote()
	{
		document.numeroteForm.actiontodo.value = "RENUMEROTATION";
		document.numeroteForm.action="renumerotation.php";
		document.numeroteForm.submit();
	}
</script>
<div class="arbo2"><strong>Petites annonces - Renumérotation</strong></div><br />
<form method="post" name="numeroteForm" id="numeroteForm">

<input type="hidden" name="actiontodo" value="" />

<div align="left" class="arbo"><?php echo $ePA; ?> petites annonces à renuméroter</div><br />
<br />

<input type="button" name="btRenumerote" onclick="javascript:renumerote()" class="arbo" value="Lancer la renum&eacute;rotation" />

<br />
<br />
<div align="left"><?php echo $sResultat; ?></div>

</form>