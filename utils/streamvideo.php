<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include("backoffice/cms/utils/awsVideo.php");
die();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>FLV player</title>
<script src="/backoffice/cms/js/AC_RunActiveContent.js" type="text/javascript"></script>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color:#000000;
}
-->
</style>
</head>
<body>
<?php
$dir = str_replace(basename($_SERVER['PHP_SELF']), "", $_SERVER['PHP_SELF']);
$host = $_SERVER['SERVER_NAME'];
$file = $_GET['file'];
if (strpos($file, "/") > 0){
	$file = "/".$file;
}
//-------------------
$vidName = basename($file);
$vidURL = "http://".$host.$file;
$phpURL = "http://".$host."/backoffice/utils/flvprovider.php";

list($width, $height, $type, $attr) = getimagesize($_SERVER['DOCUMENT_ROOT']."/backoffice/cms/utils/scrubber.swf");
/*
var _vidName = "golfers.flv";
var _vidURL = "http://www.yourdomain.com/" + _vidName;
var _phpURL = "http://www.yourdomain.com/flvprovider.php";
*/
?>
<script type="text/javascript">
swfSrc = "/backoffice/cms/utils/scrubber"+"?_vidName=<?php echo $vidName; ?>&_vidURL=<?php echo $vidURL; ?>&_phpURL=<?php echo $phpURL; ?>&";
swfSrcFull = "/backoffice/cms/utils/scrubber.swf"+"?_vidName=<?php echo $vidName; ?>&_vidURL=<?php echo $vidURL; ?>&_phpURL=<?php echo $phpURL; ?>&";

AC_FL_RunContent( 'codebase','https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,1,0,0','id', 'awsvid', 'name', 'awsvid', 'width','<?php echo $width; ?>','height','<?php echo $height; ?>','src',swfSrc,'quality','high','scale', 'noscale', 'pluginspage','https://get.adobe.com/flashplayer/','movie',swfSrc);

function videoSizeUpdate(w, h){
	//alert(w+" / "+h);
	//document.getElementById('awsvid').width = w;
	//document.getElementById('awsvid').height = h;
	
	a = document.getElementsByTagName("embed");
	for (i in a){
		if (a[i].src != undefined){
			if (a[i].src == swfSrcFull){
				a[i].width = w;
				a[i].height = h+32; // taille de l'interface
			}
			
		}
	}
	
	a = document.getElementsByTagName("object");	
	for (i in a){
		if (a[i].id != undefined){
			if (a[i].id == 'awsvid'){
				a[i].width = w;
				a[i].height = h+32; // taille de l'interface
				
			}	
		}
	}
	
}
</script>
</body>
</html>
