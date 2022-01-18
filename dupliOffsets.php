<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
header("Content-type: text/xml; charset=utf-8");

include_once("config.php");
include_once("cms-inc/htmlUtility.php");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

// params -----------------------------------------------
if (is_get('site')){
	$idSte = $_GET['site'];
}
else{
	$idSte = 1;
}
// sets ---------------------------------------------------

echo "<"."?"."xml version=\"1.0\" encoding=\"UTF-8\""."?".">\n";
echo "<offsets>\n";

// purge cms_site
$lastId =  getNextVal("cms_site", "cms_id");
//$sql = "SELECT * FROM `cms_site` ORDER BY cms_id ASC;";
//$aSites= dbGetObjectsFromRequete("cms_site", $sql);
//$firstId = $aSites[0]->id_site;
//$offsetSiteId = $lastId-$firstId;
//echo "<siteid>".$offsetSiteId."</siteid>\n";
echo "<siteid>".$lastId."</siteid>\n";

// purge cms_arbo_pages
$lastId =  getNextVal("cms_arbo_pages", "node_id");
//$sql = "SELECT * FROM `cms_arbo_pages` ORDER BY node_id ASC;";
//$aNodes= dbGetObjectsFromRequete("cms_arbo_pages", $sql);
//$firstId = $aNodes[0]->node_id;
//$offsetNodeId = $lastId-$firstId;
//echo "<nodeid>".$offsetNodeId."</nodeid>\n";
echo "<nodeid>".$lastId."</nodeid>\n";

// purge cms_page
$lastId =  getNextVal("cms_page", "id_page");
//$sql = "SELECT * FROM `cms_page` ORDER BY id_page ASC;";
//$aPages = dbGetObjectsFromRequete("Cms_page", $sql);
//$firstId = $aPages[0]->id_page;
//$offsetPageId = $lastId-$firstId;
//echo "<pageid>".$offsetPageId."</pageid>\n";
echo "<pageid>".$lastId."</pageid>\n";

// purge cms_infos_pages
$lastId =  getNextVal("cms_infos_pages", "id");
//$sql = "SELECT * FROM `cms_infos_pages` ORDER BY id ASC;";
//$aInfos_pages = dbGetObjectsFromRequete("Cms_infos_page", $sql);
//$firstId = $aInfos_pages[0]->id;
//$offsetInfoPageId = $lastId-$firstId;
//echo "<infospagesid>".$offsetInfoPageId."</infospagesid>\n";
echo "<infospagesid>".$lastId."</infospagesid>\n";

// purge cms_content
$lastId =  getNextVal("cms_content", "id_content");
//$sql = "SELECT * FROM `cms_content` order by id_content ASC;";
//$aContent = dbGetObjectsFromRequete("Cms_content", $sql);
//$firstId = $aContent[0]->id_content;
//$offsetContentId = $lastId-$firstId;
//echo "<contentid>".$offsetContentId."</contentid>\n";
echo "<contentid>".$lastId."</contentid>\n";

// purge cms_archi_content
$lastId =  getNextVal("cms_archi_content", "id_archi");
//$sql = "SELECT cms_archi_content.* FROM cms_archi_content, cms_content where cms_archi_content.id_content_archi = cms_content.id_content order by id_archi ASC;";
//$aArchiContent = dbGetObjectsFromRequete("Cms_archi_content", $sql);
//$firstId = $aArchiContent[0]->id_archi;
//$offsetArchiContentId = $lastId-$firstId;
//echo "<archicontentid>".$offsetArchiContentId."</archicontentid>\n";
echo "<archicontentid>".$lastId."</archicontentid>\n";

// purge cms_struct_page
$lastId =  getNextVal("cms_struct_page", "id_struct");
//$sql = "SELECT * FROM `cms_struct_page` ORDER BY id_struct ASC;";
//$aStruct_pages = dbGetObjectsFromRequete("Cms_struct_page", $sql);
//$firstId = $aStruct_pages[0]->id_struct;
//$offsetStructPageId = $lastId-$firstId;
//$offsetStructPageId = $lastId;
//echo "<structpageid>".$offsetStructPageId."</structpageid>\n";
echo "<structpageid>".$lastId."</structpageid>\n";

