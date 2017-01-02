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

$id = $_GET['id'];
if(is_post('id')){
	$id = $_POST['id'];
}
if(is_post('idSite')){
	$idSite = $_POST['idSite'];
	$_SESSION['idSite'] = $idSite;
}

if ($idSite == "") $idSite = $_SESSION['idSite'];

$node_id = 0;
$node_name = 'non défini';
$composant = null;

$sTitre = "Créer";
if (strlen($id) > 0 ) {
	$composant = getComposantById($id);
	$node_id = $composant['node_id'];
	$node = getNodeInfos($db, $node_id);
	$node_name = $node['libelle'];

	$sTitre = "Modifier";
}
?>
<script type="text/javascript">
  function chooseFolder() {
		window.open('/backoffice/cms/chooseFolder.php?idSite=<?php echo $idSite; ?>','browser','scrollbars=yes,width=500,height=500,resizeable=yes,menubar=no');
  }
</script>
<div class="ariane"><span class="arbo2">BRIQUE >&nbsp;</span><span class="arbo3"><?php echo $sTitre; ?> une brique HTML</span></div>

<span class="arbo4"><b>Stockage du composant <?php echo $_POST['NAMEcontent']; ?> dans la base de composants</b></span>
<br /><br />
<?php
//if (DEF_MENUS_MINISITES == "ON") print(putAfficheSite());
	
