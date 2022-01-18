<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
header("Content-type: text/javascript");

$mainConfig = 'fckconfig.js';
$customConfig = $_SERVER['DOCUMENT_ROOT'].'/custom/js/'.$_SESSION["rep_travail"].'/fckconfig.js';

// config out of the box CMS
if (is_file($mainConfig)){
	$conf = file_get_contents($mainConfig);
}

// config surcharge metier
if (is_file($customConfig)){
	$conf = file_get_contents($customConfig);
}

if (isset($_SESSION['BO']['site_langue'])){
	$conf = preg_replace('/FCKConfig\.DefaultLanguage[ \t]*= \'fr\' ;/si', 'FCKConfig.DefaultLanguage		= \''.$_SESSION['BO']['site_langue'].'\' ;', $conf);
}
elseif (isset($_SESSION['site_langue'])){
	$conf = preg_replace('/FCKConfig\.DefaultLanguage[ \t]*= \'fr\' ;/si', 'FCKConfig.DefaultLanguage		= \''.$_SESSION['site_langue'].'\' ;', $conf);
}
else{
	// pas de replace  =F R
}
echo $conf;
?>