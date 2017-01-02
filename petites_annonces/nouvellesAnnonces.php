<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: nouvellesAnnonces.php,v 1.1 2013-09-30 09:42:15 raphael Exp $
	$Author: raphael $

	$Log: nouvellesAnnonces.php,v $
	Revision 1.1  2013-09-30 09:42:15  raphael
	*** empty log message ***

	Revision 1.8  2013-03-01 10:28:18  pierre
	*** empty log message ***

	Revision 1.7  2012-07-31 14:24:50  pierre
	*** empty log message ***

	Revision 1.6  2012-04-13 07:18:38  pierre
	*** empty log message ***

	Revision 1.5  2010-10-06 13:28:28  thao
	*** empty log message ***

	Revision 1.4  2009-09-24 08:53:13  pierre
	*** empty log message ***

	Revision 1.3  2009-02-16 16:24:18  pierre
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
	
	Revision 1.2  2004/06/02 13:39:08  ddinside
	commit version finale garde partagee, reste petites annonces à faire
	
	Revision 1.1  2004/06/01 14:25:19  ddinside
	ajout petites annonce dont gardes finies
	newslettrer
	
	Revision 1.1  2004/05/18 13:47:12  ddinside
	creation module petites annonces
	
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/lib/aodb/adodb-pager.inc.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/PetitesAnnonces/include_pa.php');

activateMenu('gestionPA');  //permet de dérouler le menu contextuellement

// critères de recherche, de tris, constitution des listes
include('crit_recherche.php');

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
<script>
// fonction de tri
function doTri(sElementTri) {
	document.annonceForm.champTri.value = sElementTri;

	// on change de tri
	if (document.annonceForm.sensTri.value == "ASC") sSensTri = "DESC";
	else if (document.annonceForm.sensTri.value == "DESC") sSensTri = "ASC";
	else if (document.annonceForm.sensTri.value == "") sSensTri = "ASC";

	document.annonceForm.sensTri.value = sSensTri;

	document.annonceForm.action = "nouvellesAnnonces.php";
	document.annonceForm.submit();
}
// fonction de recherche
function doRecherche() {
	document.annonceForm.action = "nouvellesAnnonces.php";
	document.annonceForm.submit();
}
</script>
<form name="annonceForm" method="post">
<input type="hidden" name="sensTri" value="<?php echo $sensTri; ?>">
<input type="hidden" name="champTri" value="<?php echo $champTri; ?>">

<?php
if (!isset($_GET['valid'])) $sTitre = "toutes les annonces";
else if ($_GET['valid'] == 0) $sTitre = "les annonces non validées";
else if ($_GET['valid'] == 1) $sTitre = "les annonces validées";
?>

<div class="arbo2"><strong>Petites annonces - <?php echo $sTitre; ?></strong></div><br>

<table align="center">
<tr>
<?php 
// garde et classqiue : spécifique boulogne
if ($_SESSION['idSite_travail'] == 1) {
?>
	<td class="arbo"><?php
if ($fTypeSelect == "-1") $checked_tous_type="selected"; else $checked_tous_type="";
if ($fTypeSelect == "1") $checked_garde="selected"; else $checked_garde="";
if ($fTypeSelect == "0") $checked_classique="selected"; else $checked_classique="";
?>recherche par type : <select name="fTypeSelect" class="arbo">
	<option value="-1" <?php echo $checked_tous_type; ?> >tous les types</option>
	<option value="1" <?php echo $checked_garde; ?> >Garde</option>
	<option value="0" <?php echo $checked_classique; ?> >classique</option>
</select></td>
<?php 
} // fin if ($oCategpa->getId_site() != 1)
?>
	<td class="arbo"><?php
if ($fCategSelect == "-1") $checked_tous_categ="selected"; else $checked_tous_categ="";
?>recherche par catégorie : <select name="fCategSelect" class="arbo">
	<option value="-1" <?php echo $checked_tous_type; ?> >toutes les catégories</option><?php
for ($m=0; $m<sizeof($aListeCategpa); $m++) { 

	$oCategpa = $aListeCategpa[$m];
	if ($fCategSelect == $oCategpa->getId()) $checked_categ="selected"; else $checked_categ="";
?>
	<option value="<?php echo $oCategpa->getId(); ?>" <?php echo $checked_categ; ?> ><?php echo $oCategpa->getLibelle(); ?></option>
<?php
}
?>
</select></td>
	<td class="arbo"><input type="button" class="arbo" value="chercher" onClick="doRecherche()"></td>
</tr>
</table>
<br>
<table class="arbo" align="center">
	<tr>
		<td><?php print($pager->bandeau); ?></td>
	</tr>
</table>

