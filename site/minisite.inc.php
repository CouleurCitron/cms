<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

$isMinisite = false;

if (is_get("source")) {
	$param = "&source=".$_GET["source"]; 
	$isMinisite = true;
	
	if (is_get("v_comp_path")) {
		$virtualPath = $_GET["v_comp_path"];
		$node_id = array_pop(split(',',$virtualPath));
		$aMS = dbGetObjectsFromFieldValue("cms_minisite", array('get_site', 'get_node'), array($idSite, $node_id), NULL);
		
		
		
		if (sizeof($aMS) > 0) {
			$oMinisite = $aMS[0]; 
			$id_minisite = $oMinisite->get_id();
		}
		
	}
	

} 

	echo "<style type=\"text/css\"> 
	td.menu_td, #nav, #arborescence, .ariane, #help {display: none;}
	#managetree { margin-left: 0px; }  
	#add_cms_form { margin-left: -250px; }  
	</style>";
	 

function renameMinisite($idSite, $virtualPath, $old_libelle, $new_libelle){ 
 	
	$node_id = array_pop(split(',',$virtualPath));
	
	$aMS = dbGetObjectsFromFieldValue("cms_minisite", array('get_name', 'get_site', 'get_node'), array($old_libelle, $idSite, $node_id), NULL);

	if (sizeof($aMS) > 0) {
		$oMS = $aMS[0];
		$oMS->set_name($new_libelle);
		return (dbUpdate ($oMS));
	}
	else {
		return false;
	}
	
}



function deleteMinisite($idSite, $virtualPath){ 
 	
	$node_id = array_pop(split(',',$virtualPath)); 
	
	$aMS = dbGetObjectsFromFieldValue("cms_minisite", array('get_site', 'get_node'), array($idSite, $node_id), NULL);

	if (sizeof($aMS) > 0) {
		$oMS = $aMS[0];  
		return (dbDelete ($oMS) );
	}
	else { 
		return false;
	}
	
}

function saveNodeDescriptionMinisite($idSite, $folderdescription, $virtualPath) {
 	
	$node_id = array_pop(split(',',$virtualPath)); 
	
	$aMS = dbGetObjectsFromFieldValue("cms_minisite", array('get_site', 'get_node'), array($idSite, $node_id), NULL);

	if (sizeof($aMS) > 0) {
		$oMS = $aMS[0];  
		$oMS->set_desc($folderdescription);
		return (dbUpdate ($oMS));
	}
	else { 
		return false;
	}
	
} 

function moveNodeMinisite($idSite, $db,$virtualPath,$new_node) {
 	
	$node_id = array_pop(split(',',$virtualPath)); 
	
	$aMS = dbGetObjectsFromFieldValue("cms_minisite", array('get_site', 'get_node'), array($idSite, $node_id), NULL);

	if (sizeof($aMS) > 0) {
		$oMS = $aMS[0];  
		$oMS->set_node($new_node);
		return (dbUpdate ($oMS));
	}
	else { 
		return false;
	}
	
}
 
?>