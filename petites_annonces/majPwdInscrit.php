<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*

sponthus 27/09/2005
modification du mot de passe d'un inscrit

*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/PetitesAnnonces/include_pa.php');

activateMenu('gestionPA');  //permet de dérouler le menu contextuellement

?><span class="arbo2"><b>Modifier le mot de passe d'un inscrit</b></span><?php

	if (!( (strlen($_POST['id']) > 0) && ($_POST['id'] > 0) )) {
		die ("Appel incorrect de la fonctionnalité");
	} else {
		// modification d'un inscrit
		$oInscrit = new Inscript($_POST['id']);
	}
	
	// maj BDD
	if ($_POST['operation'] == "UPDATE") {

		// réinitisalisation du mot de passe
		$oInscrit->mdpCrypte = "";

		// nouveau mot de passe
		$oInscrit->mdpNonCrypte = $_POST['mdp'];

		// modif inscrit
		$oInscrit->id = $_POST['id'];

		// update BDD
		$bRetour = $oInscrit->update_pwd($bPwd);
				
		if ($bRetour)
			$status.= 'Mot de passe enregistré';
		else
			$status.= 'Erreur lors de la mise à jour du mot de passe';
	}


?>
<style type="text/css">
	.lignevalidee {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 12px;
		text-decoration: none;
		font-weight: normal;
		letter-spacing: normal;
	}
	tr.lignevalidee:hover {
		background-color: #DDEAAB;
	}
	.lignenonvalidee {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 12px;
		text-decoration: none;
		font-weight: bold;
		letter-spacing: normal;
	}
	tr.lignenonvalidee:hover {
		background-color: #DDEAAB;
	}
</style>
<script type="text/javascript">

	function pwd_ok()
	{
		bResult = true;
		if (document.majPwdInscrit.mdp.value != document.majPwdInscrit.mdp2.value) {
			sMessage = "Erreur dans la saisie du mot de passe\n";
			sMessage+= "Le mot de passe et sa confirmation ne sont pas identiques";
			alert(sMessage);
			bResult = false;
		}
		return bResult;
	}

	// validation de l'inscrit
	function validerInscrit()
	{
		if (validate_form(0) && pwd_ok()) 
		{ 
			document.getElementById('operation').value = "UPDATE"; 
			document.majPwdInscrit.submit(); 
		}
	}
	
</script>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="majPwdInscrit" id="majPwdInscrit">

<input type="hidden" name="operation" id="operation" value="<?php echo $_POST['operation']; ?>" />
<input type="hidden" name="urlRetour" id="urlRetour" value="<?php echo $_POST['urlRetour']; ?>" />
<input type="hidden" name="id" id="id" value="<?php echo $_POST['id']; ?>" />


<table align="center" border="0" cellpadding="2" cellspacing="2" class="arbo">
 <tr>
  <td nowrap="nowrap" align="right">&nbsp;<u><b>Inscrit</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="left"><?php echo $oInscrit->mail ;?></td>
 </tr>
 <tr>
  <td nowrap="nowrap" align="right">&nbsp;<u><b>Mot de passe</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="left"><input type="password" class="arbo" name="mdp" id="mdp" value="" pattern=".+" errormsg="Mot de passe obligatoire" /></td>
 </tr>
 <tr>
  <td nowrap="nowrap" align="right">&nbsp;<u><b>Confirmation</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="left"><input type="password" class="arbo" name="mdp2" id="mdp2" value="" pattern=".+" errormsg="Confirmation obligatoire" /></td>
 </tr>
 <tr>
   <td nowrap="nowrap" align="right">&nbsp;</td>
   <td nowrap="nowrap" align="left">&nbsp;</td>
 </tr>
<?php
if(strlen($_POST['mail'])==0) {
?>
 <tr>
  <td colspan="2" nowrap="nowrap" align="center"><input type="button" class="arbo" name="Ajouter" value="<?php $translator->echoTransByCode('btValider'); ?>" onclick="javascript:validerInscrit();" /></td>
 </tr>
<?php
}
?>
</table>
</form>
<br />
<br />
<div align="center" class="arbo"><b><?php echo $status; ?></b></div>
<?php
// si une page retour est spécifiée -> lien retour
if ($_POST['urlRetour'] != "") {
?>
<div align="center"><a href="<?php echo $_POST['urlRetour']; ?>" class="arbo">retour</a></div>
<?php
}
?>