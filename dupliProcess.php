<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

// params -----------------------------------------------
if (is_post('sourcesite')){
	$idSte = $_POST['sourcesite'];
}
else{
	$idSte = 1;
}
if (is_post('destname')){
	$nomSte = $_POST['destname'];
}
else{
	$nomSte = "en";
}
if (is_post('serveroffsetsxml')){	
	$xmlOffsets = readXMLfromURL($_POST['serveroffsetsxml']);
	//pre_dump(htmlentities($xmlOffsets));
	xmlStringParse($xmlOffsets);
	
	foreach ($stack[0]["children"] as $key => $xnode){
		if ($xnode["name"] == "SITEID"){
			$offsetSiteId = (int)$xnode["cdata"];
		}
		elseif ($xnode["name"] == "NODEID"){
			$offsetNodeId = (int)$xnode["cdata"];
		}
		elseif ($xnode["name"] == "PAGEID"){
			$offsetPageId = (int)$xnode["cdata"];
		}
		elseif ($xnode["name"] == "INFOSPAGESID"){
			$offsetInfoPageId = (int)$xnode["cdata"];
		}
		elseif ($xnode["name"] == "CONTENTID"){
			$offsetContentId = (int)$xnode["cdata"];
		}
		elseif ($xnode["name"] == "ARCHICONTENTID"){
			$offsetArchiContentId = (int)$xnode["cdata"];
		}
		elseif ($xnode["name"] == "STRUCTPAGEID"){
			$offsetStructPageId = (int)$xnode["cdata"];
		}
		elseif ($xnode["name"] == "DROITSID"){
			$offsetDroitseId = (int)$xnode["cdata"];
		}
		elseif ($xnode["name"] == "CLASSEID"){
			$offsetClasseId = (int)$xnode["cdata"];
		}
		elseif ($xnode["name"] == "ASSOCLASSEPAGEID"){
			$offsetAssoClassePageId = (int)$xnode["cdata"];
		}	
		elseif ($xnode["name"] == "JSID"){
			$offsetJsId = (int)$xnode["cdata"];
		}	
		elseif ($xnode["name"] == "ASSOJSARBOPAGESID"){
			$offsetAssoJsArbopagesId = (int)$xnode["cdata"];
		}
		elseif ($xnode["name"] == "ASSOJSCMSSITEID"){
			$offsetAssoJsCmssiteId = (int)$xnode["cdata"];
		}
		elseif ($xnode["name"] == "PREPENDID"){
			$offsetPrependId = (int)$xnode["cdata"];
		}
		elseif ($xnode["name"] == "ASSOPREPENDARBOPAGESID"){
			$offsetAssoPrependArbopagesId = (int)$xnode["cdata"];
		}
		elseif ($xnode["name"] == "VIDEOID"){
			$offsetVideoId = (int)$xnode["cdata"];
		}
		elseif ($xnode["name"] == "ASSOVIDEOPAGEID"){
			$offsetAssoVideoPageId = (int)$xnode["cdata"];
		}
		elseif ($xnode["name"] == "DIAPOID"){
			$offsetDiapoId = (int)$xnode["cdata"];
		}
		elseif ($xnode["name"] == "DIAPORAMAID"){
			$offsetDiaporamaId = (int)$xnode["cdata"];
		}
		elseif ($xnode["name"] == "DIAPORAMATYPEID"){
			$offsetDiaporamaTypeId = (int)$xnode["cdata"];
		}
		elseif ($xnode["name"] == "ASSODIAPODIAPORAMAID"){
			$offsetAssoDiapoDiaporamaId = (int)$xnode["cdata"];
		}
		elseif ($xnode["name"] == "CHAINEREFERENCEID"){
			$offsetChaineReferenceId = (int)$xnode["cdata"];
		}
		elseif ($xnode["name"] == "CHAINETRADUITEID"){
			$offsetChaineTraduiteId = (int)$xnode["cdata"];
		}
	}
}
else{
	$offsetSiteId = 0;
	$offsetNodeId = 0;
	$offsetPageId = 0;
	$offsetInfoPageId = 0;
	$offsetContentId = 0;
	$offsetArchiContentId = 0;
	$offsetStructPageId = 0;
	$offsetDroitseId = 0;
	$offsetClasseId = 0;	
	$offsetJsId = 0;
	$offsetAssoJsArbopagesId = 0;
	$offsetAssoJsCmssiteId = 0;
	$offsetPrependId = 0;
	$offsetAssoPrependArbopagesId = 0;
	$offsetVideoId = 0;
	$offsetAssoVideoPageId = 0;
	$offsetDiapoId = 0;
	$offsetDiaporamaId = 0;
	$offsetDiaporamaTypeId = 0;
	$offsetAssoDiapoDiaporamaId = 0;
	$offsetChaineReferenceId = 0;
	$offsetChaineTraduiteId = 0;			
}

