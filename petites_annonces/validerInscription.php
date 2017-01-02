<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: validerInscription.php,v 1.1 2013-09-30 09:42:18 raphael Exp $
	$Author: raphael $

	$Log: validerInscription.php,v $
	Revision 1.1  2013-09-30 09:42:18  raphael
	*** empty log message ***

	Revision 1.6  2013-03-01 10:28:18  pierre
	*** empty log message ***

	Revision 1.5  2012-07-31 14:24:50  pierre
	*** empty log message ***

	Revision 1.4  2009-02-16 16:24:18  pierre
	*** empty log message ***

	Revision 1.3  2009-02-16 15:33:37  pierre
	*** empty log message ***

	Revision 1.2  2009-02-16 15:28:05  pierre
	*** empty log message ***

	Revision 1.1  2009-02-16 14:56:09  pierre
	*** empty log message ***

	Revision 1.2  2008/03/04 14:54:19  pierre
	*** empty log message ***
	
	Revision 1.1  2006/04/12 07:25:22  sylvie
	*** empty log message ***
	
	Revision 1.1  2004/06/01 14:25:19  ddinside
	ajout petites annonce dont gardes finies
	newslettrer
	
	Revision 1.1  2004/05/18 13:47:12  ddinside
	creation module petites annonces
	
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/PetitesAnnonces/include_pa.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/mail_lib.php');

activateMenu('gestionPA');  //permet de dérouler le menu contextuellement

if (!( (strlen($_GET['id'])>0) && ($_GET['id']>0) )) {
	die("Appel incorrect à la fonctionnalité.");
}

$status = '&nbsp;';
$inscrit = new Inscript($_GET['id']);

if(!strlen($_GET['action'])>0)
	$_GET['action'] = 'valider';
if($_GET['action']=='valider') {
	if($inscrit->validate()) $status = "Validation enregistrée.";
} elseif($_GET['action']=='invalider') {
	if($inscrit->unvalidate()) $status = "Inscrit invalidé.";
} elseif($_GET['action']=='supprimer') {
	if($inscrit->delete()) $status = "Inscrit supprimé.";
}
if($_GET['action']!='supprimer') {
?>
<div class="arbo2"><strong>Petites annonces - Liste des inscrits - validation</strong></div>
<div align="center"><b><?php echo $status; ?></b></div>
<br />
<br />
<table align="center" border="1" cellpadding="0" cellspacing="0">
 <tr>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Date d'inscription</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Validé</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Email</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Nom</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Prénom</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Tel</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Adresse</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Code Postal</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Ville</b></u>&nbsp;</td>
 </tr>
<tr>
  <td nowrap="nowrap" align="center">&nbsp;<?php echo from_dbdate_TIMESTAMP_WITH_TIME_ZONE($inscrit->dateInscription); ?>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<?php echo ($inscrit->valide) ? "oui" : "non" ; ?>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<?php echo $inscrit->mail; ?>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<?php echo $inscrit->nom; ?>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<?php echo $inscrit->prenom; ?>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<?php echo $inscrit->telephone; ?>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<?php echo $inscrit->adresse; ?>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<?php echo $inscrit->codePostal; ?>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<?php echo $inscrit->ville; ?>&nbsp;</td>
</tr>
</table>
<?php
} else {
?>
<div align="center"><b><?php echo $status; ?></b></div>
<?php 
}
?>
<br />
<br />
Retour à la <a href="listeInscrits.php">liste des inscrits</a>
