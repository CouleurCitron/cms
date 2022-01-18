<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
if (!isset($classeName)){
	$classeName = preg_replace('/[^_]*_(.*)\.php/si', '$1', basename($_SERVER['PHP_SELF']));
}




//------------------------------------------------------------------------------------------------------

// Formulaire de saisie 

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/lib.inc.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
 include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

// Chargement de la classe Upload
require_once('cms-inc/lib/fileUpload/upload.class.php');

//cas de parametres dans l'url
if($_SESSION['listParam']!="") {
	$listParam = $_SESSION['listParam'];
}
else {
	$_SESSION['listParam']=$_SERVER['QUERY_STRING'];
	$listParam=$_SESSION['listParam'];
}
// Instanciation d'un nouvel objet "upload"
$Upload = new Upload();

$Upload-> MaxFilesize = strval(12*1024);
// -----------------------------------

$actiontodo = ( $_POST['actiontodo']!="SAUVE" ) ? "MODIF" : "SAUVE" ;

// enregistrement à modifier
//$id=intval($_GET['id']);


$display=intval($_GET['display']);
if(!isset($display)){
	$display=intval($_POST['display']);
}

if (is_get("id")) {
	$id=$_GET['id'];
	$qryref = dbGetArrayOneFieldFromRequete($_SESSION['sqlpag']);
	for ($i=0;$i<count($qryref);$i++) {
		if ($qryref[$i]==$id) {
		//echo $qryref[$i].$i."<br>";
		$_SESSION['pag']=$i+1;
		}
	}
}
// Pour calcul de l'id par rapport à la recherche et récupération de la position dans la navigation par rapport à cet id
else {
	$qryref = dbGetArrayOneFieldFromRequete($_SESSION['sqlpag']);
	$id=$qryref[$id-1];
	for ($i=0;$i<count($qryref);$i++) {
		if ($qryref[$i]==$id) {
			$_SESSION['pag']=$i+1;
		}
	}
	if (is_get("adodb_next_page")) {
		$id=$_GET['adodb_next_page'];
	}
	else {
		$qryref = dbGetArrayOneFieldFromRequete($_SESSION['sqlpag']);
		$id=intval($_POST['id']);
		for ($i=0;$i<count($qryref);$i++) {
			if ($qryref[$i]==$id) {
				$_SESSION['pag']=$i+1;
			}
		}
	}
}
if (is_post("id")) {
	$id=$_POST['id'];
}

// activation du menu : déroulement
activateMenu("gestion".$classeName); 

// objet 
if ( $display > 0 ){
	 $operation = "UPDATEORINSERT";
}
elseif ( $id > 0 ){
	 $operation = "UPDATE";
}
else {
	$operation = "INSERT";
}

if ( $operation == "INSERT" ) { // Mode ajout
	eval("$"."oRes = new ".$classeName."();");
}
elseif ( $operation == "UPDATE" ) { // Mode mise à jour
	eval("$"."oRes = new ".$classeName."($"."id);");
}
else{ // Mode acces par display
	eval("$"."oRes = new ".$classeName."();");
}



$sXML = $oRes->XML;
xmlStringParse($sXML);

$classeName = $stack[0]["attrs"]["NAME"];
if (isset($stack[0]["attrs"]["LIBELLE"]) && ($stack[0]["attrs"]["LIBELLE"] != "")){
	$classeLibelle = stripslashes($stack[0]["attrs"]["LIBELLE"]);
}
else{
	$classeLibelle = $classeName;
}

$classePrefixe = $stack[0]["attrs"]["PREFIX"];
$aNodeToSort = $stack[0]["children"];

// enregistrement à modifier par son display si display isset 


if(isset($display) && ($display!="")){
	// recherche le record 
	$displayField = $stack[0]["attrs"]["DISPLAY"];
	
	$aRechercheDisplay = array();
	$oRechDisplay = new dbRecherche();

	$oRechDisplay->setValeurRecherche("declencher_recherche");
	$oRechDisplay->setTableBD($classeName);	
	$oRechDisplay->setJointureBD($classePrefixe."_".$displayField." = ".$display);
	$oRechDisplay->setPureJointure(1);
	$aRechercheDisplay[] = $oRechDisplay;
	$sqlDisplay = "SELECT count(".$classeName.".".$classePrefixe."_id) ";
	$sqlDisplay.= dbMakeRequeteWithCriteres($classeName, $aRechercheDisplay, "");
	$recordNum = intval(dbGetUniqueValueFromRequete($sqlDisplay));
	
	if ($recordNum == 0){
		//echo "insert";
		$operation = "INSERT";
		eval("$"."oRes->set_".$displayField."(".$display.");");
		//pre_dump($oRes);
	}
	elseif ($recordNum == 1){ // tout va bien
		//echo "update";
		$operation = "UPDATE";
		$sqlDisplay = "SELECT ".$classeName.".* ";
		$sqlDisplay .= dbMakeRequeteWithCriteres($classeName, $aRechercheDisplay, " ".$classePrefixe."_id DESC ");
		$aResponseDisplay = dbGetObjectsFromRequete($classeName, $sqlDisplay);

		$oRes = $aResponseDisplay[0];
		$id = $oRes->get_id();
	}
	else{
		echo "trop de résultats!";
	}

}


// nombre de champs upload désiré
$numUploadFields = 0;
for ($iFile=0;$iFile<count($aNodeToSort);$iFile++){
	if ($aNodeToSort[$iFile]["name"] == "ITEM"){
		if ($aNodeToSort[$iFile]["attrs"]["OPTION"] == "file"){ 
			$numUploadFields++;
		}
	}
}
// gestion popup wysiwyg
/*
<a href="javascript:openWYSYWYGWindow('http://".$_SERVER['HTTP_HOST']."/backoffice/cms/utils/popup_wysiwyg.php', 'wysiwyg', 600, 600, 'scrollbars=yes', 'true','f_texte', 'eve_add_form');" description="HTML editor"><img src="/backoffice/cms/img/bt_popup_wysiwyg.gif" id="wysiwyg" style="cursor: pointer; border: 1px solid red;" alt="HTML editor" onMouseOver="this.style.background='red';" onMouseOut="this.style.background=''" /></a>*/
if(is_file($_SERVER['DOCUMENT_ROOT']."/backoffice/cms/utils/popup_wysiwyg.php") && is_file($_SERVER['DOCUMENT_ROOT']."/custom/lib/FCKeditor/fckconfig.js")){
	$bPopupWysiwyg = true;
}

//popup link
if(is_file($_SERVER['DOCUMENT_ROOT']."/backoffice/cms/utils/popup/dir.php")){
	$bPopupLinks = true;
}

// gestion calendrier jscalendar
if(is_dir($_SERVER['DOCUMENT_ROOT']."/backoffice/cms/lib/jscalendar")){
	$bJScalendar = true;
	?>
<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="/backoffice/cms/lib/jscalendar/calendar-win2k-cold-1.css" description="win2k-cold-1" />
<!-- main calendar program -->
<script type="text/javascript" src="/backoffice/cms/lib/jscalendar/calendar.js"></script>
<!-- language for the calendar -->
<script type="text/javascript" src="/backoffice/cms/lib/jscalendar/lang/calendar-fr.js"></script>
<!-- the following script defines the Calendar.setup helper function -->
<script type="text/javascript" src="/backoffice/cms/lib/jscalendar/calendar-setup.js"></script>

<?php
}
else{
	$bJScalendar = false;
}
//---------------------------------------------------------------

?>
<script>
	// retour à la page précédente
	// ce retour doit être posté pour conserver le type
	function retour()
	{
		document.add_<?php echo $classePrefixe; ?>_form.action = "<?php echo $_POST['urlRetour']; ?><?php if($listParam!="") echo "&".$listParam;?>"; 
		document.add_<?php echo $classePrefixe; ?>_form.submit(); 
	}
</script>
<form name="add_<?php echo $classePrefixe; ?>_form" id="add_<?php echo $classePrefixe; ?>_form" enctype="multipart/form-data" method="post">
<input type="hidden" name="classeName" id="classeName"  value="<?php echo $classeName; ?>">
<input type="hidden" name="postnumberone" value="XXXX">
<input type="hidden" name="postnumber2" value="XXXX2">

<?php
//////////////////////////////////
// A VOIR
//////////////////////////////////
// le premier champ posté est "supprimé" quand il y a un caractère spécial dans la description
// (en l'occurence un ’)
// pourtant dans l'auprepend tous les champs postés sont correctement nettoyés
// j'ai mis ici deux champ postnumberone et postnumber2
// ils ne servent à rien sinon à tester que le premier champ est systématiquement supprimé 
// quand il y a un caractère spécial dans la description
//////////////////////////////////
// A VOIR
/////////////////////////////////