if (is_post('destid')){
	$offsetSiteId = $_POST['destid'];
}

// sets ---------------------------------------------------

$simul = true; // true false

echo "<h2>-- ATTENTION -- dump en encodage iso8859-1</h2>";

// ----- le site cloné existe ou on le crée -------------------
if ($_POST['dowhat'] == "clone"){
	if (getCount("cms_site", "cms_id", "cms_rep", $nomSte, "TEXT") == 0){
		echo "-- on crée le site ".$nomSte." | id : ".($offsetSiteId)."<br />\n";
		$origSite =  new Cms_site($idSte);
		$clonedSite = $origSite;
		
		$clonedSite->set_id($offsetSiteId);
		$clonedSite->set_name($nomSte);
		//$clonedSite->set_isminisite(-1);
		$clonedSite->set_rep($nomSte);
		
		$idNewSte = dbSauve($clonedSite);
	}
	else{
		echo "-- le site existe ".$nomSte." | id : ".($offsetSiteId)."<br />\n";
		$aSite = dbGetObjectsFromRequete("cms_site", "select * from cms_site where cms_rep = '".$nomSte."'");
		$oSite = $aSite[0];
		$idNewSte = $oSite->get_id();
	}
}
else{
	$idNewSte = $offsetSiteId;
	echo "-- on crée le site ".$nomSte." | id : ".($idNewSte)."<br />\n";
	$origSite =  new Cms_site($idSte);
	$clonedSite = $origSite;
	$clonedSite->set_id($idNewSte);
	$clonedSite->set_name($nomSte);
	$clonedSite->set_isminisite(1);
	$clonedSite->set_rep($nomSte);	
	
	echo htmlentities(dbMakeInsertReq($clonedSite)).";<br />\n";
}

// purge cms_arbo_pages
$db->Execute('ALTER TABLE `cms_arbo_pages` ORDER BY `node_id`;');
echo "<h2>-- cms_arbo_pages</h2>";
$lastId =  getNextVal("cms_arbo_pages", "node_id");
$sql = "SELECT * FROM `cms_arbo_pages` where node_id_site = ".$idSte." AND node_id > 0;";
$aNodes= dbGetObjectsFromRequete("cms_arbo_pages", $sql);
$firstId = $aNodes[0]->node_id;

if($offsetNodeId<=$firstId){
	$offsetNodeId = 0;
}
else{
	$offsetNodeId = ($offsetNodeId-$firstId);
}

echo '-- offset = '.$offsetNodeId."<br />\n";

foreach($aNodes as $key => $node){
	$node->setNode_id($node->getNode_id()+$offsetNodeId);
	if ($node->getNode_parent_id() != 0){
		$node->setNode_parent_id($node->getNode_parent_id()+$offsetNodeId);
	}
	$node->setId_site($idNewSte);
	$node->setAbsolute_path_name(preg_replace("/\/[^\/]+\/(.*)/msi", "/".$nomSte."/$1", $node->getAbsolute_path_name()));
	//dumpObjLite($node);
	echo "-- create node ".$node->getNode_id()."<br />\n";
	if ($simul == false){
		dbInsert($node);
	}
	else{
		echo htmlentities(dbMakeInsertReq($node)).";<br />\n";
	}
}

// purge cms_page
$db->Execute('ALTER TABLE `cms_page` ORDER BY `id_page`;');
echo "<h2>-- cms_page</h2>";
$lastId =  getNextVal("cms_page", "id_page");
$sql = "SELECT * FROM `cms_page` where id_site = ".$idSte.";";
$aPages = dbGetObjectsFromRequete("Cms_page", $sql);
$firstId = $aPages[0]->id_page;

if($offsetPageId<=$firstId){
	$offsetPageId = 0;
}
else{
	$offsetPageId = ($offsetPageId-$firstId);
}

echo '-- offset = '.$offsetPageId."<br />\n";

foreach($aPages as $key => $page){
	$page->setId_page($page->getId_page()+$offsetPageId);
	if ($page->getNodeid_page() != 0){
		$page->setNodeid_page($page->getNodeid_page()+$offsetNodeId);
	}
	if ($page->getGabarit_page() > 1){
		$page->setGabarit_page($page->getGabarit_page()+$offsetPageId);
	}
	$page->setId_site($idNewSte);
	$page->setDatedlt_page("0000-00-00 00:00:00");
	//dumpObjLite($page);
	echo "-- create page ".$page->getId_page()."<br />\n";
	if ($simul == false){
		dbInsert($page);
	}
	else{
		echo htmlentities(dbMakeInsertReq($page)).";<br />\n";
	}
}

