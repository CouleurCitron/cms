<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once("cms-inc/include_cms.php");
include_once("cms-inc/include_class.php");


// fonction verif format email
if (isset($_GET['idclasse']) && $_GET['idclasse']!="") {
	
	$sql="select * from classe where cms_id=".$_GET['idclasse'];
	$aClasse = dbGetObjectsFromRequeteID("classe", $sql);
	if ($aClasse){
		$oClasse = $aClasse[0];
		$sql2="select * from ".$oClasse->get_nom()." ";
		$aObjet = dbGetObjectsFromRequete($oClasse->get_nom(), $sql2);
		if (sizeof($aObjet) > 0 ) {	
			$sel = "Sélectionner un enregistrement &nbsp; <select name=\"fEnregistrement\" id=\"fEnregistrement\" class=\"arbo\" >";
			$sel.="<option value=\"-1\">- - Liste de ".$oClasse->get_nom()."  - -</option>";
			for($i=0;$i<sizeof($aObjet);$i++){
				$oObjet=$aObjet[$i];
				if ($oObjet->get_statut() == DEF_ID_STATUT_LIGNE) {	
					eval ("$"."displayValue = $"."oObjet->get_".strval($oObjet->getDisplay())."();");
					$displayValueShort = substr($displayValue, 0, 50);
					if (strlen($displayValue) > 50 ) $displayValueShort.= " ... ";
					eval ("$"."abstractValue = $"."oObjet->get_".strval($oObjet->getAbstract())."();");
					$abstractValueShort = substr($abstractValue, 0, 50);
					if (strlen($abstractValue) > 50 ) $abstractValueShort.= " ... ";
					$sel.="<option value=\"".$oObjet->get_id()."\" >".$displayValueShort." - ".$abstractValueShort."</option>";
				}
			}
			$sel.="</select>";
		}
		else {
			$sel = "Pas d'enregistement";
		}
	}
	else{
		$sel = "Pas d'enregistement";	
	}	
	echo $sel;
}




?>
