<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/lib.inc.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php'); 
include_once("scan.lib.php");

if (!isset($classeName)){
	$classeName = preg_replace('/[^_]*_(.*)\.php/si', '$1', basename($_SERVER['PHP_SELF']));
}


define('MAGPIE_DIR', 'backoffice/cms/lib/rss/');
 
require_once(MAGPIE_DIR.'rss_fetch.inc');
 
if ($_GET['id']) {
	$id=$_GET['id'];
	if ($id != -1) {
		// objet 
		
		eval("$"."oClasse = new ".$classeName."($"."id);");
		if (!$oClasse->get_is_firstfeed()) { 
			$isnew = true; 
			$eNew_entries = set_new_flux ($classeName, $id, $isnew); 
			echo "$eNew_entries nouvelle(s) entrée(s).<br>";
			$oClasse->set_is_firstfeed(1); 
			
			
		}
		else {
			echo "Ce flux RSS a déjà été pris en compte.<br>";
		}
		$oClasse->set_dtmod(date("d/m/Y"));
		$bRetour = dbUpdate ($oClasse);
		
	}
	
	
	// on crée le fichier d'automatisation
	if (!is_dir($_SERVER['DOCUMENT_ROOT']."/custom/rss")){
		mkdir($_SERVER['DOCUMENT_ROOT']."/custom/rss", 0777);
		$list = fopen($_SERVER['DOCUMENT_ROOT']."/custom/rss/check_scan_cms_rss_url.sh", "x+");
		$listContent = '#!/bin/bash

url="http://'.$_SERVER['HTTP_HOST'].'/backoffice/cms/cms_rss_url/check_scan_cms_rss_url.php"

wget  $url';
		fwrite($list, $listContent);
		fclose($list); 
	} 
	 
} 

 

?>

 
