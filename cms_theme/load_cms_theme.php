<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

//init_set ('error_reporting', E_ALL);
	
// permet de dérouler le menu contextuellement
activateMenu("gestioncms_theme");

if (is_post("todo") == false){
	if (is_get("display") == true){
		
		$id = $_GET["display"];
		$oTheme = new cms_theme($id);
		$bRetour = $oTheme->loadFromFile();
		dbSauve($oTheme);
		
		if ($bRetour){
			echo '<strong>lecture réussie :</strong><br /><br />';
			
			echo '<pre style="border:dotted">';
			echo '/***** classes *****/'."\n";
			echo $oTheme->get_classes();
			echo '/***** tags *****/'."\n";
			echo $oTheme->get_tags();
			echo '</pre>';
			
			echo '<pre style="border:dotted">';
			echo '/***** ie *****/'."\n";
			echo $oTheme->get_ie();
			echo '</pre>';		
		
		}
	}
	else{
		echo "no valid theme id submitted";
	}
}
?>
