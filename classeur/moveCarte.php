<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 

***************************************************
*** Gestion de l'arbo des cartes                ***
*** Classeur de cartes                          ***
*** Déplacement d'une carte                     ***
***************************************************

*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbocarte.lib.php');

activateMenu('gestioncarte');  //permet de dérouler le menu contextuellement

$idSite = $_GET['idSite'];
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

$id = $_GET['id'];
$node_id = 0;
$node_name = 'non défini';
$carte = null;
if (strlen($id) > 0 ) {
	$carte = getCarteById($id, $_GET['idnode']);
	$node_id = $carte['node_id'];
	$node = getNodeInfosCarte($idSite, $db, $_GET['idnode']);
	$node_name = $node['libelle'];
}

?>
<script type="text/javascript">
  function chooseFolder() {
	window.open('chooseFoldercarte.php','browser','scrollbars=yes,width=500,height=500,resizeable=yes,menubar=no');
  }
</script>
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
<link href="<?php echo $URL_ROOT; ?>/css/bo.css" rel="stylesheet" type="text/css">

<?php

if(!(isset($_POST['node_id']) && strlen($_POST['node_id'])>0 )) {

?>
<span class="arbo2"><strong>Déplacement de l'association <?php echo $carte['name']; ?> dans le classeur</strong></span>
<form name="saveCuBrick" action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $_GET['id']; ?>" method="post">
<input type="hidden" name="node_id" value="<?php echo $node_id; ?>">
<input type="hidden" name="idnode" value="<?php echo $_GET['idnode']; ?>">
<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
 <tr>
  <td align="right">Dossier de stockage&nbsp;:&nbsp;</td>
  <td align="left"><input name="foldername" type="text" disabled class="arbo" value="<?php echo $node_name; ?>" size="20">&nbsp;&nbsp;<a href="#" class="arbo" onClick="chooseFolder();"><img src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/go.gif" border="0">&nbsp;choisir le dossier</a></td>
 </tr>
 <tr bgcolor="D2D2D2">
  <td colspan="2" align="center"><input name="Déplacer" type="submit" class="arbo" value="Déplacer"></td>
 </tr>
</table>
</form>

<?php
} else {
	// ok, on enregistre
	$test = null;
	$id = $_GET['id'];
	$carte = getCarteById($id, $_GET['idnode']);
	$node = getNodeInfosCarte($idSite, $db, $_POST['idnode']);
	$ancien_dossier = $node['libelle'];
	$test = moveComposantCarte($id,$_POST['idnode'], $_POST['node_id']);
	if($test) {
	$node = getNodeInfosCarte($idSite, $db, $_POST['node_id']);
	$nouveau_dossier = $node['libelle'];

?>
<span class="arbo">Ancien dossier : <?php echo $ancien_dossier; ?><br>
<br>
Nouveau dossier : <?php echo $nouveau_dossier; ?><br>
<br>
La carte a été déplacée.</span>
<script>bOpen=false; OpenMenu('nav'); </script>
<?php
	} else {
?>
<br>
<br>
<span class="arbo">Une erreur s'est produite lors de la sauvegarde de la carte. Veuillez réessayer. Si le problème persiste, veuillez contacter l'administrateur</span>
<?php
	}
}
?>
