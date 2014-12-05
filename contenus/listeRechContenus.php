<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

// utilisateur connecté
$oUserLogged = unserialize($_SESSION['BO']['LOGGED']);
$idUser_logged = $oUserLogged->get_id();

$sRankId = $oUserLogged->get_rank();
$oRank = new Bo_rank($sRankId);
$sRank = $oRank->get_libelle();


$sValidauto = $oUserLogged->get_validauto();

// maj statut du contenu
if ($_POST['operation'] != "")
{
	$oContent = new Cms_content($_POST['id']);
	$oContent->setStatut_content($_POST['operation']);
	$oContent->updateStatut();
	
	if($_POST['operation'] == DEF_ID_STATUT_LIGNE) {
			
		// On sauvegarde la brique (HTML) dans archi
		$oArchi_content = new Cms_archi_content();
		$oArchi_content->cms_getFromContent($oContent);

		// On pousse les éléments
		// on passe tous les status des différentes versions de la même brique à "archivé"
		$aArchi_content = getArchiList($oArchi_content->getId_content_archi());
		foreach($aArchi_content as $k=>$oArchi) {
			$oArchi->setStatut_archi(DEF_ID_STATUT_ARCHI);
			$oArchi->updateStatutArchi();
		}
		
		// puis on ajoute la nouvelle version "en ligne"
		$oArchi_content->cms_archi_content_save();
		
		//--------------------------------------------
		// REGENERATION DE PAGE //
		// On a poussé la brique en ligne
		// Il faut donc régénérer la page
		// (c'est dans la fonction de régénération que l'on gère le fait que
		// si la page n'a pas toutes ses briques "en ligne" on fait rien)
	
		// recherche de la page de cette brique editable
		$aStruct = getObjetWithContentBEdit($oContent->getId_content());
		$oStruct = $aStruct[0];
		$oPage = new Cms_page($oStruct->getId_page());

		// régénération dans la base
		$oPage->cms_page_regenerate();

		// maj de l'attribut "tout en ligne"
		$bToutenligne_page = updateToutenligne($oPage->getId_page());
		$oPage->setToutenligne_page($bToutenligne_page);

		// maj de l'attribut "existe version ligne"
		$bExisteligne_page = updateExisteligne($oPage->getId_page());
		$oPage->setExisteligne_page($bExisteligne_page);


		// régénération du fichier
		if ($bExisteligne_page) {
			if( ! $oPage->cms_page_write() ){ 
				echo "<br />Erreur interne de programme";	
				error_log('ERREUR :: generation fichier :: listeContenus.php > oPage->cms_page_write()');
			}
		} else {
			// pas de regénération du fichier
		}
		//--------------------------------------------

	} 
	$oStatut = new Cms_statut_content();
	$aStatut = getStatutByRankOperation($sRankId,$_POST['operation']);
	$ope = $_POST['operation'];
	$_POST['operation'] = "";
	
	
	
	
}

// liste des utilisateurs du site + les admin
// ajout 22/06
// gestion des alertes pour les admins
$aUser = listUsersWidthAdmin($_SESSION['idSite_travail']);
if (sizeof($aStatut) > 0) {
	// je récupère la première valeur
	$idStatutContent = $aStatut[0]->get_id();
	
	foreach ($aUser as $oUser) {
	 
		if (getCount_where("cms_assobo_userscms_statut_content", array("xus_bo_users", "xus_cms_statut_content"), array($oUser->getUser_id(),$idStatutContent), array("NUMBER","NUMBER")) > 0){
			// on vérifie email et on envoit
			if (isEmail( $oUser->getUser_mail())) {
				$aXus = dbGetObjectsFromFieldValue("cms_assobo_userscms_statut_content", array("get_bo_users", "get_cms_statut_content"), array($oUser->getUser_id(),$idStatutContent), "") ;
				$bRetour = sendAlerteToAdmin ($aXus[0], $idUser_logged, $_POST['id'], $_SESSION['idSite_travail']);
			}
		}
		
	}

}
  


 
// liste des gabarits
$aGabarit = getListGabarits($_SESSION['idSite_travail']);

//===================================================================
// critères de recherche filtrant la liste des contenus


//---------------------------------------------------
// critère caché : site
$oRech = new dbRecherche();

