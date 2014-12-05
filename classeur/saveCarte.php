<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbocarte.lib.php');

activateMenu('gestioncarte');  //permet de dérouler le menu contextuellement

// Si pas de dossier choisi, on met la carte à la racine
$node_id = ($_POST['node_id']!="") ? $_POST['node_id'] : 0;

?>

<link href="<?php echo $URL_ROOT; ?>/css/bo.css" rel="stylesheet" type="text/css" />

<span class="arbo2"><b>Stockage du composant <?php echo stripslashes($_POST['nom']); ?> dans le classeur</b></span>

<?php

// Remplacement des :;,./ par des , dans la liste des mots clés
// Et ajustement des espaces
$_POST['motscles'] = preg_replace('/[\s]*[\:\;\.\,\/\_][\s]*/',', ',$_POST['motscles']);

	// ok, on enregistre
	$id = $_GET['id'];
	if($_POST['id']!="") $id = $_POST['id'];
	$idnodeold= $_POST['idnode'];
	$idnodenew= $_POST['node_id'];
	if (strlen($id) > 0 ) {
		$test = storeComposantCarte($_POST['nom'],$_POST['motscles'],$_POST['description'],$_POST['chemin_relatif'], $idnodenew, $idnodeold, $_POST['dateetude'], $_POST['datepub'], $_POST['lieu'], $_POST['page'], $_POST['poids'],$id); // 10 param = ajout, 11 = modif

	} else {
		$test = storeComposantCarte($_POST['nom'],$_POST['motscles'],$_POST['description'],$_POST['chemin_relatif'], $idnodenew, $idnodeold, $_POST['dateetude'], $_POST['datepub'], $_POST['lieu'], $_POST['page'], $_POST['poids']); // 10 param = ajout, 11 = modif
	}
	
	if($test) {
?>
<br />
<br />
<span class="arbo">L'association a été correctement sauvegardée. <br />
<br />
<script language="javascript" type="text/javascript">bOpen=false; OpenMenu('nav'); </script>
<?php
	} else {
?>
<br />
<br />
Une erreur s'est produite lors de la sauvegarde de l'association. Veuillez réessayer. Si le problème persiste, veuillez contacter l'administrateur</span>
<?php
	}

?>
</span>