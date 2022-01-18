<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

$oSite = detectSite($_SERVER['PHP_SELF']);
 
if ($oSite) {	
	$idSite =  $oSite->get_id();
}
else{
	$idSite =  -1; 
}	

$currDir = str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']);
$baseDir = '/content/'.$oSite->get_rep().'/';
$fmDir = str_replace($baseDir, '', $currDir);
if ($fmDir == ""){ $fmDir= "/";} 

if ($fmDir == "/"){
	$aFM = dbGetObjectsFromFieldValue("cms_filemanager", array('get_url', 'get_cms_site'),  array($fmDir, $idSite), NULL);
	if (!$aFM){
		$aFM = dbGetObjectsFromFieldValue("cms_filemanager", array('get_url', 'get_cms_site'),  array('', $idSite), NULL);
	}
}
else{
	$aFM = dbGetObjectsFromFieldValue("cms_filemanager", array('get_url', 'get_cms_site'),  array($fmDir, $idSite), NULL);
}



if ((count($aFM) > 0)&&($aFM!=false)){
	$oFM = $aFM[0];
	$_localDir = $_SERVER['DOCUMENT_ROOT'].'/custom/filemanager/'.$oSite->get_rep().'/'.$oFM->get_url().'/files/'.$_GET['dir'];
	$_localDir = preg_replace('/\/+/', '/', $_localDir);// dédoublonne les /
	
	$_skinDir = '/custom/filemanager/'.$oSite->get_rep().'/'.$oFM->get_url().'/skin/';
	$_skinDir = preg_replace('/\/+/', '/', $_skinDir);	// dédoublonne les /
	
	if (strpos($_localDir, $_SERVER['DOCUMENT_ROOT'])===false){
		header('HTTP/1.0 403 Forbidden'); 
		die();
	}
	elseif(!is_dir($_localDir)){
		header('HTTP/1.0 404 Not Found'); 
		die();
	}
}
else{
	die($fmDir.'pas de filemanager');
}

include('secure.inc.php');


// tsst chroot de l'user loggé
if (($_SESSION['FO']['chroot']==1)&&(	($_SESSION['FO']['login']!='*')||($_SESSION['FO']['login']!='')	)){
	if (($_GET['dir']=='')||($_GET['dir']==NULL)){
		$_GET['dir']='/'.$_SESSION['FO']['login'];
		$_localDir.=$_SESSION['FO']['login'];
	}
}


//----------------------------------------------------------

if (!isset($_GET['dir']) || ($_GET['dir'] == '') || ($_GET['dir'] == '/') || !is_dir($_localDir)){
	unset($_GET['dir']);
}
else{	
	if (preg_match('/^[\.\/]+$/', $_GET['dir'])==1){
		$_GET['dir'] = '/';
		$_localDir = $_SERVER['DOCUMENT_ROOT'].'/custom/filemanager/'.$oSite->get_rep().'/'.$oFM->get_url().'/files/';
	}
}

?><!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title><?php echo $oFM->get_nom(); ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo $_skinDir; ?>filemanager.css">
  <script src="/backoffice/cms/js/fojsutils.js" type="text/javascript"></script>
<script type="text/javascript" src="/backoffice/cms/js/flashDetection.js.php"></script>
<script type="text/vbscript" src="/backoffice/cms/js/flashDetection.vbs.php"></script>
<script type="text/javascript" src="/backoffice/cms/js/flashDetection.php"></script>
<script src="/backoffice/cms/js/AC_RunActiveContent.js" type="text/javascript"></script> 

</head>

<body>
<div id="header"><div id="header_tt"></div></div>
<div id="content">

<?php
include('ls.inc.php');

?>

</div>
<div id="footer">

</div>
</body>
</html>
