<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

if (!isset($classeName)){
	$classeName = preg_replace('/[^_]*_(.*)\.php/si', '$1', basename($_SERVER['PHP_SELF']));
}
//------------------------------------------------------------------------------------------------------


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/lib.inc.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbocarte.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/tableau.lib.php');

activateMenu("gestion".$classeName);

//provoque erreur s'il est à NULL
if ($_SESSION['id']== NULL)
	$_SESSION['id'] = "";

if($_SESSION['listParam']!="") {
	$listParam = $_SESSION['listParam'];
}
else {
	$_SESSION['listParam']!=$_SERVER['QUERY_STRING'];
	$listParam=$_SESSION['listParam'];
}


// récupération de l'id 
if ($id == "") {
	if (isset($_GET['adodb_next_page'])) { 
	$idPage=$_GET['adodb_next_page'];
	} 
	else if (isset($_GET['adodb_prev_page'])) { 
	$idPage=$_GET['adodb_prev_page'];
	}
	$qryref = dbGetArrayOneFieldFromRequete($_SESSION['sqlpag']);
	$id=$qryref[$idPage-1];
	for ($i=0;$i<count($qryref);$i++) {
		if ($qryref[$i]==$id) {
			$_SESSION['pag']=$i+1;
		}
	}
} 
// Pour calcul de l'id par rapport à la recherche et récupération de la position dans la navigation par rapport à cet id



// Pour récupération de la position dans la navigation de get_id
if ($_GET['id']) {
	$id=$_GET['id'];
	$qryref = dbGetArrayOneFieldFromRequete($_SESSION['sqlpag']);
	for ($i=0;$i<count($qryref);$i++) {
		if ($qryref[$i]==$id) {
		$_SESSION['pag']=$i+1;
		}
	}
}

// si l'opération est une suppression revenir à la page précédente
if ($operation=="DELETE") {?><script>
window.location="show_<?php echo $classeName; ?>.php?adodb_next_page=<?php echo $_GET['adodb_next_page'];; ?>"
</script>
<?php
						  }
?>

<script language="javascript" type="text/javascript">
	// retour à la liste
	function retour(){
		document.location.href="list_<?php echo  $classeName ; ?>.php<?php if($listParam!="") echo "?".$listParam;?>";
	}
	
	// ajout d'un enregistrement
	function addEmp()
	{
		document.<?php echo $classeName; ?>_form.actiontodo.value = "MODIF";
		document.<?php echo $classeName; ?>_form.display.value = null;
		document.<?php echo $classeName; ?>_form.id.value = -1;
		document.<?php echo $classeName; ?>_form.action = "maj_<?php echo $classeName; ?>.php?id=-1&adodb_next_page=-1<?php if($listParam!="") echo "&".preg_replace("/(id=)([0-9]+)/msi", "previd=$2", $listParam);?>";
		document.<?php echo $classeName; ?>_form.submit();
		
	}
	
	// modification de l'enregistrement
	function modifEmp() 
	{
		document.<?php echo $classeName; ?>_form.display.value = null;
		document.<?php echo $classeName; ?>_form.actiontodo.value = "MODIF";
		document.<?php echo $classeName; ?>_form.action = "maj_<?php echo $classeName; ?>.php?id=<?php echo $id; ?><?php if($listParam!="") echo "&".$listParam;?>";
		document.<?php echo $classeName; ?>_form.submit();
	}
	
	// suppression de l'enregtistrement
	function deleteEmp()
	{
		sMessage = "Etes vous sur(e) de vouloir supprimer cet enregistrement ?";
  		if (confirm(sMessage)) {

			document.<?php echo $classeName; ?>_form.operation.value = "DELETE";
			document.<?php echo $classeName; ?>_form.id.value = "<?php echo $id; ?>";
			document.<?php echo $classeName; ?>_form.display.value = null;
			document.<?php echo $classeName; ?>_form.submit();
		}
	}

</script>
<?php

// permet de dérouler le menu contextuellement
activateMenu("gestion".$classeName); 