// purge cms_infos_pages
$db->Execute('ALTER TABLE `cms_infos_pages` ORDER BY `id`;');
echo "<h2>-- cms_infos_pages</h2>";
$lastId =  getNextVal("cms_infos_pages", "id");
$sql = "SELECT cms_infos_pages.* FROM `cms_infos_pages`, `cms_page` WHERE cms_page.id_site = ".$idSte." AND cms_infos_pages.page_id = cms_page.id_page;";
$aInfos_pages = dbGetObjectsFromRequete("Cms_infos_page", $sql);
$firstId = $aInfos_pages[0]->id;

if($offsetInfoPageId<=$firstId){
	$offsetInfoPageId = 0;
}
else{
	$offsetInfoPageId = ($offsetInfoPageId-$firstId);
}

echo '-- offset = '.$offsetInfoPageId."<br />\n";

foreach($aInfos_pages as $key => $infopage){
	$infopage->setId($infopage->getId()+$offsetInfoPageId);
	$infopage->setPage_id($infopage->getPage_id()+$offsetPageId);
	echo "-- create info_page ".$infopage->getId()."<br />\n";
	if ($simul == false){
		dbInsert($infopage);
	}
	else{
		echo htmlentities(dbMakeInsertReq($infopage)).";<br />\n";
	}
}

// purge cms_content
$db->Execute('ALTER TABLE `cms_content` ORDER BY `id_content`;');
echo "<h2>-- cms_content </h2>";
$lastId =  getNextVal("cms_content", "id_content");
$sql = "SELECT * FROM `cms_content` where id_site = ".$idSte." order by id_content ASC;";
$aContent = dbGetObjectsFromRequete("Cms_content", $sql);
$firstId = $aContent[0]->id_content;

if($offsetContentId<=$firstId){
	$offsetContentId = 0;
}
else{
	$offsetContentId = ($offsetContentId-$firstId);
}

echo '-- offset = '.$offsetContentId."<br />\n";

foreach($aContent as $key => $content){
	$content->setId_content($content->getId_content()+$offsetContentId);
	if ($content->getNodeid_content() >= 0){
		$content->setNodeid_content($content->getNodeid_content()+$offsetNodeId);
	}
	$content->setId_site($idNewSte);
	//dumpObjLite($content);
	echo "-- create content ".$content->getId_content().";<br />\n";
	if ($simul == false){
		dbInsert($content);
	}
	else{
		echo htmlentities(dbMakeInsertReq($content)).";<br />\n";
	}
}

// purge cms_archi_content
$db->Execute('ALTER TABLE `cms_archi_content` ORDER BY `id_archi`;');
echo "<h2>-- cms_archi_content </h2>";
$lastId =  getNextVal("cms_archi_content", "id_archi");
$sql = "SELECT cms_archi_content.* FROM cms_archi_content, cms_content where cms_archi_content.id_content_archi = cms_content.id_content and cms_content.id_site = ".$idSte." order by id_archi ASC;";
$aArchiContent = dbGetObjectsFromRequete("Cms_archi_content", $sql);
$firstId = $aArchiContent[0]->id_archi;

if($offsetArchiContentId<=$firstId){
	$offsetArchiContentId = 0;
}
else{
	$offsetArchiContentId = ($offsetArchiContentId-$firstId);
}

echo '-- offset = '.$offsetArchiContentId."<br />\n";

foreach($aArchiContent as $key => $archi){
	$archi->setId_archi($archi->getId_archi()+$offsetArchiContentId);
	$archi->setId_content_archi($archi->getId_content_archi()+$offsetContentId);
	//dumpObjLite($archi);
	echo "-- create cms_archi_content ".$archi->getId_archi()."<br />\n";
	if ($simul == false){
		dbInsert($archi);
	}
	else{
		echo htmlentities(dbMakeInsertReq($archi)).";<br />\n";
	}
}

// purge cms_struct_page
$db->Execute('ALTER TABLE `cms_struct_page` ORDER BY `id_struct`;');
echo "<h2>-- cms_struct_page</h2>";
$lastId =  getNextVal("cms_struct_page", "id_struct");
$sql = "SELECT cms_struct_page.* FROM `cms_struct_page`, `cms_page` WHERE cms_page.id_site = ".$idSte." AND cms_struct_page.id_page = cms_page.id_page;";
$aStruct_pages = dbGetObjectsFromRequete("Cms_struct_page", $sql);
$firstId = $aStruct_pages[0]->id_struct;

if($offsetStructPageId<=$firstId){
	$offsetStructPageId = 0;
}
else{
	$offsetStructPageId = ($offsetStructPageId-$firstId);
}

echo '-- offset = '.$offsetStructPageId."<br />\n";

