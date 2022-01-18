<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

$oNews = new newsletter($_GET['idnew']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $oNews->get_libelle(); ?></title>
</head>
<body>
<?php
echo '<p><strong>'.$oNews->get_libelle().'</strong><p>';
$sBodyHTML = str_replace("XX-ID-INSCRIT-XX", $_GET['ins'], $oNews->get_html());
echo $sBodyHTML; 		
?>	
</body>
</html>	