// purge cms_struct_page
$lastId =  getNextVal("cms_droit", "id_droit");
//$sql = "SELECT * FROM `cms_droit` ORDER BY id_droit ASC;";
//$aDroits = dbGetObjectsFromRequete("Droit", $sql);
//$firstId = $aDroits[0]->id_droit;
//$offsetDroitseId = $lastId-$firstId;
//echo "<droitsid>".$offsetDroitseId."</droitsid>\n";
echo "<droitsid>".$lastId."</droitsid>\n";

// classes
$lastId =  getNextVal("classe", "cms_id");
//$sql = "SELECT * FROM `classe` ORDER BY cms_id ASC;";
//$aO = dbGetObjectsFromRequete("classe", $sql);
//$firstId = $aO[0]->cms_id;
//$offset = $lastId-$firstId;
//echo "<classeid>".$offset."</classeid>\n";
echo "<classeid>".$lastId."</classeid>\n";

if (istable("cms_assoclassepage")){
	// cms_assoclassepage
	$lastId =  getNextVal("cms_assoclassepage", "xcp_id");
	echo "<assoclassepageid>".$lastId."</assoclassepageid>\n";
}
else{
	echo "<assoclassepageid>-1</assoclassepageid>\n";
}

// cms_js
$lastId =  getNextVal("cms_js", "cms_id");
echo "<jsid>".$lastId."</jsid>\n";

// cms_assojsarbopages
$lastId =  getNextVal("cms_assojsarbopages", "xja_id");
echo "<assojsarbopagesid>".$lastId."</assojsarbopagesid>\n";

// cms_assojscmssite
$lastId =  getNextVal("cms_assojscmssite", "xjs_id");
echo "<assojscmssiteid>".$lastId."</assojscmssiteid>\n";

// cms_prepend
$lastId =  getNextVal("cms_prepend", "ppd_id");
echo "<prependid>".$lastId."</prependid>\n";

// cms_assoprependarbopages
$lastId =  getNextVal("cms_assoprependarbopages", "xpa_id");
echo "<assoprependarbopagesid>".$lastId."</assoprependarbopagesid>\n";

// cms_video
if (class_exists('cms_video')&&istable("cms_video")){
	$lastId =  getNextVal("cms_video", "cms_id");
}
else{
	$lastId = -1;
}
echo "<videoid>".$lastId."</videoid>\n";

// cms_assovideopage
if (class_exists('cms_assovideopage')&&istable("cms_assovideopage")){
	$lastId =  getNextVal("cms_assovideopage", "xvp_id");
}
else{
	$lastId = -1;
}
echo "<assovideopageid>".$lastId."</assovideopageid>\n";

// cms_diapo
$lastId =  getNextVal("cms_diapo", "img_id");
echo "<diapoid>".$lastId."</diapoid>\n";

// cms_diaporama
$lastId =  getNextVal("cms_diaporama", "dia_id");
echo "<diaporamaid>".$lastId."</diaporamaid>\n";

// cms_diaporama_type
$lastId =  getNextVal("cms_diaporama_type", "dia_id");
echo "<diaporamatypeid>".$lastId."</diaporamatypeid>\n";

// cms_assodiapodiaporama
$lastId =  getNextVal("cms_assodiapodiaporama", "xdp_id");
echo "<assodiapodiaporamaid>".$lastId."</assodiapodiaporamaid>\n";

// cms_chaine_reference
$lastId =  getNextVal("cms_chaine_reference", "cms_crf_id");
echo "<chainereferenceid>".$lastId."</chainereferenceid>\n";

// cms_chaine_traduite
$lastId =  getNextVal("cms_chaine_traduite", "cms_ctd_id");
echo "<chainetraduiteid>".$lastId."</chainetraduiteid>\n";

echo "</offsets>\n"
?>