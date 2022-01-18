<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

//init_set ('error_reporting', E_ALL);
	
// permet de dérouler le menu contextuellement
activateMenu("gestioncms_theme");

if (is_post("todo") == false){
	if (is_get("display") == true){
		
		$id = $_GET["display"];
		$oTheme = new cms_theme($id);
		$aRetour = $oTheme->saveToFile();
		
		if (is_array($aRetour)){
			echo '<strong>écriture réussie :</strong><br /><br />';
			
			echo '<pre style="border:dotted">';
			include($_SERVER['DOCUMENT_ROOT'].$aRetour[0]);
			echo '</pre>';
			
			echo '<pre style="border:dotted">';
			include($_SERVER['DOCUMENT_ROOT'].$aRetour[1]);
			echo '</pre>';		
		
		}
		//;
	}
	else{
		echo "no valid theme id submitted";
	}
}
?>
