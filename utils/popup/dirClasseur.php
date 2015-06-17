<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	2005/05/11 Michael
	Modif pour Moz 

	$Id: dirClasseur.php,v 1.7 2013-03-01 10:28:21 pierre Exp $
	$Author: pierre $

	$Log: dirClasseur.php,v $
	Revision 1.7  2013-03-01 10:28:21  pierre
	*** empty log message ***

	Revision 1.6  2009-09-24 08:53:14  pierre
	*** empty log message ***

	Revision 1.5  2009-04-02 09:32:09  pierre
	*** empty log message ***

	Revision 1.4  2008-11-28 14:09:56  pierre
	*** empty log message ***

	Revision 1.3  2008-11-06 12:03:54  pierre
	*** empty log message ***

	Revision 1.2  2007-09-24 14:13:41  thao
	*** empty log message ***

	Revision 1.1  2007/08/08 14:26:26  thao
	*** empty log message ***
	
	Revision 1.1  2007/08/08 14:18:47  thao
	*** empty log message ***
	
	Revision 1.4  2007/08/08 14:16:23  thao
	*** empty log message ***
	
	Revision 1.3  2007/03/12 17:25:34  pierre
	*** empty log message ***
	
	Revision 1.2  2007/03/12 15:08:02  pierre
	*** empty log message ***
	
	Revision 1.1  2006/12/15 12:30:22  pierre
	*** empty log message ***
	
	Revision 1.2  2006/09/12 15:42:05  pierre
	*** empty log message ***
	
	Revision 1.1.1.1  2006/01/25 15:14:31  pierre
	projet CCitron AWS 2006 Nouveau Website
	
	Revision 1.2  2005/11/02 13:29:03  sylvie
	*** empty log message ***
	
	Revision 1.1.1.1  2005/10/20 13:10:53  pierre
	Espace V2
	
	Revision 1.3  2005/06/02 13:51:00  michael
	Rajout de stripslashes et addslashes pour les dossiers avec apostrophe
	
	Revision 1.2  2005/05/13 09:58:38  michael
	Ajout de spaw 1.1rc1 / compatibilité Moz
	
	Revision 1.1.1.1  2005/04/18 13:53:29  pierre
	again
	
	Revision 1.1.1.1  2005/04/18 09:04:21  pierre
	oremip new
	
	Revision 1.1.1.1  2004/11/03 13:49:54  ddinside
	lancement du projet - import de adequat
	
	Revision 1.1  2004/06/16 15:25:19  ddinside
	fin synchro
	
	Revision 1.1  2004/05/14 14:25:12  ddinside
	ajout fonctions url
	
*/

include_once('include/utils/dir.lib.php');

?>
<link href="/css/bo.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
body {
	font-family: Verdana, Arial, Helvetica, sans-serif;
}
-->

.texteFile {
	font-size:11px;
	text-decoration:none;
}

.texteFile a{
	text-decoration:none; 
	font-size:11px;
}

.texteFile a:hover{
	text-decoration:underline; 
	font-size:11px;
}

</style>
<br />

<?php

if (isset($_GET['form']) && isset($_GET['champ']) && ($_GET['form'] != "") && ($_GET['champ'] != "")){
	$_GET['idForm'] = $_GET['form'];
	$_GET['idField'] = $_GET['champ'];
}
if (isset($_GET['idForm']) && isset($_GET['idField']) && ($_GET['idForm'] != "") && ($_GET['idField'] != "")){
	$_GET['form'] = $_GET['idForm'];
	$_GET['champ'] = $_GET['idField'];
}

$DEBUG = true;

$dirExcludeList = array(
	'CVS' => 'exact',
	  '.' => 'exact',
	 '..' => 'exact',
 'backoffice' => 'pattern',
 'automates' => 'pattern',
 'css' => 'pattern',
 'js' => 'pattern',
 'modules' => 'pattern',
    'include' => 'pattern',
        'lib' => 'pattern'
);
$fileExcludeList = array(
	'/\.fla$/' => 'pattern',
	'/\.php\..+/' => 'pattern',
	'/\.js$/' => 'pattern',
	
);

