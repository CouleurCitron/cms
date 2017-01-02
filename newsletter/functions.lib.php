<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT']."/include/cms-inc/mail_lib.php");
include_once($_SERVER['DOCUMENT_ROOT']."/include/cms-inc/utils/dir.lib.php");

if (!is_file($_SERVER['DOCUMENT_ROOT'].'/frontoffice/newsletter/read_newsletter.php')){
	dirExists($_SERVER['DOCUMENT_ROOT'].'/frontoffice/newsletter');
	copy($_SERVER['DOCUMENT_ROOT'].'/backoffice/cms/newsletter/read_newsletter.php', $_SERVER['DOCUMENT_ROOT'].'/frontoffice/newsletter/read_newsletter.php');
}

function getInscritLang($oInscrit){
	$insIdSite=$oInscrit->get_cms_site();
	
	//$oInsSite=new Cms_site($insIdSite); // à optimiser en cache 
	$oInsSite=cacheObject('Cms_site', $insIdSite);	
	 
	$lang=$oInsSite->get_langue(); 
	if($lang==NULL){ 
		$lang=$_SESSION['id_langue'];
	}
	elseif($lang==-1){ 
		$lang=$_SESSION['id_langue'];
	}
	return $lang;
}

function selectNewsletter($id){
	$oNews = getObjectById("newsletter", $id);
	
	if ($oNews){   
		$themeNews = $oNews->get_theme();
		$oTheme = new news_theme($themeNews);
		
		if (method_exists($oTheme, 'get_abon_criteres')){
			$bUseCriteres = str_replace(array('Y', 'N'), array(1, 0), $oTheme->get_abon_criteres());
		}
		else{
			$bUseCriteres = 0;
		}
		if (method_exists($oTheme, 'get_abon_multiple')){
			$bUseMultiple = str_replace(array('Y', 'N'), array(1, 0), $oTheme->get_abon_multiple());
		}
		else{
			$bUseMultiple = 0;
		}
		
		// newsletter inscrit	
		$sql = " SELECT distinct news_inscrit.* FROM news_inscrit, news_assoinscrittheme";
		$sql.= " WHERE  xit_statut =".DEF_ID_STATUT_LIGNE." ";
		$sql.= " AND xit_news_inscrit = ins_id ";
		$sql.= " AND xit_news_theme = ".$oNews->get_theme()." ORDER BY xit_news_inscrit ASC"; 
		
		//echo $sql;
		
		$aInscrit = dbGetObjectsFromRequete("News_inscrit", $sql);
	 
		// je supprime les lignes qui existent pour cette newsletter pour éviter la confusion
		$sql = "delete from news_select where slc_newsletter=".$id;
		$bRetour = dbExecuteQuery($sql);	
		
		// envoyer
		$_SESSION['exp'] = "select";
		// newsletter à envoyer
		$_SESSION['idal'] = $idal;
		$_SESSION['action']="env";
		
		$cpt = 0; 

		if ($oNews->get_statut() != DEF_ID_STATUT_ARCHI ) {  // SID à décommenter
		//if (true) { 
		
			$eMail_envoyes = 0; 
			
			
			//*****************************   suppression de tous les enregistrements liés à la nl
			if (defined("DEF_APP_USE_CRON") &&  (DEF_APP_USE_CRON)){
				$sql = 'delete from news_queue where news_newsletter = '.$oNews->get_id();
				dbExecuteQuery($sql);
			}
			
			// tous les inscrits pour cette newsletter
			for ($a=0; $a<sizeof($aInscrit); $a++) {
				
				
				
				$oInscrit=$aInscrit[$a];		
				$idIns = $oInscrit->get_id();
				
				//if ($cpt%100 == 0) echo $oInscrit->get_mail().'-<br />';
				if ($cpt%100 == 0) echo '-<br />';
				
				$to = $oInscrit->get_mail();
				$lang=getInscritLang($oInscrit);
				
				
				// function updated 
				$bSend = sendNewsletter($oNews->get_id(), $idIns, $_GET['ldj'], $to, $themeNews, $bUseCriteres, $bUseMultiple, $lang);
		
				if ($bSend)  
					$eMail_envoyes++; 
				//creation de l'objet select 
				/*$oNewsselect = new News_select();
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
				*/
					
				
			} 
			
			
			//*****************************   appel cron
			
			if (defined("DEF_APP_USE_CRON") &&  (DEF_APP_USE_CRON)){
				
				return 
				"La newsletter est en cours d'envoi (".$eMail_envoyes." mail(s)).<br />
				Le délai d'envoi dépend du nombre de mail à envoyer.<br />
				Un mail à l'administrateur lui sera envoyé une fois l'ensemble des mails envoyés.";  
			
			}
			else {
				
				 
				//MAJ newsletter
				$oNews->set_dateenvoi(getDateNow());  
				$oNews->set_nbmail($eMail_envoyes);
				
				// statut
				if (defined('DEF_JOBS_NEWS_THEME_ID') && DEF_JOBS_NEWS_THEME_ID == $themeNews) {
					// pas de change de statut
				}
				else{
					$oNews->set_statut(DEF_ID_STATUT_ARCHI);
				}
				dbUpdate($oNews);
				
				// envoi mail 
				$html = 'http://'.$_SERVER['HTTP_HOST'].'/<br />';
				$html.= 'La newsletter '.$oNews->get_libelle().' a bien été envoyée à '.$eMail_envoyes.' inscrit(s).';
				
				$from = DEF_CONTACT_FROM;
				if (defined("DEF_CONTACT_NO_REPLY")) {
					$replyto = DEF_CONTACT_NO_REPLY;
				}
				else {
					$replyto = DEF_CONTACT_FROM;
				} 
		
				$bSend = (bool)  multiPartMail_file(DEF_CONTACT_TO_ADMIN , $_SERVER['HTTP_HOST'].' : Envoi de la newsletter '.$oNews->get_libelle()." finalisée" , $html , html2text($html), $from, $attachPath, $aName_file, $typeAttach='text/plain', DEF_MAIL_HOST, $replyto);
				
				if (!preg_match('/hephaistos|emulgator/', $_SERVER['HTTP_HOST']) == 1 ) {
					$bSend = (bool)  multiPartMail_file("technique@couleur-citron.com" , $_SERVER['HTTP_HOST'].' : Envoi de la newsletter '.$oNews->get_libelle()." finalisée" , $html , html2text($html), $from, $attachPath, $aName_file, $typeAttach='text/plain', DEF_MAIL_HOST, $replyto); 
				}
		 
				return 
				"La newsletter est en cours d'envoi (".$eMail_envoyes." mail(s)).<br />
				Le délai d'envoi dépend du nombre de mail à envoyer.<br />
				Un mail à l'administrateur lui sera envoyé une fois l'ensemble des mails envoyés.";  
				 
				
			}  
			
				
		}  
		else {
		
			$sMessage="La newsletter a déjà été envoyé."; 
			
		}

		// rapport inclu dans envoi_list.php
		$oNews = new Newsletter($id);
	}
	else{
		echo "La newsletter n'a pas été trouvé";
	}
	 

}

