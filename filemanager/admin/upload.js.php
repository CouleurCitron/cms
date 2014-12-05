<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');

header('Content-Type: application/x-javascript'); 
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always	modified
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0

if ((isset($_SESSION["BO"]["LOGGED"]) != true)||($_SERVER['HTTP_REFERER'] == NULL)){
//if (1 == 2){
	header("HTTP/1.0 403 Forbidden");
	die("403 Forbidden");
}
else{
	if(intval($_SESSION['filemanager'])<0){ // cas cms_filemanager
		$oFM = new cms_filemanager(abs(intval($_SESSION['filemanager'])));
		$base_dir=$ROOT.'custom/filemanager/'.$_SESSION['rep_travail'].'/files';
		dirExists($base_dir);
		$FM_ftpUser=$oFM->get_login();
		$FM_ftpPwd=$oFM->get_passwd();
	} 
	//$sAppletInclusion = "<applet code=\"ZUpload\" Archive=\"/backoffice/cms/filemanager/admin/ZUpload/ZUpload.jar\" width=\"450\" height=\"300\" border=\"0\"><param name=\"host\" value=\"".$_SERVER['HTTP_HOST']."\"><param name=\"user\" value=\"".$FM_ftpUser."\"><param name=\"pass\" value=\"".$FM_ftpPwd."\"><param name=\"path\" value=\"".$rel_dir."\"><param name=\"postscript\" value=\"".$_SERVER['HTTP_REFERER']."\">";
?>
//document.open();
//document.write("<?php echo addslashes($sAppletInclusion); ?>");
//document.close();
 if (isValidJVM())  
        {  
			document.open();  
            document.write('<applet code="Uploader.class" archive="/backoffice/cms/lib/uploader/Uploader.jar,/backoffice/cms/lib/uploader/Net.jar" width="430" height="29" id="uploader">');  
            document.write('    <param name="address" value="<?php echo $_SERVER['HTTP_HOST']; ?>" />');  
            document.write('    <param name="username" value="<?php echo $FM_ftpUser; ?>" />');  
            document.write('    <param name="password" value="<?php echo $FM_ftpPwd; ?>" />');  
            document.write('    <param name="uploadedFileName" value="/test.upload" />');  
            document.write('    <param name="backgroundColor" value="#FFFFFF" />');  
            document.write('    <param name="foregroundColor" value="#000000" />');  
            document.write('</applet>');  
			document.close();  
            document.getElementById("error").style.display = "none";  
        }  
<?php
}
?>