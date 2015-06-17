<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

// params -----------------------------------------------
$idSte = $_SESSION["idSite"];
$idNewSte = $idSte;
$nomSte = $_SESSION["site"]; 
$offsetSiteId = 0;
$offsetNodeId = 10;
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
if (is_post('destid')){
	$offsetSiteId = $_POST['destid'];
}


if ($simul) echo "offsetNodeI ".$offsetNodeId;
// sets ---------------------------------------------------

$simul = true; // true false
$simul = false; // true false

if ($simul) echo "<h2>-- ATTENTION -- dump en encodage iso8859-1</h2>";

 
// ----- le site cloné existe ou on le crée -------------------
/*
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
*/
// purge cms_arbo_pages
$db->Execute('ALTER TABLE `cms_arbo_pages` ORDER BY `node_id`;');
if ($simul) echo "<h2>-- cms_arbo_pages</h2>";
$lastId =  getNextVal("cms_arbo_pages", "node_id");


$nodeInfos=getNodeInfos($db, $node_id_to_duplicate);
$libelle_to_duplicate = $nodeInfos["libelle"];

//$sql = "SELECT * FROM `cms_arbo_pages` where node_id_site = ".$idSte." AND node_id > 0;";


//$sql = "SELECT * FROM `cms_arbo_pages` where node_id_site = ".$idSte." AND node_absolute_path_name LIKE '%".$nodeInfos["path"]."%' AND node_id <> ".$node_id_to_duplicate.";";
$sql = "SELECT * FROM `cms_arbo_pages` where node_id_site = ".$idSte." AND node_absolute_path_name LIKE '%".$nodeInfos["path"]."%' ;";
//echo $sql;
 
if ($simul) echo $sql;

$aNodes= dbGetObjectsFromRequete("cms_arbo_pages", $sql);
 

$aNode_id_to_duplicate = array();


$array_cms_arbo_pages = array();
$offsetNodeId = 0;
foreach($aNodes as $key => $node){
	$array_cms_arbo_pages[$node->getNode_id()] = getNextVal("cms_arbo_pages", "Node_id")+$offsetNodeId;
	if ($offsetNodeId == 0) $id = $array_cms_arbo_pages[$node->getNode_id()];
	$offsetNodeId++ ;
}
 
foreach($aNodes as $key => $node){
	 
	$node->setNode_id($array_cms_arbo_pages[$node->getNode_id()]);
	if ($node->getNode_parent_id() != 0){
		$node->setNode_parent_id($array_cms_arbo_pages[$node->getNode_parent_id()]);
	} 
	
	if ($node->getNode_parent_id() == 0){
		$libelle = str_replace('"',"'\\'", $_POST['foldername']); // On remplace les guillemets par des doubles quotes
		$libelle = str_replace('`',"'", $libelle); // On remplace les quotes zarbi
		$_POST['foldername'] = $libelle; 
		$node->setNode_libelle($libelle);
	}
	 
	 
	$path = preg_replace ("/\/".$_SESSION["site"]."\/".$libelle_to_duplicate."\//", "/".$_SESSION["site"]."/".to_dbquote(removeForbiddenChars($libelle))."/", $node->getAbsolute_path_name());	
	$node->setId_site($idNewSte);
	$node->setAbsolute_path_name($path);
	//dumpObjLite($node);
	if ($simul) echo "-- create node ".$node->getNode_id()."<br />\n";
	if ($simul == false){ 
		dbInsert($node);
		//echo "INSERT";
		global $CMS_ROOT;
		if ($simul) echo $CMS_ROOT.stripslashes($path);
		if(!dirExists($CMS_ROOT.stripslashes($path))) {

			if(DEF_MODE_DEBUG==true) error_log("TRACE :: MKDIR : ECHEC");

			// si echec de la creation du rep, on efface le rep en base...
			$sql = " DELETE FROM cms_arbo_pages WHERE node_id = $result";
			// une seule racine pour tous les arbres
			if ($node_id != 0) $sql.= " AND id_site=$idSite";
			if (DEF_BDD != "ORACLE") $sql.=";";
			$rs = $db->Execute($sql);

			if(DEF_MODE_DEBUG==true) {
				if ($simul) echo "include/cms-inc/arbominisite.lib.php > addNode_cms_arbo_pages";
				if ($simul) echo "<br />Erreur interne de programme";
				if ($simul) echo "<br /><strong>$sql</strong>";
			}
			error_log(" Erreur lors de la creation du repertoire $CMS_ROOT/$path");
			$result = false;
		} else {
			if(DEF_MODE_DEBUG==true) error_log("TRACE :: MKDIR : SUCCES");			
		}
		
		
	}
	else{
		if ($simul) echo htmlentities(dbMakeInsertReq($node)).";<br />\n";
	}
	
	
}

