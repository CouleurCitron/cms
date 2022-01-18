<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
header('Content-type: text/xml; charset=utf-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/utils/xml.parser.inc.php');
 
if (is_get('source')){
	echo utf8_encode(readXMLfromURL($_GET['source']));
}

?>