<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
if (!isset($classeName)){
	$classeName = preg_replace('/[^_]*_(.*)\.php/si', '$1', basename($_SERVER['PHP_SELF']));
}
 
//------------------------------------------------------------------------------------------------------

// Formulaire de saisie 

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/lib.inc.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
 include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

// Chargement de la classe Upload
require_once('cms-inc/lib/fileUpload/upload.class.php');

//cas de parametres dans l'url
if($_SESSION['listParam']!="") {
	$listParam = $_SESSION['listParam'];
}
else {
	$_SESSION['listParam']=$_SERVER['QUERY_STRING'];
	$listParam=$_SESSION['listParam'];
}
// Instanciation d'un nouvel objet "upload"
$Upload = new Upload();

$Upload-> MaxFilesize = strval(12*1024);
// -----------------------------------

$actiontodo = ( $_POST['actiontodo']!="SAUVE" ) ? "MODIF" : "SAUVE" ;

 
// enregistrement à modifier
//$id=intval($_GET['id']);


$display=intval($_GET['display']);
if(!isset($display)){
	$display=intval($_POST['display']);
}

if (is_get("id")) {
	$id=$_GET['id'];
	$qryref = dbGetArrayOneFieldFromRequete($_SESSION['sqlpag']);
	for ($i=0;$i<count($qryref);$i++) {
		if ($qryref[$i]==$id) {
		//echo $qryref[$i].$i."<br>";
		$_SESSION['pag']=$i+1;
		}
	}
}
// Pour calcul de l'id par rapport à la recherche et récupération de la position dans la navigation par rapport à cet id
else {
	$qryref = dbGetArrayOneFieldFromRequete($_SESSION['sqlpag']);
	$id=$qryref[$id-1];
	for ($i=0;$i<count($qryref);$i++) {
		if ($qryref[$i]==$id) {
			$_SESSION['pag']=$i+1;
		}
	}
	if (is_get("adodb_next_page")) {
		$id=$_GET['adodb_next_page'];
	}
	else {
		$qryref = dbGetArrayOneFieldFromRequete($_SESSION['sqlpag']);
		$id=intval($_POST['id']);
		for ($i=0;$i<count($qryref);$i++) {
			if ($qryref[$i]==$id) {
				$_SESSION['pag']=$i+1;
			}
		}
	}
}
if (is_post("id")) {
	$id=$_POST['id'];
}

// activation du menu : déroulement
activateMenu("gestion".$classeName); 

 

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
	//eval("$"."oRes = new ".$classeName."();");
}
elseif ( $operation == "UPDATE" ) { // Mode mise à jour 
}
elseif ( $operation == "ETAPE2" ) { // Mode mise à jour
	//echo $operation;
}
else{ // Mode acces par display
	 
	eval("$"."oRes = new ".$classeName."();");
}  

$aClasseTitle = getTagPosts($_POST, "fAssoCms_title_Classe_");
 

$sXML = $oRes->XML;
xmlStringParse($sXML);
 
 
?>
<script>
	// retour à la page précédente
	// ce retour doit être posté pour conserver le type
	function retour()
	{
		document.add_<?php echo $classePrefixe; ?>_form.action = "<?php echo $_POST['urlRetour']; ?><?php if($listParam!="") echo "&".$listParam;?>"; 
		document.add_<?php echo $classePrefixe; ?>_form.submit(); 
	}
</script>
<form name="add_<?php echo $classePrefixe; ?>_form" id="add_<?php echo $classePrefixe; ?>_form" enctype="multipart/form-data" method="post">
<input type="hidden" name="classeName" id="classeName"  value="<?php echo $classeName; ?>">
<input type="hidden" name="postnumberone" value="XXXX">
<input type="hidden" name="postnumber2" value="XXXX2">

<?php
//////////////////////////////////
// A VOIR
//////////////////////////////////
// le premier champ posté est "supprimé" quand il y a un caractère spécial dans la description
// (en l'occurence un ’)
// pourtant dans l'auprepend tous les champs postés sont correctement nettoyés
// j'ai mis ici deux champ postnumberone et postnumber2
// ils ne servent à rien sinon à tester que le premier champ est systématiquement supprimé 
// quand il y a un caractère spécial dans la description
//////////////////////////////////
// A VOIR
/////////////////////////////////

