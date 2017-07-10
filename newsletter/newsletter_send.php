<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/backoffice/cms/newsletter/functions.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/mail_lib.php');

if (!is_file($_SERVER['DOCUMENT_ROOT'].'/frontoffice/newsletter/read_newsletter.php')){
	copy('read_newsletter.php', $_SERVER['DOCUMENT_ROOT'].'/frontoffice/newsletter/read_newsletter.php');
}

$cpt = 0; 
 
if ($oNews->get_statut() != DEF_ID_STATUT_ARCHI ) {  // SID à décommenter
//if (true) { 

	$eMail_envoyes = 0; 
	
	if (defined('DEF_CRITERE_LIB') && is_file(DEF_CRITERE_LIB) && $bUseCriteres){
		
		$oCriNlter = new critereNewsletter();
		$oCriNlter->bUseCriteres=$bUseCriteres;
		$oCriNlter->eNews=$oNews->get_id();
		$oCriNlter->theme=$oNews->get_theme();
		
		if (method_exists($oCriNlter, 'preProcess')){
			$oCriNlter->preProcess();
		}
	}
	
	// tous les inscrits pour cette newsletter
	for ($a=0; $a<count($aInscrit); $a++) {
		
		if ($cpt%100 == 0) echo '&nbsp;<br />';
		
		$oInscrit=$aInscrit[$a];		
		$idIns = $oInscrit->get_id();
		$to = $oInscrit->get_mail();
		$insIdSite=$oInscrit->get_cms_site();
		$oInsSite=new Cms_site($insIdSite); // à optimiser en cache
		$lang=$oInsSite->get_langue();
		if($lang==NULL	||	$lang=-1){
			$lang=$_SESSION['id_langue'];
		}		
		
		$bSend = sendNewsletter($oNews->get_id(), $idIns, $_GET['ldj'], $to, $themeNews, $bUseCriteres, $bUseMultiple, $lang, $oCriNlter);

		//creation de l'objet select 
		$oNewsselect = new News_select();
		$oNewsselect->set_newsletter($id);
		$oNewsselect->set_inscrit($oInscrit->get_id());
		$oNewsselect->set_datecrea(getDateNow());
		$oNewsselect->set_recu(0); 
		if ($bSend) {
			$eMail_envoyes++; 
			$oInscrit->set_recu(1);
			$oInscrit->set_dt_recu(getDateNow()); 
			$bR = dbUpdate($oInscrit);
			
			$oNewsselect->set_dateenvoi(getDateNow());
			$oNewsselect->set_statut(DEF_ID_STATUT_LIGNE);
		}
		else {
			$oNewsselect->set_dateenvoi(""); 
			$oNewsselect->set_statut(DEF_ID_STATUT_NEWS_ERREUR);
		} 
		$bRetour = dbInsertWithAutoKey($oNewsselect);		
	} 
		
	//MAJ newsletter
	$oNews->set_dateenvoi(getDateNow());  
	$oNews->set_nbmail($eMail_envoyes);
	
	// statut
	if (defined('DEF_JOBS_NEWS_THEME_ID') && DEF_JOBS_NEWS_THEME_ID == $themeNews) {
		// pas de change de statut
	}
	elseif(defined('DEF_ID_STATUT_ENCOURS')	&&	defined("DEF_APP_USE_CRON") &&  (DEF_APP_USE_CRON)){
		$oNews->set_statut(DEF_ID_STATUT_ENCOURS);
	}
	else{
		$oNews->set_statut(DEF_ID_STATUT_ARCHI);
	}
	dbUpdate($oNews);
	
	$sMessage = "<br>La newsletter a été envoyé à ".$eMail_envoyes." inscrit(s)";
	$sMail_recap = "<br><strong>mails envoyés : ".$eMail_envoyes."</strong><br>";		
}  
else {

	$sMessage="La newsletter a déjà été envoyé."; 
	
}
?>