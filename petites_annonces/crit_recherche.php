<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

///////////////////////////////////////////////
///////////////////////////////////////////////
// liste des petites annonces
///////////////////////////////////////////////
///////////////////////////////////////////////

//////////////////////////
// RECHERCHE
//////////////////////////

$aRecherche = array();



//////////////////////////
// pure jointure 
//---------------------------------------------------
// lien vers catégories
$oRech = new dbRecherche();

$oRech->setValeurRecherche("declencher_recherche");
$oRech->setTableBD("pa_annonces;pa_categories");
$oRech->setJointureBD("pa_annonces.annonce_categorie_id=pa_categories.categorie_id");
$oRech->setPureJointure(1);

$aRecherche[] = $oRech;
//---------------------------------------------------
//---------------------------------------------------
// lien vers inscrits
$oRech = new dbRecherche();

$oRech->setValeurRecherche("declencher_recherche");
$oRech->setTableBD("pa_annonces;pa_inscrits");
$oRech->setJointureBD("pa_annonces.annonce_depot_inscrit_id=pa_inscrits.inscrit_id");
$oRech->setPureJointure(1);

$aRecherche[] = $oRech;
//---------------------------------------------------
//////////////////////////




//////////////////////////
// annonces validées ou non validées
// demandé via l'url depuis le menu
$valid = $_GET['valid'];
if ($valid == "") $valid = $_POST['valid'];

if ($valid != "") {
//---------------------------------------------------
// annonces validées / non validées
$oRech = new dbRecherche();

$oRech->setNomBD("annonce_valid");
$oRech->setTypeBD("entier");
$oRech->setValeurRecherche($valid);
$oRech->setTableBD("pa_annonces");

$aRecherche[] = $oRech;
//---------------------------------------------------
}
//////////////////////////

//---------------------------------------------------
// site
$oRech = new dbRecherche();

$oRech->setNomBD("pa_annonces.id_site");
$oRech->setTypeBD("entier");
$oRech->setValeurRecherche($_SESSION['idSite_travail']);
$oRech->setTableBD("pa_annonces");

$aRecherche[] = $oRech;
//---------------------------------------------------


//////////////////////////
// select recherche par type
$fTypeSelect = $_GET['fTypeSelect'];
if ($fTypeSelect == "") $fTypeSelect = $_POST['fTypeSelect'];

if ($fTypeSelect != "" && $fTypeSelect != "-1")
{
//---------------------------------------------------
// critère type
$oRech = new dbRecherche();

$oRech->setNomBD("annonce_is_garde");
$oRech->setTypeBD("entier");
$oRech->setValeurRecherche($fTypeSelect);
$oRech->setTableBD("pa_annonces");

$aRecherche[] = $oRech;
//---------------------------------------------------
}
//////////////////////////

//////////////////////////
// select recherche par catégorie
$fCategSelect = $_GET['fCategSelect'];
if ($fCategSelect == "") $fCategSelect = $_POST['fCategSelect'];

if ($fCategSelect != "" && $fCategSelect != "-1")
{
//---------------------------------------------------
// critère catégorie
$oRech = new dbRecherche();

$oRech->setNomBD("categorie_id");
$oRech->setTypeBD("entier");
$oRech->setValeurRecherche($fCategSelect);
$oRech->setTableBD("pa_categories");

$aRecherche[] = $oRech;
//---------------------------------------------------
}
//////////////////////////





//////////////////////////
// TRIS
//////////////////////////

$champTri = $_POST['champTri'];
if ($champTri == "") $champTri = $_GET['champTri'];

$sensTri = $_POST['sensTri'];
if ($sensTri == "") $sensTri = $_GET['sensTri'];


// par défaut les autres tris sont à faire
// on enlèvera de cette liste le tri demandé par l'utilisateur
$bTriToDo_categ = 1;
$bTriToDo_type = 1;
$bTriToDo_valid = 1;
$bTriToDo_deposant = 1;
$bTriToDo_dsoumission = 1;
$bTriToDo_dperim = 1;