if (!isset($_POST['node_id']) or (strlen($_POST['node_id'])==0)) {
	$htmlContent=stripslashes($_POST['HTMLcontent']);
	$htmlContent=preg_replace("/'/",'`',$htmlContent);
	 
?>
<form name="saveCuBrick" action="<?php echo $_SERVER['PHP_SELF']; ?>?idSite=<?php echo $idSite; ?>&id=<?php echo $_GET['id']; ?>" method="post">
<input type="hidden" name="NAMEcontent" value="<?php echo $_POST['NAMEcontent']; ?>">
<input type="hidden" name="HTMLcontent" value='<?php echo $htmlContent; ?>'>
<input type="hidden" name="VALIDcontent" value="true">
<input type="hidden" name="ACTIFcontent" value="true">
<input type="hidden" name="node_id" value="<?php echo $node_id; ?>">

<input type="hidden" name="OBJTABLEcontent" value="<?php echo $_POST['OBJTABLEcontent']; ?>">
<input type="hidden" name="OBJIDcontent" value="<?php echo $_POST['OBJIDcontent']; ?>">

<input type="hidden" name="id" value="<?php echo $id; ?>">


<!-- spécifique brique formulaire -->
<input type="hidden" name="desc_form" value="<?php echo $_POST['desc_form']; ?>">	
<input type="hidden" name="comm_form" value="<?php echo $_POST['comm_form']; ?>">		
 
<?php
	// préparation des objets formulaire, champs
	if ($_POST['OBJTABLEcontent'] == 'cms_formulaire') {
?>
		<!-- champs du formulaire enr à créer : CMS_FORMULAIRE, CMS_CHAMPFORM -->
		<?php for ($p=0; $p < DEF_MAXCHAMPSFORM; $p++) { ?>
		<!--<input type="hidden" name="name_champ_<?php echo $p; ?>" value="<?php echo $_POST['name_champ_'.$p]; ?>">
		<input type="hidden" name="type_champ_<?php echo $p; ?>" value="<?php echo $_POST['type_champ_'.$p]; ?>">
		<input type="hidden" name="valdefaut_champ_<?php echo $p; ?>" value="<?php echo $_POST['valdefaut_champ_'.$p]; ?>">
		<input type="hidden" name="largeur_champ_<?php echo $p; ?>" value="<?php echo $_POST['largeur_champ_'.$p]; ?>">
		<input type="hidden" name="taillemax_champ_<?php echo $p; ?>" value="<?php echo $_POST['taillemax_champ_'.$p]; ?>">
		<input type="hidden" name="obligatoire_champ_<?php echo $p; ?>" value="<?php echo $_POST['obligatoire_champ_'.$p]; ?>">
		<input type="hidden" name="valeur_champ_<?php echo $p; ?>" value="<?php echo $_POST['valeur_champ_'.$p]; ?>">
		<input type="hidden" name="hauteur_champ_<?php echo $p; ?>" value="<?php echo $_POST['hauteur_champ_'.$p]; ?>">
		<input type="hidden" name="objet_champ_<?php echo $p; ?>" value="<?php echo $_POST['objet_champ_'.$p]; ?>">
		<input type="hidden" name="objetid_champ_<?php echo $p; ?>" value="<?php echo $_POST['objetid_champ_'.$p]; ?>">
		<input type="hidden" name="texte_champ_<?php echo $p; ?>" value="<?php echo replaceQuote2($_POST['texte_champ_'.$p]); ?>">-->
		<?php } ?>	
<?php
}
?>
<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
 <tr>
  <td align="right" bgcolor="E6E6E6">Dossier de stockage&nbsp;:&nbsp;</td>
  <td align="left" bgcolor="E6E6E6"><input name="foldername" type="text" disabled class="arbo" value="<?php echo $node_name; ?>" size="20">
    &nbsp;&nbsp;<?php if(strlen($id) == 0 ) { ?><a href="#" class="arbo" onClick="chooseFolder();"><img src="/backoffice/cms/img/2013/icone/go.png" border="0">&nbsp;choisir le dossier</a><?php } ?></td>
 </tr>
 <tr>
  <td align="right" bgcolor="EEEEEE">Type&nbsp;:&nbsp;</td>
  <td align="left" bgcolor="EEEEEE"><input type="text" disabled class="arbo" value="<?php echo $_POST['TYPEcontent']; ?>" size="15">
<input type="hidden" name="TYPEcontent" value="<?php echo $_POST['TYPEcontent']; ?>"></td>
 </tr>
 <tr bgcolor="E6E6E6">
  <td align="right">Largeur&nbsp;:&nbsp;</td>
  <td align="left"><input type="text" disabled class="arbo" value="<?php echo $_POST['WIDTHcontent']; ?>" size="3"></td>
  <input type="hidden" name="WIDTHcontent" value="<?php echo $_POST['WIDTHcontent']; ?>">
 </tr>
 <tr>
  <td align="right" bgcolor="EEEEEE">Hauteur&nbsp;:&nbsp;</td>
  <td align="left" bgcolor="EEEEEE"><input type="text" disabled class="arbo" value="<?php echo $_POST['HEIGHTcontent']; ?>" size="3"></td>
  <input type="hidden" name="HEIGHTcontent" value="<?php echo $_POST['HEIGHTcontent']; ?>">
 </tr>
 <tr bgcolor="D2D2D2">
  <td colspan="2" align="center"><input name="Enregistrer" type="submit" class="arbo" value="Enregistrer"></td>
 </tr>
</table>
</form>
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
	 
	//pre_dump($_POST);
	// préparation des objets formulaire, champs
	if ($_POST['OBJTABLEcontent'] == 'cms_formulaire')
	{
		// objet cms_formulaire
		if ($_POST["id_form"] != '') {
			$oForm = new Cms_form($_POST["id_form"]);
		}
		else {
			$oForm = new Cms_form();
		}	
		// alimentation objet
		$oForm->setName_form($_POST['NAMEcontent']);
		$oForm->setDesc_form($_POST['desc_form']);
		$oForm->setComm_form($_POST['comm_form']);
		$oForm->setAr_form($_POST['ar_form']);
		$oForm->setPost_form($_POST['post_form']);
		$oForm->setId_site($idSite);
 
		if ($id > 0) {
			$oContentForm = new Cms_content($id);
			$oForm->setId_form($oContentForm->getObj_id_content());
			dbUpdate($oForm);
			$eId_form = $oContentForm->getObj_id_content();
		} 
		else {
			if ($_POST["id_form"] != '') { 
				$eId_form = dbUpdate($oForm);
			}
			else {
				$eId_form = dbInsertWithAutoKey($oForm);
			}
		}	
		
		if ($_POST["id_form"] != '') {
			$oForm = new Cms_form($_POST["id_form"]);
		}
		else {
			$oForm = new Cms_form();
		}
		
		if ($_POST['OBJIDcontent'] != "") $_POST['OBJIDcontent'] = $eId_form;

		// objet cms_champform
		$oChampform = new Cms_champform();
		 
		// on met à jour les champs avec le bon id du formulaire
		if (isset($_POST['nb_champ']) && $_POST['nb_champ'] > 0 )  {
			$sql= 'select * from  cms_champform where id_form = '.$_POST["id_form"].' order by id_champ DESC '; 
			 
			$aChamps = dbGetObjectsFromRequete("cms_champform", $sql); 
			foreach ($aChamps as $oChamp) {
				$oChamp->setId_form($eId_form);
				$oR = dbUpdate ($oChamp);
			}
		}
		// suppression de tous les champs du formulaire s'ils existent
		/*deleteAllChampsForm($eId_form);
		
		for ($p=0; $p < DEF_MAXCHAMPSFORM; $p++) {
			// alimentation objet
			$oChampform->setId_form($eId_form);
			$oChampform->setName_champ($_POST['name_champ_'.$p]);
			$oChampform->setDesc_champ("");
			$oChampform->setType_champ($_POST['type_champ_'.$p]);
			$oChampform->setValeur_champ($_POST['valeur_champ_'.$p]);
			$oChampform->setValdefaut_champ($_POST['valdefaut_champ_'.$p]);
			$oChampform->setObjet_champ($_POST['objet_champ_'.$p]);
			if ($_POST['objetid_champ_'.$p]!="") 
				$oChampform->setObjetid_champ($_POST['objetid_champ_'.$p]);
			else 
				$oChampform->setObjetid_champ(-1);
			$oChampform->setTexte_champ($_POST['texte_champ_'.$p]);
	 
			if ($_POST['largeur_champ_'.$p] != "") $oChampform->setLargeur_champ($_POST['largeur_champ_'.$p]);
			if ($_POST['hauteur_champ_'.$p] != "") $oChampform->setHauteur_champ($_POST['hauteur_champ_'.$p]);
			if ($_POST['taillemax_champ_'.$p] != "") $oChampform->setTaillemax_champ($_POST['taillemax_champ_'.$p]);
			if ($_POST['obligatoire_champ_'.$p] != "") $oChampform->setObligatoire_champ($_POST['obligatoire_champ_'.$p]);
			
			if ($_POST['type_champ_'.$p] != "INUTIL") $eId_champ = dbInsertWithAutoKey($oChampform);			
		}*/
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
			include_once($_SERVER['DOCUMENT_ROOT'].'/backoffice/cms/regeneratePages.php');
		}
	} else {
?>
<br /><br />Une erreur s'est produite lors de la sauvegarde de la brique. Veuillez réessayer. Si le problème persiste, veuillez contacter l'administrateur</span>
<?php
	}
}


?>
</span>