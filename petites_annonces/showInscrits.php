<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
sponthus 27/09/05 visualisation d'un inscrit
*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/PetitesAnnonces/include_pa.php');

activateMenu('gestionPA');  //permet de dérouler le menu contextuellement


// l'inscrit
$oInscrit = dbGetObjectFromPK("Inscript", $_POST['id']);

?>
<div class="arbo2"><strong>Petites annonces - Visualisation d'un inscrit</strong></div><p>&nbsp;</p>

<table align="center" border="0" cellpadding="5" cellspacing="0" class="arbo">
 <tr>
  <td nowrap="nowrap" align="right">&nbsp;<u><b>Date d'inscription</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="left" class="arbo">&nbsp;<?php echo $oInscrit->getDt() ?>&nbsp;</td>
</tr>
 <tr>
  <td nowrap="nowrap" align="right">&nbsp;<u><b>Date de dernière connexion</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="left" class="arbo">&nbsp;<?php echo $oInscrit->getConndt() ?>&nbsp;</td>
</tr>
 <tr>
  <td nowrap="nowrap" align="right">&nbsp;<u><b>Date de rappeln</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="left" class="arbo">&nbsp;<?php echo $oInscrit->getRappeldt() ?>&nbsp;</td>
</tr>
<tr>
  <td nowrap="nowrap" align="right">&nbsp;<u><b>Validé</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="left" class="arbo">&nbsp;<?php echo ($oInscrit->valide) ? "Oui&nbsp;" : "Non&nbsp;"; ?>&nbsp;</td>
</tr>
<tr>
  <td nowrap="nowrap" align="right">&nbsp;<u><b>Email</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="left" class="arbo">&nbsp;<?php echo $oInscrit->mail; ?>&nbsp;</td>
</tr>
<tr>
  <td nowrap="nowrap" align="right">&nbsp;<u><b>Nom</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="left" class="arbo">&nbsp;<?php echo $oInscrit->nom; ?>&nbsp;</td>
</tr>
<tr>
  <td nowrap="nowrap" align="right">&nbsp;<u><b>Prénom</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="left" class="arbo">&nbsp;<?php echo $oInscrit->prenom; ?>&nbsp;</td>
</tr>
<tr>
  <td nowrap="nowrap" align="right">&nbsp;<u><b>Tel</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="left" class="arbo">&nbsp;<?php echo $oInscrit->telephone; ?>&nbsp;</td>
</tr>
<tr>
  <td nowrap="nowrap" align="right">&nbsp;<u><b>Adresse</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="left" class="arbo">&nbsp;<?php echo $oInscrit->adresse; ?>&nbsp;</td>
</tr>
<tr>
  <td nowrap="nowrap" align="right">&nbsp;<u><b>Code Postal</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="left" class="arbo">&nbsp;<?php echo $oInscrit->codePostal; ?>&nbsp;</td>
</tr>
<tr>
  <td nowrap="nowrap" align="right">&nbsp;<u><b>Ville</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="left" class="arbo">&nbsp;<?php echo $oInscrit->ville; ?>&nbsp;</td>
  </tr>
</table>