if ($id != -1) {
// objet 
eval("$"."oRes = new ".$classeName."($"."id);");

$sXML = $oRes->XML;
unset($stack);
$stack = array();
xmlStringParse($sXML);

$classeName = $stack[0]["attrs"]["NAME"];
if (isset($stack[0]["attrs"]["LIBELLE"]) && ($stack[0]["attrs"]["LIBELLE"] != "")){
	$classeLibelle = stripslashes($stack[0]["attrs"]["LIBELLE"]);
}
else{
	$classeLibelle = $classeName;
}
$classePrefixe = $stack[0]["attrs"]["PREFIX"];
$aNodeToSort = $stack[0]["children"];

if($oRes) {

?>

<div class="ariane"><span class="arbo2">MODULE >&nbsp;</span><span class="arbo3">Tableau&nbsp;>&nbsp;Visualisation</span></div>


<?php
if ($_SESSION['sTexte']) echo ": votre recherche porte sur <b>".$_SESSION['sTexte']."</b> comme mot-clé.";
?><br><br>


<div align="center" class="arbo">
<?php
//===============================
// operations de BDD
//===============================

// suppression

$operation = $_POST['operation'];

if($operation == "DELETE") {

	$id = $_POST['id'];

	if ($id != " ") {
	
		
		// compte les objets avec cet id
		// pour voir si cet objet existe
		$eEmp = getCount($classeName, ucfirst($classePrefixe)."_id", ucfirst($classePrefixe)."_id", $id);
	
		if ($eEmp == 1) {
			eval("$"."oRes = new ".$classeName."($"."id);");
		
			dbDelete($oRes);
			$sMessage = $classeName." ".$oRes->get_id()." supprimé ";
			
			//****************modif thao**********************
			// récup de toutes les asso à $classeName
			
			$urlClass= "../../include/bo/class";
			//table contenant les classes liés
			$aTempClas=ScanDirs($urlClass, $classeName);
			for ($j=0; $j<sizeof($aTempClas);$j++) {
				$sAssoClasse = $aTempClas[$j];
				eval("$"."oAsso = new ".$sAssoClasse."();");
				$aForeign = dbGetObjects($sAssoClasse);
				$sXML = $oAsso->XML;
				// on vide le tableau stack
				unset($stack);
				$stack = array();
				xmlStringParse($sXML);	
				$foreignName = $stack[0]["attrs"]["NAME"];
				$foreignPrefixe = $stack[0]["attrs"]["PREFIX"];
				$foreignNodeToSort = $stack[0]["children"];
				for ($i=0;$i<count($foreignNodeToSort);$i++){
					if ($foreignNodeToSort[$i]["name"] == "ITEM"){	
						if ($foreignNodeToSort[$i]["attrs"]["NAME"] == $classeName){
							$eEmp = getCount($foreignName, ucfirst($foreignPrefixe)."_id", ucfirst($foreignPrefixe)."_".ucfirst($classeName), $id);
							if ($eEmp > 0) {
								
								$sqlDisplay =  "select ".ucfirst($foreignPrefixe)."_id from ".$foreignName." where ".ucfirst($foreignPrefixe)."_".ucfirst($classeName)."=".$id; 
								$aResponseDisplay = dbGetObjectsFromRequeteID($foreignName, $sqlDisplay);
								for ($a=0; $a<sizeof($aResponseDisplay); $a++) {
									$oResponseDisplay = $aResponseDisplay[$a];
									$idResponseDisplay = $oResponseDisplay->get_id();
									eval("$"."oRes3 = new ".$foreignName."($".idResponseDisplay.");");
									if ($oRes3->getGetterStatut()!="none") {
										if ($foreignNodeToSort[$i]["attrs"]["DEFAULT"] != ""){
											$foreignDefault=$foreignNodeToSort[$i]["attrs"]["DEFAULT"];
										}
										else {
											$foreignDefault="";
										}
										eval("$"."oRes3->set_".$classeName."($".foreignDefault.");"); 
										
										for ($l=0;$l<count($foreignNodeToSort);$l++){
											if ($foreignNodeToSort[$l]["name"] == "ITEM"){	
											
												if ($foreignNodeToSort[$l]["attrs"]["NAME"] == "statut") {
													if (isset($foreignNodeToSort[$l]["children"]) && (count($foreignNodeToSort[$l]["children"]) > 0)){
														$eCodeStatut = 5; // libelle écartée, code à reprendre pour le libellé "ecarté" ou autre
													}
													else
													{
														$eCodeStatut = DEF_CODE_STATUT_DEFAUT;
													}
												} // if ($foreignNodeToSort[$i]["attrs"]["name"] == "STATUT") {
											}
										}
										$oRes3->set_statut($eCodeStatut);
										
										$bAssoRetour = dbUpdate($oRes3);
										
									}
									else {
										$bAssoRetour = dbDelete($oRes3);
									} //if ($oRes3->getGetterStatut()!="none") {
									
								} //for ($a=0; $a<sizeof($aResponseDisplay); $a++) {
							}// if ($eEmp > 0) {
							
						}// if ($foreignNodeToSort[$i]["attrs"]["NAME"] == $classeName){
					}// if ($foreignNodeToSort[$i]["name"] == "ITEM"){	
				} //if ($foreignNodeToSort[$i]["name"] == "ITEM"){	
			} //for ($i=0;$i<count($foreignNodeToSort);$i++){
			
			//*************************************************
		}
	}
	
	// on reinitialise la table stack
	eval("$"."oRes = new ".$classeName."();");
	unset($stack);
	$stack = array();
	$sXML = $oRes->XML;
	xmlStringParse($sXML);
	
	$classeName = $stack[0]["attrs"]["NAME"];
	$classePrefixe = $stack[0]["attrs"]["PREFIX"];
	$aNodeToSort = $stack[0]["children"];
	
	echo "Enregistrement ".$id." supprimé<br><br>";

} // fin DELETE

else {  // operation autre DELETE
?>
<table border="0" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo" width="100%">
<?php

} // fin operation autre DELETE

?>
<br />
<form name="<?php echo $classeName; ?>_form" method="post">
<input type="hidden" name="urlRetour" id="urlRetour" value="<?php echo  $_SERVER['REQUEST_URI'] ; ?>" />
<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
<input type="hidden" name="display" id="display" value="" />
<input type="hidden" name="actionUser" id="actionUser" value="" />
<input type="hidden" name="operation" id="operation" value="<?php echo $operation; ?>" />
<input type="hidden" name="actiontodo" id="actiontodo" value="" />
<input type="hidden" name="sensTri" id="sensTri" value="<?php echo $sensTri; ?>" />
<input type="hidden" name="champTri" id="champTri" value="<?php echo $champTri; ?>" />
<input type="hidden" name="idStatut" id="idStatut" value="" />
<input type="hidden" name="cbToChange" id="cbToChange" value="" />
<input type="hidden" name="eStatut" id="eStatut" value="" />
<input type="hidden" name="sTexte" id="sTexte" value="" />




<tr><td >
		 <div class="technical_contenu_bloc2">
		 <?php
		 
		 $sql = "select * from cms_tableau where cms_id=".$_GET['id'];
		 //echo $sql ;
		 $aTableau=dbgetObjectsFromRequete("cms_tableau", $sql);
		 $HTMLtoPrint.= "";
		 //echo sizeof($aTableau);
		 for($i=0;$i<sizeof($aTableau);$i++){
			$oTableau = $aTableau[$i];
			$strHTMLBase = $oTableau->get_html();
			$arrayTR = getArrayByTR ($strHTMLBase);
			$HTMLtoPrint="";
			$HTMLtoPrint.="<p><strong>".$oTableau->get_libelle()."</strong></p>";
			for ($j=0; $j<sizeof($arrayTR);$j++) {
				$contenuTR = $arrayTR[$j];
				// PDF
				$valuePDF="";
				$valuePDF = strchr($contenuTR, "PDF-");
				$toDel = strchr($valuePDF, "-PDF");
				$valuePDF = str_replace($toDel, "", $valuePDF);
				$valuePDF = str_replace("PDF-", "", $valuePDF);
				$contenuTR = str_replace ("PDF-".$valuePDF."-PDF", "", $contenuTR);
				
				// Ajout col PDF
				if ($j==0) $contenuTR = str_replace ("</tr>", "<td rowspan=\"2\" class=\"technical_table_td3\" width=\"50\">PDF</td></tr>", $contenuTR);
				else if($j>1) {
					if ($valuePDF!="") $imgPDF = "<a href=\"http://".$_SERVER['HTTP_HOST']."/backoffice/cms/utils/telecharger.php?chemin=/custom/upload/composant/tableaux/&file=/custom/upload/composant/tableaux/".$valuePDF.".pdf\" target=\"_blank\"><img src=\"http://".$_SERVER['HTTP_HOST']."/images/picto_pdf2.gif\" alt=\"\" width=\"16\" height=\"16\" /></a>";
					else $imgPDF = "&nbsp;";
					$contenuTR = str_replace ("</tr>", "<td class=\"technical_table_td4\">".$imgPDF."</td></tr>", $contenuTR);
				}
				$HTMLtoPrint.= $contenuTR;
				//if ($j>0) $HTMLtoPrint.= getHTMLSpacer();
			}
		 	echo "<table class=\"technical_table\">";
			echo $HTMLtoPrint;
			echo "</table>";
			echo "<br />";
			echo "<span>".$oTableau->get_basdepage()."<br><br><br></span>";
		 }
		 ?>
		 </div>
</td></tr>
<tr>
<td>
<div align="center" class="arbo">
<a href="list_cms_tableau.php?<?php echo $_SERVER["QUERY_STRING"]; ?>" class="arbo">Retour à la liste</a>&nbsp;&nbsp;&nbsp;
<a href="create_cms_tableau.php?<?php echo $_SERVER["QUERY_STRING"]; ?>" class="arbo">Retour au tableau</a>&nbsp;&nbsp;&nbsp;
<a href="maj_cms_tableau.php?<?php echo $_SERVER["QUERY_STRING"]; ?>" class="arbo">Retour à la fiche</a>
<!--<a href="javascript:addEmp()" class="arbo">Ajout nouvel enregistrement</a>&nbsp;&nbsp;
<?php if($operation != "DELETE") { ?>
<a href="javascript:modifEmp()" class="arbo">Modification de l'enregistrement</a>&nbsp;&nbsp;
<a href="javascript:deleteEmp()" class="arbo">Suppression de l'enregistrement</a>&nbsp;&nbsp;
<?php } ?>-->
</div>
</td>
</tr>
</table>
<?php

	
} else {
	die("Erreur ".$classeName." non trouvé");
		}
}
?>
</div>
</form>