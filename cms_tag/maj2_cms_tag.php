<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
if (!isset($classeName)){
	$classeName = preg_replace('/[^_]*_(.*)\.php/si', '$1', basename($_SERVER['PHP_SELF']));
}
  
 

// translation engine
// Added by Luc - 6 oct. 2009
if (DEF_APP_USE_TRANSLATIONS) {
	$translator =& TslManager::getInstance();
	if (DEF_EDIT_INACTIVE_LANG)
		$langpile = $translator->getLanguages();
	else	$langpile = $translator->getLanguages(true);
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
	eval("$"."oRes = new ".$classeName."(".$_GET["id"].");");
}
elseif ( $operation == "ETAPE2" ) { // Mode mise à jour
	//echo $operation;
}
else{ // Mode acces par display
	eval("$"."oRes = new ".$classeName."();");
} 


//$aClasseTag = getTagPosts("fAssoCms_tag_Classe_");
  
$aClasseTag = dbGetObjectsFromRequeteID("cms_assotagclasse", 'select * from cms_assotagclasse where xtc_cms_tag = '.$oRes->get_id());

//pre_dump($aClasseTag);
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
/*	echo "SAUVE";
	echo $operation;*/

	// maj BDD
	if ($operation == "UPDATE") {
    	$oTag = new Cms_tag($id);
		//pre_dump($oTag);
		/*$oTag->set_nom($_POST["fTag_nom"]); 
		$oTag->set_cms_site($_SESSION['idSite_travail']);
		$oTag->set_statut($_POST["fTag_statut"]); 
		$bRetour = dbUpdate($oTag);*/
		
		// on efface les assos 
		$sql = "select * from cms_assotagclasse where xtc_cms_tag = ".$id.""; 
		
		$aAssotag = dbGetObjectsFromRequete ("cms_assotagclasse", $sql);
		if (sizeof($aAssotag) > 0) {
			for ($k=0; $k<sizeof($aAssotag); $k++) {
				$oAssotag = $aAssotag[$k];
				$bDelete = dbDelete ($oAssotag);
			}
		}
		
		// puis on les recrée 
		 
		$aClasseTag = getTagPosts($_POST, "fAssoCms_tag_Classe_"); 
		  
		if ($aClasseTag) {
			for ($k=0; $k < sizeof($aClasseTag);$k++) {
				$oClasse = getObjectById("classe", $aClasseTag[$k]); 
				$sTempClasse = $oClasse->get_nom();   
				$aAssoClasse = getTagPosts($_POST, "fAsso".ucfirst($sTempClasse)."_"); 
			 	 
				if ($aAssoClasse) {
					for ($j = 0; $j<sizeof($aAssoClasse); $j++) {
						$oTagAsso = new Cms_assotagclasse();
						$oTagAsso->set_cms_tag($id);
						$oTagAsso->set_classe($aClasseTag[$k]);
						$oTagAsso->set_classeid($aAssoClasse[$j]); 
						//$reason = " sizeof aAssoClasse > 0 dbInsertWithAutoKey oTagAsso";
						 
						$bRetour2 = dbInsertWithAutoKey($oTagAsso);
					}
				}
				else { 
					$oTagAsso = new Cms_assotagclasse();
					$oTagAsso->set_cms_tag($id);
					$oTagAsso->set_classe($aClasseTag[$k]);
					$oTagAsso->set_classeid(-1);
					 
					//$reason = " sizeof aAssoClasse = 0 dbInsertWithAutoKey oTagAsso"; 
					$bRetour2 = dbInsertWithAutoKey($oTagAsso); 
				}
				 
				   
			}
		}
		else {
			$oTagAsso = new Cms_assotagclasse();
			$oTagAsso->set_cms_tag($id);
			$oTagAsso->set_classe(-1);
			$oTagAsso->set_classeid(-1); 
			//$reason = " sizeof aClasseTag = 0 dbInsertWithAutoKey oTagAsso"; 
			$bRetour2 = dbInsertWithAutoKey($oTagAsso); 
		} 
	
			
	} else if ($operation == "INSERT") {
		
		if (getCount_where($classeName, array("tag_nom", "tag_cms_site"), array($_POST["fTag_nom"], $_SESSION['idSite_travail']), array("TEXT", "NUMBER")) ==  0){
		$oTag = new Cms_tag();
		$oTag->set_nom($_POST["fTag_nom"]); 
		$oTag->set_cms_site($_SESSION['idSite_travail']);
		$oTag->set_statut($_POST["fTag_statut"]);
		$bRetour = dbInsertWithAutoKey($oTag);
			if ($bRetour) {
				$aClasseTag = getTagPosts($_POST, "fAssoCms_tag_Classe_"); 
				if ($aClasseTag) {
					for ($k = 0; $k<sizeof($aClasseTag); $k++) {
						$oClasse = getObjectById("classe", $aClasseTag[$k]); 
						$sTempClasse = $oClasse->get_nom();   
						$aAssoClasse = getTagPosts($_POST, "fAsso".ucfirst($sTempClasse)."_"); 
						//pre_dump($aAssoClasse);
						if ($aAssoClasse) {
							for ($j = 0; $j<sizeof($aAssoClasse); $j++) {
								$oTagAsso = new Cms_assotagclasse();
								$oTagAsso->set_cms_tag($bRetour);
								$oTagAsso->set_classe($aClasseTag[$k]);
								$oTagAsso->set_classeid($aAssoClasse[$j]); 
								$bRetour2 = dbInsertWithAutoKey($oTagAsso);
							}
						}
						else { 
							$oTagAsso = new Cms_assotagclasse();
							$oTagAsso->set_cms_tag($bRetour);
							$oTagAsso->set_classe($aClasseTag[$k]);
							$oTagAsso->set_classeid(-1); 
							$bRetour2 = dbInsertWithAutoKey($oTagAsso); 
						}
						 
						   
					}
				}
				else {
					$oTagAsso = new Cms_assotagclasse();
					$oTagAsso->set_cms_tag($bRetour);
					$oTagAsso->set_classe(-1);
					$oTagAsso->set_classeid(-1); 
					$bRetour2 = dbInsertWithAutoKey($oTagAsso); 
				} 
			}
		}
		else {
			$status.= "Le tag ".$_POST["fTag_nom"]." existe déjà.<br>";
		}
		
		  
	}

	if($bRetour2) {
		// type de record enregistré
	}
	else
		$status.= $classeName.' - Erreur lors de '. ( ($operation == "INSERT") ? "l'ajout" : "la modification" )."<br><br>";
		//$status.= $reason."<br><br>";
		//$status.= "taille aClasseTag ".sizeof($aClasseTag)."<br><br>";
		//$status.= "taille aAssoClasse ".sizeof($aAssoClasse)."<br><br>";
		$status.="<a href=\"javascript:retour()\" class=\"arbo\"></a>";
	 
