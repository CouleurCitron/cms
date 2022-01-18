<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

$script = explode('/',$_SERVER['PHP_SELF']);
$script = $script[newSizeOf($script)-1];

if (is_file($_SERVER['DOCUMENT_ROOT'].'/include/bo/cms/prepend.'.$script))
	require_once('include/bo/cms/prepend.'.$script);

include('cms-inc/autoClass/list.php');
?>