// purge cms_page
$db->Execute('ALTER TABLE `cms_page` ORDER BY `id_page`;');
if ($simul) echo "<h2>-- cms_page</h2>";
$lastId =  getNextVal("cms_page", "id_page");
// $sql = "SELECT * FROM `cms_page` where id_site = ".$idSte.";";
$sql = "SELECT * FROM `cms_page` where id_site = ".$idSte." and  nodeid_page IN (".implode(",", array_keys($array_cms_arbo_pages) ) .");";
if ($simul) echo $sql;

$aPages = dbGetObjectsFromRequete("Cms_page", $sql);
$firstId = $aPages[0]->id_page;

if($offsetPageId<=$firstId){
	$offsetPageId = 0;
}
else{
	$offsetPageId = ($offsetPageId-$firstId);
}

$aCache_cms_pages = array ();
if ($simul) echo '-- offset = '.$offsetPageId."<br />\n";
$offsetPageId = 0;
$lastId =  getNextVal("cms_page", "id_page");
foreach($aPages as $key => $page){
	$aCache_cms_pages[$page->getId_page()]= $lastId+$offsetPageId;
	$page->setId_page($lastId+$offsetPageId);
	$offsetPageId++;
	if ($page->getNodeid_page() != 0){
		//$page->setNodeid_page($page->getNodeid_page()+$offsetNodeId) ;  ???????????????????????????????????????
		//$page->setNodeid_page($offsetNodeId); 
		$page->setNodeid_page($array_cms_arbo_pages[$page->getNodeid_page()]); 
		
	}
	if ($page->getGabarit_page() > 1){
		$page->setGabarit_page($page->getGabarit_page());
	} 
	$page->setDatedlt_page("0000-00-00 00:00:00");
        
        if(is_post('theme') && $_POST['theme'] != "" && $_POST['theme'] != -1)
            $page->set_theme((int)$_POST['theme']); 
		else {
			$page->set_theme($page->get_theme()); 
			$_POST['theme'] = $page->get_theme();
		}
        
	//dumpObjLite($page);
	if ($simul) echo "-- create page ".$page->getId_page()."<br />\n";
	if ($simul == false){
		dbInsert($page);
	}
	else{
		if ($simul) echo htmlentities(dbMakeInsertReq($page)).";<br />\n";
	}
}

// purge cms_infos_pages
$db->Execute('ALTER TABLE `cms_infos_pages` ORDER BY `id`;');
if ($simul) echo "<h2>-- cms_infos_pages</h2>";
$lastId =  getNextVal("cms_infos_pages", "id");
$sql = "SELECT cms_infos_pages.* FROM `cms_infos_pages`, `cms_page` WHERE cms_page.id_site = ".$idSte." AND  cms_page.id_page IN (".implode(",", array_keys($aCache_cms_pages) ) .") AND cms_infos_pages.page_id = cms_page.id_page;";
if ($simul) echo $sql;

$aInfos_pages = dbGetObjectsFromRequete("Cms_infos_page", $sql);


$offsetInfoPageId = 0;

if ($simul) echo '-- offset = '.$offsetInfoPageId."<br />\n";
$lastId =  getNextVal("cms_infos_pages", "id");
foreach($aInfos_pages as $key => $infopage){
	$infopage->setId($lastId+$offsetInfoPageId);
	$offsetInfoPageId++;
	$infopage->setPage_id($aCache_cms_pages[$infopage->getPage_id()]);
	if ($simul) echo "-- create info_page ".$infopage->getId()."<br />\n";
	if ($simul == false){
		dbInsert($infopage);
	}
	else{
		if ($simul) echo htmlentities(dbMakeInsertReq($infopage)).";<br />\n";
	}
}

