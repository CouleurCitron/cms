<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/* 



***************************************************

*** Gestion de l'arbo des cartes                ***

*** Classeur de cartes                          ***

*** Suppression d'une carte                     ***

***************************************************



*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbocarte.lib.php');
activateMenu('gestioncarte');  //permet de dérouler le menu contextuellement
$id = $_GET['id'];
$carte = null;
$result = false;
if (strlen($id) > 0)
	/*$carte = getCarteById($id, $_GET['idnode']);
echo $carte;
if ($carte!=null) {
	echo "je rentre";*/
	$result = deleteCarte($id, $_GET['idnode']);
		
//}

?>

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

<body class="arbo">



<?php
if ($result) {
?>



<strong>Suppression effectuée.</strong>
<script language="JavaScript" type="text/javascript">
document.location.href="arboCarte_browse.php";
</script>


<?php
} else {
?>



Erreur lors de la suppression de l'association. 

Merci de contacter l'administrateur si l'erreur persiste.



<?php
}
?>

</body>