$status = '';
if($actiontodo == "SAUVE") { // MODE ENREGISTREMENT

	
	//-------------- upload --------------------------------------------------
	if ($numUploadFields > 0){
	
		// Pour ne pas écraser un fichier existant
		$Upload->  WriteMode  = '1';		
		
		 // Définition du répertoire de destination
		$Upload-> DirUpload    = $_SERVER['DOCUMENT_ROOT'].'/tmp/';
		dirExists("/tmp/");
			
		// controle l'existence du dir tmp
		if (!is_dir($Upload-> DirUpload)){ // on le crée
			mkdir($Upload-> DirUpload);
		}
		
		// Pour limiter la taille d'un  fichier (exprimée en ko)
		$Upload-> MaxFilesize  = strval(12*1024);
		
		//pre_dump($Upload);
	
		// On lance la procédure d'upload
		$Upload-> Execute();    
		
		$DIR_UPLOAD = $_SERVER['DOCUMENT_ROOT'].'/custom/upload/';
		
		if (!is_dir($DIR_UPLOAD)){ // on le crée
			mkdir($DIR_UPLOAD);
		}
		
		$DIR_MEDIA = $_SERVER['DOCUMENT_ROOT'].'/custom/upload/'.$classeName.'/';
		
		// controle l'existence du dir dest
		if (!is_dir($DIR_MEDIA)){ // on le crée
			mkdir($DIR_MEDIA);
		}
		$URL_MEDIA = '/custom/upload/'.$classeName.'/';
		
		if (count($Upload-> Infos) >= 1)  {	
			$aUploadIndexes[] = 0;
			while ($currInfos = current($Upload-> Infos)) {
				$aUploadIndexes[] = key($Upload-> Infos);
				next($Upload-> Infos);
			}
				
			for ($i = 1;$i <= count($Upload-> Infos);$i++){
				if (!copy($Upload->Infos[$aUploadIndexes[$i]]['chemin'], $DIR_MEDIA.$Upload-> Infos[$aUploadIndexes[$i]]['nom'])) {
				   //echo "failed to copy ".$Upload->Infos[$aUploadIndexes[$i]]['chemin']."...\n";
				}
				else{
					//echo "copied ".$Upload-> Infos[$aUploadIndexes[$i]]['chemin']."...\n";
					unlink($Upload-> Infos[$aUploadIndexes[$i]]['chemin']);
					// var à mettre à jour
					$_POST[strval($_POST[strval('fUpload'.$aUploadIndexes[$i])])] = $Upload->Infos[$aUploadIndexes[$i]]['nom'];
				}
			}// for
				
		}// if
		
	} // fin if ($numUploadFields > 0){
	
	//------------- fin upload --------------------------------------------------
	
	// ------------ delete fichiers cochés --------------------------------------	
	for ($iDel = 1;$iDel <= $numUploadFields;$iDel++){	
		if (strval($_POST['fDeleteFile'.$iDel]) == "true"){		
			$_POST[strval($_POST['fUpload'.$iDel])] = "";
			
			$tempGetter = "$"."tempFile = $"."oRes->get_".preg_replace("/.+".$classePrefixe."_(.*)/msi", "$1", strval($_POST['fUpload'.$iDel]))."();";
			
			$tempGetter = str_replace("get_".$classePrefixe."_", "get", $tempGetter);
			eval($tempGetter);
			$tempFile = $DIR_MEDIA.$tempFile;
			@unlink($tempFile);
		}
	}
	//----------------------------------------------------------------------------
	
	// Récupération des infos saisies dans l'objet
	if($operation == "UPDATE"){
		//$oRes->set_id($id);
	}
	else{
	
			$bRetour = dbInsertWithAutoKey($oRes);
			$id = $bRetour;
		
	}
	
	global $oRes;
	// post des champs normaux
	for ($i=0;$i<count($aNodeToSort);$i++){
		if ($aNodeToSort[$i]["name"] == "ITEM"){	
			if ($aNodeToSort[$i]["attrs"]["TYPE"] == "int"){ // cas des int, ne pas inscrire de value vide dans la base
				if ($_POST["f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]] == ""){
					$_POST["f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]] = -1;
				}
			}
			if ($aNodeToSort[$i]["attrs"]["OPTION"] == "password"){ // cas password, on cryte en md5
				if (is_post("f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"])){
					//setItemValue($oRes, $aNodeToSort[$i]["attrs"]["NAME"], md5($_POST["f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]]));
					setItemValue($oRes, $aNodeToSort[$i]["attrs"]["NAME"], password_hash($_POST["f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]], PASSWORD_DEFAULT));
				}
			}
			elseif ($aNodeToSort[$i]["attrs"]["OPTION"] == "url"){ // cas url, on ajoute le protocole http:// si manque
				if (isset($_POST["f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]])){
					$tempUrl = trim($_POST["f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]]);
					if (!preg_match("/^http|ftp]:\/\/.*/msi", $tempUrl) && ($tempUrl != "")){
						$tempUrl = "http://".$tempUrl;
					}					
					setItemValue($oRes, $aNodeToSort[$i]["attrs"]["NAME"], $tempUrl);
				}
			}
			else {
				setItemValue($oRes, $aNodeToSort[$i]["attrs"]["NAME"], $_POST["f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]]);
			}
			
		}
	}
	$oRes->set_id($id);
	

	
	
	// post des checkboxes d'asso	
	for ($i=0;$i<count($aNodeToSort);$i++){
		if ($aNodeToSort[$i]["name"] == "ITEM"){
			if ($aNodeToSort[$i]["attrs"]["ASSO"]){ // cas d'asso
								
				$sAssoClasse = $aNodeToSort[$i]["attrs"]["ASSO"];
		
				eval("$"."oAsso = new ".$sAssoClasse."();");
				$aForeign = dbGetObjects($sAssoClasse);
				
				// cas des deroulant d'id, poindescriptione vers foreign
				//$sXML = $aForeign[0]->XML;
				$sXML = $oAsso->XML;
				unset($stack);
				$stack = array();
				xmlStringParse($sXML);
	
				$foreignName = $stack[0]["attrs"]["NAME"];
				$foreignPrefixe = $stack[0]["attrs"]["PREFIX"];
				$foreignNodeToSort = $stack[0]["children"];
				
				$tempIsAbstractForeign = false;
				$tempForeignAbstract = "";
				$tempIsDisplayForeign = false;
				$tempForeignDisplay = "";
				$tempAsso = $stack[0]["attrs"]["NAME"];
				$tempAssoPrefixe = $stack[0]["attrs"]["PREFIX"];
				$tempAssoIn = "";
				$tempAssoOut = "";
				if(is_array($foreignNodeToSort)){
					foreach ($foreignNodeToSort as $nodeId => $nodeValue) {		
						if ($nodeValue["attrs"]["NAME"] == $oAsso->getAbstract()){					
							if (($nodeValue["attrs"]["FKEY"] != "") && isset($nodeValue["attrs"]["FKEY"])){
								$tempIsAbstractForeign = true;
								$tempForeignAbstract = $nodeValue["attrs"]["FKEY"];
								//break;
							}
						}
						if ($nodeValue["attrs"]["NAME"] == $oAsso->getDisplay()){					
							if (($nodeValue["attrs"]["FKEY"] != "") && isset($nodeValue["attrs"]["FKEY"])){
								$tempIsDisplayForeign = true;
								$tempForeignDisplay = $nodeValue["attrs"]["FKEY"];
								//break;
							}
						}
						if (($nodeValue["attrs"]["FKEY"] != "") && isset($nodeValue["attrs"]["FKEY"])){
							if ($nodeValue["attrs"]["FKEY"] == $classeName){	
								$tempAssoIn = $nodeValue["attrs"]["FKEY"]; // obvious
							}
							else{
								$tempAssoOut = $nodeValue["attrs"]["FKEY"]; // 
							}
						}
					}
				}
	
				if ($tempAssoOut != ""){
					// -- DEBUT traiment asso par table d'asso -------------------
					
		
					// on connait $tempAssoOut -- on recommence la recherche de foreign vers $tempAssoOut
					
					$sTempClasse = $tempAssoOut;
			
					eval("$"."oAssoOut = new ".$tempAssoOut."();");
					$aForeign = dbGetObjects($tempAssoOut);
					
					// cas des deroulant d'id, poindescriptione vers foreign
					//$sXML = $aForeign[0]->XML;
					//unset($stack);
					//$stack = array();
					//xmlStringParse($sXML);
		
					//$foreignName = $stack[0]["attrs"]["NAME"];
					//$foreignPrefixe = $stack[0]["attrs"]["PREFIX"];
					//$foreignNodeToSort = $stack[0]["children"];
					
					for ($iForeign=0;$iForeign<count($aForeign);$iForeign++){
						$oForeign = $aForeign[$iForeign];
						if ($oAssoOut->getGetterStatut() != "none"){
							eval ("$"."tempStatus = $"."oForeign->".strval($oAssoOut->getGetterStatut())."();");					
						}
						else{
							$tempStatus = DEF_ID_STATUT_LIGNE;
						}
						eval ("$"."tempId = $"."oForeign->get_id();");
						
						if ($tempStatus == DEF_ID_STATUT_LIGNE){
							// check sur post de fAsso".ucfirst($tempAssoIn)."_".ucfirst($tempAssoOut) et sa valeur (le assoOut)
							if (isset($_POST["fAsso".ucfirst($tempAssoIn)."_".ucfirst($tempAssoOut)."_".$tempId])){
								if ($_POST["fAsso".ucfirst($tempAssoIn)."_".ucfirst($tempAssoOut)."_".$tempId] == $tempId){
									//echo "fAsso".ucfirst($tempAssoIn)."_".ucfirst($tempAssoOut)."_".$tempId." = checked";
									
									if (getCount_where($tempAsso, array($tempAssoPrefixe."_".$tempAssoIn, $tempAssoPrefixe."_".$tempAssoOut), array($id,$tempId), array("NUMBER", "NUMBER")) ==  1){
										//echo " deja checked";
										//echo " - on ne fait rien";
									}
									else{
										//echo " pas deja checked";
										//echo " - on cree l'asso ".$tempAsso." ".$id." - ".$tempId;
										eval("$"."oNewAsso = new ".$tempAsso."();");
										
										eval("$"."oNewAsso->set_".$tempAssoIn."(".$id.");");
										eval("$"."oNewAsso->set_".$tempAssoOut."(".$tempId.");");
										
										//pre_dump($oNewAsso);
										$bAssoRetour = dbInsertWithAutoKey($oNewAsso);
									}
								}
								//echo "<br />";
							}
							else{
								//echo "fAsso".ucfirst($tempAssoIn)."_".ucfirst($tempAssoOut)."_".$tempId." = not checked";
	
								if (getCount_where($tempAsso, array($tempAssoPrefixe."_".$tempAssoIn, $tempAssoPrefixe."_".$tempAssoOut), array($id,$tempId), array("NUMBER", "NUMBER")) ==  1){
										//echo ", was checked";
										//echo " - on del l'asso ".$tempAsso." ".$id." - ".$tempId;
										$resTrashAsso = getSearchFields($tempAsso, array($tempAssoPrefixe."_id", $tempAssoPrefixe."_".$tempAssoIn, $tempAssoPrefixe."_".$tempAssoOut), array($tempAssoPrefixe."_".$tempAssoIn, $tempAssoPrefixe."_".$tempAssoOut), array($id,$tempId), array("NUMBER", "NUMBER"));
										//echo " - id to del : ".$resTrashAsso[0][0];
										eval("$"."oNewAsso = new ".$tempAsso."(".$resTrashAsso[0][0].");");
										$bAssoRetour = dbDelete($oNewAsso);
									}
									else{
										//echo ", deja pas checked";
										//echo " - on ne fait rien";
								}
								//echo "<br />";
							}
						}						
					}	
					// -- FIN traiment asso par table d'asso -------------------	
				}
				else{
					// -- DEBUT traiment asso sans table d'asso -------------------	
					if ($tempAsso != ""){ // check les records pointant vers la table sont plus que ZERO
	
						$sTempClasse = $tempAsso;
	
						eval("$"."oAssoOut = new ".$tempAsso."();");
						$aForeign = dbGetObjects($tempAsso);
	
						
						for ($iForeign=0;$iForeign<count($aForeign);$iForeign++){
							$oForeign = $aForeign[$iForeign];
							if ($oAssoOut->getGetterStatut() != "none"){
								eval ("$"."tempStatus = $"."oForeign->".strval($oAssoOut->getGetterStatut())."();");					
							}
							else{
								$tempStatus = DEF_ID_STATUT_LIGNE;
							}
							eval ("$"."tempId = $"."oForeign->get_id();");
							
							if ($tempStatus == DEF_ID_STATUT_LIGNE){
								// check sur post de fAsso".ucfirst($tempAssoIn)."_".ucfirst($tempAssoOut) et sa valeur (le assoOut)
								if (isset($_POST["fAsso".ucfirst($tempAssoIn)."_".$tempId])){
									if ($_POST["fAsso".ucfirst($tempAssoIn)."_".$tempId] == $tempId){
										//echo "fAsso".ucfirst($tempAssoIn)."_".$tempId." = checked";
										
										if (getCount_where($tempAsso, array($tempAssoPrefixe."_".$tempAssoIn, $tempAssoPrefixe."_id"), array($id,$tempId), array("NUMBER","NUMBER")) ==  1){
											//echo " deja checked";
											//echo " - on ne fait rien";
										}
										else{
											//echo " pas deja checked";
											//echo " - on set l'asso ".$tempAsso." ".$tempId." a : ".$id;
											eval("$"."oNewAsso = new ".$tempAsso."(".$tempId.");");
											
											eval("$"."oNewAsso->set_".$tempAssoIn."(".$id.");");
											//eval("$"."oNewAsso->set_".$tempAssoOut."(".$tempId.");");
											
											//pre_dump($oNewAsso);
											$bAssoRetour = dbUpdate($oNewAsso);
										}
									}
									//echo "<br />";
								}
								else{
									//echo "fAsso".ucfirst($tempAssoIn)."_".$tempId." = not checked";
		
									if (getCount_where($tempAsso, array($tempAssoPrefixe."_".$tempAssoIn, $tempAssoPrefixe."_id"), array($id,$tempId), array("NUMBER","NUMBER")) ==  1){
											//echo ", was checked";
											//echo " - on set l'asso ".$tempAsso." ".$tempId." a : -1";
											
											eval("$"."oNewAsso = new ".$tempAsso."(".$tempId.");");
											
											eval("$"."oNewAsso->set_".$tempAssoIn."(-1);");
											//eval("$"."oNewAsso->set_".$tempAssoOut."(".$tempId.");");
											
											//pre_dump($oNewAsso);
											$bAssoRetour = dbUpdate($oNewAsso);
										}
										else{
											//echo ", deja pas checked";
											//echo " - on ne fait rien";
									}
									//echo "<br />";
								}
							}						
						}
					} // fin if ($tempAsso != ""){ // check les records pointant vers la table sont plus que ZERO
					// -- FIN traiment asso sans table d'asso -------------------	
				}				
			}
		}
	}
	
	// fin ------- post des checkboxes d'asso

	// maj BDD
	if ($operation == "UPDATE") {
					
		// modif
		$bRetour = dbUpdate($oRes);
		//echo "udpate";
			
	} else if ($operation == "INSERT") {
		$bRetour = true;
		// recherche si un enr avec même FIELD existe déjà
		// $bDeja_present = (getCount2($oRes, "Tyres_FIELD", $oRes->getTyres_FIELD(), "TEXT")>0);
		// recherche du FIELD identique 
		
		// cas 1 : email
		$bMailDejaPresent=false;
		for ($i=0;$i<count($aNodeToSort);$i++){
			if ($aNodeToSort[$i]["attrs"]["OPTION"]){
				if ($aNodeToSort[$i]["attrs"]["OPTION"]=="email"){ // si option
					// si unique=true ou non défini
					if (($aNodeToSort[$i]["attrs"]["UNIQUE"]=="true")||(!isset($aNodeToSort[$i]["attrs"]["UNIQUE"]))){ 					
						eval("$"."eKeyValue = "."$"."oRes->get_".$aNodeToSort[$i]["attrs"]["NAME"]."();");
						if (getCount($classeName, $classePrefixe."_".$aNodeToSort[$i]["attrs"]["NAME"], $classePrefixe."_".$aNodeToSort[$i]["attrs"]["NAME"], $eKeyValue, "TEXT") > 0 ) {
							$bMailDejaPresent=true;
							$status.="\nUn enregistrement avec cet email est déjà présent.<br /><br />";
						}
						if ($eKeyValue=="") {
							$bMailDejaPresent=true;
							$status.="\nUn enregistrement avec cet email est déjà présent.<br /><br />";
						}
					}
				}
			}
		}
		
		// cas 2 : generiqe, unique=true
		$bDeja_present = false;
		for ($i=0;$i<count($aNodeToSort);$i++){
			if ($aNodeToSort[$i]["attrs"]["UNIQUE"]){		
				// si unique=true
				if ($aNodeToSort[$i]["attrs"]["UNIQUE"]=="true"){ 				
					eval("$"."eKeyValue = "."$"."oRes->get_".$aNodeToSort[$i]["attrs"]["NAME"]."();");
					if (getCount($classeName, $classePrefixe."_".$aNodeToSort[$i]["attrs"]["NAME"], $classePrefixe."_".$aNodeToSort[$i]["attrs"]["NAME"], $eKeyValue, "TEXT") > 0 ) {
						$bDeja_present=true;
						$status.="\nUn enregistrement avec cette valeur de '".$aNodeToSort[$i]["attrs"]["NAME"]."' est déjà présent.<br /><br />";
					}
					if ($eKeyValue=="") {
						$bDeja_present=true;
						$status.="\nUn enregistrement avec cette valeur de '".$aNodeToSort[$i]["attrs"]["NAME"]."' est déjà présent.<br /><br />";
					}
				}				
			}
		}		
		
		if($bDeja_present!=false || $bMailDejaPresent!=false) {
			$bRetour = false;
			dbDelete($oRes);
		}
		else { // tout est ok => on enregistre
			// on envoie id dans retour pour la redir vers show
			$bRetour = dbUpdate($oRes);
			$bRetour = $id;
		}
	}

	if($bRetour) {
		// type de record enregistré
	}
	else
		$status.= $classeName.' - Erreur lors de '. ( ($operation == "INSERT") ? "l'ajout" : "la modification" );

?>
<div class="arbo"><u><b><?php if($status!="") echo $status; ?></b></u></div><br>
	<table border="0" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo"><?php
	if($bRetour) {
	
		// Récapitulatif de la saisie
		$id = $bRetour;
		eval("$"."oRes = new ".$classeName."($"."bRetour);");
		$qryref = dbGetArrayOneFieldFromRequete($_SESSION['sqlpag']);
		for ($i=0;$i<count($qryref);$i++) {
			if ($qryref[$i]==$id) {
				$_SESSION['pag']=$i+1;
			}
		}
		if ($classeName == "cms_tableau") {
			$redirString = "/backoffice/cms/cms_tableau/show_".$classeName.".php?id=".$id."&adodb_next_page=".$_SESSION['pag'];
		}
		else if ($classeName == "cms_description") {
			$redirString = "/backoffice/cms/cms_tableau/maj2_".$classeName.".php?id=".$_GET["id"]."&adodb_next_page=".$_SESSION['pag'];
		}
		else {
			$redirString = "show_".$classeName.".php?id=".$id."&adodb_next_page=".$_SESSION['pag'];
		}
		if($listParam!=""){
			$redirString .= "&".$listParam;
		}
		if (preg_match("/id=[1-9]+/msi", $redirString) && preg_match("/id=\-1/msi", $redirString)){ // 2 fois id= , on garde la value > 0
			$redirString = preg_replace("/([?&]{1})id=-1/", "$1", $redirString);		
		}	
?>
<script language="javascript" type="text/javascript">
	window.location.href="<?php echo $redirString; ?>";
</script>
 <?php if ($aCustom["Sendmail"] == true)  { ?>
			<tr><td colspan="2" class="arbo"><?php include ("send_".$classeName.".php"); ?>&nbsp;</td></tr> 
  <?php } //if ($aCustom["Sendmail"] == true) 
	} // Fin si pas d'erreur d'ajout
/*
echo '<tr><td align="center" colspan="2" bgcolor="#D2D2D2" class="arbo">';

if ($operation == "INSERT") // Pour faciliter la saisie à la chaine
	echo '<input class="arbo" type="button" value="Créer une nouvelle '.$classeName.' >>" onclick="document.location=\''.$_SERVER['PHP_SELF'].'\'">';

else // On retourne à la liste 
	echo "<script>document.location='". $_POST['urlRetour'] ."'</script>";

// si une page retour est spécifiée -> lien retour
if ($_POST['urlRetour'] != "") {
	echo '<input class="arbo" type="button" value="<< Retour à la liste" onclick="retour()">';
}
*/
?>
</table>
<?php
}
else { // MODE EDITION

// Pour ajouter des attributs aux champs de type file
$Upload-> FieldOptions = 'style="border-color:black;border-width:1px;"';

// Pour indiquer le nombre de champs désiré
$Upload-> Fields = $numUploadFields;

// Initialisation du formulaire
$Upload-> InitForm();
?>
<span class="arbo2">MODULE >&nbsp;</span><span class="arbo3"><?php echo  $classeLibelle ; ?>&nbsp;>&nbsp;
<?php echo  ($operation == "INSERT") ? "Ajouter" : "Modifier" ?></span><br><br>
<script src="/backoffice/cms/js/validForm.js" type="text/javascript"></script>
<script>

	///////////////////////////////////////////////////
	// contrôle la validité du formulaire
	///////////////////////////////////////////////////
	
	function ifFormValid()
	{
		/*erreur=0;
		sMessage = "Valeur(s) incorrecte(s) : \n\n";
		
		if (document.getElementById('f<?php echo ucfirst($classePrefixe); ?>_groupe')) {
			if (document.add_<?php echo $classePrefixe; ?>_form.f<?php echo ucfirst($classePrefixe); ?>_groupe.value == -1) {
				sMessage+="- Le groupe doit être renseigné\n";
				erreur++;
			}
		}*/
		/*

// TITRE /////////
		if (document.add_<?php echo $classePrefixe; ?>_form.f<?php echo ucfirst($classePrefixe); ?>_titre.value == "") {
			sMessage+="- Le titre doit être renseigné\n";
			erreur++;
		}

// REF /////////
		if (document.add_<?php echo $classePrefixe; ?>_form.f<?php echo ucfirst($classePrefixe); ?>_src.value == "") {
			if (document.add_<?php echo $classePrefixe; ?>_form["userfile[]"].value == "") {
				sMessage+="- Le fichier doit être renseigné\n";
				erreur++;
			}
		}

// DATE /////////
		if (document.add_<?php echo $classePrefixe; ?>_form.f<?php echo ucfirst($classePrefixe); ?>_datec.value == "") {
			sMessage+="- La date de création doit être renseignée\n";
			erreur++;
		}
		if (document.add_<?php echo $classePrefixe; ?>_form.f<?php echo ucfirst($classePrefixe); ?>_datem.value == "") {
			sMessage+="- La date de modification doit être renseignée\n";
			erreur++;
		}
// TYPE de record /////////

		bType_<?php echo $classeName; ?> = false;
		<?php
		for ($i=0; $i<newSizeOf($aTyres); $i++)
		{
			$oTyres = $aTyres[$i];
		?>
			type_<?php echo $classeName; ?>_checked = document.add_<?php echo $classePrefixe; ?>_form.f<?php echo ucfirst($classePrefixe); ?>_Tyres_id[<?php echo $i; ?>].checked;
			if (type_<?php echo $classeName; ?>_checked == true) bType_<?php echo $classeName; ?> = true;
		<?php
		}
		?>


// DESC /////////
		if (document.add_<?php echo $classePrefixe; ?>_form.f<?php echo ucfirst($classePrefixe); ?>_desc.value == "") {
			sMessage+="- La description doit être renseignée\n";
			erreur++;
		}
*/


		// affichage des messages d'erreur
		/*if (erreur > 0) {
			alert(sMessage);
			return false;
		}*/
		//else 
		return true;
	}


	///////////////////////////////////////////////////
	// validation du formulaire
	///////////////////////////////////////////////////
	
	function validerForm()
	{
		// si le formulaire est valide
		if (ifFormValid()) {

			if (validate_form(0) && validerPattern() && validerChampsOblig()) 
			{ 
				//document.add_<?php echo $classePrefixe; ?>_form.operation.value = "ETAPE2"; 
				document.add_<?php echo $classePrefixe; ?>_form.action = "maj2_<?php echo $classeName; ?>.php<?php if($listParam!="") echo "?".$listParam;?><?php if(isset($_GET["id"])&& $_GET["id"]!="") echo "&id=".$_GET["id"];?>"; 
				document.add_<?php echo $classePrefixe; ?>_form.target = "_self";
				document.add_<?php echo $classePrefixe; ?>_form.submit(); 
			}
		}
	}
	
	///////////////////////////////////////////////////
	// validation du mot de passe
	///////////////////////////////////////////////////
	function checkPwd(id){
		saisie = document.getElementById(id).value;
		confirmation = document.getElementById(id+"conf").value;
		if (saisie != confirmation){
			alert("Les valeurs saisies pour le mot de passe et sa confirmation ne correspondent pas.\nVeuillez corriger votre saisie");
		}
	}

</script>
<!-- MODE EDITION -->

<input type="hidden" name="operation" value="<?php echo $operation; ?>">
<input type="hidden" name="urlRetour" value="<?php echo $_POST['urlRetour']; ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="actiontodo" value="ETAPE2">
<input type="hidden" name="sChamp" value="">

<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
<?php
$indexUpload = 0;
// Affichage du champ MAX_FILE_SIZE
print $Upload-> Field[$indexUpload];


//champs obligatoires

echo "<script language=\"javascript\" type=\"text/javascript\">\n";
	echo "function validerChampsOblig() {\n";
	echo "erreur=0;\n";
	echo "lib=\"\";\n"; 

for ($i=0;$i<count($aNodeToSort);$i++){
	if ($aNodeToSort[$i]["name"] == "ITEM"){	
		if (isset ($aNodeToSort[$i]["attrs"]["OBLIG"]) &&  $aNodeToSort[$i]["attrs"]["OBLIG"]!=""){ // cas des int, ne pas inscrire de value vide dans la base
			
			echo "if(document.getElementById(\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\").value== \"\" || document.getElementById(\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\").value==-1){\n";
			echo " erreur++;\n";
			if (isset ($aNodeToSort[$i]["attrs"]["LIBELLE"]) && $aNodeToSort[$i]["attrs"]["LIBELLE"] != "") $nomChamp = $aNodeToSort[$i]["attrs"]["LIBELLE"];
			else  $nomChamp = $aNodeToSort[$i]["attrs"]["NAME"];
			echo " lib+=\" - ".$nomChamp." \\n\";\n";
			echo "}\n";
		}	
		
	}
}
echo "if (erreur == 0) {\n";
echo "  return true; \n";
echo "}\n";
echo "else{\n";
echo "alert(\"Les champs suivants sont obligatoires : \\n\"+lib);";		
echo "	return false;\n";
echo "}\n";
echo "}";
echo "</script>\n";	






// tableau contenant les champs et pattern à vérifier
$ControlePattern = false;
$ControlePatternFields = array();
$ControlePatternValues = array();

for ($i=0;$i<count($aNodeToSort);$i++){
	if ($aNodeToSort[$i]["name"] == "ITEM"){
		$eKeyValue = getItemValue($oRes, $aNodeToSort[$i]["attrs"]["NAME"]);	
		echo "<tr>\n";
		echo "<td width=\"141\" align=\"right\" bgcolor=\"#E6E6E6\" class=\"arbo\">";
		if (isset($aNodeToSort[$i]["attrs"]["LIBELLE"]) && ($aNodeToSort[$i]["attrs"]["LIBELLE"] != "")){
			echo "&nbsp;<u><b>".stripslashes($aNodeToSort[$i]["attrs"]["LIBELLE"])."</b></u>";
		}
		else{
			echo "&nbsp;<u><b>".stripslashes($aNodeToSort[$i]["attrs"]["NAME"])."</b></u>";
		}
		if (isset ($aNodeToSort[$i]["attrs"]["OBLIG"]) &&  $aNodeToSort[$i]["attrs"]["OBLIG"]!="") echo "&nbsp;*";
		echo "</td>\n";
		echo "<td width=\"535\" align=\"left\" bgcolor=\"#EEEEEE\" class=\"arbo\">";
		
		// tableau contenant les champs et pattern à vérifier
		if (isset($aNodeToSort[$i]["attrs"]["PATTERN"])){
			$ControlePattern = true;
			$ControlePatternFields[] = $aNodeToSort[$i]["attrs"]["NAME"];
			$ControlePatternValues[] = $aNodeToSort[$i]["attrs"]["PATTERN"];
		}
		
		// test de conditionnement - esclave
		$ActiveIf = false;
		if (isset($aNodeToSort[$i]["children"]) && (count($aNodeToSort[$i]["children"]) > 0)){
			foreach ($aNodeToSort[$i]["children"] as $childKey => $childNode){
				if($childNode["name"] == "OPTION"){ // on a un node d'option				
					if ($childNode["attrs"]["TYPE"] == "if"){
						$ActiveIf = true;
						$whereField = $childNode["attrs"]["ITEM"];
						$whereValue = $childNode["attrs"]["VALUE"];
						break;
					} //fin type  == if			
				}
			}
		}
		// fin test condion
		
		
		// tst de condition - maitre
		$ControlIf = false;
		$ControledFields = array();
		$ControlValues = array();
		for ($ii=0;$ii<count($aNodeToSort);$ii++){
			if ($aNodeToSort[$ii]["name"] == "ITEM"){
				if (isset($aNodeToSort[$ii]["children"]) && (count($aNodeToSort[$ii]["children"]) > 0)){
					foreach ($aNodeToSort[$ii]["children"] as $childKey => $childNode){
						if($childNode["name"] == "OPTION"){ // on a un node d'option				
							if ($childNode["attrs"]["TYPE"] == "if"){ // test maitre = conditonner du item selected
								if ($childNode["attrs"]["ITEM"] == $aNodeToSort[$i]["attrs"]["NAME"]){
									$ControlIf = true;
									$ControledFields[] = $aNodeToSort[$ii]["attrs"]["NAME"];
									$ControlValues[] = $childNode["attrs"]["VALUE"];							
								}
							} //fin type  == if			
						}
					}
				}
			}
		}	
		// fin tst de condition - maitre
		
		if ($aNodeToSort[$i]["attrs"]["FKEY"]){ // cas de foregin key
			$sTempClasse = $aNodeToSort[$i]["attrs"]["FKEY"];
			
			eval("$"."oTemp = new ".$sTempClasse."();");
			$aForeign = dbGetObjects($sTempClasse);
			$aValue = array();
			$default ="";
			// test de condtion where
			$DoWhere = false;
			
			// tst de condition - type WHERE
			if (isset($aNodeToSort[$i]["children"]) && (count($aNodeToSort[$i]["children"]) > 0)){
				foreach ($aNodeToSort[$i]["children"] as $childKey => $childNode){
					if($childNode["name"] == "OPTION"){ // on a un node d'option				
						if ($childNode["attrs"]["TYPE"] == "where"){
							$DoWhere = true;
							$whereField = $childNode["attrs"]["ITEM"];
							if (isset ($childNode["attrs"]["OPTION"]) && $childNode["attrs"]["OPTION"] != "") {
								// test sur value passée par session 
								if ($childNode["attrs"]["OPTION"] == "session")  {
									$whereValue= $_SESSION[$childNode["attrs"]["VALUE"]];
									if (isset($childNode["attrs"]["ASSO"]) && $childNode["attrs"]["ASSO"]!= "")  {
										$whereClasse = $childNode["attrs"]["ASSO"];
										$def = $childNode["attrs"]["DEFAULT"];
										//echo $whereValue."-".DEF_ID_ADMIN_DEFAUT."".$whereField." ".$whereClasse;
										$aWhere = dbGetObjects($whereClasse);
										//var_dump($aWhere);
										for($a=0;$a<newSizeOf($aWhere);$a++){
											$oWhere = $aWhere[$a];
											// test sur valeur par defaut
											if (isset($childNode["attrs"]["DEFAULT"]) && $childNode["attrs"]["DEFAULT"]!= "" && DEF_ID_ADMIN_DEFAUT == $whereValue) {
												//echo "toto";
												$default = $childNode["attrs"]["DEFAULT"];
												$aValue[] = $oWhere->get_id();	
											} 
											
											else {
												eval("$"."currentWhereFieldValue = $"."oWhere->get_".$whereField."();");
												if ($currentWhereFieldValue == $whereValue){
													//$aValue[] = $oWhere->get_id();	
													eval("$"."aValue[] = $"."oWhere->get_".$aNodeToSort[$i]["attrs"]["NAME"]."();");	
															
												}
											}
										}
										$whereField = $childNode["attrs"]["FKEY"];
									}
									else {
										$aValue[] =$whereValue;
									}
								}							
							}
							else {
								$aValue[] = $childNode["attrs"]["VALUE"];
							}// fin value par session
							break;
						} 		
					}
				}
			}
			// fin test WHERE
			
			// debut traitement WHERE
			
			if ($DoWhere == true){
				//var_dump($aValue);
				$fieldInURL =  $classeName."_".$sTempClasse;
				$aForeignNew = array();
				for($ii=0;$ii<count($aForeign);$ii++){
					$oForeign = $aForeign[$ii];
					if (!isset($_GET[$fieldInURL]) || $_GET[$fieldInURL]=="") {
						// valeur par défaut
						if ($default !="") {
							$aForeignNew[] = $aForeign[$ii];	
						} 
						else { 
							for($b=0;$b<newSizeOf($aValue);$b++) {
								eval("$"."currentWhereFieldValue = $"."oForeign->get_".$whereField."();");
								if ($currentWhereFieldValue == $aValue[$b]){
									$aForeignNew[] = $aForeign[$ii];					
								}
							}
						}
					}
					else {
						$chaine_result = strstr($listParam, $fieldInURL);
						$chaine_result = strstr($chaine_result, "comparateur");
						$tableau = explode("&", $chaine_result);
						$tableau = explode("=", $tableau[0]);
						eval("$"."currentWhereFieldValue = $"."oForeign->get_id();");
						if ($_GET[$tableau[0]] == "=") {
							if ($currentWhereFieldValue == $_GET[$fieldInURL]){
								$aForeignNew[] = $aForeign[$ii];					
							}
						}else if ($_GET[$tableau[0]] == "<>"){
							if ($currentWhereFieldValue != $_GET[$fieldInURL]){
								$aForeignNew[] = $aForeign[$ii];					
							}
						}
					}
				}
				$aForeign = $aForeignNew;
			}
			// fin traitement where
			
			// cas des deroulant d'id, poindescriptione vers foreign
			//$sXML = $aForeign[0]->XML;
			$sXML = $oTemp->XML;

			unset($stack);
			$stack = array();
			xmlStringParse($sXML);

			$foreignName = $stack[0]["attrs"]["NAME"];
			$foreignPrefixe = $stack[0]["attrs"]["PREFIX"];
			$foreignNodeToSort = $stack[0]["children"];
			
			$tempIsAbstractForeign = false;
			$tempForeignAbstract = "";
			$tempIsDisplayForeign = false;
			$tempForeignDisplay = "";
			$bCms_site = false;
			
			if(is_array($foreignNodeToSort)){
				
				foreach ($foreignNodeToSort as $nodeId => $nodeValue) {			
					
					if ($nodeValue["attrs"]["NAME"] == $oTemp->getAbstract()){		
							
						if (($nodeValue["attrs"]["FKEY"] != "") && isset($nodeValue["attrs"]["FKEY"])){
							$tempIsAbstractForeign = true;
							$tempForeignAbstract = $nodeValue["attrs"]["FKEY"];
							//break;
						}
					}
					if ($nodeValue["attrs"]["NAME"] == $oTemp->getDisplay()){					
						if (($nodeValue["attrs"]["FKEY"] != "") && isset($nodeValue["attrs"]["FKEY"])){
							$tempIsDisplayForeign = true;
							$tempForeignDisplay = $nodeValue["attrs"]["FKEY"];
							//break;
						}
					}
					if (strtolower(stripslashes($nodeValue["attrs"]["NAME"])) == "cms_site"){
						$bCms_site = true;
					}
				}
			}
			
			$disabled = "";
			
			if (($aNodeToSort[$i]["attrs"]["NAME"] == $displayField) && isset($display) && ($display!="")){
				$disabled = "disabled";
				echo "<input type=\"hidden\" name=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" value=\"".$eKeyValue."\" />\n";
				
				for ($iForeign=0;$iForeign<count($aForeign);$iForeign++){
					
					$oForeign = $aForeign[$iForeign];
					if ($oTemp->getGetterStatut() != "none"){
						eval ("$"."tempStatus = $"."oForeign->".strval($oTemp->getGetterStatut())."();");					
					}
					else{
						$tempStatus = DEF_ID_STATUT_LIGNE;
					}
					if ($aNodeToSort[$i]["attrs"]["NAME"] == "candidature")
						$tempStatus = DEF_ID_STATUT_LIGNE;
					//eval ("$"."tempStatus = $"."oForeign->".strval($oTemp->getGetterStatut())."();");
					eval ("$"."tempId = $"."oForeign->get_id();");
					if ($tempStatus == DEF_ID_STATUT_LIGNE){		
								
						if ($eKeyValue == $tempId){
							if ($tempIsDisplayForeign){
								eval("$"."oForeignDisplay = new ".$tempForeignDisplay."($"."oForeign->get_".strval($oTemp->getDisplay())."());");
								eval ("$"."itemValue = $"."oForeignDisplay->get_".strval($oForeignDisplay->getDisplay())."();");
								$itemValueShort = substr($itemValue, 0, 50);
								if (strlen($itemValue) > 50 ) 
									$itemValueShort.= " ... ";
								echo $itemValueShort;	
							}
							else{
								eval ("$"."itemValue = $"."oForeign->get_".strval($oTemp->getDisplay())."();");
								$itemValueShort = substr($itemValue, 0, 50);
								if (strlen($itemValue) > 50 ) 
									$itemValueShort.= " ... ";
								echo $itemValueShort;	
							}
							
							if ($oTemp->getDisplay() != $oTemp->getAbstract()){
								echo " - ";
								if ($tempIsAbstractForeign){
									eval("$"."oForeignAbstract = new ".$tempForeignAbstract."($"."oForeign->get_".strval($oTemp->getAbstract())."());");
									eval ("$"."itemValue = $"."oForeignAbstract->get_".strval($oForeignAbstract->getDisplay())."();");
									$itemValueShort = substr($itemValue, 0, 50);
									if (strlen($itemValue) > 50 ) 
										$itemValueShort.= " ... ";
									echo $itemValueShort;	
								}
								else{
									eval ("$"."itemValue = $"."oForeign->get_".strval($oTemp->getAbstract())."();");
									$itemValueShort = substr($itemValue, 0, 50);
									if (strlen($itemValue) > 50 ) 
										$itemValueShort.= " ... ";
									echo $itemValueShort;	
								}
							} 
						}					
					}	 // fin if statut					
				}// fin for
					
			}
			else{
				if ( $aNodeToSort[$i]["attrs"]["FKEY"] == "cms_site" && $classeName !="classe") {
					eval("$"."oForeignDisplay = new ".$aNodeToSort[$i]["attrs"]["FKEY"]."(".$_SESSION['idSite_travail'].");");
					eval ("echo $"."oForeignDisplay->get_".strval($oForeignDisplay->getDisplay())."();");
					echo " - ";
					eval ("echo $"."oForeignDisplay->get_".strval($oForeignDisplay->getAbstract())."();");
					echo "<input type=\"hidden\" name=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" value=\"".$_SESSION['idSite_travail']."\" >\n";
				
					
				} 
				else {
					
					echo "<select name=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" class=\"arbo\" ".$disabled.">\n";
					echo "<option value=\"-1\">Choisir un item</option>\n";
					for ($iForeign=0;$iForeign<count($aForeign);$iForeign++){
						$oForeign = $aForeign[$iForeign];
						//if ($iForeign==0) var_dump($oForeign);
						if ($oTemp->getGetterStatut() != "none"){
							eval ("$"."tempStatus = $"."oForeign->".strval($oTemp->getGetterStatut())."();");					
						}
						else{
							$tempStatus = DEF_ID_STATUT_LIGNE;
						}
						//eval ("$"."tempStatus = $"."oForeign->".strval($oTemp->getGetterStatut())."();");
						eval ("$"."tempId = $"."oForeign->get_id();");
						
						if ($tempStatus == DEF_ID_STATUT_LIGNE){
							if ($bCms_site == true && $classeName != "classe" ) {
								echo "je rentre".$oForeign->get_cms_site(). " ".$_SESSION['idSite_travail']. " ".$idSite;
								$temp_cms_site = $oForeign->get_cms_site();
								if ((isset($_SESSION['idSite_travail']) && $_SESSION['idSite_travail']!= "" &&  preg_match("/backoffice/msi", $_SERVER['PHP_SELF']) && $temp_cms_site == $_SESSION['idSite_travail']) || ($temp_cms_site == $idSite)) {
										echo "<option value=\"".$tempId."\"";
										if ($eKeyValue == $tempId){
											echo " selected";
										}						
										echo ">";
										//eval ("echo substr($"."oForeign->get_".strval($oTemp->getDisplay())."(), 0, 100);");
										if ($tempIsDisplayForeign){
											eval("$"."oForeignDisplay = new ".$tempForeignDisplay."($"."oForeign->get_".strval($oTemp->getDisplay())."());");
											eval (" $"."itemValue = $"."oForeignDisplay->get_".strval($oForeignDisplay->getDisplay())."();");
											$itemValueShort = substr($itemValue, 0, 50);
											if (strlen($itemValue) > 50 ) 
												$itemValueShort.= " ... ";
											echo $itemValueShort;	
										}
										else{
											eval (" $"."itemValue = $"."oForeign->get_".strval($oTemp->getDisplay())."();");
											$itemValueShort = substr($itemValue, 0, 50);
											if (strlen($itemValue) > 50 ) 
												$itemValueShort.= " ... ";
											echo $itemValueShort;	
										}
										
										if ($oTemp->getDisplay() != $oTemp->getAbstract()){
											echo " - ";
											if ($tempIsAbstractForeign){
												eval("$"."oForeignAbstract = new ".$tempForeignAbstract."($"."oForeign->get_".strval($oTemp->getAbstract())."());");
												eval (" $"."itemValue = $"."oForeignAbstract->get_".strval($oForeignAbstract->getDisplay())."();");
												$itemValueShort = substr($itemValue, 0, 50);
												if (strlen($itemValue) > 50 ) 
													$itemValueShort.= " ... ";
												echo $itemValueShort;	
											}
											else{
												eval (" $"."itemValue = $"."oForeign->get_".strval($oTemp->getAbstract())."();");
												$itemValueShort = substr($itemValue, 0, 50);
												if (strlen($itemValue) > 50 ) 
													$itemValueShort.= " ... ";
												echo $itemValueShort;	
											}
										} 
										echo "</option>\n";
								}
							}
							else {
									echo "<option value=\"".$tempId."\"";
										if ($eKeyValue == $tempId){
											echo " selected";
										}						
										echo ">";
										//eval ("echo substr($"."oForeign->get_".strval($oTemp->getDisplay())."(), 0, 100);");
										if ($tempIsDisplayForeign){
											eval("$"."oForeignDisplay = new ".$tempForeignDisplay."($"."oForeign->get_".strval($oTemp->getDisplay())."());");
											eval (" $"."itemValue = $"."oForeignDisplay->get_".strval($oForeignDisplay->getDisplay())."();");
											$itemValueShort = substr($itemValue, 0, 50);
											if (strlen($itemValue) > 50 ) 
												$itemValueShort.= " ... ";
											echo $itemValueShort;	
										}
										else{
											eval (" $"."itemValue = $"."oForeign->get_".strval($oTemp->getDisplay())."();");
											$itemValueShort = substr($itemValue, 0, 50);
											if (strlen($itemValue) > 50 ) 
												$itemValueShort.= " ... ";
											echo $itemValueShort;	
										}
										
										if ($oTemp->getDisplay() != $oTemp->getAbstract()){
											echo " - ";
											if ($tempIsAbstractForeign){
												eval("$"."oForeignAbstract = new ".$tempForeignAbstract."($"."oForeign->get_".strval($oTemp->getAbstract())."());");
												eval (" $"."itemValue = $"."oForeignAbstract->get_".strval($oForeignAbstract->getDisplay())."();");
												$itemValueShort = substr($itemValue, 0, 50);
												if (strlen($itemValue) > 50 ) 
													$itemValueShort.= " ... ";
												echo $itemValueShort;	
											}
											else{
												eval (" $"."itemValue = $"."oForeign->get_".strval($oTemp->getAbstract())."();");
												$itemValueShort = substr($itemValue, 0, 50);
												if (strlen($itemValue) > 50 ) 
													$itemValueShort.= " ... ";
												echo $itemValueShort;	
											}
										} 
										echo "</option>\n";
							
							}
						}	 // fin if statut					
					}// fin for
					echo "</select>\n";
				}
			}

		}// fin fkey
		elseif ($aNodeToSort[$i]["attrs"]["OPTION"] == "enum"){ // cas enum
			echo "<select id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" name=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" class=\"arbo\" >\n";
			echo "<option value=\"-1\">Choisir un cas</option>\n";		
			if (isset($aNodeToSort[$i]["children"]) && (count($aNodeToSort[$i]["children"]) > 0)){
				foreach ($aNodeToSort[$i]["children"] as $childKey => $childNode){
					if($childNode["name"] == "OPTION"){ // on a un node d'option				
						if ($childNode["attrs"]["TYPE"] == "value"){
							if (intval($eKeyValue) == intval($childNode["attrs"]["VALUE"])){							
								$enumSelected = "selected";
							}
							else {
								$enumSelected = "";
							}
							echo "<option value=\"".$childNode["attrs"]["VALUE"]."\" ".$enumSelected.">".stripslashes($childNode["attrs"]["LIBELLE"])."</option>\n";
						} //fin type  == value				
					}
				}
			}
			echo "</select>\n";		
		} // fin cas enum
		else{ // cas typique
			if ($aNodeToSort[$i]["attrs"]["NAME"] == "statut"){ // cas statut
				//echo lib($eKeyValue);
				echo "voir boutons radios";
			}
			else{
				if ($eKeyValue > -1){ // cas typique typique
					if ($aNodeToSort[$i]["attrs"]["OPTION"] == "file"){ // cas file
						echo "<input type=\"hidden\" id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" name=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" value=\"".$eKeyValue."\" />\n";
						$indexUpload++;
						echo "<div id=\"div".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\">\n";
						echo "<!-- upload field # ".$indexUpload."/".$numUploadFields." -->\n";
						echo "<input type=\"hidden\" id=\"fUpload".$indexUpload."\" name=\"fUpload".$indexUpload."\" value=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" />\n";
						
						// Affichage du champ de type FILE						
						print $Upload-> Field[$indexUpload];
						if ($eKeyValue != ""){
							echo "&nbsp;(actuellement <a href=\"/backoffice/cms/utils/viewer.php?file=/custom/upload/".$classeName."/".$eKeyValue."\" target=\"_blank\" description=\"Visualiser le fichier : '".$eKeyValue."'\">".$eKeyValue."</a>)<br />";
							echo "<input type=\"checkbox\" id=\"fDeleteFile".$indexUpload."\" name=\"fDeleteFile".$indexUpload."\" value=\"true\" />&nbsp;supprimer le fichier \n";
						}	
						else{
							echo "&nbsp;(pas de fichier)<br />";
						}
						if (isset($aNodeToSort[$i]["children"]) && (count($aNodeToSort[$i]["children"]) > 0)){
							foreach ($aNodeToSort[$i]["children"] as $childKey => $childNode){
								if($childNode["name"] == "OPTION"){ // on a un node d'option	
									echo "<br />\n";
									if (($childNode["attrs"]["TYPE"] != "") && ($childNode["attrs"]["TYPE"] != "if")){
										echo "Type de fichier&nbsp;: ".$childNode["attrs"]["TYPE"]."<br />\n";
									}
									if ($childNode["attrs"]["WIDTH"] != ""){
										echo "Largeur max. de l'image&nbsp;: ".$childNode["attrs"]["WIDTH"]." pixels<br />\n";
									}
									if ($childNode["attrs"]["HEIGHT"] != ""){
										echo "Hauteur max. de l'image&nbsp;: ".$childNode["attrs"]["HEIGHT"]." pixels<br />\n";
									}						
								}
							}
						}
						echo "</div>\n";
					}
					elseif ($aNodeToSort[$i]["attrs"]["OPTION"] == "textarea"){ // cas textarea	
						$maxlength="";
						if 	(isset($aNodeToSort[$i]["attrs"]["MAXLENGTH"]) && $aNodeToSort[$i]["attrs"]["MAXLENGTH"]!="") {
							$maxlength="onkeyup=\"this.value = this.value.slice(0, ".$aNodeToSort[$i]["attrs"]["MAXLENGTH"].")\" onchange=\"this.value = this.value.slice(0, ".$aNodeToSort[$i]["attrs"]["MAXLENGTH"].")\"";
						}
						echo "<textarea name=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" class=\"arbo\" cols=\"50\" rows=\"6\" ".$maxlength.">".$eKeyValue."</textarea>\n";
						// gestion popup wysiwyg
						if ((($aNodeToSort[$i]["attrs"]["TYPE"] == "varchar") || ($aNodeToSort[$i]["attrs"]["TYPE"] == "text")) && ($bPopupWysiwyg == true) && (($aNodeToSort[$i]["attrs"]["NOHTML"] != "true")||(!isset($aNodeToSort[$i]["attrs"]["NOHTML"])))){ // cas wysiwyg
							echo "<a href=\"javascript:openWYSYWYGWindow('http://".$_SERVER['HTTP_HOST']."/backoffice/cms/utils/popup_wysiwyg.php', 'wysiwyg', 600, 600, 'scrollbars=yes', 'true','f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."', 'add_".$classePrefixe."_form');\" description=\"HTML editor\"><img src=\"/backoffice/cms/img/bt_popup_wysiwyg.gif\" id=\"wysiwyg\" style=\"cursor: pointer; border: 1px solid red;\" alt=\"HTML editor\" onMouseOver=\"this.style.background='red';\" onMouseOut=\"this.style.background=''\" /></a>\n";
						} // wysiwyg
					}
					elseif ($aNodeToSort[$i]["attrs"]["OPTION"] == "link"){ // cas link						
						echo "<input type=\"text\" name=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" class=\"arbo\" size=\"80\" value=\"".str_replace('"', '&quot;', $eKeyValue)."\" ".$disabled." />\n";
						// gestion popup link
						if ((($aNodeToSort[$i]["attrs"]["TYPE"] == "varchar") || ($aNodeToSort[$i]["attrs"]["TYPE"] == "text")) && ($bPopupLinks == true)){ // cas link						
							echo "<a href=\"javascript:openLinkWindow('/backoffice/cms/utils/popup/dir.php', 'links', 600, 600, 'scrollbars=yes', 'true','f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."', 'add_".$classePrefixe."_form');\" description=\"Link picker\"><img src=\"/backoffice/cms/img/bt_popup_wysiwyg.gif\" id=\"wysiwyg\" style=\"cursor: pointer; border: 1px solid red;\" alt=\"Link picker\" onMouseOver=\"this.style.background='red';\" onMouseOut=\"this.style.background=''\" /></a>\n";
						} // link
					}
					elseif ($aNodeToSort[$i]["attrs"]["OPTION"] == "filedir"){ // cas filedir						
					
						echo "<input type=\"text\" name=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" class=\"arbo\" size=\"80\" value=\"".str_replace('"', '&quot;', $eKeyValue)."\" ".$disabled." />\n";
						// gestion popup link
						if (($aNodeToSort[$i]["attrs"]["TYPE"] == "varchar") || ($aNodeToSort[$i]["attrs"]["TYPE"] == "text")){ // cas filedir						
							echo "<a href=\"javascript:openLinkWindow('/backoffice/cms/utils/popup/dir.php', 'links', 600, 600, 'scrollbars=yes', 'true','f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."', 'add_".$classePrefixe."_form');\" description=\"Link picker\"><img src=\"/backoffice/cms/img/bt_popup_wysiwyg.gif\" id=\"wysiwyg\" style=\"cursor: pointer; border: 1px solid red;\" alt=\"Link picker\" onMouseOver=\"this.style.background='red';\" onMouseOut=\"this.style.background=''\" /></a>\n";
						} // link
					}
					elseif ($aNodeToSort[$i]["attrs"]["OPTION"] == "password"){ // cas password	
						if ($eKeyValue=="") {				
							
						}
						else {
							echo "réinitialiser le mot de passe<br />(laisser les champs vide pour conserver le mot de passe actuel)<br />";
						}
						echo "<input type=\"password\" name=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" class=\"arbo\" size=\"20\" value=\"\" ".$disabled." /><br />\n";
						echo "<input type=\"password\" name=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."conf\" id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."conf\" class=\"arbo\" size=\"20\" value=\"\" ".$disabled." onblur=\"checkPwd('f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."')\" /> (confirmer le mot de passe)\n";
						
					}
					elseif ($aNodeToSort[$i]["attrs"]["OPTION"] == "module"){ // cas password	
						if (isset($aNodeToSort[$i]["attrs"]["MODULE"]) && $aNodeToSort[$i]["attrs"]["MODULE"]!="")  {
							echo "<a href=\"".$aNodeToSort[$i]["attrs"]["MODULE"]."_".$classeName.".php?id=".$_GET["id"]."\" description=\"".$aNodeToSort[$i]["attrs"]["MODULE"]."\">Accéder au module de modification de : ".$aNodeToSort[$i]["attrs"]["LIBELLE"]."</a>\n";
						}
						
						
					}
					else{// cas typique typique typique
						if ($aNodeToSort[$i]["attrs"]["OPTION"] != "bool"){ // pas boolean
							if ($aNodeToSort[$i]["attrs"]["NAME"] == "id"){
								echo $eKeyValue;
								
								echo "<input type=\"hidden\" name=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" class=\"arbo\" size=\"80\" value=\"".$eKeyValue."\" ".$disabled." />\n";
							}
							elseif ($aNodeToSort[$i]["attrs"]["TYPE"] == "timestamp"){ // cas timestamp
								
								if ($id == -1) 
									echo "<input type=\"text\" name=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" class=\"arbo\" size=\"80\" value=\"".$eKeyValue."\" ".$disabled." />\n";
								else 
									echo "<input type=\"text\" name=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" class=\"arbo\" size=\"80\" value=\"".timestampFormat($eKeyValue)."\" ".$disabled." />\n";
							}
						
							else{
								echo "<input type=\"text\" name=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" class=\"arbo\" size=\"80\" value=\"".str_replace('"', '&quot;', $eKeyValue)."\" ".$disabled." />\n";
							}
							
							
							if (($aNodeToSort[$i]["attrs"]["TYPE"] == "date") && ($bJScalendar == true)){ // cas date
								echo "<img src=\"/backoffice/cms/lib/jscalendar/img.gif\" id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."_trigger\" style=\"cursor: pointer; border: 1px solid red;\" description=\"Date selector\" onmouseover=\"this.style.background='red';\" onmouseout=\"this.style.background=''\" />\n";
								?>
								<script type="text/javascript">
								Calendar.setup({
									inputField     :    "<?php echo "f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]; ?>",  // id of the input field
									ifFormat       :    "%d/%m/%Y",      // format of the input field
									button         :    "<?php echo "f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."_trigger"; ?>", // trigger ID
									align          :    "Tl",           // alignment (defaults to "Bl")
									singleClick    :    true
								});
								</script>
								<?php
							} // JScalendar
							else if (($aNodeToSort[$i]["attrs"]["TYPE"] == "timestamp") && ($bJScalendar == true)){ // cas date
								echo "<img src=\"/backoffice/cms/lib/jscalendar/img.gif\" id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."_trigger\" style=\"cursor: pointer; border: 1px solid red;\" description=\"Date selector\" onmouseover=\"this.style.background='red';\" onmouseout=\"this.style.background=''\" />\n";
								?>
								<script type="text/javascript">
								Calendar.setup({
									inputField     :    "<?php echo "f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]; ?>",  // id of the input field
									ifFormat       :    "%d/%m/%Y %H:%M:%S",      // format of the input field
									button         :    "<?php echo "f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."_trigger"; ?>", // trigger ID
									align          :    "Tl",           // alignment (defaults to "Bl")
									singleClick    :    true
								});
								</script>
								<?php
							} // JScalendar
							// gestion popup wysiwyg
							elseif ((($aNodeToSort[$i]["attrs"]["TYPE"] == "varchar") || ($aNodeToSort[$i]["attrs"]["TYPE"] == "text")) && ($bPopupWysiwyg == true) && (($aNodeToSort[$i]["attrs"]["NOHTML"] != "true")||(!isset($aNodeToSort[$i]["attrs"]["NOHTML"])))){ // cas wysiwyg
							//elseif ($bPopupWysiwyg == true){ // cas wysiwyg
								echo "<a href=\"javascript:openWYSYWYGWindow('http://".$_SERVER['HTTP_HOST']."/backoffice/cms/utils/popup_wysiwyg.php', 'wysiwyg', 600, 600, 'scrollbars=yes', 'true','f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."', 'add_".$classePrefixe."_form');\" description=\"HTML editor\"><img src=\"/backoffice/cms/img/bt_popup_wysiwyg.gif\" id=\"wysiwyg\" style=\"cursor: pointer; border: 1px solid red;\" alt=\"HTML editor\" onMouseOver=\"this.style.background='red';\" onMouseOut=\"this.style.background=''\" /></a>\n";
							} // wysiwyg
							
						}
						else{ // option="bool"
							echo "<select name=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" class=\"arbo\">\n";
							if (intval($eKeyValue) == 0){
								echo "<option value=\"0\" selected>non</option>\n";
								echo "<option value=\"1\">oui</option>\n";
							}
							else{
								echo "<option value=\"0\">non</option>\n";
								echo "<option value=\"1\" selected>oui</option>\n";
							}							
							echo "</select>\n";
						
						}
					}
				}
				else{
					// cas not set
					if ($aNodeToSort[$i]["attrs"]["NAME"] == "id"){ // cas id = -1
						//echo lib($eKeyValue);
						echo "id généré automatiquement";
					}
					else{ // cas autre texte not set
						if ($aNodeToSort[$i]["attrs"]["OPTION"] == "file"){ // cas file
							echo "<input type=\"hidden\" id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" name=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" value=\"".$eKeyValue."\" />\n";
							$indexUpload++;
							echo "<div id=\"div".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\">\n";
							echo "<!-- upload field # ".$indexUpload."/".$numUploadFields." -->\n";
							echo "<input type=\"hidden\" id=\"fUpload".$indexUpload."\" name=\"fUpload".$indexUpload."\" value=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" />\n";
							
							// Affichage du champ de type FILE						
							print $Upload-> Field[$indexUpload];								
							echo "&nbsp;(pas de fichier)<br />";
							
							if (isset($aNodeToSort[$i]["children"]) && (count($aNodeToSort[$i]["children"]) > 0)){
								foreach ($aNodeToSort[$i]["children"] as $childKey => $childNode){
									if($childNode["name"] == "OPTION"){ // on a un node d'option	
										echo "<br />\n";
										if (($childNode["attrs"]["TYPE"] != "") && ($childNode["attrs"]["TYPE"] != "if")){
											echo "Type de fichier&nbsp;: ".$childNode["attrs"]["TYPE"]."<br />\n";
										}
										if ($childNode["attrs"]["WIDTH"] != ""){
											echo "Largeur max. de l'image&nbsp;: ".$childNode["attrs"]["WIDTH"]." pixels<br />\n";
										}
										if ($childNode["attrs"]["HEIGHT"] != ""){
											echo "Hauteur max. de l'image&nbsp;: ".$childNode["attrs"]["HEIGHT"]." pixels<br />\n";
										}						
									}
								}
							}
							echo "</div>\n";
						}
						elseif ($aNodeToSort[$i]["attrs"]["OPTION"] != "bool"){ // cas pas bool
							if ($aNodeToSort[$i]["attrs"]["OPTION"] == "textarea"){ // cas textarea						
								echo "<textarea name=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" class=\"arbo\" cols=\"50\" rows=\"6\" >".$eKeyValue."</textarea>\n";
								// gestion popup wysiwyg
								if ((($aNodeToSort[$i]["attrs"]["TYPE"] == "varchar") || ($aNodeToSort[$i]["attrs"]["TYPE"] == "text")) && ($bPopupWysiwyg == true) && (($aNodeToSort[$i]["attrs"]["NOHTML"] != "true")||(!isset($aNodeToSort[$i]["attrs"]["NOHTML"])))){ // cas wysiwyg
								//elseif ($bPopupWysiwyg == true){ // cas wysiwyg
									echo "<a href=\"javascript:openWYSYWYGWindow('http://".$_SERVER['HTTP_HOST']."/backoffice/cms/utils/popup_wysiwyg.php', 'wysiwyg', 600, 600, 'scrollbars=yes', 'true','f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."', 'add_".$classePrefixe."_form');\" description=\"HTML editor\"><img src=\"/backoffice/cms/img/bt_popup_wysiwyg.gif\" id=\"wysiwyg\" style=\"cursor: pointer; border: 1px solid red;\" alt=\"HTML editor\" onMouseOver=\"this.style.background='red';\" onMouseOut=\"this.style.background=''\" /></a>\n";
								} // wysiwyg
						
							}
							else{ // pas textarea
								echo "<input type=\"text\" name=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" class=\"arbo\" size=\"80\" value=\"\" />\n";
							}
						}
						else{ // option="bool"
							echo "<select name=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" id=\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\" class=\"arbo\">\n";

							echo "<option value=\"0\" selected>non</option>\n";
							echo "<option value=\"1\">oui</option>\n";
				
							echo "</select>\n";
						
						}
					}
				}
			}
		}			
		
		// retour test de conditionnement - esclave
		if ($ActiveIf == true){
		
			
			$test2 = array();
			for ($ii=0;$ii<count($aNodeToSort);$ii++){
				if ($aNodeToSort[$ii]["name"] == "ITEM"){ 
					if ($aNodeToSort[$ii]["attrs"]["NAME"]==$aNodeToSort[$i]["attrs"]["NAME"]) {
						if (isset($aNodeToSort[$ii]["children"]) && (count($aNodeToSort[$ii]["children"]) > 0)){ 
							foreach ($aNodeToSort[$ii]["children"] as $childKey => $childNode){
								if($childNode["name"] == "OPTION"){ // on a un node d'option	
									if ($childNode["attrs"]["TYPE"] == "if"){ // test maitre = conditonner du item selected
										//if ($childNode["attrs"]["ITEM"] == $aNodeToSort[$i]["attrs"]["NAME"] ){ 
											$test2[] = $childNode["attrs"]["VALUE"];							
										//}
									} //fin type  == if			
								}
							}
						}
					}
				}
			} 
			echo "<script language=\"javascript\" type=\"text/javascript\">\n";
			echo "// retour test de conditionnement - esclave\n";
			$k=0;
			if (newSizeOf($test2)>1) { 
				echo "// plusieurs \n";
				echo "if (";
				echo " document.getElementById(\"f".ucfirst($classePrefixe)."_".$whereField."\").value != \"".$test2[$k]."\" ";
					while ($k<newSizeOf($test2)) {
						$k++;
						echo "	&& document.getElementById(\"f".ucfirst($classePrefixe)."_".$whereField."\").value != \"".$test2[$k]."\"";
					}
				echo ")";
				echo "{\n";
				echo "document.getElementById(\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\").disabled = true;\n";
				if ($aNodeToSort[$i]["attrs"]["OPTION"] == "file"){ // cas file
					echo "document.getElementById(\"div".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\").style.display = \"none\";\n";
				}
				echo "}\n";
			
			}
			else {
				echo "// unique \n";
				echo "if(document.getElementById(\"f".ucfirst($classePrefixe)."_".$whereField."\").value != \"".$whereValue."\"){\n";
				echo "document.getElementById(\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\").disabled = true;\n";
				if ($aNodeToSort[$i]["attrs"]["OPTION"] == "file"){ // cas file
					echo "document.getElementById(\"div".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\").style.display = \"none\";\n";
				}
				echo "}\n";
				
			}
			echo "</script>\n";

		}
		
		
		//-------
		
		// retour tst de condition - maitre
		if ($ControlIf == true){
			echo "<script language=\"javascript\" type=\"text/javascript\">\n";
			echo "// retour tst de condition - maitre\n";
			echo "document.getElementById(\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\").onchange = function(){\n";
			for ($ii=0;$ii<count($ControledFields);$ii++){
				echo "	if(";
				echo "	 document.getElementById(\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\").value != \"".$ControlValues[$ii]."\"";
				while ($ControledFields[$ii] == $ControledFields[($ii+1)]) {
					$ii++;
					echo "	&& document.getElementById(\"f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."\").value != \"".$ControlValues[$ii]."\"";
				}
				
				echo " ){\n";
				echo "		document.getElementById(\"f".ucfirst($classePrefixe)."_".$ControledFields[$ii]."\").disabled = true;\n";
				
				$tempControledNode =getItemByName($aNodeToSort, $ControledFields[$ii]);
				//pre_dump($tempControledNode);
				if ($tempControledNode["attrs"]["OPTION"] == "file"){ // cas file
					echo "document.getElementById(\"div".ucfirst($classePrefixe)."_".$tempControledNode["attrs"]["NAME"]."\").style.display = \"none\";\n";
				}
				echo "	}\n";
				echo "	else{\n";
				echo "		document.getElementById(\"f".ucfirst($classePrefixe)."_".$ControledFields[$ii]."\").disabled = false;\n";

				if ($tempControledNode["attrs"]["OPTION"] == "file"){ // cas file
					echo "document.getElementById(\"div".ucfirst($classePrefixe)."_".$tempControledNode["attrs"]["NAME"]."\").style.display = \"inline\";\n";
				}
				echo "	}\n";
			}
			echo "}\n";
			echo "</script>\n";	
		}
		
		// appel javascript qui vérifie les pattern
		
		echo "<script language=\"javascript\" type=\"text/javascript\">\n";
		echo "function validerPattern() {\n";
		echo " var eCountPattern = 0;\n";
		echo " var sMessagePattern;\n";
		echo " sMessagePattern=\"\";\n";
		if ($ControlePattern == true){	
			for ($iii=0;$iii<count($ControlePatternValues);$iii++){
				echo "var regexp = new RegExp(\"^".$ControlePatternValues[$iii]."+$\");\n"; 
				echo "if(!regexp.exec(document.getElementById(\"f".ucfirst($classePrefixe)."_".$ControlePatternFields[$iii]."\").value)){\n";
				echo " eCountPattern++;\n";
				echo " sMessagePattern+= \"Le champs ".$ControlePatternFields[$iii]." ne doit contenir que  les expressions suivantes: ".$ControlePatternValues[$iii]."\\n\";\n";
				echo "}\n";
			}	
		}
		echo "if (eCountPattern == 0) {\n";
		echo "  return true; \n";
		echo "}\n";
		echo "else{\n";
		echo "alert(sMessagePattern);";		
		echo "	return false;\n";
		echo "}\n";
		echo "}";
		echo "</script>\n";	

		// fin  appel javascript qui vérifie les pattern
					
		//-----
		echo "</td>\n";
		echo "</tr>\n";
		echo "<!-- fin des champs de la classe -->\n\n";
		
	}
}

// recherche d'eventuelles asso
for ($i=0;$i<count($aNodeToSort);$i++){
	if ($aNodeToSort[$i]["name"] == "ITEM"){
		if ($aNodeToSort[$i]["attrs"]["ASSO"]){ // cas d'asso
			echo "<!-- debut des champs d'association -->\n";
			echo "<tr>\n";
			echo "<td width=\"141\" align=\"right\" bgcolor=\"#E6E6E6\" class=\"arbo\">";
			echo "<br /><b>Associations</b>";
			echo "</td>\n";
			echo "<td width=\"535\" align=\"left\" bgcolor=\"#EEEEEE\" class=\"arbo\">&nbsp;</td></tr>";
		}	
	}
}

for ($i=0;$i<count($aNodeToSort);$i++){
	if ($aNodeToSort[$i]["name"] == "ITEM"){
		if ($aNodeToSort[$i]["attrs"]["ASSO"]){ // cas d'asso
			
			$eKeyValue = getItemValue($oRes, $aNodeToSort[$i]["attrs"]["NAME"]);			

			$sTempClasse = $aNodeToSort[$i]["attrs"]["ASSO"];
			
			eval("$"."oTemp = new ".$sTempClasse."();");
			$aForeign = dbGetObjects($sTempClasse);
			
			 
			// cas des deroulant d'id, poindescriptione vers foreign
			//$sXML = $aForeign[0]->XML;
			$sXML = $oTemp->XML;

			unset($stack);
			$stack = array();
			xmlStringParse($sXML);

			$foreignName = $stack[0]["attrs"]["NAME"];
			$foreignPrefixe = $stack[0]["attrs"]["PREFIX"];
			$foreignNodeToSort = $stack[0]["children"];
			
			$tempIsAbstractForeign = false;
			$tempForeignAbstract = "";
			$tempIsDisplayForeign = false;
			$tempForeignDisplay = "";
			$tempAsso = $stack[0]["attrs"]["NAME"];
			$tempAssoPrefixe = $stack[0]["attrs"]["PREFIX"];
			$tempAssoIn = "";
			$tempAssoOut = ""; 
			if(is_array($foreignNodeToSort)){
				foreach ($foreignNodeToSort as $nodeId => $nodeValue) {				
					if ($nodeValue["attrs"]["NAME"] == $oTemp->getAbstract()){					
						if (($nodeValue["attrs"]["FKEY"] != "") && isset($nodeValue["attrs"]["FKEY"])){
							$tempIsAbstractForeign = true;
							$tempForeignAbstract = $nodeValue["attrs"]["FKEY"];
							//break;
						}
					}
					if ($nodeValue["attrs"]["NAME"] == $oTemp->getDisplay()){					
						if (($nodeValue["attrs"]["FKEY"] != "") && isset($nodeValue["attrs"]["FKEY"])){
							$tempIsDisplayForeign = true;
							$tempForeignDisplay = $nodeValue["attrs"]["FKEY"];
							//break;
						}
					}
					if (($nodeValue["attrs"]["FKEY"] != "") && isset($nodeValue["attrs"]["FKEY"])){
						if ($nodeValue["attrs"]["FKEY"] == $classeName){	
							$tempAssoIn = $nodeValue["attrs"]["FKEY"]; // obvious
						}
						else{
							$tempAssoOut = $nodeValue["attrs"]["FKEY"]; // 
						}
					}
				}
			}
			
			if ($tempAssoOut != ""){
				// debut traitement asso par table asso
	
				echo "<tr>\n";
				echo "<td width=\"141\" align=\"right\" bgcolor=\"#E6E6E6\" class=\"arbo\">";
				echo "&nbsp;<u><b>".$tempAssoOut."</b></u>&nbsp;*";
				echo "</td>\n";
				echo "<td width=\"535\" align=\"left\" bgcolor=\"#EEEEEE\" class=\"arbo\">\n";
				// on connait $tempAssoOut -- on recommence la recherche de foreign vers $tempAssoOut
				
				$sTempClasse = $tempAssoOut;
		
				eval("$"."oTemp = new ".$sTempClasse."();");
				$aForeign = dbGetObjects($sTempClasse);
			  	
				// cas des deroulant d'id, poindescriptione vers foreign
				//$sXML = $aForeign[0]->XML;
				$sXML = $oTemp->XML;

				unset($stack);
				$stack = array();
				xmlStringParse($sXML);
	
				$foreignName = $stack[0]["attrs"]["NAME"];
				$foreignPrefixe = $stack[0]["attrs"]["PREFIX"];
				$foreignNodeToSort = $stack[0]["children"];
				
			 
		
				$tempIsAbstractForeign = false;
				$tempForeignAbstract = "";
				$tempIsDisplayForeign = false;
				$tempForeignDisplay = "";
				$bCms_site = false;
				if(is_array($foreignNodeToSort)){
					foreach ($foreignNodeToSort as $nodeId => $nodeValue) {				
						if ($nodeValue["attrs"]["NAME"] == $oTemp->getAbstract()){					
							if (($nodeValue["attrs"]["FKEY"] != "") && isset($nodeValue["attrs"]["FKEY"])){
								$tempIsAbstractForeign = true;
								$tempForeignAbstract = $nodeValue["attrs"]["FKEY"];
								//break;
							}
						}
						if ($nodeValue["attrs"]["NAME"] == $oTemp->getDisplay()){					
							if (($nodeValue["attrs"]["FKEY"] != "") && isset($nodeValue["attrs"]["FKEY"])){
								$tempIsDisplayForeign = true;
								$tempForeignDisplay = $nodeValue["attrs"]["FKEY"];
								//break;
							}
						}
						if (strtolower(stripslashes($nodeValue["attrs"]["NAME"])) == "cms_site"){
							$bCms_site = true;
						}
					}
				}
				
				//pre_dump($aForeign);
				for ($iForeign=0;$iForeign<count($aForeign);$iForeign++){
					
					
					$oForeign = $aForeign[$iForeign];
					eval (" $"."itemValue = $"."oForeign->get_".strval($oTemp->getDisplay())."();");
 					
					// on vérifie les droits de la classe
					if ($foreignName == "classe" && allowClasse($itemValue))  {
						if ($oTemp->getGetterStatut() != "none"){
							eval ("$"."tempStatus = $"."oForeign->".strval($oTemp->getGetterStatut())."();");					
						}
						else{
							$tempStatus = DEF_ID_STATUT_LIGNE;
						}
						eval ("$"."tempId = $"."oForeign->get_id();");
						
						if ($tempStatus == DEF_ID_STATUT_LIGNE){
							if ($bCms_site == true && $classeName != "classe" ) {
								
								$temp_cms_site = $oForeign->get_cms_site();
								if ((isset($_SESSION['idSite_travail']) && $_SESSION['idSite_travail']!= "" &&  preg_match("/backoffice/msi", $_SERVER['PHP_SELF']) /* && $temp_cms_site == $_SESSION['idSite_travail'*/)/* || ($temp_cms_site == $idSite)*/) {
									// test sur select. chercher id de la fiche en cours ($id) et id du foreign en cours ($tempId) dans Asso.
									echo "<input type=\"checkbox\" name=\"fAsso".ucfirst($tempAssoIn)."_".ucfirst($tempAssoOut)."_".$tempId."\" id=\"fAsso".ucfirst($tempAssoIn)."_".ucfirst($tempAssoOut)."_".$tempId."\" value=\"".$tempId."\" ";
							
									if ($operation != "INSERT")  {
										// on ne fait le calcul qu'en cas de update
										if (getCount_where($tempAsso, array($tempAssoPrefixe."_".$tempAssoIn, $tempAssoPrefixe."_".$tempAssoOut), array($id,$tempId), array("NUMBER", "NUMBER")) > 0){
											echo " checked";
										}						
									}	
									echo " >";
						
									if ($tempIsDisplayForeign){
										eval("$"."oForeignDisplay = new ".$tempForeignDisplay."($"."oForeign->get_".strval($oTemp->getDisplay())."());");
										eval (" $"."itemValue = $"."oForeignDisplay->get_".strval($oForeignDisplay->getDisplay())."();");
										$itemValueShort = substr($itemValue, 0, 50);
										if (strlen($itemValue) > 50 ) 
											$itemValueShort.= " ... ";
											echo $itemValueShort;	
									}
									else{
										eval (" $"."itemValue = $"."oForeign->get_".strval($oTemp->getDisplay())."();");
										$itemValueShort = substr($itemValue, 0, 50);
												if (strlen($itemValue) > 50 ) 
													$itemValueShort.= " ... ";
												echo $itemValueShort;	
									}
							
									/*if ($oTemp->getDisplay() != $oTemp->getAbstract()){
										echo " - ";
										if ($tempIsAbstractForeign){
											eval("$"."oForeignAbstract = new ".$tempForeignAbstract."($"."oForeign->get_".strval($oTemp->getAbstract())."());");
											eval (" $"."itemValue = $"."oForeignAbstract->get_".strval($oForeignAbstract->getDisplay())."();");
											$itemValueShort = substr($itemValue, 0, 50);
												if (strlen($itemValue) > 50 ) 
													$itemValueShort.= " ... ";
												echo $itemValueShort;	
										}
										else{
											eval (" $"."itemValue = $"."oForeign->get_".strval($oTemp->getAbstract())."();");
											$itemValueShort = substr($itemValue, 0, 50);
												if (strlen($itemValue) > 50 ) 
													$itemValueShort.= " ... ";
												echo $itemValueShort;	
										}
									}*/
									echo "<br />\n";
								 }  //endif ((isset($_SESSION['idSite_travail'])
							} 
							else {
								// test sur select. chercher id de la fiche en cours ($id) et id du foreign en cours ($tempId) dans Asso.
							echo "<input type=\"checkbox\" name=\"fAsso".ucfirst($tempAssoIn)."_".ucfirst($tempAssoOut)."_".$tempId."\" id=\"fAsso".ucfirst($tempAssoIn)."_".ucfirst($tempAssoOut)."_".$tempId."\" value=\"".$tempId."\" ";
							
							if ($operation != "INSERT")  {
							// on ne fait le calcul qu'en cas de update
							if (getCount_where($tempAsso, array($tempAssoPrefixe."_".$tempAssoIn, $tempAssoPrefixe."_".$tempAssoOut), array($id,$tempId), array("NUMBER", "NUMBER")) ==  1){
								echo " checked";
							}						
							}	
							echo ">";
						
							if ($tempIsDisplayForeign){
								eval("$"."oForeignDisplay = new ".$tempForeignDisplay."($"."oForeign->get_".strval($oTemp->getDisplay())."());");
								eval (" $"."itemValue = $"."oForeignDisplay->get_".strval($oForeignDisplay->getDisplay())."();");
								$itemValueShort = substr($itemValue, 0, 50);
										if (strlen($itemValue) > 50 ) 
											$itemValueShort.= " ... ";
										echo $itemValueShort;	
							}
							else{
								eval (" $"."itemValue = $"."oForeign->get_".strval($oTemp->getDisplay())."();");
								$itemValueShort = substr($itemValue, 0, 50);
										if (strlen($itemValue) > 50 ) 
											$itemValueShort.= " ... ";
										echo $itemValueShort;	
							}
							
							if ($oTemp->getDisplay() != $oTemp->getAbstract()){
								echo " - ";
								if ($tempIsAbstractForeign){
									eval("$"."oForeignAbstract = new ".$tempForeignAbstract."($"."oForeign->get_".strval($oTemp->getAbstract())."());");
									eval (" $"."itemValue = $"."oForeignAbstract->get_".strval($oForeignAbstract->getDisplay())."();");
									$itemValueShort = substr($itemValue, 0, 50);
										if (strlen($itemValue) > 50 ) 
											$itemValueShort.= " ... ";
										echo $itemValueShort;	
								}
								else{
									eval (" $"."itemValue = $"."oForeign->get_".strval($oTemp->getAbstract())."();");
									$itemValueShort = substr($itemValue, 0, 50);
										if (strlen($itemValue) > 50 ) 
											$itemValueShort.= " ... ";
										echo $itemValueShort;	
								}
							}
								echo "<br />\n";
							}
						}	
					} //if ($foreignName == "classe" && allowClasse($itemValue))  {
										
				}	//for ($iForeign=0;$iForeign<count($aForeign);$iForeign++){
				// fin traitement par table asso ----------	
			}
			else{
				// debut traitement sans table asso ----------				
				if ($tempAsso != ""){ // check les records pointant vers la table sont plus que ZERO
					echo "<tr>\n";
					echo "<td width=\"141\" align=\"right\" bgcolor=\"#E6E6E6\" class=\"arbo\">";
					echo "&nbsp;<u><b>".$tempAsso."</b></u>&nbsp;*";
					echo "</td>\n";
					echo "<td width=\"535\" align=\"left\" bgcolor=\"#EEEEEE\" class=\"arbo\">\n";
					// on connait $tempAssoOut -- on recommence la recherche de foreign vers $tempAssoOut
					
					$sTempClasse = $tempAsso;

					eval("$"."oTemp = new ".$sTempClasse."();");
					$aForeign = dbGetObjects($sTempClasse);
					
					// cas des deroulant d'id, poindescriptione vers foreign
					//$sXML = $aForeign[0]->XML;
					$sXML = $oTemp->XML;					

					unset($stack);
					$stack = array();
					xmlStringParse($sXML);
		
					$foreignName = $stack[0]["attrs"]["NAME"];
					$foreignPrefixe = $stack[0]["attrs"]["PREFIX"];
					$foreignNodeToSort = $stack[0]["children"];
					
					$tempIsAbstractForeign = false;
					$tempForeignAbstract = "";
					$tempIsDisplayForeign = false;
					$tempForeignDisplay = "";
					if(is_array($foreignNodeToSort)){
						foreach ($foreignNodeToSort as $nodeId => $nodeValue) {				
							if ($nodeValue["attrs"]["NAME"] == $oTemp->getAbstract()){					
								if (($nodeValue["attrs"]["FKEY"] != "") && isset($nodeValue["attrs"]["FKEY"])){
									$tempIsAbstractForeign = true;
									$tempForeignAbstract = $nodeValue["attrs"]["FKEY"];
									//break;
								}
							}
							if ($nodeValue["attrs"]["NAME"] == $oTemp->getDisplay()){					
								if (($nodeValue["attrs"]["FKEY"] != "") && isset($nodeValue["attrs"]["FKEY"])){
									$tempIsDisplayForeign = true;
									$tempForeignDisplay = $nodeValue["attrs"]["FKEY"];
									//break;
								}
							}
						}
					}
		
					for ($iForeign=0;$iForeign<count($aForeign);$iForeign++){
						$oForeign = $aForeign[$iForeign];
						if ($oTemp->getGetterStatut() != "none"){
							eval ("$"."tempStatus = $"."oForeign->".strval($oTemp->getGetterStatut())."();");					
						}
						else{
							$tempStatus = DEF_ID_STATUT_LIGNE;
						}
						eval ("$"."tempId = $"."oForeign->get_id();");
						
						if ($tempStatus == DEF_ID_STATUT_LIGNE){
							// test sur select. chercher id de la fiche en cours ($id) et id du foreign en cours ($tempId) dans Asso.
			
							echo "<input type=\"checkbox\" name=\"fAsso".ucfirst($tempAssoIn)."_".$tempId."\" id=\"fAsso".ucfirst($tempAssoIn)."_".$tempId."\" value=\"".$tempId."\" ";
							
							if (getCount_where($tempAsso, array($tempAssoPrefixe."_".$tempAssoIn, $tempAssoPrefixe."_id"), array($id,$tempId), array("NUMBER","NUMBER")) ==  1){
								echo " checked";
							}						
							echo ">";
	
						
							if ($tempIsDisplayForeign){
								eval("$"."oForeignDisplay = new ".$tempForeignDisplay."($"."oForeign->get_".strval($oTemp->getDisplay())."());");
								eval ("$"."itemValue = $"."oForeignDisplay->get_".strval($oForeignDisplay->getDisplay())."();");
								$itemValueShort = substr($itemValue, 0, 50);
									if (strlen($itemValue) > 50 ) 
										$itemValueShort.= " ... ";
									echo $itemValueShort;	
							}
							else{
								eval ("$"."itemValue = $"."oForeign->get_".strval($oTemp->getDisplay())."();");
								$itemValueShort = substr($itemValue, 0, 50);
									if (strlen($itemValue) > 50 ) 
										$itemValueShort.= " ... ";
									echo $itemValueShort;	
							}
							
							if ($oTemp->getDisplay() != $oTemp->getAbstract()){
								echo " - ";
								if ($tempIsAbstractForeign){
									eval("$"."oForeignAbstract = new ".$tempForeignAbstract."($"."oForeign->get_".strval($oTemp->getAbstract())."());");
									eval ("$"."itemValue = $"."oForeignAbstract->get_".strval($oForeignAbstract->getDisplay())."();");
									$itemValueShort = substr($itemValue, 0, 50);
									if (strlen($itemValue) > 50 ) 
										$itemValueShort.= " ... ";
									echo $itemValueShort;	
								}
								else{
									eval ("$"."itemValue = $"."oForeign->get_".strval($oTemp->getAbstract())."();");
									$itemValueShort = substr($itemValue, 0, 50);
									if (strlen($itemValue) > 50 ) 
										$itemValueShort.= " ... ";
									echo $itemValueShort;	
								}
							}
							echo "<br />\n";
						}						
					}	
				} //fin if (intval($tempAsso) != 0)){ // check les records pointant vers la table sont plus que ZERO
				// fin traitement sans table asso ----------	
			}
		}
	}
}
?>
 <tr>
   <td bgcolor="#E6E6E6"  align="right" class="arbo">&nbsp;</td>
   <td bgcolor="#EEEEEE"  align="left" class="arbo">&nbsp;</td>
 </tr>
<?php
if ($oRes->getGetterStatut() != "none" ){
	
?>
 <tr>
  <td width="141" align="right" bgcolor="#E6E6E6" class="arbo">&nbsp;<u><b>Statut de publication</b></u>&nbsp;*</td>
  <td width="535" align="left" bgcolor="#EEEEEE" class="arbo">
  <?php
  	if (isAllowed ($rankUser, "ADMIN;GEST")) {
		if ($oRes->get_statut() == DEF_ID_STATUT_ATTEN) $checked_ATTEN = "checked"; else $checked_ATTEN = "";
		if ($oRes->get_statut() == DEF_ID_STATUT_LIGNE) $checked_LIGNE = "checked"; else $checked_LIGNE = "";
		if ($oRes->get_statut() == DEF_ID_STATUT_ARCHI) $checked_ARCHI = "checked"; else $checked_ARCHI = "";
		?>
		<input type="radio" name="f<?php echo ucfirst($classePrefixe); ?>_statut" id="f<?php echo ucfirst($classePrefixe); ?>_statut" value="<?php echo DEF_ID_STATUT_ATTEN; ?>" <?php echo $checked_ATTEN; ?>  />&nbsp;<?php echo lib(DEF_ID_STATUT_ATTEN); ?>&nbsp;
		<input type="radio" name="f<?php echo ucfirst($classePrefixe); ?>_statut" id="f<?php echo ucfirst($classePrefixe); ?>_statut" value="<?php echo DEF_ID_STATUT_LIGNE; ?>" <?php echo $checked_LIGNE; ?> />&nbsp;<?php echo lib(DEF_ID_STATUT_LIGNE); ?>&nbsp;
		<input type="radio" name="f<?php echo ucfirst($classePrefixe); ?>_statut" id="f<?php echo ucfirst($classePrefixe); ?>_statut" value="<?php echo DEF_ID_STATUT_ARCHI; ?>" <?php echo $checked_ARCHI; ?> />&nbsp;<?php echo lib(DEF_ID_STATUT_ARCHI); ?>&nbsp;
  		<?php }
	else {
		if ($oRes->get_statut() == DEF_ID_STATUT_ATTEN) {
			echo lib(DEF_ID_STATUT_ATTEN);
			?> <input type="hidden" name="f<?php echo ucfirst($classePrefixe); ?>_statut" id="f<?php echo ucfirst($classePrefixe); ?>_statut" value="<?php echo DEF_ID_STATUT_ATTEN; ?>" /><?php
		}
		if ($oRes->get_statut() == DEF_ID_STATUT_LIGNE) {
			echo lib(DEF_ID_STATUT_LIGNE);
			?><input type="hidden" name="f<?php echo ucfirst($classePrefixe); ?>_statut" id="f<?php echo ucfirst($classePrefixe); ?>_statut" value="<?php echo DEF_ID_STATUT_LIGNE; ?>"/><?php
		}
		if ($oRes->get_statut() == DEF_ID_STATUT_ARCHI) {
			echo lib(DEF_ID_STATUT_ARCHI);
		?>
		<input type="hidden" name="f<?php echo ucfirst($classePrefixe); ?>_statut" id="f<?php echo ucfirst($classePrefixe); ?>_statut" value="<?php echo DEF_ID_STATUT_ARCHI; ?>"/><?php
		}
		
	}
	?>
	</td>
 </tr>
<?php
	

}
?>
 <tr>
   <td bgcolor="#E6E6E6"  align="right" class="arbo">&nbsp;</td>
   <td bgcolor="#EEEEEE"  align="left" class="arbo">&nbsp;</td>
 </tr>
  <tr>
   <td bgcolor="#E6E6E6"  align="right" class="arbo">&nbsp;</td>
   <td bgcolor="#EEEEEE"  align="left" class="arbo">&nbsp;<small>(* champs obligatoires)</small></td>
 </tr>
 <tr>
   <td bgcolor="#E6E6E6"  align="right" class="arbo">&nbsp;</td>
   <td bgcolor="#EEEEEE"  align="left" class="arbo">&nbsp;</td>
 </tr>
 <tr>
  <td bgcolor="#D2D2D2" colspan="2"  align="center" class="arbo"><?php if ($_POST['urlRetour'] != "") { ?>
        <input name="button" type="button" class="arbo" onClick="javascript:history.back();" value="<< Retour (annuler)">     &nbsp;&nbsp;&nbsp;&nbsp; <?php } ?>
     <input class="arbo" type="button" name="Ajouter" value="Suite (enregistrer) >>" onClick="validerForm()"></td>
 </tr>
</table>
<br><br>
<?php

} // FIN MODE EDITION
?>
</form>