<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

//include('cms-inc/autoClass/import.php');

if (!isset($classeName)){
	$classeName = preg_replace('/[^_]*_(.*)\.php/si', '$1', basename($_SERVER['PHP_SELF']));
}

//------------------------------------------------------------------------------------------------------

$translator =& TslManager::getInstance();
$langpile = $translator->getLanguages();

$rev_langpile = Array();
foreach ($langpile as $index => $props)
	$rev_langpile[$props['libellecourt']] = $index;
$langcodes = array_keys($rev_langpile);
										
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
$aImportReport = Array();
$aImportAlert = Array();
$bIsinscrit = false;
$aChamps = array();

$uploadRep = $_SERVER['DOCUMENT_ROOT']."/custom/upload/";

//------------------------------------------------
function printCheck($postedValue) {
	if (!isset($postedValue))
		echo "";	
	elseif (intval($postedValue) > 0)
		echo " checked";
	elseif (((bool)($postedValue)) == true)
		echo " checked";
	elseif (strval($postedValue) == "true")
		echo " checked";
	else	echo "";
}

function makeShortText($text) {
	$short = substr($text, 0, 50);
	if (strlen($text) > 50 ) 
		return (String)	$short . " ... ";
	return (String)	$text;
}

//---------------------------------------------

$sRapport = "";

if (count($_POST) == 0) {	
	if (count($_GET) > 0) {
	
		$_POST = $_GET;
		
		if (isset($_GET['importfile'])) {
			if (is_file($_SERVER['DOCUMENT_ROOT'].$_GET['importfile'])) {
				$_FILES['importfile']['tmp_name'] = $_SERVER['DOCUMENT_ROOT'].$_GET['importfile'];
				$saveFile = $_SERVER['DOCUMENT_ROOT'].$_GET['importfile'];
				
			}
		}
	}
} else {
	$ext = ereg_replace('.*\.([^\.]+)', '\\1', $_FILES['importfile']['name']);
	$saveFile = $uploadRep.'import.'.$ext;
}
	
