<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');  
include_once('backoffice/cms/site/minisite.inc.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');

$script = explode('/',$_SERVER['PHP_SELF']);
$script = $script[sizeof($script)-1];


if (!$isMinisite) {
	echo "<style type=\"text/css\">
	td.menu_td, #nav, #arborescence, .ariane, #help {display: block;}
	#managetree { margin-left:  0px; }  
	#add_cms_form { margin-left: 0px; }  
	
	#actionDel, #actionDupli {display:none};
	</style>";
}	

 
 

// en cas de suppression de ministe, on supprime les noeuds d'arbo associés
if ($_POST["operation"] == "DELETE" && $_POST["id"]!= ''){
	$oMinisite = new Cms_minisite ($_POST["id"]);
	$id_node = $oMinisite->get_node(); 
	deleteNode($_SESSION['idSite_travail'], $db, $id_node) ;

}



$aCustom = array();
$aCustom["JS"] = " // js
function geturl(id){
		document.##classePrefixe##_list_form.display.value = id;
		document.##classePrefixe##_list_form.actiontodo.value = \"GETURL\";
		document.##classePrefixe##_list_form.action = \"geturl_cms_minisite.php?display=\"+id+\"&\";
		//document.##classePrefixe##_list_form.action = \"geturl_cms_minisite.php\";
		document.##classePrefixe##_list_form.submit();
}
 ";
 

$aCustom["Action"] = "<a href=\"javascript:geturl(##id##);\" title=\"Get URL\"><img src=\"/backoffice/cms/img/link.gif\"  alt=\"Get URL\" border=\"0\" /></a>";

include('cms-inc/autoClass/list.php');
?>