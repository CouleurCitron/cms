<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
// sponthus 17/06/05
// ajout du site dans l'arbo

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

// site sélectionné
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

$virtualPath=$_GET['v_comp_path'];

if(strlen($virtualPath)==0)	$virtualPath=0;

$nodeInfos=getNodeInfos($db,$virtualPath);
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
echo drawCompTree($idSite, $db,$virtualPath);
?></span>
<hr width="100%" class="arbo2">
<div align="center"><input name="Choisir ce dossier" type="button" class="arbo" onClick="javascript:execChoose();" value="Choisir ce dossier">
</div>
</form>