// tri demandé
if ($champTri == "categorie") {

	$aGetterOrderBy[] = "categorie_libelle";
	$aGetterSensOrderBy[] = $sensTri;

	$bTriToDo_categ = 0;

} else if ($champTri == "type") {

	$aGetterOrderBy[] = "annonce_is_garde";
	$aGetterSensOrderBy[] = $sensTri;

	$bTriToDo_type = 0;

} else if ($champTri == "valide") {

	$aGetterOrderBy[] = "annonce_valid";
	$aGetterSensOrderBy[] = $sensTri;

	$bTriToDo_valid = 0;

} else if ($champTri == "deposant") {

	$aGetterOrderBy[] = "inscrit_nom";
	$aGetterSensOrderBy[] = $sensTri;

	$bTriToDo_deposant = 0;

} else if ($champTri == "dsoumission") {

	$aGetterOrderBy[] = "annonce_dt_debut";
	$aGetterSensOrderBy[] = $sensTri;

	$bTriToDo_dsoumission = 0;

} else if ($champTri == "dperim") {

	$aGetterOrderBy[] = "annonce_dt_perim";
	$aGetterSensOrderBy[] = $sensTri;

	$bTriToDo_dperim = 0;
}

// tableau de tri
// les tris sont ici ordonnés 
// si un tri est demandé il passe en premier et il n'est pas refait dans cette liste

if ($bTriToDo_valid) {
$aGetterOrderBy[] = "annonce_valid";
$aGetterSensOrderBy[] = "ASC";
}

if ($bTriToDo_type) {
$aGetterOrderBy[] = "annonce_is_garde";
$aGetterSensOrderBy[] = "ASC";
}

if ($bTriToDo_dsoumission) {
$aGetterOrderBy[] = "annonce_dt_debut";
$aGetterSensOrderBy[] = "DESC";
}

if ($bTriToDo_dperim) {
$aGetterOrderBy[] = "annonce_dt_perim";
$aGetterSensOrderBy[] = "ASC";
}


if ($bTriToDo_deposant) {
$aGetterOrderBy[] = "inscrit_nom";
$aGetterSensOrderBy[] = "ASC";
}


// obtention de la requete
$sql = "SELECT pa_annonces.* ";
$sql.= dbMakeRequeteWithCriteres2("Annonce", $aRecherche, $aGetterOrderBy, $aGetterSensOrderBy);

//print("<bR>$sql");

// paramètres à passer par l'url quand on change de page
$aParam = array();

$aParam[] = "valid";
$aParamValue[] = $valid;

$aParam[] = "fTypeSelect";
$aParamValue[] = $fTypeSelect;

$aParam[] = "fCategSelect";
$aParamValue[] = $fCategSelect;

$aParam[] = "champTri";
$aParamValue[] = $champTri;

$aParam[] = "sensTri";
$aParamValue[] = $sensTri;





$sParam = "";
for ($m=0; $m<newSizeOf($aParam); $m++) {
$sParam.= "&".$aParam[$m]."=".$aParamValue[$m];
}

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

$listeAnnonces = array();
for ($m=0; $m<newSizeOf($aId); $m++)
{
	$listeAnnonces[] = new Annonce($aId[$m]);
}






///////////////////////////////////////////////
///////////////////////////////////////////////
// catégories de petites annonces
///////////////////////////////////////////////
///////////////////////////////////////////////

$aGetterWhere_categpa = array();
$aValeurChamp_categpa = array();
$aGetterOrderBy_categpa = array();
$aGetterSensOrderBy_categpa = array();

$aGetterOrderBy_categpa[] = "getOrdre";
$aGetterSensOrderBy_categpa[] = "ASC";

$aListeCategpa = dbGetObjectsFromFieldValue2("Categorie", $aGetterWhere_categpa, $aValeurChamp_categpa, $aGetterOrderBy_categpa, $aGetterSensOrderBy_categpa);



?>