foreach($aStruct_pages as $key => $struct_pages){
	//pre_dump($struct_pages);
	echo "-- .getId_content = ".$struct_pages->id_content."<br />\n";
	echo "-- .offsetContentId = ".$offsetContentId."<br />\n";
	$struct_pages->setId_struct($struct_pages->getId_struct()+$offsetStructPageId);
	$struct_pages->setId_page($struct_pages->getId_page()+$offsetPageId);
	$struct_pages->setId_zonedit_content($struct_pages->getId_zonedit_content()+$offsetContentId);
	$struct_pages->setId_content($struct_pages->getId_content()+$offsetContentId);
	//dumpObjLite($struct_pages);
	echo "-- create struct_pages ".$struct_pages->getId_struct()."<br />\n";
	if ($simul == false){
		dbInsert($struct_pages);
	}
	else{
		echo htmlentities(dbMakeInsertReq($struct_pages)).";<br />\n";
	}
}

// purge cms_droit
$db->Execute('ALTER TABLE `cms_droit` ORDER BY `id_droit`;');
echo "<h2>-- Droit</h2>";
$lastId =  getNextVal("cms_droit", "id_droit");
$sql = "SELECT cms_droit.* FROM `cms_droit`, `cms_content` WHERE cms_content.id_site = ".$idSte." AND cms_content.id_content = cms_droit.id_content;";
$aDroits = dbGetObjectsFromRequete("Droit", $sql);
$firstId = $aDroits[0]->id_droit;

if($offsetDroitseId<=$firstId){
	$offsetDroitseId = 0;
}
else{
	$offsetDroitseId = ($offsetDroitseId-$firstId);
}

echo '-- offset = '.$offsetDroitseId."<br />\n";

foreach($aDroits as $key => $droits){
	$droits->setId_droit($droits->getId_droit()+$offsetDroitseId);
	$droits->setId_content($droits->getId_content()+$offsetContentId);
	//dumpObjLite($struct_pages);
	echo "-- create droit ".$droits->getId_droit()."<br />\n";
	if ($simul == false){
		dbInsert($droits);
	}
	else{
		echo htmlentities(dbMakeInsertReq($droits)).";<br />\n";
	}
}

// purge classe
$db->Execute('ALTER TABLE `classe` ORDER BY `cms_id`;');
echo "<h2>-- Classe</h2>";
$lastId =  getNextVal("classe", "cms_id");
$sql = "SELECT classe.* FROM `classe` WHERE cms_cms_site = ".$idSte.";";
$aClasses = dbGetObjectsFromRequete("Classe", $sql);
$firstId = $aClasses[0]->id;

if(count($aClasses==0)){
	$offsetClasseId = 0;
}
elseif($offsetClasseId<=$firstId){
	$offsetClasseId = 0;
}
else{
	$offsetClasseId = ($offsetClasseId-$firstId);
}

echo '-- offset = '.$offsetClasseId."<br />\n";

foreach($aClasses as $key => $oO){
	$oO->set_id($oO->get_id()+$offsetClasseId);
	//dumpObjLite($struct_pages);
	echo "-- create classe ".$oO->get_id()."<br />\n";
	if ($simul == false){
		dbInsert($oO);
	}
	else{
		echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
	}
}

// purge classe
echo "<h2>-- cms_assoclassepage</h2>";
$db->Execute('ALTER TABLE `cms_assoclassepage` ORDER BY `xcp_id`;');
if ($offsetAssoClassePageId==-1){ // n'existe pas sur la cible :: la créer
	$oO = new cms_assoclassepage();
	echo $oO->sMySql.";<br />";
	$offsetAssoClassePageId=0;
	$sql = "SELECT * FROM cms_assoclassepage;";
}
else{
	$lastId =  getNextVal("cms_assoclassepage", "xcp_id");
	$sql = "SELECT cms_assoclassepage.* FROM cms_assoclassepage, cms_page WHERE cms_page.id_site = ".$idSte." AND cms_page.id_page = cms_assoclassepage.xcp_cms_page ORDER BY cms_assoclassepage.xcp_id;";
}
$aO = dbGetObjectsFromRequete("cms_assoclassepage", $sql);
$firstId = $aO[0]->id;

if($offsetAssoClassePageId<=$firstId){
	$offsetAssoClassePageId = 0;
}
else{
	$offsetAssoClassePageId = ($offsetAssoClassePageId-$firstId);
}

echo '-- offset = '.$offsetAssoClassePageId."<br />\n";

foreach($aO as $key => $oO){
	$oO->set_id($oO->get_id()+$offsetAssoClassePageId);
	$oO->set_cms_page($oO->get_cms_page()+$offsetPageId);
	$oO->set_classe($oO->get_classe()+$offsetClasseId);	
	//dumpObjLite($struct_pages);
	echo "-- create cms_assoclassepage ".$oO->get_id()."<br />\n";
	if ($simul == false){
		dbInsert($oO);
	}
	else{
		echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
	}
}