if (isset($_FILES['importfile'])) {
	// Upload du fichier
	
	if (move_uploaded_file($_FILES['importfile']['tmp_name'], $saveFile) || is_file($_SERVER['DOCUMENT_ROOT'].$_GET['importfile'])) {
		$status .= "Téléchargement du fichier : OK <br /><br />"; 
		if (ereg('xls', $ext))
			echo ' import de XLS<br />';
		elseif (ereg('csv', $ext))
			echo ' import de CSV<br />';
		else	die(' import de type inconnu<br />');
		
		
		// controle ficher XLS HTML
		$fh = @fopen($uploadRep.'import.xls','r');
		if ($fh) {
			while(!feof($fh))
				$sBodyXLS.=fgets($fh);
		}
		
		if (strpos($sBodyXLS, "<!-- htmlxls -->") !== false) {
			echo "fichier XLS HTML en provenance d'un export\n<br>";

			$rows = split("<tr>", $sBodyXLS);
			
			// check ligne 0
			if (strpos($rows[0], "</tr>") === false) {
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
		
		//error_reporting(E_ALL ^ E_NOTICE);
		$bIntegre = true;
		// chercher une signature d'intégrité FINYYYY-MM-DD
		 
		if (isset($_POST["signature"]) && (intval($_POST["signature"]) == 1)) { 
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
		if ($bIntegre == false)
			die("false");

		//----------------------------------------------------------	

		// do purge ??
		if (isset($_POST["dopurge_refs"]) && (intval($_POST["dopurge_refs"]) == 1)){
			$purgeSQL = "truncate cms_chaine_reference;";
			//echo $purgeSQL."<br />";
			dbExecuteQuery($purgeSQL);
		}
		if (isset($_POST["dopurge_trad"]) && (intval($_POST["dopurge_trad"]) == 1)){
			$purgeSQL = "truncate cms_chaine_traduite;";
			//echo $purgeSQL."<br />";
			dbExecuteQuery($purgeSQL);
		}
		//----------------------------------------------------------
		
		// skip line 1 ??
		if (isset($_POST["ignoreline1"]) && (intval($_POST["ignoreline1"]) == 1))
			$startLine = 1;
		else	$startLine = 0;

		//----------------------------------------------------------
		
		// import ids ??
		if (isset($_POST["importids"]) && (intval($_POST["importids"]) == 1))
			$bImportIds = true;
		else	$bImportIds = false;

		//----------------------------------------------------------
		
		// import references ??
		if (isset($_POST["importrefs"]) && (intval($_POST["importrefs"]) == 1))
			$bImportRefs = true;
		else	$bImportRefs = false;
		
		//----------------------------------------------------------
		//echo "PARAMETERS startLine : $startLine | bImportIds : $bImportIds | bImportRefs : $bImportRefs<br/>";
		
		if (ereg('xls', $ext))  {
			
			$data->read($saveFile);

			$bIsFirstLine = true;
			$langs = Array();
			for ($irow=1; $irow<$data->sheets[0]['numRows']; $irow++) {
				
				if ($bImportIds)
					$firstpos = 2;
				else	$firstpos = 1;

				if ($bIsFirstLine == true && $startLine == 1){
					// on skippe la premiere ligne si elle contient les noms des champs
					$bIsFirstLine = false;
					$col_headers = $data->sheets[0]["cells"][$irow];

					foreach ($col_headers as $pos => $tranche) {
						if ($bImportIds && $pos == 0)
							continue;
						$tranche = trim($tranche);

						if ($pos == $firstpos && $bImportRefs && $tranche != $langpile[DEF_APP_LANGUE]['libellecourt'])
							die("<b>".$tranche."</b> header in column ".$pos." does not correspond to the declared default language (DEF_APP_LANGUE)<br/>Check column header value or uncheck 'References lay in column 1' if you only use IDs.");
						elseif ($pos == $firstpos && !$bImportRefs && $tranche == $langpile[DEF_APP_LANGUE]['libellecourt'])
							die("<b>".$tranche."</b> header in column ".$pos." corresponds to the declared default language (DEF_APP_LANGUE) and you did not check 'References lay in column 1'.");
						elseif (!in_array($tranche, $langcodes))
							die("<b>".$tranche."</b> header in column ".$pos." does not correspond to a valid language code.");
						$langs[] = $tranche;
					}
				} elseif ($bSignature == true && strpos($sRawLigne, "FIN".date("Y-m-d")) !== false) {
					// ligne de signature, on skippe
					break;	
				} else { // sinon on traite
					 
					$bIsFirstLine = false;
					//echo "<br />avant explode".$sRawLigne."<br><br>";
					
					$aLigne = $data->sheets[0]["cells"][$irow];
					//pre_dump($aLigne);
					$skip = false;
					if ($bImportIds) {
						if ($aLigne[1] > 0) {
							// if we do have a valid ID in first column 
							if ($bImportRefs) {
								// check wether reference chain exists and wether it matches given ID
								$ref_id = $translator->getTextID($aLigne[$firstpos]);
								if ($ref_id <= 0) {
									$aImportAlert[] = "Reference chain given at row ".$irow." does not exist in database. Row was not processed.";
									$skip = true;
								} elseif ($aLigne[1] != $ref_id) {
									$aImportAlert[] = "Reference ID and reference chain given at row ".$irow." do not match. Row was not processed.";
									$skip = true;
								}
							} elseif (!$translator->isText($aLigne[1])) {
								$aImportAlert[] = "Reference ID (".$aLigne[1].") given at row ".$irow." does not exist in database. Row was not processed.";
								$skip = true;
							}
						} else {
							$aImportAlert[] = "Reference ID (".$aLigne[1].") given at row ".$irow." is invalid. Row was not processed.";
							$skip = true;
						}
					}

					if (!$skip) {
						//pre_dump($aLigne);
						$translations = Array();
						for ($iLigne=$firstpos; $iLigne <= count($aLigne); $iLigne++) {
							if ($iLigne == $firstpos) {
								// skip second column if ID/reference was already checked
								if ($bImportIds)
									$reference = $translator->getByID($aLigne[$iLigne]);
								else {
									$reference = $aLigne[$iLigne];
									// skip empty reference chain
									if (trim($reference == ''))
										break;
									// process reference
									$ref_id = $translator->getTextID($reference);
									if ($ref_id > 0) {
										// reference exists
										$aImportReport[] = "Row ".$irow." : Reference '<i>".makeShortText($reference)."</i>' already exist in database.";
										// checks wether database version of reference is jerky and may be cleaned
										if (trim($reference) != $reference) {
											$test = $translator->getTextID(trim($reference));
											if ($test > 0) {
												// better keep both references as records may need them
											} else {
												$translator->updateText($ref_id, trim($reference));
												$aImportReport[] = "<span style=\"color: red;\">Row ".$irow." : Reference '<i>".makeShortText($reference)."</i>' in database was jerky ans was trimmed.</span>";
												$reference = trim($reference);
											}
										}
									} else {
										// test if jerky version of reference was given
										$ref_id = $translator->getTextID(trim($reference));
										if ($ref_id > 0) {
											$aImportReport[] = "<span style=\"color: red;\">Row ".$irow." : Reference '<i>".makeShortText($reference)."</i>' was jerky so database version was used.</span>";
											$reference = trim($reference);
										} else {
											// create reference
											$translator->addReference($reference, false);
											$aImportReport[] = "<span style=\"color: green;\">Row ".$irow." : Reference '<i>".makeShortText($reference)."</i>' was created in database.</span>";
											$eImportSucces++;
										}
									}
								}
								continue;
							}
							// if extra columns exist for translations
							$translations[$rev_langpile[trim($col_headers[$iLigne])]] = $aLigne[$iLigne];
						}
						if (!empty($translations)) {
							$ref_id = $translator->addTranslation($reference, $translations);
							if ($ref_id > 0) {
								$aImportReport[] = "Row ".$irow." : Translations (".implode(', ', $langs).") for reference <i>".makeShortText($reference)."</i> were created in database.";
								$eImportSucces++;
							}
						}
					}
				}
			}// fin while

		} elseif (ereg('csv', $ext)) {
			// Debut traitement CSV
			//echo "open file : ".$saveFile."<br/>";
			$fh = fopen($saveFile,'r'); 
			if ($fh) {
				//pre_dump(htmlentities($oRes->XML));
				$bIsFirstLine = true;
				$cnt_line = 1;
				$langs = Array();
				while (!feof($fh)) {
					$sRawLigne = fgets($fh);
					//echo "NEW LINE : ".$sRawLigne."<br/><br/>";
					if ($bImportIds)
						$firstpos = 1;
					else	$firstpos = 0;

					if (($bIsFirstLine == true) && ($startLine == 1)) {
						// possibly skip first row (column headers)
						$bIsFirstLine = false;
						//echo "skip first line";
						$col_headers = explode(";",$sRawLigne);
						// check all column headers
						foreach ($col_headers as $pos => $tranche) {
							if ($bImportIds && $pos == 0)
								continue;
							$tranche = trim($tranche);

							if ($pos == $firstpos && $bImportRefs && $tranche != $langpile[DEF_APP_LANGUE]['libellecourt'])
								die("<b>".$tranche."</b> header in column ".$pos." does not correspond to the declared default language (DEF_APP_LANGUE)<br/>Check column header value or uncheck 'References lay in column 1' if you only use IDs.");
							elseif ($pos == $firstpos && !$bImportRefs && $tranche == $langpile[DEF_APP_LANGUE]['libellecourt'])
								die("<b>".$tranche."</b> header in column ".$pos." corresponds to the declared default language (DEF_APP_LANGUE) and you did not check 'References lay in column 1'.");
							elseif (!in_array($tranche, $langcodes))
								die("<b>".$tranche."</b> header in column ".$pos." does not correspond to a valid language code.");
							$langs[] = $tranche;
						}
						//pre_dump($col_headers);
					} else { // sinon on traite
						 
						$bIsFirstLine = false;
						//echo "<br />avant explode".$sRawLigne."<br><br>";

						$skip = false;
						$aLigne = explode(";",$sRawLigne);
						if ($bImportIds) {
							if ($aLigne[0] > 0) {
								// if we do have a valid ID in first column 
								if ($bImportRefs) {
									// check wether reference chain exists and wether it matches given ID
									$ref_id = $translator->getTextID($aLigne[$firstpos]);
									if ($ref_id <= 0) {
										$aImportAlert[] = "Reference chain given at row ".$cnt_line." does not exist in database. Row was not processed.";
										$skip = true;
									} elseif ($aLigne[0] != $ref_id) {
										$aImportAlert[] = "Reference ID and reference chain given at row ".$cnt_line." do not match. Row was not processed.";
										$skip = true;
									}
								} elseif (!$translator->isText($aLigne[0])) {
									$aImportAlert[] = "Reference ID (".$aLigne[0].") given at row ".$cnt_line." does not exist in database. Row was not processed.";
									$skip = true;
								}
							} else {
								$aImportAlert[] = "Reference ID (".$aLigne[0].") given at row ".$cnt_line." is invalid. Row was not processed.";
								$skip = true;
							}
						}

						if (!$skip) {
							//pre_dump($aLigne);
							$translations = Array();
							for ($iLigne=$firstpos; $iLigne < count($aLigne); $iLigne++) {
								if ($iLigne == $firstpos) {
									// skip second column if ID/reference was already checked
									if ($bImportIds)
										$reference = $translator->getByID($aLigne[$iLigne]);
									else {
										$reference = $aLigne[$iLigne];
										// skip empty reference chain
										if (trim($reference == ''))
											break;
										// process reference
										$ref_id = $translator->getTextID($reference);
										if ($ref_id > 0) {
											// reference exists
											$aImportReport[] = "Row ".$cnt_line." : Reference '<i>".makeShortText($reference)."</i>' already exist in database.";
											// checks wether database version of reference is jerky and may be cleaned
											if (trim($reference) != $reference) {
												$test = $translator->getTextID(trim($reference));
												if ($test > 0) {
													// better keep both references as records may need them
												} else {
													$translator->updateText($ref_id, trim($reference));
													$aImportReport[] = "<span style=\"color: red;\">Row ".$cnt_line." : Reference '<i>".makeShortText($reference)."</i>' in database was jerky ans was trimmed.</span>";
													$reference = trim($reference);
												}
											}
										} else {
											// test if jerky version of reference was given
											$ref_id = $translator->getTextID(trim($reference));
											if ($ref_id > 0) {
												$aImportReport[] = "<span style=\"color: red;\">Row ".$cnt_line." : Reference '<i>".makeShortText($reference)."</i>' was jerky so database version was used.</span>";
												$reference = trim($reference);
											} else {
												// create reference
												$translator->addReference($reference, false);
												$aImportReport[] = "<span style=\"color: green;\">Row ".$cnt_line." : Reference '<i>".makeShortText($reference)."</i>' was created in database.</span>";
												$eImportSucces++;
											}
										}
									}
									continue;
								}
								// if extra columns exist for translations
								$translations[$rev_langpile[trim($col_headers[$iLigne])]] = $aLigne[$iLigne];
							}
							if (!empty($translations)) {
								$ref_id = $translator->addTranslation($reference, $translations);
								if ($ref_id > 0) {
									$aImportReport[] = "Row ".$cnt_line." : Translations (".implode(', ', $langs).") for reference '<i>".makeShortText($reference)."</i>' were created in database.";
									$eImportSucces++;
								}
							}
						}
						//echo "Insert entrée id ".$eRes."<br>";
					} // skip line 1
					$cnt_line++;
				}// fin while
			}
			// Fin traitement CSV
		}

		$status .= "Suppression du fichier après importation OK <br /><br />";
		$status = "<span class=\"arbo2\"><strong>".$status."</strong></span>";

		if (is_file($_SERVER['DOCUMENT_ROOT'].DEF_ROOT_IMPORTREPORT."export_inscrit_doublon.csv"))
			unlink($_SERVER['DOCUMENT_ROOT'].DEF_ROOT_IMPORTREPORT."export_inscrit_doublon.csv");
		if (is_file($_SERVER['DOCUMENT_ROOT'].DEF_ROOT_IMPORTREPORT."export_inscrit_syntaxe.csv"))
			unlink($_SERVER['DOCUMENT_ROOT'].DEF_ROOT_IMPORTREPORT."export_inscrit_syntaxe.csv");

		// resultat de l'import
		//--------------------------------------------
		echo "<table  border=\"0\" cellpadding=\"10\" cellspacing=\"0\" class=\"arbo\" width=\"600\" bgcolor=\"#cccccc\">";
		echo "<tr><td>"; 
		echo "<span class=\"arbo2\"><strong>Résultats de l'import : </strong></span><br><br>";
		echo "<span class=\"arbo2\">".$eImportSucces." imports réussis</span><br><br>";	
		if (!empty($aImportAlert))
			echo "<span class=\"arbo2\" style=\"text-transform: none;\">ALERTES :<br/><span class=\"arbo\" style=\"font-weight: normal;\">".implode('<br/>', $aImportAlert)."</span><br/><br/></span>";	
		if (!empty($aImportReport))
			echo "<span class=\"arbo2\" style=\"text-transform: none;\">RAPPORTS :<br/><span class=\"arbo\" style=\"font-weight: normal;\">".implode('<br/>', $aImportReport)."</span><br/><br/></span>";	
		echo "</td></tr></table>";
		// ----------------------------------------------

	} else	$status = "<span class=\"alert\"><strong>Erreur lors de du téléchargement du fichier</strong></span>";
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
<p class="arbo"><span class="arbo2"><strong>Import d'un fichier de données</strong></span><br />
<br /><br />
Le structures de documents suivantes sont traitées :
<br/>
<ul class="arbo">
	<li>Liste simple de chaines de référence (langue par défaut, les entête de colonne en première ligne peuvent être absents).</li>
	<li>Liste d'IDs et chaines de références (une vérification ID/chaîne est effectuée, les entêtes peuvent être absents).</li>
	<li>Chaines de références suivies des traductions (entêtes de colonne obligatoires, vérification ID/chaîne si une première colonne IDs est présente).</li>
</ul>
<br/>
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
   <td nowrap="nowrap" align="center" style="color: red;">!!! purger les chaines de référence avant l'import </td>
   <td nowrap="nowrap" align="left"><input type="checkbox" id="dopurge_refs" name="dopurge_refs" value="1" <?php echo printCheck($_GET["dopurge_refs"])?> /></td>
 </tr>
 <tr valign="middle" bgcolor="#CCCCCC">
   <td nowrap="nowrap" align="center">purger les chaines de traduction avant l'import </td>
   <td nowrap="nowrap" align="left"><input type="checkbox" id="dopurge_trad" name="dopurge_trad" value="1" <?php echo printCheck($_GET["dopurge_refs"])?> /></td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
   <td nowrap align="center">ignorer la premi&egrave;re ligne (code des langues) </td>
   <td nowrap align="left"><input type="checkbox" id="ignoreline1" name="ignoreline1" value="1" <?php echo printCheck($_POST["ignoreline1"])?> /></td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
   <td nowrap align="center">la toute premi&egrave;re colonne contient les ids </td>
   <td nowrap align="left"><input type="checkbox" id="importids" name="importids" value="1" <?php echo printCheck($_POST["importids"])?> /></td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
   <td nowrap align="center">la premi&egrave;re colonne de texte contient les chaines de référence </td>
   <td nowrap align="left"><input type="checkbox" id="importrefs" name="importrefs" value="1" <?php echo printCheck($_POST["importrefs"])?> /></td>
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
   <td nowrap="nowrap" align="center" style="color: red;">!!! purger les chaines de référence avant l'import </td>
   <td nowrap="nowrap" align="left"><input type="checkbox" id="dopurge_refs" name="dopurge_refs" value="1" <?php echo printCheck($_GET["dopurge_refs"])?> /></td>
 </tr>
 <tr valign="middle" bgcolor="#CCCCCC">
   <td nowrap="nowrap" align="center">purger les chaines de traduction avant l'import </td>
   <td nowrap="nowrap" align="left"><input type="checkbox" id="dopurge_trad" name="dopurge_trad" value="1" <?php echo printCheck($_GET["dopurge_refs"])?> /></td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
   <td nowrap align="center">ignorer la premi&egrave;re ligne (code des langues) </td>
   <td nowrap align="left"><input type="checkbox" id="ignoreline1" name="ignoreline1" value="1" <?php echo printCheck($_GET["ignoreline1"])?> /></td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
   <td nowrap align="center">la toute premi&egrave;re colonne contient les ids</td>
   <td nowrap align="left"><input type="checkbox" id="importids" name="importids" value="1" <?php echo printCheck($_GET["importids"])?> /></td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
   <td nowrap align="center">la premi&egrave;re colonne de texte contient les chaines de référence</td>
   <td nowrap align="left"><input type="checkbox" id="importrefs" name="importrefs" value="1" <?php echo printCheck($_GET["importrefs"])?> /></td>
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

if (is_file($saveFile))
	unlink($saveFile);


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