<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
fichier privé : non accessible par le menu.
sert à abonner des inscrit à la newsletter $outTheme si leur date d'ajout est postérieure à $inDate

*/

$inDate = '2012-10-08';
$outTheme=2;

if ($outTheme){   

	
	// newsletter inscrit	
	$sql = " SELECT * FROM `news_inscrit` WHERE `ins_dt_crea` > '".$inDate."'; ";	
	 
 	$aInscrit = dbGetObjectsFromRequete("news_inscrit", $sql);	

	foreach($aInscrit as $k => $oInscrit){

		$sqlValide = "select count(*) from news_assoinscrittheme where xit_id ='".$oInscrit->get_id()."' and xit_news_theme=".$outTheme.";";

		$eCountValide = dbGetUniqueValueFromRequete($sqlValide);

		if($eCountValide==0){ // on abonne
			$oX = new news_assoinscrittheme();
			$oX->set_news_inscrit($oInscrit->get_id());
			$oX->set_news_theme($outTheme);
			$oX->set_statut(DEF_ID_STATUT_LIGNE);
			dbSauve($oX);
		}
		else{
			$sqlValide = "select * from news_assoinscrittheme where xit_id ='".$oInscrit->get_id()."' and xit_news_theme=".$outTheme.";";
			$aX = dbGetObjectsFromRequete("news_assoinscrittheme", $sql);
			if ($aX){
				$oX = $aX[0];
				if ($oX->get_statut() == DEF_ID_STATUT_LIGNE){// deja abonné
					/// ras
				}
				elseif ($oX->get_statut() == DEF_ID_STATUT_ARCHI){ /// archivé == desabonne, on ne touche à rien
					/// ras
				}
				elseif ($oX->get_statut() == DEF_ID_STATUT_ATTEN){ // en attente on abonne
					$oX->set_statut(DEF_ID_STATUT_LIGNE);
					dbSauve($oX);
				}
				else{ // ne devrait JAMAIS se produire 
					$oX->set_statut(DEF_ID_STATUT_LIGNE);
					dbSauve($oX);
				}
			}	
		}

	}
}
else{
	echo "La newsletter n'a pas été trouvé";
}
?>