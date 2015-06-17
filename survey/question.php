<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
// CVS
/*
$Id: question.php,v 1.11 2013-03-01 10:28:20 pierre Exp $
$Author: pierre $
$Log: question.php,v $
Revision 1.11  2013-03-01 10:28:20  pierre
*** empty log message ***

Revision 1.10  2012-03-02 09:28:19  pierre
*** empty log message ***

Revision 1.9  2010-10-20 15:28:42  thao
*** empty log message ***

Revision 1.8  2010-10-18 08:02:29  pierre
prise en comptes des traductions
possibilité de vider la brique

Revision 1.7  2010-10-07 14:49:53  thao
*** empty log message ***

Revision 1.6  2010-10-06 13:28:28  thao
*** empty log message ***

Revision 1.5  2010-09-27 09:50:59  thao
*** empty log message ***

Revision 1.4  2010-07-22 08:57:21  luc
no message

Revision 1.3  2009-09-24 08:53:14  pierre
*** empty log message ***

Revision 1.2  2008-04-30 15:01:49  thao
*** empty log message ***

Revision 1.1  2008/04/30 09:33:59  thao
*** empty log message ***

Revision 1.2  2008/03/04 14:54:19  pierre
*** empty log message ***

Revision 1.1  2006/04/12 07:25:22  sylvie
*** empty log message ***

Revision 1.1.1.1  2003/10/24 09:08:08  ddinside
nouvel import projet Boulogne apres migration machine

Revision 1.2  2003/10/17 12:16:37  tofnet
Ajout de la gestion du sondage et de la génaration de la brique sondage

Revision 1.1  2003/10/14 09:09:34  tofnet
Ajout du menu pour accèder au sondage
Ajout du module d'admin du forum

*/

activateMenu('composants');  //permet de dérouler le menu contextuellement

//Translation engine is needed for the survey module
$translator =& TslManager::getInstance();
$langpile = $translator->getLanguages();


$strHTML = '';
if((isset($_GET['delid'])) and (strlen($_GET['delid']) >0)) {

	// Sélection des réponses dans la table survey_reponse
	$sqlSelect = "SELECT cms_id FROM cms_survey_answer WHERE cms_ask = ".$_GET["delid"]."";
	$aSelect = dbGetObjectsFromRequete("cms_survey_answer", $sqlSelect);

	// Si il y a des réponse alors on boucle
	for ($i = 0; $i<sizeof($aSelect);$i++) {
		$oSelect = $aSelect[$i];
		$bRetour = dbDeleteId("cms_survey_reponse", "cms_answer", $oSelect->get_id()); 
		if (!$bRetour) {
			// planté !  On loggue
			error_log(" plantage lors de l'execution de la requete ".$sql);
			error_log($db->ErrorMsg());
			$strHTML .= '<br><br><b>Erreur de traitement. Suppression impossible.</b><br><br>';
		}
	}
	
	// suppression des réponses 
	$rs = dbDeleteId("cms_survey_answer", "cms_ask", $_GET["delid"]); 
	// Gestion des erreurs
	if (!$rs) {
		// planté !  On loggue
		error_log(" plantage lors de l'execution de la requete ".$sql);
		error_log($db->ErrorMsg());
		$strHTML .= '<br><br><b>Erreur de traitement. Suppression impossible.</b><br><br>';
	}

	// Suppression de la question en elle même 
	$rs_ask = dbDeleteId("cms_survey_ask", "cms_id", $_GET["delid"]); 
	// Gestion des erreurs
	if (!$rs) {
  	// planté !  On loggue
		error_log(" plantage lors de l'execution de la requete ".$sql_ask);
		error_log($db->ErrorMsg());
		$strHTML .= '<br><br><b>Erreur de traitement. Suppression impossible.</b><br><br>';
	}
				
}

$sql = "select *
	from cms_survey_ask WHERE cms_id_site=".$_SESSION['idSite_travail']."
	order by cms_dateadd DESC, cms_id DESC"; 
$aSelect = dbGetObjectsFromRequete("cms_survey_ask", $sql);

 
	if (sizeof($aSelect)==0) {
		$strHTML .= "<div class='arbo'>Il n'y a pas de question définie. Pour en créer, veuillez cliquer sur <a href=\"question_form.php\">Ajouter</a></div>";
	} else {
		$strHTML .= '<table class="listDataTable">
 <tr>
  <td class="MainTitle">&nbsp;Libellé&nbsp;</td>
  <td class="MainTitle">&nbsp;Date d\'ajout&nbsp;</td>
  <td class="MainTitle" colspan="3">&nbsp;Action&nbsp;</td>
 </tr>';
 		$c=0;
		for ($i = 0; $i<sizeof($aSelect);$i++) {
			$oSelect = $aSelect[$i];
			
			
			 
				 
			
			
			$strHTML.="
<tr class=\"listDataL". $i%2 ."\">";

$strHTML.="  <td class=\"listDataValue\">";
foreach ($langpile as $lang_id => $lang_props) {
	$eTslValue = $translator->getByID($oSelect->get_libelle(), $lang_id); 
	if ($eTslValue == "") { 
		$tsl_table = Array();
		$tsl_default  = $oSelect->get_libelle(); 
		$tsl_table[$lang_id] = $oSelect->get_libelle(); 
		$libelle = $translator->addTranslation($tsl_default, $tsl_table);
		$eTslValue = $translator->getByID($libelle, $lang_id); 
	}
	$strHTML.="&nbsp;".$oSelect->get_id()." - ". str_replace("\n","<br />",stripslashes($eTslValue))." (".$lang_props['libellecourt'].")<br /> ";
}

$strHTML.="  </td> ";

$strHTML.="  <td class=\"listDataValue\">&nbsp;".adodb_date2("d/m/Y",$oSelect->get_dateadd())."&nbsp;</td>
	<td class=\"listDataValue\">&nbsp;<a href=\"add_reponse.php?question=". $oSelect->get_id() ."\"><img onMouseOver='popup(\"Gérer les réponses de cette question\")' onMouseOut='kill();' border=\"0\" alt=\"Modifier les réponses de cette question\" src=\"/backoffice/cms/img/detail.gif\"></a>&nbsp;</td>
  <td class=\"listDataValue\">&nbsp;<a href=\"question_form.php?id=". $oSelect->get_id() ."\"><img onMouseOver='popup(\"Modifier la fiche question\");' onMouseOut='kill();' border=\"0\" alt=\"Modifier\" src=\"$URL_ROOT/backoffice/cms/img/modifier.gif\"></a>&nbsp;</td>
  <td class=\"listDataValue\">&nbsp;<a href=\"#\" onclick=\"redirect('question.php?delid=". $oSelect->get_id() ."','Etes vous sur de vouloir supprimer cette question ?');\"><img onMouseOver='popup(\"Supprimer cette question\");' onMouseOut='kill();' border=\"0\" alt=\"Supprimer\" src=\"$URL_ROOT/backoffice/cms/img/supprimer.gif\"></a>&nbsp;</td>
</tr>
";
		}
		$strHTML .='
</table>
';
	} 

?>
<strong><div class="arbo2">Gestion des questions du sondage</div></strong><br><br>
<div class="arbo"><strong>Liste des questions :</strong></div><br><br>
<form name="question" method="post" action="<?php echo  $_SERVER['PHP_SELF'] ; ?>">
<?php echo  $strHTML ; ?>
</form>
<br><br>
<div class="arbo"><a href="question_form.php" class="arbo">Ajouter</a> une question.</div>
