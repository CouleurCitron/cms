<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
sponthus 07/06/2005

Gestion des propriétés du site

*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/swish.php');	
$swishObj = new swish($index);  

//include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


activateMenu('gestiondusite');  //permet de dérouler le menu contextuellement

// site sur lequel on est positionné
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

// pas de site
if ($idSite == "") die ("Appel incorrect de la fonctionnalité");

$oSite = new Cms_site($idSite);

// controles preliminaires
dirExists($_SERVER['DOCUMENT_ROOT']."/custom/search/".strtoupper($oSite->get_rep())."/");
dirExists($_SERVER['DOCUMENT_ROOT']."/custom/search/template/");

if (!is_file($_SERVER['DOCUMENT_ROOT']."/custom/search/template/swish.conf")){
	$templateConf = "# Fichier de configuration de Swish-E pour le site TEMPLATE
IndexFile donnees.index
IndexDir http://".$_SERVER['HTTP_HOST']."/content/TEMPLATE/
IndexOnly .php .pdf .doc .xls .htm .html
MaxDepth 5
ParserWarnLevel 0
FollowSymLinks no
IgnoreMetaTags DESCRIPTION KEYWORDS
#TranslateCharacters :ascii7:";

	// ecriture de la conf template
	$file = $_SERVER['DOCUMENT_ROOT']."/custom/search/template/swish.conf";

   if (!$handle = fopen($file, 'w')) {
		 echo "Impossible d'ouvrir ".$file;
		 exit;
   }
   // Write $somecontent to our opened file.
   if (fwrite($handle, $templateConf) === FALSE) {
	   echo "Impossible d'écrire dans ".$file;
	   exit;
   }
   //$statutRetour = "Ecriture avec succès dans le fichier de conf.";
   fclose($handle);
}

//---------------------------------------------

$sFileName = $_SERVER['DOCUMENT_ROOT']."/custom/search/".strtoupper($oSite->get_rep())."/swish.conf";

// si premier access
if (!is_file($sFileName) || (filesize($sFileName) == 0)){
	// si pas dossier
	if (!is_dir($_SERVER['DOCUMENT_ROOT']."/custom/search/".strtoupper($oSite->get_rep()))){
		mkdir ($_SERVER['DOCUMENT_ROOT']."/custom/search/".strtoupper($oSite->get_rep()));
		//echo "wrote : ".$_SERVER['DOCUMENT_ROOT']."/custom/search/".strtoupper($oSite->get_rep());
	}
	// lecture du template
	$handle = fopen($_SERVER['DOCUMENT_ROOT']."/custom/search/template/swish.conf", "r");
	if($handle){
		$template = '';  
		while(!feof($handle)){ 
			$template .= fread($handle,4000);
		}
		fclose($handle); 
	}
	// edition du template

	$conf = str_replace("TEMPLATE", strtolower($oSite->get_rep()), $template);

	// ecriture de la conf
	$file = $_SERVER['DOCUMENT_ROOT']."/custom/search/".strtoupper($oSite->get_rep())."/swish.conf";

   if (!$handle = fopen($file, 'w')) {
		 echo "Impossible d'ouvrir $file";
		 exit;
   }
   // Write $somecontent to our opened file.
   if (fwrite($handle, $conf) === FALSE) {
	   echo "Impossible d'écrire dans $file";
	   exit;
   }
   //$statutRetour = "Ecriture avec succès dans le fichier de conf.";
   fclose($handle);
}

if (!isset($_POST['fDir']) || ($_POST['fDir'] == "") || !isset($_POST['fType']) || ($_POST['fType'] == "") || ($_POST['todo'] != "save")){
	// mode lecture
	$handle = fopen($sFileName, "r");
	if($handle){
		$conf = '';  
		while(!feof($handle)){ 
			$conf .= fread($handle,4000);
		}
		fclose($handle); 
	}	
	
	$ConfDir = preg_replace("/.*IndexDir ([^\n]+).*/msi", "\\1", $conf);
	$ConfType = preg_replace("/.*IndexOnly ([^\n]+).*/msi", "\\1", $conf);
	$aConfDir = explode(" ", $ConfDir);
	$aConfType = explode(" ", $ConfType);
}
elseif (isset($_POST['todo']) && ($_POST['todo'] == "save")) {
	// mode sauve
	$aConfDir = explode("\n", $_POST['fDir']);
	$aConfType = explode("\n", $_POST['fType']);
	foreach($aConfDir as $kd => $sDir){
		$aConfDir[$kd] = preg_replace('/^\/(.+)$/msi', '../../../$1', trim($aConfDir[$kd]));	
	}
	foreach($aConfType as $kd => $sType){
		$aConfType[$kd] = trim($aConfType[$kd]);	
	}
	
	$ConfDir = implode(' ', $aConfDir);
	
	$ConfType = preg_replace("/[\n\r\t\v]/msi", "", join($aConfType, " "));
	$conf = "# Fichier de configuration de Swish-E pour le site ".$oSite->get_name()."\nIndexFile donnees.index\nIndexDir ".$ConfDir."\nIndexOnly ".$ConfType."\nMaxDepth 5\nParserWarnLevel 0\nIgnoreMetaTags DESCRIPTION KEYWORDS\n#TranslateCharacters :ascii7:";
	
	$file = $_SERVER['DOCUMENT_ROOT']."/custom/search/".strtoupper($oSite->get_rep())."/swish.conf";
	
	// Let's make sure the file exists and is writable first.
	if (is_writable($file)) {
	
	   if (!$handle = fopen($file, 'w')) {
			 echo "Impossible d'ouvrir $file";
			 exit;
	   }
	
	   // Write $somecontent to our opened file.
	   if (fwrite($handle, $conf) === FALSE) {
		   echo "Impossible d'écrire dans $file";
		   exit;
	   }
	   
	   //echo "Ecriture avec succès dans le fichier ";
	   $statutRetour = "Ecriture avec succès dans le fichier de conf.";
	   
	   fclose($handle);
	
	} else {
	   echo "Le fichier $file ne peut être écrit";
	}
}
// partie index
$dir = $_SERVER['DOCUMENT_ROOT']."/custom/search/".strtoupper($oSite->get_rep());
$index = $dir.'/donnees.index';
$indexTemp = $dir.'/donnees.index.temp';

