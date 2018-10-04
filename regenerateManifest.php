<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/gabarit.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

activateMenu('gestiongabarit');  //permet de dérouler le menu contextuellement

if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

?><div class="ariane"><span class="arbo2">GABARIT&nbsp;>&nbsp;</span>
<span class="arbo3">Regénération</span></div>
<?php

// liste des gabarits
// $contenus = getListGabarits($idSite);
$contenus = getAllPagesNotRecursive($_SESSION['idSite']);

?>
<script type="text/javascript">
document.title="Regénération de tout le site";
</script>
<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<form name="managetree" action="arboaddnode.php" method="post">
<input type="hidden" name="urlRetour" value="<?php echo $_POST['urlRetour']; ?>">
<input type="hidden" name="idSite" value="<?php echo $idSite; ?>">
<?php
// site de travail
if (DEF_MENUS_MINISITES == "ON") print(putAfficheSite());
?>
</form>
<div class="arbo">Attention, cette fonction propose de regénérer le manifeste permettant de consulter le site hors ligne.<br /><br />
Cette fonction peut être longue à s'executer, patienter jusqu'à la fin du traitement.
</div>
<br /><br />
<?php
	// tableau vide
	if((!is_array($contenus)) or (sizeof($contenus)==0)) {
?>
<strong><div class="arbo">Pas de pages pour ce site</div></strong>
<?php
} else {

	$oSite = new cms_site($_SESSION['idSite']);
	$site_repo = $oSite->get_rep();


	?> 

	<form name="updateManifest" method="post" action="/backoffice/cms/updateManifest.php">
		<input type="hidden" name="connectSite" id="connectSite" value="<?php echo $_SESSION['idSite_travail'];?>" />
		<input name="envoyer" id="boutonvalid" type="submit" class="arbo" value="Générer le manifeste">
	</form>

	<?php

}

include_once('regeneratePages.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/append.php');
?>