// purge cms_js
$db->Execute('ALTER TABLE `cms_js` ORDER BY `cms_id`;');
echo "<h2>-- cms_js</h2>";
$lastId =  getNextVal("cms_js", "cms_id");
$sql = "SELECT DISTINCT cms_js.* FROM cms_js,  cms_assojscmssite WHERE cms_assojscmssite.xjs_cms_site = ".$idSte." AND cms_assojscmssite.xjs_cms_js = cms_js.cms_id ORDER BY cms_js.cms_id;";
$aO1 = dbGetObjectsFromRequete("cms_js", $sql);

$sql = "SELECT DISTINCT cms_js.* FROM cms_js, cms_assojsarbopages, cms_arbo_pages WHERE cms_assojsarbopages.xja_cms_js = cms_js.cms_id AND cms_assojsarbopages.xja_cms_arbo_pages = cms_arbo_pages.node_id AND cms_arbo_pages.node_id_site = ".$idSte." ORDER BY cms_js.cms_id;";
$aO2 = dbGetObjectsFromRequete("cms_js", $sql);

$aO = array();
foreach($aO1 as $key => $oO){
	$aO[$key]=$oO;
}
foreach($aO2 as $key => $oO){
	$aO[$key]=$oO;
}

$firstId = $aO[0]->id;

if($offsetJsId<=$firstId){
	$offsetJsId = 0;
}
else{
	$offsetJsId = ($offsetJsId-$firstId);
}

echo '-- offset = '.$offsetJsId."<br />\n";

foreach($aO as $key => $oO){
	$oO->set_id($oO->get_id()+$offsetJsId);
	echo "-- create cms_js ".$oO->get_id()."<br />\n";
	if ($simul == false){
		dbInsert($oO);
	}
	else{
		echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
	}
}




//	$offsetAssoJsArbopagesId = 0;
// purge cms_assojsarbopages
$db->Execute('ALTER TABLE `cms_assojsarbopages` ORDER BY `xja_id`;');
echo "<h2>-- cms_assojsarbopages</h2>";
$lastId =  getNextVal("cms_assojsarbopages", "xja_id");
$sql = "SELECT DISTINCT cms_assojsarbopages.* FROM cms_js, cms_assojsarbopages, cms_arbo_pages WHERE cms_assojsarbopages.xja_cms_js = cms_js.cms_id AND cms_assojsarbopages.xja_cms_arbo_pages = cms_arbo_pages.node_id AND cms_arbo_pages.node_id_site = ".$idSte." ORDER BY cms_assojsarbopages.xja_id;";
$aO = dbGetObjectsFromRequete("cms_assojsarbopages", $sql);

$firstId = $aO[0]->id;

if($offsetAssoJsArbopagesId<=$firstId){
	$offsetAssoJsArbopagesId = 0;
}
else{
	$offsetAssoJsArbopagesId = ($offsetAssoJsArbopagesId-$firstId);
}

echo '-- offset = '.$offsetAssoJsArbopagesId."<br />\n";

foreach($aO as $key => $oO){
	$oO->set_id($oO->get_id()+$offsetAssoJsArbopagesId);
	$oO->set_cms_js($oO->get_cms_js()+$offsetJsId);
	$oO->set_cms_arbo_pages($oO->get_cms_arbo_pages()+$offsetNodeId);
	echo "-- create cms_assojsarbopages ".$oO->get_id()."<br />\n";
	if ($simul == false){
		dbInsert($oO);
	}
	else{
		echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
	}
}

//	$offsetAssoJsCmssiteId = 0;
// purge cms_assojscmssite
$db->Execute('ALTER TABLE `cms_assojscmssite` ORDER BY `xjs_id`;');
echo "<h2>-- cms_assojscmssite</h2>";
$lastId =  getNextVal("cms_assojscmssite", "xjs_id");
$sql = "SELECT DISTINCT cms_assojscmssite.* FROM cms_js,  cms_assojscmssite WHERE cms_assojscmssite.xjs_cms_site = ".$idSte." AND cms_assojscmssite.xjs_cms_js = cms_js.cms_id ORDER BY cms_assojscmssite.xjs_id;";
$aO = dbGetObjectsFromRequete("cms_assojscmssite", $sql);

$firstId = $aO[0]->id;

if($offsetAssoJsCmssiteId<=$firstId){
	$offsetAssoJsCmssiteId = 0;
}
else{
	$offsetAssoJsCmssiteId = ($offsetAssoJsCmssiteId-$firstId);
}

echo '-- offset = '.$offsetAssoJsCmssiteId."<br />\n";

