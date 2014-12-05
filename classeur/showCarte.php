<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/* 



****************************************************

*** Gestion de l'arbo des cartes                 ***

*** Classeur de cartes                           ***

*** Affichage d'une carte                        ***

*** ruse pour cacher le répertoire source en 777 ***

****************************************************



*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbocarte.lib.php');
$id = $_GET['id'];
/*
$carte = getCarteById($_GET['id']);
pre_dump($carte);
echo $sql;*/
if($carte = getCarteById2($id)) {
	//echo $CMS_ROOT_CLASSEUR_UPLOADFILE."/".$carte['chemin_absolu'];
	   	//Begin writing headers
	  	header("Pragma: public");
	  	header("Expires: 0");
	  	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	  	header("Cache-Control: public");
	   	//Use the switch-generated Content-Type
	   	header("Content-Type: application/pdf");
		// Si un jour un veut le télécharger plutot que de l'afficher!
		// header('Content-Disposition: attachment; filename="'.$carte['chemin_absolu'].'"');
	@readfile($CMS_ROOT_CLASSEUR_UPLOADFILE."/".$carte['chemin_absolu']);
}
?>