$oRech->setNomBD("cms_content.id_site");
$oRech->setTypeBD("entier");
$oRech->setValeurRecherche($idSite);
$oRech->setTableBD("cms_content");

$aRecherche = array();
$aRecherche[] = $oRech;
$aRecherche_archi[] = $oRech;
//---------------------------------------------------

//---------------------------------------------------
// critère caché : tous les content non zone editable
$oRech = new dbRecherche();

$oRech->setNomBD("iszonedit_content");
$oRech->setTypeBD("entier");
$oRech->setValeurRecherche("0");
$oRech->setTableBD("cms_content");

$aRecherche[] = $oRech;
//---------------------------------------------------

//---------------------------------------------------
// critère caché : tous les content brique editable
$oRech = new dbRecherche();

$oRech->setNomBD("isbriquedit_content");
$oRech->setTypeBD("entier");
$oRech->setValeurRecherche("1");
$oRech->setTableBD("cms_content");

$aRecherche[] = $oRech;
//---------------------------------------------------

//---------------------------------------------------
// critère caché : tous les content de statut (liste à statut demandée)

if ($_SESSION['idStatut'] != "") {
$oRech = new dbRecherche();

$oRech->setNomBD("statut_content");
$oRech->setTypeBD("entier");
$oRech->setValeurRecherche($_SESSION['idStatut']);
$oRech->setTableBD("cms_content");

$aRecherche[] = $oRech;
}
//---------------------------------------------------

//---------------------------------------------------
// critère caché : tous les content de CMS_ARCHI_CONTENT de statut EN LIGNE


$oRech = new dbRecherche();

$oRech->setNomBD("cms_archi_content.statut_archi");
$oRech->setTypeBD("entier");
$oRech->setValeurRecherche(DEF_ID_STATUT_LIGNE);
$oRech->setTableBD("cms_content;cms_archi_content");
$oRech->setJointureBD("cms_archi_content.id_content_archi=cms_content.id_content");

$aRecherche_archi[] = $oRech;
//---------------------------------------------------

//---------------------------------------------------
// critère caché : tous les content qui ne sont pas de statut en ligne
// les content en ligne sont affichés depuis la table CMS_ARCHI_CONTENT
$oRech = new dbRecherche();

$oRech->setValeurRecherche("declencher_recherche");
$oRech->setTableBD("cms_content");

if (DEF_BDD == "ORACLE" || DEF_BDD == "POSTGRES") $sJointure = "NOT statut_content = ".DEF_ID_STATUT_LIGNE;
else if (DEF_BDD == "MYSQL") $sJointure = "cms_content.statut_content <> ".DEF_ID_STATUT_LIGNE;

$oRech->setJointureBD($sJointure);
$oRech->setPureJointure(1);

$aRecherche[] = $oRech;
//---------------------------------------------------

//---------------------------------------------------
// critère caché : tous les content dans cms_struct_page dans une zone editable
$oRech = new dbRecherche();

$oRech->setValeurRecherche("declencher_recherche");
$oRech->setTableBD("cms_struct_page;cms_content");
$oRech->setJointureBD("cms_content.id_content=cms_struct_page.id_content;cms_struct_page.id_zonedit_content is not null");
$oRech->setPureJointure(1);

$aRecherche[] = $oRech;
//---------------------------------------------------

//---------------------------------------------------
// critère caché : tous les archi content dans cms_struct_page dans une zone editable
$oRech = new dbRecherche();

$oRech->setValeurRecherche("declencher_recherche");
$oRech->setTableBD("cms_struct_page;cms_archi_content");
$oRech->setJointureBD("cms_archi_content.id_content_archi=cms_struct_page.id_content;cms_struct_page.id_zonedit_content is not null");
$oRech->setPureJointure(1);

$aRecherche_archi[] = $oRech;
//---------------------------------------------------

//---------------------------------------------------
// critère caché : tous les content des pages valides
$oRech = new dbRecherche();

$oRech->setValeurRecherche("declencher_recherche");
$oRech->setTableBD("cms_struct_page;cms_content;cms_page");
$oRech->setJointureBD("cms_content.id_content=cms_struct_page.id_content;cms_struct_page.id_page=cms_page.id_page;cms_page.valid_page=1");
$oRech->setPureJointure(1);

$aRecherche[] = $oRech;
//---------------------------------------------------

//---------------------------------------------------
// critère caché : tous les archi content des pages valides
$oRech = new dbRecherche();