function sendNewsletter ( $id , $idIns, $idLdj, $addy, $themeNews, $bUseCriteres=0, $bUseMultiple=0, $lang=1) {
  	
	

  	if ($_POST["actiontodo"] == "SENDTEST") {
		 
	}
	else {
		if($bUseCriteres==1){
			// si traitement par critères, donc custom, report sur objet metier
			if (defined('DEF_CRITERE_LIB') && is_file(DEF_CRITERE_LIB)) {
				include_once(DEF_CRITERE_LIB);
				
				$_SESSION['id_langue']=$lang;
				$translator =& TslManager::getInstance();			
				
				$oCriNlter = new critereNewsletter();
				
				$oCriNlter->bUseCriteres=$bUseCriteres;
				$oCriNlter->lang=$lang;
				$oCriNlter->eIns=$idIns;
				$oCriNlter->eNews=$id;
				$oCriNlter->theme=$themeNews;
				
				if (method_exists($oCriNlter, 'sendNewsletterOrNot')){
					$bSend = $oCriNlter->sendNewsletterOrNot();
					if ($bSend==false){
						return false;
					}
				}
			}
		}
 	}
	
	$oNews = new Newsletter ($id);
	$theme = $oNews->get_theme(); 
	$sSubject = rewriteNewsletterSubject($oNews->get_libelle(), $bUseCriteres, $lang);
	
	if ($sSubject == false){ // sortie si pas de newsletter dans cette lang pour cet user // sujet vide
		return false;
	}
	
	$sBodyHTML = $oNews->get_html();			
	$sBodyHTML = rewriteNewsletterBody($sBodyHTML, $idIns, $id, $theme, $bUseCriteres, $bUseMultiple, $lang, $sSubject);

	if($sBodyHTML!=false){	 // sortie si pas de newsletter dans cette lang pour cet user // body vide
		 
		$sBodyTEXT = "Si vous n'arrivez pas à lire cet email, copiez-coller ce lien :\n";
		$sBodyTEXT.= "http://".$_SERVER['HTTP_HOST']."/frontoffice/newsletter/read_newsletter.php?idnew=".$oNews->get_id()."&idInslien=1&ins=".md5($idIns);	
		
		$sBodyTEXT.= strip_tags($sBodyHTML);
		
		
		$from = getNoReply ($id);
		$replyto = getReplyTo ($id);
		
		// FIN ----------------------------------------------------------------------------------------------------------------
		$limite = "_----------=_parties_".md5(uniqid (rand()));
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type:  multipart/alternative; charset=iso-8859-1 ;boundary="'.$limite.'"' . "\r\n"; 
		$headers .= 'From: '.$from.'' . "\r\n";
		 
		//-----------------------------------------------
		//MESSAGE TEXTE
		//----------------------------------------------- 
		$message_text=  $sBodyTEXT;		
	
		//-----------------------------------------------
		//MESSAGE HTML
		//----------------------------------------------- 
		$message_html = $sBodyHTML;			
		
		//-----------------------------------------------
		//MESSAGE TEXTE
		//-----------------------------------------------
		$message = "";
		$message .= "--".$limite."\n";
		$message .= "Content-Type: text/plain\n";
		$message .= "charset=\"iso-8859-1\"\n";
		$message .= "Content-Transfer-Encoding: 8bit\n\n";
		$message .= $message_text."\n\n";
	
		//-----------------------------------------------
		//MESSAGE HTML
		//-----------------------------------------------
		$message .= "\n\n--".$limite."\n";
		$message .= "Content-Type: text/html; ";
		$message .= "charset=\"iso-8859-1\"; ";
		$message .= "Content-Transfer-Encoding: 8bit;\n\n";
		$message .= $message_html."\n\n";			
		
		$message .= "\n--".$limite."--";	
		
		$aName_file = array();		
		$attachPath = $_SERVER['DOCUMENT_ROOT'].'/custom/upload/cms_pdf/'; // dossier où sera déplacé le fichier
		$aName_file = getAttachedFile($id);
		
		
		/*
		mise en queue 
		
		*/
		
		//suppression de tous les enregistrements liés à la nl
		/*$sql = 'delete * from news_queue where news_newsletter = '.$id;
		dbExecuteQuery($sql);*/
		
		//$oNews->get_test() == 1 ) 
		
		if (defined("DEF_APP_USE_CRON") &&  (DEF_APP_USE_CRON)){
		
			if ($_POST['actiontodo'] != "SENDTEST") {
		
				$oQu = new News_queue();
				$oQu->set_inscrit($idIns);
				$oQu->set_newsletter($id); 
				$oQu->set_to($addy);
				$oQu->set_from($from);
				$oQu->set_replyto($replyto);
				$oQu->set_subject($sSubject);
				$oQu->set_files(serialize($aName_file));
				$oQu->set_headers($headers); 
				$oQu->set_html($message_html); 
				$oQu->set_date_send('0000-00-00 00:00:00'); 
				$oQu->set_statut(DEF_ID_STATUT_ATTEN);
				
				$b = dbInsertWithAutoKey($oQu); 
				 
				$bSend = true;
			}
			else {
				echo "&nbsp;";
				if (defined("DEF_USEPHPMAILFUNCTION") && (strval(DEF_USEPHPMAILFUNCTION)=="1")){
					$bSend = (bool)  mail($addy, $sSubject, $message, $headers);
				}
				else{					
					if (count($aName_file)>0){ // si on a des fichiers
						$bSend = (bool)  multiPartMail_file($addy , $sSubject , $message_html , $message_text, $from, $attachPath, $aName_file, $typeAttach='text/plain', DEF_MAIL_HOST, $replyto);
					}
					else{
						$bSend = (bool)  multiPartMail($addy , $sSubject , $message_html , $message_text, $from, "", "", DEF_MAIL_HOST, $replyto);
					}						
				} 
			
			}
		
		} 
		else {  
		 	echo "&nbsp;";
			if (defined("DEF_USEPHPMAILFUNCTION") && (strval(DEF_USEPHPMAILFUNCTION)=="1")){
				$bSend = (bool)  mail($addy, $sSubject, $message, $headers);
			}
			else{
				if (count($aName_file)>0){ // si on a des fichiers
					$bSend = (bool)  multiPartMail_file($addy , $sSubject , $message_html , $message_text, $from, $attachPath, $aName_file, $typeAttach='text/plain', DEF_MAIL_HOST, $replyto);
				}
				else{
					$bSend = (bool)  multiPartMail($addy , $sSubject , $message_html , $message_text, $from, "", "", DEF_MAIL_HOST, $replyto);
				}					
			} 
		}
		//$bSend = (bool)  multiPartMail_file('thao@couleur-citron.com' , 'test'.$sSubject , $addy.$lang.$message_html , $message_text, $from, $attachPath, $aName_file, $typeAttach='text/plain', DEF_MAIL_HOST, $replyto);
		
		
		return $bSend;
	}
	else{
		return false;
	}	
}	