foreach($aO as $key => $oO){
	$oO->set_id($oO->get_id()+$offsetAssoJsCmssiteId);
	$oO->set_cms_js($oO->get_cms_js()+$offsetJsId);
	$oO->set_cms_site($idNewSte);
	echo "-- create cms_assojscmssite ".$oO->get_id()."<br />\n";
	if ($simul == false){
		dbInsert($oO);
	}
	else{
		echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
	}
}


//	$offsetPrependId = 0;
// purge cms_prepend
$db->Execute('ALTER TABLE `cms_prepend` ORDER BY `ppd_id`;');
echo "<h2>-- cms_prepend</h2>";
$lastId =  getNextVal("cms_prepend", "ppd_id");
$sql = "SELECT DISTINCT cms_prepend.* FROM cms_prepend, cms_assoprependarbopages, cms_arbo_pages WHERE cms_assoprependarbopages.xpa_cms_prepend = cms_prepend.ppd_id AND cms_assoprependarbopages.xpa_cms_arbo_pages = cms_arbo_pages.node_id AND cms_arbo_pages.node_id_site = ".$idSte." ORDER BY cms_prepend.ppd_id;";
$aO = dbGetObjectsFromRequete("cms_prepend", $sql);

$firstId = $aO[0]->id;

if($offsetPrependId<=$firstId){
	$offsetPrependId = 0;
}
else{
	$offsetPrependId = ($offsetPrependId-$firstId);
}

echo '-- offset = '.$offsetPrependId."<br />\n";

foreach($aO as $key => $oO){
	$oO->set_id($oO->get_id()+$offsetPrependId);
	echo "-- create cms_prepend ".$oO->get_id()."<br />\n";
	if ($simul == false){
		dbInsert($oO);
	}
	else{
		echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
	}
}


//	$offsetAssoPrependArbopagesId = 0;
// purge cms_assoprependarbopages
$db->Execute('ALTER TABLE `cms_assoprependarbopages` ORDER BY `xpa_id`;');
echo "<h2>-- cms_assoprependarbopages</h2>";
$lastId =  getNextVal("cms_assoprependarbopages", "xpa_id");
$sql = "SELECT DISTINCT cms_assoprependarbopages.* FROM cms_prepend, cms_assoprependarbopages, cms_arbo_pages WHERE cms_assoprependarbopages.xpa_cms_prepend = cms_prepend.ppd_id AND cms_assoprependarbopages.xpa_cms_arbo_pages = cms_arbo_pages.node_id AND cms_arbo_pages.node_id_site = ".$idSte." ORDER BY cms_assoprependarbopages.xpa_id;";
$aO = dbGetObjectsFromRequete("cms_assoprependarbopages", $sql);

$firstId = $aO[0]->id;

if($offsetAssoPrependArbopagesId<=$firstId){
	$offsetAssoPrependArbopagesId = 0;
}
else{
	$offsetAssoPrependArbopagesId = ($offsetAssoPrependArbopagesId-$firstId);
}

echo '-- offset = '.$offsetAssoPrependArbopagesId."<br />\n";

foreach($aO as $key => $oO){
	$oO->set_id($oO->get_id()+$offsetAssoPrependArbopagesId);
	$oO->set_cms_arbo_pages($oO->get_cms_arbo_pages()+$offsetNodeId);
	
	echo "-- create cms_assoprependarbopages ".$oO->get_id()."<br />\n";
	if ($simul == false){
		dbInsert($oO);
	}
	else{
		echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
	}
}

//	$offsetVideoId = 0;
// purge cms_video
$db->Execute('ALTER TABLE `cms_video` ORDER BY `cms_id`;');
echo "<h2>-- cms_video</h2>";
$lastId =  getNextVal("cms_video", "cms_id");
$sql = "SELECT DISTINCT cms_video.* FROM cms_video WHERE cms_cms_site = ".$idSte.";";
$aO = dbGetObjectsFromRequete("cms_video", $sql);

$firstId = $aO[0]->id;

if($offsetVideoId<=$firstId){
	$offsetVideoId = 0;
}
else{
	$offsetVideoId = ($offsetVideoId-$firstId);
}

echo '-- offset = '.$offsetVideoId."<br />\n";

foreach($aO as $key => $oO){
	$oO->set_id($oO->get_id()+$offsetVideoId);
	$oO->set_cms_site($idNewSte);
	echo "-- create cms_video ".$oO->get_id()."<br />\n";
	if ($simul == false){
		dbInsert($oO);
	}
	else{
		echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
	}
}


