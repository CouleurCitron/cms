<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

// sopnthus 17/06/05
// ajout id_site

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

if ($idSite == "") $idSite = $_SESSION['idSite_travail'];



if ($_GET['v_comp_path']=="") { // Si on a pas de dossier par défaut on essaye de charger celui en mémoire
	if($_SESSION['brique_v_comp_path']!="") { // On a justement un dossier favoris en mémoire
		$_GET['v_comp_path'] = $_SESSION['brique_v_comp_path'];
	}
}
$virtualPath=$_GET['v_comp_path'];
	$_SESSION['brique_v_comp_path'] = $virtualPath;// On enregistre dans le dossier favoris le dossier courant
if(strlen($virtualPath)==0)
	$virtualPath=0;
$nodeInfos=getNodeInfos($db, $virtualPath);
?><style type="text/css">
<!--
body {
	margin-left: 3px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style><link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<form>
<style type="text/css">
body {
	background-color:#efefef;
};
</style>
<script type="text/javascript">
function execChoose() {

//alert("<?//=$nodeInfos['id']?>");

	window.opener.document.forms['saveCuBrick'].node_id.value=<?php echo $nodeInfos['id']; ?>;
	window.opener.document.forms['saveCuBrick'].foldername.disabled=false;
	window.opener.document.forms['saveCuBrick'].foldername.value="<?php echo $nodeInfos['libelle']; ?>";
	window.opener.document.forms['saveCuBrick'].foldername.disabled=true;
	self.close();
}
</script>

<hr width="100%" class="arbo2">
<span class="arbo"><small><?php echo getAbsolutePathString($idSite, $db, $virtualPath); ?></small>
</span>
<hr width="100%" class="arbo2">
<span class="arbo">
<?php
echo drawCompTree($idSite, $db, $virtualPath);
?>
</span>
<hr width="100%" class="arbo2">
<div align="center"><input name="Choisir ce dossier" type="button" class="arbo" onClick="javascript:execChoose();" value="Choisir ce dossier">
</div>
</form>
