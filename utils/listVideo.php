<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');


$file = $_SERVER['DOCUMENT_ROOT']."/custom/upload/video/";


if ($subdir = @opendir($file)) {
	while (($subfile = readdir($subdir)) !== false) {
		if (ereg("\.flv$",$subfile) or ereg("\.FLV$",$subfile)) {
			$fullpath = $file."/".$subfile;
			echo "<a href=\"/backoffice/cms/utils/streamvideoLarge.php?file=/custom/upload/video/".rawurlencode($subfile)."\" target=\"_blank\">".$subfile."</a><br />";
						
		}

	} 
	closedir($subdir);
}




?>