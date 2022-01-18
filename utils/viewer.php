<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 
$file = $_GET['file'];
if (is_get('chemin')){
	$file = $_GET['chemin'].$file;
}

if (is_file($_SERVER['DOCUMENT_ROOT'].$file)){
	// nada tout va bien
}
elseif (is_file($_SERVER['DOCUMENT_ROOT']."/".$file)){
	$file = "/".$file;
}
else{
	die($file." est manquant");
}

switch(strrchr(basename(strtolower($file)), ".")) {
	case ".flv":
		$viewer = "/backoffice/cms/utils/streamvideoLarge.php?file=".$file;
		break;
	case ".jpeg":
		$viewer = "/backoffice/cms/utils/img.php?file=".$file;
		break;
	case ".jpg":
		$viewer = "/backoffice/cms/utils/img.php?file=".$file;
		break;
	case ".png":
		$viewer = "/backoffice/cms/utils/img.php?file=".$file;
		break;
	case ".gif":
		$viewer = "/backoffice/cms/utils/img.php?file=".$file;
		break;
	case ".pdf":
		$viewer = "/backoffice/cms/utils/telecharger.php?file=".basename($file)."&chemin=".str_replace(basename($file), "", $file)."&";		
		break;
	case ".doc":
		$viewer = "/backoffice/cms/utils/telecharger.php?file=".basename($file)."&chemin=".str_replace(basename($file), "", $file)."&";		
		break;
	default:
		//$viewer = $file;
		$viewer = "/backoffice/cms/utils/passthru.php?file=".basename($file)."&chemin=".str_replace(basename($file), "", $file)."&";
		break;
}
$headerRedirect = "Location: http://".$_SERVER['SERVER_NAME'].$viewer;

header($headerRedirect);
?>