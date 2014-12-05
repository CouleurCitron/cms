<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/lib/phpfastcache/php_fast_cache.php'); 
phpFastCache::$storage = "auto";
	
$html = phpFastCache::get(md5($_SERVER['REQUEST_URI']));
if($html == null) {	
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

	ob_start();
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/list.fkey.php');
	$html = ob_get_flush();
	phpFastCache::set(md5($_SERVER['REQUEST_URI']),$html,120);
}
else{
	echo $html;
}
?>
