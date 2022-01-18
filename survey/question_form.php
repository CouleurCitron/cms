<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

include_once('include_class.php');
include_once('include_cms.php');

activateMenu('gestionsondage');  //permet de dérouler le menu contextuellement

//Translation engine is needed for the survey module
$translator =& TslManager::getInstance();
$langpile = $translator->getLanguages();

// Déclaration des variables
$errors = 0;
$msgStatus = '';
$sql='';
$id = '';
$famille_id='';
$libelle = '';
 
	if(isset($_GET['id'])) {
	
		$id = $_GET['id']; 
		
		$rs = getObjectById("cms_survey_ask", $id);
		if (newSizeOf($rs) == 0){
			// planté !  On loggue
			error_log(" erreur lors de l'execution de la requete ".$sql);
			error_log($db->ErrorMsg());
			$errors++;
		} else {
			$libelle = stripslashes($rs->get_libelle());
			$multiple = $rs->get_multiple();
			$active = $rs->get_active();
		}
		$submitText="Modifier";
	} 
	elseif (isset($_POST['libelle']) || isset($_POST['libelle_FR'])) {
		
		if (isset ($_POST['libelle'])) {	/// ?????
			$libelle = $_POST['libelle'];
		} else {
			foreach ($langpile as $lang_id => $lang_props) {
				
				$tsl_default = $_POST["libelle_FR"];
				//echo "TEST : ".$tsl_default." : ".$form_field."_".$langpile[DEF_APP_LANGUE]['libellecourt']."<br />";
				if ($tsl_default != '') {
					$tsl_table = Array();
					foreach ($langpile as $lang_id => $lang_props) {
						if ($lang_id != DEF_APP_LANGUE) {
							if ($_POST["libelle_".$lang_props['libellecourt']] != '') 
								$tsl_table[$lang_id] = $_POST["libelle_".$lang_props['libellecourt']];
						}
					}
					$libelle = $translator->addTranslation($tsl_default, $tsl_table);
				} else {
					// unsset reference when updating to an empty text
					$libelle = -1;
				}
					  
					
			} 
			
		}
	
	$multiple = (!empty($_POST['multiple']) ? 'Y' : 'N');
	$active = (!empty($_POST['active']) ? 'Y' : 'N');
	
	  
	if (isset($_POST['id']) and (strlen($_POST['id']) > 0)) {
		
		$id = $_POST['id'];
		$oAsk = new Cms_survey_ask ($id);
		$oAsk->set_libelle(addslashes($libelle));
		$oAsk->set_multiple($multiple);
		$oAsk->set_active($active);
		$bRetour = dbUpdate($oAsk);
		$msgStatus = "Modification effectuée.<br>";
	} else {
	
		$oAsk = new Cms_survey_ask();
		$oAsk->set_libelle(addslashes($libelle)); 
		$oAsk->set_multiple($multiple);
		$oAsk->set_active($active);
		$oAsk->set_id_site($_SESSION['idSite_travail']);
		$bRetour = dbInsertWithAutokey($oAsk);
    	$msgStatus = "Ajout effectué.<br>";
	} 
	if (!$bRetour) {
		// planté !  On loggue
		error_log(" erreur lors de l'execution de la requete ".$sql);
		error_log($db->ErrorMsg());
		$errors++;
	} else {
		//Séquences 
		$id = $bRetour;
	}
	$submitText="Modifier";
} else {
	// il s'agit d'un ajout
	$submitText="Ajouter";
}

if ($errors > 0) {
	$msgStatus = '<i>Il y a eu des erreurs pendant le traitement.</i><br><b>Echec de la requête.</b>';
}
?>
<strong><div class="arbo2">Gestion des questions du sondage</div></strong><br><br>
<div class="arbo"><strong><?php echo  $submitText ; ?> une question :</strong></div>
<br>
<?php echo  $msgStatus ; ?>
<br>
<form name="ask" method="post" action="<?php echo  $_SERVER['PHP_SELF'] ; ?>">
<input type="hidden" name="id" value="<?php echo  $id ; ?>">
<table class="listDataTable">

 

<tr>
 <td class="listDataTitle">Libellé de la question :&nbsp;</td>
  <td class="listDataValue">

<?php 
 
foreach ($langpile as $lang_id => $lang_props) {
	if (isset($_POST['id']) and (strlen($_POST['id']) > 0)) {  
		$eTslValue = $translator->getByID($libelle, $lang_id);
	}
	else if (isset($_GET['id']) and (strlen($_GET['id']) > 0)) {   
		$eTslValue = $translator->getByID($libelle, $lang_id);
	}
	else {
		$eTslValue = '';
	}
		 
	echo "<textarea id=\"libelle_".$lang_props['libellecourt']."\" name=\"libelle_".$lang_props['libellecourt']."\" cols=\"50\" rows=\"8\" style=\"font-size:11px\">".$eTslValue."</textarea>&nbsp;".$lang_props['libellecourt']." <br />";
	  
		
} 
		
?>

</td>
</tr>
<tr>
 <td class="listDataTitle" colspan="2">
 <input type="checkbox" name="multiple" id="multiple"<?php echo ($multiple == 'Y' ? 'checked="true"' : ''); ?> />&nbsp;&nbsp;Cochez pour autoriser les réponses multiples.</td>
 </td>
</tr>
<tr>
 <td class="listDataTitle" colspan="2">
 <input type="checkbox" name="active" id="active"<?php echo ($active == 'Y' ? 'checked="true"' : ''); ?> />&nbsp;&nbsp;Cochez pour rendre la question active (publiable).</td>
 </td>
</tr>
<tr>
 <td class="listDataTitle" colspan="2"><input type="button" onClick="if(validate_form()) submit();" value="<?php echo  $submitText ; ?>"></td>
</tr>
</table>
</form>
<div class="arbo">Retour à la <a href="question.php">liste des questions</a><?php if($id != ""){echo "&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"add_reponse.php?question=".$id."\">Associer des réponses</a>";}?></div>