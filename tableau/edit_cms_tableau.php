<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 

***************************************************
*** Gestion de l'arbo des cartes                ***
*** Classeur de cartes                          ***
*** Création d'une carte                        ***
***************************************************

*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbocarte.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/tableau.lib.php');

$classeName = "cms_tableau";

activateMenu('gestiontableau');  //permet de dérouler le menu contextuellement

$idSite = $_GET['idSite'];
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];



$display=intval($_GET['display']);
if(!isset($display)){
	$display=intval($_POST['display']);
}

if (is_get("id")) {
	$id=$_GET['id'];
}
// Pour calcul de l'id par rapport à la recherche et récupération de la position dans la navigation par rapport à cet id
else {
	
}
if (is_post("id")) {
	$id=$_POST['id'];
}

$actiontodo = ( isset($_GET['actiontodo']) && $_GET['actiontodo']!="" ) ? $_GET['actiontodo'] : "MODIF" ;
$actiontodo = ( $_POST['actiontodo']!="SAUVE" ) ? "MODIF" : "SAUVE" ;
// objet 
if ( $display > 0 ){
	 $operation = "UPDATEORINSERT";
}
elseif ( $id > 0 ){
	 $operation = "UPDATE";
}
else {
	$operation = "INSERT";
}



if ( $operation == "INSERT" ) { // Mode ajout
	eval("$"."oRes = new ".$classeName."();");
}
elseif ( $operation == "UPDATE" ) { // Mode mise à jour
	eval("$"."oRes = new ".$classeName."($"."id);");
}
else{ // Mode acces par display
	eval("$"."oRes = new ".$classeName."();");
}
$idLigne=$_GET["ligne"];

?>
<link href="/backoffice/cms/css/bo.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>
<script type="text/javascript">
<!--

function validformul() {

	document.editLigne.actiontodo.value="SAUVE";
	document.editLigne.submit();
}
-->
</script><br />
<br />
<br />
<div class="ariane"><span class="arbo2">MODULE >&nbsp;</span><span class="arbo3">Création&nbsp;>&nbsp;Editer une ligne</span></div>

<?php

