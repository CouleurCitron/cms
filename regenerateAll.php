<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 
sponthus 10/08/2005

regénération de TOUT le site
à partir de la re génération des gabarits
*/

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
$contenus = getListGabarits($idSite);

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
<div class="arbo">Attention, cette fonction propose de regénérer TOUS LES GABARITS ainsi que TOUTES LES PAGES
liées à ces gabarits.<br /><br />
Vous pouvez désélectionner les pages et gabarits que vous ne souhaitez pas regénérer.<br /><br />
Cette fonction peut être longue à s'executer, patienter jusqu'à la fin du traitement.
</div>
<br /><br />
<?php
	// tableau vide
	if((!is_array($contenus)) or (sizeof($contenus)==0)) {
?>
<strong><div class="arbo">Pas de gabarit pour ce site</div></strong>
<?php
} else {
	// pour chaque gabarit
	foreach ($contenus as $k => $page) {

		// Variable envoyée par effet de bord à regeneratePages.php
		$id_gabarit = $page['id'];
		$aIdGab[] = $id_gabarit;
	}
}

include_once('regeneratePages.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/append.php');
?>