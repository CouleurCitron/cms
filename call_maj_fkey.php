<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/lib/phpfastcache/php_fast_cache.php'); 
phpFastCache::setup("storage","auto");
//phpFastCache::$storage = "auto";
$cache = phpFastCache();
	
$html = $cache->get(md5($_SERVER['REQUEST_URI']));
if($html == null) {	
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

	ob_start();
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/maj.fkey.php');
	$html = ob_get_flush();
	$cache->set(md5($_SERVER['REQUEST_URI']),$html,60);
}
else{
	echo $html;
}
?>