$oRech->setValeurRecherche("declencher_recherche");
$oRech->setTableBD("cms_struct_page;cms_archi_content;cms_page;cms_arbo_pages");
$oRech->setJointureBD("cms_archi_content.id_content_archi=cms_struct_page.id_content;cms_struct_page.id_page=cms_page.id_page;cms_page.valid_page=1;cms_content.nodeid_content = cms_arbo_pages.node_id");
$oRech->setPureJointure(1);

$aRecherche_archi[] = $oRech;

//---------------------------------------------------
//on ne traite pas les content archivés au cas où certaines 
// ne sont plus reliées aux pages
//---------------------------------------------------

if ($_SESSION["idStatut"] == "") {
	$oRech = new dbRecherche();
	
	$oRech->setValeurRecherche("declencher_recherche");
	$oRech->setTableBD("cms_content");

	if (DEF_BDD == "ORACLE" || DEF_BDD == "POSTGRES") $sJointure = "NOT statut_content = 5 ";
	else if (DEF_BDD == "MYSQL") $sJointure = "cms_content.statut_content <> 5 ";
	
	$oRech->setJointureBD($sJointure);
	$oRech->setPureJointure(1);
	
	$aRecherche_archi[] = $oRech;
	$aRecherche[] = $oRech;
}





//---------------------------------------------------
// nom de la zone éditée

if ($rech_zone != "") {
 
$oRech = new dbRecherche();

$oRech->setValeurRecherche("declencher_recherche");
$oRech->setTableBD("cms_content;cms_struct_page;cms_page;cms_arbo_pages");
$oRech->setJointureBD("(cms_content.name_content like '%".$rech_zone."%' or cms_arbo_pages.node_libelle like '%".$rech_zone."%' or cms_arbo_pages.node_absolute_path_name like '%".$rech_zone."%' );cms_content.id_content=cms_struct_page.id_content;cms_struct_page.id_page=cms_page.id_page;cms_page.nodeid_page=cms_arbo_pages.node_id");
$oRech->setPureJointure(1);

$aRecherche[] = $oRech;
$aRecherche_archi[] = $oRech;

 


}
//---------------------------------------------------

// les admin et gestionnaire de site ont accès aux critères de recherche
// les rédacteurs ne voient que LEUR briques
$idUser = $idUser_logged;
if ($sRank != DEF_REDACT) {
	$idUser = $_POST['selectUser'];
}
else {
	// on ne fait rien
	// le user est alors celui qui est connecté
	// le rédacteur ne verra que SES briques
}

//---------------------------------------------------
// utilisateur

$oRech = new dbRecherche();

$oRech->setNomBD("bo_users.user_id");
$oRech->setTypeBD("entier");
$oRech->setValeurRecherche($idUser);
$oRech->setTableBD("bo_users;cms_droit;cms_content;cms_struct_page");
$oRech->setJointureBD("bo_users.user_id=cms_droit.user_id;cms_droit.id_content=cms_content.id_content;cms_struct_page.id_content=cms_content.id_content;cms_arbo_pages.node_id=cms_content.nodeid_content");
$oRech->setPureJointure(0);

$aRecherche[] = $oRech;
$aRecherche_archi[] = $oRech;
//---------------------------------------------------

//---------------------------------------------------
// gabarit

$oRech = new dbRecherche();

$oRech->setNomBD("cms_page.gabarit_page");
$oRech->setTypeBD("text");
$oRech->setValeurRecherche($rech_selectGabarit);
$oRech->setTableBD("cms_content;cms_struct_page;cms_page");
$oRech->setJointureBD("cms_content.id_content=cms_struct_page.id_content;cms_struct_page.id_page=cms_page.id_page");
$oRech->setPureJointure(0);

$aRecherche[] = $oRech;
$aRecherche_archi[] = $oRech;
//---------------------------------------------------

//---------------------------------------------------
// page

$oRech = new dbRecherche();

$oRech->setNomBD("cms_struct_page.id_page");
$oRech->setTypeBD("entier");
$oRech->setValeurRecherche($rech_page_id);
$oRech->setTableBD("cms_content;cms_struct_page");
$oRech->setJointureBD("cms_content.id_content=cms_struct_page.id_content");
$oRech->setPureJointure(0);

