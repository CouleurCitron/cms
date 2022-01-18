<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
require 'cms-inc/lib/packer.php/class.JavaScriptPacker.php';
header('Content-Type: application/x-javascript'); 

if (!is_get('file')){
	$script = file_get_contents('fojsutils.js');
}
else{
	$script = file_get_contents($_GET['file']);	
}
$packer = new JavaScriptPacker($script, 'Normal', true, false);
$packed = $packer->pack();	

echo $packed;
?>