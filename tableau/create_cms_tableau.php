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


//echo $_POST["actiontodo"];
$idSite = $_GET['idSite'];
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];



$display=intval($_GET['display']);
if(!isset($display)){
	$display=intval($_POST['display']);
}

if (is_get("id")) {
	$id=$_GET['id'];
}
else if (is_post("id")) {
	$id=$_POST['id'];
}


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

if (isset($_GET['actiontodo']) && $_GET['actiontodo']!="" ) 
	$actiontodo =  $_GET['actiontodo'];
else if ( isset($_POST['actiontodo']) && $_POST['actiontodo']!="" ) 
	$actiontodo =  $_POST['actiontodo'] ;
else $actiontodo =  "MODIF" ;


?>
<script language="JavaScript" type="text/javascript">
  function chooseFolder() {
	window.open('chooseFoldercarte.php','browser','scrollbars=yes,width=500,height=500,resizeable=yes,menubar=no');
  }
  
  function checkIfNameExists() {
	if (document.creation.nom.value == "") alert ("Vous n'avez pas saisi de nom à l'étude");
	else document.creation.submit();
  }
  
</script>
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
<link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/custom/css/fo_<?php echo $site_langue; ?>.css" rel="stylesheet" type="text/css" />

<br />
<br />
<br />
<div class="ariane"><span class="arbo2">MODULE >&nbsp;</span><span class="arbo3">Tableau&nbsp;>&nbsp;Gestion d'un tableau</span></div>

<?php