$aRecherche[] = $oRech;
$aRecherche_archi[] = $oRech;
//---------------------------------------------------

//---------------------------------------------------
// chemin

$oRech = new dbRecherche();

$oRech->setNomBD("cms_content.nodeid_content");
$oRech->setTypeBD("entier");
$oRech->setValeurRecherche($rech_node_id);
$oRech->setTableBD("cms_content");
$oRech->setJointureBD("");
$oRech->setPureJointure(0);



$aRecherche[] = $oRech;
$aRecherche_archi[] = $oRech;



//===================================================================
/*

A REVOIR
TRIER LE TABLEAU
ORDER IMPOSSIBLE CAR DONNEES DE TABLES DIFFERENTES


// clause order by envoyée par champtri et senstri
if ($_POST['champTri'] != "") 
	$sOrderBy = $_POST['champTri']." ".$_POST['sensTri'];
else $sOrderBy = " cms_content.name_content ASC ";
*/


//===================================================================

	// EXECUTION DE LA REQUETE
	// liste des briques editables (nommées zones dans l'interface)

	// liste de toutes les briques EDITABLES inclues dans une zone editable
	
	$aContenusID = getListContentEdit($aRecherche, $sOrderBy, DEF_NBDROIT_CONTENT);

	// taille totale de la liste résultat
	$eContent = getCountListContentEdit($aRecherche, $sOrderBy);
	 
	// affichage de TOUTES les briques
	// les briques de CMS_CONTENT et toutes les briques en ligne de CMS_ARCHI_CONTENT
	
	if ($_SESSION['idStatut'] == "") {
		// liste de toutes les briques en ligne dans CMS_ARCHI_CONTENT
		$sOrderBy = "cms_arbo_pages.node_absolute_path_name";
		$aContenusArchiID = getListContentEdit_archi($aRecherche_archi, $sOrderBy, DEF_NBDROIT_CONTENT);
	}

	// construction de l'objet d'affichage
	
	// CMS_CONTENT
	for ($p=0; $p<sizeof($aContenusID); $p++)
	{	
		$tempContenus = new Cms_affich_content("CMS_CONTENT", $aContenusID[$p][0]);
		if($tempContenus->getId() != NULL){
			$aContenus[] = $tempContenus;
		}
	}
	
	// affichage de TOUTES les briques
	// les briques de CMS_CONTENT et toutes les briques en ligne de CMS_ARCHI_CONTENT
	if ($_SESSION['idStatut'] == "") {
		// CMS_ARCHI_CONTENT
		for ($p=0; $p<sizeof($aContenusArchiID); $p++)
		{
			
			$tempContenus = new Cms_affich_content("CMS_ARCHI_CONTENT", $aContenusArchiID[$p][1]);
			if($tempContenus->getId() != NULL){
				$aContenus[] = $tempContenus;
			}
		}
	}

//===================================================================

// affichage de l'état de mes contenus (par statut) et des contenus en général

function getSqlContentForUser($eStatut, $eUser) {

	$sql = " SELECT count(*) ";
	$sql.= " FROM cms_content, cms_droit, cms_struct_page, cms_page";
	$sql.= " WHERE cms_content.statut_content=".$eStatut." AND ";
	$sql.= " cms_droit.id_content=cms_content.id_content AND ";
	$sql.= " cms_droit.user_id=".$eUser." AND ";
	$sql.= " cms_content.id_site=".$_SESSION['idSite_travail']." AND ";
	$sql.= " cms_content.id_content=cms_struct_page.id_content AND ";
	$sql.= " cms_struct_page.id_zonedit_content is not null AND ";
	$sql.= " cms_struct_page.id_page=cms_page.id_page AND ";
	$sql.= " cms_page.valid_page=1";
	
	return($sql);
}

function getSqlArchiForUser($eStatut, $eUser) {

	$sql = " SELECT count(*) ";
	$sql.= " FROM cms_archi_content, cms_droit, cms_struct_page, cms_page ";
	$sql.= " WHERE cms_archi_content.statut_archi=".$eStatut." AND ";
	$sql.= " cms_droit.id_content=cms_archi_content.id_content_archi AND ";
	$sql.= " cms_droit.user_id=".$eUser." AND ";
	$sql.= " cms_archi_content.id_content_archi=cms_struct_page.id_content AND ";
	$sql.= " cms_struct_page.id_zonedit_content is not null AND ";
	$sql.= " cms_struct_page.id_page=cms_page.id_page AND ";
	$sql.= " cms_page.valid_page=1 AND ";
	$sql.= " cms_page.id_site=".$_SESSION['idSite_travail'];

	return($sql);
}

