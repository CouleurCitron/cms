<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

// permet de dérouler le menu contextuellement
activateMenu("videos");
 

function processDir($file){
	global $httpBase;
	$cpt = 0;

	if ($subdir = @opendir($file)) {
		while (($subfile = readdir($subdir)) !== false) { 
		
			if ((preg_match("/[^\.]\.flv/msi", $subfile))==1) {
				$fullpath = $file.$subfile;
				//echo $fullpath."<br />";
				$relPath = str_replace($_SERVER['DOCUMENT_ROOT'], "", $fullpath);

				$fh = fopen( $fullpath, "r");
				
				if ($fh){
				 
					$targetDir = str_replace ($_SERVER['DOCUMENT_ROOT'], "", $file);
					
					$bon_id_site = true;
					
					if ((preg_match("/custom\/video/msi", $targetDir))==1) {
						// 2 = brique vidéo 
						$type = 2;
						$sType = "[Brique video] ";
					}
					else {
						// 2 = fichier diaporama vidéo 
						$type = 3;
						$sType = "[Fichier video] ";
						// vérifier idsite
						$aFileDiapo = dbGetObjectsFromFieldValue("cms_diapo", array("get_src", "get_cms_site"),  array($subfile."".$subfile, $_SESSION["idSite"]), NULL);
						
						if( sizeof($aFileDiapo) == 0) {
							$bon_id_site = false;	
						}
						
					}
					
					if ($bon_id_site) {
					
						$aFile = dbGetObjectsFromFieldValue("cms_video", array("get_file", "get_cms_site"),  array($targetDir."".$subfile, $_SESSION["idSite"]), NULL);
						
						
						
						if (count($aFile) > 0){ 
							
							$oVideo = $aFile[0]; 
							$oVideo->set_type($type);  
							$oVideo->set_file($targetDir."".$subfile);  
							$oVideo->set_player_loc("http://".$_SERVER['HTTP_HOST']."/backoffice/cms/utils/scrubberLarge.swf?_vidName=".$subfile."&_vidURL=http://".$_SERVER['HTTP_HOST'].$targetDir.$subfile."&_phpURL=http://".$_SERVER['HTTP_HOST']."/backoffice/cms/utils/flvprovider.php&_start=0&_end=0&_autostart=1");
							$oVideo->set_cms_site($_SESSION["idSite"]);  
							
							$r = dbSauve($oVideo);
							
							if ($r) {
								echo $sType.$targetDir.$subfile." : mis à jour<br />\n";
							}
							else {
								echo $sType.$targetDir.$subfile." : erreur lors de l'import<br />\n";
							}
						}
						else {
							
							
							$oVideo = new Cms_video(); 
							$oVideo->set_type($type); 
							$oVideo->set_title($subfile); 
							$oVideo->set_content_loc("http://".$_SERVER['HTTP_HOST'].$targetDir.$subfile); 
							$oVideo->set_player_loc("http://".$_SERVER['HTTP_HOST']."/backoffice/cms/utils/scrubberLarge.swf?_vidName=".$subfile."&_vidURL=http://".$_SERVER['HTTP_HOST'].$targetDir.$subfile."&_phpURL=http://".$_SERVER['HTTP_HOST']."/backoffice/cms/utils/flvprovider.php&_start=0&_end=0&_autostart=1");
							
							
							$oVideo->set_file($targetDir."".$subfile);  
							$oVideo->set_tag($_SESSION["site_travail"]);
							$oVideo->set_cms_site($_SESSION["idSite"]); 
							$oVideo->set_statut(DEF_ID_STATUT_ATTEN);
							 
							$r = dbSauve($oVideo);
							
							if ($r) {
								echo $sType.$targetDir.$subfile." : importé<br />\n";
							}
							else {
								echo $sType.$targetDir.$subfile." : erreur lors de l'import<br />\n";
							}
								
							
						} 
						$cpt++;
						echo "<hr />";
					}
				  
				}
				
			}
			if ((is_dir($file."/".$subfile)) and ($subfile != ".") and ($subfile != "..")) {
				// sous rep xxxxxxxxxxxxxxxxxxxxxxxxxxxx
				//processDir($file."/".$subfile);
  			} 
			
		} 
		closedir($subdir);
		if ($cpt == 0) echo '<p>'.$sType.'Aucun import</p>';
	} 
	
	
	return true;
}

$targetDir = "/custom/video/".$_SESSION["rep_travail"]."/";
processDir($_SERVER['DOCUMENT_ROOT'].$targetDir);

$targetDir = "/custom/upload/cms_diapo/";
processDir($_SERVER['DOCUMENT_ROOT'].$targetDir);

?>