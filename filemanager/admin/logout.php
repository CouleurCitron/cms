<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 
session_start();
session_destroy();
$ret="Location : admin.php";
header($ret);
?>