<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 
$Author: raphael $
$Revision: 1.1 $

$Log: arboPageDroit_browse.php,v $
Revision 1.1  2013-09-30 09:23:51  raphael
*** empty log message ***

Revision 1.13  2013-03-01 10:28:04  pierre
*** empty log message ***

Revision 1.12  2012-07-31 14:24:46  pierre
*** empty log message ***

Revision 1.11  2012-04-13 07:18:37  pierre
*** empty log message ***

Revision 1.10  2010-03-08 12:10:27  pierre
syntaxe xhtml

Revision 1.9  2009-09-24 08:53:11  pierre
*** empty log message ***

Revision 1.8  2008-11-28 14:09:55  pierre
*** empty log message ***

Revision 1.7  2008-11-27 11:35:37  pierre
*** empty log message ***

Revision 1.6  2008-11-06 12:03:52  pierre
*** empty log message ***

Revision 1.5  2008-08-28 08:38:12  thao
*** empty log message ***

Revision 1.4  2007/11/15 18:08:49  pierre
*** empty log message ***

Revision 1.5  2007/11/06 15:33:25  remy
*** empty log message ***

Revision 1.3  2007/08/28 15:55:28  pierre
*** empty log message ***

Revision 1.2  2007/08/27 10:13:47  pierre
*** empty log message ***

Revision 1.1  2007/08/08 14:26:20  thao
*** empty log message ***

Revision 1.3  2007/08/08 14:16:14  thao
*** empty log message ***

Revision 1.2  2007/08/08 13:56:04  thao
*** empty log message ***

Revision 1.1.1.1  2006/01/25 15:14:32  pierre
projet CCitron AWS 2006 Nouveau Website

Revision 1.3  2005/12/21 08:36:44  sylvie
*** empty log message ***

Revision 1.2  2005/12/20 09:12:09  sylvie
application des styles

Revision 1.1.1.1  2005/10/24 13:37:05  pierre
re import fusion espace v2 et ADW v2

Revision 1.2  2005/10/21 10:46:46  sylvie
*** empty log message ***

Revision 1.1.1.1  2005/10/20 13:10:53  pierre
Espace V2

Revision 1.1.1.1  2005/04/18 13:53:29  pierre
again

Revision 1.1.1.1  2005/04/18 09:04:21  pierre
oremip new

Revision 1.1.1.1  2004/11/03 13:49:54  ddinside
lancement du projet - import de adequat

Revision 1.3  2004/06/16 15:23:19  ddinside
inclusion corrections

Revision 1.2  2004/04/26 08:07:09  melanie
*** empty log message ***

Revision 1.1.1.1  2004/04/01 09:20:27  ddinside
Création du projet CMS Couleur Citron nom de code : tipunch

Revision 1.4  2004/02/12 15:56:16  ddinside
mise à jour plein de choses en fait, mais je sais plus quoi parce que ça fait longtemps que je l'avais pas fait.
Mea Culpa...

Revision 1.3  2004/02/05 15:56:25  ddinside
ajout fonctionnalite de suppression de pages
ajout des styles dans spaw
debuggauge prob du nom de fichier limite à 30 caracteres

Revision 1.2  2004/01/27 12:12:50  ddinside
application de dos2unix sur les scripts SQL
modification des scripts d'ajout pour correction bug lors d'une modif de page
ajout foncitonnalité de modification de page
ajout visu d'une page si créée

Revision 1.1  2004/01/20 15:16:38  ddinside
mise à jour de plein de choses
ajout de gabarit vie des quartiers
eclatement gabarits par des includes pour contourner prob des flashs non finalisés

Revision 1.3  2004/01/07 18:27:37  ddinside
première mise à niveau pour plein de choses

Revision 1.2  2003/11/26 13:08:42  ddinside
nettoyage des fichiers temporaires commités par erreur
ajout config spaw
corrections bug menu et positionnement des divs

Revision 1.1  2003/10/16 21:19:46  ddinside
suite dev gestio ndes composants
ajout librairies d'images
suppressions fichiers vi
ajout gabarit

Revision 1.2  2003/10/07 08:15:53  ddinside
ajout gestion de l'arborescence des composants

Revision 1.1  2003/09/25 14:55:16  ddinside
gestion de l'arborescene des composants : ajout
*/

// sopnthus 17/06/05
// ajout id_site

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/gabarit.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
 include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


activateMenu('gestionpage');  //permet de dérouler le menu contextuellement

if ($idSite == "") $idSite = $_SESSION['idSite_travail'];


// crirères de recherche

// liste des utilisateurs du site + les admin
$aUser = listUsersWidthAdmin($_SESSION['idSite_travail']);

// liste des gabarits
$aGabarit = getListGabarits($_SESSION['idSite_travail']);


