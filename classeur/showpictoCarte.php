<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/* 



****************************************************

*** Gestion de l'arbo des cartes                 ***

*** Classeur de cartes                           ***

*** Affichage d'un picto                         ***

*** ruse pour cacher le répertoire source en 777 ***

****************************************************



*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbocarte.lib.php');
$id = $_GET['img_id'];
if($picto = getArbopictoCarte($id)) {
	   //Begin writing headers
	   header("Pragma: public");
	   header("Expires: 0");
	   header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	   header("Cache-Control: public");
	   //Use the switch-generated Content-Type
	   header("Content-Type: image/".preg_replace('/^.*\.([^\.]+)$/','\\1',$picto));
	@readfile($CMS_ROOT_CLASSEUR_UPLOADIMG."/".$picto);

}
?>