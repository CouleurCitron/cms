<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');

// site sur lequel on est positionné
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

if ($idSite == "") die ("Appel incorrect de la fonctionnalité");

//-------------------------------------------------
// dossier parcouru de l'arborescence en mémoire

$virtualPath = 0;

if ($_GET['v_comp_path']=="") { // Si on a pas de dossier par défaut on essaye de charger celui en mémoire
	if($_SESSION['brique_v_comp_path']!="") { // On a justement un dossier favoris en mémoire
		$_GET['v_comp_path'] = $_SESSION['brique_v_comp_path'];
	}
}

if (strlen($_GET['v_comp_path']) > 0)
	$virtualPath = $_GET['v_comp_path'];
	$_SESSION['brique_v_comp_path'] = $virtualPath;// On enregistre dans le dossier favoris le dossier courant

//-------------------------------------------------

$nodeInfos = getNodeInfos($db, $virtualPath);

?><link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<style type="text/css">
<!--
body {
	margin-left: 3px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style><form>
<style type="text/css" class="arbo2">
body {
	background-color:#efefef;
};
</style>

<script type="text/javascript">
function execChoose() {
	window.opener.document.forms[1].node_id.value=<?php echo $nodeInfos['id']; ?>;
	window.opener.document.forms[1].foldername.disabled=false;
	window.opener.document.forms[1].foldername.value="<?php echo $nodeInfos['libelle']; ?>";
	window.opener.document.forms[1].foldername.disabled=true;
	self.close();
}
</script>
<hr width="100%" class="arbo2">
<span class="arbo"><?php echo getAbsolutePathString($idSite, $db, $virtualPath); ?></span>
<hr width="100%" class="arbo2">
<span class="arbo"><?php
echo drawCompTree($idSite, $db, $virtualPath);
?></span>
<hr width="100%" class="arbo2">
<div align="center"><input name="Choisir ce dossier" type="button" class="arbo" onClick="javascript:execChoose();" value="Choisir ce dossier">
</div>
</form>
