<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
// CVS
/*
$Id: surveyList.php,v 1.10 2013-03-01 10:28:20 pierre Exp $
$Author: pierre $
$Log: surveyList.php,v $
Revision 1.10  2013-03-01 10:28:20  pierre
*** empty log message ***

Revision 1.9  2012-07-31 14:24:50  pierre
*** empty log message ***

Revision 1.8  2010-10-18 08:02:29  pierre
prise en comptes des traductions
possibilité de vider la brique

Revision 1.7  2010-10-07 14:49:53  thao
*** empty log message ***

Revision 1.6  2010-08-09 10:24:30  thao
*** empty log message ***

Revision 1.5  2010-06-24 15:33:40  luc
no message

Revision 1.4  2010-06-24 15:05:53  luc
no message

Revision 1.3  2009-09-24 08:53:14  pierre
*** empty log message ***

Revision 1.2  2008-07-16 10:48:36  pierre
*** empty log message ***

Revision 1.1  2008/04/30 15:01:49  thao
*** empty log message ***

Revision 1.4  2007/11/15 18:08:49  pierre
*** empty log message ***

Revision 1.5  2007/11/06 15:33:25  remy
*** empty log message ***

Revision 1.3  2007/08/28 15:55:28  pierre
*** empty log message ***

Revision 1.2  2007/08/27 10:13:47  pierre
*** empty log message ***

Revision 1.1  2007/08/08 14:26:20  thao
*** empty log message ***

Revision 1.3  2007/08/08 14:16:14  thao
*** empty log message ***

Revision 1.2  2007/08/08 13:56:04  thao
*** empty log message ***

Revision 1.1.1.1  2006/01/25 15:14:32  pierre
projet CCitron AWS 2006 Nouveau Website

Revision 1.3  2005/12/20 15:11:50  sylvie
*** empty log message ***

Revision 1.2  2005/10/28 07:53:14  sylvie
*** empty log message ***

Revision 1.1.1.1  2005/10/20 13:10:53  pierre
Espace V2

Revision 1.1.1.1  2005/04/18 13:53:29  pierre
again

Revision 1.1.1.1  2005/04/18 09:04:21  pierre
oremip new

Revision 1.1.1.1  2004/11/03 13:49:54  ddinside
lancement du projet - import de adequat

Revision 1.3  2004/06/16 15:23:19  ddinside
inclusion corrections

Revision 1.2  2004/04/26 08:07:09  melanie
*** empty log message ***

Revision 1.1.1.1  2004/04/01 09:20:27  ddinside
Création du projet CMS Couleur Citron nom de code : tipunch

Revision 1.2  2003/11/26 13:08:42  ddinside
nettoyage des fichiers temporaires commités par erreur
ajout config spaw
corrections bug menu et positionnement des divs

Revision 1.1  2003/10/17 12:16:37  tofnet
Ajout de la gestion du sondage et de la génaration de la brique sondage

Revision 1.1  2003/10/14 09:09:34  tofnet
Ajout du menu pour accèder au sondage
Ajout du module d'admin du forum

*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
 include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


 //Translation engine is needed for the survey module
$translator =& TslManager::getInstance();
$langpile = $translator->getLanguages();


$strHTML = '';

/*
$sql = " select * from cms_survey_ask
	where cms_survey_ask.cms_id in (SELECT cms_id FROM cms_survey_answer)
	AND cms_id_site = ".$_SESSION['idSite_travail']."
	AND cms_active = 'Y'
	order by cms_dateadd DESC ";
*/
$sql = " select * from cms_survey_ask
	WHERE cms_id_site = ".$_SESSION['idSite_travail']."
	AND cms_active = 'Y'
	order by cms_dateadd DESC ";

$aSelect = dbGetObjectsFromRequete("cms_survey_ask", $sql);
 

if (sizeof($aSelect)==0) {
	// planté !  On loggue 
	$strHTML .= 'Aucune question.';
} else {
	
		$strHTML .= '<table class="arbo" border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF">
 <tr>
  <td class="arbo" bgcolor="#D2D2D2"><b>Libellé</b></td>
  <td class="arbo" bgcolor="#D2D2D2"><b>Date d\'ajout</b></td>
 </tr>';
 		 
		for ($i = 0; $i<sizeof($aSelect);$i++) {
		$oSelect = $aSelect[$i];
			$strHTML.="
<tr class=\"listDataL". $i%2 ."\"><td class=\"listDataValue\">";


$aMesQuestions = array ();

foreach ($langpile as $lang_id => $lang_props) { 
		$eTslValue = $translator->getByID($oSelect->get_libelle(), $lang_id); 
		 
		if (count($langpile) > 1)
			$eTslValue.="&nbsp; (".$lang_props['libellecourt'].")\n";
		array_push ($aMesQuestions, $eTslValue);
  
} 
$strHTML.="<a class=\"arbo\" href=\"javascript:setSelectAsk(".$oSelect->get_id().",'".addslashes($translator->getByID($oSelect->get_libelle()))."')\">".stripslashes(join("<br />", $aMesQuestions)) ."</a>";
  
  $strHTML.="</td><td class=\"listDataValue\">".adodb_date2("d/m/Y",$oSelect->get_dateadd())."</td>
</tr>
";
			 
		}
		$strHTML .='
</table>
'; 
}

?>
<html lang="fr">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<title>Backoffice de Stela</title>
<style type="text/css">
	@import "<?php echo  $URL_ROOT; ?>/backoffice/cms/css/menu.css";
</style>
<link rel="stylesheet" href="<?php echo  $URL_ROOT; ?>/backoffice/cms/css/bo.css" type="text/css">
<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<script language="javascript" type="text/javascript">
<!--
function setSelectAsk(askId, askLibelle){
	window.opener.document.forms['SURVEYform'].elements['SURVEYid'].value = askId;
	window.opener.document.forms['SURVEYform'].elements['libelle_ask'].value = askLibelle;
	window.opener.document.forms['SURVEYform'].elements['libelle_ask'].disabled = true;
	this.close();
}
//-->
</script>
</head>
<body>
<span class="arbo2"><strong>Pour sélectionner une question cliquez sur son libellé :</strong></span><br>
<br>
<span class="arbo">
<?php echo  $strHTML ; ?></span>
</body>
</htmL>
