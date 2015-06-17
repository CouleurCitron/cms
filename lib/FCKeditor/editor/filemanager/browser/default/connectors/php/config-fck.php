<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 
include_once('autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/utils/dir.lib.php');

$idSite = $_SESSION["idSite"];
$site = new Cms_site($idSite);
/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2005 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * "Support Open Source software. What about a donation today?"
 * 
 * File Name: config.php
 * 	Configuration file for the File Manager Connector for PHP.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

global $Config ;


// SECURITY: You must explicitelly enable this "connector". (Set it to "true") .
$Config['Enabled'] = true ;

if ($_GET['Type'] == "Image"){
	// Path to user files relative to the document root.
	$Config['UserFilesPath'] = '/custom/img/'.$site->get_rep().'/' ;
	
	// Fill the following value it you prefer to specify the absolute path for the
	// user files directory. Usefull if you are using a virtual directory, symbolic
	// link or alias. Examples: 'C:\\MySite\\UserFiles\\' or '/root/mysite/UserFiles/'.
	// Attention: The above 'UserFilesPath' must point to the same directory.
	$Config['UserFilesAbsolutePath'] = $_SERVER['DOCUMENT_ROOT'].'/custom/img/'.$site->get_rep().'/' ;
}
elseif($_GET['Type'] == "File"){
	// Path to user files relative to the document root.
	$Config['UserFilesPath'] = '/content/'.$site->get_rep().'/' ;
	
	// Fill the following value it you prefer to specify the absolute path for the
	// user files directory. Usefull if you are using a virtual directory, symbolic
	// link or alias. Examples: 'C:\\MySite\\UserFiles\\' or '/root/mysite/UserFiles/'.
	// Attention: The above 'UserFilesPath' must point to the same directory.
	$Config['UserFilesAbsolutePath'] = $_SERVER['DOCUMENT_ROOT'].'/content/'.$site->get_rep().'/' ;
}
elseif($_GET['Type'] == "Flash"){
	// Path to user files relative to the document root.
	$Config['UserFilesPath'] = '/custom/swf/'.$site->get_rep().'/' ;
	
	// Fill the following value it you prefer to specify the absolute path for the
	// user files directory. Usefull if you are using a virtual directory, symbolic
	// link or alias. Examples: 'C:\\MySite\\UserFiles\\' or '/root/mysite/UserFiles/'.
	// Attention: The above 'UserFilesPath' must point to the same directory.
	$Config['UserFilesAbsolutePath'] = $_SERVER['DOCUMENT_ROOT'].'/custom/swf/'.$site->get_rep().'/' ;
}
elseif($_GET['Type'] == "PDF"){
	// Path to user files relative to the document root.
	//$Config['UserFilesPath'] = '/backoffice/cms/utils/telecharger.php?file=custom/upload/cms_pdf/' ; 
	$Config['UserFilesPath'] = '/custom/upload/cms_pdf/' ; 
	
	// Fill the following value it you prefer to specify the absolute path for the
	// user files directory. Usefull if you are using a virtual directory, symbolic
	// link or alias. Examples: 'C:\\MySite\\UserFiles\\' or '/root/mysite/UserFiles/'.
	// Attention: The above 'UserFilesPath' must point to the same directory.
	$Config['UserFilesAbsolutePath'] = $_SERVER['DOCUMENT_ROOT'].'/custom/upload/cms_pdf/' ;
}
elseif($_GET['Type'] == "Media"){
	// Path to user files relative to the document root.
	$Config['UserFilesPath'] = '/medias/'.$site->get_rep() ;
	
	// Fill the following value it you prefer to specify the absolute path for the
	// user files directory. Usefull if you are using a virtual directory, symbolic
	// link or alias. Examples: 'C:\\MySite\\UserFiles\\' or '/root/mysite/UserFiles/'.
	// Attention: The above 'UserFilesPath' must point to the same directory.
	$Config['UserFilesAbsolutePath'] = $_SERVER['DOCUMENT_ROOT'].'/medias/'.$site->get_rep() ;
}
else{
	//$_SERVER['DOCUMENT_ROOT'].'/custom/upload/'.$oClasse->get_nom())
	$Config['UserFilesPath'] = '/custom/upload/'.$_GET['Type'] ;
	$Config['UserFilesAbsolutePath'] = $_SERVER['DOCUMENT_ROOT'].'/custom/upload/'.$_GET['Type'] ;
}

dirExists($Config['UserFilesAbsolutePath']) ;

$Config['AllowedExtensions']['Image']	= array('jpg','gif','jpeg','png') ;
$Config['DeniedExtensions']['Image']	= array() ;

$Config['AllowedExtensions']['File']	= array('php', 'html', 'htm') ;
$Config['DeniedExtensions']['File']		= array('jpg','gif','jpeg','png', 'php3','php5','phtml','asp','aspx','ascx','jsp','cfm','cfc','pl','bat','exe','dll','reg','cgi') ;

$Config['AllowedExtensions']['Flash']	= array('swf') ;
$Config['DeniedExtensions']['Flash']	= array('fla') ;

$Config['AllowedExtensions']['Media']	= array('jpg','gif','jpeg','png','avi','mpg','mpeg', 'doc', 'docx', 'ppt', 'pps', 'pptx') ;
$Config['DeniedExtensions']['Media']	= array() ;

$Config['AllowedExtensions']['PDF']	= array('pdf') ;
$Config['DeniedExtensions']['PDF']	= array() ;


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php'); // classe de customisation des classes

$aClasse = dbGetObjectsFromFieldValue("classe", array("get_statut"),  array(DEF_ID_STATUT_LIGNE), NULL);

if (count($aClasse) > 0){
	foreach($aClasse as $cKey => $oClasse){				
		if (($oClasse->get_cms_site() == -1) || ($oClasse->get_cms_site() == $_SESSION['idSite'])){					
			if (is_dir($_SERVER['DOCUMENT_ROOT'].'/custom/upload/'.$oClasse->get_nom())){
				$Config['AllowedExtensions'][$oClasse->get_nom()]	= array() ;
				$Config['DeniedExtensions'][$oClasse->get_nom()]	= array('php','php3','php5','phtml','asp','aspx','ascx','jsp','cfm','cfc','pl','bat','exe','dll','reg','cgi') ;
			}			
		}		
	}
}

?>