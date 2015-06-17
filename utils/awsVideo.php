<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
if (preg_match('/Mobile/si', $_SERVER['HTTP_USER_AGENT'])==1){ //mobile	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php
}
else{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php
}
?>
<title>CC FLV player</title>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color:#000000;
	color:#FFFFFF;
}
-->
</style>
</head>
<body>
<?php include("awsVideo.inc.php"); ?>
</body>
</html>
