<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: listeInscrits.php,v 1.9 2013-03-01 10:28:18 pierre Exp $
	$Author: pierre $

	$Log: listeInscrits.php,v $
	Revision 1.9  2013-03-01 10:28:18  pierre
	*** empty log message ***

	Revision 1.8  2012-07-31 14:24:50  pierre
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

	Revision 1.5  2008-09-10 12:25:41  pierre
	*** empty log message ***

	Revision 1.4  2006/12/05 11:10:08  arnaud
	*** empty log message ***
	
	Revision 1.3  2006/12/04 15:39:58  arnaud
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


//////////////////////////
// RECHERCHE
//////////////////////////

$aRecherche = array();


//---------------------------------------------------
// critère caché : site de travail
$oRech = new dbRecherche();

$oRech->setNomBD("pa_inscrits.id_site");
$oRech->setValeurRecherche($_SESSION['idSite_travail']);
$oRech->setTableBD("pa_inscrits");

$aRecherche[] = $oRech;
//---------------------------------------------------

//---------------------------------------------------
// zone de recherche
$oRech = new dbRecherche();

$oRech->setValeurRecherche("declencher_recherche");
$oRech->setTableBD("pa_inscrits");
$sLike = "(";
$sLike.= "lower(inscrit_mail) like '%".strtolower($_POST['sRecherche'])."%' OR ";
$sLike.= "lower(inscrit_nom) like '%".strtolower($_POST['sRecherche'])."%' OR ";
$sLike.= "lower(inscrit_prenom) like '%".strtolower($_POST['sRecherche'])."%' OR ";
$sLike.= "lower(inscrit_adresse) like '%".strtolower($_POST['sRecherche'])."%' OR ";
$sLike.= "lower(inscrit_cp) like '%".strtolower($_POST['sRecherche'])."%' OR ";
$sLike.= "lower(inscrit_ville) like '%".strtolower($_POST['sRecherche'])."%' ";
$sLike.= ")";
$oRech->setJointureBD($sLike);
$oRech->setPureJointure(1);

$aRecherche[] = $oRech;
//---------------------------------------------------


//////////////////////////
// TRI
//////////////////////////

$aGetterOrderBy = array();
$aGetterSensOrderBy = array();

$aGetterOrderBy[] = "inscrit_dt";
$aGetterSensOrderBy[] = "DESC";


// obtention de la requete
$sql = "SELECT pa_inscrits.* ";
$sql.= dbMakeRequeteWithCriteres2("Inscript", $aRecherche, $aGetterOrderBy, $aGetterSensOrderBy);

//print("<br>$sql<br><br>");

// execution de la requette avec pagination
$pager = new Pagination($db, $sql, $sParam);


$pager->Render($rows_per_page=20);

//////// DEBUGAGE ////////
//$bDebug = true;
if ($bDebug) {
print("<br>///////////////////////");
print("<br>".var_dump($pager->aId));
print("<br>///////////////////////");
}
//////// DEBUGAGE ////////

// tableau d'id renvoyé par la fonction de pagination
//adubois aId est vide, le tableau desiré se trouve dans aResult. bug?
//$aId = $pager->aId;
$aId = $pager->aResult;

// A VOIR sponthus
// la fonction de pagination devrait renvoyer un tableau d'objet
// pour l'instant je n'exploite qu'un tableau d'id
// ce ui m'oblige à re sélectionner mes objets
// à perfectionner

$listeInscrits = array();
for ($m=0; $m<sizeof($aId); $m++)
{
	$listeInscrits[] = new Inscript($aId[$m]);
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

// modification du mot de passe
function change_pwd_inscrit(id)
{
	document.listInscritForm.id.value = id;
	document.listInscritForm.action = "majPwdInscrit.php";
	document.listInscritForm.submit();
}

// recherche d'inscrits
function rechercher()
{
	document.listInscritForm.action = "listeInscrits.php";
	document.listInscritForm.submit();
}



</script>
<form method="post" name="listInscritForm" id="listInscritForm">
<input type="hidden" name="id" id="id" />
<div class="arbo2"><strong>Petites annonces - Liste des inscrits</strong></div>

<table class="arbo" align="center">
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td align="center"><input type="text" class="arbo" name="sRecherche" id="sRecherche" value="<?php echo $_POST['sRecherche']; ?>" size="60" />
		&nbsp;
          <input type="button" name="btRechercher" onclick="javascript:rechercher()" value="chercher" class="arbo" /></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td><?php print($pager->bandeau); ?></td>
	</tr>
</table>

<br />

<?php
if(sizeof($listeInscrits)>0) {
?>
<table align="center" border="1" cellpadding="1" cellspacing="0" class="arbo">
 <tr>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Date d'inscription</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Validé</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Email</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Nom</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Prénom</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Changer<br />
    mot de passe </b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Afficher</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Supprimer</b></u>&nbsp;</td>
 </tr>
<?php
foreach( $listeInscrits as $k => $inscrit) {

?>
 <tr class="<?php echo ($inscrit->valide)? "lignevalidee" : "lignenonvalidee"; ?>">
  <td nowrap="nowrap" align="center" class="arbo">&nbsp;<?php echo $inscrit->getDt() ?>&nbsp;</td>
  <td nowrap="nowrap" align="center" class="arbo">&nbsp;<?php echo ($inscrit->valide) ? "Oui&nbsp;<a href=\"validerInscription.php?id=".$inscrit->id."&action=invalider\" onMouseOver='popup(\"Définir le compte comme non validé\");' onMouseOut=\"kill();\">invalider</a>" : "Non&nbsp;<a href=\"validerInscription.php?id=".$inscrit->id."&action=valider\" onMouseOver='popup(\"Définir le compte comme validé\");' onMouseOut=\"kill()\";>valider</a>"; ?>&nbsp;</td>
  <td nowrap="nowrap" align="center" class="arbo">&nbsp;<?php echo $inscrit->mail; ?>&nbsp;</td>
  <td nowrap="nowrap" align="center" class="arbo">&nbsp;<?php echo $inscrit->nom; ?>&nbsp;</td>
  <td nowrap="nowrap" align="center" class="arbo">&nbsp;<?php echo $inscrit->prenom; ?>&nbsp;</td>
  <td nowrap="nowrap" align="center" class="arbo"><a href="javascript:change_pwd_inscrit(<?php echo $inscrit->id; ?>)" class="arbo">changer</a></td>
  <td nowrap="nowrap" align="center" class="arbo"><a href="javascript:afficher_inscrit(<?php echo $inscrit->id; ?>)" class="arbo">voir</a></td>
  <td nowrap="nowrap" align="center" class="arbo">&nbsp;<a href="#" onclick="if(window.confirm('Etes vous sur(e) de vouloir supprimer cet inscrit ?')) {document.location='validerInscription.php?action=supprimer&amp;id=<?php echo $inscrit->id; ?>'}" title="Supprimer cette personne"><img src="/backoffice/cms/img/supprimer.gif" border="0" /></a>&nbsp;</td>
 </tr>
<?php
}
?>
</table>
<?php
} else {
?>
<div align="center" class="arbo">Aucun inscrit à afficher.</div>
<?php
}
?>
</form>