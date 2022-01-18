<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 
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
 * 	Configuration file for the PHP File Uploader.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

global $Config ;

// SECURITY: You must explicitelly enable this "uploader". 
$Config['Enabled'] = true ;

// Path to uploaded files relative to the document root.
if(isset( $_GET['Type'] ) && ($_GET['Type'] == 'Image')){
	$Config['UserFilesPath'] = '/custom/img/'.$_SESSION['rep_travail'].'/' ; // CC custom
}
elseif(isset( $_GET['Type'] ) && ($_GET['Type'] == 'File')){
	$Config['UserFilesPath'] = '/content/'.$_SESSION['rep_travail'].'/' ; // CC custom
}
elseif(isset( $_GET['Type'] ) && ($_GET['Type'] == 'Flash')){
	$Config['UserFilesPath'] = '/custom/swf/'.$_SESSION['rep_travail'].'/' ; // CC custom
}
elseif(isset( $_GET['Type'] ) && ($_GET['Type'] == 'PDF')){
	$Config['UserFilesPath'] = '/custom/upload/cms_pdf/' ;  // CC custom
}
else{
	$Config['UserFilesPath'] = '/custom/img/'.$_SESSION['rep_travail'].'/' ; // CC custom
}


$Config['AllowedExtensions']['File']	= array('php', 'html', 'htm') ;
$Config['DeniedExtensions']['File']		= array('jpg','gif','jpeg', 'png', 'php3','php5','phtml','asp','aspx','ascx','jsp','cfm','cfc','pl','bat','exe','dll','reg','cgi') ;

$Config['AllowedExtensions']['Image']	= array('jpg','gif','jpeg','png') ;
$Config['DeniedExtensions']['Image']	= array() ;

$Config['AllowedExtensions']['Flash']	= array('swf') ;
$Config['DeniedExtensions']['Flash']	= array('fla') ;

$Config['AllowedExtensions']['Media']	= array() ;
$Config['DeniedExtensions']['Media']	= array() ;

$Config['AllowedExtensions']['PDF']	= array('pdf') ;
$Config['DeniedExtensions']['PDF']	= array() ;

?>