<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/class/cms_flvmeta.class.php'); // acces au cache de la meta donnée FLV

$ext=strtolower(strrchr(basename($file), '.'));
$filename = basename($file);

if (is_file($_SERVER['DOCUMENT_ROOT'].$file)){
	$file = $_SERVER['DOCUMENT_ROOT'].$file;
}

if((is_file($file)) && (($ext=='.flv')||($ext=='.mov')||($ext=='.mp4'))){

	$aStats = stat($file);

	$aMeta = dbGetObjectsFromFieldValue('cms_flvmeta', array('get_src', 'get_filemdate'),  array($filename, $aStats['mtime']), NULL);

	if ((count($aMeta) > 0)&&($aMeta!=false)){
		$oMeta = $aMeta[0];
		$width = $oMeta->get_w();
		$height = $oMeta->get_h();
	}
	else{
		include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/lib/getid3/getid3.php');
		$getID3 = new getID3;
		$ThisFileInfo = $getID3->analyze($file);	

		if (isset($ThisFileInfo["flv"]["meta"]["onMetaData"]["width"])){
			$width = $ThisFileInfo["flv"]["meta"]["onMetaData"]["width"];
		}
		else{
			$width = $ThisFileInfo["video"]["resolution_x"];
		}
		if (isset($ThisFileInfo["flv"]["meta"]["onMetaData"]["height"])){
			$height = $ThisFileInfo["flv"]["meta"]["onMetaData"]["height"];
		}
		else{
			$height = $ThisFileInfo["video"]["resolution_y"];
		}
		
		$oMeta = new cms_flvmeta();
		$oMeta->set_src($filename);
		$oMeta->set_filemdate($aStats['mtime']);
		$oMeta->set_w($width);
		$oMeta->set_h($height);
		dbSauve($oMeta);
	}
	
}
else{
       print('ERROR: The file '.$file.' does not exist');
}		
?>