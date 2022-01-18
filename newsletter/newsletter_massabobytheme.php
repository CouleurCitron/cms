<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
fichier privé : non accessible par le menu.
sert à abonner des inscrit à la newsletter $outTheme si déjà abonnés à $inTheme

*/
 
$inTheme=7;
$outTheme=9;

if ($inTheme && $outTheme){   

	
	// newsletter inscrit	
	$sql = " SELECT news_assoinscrittheme.* FROM news_inscrit, news_assoinscrittheme";
	$sql.= " WHERE xit_statut =".DEF_ID_STATUT_LIGNE." ";
	$sql.= " AND xit_news_inscrit = ins_id ";
	$sql.= " AND xit_news_theme = ".$inTheme." ";	
	 
 	$aInscrit = dbGetObjectsFromRequete("news_assoinscrittheme", $sql);	

	foreach($aInscrit as $k => $oInscrit){
		$oInscrit->set_news_theme($outTheme);
		dbSauveAsNew($oInscrit);
	}
}
else{
	echo "La newsletter n'a pas été trouvé";
}
?>