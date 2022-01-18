<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
///////////////////////////////////////////////
// sponthus 16/12/2005
//
// export d'enregistrements : 
//	- liste d'envois des newsletter 
//	- liste des inscrits envoyés par newsletter
///////////////////////////////////////////////

/*


*/




//=====================================================
// liste des inscrits envoyés d'un envoi de newsletter
//=====================================================

function get_list_inscrits_envoi($id) {

	$sSql = "SELECT news_inscrit.* FROM news_inscrit, news_select ";
	$sSql.= "WHERE slc_newsletter=".$id." AND slc_inscrit=ins_id ";
	$sSql.= "ORDER BY ins_mail ASC";

	$aResult = dbGetObjectsFromRequete("news_inscrit", $sSql);

//print("<br>".$sSql);

	return ($aResult);
}


//=====================================================
// liste des envois des newsletter
//=====================================================

function get_list_envois() {

	$sSql = "SELECT newsletter.* FROM newsletter ";
	$sSql.= "ORDER BY news_id DESC";

	$aResult = dbGetObjectsFromRequete("newsletter", $sSql);

//print("<br>".$sSql);

	return ($aResult);
}


//=====================================================
// export des envois des newsletter
//=====================================================

function export_envoi_newsletter($sRep)
{
	// liste des envois des newsletter
	$aResult = get_list_envois();

	// fichier output
		
	// export des enregistrements par lot de 300
	// sinon les requetes ne s'executent pas ...

	// indice des fichiers
	if (newSizeOf($aResult) == 0) $j=0;
	else $j=1;
		
	$sFilename = $sRep."export_envoi_newsletter_".$j.".txt";

	if (!$handle = fopen($sFilename, 'w+')) {
	   echo "Impossible d'ouvrir le fichier ($filename)";
	   exit;
	}
		
	// Assurons nous que le fichier est accessible en écriture
	if (is_writable($sFilename)) {
		// ok tout va bien le fichier est accessible en écriture
	} else {
	   echo "Le fichier $sFilename n'est pas accessible en écriture.";
	}

	// requete d'insertion pour la nouvelle BDD
	for ($i=0; $i<newSizeOf($aResult); $i++)
	{
		$bChangeFile = false;
	
		if ($i%300 == 0 && $i != 0) {
	
			// fichier output
			
			// export des enregistrements par lot de 300
			// sinon les requetes ne s'executent pas ...

			// ferme l'ancien fichier
			if ($i != 0) {
				fclose($handle);
				//print("<br><div class='arbo'>Export réussi :: $sFilename</div>");
			}
	
			$j++;
			$sFilename = $sRep."export_envoi_newsletter_".$j.".txt";

			if (!$handle = fopen($sFilename, 'w+')) {
				   echo "Impossible d'ouvrir le fichier ($sFilename)";
				   exit;
			}
			
			// Assurons nous que le fichier est accessible en écriture
			if (is_writable($sFilename)) {
				// ok tout va bien le fichier est accessible en écriture
			} else {
			   echo "Le fichier $sFilename n'est pas accessible en écriture.";
			}
	
			$bChangeFile = true;
		}
	
		// envoi
		$oEnv = $aResult[$i];

		// newsletter		
		$oNews = new Newsletter($oEnv->getEnv_news_id());
	
		// liste de diffusion
		$oInt = new Interet($oNews->getNews_int_id());

		$sContent = "".$oEnv->getEnv_id().";";
		$sContent.= "".$oNews->getNews_titre().";";
		$sContent.= "".$oInt->getInt_libelle().";";
		$sContent.= "".$oEnv->getEnv_date().";";
		$sContent.= "".$oEnv->getEnv_nbmail().";";
		$sContent.= ""."\n";
	
	   // écriture dans le fichier
	
	   if (fwrite($handle, $sContent) === FALSE) {
		   echo "Impossible d'écrire dans le fichier ($sFilename)";
		   exit;
	   }
	
	}

	// fermeture des fichiers
	fclose($handle);
	//print("<br><div class='arbo'>Export réussi :: $sFilename</div>");

	// on renvoie le nombre de fichiers d'export créés
	return $j;
}


//=====================================================
// export des listes des inscrits envoyés par newsletter
//=====================================================

function export_list_inscrits_envoi($sRep, $id)
{
	

	// liste
	$aResult = get_list_inscrits_envoi($id);
	// fichier output
	
	// export des enregistrements par lot de 300
	// sinon les requetes ne s'executent pas ...

	// indice des fichiers
	if (newSizeOf($aResult) == 0) $j=0;
	else $j=1;
		
	$sFilename = $sRep."export_envoi_newsletter_".$id.".csv";

	if (!$handle = fopen($sFilename, 'a+')) {
	   echo "Impossible d'ouvrir le fichier ($filename)";
	   exit;
	}
		
	// Assurons nous que le fichier est accessible en écriture
	if (is_writable($sFilename)) {
		// ok tout va bien le fichier est accessible en écriture
	} else {
	   echo "Le fichier $sFilename n'est pas accessible en écriture.";
	}
	$sContent ="";
	$sContent.= "Id;";
	$sContent.= "E-mail;";
	$sContent.= "Mail ouvert;";
	$sContent.= "Nom;";
	$sContent.= "Prénom;";
	$$sContent.= "Sexe;";
	$sContent.= "CP;";
	$sContent.= "Ville;";
	$sContent.= "Pays;";
	$sContent.= "Statut;";
	$sContent.= ""."\n";
	// requete d'insertion pour la nouvelle BDD
	for ($i=0; $i<newSizeOf($aResult); $i++)
	{
		
		// envoi
		$oIns = $aResult[$i];
		$sql = "select * from news_select ";
		$sql.= " WHERE slc_newsletter=".$id." AND slc_inscrit=".$oIns->get_id();

		$aNewselect = dbGetObjectsFromRequete("news_select", $sql);
		$oNewselect = $aNewselect[0];
	
		$sContent.= "".$oIns->get_id().";";
		$sContent.= "".$oIns->get_mail().";";
		//$sContent.= "".$oIns->get_recu().";";
		if ($oNewselect->get_recu() == 1) $recu = "oui";
		else if ($oNewselect->get_recu() == 0) $recu = "non";
		$sContent.=$recu.";";
		//$oTheme = new news_theme ($oIns->get_theme());
		//$sContent.= "".$oTheme->get_libelle().";";
		$sContent.= "".$oIns->get_nom().";";
		$sContent.= "".$oIns->get_prenom().";";
		$sContent.= "".$oIns->get_sexe().";";
		$sContent.= "".$oIns->get_codepostal().";";
		$sContent.= "".$oIns->get_ville().";";
		$oPays = new Pays ($oIns->get_pays());
		$sContent.= "".$oPays->get_libelle().";";
		if ($oIns->get_statut() == 1) $statut = "en attente";
		else if ($oIns->get_statut() == 4) $statut = "abonné";
		else if ($oIns->get_statut() == 5) $statut = "desabonné";
		$sContent.="".$statut.";";
		$sContent.= ""."\n";
	
	   // écriture dans le fichier
	
	   
	
	}
	if (fwrite($handle, $sContent) == FALSE) {
		   echo "Impossible d'écrire dans le fichier (".$sFilename.")";
		   exit;
	 }
	// fermeture des fichiers
	fclose($handle);
	//print("<br><div class='arbo'>Export réussi :: $sFilename</div>");
	//echo $sContent;
	// on renvoie le nombre de fichiers d'export créés
	return $j;
}


?>