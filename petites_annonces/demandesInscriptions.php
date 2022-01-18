<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: demandesInscriptions.php,v 1.1 2013-09-30 09:42:07 raphael Exp $
	$Author: raphael $

	$Log: demandesInscriptions.php,v $
	Revision 1.1  2013-09-30 09:42:07  raphael
	*** empty log message ***

	Revision 1.9  2013-03-01 10:28:18  pierre
	*** empty log message ***

	Revision 1.8  2012-07-31 14:24:49  pierre
	*** empty log message ***

	Revision 1.7  2012-04-13 07:18:38  pierre
	*** empty log message ***

	Revision 1.6  2010-10-06 13:28:28  thao
	*** empty log message ***

	Revision 1.5  2009-09-24 08:53:13  pierre
	*** empty log message ***

	Revision 1.4  2009-02-16 16:24:18  pierre
	*** empty log message ***

	Revision 1.3  2009-02-16 15:33:36  pierre
	*** empty log message ***

	Revision 1.2  2009-02-16 15:28:05  pierre
	*** empty log message ***

	Revision 1.1  2009-02-16 14:56:09  pierre
	*** empty log message ***

	Revision 1.3  2008/03/04 14:54:19  pierre
	*** empty log message ***
	
	Revision 1.2  2006/10/19 16:22:56  pierre
	*** empty log message ***
	
	Revision 1.1  2006/04/12 07:25:22  sylvie
	*** empty log message ***
	
	Revision 1.1  2004/06/01 14:25:19  ddinside
	ajout petites annonce dont gardes finies
	newslettrer
	
	Revision 1.1  2004/05/18 13:47:12  ddinside
	creation module petites annonces
	
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/PetitesAnnonces/include_pa.php');

activateMenu('gestionPA');  //permet de dérouler le menu contextuellement

$aGetterWhere = array();
$aValeurChamp = array();

$aGetterWhere[] = "getValid";
$aValeurChamp[] = "0";

$aGetterWhere[] = "getId_site";
$aValeurChamp[] = $_SESSION['idSite_travail'];

$aGetterOrderBy = array();
$aGetterSensOrderBy = array();

$aGetterOrderBy[] = "getDt";
$aGetterSensOrderBy[] = "DESC";

$listeInscrits = dbGetObjectsFromFieldValue2("Inscript", $aGetterWhere, $aValeurChamp, $aGetterOrderBy, $aGetterSensOrderBy);

?>
<form method="post" name="listInscritForm" id="listInscritForm">
<input type="hidden" name="id" id="id" />
<style type="text/css">
	.lignevalidee {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 12px;
		text-decoration: none;
		font-weight: normal;
		letter-spacing: normal;
	}
	.lignenonvalidee {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 12px;
		text-decoration: none;
		font-weight: bold;
		letter-spacing: normal;
	}
</style>
<script type="text/javascript">

// affichage d'un inscrit
function afficher_inscrit(id)
{
	document.listInscritForm.id.value = id;
	document.listInscritForm.action = "showInscrits.php";
	document.listInscritForm.submit();
}

</script>
<div class="arbo2"><strong>Petites annonces - demandes d'inscription</strong></div><br />
<?php
if(newSizeOf($listeInscrits)>0) {
?>
<table align="center" border="1" cellpadding="1" cellspacing="0" class="arbo">
 <tr>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Date d'inscription</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Validé</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Email</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Nom</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Prénom</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Supprimer</b></u>&nbsp;</td>
 </tr>
<?php
foreach( $listeInscrits as $k => $inscrit) {
?>
 <tr class="<?php echo ($inscrit->valide)? "lignevalidee" : "lignenonvalidee"; ?>">
  <td nowrap="nowrap" align="center" class="arbo">&nbsp;<?php echo $inscrit->dateInscription ?>&nbsp;</td>
  <td nowrap="nowrap" align="center" class="arbo">&nbsp;<?php echo ($inscrit->valide) ? "Oui&nbsp;<a href=\"validerInscription.php?id=".$inscrit->id."&action=invalider\" onMouseOver='popup(\"Définir le compte comme non validé\");' onMouseOut=\"kill();\">invalider</a>" : "Non&nbsp;<a href=\"validerInscription.php?id=".$inscrit->id."&action=valider\" onMouseOver='popup(\"Définir le compte comme validé\");' onMouseOut=\"kill()\";>valider</a>"; ?>&nbsp;</td>
  <td nowrap="nowrap" align="center" class="arbo">&nbsp;<?php echo $inscrit->mail; ?>&nbsp;</td>
  <td nowrap="nowrap" align="center" class="arbo">&nbsp;<?php echo $inscrit->nom; ?>&nbsp;</td>
  <td nowrap="nowrap" align="center" class="arbo">&nbsp;<?php echo $inscrit->prenom; ?>&nbsp;</td>
  <td nowrap="nowrap" align="center" class="arbo"><a href="javascript:afficher_inscrit(<?php echo $inscrit->id; ?>)" class="arbo">voir</a></td>
  <td nowrap="nowrap" align="center" class="arbo">&nbsp;<a href="#" onclick="if(window.confirm('Etes vous sur(e) de vouloir supprimer cet inscrit ?')) {document.location='validerInscription.php?action=supprimer&amp;id=<?php echo $inscrit->id; ?>'}" title="Supprimer cette personne"><img src="/backoffice/cms/img/2013/icone/supprimer.png" border="0" /></a>&nbsp;</td>
 </tr>
<?php
}
?>
</table>
<?php
} else {
?>
<div align="center">Aucun inscrit à afficher.</div>
<?php
}
?>
</form>