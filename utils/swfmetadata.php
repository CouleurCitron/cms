<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

if (is_get('file')){
	$sFileName = basename($_GET['file']);
	$sFileUrl = $_GET['file'];

	if (preg_match('/\.swf$/msi', $sFileName)){
		include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/lib/getid3/getid3.php');
		$getID3 = new getID3;			
		$ThisFileInfo = $getID3->analyze($_SERVER['DOCUMENT_ROOT'].$sFileUrl);	
		$height = $ThisFileInfo['video']['resolution_y'];
		$width = $ThisFileInfo['video']['resolution_x'];
		$sCustomMessage=$width.'x'.$height;
		
		echo $sCustomMessage;
	}
	else{
		echo 'not swf';
	}

}
else{
	echo 'no file';
}
?>