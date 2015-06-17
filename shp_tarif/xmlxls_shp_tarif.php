<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
// include('cms-inc/autoClass/xmlxls.php');

if (!isset($classeName)){
	$classeName = preg_replace('/[^_]*_(.*)\.php/si', '$1', basename($_SERVER['PHP_SELF']));
}

//------------------------------------------------------------------------------------------------------

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/lib.inc.php');

$bDebug = false;
$sMessage='';

// objet 
eval('$'.'oRes = new '.$classeName.'();');

$sXML = $oRes->XML;

xmlStringParse($sXML);

$classeName = $stack[0]['attrs']['NAME'];
$classePrefixe = $stack[0]['attrs']['PREFIX'];
$aNodeToSort = $stack[0]['children'];
$statusGetter = $oRes->getGetterStatut();

//////////////////////////
// recherche par statut
//////////////////////////
if (isset($_POST['eStatut'])){
	$eStatut=$_POST['eStatut'];
	$_SESSION['eStatut']=$eStatut;
}
if($eStatut==''){
	$eStatut=$_SESSION['eStatut'];
}

if (($eStatut != -1) && ($eStatut != '') && ($statusGetter != 'none')) {
	$sql= 'SELECT * FROM '.$classeName.' WHERE '.$classePrefixe.'_statut = '.$eStatut.';';
}
else{
	$sql= 'SELECT * FROM '.$classeName.';';
}

//paramètre 
 
$k=0;
while (isset($_GET['champ'.$k])&& $_GET['champ'.$k]!=""){ 
	if (isset($_GET['operateur'.$k]) && $_GET['operateur'.$k]!="" && isset($_GET['valeur'.$k]) && $_GET['valeur'.$k]!="") 
	
	
	$jointure = " ".$_GET['champ'.$k]." ".urldecode($_GET['operateur'.$k])." '".$_GET['valeur'.$k]."' ";
	if (eregi('where', $sql)){
		$sql = str_replace('where', 'where '.$jointure.' and ', $sql);
	}
	else{
		$sql = str_replace($classeName, $classeName.' where '.$jointure, $sql);
	}
	
	$k++;
}

$aListe_res = dbGetObjectsFromRequete($classeName, $sql);

header('Content-type: application/octet-stream; charset=utf-8');
//header('Content-type: text/xml; charset=utf-8');
header('Content-Disposition: attachment; filename="export_'.$classeName.'.xml"'); 
echo "<?xml version=\"1.0\""."?".">\n";
echo "<?mso-application progid=\"Excel.Sheet\""."?".">\n";
?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>Couleur Citron</Author>
  <LastAuthor>Couleur Citron</LastAuthor>
  <Created><?php echo date('Y-m-d').'T'.date('H:i:s').'Z'; ?></Created>
  <LastSaved><?php echo date('Y-m-d').'T'.date('H:i:s').'Z'; ?></LastSaved>
  <Company>Couleur Citron</Company>
  <Version>12.00</Version>
 </DocumentProperties>
 <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
  <WindowHeight>8775</WindowHeight>
  <WindowWidth>16320</WindowWidth>
  <WindowTopX>240</WindowTopX>
  <WindowTopY>75</WindowTopY>
  <ProtectStructure>False</ProtectStructure>
  <ProtectWindows>False</ProtectWindows>
 </ExcelWorkbook>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Bottom"/>
   <Borders/>
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="s62">
   <Alignment ss:Vertical="Bottom" ss:WrapText="1"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="Feuil1">
  <Table ss:ExpandedColumnCount="<?php echo count($aNodeToSort); ?>" ss:ExpandedRowCount="<?php echo (sizeof($aListe_res)+1); ?>" x:FullColumns="1"
   x:FullRows="1" ss:DefaultColumnWidth="60" ss:DefaultRowHeight="15">   
<?php

$search = array();
//$search = getExtendedAsciiTable(128, 255);
$search[] = 'apos;';
$search[] = 'quot;';
$search[] = '&';
$search[] = chr(10);
$search[] = '<';
$destroy = array();
//$destroy = getExtendedAsciiTableUTF8(128, 255);
$destroy[] = "'";
$destroy[] = '"';
$destroy[] = '&amp;';
$destroy[] = "&#10;";
$destroy[] = "&lt;";
				