//	$offsetAssoVideoPageId = 0;
// purge cms_assovideopage
$db->Execute('ALTER TABLE `cms_assovideopage` ORDER BY `xvp_id`;');
echo "<h2>-- cms_assovideopage</h2>";
$lastId =  getNextVal("cms_assovideopage", "xvp_id");
$sql = "SELECT DISTINCT cms_assovideopage.* FROM cms_video, cms_assovideopage, cms_page WHERE cms_assovideopage.xvp_cms_video = cms_video.cms_id AND cms_assovideopage.xvp_cms_page = cms_page.id_page AND cms_page.id_site = ".$idSte." ORDER BY cms_assovideopage.xvp_id;";
$aO = dbGetObjectsFromRequete("cms_assovideopage", $sql);

$firstId = $aO[0]->id;

if($offsetAssoVideoPageId<=$firstId){
	$offsetAssoVideoPageId = 0;
}
else{
	$offsetAssoVideoPageId = ($offsetAssoVideoPageId-$firstId);
}

echo '-- offset = '.$offsetAssoVideoPageId."<br />\n";

foreach($aO as $key => $oO){
	$oO->set_id($oO->get_id()+$offsetAssoVideoPageId);
	$oO->set_cms_page($oO->get_cms_page()+$offsetPageId);
	$oO->set_cms_video($oO->get_cms_video()+$offsetVideoId);
	
	echo "-- create cms_assovideopage ".$oO->get_id()."<br />\n";
	if ($simul == false){
		dbInsert($oO);
	}
	else{
		echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
	}
}


//	$offsetDiapoId = 0;
// purge cms_diapo
$db->Execute('ALTER TABLE `cms_diapo` ORDER BY `img_id`;');
echo "<h2>-- cms_diapo</h2>";
$lastId =  getNextVal("cms_diapo", "img_id");
$sql = "SELECT DISTINCT cms_diapo.* FROM cms_diapo WHERE img_cms_site = ".$idSte.";";
$aO = dbGetObjectsFromRequete("cms_diapo", $sql);

$firstId = $aO[0]->id;

if($offsetDiapoId<=$firstId){
	$offsetDiapoId = 0;
}
else{
	$offsetDiapoId = ($offsetDiapoId-$firstId);
}

echo '-- offset = '.$offsetDiapoId."<br />\n";

foreach($aO as $key => $oO){
	$oO->set_id($oO->get_id()+$offsetDiapoId);
	$oO->set_cms_site($idNewSte);
	echo "-- create cms_diapo ".$oO->get_id()."<br />\n";
	if ($simul == false){
		dbInsert($oO);
	}
	else{
		echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
	}
}

//	$offsetDiaporamaTypeId = 0;
// purge cms_diaporama_type
$db->Execute('ALTER TABLE `cms_diaporama_type` ORDER BY `dia_id`;');
echo "<h2>-- cms_diaporama_type</h2>";
$lastId =  getNextVal("cms_diaporama_type", "dia_id");
$sql = "SELECT DISTINCT cms_diaporama_type.* FROM cms_diaporama_type, cms_diaporama, cms_assodiapodiaporama, cms_diapo WHERE cms_diaporama_type.dia_id = cms_diaporama.dia_diaporama_type AND cms_assodiapodiaporama.xdp_cms_diaporama = cms_diaporama.dia_id AND xdp_cms_diapo = cms_diapo.img_id AND cms_diapo.img_cms_site = ".$idSte." ORDER BY cms_diaporama_type.dia_id;";
$aO = dbGetObjectsFromRequete("cms_diaporama_type", $sql);

$firstId = $aO[0]->id;

if($offsetDiaporamaTypeId<=$firstId){
	$offsetDiaporamaTypeId = 0;
}
else{
	$offsetDiaporamaTypeId = ($offsetDiaporamaTypeId-$firstId);
}

echo '-- offset = '.$offsetDiaporamaTypeId."<br />\n";

foreach($aO as $key => $oO){
	$oO->set_id($oO->get_id()+$offsetDiaporamaTypeId);
	echo "-- create cms_diaporama_type ".$oO->get_id()."<br />\n";
	if ($simul == false){
		dbInsert($oO);
	}
	else{
		echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
	}
}



//	$offsetDiaporamaId = 0;
// purge cms_diaporama
$db->Execute('ALTER TABLE `cms_diaporama` ORDER BY `dia_id`;');
echo "<h2>-- cms_diaporama</h2>";
$lastId =  getNextVal("cms_diaporama", "dia_id");
$sql = "SELECT DISTINCT cms_diaporama.* FROM cms_diaporama, cms_assodiapodiaporama, cms_diapo WHERE cms_assodiapodiaporama.xdp_cms_diaporama = cms_diaporama.dia_id AND xdp_cms_diapo = cms_diapo.img_id AND cms_diapo.img_cms_site = ".$idSte." ORDER BY cms_diaporama.dia_id;";
$aO = dbGetObjectsFromRequete("cms_diaporama", $sql);

$firstId = $aO[0]->id;

