<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!--[if gt IE 8]><meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" /><![endif]-->
<!--[if IE 10]><meta http-equiv="X-UA-Compatible" content="requiresActiveX=true" /><![endif]-->
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<title>Minimal WYSIWYG Editor</title> 

<?php

if (isset($_GET['idForm']) && $_GET['idForm']!="") {
	$idForm = $_GET['idForm'];
	$idField = $_GET['idField'];
}
else {
	$idForm = $_POST['idForm'];
	$idField = $_POST['idField'];
}



if (!isset($translator)){
	$translator =& TslManager::getInstance(); 
}
if (!isset($_SESSION['BO']['cms_texte'])){
	$translator->loadAllTransToSession();
}

if (defined("DEF_FCK_VERSION") && DEF_FCK_VERSION == "ckeditor" ) { 
	echo '<script src="/backoffice/cms/lib/ckeditor/ckeditor.js"></script>';
} 
else {
	include("backoffice/cms/lib/FCKeditor/fckeditor.php") ;
}
//
// fonction valider validerWysiwyg
//
echo "<script type=\"text/javascript\">\n";
echo "function validerWysiwyg() { \n";
echo "document.createHTML.submit();\n";   
echo "var chaine = document.createHTML.FCKeditor1.value; \n";
echo "chaine = chaine.replace(\"\\n\",\"\");\n";
echo "chaine = chaine.replace(\"\\r\",\"\");\n";  
echo "window.opener.document.".$idForm.".".$idField.".value = chaine;\n"; 
echo "self.close();";
echo "}\n";
echo "</script>\n"; 
?>
</head>
<body >
<form id="createHTML" name="createHTML" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<?php 

if (defined("DEF_FCK_VERSION") && DEF_FCK_VERSION == "ckeditor" ) { 
	
	$sHtml = $_POST['idSource'];
	
	?>
	 
		<textarea cols="50" id="FCKeditor1" name="FCKeditor1" rows="30" width="500" height="500"><?php echo htmlFCKcompliant($sHtml); ?></textarea>
		<script>
		
                        var roxyFileman = '/backoffice/cms/lib/ckeditor/fileman/index.html';
                
			function init_ck () {  
				CKEDITOR.replace( 'FCKeditor1', {

                                    customConfig : '/backoffice/cms/lib/ckeditor/config.php',
                                    <?php if( defined( 'DEF_FILEMANAGER' ) && DEF_FILEMANAGER == 'fileman' ){ ?>
                                    filebrowserBrowseUrl:roxyFileman,
                                    filebrowserImageBrowseUrl:roxyFileman+'?type=image',
                                    removeDialogTabs: 'link:upload;image:upload'
                                    <?php } ?>
                                });
			}
			
			if (navigator.userAgent.toLowerCase().indexOf("chrome") != -1) {
				
				setTimeout( function(){ init_ck() }, 1000); 
			}
			else {
				init_ck();
			}
			

		</script> 
<?php

}
else {


	$oFCKeditor = new FCKeditor('FCKeditor1') ;
	 
	if (defined("DEF_FCK_TOOLBARSET")) $oFCKeditor->ToolbarSet = DEF_FCK_TOOLBARSET;
	else  $oFCKeditor->ToolbarSet = "Basic";
	$oFCKeditor->BasePath = '/backoffice/cms/lib/FCKeditor/' ;	// '/FCKeditor/' is the default value.
	 
	$sHtml = $_POST['idSource'];
	$oFCKeditor->Value = htmlFCKcompliant($sHtml);
	$oFCKeditor->Width  = '95%';
	if(is_post('height') && $_POST['height'] > 0){
		$oFCKeditor->Height = $_POST['height'] - 50;
	}
	else{
		$oFCKeditor->Height = 550;
	}
	
	
	$oFCKeditor->Create();
}

$_POST['idSource'] = str_replace("\"", "&quot;", $_POST['idSource']); 
?>
<input type="hidden" id="height" name="height" value="<?php echo $_GET['height']; ?>"/>
<input type="hidden" id="idSource" name="idSource" value="<?php echo $_POST['idSource']; ?>"/>
<input type="hidden" id="idForm" name="idForm" value="<?php echo $idForm; ?>" />
<input type="hidden" id="idField" name="idField" value="<?php echo $idField; ?>" />
<input name="Valider" id="Valider" type="button" class="arbo" onClick="javascript:validerWysiwyg();" value="<?php $translator->echoTransByCode('btValider'); ?>" />
</form>
<?php 
if ( isset($_GET['idForm']) && $_GET['idForm'] != "") { 
	echo "<script type=\"text/javascript\">\n";
	echo "document.createHTML.idSource.value = window.opener.document.".$_GET['idForm'].".".$_GET['idField'].".value;";
	echo "document.createHTML.submit();";
	echo "</script>";
} 
?>
</body>
</html>