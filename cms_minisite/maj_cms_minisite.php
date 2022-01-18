 <?php

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');  
include_once('backoffice/cms/site/minisite.inc.php');
 
if ($_GET["id"]!='') $redirString = '/backoffice/cms/cms_minisite/setnode_cms_minisite.php?xid='.$_GET["id"]; 
else $redirString = '/backoffice/cms/cms_minisite/setnode_cms_minisite.php?xid='.$_POST["id"]; 

$actiontodo = ( $_POST['actiontodo']!="SAUVE" ) ? "MODIF" : "SAUVE" ;

if (!$isMinisite) {
	echo "<style type=\"text/css\">
	td.menu_td, #nav, #arborescence, .ariane, #help {display: block;}
	#managetree { margin-left:  0px; }  
	#add_cms_form { margin-left: 0px; }  
	</style>";
}	
 
 
if ($actiontodo == 'SAUVE') { 
	if ($_POST["id"] == -1) {
		// on teste le nom 
		$name = $_POST["fCms_name"]; 
		$aMS = dbGetObjectsFromFieldValue("cms_minisite", array('get_name', 'get_site' ),  array($name, $_SESSION['idSite']), NULL);
	
		if (newSizeOf($aMS) > 0) {
			echo "<p>Le nom du minisite existe déjà, merci de le modifier <br /></p>";
			echo "<p><a href=\"list_cms_minisite.php\">Retour à liste de minisite</a><br /></p>";
			$oMS = $aMS[0]; 
		}
		else {
			include('cms-inc/autoClass/maj.php'); 
		}
		
	}  
	else {
	
		
		include('cms-inc/autoClass/maj.php'); 
	}

}
else {
	include('cms-inc/autoClass/maj.php'); 
}

?>