/*
// purge cms_content
$db->Execute('ALTER TABLE `cms_content` ORDER BY `id_content`;');
if ($simul) echo "<h2>-- cms_content </h2>";
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

if ($simul) echo '-- offset = '.$offsetContentId."<br />\n";

foreach($aContent as $key => $content){
	$content->setId_content($content->getId_content()+$offsetContentId);
	if ($content->getNodeid_content() >= 0){
		$content->setNodeid_content($content->getNodeid_content()+$offsetNodeId);
	}
	$content->setId_site($idNewSte);
	//dumpObjLite($content);
	if ($simul) echo "-- create content ".$content->getId_content().";<br />\n";
	if ($simul == false){
		dbInsert($content);
	}
	else{
		if ($simul) echo htmlentities(dbMakeInsertReq($content)).";<br />\n";
	}
}

// purge cms_archi_content
$db->Execute('ALTER TABLE `cms_archi_content` ORDER BY `id_archi`;');
if ($simul) echo "<h2>-- cms_archi_content </h2>";
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

if ($simul) echo '-- offset = '.$offsetArchiContentId."<br />\n";

foreach($aArchiContent as $key => $archi){
	$archi->setId_archi($archi->getId_archi()+$offsetArchiContentId);
	$archi->setId_content_archi($archi->getId_content_archi()+$offsetContentId);
	//dumpObjLite($archi);
	if ($simul) echo "-- create cms_archi_content ".$archi->getId_archi()."<br />\n";
	if ($simul == false){
		dbInsert($archi);
	}
	else{
		if ($simul) echo htmlentities(dbMakeInsertReq($archi)).";<br />\n";
	}
}

// purge cms_struct_page
$db->Execute('ALTER TABLE `cms_struct_page` ORDER BY `id_struct`;');
if ($simul) echo "<h2>-- cms_struct_page</h2>";
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
*/ 

// purge classe
if ($simul) echo "<h2>-- cms_assoclassepage</h2>";
$db->Execute('ALTER TABLE `cms_assoclassepage` ORDER BY `xcp_id`;');
if ($offsetAssoClassePageId==-1){ // n'existe pas sur la cible :: la créer
	$oO = new cms_assoclassepage();
	if ($simul) echo $oO->sMySql.";<br />";
	$offsetAssoClassePageId=0;
	$sql = "SELECT * FROM cms_assoclassepage AND  xcp_cms_page IN (".implode(",", array_keys($aCache_cms_pages) ) .") ;";
}
else{
	$lastId =  getNextVal("cms_assoclassepage", "xcp_id");
	$sql = "SELECT cms_assoclassepage.* FROM cms_assoclassepage, cms_page WHERE cms_page.id_site = ".$idSte."  AND  xcp_cms_page IN (".implode(",", array_keys($aCache_cms_pages) ) .")  AND cms_page.id_page = cms_assoclassepage.xcp_cms_page ORDER BY cms_assoclassepage.xcp_id;";
}
$aO = dbGetObjectsFromRequete("cms_assoclassepage", $sql);
$firstId = $aO[0]->id;

if($offsetAssoClassePageId<=$firstId){
	$offsetAssoClassePageId = 0;
}
else{
	$offsetAssoClassePageId = ($offsetAssoClassePageId-$firstId);
}

if ($simul) echo '-- offset = '.$offsetAssoClassePageId."<br />\n";

