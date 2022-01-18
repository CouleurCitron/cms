
<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');



// purge gabarit
// supprime les pages suite à la suppression d'un gabarit
function deletePageByIdGabarit ($id, $action){
	
	$gabaritToDelete = new Cms_page($id);
	echo "<h2>SUPPRESSION GABARIT ".$gabaritToDelete->name_page."</h2>";

	echo "<br /><br><b>SUPPRESSION DES PAGES LIEES</b><br>";
	
	$sql = "SELECT * FROM `cms_page` where gabarit_page=".$id." and id_site = ".$_SESSION['idSite_travail']." and valid_page=1;";
	$aPages = dbGetObjectsFromRequete("Cms_page", $sql);
	foreach($aPages as $key => $page){
		echo "<br /><br>";
		echo "-- page : ".$page->name_page.".php<br>\n";
		echo "-- gabarit : ".$page->getGabarit_page()."<br>\n";
		echo "-- site : ".$page->id_site."<br>\n";		
		
		if ($action == "delall") {
			global $HTTP_SERVER_VARS;
			$urlFile_ = getUrlWithIdPage($page->id_page);
			$urlFile=str_replace(" ", "", $urlFile_);
			$file = "../../../content/".$_SESSION['site_travail'].$urlFile;
			//echo $file."<br>";
			/*if(file_exists($file)) {
				$resultSVR = @unlink($file);
				if ($resultSVR) echo "Suppression du Serveur réussi<br>"; else echo "\nErreur sur la suppression sur Serveur<br>";
			}
			else {
				echo "Le fichier ".$file." n'existe pas.<br>";
			}*/
			$resultSVR = true;
			@unlink($file);
			echo "Suppression du Serveur réussie<br>";
		}
		else {
			$resultSVR = true;
		}
		$resultCMS = deletePage($page->id_page);
		if ($resultCMS) {
			echo "Suppression du CMS réussie<br>"; 
			deleteInfosByIdPage($page->id_page);
			deleteStructByIdPage($page->id_page);
		}
		else {
			echo "\nErreur sur la suppression sur CMS<br>";
		}
		
		
	}
	echo "<br /><br>";
	return $resultCMS;
	
}

function deletePageByIdPage ($id, $action){
	
	$page = new Cms_page($id);
	echo "<h2>SUPPRESSION PAGE ".$page->name_page."</h2>";

	echo "<br /><br>";
	echo "-- page : ".$page->name_page.".php<br>\n";
	echo "-- gabarit : ".$page->getGabarit_page()."<br>\n";
	echo "-- site : ".$page->id_site."<br>\n";		
	
	if ($action == "delall") {
		global $HTTP_SERVER_VARS;
		$urlFile_ = getUrlWithIdPage($page->id_page);
		$urlFile=str_replace(" ", "", $urlFile_);
		$file = "../../../content/".$_SESSION['site_travail'].$urlFile;
		//echo $file."<br>";
		/*if(file_exists($file)) {
			$resultSVR = @unlink($file);
			if ($resultSVR) echo "Suppression du Serveur réussi<br>"; else echo "\nErreur sur la suppression sur Serveur<br>";
		}
		else {
			echo "Le fichier ".$file." n'existe pas.<br>";
		}*/
		$resultSVR = true;
		@unlink($file);
		echo "Suppression du Serveur réussie<br>";
	}
	else {
		$resultSVR = true;
	}
	$resultCMS = deletePage($page->id_page);

	if ($resultCMS) {
		echo "Suppression du CMS réussie<br>"; 
		deleteInfosByIdPage($page->id_page);
		deleteStructByIdPage($page->id_page);
	}
	else {
		echo "\nErreur sur la suppression sur CMS<br>";
	}
		
		
	echo "<br /><br>";
	return $resultCMS;
	
}


function deleteInfosByIdPage($idpage) {
	$oPage = new Cms_page($idpage);
	$isGabarit = $oPage->isgabarit_page;
	$namePage = $oPage->name_page;
	$resultDelInfos = dbDeleteId("cms_infos_pages", "page_id", $idpage);
	if ($resultDelInfos) {
		if ( $isGabarit == 1) echo "Suppression des <b>INFOS</b> du Gabarit ".$namePage."</b><br>";
		else echo "Suppression des <b>INFOS</b> de la Page ".$namePage.".php<br>";
	}
	else {
		echo "\nErreur sur la suppression des infos pour la page/le gabarit ".$namePage."<br><br><br>";
	}
}

function deleteStructByIdPage($idpage) {
	
	$oPage = new Cms_page($idpage);
	$isGabarit = $oPage->isgabarit_page;
	$namePage = $oPage->name_page;
	$sql = "SELECT * FROM `cms_struct_page` where id_page = ".$idpage.";";
	$aStruct_pages = dbGetObjectsFromRequete("cms_struct_page", $sql);
	
	foreach($aStruct_pages as $key => $struct_pages){
		$idContent = $struct_pages->getId_content();
		$resultDelStruct = dbDeleteId("cms_struct_page", "id_page", $idpage);
		if ($resultDelStruct) {
			if ( $isGabarit == 1) echo "Suppression de la <b>STRUCTURE</b> du Gabarit ".$namePage."</b><br>";
			else echo "Suppression de la <b>STRUCTURE</b> de la Page ".$namePage.".php<br>";
			deleteContentByIdStruct($idContent, $isGabarit, $namePage);
		}
		else {
			echo "\nErreur sur la suppression de la structure pour la page/le gabarit ".$namePage."<br><br><br>";
		}
	}
}


// purge cms_content
function deleteContentByIdStruct($idcontent, $isGabarit,  $namePage) {

	$oContent = new Cms_content($idcontent);
	$oContent->setStatut_content(DEF_ID_STATUT_ARCHI);
	$nameContent = $oContent->name_content;
	
	if ($oContent->getIsbriquedit_content() == 1) {
		$resultUpdStruct = dbUpdate($oContent);
		if ($resultUpdStruct) {
			if ( $isGabarit == 1) echo "Archivage du <b>CONTENU</b> ".$nameContent." lié au Gabarit ".$namePage."</b><br>";
			else echo "Archivage du <b>CONTENU</b> ".$nameContent." lié à la Page ".$namePage.".php<br>";
		}
		else {
			echo "\nErreur sur l'archivage de la CONTENU pour la page/le gabarit ".$namePage."<br><br><br>";
		}
	}
}


?>