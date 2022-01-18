<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

include_once("cms-inc/include_cms.php");
include_once("cms-inc/include_class.php");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');
 
if ((isset($_REQUEST['refer'])) and ($_REQUEST['refer'] != "")){
	$referUrl = $_REQUEST['refer'];
}
else{
	$referUrl = $_SERVER['PHP_SELF']; 
}

$idSite = path2idside($db, $referUrl); 

$site = new Cms_site($idSite);

// définition de la class et ce qui va avec
 

if ($classeName!="" && $fieldName!="") {
	eval("$"."oRes = new ".$classeName."();");
	
	global $stack;
	global $db;
	
	$sXML = $oRes->XML;
	xmlStringParse($sXML);
	$classePrefixe = $stack[0]["attrs"]["PREFIX"];
	/*$aNodeToSort = $stack[0]["children"];
	$isStatut = false;
	for ($i=0;$i<count($aNodeToSort);$i++){
		if ($aNodeToSort[$i]["name"] == "ITEM"){
			if ($aNodeToSort[$i]["attrs"]["NAME"] == "statut"){
				 $isStatut = true;	
			}	
		}
	}*/
	
	if (method_exists($oRes, 'get_statut')) $isStatut = true;	
	else $isStatut = false;	
	
	$dataName = $classeName."_".$classePrefixe."_".str_replace(",", "_", $fieldName);
	
	$aRecherche = array();
	$aGetterOrderBy = array();
	$aGetterSensOrderBy = array();
	
	$aFieldName = explode(",", $fieldName);
	$aJoinData = array();
	 
	foreach ($aFieldName as $sFieldName) {
		$sql = "SELECT distinct ".$classeName.".".$classePrefixe."_".$sFieldName."  AS ".strtoupper($sFieldName); 
		if ($isStatut) {
			$oRech = new dbRecherche();
			$oRech->setValeurRecherche("declencher_recherche");
			if ($classeTemp=="")  $oRech->setTableBD($classeName.""); 
			else $oRech->setTableBD($classeTemp);
			$oRech->setJointureBD(" ".$classePrefixe."_statut = ".DEF_ID_STATUT_LIGNE." ".$sCondition);
			$oRech->setPureJointure(1);
			$aRecherche[] = $oRech;
		}	
		 
		$sql.= dbMakeRequeteWithCriteres2($classeName, $aRecherche, $aGetterOrderBy, $aGetterSensOrderBy);   
		//$sql.= "LIMIT 0,500";
		$res = $db->Execute($sql);
		if ($res) {
			$res->MoveFirst();	  
			while(!$res->EOF) {
				$row = $res->fields; 
				array_push($aJoinData, trim(str_replace(array('"',',', "\n", "\r", '\\'), array('\"','', ' ', ' ', ''), $row[strtoupper($sFieldName)])));
				$res->MoveNext();	  
			}
		}
	}  
	
	
	$aJoinData = array_unique($aJoinData);
	
	//echo $sql;
	//echo newSizeOf($aJoinData);
	$sData = "var ".$dataName." = [\"".join ("\" , \"", $aJoinData )."\"];";
  
	
 	// creation du fichier js avec les données de la classe et champ demander
	if (!is_dir($_SERVER['DOCUMENT_ROOT']."/custom/js"))  mkdir ($_SERVER['DOCUMENT_ROOT']."/custom/js", 0777);
	$jsFile = $_SERVER['DOCUMENT_ROOT']."/custom/js/".$site->get_rep()."/".$dataName."_autocomplete_data.js";
	$js = fopen($jsFile, "w"); 
	if ($js){ 
		fwrite($js, $sData);
		fclose($js);	
	}
	
	
	//creation fichier css contenant les styles de l'autocomplete
	if (!is_dir($_SERVER['DOCUMENT_ROOT']."/custom/css"))  mkdir ($_SERVER['DOCUMENT_ROOT']."/custom/css", 0777);
	$cssFile = $_SERVER['DOCUMENT_ROOT']."/backoffice/cms/lib/jsautocomplete/jquery.autocomplete.css"; 
	$cssFilenew = $_SERVER['DOCUMENT_ROOT']."/custom/css/jquery.autocomplete_".$site->get_rep().".css";
	if (!is_file($cssFilenew)){
		if (!copy($cssFile, $cssFilenew)) {
		   echo "failed to copy ".$cssFile."...\n";
		}
	}
  

	?>
	<script type="text/javascript" src="/backoffice/cms/lib/jsautocomplete/jquery.js"></script> 
	<script type='text/javascript' src='/backoffice/cms/lib/jsautocomplete/jquery.bgiframe.js'></script> 	
	<script type='text/javascript' src='/backoffice/cms/lib/jsautocomplete/jquery.autocomplete.js'></script>
	<script type='text/javascript' src='/custom/js/<?php echo $site->get_rep(); ?>/<?php echo $dataName; ?>_autocomplete_data.js'></script> 
	<link rel="stylesheet" type="text/css" href="/custom/css/jquery.autocomplete_<?php echo $site->get_rep(); ?>.css" /> 
		
	<script type="text/javascript">
	$().ready(function() {
	
		function findValueCallback(event, data, formatted) {
			$("<li>").html( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
		} 
		
		$("#<?php echo $classeName."_".str_replace(",", "_", $fieldName); ?>").autocomplete({data:<?php echo $dataName; ?><?php echo $param_autocomplete; ?>}); 
		 /*$(":text, textarea").result(findValueCallback).next().click(function() {
			$(this).prev().search();
		})*/
		
		
	});
	
	 
	</script>

	<input type="text" id="<?php echo $classeName."_".str_replace(",", "_", $fieldName); ?>" name="<?php echo $classeName."_".str_replace(",", "_", $fieldName); ?>" value="<?php echo eval("echo $".$classeName."_".str_replace(",", "_", $fieldName)."_value;"); ?>"/> 
				<!--<input type="button" value="Get Value" /> 
		 
			
			<input type="submit" value="Submit" /> 
		<h3>Result:</h3> <ol id="result"></ol>-->

<?php

}


?>