if(!$_GET['nom']&&!$_POST['nom']) {
// MODE EDITION //

?>
<form name="creation" id="creation" action="#" method="post">
	<input type="hidden" name="actiontodo" value="SAUVE">
	<input type="hidden" name="lignetodel" value="">
	<input type="hidden" name="colonnetodel" value="">

	<input type="hidden" name="colonneLignetomove" value="">
	
	
	<?php if (strlen($id) > 0 ) { ?>
	<input type="hidden" name="id" value="<?php echo  $id ; ?>" />
	<input type="hidden" name="node_id" value="<?php echo  $carte['node_id'] ; ?>" />
	<input type="hidden" name="idnode" value="<?php echo $_GET['idnode']; ?>" />
	<?php } 
		else {
	?>
	<input type="hidden" name="id" id="id" value="<?php echo $_GET['id']; ?>" />
	<input type="hidden" name="node_id" id="node_id" value="<?php echo  $id_node ; ?>" />
	<input type="hidden" name="idnode" id="idnode" value="<?php echo $_POST['idnode']; ; ?>" />
	<?php
	}
	?>

<br />
<table border="0" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">

<script language="javascript" type="text/javascript">
function validerChampsOblig() {
erreur=0;
lib="";
if (erreur == 0) {
  return true; 
}
else{
alert("Les champs suivants sont obligatoires : \n"+lib);	return false;
}
}


function ajoutLigne() {

	document.creation.actiontodo.value="AJOUTLIGNE";
	if (checkNumber(document.creation.lignetoadd.value)) 
		document.creation.submit();
	else 
		alert("Vous devez saisir un entier.");
}

function ajoutColonne() {

	document.creation.actiontodo.value="AJOUTCOLONNE"; 
	if (checkNumber(document.creation.colonnetoadd.value)) 
		document.creation.submit();
	else 
		alert("Vous devez saisir un entier.");
}

function delLigne(idLigne) {
	document.creation.actiontodo.value="DELLIGNE";
	document.creation.lignetodel.value=idLigne;
	document.creation.submit();
}


function delColonne(idColonne) {
	//alert(idColonne);
	document.creation.actiontodo.value="DELCOLONNE";
	document.creation.colonnetodel.value=idColonne;
	document.creation.submit();
}

function validerForm() {
	document.creation.actiontodo.value="SAVEALL";
	document.creation.submit();
}

function moveLigne(idColonneLigne, sens) {
	//alert(idColonneLigne);
	if (sens == "down") {
		document.creation.actiontodo.value="DOWNLIGNE";
	}
	else if (sens == "up") {
		document.creation.actiontodo.value="UPLIGNE";
	}
	else if (sens == "left") {
		document.creation.actiontodo.value="LEFTCOLONNE";
	}
	document.creation.colonneLignetomove.value=idColonneLigne;
	document.creation.submit();
}

function checkNumber(entier){
	var verif = /^[0-9]+$/;
    if (verif.exec(entier) == null){
		return false; 
	}
	else{
		return true; 
	}
    
}
 
</script>
<tr>
<td width="141" align="right" bgcolor="#E6E6E6" class="arbo">&nbsp;<b>Identifiant du tableau</b></td>

<td width="535" align="left" bgcolor="#EEEEEE" class="arbo">
<?php
if ($aNodeToSort[$i]["attrs"]["NAME"] == "id"){ // cas id = -1
	//echo lib($eKeyValue);
	echo "id généré automatiquement";
}
else {
	echo $oRes->get_id();
}
?>
</td>
</tr>
<tr>
<td width="141" align="right" bgcolor="#E6E6E6" class="arbo">&nbsp;<b>Référence du tableau</b></td>
<td width="535" align="left" bgcolor="#EEEEEE" class="arbo"><?php echo $oRes->get_reference(); ?></td>
</tr>
<tr>
<td width="141" align="right" bgcolor="#E6E6E6" class="arbo">&nbsp;<b>Libellé du composant</b></td>
<td width="535" align="left" bgcolor="#EEEEEE" class="arbo"><?php echo $oRes->get_libelle(); ?></td>
</tr>





<?php
// -------------------------- NOTRE TABLEAU ------------------------------ //
	 
	if (ereg("maj_cms_tableau", $_SERVER["HTTP_REFERER"])) {
		$strHTMLBase = getHTML ($oRes);
		
		$sql="select * from cms_tableau_perempt where cms_tableau =".$_GET["id"]." order by cms_version DESC";
		$aResTemp=dbGetObjectsFromRequete("cms_tableau_perempt", $sql); 
		
		if (sizeof($aResTemp) == 0) {
			$tableauTmp = new Cms_tableau_perempt();
			$tableauTmp->set_tableau($oRes->get_id()); 
			$tableauTmp->set_version(1);
			$tableauTmp->set_html($strHTMLBase);
			$bRetour = dbInsertWithAutoKey ($tableauTmp);
			$tableauTmp2 = new Cms_tableau_perempt();
			$tableauTmp2->set_tableau($oRes->get_id()); 
			$tableauTmp2->set_version(2);
			$tableauTmp2->set_html($strHTMLBase);
			$bRetour = dbInsertWithAutoKey ($tableauTmp2);
			$idTemp = $bRetour;
		}
		else {
			$oResTemp = $aResTemp[0];
			$idTemp = $oResTemp->get_id();
			if ($strHTMLBase == $oResTemp->get_html() || $oResTemp->get_html()=="") {
				//on ne fait rien;
			}
			else {
				$tableauTmp = new Cms_tableau_perempt();
				$tableauTmp->set_tableau($oRes->get_id()); 
				$eVersion = getCount("cms_tableau_perempt", "cms_tableau", "cms_tableau", $oRes->get_id() , "NUMBER") + 1 ;
				$tableauTmp->set_version($eVersion);
				$tableauTmp->set_html($strHTMLBase);
				$bRetour = dbInsertWithAutoKey ($tableauTmp);
				$idTemp = $bRetour;
			}
		}
	}	
	else { 
		$sql="select * from cms_tableau_perempt where cms_tableau =".$_GET["id"]." order by cms_version DESC";
		$aResTemp=dbGetObjectsFromRequete("cms_tableau_perempt", $sql); 
		$oResTemp = $aResTemp[0];
		$idTemp = $oResTemp->get_id();
	}
	$oResTemp = new Cms_tableau_perempt($idTemp);
	
	$strHTMLBase =  $oResTemp->get_html();
	
	if ($strHTMLBase == "") {
		// je récupère le html d'origine à partir de l'id
		$strHTMLBase = deleteRowspanColspan ($oRes);
		$oResTemp->set_html($strHTMLBase);
		$bRetour = dbUpdate($oResTemp);
	}
	// récupère les lignes dans un tableau
	$arrayTR = getArrayByTR ($strHTMLBase);
	//calcul nombre de colonnes
	$nombreColonnes = getNombreColonnes($arrayTR);
	//calcul nombre de lignes
	$nombreLignes = getNombreLignes($arrayTR);

// -------------------------- AJOUT  LIGNE ------------------------------ //



if ($actiontodo == "AJOUTLIGNE") {

	$idLigne = $_POST["lignetoadd"];
	if (!is_numeric($idLigne) || $idLigne > ($nombreLignes+1) || $idLigne<1 || $idLigne=="")
		$idLigne = $nombreLignes+1;
	$colorTD = getColorTD ($idLigne);
	$ligneAjout ="<tr>";
	for ($b=0;$b<$nombreColonnes;$b++) {
		$idColonne = $b+1;
		$ref = ($nombreLignes+1)."/".$idColonne;
		$ligneAjout.= "<td ".$colorTD.">".$_POST[$ref]."</td>";
		
	}
	$ligneAjout.="</tr>";
	
	$ligneTR = "";
	$ligneAllTR = "";
	 
	for ($l=0;$l<($idLigne-1);$l++) {
		$ligneTR=$arrayTR[$l];
		$ligneAllTR.=$ligneTR;
	}
	$ligneAllTR.=$ligneAjout;
	
	for ($l=($idLigne-1);$l<$nombreLignes;$l++) {
		$ligneTR=$arrayTR[$l];
		$ligneAllTR.=$ligneTR;
	}
	 
	
	
	$oResTemp->set_html( $ligneAllTR);
	$bRetour = dbUpdate($oResTemp);
	if ($bRetour)
		echo "Ajout de la ligne <b>".$idLigne."</b> effectuée<br><br><br>";
	else
		echo "erreur";


	$strHTMLBase= getHTMLTemp ($oResTemp);
	$arrayTR = getArrayByTR ($strHTMLBase);
	//calcul nombre de colonnes
	$nombreColonnes = getNombreColonnes($arrayTR);
	//calcul nombre de lignes
	$nombreLignes = getNombreLignes($arrayTR);

} // -------------------------- AJOUT  COLONNE ------------------------------ //



else if ($actiontodo == "AJOUTCOLONNE") {

	$idColonne = $_POST["colonnetoadd"];
	if (!is_numeric($idColonne) || $idColonne > ($nombreColonnes+1) || $idColonne<1 || $idColonne=="")
		$idColonne = $nombreColonnes+1;
	$ligneAllTR = "";
	
	
	for ($i=0; $i<$nombreLignes;$i++){
		$ligneTR = $arrayTR[$i];
		$ligneAll ="";
		$ligneAllTR ="";
		$aItems = array();
		$idLigne = $i+1;
		$ref = $idLigne."/".($nombreColonnes+1);
		
		preg_match_all  ("|<td[^>]*>(.*)<\/td>|U", $ligneTR, $aItems);
		$arrayTDValue = $aItems[1];
		$arrayTDBalise = $aItems[0];
		$idLigne = $i+1; 
		
		
		for ($l=0;$l<($idColonne-1);$l++) {
			$ligneTD=$arrayTDBalise[$l];
			$ligneAll.=$ligneTD;
		}
		
		$colorTD = getColorTD ($idLigne);
		if ($i==0) $ligneTD = "<td ".$colorTD."><b>".$_POST[$ref]."</b></td>";
		else $ligneTD = "<td ".$colorTD.">".$_POST[$ref]."</td>";
		$ligneAll.=$ligneTD;
		
		for ($l=($idColonne-1);$l<$nombreColonnes;$l++) {
			$ligneTD=$arrayTDBalise[$l];
			$ligneAll.=$ligneTD;
		}
		
		$ligneAllAll.= "<tr>".$ligneAll;
		$ligneAllAll.= "</tr>";

	}

	$oResTemp->set_html($ligneAllAll);
	$bRetour = dbUpdate($oResTemp);
	if ($bRetour)
		echo "Ajout de la colonne <b>".$idColonne."</b> effectuée<br><br><br>";
	else
		echo "erreur";

	$strHTMLBase= getHTMLTemp ($oResTemp);
	$arrayTR = getArrayByTR ($strHTMLBase);
	$nombreColonnes = getNombreColonnes($arrayTR);
	$nombreLignes = getNombreLignes($arrayTR);

} 
// -------------------------- INVERSION UPLIGNE ------------------------------ //
else if ($actiontodo == "UPLIGNE") { 
	
	$strHTMLBase= getHTMLTemp ($oResTemp);
	$arrayTR = getArrayByTR ($strHTMLBase);
	
	$iligneToUp = $_POST["colonneLignetomove"]-1;
	$iligneToDown = $_POST["colonneLignetomove"]-2;
	
	$ligneToDown = $arrayTR[$iligneToDown];
	$ligneToUp = $arrayTR[$iligneToUp];
	$ligneAllTR = "";
	for ($i=0; $i<sizeof($arrayTR);$i++){
		if ($i == $iligneToDown) {
			$ligneAllTR.=$ligneToUp;
		}
		else if ($i == $iligneToUp) {
			$ligneAllTR.=$ligneToDown;
		}
		else {
			$ligneAllTR.=$arrayTR[$i];
		}
	}
	
	$oResTemp->set_html($ligneAllTR);
	$bRetour = true;
	$bRetour = dbUpdate($oResTemp);
	if ($bRetour)
		echo "Inversion de la ligne <b>".$_POST["colonneLignetomove"]."</b> avec la ligne <b>".($_POST["colonneLignetomove"]-1)."</b><br><br><br>";
	else
		echo "erreur";


	$strHTMLBase = $oResTemp->get_html();

	$arrayTR = getArrayByTR ($strHTMLBase);
	//calcul nombre de colonnes
	$nombreColonnes = getNombreColonnes($arrayTR);
	//calcul nombre de lignes
	$nombreLignes = getNombreLignes($arrayTR);

} 
else if ($actiontodo == "DOWNLIGNE") {

	$iligneToUp = $_POST["colonneLignetomove"];
	$iligneToDown = $_POST["colonneLignetomove"]-1;
	
	$ligneToDown = $arrayTR[$iligneToDown];
	$ligneToUp = $arrayTR[$iligneToUp];
	$ligneAllTR = "";
	for ($i=0; $i<sizeof($arrayTR);$i++){
		if ($i == $iligneToDown) {
			$ligneAllTR.=$ligneToUp;
		}
		else if ($i == $iligneToUp) {
			$ligneAllTR.=$ligneToDown;
		}
		else {
			$ligneAllTR.=$arrayTR[$i];
		}
	}
	
	$oResTemp->set_html($ligneAllTR);
	$bRetour = true;
	$bRetour = dbUpdate($oResTemp);
	if ($bRetour)
		echo "Inversion de la ligne <b>".$_POST["colonneLignetomove"]."</b> avec la ligne <b>".($_POST["colonneLignetomove"]-1)."</b><br><br><br>";
	else
		echo "erreur";


	$strHTMLBase = $oResTemp->get_html();

	$arrayTR = getArrayByTR ($strHTMLBase);
	//calcul nombre de colonnes
	$nombreColonnes = getNombreColonnes($arrayTR);
	//calcul nombre de lignes
	$nombreLignes = getNombreLignes($arrayTR);

} 
else if ($actiontodo == "LEFTCOLONNE") {

	$icolToLeft = $_POST["colonneLignetomove"]-1;
	$icolToRight = $_POST["colonneLignetomove"]-2;
	
	$ligneAllTR = "";
	 
	for ($j=0; $j<sizeof($arrayTR);$j++) {
		$ligneTR = $arrayTR[$j];
		$aItems = array();
		preg_match_all  ("|<td[^>]*>(.*)<\/td>|U", $ligneTR, $aItems);
		$arrayTDValue = $aItems[1];
		$arrayTDBalise = $aItems[0];
		
		$colToLeft = $arrayTDBalise[$icolToLeft];
		$colToRight = $arrayTDBalise[$icolToRight];
		
		$ligneTR = "<tr>";
		for ($i=0;$i<sizeof($arrayTDBalise);$i++) {
			
			if ($i == $icolToLeft) {
				$ligneTR.=$colToRight;
			}
			else if ($i == $icolToRight) {
				$ligneTR.=$colToLeft;
			}
			else {
				$ligneTR.=$arrayTDBalise[$i];
			}
			
		}
		$ligneAllTR.=$ligneTR."</tr>";
		
		//echo "<br /><br>";
	}
		
	
	
	$oResTemp->set_html($ligneAllTR);
	$bRetour = dbUpdate($oResTemp);
	if ($bRetour)
		echo "Inversion de la colonne <b>".$_POST["colonneLignetomove"]."</b> avec la colonne <b>".($_POST["colonneLignetomove"]-1)."</b><br><br><br>";
	else
		echo "erreur";


	$strHTMLBase = $oResTemp->get_html();

	$arrayTR = getArrayByTR ($ligneAllTR);
	//calcul nombre de colonnes
	$nombreColonnes = getNombreColonnes($arrayTR);
	//calcul nombre de lignes
	$nombreLignes = getNombreLignes($arrayTR);

} 
// -------------------------- SUPPRESSION LIGNE ------------------------------ //
else if ($actiontodo == "DELLIGNE") {

	
	$idLignetoDel = $_POST["lignetodel"];
	
	$ligneAllTR = "";
	for ($i=0; $i<sizeof($arrayTR);$i++){
		$ligneTR = $arrayTR[$i];
		$ligneTR1 = $arrayTR[0];
		$ligneTR2 = $arrayTR[1];
		if ($idLignetoDel == 2 &&($i+1)==1) {
			// j'enleve les rowspans de la ligne 1
			$aItems = array();
			preg_match_all  ("|<td[^>]*>(.*)<\/td>|U", $ligneTR, $aItems);
			$arrayTDValue = $aItems[1];
			$ligneTRsansRowspan = "<tr>";
			for ($l=0;$l<sizeof($arrayTDValue);$l++) {
				preg_match_all  ("|<td[^>]*>(.*)<\/td>|U", $ligneTR, $aItems);
				$arrayTDValue = $aItems[1];
				$arrayTDBalise = $aItems[0];
				
				$nbRowspan = getRowspan ( "", $arrayTDBalise[$l]);
				$ligneTD = $arrayTDBalise[$l];
				//echo "--------------------------------------".$arrayTDBalise[$l].$nbRowspan."<br>";
				if ($nbRowspan > 1) {
					$ligneTD = str_replace("rowspan=\"".$nbRowspan."\"", "", $ligneTD);
					$ligneTD = str_replace("rowspan=".$nbRowspan."", "", $ligneTD);
				}
				$ligneTRsansRowspan.=$ligneTD;
				
				
			}
			$ligneTRsansRowspan.= "</tr>";
			$ligneTR= str_replace ($ligneTR, $ligneTRsansRowspan, $ligneTR);
			
		}

		if (($i+1) == $idLignetoDel ) {
			
			if ($idLignetoDel == 2) {
				// je vide la ligne 2 mais je ne la supprime pas.
				$ligneTRvide = "<tr>";
				for ($j=0; $j<$nombreColonnes;$j++) {
					$ligneTRvide.= "<td class=\"technical_table_td1_1\">&nbsp;</td>";
				}
				$ligneTRvide.= "</tr>";
				$ligneTR = str_replace ($ligneTR, $ligneTRvide, $ligneTR);
			}
			else {
				$ligneTR = str_replace ($ligneTR, "", $ligneTR);
			}
		}
		$ligneAllTR.=$ligneTR;
	}
	
	$oResTemp->set_html($ligneAllTR);
	$bRetour = dbUpdate($oResTemp);
	if ($bRetour)
		echo "Suppression de la ligne <b>".$idLigne."</b> effectuée<br><br><br>";
	else
		echo "erreur";


	$strHTMLBase= getHTMLTemp ($oResTemp);
	$arrayTR = getArrayByTR ($strHTMLBase);
	$nombreColonnes = getNombreColonnes($arrayTR);
	$nombreLignes = getNombreLignes($arrayTR);
	
}
else if ($actiontodo == "DELCOLONNE") {

	$idColonnetoDel = $_POST["colonnetodel"]-1;
	//echo "je del la colonne ".$idColonnetoDel;
	
	$ligneAllAll = "";
	$arrayROWSPAN = array ();
	$arrayROWSPANValue = array ();
	//echo "à suprimer ".$idColonnetoDel;
	$arrayTDNbRowspanTemp = array(); // array taille nombre de colonnes
	for ($i=0; $i<$nombreLignes;$i++){
		$ligneTR = $arrayTR[$i];
		$ligneAll ="";
		$ligneAllTR ="";
		$aItems = array();
		
		preg_match_all  ("|<td[^>]*>(.*)<\/td>|U", $ligneTR, $aItems);
		$arrayTDValue = $aItems[1];
		$arrayTDBalise = $aItems[0];
		$idLigne = $i+1; 
		$idColonne = 0;
		
		for ($l=0;$l<sizeof($arrayTDValue);$l++) {
			if ($l==$idColonnetoDel) {
				$ligneTD="";
			}
			else {
				$ligneTD=$arrayTDBalise[$l];
			}
			
			$ligneAll.=$ligneTD;
		}
	
		$ligneAllAll.= "<tr>".$ligneAll;
		$ligneAllAll.= "</tr>";

	}
	$oResTemp->set_html($ligneAllAll);

 	$bRetour = dbUpdate($oResTemp);
	$bRetour = true;
	if ($bRetour)
		echo "Suppression de la colonne <b>".$idLigne."</b> effectuée<br><br><br>";
	else
		echo "erreur";  
	$strHTMLBase= getHTMLTemp ($oResTemp);
	$arrayTR = getArrayByTR ($strHTMLBase);
	$nombreColonnes = getNombreColonnes($arrayTR);
	$nombreLignes = getNombreLignes($arrayTR);
}
else if ($actiontodo == "SAVEALL") {
	$htmlTemp = $oResTemp->get_html();
    $oRes->set_htmltmp($htmlTemp);
	$htmlToSave = initRowspanColspan($htmlTemp);
	$htmlToSave = checkCss($htmlToSave); 
	$oRes->set_html($htmlToSave);
	$bRetour = dbUpdate($oRes);
	
	
	if ($bRetour)
		echo "Modification enregistrée <br><br><br>";
	else
		echo "erreur";
?>
<div class="arbo"><u><b><?php if($status!="") echo $status; ?></b></u></div><br>
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
		
		$redirString = "show_".$classeName.".php?id=".$_GET["id"]."&adodb_next_page=".$_SESSION['pag'];
		if($listParam!=""){
			$redirString .= "&".$listParam;
		}
		if (ereg("id=[1-9]+", $redirString) && ereg("id=-1", $redirString)){ // 2 fois id= , on garde la value > 0
			$redirString = ereg_replace("([?&]{1})id=-1", "\\1", $redirString);		
		}	
		
		//echo $oRes->get_html();
?>
<script language="javascript" type="text/javascript">
	window.location.href="<?php echo $redirString; ?>";
</script>
<?php
	}
}