function getSqlContentAll($eStatut) {

	$sql = " SELECT count(*) ";
	$sql.= " FROM cms_content, cms_struct_page, cms_page, cms_arbo_pages ";
	$sql.= " WHERE cms_content.statut_content=".$eStatut." AND ";
	$sql.= " cms_content.valid_content=1 AND cms_content.actif_content=1 AND ";
	$sql.= " cms_content.id_site=".$_SESSION['idSite_travail']." AND ";
	$sql.= " cms_content.isbriquedit_content=1 AND ";
	$sql.= " cms_content.id_content=cms_struct_page.id_content AND ";
	$sql.= " cms_struct_page.id_zonedit_content is not null AND ";
	$sql.= " cms_struct_page.id_page=cms_page.id_page AND ";
	$sql.= " cms_page.valid_page=1";
	$sql.= " AND cms_content.nodeid_content = cms_arbo_pages.node_id ";

	return($sql);
}

function getSqlArchiAll($eStatut) {

	$sql = " SELECT count(*) ";
	$sql.= " FROM cms_archi_content, cms_struct_page, cms_page, cms_arbo_pages, cms_content";
	$sql.= " WHERE cms_archi_content.statut_archi=".$eStatut." AND ";
	$sql.= " cms_archi_content.id_content_archi=cms_struct_page.id_content AND ";
	$sql.= " cms_struct_page.id_zonedit_content is not null AND ";
	$sql.= " cms_struct_page.id_page=cms_page.id_page AND ";
	$sql.= " cms_page.valid_page=1 AND ";
	$sql.= " cms_page.id_site=".$_SESSION['idSite_travail'];
	$sql.= " AND cms_content.nodeid_content = cms_arbo_pages.node_id ";
	$sql.= " AND cms_content.id_content = cms_archi_content.id_content_archi ";
	$sql.= " AND cms_content.statut_content = ".$eStatut;

	return($sql);
}

// MES CONTENUS

$sql = getSqlContentForUser(DEF_ID_STATUT_ATTEN, $idUser_logged);
$eContent_ATTEN_MOI = dbGetUniqueValueFromRequete($sql);

$sql = getSqlContentForUser(DEF_ID_STATUT_REDACT, $idUser_logged);
$eContent_REDACT_MOI = dbGetUniqueValueFromRequete($sql);

$sql = getSqlContentForUser(DEF_ID_STATUT_GEST, $idUser_logged);
$eContent_GEST_MOI = dbGetUniqueValueFromRequete($sql);

$sql = getSqlArchiForUser(DEF_ID_STATUT_LIGNE, $idUser_logged);
$eContent_LIGNE_MOI = dbGetUniqueValueFromRequete($sql);

$sql = getSqlContentForUser(DEF_ID_STATUT_ARCHI, $idUser_logged);
$eContent_ARCHI_MOI = dbGetUniqueValueFromRequete($sql);

$eContent_ALL_MOI = $eContent_ATTEN_MOI + $eContent_REDACT_MOI + $eContent_GEST_MOI + $eContent_LIGNE_MOI + $eContent_ARCHI_MOI;


// LES CONTENUS

$sql = getSqlContentAll(DEF_ID_STATUT_ATTEN);
$eContent_ATTEN = dbGetUniqueValueFromRequete($sql);

$sql = getSqlContentAll(DEF_ID_STATUT_REDACT);
$eContent_REDACT = dbGetUniqueValueFromRequete($sql);

$sql = getSqlContentAll(DEF_ID_STATUT_GEST);
$eContent_GEST = dbGetUniqueValueFromRequete($sql);

$sql = getSqlArchiAll(DEF_ID_STATUT_LIGNE);
$eContent_LIGNE = dbGetUniqueValueFromRequete($sql);

$sql = getSqlContentAll(DEF_ID_STATUT_ARCHI);
$eContent_ARCHI = dbGetUniqueValueFromRequete($sql);

$eContent_ALL = $eContent_ATTEN + $eContent_REDACT + $eContent_GEST + $eContent_LIGNE + $eContent_ARCHI;

?>