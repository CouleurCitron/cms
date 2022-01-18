<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: listeCategories.php,v 1.1 2013-09-30 09:42:11 raphael Exp $
	$Author: raphael $

	$Log: listeCategories.php,v $
	Revision 1.1  2013-09-30 09:42:11  raphael
	*** empty log message ***

	Revision 1.8  2013-03-01 10:28:18  pierre
	*** empty log message ***

	Revision 1.7  2012-07-31 14:24:49  pierre
	*** empty log message ***

	Revision 1.6  2012-04-13 07:18:38  pierre
	*** empty log message ***

	Revision 1.5  2010-10-06 13:28:28  thao
	*** empty log message ***

	Revision 1.4  2009-02-16 16:24:18  pierre
	*** empty log message ***

	Revision 1.3  2009-02-16 15:33:36  pierre
	*** empty log message ***

	Revision 1.2  2009-02-16 15:28:05  pierre
	*** empty log message ***

	Revision 1.1  2009-02-16 14:56:09  pierre
	*** empty log message ***

	Revision 1.2  2008/03/04 14:54:19  pierre
	*** empty log message ***
	
	Revision 1.1  2006/04/12 07:25:22  sylvie
	*** empty log message ***
	
	Revision 1.2  2004/06/02 13:39:08  ddinside
	commit version finale garde partagee, reste petites annonces à faire
	
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

$categorie = new Categorie();

$aGetterWhere = array();
$aValeurChamp = array();

$aGetterOrderBy = array();
$aGetterSensOrderBy = array();

$aGetterOrderBy[] = "getIs_garde";
$aGetterSensOrderBy[] = "ASC";

$aGetterOrderBy[] = "getOrdre";
$aGetterSensOrderBy[] = "ASC";


$listeCategories = dbGetObjectsFromFieldValue2("Categorie", $aGetterWhere, $aValeurChamp, $aGetterOrderBy, $aGetterSensOrderBy);

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

// suppresion de la catégorie
function delete_categorie(eAnnonce, id)
{
	if (eAnnonce == 0) {
		sMsg = "Etes vous sur(e) de vouloir supprimer cette categorie ?";
		if (confirm(sMsg)) {
			document.location = "validerCategorie.php?id="+id+"&action=supprimer";
		}
	} else {
		sMsg = "Vous ne pouvez supprimer cette catégorie, " + eAnnonce + " annonces lui sont liées";
		alert(sMsg);
	}
}

</script>
<div class="arbo2"><strong>Petites annonces - liste des catégories</strong></div><br />
<?php
if(newSizeOf($listeCategories)>0) {
?>
<table align="center" border="1" cellpadding="0" cellspacing="0">
 <tr>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>N° Id</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Libellé</b></u>&nbsp;</td>
<?php 
// garde et classqiue : spécifique boulogne
if ($_SESSION['idSite_travail'] == 1) {
?>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Type</b></u>&nbsp;</td>
<?php
}
?>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Ordre</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<b>---</b>&nbsp;</td>
 </tr>
<?php
foreach( $listeCategories as $k => $categorie) {

// compte le nombre d'annonces liées à cette catégorie
$sql = " SELECT count(*) FROM pa_annonces WHERE annonce_categorie_id=".$categorie->id;
$eCount = dbGetUniqueValueFromRequete($sql);
?>
 <tr class="lignevalidee">
  <td nowrap="nowrap" align="center">&nbsp;<a href="validerCategorie.php?id=<?php echo $categorie->id;?>" onmouseover='popup(&quot;Visualiser cette annonce&quot;);' onmouseout="kill();"><?php echo $categorie->id; ?>&nbsp;</a></td>
  <td nowrap="nowrap" align="center">&nbsp;<?php echo $categorie->libelle; ?>&nbsp;</td>
<?php 
// garde et classqiue : spécifique boulogne
if ($_SESSION['idSite_travail'] == 1) {
?>
  <td nowrap="nowrap" align="center">&nbsp;<?php echo ($categorie->isGarde) ? "Garde" : "classique" ; ?>&nbsp;</td>
<?php
}
?>
  <td nowrap="nowrap" align="center">&nbsp;<?php echo $categorie->ordre; ?>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<a href="javascript:delete_categorie(<?php echo $eCount; ?>, <?php echo $categorie->id; ?>)"><img src="/backoffice/cms/img/2013/icone/supprimer.png" border="0" /></a>&nbsp;</td>
 </tr>
<?php
}
?>
</table>
<?php
} else {
?>
<div align="center">Aucune annonce/garde à valider.</div>
<?php
}
?>
