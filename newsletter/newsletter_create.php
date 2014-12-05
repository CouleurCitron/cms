<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
 
include_once('newsletter_export.php');
include_once('functions.lib.php'); 

ini_set ('max_execution_time', 0); // Aucune limite d'execution
ini_set("memory_limit","-1");

// XML autoclass init
$oNews = new newsletter();
   
if(!is_null($oNews->XML_inherited))
	$sXML = $oNews->XML_inherited;
else
	$sXML = $oNews->XML;
	
xmlClassParse($sXML);

$classeName = $stack[0]["attrs"]["NAME"];

if (isset($stack[0]["attrs"]["LIBELLE"]) && ($stack[0]["attrs"]["LIBELLE"] != "")){
	$classeLibelle = stripslashes($stack[0]["attrs"]["LIBELLE"]);
}
else{
	$classeLibelle = $classeName;
}

$classePrefixe = $stack[0]["attrs"]["PREFIX"];
$aNodeToSort = $stack[0]["children"];

unset($oNews);

// translator

$translator =& TslManager::getInstance();
if (DEF_EDIT_INACTIVE_LANG)
	$langpile = $translator->getLanguages();
else	$langpile = $translator->getLanguages(true);

/////-----------------------------

activateMenu('gestiondesnewsletter');  //permet de dérouler le menu contextuellement

if (!is_file($_SERVER['DOCUMENT_ROOT'].'/frontoffice/newsletter/read_newsletter.php')){
	copy('read_newsletter.php', $_SERVER['DOCUMENT_ROOT'].'/frontoffice/newsletter/read_newsletter.php');
}

if (is_as_get("ldj")) {
	$idLDJ = $_GET["ldj"];
	$param = "ldj=".$idLDJ;
}
if (is_as_get("display")) {
	$id = $_GET["display"];
	$param = "display=".$id;
}
ini_set ("memory_limit", '256M'); 
?>
<script type="text/javascript">
	function isEmail(sEmail){
		if ((sEmail.indexOf('@',0)==-1) || (sEmail.indexOf('.',0)==-1)) return false;
		else return true;
	}
	
	function doSend()
	{
		document.frm_grp_theme.actiontodo.value = "SEND"; 
		<?php		
		echo 'document.frm_grp_theme.action = "'.$_SERVER['PHP_SELF'].'?'.$param.'"'.";\n";
		?>
		document.frm_grp_theme.submit();
	}
	
	// envoi un test
	function doSendTest()
	{
		erreur=0;
		lib="";
		if(document.frm_grp_theme.mailtest.value==""){
			erreur++;
			lib+="Vous n'avez pas saisi d'email de test.\n";
		}
		
		if(erreur>0){
			alert(lib);
		}
		else {
			document.frm_grp_theme.actiontodo.value = "SENDTEST";
			//document.frm_grp_theme.action = "newsletter_create.php?<?php echo $param; ?>";
			<?php		
			echo 'document.frm_grp_theme.action = "'.$_SERVER['PHP_SELF'].'?'.$param.'"'.";\n";
			?>
			document.frm_grp_theme.submit();
		}
	}

	// retour à la page précédente
	function retour()
	{
		<?php
		if (is_post('urlRetour')){
			echo 'document.add_news_form.action = "'.$_POST['urlRetour'].'"'.";\n";
		}
		else{
			echo 'document.add_news_form.action = "list_newsletter.php"'.";\n";
		}
		?>
		document.add_news_form.submit(); 
	}
