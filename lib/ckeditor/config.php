<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
header("Content-type: text/javascript");

$conf = "";
$conf.= "CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	//config.toolbar_Full =
        ";

if(!defined('CKEDITOR_TOOLBAR_CONFIG')){
    $conf.= "config.toolbar =
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

            ];";
} else {
    $conf.= "config.toolbar =
            [
                    ". CKEDITOR_TOOLBAR_CONFIG ."

            ];";
}
$conf.= "config.height = 600;

	//Appel des plugins	+ configuration
	config.extraPlugins = 'aws_video,oembed,widget';
	config.allowedContent = true;
	config.oembed_WrapperClass = 'embededContent';	

	//CSS et styles
        ";

/* on va vérifie si le styles_ck.js n'est pas vide, si un all/styles_ck.js existe on le mettra à la place */
if( is_file( $_SERVER[ 'DOCUMENT_ROOT' ] . "/custom/js/".$_SESSION['rep_travail']."/styles_ck.js" ) ){
    $sFile = file_get_contents( $_SERVER[ 'DOCUMENT_ROOT' ] . "/custom/js/".$_SESSION['rep_travail']."/styles_ck.js" );
    //var_dump( $sFile );
    $contenuDefaultEditor = "/* JS CK Editor Styles Document */

CKEDITOR.stylesSet.add( 'styles_ck', [

			/* Block Styles */



			{ name: 'Paragraphe', element: 'p' } //,


						
		]);
";
    
    $file_config = "/custom/js/".$_SESSION['rep_travail']."/styles_ck.js";
    $file_spaw = "/custom/css/fo_".$_SESSION['rep_travail']."_spaw.css";
    if( $sFile === $contenuDefaultEditor ){
        //alors le fichier est "vide"
        if(is_file( $_SERVER[ 'DOCUMENT_ROOT' ] . "/custom/js/all/styles_ck.js" ) ){
            /* si le fichier style all existe, il surcharge le fichier de config du mini site */
            $file_config = "/custom/js/all/styles_ck.js";
            $file_spaw = "/custom/css/fo_all_spaw.css";
        }
    }
}
$conf.= "config.contentsCss = '$file_spaw';
        config.stylesSet = 'styles_ck:$file_config';";
	
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

        if(defined('CKEDITOR_MORE_COLOR')){
            if(!CKEDITOR_MORE_COLOR){
                $conf .= "config.colorButton_enableMore = false;
                        ";
            }
        }
        
        if(defined('CKEDITOR_COLOR_LIST')){
                $conf .= "config.colorButton_colors = '".CKEDITOR_COLOR_LIST."';
                        ";
        }
        

$conf.= "	config.filebrowserFlashUploadUrl = '';
	config.filebrowserAws_videoDialogUploadUrl = '';
 
	
	
	
};";



echo $conf;
?>