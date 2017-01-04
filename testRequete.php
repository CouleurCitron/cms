<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

$rq = " SELECT cms_content.id_content, cms_content.name_content, cms_arbo_pages.node_absolute_path_name, cms_arbo_pages.node_id, cms_content.type_content, cms_archi_content.statut_archi AS statut, cms_content.statut_content AS statut_content, cms_archi_content.version_archi , cms_site.cms_name, cms_site.cms_id, cms_content.width_content, cms_content.height_content, date_format(date_archi, '%d/%m/%Y %H:%i:%s') AS \"date_archi\", cms_page.id_page, cms_page.name_page, cms_page.toutenligne_page, cms_page.existeligne_page FROM cms_content, cms_site, cms_archi_content, cms_arbo_pages, cms_struct_page, cms_page WHERE cms_content.id_content = 148 AND cms_content.id_site=cms_site.cms_id AND cms_archi_content.id_content_archi=cms_content.id_content AND cms_archi_content.statut_archi=4 AND cms_content.nodeid_content=cms_arbo_pages.node_id AND cms_content.id_content=cms_struct_page.id_content AND cms_struct_page.id_page=cms_page.id_page;";

$rqs = explode("WHERE", $rq);

$select = $rqs[0];

$wheres = explode("AND", $rqs[1]);

foreach($wheres as $key => $whereReq){
	echo "--<br />\n";
	$testReq =  $select." WHERE ".str_replace($whereReq, " 1 = 1 ", $rqs[1]);
	echo $testReq."<br />\n";
	$aRes = dbGetArrayOneFieldFromRequete($testReq);
	if (count($aRes) == 0){
		echo "<strong>le problème n'est pas ".$whereReq."</strong><br />\n";
	}
	else{
		echo "<strong style=\"color:red\">le problème est peut-être ".$whereReq."</strong><br />\n";
	}
}


	


?>