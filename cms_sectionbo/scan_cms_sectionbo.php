<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

// permet de dérouler le menu contextuellement
activateMenu("cms_sectionboscan");


function processDir($file){
	global $httpBase;

	if ($subdir = @opendir($file)) {
		while (($subfile = readdir($subdir)) !== false) {
			if (is_dir($subfile)  && ($subfile == ".") ) {
				$fullpath = $file;
				$relPath = str_replace($_SERVER['DOCUMENT_ROOT'], "", $fullpath);
				echo "<a href=\"".$relPath."\">".$relPath."</a>&nbsp;";
				
				$aSect = dbGetObjectsFromFieldValue("cms_sectionbo", array("get_url"),  array($relPath), NULL);
		
				if (count($aSect) > 0){
					echo "is already registered<br />\n";
				}
				else{
					echo "should be registered<br />\n";
					$oSect = new cms_sectionbo();
					$oSect->set_libelle(basename($relPath));
					$oSect->set_url($relPath);
					dbSauve($oSect);
				}

				echo "<hr />";
			}
			if ((is_dir($file."/".$subfile)) && ($subfile != ".") && ($subfile != "..") && ($subfile != "_notes") && ($subfile != "CVS")) {
				// sous rep xxxxxxxxxxxxxxxxxxxxxxxxxxxx
				processDir($file."/".$subfile);
  			} 
		} 
		closedir($subdir);
	}
	return true;
}

$targetDir = "/backoffice";
processDir($_SERVER['DOCUMENT_ROOT'].$targetDir);

?>