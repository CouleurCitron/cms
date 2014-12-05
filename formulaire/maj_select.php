<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once("cms-inc/include_cms.php");
include_once("cms-inc/include_class.php");

$sel = "";
if (isset($_GET['type_champ']) && $_GET['type_champ']!="" && isset($_GET['id']) && $_GET['id']!="") {
	
	if ($_GET['type_champ'] == "ABO") {
		// on regarde s'il existe des themes
		$eClasseTheme = getCount_where ("classe", array("cms_nom", "cms_statut"), array("news_theme", DEF_ID_STATUT_LIGNE), array("TEXT", "NUMBER"));
		if ( $eClasseTheme > 0 ) {
			
			// on compte le nombre de thème
			$eTheme = getCount_where ("news_theme", array("theme_statut"), array(DEF_ID_STATUT_LIGNE), array("NUMBER"));
			
			if ($eTheme>0) {
				$sRequete="select * from news_theme where theme_statut = ".DEF_ID_STATUT_LIGNE." order by theme_libelle";
				$aTheme= dbGetObjectsFromRequete("news_theme", $sRequete);
				$sel.="<select class=\"arbo\" id=\"type_champ_plus_".$_GET['id']."\" name=\"type_champ_plus_".$_GET['id']."\">";
				$sel.="<option value =\"\">Sélectionnez la newsletter</option>";
				for ($i=0; $i<sizeof($aTheme); $i++) {
					$oTheme = $aTheme[$i];
					$sel.="<option value=\"".$oTheme->get_id()."\">".$oTheme->get_libelle()."</option>";
				}
				$sel.="</select>";
			}
			else {
				$sel.="Vous ne disposez d'aucune newsletter";
			}
		}
		
	}
	else {
		$sel.="<input type=\"hidden\" id=\"type_champ_plus_".$_GET['id']."\" name=\"type_champ_plus_".$_GET['id']." value=\"none\"\">";
	}
}
else {
	$sel = "";
}


$sel = str_replace("'", "\'", $sel);
//$sel = str_replace('"', "'+String.fromCharCode(34)+'", $sel);
$sel = str_replace ("\r\n", '\n', $sel);
$sel = str_replace ("\r", '\n', $sel);
$sel = str_replace ("\n", '\n', $sel);
?>

el = document.getElementById('bloc_<?php echo $_GET['id']; ?>');
el.innerHTML = '<?php echo $sel; ?>'; 
 