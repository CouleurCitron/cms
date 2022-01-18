<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

// permet de dérouler le menu contextuellement
activateMenu("gestionclasse");


function processDir($file){
	global $httpBase;

	if ($subdir = @opendir($file)) {
		while (($subfile = readdir($subdir)) !== false) {
			if (preg_match("/\.class\.php$/",$subfile)==1) {
				$fullpath = $file.$subfile;
				//echo $fullpath;
				$relPath = str_replace($_SERVER['DOCUMENT_ROOT'], "", $fullpath);

				$fh = fopen( $fullpath, "r");
				
				if ($fh){
					$sClassSRC = "";
					while(!feof($fh)) {
						$sTemp=fgets($fh);
						if (preg_match("/objet de BDD/msi",$sTemp)){
							$sClassSRC = $sTemp;
							break;
						}
					}
					
					//objet de BDD dealer :: class dealer
					$sClasseName = trim(preg_replace("/.*objet de BDD ([^ ]+) :: .*/msi", "$1", $sClassSRC));
					
					if (basename($relPath) == $sClasseName.".class.php"){
						echo $sClasseName." is valid (".basename($relPath).")<br />\n";
						
						$aClasse = dbGetObjectsFromFieldValue("classe", array("get_nom"),  array($sClasseName), NULL);
				
						if (count($aClasse) > 0){
							echo $sClasseName." is already registered<br />\n";
						}
						else{
							echo $sClasseName." should be registered<br />\n";
							$oClass = new classe();
							$oClass->set_nom($sClasseName);
							$oClass->set_iscms(0);
							dbSauve($oClass);
						}
					}
					else{
						echo $sClasseName." is NOT valid (".basename($relPath).")<br />\n";
					}
					echo "<hr />";
					
				}
				
			}
			if ((is_dir($file."/".$subfile)) and ($subfile != ".") and ($subfile != "..")) {
				// sous rep xxxxxxxxxxxxxxxxxxxxxxxxxxxx
				//processDir($file."/".$subfile);
  			} 
		} 
		closedir($subdir);
	}
	return true;
}

$targetDir = "/include/bo/class/";
processDir($_SERVER['DOCUMENT_ROOT'].$targetDir);

?>