$status = '';
if($actiontodo == "SAUVE") { // MODE ENREGISTREMENT
 

	// maj BDD
	if ($operation == "UPDATE") {
    	$oTitle = new Cms_title($id);
		$oTitle->set_nom($_POST["fTit_nom"]); 
		$oTitle->set_cms_site($_SESSION['idSite_travail']);
		$oTitle->set_statut($_POST["fTit_statut"]);
		$bRetour = dbUpdate($oTitle);
		
		// on efface les assos 
		$sql = "select * from cms_assotitleclasse where xtc_cms_title = ".$id.""; 
		$aAssotitle = dbGetObjectsFromRequete ("cms_assotitleclasse", $sql);
		if (sizeof($aAssotitle) > 0) {
			for ($k=0; $k<sizeof($aAssotitle); $k++) {
				$oAssotitle = $aAssotitle[$k];
				$bDelete = dbDelete ($oAssotitle);
			}
		}
		
		// puis on les recrée
		
		$aClasseTitle = getTagPosts($_POST, "fAssoCms_title_Classe_"); 
		if ($aClasseTitle) {
			for ($k = 0; $k<sizeof($aClasseTitle); $k++) {
				$oClasse = getObjectById("classe", $aClasseTitle[$k]); 
				$sTempClasse = $oClasse->get_nom();   
				$aAssoClasse = getTagPosts($_POST, "fAsso".ucfirst($sTempClasse)."_");  
				if ($aAssoClasse) {
					for ($j = 0; $j<sizeof($aAssoClasse); $j++) {
						$oTitleAsso = new Cms_assotitleclasse();
						$oTitleAsso->set_cms_title($bRetour);
						$oTitleAsso->set_classe($aClasseTitle[$k]);
						$oTitleAsso->set_classeid($aAssoClasse[$j]); 
						$bRetour2 = dbInsertWithAutoKey($oTitleAsso);
					}
				}
				else { 
					$oTitleAsso = new Cms_assotitleclasse();
					$oTitleAsso->set_cms_title($bRetour);
					$oTitleAsso->set_classe($aClasseTitle[$k]);
					$oTitleAsso->set_classeid(-1); 
					$bRetour2 = dbInsertWithAutoKey($oTitleAsso); 
				}
				 
				   
			}
		}
		else {
			$oTitleAsso = new Cms_assotitleclasse();
			$oTitleAsso->set_cms_title($bRetour);
			$oTitleAsso->set_classe(-1);
			$oTitleAsso->set_classeid(-1); 
			$bRetour2 = dbInsertWithAutoKey($oTitleAsso); 
		} 
	 
			
	} else if ($operation == "INSERT") { 
		if (getCount_where($classeName, array("tit_nom", "tit_cms_site"), array($_POST["fTit_nom"], $_SESSION['idSite_travail']), array("TEXT", "NUMBER")) ==  0){
		$oTitle = new Cms_title();
		$oTitle->set_nom($_POST["fTit_nom"]); 
		$oTitle->set_cms_site($_SESSION['idSite_travail']);
		$oTitle->set_statut($_POST["fTit_statut"]);
		$bRetour = dbInsertWithAutoKey($oTitle);
			if ($bRetour) {
				$aClasseTitle = getTagPosts($_POST, "fAssoCms_title_Classe_"); 
				if ($aClasseTitle) {
					for ($k = 0; $k<sizeof($aClasseTitle); $k++) {
						$oClasse = getObjectById("classe", $aClasseTitle[$k]); 
						$sTempClasse = $oClasse->get_nom();   
						$aAssoClasse = getTagPosts($_POST, "fAsso".ucfirst($sTempClasse)."_");  
						if ($aAssoClasse) {
							for ($j = 0; $j<sizeof($aAssoClasse); $j++) {
								$oTitleAsso = new Cms_assotitleclasse();
								$oTitleAsso->set_cms_title($bRetour);
								$oTitleAsso->set_classe($aClasseTitle[$k]);
								$oTitleAsso->set_classeid($aAssoClasse[$j]); 
								$bRetour2 = dbInsertWithAutoKey($oTitleAsso);
							}
						}
						else { 
							$oTitleAsso = new Cms_assotitleclasse();
							$oTitleAsso->set_cms_title($bRetour);
							$oTitleAsso->set_classe($aClasseTitle[$k]);
							$oTitleAsso->set_classeid(-1); 
							$bRetour2 = dbInsertWithAutoKey($oTitleAsso); 
						}
						 
						   
					}
				}
				else {
					$oTitleAsso = new Cms_assotitleclasse();
					$oTitleAsso->set_cms_title($bRetour);
					$oTitleAsso->set_classe(-1);
					$oTitleAsso->set_classeid(-1); 
					$bRetour2 = dbInsertWithAutoKey($oTitleAsso); 
				} 
			}
		}
		else {
			$status.= "Le title ".$_POST["fTit_nom"]." existe déjà.<br>";
		}
		
		  
	}

	if($bRetour2) {
		// type de record enregistré
	}
	else
		$status.= $classeName.' - Erreur lors de '. ( ($operation == "INSERT") ? "l'ajout" : "la modification" )."<br><br>";
		$status.="<a href=\"javascript:retour()\" class=\"arbo\"></a>";

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
		if ($classeName == "cms_tableau") {
			$redirString = "/backoffice/cms/cms_tableau/show_".$classeName.".php?id=".$id."&adodb_next_page=".$_SESSION['pag'];
		}
		else {
			$redirString = "show_".$classeName.".php?id=".$id."&adodb_next_page=".$_SESSION['pag'];
		}
		if($listParam!=""){
			$redirString .= "&".$listParam;
		}
		if (ereg("id=[1-9]+", $redirString) && ereg("id=-1", $redirString)){ // 2 fois id= , on garde la value > 0
			$redirString = ereg_replace("([?&]{1})id=-1", "\\1", $redirString);		
		}	
?>
<script language="javascript" type="text/javascript">
	window.location.href="<?php echo $redirString; ?>";
</script>
 <?php if ($aCustom["Sendmail"] == true)  { ?>
			<tr><td colspan="2" class="arbo"><?php include ("send_".$classeName.".php"); ?>&nbsp;</td></tr> 
  <?php } //if ($aCustom["Sendmail"] == true) 
	} // Fin si pas d'erreur d'ajout