</script>
<?php
// on regarde s'il s'agit du LDJ
if (isset($_GET['ldj']) && $_GET['ldj']!="") {
	$idLDJ = $_GET['ldj'];
	$oLDJ = new Liendujour ($idLDJ);	 
	 
	if ($oLDJ->get_newsletter() == -1) { 
		// on récupère le contenu
		$httpLDJ = 'http://'.$_SERVER['HTTP_HOST'].'/frontoffice/liendujour/foshow_liendujour.mail.php?id='.$idLDJ;
		//error_log($httpLDJ);
		$context = stream_context_create(array('http' => array('header'=>'Connection: close'))); 
		$htmlLDJ = file_get_contents($httpLDJ);
		// on crée une news
		$oNews = new Newsletter();
		$oNews->set_datecrea(getDateNow());
		$oNews->set_dateenvoi(getDateNow());
		$oNews->set_theme(DEF_ID_LDJ);
		$oNews->set_libelle($oLDJ->get_titre());
		$oNews->set_expediteur(DEF_ID_EXPEDITEUR_LDJ);
		if ($htmlLDJ != "") $oNews->set_html($htmlLDJ);
		$oNews->set_statut(DEF_ID_STATUT_NEWS_CREA);
		$id = dbInsertWithAutoKey($oNews);		
	}
	else {
		$id = $oLDJ->get_newsletter(); 		
	} 
	$oLDJ->set_newsletter($id); 
	dbUpdate($oLDJ); 
	
	$oNews = new newsletter($id);
	if ($oNews->get_theme() == DEF_ID_LDJ) {
	
		$bodyParrain = "Je%20me%20suis%20inscrit%20au%20lien%20du%20jour%20!\n
	%20Tous%20les%20jours%20un%20lien%20dans%20ma%20boite%20mail%20pour%20découvrir%20un%20site%20aux%20qualités%20graphiques,%20ergonomiques%20ou%20techniques%20remarquables...\n
	On%20peut%20s'inscrire%20là%20: \nhttp://www.liendujour.net/";	
	
		// en cas de modif, on récupère le contenu à nouveau
		$fp = fopen("http://".$_SERVER['HTTP_HOST']."/frontoffice/liendujour/foshow_liendujour.mail.php?id=".$idLDJ."","r"); //lecture du fichier
		$htmlLDJ= "";
		if ($fp){
			while (!feof($fp)) { //on parcourt toutes les lignes
			  $htmlLDJ.= fgets($fp, 4096); // lecture du contenu de la ligne
			}
		}
		//Appel page de traitement 
		include_once('frontoffice/liendujour/traitement_liendujour.php');

		$htmlLDJ = str_replace("XX-FOND-XX", $nameImg, $htmlLDJ);
		$htmlLDJ = str_replace("XX-SLOGAN-XX", $data, $htmlLDJ);
		$htmlLDJ = str_replace("XX-CONTACT-BODY-XX", $bodyParrain, $htmlLDJ);
		$oNews->set_html($htmlLDJ);
		$bRetour = dbUpdate($oNews);
		$oLDJ = new LienduJour ($idLDJ);
	}
}// FIN LIEN DU JOUR ----------------------------------------------------------------------------------------------------------------
else {
	// newsletter
	$id = $_GET['display'];
	if ($id == "") {
		$id = $_POST['id'];
	}
	$oNews = new newsletter($id);
} 

