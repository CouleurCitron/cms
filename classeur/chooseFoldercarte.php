<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 

**********************************************
*** Gestion de l'arbo des cartes           ***
*** Classeur de cartes                     ***
*** Récupéré du fichier chooseFolder.php   ***
**********************************************

*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbocarte.lib.php');

$idSite = $_GET['idSite'];
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

if ($_GET['v_comp_path']=="") { // Si on a pas de dossier par défaut on essaye de charger celui en mémoire
	if($_SESSION['carte_v_comp_path']!="") { // On a justement un dossier favoris en mémoire
		$_GET['v_comp_path'] = $_SESSION['carte_v_comp_path'];
	}
}

$virtualPath=$_GET['v_comp_path'];
if(strlen($virtualPath)==0)
	$virtualPath=0;
$_SESSION['carte_v_comp_path'] = $virtualPath;// On enregistre dans le dossier favoris le dossier courant

$nodeInfos=getNodeInfosCarte($idSite,$db,$virtualPath);
?><style type="text/css">
<!--
body {
	margin-left: 3px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style><link rel="stylesheet" type="text/css" href="<?php echo $URL_ROOT; ?>/css/bo.css">
<form>
<style type="text/css">
body {
	background-color:#efefef;
};
.toto {
        color: #ffffff;
        text-decoration: underline;
        background-color: #316AC5;
};
</style>
<script type="text/javascript">
function execChoose() {
	window.opener.document.forms[0].node_id.value=<?php echo $nodeInfos['id']; ?>;
	window.opener.document.forms[0].foldername.disabled=false;
	window.opener.document.forms[0].foldername.value="<?php echo $nodeInfos['libelle']; ?>";
	window.opener.document.forms[0].foldername.disabled=true;
	self.close();
}
</script>

<hr width="100%" class="arbo2">
<span class="arbo"><small><?php echo getAbsolutePathStringCarte($db, $virtualPath); ?></small>
</span>
<hr width="100%" class="arbo2">
<span class="arbo">
<?php

// On ne déplie pas les répertoires de 3ème niveau
$argprofondmax = null;
if($_GET['arbomax']!="") {
	$toodeep = (substr_count($virtualPath,",")>2);
	$OP = (preg_match('/\?/',$_SERVER['PHP_SELF'])) ? '&' : '?' ; // Si la page a déjà des arguments => on ajoute
	$argprofondmax = $_SERVER['PHP_SELF'].$OP."arbomax=".$_GET['arbomax'];
}
echo drawCompTreeCarte($db,$virtualPath,null,$argprofondmax); // $profondmax => affiche pas plus de 3 niveaux
?>
</span>
<hr width="100%" class="arbo2">
<div align="center">
<?php
	if($toodeep) {
?>
<span class="arbo"><strong>Vous ne pouvez pas choisir ce dossier<br>
Il ne peut pas y avoir plus de 3 niveaux dans le classeur.</strong></span>
<?php
	} else {
?>
<input name="Choisir ce dossier" type="button" class="arbo" onClick="javascript:execChoose();" value="Choisir ce dossier">
<?php
	}
?>
</div>
</form>