/*
echo '<tr><td align="center" colspan="2" bgcolor="#D2D2D2" class="arbo">';

if ($operation == "INSERT") // Pour faciliter la saisie à la chaine
	echo '<input class="arbo" type="button" value="Créer une nouvelle '.$classeName.' >>" onclick="document.location=\''.$_SERVER['PHP_SELF'].'\'">';

else // On retourne à la liste 
	echo "<script>document.location='". $_POST['urlRetour'] ."'</script>";

// si une page retour est spécifiée -> lien retour
if ($_POST['urlRetour'] != "") {
	echo '<input class="arbo" type="button" value="<< Retour à la liste" onclick="retour()">';
}
*/
?>
</table>
<?php
}
else { // MODE EDITION


	
?>
<span class="arbo2">MODULE >&nbsp;</span><span class="arbo3"><?php echo  $classeLibelle ; ?>&nbsp;>&nbsp;
<?php echo  ($operation == "INSERT") ? "Ajouter" : "Modifier" ?></span><br><br>
<script src="/backoffice/cms/js/validForm.js" type="text/javascript"></script>
<script>


	///////////////////////////////////////////////////
	// validation du formulaire
	///////////////////////////////////////////////////
	
	function validerForm()
	{
		// si le formulaire est valide
	 
				document.add_<?php echo $classePrefixe; ?>_form.operation.value = "<?php echo $operation; ?>"; 
				document.add_<?php echo $classePrefixe; ?>_form.action = "maj2_<?php echo $classeName; ?>.php<?php if($listParam!="") echo "?".$listParam;?>"; 
				document.add_<?php echo $classePrefixe; ?>_form.target = "_self";
				document.add_<?php echo $classePrefixe; ?>_form.submit();   
	}
	
	///////////////////////////////////////////////////
	// validation du mot de passe
	///////////////////////////////////////////////////
	function checkPwd(id){
		saisie = document.getElementById(id).value;
		confirmation = document.getElementById(id+"conf").value;
		if (saisie != confirmation){
			alert("Les valeurs saisies pour le mot de passe et sa confirmation ne correspondent pas.\nVeuillez corriger votre saisie");
		}
	}

</script>
<!-- MODE EDITION -->

<input type="hidden" name="operation" value="<?php echo $operation; ?>">
<input type="hidden" name="urlRetour" value="<?php echo $_POST['urlRetour']; ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="actiontodo" value="SAUVE">
<input type="hidden" name="sChamp" value="">

<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
<?php



// tableau contenant les champs et pattern à vérifier
$ControlePattern = false;
$ControlePatternFields = array();
$ControlePatternValues = array();
	echo "<tr>\n";
	echo "<td width=\"141\" align=\"right\" bgcolor=\"#E6E6E6\" class=\"arbo\">";
	echo "&nbsp;<u><b>Title</b></u>";
	echo "</td>\n";
	echo "<td width=\"535\" align=\"left\" bgcolor=\"#EEEEEE\" class=\"arbo\">\n";
	echo "&nbsp;".$_POST["fTit_nom"]."<input type=\"hidden\" name=\"fTit_nom\" id=\"fTit_nom\" class=\"arbo\" size=\"80\" value=\"".$_POST["fTit_nom"]."\"  />";
	echo "</td>\n";
	echo "</tr>\n";


if ($aClasseTitle) { 
	for ($i=0;$i<count($aClasseTitle);$i++){
		echo "<input type=\"hidden\" name=\"fAssoCms_title_Classe_".$aClasseTitle[$i]."\" id=\"fAssoCms_title_Classe_".$aClasseTitle[$i]."\" class=\"arbo\" size=\"80\" value=\"".$aClasseTitle[$i]."\"  />";
		$oClasse = getObjectById("classe", $aClasseTitle[$i]); 
		$sTempClasse = $oClasse->get_nom(); 
		eval("$"."oTemp = new ".$sTempClasse."();");
		$aForeign = dbGetObjects($sTempClasse);
		
		 
		// cas des deroulant d'id, pointitlee vers foreign
		//$sXML = $aForeign[0]->XML;
		$sXML = $oTemp->XML;
	
		unset($stack);
		$stack = array();
		xmlStringParse($sXML); 
		$foreignName = $stack[0]["attrs"]["NAME"];
		$foreignPrefixe = $stack[0]["attrs"]["PREFIX"];
		$foreignNodeToSort = $stack[0]["children"]; 
		$tempIsAbstractForeign = false;
		$tempForeignAbstract = "";
		$tempIsDisplayForeign = false;
		$tempForeignDisplay = "";
		$tempAsso = $stack[0]["attrs"]["NAME"];
		$tempAssoPrefixe = $stack[0]["attrs"]["PREFIX"];
		$sTempClasse = "";
		$tempAssoOut = ""; 
		$bCms_site = false;	  
		if(is_array($foreignNodeToSort)){
			foreach ($foreignNodeToSort as $nodeId => $nodeValue) {				
				if ($nodeValue["attrs"]["NAME"] == $oTemp->getAbstract()){					
					if (($nodeValue["attrs"]["FKEY"] != "") && isset($nodeValue["attrs"]["FKEY"])){
						$tempIsAbstractForeign = true;
						$tempForeignAbstract = $nodeValue["attrs"]["FKEY"];
						//break;
					}
				}
				if ($nodeValue["attrs"]["NAME"] == $oTemp->getDisplay()){					
					if (($nodeValue["attrs"]["FKEY"] != "") && isset($nodeValue["attrs"]["FKEY"])){
						$tempIsDisplayForeign = true;
						$tempForeignDisplay = $nodeValue["attrs"]["FKEY"];
						//break;
					}
				}
				if (($nodeValue["attrs"]["FKEY"] != "") && isset($nodeValue["attrs"]["FKEY"])){
					if ($nodeValue["attrs"]["FKEY"] == $classeName){	
						$sTempClasse = $nodeValue["attrs"]["FKEY"]; // obvious
					}
					else{
						$tempAssoOut = $nodeValue["attrs"]["FKEY"]; // 
					}
				}
				if ($nodeValue["attrs"]["NAME"] == "cms_site"){					
					$bCms_site = true;	  
				}
			}
		}
		
	
		
		echo "<tr>\n";
		echo "<td width=\"141\" align=\"right\" bgcolor=\"#E6E6E6\" class=\"arbo\">";
		echo "&nbsp;<u><b>".$tempAsso."</b></u>";
		echo "</td>\n";
		echo "<td width=\"535\" align=\"left\" bgcolor=\"#EEEEEE\" class=\"arbo\">\n";
		// on connait $tempAssoOut -- on recommence la recherche de foreign vers $tempAssoOut
		
		$sTempClasse = $tempAsso;
	
		eval("$"."oTemp = new ".$sTempClasse."();");
		$aForeign = dbGetObjects($sTempClasse);
		
		// cas des deroulant d'id, pointitlee vers foreign
		//$sXML = $aForeign[0]->XML;
		$sXML = $oTemp->XML;					
	
		unset($stack);
		$stack = array();
		xmlStringParse($sXML);
	
		$foreignName = $stack[0]["attrs"]["NAME"];
		$foreignPrefixe = $stack[0]["attrs"]["PREFIX"];
		$foreignNodeToSort = $stack[0]["children"];
		
		$tempIsAbstractForeign = false;
		$tempForeignAbstract = "";
		$tempIsDisplayForeign = false;
		$tempForeignDisplay = "";
		if(is_array($foreignNodeToSort)){
			foreach ($foreignNodeToSort as $nodeId => $nodeValue) {				
				if ($nodeValue["attrs"]["NAME"] == $oTemp->getAbstract()){					
					if (($nodeValue["attrs"]["FKEY"] != "") && isset($nodeValue["attrs"]["FKEY"])){
						$tempIsAbstractForeign = true;
						$tempForeignAbstract = $nodeValue["attrs"]["FKEY"];
						//break;
					}
				}
				if ($nodeValue["attrs"]["NAME"] == $oTemp->getDisplay()){					
					if (($nodeValue["attrs"]["FKEY"] != "") && isset($nodeValue["attrs"]["FKEY"])){
						$tempIsDisplayForeign = true;
						$tempForeignDisplay = $nodeValue["attrs"]["FKEY"];
						//break;
					}
				}
			}
		}
		
		$sql = "select * from ".$sTempClasse." ";
		if ($bCms_site) $sql .= " where ".$foreignPrefixe."_cms_site = ".$_SESSION['idSite_travail']; 
		$sql .= " order by ".$foreignPrefixe."_".$oTemp->getAbstract(); 
		$aForeign = dbGetObjectsFromRequete ($sTempClasse, $sql);
		
		for ($iForeign=0;$iForeign<count($aForeign);$iForeign++){
			$oForeign = $aForeign[$iForeign];
			if ($oTemp->getGetterStatut() != "none"){
				eval ("$"."tempStatus = $"."oForeign->".strval($oTemp->getGetterStatut())."();");					
			}
			else{
				$tempStatus = DEF_ID_STATUT_LIGNE;
			}
			eval ("$"."tempId = $"."oForeign->get_id();");
			
			if ($tempStatus == DEF_ID_STATUT_LIGNE){
				// test sur select. chercher id de la fiche en cours ($id) et id du foreign en cours ($tempId) dans Asso.
	
				echo "<input type=\"checkbox\" name=\"fAsso".ucfirst($sTempClasse)."_".$tempId."\" id=\"fAsso".ucfirst($sTempClasse)."_".$tempId."\" value=\"".$tempId."\" ";
				
				if (getCount_where("cms_assotitleclasse", array("xtc_cms_title", "xtc_classe", "xtc_classeid"), array($id,$aClasseTitle[$i], $tempId), array("NUMBER","NUMBER","NUMBER")) >0){
					echo " checked";
				}					
				echo ">";
	
			
				if ($tempIsDisplayForeign){
					eval("$"."oForeignDisplay = new ".$tempForeignDisplay."($"."oForeign->get_".strval($oTemp->getDisplay())."());");
					eval ("$"."itemValue = $"."oForeignDisplay->get_".strval($oForeignDisplay->getDisplay())."();");
					$itemValueShort = substr($itemValue, 0, 50);
						if (strlen($itemValue) > 50 ) 
							$itemValueShort.= " ... ";
						echo $itemValueShort;	
				}
				else{
					eval ("$"."itemValue = $"."oForeign->get_".strval($oTemp->getDisplay())."();");
					$itemValueShort = substr($itemValue, 0, 50);
						if (strlen($itemValue) > 50 ) 
							$itemValueShort.= " ... ";
						echo $itemValueShort;	
				}
				
				if ($oTemp->getDisplay() != $oTemp->getAbstract()){
					echo " - ";
					if ($tempIsAbstractForeign){
						eval("$"."oForeignAbstract = new ".$tempForeignAbstract."($"."oForeign->get_".strval($oTemp->getAbstract())."());");
						eval ("$"."itemValue = $"."oForeignAbstract->get_".strval($oForeignAbstract->getDisplay())."();");
						$itemValueShort = substr($itemValue, 0, 50);
						if (strlen($itemValue) > 50 ) 
							$itemValueShort.= " ... ";
						echo $itemValueShort;	
					}
					else{
						eval ("$"."itemValue = $"."oForeign->get_".strval($oTemp->getAbstract())."();");
						$itemValueShort = substr($itemValue, 0, 50);
						if (strlen($itemValue) > 50 ) 
							$itemValueShort.= " ... ";
						echo $itemValueShort;	
					}
				}
				echo "<br />\n";
			}						
		}	   
	}
}
else {
	echo "<tr>\n";
	echo "<td width=\"141\" align=\"right\" bgcolor=\"#E6E6E6\" class=\"arbo\">";
	echo "&nbsp;";
	echo "</td>\n";
	echo "<td width=\"535\" align=\"left\" bgcolor=\"#EEEEEE\" class=\"arbo\">\n";
	echo "&nbsp;Aucune classe n'a été sélectionnée pour ce title.";
	echo "</td>\n"; 
	echo "</tr>\n";
}
	echo "<tr>\n";
	echo "<td width=\"141\" align=\"right\" bgcolor=\"#E6E6E6\" class=\"arbo\">";
	echo "&nbsp;<u><b>Statut de publication</b></u>";
	echo "</td>\n";
	echo "<td width=\"535\" align=\"left\" bgcolor=\"#EEEEEE\" class=\"arbo\">\n";
	if ( $_POST["fTit_statut"] == DEF_ID_STATUT_ATTEN) $sStatut=lib(DEF_ID_STATUT_ATTEN);
	else if ($_POST["fTit_statut"] == DEF_ID_STATUT_LIGNE) $sStatut=lib(DEF_ID_STATUT_LIGNE);
	else if ($_POST["fTit_statut"] == DEF_ID_STATUT_ARCHI) $sStatut=lib(DEF_ID_STATUT_ARCHI);
	echo "".$sStatut."<input type=\"hidden\" name=\"fTit_statut\" id=\"fTit_statut\"  value=\"".$_POST["fTit_statut"]."\">";
	echo "</td>\n";
	echo "</tr>\n";
?>
  
 <tr>
   <td bgcolor="#E6E6E6"  align="right" class="arbo">&nbsp;</td>
   <td bgcolor="#EEEEEE"  align="left" class="arbo">&nbsp;</td>
 </tr>
 <tr>
   <td bgcolor="#E6E6E6"  align="right" class="arbo">&nbsp;</td>
   <td bgcolor="#EEEEEE"  align="left" class="arbo">&nbsp;</td>
 </tr>
 <tr>
  <td bgcolor="#D2D2D2" colspan="2"  align="center" class="arbo"><?php if ($_POST['urlRetour'] != "") { ?>
        <input name="button" type="button" class="arbo" onClick="javascript:history.back();" value="<< Retour (annuler)">     &nbsp;&nbsp;&nbsp;&nbsp; <?php } ?>
     <input class="arbo" type="button" name="Ajouter" value="Suite (enregistrer) >>" onClick="validerForm()"></td>
 </tr>
</table>
<br><br>
<?php

} // FIN MODE EDITION
?>
</form>