function getExpediteur( $id ) {

	$oNews = new Newsletter ($id);
	if ($oNews->get_theme() == DEF_ID_LDJ) {
		$oExp = new News_expediteur(DEF_ID_EXPEDITEUR_LDJ); 
		//$oLDJ = new Liendujour($idLdj); 
		//$from = "\"".$oLDJ->get_contactnom()."\" <".$oLDJ->get_contactmail().">";		
		$from = "\"".$oExp->get_prenom()." ".$oExp->get_nom()."\" <".$oExp->get_mail().">";
	}
	else if ($oNews->get_expediteur() != "") {
		$oExp = new News_expediteur($oNews->get_expediteur()); 
		if ($oExp->get_prenom()!= "" || $oExp->get_nom()!= "") {
			if (preg_match('/^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]+$/msi', $oExp->get_prenom() )) {
				$from = $oExp->get_prenom();
			}
			elseif (preg_match('/^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]+$/msi', $oExp->get_nom() )) {
				$from = $oExp->get_nom();
			}
			else {
				$from = "\"".trim($oExp->get_prenom())." ".trim($oExp->get_nom())."\" <".$oExp->get_mail().">";
			}
		}
		else {
			$from =  "".$oExp->get_mail()."";
		}
	}
	else {
		$from = DEF_CONTACT_FROM;
	}
	 
	 
	
	 
	
	return $from;
	
}	


