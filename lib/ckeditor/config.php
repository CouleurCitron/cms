<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
header("Content-type: text/javascript");

$conf = "";
$conf.= "CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	//config.toolbar_Full =
	config.toolbar =
	[
		['Source'],
		['Cut','Copy','Paste','PasteText','PasteWord','-','Print'],
		['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
		'/',
		['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
		['NumberedList','BulletedList'],
		['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		['Link','Unlink','Anchor'],
		['Image','Flash','Table','HorizontalRule','SpecialChar'],
			'/',
		['Styles','Format','FontSize','TextColor'] ,
		['aws_video','oembed']
		
	];
 
	config.height = 600;

	//Appel des plugins	+ configuration
	config.extraPlugins = 'aws_video,oembed,widget';
	config.allowedContent = true;
	config.oembed_WrapperClass = 'embededContent';	

	//CSS et styles
	config.contentsCss = '/custom/css/fo_".$_SESSION['rep_travail']."_spaw.css';
	config.stylesSet = 'styles_ck:/custom/js/".$_SESSION['rep_travail']."/styles_ck.js';";
	
	//Config balise auto p ou br  
	if(defined("DEF_FCK_ENTER_P")){
		if(DEF_FCK_ENTER_P==0) {
			$baliseauto = 'CKEDITOR.ENTER_BR';
		} else{
			$baliseauto = 'CKEDITOR.ENTER_P';
		}
		
	} else {
		$baliseauto = 'CKEDITOR.ENTER_P';
	}

	$conf.= "

	//Config balise auto p ou br 
	config.enterMode = ".$baliseauto.";

	//Filemanager-master	
	config.filebrowserBrowseUrl = '/backoffice/cms/lib/ckeditor/Filemanager-master/index.php?dir=/content/".$_SESSION['rep_travail']."/&langCode=".$_SESSION["site_langue"]."';
	
";

	if( preg_match('/newsletter/msi', $_SERVER['HTTP_REFERER'])){
		$conf.= "config.filebrowserImageBrowseUrl = '/backoffice/cms/lib/ckeditor/Filemanager-master/index.php?dir=/custom/newsletter/".$_SESSION['rep_travail']."/&source=init&langCode=".$_SESSION["site_langue"]."';
		";
	}
	else{
		$conf.= "config.filebrowserImageBrowseUrl = '/backoffice/cms/lib/ckeditor/Filemanager-master/index.php?dir=/custom/img/".$_SESSION['rep_travail']."/&source=init&langCode=".$_SESSION["site_langue"]."';
		";
	}
	
$conf.= "	config.filebrowserFlashBrowseUrl = '/backoffice/cms/lib/ckeditor/Filemanager-master/index.php?dir=/custom/swf/".$_SESSION['rep_travail']."/&source=init&langCode=".$_SESSION["site_langue"]."';
	config.filebrowserAws_videoDialogBrowseUrl = '/backoffice/cms/lib/ckeditor/Filemanager-master/index.php?dir=/custom/video/".$_SESSION['rep_travail']."/&source=initvideo&langCode=".$_SESSION["site_langue"]."';
	
	
	config.filebrowserUploadUrl = '/backoffice/cms/lib/ckeditor/Filemanager-master/index.html?dir=/content/".$_SESSION['rep_travail']."/';
";
	if( preg_match('/newsletter/msi', $_SERVER['HTTP_REFERER'])){
		$conf.= "config.filebrowserImageUploadUrl = '';
		";
	}
	else{
		$conf.= "config.filebrowserImageUploadUrl = '';
		";
	}

$conf.= "	config.filebrowserFlashUploadUrl = '';
	config.filebrowserAws_videoDialogUploadUrl = '';
 
	
	
	
};";



echo $conf;
?>