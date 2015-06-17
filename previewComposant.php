<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15" />
<script src="/backoffice/cms/js/fojsutils.js" type="text/javascript"></script>
<script src="<?php echo $URL_ROOT;?>/backoffice/cms/js/AC_RunActiveContent.js" type="text/javascript"></script>
<link href="<?php echo  "/custom/css/fo_".strtolower($_SESSION['site']).".css" ; ?>" rel="stylesheet" type="text/css"></link> 
</head>
<body style="border:0px; margin:0px; padding:0px;">
<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');

$buffer=1024;   // buffer de lecture de 1Ko
$tmpfile=$_SERVER['DOCUMENT_ROOT'].'/tmp/'.md5(uniqid(rand())).'.tmp.php';

$element='';

$id = $_GET['id'];
$version = $_GET['version'];
	
if ($version != "") {
	// contenu de CMS_ARCHI_CONTENT
	$composant = getComposantArchiById($id, $version);
} 
else {
	// contenu de CMS_CONTENT
	$composant = getComposantById($id);
}
	
if ($composant!=false){
	$element = stripslashes($composant['html']);
}
else{
	$element = 'code html source introuvable. id = '.$id;
}
	
$tmpfilehandle = fopen($tmpfile,'w');
	
if ($tmpfilehandle){	
	fputs($tmpfilehandle,$element);	
	fclose($tmpfilehandle);
	include_once($tmpfile) ;
	unlink($tmpfile);
	echo '<!-- '.$element.' -->';
}
else{
	error_log("Ecriture impossible dans ".$tmpfile);
	print("Ecriture impossible dans ".$tmpfile);
	echo $element;
}
?>
</body>
</html>