function getNoReply ( $id ) {

	if (defined("DEF_CONTACT_NO_REPLY")) {
		$from = DEF_CONTACT_NO_REPLY;
	}
	else { 
		$from = getExpediteur ( $id ) ; 
	} 
	return $from;
	
}


function getUserConnected () {

	if (isset($_SESSION["userid"]) &&  $_SESSION["userid"]!=-1) {
		$user = new Bo_users ($_SESSION["userid"]);
		$user = $user->get_mail();
	}
	else {
		$user = false;
	}
	return $user;
	
}

function getReplyTo ( $id ) { 
	if ( $id != 0 ) {
		if (defined("DEF_USE_REPLYTO_BO_USER_CONNECTED") && DEF_USE_REPLYTO_BO_USER_CONNECTED) {
			$replyto = getUserConnected(); 
			if (!$replyto) {
				$replyto = getExpediteur( $id );
			} 
		}
		else {
			$replyto = getExpediteur( $id );
		}
	}
	else {
	
		$replyto = '';
	
	}
	return $replyto;
	
}
 
function getAttachedFile ( $id ) { 
	
	$myAttachedFile = array();
	
	$sql = "select cms_pdf.* 
			from cms_pdf, news_assonewspdf 
			where nws_cms_pdf = pdf_id 
			and nws_newsletter = ".$id." 
			and pdf_statut = ".DEF_ID_STATUT_LIGNE." 
			and pdf_cms_site =".$_SESSION["idSite"]." ";
	
	//echo $sql;
			
	$aAttachedFile = dbGetObjectsFromRequete("cms_pdf", $sql);

	
	if (($aAttachedFile!==false)	&&	(sizeof($aAttachedFile) > 0)) {
		foreach ($aAttachedFile as $oAttachedFile) {
			$attachedFile = $oAttachedFile->get_src ();
			if (is_file($_SERVER['DOCUMENT_ROOT'].'/custom/upload/cms_pdf/'.$attachedFile)){					
				array_push ( $myAttachedFile, $attachedFile) ;
			}
		}
	}
	
	return $myAttachedFile;
	
}

function selectNewsletterByCron($id_cron){

	$aObj = dbGetObjectsFromFieldValue2('news_assonewscron', array('get_cms_cron', 'get_statut'), array($id_cron, DEF_ID_STATUT_LIGNE), array(), array());
	
	if (sizeof($aObj) > 0 && $aObj != false) {
		return $aObj;
	}
	else {
		return false;
	} 
}

 
?>