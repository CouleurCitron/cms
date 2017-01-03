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
 * File Name: connector.php
 * 	This is the File Manager Connector for PHP.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

include('config-fck.php') ;
include('util.php') ;
include('io.php') ;
include('basexml.php') ;
include('commands.php') ;
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/utils/dir.lib.php');

if ( !$Config['Enabled'] )
	SendError( 1, 'This connector is disabled. Please check the "editor/filemanager/browser/default/connectors/php/config.php" file' ) ;

// Get the "UserFiles" path.
$GLOBALS["UserFilesPath"] = '' ;

if ( isset( $Config['UserFilesPath'] ) )
	$GLOBALS["UserFilesPath"] = $Config['UserFilesPath'] ;
else if ( isset( $_GET['ServerPath'] ) )
	$GLOBALS["UserFilesPath"] = $_GET['ServerPath'] ;
else
	$GLOBALS["UserFilesPath"] = '/UserFiles/' ;

if ( ! preg_match( '/\/$/msi', $GLOBALS["UserFilesPath"] ) )
	$GLOBALS["UserFilesPath"] .= '/' ;

if ( strlen( $Config['UserFilesAbsolutePath'] ) > 0 ) 
{
	$GLOBALS["UserFilesDirectory"] = $Config['UserFilesAbsolutePath'] ;

	if ( ! preg_match( '/\/$/msi', $GLOBALS["UserFilesDirectory"] ) )
		$GLOBALS["UserFilesDirectory"] .= '/' ;
}
else
{
	// Map the "UserFiles" path to a local directory.
	$GLOBALS["UserFilesDirectory"] = GetRootPath() . $GLOBALS["UserFilesPath"] ;
	
}

dirExists($GLOBALS["UserFilesPath"]) ;


DoResponse() ;

function DoResponse()
{
	if ( !isset( $_GET['Command'] ) || !isset( $_GET['Type'] ) || !isset( $_GET['CurrentFolder'] ) )
		return ;

	// Get the main request informaiton.
	$sCommand		= $_GET['Command'] ;
	$sResourceType	= $_GET['Type'] ;
	$sCurrentFolder	= $_GET['CurrentFolder'] ;
	
	$aTypes = array('Image','File','Flash','Media','PDF');
	
	$aClasse = dbGetObjectsFromFieldValue("classe", array("get_statut"),  array(DEF_ID_STATUT_LIGNE), NULL);

	if (count($aClasse) > 0){
		foreach($aClasse as $cKey => $oClasse){				
			if (($oClasse->get_cms_site() == -1) || ($oClasse->get_cms_site() == $_SESSION['idSite'])){					
				if (is_dir($_SERVER['DOCUMENT_ROOT'].'/custom/upload/'.$oClasse->get_nom())){
					$aTypes[] = $oClasse->get_nom();
				}			
			}		
		}
	}

	// Check if it is an allowed type.
	if ( !in_array( $sResourceType, $aTypes ) )
		return ;

	// Check the current folder syntax (must begin and start with a slash).
	if ( ! preg_match( '/\/$/msi', $sCurrentFolder ) ) $sCurrentFolder .= '/' ;
	if ( strpos( $sCurrentFolder, '/' ) !== 0 ) $sCurrentFolder = '/' . $sCurrentFolder ;
	
	// Check for invalid folder paths (..)
	if ( strpos( $sCurrentFolder, '..' ) )
		SendError( 102, "" ) ;

	// File Upload doesn't have to Return XML, so it must be intercepted before anything.
	if ( $sCommand == 'FileUpload' )
	{
		FileUpload( $sResourceType, $sCurrentFolder ) ;
		return ;
	}

	CreateXmlHeader( $sCommand, $sResourceType, $sCurrentFolder ) ;

	// Execute the required command.
	switch ( $sCommand )
	{
		case 'GetFolders' :
			GetFolders( $sResourceType, $sCurrentFolder ) ;
			break ;
		case 'GetFoldersAndFiles' :
			GetFoldersAndFiles( $sResourceType, $sCurrentFolder ) ;
			break ;
		case 'CreateFolder' :
			CreateFolder( $sResourceType, $sCurrentFolder ) ;
			break ;
	}

	CreateXmlFooter() ;

	exit ;
}
?>