if(!$_GET['nom']&&!$_POST['nom']) {
// MODE EDITION //

?>
<form name="editLigne" id="editLigne" action="#" method="post">
	
<input type="hidden" name="actiontodo" value="SAUVE">
<table border="0" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
<tr><td width="676"  class="arbo"><br /><br /><u><b>Tableau</b></u>

<?php
	echo "<br />Ligne ".$_GET["ligne"]."<br><br><br>";
	$sql="select * from cms_tableau_perempt where cms_tableau=".$_GET["id"]." order by cms_version DESC";
	$aResTemp=dbGetObjectsFromRequete("cms_tableau_perempt", $sql); 
	$oResTemp =  $aResTemp[0];
	$oResTemp = new Cms_tableau_perempt($oResTemp->get_id());
	
	$strHTMLBase=$oResTemp->get_html();

	if($actiontodo == "MODIF") { 
	//calcul nombre de colonnes
	$arrayTR = getArrayByTR ($strHTMLBase);
	$nombreColonnes = getNombreColonnes($arrayTR);
	$ligneAllAll = "";
	$idLigne = ($_GET["ligne"]-1);
	
	// premier ligne 
	$ligneTR = $arrayTR[0];
	$aItems = array();
	preg_match_all  ("|<td[^>]*>(.*)<\/td>|U", $ligneTR, $aItems);
	$arrayTD = $aItems[1];
	$nbColspanTotal = 1;
	$arrayROWSPAN = array ();
	$arrayROWSPANValue = array ();

		
	for ($l=0;$l<newSizeOf($arrayTD);$l++) {
		$idColonne = $l+1;
		$ligneAll = $aItems[0][$l];
		if ($idLigne == 0) {
			$ligneAll = str_replace (">".$arrayTD[$l]."<", "><input type=\"text\" id=\"".($idLigne+1)."/".($l+1)."\" name=\"".($idLigne+1)."/".($l+1)."\" value=\"".$arrayTD[$l]."\" class=\"arbo\" size=\"10\" ><", $ligneAll);
		
		}
		$ligneAllTR.= $ligneAll;
		
	}
	
	// "*********************************************".$nbColspanTotal;
	$ligneAllTR="<tr>".$ligneAllTR."</tr>"; 
	$ligneAllAll.= $ligneAllTR;
	if ($i>1) $ligneAllAll.= getHTMLSpacer();
	
	if ($idLigne!=0) {
		
		$ligneTR = $arrayTR[$idLigne];
		$ligneAllTR="";
		$aItems = array();
		preg_match_all  ("|<td[^>]*>(.*)<\/td>|U", $ligneTR, $aItems);
		$arrayTD = $aItems[1];
		$arrayTDBalise = $aItems[0];
		$ligneAll ="";
		$cptRowspan=0;
		for ($l=0;$l<newSizeOf($arrayTD);$l++) { 
			$idColonne = $l+1;
			$ligneTD = $arrayTDBalise[$l];
			$boolRechercheClass = strpos($ligneTD, "class");
			if ($boolRechercheClass == "" ) {
				$colorTD = getColorTD ($l+1);
				$ligneTD = str_replace ("<td", "<td ".$colorTD, $ligneTD);
			}
			
			$ligneTD = str_replace (">".$arrayTD[$l]."<", "><input type=\"text\" id=\"".($idLigne+1)."/".$idColonne."\" name=\"".($idLigne+1)."/".$idColonne."\" value=\"".$arrayTD[$l]."\" class=\"arbo\" size=\"10\" ><", $ligneTD);
			
			$ligneAllTR.= $ligneTD;  
			
		}
		
		$ligneAllTR="<tr>".$ligneAllTR."</tr>"; 
		$ligneAllAll.= $ligneAllTR;
	}


	$ligneAllAll= checkCss($ligneAllAll);
	$ligneAll2.= "<TABLE width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#FFFFFF\"><TR><TD align=\"center\" valign=\"middle\">\n";
	$ligneAll2.= "<table width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"technical_table\">\n";
	$ligneAll2.=$ligneAllAll."\n";
	$ligneAll2.="</TABLE></TD></TR></TABLE>\n";
	echo $ligneAll2;
	?>
	</td></tr>
 <tr>
  <td align="center" class="arbo">       
  <a href="maj_cms_tableau.php?<?php echo $_SERVER["QUERY_STRING"]; ?>"><< Retour à la fiche du tableau</a>     &nbsp;&nbsp;&nbsp;&nbsp;      
  <a href="javascript:validformul()">Suite (enregistrer) >></a>
</td>
 </tr>
	<?php
	
} // fin MODIF
else {
	
	//calcul nombre de colonnes
	$htmlToSave="";
	$arrayTR = getArrayByTR ($strHTMLBase);
	$nombreColonnes = getNombreColonnes($arrayTR);
	$nombreLignes = getNombreLignes($arrayTR);
	$ligneAllAll = "";
	$idLigne = ($_GET["ligne"]-1);
	
	for ($i=0; $i<newSizeOf($arrayTR);$i++){
		$ligneTR = $arrayTR[$i];
		if ($i == 0) $ligneFirst =$arrayTR[$i];
		$ligneAll ="";
		$ligneAllTR ="";
		$aItems = array();
		 
		if ($i==$idLigne) {
			$ligneTRtemp="";
			preg_match_all  ("|<td[^>]+>(.*)<\/td>|U", $ligneTR, $aItems);
			for ($l = 0; $l<newSizeOf($aItems[0]);$l++) {
				$idColonne = $l+1;
				$ref = ($idLigne+1)."/".($l+1); 
				$ligneTRtemp.= "<td ".getColorTD($idLigne+1).">".$_POST[$ref]."</td>";
			}
			
			$ligneAllTR.="<tr>".$ligneTRtemp."</tr>";
		}
		else {
			$ligneAllTR.=$ligneTR;
		}
		
		$ligneAllAll.= $ligneAllTR;	
		if ($i>1) {
			$ligneAllAll.= getHTMLSpacer();
		}
		$htmlToSave.= $ligneAllTR;	
	}
	echo $htmlToSave;
	$oResTemp->set_html($htmlToSave);
	$bRetour = dbUpdate($oResTemp);
	if ($bRetour)
		echo "Modification de la ligne <b>".$idLigne."</b> effectuée<br><br><br>";
	else
		echo "erreur";
 
	?>
	
	
	<table border="0" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo"><?php
	if($bRetour) {
		// Récapitulatif de la saisie
		$id = $bRetour;
		eval("$"."oRes = new ".$classeName."($"."bRetour);");
		$qryref = dbGetArrayOneFieldFromRequete($_SESSION['sqlpag']);
		for ($i=0;$i<count($qryref);$i++) {
			if ($qryref[$i]==$id) {
				$_SESSION['pag']=$i+1;
			}
		}
		
		//$redirString = "show_".$classeName.".php?id=".$id."&adodb_next_page=".$_SESSION['pag'];
		$redirString = "/backoffice/cms/cms_tableau/create_".$classeName.".php?id=".$_GET["id"]."&adodb_next_page=".$_SESSION['pag'];
		if($listParam!=""){
			$redirString .= "&".$listParam;
		}
		if (preg_match("/id=[1-9]+/msi", $redirString) && preg_match("/id=\-1/msi", $redirString)){ // 2 fois id= , on garde la value > 0
			$redirString = preg_replace("/([?&]{1})id=-1/msi", "$1", $redirString);		
		}	
		
		
		?>
		<script language="javascript" type="text/javascript">
		window.location.href="<?php echo $redirString; ?>";
		</script>
		<?php
	}

}
?>

</table>
</form>
<?php	
}
?>
