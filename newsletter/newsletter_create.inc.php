<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
$sqlValide = "select distinct count(*) from news_inscrit, news_assoinscrittheme where ins_id=xit_news_inscrit and ins_mail ='".$addy."' and xit_news_theme=".$oNews->get_theme()." and xit_statut =".DEF_ID_STATUT_LIGNE;

$eCountValide = dbGetUniqueValueFromRequete($sqlValide);
if ($eCountValide == 0) {
	$sqlPasValide = "select count(*) from news_inscrit, news_assoinscrittheme where ins_id=xit_news_inscrit and ins_mail ='".$addy."' and xit_news_theme=".$oNews->get_theme()." and xit_statut =".DEF_ID_STATUT_ATTEN;
	$eCountPasValide = dbGetUniqueValueFromRequete($sqlPasValide);
	
	if ($eCountPasValide!= 0) { // user en attente
		// update du statut
		$sql = "select * from news_inscrit where ins_mail ='".$addy."' ";
		$aIns = dbGetObjectsFromRequete("news_inscrit", $sql);
		$oIns = $aIns[0];
		$sql = "select * from news_assoinscrittheme where xit_news_theme=".$oNews->get_theme()." and xit_news_inscrit= ".$oIns->get_id();
		$aInscrittheme = dbGetObjectsFromRequete("news_assoinscrittheme", $sql);
		$oInscrittheme = $aInscrittheme[0];
		$oInscrittheme = new news_assoinscrittheme($oInscrittheme->get_id());
		$oInscrittheme->set_statut(DEF_ID_STATUT_LIGNE);
		dbUpdate($oInscrittheme);
		
		$messageAdd.= $addy." a été créé <br/>\n";
	}
	else { // user non inscrit ou désabonné
		$sqlDesabo = "select count(*) from news_inscrit, news_assoinscrittheme where ins_id=xit_news_inscrit and ins_mail ='".$addy."' and xit_news_theme=".$oNews->get_theme()." and xit_statut =".DEF_ID_STATUT_ARCHI;
		$eCountDesabo = dbGetUniqueValueFromRequete($sqlDesabo);
		
		if ($eCountDesabo == 1) {
			$messageAdd.= $addy." déjà inscrit et désabonné <br/>\n";
		}
		else {
			// faut-il créer l'inscrit ?
			$sql = "select * from news_inscrit where ins_mail ='".$addy."' ";
			$eCountIns = dbGetUniqueValueFromRequete($sql);
			
			if ($eCountIns == 0) {// insert de l'inscrit
				$oIns = new news_inscrit();
				$oIns->set_mail($addy);
				$oIns->set_inscrit(1);
				$oIns->set_cms_site($_SESSION['idSite']);
				$bRetour = dbInsertWithAutoKey($oIns);
				$messageAdd.= $addy." a été inscrit et abonné<br/>\n";
			}
			else{//existe
				$sql = "select * from news_inscrit where ins_mail ='".$addy."' ";
				$aIns = dbGetObjectsFromRequete("news_inscrit", $sql);
				$oIns = $aIns[0];
				$bRetour=$oIns->get_id();
				$messageAdd.= $addy." déjà existant a été abonné<br/>\n";
			}
			
			// insert asso inscrit-theme
			$oInscrittheme = new news_assoinscrittheme();
			$oInscrittheme->set_news_inscrit($bRetour);
			$oInscrittheme->set_news_theme($oNews->get_theme());
			$oInscrittheme->set_statut(DEF_ID_STATUT_LIGNE);
			$bRetour = dbInsertWithAutoKey($oInscrittheme);
			
			
		}					
	}
}
else {
	$messageAdd.= $addy." déjà inscrit <br/>\n";
	$sql = "select * from news_inscrit where ins_mail ='".$addy."' ";
	$aIns = dbGetObjectsFromRequete("news_inscrit", $sql);
	$oIns = $aIns[0];				
}	


if (defined('DEF_CRITERE_LIB') && is_file(DEF_CRITERE_LIB)) {
	include_once(DEF_CRITERE_LIB);
	
	 
	$oCriNlter = new critereNewsletter();
	$oCriNlter->eNews = $id;
	 
	if (method_exists($oCriNlter, 'updateNewInscrit')){
		$bR = $oCriNlter->updateNewInscrit($oIns); 
	} 
 
}		
			
?>