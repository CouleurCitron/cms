<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/gabarit.lib.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


activateMenu('site_gabarit');  //permet de dérouler le menu contextuellement

if ($idSite == "") $idSite = $_SESSION['idSite_travail'];



$id = $_GET['id'];
$node_id = 0;
$node_name = 'non défini';
$page = null;

if (strlen($id) > 0 ) {

	$page = getPageById($id);

	$node_id = $page['node_id'];
	$node = getNodeInfos($db, $node_id);
	$name = $page['name'];
	$node_name = $node['libelle'];
}

if(!(isset($_POST['node_id']) && strlen($_POST['node_id'])>0 )) {
?>
<script type="text/javascript">
  function chooseFolder() {
	window.open('choosePageFolder.php','browser','scrollbars=yes,width=500,height=500,resizeable=yes,menubar=no');
  }
</script>
<link href="/backoffice/cms/css/bo.css" rel="stylesheet" type="text/css">

<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>
<span class="arbo2">GABARIT&nbsp;>&nbsp;</span>
<span class="arbo3">Duplication du gabarit</span><br><br>

<form name="saveCuBrick" action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $_GET['id']; ?>" method="post">
<input type="hidden" name="node_id" value="<?php echo $node_id; ?>">
<input type="hidden" name="idSite" value="<?php echo $idSite; ?>">

<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
 <tr>
  <td align="right">Original&nbsp;:&nbsp;</td>
  <td align="left"><input name="oldname" type="text" disabled class="arbo" value="<?php echo $name; ?>" size="35" maxlength="50">.php</td>
 </tr>
 <tr>
  <td align="right">Copie&nbsp;:&nbsp;</td>
  <td align="left"><input name="newname" type="text" class="arbo" value="copie_de_<?php echo $name; ?>" size="35" maxlength="50">.php</td>
 </tr>
 <tr bgcolor="D2D2D2">
  <td colspan="2" align="center"><br>    <input name="Dupliquer" type="submit" class="arbo" value="Dupliquer"></td>
 </tr>
</table>
</form>
<br>
<span class="arbo"><br>
<small>Merci de spécifier un nom pour la page, ne comportant que des caractères alphanumériques sans espaces.</small>
<br>
<br>
<?php
} else {
	$id = $_GET['id'];	
	$oGab = new Cms_page($id);
	$newId = dbSauveAsNew($oGab);	

	// copier les cms_struct_page
	$sql = "SELECT * from cms_struct_page where id_page = ".$id.";";	
	$aStruct = dbGetObjectsFromRequete("cms_struct_page", $sql);
	
	foreach($aStruct as $k => $oStruct){
		$oStruct->setId_page($newId);
		dbSauveAsNew($oStruct);
	}
	
	// copier les cms_infos_pages
	$sql = "SELECT * from cms_infos_pages where page_id = ".$id.";";	
	$aInfos = dbGetObjectsFromRequete("cms_infos_page", $sql);
	
	foreach($aInfos as $k => $oInfos){
		$oInfos->setPage_id($newId);
		dbSauveAsNew($oInfos);
	}
	
	$test = null;
	$test = renamePage($newId, $_POST['newname']);

	if(is_array($test)) {
?>
<br>
Copie : <?php echo $test[1]; ?><br>
<br>
<strong>La page a été dupliquée.</strong><br>
<br>
<?php
	} else {
?>
Une erreur s'est produite lors de la sauvegarde de la page. Veuillez réessayer. Si le problème persiste, veuillez contacter l'administrateur
<?php
	}
}
?>
</span>
