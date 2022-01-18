<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
header("Content-type: text/css; charset=utf-8");

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

$tempFile = $_SERVER['DOCUMENT_ROOT'].'/custom/css/fo_'.$_SESSION['rep_travail'].'_spaw.css';
$altFile = $_SERVER['DOCUMENT_ROOT'].'/custom/css/fo_'.$_SESSION['rep_travail'].'.css';

//fck_internal.css (patch pour gecko)
$internalFile = $_SERVER['DOCUMENT_ROOT'].'/backoffice/cms/lib/FCKeditor/editor/css/fck_internal.css';

if (is_session('idThe')){
	$aThemes = dbGetObjectsFromFieldValue("cms_theme", array("get_id"),  array($_SESSION['idThe']), NULL);
}
else{
	$aThemes=false;
}

if (($aThemes!=false) && (newSizeOf($aThemes) == 1)){
	$oThemes = $aThemes[0];
	$sEditorStyles = $oThemes->get_editor();
}
elseif (is_file($tempFile)){
	$sEditorStyles = file_get_contents($tempFile);
}
elseif (is_file($altFile)){
	$sEditorStyles = file_get_contents($altFile);
}
else{
	$sEditorStyles = '';
}

$sEditorStyles .= "\n".str_replace('images/fck_', '/backoffice/cms/lib/FCKeditor/editor/css/images/fck_', file_get_contents($internalFile));

header('Content-Length: '.strlen($sEditorStyles)); 
header('Pragma: no-cache'); 
header('Cache-Control: must-revalidate, post-check=0, pre-check=0, public'); 
header('Expires: 0'); 
echo $sEditorStyles;	
?>