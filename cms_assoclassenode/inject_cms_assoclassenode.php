<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');
//init_set ('error_reporting', E_ALL);
	
// permet de dérouler le menu contextuellement
activateMenu("gestioncms_assoclassenode");

if (!isset($classeName)){
	$classeName = preg_replace('/[^_]*_(.*)\.php/si', '$1', basename($_SERVER['PHP_SELF']));
}
//------------------------------------------------------------------------------------------------------

// permet de drouler le menu contextuellement
if (strpos($_SERVER['PHP_SELF'], "backoffice") !== false){
	activateMenu("gestion".$classeName); 
}

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/htmlUtility.php'); 
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/lib.inc.php');

// objet 
eval("$"."oRes = new ".$classeName."();");

$sXML = $oRes->XML;
xmlStringParse($sXML);

$classeName = $stack[0]["attrs"]["NAME"];
$classePrefixe = $stack[0]["attrs"]["PREFIX"];
$aNodeToSort = $stack[0]["children"];

if (is_post('level')&&is_post('libelle')){
	$level = $_POST['level'];
	$libelle = $_POST['libelle'];
}

?>
<form id="dupli" name="dupli" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
injecter des noeuds au niveau :<br />
<input type="text" value="<?php echo $libelle; ?>" id="libelle" name="libelle" />
<select id="level" name="level">
<?php

	for($i=0;$i<10;$i++){
		if ($i == $level){
			$selected = " selected ";
		}
		else{
			$selected = "";
		}
		echo "<option value=\"".$i."\"".$selected.">".$i."</option>\n";
	}

?>
</select>
<input type="submit" />
</form>
<hr />
<?php
if (is_post('level')&&is_post('libelle')){
	$libelle = $_POST['libelle'];
	$level = $_POST['level'];
	
	echo 'injecter "'.$libelle.'" sous le niveau : '.$level;
	
	if ($level==0){	// racine
		$sql = ' SELECT * FROM `cms_arbo_pages` WHERE `node_id` = 0;';
	}
	else{
		$aPattern = array();
		for ($i=0;$i<($level+2);$i++){
			$aPattern[]='/';		
		}
		$sPattern = implode ('%', $aPattern);		
		$sql = ' SELECT * FROM `cms_arbo_pages` WHERE `node_absolute_path_name` LIKE \''.$sPattern.'\' AND `node_absolute_path_name` NOT LIKE \''.$sPattern.'%/\' ;';	
	}	
	
	//echo $sql;	
	$aNodes = dbGetObjectsFromRequete('cms_arbo_pages', $sql);

	if ((count($aNodes) > 0)&&($aNodes!=false)){
		foreach($aNodes as $cKey => $oNode){
			//pre_dump($oNode);		
			
			addNode($oNode->get_id_site(), $db, $oNode->get_id(), $libelle);			
		
		}		
	}
}
?>