?>
<div class="arbo"><u><b><?php if($status!="") echo $status; ?></b></u></div><br>
	<table border="0" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo"><?php
	if($bRetour || $bRetour2) {
	
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
		if (preg_match("/id=[1-9]+/msi", $redirString) && preg_match("/id=\-1/msi", $redirString)){ // 2 fois id= , on garde la value > 0
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
	echo "&nbsp;<u><b>Tag</b></u>";
	echo "</td>\n";
	echo "<td width=\"535\" align=\"left\" bgcolor=\"#EEEEEE\" class=\"arbo\">\n";
	echo "&nbsp;".$translator->getByID($oRes->get_nom())."<input type=\"hidden\" name=\"fTag_nom\" id=\"fTag_nom\" class=\"arbo\" size=\"80\" value=\"".$translator->getByID($oRes->get_nom())."\"  />";
	echo "</td>\n";
	echo "</tr>\n";

$listClass = array();

if (sizeof($aClasseTag) > 0) { 
	
	foreach ($aClasseTag as $oClasseTag){ 
		
			if (!in_array ($oClasseTag->get_classe(), $listClass) ) {
				array_push ($listClass , $oClasseTag->get_classe());
			echo "<input type=\"hidden\" name=\"fAssoCms_tag_Classe_".$oClasseTag->get_id()."\" id=\"fAssoCms_tag_Classe_".$oClasseTag->get_id()."\" class=\"arbo\" size=\"80\" value=\"".$oClasseTag->get_classe()."\"  />";
			//echo $oClasseTag->get_classe();
			$oClasse = new Classe ($oClasseTag->get_classe()); 
			$sTempClasse = $oClasse->get_nom(); 
			eval("$"."oTemp = new ".$sTempClasse."();");
			 
			$aForeign = dbGetObjects($sTempClasse);
			
			 
			// cas des deroulant d'id, pointage vers foreign
			//$sXML = $aForeign[0]->XML;
			$sXML = $oTemp->XML;
			$bCms_site = false;
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
			
			// cas des deroulant d'id, pointage vers foreign
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
			
			$sql = "select * from ".$sTempClasse." ";
			if ($bCms_site) $sql .= " where ".$foreignPrefixe."_cms_site = ".$_SESSION['idSite_travail']; 
			$sql .= " order by ".$foreignPrefixe."_".$oTemp->getAbstract(); 
			 
			$aForeign = dbGetObjectsFromRequete ($sTempClasse, $sql);
			
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
					
					if (getCount_where("cms_assotagclasse", array("xtc_cms_tag", "xtc_classe", "xtc_classeid"), array($id,$oClasseTag->get_classe(), $tempId), array("NUMBER","NUMBER","NUMBER")) >0){
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
							echo $translator->getByID($itemValueShort);	
						}
					}
					echo "<br />\n";
				}						
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
	echo "&nbsp;Aucune classe n'a été sélectionnée pour ce tag.";
	echo "</td>\n"; 
	echo "</tr>\n";
}
	echo "<tr>\n";
	echo "<td width=\"141\" align=\"right\" bgcolor=\"#E6E6E6\" class=\"arbo\">";
	echo "&nbsp;<u><b>Statut de publication</b></u>";
	echo "</td>\n";
	echo "<td width=\"535\" align=\"left\" bgcolor=\"#EEEEEE\" class=\"arbo\">\n";
	if ( $_POST["fTag_statut"] == DEF_ID_STATUT_ATTEN) $sStatut=lib(DEF_ID_STATUT_ATTEN);
	else if ($_POST["fTag_statut"] == DEF_ID_STATUT_LIGNE) $sStatut=lib(DEF_ID_STATUT_LIGNE);
	else if ($_POST["fTag_statut"] == DEF_ID_STATUT_ARCHI) $sStatut=lib(DEF_ID_STATUT_ARCHI);
	echo "".$sStatut."<input type=\"hidden\" name=\"fTag_statut\" id=\"fTag_statut\"  value=\"".$_POST["fTag_statut"]."\">";
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