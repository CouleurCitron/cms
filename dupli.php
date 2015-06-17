<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
?>
<p>Cloner un site sur ce serveur</p>
<form id="dupli" name="dupli" action="dupliProcess.php" method="post">
id site à cloner :<br />
<!--<input type="text" value="1" id="sourcesite" name="sourcesite" />-->
<select id="sourcesite" name="sourcesite">
<?php
$aSite = dbGetObjectsFromRequete("cms_site", "select * from cms_site ");
if ($aSite != false){
	foreach ($aSite as $key => $oSite){
		echo "<option value=\"".$oSite->get_id()."\">".$oSite->get_name()."</option>\n";

	}
}
?>
</select><br />
nom du site cloné:<br />
<input type="text" value="en" id="destname" name="destname" /><br />
[option : forcer la valeur de l'id de cms_site]<br /><input type="text" value="" id="destid" name="destid" /><br />
<input type="hidden" value="http://<?php echo $_SERVER['HTTP_HOST']; ?>/backoffice/cms/dupliOffsets.php" id="serveroffsetsxml" name="serveroffsetsxml" />
<input type="hidden" value="clone" id="dowhat" name="dowhat" /><br />
<input type="submit" />
</form>
<hr />
<p>Cloner un site vers un serveur distant</p>
<form id="migre" name="migre" action="dupliProcess.php" method="post">
id site à cloner :<br />
<!--<input type="text" value="1" id="sourcesite" name="sourcesite" />-->
<select id="sourcesite" name="sourcesite">
<?php
$aSite = dbGetObjectsFromRequete("cms_site", "select * from cms_site ");
if ($aSite != false){
	foreach ($aSite as $key => $oSite){
		echo "<option value=\"".$oSite->get_id()."\">".$oSite->get_name()."</option>\n";

	}
}
?>
</select><br />
nom du site cloné:<br />
<input type="text" value="en" id="destname" name="destname" /><br />
 [option : forcer la valeur de l'id de cms_site]<br /><input type="text" value="" id="destid" name="destid" /><br />
url du XML décrivant l'état de la base CMS distante:<br />
(ex. : <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/backoffice/cms/dupliOffsets.php" target="_blank">http://<?php echo $_SERVER['HTTP_HOST']; ?>/backoffice/cms/dupliOffsets.php</a>)<br />
<input type="text" value="" id="serveroffsetsxml" name="serveroffsetsxml" size="120" />
<input type="hidden" value="migre" id="dowhat" name="dowhat" /><br /><br />
<input type="submit" />
</form>