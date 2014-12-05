<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
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
$eImportGridSucces = 0;
$eImportValueSucces = 0;
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
	
if (isset($_FILES['importfile'])) {
	// Upload du fichier
	
	if (isset($_POST['shipper_id']) && $_POST['shipper_id'] > 0) {
		if(move_uploaded_file($_FILES['importfile']['tmp_name'], $saveFile) || is_file($_SERVER['DOCUMENT_ROOT'].$_GET['importfile'])){
			$status .= "Téléchargement du fichier : OK <br /><br />"; 
			if (ereg('xls', $ext)) {
				echo ' import de XLS<br />';
			} elseif (ereg('csv', $ext)) {
				echo ' import de CSV<br />';
			} else {
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
			} else {			
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
				dbExecuteQuery("TRUNCATE `shp_frais_port_grille`;");
				dbExecuteQuery("TRUNCATE `shp_frais_port_valeur`;");
			} else{
				// nada
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
			
			// remplir les données vides avec le précédent record ??
			if (isset($_POST["fillnull"]) && (intval($_POST["fillnull"]) == 1))
				$bFillNull = true;
			else	$bFillNull = false;

			//----------------------------------------------------------		
			
			if (ereg('xls', $ext)){
				include('import.xls.php');
			}
			elseif (ereg('csv', $ext)) {
				
				// Debut traitement CSV
				//echo "open file : ".$saveFile."<br/>";
				$fh = fopen($saveFile,'r'); 
				if ($fh) {
				//$lines = file($saveFile);
				//if (!empty($lines)) {
					//pre_dump(htmlentities($oRes->XML));
					$bIsFirstLine = true;
					$track_country = '';
					while(!feof($fh)) {
						$sRawLigne = fgets($fh);
						//echo "NEW LINE : ".$sRawLigne."<br/><br/>";
						if (($bIsFirstLine == true) && ($startLine == 1)) {
							// on skippe la premiere ligne si elle contient les noms des champs
							$bIsFirstLine = false;
							//echo "> skip first line<br/><br/>";
							$tranches = explode(";",$sRawLigne);
						} else { // sinon on traite
							 
							$bIsFirstLine = false;
							//echo "<br />avant explode".$sRawLigne."<br><br>";
							$aLigne = explode(";",$sRawLigne);
	
							//check row values (skip empty or imcomplete rows)
							$valid_row = false;
							for ($iLigne=1; $iLigne<count($aLigne); $iLigne++) {
								if (trim($aLigne[$iLigne]) != '') // && !empty(trim($aLigne[$iLigne]))
									$valid_row = true;
							}
							if (!$valid_row)
								continue;

							// process row values
							// viewArray($aLigne, 'test ligne');
							// Grids will then be created on the fly (so empty grid table first)
							unset($oInsert);
							$oInsert = new shp_frais_port_grille();
							$oInsert->set_id($next_id);
							$oInsert->set_id_transporteur($_POST['shipper_id']);
							$oInsert->set_id_pays($_POST['country_id']);
							$oInsert->set_type($_POST['grid_type']);
							$oInsert->set_match($_POST['match_type']);
							$oInsert->set_unite_poids($_POST['weight_unit']);
							$oInsert->set_zone($aLigne[0]);
							$oInsert->set_statut(4);
							$oInsert->set_cdate(date('Y-m-d H:i:s'));
							$oInsert->set_mdate(date('Y-m-d H:i:s'));
	                                                              
							$eRes = dbInsertWithAutoKey($oInsert);
							if ($eRes) {
								$eImportGridSucces++;
								$grille = $eRes;

								for ($iLigne=1; $iLigne<(count($aLigne)); $iLigne++) {
									// echo ">> ".$tranches[$iLigne]." - ".$aLigne[$iLigne]." >> " ;
									if (trim($tranches[$iLigne]) != '' && trim($aLigne[$iLigne]) != '') {
										$tranche = explode('_', $tranches[$iLigne]);
										$bound = explode('/', $tranche[1]);
										//viewArray($bound, $tranche[0]);
										unset($oInsert);
										$oInsert = new shp_frais_port_valeur();
										$oInsert->set_id($next_id);
										$oInsert->set_id_grille($grille);
										if ($tranche[0] == 'COEF')
											$oInsert->set_coef_poids('Y');
										else	$oInsert->set_coef_poids('N');
										$oInsert->set_minimum($bound[0]);
										$oInsert->set_maximum($bound[1]);
										$oInsert->set_valeur($aLigne[$iLigne]);
										$oInsert->set_statut(4);
										$oInsert->set_cdate(date('Y-m-d H:i:s'));
										$oInsert->set_mdate(date('Y-m-d H:i:s'));
	                                                		              	
										$eRes = dbInsertWithAutoKey($oInsert);
										$id = $eRes; 
										//echo "1. $id = dbInsertWithAutoKey ".$oRes->get_id()."<br>";
										// echo "inserted in grille {$grille} / min {$bound[0]} / max {$bound[1]} / value {$aLigne[$iLigne]}<br/>";
										if ($eRes)
											$eImportValueSucces++;
									}
									//echo "Insert entrée id ".$eRes."<br>";
								}
							}
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
			echo "<span class=\"arbo2\">".$eImportGridSucces." grille(s) créée(s)<br/> ".$eImportValueSucces." valeur(s) importée(s)</span><br><br>";	
			echo "</td></tr></table>";
			// ----------------------------------------------
			
			
		} else {
			$status = "<span class=\"alert\"><strong>Erreur lors de du téléchargement du fichier</strong></span>";
		}
	} else echo "Vous devez fournir une ID de transporteur à qui associer ces valeurs !!</br>";
}

// shippers
$shippers = Array();
$sql = "	SELECT	t.shp_tsp_id as id,
		t.shp_tsp_libelle as name
	FROM	`shp_transporteur` t
	WHERE	t.shp_tsp_statut=".DEF_ID_STATUT_LIGNE.";";

//echo $sql."<br/>";
$rs = $db->Execute($sql);
if ($rs) {
	while(!$rs->EOF) {
		$shippers[] = $rs->fields;
		$rs->MoveNext();
	}
}

// countries
include_once("cms-inc/account/class.AccountModel.php");
$model = new AccountModel();
$langpile = $model->getCountryPile('fr');

//viewArray($langpile);
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
<p>
<span class="export_template">
<img align="top" border="0" src="/backoffice/cms/img/go.gif" alt="Import">
<strong>
<a href="/backoffice/cms/utils/telecharger.php?file=frais_pays.zip&chemin=/backoffice/cms/shp_frais_port_grille/&">Télécharger l'ensemble des fichiers avec les frais de port de tous les pays</a>
</strong>
</span>
<br>
<br>
</p>
<table  border="1" cellpadding="5" cellspacing="0" class="arbo" width="600">
<form name="formFile" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

 <tr bgcolor="#CCCCCC" valign="middle">
  <td nowrap align="center">&nbsp;<b>S&eacute;lection du fichier</b>&nbsp;</td>
  <td nowrap align="left"><input type="file" name="importfile" id="importfile" class="arbo" pattern=".+(\.(csv|xls))" errorMsg="Vous devez sélectionner un fichier qui porte l'extension .csv ou .xls" value="*.csv"></td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
  <td nowrap align="center">&nbsp;<b>Transporteur</b> (Colis Postal : - 7 kg)&nbsp;</td>
  <td nowrap align="left"><select name="shipper_id" id="shipper_id">
<?php
	foreach ($shippers as $shipper)
		echo '<option value="'.$shipper['id'].'">'.$shipper['name'].'</option>';
?>
  	</select>
  </td></td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
  <td nowrap align="center">&nbsp;<b>Pays ciblé</b>&nbsp;</td>
  <td nowrap align="left">
  	<select name="country_id" id="country_id">
<?php
	foreach ($langpile as $language)
		echo '<option value="'.$language['id'].'">'.$language['name'].'</option>';
?>
  	</select>
  </td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
  <td nowrap align="center">&nbsp;<b>Type des grilles</b> (Choisir CHAR pour pays GB)&nbsp;</td>
  <td nowrap align="left"><select name="grid_type" id="grid_type" class="arbo" width="60"><option value=""> --- </option><option value="NUM">NUM</option><option value="CHAR">CHAR</option></select></td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
  <td nowrap align="center">&nbsp;<b>Match des valeurs</b> (Choisir PREFIX par défaut)&nbsp;</td>
  <td nowrap align="left"><select name="match_type" id="match_type" class="arbo" width="60"><option value=""> --- </option><option value="PREFIX">PREFIX</option><option value="FULL">FULL</option></select></td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
  <td nowrap align="center">&nbsp;<b>Unité de poids pour les valeurs</b>&nbsp;</td>
  <td nowrap align="left"><input type="text" name="weight_unit" id="wight_unit" class="arbo" width="60" value="kg"/></td>
 </tr>
 <tr valign="middle" bgcolor="#CCCCCC">
   <td nowrap="nowrap" align="center">purger avant l'import </td>
   <td nowrap="nowrap" align="left"><input type="checkbox" id="dopurge" name="dopurge" value="1" <?php echo printCheck($_POST["dopurge"]); ?> /></td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
   <td nowrap align="center">ignorer la premi&egrave;re ligne (nom des champs) </td>
   <td nowrap align="left"><input type="checkbox" id="ignoreline1" name="ignoreline1" value="1" <?php echo printCheck($_POST["ignoreline1"]); ?> /></td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
   <td nowrap align="center">la premi&egrave;re colonne contient les ids </td>
   <td nowrap align="left"><input type="checkbox" id="importids" name="importids" value="1" <?php echo printCheck($_POST["importids"]); ?> /></td>
 </tr>
 <tr valign="middle" bgcolor="#CCCCCC">
   <td nowrap="nowrap" align="center">remplir les champs vides avec les donn&eacute;es de la ligne pr&eacute;c&eacute;dente</td>
   <td nowrap="nowrap" align="left"><input type="checkbox" id="fillnull" name="fillnull" value="1" <?php echo printCheck($_POST["fillnull"]); ?> /></td>
 </tr>
 <tr valign="middle" bgcolor="#CCCCCC">
   <td nowrap="nowrap" align="center">signature de fin de fichier de type <b>FINaaaa-mm-jj</b></td>
   <td nowrap="nowrap" align="left"><input type="checkbox" id="signature" name="signature" value="1" <?php echo printCheck($_POST["signature"]); ?> /></td>
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
<form name="formFile" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">

 <tr bgcolor="#CCCCCC" valign="middle">
  <td width="388" align="center" nowrap>&nbsp;<b>S&eacute;lection du fichier</b>&nbsp;</td>
  <td width="186" align="left" nowrap><input type="text" name="importfile" id="importfile" class="arbo" size="35" value="<?php echo $_GET['importfile']; ?>"></td>
 </tr>
 <tr valign="middle" bgcolor="#CCCCCC">
   <td nowrap="nowrap" align="center">purger avant l'import </td>
   <td nowrap="nowrap" align="left"><input type="checkbox" id="dopurge" name="dopurge" value="1" <?php echo printCheck($_GET["dopurge"]); ?> /></td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
   <td nowrap align="center">ignorer la premi&egrave;re ligne (nom des champs) </td>
   <td nowrap align="left"><input type="checkbox" id="ignoreline1" name="ignoreline1" value="1" <?php echo printCheck($_GET["ignoreline1"]); ?> /></td>
 </tr>
 <tr bgcolor="#CCCCCC" valign="middle">
   <td nowrap align="center">la premi&egrave;re colonne contient les ids </td>
   <td nowrap align="left"><input type="checkbox" id="importids" name="importids" value="1" <?php echo printCheck($_GET["importids"]); ?> /></td>
 </tr>
 <tr valign="middle" bgcolor="#CCCCCC">
   <td nowrap="nowrap" align="center">remplir les champs vides avec les donn&eacute;es de la ligne pr&eacute;c&eacute;dente</td>
   <td nowrap="nowrap" align="left"><input type="checkbox" id="fillnull" name="fillnull" value="1" <?php echo printCheck($_GET["fillnull"]); ?> /></td>
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

if (is_file($saveFile)) {
	unlink($saveFile);

}

?>