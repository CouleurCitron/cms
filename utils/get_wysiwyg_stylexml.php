<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
header("Content-type: text/xml; charset=utf-8");

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

$tempFile = $_SERVER['DOCUMENT_ROOT'].'/custom/css/fo_'.$_SESSION['rep_travail'].'_spaw.css';
$altFile = $_SERVER['DOCUMENT_ROOT'].'/custom/css/fo_'.$_SESSION['rep_travail'].'.css';

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


// on vide les { }
$sEditorStyles = preg_replace("/\{[^\}]*\}/msi", "{}", $sEditorStyles);

preg_match_all("/\.([^ ^\{^\)^;^\n^\r^\f]{4,})[ \{]*/", $sEditorStyles, $aStyles);
$aStyles = array_unique($aStyles[1]);
$xmlStyles = "<Styles>\n";
foreach ($aStyles as $idStyle => $StyleName){
	$xmlStyles .= "<Style name=\"".$StyleName."\" element=\"span\">\n";
	$xmlStyles .= "<Attribute name=\"class\" value=\"".$StyleName."\" />\n";
	$xmlStyles .= "</Style>\n";	
}
$xmlStyles .= "</Styles>\n";
	
	
header('Content-Length: '.strlen($xmlStyles)); 
header('Pragma: no-cache'); 
header('Cache-Control: must-revalidate, post-check=0, pre-check=0, public'); 
header('Expires: 0'); 
echo $xmlStyles;	
?>