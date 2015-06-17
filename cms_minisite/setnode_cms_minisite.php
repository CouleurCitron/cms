<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/utils/dir.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
include_once('backoffice/cms/site/minisite.inc.php');

activateMenu('gestionminisite'); 

if (!$isMinisite) {
	echo "<style type=\"text/css\">
	td.menu_td, #nav, #arborescence, .ariane, #help {display: block;}
	#managetree { margin-left:  0px; }  
	#add_cms_form { margin-left: 0px; }  
	</style>";
}	
 
//creation du noeud d'arbo
if (is_get("id")) {

	$id = $_GET["id"];

	$oMinisite = new Cms_minisite ($id);
	$name = $oMinisite->get_name();
	
	$name = str_replace('"',"'\\'", $name); // On remplace les guillemets par des doubles quotes
	$name = str_replace('`',"'", $name); // On remplace les quotes zarbi
	$name = stripslashes(removeForbiddenChars($name));
	
	// site connecté
	if ($idSite == "") $idSite = $_SESSION['idSite_travail'];
	
	// pas de site
	if ($idSite == "") die ("Appel incorrect de la fonctionnalité");
	  
	$virtualPath = 0;
	
	$nodeInfos=array();
	 
	
	$nodeInfos=getNodeInfos($db, $virtualPath);
  
	$aArboPages = dbGetObjectsFromFieldValue2("cms_arbo_pages", 
		array("getNode_libelle", "getId_site") , 
		array($name , $idSite) , array(), array());
	 
	if (sizeof($aArboPages ) == 0) {
		$id_node = addNode($idSite, $db, $virtualPath, $name);
		$oNode = new Cms_arbo_pages ($id_node);
		$oMinisite->set_node($id_node);
	}
	else {
		$oNode = new Cms_arbo_pages ($oMinisite->get_node()); 
		$id_node = $oMinisite->get_node();  
		$oMinisite->set_node($id_node);
	}
	
	
	$oR = dbUpdate ($oMinisite);
	if ($oR) {
		if (sizeof($aArboPages ) == 0) {
			echo "<span class=\"arbo\">Le minisite &quot;<strong>".$oMinisite->get_name()."</strong>&quot; a été créé avec succès.</span><br /><br />";
		}
		else  {
			echo "<span class=\"arbo\">Le minisite &quot;<strong>".$oMinisite->get_name()."</strong>&quot; a été modifié avec succès.</span><br /><br />";

			echo '<b><span class="arbo">Pour prendre en compte les modifications du thème : <a href="/backoffice/cms/regenerateAll.php?menuOpen=true" target="_parent">regénérer</a></span></b><br /><br />';
		}
		echo "<span class=\"arbo\"><a href=\"list_cms_minisite.php\">Retour à la liste de minisite</a><br /></span><br /><br />";
	}
	else echo "<span class=\"arbo\">Erreur lors de la création du minisite.</span><br /><br />";
	
	
	
	// checke les thèmes des pages 
	$r = updateThemePageByNode($id_node, $oMinisite->get_theme());
	
		
	
	
	if (!is_dir($_SERVER['DOCUMENT_ROOT']."/content/".$oNode->getAbsolute_path_name())){
		mkdir($_SERVER['DOCUMENT_ROOT']."/content/".$oNode->getAbsolute_path_name());
	}
	  
	
	/*if (is_get("xid")) {
	 
		$oMinisite_old = new Cms_minisite ($_GET["display"]);
		$node_id_to_duplicate = $oMinisite_old->get_node();  
		$node_duplicated = $id_node; 
		include_once ($_SERVER['DOCUMENT_ROOT']."/backoffice/cms/site/dupliMinisiteProcess.php");  
	}*/
	
}


else {
	echo "Minisite inexistant"; 
}
 
 
// supprimer un noeud
function updateThemePageByNode($virtualPath, $theme){
	global $CMS_ROOT;
	global $db; 
	
	if( ($virtualPath=='0') || (strlen($virtualPath)=='0'))
		return false;
	$array_path = split(',',$virtualPath);
	$node_id = array_pop($array_path);
	 
	
	$sql = "SELECT * from cms_page where nodeid_page = ".$node_id.";";	
	$aPages = dbGetObjectsFromRequete("Cms_page", $sql);
	
	if (count($aPages) > 0){
		foreach ($aPages as $page) {
			if ( $page->get_theme() != $theme) {
				$page->set_theme($theme);
				dbUpdate ($page); 
				//echo "UPDATE cms_page theme;<br />\n";
				//echo "SELECT * FROM cms_infos_pages WHERE page_id = ".$infos_pages->page_id.";<br />\n";
				//echo "<br />\n";
			}
		}
	}
	else{
		//echo "ok<br />\n<br />\n";
	}

	$children=getNodeChildren($idSite, $db, $virtualPath);

	foreach($children as $k => $child) {
		if (updateThemePageByNode($virtualPath.','.$child['id'])==false) {
			error_log("Impossible de supprimer le dossier id=$child");
		}
	} 

}

?>