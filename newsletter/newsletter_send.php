<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

include_once('backoffice/cms/newsletter/functions.lib.php');


/*
  $Id: newsletter_send.php,v 1.28 2014-11-03 15:50:21 pierre Exp $
  $Author: pierre $

  $Log: newsletter_send.php,v $
  Revision 1.28  2014-11-03 15:50:21  pierre
  *** empty log message ***

  Revision 1.27  2013-03-22 13:47:08  raphael
  *** empty log message ***

  Revision 1.26  2013-03-01 10:28:17  pierre
  *** empty log message ***

  Revision 1.25  2012-11-06 16:47:22  pierre
  *** empty log message ***

  Revision 1.24  2012-11-06 16:42:31  pierre
  *** empty log message ***

  Revision 1.23  2012-10-26 11:09:55  pierre
  *** empty log message ***

  Revision 1.22  2012-10-26 07:23:25  pierre
  *** empty log message ***

  Revision 1.21  2012-10-03 10:18:09  thao
  *** empty log message ***

  Revision 1.20  2012-03-28 08:56:45  thao
  *** empty log message ***

  Revision 1.19  2011-07-05 12:36:06  pierre
  *** empty log message ***

  Revision 1.18  2011-04-19 09:44:39  pierre
  *** empty log message ***

  Revision 1.17  2011-02-21 09:10:36  pierre
  *** empty log message ***

  Revision 1.16  2011-02-16 10:39:33  pierre
  *** empty log message ***

  Revision 1.15  2011-01-31 11:20:10  pierre
  *** empty log message ***

  Revision 1.14  2011-01-31 11:13:48  pierre
  usage de la page de lecture de la newsletter pour les destinataires ne supportant pas le rich text

  Revision 1.13  2011-01-19 14:39:05  pierre
  *** empty log message ***

  Revision 1.12  2011-01-11 08:32:38  pierre
  support des caractères e dans l'o et dans l'a

  Revision 1.11  2010-10-22 07:26:57  thao
  *** empty log message ***

  Revision 1.10  2010-10-04 09:02:40  thao
  *** empty log message ***

  Revision 1.9  2010-07-28 10:24:59  thao
  *** empty log message ***

  Revision 1.8  2010-07-28 09:42:44  thao
  *** empty log message ***

  Revision 1.7  2010-07-02 12:52:53  thao
  *** empty log message ***

  Revision 1.6  2010-07-01 14:37:04  thao
  *** empty log message ***

  Revision 1.5  2010-05-11 08:19:17  thao
  *** empty log message ***

  Revision 1.4  2010-05-10 15:42:34  pierre
  *** empty log message ***

  Revision 1.3  2010-05-10 15:40:32  pierre
  *** empty log message ***

  Revision 1.2  2010-01-22 13:13:23  thao
  *** empty log message ***

  Revision 1.1  2010-01-21 10:00:36  thao
  *** empty log message ***

  Revision 1.2  2009-09-15 12:58:57  thao
  *** empty log message ***

  Revision 1.1  2009-09-09 16:06:21  thao
  *** empty log message ***

  Revision 1.2  2009-03-09 08:35:03  thao
  *** empty log message ***

  Revision 1.1  2008-11-27 11:11:34  thao
  *** empty log message ***

  Revision 1.13  2008-09-09 16:31:49  thao
  *** empty log message ***

  Revision 1.12  2008-08-28 08:39:10  thao
  *** empty log message ***

  Revision 1.11  2007/11/29 17:25:20  thao
  *** empty log message ***

  Revision 1.10  2007/11/29 16:13:26  thao
  *** empty log message ***

  Revision 1.7  2007/11/29 15:54:23  pierre
  *** empty log message ***

  Revision 1.6  2007/11/07 09:09:38  thao
  *** empty log message ***

  Revision 1.5  2007/11/06 14:52:40  thao
  *** empty log message ***

  Revision 1.4  2007/10/31 11:06:43  thao
  *** empty log message ***

  Revision 1.3  2007/10/31 08:32:54  thao
  *** empty log message ***

  Revision 1.2  2007/10/30 13:53:21  thao
  *** empty log message ***

  Revision 1.7  2007/10/24 15:57:44  thao
  *** empty log message ***

  Revision 1.6  2007/10/24 13:05:05  thao
  *** empty log message ***

  Revision 1.5  2007/10/23 08:38:35  thao
  *** empty log message ***

  Revision 1.4  2007/10/23 08:07:42  thao
  *** empty log message ***

  Revision 1.3  2007/10/22 10:31:35  thao
  *** empty log message ***

  Revision 1.2  2007/10/22 09:48:23  thao
  *** empty log message ***

  Revision 1.1  2007/10/18 13:07:05  thao
  *** empty log message ***

  Revision 1.1  2007/10/17 12:26:05  thao
  *** empty log message ***

  Revision 1.10  2007/08/20 08:39:37  thao
  *** empty log message ***

  Revision 1.9  2007/08/20 08:31:34  thao
  *** empty log message ***

  Revision 1.8  2007/08/20 08:09:54  thao
  *** empty log message ***

  Revision 1.7  2007/07/25 09:51:16  thao
  *** empty log message ***

  Revision 1.19  2007/07/10 15:14:12  thao
  *** empty log message ***

  Revision 1.17  2007/06/27 15:03:03  thao
  *** empty log message ***

  Revision 1.16  2007/06/15 08:37:51  thao
  *** empty log message ***

  Revision 1.15  2007/06/14 08:45:18  thao
  *** empty log message ***

  Revision 1.14  2007/06/14 07:21:50  thao
  *** empty log message ***

  Revision 1.13  2007/06/13 12:40:03  thao
  *** empty log message ***

  Revision 1.12  2007/06/12 16:00:53  thao
  *** empty log message ***

  Revision 1.11  2007/06/12 15:36:39  thao
  *** empty log message ***

  Revision 1.10  2007/06/12 08:32:21  thao
  *** empty log message ***

  Revision 1.9  2007/06/08 16:36:52  thao
  *** empty log message ***

  Revision 1.8  2007/06/08 16:05:10  thao
  *** empty log message ***

  Revision 1.6  2007/06/08 08:17:11  thao
  *** empty log message ***

  Revision 1.5  2007/05/23 14:38:46  thao
  *** empty log message ***

  Revision 1.4  2007/05/21 15:14:47  thao
  *** empty log message ***

  Revision 1.2  2007/05/11 16:19:00  thao
  *** empty log message ***

  Revision 1.1  2006/12/15 12:30:21  pierre
  *** empty log message ***

  Revision 1.2  2006/06/22 13:44:49  pierre
  *** empty log message ***

  Revision 1.22  2006/01/30 13:14:42  sylvie
  *** empty log message ***

  Revision 1.21  2006/01/27 17:03:05  sylvie
  *** empty log message ***

  Revision 1.20  2005/12/22 07:42:42  sylvie
  *** empty log message ***

  Revision 1.19  2005/12/21 08:22:05  sylvie
  *** empty log message ***

  Revision 1.18  2005/12/20 10:22:22  sylvie
  *** empty log message ***

  Revision 1.17  2005/12/16 16:42:02  sylvie
  *** empty log message ***

  Revision 1.16  2005/12/16 14:34:08  sylvie
  *** empty log message ***

  Revision 1.7  2005/12/15 16:51:24  sylvie
  *** empty log message ***

  Revision 1.6  2005/12/15 14:28:47  sylvie
  *** empty log message ***

  Revision 1.4  2005/11/17 16:09:17  sylvie
  *** empty log message ***

  Revision 1.2  2005/11/02 13:29:03  sylvie
  *** empty log message ***

  Revision 1.1.1.1  2005/10/20 13:10:53  pierre
  Espace V2

  Revision 1.4  2005/09/27 12:50:37  sylvie
  *** empty log message ***

  Revision 1.3  2005/09/22 10:35:03  sylvie
  *** empty log message ***

  Revision 1.2  2005/09/21 07:23:41  sylvie
  envoi avec groupes

  Revision 1.1.1.1  2005/08/16 13:29:56  pierre
  init sisqa d'après espace

  Revision 1.6  2005/07/28 12:42:08  pierre
  finalisation newsletter, insertion auto des puces

  Revision 1.5  2005/07/25 16:49:34  pierre
  modif pour integrer des pucs aux html

  Revision 1.4  2005/07/05 13:12:54  pierre
  correction des liens dans le message

  Revision 1.3  2005/06/17 14:44:09  pierre
  ajout du mode lite qui envoie /documents/newsletter.html

  Revision 1.1.1.1  2005/04/18 13:53:29  pierre
  again

  Revision 1.1.1.1  2005/04/18 09:04:21  pierre
  oremip new

  Revision 1.3  2004/11/15 12:36:17  tofnet
  *** empty log message ***

  Revision 1.2  2004/11/02 16:19:36  tofnet
  Mise à jour pour l'envoi

  Revision 1.1  2004/11/02 13:55:29  tofnet
  Ajout du système d'envoi de la Newsletter

  
*/

/*

function generate_file_HTML($html)
function getHeader()
function getFooter()

*/




include_once($_SERVER['DOCUMENT_ROOT']."/include/cms-inc/mail_lib.php");

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
	else{
		$oNews->set_statut(DEF_ID_STATUT_ARCHI);
	}
	dbUpdate($oNews);
	
	//print("<br><div><strong>Mails envoyés : ".$eMail_envoyes."</strong>");
	$sMessage = "<br>La newsletter a été envoyé à ".$eMail_envoyes." inscrit(s)";
	$sMail_recap = "<br><strong>mails envoyés : ".$eMail_envoyes."</strong><br>";
 			
		
}  
else {

	$sMessage="La newsletter a déjà été envoyé."; 
	
}

?>