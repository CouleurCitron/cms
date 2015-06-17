<?php
 
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

?>

<link href="/backoffice/cms/css/bo.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.arbo2 a {
color:#575B60;
}
-->
</style>
<?php 
 
/*
	2005/05/11 Michael
	Modif pour Moz 

	$Id: dir.php,v 1.11 2013-04-04 13:36:07 thao Exp $
	$Author: thao $

	$Log: dir.php,v $
	Revision 1.11  2013-04-04 13:36:07  thao
	*** empty log message ***

	Revision 1.10  2012-07-31 14:24:51  pierre
	*** empty log message ***

	Revision 1.9  2012-04-13 07:18:38  pierre
	*** empty log message ***

	Revision 1.8  2012-03-20 09:58:40  thao
	modif autoclass option="link" : Affichage d'arborescence et rattachement de page en fonction de la langue

	Revision 1.7  2009-04-02 10:15:25  thao
	*** empty log message ***

	Revision 1.4  2009-04-02 09:31:02  thao
	*** empty log message ***

	Revision 1.3  2009-04-02 09:13:22  pierre
	*** empty log message ***

	Revision 1.1  2007-08-08 14:26:26  thao
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
 


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/utils/dir.lib.php');

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
        'lib' => 'pattern',
		'brique' => 'pattern'  
);



if (isset($_GET['idSite'])  && ($_GET['idSite'] != "")){
	$idSite = $_GET['idSite']; 
	$oSite = new Cms_site ($idSite) ; 
	$repIdSite = $oSite->get_rep();
	$sql = "select * from cms_site where cms_id != ".$idSite;
	$paramSite = "&idSite=".$idSite;
	$aSite = dbGetObjectsFromRequete('cms_site', $sql);
	foreach ($aSite as $oSite) { 
		$dirExcludeList['\/'.$oSite->get_rep().''] = 'pattern' ;
	}
	
} 
 
$fileExcludeList = array(
	'/\.fla$/' => 'pattern',
	'/\.php\..+/' => 'pattern',
	'/\.js$/' => 'pattern',
	'/404\.php$/' => 'pattern',
	'/htaccess/' => 'pattern',
	'/robots\./' => 'pattern',
	'/sitemap\./' => 'pattern',
	
);

$rootDir = $_SERVER['DOCUMENT_ROOT'];
if($_GET['src']=='session' && strlen($_SESSION['rootDir'])) {
	if(is_dir($_SESSION['rootDir']))
		$rootDir = $_SESSION['rootDir'];
}
$relDir = stripslashes(rawurldecode($_GET['dir']));
$currentDir = $rootDir.$relDir;
if(!is_dir($currentDir))
	die('Le répertoire '.$relDir.' n\'est pas valide.');

$files = getSSFiles($currentDir);
$dirs = getSSDirs($currentDir);

?>
<script language="javascript">
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

<?php
if ($_GET["source"]!="") {
	$url_source = substr($_GET["source"], 0, strrpos($_GET["source"], "/"));
 	echo "window.location.href='http://".$_SERVER['HTTP_HOST']."".$_SERVER['PHP_SELF']."?dir=".rawurlencode($url_source)."&form=".$_GET['form']."&champ=".$_GET['champ']."".$paramSite."';";
}
?>
</script>
<table cellpadding="0" cellspacing="2" border="0" width="600">
<tr>
  <td><div class="ariane">
<span class="arbo2"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?><?php echo $_SERVER['PHP_SELF']; ?>?form=<?php echo $_GET['form']; ?>&champ=<?php echo $_GET['champ']."".$paramSite.""; ?>">Racine</a>
<?php
	 
	$path = split('/',$relDir);
	$tmpPath = '';
	if(strlen($relDir)>0) {
		foreach($path as $k => $v) {
			if(strlen($v)>0) {
				$tmpPath.="/$v";
?>
&nbsp;/&nbsp;<a href="http://<?php echo $_SERVER['HTTP_HOST']; ?><?php echo $_SERVER['PHP_SELF']."?dir=$tmpPath"; ?>&form=<?php echo $_GET['form']; ?>&champ=<?php echo $_GET['champ']."".$paramSite.""; ?>"><?php echo $v; ?></a>
<?php
			}
		}
	}
	$i=0;
	$colors = array('pair','impair'); 
?></span></div>
</td>
</table>
<hr size="1" />
<table cellpadding="0" cellspacing="0" border="0" width="100%" id="arbo_liste_contenu" class="arbo">
<tr class="<?php echo $colors[$i%2]; $i++; ?>"><td colspan="2"><img src="/backoffice/cms/img/ico_dossier_opened.gif" border="0">&nbsp;&nbsp;<?php if($relDir) echo $relDir; else echo "/";?></td></tr>
<?php 
	
	foreach($dirs as $k => $dir) {  
		if ((ereg("custom", $relDir.'/'.$dir) || ereg("documents", $relDir.'/'.$dir)) && $dir!="GABARITS" || (ereg("content", $relDir.'/'.$dir )  ) ) { 
		 
?>
<tr class="<?php echo $colors[$i%2]; $i++; ?>">
   <td>&nbsp;&nbsp;</td>
   <td><span style="text-decoration: none">&nbsp;&nbsp;</span><a href="<?php echo $_SERVER['PHP_SELF']."?dir=".rawurlencode($relDir.'/'.$dir); ?>&form=<?php echo $_GET['form']; ?>&champ=<?php echo $_GET['champ']."".$paramSite.""; ?>"><img src="/backoffice/cms/img/ico_dossier.gif" border="0"><?php echo $dir; ?></a></td>
</tr>
<?php	
			 
		}
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
<tr class="<?php echo $colors[$i%2]; $i++; ?>">
   <?php if (!ereg("cvs", $file) && !ereg("node.php", $file)) {?> 
   <td>&nbsp;&nbsp;</td>
   <td><span style="text-decoration: none">&nbsp;&nbsp;</span><a href="#" onClick="doIt('<?php echo str_replace("'","\'",$relDir).'/'.$file; ?>');"><img src="/backoffice/cms/filemanager/images/<?php echo $img; ?>" border="0"><?php echo $file; ?></a></td>
   <?php } ?>
</tr>
<?php
	}
?>

</table>
