<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
require_once 'cms-inc/include_cms.php';
require_once 'cms-inc/include_class.php';


$translator =& TslManager::getInstance();


$translator->downloadLangPacks();
?>