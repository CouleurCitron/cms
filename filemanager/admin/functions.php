<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); function isimage($str)
{
$image_file = array("gif","jpg","jpeg","png");
for ( $f = 0 ; $f < count($image_file) ; $f++)
{
if ( $str == $image_file[$f] ) return true;
}
return false;
}
function iseditable($str)
{
$edit_file= array("php","txt","htm","html","php3","asp","xml","css","inc");
for ( $f = 0 ; $f < count($edit_file) ; $f++)
{
if ( $str == $edit_file[$f] ) return true;
}
return false;
}
function isviewable($str)
{
$edit_file= array("php","txt","htm","html","php3","asp","xml","css","inc");
for ( $f = 0 ; $f < count($edit_file) ; $f++)
{
if ( $str == $edit_file[$f] ) return true;
}
return false;
}
?>