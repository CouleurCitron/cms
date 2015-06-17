<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

//init_set ('error_reporting', E_ALL);
	
// permet de dérouler le menu contextuellement
activateMenu("gestionclasse");

if (is_post("todo") == false){
	if (is_get("display") == true){
		
		$id = $_GET["display"];
		$oClass = new classe($id);
		$oEditedClassName = $oClass->get_nom();
		echo "edit XML for classe ".$oEditedClassName." (".$_GET["display"].")";
		if ($oClass->get_statut() == 4){
			echo ' | classe existante';
			$oEditedClass = new $oEditedClassName();
			$sXML = $oEditedClass->XML;
		}
		else{
			echo ' | nouvelle classe';
			$sXML = "";
		}
		//;
	}
	else{
		echo "no valid class id submitted";
	}
?>
<form id="xmlclass" name="xmlclass" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input id="id" name="id" value="<?php echo $id; ?>" type="hidden" />
<input id="todo" name="todo" value="sauve" type="hidden" />
<textarea id="fxml" name="fxml" rows="25" cols="75">
<?php echo stripslashes(htmlentities($sXML)); ?>
</textarea><br />
<input id="freset" name="freset" type="reset" value="<?php $translator->echoTransByCode('btAnnuler'); ?>" />
<input id="fsubmit" name="fsubmit" type="submit" value="<?php $translator->echoTransByCode('btValider'); ?>" />
</form>
<?php
} // if (is_post("todo") == false){
else{
	if (is_post("id") == true){
		echo "sauve XML for classe ".$_POST["id"];
		$id = $_POST["id"];
		$oClass = new classe($id);
		$oEditedClassName = $oClass->get_nom();
		//$oEditedClass = new $oEditedClassName();
		
		$postedXML = stripslashes($_POST["fxml"]);
		$stack = array();
		
		xmlStringParse($postedXML);
		
		if(is_array($stack) == true){
			echo "le XML est valide<br />\n";
			//$oEditedClass->XML = postedXML;
			$phpClass = generateAS2FromXMLString($postedXML);
			$phpClass = str_replace('DEF_CODE_STATUT_DEFAUT', DEF_CODE_STATUT_DEFAUT, $phpClass);
			
			echo "new content for ".$oEditedClassName."<br />\n";
			echo "<pre style=\"border:dashed\">".htmlentities($phpClass)."</pre>";			
		   
		}
		else{
			echo "le XML est invalide<br />\n";
		}
		//;
	}
	else{
		echo "no valid class id submitted";
	}
}
?>