//===================================================================
// critères de recherche filtrant la liste des pages

//---------------------------------------------------
// critère caché : site
$oRech = new dbRecherche();

$oRech->setNomBD("cms_page.id_site");
$oRech->setTypeBD("entier");
$oRech->setValeurRecherche($idSite);
$oRech->setTableBD("cms_page");

$aRecherche[] = $oRech;
//---------------------------------------------------

//---------------------------------------------------
// critère caché : toutes les pages de type page (non gabarit)
$oRech = new dbRecherche();

$oRech->setNomBD("isgabarit_page");
$oRech->setTypeBD("entier");
$oRech->setValeurRecherche("0");
$oRech->setTableBD("cms_page");
$oRech->setJointureBD("");

$aRecherche[] = $oRech;
//---------------------------------------------------

//---------------------------------------------------
// critère caché : toutes les pages valides

$oRech = new dbRecherche();

$oRech->setNomBD("valid_page");
$oRech->setTypeBD("entier");
$oRech->setValeurRecherche("1");
$oRech->setTableBD("cms_page");
$oRech->setJointureBD("");

$aRecherche[] = $oRech;
//---------------------------------------------------

//---------------------------------------------------
// critère caché : toutes les pages ayant des briques editables

$oRech = new dbRecherche();

$oRech->setValeurRecherche("declencher_recherche");
$oRech->setTableBD("cms_page;cms_struct_page;cms_content");
$oRech->setJointureBD("cms_page.id_page=cms_struct_page.id_page;cms_struct_page.id_content=cms_content.id_content;cms_content.isbriquedit_content=1");
$oRech->setPureJointure(1);

$aRecherche[] = $oRech;
//---------------------------------------------------

//---------------------------------------------------
// droit affecté ou non affecté