// envoi
if ($_POST['actiontodo'] == "SEND") {
	// $idNews = $_POST['display']id;
	// vider la table des contacts à envoyer
	//dbDeleteAll("News_select");

	// traite liste saisie	
	preg_match_all('/[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]+/msi', $_POST['maillist'], $addies );
	$addies=$addies[0];
	
	$messageAdd = "<br>";
	foreach ($addies as $key => $addy) {
		$addy = trim(strtolower($addy));
		if (preg_match('/^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]+$/', $addy)==1){	
			include('newsletter_create.inc.php');
		} // fin if adresse valide
	}// fin boucle adresses		

	// envoyer
	$_SESSION['exp'] = "select";
	// newsletter à envoyer
	$_SESSION['news'] = $id;
	 
	//include_once('newsletter_select.php');
	$retour = selectNewsletter($id);
	if ($retour != '') $messageAdd = $retour; 
	$sRep = $_SERVER['DOCUMENT_ROOT']."/custom/newsletter/";
	//$eFile = export_list_inscrits_envoi($sRep, $id);
	 
?>
	<form name="add_news_form" method="post" enctype="multipart/form-data">
<?php
	echo "<span class=\"arbo2\">NEWSLETTER >&nbsp;</span><span class=\"arbo3\">Envoyer une newsletter</span><br><br><span class=\"arbo\">".$sMessage."</span>";
	// rapport inclu dans envoi_list.php
	echo '<p>'.$messageAdd.'</p>';
	echo '<br><br><input class="arbo" type="button" value="<< Retour à la liste des newsletter" onclick="retour()">';
?>
	</form>
<?php
}
elseif ($_POST['actiontodo'] == "SENDTEST") {
	
	preg_match_all('/[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]+/msi', $_POST['mailtest'], $addies );
	$addies=$addies[0];
	$oNews = new Newsletter($id);	
	$from = getExpediteur($id);
	
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
	if (method_exists($oTheme, 'get_allow_edit')){
		$bAllowEdit = str_replace(array('Y', 'N'), array(1, 0), $oTheme->get_allow_edit());
	}
	else{
		$bAllowEdit = 0;
	}
	
	if (defined('DEF_CRITERE_LIB') && is_file(DEF_CRITERE_LIB) && $bUseCriteres){
		include_once(DEF_CRITERE_LIB);
		$oCriNlter = new critereNewsletter();			
		
		$oCriNlter->bUseCriteres=$bUseCriteres;
		$oCriNlter->eNews=$oNews->get_id();
		$oCriNlter->theme=$oNews->get_theme();
		
		if (method_exists($oCriNlter, 'preProcess')){
			$oCriNlter->preProcess();
		}
	}
	
	$sMessage = 'E-mail envoyé aux adresses (expéditeur : '.htmlentities($from).') :<br />';  
	
	if (isset($addies)&&(count($addies)>0)){	
		foreach($addies as $k => $addy){		
			include('newsletter_create.inc.php');			
			
			if(isset($oIns)){
				$idIns = $oIns->get_id();
				$lang=getInscritLang($oIns);
			}
			else{
				$idIns = 0;
				$lang=1;
			}			
			
			//$bSend = sendNewsletter ($id, $idIns, $_GET["ldj"], $addy);
			$bSend = sendNewsletter ($id, $idIns, $_GET["ldj"], $addy, $themeNews, $bUseCriteres, $bUseMultiple, $lang, $oCriNlter);
			
			if ($bSend == true){
				$sMessage.= $addy.'<br />';
			}
		}// fin boucle sur adresses
		
		//echo $sBodyHTML;
		
		$oNews->set_test(1);
		$bRetour = dbUpdate($oNews);	
	} // fin test if adresses
	else{
		echo '<p>erreur : veuillez saisir des adresses pour cet envoi. <br /><br /><a href="javascript:history.back();">retour</a></p>';
	}
?>
	<form name="add_news_form" method="post" enctype="multipart/form-data">
<?php
	echo "<span class=\"arbo2\">NEWSLETTER >&nbsp;</span><span class=\"arbo3\">Envoyer une newsletter</span><br><br><span class=\"arbo\">".$sMessage."</span>";
	
	if (trim($messageAdd)!=''){
		echo "<span class=\"arbo\"><br><br>".$messageAdd."</span>";
	}
	// rapport inclu dans envoi_list.php
	
	echo '<br><br><input class="arbo" type="button" value="<< Retour à la liste des newsletter" onclick="retour()" />';
?>
	</form>
<?php
}
else{
?>
	<span class="arbo2">NEWSLETTER >&nbsp;</span><span class="arbo3">Envoyer une newsletter</span><br><br>
	<?php
	// vérifie s'il s'agit d'un lien du jour
	if ( is_file ($_SERVER['DOCUMENT_ROOT']."/include/bo/class/liendujour.class.php" )) {
		
		$aLDJ = dbGetObjectsFromFieldValue('liendujour', array('get_statut', 'get_newsletter'),  array(DEF_ID_STATUT_LIGNE, $id), NULL); 
		if (sizeof($aLDJ) > 0) $oLDJ = $aLDJ[0];
	}	
	 
	if ($oNews->get_statut() == DEF_CODE_STATUT_DEFAUT) { ?>
		<form name="add_news_form" method="post" enctype="multipart/form-data">
		<table class="arbo" cellpadding="5" cellspacing="0" width="700">
		<tr><td>
		<?php	echo "<br /><br>La newsletter n'a pas été validé.<br><br>"; ?>
		</td></tr>	
		<tr><td align="center">
		<?php
		if ($_POST['urlRetour'] != "") {
			echo '<input class="arbo" type="button" value="<< Retour à la liste des newsletter" onclick="retour()">';
		}
		?>
		</td></tr></table></form>
	<?php
	}
	elseif ($oNews->get_statut() == DEF_ID_STATUT_NEWS_ENVOI) { 
	?>
		<form name="add_news_form" method="post" enctype="multipart/form-data">
		<table class="arbo" cellpadding="5" cellspacing="0" width="700">
		<tr><td>
		<?php	echo "<br /><br>La newsletter a déjà été envoyé. Vous pouvez modifier son statut pour le renvoyer à nouveau.<br><br>"; ?>
		</td></tr>	
		<tr><td align="center">
		<?php
		if ($_POST['urlRetour'] != "") {
			echo '<input class="arbo" type="button" value="<< Retour à la liste des newsletter" onclick="retour()">';
		}
		?>
		</td></tr></table></form>
	<?php
	}
	elseif (($oNews->get_theme() == DEF_ID_LDJ)  && ($oLDJ->get_statut() != DEF_ID_STATUT_LIGNE)) {
		echo '<form name="add_news_form" method="post" enctype="multipart/form-data">
		<table class="arbo" cellpadding="5" cellspacing="0" width="700">
		<tr><td>';
		echo "<br><br>Le Lien du jour <b>".$oLDJ->get_lien()."</b> n'est pas validé. Vous devez modifier son statut pour pouvoir l'envoyer.<br><br>"; 
		echo '</td></tr>	
		<tr><td align="center">';	 
		if ($_POST['urlRetour'] != ""){
			echo '<input class="arbo" type="button" value="<< Retour à la liste des newsletter" onclick="retour()">';
		}
		echo '</td></tr></table></form>';  
	}
	else {  // la news est validée
	?>
	<form name="frm_grp_theme" method="post">
	<input type="hidden" name="actiontodo" id="actiontodo">
	<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
	<input type="hidden" name="typnews" id="typnews" value="4">
	<table class="arbo" cellpadding="5" cellspacing="0" >
		<tr>
			<td colspan="2">Vous êtes sur le point d'envoyer la newsletter suivante : </td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Titre&nbsp;&nbsp;</td>
			<td><strong><?php

foreach ($aNodeToSort as $key => $node){
	if ($node["attrs"]["NAME"] == 'libelle'){
		$i = $key;
		break;
	}
}


if (DEF_APP_USE_TRANSLATIONS && $aNodeToSort[$i]["attrs"]["TRANSLATE"]) { // cas d'une traduction
	echo $translator->getByID($oNews->get_libelle());

}
else{
	echo $oNews->get_libelle();
}

	
	 
	  ?></strong>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Date&nbsp;&nbsp;</td>
			<td><?php
			$sDate = $oNews->get_dateenvoi();
			if (isDdateVide($sDate)){ $sDate = getDateNow();}
			echo $sDate;
			?>&nbsp;</td>
		</tr>
		<tr>
		  <td align="right">Nombre d'inscrits&nbsp;&nbsp;</td>
		  <td><?php
			
			$sqlCount = " SELECT count(*) FROM news_inscrit, news_assoinscrittheme";
			$sqlCount.= " WHERE  xit_statut =".DEF_ID_STATUT_LIGNE." ";
			$sqlCount.= " AND xit_news_inscrit = ins_id ";
			$sqlCount.= " AND xit_news_theme = ".$oNews->get_theme()." ";
			$eCountInscrit = dbGetUniqueValueFromRequete($sqlCount);	
			
			
			$sql = " SELECT news_inscrit.* FROM news_inscrit, news_assoinscrittheme";
			$sql.= " WHERE  xit_statut =".DEF_ID_STATUT_LIGNE." ";
			$sql.= " AND xit_news_inscrit = ins_id ";
			$sql.= " AND xit_news_theme = ".$oNews->get_theme()." "; 	
			$aReq = dbGetObjectsFromRequete('news_inscrit', $sql); 
			
			$sql = ' DELETE FROM news_assoinscritnewsletter WHERE Xin_newsletter = '.$oNews->get_id().';';
			dbExecuteQueryQuiet($sql);
			
			if (defined('DEF_CRITERE_LIB') && is_file(DEF_CRITERE_LIB)) {
				
				include_once(DEF_CRITERE_LIB);
				
				$_SESSION['id_langue']=$lang;
				$translator =& TslManager::getInstance();	
				
				$e = 0;
				
				$id = $oNews->get_id();
				$bUseCriteres = false;					
				$oCriNlter = new critereNewsletter();
				$oCriNlter->bUseCriteres=$bUseCriteres;
				$oCriNlter->eNews=$id;
				$oCriNlter->theme=$themeNews;
				
				if (method_exists($oCriNlter, 'preProcess')){
					$oCriNlter->preProcess();
				}
						
				foreach ($aReq as $oInscrit) {
					$idIns = $oInscrit->get_id();
					
					$themeNews = $oNews->get_theme();
					$lang = getInscritLang($oInscrit);
					$oCriNlter->lang=$lang;
					$oCriNlter->eIns=$idIns;					
					
					if (method_exists($oCriNlter, 'sendNewsletterOrNot')){
						$bSend = $oCriNlter->sendNewsletterOrNot();
						if ($bSend){
							
							$oX = new news_assoinscritnewsletter();
							$oX->set_news_inscrit($idIns);
							$oX->set_newsletter($oNews->get_id());
							dbSauve($oX);
							
							$e++;
						}
					}
				}
				echo $e." inscrit(s) - sur critères d'association (".$eCountInscrit." inscrits au total)";
				
				//echo $eCountInscrit." inscrit(s) au total - le nombre effectif peut être inférieur à ce total selon les critères d'association";
				
			}
		else { 
			foreach ($aReq as $oInscrit) {
				$idIns = $oInscrit->get_id();
				
				$oX = new news_assoinscritnewsletter();
				$oX->set_news_inscrit($idIns);
				$oX->set_newsletter($oNews->get_id());
				dbSauve($oX);
			}
			echo $eCountInscrit." inscrit(s)";
		}
			
			?></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2" bgcolor="#FFFFFF">
			<table cellpadding="0" cellspacing="0" border="0"><tr><td>		
			<?php
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
			
			if ($bUseCriteres == 1){
				$sBodyHTML = rewriteNewsletterBody($oNews->get_html(), 0, $id, $themeNews, $bUseCriteres, $bUseMultiple, $_SESSION['id_langue'], $oNews->get_libelle(), $oCriNlter);
			}
			else{
				$sBodyHTML = $oNews->get_html();
			}
			echo $sBodyHTML;

			?>
		</td></tr></table>	</tr>
		<tr>
			<td colspan="2">&nbsp; </td>
		</tr>
		<tr height="30px">
			<td align="center" ><textarea  type="textarea" name="mailtest" id="mailtest"  cols="50"  rows="20" class="arbo"></textarea>
			<br />		</td>
			<td height="30px">
			<input type="hidden" value="<?php echo $_POST['urlRetour']; ?>" id="urlRetour" name="urlRetour" />
			<input type="button" value="Faire un envoi one-shot, ou envoi de test" class="arbo" onClick="javascript:doSendTest()">		</td>
		</tr>
		<tr height="30px">
		  <td height="30px" colspan="2" align="center" ><hr height="1"  /></td>
		</tr>
		<?php if ($oNews->get_test() == 1) {?>
		<tr>
			<td align="center">&nbsp;
			<textarea type="textarea" name="maillist" id="maillist"  cols="50"  rows="20" class="arbo"  ></textarea></td>
			<td align="left">	
			<input type="hidden" value="<?php echo $_POST['urlRetour']; ?>" id="urlRetour" name="urlRetour" />	
			<input type="button" value="Envoyer la newsletter (en y incorporant les adresses ci-contre)" class="arbo" onClick="javascript:doSend()">		</td>
		</tr>
		<?php } ?>
	</table>
	</form>
	<?php
	} //
}
?>