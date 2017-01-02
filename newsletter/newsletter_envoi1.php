<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

// sponthus 21/12/2005

// envoi automatique des newsletter du jour



// newsletter à envoyer
$sSql = "SELECT * FROM newsletter ";
$sSql.= " WHERE news_id=".$id;

$aNews = dbGetObjectsFromRequete("newsletter", $sSql);

if ($aNews){
	$oNews = $aNews[0];
	$idNews = $oNews->get_id();
	$statutNews = $oNews->get_statut();
	$themeNews = $oNews->get_theme();
	
	// newsletter inscrit
	$sql = " SELECT * FROM news_inscrit ";
	$sql.= " WHERE ins_statut=".DEF_ID_STATUT_LIGNE;
	$sql.= " AND ins_theme=".$themeNews;

	$aInscrit2 = dbGetObjectsFromRequete("News_inscrit", $sql);
	//--------------------------------------
	//print("<br><div>sizeof(aInscrit2)=".sizeof($aInscrit2)."</div>");
	
	/*
	$html = "sizeof(aNews)=".sizeof($aNews)."<br>";
	$html.= "sSql=".$sSql;
	$html = "sizeof(aInscrit2)=".sizeof($aInscrit2)."<br>";
	send_mail("", "sylvie@couleur-citron.com" , "newsletter ?" , $html , "newsetter_text", "mail_auto");
	*/
	
	$eInssel = getCount("news_select", "slc_id", "slc_newsletter", $idNews ,"");
	
	
	if ($eInssel != 0) {
		$sql = "select * from news_select where slc_newsletter=".$idNews;
		$aNewssel = dbGetObjectsFromRequete("news_select", $sql);
		for ($pp=0; $pp<sizeof($aNewssel); $pp++) {
			$oNewssel = $aNewssel[$pp];
			$bRetour = dbDelete($oNewssel);
		}
	}
	
	
	for ($p=0; $p<sizeof($aInscrit2); $p++) {
		$oInscrit2 = $aInscrit2[$p];
		$oNewsselect = new News_select();
		$oNewsselect->set_newsletter($idNews);
		$oNewsselect->set_inscrit($oInscrit2->get_id());
		$oNewsselect->set_datecrea(getDateNow());
		$oNewsselect->set_dateenvoi("");
		$oNewsselect->set_statut(DEF_ID_STATUT_NEWS_ATTEN);
		$$idNews = dbInsertWithAutoKey($oNewsselect);
	} // fin for ($p=0; $p<sizeof($aInscrit2); $p++) {
	
	
	// envoyer
	$_SESSION['exp'] = "select";
	// newsletter à envoyer
	$_SESSION['idal'] = $idal;
	$_SESSION['action']="env";
	include_once("newsletter_send.php");
		// rapport inclu dans envoi_list.php
	$oNews = new Newsletter($idnews);
	

}
else{
	echo "La newsletter n'a pas été trouvé";
}

?>