if($offsetDiaporamaId<=$firstId){
	$offsetDiaporamaId = 0;
}
else{
	$offsetDiaporamaId = ($offsetDiaporamaId-$firstId);
}

echo '-- offset = '.$offsetDiaporamaId."<br />\n";

foreach($aO as $key => $oO){
	$oO->set_id($oO->get_id()+$offsetDiaporamaId);
	$oO->set_diaporama_type($oO->get_diaporama_type()+$offsetDiaporamaTypeId);	
	
	echo "-- create cms_diaporama ".$oO->get_id()."<br />\n";
	if ($simul == false){
		dbInsert($oO);
	}
	else{
		echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
	}
}

//	$offsetAssoDiapoDiaporamaId = 0;
// purge cms_assodiapodiaporama
$db->Execute('ALTER TABLE `cms_assodiapodiaporama` ORDER BY `xdp_id`;');
echo "<h2>-- cms_assodiapodiaporama</h2>";
$lastId =  getNextVal("cms_assodiapodiaporama", "xdp_id");
$sql = "SELECT DISTINCT cms_assodiapodiaporama.* FROM cms_assodiapodiaporama, cms_diapo WHERE xdp_cms_diapo = cms_diapo.img_id AND cms_diapo.img_cms_site = ".$idSte." ORDER BY cms_assodiapodiaporama.xdp_id;";
$aO = dbGetObjectsFromRequete("cms_assodiapodiaporama", $sql);

$firstId = $aO[0]->id;

if($offsetAssoDiapoDiaporamaId<=$firstId){
	$offsetAssoDiapoDiaporamaId = 0;
}
else{
	$offsetAssoDiapoDiaporamaId = ($offsetAssoDiapoDiaporamaId-$firstId);
}

echo '-- offset = '.$offsetAssoDiapoDiaporamaId."<br />\n";

foreach($aO as $key => $oO){
	$oO->set_id($oO->get_id()+$offsetAssoDiapoDiaporamaId);
	$oO->set_cms_diaporama($oO->get_cms_diaporama()+$offsetDiaporamaId);	
	$oO->set_cms_diapo($oO->get_cms_diapo()+$offsetDiapoId);	
	
	echo "-- create cms_assodiapodiaporama ".$oO->get_id()."<br />\n";
	if ($simul == false){
		dbInsert($oO);
	}
	else{
		echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
	}
}

//	$offsetChaineReferenceId = 0;
// purge cms_chaine_reference
$db->Execute('ALTER TABLE `cms_chaine_reference` ORDER BY `cms_crf_id`;');
echo "<h2>-- cms_chaine_reference</h2>";
$lastId =  getNextVal("cms_chaine_reference", "cms_crf_id");
$sql = "SELECT cms_chaine_reference.* FROM cms_chaine_reference WHERE cms_crf_id >= ".$offsetChaineReferenceId.";";
$aO = dbGetObjectsFromRequete("cms_chaine_reference", $sql);

$firstId = $aO[0]->id;

echo "-- pas d'offset = <br />\n";

foreach($aO as $key => $oO){	
	echo "-- create cms_chaine_reference ".$oO->get_id()."<br />\n";
	if ($simul == false){
		dbInsert($oO);
	}
	else{
		echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
	}
}


//	$offsetChaineTraduiteId = 0;
// purge cms_chaine_traduite
$db->Execute('ALTER TABLE `cms_chaine_traduite` ORDER BY `cms_ctd_id`;');
echo "<h2>-- cms_chaine_traduite</h2>";
$lastId =  getNextVal("cms_chaine_traduite", "cms_ctd_id");
$sql = "SELECT cms_chaine_traduite.* FROM cms_chaine_traduite WHERE cms_chaine_traduite.cms_ctd_id_reference >= ".$offsetChaineReferenceId." ORDER BY cms_chaine_traduite.cms_ctd_id;";
$aO = dbGetObjectsFromRequete("cms_chaine_traduite", $sql);

$firstId = $aO[0]->id;

if($offsetChaineTraduiteId<=$firstId){
	$offsetChaineTraduiteId = 0;
}
else{
	$offsetChaineTraduiteId = ($offsetChaineTraduiteId-$firstId);
}

echo "-- offset = ".$offsetChaineTraduiteId."<br />\n";

foreach($aO as $key => $oO){	
	echo "-- create cms_chaine_traduite ".$oO->get_id()."<br />\n";
	$oO->set_id($oO->get_id()+$offsetChaineTraduiteId);
	if ($simul == false){
		dbInsert($oO);
	}
	else{
		echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
	}
}



echo "--  <br />\n";
echo "--  NEXT ETAPE : EXPORTER DEPUIS PHPMYAPMIN les structures et contenus des classes metier<br />\n";
echo "--  <br />\n";
?>