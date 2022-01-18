<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');  
header('Content-Type: application/x-javascript'); 
/*
define ("DEF_FLASHPLAYERREQUIRED", "9"); // valeurs possibles : -1 = pas flash, 1,2,...,8,9,etc
define ("DEF_FLASHPLAYERLATEST", "9"); // valeurs possibles : 1,2,...,8,9,etc
*/
if (intval(DEF_FLASHPLAYERREQUIRED) > 0){
?> 
<!--
var flashinstalled = 0;
var flashversion = 0;
MSDetect = "false";
if (navigator.plugins && navigator.plugins.length){
  x = navigator.plugins["Shockwave Flash"];
  if (x)  {
    flashinstalled = 2;
    if (x.description)    {
      y = x.description;
      var versionNum=new RegExp(".+ ([0-9]{1,3})\..*", "i")
	  flashversion = y.replace(versionNum, "$1");
      //flashversion = y.charAt(y.indexOf('.')-1);
    }
  }else{   
    flashinstalled = 1;
  }
 <?php
 for ($v=1;$v<=DEF_FLASHPLAYERLATEST;$v++){
  echo  "if (navigator.plugins[\"Shockwave Flash ".$v.".0\"]){\n";
  if ($v < DEF_FLASHPLAYERREQUIRED){
  	echo  "  flashinstalled = 1;\n";
  }
  else{
  	echo  "  flashinstalled = 2;\n";
  }
  echo  "  flashversion = ".$v.";\n";
  echo  "}\n";
 }
 ?>
}else if (navigator.mimeTypes && navigator.mimeTypes.length){
  x = navigator.mimeTypes['application/x-shockwave-flash'];
  if (x && x.enabledPlugin){
    flashinstalled = 2;
  }else{
    flashinstalled = 1;
  }
}else{
  MSDetect = "true";
}
// -->
<?php
}
?>