<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: validerCategorie.php,v 1.7 2013-03-01 10:28:18 pierre Exp $
	$Author: pierre $

	$Log: validerCategorie.php,v $
	Revision 1.7  2013-03-01 10:28:18  pierre
	*** empty log message ***

	Revision 1.6  2012-07-31 14:24:50  pierre
	*** empty log message ***

	Revision 1.5  2012-04-13 07:18:38  pierre
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
	
	Revision 1.2  2004/06/04 12:35:30  ddinside
	fin petites annonces
	
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
//if (!( (strlen($_GET['id'])>0) && ($_GET['id']>0) )) {
//	die("Appel incorrect à la fonctionnalité.");
//}
$status = '&nbsp;';
$categorie = new Categorie($_GET['id']);

if(!strlen($_GET['action'])>0)
	$_GET['action'] = 'modif';

if($_GET['action']=='modif') {
	if(strlen($_POST['libelle']) > 0) {

		$categorie->ordre = $_POST['ordre'];
		if ($categorie->ordre == "") $categorie->ordre = 99;

		$categorie->libelle = $_POST['libelle'];

		$categorie->id_site = $_SESSION['idSite_travail'];

		if($_GET['id']>0) {
			$id_categ = dbUpdate($categorie);
			if($id_categ) {
				$status = "Modifications enregistrées.";
				$id_categ = $categorie->id;
			}
		} else {
			$categorie->isGarde = $_POST['is_garde'];
			$id_categ = dbInsertWithAutoKey($categorie);

			if($id_categ) {
				$status = "Catégorie ajoutée.";
			}
		}
	} else {
?>
<form action="<?php echo $_SERVER['PHP_SELF'].'?id='.$_GET['id']; ?>" method="post" name="updateCategorie" id="updateCategorie">
<div class="arbo2"><strong>Petites annonces - ajout d'une catégorie</strong></div><br />

<table align="center" border="0" cellpadding="5" cellspacing="0" class="arbo">
 <tr>
 	<td nowrap="nowrap" align="right">&nbsp;<u><b>N° Id</b></u>&nbsp;</td>
	<td align="left">&nbsp;<?php echo $categorie->id; ?>&nbsp;</td>
</tr>
<?php 
// garde et classqiue : spécifique boulogne
if ($_SESSION['idSite_travail'] == 1) {
?>
<tr>
	<td nowrap="nowrap" align="right">&nbsp;<u><b>Type</b></u>&nbsp;</td>
	<td align="left"><?php
if($_GET['id']>0) {
?><?php echo ($categorie->isGarde) ? "Garde" : "classique" ; ?><?php 
} else {
?><select name="is_garde" class="arbo">
<option value="1">Garde</option>
<option value="0">classique</option>
</select><?php 
}
?>&nbsp;</td>
 </tr>
<?php
} else {
?><tr><td colspan="2"><input type="hidden" name="is_garde" value="0" /></td></tr><?php
}
?>
 <tr>
	<td nowrap="nowrap" align="right">&nbsp;<u><b>Libellé de la catégorie</b></u>&nbsp;</td>
	<td nowrap="nowrap" align="left"><input type="text" name="libelle" size="50" maxlength="255" value="<?php echo $categorie->libelle; ?>" class="arbo" />
	  &nbsp;</td>
 </tr>
<tr>
	<td nowrap="nowrap" align="right">&nbsp;<u><b>Ordre</b></u>&nbsp;</td>
	<td nowrap="nowrap" align="left"><input type="text" name="ordre" size="2" maxlength="3" value="<?php echo $categorie->ordre; ?>" class="arbo" />
	  &nbsp;</td>
 </tr>
</table><br />
<div align="center"><input type="submit" value="Enregistrer" name="Enregistrer" class="arbo" />
</div>
</form>
<?php

			exit;
	}
} elseif($_GET['action']=='supprimer') {
	if(dbDelete($categorie)) $status = "Categorie supprimée.";
}
if($_GET['action']!='supprimer') {
?>
<div class="arbo2"><strong>Petites annonces - ajout d'une catégorie</strong></div><br />
<div align="center" class="arbo"><b><?php echo $status; ?></b></div>
<br />
<br />
<table align="center" border="0" cellpadding="5" cellspacing="0" class="arbo">
 <tr>
 	<td nowrap="nowrap" align="right">&nbsp;<u><b>N° Id</b></u>&nbsp;</td>
	<td nowrap="nowrap" align="left">&nbsp;<?php echo $id_categ; ?>&nbsp;</td>
</tr>
<?php 
// garde et classqiue : spécifique boulogne
if ($_SESSION['idSite_travail'] == 1) {
?>
<tr>
	<td nowrap="nowrap" align="right">&nbsp;<u><b>Type</b></u>&nbsp;</td>
	<td nowrap="nowrap" align="left">&nbsp;<?php echo ($categorie->isGarde) ? "Garde" : "classique" ; ?>&nbsp;</td>
</tr>
<?php
}
?>
<tr>
	<td nowrap="nowrap" align="right">&nbsp;<u><b>Libellé de la catégorie</b></u>&nbsp;</td>
	<td nowrap="nowrap" align="left">&nbsp;<?php echo $categorie->libelle; ?>&nbsp;</td>
 </tr>
 <tr>
	<td nowrap="nowrap" align="right">&nbsp;<u><b>Ordre</b></u>&nbsp;</td>
	<td nowrap="nowrap" align="left">&nbsp;<?php echo $categorie->ordre; ?>&nbsp;</td>
 </tr>
<?php

?>
</table>
<?php
} else {
?>
<div align="center" class="arbo"><b><?php echo $status; ?></b></div>
<?php 
}
?>
<br />
<br />
<div align="left" class="arbo">Retour à la <a href="listeCategories.php">liste des categories</a></div>