echo "<Row>\n"; 
for ($i=0;$i<count($aNodeToSort);$i++){
	if ($aNodeToSort[$i]['name'] == 'ITEM'){	
		echo '<Cell><Data ss:Type="String">';
		echo $aNodeToSort[$i]['attrs']['NAME'];
		echo "</Data></Cell>\n";					

	}
}
echo "</Row>\n"; 

if(sizeof($aListe_res)>0) {
	// liste
	for($k=0; $k<sizeof($aListe_res); $k++) {
		$oRes = $aListe_res[$k];
		
		echo "<Row>\n";
	
	    for ($i=0;$i<count($aNodeToSort);$i++){    	
		
			if ($aNodeToSort[$i]['name'] == 'ITEM'){			 
				//$eKeyValue = getItemValue($oRes, $aNodeToSort[$i]["attrs"]["NAME"]);
				$eKeyValue = trim(call_user_func(array($oRes, 'get_'.$aNodeToSort[$i]['attrs']['NAME'])));
				//$eKeyValue = getItemValue($oRes, $aNodeToSort[$i]["attrs"]["NAME"]);
				/*
				if ($aNodeToSort[$i]["attrs"]["FKEY"]){ // cas de foregin key
					$sTempClasse = $aNodeToSort[$i]["attrs"]["FKEY"];
					if ($eKeyValue > -1){
						eval("$"."oTemp = new ".$sTempClasse."(".$eKeyValue.");"); 
						//$eKeyValue = getItemValue($oTemp, $oTemp->getDisplay()); 
						$eKeyValue = trim(call_user_func(array($oTemp, 'get_'.$oTemp->getDisplay())));
					}
					else{
						$eKeyValue= "n/a";
					}
				}
				else if ($aNodeToSort[$i]["attrs"]["NAME"] == "statut"){ // cas statut
					$eKeyValue=  lib($eKeyValue);
				}*/
				
			
			//$eKeyValue = getItemValue($oRes, $eKeyValue);	
				/*
				// statut SID
				if ($aNodeToSort[$i]['attrs']['NAME'] == 'statut'){
					switch ((int)intval($eKeyValue)) {
					case -1:
						$eKeyValue = 'en attente';
						break;
					case 0:
						$eKeyValue = 'en attente';
						break;
					case 1:
						$eKeyValue = '1er log - brouillon';
						break;
					case 2:
						$eKeyValue = '1er log - complet';
						break;
					case 3:
						$eKeyValue = 'retour';
						break;
					case 4:
						$eKeyValue = 'retour - complet';
						break;
					case 5:
						$eKeyValue = 'en ligne';
						break;
					case 6:
						$eKeyValue = 'archive';
						break;
					}  // fin switch
				
				} // fin if statut
				*/
				
				/*
				// cp
				elseif ($aNodeToSort[$i]['attrs']['NAME'] == 'cp'){						
					if (strlen($eKeyValue) == 4){ // zero fill
						$eKeyValue = '0'.$eKeyValue;
					}
				} // fin if cp
				*/
				
				/*
				// champs oblig vides
				elseif (($eKeyValue == '-') || ($eKeyValue == '0') || ($eKeyValue == '-1')){
					$eKeyValue = '';
				}
				*/				
				
				echo '<Cell><Data ss:Type="String">';				
				echo utf8_encode(stripslashes(str_replace($search, $destroy, $eKeyValue)));				
				echo "</Data></Cell>\n";
			}// fin if
		}// fin for cols
		echo "</Row>\n";
	 
	} // fin for rows

} // fin if
?>
  </Table>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <PageSetup>
    <Header x:Margin="0.3"/>
    <Footer x:Margin="0.3"/>
    <PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
   </PageSetup>
   <Selected/>
   <Panes>
    <Pane>
     <Number>1</Number>
     <ActiveRow>1</ActiveRow>
     <ActiveCol>1</ActiveCol>
    </Pane>
   </Panes>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
</Workbook>