foreach($aO as $key => $oO){

	$oO->set_id($lastId+$offsetAssoClassePageId);
	$offsetAssoClassePageId++;
	$oO->set_cms_page($aCache_cms_pages[$oO->get_cms_page()]);
	$oO->set_classe($oO->get_classe());	
	
	/*
	ATTENTION (duplication de l'objet)
	*/
	//echo $oO->get_classe(); 
	$oClasse = new Classe ($oO->get_classe());
	
	eval ("$"."oNewObjet = new ". $oClasse->get_nom()." (".$oO->get_objet().") ;"); 
	$oNewObjet->set_id(-1); 
	if ($simul == false){ 
		dbInsertWithAutoKey($oNewObjet);
	}
	else{
		if ($simul) echo htmlentities(dbMakeInsertReq($oNewObjet)).";<br />\n";
	}
	
	/*
	*/
	
	$oO->set_objet($oNewObjet->get_id());	
	//dumpObjLite($struct_pages);
	if ($simul) echo "-- create cms_assoclassepage ".$oO->get_id()."<br />\n";
	if ($simul == false){ 
		dbInsert($oO);
	}
	else{
		if ($simul) echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
	}
}

/*
// purge cms_js
$db->Execute('ALTER TABLE `cms_js` ORDER BY `cms_id`;');
if ($simul) echo "<h2>-- cms_js</h2>";
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

if ($simul) echo '-- offset = '.$offsetJsId."<br />\n";

foreach($aO as $key => $oO){
	$oO->set_id($oO->get_id()+$offsetJsId);
	if ($simul) echo "-- create cms_js ".$oO->get_id()."<br />\n";
	if ($simul == false){
		dbInsert($oO);
	}
	else{
		if ($simul) echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
	}
}


*/

//	$offsetAssoJsArbopagesId = 0;
// purge cms_assojsarbopages
$db->Execute('ALTER TABLE `cms_assojsarbopages` ORDER BY `xja_id`;');
if ($simul) echo "<h2>-- cms_assojsarbopages</h2>";
$lastId =  getNextVal("cms_assojsarbopages", "xja_id");
$sql = "SELECT DISTINCT cms_assojsarbopages.* FROM cms_js, cms_assojsarbopages, cms_arbo_pages WHERE cms_assojsarbopages.xja_cms_js = cms_js.cms_id AND cms_assojsarbopages.xja_cms_arbo_pages = cms_arbo_pages.node_id AND cms_arbo_pages.node_id_site = ".$idSte." AND xja_cms_arbo_pages IN (".implode(",", array_keys($array_cms_arbo_pages) ) .") ORDER BY cms_assojsarbopages.xja_id ;";
$aO = dbGetObjectsFromRequete("cms_assojsarbopages", $sql);
 
$offsetAssoJsArbopagesId= 0;
foreach($aO as $key => $oO){
	$oO->set_id($lastId+$offsetAssoJsArbopagesId);
	$offsetAssoJsArbopagesId++;
	$oO->set_cms_js($oO->get_cms_js()+$offsetJsId);
	$oO->set_cms_arbo_pages($array_cms_arbo_pages[$oO->get_cms_arbo_pages()]);
	if ($simul) echo "-- create cms_assojsarbopages ".$oO->get_id()."<br />\n";
	if ($simul == false){
		dbInsert($oO);
	}
	else{
		if ($simul) echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
	}
}
 
//	$offsetAssoPrependArbopagesId = 0;
// purge cms_assoprependarbopages
$db->Execute('ALTER TABLE `cms_assoprependarbopages` ORDER BY `xpa_id`;');
if ($simul) echo "<h2>-- cms_assoprependarbopages</h2>";
$lastId =  getNextVal("cms_assoprependarbopages", "xpa_id");
$sql = "SELECT DISTINCT cms_assoprependarbopages.* FROM cms_prepend, cms_assoprependarbopages, cms_arbo_pages WHERE cms_assoprependarbopages.xpa_cms_prepend = cms_prepend.ppd_id AND cms_assoprependarbopages.xpa_cms_arbo_pages = cms_arbo_pages.node_id AND cms_arbo_pages.node_id_site = ".$idSte." AND xpa_cms_arbo_pages IN (".implode(",", array_keys($array_cms_arbo_pages) ) .") ORDER BY cms_assoprependarbopages.xpa_id;";
$aO = dbGetObjectsFromRequete("cms_assoprependarbopages", $sql);
  
