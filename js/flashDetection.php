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
//alert(flashinstalled);
//alert(flashversion);
if((flashversion < <?php echo DEF_FLASHPLAYERREQUIRED; ?>) && (flashversion!=0)){
	window.location.href = '/noFlash.php';
}
// -->
<?php
}
?>