if (is_file($index)){
	$sMessIndex = date("d-m-Y", filemtime($index));
}
elseif (is_file($indexTemp)){
	$sMessIndex = "site en cours d'indexation";
}
else{
	$sMessIndex = "site jamais indexé";
}

if (isset($_POST['todo']) && ($_POST['todo'] == "index")) {
	
	
	chdir($dir);
	//current directory
	//echo getcwd()."\n"; 
	echo $index."<br />";
	
	$indexCommand = $swishObj->str_engine." -S http -c swish.conf";
	echo $indexCommand;
	
	exec($indexCommand, $output);
	pre_dump($output); 
	//echo $indexCommand;
}

?>
<style type="text/css">
	.ligne {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 12px;
		text-decoration: none;
		font-weight: normal;
		letter-spacing: normal;
	}
</style>
<script type="text/javascript">
	// validation du formulaire
	function valider(id, operation)
	{
		document.siteForm.action = "<?php echo $_SERVER['PHP_SELF']; ?>";
		document.siteForm.todo.value = "save";		
		document.siteForm.submit();	
	}
	function indexer(id)
	{
		document.siteForm.action = "<?php echo $_SERVER['PHP_SELF']; ?>";
		document.siteForm.todo.value = "index";		
		document.siteForm.submit();	
	}
</script>
<form name="siteForm" method="post">
<input type="hidden" id="todo" name="todo" value="" />
<span class="arbo2"><b>Propriétés de recherche</b></span>

<table width="500" border="0" align="center" cellpadding="3" cellspacing="3" class="arbo">
<?php
if (isset($statutRetour) && ( $statutRetour != "")){
?>
	<tr class="ligne">
		<td class="arbo" colspan="2"><strong><?php echo $statutRetour; ?>&nbsp;</strong></td>
	</tr>
<?php
	unset($statutRetour);
}
?>
	<tr class="ligne">
		<td width="146" class="arbo"><strong>Site&nbsp;:</strong></td>
		<td width="265" class="arbo"><?php echo $oSite->get_name(); ?><?//=$oSite->getDesc_site()?>&nbsp;</td>
	</tr>
	<tr class="ligne">
		<td class="arbo"><strong>Dossiers index&eacute;s&nbsp;:</strong><br />
un dossier par ligne,<br />
par exemple:<br />
content/<?php echo $oSite->get_rep(); ?>/</td>
		<td class="arbo"><textarea class="arbo textareaEdit" id="fDir" name="fDir"><?php echo join($aConfDir, "
"); ?></textarea></td>
	</tr>
	<tr class="ligne">
	  <td class="arbo"><strong>Fichiers index&eacute;s&nbsp;:</strong><br />
un type par ligne,<br />
par exemple:<br /> .php</td>
	  <td class="arbo"><textarea class="arbo textareaEdit" id="fType" name="fType"><?php echo join($aConfType, "
"); ?></textarea></td>
    </tr>
</table>

<br>
<div align="center" class="arbo">
  <p>
    <input type="button" class="arbo" value="<?php $translator->echoTransByCode('btValider'); ?>" onClick="javascript:valider(<?php echo $oSite->get_id(); ?>, 'UPDATE')">
  </p>
</div>  
<span class="arbo2"><b>Indexation</b></span>
<p></p>


<table width="500" border="0" align="center" cellpadding="3" cellspacing="3" class="arbo">
  <?php
if (isset($indexCommand) && ( $indexCommand != "")){
?>
  <tr class="ligne">
    <td class="arbo" colspan="2"><strong>
      Indexation lancée par&nbsp;:&nbsp;<?php echo $indexCommand; ?><br>
	dans <?php echo $dir; ?>
&nbsp;</strong></td>
  </tr>
  <?php
	unset($indexCommand);
}
?>
  <tr class="ligne">
    <td width="146" class="arbo"><strong>derni&egrave;re indexation&nbsp;:</strong><br />      </td>
	
    <td width="265" class="arbo"><?php echo $sMessIndex; ?>&nbsp;</td>
  </tr>
</table>
<div align="center" class="arbo">
  <p>
    <input type="button" class="arbo" value="Lancer l'indexation" onClick="javascript:indexer(<?php echo $oSite->get_id(); ?>)">
  </p>
</div>
</form>
<span class="arbo2"><b>Infos - commandes Shell</b></span>
<p></p>
<table width="500" border="0" align="center" cellpadding="3" cellspacing="3" class="arbo">

  <tr class="ligne">
    <td class="arbo" colspan="2">
      En shell, lancer l'indexation par&nbsp;:<br />
&nbsp;<?php echo $swishObj->str_engine; ?> -S http -c swish.conf<br>
	dans <?php echo $dir; ?>
&nbsp;</td>
  </tr>
  <tr class="ligne">
    <td class="arbo" colspan="2">Fichier de configuration:<br />
<?php echo $dir; ?>/swish.conf&nbsp;</td>
  </tr>
   <tr class="ligne">
    <td class="arbo" colspan="2">Fichier d'index:<br />
<?php echo $dir; ?>/donnees.index&nbsp;</td>
  </tr>
</table>
