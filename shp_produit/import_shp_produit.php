<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
if (!isset($classeName)){
	$classeName = preg_replace('/[^_]*_(.*)\.php/si', '$1', basename($_SERVER['PHP_SELF']));
}
//------------------------------------------------------------------------------------------------------

// translation engine
$translator =& TslManager::getInstance();

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
	$ext = preg_replace('/.*\.([^\.]+)/msi', '$1', $_FILES['importfile']['name']);
	$saveFile = $uploadRep.'import.'.$ext;
}
	
if(isset($_FILES['importfile'])){
	// Upload du fichier
	
	if(move_uploaded_file($_FILES['importfile']['tmp_name'], $saveFile) || is_file($_SERVER['DOCUMENT_ROOT'].$_GET['importfile'])){
		$status .= "Téléchargement du fichier : OK <br /><br />"; 
		if (preg_match('/xls/msi', $ext)){
			echo ' import de XLS<br />';
		}
		elseif (preg_match('/csv/msi', $ext)){
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

			$rows = explode("<tr>", $sBodyXLS);
			
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
		
		if (preg_match('/xls/msi', $ext)){
			include('import.xls.php');
		}
		elseif (preg_match('/csv/msi', $ext)){
			// Debut traitement CSV
			//echo "open file : ".$saveFile."<br/>";
			$fh = fopen($saveFile,'r'); 
			if ($fh){
			//$lines = file($saveFile);
			//if (!empty($lines)) {
				//pre_dump(htmlentities($oRes->XML));
				$bIsFirstLine = true;
				while(!feof($fh)) {
					$sRawLigne = fgets($fh);
				//foreach ($lines as $sRawLigne) {
					//echo "NEW LINE : ".$sRawLigne."<br/><br/>";
					if (($bIsFirstLine == true) && ($startLine == 1)){
						// on skippe la premiere ligne si elle contient les noms des champs
						$bIsFirstLine = false;
						//echo "skip first line";
						$tranches = explode(";",$sRawLigne);
						//pre_dump($tranches);
					} else { // sinon on traite
						 
						$bIsFirstLine = false;
						//echo "<br />avant explode".$sRawLigne."<br><br>";

						$aLigne = explode(";",$sRawLigne);
						//viewArray($aLigne, 'ligne');
						if (!empty($aLigne[0]) && trim($aLigne[0]) != '') {
							unset($oInsert);
							$oInsert = new shp_produit();
							$oInsert->set_id($next_id);
							$oInsert->set_id_gamme($aLigne[3]);
							$oInsert->set_id_type($aLigne[4]);
							$oInsert->set_id_unite($aLigne[5]);
							$oInsert->set_id_diaporama(-1);
							$oInsert->set_statut(4);
							$oInsert->set_reference('');
							$oInsert->set_remontee('N');
							$oInsert->set_titre_court($translator->getTextID(ucfirst($aLigne[1]).' '.$aLigne[0].' '.$aLigne[2], true));
							$oInsert->set_titre_long('NULL');
							if ($aLigne[6] != '')
								$oInsert->set_sous_titre($translator->getTextID($aLigne[6], true));
							else	$oInsert->set_sous_titre(-1);
							$oInsert->set_texte_long('NULL');
							$oInsert->set_dimensions($aLigne[7]);
							if ($aLigne[8] != '')
								$oInsert->set_infos($translator->getTextID($aLigne[8], true));
							else	$oInsert->set_infos(-1);
							$oInsert->set_vignette($aLigne[9]);
							$oInsert->set_visuel($aLigne[10]);
							$oInsert->set_document('NULL');
							$oInsert->set_url('');
							//if ($aLigne[11] == '1')
							//	$aLigne[11] = '1,000';
							$oInsert->set_pieces_unite($aLigne[11]);
							$oInsert->set_poids_unite($aLigne[12]);
							$oInsert->set_delai_livraison(-1);
							$oInsert->set_quantite_stock($aLigne[13]);
							$oInsert->set_alerte_stock($aLigne[14]);
							$oInsert->set_ordre(-1);
							$oInsert->set_cdate(date('Y-m-d H:i:s'));
							$oInsert->set_mdate(date('Y-m-d H:i:s'));
							$eRes = dbInsertWithAutoKey($oInsert);

							//pre_dump($aLigne);
							if ($aLigne[15] > 0) {
								unset($oInsert);
								$oInsert = new shp_tarif();
								$oInsert->set_id($next_id);
								$oInsert->set_id_produit($eRes);
								$oInsert->set_id_unite($aLigne[5]);
								$oInsert->set_statut(4);
								$oInsert->set_intitule("Tarif standard ".ucfirst($aLigne[1]).' '.$aLigne[0].' '.$aLigne[2], true);
								$oInsert->set_prix($aLigne[15]);
								$oInsert->set_ordre(-1);
								$oInsert->set_cdate(date('Y-m-d H:i:s'));
								$oInsert->set_mdate(date('Y-m-d H:i:s'));

								$_eRes = dbInsertWithAutoKey($oInsert);
							}

							if ($eRes && $_eRes)
								$eImportSucces++;

						}
						//echo "Insert entrée id ".$eRes."<br>";
					} // skip line 1
				}// fin while
			}
			// Fin traitement CSV
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
<style type="text/css">
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




echo $sRapport */ 
?>