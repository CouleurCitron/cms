<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/*-----------------------------*/
// controls droit NIX


$rel_dir = $_GET['rel_dir'];
$rel_dir = preg_replace('/^\/(.+)$/', '$1', $rel_dir);
//$currentFullPath = $_SERVER['DOCUMENT_ROOT'].$FM_workdir."/".$rel_dir;
$currentFullPath = $base_dir.$rel_dir;

//echo passthru("chown -R ".$FM_ftpUser.":www-data ".$currentFullPath);
//echo passthru("chmod -R 775 ".$currentFullPath);
//echo passthru("ls -al ".$currentFullPath);

// spéc retour upload ftp rel_dir
if(is_get('uploaded')){
	if (rename($base_dir.'/'.basename($_GET['uploaded']), $base_dir.$_GET['uploaded'])){
		echo '<span class="arbo2">upload réussi</span>';
	}
}


/*-----------------------------*/

if ($_GET['dir_id'] == 0) $sTitre = "Documents";
else if ($_GET['dir_id'] == 1) $sTitre = "PDF";
else if ($_GET['dir_id'] == 2) $sTitre = "Médias";
else if ($_GET['dir_id'] == 3) $sTitre = "Images";
else if ($_GET['dir_id'] == 4) $sTitre = "Images téléchargées";
else $sTitre = "";

if(isset($_GET['section_id']) && strlen($_GET['section_id'])>0) {
	$oSection = getObjectById("cms_sectionbo", $_GET['section_id']);
	$sSection = $oSection->get_libelle();
	if (substr($sSection, strlen($sSection)-1, strlen($sSection)) == "u")
		$sSection.="x";
	else 
		$sSection.="s";
	$sTitre=$sSection." téléchargé(e)s";
}

?><span class="arbo2">FICHIERS >&nbsp;</span><span class="arbo3"><?php echo $sTitre; ?></span><br><br><?php

$action = $_POST['action'];

if (! strlen($action) > 0)
	$action = $_GET['action'];
//print "<b>action = $action</b>";

if ( isset ( $action ) )
{
if( $action == "logout" ){ include("admin/logout.php"); return; }
}

//if(!isset($_SESSION['admin']))$action="browse";
//else if ($_SESSION['adminname'] <> $user) $action="login";
// else
if ( !isset ( $action )) $action= "browse" ;
if( $action == "login" ){ include("admin/login.php"); return; }
//print "<b>action = $action</b>";

if( $action == "main" ){ include("admin/head.php");include("admin/main.php");include("admin/tail.php"); return; }
if( $action == "browse" ){ include("admin/head.php");include("admin/browse.php");include("admin/tail.php"); return; }
if( $action == "edit" ){ include("admin/edit.php"); return; }
if( $action == "upload" ){ include("admin/upload.php"); return; }
if( $action == "zupload" ){ include("admin/zupload.php"); return; }
if( $action == "view" ){ include("admin/view.php"); return; }
if( $action == "vote" ){ include("admin/head.php");include("admin/vote.php");include("admin/tail.php"); return; }
if( $action == "licence" ){ include("admin/head.php");include("gpl.htm");include("admin/tail.php"); return; }
if( $action == "help" ){ include("admin/head.php");include("readme.htm");include("admin/tail.php"); return; }

?>