/*$rootDir = $_SERVER['DOCUMENT_ROOT'];
echo $rootDir;
if($_GET['src']=='session' && strlen($_SESSION['rootDir'])) {
	if(is_dir($_SESSION['rootDir']))
		$rootDir = $_SESSION['rootDir'];
}*/
$rootDir = $_SERVER['DOCUMENT_ROOT']."/pdf/classeur";

$relDir = stripslashes(rawurldecode($_GET['dir']));
$currentDir = $rootDir.$relDir;
if(!is_dir($currentDir))
	die('Le répertoire '.$relDir.' n\'est pas valide.');

$files = getSSFiles($currentDir);
$dirs = getSSDirs($currentDir);

?>
<script type="text/javascript"><!--
function doIt(str) { // *** CC Mkl : Rajout de "document." pour Moz
<?php
	if (isset($_GET['form']) && isset($_GET['champ']) && ($_GET['form'] != "") && ($_GET['champ'] != "")){
	?>
	window.opener.document.<?php echo $_GET['form']; ?>.<?php echo $_GET['champ']; ?>.value=escape(str);
	<?php
	}
	else{ // if (isset($_GET['ifForm']) && isset($_GET['idField']){
	?>
	window.opener.document.<?php echo $_GET['ifForm']; ?>.<?php echo $_GET['idField']; ?>.value=escape(str);
	<?php
	}
	?>
	self.close();
}
--></script>
<body>
<table cellpadding="0" cellspacing="2" border="0">
<tr>
  <td>&nbsp;&nbsp;</td>
  <td><span class="texteFile"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?form=<?php echo $_GET['form']; ?>&champ=<?php echo $_GET['champ']; ?>">Racine</a></span></td>
<?php
	$path = split('/',$relDir);
	$tmpPath = '';
	if(strlen($relDir)>0) {
		foreach($path as $k => $v) {
			if(strlen($v)>0) {
				$tmpPath.="/$v";
?>
  <td>&nbsp;&gt;&nbsp;</td>
  <td><a href="<?php echo $_SERVER['PHP_SELF']."?dir=$tmpPath"; ?>&form=<?php echo $_GET['form']; ?>&champ=<?php echo $_GET['champ']; ?>"><?php echo $v; ?></a></td>
<?php
			}
		}
	}
	$i=0;
	$colors = array('#cfdfef','#cfcfcf'); 
?>
</tr>
</table>
<hr size="1" />
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr bgcolor="<?php echo $colors[$i%2]; $i++; ?>"><td colspan="2"><img src="/backoffice/cms/img/ico_dossier_opened.gif" border="0">&nbsp;&nbsp;<?php if($relDir) echo $relDir; else echo "/";?></td></tr>
<?php
	foreach($dirs as $k => $dir) {
?>
<tr bgcolor="<?php echo $colors[$i%2]; $i++; ?>">
   <td>&nbsp;&nbsp;</td>
   <td><span class="texteFile"><a href="<?php echo $_SERVER['PHP_SELF']."?dir=".rawurlencode($relDir.'/'.$dir); ?>&form=<?php echo $_GET['form']; ?>&champ=<?php echo $_GET['champ']; ?>"><img src="/backoffice/cms/img/ico_dossier.gif" border="0">&nbsp;&nbsp;<?php echo $dir; ?></a></span></td>
</tr>
<?php
	}
?>
<?php
	$exts = getKnownExt();
	foreach($files as $k => $file) {
		$img = '';
		$ext = substr($file,-3,3);
		if(in_array($ext,$exts))
			$img = "$ext.gif";
		else 
			$img = "ukn.gif";
?>
<tr bgcolor="<?php echo $colors[$i%2]; $i++; ?>">
   <td>&nbsp;&nbsp;</td>
   <?php 
   
	$nameFile = str_replace("'","\'",$relDir).'/'.$file; 
   	$nameFile = substr($nameFile, 1, strlen($nameFile));
   ?>
   <td><span class="texteFile"><a href="#" onClick="doIt('<?php echo $nameFile; ?>');" ><img src="/backoffice/cms/filemanager/images/<?php echo $img; ?>" border="0">&nbsp;&nbsp;<?php echo $file; ?></a></span></td>
</tr>
<?php
	}
?>

</table>
</body>