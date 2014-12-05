<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
// CVS
/*
$Id: add_reponse.php,v 1.7 2013-03-01 10:28:20 pierre Exp $
$Author: pierre $
$Log: add_reponse.php,v $
Revision 1.7  2013-03-01 10:28:20  pierre
*** empty log message ***

Revision 1.6  2012-03-02 09:28:19  pierre
*** empty log message ***

Revision 1.5  2010-10-07 14:49:53  thao
*** empty log message ***

Revision 1.4  2010-10-06 13:28:28  thao
*** empty log message ***

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

//traduction
if (DEF_APP_USE_TRANSLATIONS) {
	$translator =& TslManager::getInstance();
	$langpile = $translator->getLanguages();
}
 

$questionId = $_GET["question"];
$strHTML = '';
if((isset($_GET['delid'])) and (strlen($_GET['delid']) >0)) {
	// Suppression des clicks internautes 
	$rs = dbDeleteId("cms_survey_reponse", "cms_answer", $_GET["delid"]); 
	
	// Suppresion de la réponse en question 
	$rs = dbDeleteId("cms_survey_answer", "cms_id", $_GET["delid"]); 
}
 
if((isset($_POST['id_ans'])) and  (strlen($_POST['id_ans']) >0)){
 
	// Mise à jour des donnée 
	$oAnswer = new Cms_survey_answer ($_POST['id_ans']);
	
	if (DEF_APP_USE_TRANSLATIONS) {
		foreach ($langpile as $lang_id => $lang_props) {
			
			$tsl_default = $_POST["libelle_ans_FR"];
			//echo "TEST : ".$tsl_default." : ".$form_field."_".$langpile[DEF_APP_LANGUE]['libellecourt']."<br />";
			if ($tsl_default != '') {
				$tsl_table = Array();
				foreach ($langpile as $lang_id => $lang_props) {
					if ($lang_id != DEF_APP_LANGUE) {
						if ($_POST["libelle_ans_".$lang_props['libellecourt']] != '') 
							$tsl_table[$lang_id] = $_POST["libelle_ans_".$lang_props['libellecourt']];
					}
				}
				$libelle = $translator->addTranslation($tsl_default, $tsl_table);
			} else {
				// unsset reference when updating to an empty text
				$libelle = -1;
			}
			$oAnswer->set_libelle( 	$libelle);		
		 		  
				
		} 
	}	
	else {
		$oAnswer->set_libelle($_POST['libelle_ans']);
	}
	
	
	$bRetour = dbUpdate($oAnswer); 
	if(!$bRetour){
		//planté on log
		error_log(date()." plantage lors de l'execution de la requete ".$sql);
		error_log($db->ErrorMsg());
		$strHTML .= 'Erreur de fonctionnement interne (Mise à jour impossible). Merci de prévenir l\'administrateur si l\'erreur persiste.';
	}else{
		$strHTML .= "Mise à jour effectuée<br><br>";
	}
}elseif((isset($_POST['libelle_ans'])) and (strlen($_POST['libelle_ans']) >0)){
 
		$oAnswer = new Cms_survey_answer();
		$oAnswer->set_libelle($_POST['libelle_ans']); 
		$oAnswer->set_ask($questionId);
		$bRetour = dbInsertWithAutokey($oAnswer);

		 
	// Gestion des erreurs
	if(!$bRetour){
		//planté on log
		error_log(date()." plantage lors de l'execution de la requete ".$sql);
		error_log($db->ErrorMsg());
		$strHTML .= 'Erreur de fonctionnement interne (Ajout impossible). Merci de prévenir l\'administrateur si l\'erreur persiste.';
	}else{
		$strHTML .= "Ajout effectué<br><br>";
	}
}
elseif((isset($_POST['libelle_ans_FR'])) and (strlen($_POST['libelle_ans_FR']) >0)){
 
		$oAnswer = new Cms_survey_answer();
		
		
		
		
		foreach ($langpile as $lang_id => $lang_props) {
			
			$tsl_default = $_POST["libelle_ans_FR"];
			//echo "TEST : ".$tsl_default." : ".$form_field."_".$langpile[DEF_APP_LANGUE]['libellecourt']."<br />";
			if ($tsl_default != '') {
				$tsl_table = Array();
				foreach ($langpile as $lang_id => $lang_props) {
					if ($lang_id != DEF_APP_LANGUE) {
						if ($_POST["libelle_ans_".$lang_props['libellecourt']] != '') 
							$tsl_table[$lang_id] = $_POST["libelle_ans_".$lang_props['libellecourt']];
					}
				}
				$libelle = $translator->addTranslation($tsl_default, $tsl_table);
			} else {
				// unsset reference when updating to an empty text
				$libelle = -1;
			} 	
		 		  
				
		} 
		
		
		
		
		
		
		
		
		
		
		$oAnswer->set_libelle($libelle); 
		$oAnswer->set_ask($questionId);
		$bRetour = dbInsertWithAutokey($oAnswer);

		 
	// Gestion des erreurs
	if(!$bRetour){
		//planté on log
		error_log(date()." plantage lors de l'execution de la requete ".$sql);
		error_log($db->ErrorMsg());
		$strHTML .= 'Erreur de fonctionnement interne (Ajout impossible). Merci de prévenir l\'administrateur si l\'erreur persiste.';
	}else{
		$strHTML .= "Ajout effectué<br><br>";
	}
}
//Sélection de la question pour laquelle on veut gérer les réponse
 
$rs = getObjectById("cms_survey_ask", $questionId);

// Gestion des erreurs
if (sizeof($rs) == 0){
	// planté !  On loggue
	error_log(date()." plantage lors de l'execution de la requete ".$sql);
	error_log($db->ErrorMsg());
	$strHTML .= 'Erreur de fonctionnement interne. Merci de prévenir l\'administrateur si l\'erreur persiste.';
}

// Sélection des réponse à la question
$sqlReponse = "SELECT * FROM cms_survey_answer WHERE cms_ask = ".$questionId.""; 
//echo $sqlReponse;
$aReponse =dbGetObjectsFromRequete("cms_survey_answer", $sqlReponse);

// Gestion des erreurs
if (sizeof($aReponse) == 0) { 
	$strHTML .=	"<br><br><div class='arbo'>Il n'y a pas de réponse définie. Pour en créer, veuillez compléter le formulaire en bas de page.</div>";
}else {
	$strHTML .= "<div class='arbo'>Intitulé de la question :<br><br>".str_replace("\n","<br>",stripslashes($rs->fields[1]))."<bR><br></div>";
	 
	$strHTML .= '<table class="listDataTable">
		<tr>
	<td class="MainTitle">&nbsp;Libellé&nbsp;</td>
	<td class="MainTitle" colspan="2">&nbsp;Action&nbsp;</td>
		</tr>';
	for ($i = 0; $i<sizeof($aReponse);$i++) {
	
	
		$oReponse = $aReponse[$i];
		
		$strHTML.="
			<tr class=\"listDataL". $i%2 ."\">";
			
			
			if (DEF_APP_USE_TRANSLATIONS) {
				$strHTML.="<td class=\"listDataValue\">";
				
				$aMesReponses = array ();
				foreach ($langpile as $lang_id => $lang_props) {
					
					
					$eTslValue = $translator->getByID($oReponse->get_libelle(), $lang_id); 
					 
					$strHTML.="&nbsp;".str_replace("\n","<br />",stripslashes($eTslValue))." (".$lang_props['libellecourt'].")<br /> ";
					array_push ($aMesReponses, $eTslValue);
				
				
						
				} 
				$strHTML.="</td>";
				$strHTML.="<td class=\"listDataValue\">&nbsp;<a href=\"javascript:setUpdateForm_translation(". $oReponse->get_id() .",'".addslashes(htmlentities(join(";",$aMesReponses)))."')\"><img onMouseOver='popup(\"Modifier la fiche réponse\");' onMouseOut='kill();' border=\"0\" alt=\"Modifier\" src=\"$URL_ROOT/backoffice/cms/img/modifier.gif\"></a>&nbsp;</td>";
		
				
			}
			else {
				$strHTML.="<td class=\"listDataValue\">&nbsp;".str_replace("\n","<br>",stripslashes($oReponse->get_libelle())) ."&nbsp;</td>";
				$strHTML.="<td class=\"listDataValue\">&nbsp;<a href=\"javascript:setUpdateForm(". $oReponse->get_id() .",'".addslashes(htmlentities($oReponse->get_libelle()))."')\"><img onMouseOver='popup(\"Modifier la fiche réponse\");' onMouseOut='kill();' border=\"0\" alt=\"Modifier\" src=\"$URL_ROOT/backoffice/cms/img/modifier.gif\"></a>&nbsp;</td>";
		
			}
		$strHTML.="<td class=\"listDataValue\">&nbsp;<a href=\"#\" onclick=\"redirect('add_reponse.php?delid=". $oReponse->get_id()."&question=".$questionId."','Etes vous sur de vouloir supprimer cette réponse ?');\"><img onMouseOver='popup(\"Supprimer cette réponse\");' onMouseOut='kill();' border=\"0\" alt=\"Supprimer\" src=\"$URL_ROOT/backoffice/cms/img/supprimer.gif\"></a>&nbsp;</td>
			</tr>";
			
			
			
			
			
	}
	$strHTML .='</table>'; 
}
$strHTML .= "<hr>
<table class=\"listDataTable\">
	<tr>
		<td class=\"listDataTitle\">Libellé de la réponse : </td>
		<td>";
		
		if (DEF_APP_USE_TRANSLATIONS) { 

 
			foreach ($langpile as $lang_id => $lang_props) {
				if (isset($_POST['id']) and (strlen($_POST['id']) > 0)) {  
					$eTslValue = $translator->getByID($libelle, $lang_id);
				}
				else {
					$eTslValue = '';
				} 
				
				$strHTML .= "<input type=\"text\" id=\"libelle_ans_".$lang_props['libellecourt']."\" name=\"libelle_ans_".$lang_props['libellecourt']."\"  maxlength=\"250\" size=\"50\" value=\"".$eTslValue."\" pattern=\"^.+$\" errorMsg=\"Vous devez saisir un libellé à la réponse.\">&nbsp;".$lang_props['libellecourt']."  <br />";
		  
					
			} 
			$strHTML .= "<input id=\"libelle_ans\" type=\"hidden\" value=\"\" name=\"libelle_ans\">";		
				
		}
		else {
				
		
			$strHTML .= "<input type=\"text\" id=\"libelle_ans\" name=\"libelle_ans\"  maxlength=\"250\" size=\"50\" value=\"\" pattern=\"^.+$\" errorMsg=\"Vous devez saisir un libellé à la réponse.\">";
		
		}
		
		$strHTML .= "<input id=\"id_ans\" type=\"hidden\" value=\"\" name=\"id_ans\"></td>
	</tr>
	<tr>
		<td colspan=\"2\" class=\"listDataTitle\"><input type=\"button\" onClick=\"if(validate_form()) submit();\" value=\"Valider\">&nbsp;&nbsp;<input type=\"reset\" value=\"Annuler\" name=\"cancel\"></td>
	</tr>
</table>"
;
?>
<script language="javascript" type="text/javascript">
<!--
function setUpdateForm(idToUpdate, libelle){
	document.forms["reponse"].elements["libelle_ans"].value = libelle;
	document.forms["reponse"].elements["id_ans"].value = idToUpdate;
}

function setUpdateForm_translation(idToUpdate, libelle){
	items = libelle.split(";");
		
	<?php
	$cpt = 0;
	
	foreach ($langpile as $lang_id => $lang_props) {
		$eTslValue = $translator->getByID($libelle, $lang_id);
		echo "document.forms[\"reponse\"].elements[\"libelle_ans_".$lang_props['libellecourt']."\"].value = items[".$cpt."];\n";	 
		$cpt++;	
	} 
	?>
	document.forms["reponse"].elements["id_ans"].value = idToUpdate;
}
//-->
</script>
<strong><div class="arbo2">Gestion des questions du sondage</div></strong><br><br>
<div class="arbo"><strong>Gestion des réponses :</strong></div>
<form name="reponse" method="post" action="<?php echo  $_SERVER['PHP_SELF'] ; ?>?question=<?php echo $questionId ?>">
<?echo stripslashes($strHTML) ?>
</form>
<br><br>
<div class="arbo"><a href="question_form.php">Ajouter</a> une question&nbsp;&nbsp;|&nbsp;&nbsp;Retour à la <a href="question.php">liste des questions</a></div>