?>
<tr>
<td width="141" align="right" bgcolor="#E6E6E6" class="arbo">&nbsp;</td>
<td width="535" align="left" bgcolor="#EEEEEE" class="arbo"><?php echo $nombreColonnes; ?> colonne(s)<br /><?php echo $nombreLignes; ?> ligne(s)<br /></td>
</tr>
</table>
<br /><br />
<table border="0" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
<tr><td class="arbo">
<?php

// -------------------------- FIN AJOUT  LIGNE ------------------------------ //







// -------------------------- AFFICHAGE TABLEAU ------------------------------ //
$htmlToSave = "";
$ligneAllAll = "";
$arrayROWSPAN = array ();
$arrayROWSPANValue = array ();

$nombreColonnesAvecActions =  getNbColonnesActionsDroite() + $nombreColonnes;
for ($i=0; $i<$nombreLignes;$i++){
	$ligneTR = $arrayTR[$i];
	$ligneAll ="";
	$ligneAllTR ="";
	$aItems = array();
	
	preg_match_all  ("|<td[^>]*>(.*)<\/td>|U", $ligneTR, $aItems);
	$arrayTD = $aItems[1];
	$idLigne = $i+1;
	//if ($i == 1) pre_dump($aItems[0]);
	$cptArrayTD = 0;
	$cptArrayTDtoInsert = -1;
	
	// Gestion des PDF
	if ($i<2) $ligneAllTR= "<td>&nbsp;</td>";
	
	else {
		$valuePDF = strchr($aItems[1][0], "PDF-");
		$toDel = strchr($valuePDF, "-PDF");
		$valuePDF = str_replace($toDel, "", $valuePDF);
		$valuePDF = str_replace("PDF-", "", $valuePDF);
		$ligneAllTR.= "<td width=\"100px\"><input type=\"text\" id=\"".$idLigne."/PDF\" name=\"".$idLigne."/PDF\" value=\"".$valuePDF."\" class=\"arbo\" size=\"10\" ></td>";
	}
	
	for ($l=0;$l<$nombreColonnes;$l++) {
	 	 
			$ligneTD = $aItems[0][$l];
			$idColonne = $l+1;
			if ($valuePDF != "") {
				$valueSansPDF = str_replace ("PDF-".$valuePDF."-PDF", "", $arrayTD[$l]); 
			}
			
			$ligneTD = str_replace (">".$arrayTD[$l]."<", ">".$arrayTD[$l]."<input type=\"hidden\" id=\"".$idLigne."/".$idColonne."\" name=\"".$idLigne."/".$idColonne."\" value=\"".$valueSansPDF."\" size=\"10\" ><", $ligneTD);

			$ligneTDcontenu = $aItems[1][$l];
			if ($l==0) {
				if (!ereg ("<b>", $ligneTDcontenu) && ltrim($ligneTDcontenu)!="") {
					$ligneTD = str_replace ($aItems[1][$l], "<b>".$aItems[1][$l]."</b>", $ligneTD);
				}
				if ($valuePDF!="") {
					$urlPDF="<a href=\"http://".$_SERVER['HTTP_HOST']."/backoffice/cms/utils/telecharger.php?chemin=/custom/upload/composant/tableaux/&file=/custom/upload/composant/tableaux/".$valuePDF.".pdf\" target=\"_blank\">";
					$ligneTD = str_replace ("PDF-".$valuePDF."-PDF", $urlPDF, $ligneTD);
					$ligneTD = $ligneTD."</a>";
				} 
			}
			 
			
			$ligneAllTR.= $ligneTD; 
	}

	//if ($i == 1) {
	$colorTD = getColorTD ($idLigne);
	
	$idColonne = $nombreColonnes+1;
	
	$ligneAllAll.= "<tr>".$ligneAllTR;
	$ligneAllAll.= getHTMLActionsDroite ($idLigne, $idColonne, $colorTD, $id, $nombreLignes);
	$ligneAllAll.= "</tr>";
	
	$htmlToSave.= $ligneAllTR;
	//}
}

