<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

// sponthus 20/06/2005
// ajout de id_site (sur quel site on enregistre la brique)

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/procedures.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
 include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


activateMenu('gestionbrique');  //permet de dérouler le menu contextuellement

////////////////////////
// VALEURS POSTEES /////

$id = $_GET['id'];
if($_POST['id']!="") $id = $_POST['id'];
////////////////////////

if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

////////////////////////
// objet content
$composant = null;
if (strlen($id) > 0 ) {
	$composant = getComposantById($id);
	$node_id = $composant['node_id'];
	$node = getNodeInfos($db, $node_id);
	$node_name = $node['libelle'];
}
////////////////////////

?>
<link href="/backoffice/cms/css/bo.css" rel="stylesheet" type="text/css">

<span class="arbo2"><b>Stockage du composant <?php echo $_POST['NAMEcontent']; ?> dans la base de composants</b></span>
<?php
	// a_voir melanie bandeau d'affichage à faire plus joli ?
	// dans le fichier selectSite.php
if (DEF_MENUS_MINISITES == "ON") print(putAfficheSite());
	
	if (!isset($_POST['node_id']) or (strlen($_POST['node_id'])==0)) {
	$htmlContent=stripslashes($_POST['HTMLcontent']);
	$htmlContent=preg_replace("/'/",'`',$htmlContent);
?>
<span class="arbo">
<?php
} else {
?>
<?php
	// ok, on enregistre
	$htmlContent = stripslashes($_POST['HTMLcontent']);
	$htmlContent= preg_replace("/`/","'",$htmlContent);
	$htmlContent= preg_replace("/(<|&lt;)§/","<?",$htmlContent);
	$htmlContent= preg_replace("/§(>|&gt;)/","?>",$htmlContent);
	$test = null;
	
	$id = $_GET['id'];
	if($_POST['id']!="") $id = $_POST['id'];

	// préparation des objets formulaire, champs
	if ($_POST['OBJTABLEcontent'] == 'cms_formulaire')
	{
		// objet cms_formulaire
		$oForm = new Cms_form();
			
		// alimentation objet
		$oForm->setName_form($_POST['NAMEcontent']);
		$oForm->setDesc_form($_POST['desc_form']);
		$oForm->setComm_form($_POST['comm_form']);
		$oForm->setId_site($idSite);

		if ($id > 0) {
			$oContentForm = new Cms_content($id);
			$oForm->setId_form($oContentForm->getObj_id_content());
			dbUpdate($oForm);
			$eId_form = $oContentForm->getObj_id_content();
		} else $eId_form = dbInsertWithAutoKey($oForm);

		if ($_POST['OBJIDcontent'] != "") $_POST['OBJIDcontent'] = $eId_form;

		// objet cms_champform
		$oChampform = new Cms_champform();
	
		// suppression de tous les champs du formulaire s'ils existent
		deleteAllChampsForm($eId_form);
		
		for ($p=0; $p < DEF_MAXCHAMPSFORM; $p++) {
			// alimentation objet
			$oChampform->setId_form($eId_form);
			$oChampform->setName_champ($_POST['name_champ_'.$p]);
			$oChampform->setDesc_champ("");
			$oChampform->setType_champ($_POST['type_champ_'.$p]);
			$oChampform->setValeur_champ($_POST['valeur_champ_'.$p]);
			$oChampform->setValdefaut_champ($_POST['valdefaut_champ_'.$p]);

			if ($_POST['largeur_champ_'.$p] != "") $oChampform->setLargeur_champ($_POST['largeur_champ_'.$p]);
			if ($_POST['hauteur_champ_'.$p] != "") $oChampform->setHauteur_champ($_POST['hauteur_champ_'.$p]);
			if ($_POST['taillemax_champ_'.$p] != "") $oChampform->setTaillemax_champ($_POST['taillemax_champ_'.$p]);
			if ($_POST['obligatoire_champ_'.$p] != "") $oChampform->setObligatoire_champ($_POST['obligatoire_champ_'.$p]);
			
			if ($_POST['type_champ_'.$p] != "INUTIL") $eId_champ = dbInsertWithAutoKey($oChampform);			
		}
	}

	
	$oContent = new Cms_content();
	
	$oContent->setId_content($id);
	$oContent->setName_content($_POST['NAMEcontent']); 
	$oContent->setType_content($_POST['TYPEcontent']);
	$oContent->setWidth_content($_POST['WIDTHcontent']);
	$oContent->setHeight_content($_POST['HEIGHTcontent']);
	$oContent->setDateadd_content("");
	$oContent->setDateupd_content("");
	$oContent->setDatedlt_content("");

	if ($_POST['VALIDcontent'] == "true") $oContent->setValid_content(1);
	else  $oContent->setValid_content(0);

	if ($_POST['ACTIFcontent'] == "true") $oContent->setActif_content(1);
	else $oContent->setActif_content(0);

	$oContent->setHtml_content($htmlContent);
	$oContent->setNodeid_content("".$_POST['node_id']);

	$oContent->setObj_table_content($_POST['OBJTABLEcontent']);
	if ($_POST['OBJIDcontent'] == "") $_POST['OBJIDcontent']=-1;
	$oContent->setObj_id_content($_POST['OBJIDcontent']);			

	$oContent->setId_site($idSite);	

	$oContent->setIszonedit_content(0);
	
	$oContent->setStatut_content(DEF_ID_STATUT_LIGNE); // Par défo les briques créées ici sont "en ligne"

	$oContent->setIsbriquedit_content(0);
	
	$test = storeComposant($idSite, $oContent);

/*
	if (strlen($id) > 0 ) {
		$test = storeComposant($idSite, $_POST['NAMEcontent'], $_POST['TYPEcontent'], $_POST['WIDTHcontent'], $_POST['HEIGHTcontent'], $_POST['VALIDcontent'], $_POST['ACTIFcontent'], $htmlContent, $_POST['node_id'], $_POST['OBJTABLEcontent'], $_POST['OBJIDcontent'], $id); // 10 param = ajout, 11 = modif
	} else {
		$test = storeComposant($idSite, $_POST['NAMEcontent'], $_POST['TYPEcontent'], $_POST['WIDTHcontent'], $_POST['HEIGHTcontent'], $_POST['VALIDcontent'], $_POST['ACTIFcontent'], $htmlContent, $_POST['node_id'], $_POST['OBJTABLEcontent'], $_POST['OBJIDcontent']); // 10 param = ajout, 11 = modif
	}
*/
	if($test) {
?>
<br /><br /><span class="arbo">La brique composant a été correctement sauvegardé. Vous pouvez dès à présent l'utiliser dans la conception de vos pages. <br />
<br />
<?php
		// test des pages qui utilisent la brique modifiée.
		if(strlen($id) > 0) {
?>
</span>
<hr align="center" width="80%" class="arbo2" height="1">
<br /><br />
<span class="arbo"> La modification d'une brique utilisée dans des pages enregistrées nécessite que la page soit regénérée pour que les changements soient visibles.<br />
<br />
<?php
			include_once('regeneratePages.php');
		}
	} else {
?>
<br /><br />Une erreur s'est produite lors de la sauvegarde de la brique. Veuillez réessayer. Si le problème persiste, veuillez contacter l'administrateur</span>
<?php
	}
}


?>
</span>