<br>
<?php
if(sizeof($listeAnnonces)>0) {
?>
<table align="center" border="1" cellpadding="1" cellspacing="0" class="arbo">
 <tr>
  <td nowrap align="center" class="arbo">&nbsp;<b>N° Id</b>&nbsp;</td>
<?php 
// garde et classqiue : spécifique boulogne
if ($_SESSION['idSite_travail'] == 1) {
?>
  <td nowrap align="center" class="arbo">&nbsp;<a href="javascript:doTri('type')">Type</a>
<?php
if ($champTri == "type") print("***");
?></td>
<?php
}
?>
  <td nowrap align="center" class="arbo">&nbsp;
<b><a href="javascript:doTri('dsoumission', 'ASC')">Date soumission</a></b>&nbsp;<?php
if ($champTri == "dsoumission") print("***");
?></td>
  <td nowrap align="center" class="arbo">&nbsp;
<b><a href="javascript:doTri('dperim', 'ASC')">Date péremption</a></b>&nbsp;<?php
if ($champTri == "dperim") print("***");
?></td>
  <td align="center" nowrap class="arbo">&nbsp;
<b><a href="javascript:doTri('deposant', 'ASC')">Déposant</a></b>&nbsp;<?php
if ($champTri == "deposant") print("***");
?></td>
  <td nowrap align="center" class="arbo">&nbsp;
<a href="javascript:doTri('categorie', 'ASC')"><b>Catégorie&nbsp;</b></a>&nbsp;<?php
if ($champTri == "categorie") print("***");
?></td>
  <td nowrap align="center" class="arbo">&nbsp;
<a href="javascript:doTri('valide', 'ASC')"><b>Validée&nbsp;</b></a>&nbsp;<?php
if ($champTri == "valide") print("***");
?></td>
  <td nowrap align="valide" class="arbo">&nbsp;<b>---</b>&nbsp;</td>
 </tr>
<?php
foreach( $listeAnnonces as $k => $annonce) {

$oInscript = new Inscript($annonce->deposant);
$oCategorie = new Categorie($annonce->categorie);

?>
 <tr class="<?php echo ($annonce->valide)? "lignevalidee" : "lignenonvalidee"; ?>">
  <td nowrap align="center" class="arbo">&nbsp;<a href="detail<?php echo ($annonce->isGarde) ? "garde" : "annonce"; ?>.php?id=<?php echo $annonce->getId();?>" target="_blank" onMouseOver='popup("Visualiser cette annonce");' onMouseOut="kill();"><?php echo ($annonce->getIs_garde()) ? "GAR" : "ANN" ; ?><?php echo $annonce->getId(); ?>&nbsp;</a></td>
<?php 
// garde et classqiue : spécifique boulogne
if ($_SESSION['idSite_travail'] == 1) {
?>
  <td nowrap align="center" class="arbo">&nbsp;<?php echo ($annonce->getIs_garde()) ? "Garde" : "classique" ; ?>&nbsp;</td>
<?php
}
?>
  <td nowrap align="center" class="arbo">&nbsp;<?php echo $annonce->getDt_debut() ?>&nbsp;</td>
  <td nowrap align="center" class="arbo">&nbsp;<?php echo $annonce->getDt_perim(); ?>&nbsp;</td>
  <td nowrap align="left" class="arbo"><a 
href="mailto:<?php echo $oInscript->getMail();?>" title="<?php echo $oInscript->getMail();?>"><img src="/backoffice/cms/img/tb_sendfriend.gif" border="0"></a>&nbsp;
<?php echo $oInscript->getPrenom().'&nbsp;'.$oInscript->getNom(); ?>&nbsp;</td>
  <td nowrap align="center" class="arbo">&nbsp;<?php echo $oCategorie->getLibelle(); ?>&nbsp;</td>
  <td nowrap align="center" class="arbo">&nbsp;<?php echo ($annonce->getValid()) ? "Oui&nbsp;<a href=\"validerAnnonce.php?id=".$annonce->getId()."&action=invalider\" onMouseOver='popup(\"Définir cette annonce comme non validé\");' onMouseOut=\"kill();\">invalider</a>" : "Non&nbsp;<a href=\"validerAnnonce.php?id=".$annonce->getId()."&action=valider\" onMouseOver='popup(\"Définir cette annonce comme validé\");' onMouseOut=\"kill();\">valider</a>"; ?>&nbsp;</td>
  <td nowrap align="center" class="arbo">&nbsp;<a href="#" onClick="if(window.confirm('Etes vous sur(e) de vouloir supprimer cette annonce ?')) document.location='validerAnnonce.php?id=<?php echo $annonce->getId(); ?>&action=supprimer';" title="Supprimer cette annonce"><img src="/backoffice/cms/img/2013/icone/supprimer.png" border="0"></a>&nbsp;</td>
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
</form>
