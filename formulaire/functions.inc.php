<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

function createObjectForm( $formulaire , $idform) { 

	echo "createObjectForm";

	//echo "attributs <br />";
	//foreach ($formulaire as $myField) {
	$myAttributes = $formulaire->getAttributes();
	//}
	 
	// nom du formulaire
	if (isset ($myAttributes[0]["NAME"]) && $myAttributes[0]["NAME"] != '' ) {
		$form_name = noAccent($myAttributes[0]["NAME"]);
	}
	
	// email du destinataire des mails
	if (isset ($myAttributes[0]["EMAIL"]) && $myAttributes[0]["EMAIL"] != '' ) {
		$form_email = $myAttributes[0]["EMAIL"] ;
	}
	
	// post du formulaire, si renseigné, on renvoie sur cette page
	if (isset ($myAttributes[0]["POST"]) && $myAttributes[0]["POST"] != '' ) {
		$form_post = $myAttributes[0]["POST"] ;
	}
	
	// post du formulaire, si renseigné, on renvoie sur cette page
	if (isset ($myAttributes[0]["MINISITE"]) && $myAttributes[0]["MINISITE"] != '' ) {
		$form_minisite = $myAttributes[0]["MINISITE"];
		$aSite = dbGetObjectsFromFieldValue("cms_site", array("get_rep"),  array($form_minisite), NULL);
		if (newSizeOf($aSite)) {
			$form_idSite = $aSite[0]->get_id();
		} 
	}
	else {
		$form_idSite = $_SESSION["idSite_travail"];
	}
	
	// texte à afficher après validation   
	if (isset ($myAttributes[0]["TEXTEOK"]) && $myAttributes[0]["TEXTEOK"] != '' ) {
		$form_texteok = $myAttributes[0]["TEXTEOK"] ;
	}
	
	// texte d'accusé de réception 
	if (isset ($myAttributes[0]["TEXTEAR"]) && $myAttributes[0]["TEXTEAR"] != '' ) {
		$form_textear = $myAttributes[0]["TEXTEAR"] ;
	}
	
	// on vérifie s'il existe déjà un formulaire avec ce nom
	
	if ($idform == '') {
		$aForm = dbGetObjectsFromFieldValue("Cms_form", array("getName_form", "getId_site"),  array($form_name, $form_idSite), NULL);
		if (newSizeOf($aForm) > 0) {
			$idform = $aForm[0]->getId_form();
		} 
	}
	
	// création de l'objet Formulaire
	$oForm = new Cms_form();
	if ($idform != '') $oForm->setId_form($idform);
	$oForm->setName_form($form_name);
	$oForm->setDesc_form($form_texteok);
	$oForm->setComm_form($form_email);
	$oForm->setAr_form($form_textear);
	$oForm->setPost_form($form_post);
	$oForm->setId_site($form_idSite);
	
	if ($idform != '') {
		$oR = dbUpdate ($oForm);
	}
	else {
		$idform = dbInsertWithAutoKey ($oForm);
	}
	 
	 
	 
 	// ---------------------------------------
	// champs
	// -------------------------------------------
	//echo "idform : ".$idform."<br />";
  	deleteAllChampsForm($idform);
	  
	$myFields = $formulaire->getParams();
	 
	foreach ($myFields as $field) {
		
		
		$name_champ = $field['name'];
		$id_champ = $field['id']; 
		$type_champ = $field['type'];
		$values_champ = $field['values']; // array
		$valeur_champ = "";
		if (newSizeOf($values_champ) > 0) {
			$aValues = array () ;
			foreach ($values_champ as $value) {
				array_push ($aValues, $value["LIBELLE"]) ;
			}
			$valeur_champ = join (";", $aValues);
		}
		else {
			$valeur_champ = "";
		}
		$oblig_champ = $field['oblig']; 
		if ($oblig_champ ==  "true" ) $idoblig_champ = 1; else $idoblig_champ = 0; 
		$class_champ = $field['class'];
		$option_champ = $field['option'];
		$small_champ = $field['small'];
		$br_champ = $field['br'];
		$display_champ = $field['display'];
		$pubkey_champ = $field['pubkey'];// captcha
		$privkey_champ = $field['privkey'];// captcha
		$theme_champ = $field['theme'];// captcha
		$lang_champ = $field['lang']; // captcha
		$pattern_champ = $field['pattern'];
		$myClass_champ = $field['myClass'];
		$myClassField_champ = $field['myClassField'];
		$myClassOrderBy_champ = $field['myClassOrderBy'];
		$myClassOrderBySens_champ = $field['myClassOrderBySens'];
		$myClassConditions_champ = $field['myClassConditions'];     
 
		$params = array();
		
		if ($class_champ != '') $params["class"] = $class_champ;
		if ($small_champ != '') $params["small"] = $small_champ;
		if ($br_champ != '') $params["br"] = $br_champ;
		if ($display_champ) {
			$params["captcha"] = array(); 
				$params["captcha"]["pubkey"] = $pubkey_champ;
				$params["captcha"]["privkey"] = $privkey_champ;
				$params["captcha"]["theme"] = $theme_champ;
				$params["captcha"]["lang"] = $lang_champ;
		}
		if ($myClass_champ != '') {
			$params["classe"] = array(); 
				$params["classe"]["myClass"] = $myClass_champ;
				$params["classe"]["myClassField"] = $myClassField_champ;
				$params["classe"]["myClassOrderBy"] = $myClassOrderBy_champ;
				$params["classe"]["myClassOrderBySens"] = $myClassOrderBySens_champlang_champ;
				$params["classe"]["myClassConditions"] = $myClassConditions_champ;
		}
		
		$params_champ = serialize($params);
		
		
		// on vérifie s'il existe déjà un formulaire avec ce nom
	 
		
		// on ne traite pas le captcha si display = false
		//echo $id_champ.' '.$idform."<br />";
		//echo "type_champ : ".$type_champ."<br />";
		//echo "display : ".$display_champ."<br />";
		//echo "<br />";
		if ( ($type_champ == "captcha" &&  $display_champ == "false") || ($type_champ == "submit") ) { 
		}
		else {
			$aChamp = dbGetObjectsFromFieldValue("Cms_champform", array("getDesc_champ", "getId_form"),  array($id_champ, $idform), NULL); 
			if (newSizeOf($aChamp) > 0   ) { 
				$idChamp = $aChamp[0]->getId_champ();
			} 
			  
			//echo strtoupper($type_champ)."<br />"; 
			//echo newSizeOf($values_champ)."<br />"; 
			//echo "oblig_champ : ".$oblig_champ."<br />"; 
			//echo "idoblig_champ : ".$idoblig_champ."<br />";  
			//pre_dump(str_replace(' ','',$option_champ));
			//echo "<br />";
			
			
			
			if ($idChamp != '')  $oChamp = new Cms_champform($idChamp) ;
			else $oChamp = new Cms_champform();
			
			$largeur_champ= -1;
			$hauteur_champ= -1;
			$taillemax_champ= -1;
			$objetid_champ= -1;
			
			$oChamp->setId_form($idform);
			$oChamp->setName_champ($name_champ);
			$oChamp->setDesc_champ(removeXitiForbiddenChars(strtolower(noAccent($name_champ))));  
			$oChamp->setType_champ(strtoupper($type_champ));
			$oChamp->setValeur_champ($valeur_champ);  // à revoir
			$oChamp->setValdefaut_champ($valdefaut_champ);  // à revoir
			$oChamp->setLargeur_champ($largeur_champ);  // à revoir
			$oChamp->setHauteur_champ($hauteur_champ);  // à revoir
			$oChamp->setTaillemax_champ($taillemax_champ);  // à revoir
			$oChamp->setObligatoire_champ($idoblig_champ); 
			$oChamp->setObjet_champ($objet_champ);  // à revoir
			$oChamp->setObjetId_champ($objetid_champ);
			$oChamp->setTexte_champ($texte_champ);
			$oChamp->setParams_champ($params_champ);
			$oChamp->setPattern_champ($pattern_champ); 
			
			if ($idChamp != '') {
				$oR = dbUpdate ($oChamp);
			}
			else {
				echo "dbInsertWithAutoKeyoChamp 1";
				$oR = dbInsertWithAutoKey ($oChamp);
			} 
		}
	
		  
 
	 }
	 
	
	
	 // -------------------------
	 // enregistre content
	 // --------------------------
	
	
	
	 
	 $aContent = dbGetObjectsFromFieldValue("Cms_content", array("getType_content", "getObj_id_content"),  array("formulaireHTML", $idform), NULL);
		  
	if (newSizeOf($aContent) > 0) { 
		$id = $aContent[0]->getId_content();
	} 
	
	if ($id != '')  $oContent = new Cms_content($id) ;
	else $oContent = new Cms_content();	 
	
	$_POST['WIDTHcontent'] = -1;
	$_POST['HEIGHTcontent'] = -1 ;
	$_POST['node_id'] = 0;
	
	$oContent->setName_content($form_name); 
	$oContent->setType_content("formulaireHTML");
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
	$oContent->setObj_id_content($idform);			
	
	$oContent->setId_site($_SESSION["idSite_travail"]);	
	
	$oContent->setIszonedit_content(0);
	
	$oContent->setStatut_content(DEF_ID_STATUT_LIGNE); // Par défo les briques créées ici sont "en ligne"
	
	$oContent->setIsbriquedit_content(0);
	
	$test = storeComposant($_SESSION["idSite_travail"], $oContent);
	 
	if ($test)
		 return $oContent->getId_content();
	else
		return false;
}

?>