$offsetAssoPrependArbopagesId = 0;
foreach($aO as $key => $oO){
	$oO->set_id($lastId+$offsetAssoPrependArbopagesId);
	$oO->set_cms_arbo_pages($array_cms_arbo_pages[$oO->get_cms_arbo_pages()]);
	
	if ($simul) echo "-- create cms_assoprependarbopages ".$oO->get_id()."<br />\n";
	if ($simul == false){
		dbInsert($oO);
	}
	else{
		if ($simul) echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
	}
}
 

//	$offsetAssoVideoPageId = 0;
// purge cms_assovideopage
$db->Execute('ALTER TABLE `cms_assovideopage` ORDER BY `xvp_id`;');
if ($simul) echo "<h2>-- cms_assovideopage</h2>";
$lastId =  getNextVal("cms_assovideopage", "xvp_id");
$sql = "SELECT DISTINCT cms_assovideopage.* FROM cms_video, cms_assovideopage, cms_page WHERE cms_assovideopage.xvp_cms_video = cms_video.cms_id AND cms_assovideopage.xvp_cms_page = cms_page.id_page AND cms_page.id_site = ".$idSte." AND xvp_cms_page IN (".implode(",", array_keys($aCache_cms_pages) ) .")  ORDER BY cms_assovideopage.xvp_id;";
$aO = dbGetObjectsFromRequete("cms_assovideopage", $sql);

$offsetAssoVideoPageId = 0;

foreach($aO as $key => $oO){
	$oO->set_id($lastId+$offsetAssoVideoPageId);
	$offsetAssoVideoPageId++;
	$oO->set_cms_page($aCache_cms_pages[$oO->get_cms_page()]);
	$oO->set_cms_video($oO->get_cms_video()+$offsetVideoId);
	
	if ($simul) echo "-- create cms_assovideopage ".$oO->get_id()."<br />\n";
	if ($simul == false){
		dbInsert($oO);
	}
	else{
		if ($simul) echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
	}
}



// classe propre à chaque projet

$sql = "SELECT * FROM `classe` where cms_iscms = 0 ;";
 
$aClasses = dbGetObjectsFromRequete("classe", $sql);

$aClasses_to_duplicate = array();
foreach ($aClasses as $classe) {
 
	eval("$"."oO = new ".$classe->get_nom()."();"); 
	if (method_exists($oO, 'get_page')){  
		$aClasses_to_duplicate[] = $classe->get_nom();
	}
}
 
 
foreach ($aClasses_to_duplicate as $classe) {

	eval("$"."oRes = new ".$classe."();"); 
	
	if (!is_null($oRes->XML_inherited))
		$sXML = $oRes->XML_inherited;
	else	$sXML = $oRes->XML;
	
	xmlClassParse($sXML);

	$classeName = $stack[0]["attrs"]["NAME"]; 
	$classePrefixe = $stack[0]["attrs"]["PREFIX"];
	$aNodeToSort = $stack[0]["children"];
	
	for ($i=0;$i<count($aNodeToSort);$i++) {
		if ($aNodeToSort[$i]["name"] == "ITEM" && $aNodeToSort[$i]["attrs"]["FKEY"] == "cms_page") { 
			$libelle = $aNodeToSort[$i]["attrs"]["NAME"];
		}
	} 
	$sql = "SELECT * FROM `".$classe."` where ".$classePrefixe."_".$libelle." IN (".implode(",", array_keys($aCache_cms_pages) ) .") ;";	
	 
	$aObject = dbGetObjectsFromRequete($classe, $sql);


	$offset = 0;
	 
	$lastId =  getNextVal($classe, $classePrefixe."_id");
	foreach($aObject as $key => $oO){
	 
		$oO->set_id($lastId+$offset);
		$offset++;
		eval("$"."id_page = "."$"."oO->get_".$libelle."();");
		eval("$"."oO->set_".$libelle."(".$aCache_cms_pages[$id_page].");");
		 
		if ($simul == false){
			dbInsert($oO);
		}
		else{
			if ($simul) echo htmlentities(dbMakeInsertReq($oO)).";<br />\n";
		}
	}

	//
}


  
?>