$ligneAjout = getHTMLAjout ($nombreLignes, $nombreColonnes);
$ligneActionsHaut = getHTMLActionsHaut ($arrayTR, $colorTD);

$ligneAll2 = "";

//changement des classes 


$ligneAllAll= checkCss($ligneAllAll);

//$ligneAll2.= "<TABLE width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#AC89B8\"><TR><TD align=\"center\" valign=\"middle\"><TABLE width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\">";
$ligneAll2.= "<TABLE border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#FFFFFF\"><TR><TD align=\"center\" valign=\"middle\">\n";
$ligneAll2.= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"technical_table\">\n";
$ligneAll2.=$ligneActionsHaut."\n";
$ligneAll2.="<!-- DEBUT TABLEAU -->\n";
$ligneAll2.=$ligneAllAll."\n";
$ligneAll2.="<!-- FIN TABLEAU -->\n";
$ligneAll2.=$ligneAjout."\n";
//$ligneAll2.="</TABLE>";
$ligneAll2.="</TABLE></TD></TR></TABLE>\n";
echo $ligneAll2;
?>
</td></tr>


</table>


<table border="0" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">

<tr>
  <td colspan="2"  align="center" class="arbo"> <center>       
  	<a href="maj_cms_tableau.php?<?php echo $_SERVER["QUERY_STRING"]; ?>"><< Retour à la fiche du tableau</a>     &nbsp;&nbsp;&nbsp;&nbsp;      
	<a href="javascript:validerForm()">Suite (enregistrer) >></a>
	</center>
 </tr>
</table>


</form>
<?php	
}
?>
