<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

if (!isset($_GET["f"]) || ($_GET["f"] == "")){
	$path = "/custom/upload/video/CLS_Vietnamien.mpg";
}
else{
	$path = "/custom/upload/video/".$_GET["f"];
}
if (ereg("^/.*", $path)){
	$path = $_SERVER['DOCUMENT_ROOT'].$path;
	$name = ereg_replace("[^\.]*/([^/]+)", "\\1", $path);	
}
else{
	$name = $path;
}

header("Content-Length: ".(string)(filesize($path)));
header("Content-type: video/mpeg");
header('Content-Disposition: attachment; filename="'.$name.'"');
header('Content-Transfer-Encoding: binary');
		
if ($file = fopen($path, 'rb')) {
	 while(!feof($file) and (connection_status()==0)) {
		 print(fread($file, 1024*8));
		 flush();
		 //usleep(100000); // can be used to limit download speed
	 }
 fclose($file);
}
?>