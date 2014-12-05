<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/class/cms_flvmeta.class.php'); // acces au cache de la meta donnée FLV

header('Content-type: text/xml; charset=utf-8');
header('Cache-Control: private, max-age=0, must-revalidate'); // ajout dans le cas SSL
header('Pragma: public'); // ajout dans le cas SSL 
echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?".">\n";
echo '<adequat>';

//full path to dir with video.
$path = $_SERVER['DOCUMENT_ROOT'];

$filename = htmlspecialchars($_GET['file']);
	
$fullfilename = $path.preg_replace('/[https]{4,5}:\/\/[^\/]+(.*)/', '\\1', $filename);
	
$filename = basename($fullfilename);
$path = str_replace($filename, '', $fullfilename);

$file = $path . $filename;


include('flvmetadata.inc.php');


echo '<video w="'.$width.'" h="'.$height.'" />';	
		
echo '</adequat>';
?>