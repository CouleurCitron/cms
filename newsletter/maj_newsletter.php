<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

if (is_file($_SERVER['DOCUMENT_ROOT'].'/include/bo/cms/prepend.'.$_SERVER['PHP_SELF']))
	require_once($_SERVER['DOCUMENT_ROOT'].'/include/bo/cms/prepend.'.$_SERVER['PHP_SELF']);

include($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/maj.php');
?>