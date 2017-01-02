<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

if (is_post('site')){
	$idSte = $_POST['site'];
}
else{
	$idSte = 1;
}
?>
<form id="dupli" name="dupli" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
id site à checker :<br />
<!--<input type="text" value="1" id="sourcesite" name="sourcesite" />-->
<select id="site" name="site">
<?php
$aSite = dbGetObjectsFromRequete("cms_site", "select * from cms_site ");
if ($aSite != false){
	foreach ($aSite as $key => $oSite){
		if ($oSite->get_id() == $idSte){
			$selected = " selected ";
		}
		else{
			$selected = "";
		}
		echo "<option value=\"".$oSite->get_id()."\"".$selected.">".$oSite->get_name()."</option>\n";
	}
}
?>
</select>
<input type="submit" />
</form>
<hr />
<?php
// purge cms_page
echo "<h2>-- cms_page</h2>";
$sql = "SELECT * FROM `cms_page` where id_site = ".$idSte.";";
$aPages = dbGetObjectsFromRequete("Cms_page", $sql);

foreach($aPages as $key => $page){
	$sql = "SELECT * from cms_arbo_pages where node_id = ".$page->nodeid_page." AND node_id_site  = ".$page->id_site.";";	
	$aNodes = dbGetObjectsFromRequete("Cms_arbo_pages", $sql);
	
	if (count($aNodes) == 0){		
		if ($page->nodeid_page != 0){
			echo "-- node : ".$page->nodeid_page."<br />\n";
			echo "-- site : ".$page->id_site."<br />\n";		
			echo "-- XXX ALERTE : page ".$page->id_page." pointant vers node mort ".$page->nodeid_page."<br />\n\n";
			echo "DELETE FROM cms_page WHERE nodeid_page = ".$page->nodeid_page.";<br />\n";
			//echo "SELECT * FROM cms_page WHERE nodeid_page = ".$page->nodeid_page.";<br />\n";
			echo "<br />\n";
		}		
	}
	else{
		//echo "ok<br />\n<br />\n";
	}
	
	// contenus 		
	$aContent = getContentFromPage($page->get_id_page(), 0);
	
	if($aContent){
		for ($a=0; $a<sizeof($aContent); $a++){
			$oContent = $aContent[$a];				
			if (intval($oContent->get_isbriquedit_content())==1){
				$oContent->updateNoeud($page->get_nodeid_page());
			}
		}
	}
}
// purge cms_assoclassepage
echo "<h2>-- cms_assoclassepage</h2>";
$sql = "SELECT * FROM `cms_assoclassepage`;";
$aO = dbGetObjectsFromRequete("cms_assoclassepage", $sql);

foreach($aO as $key => $oO){
	//pre_dump($infos_pages);
	$sql = "SELECT * from cms_page where id_page = ".$oO->get_cms_page().";";	
	$aPages = dbGetObjectsFromRequete("Cms_page", $sql);
	
	if (count($aPages) == 0){
		echo "-- XXX ALERTE : cms_assoclassepage ".$oO->get_id()." pointant vers page morte ".$oO->get_cms_page()."<br />\n\n";
		echo "DELETE FROM cms_assoclassepage WHERE Xcp_cms_page = ".$oO->get_cms_page().";<br />\n";
		echo "<br />\n";
	}
	else{
		//echo "ok<br />\n<br />\n";
	}
}
// purge cms_infos_pages
echo "<h2>-- cms_infos_pages</h2>";
$sql = "SELECT * FROM `cms_infos_pages`;";
$aInfos_pages = dbGetObjectsFromRequete("Cms_infos_page", $sql);

foreach($aInfos_pages as $key => $infos_pages){
	//pre_dump($infos_pages);
	$sql = "SELECT * from cms_page where id_page = ".$infos_pages->page_id.";";	
	$aPages = dbGetObjectsFromRequete("Cms_page", $sql);
	
	if (count($aPages) == 0){
		echo "-- XXX ALERTE : info_page ".$infos_pages->id." pointant vers page morte ".$infos_pages->page_id."<br />\n\n";
		echo "DELETE FROM cms_infos_pages WHERE page_id = ".$infos_pages->page_id.";<br />\n";
		//echo "SELECT * FROM cms_infos_pages WHERE page_id = ".$infos_pages->page_id.";<br />\n";
		echo "<br />\n";
	}
	else{
		//echo "ok<br />\n<br />\n";
	}
}
// purge cms_struct_page
echo "<h2>-- cms_struct_page</h2>";
$sql = "SELECT * FROM `cms_struct_page`;";
$aStruct_pages = dbGetObjectsFromRequete("Cms_struct_page", $sql);

