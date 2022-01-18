<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: listeVieuxInscrits.php,v 1.1 2013-09-30 09:42:12 raphael Exp $
	$Author: raphael $

	$Log: listeVieuxInscrits.php,v $
	Revision 1.1  2013-09-30 09:42:12  raphael
	*** empty log message ***

	Revision 1.8  2013-03-01 10:28:18  pierre
	*** empty log message ***

	Revision 1.7  2012-07-31 14:24:50  pierre
	*** empty log message ***

	Revision 1.6  2012-04-13 07:18:38  pierre
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

	Revision 1.4  2008-09-10 12:25:41  pierre
	*** empty log message ***

	Revision 1.3  2006/12/05 11:10:08  arnaud
	*** empty log message ***
	
	Revision 1.2  2006/12/04 15:39:58  arnaud
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
// critère caché : inscrits non connectés depuis xx jours
$oRech = new dbRecherche();

$oRech->setValeurRecherche("declencher_recherche");
$oRech->setTableBD("pa_inscrits");

// la date limite de délai de connection autorisée est fonction de DEF_EXPIRE_INSCRIPTION
// DEF_EXPIRE_INSCRIPTION est une valeur en nombre de jours saisie dans le fichier de config
$date_expiration = getDateLimiteInscription_Ymd();
$sAffich_date_expiration = getDateLimiteInscription_dmY();
if (DEF_BDD != "ORACLE") {
$sDate = "(inscrit_conndt < str_to_date('".$date_expiration."', 'yyyy/mm/dd'))";
}else{
$sDate = "(inscrit_conndt < to_date('".$date_expiration."', 'yyyy/mm/dd'))";
}

$oRech->setJointureBD($sDate);
$oRech->setPureJointure(1);

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

//print("<br>$sql");

// execution de la requette avec pagination
$pager = new Pagination($db, $sql, $sParam);
$pager->Render($rows_per_page=20);

//////// DEBUGAGE ////////
$bDebug = false;
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
for ($m=0; $m<newSizeOf($aId); $m++)
{
	$listeInscrits[] = new Inscript($aId[$m]);
}


// liste des inscrits sélectionnés
$idSelected = array();
foreach( $listeInscrits as $k => $inscrit) {

$sCC = "cb_".$inscrit->id;

if ($_POST[$sCC] == 1) $idSelected[] = $inscrit->id;
}

for ($t=0; $t<newSizeOf($idSelected); $t++) {

//print("<br>".$idSelected[$t]);

	$oInscrit = new Inscript($idSelected[$t]);

	// action à entreprendre sur une liste d'inscrits sélectionnés
	if ($_POST['actiontodo'] == "DELETE") delete_inscrit($oInscrit->getId());
	else if ($_POST['actiontodo'] == "SEND_MSG") send_rappel($oInscrit);
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

// recherche d'inscrits
function rechercher()
{
	document.listInscritForm.action = "listeVieuxInscrits.php";
	document.listInscritForm.submit();
}

// action sur une liste d'inscrits
function doAction(sAction)
{
	document.listInscritForm.actiontodo.value = sAction;
	document.listInscritForm.action = "listeVieuxInscrits.php";
	document.listInscritForm.submit();
}

</script>
<form method="post" name="listInscritForm" id="listInscritForm">
<input type="hidden" name="id" id="id" />
<input type="hidden" name="actiontodo" id="actiontodo" />
<div class="arbo2"><strong>Petites annonces - Liste des inscrits inutilisés</strong></div>

<table class="arbo" align="center">
	<tr><td>&nbsp;</td></tr>
	<tr><td align="center"><div class="arbo">Inscrits non connectés depuis le <?php echo $sAffich_date_expiration; ?></div></td></tr>
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
if(newSizeOf($listeInscrits)>0) {
?>
<table align="center" border="1" cellpadding="1" cellspacing="0" class="arbo">
<tr>
	<td colspan="8">
      <input type="button" name="btEnvoyer" id="btEnvoyer" value="Envoyer un rappel" class="arbo" onclick="javascript:doAction('SEND_MSG')" />
&nbsp;
<input type="button" name="btSupprimer" id="btSupprimer" value="Supprimer" class="arbo" onclick="javascript:doAction('DELETE')" />
&nbsp;
</td>
</tr>
<tr>
	<td colspan="8">&nbsp;</td>
</tr>
 <tr>
   <td nowrap="nowrap" align="center">&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Date d'inscription</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Derni&egrave;re connexion</b></u>&nbsp; </td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Rappel</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Email</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Nom</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Prénom</b></u>&nbsp;</td>
  <td nowrap="nowrap" align="center">&nbsp;<u><b>Afficher</b></u>&nbsp;</td>
  </tr>
<?php
foreach( $listeInscrits as $k => $inscrit) {

?>
 <tr class="<?php echo ($inscrit->valide)? "lignevalidee" : "lignenonvalidee"; ?>">
   <td nowrap="nowrap" align="center" class="arbo">&nbsp;
     <input type="checkbox" class="arbo" value="1" name="cb_<?php echo $inscrit->getId() ?>" id="cb_<?php echo $inscrit->getId() ?>" />
     &nbsp;</td>
  <td nowrap="nowrap" align="center" class="arbo">&nbsp;<?php echo $inscrit->getDt() ?>&nbsp;</td>
  <td nowrap="nowrap" align="center" class="arbo">&nbsp;<?php echo $inscrit->getConndt() ?>&nbsp;</td>
  <td nowrap="nowrap" align="center" class="arbo">&nbsp;<?php echo $inscrit->getRappeldt() ?>&nbsp;</td>
  <td nowrap="nowrap" align="center" class="arbo">&nbsp;<?php echo $inscrit->mail; ?>&nbsp;</td>
  <td nowrap="nowrap" align="center" class="arbo">&nbsp;<?php echo $inscrit->nom; ?>&nbsp;</td>
  <td nowrap="nowrap" align="center" class="arbo">&nbsp;<?php echo $inscrit->prenom; ?>&nbsp;</td>
  <td nowrap="nowrap" align="center" class="arbo"><a href="javascript:afficher_inscrit(<?php echo $inscrit->id; ?>)" class="arbo">voir</a></td>
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