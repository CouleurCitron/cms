<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 
$file = $_GET['file'];

if (is_file($_SERVER['DOCUMENT_ROOT'].$file)){
	// nada tout va bien
}
elseif (is_file($_SERVER['DOCUMENT_ROOT']."/".$file)){
	$file = "/".$file;
}
else{
	die($file." est manquant");
}
list($width, $height, $type, $attr) = getimagesize($_SERVER['DOCUMENT_ROOT'].$file);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo basename($file); ?></title>
<script src="/backoffice/cms/js/fojsutils.js" type="text/javascript"></script>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color:#FFFFFF;
}
-->
</style>
</head>
<body>
<script type="text/javascript">
fileH = <?php echo $height; ?>;
fileW = <?php echo $width; ?>;
winH = getWindowHeight();
winW = getWindowWidth();
ratioH = winH/fileH;
ratioW = winW/fileW;
if (ratioH < ratioW){
	ratio = ratioH;
}
else{
	ratio = ratioW;
}
dispH = Math.floor(fileH*ratio);
dispW = Math.floor(fileW*ratio);

document.open();
//document.write("<img src=\"<?php echo $file; ?>\" width=\""+dispW+"\" height=\""+dispH+"\" id=\"awsimg\" alt=\"Aperçu de <?php echo basename($file); ?>\" />");
//"/backoffice/cms/utils/passthru.php?file=".basename($file)."&chemin=".str_replace(basename($file), "", $file)."&";
document.write("<img src=\"<?php echo "/backoffice/cms/utils/passthru.php?file=".basename($file)."&chemin=".str_replace(basename($file), "", $file); ?>\" width=\""+dispW+"\" height=\""+dispH+"\" id=\"awsimg\" alt=\"Aperçu de <?php echo basename($file); ?>\" />");
document.close();	
</script>
</body>
</html>
