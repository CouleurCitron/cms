<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
header("Content-type: text/xml; charset=utf-8");

if (!isset($translator)){
	$translator =& TslManager::getInstance();
}

$translator->echoLangPacks();


?>