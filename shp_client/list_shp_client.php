<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

$script = explode('/',$_SERVER['PHP_SELF']);
$script = $script[sizeof($script)-1];


	
include('cms-inc/autoClass/list.php'); ?>