foreach($aStruct_pages as $key => $struct_pages){
	//pre_dump($infos_pages);
	$sql = "SELECT * from cms_page where id_page = ".$struct_pages->id_page.";";	
	$aPages = dbGetObjectsFromRequete("Cms_page", $sql);
	
	if (count($aPages) == 0){
		echo "-- XXX ALERTE : struct_pages ".$struct_pages->id." pointant vers page morte ".$struct_pages->page_id."<br />\n\n";
		echo "DELETE FROM cms_struct_page WHERE id_page = ".$struct_pages->id_page.";<br />\n";
		//echo "SELECT * FROM cms_struct_page WHERE id_page = ".$struct_pages->id_page.";<br />\n";
		echo "<br />\n";
	}
	else{
		//echo "ok<br />\n<br />\n";
	}
}
// purge cms_content
echo "<h2>-- cms_content / arbo</h2>";
$sql = "SELECT * FROM `cms_content` where id_site = ".$idSte.";";
$aContent = dbGetObjectsFromRequete("Cms_content", $sql);

foreach($aContent as $key => $content){
	$sql = "SELECT * from cms_arbo_pages where node_id = ".$content->nodeid_content." AND node_id_site  = ".$content->id_site.";";	
	$aNodes = dbGetObjectsFromRequete("Cms_arbo_pages", $sql);
	
	if (count($aNodes) == 0){
		if ($content->nodeid_content > 0){
			echo "<h2>-- node : ".$content->nodeid_content."<br />\n";
			echo "-- XXX ALERTE : content ".$content->id_content."  (".$content->name_content.") pointant vers node mort ".$content->nodeid_content."<br />\n\n";
			echo "DELETE FROM cms_content WHERE nodeid_content = ".$content->nodeid_content.";<br />\n";
			//echo "SELECT * FROM cms_content WHERE nodeid_content = ".$content->nodeid_content.";<br />\n";
			echo "<br /></h2>\n";	
		}
	}
	else{
		//echo "ok<br />\n<br />\n";
	}
}
// purge cms_content
echo "<h2>-- cms_content / struct</h2>";
$sql = "SELECT * FROM cms_content where id_site = ".$idSte.";";
$aContent = dbGetObjectsFromRequete("Cms_content", $sql);

foreach($aContent as $key => $content){
	$sql = "SELECT * from cms_struct_page where id_content = ".$content->id_content.";";	
	$aStruct = dbGetObjectsFromRequete("Cms_struct_page", $sql);
	
	if (count($aStruct) == 0){

		if ($content->isbriquedit_content = 1){
			echo "-- XXX ALERTE : brique edit cms_content ".$content->id_content." (".$content->name_content.") lié a zero page struct <br />\n\n";
			//echo "DELETE FROM cms_content WHERE id_content = ".$content->id_content.";<br />\n";
			echo "SELECT * FROM cms_content WHERE id_content = ".$content->id_content.";<br />\n";
		}
		else{
			echo "-- XXX ALERTE : brique fixe cms_content ".$content->id_content." (".$content->name_content.") lié a zero page struct <br />\n\n";
			//echo "DELETE FROM cms_content WHERE id_content = ".$content->id_content.";<br />\n";
			echo "SELECT * FROM cms_content WHERE id_content = ".$content->id_content.";<br />\n";			
		}
		echo "<br />\n";
	}
	else{
		//echo "ok<br />\n<br />\n";
	}
}

// purge cms_content
echo "<h2>-- cms_content / struct</h2>";
$sql = "SELECT * FROM cms_struct_page;";
$aStruct = dbGetObjectsFromRequete("Cms_struct_page", $sql);

foreach($aStruct as $key => $struct){
	$sql = "SELECT * from cms_content where id_content = ".$struct->id_content.";";	
	$aContent = dbGetObjectsFromRequete("Cms_struct_page", $sql);
	
	if (count($aContent) == 0){


		echo "-- XXX ALERTE : cms_struct_page ".$struct->id_struct." pointant sur content mort : ".$struct->id_content."<br />\n\n";
		echo "DELETE FROM cms_struct_page WHERE id_content = ".$struct->id_content.";<br />\n";


		echo "<br />\n";
	}
	else{
		//echo "ok<br />\n<br />\n";
	}
}

// purge cms_content UTF8
echo "<h2>-- cms_content / UTF8</h2>";
$sql = "SELECT * FROM cms_content;";

$aContent = dbGetObjectsFromRequete("Cms_content", $sql);

foreach($aContent as $key => $content){

	if (!preg_match("/swf/msi",$content->getType_content())){ // pas de correction utf8 sur les contents SWF
		if (preg_match("/Ã©/msi", $content->getHtml_content())){ // Ã©
			echo "content ".$content->getId_content()." seems to be UTF8<br />\n";
			$content->setHtml_content(utf8_decode($content->getHtml_content()));
			echo "correct latin content should be ".$content->getHtml_content()."<br />\n";
			echo "correction reussie? : ".dbUpdate($content)."<br /><br />\n";
		}
	}
}


?>