// droit non affecté
if ($_POST['selectDroit'] == "NON_AFFECTE") {

$oRech = new dbRecherche();

$oRech->setValeurRecherche("declencher_recherche");
$oRech->setTableBD("cms_page;cms_struct_page");
$oRech->setJointureBD("cms_struct_page.id_page=cms_page.id_page AND
						cms_struct_page.id_content NOT IN ( SELECT distinct(cms_struct_page.id_content)
                                   FROM cms_page, cms_struct_page, cms_content, cms_droit
                                   WHERE cms_page.id_page=cms_struct_page.id_page AND
                                           cms_struct_page.id_content=cms_content.id_content AND
                                           cms_content.id_content=cms_droit.id_content)");
$oRech->setPureJointure(1);

$aRecherche[] = $oRech;

} else if ($_POST['selectDroit'] == "AFFECTE") {

// droit affecté
$oRech = new dbRecherche();

$oRech->setValeurRecherche("declencher_recherche");
$oRech->setTableBD("cms_page;cms_struct_page;cms_content;cms_droit");
$oRech->setJointureBD("cms_page.id_page=cms_struct_page.id_page AND cms_struct_page.id_content=cms_content.id_content AND cms_content.id_content=cms_droit.id_content");
$oRech->setPureJointure(1);

$aRecherche[] = $oRech;

}
//---------------------------------------------------

//---------------------------------------------------
// utilisateur

$oRech = new dbRecherche();

$oRech->setNomBD("bo_users.user_id");
$oRech->setTypeBD("entier");
$oRech->setValeurRecherche($_POST['selectUser']);
$oRech->setTableBD("bo_users;cms_droit;cms_content;cms_page;cms_struct_page");
$oRech->setJointureBD("bo_users.user_id=cms_droit.user_id;cms_droit.id_content=cms_content.id_content;cms_struct_page.id_content=cms_content.id_content;cms_struct_page.id_page=cms_page.id_page");
$oRech->setPureJointure(0);

$aRecherche[] = $oRech;
//---------------------------------------------------

//---------------------------------------------------
// gabarit

$oRech = new dbRecherche();

$oRech->setNomBD("cms_page.gabarit_page");
$oRech->setTypeBD("text");
$oRech->setValeurRecherche($_POST['selectGabarit']);
$oRech->setTableBD("cms_page");
$oRech->setJointureBD("");
$oRech->setPureJointure(0);

$aRecherche[] = $oRech;
//---------------------------------------------------

//---------------------------------------------------
// chemin

$oRech = new dbRecherche();

$oRech->setNomBD("cms_page.nodeid_page");
$oRech->setTypeBD("entier");
$oRech->setValeurRecherche($_POST['node_id']);
$oRech->setTableBD("cms_page");
$oRech->setJointureBD("");
$oRech->setPureJointure(0);

$aRecherche[] = $oRech;
//---------------------------------------------------

//===================================================================

// le tri
if ($_POST['champTri'] != "") 
	$sOrderBy = $_POST['champTri']." ".$_POST['sensTri'];
else 
	$sOrderBy = " cms_page.name_page ASC ";
	
//----------------------------------
// liste des pages
//----------------------------------
// liste renvoyant un maximum de DEF_NBDROIT_PAGE enregistrements
$aPage = getListPages($aRecherche, $sOrderBy, DEF_NBDROIT_PAGE);
// taille totale de la liste résultat
$ePage = getCountListPages($aRecherche, $sOrderBy);


	//////////////////////////////////////////////////
	// construction de l'objet d'affichage
	//////////////////////////////////////////////////
		
	for ($p=0; $p<sizeof($aPage); $p++)
	{
		$aPageAffich[] = new Cms_affich_droitpage($aPage[$p]->getId_page());
	}








?>
<script type="text/javascript">document.title="Arbo - Pages";</script>
<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<script type="text/javascript">
<!--
	// sélection d'un noeud pour la recherche
	function chooseFolder() {
	  
		window.open("chooseFolder.php?idSite=<?php echo $_SESSION['idSite_travail']; ?>","browser","scrollbars=yes,width=500,height=500,resizable=yes,menubar=no");
	}

	// efface le chemin choisi
	function effacerFolder()
	{
		document.arboDroits.foldername.value="";
	}

	// affinage de la liste des pages
	// bandeau de recherche
	function recherche()
	{
		document.arboDroits.action = "arboPageDroit_browse.php";
		document.arboDroits.submit();
	}

	// modification des droits de la page
	function modifDroits(idPage)
	{
		document.arboDroits.idPage.value = idPage;
		document.arboDroits.action = "droitContentPage.php";
		document.arboDroits.submit();
	}

	// tri des contenus
	function Tri(sChamp)
	{
		sChampTri = "";
		sSensTri = "";

		if (sChamp == "page") sChampTri = "cms_page.name_page";
		
		if (document.arboDroits.sensTri.value == "") sSensTri = "ASC";
		else if (document.arboDroits.sensTri.value == "DESC") sSensTri = "ASC";
		else if (document.arboDroits.sensTri.value == "ASC") sSensTri = "DESC";
				

		document.arboDroits.champTri.value = sChampTri;
		document.arboDroits.sensTri.value = sSensTri;

		document.arboDroits.action = "arboPageDroit_browse.php";
		document.arboDroits.submit();
	}

-->
</script>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>
<div class="ariane">
<span class="arbo2">PAGE&nbsp;>&nbsp;</span><span class="arbo3">Gestion des droits&nbsp;>&nbsp;Liste des droits</span>
</div>

<form name="arboDroits" method="post">

<input type="hidden" name="idPage" value="">
<input type="hidden" name="node_id" value="">

<input type="hidden" name="champTri" value="<?php echo $_POST['champTri']; ?>">
<input type="hidden" name="sensTri" value="<?php echo $_POST['sensTri']; ?>">

<?php
// site de travail
if (DEF_MENUS_MINISITES == "ON") print(putAfficheSite());
?>

<?php
//----------------------------------
// critères de recherche
//----------------------------------
?>
<table  border="0" cellpadding="2" cellspacing="0" bordercolor="#FFFFFF" class="arbo" width="100%">
	<tr>
		<tD><select name="selectDroit" class="arbo" style="width:300px">
			<option value="-1">tous les droits</option>
<?php
// valeur sélectionnée
if ($_POST['selectDroit'] == "NON_AFFECTE") $NON_AFFECTE_selected = "selected";
else $NON_AFFECTE_selected="";

if ($_POST['selectDroit'] == "AFFECTE") $AFFECTE_selected = "selected";
else $AFFECTE_selected="";
?>
			<option value="NON_AFFECTE" <?php echo $NON_AFFECTE_selected; ?> >non affecté</option>
			<option value="AFFECTE" <?php echo $AFFECTE_selected; ?> >affecté</option>
		</select></tD>
	</tr>
	<tr>
		<td><select name="selectUser" class="arbo" style="width:300px">
			<option value="-1">tous les utilisateurs</option><?php
// les utilisateurs + les amdin
for ($k=0; $k<sizeof($aUser); $k++)
{
	$oUser = $aUser[$k];

	// valeur sélectionnée
	if ($_POST['selectUser'] == $oUser->getUser_id()) $user_selected = "selected";
	else $user_selected = "";

?><option value="<?php echo $oUser->getUser_id(); ?>" <?php echo $user_selected; ?> >
(<?php echo getLibelleDefineWithCode($oUser->getUser_rank()); ?>)
&nbsp;<?php echo $oUser->getUser_prenom(); ?>&nbsp;<?php echo $oUser->getUser_nom(); ?>&nbsp;
</option><?php
}
?></select></td>
	</tr>
	<tr>
		<tD><select name="selectGabarit" class="arbo" style="width:300px">
			<option value="">tous les gabarits</option><?php
// gabarits
for ($k=0; $k<sizeof($aGabarit); $k++)
{
	$oGab = new Cms_page($aGabarit[$k]['id']);

	// valeur sélectionnée
	if ($_POST['selectGabarit'] == $oGab->getId_page()) $gab_selected = "selected";
	else $gab_selected = "";

?><option value="<?php echo $oGab->getId_page(); ?>" <?php echo $gab_selected; ?> ><?php echo $oGab->getName_page(); ?></option><?php
}
?>
		</select></tD>
	</tr>
	<tr>
		<td>
<?php
// valeur sélectionnée
$oNoeudSelected = new Cms_arbo_pages($_POST['node_id']);
$sChemin = $oNoeudSelected->getNode_libelle();
if ($sChemin == "") $sChemin = "Chemin";
?>
		<input name="foldername" type="text" disabled class="arbo" id="foldername" value="<?php echo $sChemin;?>" size="30">&nbsp;
		<a href="#" class="arbo" onClick="chooseFolder();"><img src="/backoffice/cms/img/2013/icone/go.png" border="0">&nbsp;choisir le dossier</a>&nbsp;
		<a href="#" class="arbo" onClick="effacerFolder();"><img src="/backoffice/cms/img/2013/icone/go.png" border="0">&nbsp;effacer</a></td>
	</tr>
	<tr>
		<tD><input type="button" name="btRecherche" class="arbo" onClick="javascript:recherche()" value="Lancer la recherche"></tD>
	</tr>
</table>
<br />

<?php
// nombre d'enregistrements de la liste
if (sizeof($aPage) == 1) $sMessage = "1 page correspond à votre recherche";
else $sMessage = sizeof($aPage)." pages correspondent à votre recherche";
?>
<div class="arbo"><?php echo $sMessage; ?></div>
<br />

<?php
// trop d'enregistrements : limitation des résultats à DEF_NBDROIT_PAGE
if ($ePage > sizeof($aPage)) {
?><div align="left" class="arbo"><font color="#FF0000">Il y a <?php echo $ePage; ?> réponses pour votre recherche, 
ce qui est supérieur au seuil autorisé de <?php echo DEF_NBDROIT_PAGE; ?> réponses. <br />
Vous pouvez préciser la recherche.</font>
</div><br /> <?php
}
?>


<table  border="0" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo" width="100%">
  <tr class="col_titre">
		<td><strong><a href="javascript:Tri('page')">Page</a></strong></td>
		<td><strong>Zones</strong></td>
		<td><strong>Contenus</strong></td>
		<td><strong>Utilisateurs</strong></td>
	</tr>
<?php
// toutes les pages
//----------------------------------
for ($p=0; $p<sizeof($aPageAffich); $p++) {
	
	// Objet d'affichage
	$oPageAffich = $aPageAffich[$p];

?>
 <tr class="<?php echo htmlImpairePaire($p);?>">
		<td><strong><?php echo $oPageAffich->getPage(); ?></strong><br /><?php
// chaine absolute path
// on enlève le nom du site dans l'arbo
// on espace les / de la chaine
// ainsi une chaine trop longue n'explosera pas la colonne
$sChemin = cheminAere($oPageAffich->getNoeud());
?><?php echo $sChemin;?></td>
		<td><?php
// pour chaque contenu
// écriture du nom de la zone
$aContenu = $oPageAffich->getAContenu();
for ($m=0; $m<sizeof($aContenu); $m++) {
	$oContenu = $aContenu[$m];
	print("".$oContenu->getZone());
	if ($m != sizeof($aContenu)-1) print("<br />");
}
?>&nbsp;</td>
		<td><?php
// pour chaque contenu
// écriture du nom de la brique
$aContenu = $oPageAffich->getAContenu();
for ($m=0; $m<sizeof($aContenu); $m++) {
	$oContenu = $aContenu[$m];
	print("".$oContenu->getContent());
	if ($m != sizeof($aContenu)-1) print("<br />");
}
?>&nbsp;</td>
		<td><?php
// pour chaque contenu
// écriture du nom de l'utilisateur affecté
$aContenu = $oPageAffich->getAContenu();
for ($m=0; $m<sizeof($aContenu); $m++) {
	$oContenu = $aContenu[$m];
	print("<a href='javascript:modifDroits(".$oPageAffich->getId_page().")'>".$oContenu->getNom()."</a>");
	if ($m != sizeof($aContenu)-1) print("<br />");
}
?>&nbsp;</td>
<?php
}
?>	
</table>


</form>





