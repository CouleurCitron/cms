<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');  
header('Content-Type: application/x-vbs'); 
/*
define ("DEF_FLASHPLAYERREQUIRED", "9"); // valeurs possibles : -1 = pas flash, 1,2,...,8,9,etc
define ("DEF_FLASHPLAYERLATEST", "9"); // valeurs possibles : 1,2,...,8,9,etc
*/
if (intval(DEF_FLASHPLAYERREQUIRED) > 0){
?> 
on error resume next
If MSDetect = "true" Then
  For i = 7 to <?php echo DEF_FLASHPLAYERLATEST; ?>
  
   If Not(IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash." & i))) Then
		flashinstalled = 1
    Else
		flashinstalled = 2
		flashversion = i
    End If
  Next
End If

If flashinstalled = 0 Then
	flashinstalled = 1
End If
<?php
}
?>