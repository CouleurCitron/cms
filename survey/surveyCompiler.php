<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
//permet de dérouler le menu contextuellement
activateMenu('gestioncontenu');

// code historique - backward compatibility
function genComposant($SURVEYid, $SURVEYstyle, $sSite){

	global $db;

  	//Translation engine is needed for the survey module
	$translator =& TslManager::getInstance();
	$langpile = $translator->getLanguages();

	
  	$SURVEYtext .= '
		<script type="text/javascript">
		$(document).ready(function() {
			$("a#survey_view_all").bind("click", function() {
				$.ajax({
					//this is the php file that processes the data and send mail
					url: "modules/survey/surveyListe.php",		
					//POST method is used
					type: "POST",
					//pass the data	
					data: {
						"id_site" : '.$_SESSION['idSite'].'
					},					
					//Do not cache the page
					cache: false,			
					//success
					success: function (data) {		
						$.fancybox(data,{
							"hideOnOverlayClick"	: true,
							"showCloseButton"	: false,
							"titleShow"		: false
						});
						$.fancybox.resize();
					}
				});
				return false;
			});
			$("#survey_form").bind("submit", function() {
				$.ajax({
					//this is the php file that processes the data and send mail
					url : "modules/survey/surveyListe.php",		
					//POST method is used
					type	: "POST",
					//pass the data	
					data	: $(this).serializeArray(),
					
					//Do not cache the page
					cache: false,			
					//success
					success: function (data) {
						alert(\'success\');
						$.fancybox(data,{
							"hideOnOverlayClick"	: true,
							"showCloseButton"	: false,
							"titleShow"		: false
						});
						$.fancybox.resize();
					},
					error: function (data) {
						alert(\'error\');
					}
				});
				return false;
			});
		});
	</script>
	';
  	$SURVEYtext .= "<div class=\"".$SURVEYstyle."\">";
		
	// Sélection de la question dans la base de donnée
 
	$oAsk = getObjectById("cms_survey_ask", $SURVEYid);
	if (newSizeOf($oAsk) == 0) { 
		 $SURVEYtext="";
	} else {
		$sqlAnswer = "	SELECT	*
				FROM	cms_survey_answer
				WHERE	cms_ask = ".$SURVEYid;

		$aAnswer = dbGetObjectsFromRequete("cms_survey_answer", $sqlAnswer);   
		$SURVEYtext .= "<div class='survey_titre'>Sondage</div>";
		
		$SURVEYtext .= "<script type=\"text/javascript\">\n";
		$SURVEYtext .= "function validate_survey(form) {\n";
		if ($oAsk->get_multiple() == 'Y') {
			$SURVEYtext .= "\tvar go_submit = false;\n";
			$SURVEYtext .= "\tfor (var i=0; i<".newSizeOf($aAnswer)."; i++) {\n";
			$SURVEYtext .= "\t\tif (document.getElementById('answer_'+[i]).checked)\n";
			$SURVEYtext .= "\t\t\tgo_submit = true;\n";
			$SURVEYtext .= "\t}\n";
		} else	$SURVEYtext .= "\tvar go_submit = true;\n";
		$SURVEYtext .= "\tif (go_submit) {\n";
		$SURVEYtext .= "\t//openBrWindow('', 'surveypopup', 418, 500, 'scrollbars=yes', 'true');\n";
		$SURVEYtext .= "\t//document.getElementById('survey_form').target = 'surveypopup';\n";
		$SURVEYtext .= "\t//document.getElementById('survey_form').submit();\n";
		$SURVEYtext .= "\t$(\"#survey_form\").trigger('submit');\n";
		$SURVEYtext .= "\t}else{return false;}\n";
		$SURVEYtext .= "}\n";
		$SURVEYtext .= "function view_survey() {\n";
		$SURVEYtext .= "\topenBrWindow('', 'surveypopup', 310,340, 'scrollbars=yes', 'true');\n";
		$SURVEYtext .= "\tdocument.getElementById('survey_form').target = 'surveypopup';\n";
		$SURVEYtext .= "\tdocument.getElementById('id_ask').value='0';\n";
		$SURVEYtext .= "\t//document.getElementById('survey_form').submit();\n";
		$SURVEYtext .= "}\n";
		$SURVEYtext .= "</script>\n";
		
		$SURVEYtext .= "<form id=\"survey_form\" name=\"survey_form\" method=\"POST\">\n"; 
		
		$eTslValue = $translator->getByID($oAsk->get_libelle()); 
		$SURVEYtext .= stripslashes($eTslValue);
		
		$SURVEYtext .= "<input type=\"hidden\" id=\"from\" name=\"from\" value=\"vote\" />\n"; 
		$SURVEYtext .= "<input type=\"hidden\" id=\"id_site\" name=\"id_site\" value=\"".$_SESSION['idSite']."\" />\n"; 
		$SURVEYtext .= "<input type=\"hidden\" id=\"id_ask\" name=\"id_ask\" value=\"".$SURVEYid."\" />\n"; 
		
		for ($i = 0; $i<newSizeOf($aAnswer);$i++) {
			$oAnswer = $aAnswer[$i];
			$SURVEYtext .="<div class='fd_blanc'>";
			if ($oAsk->get_multiple() == 'Y') {
				$eTslValue = $translator->getByID($oAnswer->get_libelle());  
				$SURVEYtext .= "<input type=\"checkbox\" id=\"answer_{$i}\" name=\"answer_{$i}\" value=\"".$oAnswer->get_id()."\" />&nbsp;<label id='labelsondage'>".$eTslValue."</label>\n";
			} else {
				$eTslValue = $translator->getByID($oAnswer->get_libelle());  
				$SURVEYtext .= "<input type=\"radio\" id=\"answer\" name=\"answer\" value=\"".$oAnswer->get_id()."\" />&nbsp;<label id='labelsondage'>".$eTslValue."</label>\n";		
			}
			$SURVEYtext .="</div>";
			//$SURVEYtext .= "</tr>\n";
		}
		
		$SURVEYtext .= "<table cellspacing=\"0\" cellpadding=\"0\"  border=\"0\">\n"; 
		//$SURVEYtext .= "<tr><td><img src=\"/img/vide.gif\" height=\"5\" width=\"10\"></td></tr>\n";
		$SURVEYtext .= "<tr align=\"left\">\n<td class=\"textOption\" width=\"150\"><input type=\"button\" onclick=\"validate_survey();\" value=\">> Voter\" class=\"voter\"/>\n</a></td></tr>";
		//$SURVEYtext .= "<tr><td><img src=\"/img/vide.gif\" height=\"5\" width=\"10\"></td></tr>\n";
		$SURVEYtext .= "<tr align=\"left\">\n<td class=\"textOption\" width=\"150\"><a href=\"#_\" id='survey_view_all' onclick='view_survey();'>\n<img src='/custom/img/stela/puce_flecherouge2.gif'>&nbsp;Voir les réponses<br />aux 10 dernières questions.\n\n</a></form></td></tr>";
		$SURVEYtext .= "<tr> <td align=\"right\">\n</td>\n</tr>\n";
		$SURVEYtext .= "</table></form>";
		//$SURVEYtext .= "</table>\n</td>\n</tr>\n</table>";
	}
	$SURVEYtext .="</div>";

	return	$SURVEYtext;
}
// fin code historique - backward compatibility

// Initialisation des variable passé en POST
$SURVEYid = $_POST['SURVEYid'];
$SURVEYcontentId = $_POST['SURVEYcontentId']; 
$SURVEYtype = $_POST['SURVEYtype'];
$SURVEYsubmitType = $_POST['SURVEYsubmitType'];
$SURVEYtopic = $_POST['SURVEYtopic'];
$SURVEYname = $_POST['SURVEYname'];
//viewArray($_POST, 'POST');

$iWidth = $_POST['SURVEYwidth'];
$iHeight = $_POST['SURVEYheight'];

if ($SURVEYid > 0)
	$strBrick = "<?php require_once('cms-inc/survey/class.SurveyController.php');\n$"."controller = new SurveyController();\n$"."controller->setCurrent({$SURVEYid});\n$"."controller->build('renderSurveyForm'); ?>";
else	$strBrick = "";

 
if ($SURVEYsubmitType == 'preview') {
	// Affichage de la preview
	echo "<style type=\"text/css\">
		.textQuestionVert{
			font-family: \"Arial\", \"Verdana\";
			font-size: 11px;
			color: #FFFFFF;
			font-weight: bold;
		}
		
		.textQuestionJaune{
			font-family: \"Arial\", \"Verdana\";
			font-size: 11px;
			color: #FFFFFF;
			font-weight: bold;
		}

		.textOption{																			
			font-family: \"Arial\", \"Verdana\";
			font-size: 11px;
			font-color: black;
		}
	</style>";
	echo "<div style=\"width: ".$iWidth."px; height: ".$iHeight."px; background-color: #ffffff; overflow: auto;\">"
	. $strBrick
	. "</div><br>";
	
} else {
	// Envoi par formulaire pour stockage en base de données

	echo "<form name=\"SURVEYform\" method=\"post\" action=\"/backoffice/cms/briques/saveComposant.php?id=".$SURVEYcontentId."\">\n"
		."<input name=\"HTMLcontent\" type=\"hidden\" class=\"arbo\" value=\"".$strBrick."\">\n"
		."<input name=\"TYPEcontent\" type=\"hidden\" class=\"arbo\" value=\"Sondage\">\n"
		."<input name=\"NAMEcontent\" type=\"hidden\" class=\"arbo\" value=\"".$SURVEYname."\">\n"
		."<input name=\"WIDTHcontent\" type=\"hidden\" class=\"arbo\" value=\"".$iWidth."\">\n"
		."<input name=\"HEIGHTcontent\" type=\"hidden\" class=\"arbo\" value=\"".$iHeight."\">\n"
		."<input name=\"OBJIDcontent\" type=\"hidden\" class=\"arbo\" value=\"".$SURVEYid."\">\n"
		."</form>\n"
		."<script language=\"javascript\" type=\"text/javascript\">\n"
		."<!--\n"
		."document.forms['SURVEYform'].submit()\n"
		."//-->\n"
		."</script>";
}
?>