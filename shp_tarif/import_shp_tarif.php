<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

//include('cms-inc/autoClass/import.php');



// L'IMPORT DES TARIFS EST CUSTOMISE

if (!isset($classeName)){
	$classeName = preg_replace('/[^_]*_(.*)\.php/si', '$1', basename($_SERVER['PHP_SELF']));
}
//------------------------------------------------------------------------------------------------------

// permet de dérouler le menu contextuellement
if (strpos($_SERVER['PHP_SELF'], "backoffice") !== false){
	activateMenu("gestion".$classeName); 
}

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/lib.inc.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/htmlUtility.php'); 

// objet 
eval("$"."oRes = new ".$classeName."();");

$sXML = $oRes->XML;
xmlStringParse($sXML);

$classeName = $stack[0]["attrs"]["NAME"];
$classePrefixe = $stack[0]["attrs"]["PREFIX"];
$aNodeToSort = $stack[0]["children"];

if($oRes) {

require_once 'cms-inc/lib/Excel/reader.php';

// ExcelFile($filename, $encoding);
$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('CP1251');
//$data->setOutputEncoding('ISO88591');


$bDebug = false;
$bDebug = true;

$sMessage="";
$sRapportMailDejaPresent = "";
$sRapportMailMauvaiseSyntaxe = "";
$eMailDejaPresent = 0;
$eMailMauvaiseSyntaxe = 0;
$eMailSucces = 0;
$eImportSucces = 0;
$bIsinscrit = false;
$aChamps = array();

$uploadRep = $_SERVER['DOCUMENT_ROOT']."/custom/upload/";

//------------------------------------------------
function printCheck($postedValue){
	if (!isset($postedValue)){
		echo "";	
	}
	elseif(intval($postedValue) > 0){
		echo " checked";
	}
	elseif(((bool)($postedValue)) == true){
		echo " checked";
	}
	elseif (strval($postedValue) == "true"){
		echo " checked";
	}
	else{
		echo "";
	}
}
//---------------------------------------------

$sRapport = "";

if (count($_POST) == 0){	
	if (count($_GET) > 0){
	
		$_POST = $_GET;
		
		if (isset($_GET['importfile'])){
			if (is_file($_SERVER['DOCUMENT_ROOT'].$_GET['importfile'])){
				$_FILES['importfile']['tmp_name'] = $_SERVER['DOCUMENT_ROOT'].$_GET['importfile'];
				$saveFile = $_SERVER['DOCUMENT_ROOT'].$_GET['importfile'];
				
			}
		}
	}
}
else{
	$ext = ereg_replace('.*\.([^\.]+)', '\\1', $_FILES['importfile']['name']);
	$saveFile = $uploadRep.'import.'.$ext;
}
	

if(isset($_FILES['importfile'])){
	// Upload du fichier
	
	if(move_uploaded_file($_FILES['importfile']['tmp_name'], $saveFile) || is_file($_SERVER['DOCUMENT_ROOT'].$_GET['importfile'])){
		$status .= "Téléchargement du fichier : OK <br /><br />"; 
		if (ereg('xls', $ext)){
			echo ' import de XLS<br />';
		}
		elseif (ereg('csv', $ext)){
			echo ' import de CSV<br />';
		}
		else{
			echo ' import de type inconnu<br />';
			die(false);
		}	
		
		
		// controle ficher XLS HTML
		$fh = @fopen($uploadRep.'import.xls','r');
		if ($fh){
			while(!feof($fh)) {
				$sBodyXLS.=fgets($fh);
			}
		}
		
		if (strpos($sBodyXLS, "<!-- htmlxls -->") !== false){
			echo "fichier XLS HTML en provenance d'un export\n<br>";

			$rows = split("<tr>", $sBodyXLS);
			
			// check ligne 0
			if (strpos($rows[0], "</tr>") === false){
				//$rows = array_slice($rows, 1); 
			}
			
			// traitement des lignes
			for ($i = 0;$i < count($rows);$i++){
				$rows[$i] = str_replace("</tr>", "", $rows[$i]);
				$rows[$i] = split("<td>", $rows[$i]);
				//check cell 0
				if (strpos($rows[$i][0], "</td>") === false){
					$rows[$i][0] = "";// = array_slice($rows[$i], 1); 
				}
				// traitement des cells
				for ($j = 0;$j < count($rows[$i]);$j++){
					$rows[$i][$j] = str_replace("</td>", "", $rows[$i][$j]);
					$rows[$i][$j] = trim($rows[$i][$j]);
				}
			}
			
			$data->sheets[0]['cells'] = $rows;
			$data->sheets[0]['numRows'] = count($rows)-1;
			$data->sheets[0]['numCols'] = count($rows[1])-1;
			
			/*
			echo "<pre>\n";
			htmlentities(var_dump($data));
			echo "</pre>\n";
			*/
		}
		else{			
			//$data->read($uploadRep.'import.csv');		
		}
		
		//error_reporting(E_ALL ^ E_NOTICE);
		$bIntegre = true;
		// chercher une signature d'intégrité FINYYYY-MM-DD
		 
		if (isset($_POST["signature"]) && (intval($_POST["signature"]) == 1)){ 
			$bSignature = true;
			$search = "FIN".date("Y-m-d");
			$bIntegre = false;
			$fh = fopen($saveFile,'r');
			if ($fh){
				while(!feof($fh)) {
					$ligne = fgets($fh);
					if (strpos($ligne, $search) !== false){
						$bIntegre = true; 
						break;					
					}
				}
			}
			fclose($fh);
		}
		if ($bIntegre == false){
			die("false");
		}
		//----------------------------------------------------------	

		// do purge ??
		if (isset($_POST["dopurge"]) && (intval($_POST["dopurge"]) == 1)){
			$purgeSQL = "truncate ".$oRes->getTable();
			//echo $purgeSQL."<br />";
			dbExecuteQuery($purgeSQL);
		}
		else{
			// nada
		}
		//----------------------------------------------------------
		
		// skip line 1 ??
		if (isset($_POST["ignoreline1"]) && (intval($_POST["ignoreline1"]) == 1)){
			$startLine = 1;
		}
		else{
			$startLine = 0;
		}
		//----------------------------------------------------------
		
		// import ids ??
		if (isset($_POST["importids"]) && (intval($_POST["importids"]) == 1)){
			$bImportIds = true;
		}
		else{
			$bImportIds = false;
		}
		//----------------------------------------------------------
		
		// remplir les données vides avec le précédent record ??
		if (isset($_POST["fillnull"]) && (intval($_POST["fillnull"]) == 1)){
			$bFillNull = true;
		}
		else{
			$bFillNull = false;
		}
		//----------------------------------------------------------		
		
		if (ereg('xls', $ext)){
			//////////////////////////
			//include('import.xls.php');





$data->read($saveFile);

$bIsFirstLine = true;

for ($irow=1;$irow<$data->sheets[0]['numRows'];$irow++){
//while(!feof($fh)) {
	
	if (($bIsFirstLine == true) && ($startLine == 1)){
		// on skippe la premiere ligne si elle contient les noms des champs
		$bIsFirstLine = false;
		//echo "skipp";
		$aLigne = $data->sheets[0]["cells"][$irow];
		//pre_dump($aLigne);
	}
	elseif(($bSignature == true)&&(strpos($sRawLigne, "FIN".date("Y-m-d")) !== false)){
		
		// ligne de signature, on skippe
		//echo "signature<br>";		
		break;	
	}
	else{ // sinon on traite
		 
		$bIsFirstLine = false;
		//echo "<br>avant explode".$sRawLigne."<br><br>";
		
		$aLigne = $data->sheets[0]["cells"][$irow];
		//pre_dump($aLigne);
		/*
		// controle sur surexplode du a ; dans champs text
		for ($iLigne=0;$iLigne<(count($aLigne)-1);$iLigne++){				
			//echo ">> ".$aLigne[$iLigne]." - ".$aLigne[$iLigne+1]." >> " ;	
			if ((ereg('^".*[^"]$', $aLigne[$iLigne])) && (ereg('^[^"].*', $aLigne[$iLigne+1]))){						
				$tempMerge = $aLigne[$iLigne].";".$aLigne[$iLigne+1];
				$aLigne[$iLigne] = $tempMerge;
				$aLigne[$iLigne+1] = $tempMerge;
				$tempArrayDebut = array_slice($aLigne, 0, $iLigne); 
				$tempArrayFin = array_slice($aLigne, $iLigne+1); 						
				$aLigne = array_merge($tempArrayDebut, $tempArrayFin);
				$iLigne = $iLigne - 1;
				//pre_dump($aLigne);
			}
		}
		// controle sur double "" dans champs txt
		for ($iLigne=0;$iLigne<count($aLigne);$iLigne++){		
			$aLigne[$iLigne] = ereg_replace('""', '\"',	$aLigne[$iLigne]);
		}
		*/
		unset($oPrev);
		if (isset($eRes)){
			eval("$"."oPrev = new ".$classeName."(".$eRes.");");
			//echo "oprev is set<br />";
			//pre_dump($oPrev);
		}
		else{
			//echo "oprev is default<br />";
			eval("$"."oPrev = new ".$classeName."();");
		}
		unset($oRes);
		eval("$"."oRes = new ".$classeName."();");
		
		$bMailDejaPresent=false; // test unicité mail
		//pre_dump($aNodeToSort);
		for ($i=0;$i<count($aNodeToSort);$i++){
			
			if ($aNodeToSort[$i]["name"] == "ITEM" ){	 
				eval("$"."eKeyValue = "."$"."oRes->get_".$aNodeToSort[$i]["attrs"]["NAME"]."();"); 
				
				
				if ($bImportIds == false){
					$csvKeyValue = $aLigne[$i];
				}
				else{ // la premiere colonne CSV contient les IDs
					$csvKeyValue = $aLigne[$i+1];
				}							
				
										
				if (isset($csvKeyValue) && (trim($csvKeyValue) != "") && ($csvKeyValue != " ")){					
					//$csvKeyValue = utf8_decode($csvKeyValue);
					$csvKeyValue = str_replace('"', '\\"', $csvKeyValue);
					$csvKeyValue = str_replace(array(chr(96)), '\'', $csvKeyValue);
					$eKeyValue = str_replace(array(chr(190),chr(191)), '"', $csvKeyValue);
					//echo ord(substr( $eKeyValue, 0,1));
												
				}							
				else{								
					if ($bFillNull == true){
						//print("$"."eKeyValue = "."$"."oPrev->get_".$aNodeToSort[$i]["attrs"]["NAME"]."();");
						eval("$"."eKeyValue = "."$"."oPrev->get_".$aNodeToSort[$i]["attrs"]["NAME"]."();");
						//echo "prev value is ".$eKeyValue. " ####<br>";								
					}
					else{
						//echo "default value is ".$eKeyValue. " ####<br>";								
					}
				}							
				
				//echo "<hr />";
				
				/*
				if ($aNodeToSort[$i]["attrs"]["FKEY"]){ // cas de foregin key  
					if ($eKeyValue == "n/a" || $eKeyValue == "") $eKeyValue = -1;
					$sTempClasse = $aNodeToSort[$i]["attrs"]["FKEY"];
					if (trim($eKeyValue)!="n/a") {
						if ($eKeyValue > -1){ 
							//print("$"."oTemp = new ".$sTempClasse."();");
							eval("$"."oTemp = new ".$sTempClasse."();");
							//echo "tester la presence de ".$eKeyValue." dans la table ".$sTempClasse." : ";
							$tempPrefix = ereg_replace("([^_]*)_.*", "\\1", $oTemp->getFieldPK());
							$tempFieldWhere = $tempPrefix."_".$oTemp->getDisplay();
							$tempGetterWhere = ereg_replace("([^_]*)_.*", "get", $oTemp->getFieldPK())."_".$oTemp->getDisplay();
							$eKeyValue_ = str_replace('\\', '\"',	$eKeyValue);
							$tempCount = getCount2($oTemp, $tempFieldWhere, $eKeyValue_, "TEXT");

							if ($tempCount == 0){
								echo "inserer  ".$eKeyValue." dans la table ".$sTempClasse."<br>";
								eval( "$"."oTemp->set_".$oTemp->getDisplay()."(\"".$eKeyValue_."\");");
								$eKeyValue = dbInsertWithAutoKey($oTemp);
								//echo $eKeyValue;
								//echo " (fk vers valeur insérée dans ".$sTempClasse.")";
							}
							else{
								$aTempWhere = array($tempGetterWhere);
								$aTempValue = array($eKeyValue_);
								$aTempRes = dbGetObjectsFromFieldValue($sTempClasse, $aTempWhere, $aTempValue, "");
								$otempRes = $aTempRes[0];
								$eKeyValue = $otempRes->get_id();
								//echo $eKeyValue;
								//echo " (fk vers valeur existante dans ".$sTempClasse.")";
							}
							unset($oTemp);
						}
						else{
							//echo "n/a";
						}
					}
					else {
						$eKeyValue = -1;
					}
				}// fin fkey
				elseif ($aNodeToSort[$i]["attrs"]["OPTION"] == "enum"){ // cas enum	
					$eNewKeyValue = 0;	
					if (isset($aNodeToSort[$i]["children"]) && (count($aNodeToSort[$i]["children"]) > 0)){
						foreach ($aNodeToSort[$i]["children"] as $childKey => $childNode){
							if($childNode["name"] == "OPTION"){ // on a un node d'option				
								if ($childNode["attrs"]["TYPE"] == "value"){
									if (strval($eKeyValue) == strval($childNode["attrs"]["LIBELLE"])){		
										$eNewKeyValue = $childNode["attrs"]["VALUE"];	
										//echo "value : ".$eKeyValue." is ".$eNewKeyValue."<br />\n";
										break;			
									}
								} //fin type  == value				
							}
						}
					}	
					$eKeyValue = $eNewKeyValue;
				} // fin cas enum

				else{ // cas typique
				*/
					/*
					if ($aNodeToSort[$i]["attrs"]["NAME"] == "statut"){ // cas statut
						//echo lib($eKeyValue); 
						//traite id
						if ($eKeyValue == DEF_ID_STATUT_ATTEN) $eKeyValue = DEF_ID_STATUT_ATTEN; 
						else if ($eKeyValue == DEF_ID_STATUT_LIGNE) $eKeyValue = DEF_ID_STATUT_LIGNE; 
						else if ($eKeyValue == DEF_ID_STATUT_ARCHI) $eKeyValue = DEF_ID_STATUT_ARCHI; 
						//traite libelle
						else if (strtolower($eKeyValue) == "en attente") $eKeyValue = DEF_ID_STATUT_ATTEN; 
						else if (strtolower($eKeyValue) == "abonné") $eKeyValue = DEF_ID_STATUT_LIGNE; 
						else if (strtolower($eKeyValue) == "abonne") $eKeyValue = DEF_ID_STATUT_LIGNE; 
						else if (strtolower($eKeyValue) == "désabonné") $eKeyValue = DEF_ID_STATUT_ARCHI; 
						else if (strtolower($eKeyValue) == "desabonne") $eKeyValue = DEF_ID_STATUT_ARCHI; 
						
						else if (strtolower($eKeyValue) == "en ligne") $eKeyValue = DEF_ID_STATUT_LIGNE; 
						else if (strtolower($eKeyValue) == "en attente") $eKeyValue = DEF_ID_STATUT_ATTEN; 
						else if (strtolower($eKeyValue) == "archivé") $eKeyValue = DEF_ID_STATUT_ARCHI; 
						else $eKeyValue = DEF_CODE_STATUT_DEFAUT;  
					}
					else{
					*/
						if ($eKeyValue > -1){ // cas typique typique
							if ($aNodeToSort[$i]["attrs"]["TYPE"] == "date"){ // cas date
								if (ereg("[0-9]{2}.{1}[0-9]{2}.{1}[0-9]{4}",$eKeyValue)){ // traduction date FR to FR
									$eKeyValue = ereg_replace("([0-9]{2}).{1}([0-9]{2}).{1}([0-9]{4})", "\\1/\\2/\\3", $eKeyValue);
								}
								elseif (ereg("[0-9]{4}.{1}[0-9]{2}.{1}[0-9]{2}",$eKeyValue)){ // reformattage date US to FR
									$eKeyValue = ereg_replace("([0-9]{4}).{1}([0-9]{2}).{1}([0-9]{2})", "\\3/\\2/\\1", $eKeyValue);
								}
								elseif (ereg("[0-9]{2}.{1}[0-9]{2}.{1}[0-9]{2}",$eKeyValue)){ // traduction date FR to FR
									$eKeyValue = ereg_replace("([0-9]{2}).{1}([0-9]{2}).{1}([0-9]{2})", "\\1/\\2/".date("Y","\\3"), $eKeyValue);
								}
								else{ // date impossible à reconnaitre
									$eKeyValue = "n/a";
								}
								//echo $eKeyValue;
							}
							else{// cas typique typique typique
								if ($aNodeToSort[$i]["attrs"]["NAME"] == "id") {
									$id = $eKeyValue;
									
								}
								//echo $eKeyValue;
							}
						}
						else{
							
							//echo "n/a";
						}
					//} // fin if status
				//} // fin if fk
			} // fin if item
			// set des attributs de l'objet
			
			if ($aNodeToSort[$i]["attrs"]["NAME"]!="") {
				 
				if (($aNodeToSort[$i]["attrs"]["TYPE"] == "varchar") || ($aNodeToSort[$i]["attrs"]["TYPE"] == "text") || ($aNodeToSort[$i]["attrs"]["TYPE"] == "decimal")){ // cas texte
					//print( "$"."oRes->set_".$aNodeToSort[$i]["attrs"]["NAME"]."(\"".$eKeyValue."\");");
					eval( "$"."oRes->set_".$aNodeToSort[$i]["attrs"]["NAME"]."(\"".$eKeyValue."\");");
				}
				elseif ($aNodeToSort[$i]["attrs"]["TYPE"] == "date"){ // cas date
					//print( "$"."oRes->set_".$aNodeToSort[$i]["attrs"]["NAME"]."('".$eKeyValue."');");
					eval( "$"."oRes->set_".$aNodeToSort[$i]["attrs"]["NAME"]."('".$eKeyValue."');");
				}
				else{ 
					//echo "--".$aNodeToSort[$i]["attrs"]["NAME"]."<br>"; 
					eval( "$"."oRes->set_".$aNodeToSort[$i]["attrs"]["NAME"]."(".(int)$eKeyValue.");");
				}
				if ($iLigne == 1) $aChamps[] = $aNodeToSort[$i]["attrs"]["NAME"];
 
			}
			
			//recherche doublon de mail
			
			if ($aNodeToSort[$i]["attrs"]["OPTION"]){
				if ($aNodeToSort[$i]["attrs"]["OPTION"]=="email"){
					$bIsinscrit = true;
					$eKeyValue = trim(strtolower($eKeyValue));
					//echo $eKeyValue;
					if (getCount($classeName, $classePrefixe."_".$aNodeToSort[$i]["attrs"]["NAME"], $classePrefixe."_".$aNodeToSort[$i]["attrs"]["NAME"], $eKeyValue, "TEXT") > 0 ) {
						$bMailDejaPresent=true;
						//echo "- ".$eKeyValue." deja<br>";
					}
					if ($eKeyValue=="") {
						$bMailDejaPresent=true;
						//echo "- ".$eKeyValue." deja<br>";
					}
					$bIsEmail=true;
					if (!isEmail($eKeyValue)){
						$bIsEmail=false;
						//echo "- ".$eKeyValue." syntaxe<br>";
					}
					if (!$bMailDejaPresent && $bIsEmai) {
						//echo "- ".$eKeyValue." ok<br>";
					
					}	 
				}
			}
			$nbColonneI = $i;
		} // fin for
		//pre_dump($oRes);
		// save de l'objet
		
		// recherche d'eventuelles asso
		//include('cms-inc/autoClass/import.association.php');  // SIF
		
		if ($bIsinscrit) {
			if ($bIsEmail) {
				
				if ($bMailDejaPresent==false) {
					if ($bImportIds == false){
						$eRes = dbInsertWithAutoKey($oRes);
						$eMailSucces++;
					}
					else{
						$eRes = dbInsert($oRes);
						$eMailSucces++;
					}
				}
				else {
					$sRapportMailDejaPresent.= $sRawLigne;
					$eMailDejaPresent++;
				
				}
			}
			else {
				$bIsEmail=false;
				$sRapportMailMauvaiseSyntaxe.= $sRawLigne;
				$eMailMauvaiseSyntaxe++;
			}
		}
		else {
			/*$eRes = dbInsertWithAutoKey($oRes);
			echo $id." ok<br>";
			*/
			if ((isset($id))&&(getCount_where($classeName, array($classePrefixe."_id"), array($id), array("NUMBER"))>0)) {
				$eRes = dbUpdate($oRes);
				//echo "$id = update ".$oRes->get_id()."<br>"; 
				if ($eRes) $eImportSucces++;
			}	
			else {
				if (($id == -1)||($id == NULL)){
					//$eRes = dbInsertWithAutoKey($oRes);
					$eRes = dbSauve($oRes);
				}
				else{
					$eRes = dbSauve($oRes);
				}
				
				//echo "$id = insert ".$oRes->get_id()."<br>"; 
				if ($eRes) $eImportSucces++;
			} 
		}
		
		//echo "Insert entrée id ".$eRes."<br>";
	} // skip line 1
}// fin while


//unlink($uploadRep.'import.xls');







			//////////////////////////
		}
		elseif (ereg('csv', $ext)){
			include('import.csv.php');
		}

		$status .= "Suppression du fichier après importation OK <br /><br />";
		
		$status = "<span class=\"arbo2\"><strong>".$status."</strong></span>";
		
		if (is_file($_SERVER['DOCUMENT_ROOT'].DEF_ROOT_IMPORTREPORT."export_inscrit_doublon.csv")) {
			unlink($_SERVER['DOCUMENT_ROOT'].DEF_ROOT_IMPORTREPORT."export_inscrit_doublon.csv");
		}
		
		if (is_file($_SERVER['DOCUMENT_ROOT'].DEF_ROOT_IMPORTREPORT."export_inscrit_syntaxe.csv")) {
			unlink($_SERVER['DOCUMENT_ROOT'].DEF_ROOT_IMPORTREPORT."export_inscrit_syntaxe.csv");
		}
		// resultat de l'inport
		//--------------------------------------------
		echo "<table  border=\"0\" cellpadding=\"10\" cellspacing=\"0\" class=\"arbo\" width=\"600\" bgcolor=\"#cccccc\">";
		echo "<tr><td>"; 
		
		echo "<span class=\"arbo2\"><strong>Résultats de l'import : </strong></span><br><br>";
		if ($bIsinscrit) {
			echo "<span class=\"arbo2\">".$eMailSucces." inscrits importés avec succès</span><br><br>";
			if (($eMailDejaPresent>0 || $eMailMauvaiseSyntaxe>0) && $bIsEmail) { 
				if(!is_dir($_SERVER['DOCUMENT_ROOT'].DEF_ROOT_IMPORTREPORT)){
					mkdir ($_SERVER['DOCUMENT_ROOT'].DEF_ROOT_IMPORTREPORT);
					
				}
				
				if ($eMailDejaPresent>0) {
					$sRetour1 = export_list_inscrits($_SERVER['DOCUMENT_ROOT'].DEF_ROOT_IMPORTREPORT, $sRapportMailDejaPresent, $aChamps, "doublon");
					if ($sRetour1!="") {
						echo "<span class=\"\"><a href=\"".$_SERVER['HOST_NAME'].DEF_ROOT_IMPORTREPORT."export_inscrit_doublon.csv\">Exporter les doublons (".$eMailDejaPresent." inscrit(s))</a></span><br>";
					}
					
				}
				if ($eMailMauvaiseSyntaxe>1) {
					$sRetour2 = export_list_inscrits($_SERVER['DOCUMENT_ROOT'].DEF_ROOT_IMPORTREPORT, $sRapportMailMauvaiseSyntaxe, $aChamps, "syntaxe");
					if ($sRetour2!="") {
			
						echo "<span class=\"\"><a href=\"".$_SERVER["HOST_NAME"].DEF_ROOT_IMPORTREPORT."export_inscrit_syntaxe.csv\">Exporter les mails avec une mauvaise syntaxe (".($eMailMauvaiseSyntaxe-1)." inscrit(s))</a></span><br>";
					}
				}
			}
		}
		else {
			echo "<span class=\"arbo2\">".$eImportSucces." imports réussis</span><br><br>";	
		}
		echo "</td></tr></table>";
		// ----------------------------------------------
		
		
	}else{
		$status = "<span class=\"alert\"><strong>Erreur lors de du téléchargement du fichier</strong></span>";
	}
}
?>
<style >
type="text/css">
	.lignevalidee {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 11px;
		text-decoration: none;
		font-weight: normal;
		letter-spacing: normal;
	}
	tr.lignevalidee:hover {
		background-color: #DDEAAB;
	}
	.lignenonvalidee {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 11px;
		text-decoration: none;
		font-weight: bold;
		letter-spacing: normal;
	}
	tr.lignenonvalidee:hover {
		background-color: #DDEAAB;
	}
</style>
<p><span class="arbo2"><strong>Import d'un fichier de données</strong></span><br />

  <br />
</p>
<p><strong>Upload d'un fichier CSV</strong><br />

  <br />
</p>
<table  border="1" cellpadding="5" cellspacing="0" class="arbo" width="600">
<form name="formFile" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">

 <tr bgcolor="#CCCCCC" valign="middle">
  <td nowrap align="center">&nbsp;<b>S&eacute;lection du fichier</b>&nbsp;</td>
  <td nowrap align="left"><input type="file" name="importfile" id="importfile" class="arbo" pattern=".+(\.(csv|xls))" errorMsg="Vous devez sélectionner un fichier qui porte l'extension .csv ou .xls" value="*.csv"></td>
 </tr>
 <tr valign="middle" bgcolor="#CCCCCC">
   <td nowrap="nowrap" align="center">purger avant l'import </td>
   <td nowrap="nowrap" align="left"><input type="checkbox" id="dopurge" name="dopurge" value="1" <?php echo printCheck($_POST["dopurge"])?> /></td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
   <td nowrap align="center">ignorer la premi&egrave;re ligne (nom des champs) </td>
   <td nowrap align="left"><input type="checkbox" id="ignoreline1" name="ignoreline1" value="1" <?php echo printCheck($_POST["ignoreline1"])?> /></td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
   <td nowrap align="center">la premi&egrave;re colonne contient les ids </td>
   <td nowrap align="left"><input type="checkbox" id="importids" name="importids" value="1" <?php echo printCheck($_POST["importids"])?> /></td>
 </tr>
 <tr valign="middle" bgcolor="#CCCCCC">
   <td nowrap="nowrap" align="center">remplir les champs vides avec les donn&eacute;es de la ligne pr&eacute;c&eacute;dente</td>
   <td nowrap="nowrap" align="left"><input type="checkbox" id="fillnull" name="fillnull" value="1" <?php echo printCheck($_POST["fillnull"])?> /></td>
 </tr>
 <tr valign="middle" bgcolor="#CCCCCC">
   <td nowrap="nowrap" align="center">signature de fin de fichier de type <b>FINaaaa-mm-jj</b></td>
   <td nowrap="nowrap" align="left"><input type="checkbox" id="signature" name="signature" value="1" <?php echo printCheck($_POST["signature"])?> /></td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
   <td nowrap align="center">&nbsp;</td>
   <td nowrap align="left"><input type="button" value="Importer" name="import" onclick="javascript:if (validate_form(0)) submit();" class="arbo" /></td>
 </tr>
</form>
</table>
<span class="arbo"> 
- Format de date : JJ/MM/AAAA<br />
- Signature de fin de fichier : FINaaaa-mm-jj
</span>
<p>&nbsp;</p>
<p><strong><br />
  <br />
  Traitement d'un fichier pr&eacute;sent sur le serveur (traitement automatisable)</strong><br />
  <br />
</p>
<table  border="1" cellpadding="5" cellspacing="0" class="arbo" width="600">
<form name="formFile" method="get" action="<?php echo $_SERVER['PHP_SELF'] ?>">

 <tr bgcolor="#CCCCCC" valign="middle">
  <td width="388" align="center" nowrap>&nbsp;<b>S&eacute;lection du fichier</b>&nbsp;</td>
  <td width="186" align="left" nowrap><input type="text" name="importfile" id="importfile" class="arbo" size="35" value="<?php echo $_GET['importfile']?>"></td>
 </tr>
 <tr valign="middle" bgcolor="#CCCCCC">
   <td nowrap="nowrap" align="center">purger avant l'import </td>
   <td nowrap="nowrap" align="left"><input type="checkbox" id="dopurge" name="dopurge" value="1" <?php echo printCheck($_GET["dopurge"])?> /></td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
   <td nowrap align="center">ignorer la premi&egrave;re ligne (nom des champs) </td>
   <td nowrap align="left"><input type="checkbox" id="ignoreline1" name="ignoreline1" value="1" <?php echo printCheck($_GET["ignoreline1"])?> /></td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
   <td nowrap align="center">la premi&egrave;re colonne contient les ids </td>
   <td nowrap align="left"><input type="checkbox" id="importids" name="importids" value="1" <?php echo printCheck($_GET["importids"])?> /></td>
 </tr>
 <tr valign="middle" bgcolor="#CCCCCC">
   <td nowrap="nowrap" align="center">remplir les champs vides avec les donn&eacute;es de la ligne pr&eacute;c&eacute;dente</td>
   <td nowrap="nowrap" align="left"><input type="checkbox" id="fillnull" name="fillnull" value="1" <?php echo printCheck($_GET["fillnull"])?> /></td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
   <td nowrap align="center">&nbsp;</td>
   <td nowrap align="left"><input type="button" value="Importer" name="import" onclick="javascript:submit();" class="arbo" /></td>
 </tr>
</form>
</table>

<?php 

} // fin if oRes

// on supprime les fichiers

if (is_file($saveFile)){
	unlink($saveFile);

}


//\">Exporter les mails avec une mauvaise syntaxe (".($eMailMauvaiseSyntaxe-1)." inscrit(s))</a></span><br>";
			
// je crée le dossier custom/rapport

/*echo "mails deja present : ".$eMailDejaPresent."<br>";
//echo $sRapportMailDejaPresent;
$sFilename = $sRep."export_inscrit_".$sTypeExport."_".$sDate.".csv";
echo "mails MauvaiseSyntaxe : ".$eMailMauvaiseSyntaxe."<br>";
//echo $sRapportMailMauvaiseSyntaxe; 
}




echo $sRapport */ ?>