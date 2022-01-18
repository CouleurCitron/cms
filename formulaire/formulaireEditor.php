<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 

raphael 26/02/2013

Bug de label pour les champs "textarea" rectifié

sponthus 03/06/2005

<input type="hidden" name="htmlcontent<?php echo $id_div; ?>" value='<?php echo  str_replace( "'", "`", $oZonedit->getHtml_content() ) ; ?>'>


*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once('functions.inc.php');
require_once('cms-inc/form/class.Form.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
activateMenu('gestionbrique'); //permet de dérouler le menu contextuellement
		
// id du formulaire
$id = $_GET['id'];
if ($id == "") $id = $_POST['id'];

if( $_GET["idForm"] != -1 && $_GET["idForm"] !='' ) {
	$id_form = $_GET["idForm"]; 
	$idForm = $_GET["idForm"]; 
	$aChamp = getChampsForm($idForm);
	
}
else if( $_POST["idForm"] != -1 && $_POST["idForm"] !='' ) {
	$id_form = $_POST["idForm"];
	$idForm = $_POST["idForm"];
	$aChamp = getChampsForm($idForm);
}

// modif d'un composant
if ($id > 0) {
	// la brique
	$oContent = new Cms_content($id);
	// un content de type "formulaireHTML" est forcément un content lié à la table cms_form
	// le formulaire 
	$node_id = $oContent->getNodeid_content();
	$oForm = new Cms_form($oContent->getObj_id_content()); 
	// champs du formulaire
	$aChamp = getChampsForm($oForm->getId_form());
	$idForm = $oForm->getId_form();

} else {
	// objet vide	 
	$oForm = new Cms_form(); 
	$node_id = 0;
} 

if($_GET['step']=='init') {
	$step = 0;
}
$bodyform = '
	<tr id="titre"><td colspan="2"><h1>'.$_POST['nom'].'</h1></td></tr>';
$step++;
?>
<script type="text/javascript"><!--

	// passage step 1 à step 2
	function envoyerLeTout() {

		sourceHTML_COMPLETE="";
<?php
for ($p=0; $p < DEF_MAXCHAMPSFORM; $p++) { 
		$sTypeChamp = $_POST['type_champ_'.$p];
		if ($sTypeChamp != "INUTIL") {
?>
			sourceHTML_COMPLETE+= document.getElementById('sourceHTML_<?php echo $p; ?>').value;
<?php
		}
	}
?>
		document.getElementById('sourceHTML').value = sourceHTML_COMPLETE;
		document.formgeneration.action="<?php echo $_SERVER['PHP_SELF']?>?step=2&id=<?php echo $id; ?>";
		document.formgeneration.submit();
	}
	
	// passage step init à step 1
	function goStep1()
	{
		nbChamp = 0;
<?php
// nombre de champs possibles
for ($p=0; $p < DEF_MAXCHAMPSFORM; $p++) { 
?>
// au moins un champ doit etre sélectionné
		nomChamp = "type_champ_<?php echo $p; ?>";
		if (document.getElementById(nomChamp).value != "INUTIL") nbChamp++;
<?php
}
?>
		if (nbChamp == 0) {
			sMessage = "Erreur : \n\n";
			sMessage+= "Au moins un champ doit être utilisé dans le formulaire";

			alert(sMessage);
		} else {
			document.genForm.submit();
		}
	}
	
--></script>
<?php
// initialisation du formulaire :: etape 1
if($_GET['step'] == 'init') {
?>
<div class="arbo2"><b>Créer un formulaire HTML</b></div><br />
<?php  
$uploadRepForm = $_SERVER['DOCUMENT_ROOT']."/custom/form/";
$uploadRep = $_SERVER['DOCUMENT_ROOT']."/custom/form/".$_SESSION["rep_travail"]."/";

if (count($_POST) == 0){	
	if (count($_GET) > 0){
	
		$_POST = $_GET;
		
		if (isset($_GET['importfile'])){
			echo $_SERVER['DOCUMENT_ROOT'].$_GET['importfile'];
			if (is_file($_SERVER['DOCUMENT_ROOT'].$_GET['importfile'])){
				
				$_FILES['importfile']['tmp_name'] = $_SERVER['DOCUMENT_ROOT'].$_GET['importfile'];
				$saveFile = $_SERVER['DOCUMENT_ROOT'].$_GET['importfile'];
				
			}
		}
	}
}
else{	  
	if (!is_dir($uploadRepForm)){ // on le crée
		mkdir($uploadRepForm);
		if (!is_dir($uploadRep)){ // on le crée
			mkdir($uploadRep);
		}	
	}
	
	$ext = preg_replace('/.*\.([^\.]+)/msi', '$1', $_FILES['importfile']['name']);
	$saveFile = $uploadRep.''.$_FILES['importfile']['name'];
	
	// import du fichier XML
	if(isset($_FILES['importfile']) && $_FILES['importfile']["name"] != ''){
		// Upload du fichier
		//eccho $saveFile
		if(move_uploaded_file($_FILES['importfile']['tmp_name'], $saveFile) || is_file($_SERVER['DOCUMENT_ROOT'].$_GET['importfile'])){
			$status .= "Téléchargement du fichier : OK <br /><br />"; 
			if (preg_match('/xml/msi', $ext)){
				//echo ' import de xml<br />';
			} 
			else{
				//echo ' import de type inconnu<br />';
				die(false);
			}				
			
			// controle ficher xml HTML
			$fh = @fopen($uploadRep.$_FILES['importfile']['name'],'r');
			if ($fh){
				while(!feof($fh)) {
					$sBodyXLS.=fgets($fh);
				}
			} 
			
			$xml_filename = $_FILES['importfile']['name'];
			
			
			 
		}
	}
	// choix d'un fichier XML
	else {		 
		$xml_filename = $_POST["form"];		
	}	
	
	// ------------------------------------------
	// BEGIN création de mon objet formulaire
	// ------------------------------------------
	$file = $uploadRep.$xml_filename; 
	$formulaire = new Form(); 
	$formulaire->getFields($file);  
	$id = createObjectForm ($formulaire, $idform);
	 
	if ($id > 0) {
		// la brique
		$oContent = new Cms_content($id);
		// un content de type "formulaireHTML" est forcément un content lié à la table cms_form
		// le formulaire
		$oForm = new Cms_form($oContent->getObj_id_content());
		// champs du formulaire
		$aChamp = getChampsForm($oForm->getId_form());
	
	} else {
		// objet vide
		$oForm = new Cms_form();
	}
	
	// ------------------------------------------
	// END création de mon objet formulaire
	// ------------------------------------------
	 
}
?>	
<form name="formFile" id="formFile" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?step=init&id=<?php echo $id; ?>">

  <!--<input type="file" name="importfile" id="importfile" class="arbo" pattern=".+(\.(xml))" errorMsg="Vous devez sélectionner un fichier qui porte l'extension .xml" value="*.xml"> <br/><br/>-->
 
 <!-- <input type="button" value="Télécharger" name="import" onclick="javascript:if (validate_form('formFile')) submit();" class="arbo" />  <br/><br/> -->

<table width="420" border="0" cellpadding="0" cellspacing="0" class="arbo">
  <tr>
    <td><table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="arbo">
        <tr valign="middle">
          <td height="28" colspan="2" nowrap="nowrap"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr class="col_titre">
                <td><strong>&nbsp;2. Chargement d'un fichier XML</strong></td>
              </tr>
          </table></td>
        </tr>
        <tr>
          <td colspan="2" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td align="right" style="vertical-align:middle"><strong>choix du XML&nbsp;:&nbsp;</strong></td>
          <td align="left"><select name="form" id="form" class="arbo">
		  	<option value="-1">Choisissez un fichier XML</option>
		  	<?php
			$aRepertoires = array(); // CUSTOM			
			$aRepertoires[] = "/custom/form/".$_SESSION["rep_travail"]; // GENERIC
			dirExists("/custom/form/".$_SESSION["rep_travail"]);			
			//pre_dump($aRepertoires);
			foreach($aRepertoires as $cKey => $sRepertoire){	
				$basedir = $_SERVER['DOCUMENT_ROOT'].$sRepertoire;
				echo $basedir;
				if (is_dir($basedir)){					
					if ($dir = @opendir($basedir)) {
						while (($file = readdir($dir)) !== false) {	
										
							if ((is_file($basedir."/".$file)) && (preg_match("/.*\.(xml)/msi", $file))) {
								$formPath = str_replace($_SERVER['DOCUMENT_ROOT'], "", $basedir."/".$file);								
								$formName = str_replace($_SERVER['DOCUMENT_ROOT'].$basedir."/", "", $file);		 
								 if($formName == $oForm->getName_form().".xml"){
									$selected = " selected";
								 }
								 else{
									$selected = "";
								}
								echo "<option value=\"".$formName."\"".$selected.">".$formName."</option>\n";
							} 
						}
					  closedir($dir);
					}	
				}				
			}
			?>  
          </select></td>
        </tr>
        <tr>
          <td colspan="2" align="center"> <br />
ou <br />
&nbsp;</td>
        </tr>
        <tr>
          <td align="right" style="vertical-align:middle"><strong>Upload de XML&nbsp;:&nbsp;</strong></td>
          <td align="left"> <input type="file" name="importfile" id="importfile" class="arbo" pattern=".+(\.(xml))" errorMsg="Vous devez sélectionner un fichier qui porte l'extension .xml" value="*.xml"></td>
        </tr>
        <tr>
          <td colspan="2" align="center"><br />
              <!--<input type="button" value="<?php $translator->echoTransByCode('btValider'); ?>" name="import" onclick="javascript:if (validate_form('formFile')) submit();" class="arbo" />  <br/><br/></td> -->
			  <input type="button" value="<?php $translator->echoTransByCode('btValider'); ?>" name="import" onclick="javascript:submit();" class="arbo" /><br /><br />
        </tr>
    </table></td>
  </tr>
</table>
</form>
<hr />
<form name="genForm" id="genForm" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?step=1&id=<?php echo $id; ?>">

<table width="420" border="0" cellpadding="0" cellspacing="0" class="arbo" >
  <tr>
    <td><table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="arbo">
        <tr valign="middle">
          <td height="28" colspan="2" nowrap="nowrap"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr class="col_titre">
                <td><strong>&nbsp;2. En remplissant directement les champs</strong></td>
              </tr>
          </table></td>
        </tr>
        <tr>
          <td colspan="2" align="right">&nbsp;</td>
        </tr>		
		 <tr>
          <td colspan="2" align="left"  style="padding-left:20px" > 
<div align="left" class="arbo">Nom du formulaire&nbsp;:&nbsp;</div>
<input type="hidden" name="nom" id="nom" value="<?php echo $_GET['nom']; ?>" />
<input type="text" name="name_form" id="name_form" pattern="^.+$" errormsg="Le nom est obligatoire" value="<?php echo $oForm->getName_form(); ?>" size="60" class="arbo" />
<br/></br>
<!--<input type="hidden" name="desc_form" id="desc_form" value="<?php echo $oForm->getDesc_form(); ?>" />-->
<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
<div align="left" class="arbo">Texte affiché après validation du formulaire &nbsp;:&nbsp;</div>
<textarea name="desc_form" id="desc_form" class="arbo textareaEdit"><?php echo $oForm->getDesc_form(); ?></textarea><br/><br/>
<div align="left" class="arbo">Destinataire e-mail éventuel &nbsp;:&nbsp;</div>
<input type="text" name="comm_form" id="comm_form" pattern="^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$" errormsg="Adresse e-mail non valide" value="<?php echo $oForm->getComm_form(); ?>" size="60" class="arbo" /><br/><br/>
<div align="left" class="arbo">Texte accusé de réception (optionnel)&nbsp;:&nbsp;</div>
<textarea name="ar_form" id="ar_form" class="arbo textareaEdit"><?php echo $oForm->getAr_form(); ?></textarea><br/><br/>
<div align="left" class="arbo">Adresse de validation (optionnel)&nbsp;:&nbsp;</div>
<input type="text" name="post_form" id="post_form" value="<?php echo $oForm->getPost_form(); ?>" size="60" class="arbo" /><br/><br/>
<input type="hidden" name="node_id" id="node_id" value="<?php echo $node_id; ?>" /><br>

<div align="left" class="arbo">Liste des champs du formulaire : </div><br />
<table>
<?php
// nombre de champs possibles
for ($p=0; $p < DEF_MAXCHAMPSFORM; $p++) { 
	// objet champ
	if ($p < newSizeOf($aChamp)) $oChamp = $aChamp[$p];
	else $oChamp = new Cms_champform();
	
	if ($oChamp->getDesc_champ()!= '') $idname_champ = noAccent($oChamp->getDesc_champ());
	else $idname_champ = noAccent($oChamp->getName_champ());
?>
	<tr>
		<td class="arbo">champ n° <?php echo $p+1; ?><input type="hidden" name="id_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getId_champ(); ?>" id="id_champ_<?php echo $p; ?>" /></td>
		<td class="arbo"><input type="text" name="name_champ_<?php echo $p; ?>" id="name_champ_<?php echo $p; ?>" class="arbo" value="<?php echo $oChamp->getName_champ(); ?>" />
		<input type="hidden" name="idname_champ_<?php echo $p; ?>" id="idname_champ_<?php echo $p; ?>" class="arbo" value="<?php echo $idname_champ; ?>" /></td>
		<td class="arbo">
<?php
if ($oChamp->getType_champ() == "INUTIL") $selectedINUTIL="selected"; else $selectedINUTIL="";
if ($oChamp->getType_champ() == "TEXT") $selectedTEXT="selected"; else $selectedTEXT="";
if ($oChamp->getType_champ() == "CC" || $oChamp->getType_champ() == "CHECKBOX") $selectedCC="selected"; else $selectedCC="";
if ($oChamp->getType_champ() == "BR" || $oChamp->getType_champ() == "RADIO") $selectedBR="selected"; else $selectedBR="";
if ($oChamp->getType_champ() == "COMBO" || $oChamp->getType_champ() == "SELECT") $selectedCOMBO="selected"; else $selectedCOMBO="";
if ($oChamp->getType_champ() == "AREA" || $oChamp->getType_champ() == "TEXTAREA") $selectedAREA="selected"; else $selectedAREA="";
if ($oChamp->getType_champ() == "HTML") $selectedHTML="selected"; else $selectedHTML="";
if ($oChamp->getType_champ() == "FROM") $selectedFROM="selected"; else $selectedFROM=""; ;
// new
if ($oChamp->getType_champ() == "FILE") $selectedFILE="selected"; else $selectedFILE="";
if ($oChamp->getType_champ() == "CAPTCHA") $selectedCAPTCHA="selected"; else $selectedCAPTCHA=""; 

?>
<select name="type_champ_<?php echo $p; ?>" id="type_champ_<?php echo $p; ?>" class="arbo">
	<option value="INUTIL" <?php echo $selectedINUTIL; ?> >champ non utilisé</option>
	<option value="TEXT" <?php echo $selectedTEXT; ?> >champ texte</option>
	<option value="CC" <?php echo $selectedCC; ?> >champ case à cocher</option>
	<option value="BR" <?php echo $selectedBR; ?> >champ bouton radio</option>
	<option value="COMBO" <?php echo $selectedCOMBO; ?> >champ liste déroulante</option>
	<option value="AREA" <?php echo $selectedAREA; ?> >champ zone de texte</option>
	<option value="HTML" <?php echo $selectedHTML; ?> >champ HTML</option>
	<option value="FROM" <?php echo $selectedFROM; ?> >champ expéditeur</option>
	<option value="FILE" <?php echo $selectedFILE; ?> >fichier</option>
	<option value="CAPTCHA" <?php echo $selectedCAPTCHA; ?> >code de sécurité</option> 
</select>
		</td>
	</tr>
<?php
}
?>
</table>

</td>
        </tr>
    </table></td>
  </tr>
</table>

<input type="button" name="bouton" id="bouton" value="Suite" pattern="^.+$" onclick="if(validate_form('genForm')) { goStep1(); }" class="arbo" />
<?php
} elseif ( (isset($_POST['name_form']) && ($_GET['step']==1)) ) {

// contrôle unicité du nom du formulaire 
 
if ($idForm == '') {
	if (checkUniqueName($oForm) ) {
		// contrôle unicité nom formulaire ok		
	} else {
		// contrôle unicité nom formulaire ko	
		$sMessage = "<div align='left' class='arbo'>Un formulaire de nom ".$_POST['name_form']." existe déjà.</div><br>";
		$sMessage.= "<div align='left' class='arbo'>Veuillez choisir un nom différent.</div><br>";
		$sMessage.="<a href='formulaireEditor.php?step=init' class='arbo'>retour</a>";		
	
		die($sMessage);
	}
}
$oForm->setId_form($idForm);
$oForm->setName_form($_POST['name_form']); 
$oForm->setDesc_form($_POST['desc_form']);
$oForm->setComm_form($_POST['comm_form']);
$oForm->setAr_form($_POST['ar_form']);
$oForm->setPost_form($_POST['post_form']);
$oForm->setId_site($idSite);
if ($idForm == '') {
	$idForm = dbInsertWithAutoKey($oForm);  
}
else {
	 $idForm = dbUpdate($oForm);
}
 
// etape 2
?><form action="<?php echo $_SERVER['PHP_SELF'].'?step=2&id='.$id; ?>" method="post" name="formgeneration" id="formgeneration">

<table width="500" border="0" cellpadding="0" cellspacing="0" class="arbo" >
  <tr>
    <td><table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="arbo">
        <tr valign="middle">
          <td height="28" colspan="2" nowrap="nowrap"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr class="col_titre">
                <td><strong>Génération du formulaire &quot;<?php echo $_POST["name_form"];?>&quot;</strong></td>
              </tr>
          </table></td>
        </tr>
        <tr>
          <td colspan="2" align="right">&nbsp;</td>
        </tr>
		
		 <tr>
          <td colspan="2" align="left"  style="padding-left:20px" >
			<input type="hidden" name="nom" id="nom" value="<?php echo $_POST['name_form']; ?>" />
			<input type="hidden" name="name_form" id="name_form" value="<?php echo $_POST['name_form']; ?>" />
			<input type="hidden" name="comm_form" id="comm_form" value="<?php echo $_POST['comm_form']; ?>" />
			<input type="hidden" name="desc_form" id="desc_form" value="<?php echo $_POST['desc_form']; ?>" />		
			<input type="hidden" name="ar_form" id="ar_form" value="<?php echo $_POST['ar_form']; ?>" />
			<input type="hidden" name="post_form" id="post_form" value="<?php echo $_POST['post_form']; ?>" />
			<input type="hidden" name="node_id" id="node_id" value="<?php echo $_POST['node_id']; ?>" />
			<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
			<input type="hidden" name="id_form" id="id_form" value="<?php echo $oForm->getId_form(); ?>" />
<?php
for ($p=0; $p < DEF_MAXCHAMPSFORM; $p++) { 
	$type_champ = $_POST['type_champ_'.$p];
	if ($type_champ == "CC") $type_champ = "CHECKBOX";
	if ($type_champ == "BR") $type_champ =  "RADIO";
	if ($type_champ == "COMBO") $type_champ = "SELECT";
	if ($type_champ == "AREA") $type_champ = "TEXTAREA";
?>
<input type="hidden" name="name_champ_<?php echo $p; ?>" id="name_champ_<?php echo $p; ?>" value="<?php echo $_POST['name_champ_'.$p]; ?>" />
<input type="hidden" name="type_champ_<?php echo $p; ?>" id="type_champ_<?php echo $p; ?>" value="<?php echo $type_champ; ?>" />
<?php } ?>

<br/><br/>
<div align="left" class="arbo">Liste des champs du formulaire : </div><br />
<table cellpadding="2" cellspacing="2">

<?php
 
$nb_champ = 0;
for ($p=0; $p < DEF_MAXCHAMPSFORM; $p++) {
 		 
		 
		$sTypeChamp = $_POST['type_champ_'.$p]; 
		
		 
		// objet champ
		if ($p < newSizeOf($aChamp)) {
			$oChamp = $aChamp[$p];
		}
		else {
			// insert formulaire, les champs n'existent pas encore
			$oChamp = new Cms_champform();
		}

		// le nom du champ peut être modifié en première page
		$oChamp->setName_champ($_POST['name_champ_'.$p]);
		
		if ($oChamp->getDesc_champ()!= '') $idname_champ = noAccent($oChamp->getDesc_champ());
		else $idname_champ = noAccent($oChamp->getDesc_champ());
		
		$oChamp->setDesc_champ(removeXitiForbiddenChars(strtolower(noAccent($_POST['name_champ_'.$p]))));
		 
		$type_champ = $_POST['type_champ_'.$p];
		
		if ($type_champ == "CC") $type_champ = "CHECKBOX";
		if ($type_champ == "BR") $type_champ =  "RADIO";
		if ($type_champ == "COMBO") $type_champ = "SELECT";
		if ($type_champ == "AREA") $type_champ = "TEXTAREA";
		
		// faudrait vérifier l'existence du champ
		//echo $oChamp->getId_champ()." ".$_POST['type_champ_'.$p]."<br />";
		if ($oChamp->getId_champ() == -1 && $_POST['type_champ_'.$p] != 'INUTIL' ) { 
			$nb_champ++;
			if ($idForm != '') $oChamp->setId_form($idForm);
			else  $oChamp->setId_form(-1);
			$oChamp->setDesc_champ(removeXitiForbiddenChars(strtolower(noAccent($_POST['name_champ_'.$p]))));
			$oChamp->setType_champ($type_champ);
			$oChamp->setLargeur_champ(-1); 
			$oChamp->setHauteur_champ(-1); 
			$oChamp->setTaillemax_champ(-1); 
			$oChamp->setObligatoire_champ(0); 
			$oChamp->setObjetid_champ(-1); 
			
			$oR = dbInsertWithAutoKey($oChamp);
		}
		else if ($oChamp->getId_champ()!= -1 && $_POST['type_champ_'.$p] == 'INUTIL' ) { 
			//echo $oChamp->getId_champ()."--". $_POST['name_champ_'.$p]." : ".$sTypeChamp." <br />";
		 	$oR = dbDelete($oChamp);
		}
		else {
			$oChamp->setType_champ($type_champ);
			$oR = dbUpdate($oChamp);
		}
		
 
		if ($sTypeChamp != "INUTIL") {
?>
	<tr>
		<td class="arbo">
<?php
		if ($sTypeChamp == "TEXT") {
			
			// prépare champ texte
			if ($oChamp->getId_champ() == -1) {
				$champTEXT = "<tr><td class='awsformlabel'>".$oChamp->getName_champ()."</td><td class='awsformfield'>&nbsp;</td></tr>";
			} else {
				if ($oChamp->getObligatoire_champ() == 1) {
					$pattern = "^.+$";
					$champTEXT = "<tr><td class='awsformlabel'>".$oChamp->getName_champ()."&nbsp;*&nbsp;</td>\n";
				}
				else {
					$pattern = "";
					$champTEXT = "<tr><td class='awsformlabel'>".$oChamp->getName_champ()."</td>\n";
				}
		
			 
				$champTEXT.= "<td class='awsformfield'><input type='text' name='".$idname_champ."' id='".$idname_champ."' ";
				$champTEXT.= "value='".$oChamp->getValdefaut_champ()."' ";
				$champTEXT.= "size='".$oChamp->getLargeur_champ()."' ";
				$champTEXT.= "maxlength='".$oChamp->getTaillemax_champ()."' ";
				$champTEXT.= "errorMsg='Le champ ".$idname_champ." est obligatoire' pattern='".$pattern."' class='arbo'></td></tr>";
			}
?>
		<input type="hidden" name="valdefaut_champ_<?php echo $p; ?>" id="valdefaut_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getValdefaut_champ(); ?>" />
		<input type="hidden" name="largeur_champ_<?php echo $p; ?>" id="largeur_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getLargeur_champ(); ?>" />
		<input type="hidden" name="taillemax_champ_<?php echo $p; ?>" id="taillemax_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getTaillemax_champ(); ?>" />
		<input type="hidden" name="obligatoire_champ_<?php echo $p; ?>" id="obligatoire_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getObligatoire_champ(); ?>" />
		<input type="hidden" name="sourceHTML_<?php echo $p; ?>" id="sourceHTML_<?php echo $p; ?>" value="<?php echo replaceQuote($champTEXT); ?>" />
		<div id="div_<?php echo $p; ?>" class="arbo"><table><?php echo $champTEXT; ?></table></div>
<?php
		//if ($_POST['obligatoire_champ_'.$p] == 1) { <font color="#FF0000">&nbsp;*</font><?php }

		} else if ($oChamp->getType_champ() == "CC" || $oChamp->getType_champ() == "CHECKBOX") {

			// prépare champ case à cocher
			$champCC="";
			if ($oChamp->getValeur_champ() == "") {
				$champCC.= "<tr><td class='awsformlabel'>".$oChamp->getName_champ()."</td><td class='awsformfield'>&nbsp;</td></tr>";
			} else {
				$aCC = explode(";", $oChamp->getValeur_champ());
				for ($t=0; $t<newSizeOf($aCC); $t++)
				{
					$sCC = $aCC[$t];
	
					if ($t == 0) $champCC.= "<tr><td class='awsformlabel'>".$oChamp->getName_champ()."</td><td class='awsformfield'>";
					else $champCC.= "<tr><td class='awsformlabel'>&nbsp;</td><td class='awsformfield'>";
	
					$champCC.= "<input type='checkbox' name='".$idname_champ."' id='".$idname_champ."' value='Oui' class='arbo'>".$sCC."</td></tr>";
				}
			}
?>
		<input type="hidden" name="valeur_champ_<?php echo $p; ?>" id="valeur_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getValeur_champ(); ?>" />
		<input type="hidden" name="sourceHTML_<?php echo $p; ?>" id="sourceHTML_<?php echo $p; ?>" value="<?php echo replaceQuote($champCC); ?>" />
		<div id="div_<?php echo $p; ?>"><table><?php echo $champCC; ?></table></div>
<?php	
		} else if ($sTypeChamp == "BR"  || $sTypeChamp == "RADIO") {

			// prépare champ bouton radio
			$champBR="";
			if ($oChamp->getValeur_champ() == "") {
				$champBR.= "<tr><td align='left' class='awsformlabel'>".$oChamp->getName_champ()."</td><td class='awsformfield'>&nbsp;</td></tr>";
			} else {
				$aBR = explode(";", $oChamp->getValeur_champ());
				for ($t=0; $t<newSizeOf($aBR); $t++)
				{
					$sBR = $aBR[$t];
					
					if ($t == 0) $champBR.= "<tr><td align='right' class='awsformlabel'>".$oChamp->getName_champ()."</td><td class='awsformfield'>";
					else $champBR.= "<tr><td class='awsformlabel'>&nbsp;</td><td class='awsformfield'>";
					
					$champBR.= "<input type='radio' name='".$idname_champ."' value='".$sBR."' class='arbo'>".$sBR."</td></tr>";
				}
			}
?>
		<input type="hidden" name="valeur_champ_<?php echo $p; ?>" id="valeur_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getValeur_champ(); ?>" />
		<input type="hidden" name="sourceHTML_<?php echo $p; ?>" id="sourceHTML_<?php echo $p; ?>" value="<?php echo replaceQuote($champBR); ?>" />
		<div id="div_<?php echo $p; ?>" class="arbo"><table><?php echo $champBR; ?></table></div>		
<?php	
		} else if ($sTypeChamp == "COMBO" || $sTypeChamp == "SELECT" ) {

			// prépare champ combobox
			if ($oChamp->getId_champ() == -1) {
				$champCOMBO.= "<tr><td class='awsformlabel'>".$oChamp->getName_champ()."</td><td class='awsformfield'>&nbsp;</td></tr>";
			} else {
				$aCOMBO = explode(";", $oChamp->getValeur_champ());
	
				$champCOMBO = "<tr><td class='awsformlabel'>".$oChamp->getName_champ()."</td><td class='awsformfield'>";
				$champCOMBO.= "<select name='".$idname_champ."' id='".$idname_champ."' class='arbo'>";
				for ($t=0; $t<newSizeOf($aCOMBO); $t++)
				{
					$sCOMBO = $aCOMBO[$t];
					$champCOMBO.= "<option value='".$sCOMBO."'>".$sCOMBO;
				}
				$champCOMBO.= "</select>";
				$champCOMBO.= "</td></tr>";
			}
?>
		<input type="hidden" name="valeur_champ_<?php echo $p; ?>" id="valeur_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getValeur_champ(); ?>" />
		<input type="hidden" name="sourceHTML_<?php echo $p; ?>" id="sourceHTML_<?php echo $p; ?>" value="<?php echo replaceQuote($champCOMBO); ?>" />
		<div id="div_<?php echo $p; ?>" class="arbo"><table><?php echo $champCOMBO; ?></table></div>		
<?php	
		} else if ($sTypeChamp == "AREA" || $sTypeChamp == "TEXTAREA") {

			// prépare champ textarea
			if ($oChamp->getLargeur_champ() == -1) $sLargeur = 30;
			else $sLargeur = $oChamp->getLargeur_champ();
			
			if ($oChamp->getHauteur_champ() == -1) $sHauteur = 10;
			else $sHauteur = $oChamp->getHauteur_champ();
			
			if ($oChamp->getId_champ() == -1) {
				$champAREA = "<tr><td class='awsformlabel'>".$oChamp->getName_champ()."</td><td class='awsformfield'>&nbsp;</td></tr>";
			} else {
				
				if ($oChamp->getObligatoire_champ() == 1) {
					$pattern = "^.+$";
					$champAREA = "<tr><td class='awsformlabel' align='left'>".$oChamp->getName_champ()."&nbsp;*&nbsp;</td><td class='arbo'>";
				}
				else {
					$pattern = "";
					$champAREA = "<tr><td class='awsformlabel' align='left'>".$oChamp->getName_champ()."</td><td class='awsformfield'>";
				}
				 
				$champAREA.= "<textarea name='".$idname_champ."' id='".$idname_champ."' cols='".$sLargeur."' ";
				$champAREA.= "rows='".$sHauteur."' class='arbo'";
				$champAREA.= "errorMsg='Le champ ".$idname_champ." est obligatoire' pattern='".$pattern."' class='arbo'>".$oChamp->getValdefaut_champ();
				$champAREA.= "</textarea>";
				$champAREA.="</td></tr>";
			}		
?>
		<input type="hidden" name="valdefaut_champ_<?php echo $p; ?>" id="valdefaut_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getValdefaut_champ(); ?>" />
		<input type="hidden" name="largeur_champ_<?php echo $p; ?>" id="largeur_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getLargeur_champ(); ?>" />
		<input type="hidden" name="hauteur_champ_<?php echo $p; ?>" id="hauteur_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getHauteur_champ(); ?>" />
		<input type="hidden" name="obligatoire_champ_<?php echo $p; ?>" id="obligatoire_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getObligatoire_champ(); ?>" />
		<input type="hidden" name="sourceHTML_<?php echo $p; ?>" id="sourceHTML_<?php echo $p; ?>" value="<?php echo replaceQuote($champAREA); ?>" />
		<div id="div_<?php echo $p; ?>" class="arbo"><table><?php echo $champAREA; ?></table></div>
<?php	
		} else if ($sTypeChamp == "HTML") {

			// prépare champ textarea
			$champHTML = "<tr><td class='awsformcols' colspan='2'>".$oChamp->getValeur_champ()."</td></tr>";
?>
		<input type="hidden" name="valeur_champ_<?php echo $p; ?>" id="valeur_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getValeur_champ(); ?>" />
		<input type="hidden" name="sourceHTML_<?php echo $p; ?>" id="sourceHTML_<?php echo $p; ?>" value="<?php echo replaceQuote($champHTML); ?>" />
		<div id="div_<?php echo $p; ?>" class="arbo"><table><?php echo $champHTML; ?></table></div>
<?php	
		}
		else if ($sTypeChamp == "FROM") {

				// prépare champ texte
			if ($oChamp->getId_champ() == -1) {
				$champFROM = "<tr><td class='awsformlabel'>".$oChamp->getName_champ()."</td><td class='awsformfield'>&nbsp;</td></tr>";
			} else {
				
				if ($oChamp->getObligatoire_champ() == 1) {
					$pattern = "^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$";
					$champFROM = "<tr><td class='awsformlabel'>".$oChamp->getName_champ()."&nbsp;*&nbsp;</td>\n";
				}
				else {
					$pattern = "";
					$champFROM = "<tr><td class='awsformlabel'>".$oChamp->getName_champ()."</td>\n";
				}
		 
				$champFROM.= "<td class='awsformfield'><input type='text' name='fromperso_".$idname_champ."' id='fromperso_".$idname_champ."' ";
				$champFROM.= "value='".$oChamp->getValdefaut_champ()."' ";
				$champFROM.= "size='".$oChamp->getLargeur_champ()."' ";
				$champFROM.= "maxlength='".$oChamp->getTaillemax_champ()."' ";
				$champFROM.= "errorMsg='Le champ ".$idname_champ." doit respecter la syntaxe d un email' pattern='".$pattern."' class='arbo'></td></tr>";
			}
?>
		<input type="hidden" name="valdefaut_champ_<?php echo $p; ?>" id="valdefaut_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getValdefaut_champ(); ?>" />
		<input type="hidden" name="largeur_champ_<?php echo $p; ?>" id="largeur_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getLargeur_champ(); ?>" />
		<input type="hidden" name="taillemax_champ_<?php echo $p; ?>" id="taillemax_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getTaillemax_champ(); ?>" />
		<input type="hidden" name="obligatoire_champ_<?php echo $p; ?>" id="obligatoire_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getObligatoire_champ(); ?>" />
		<input type="hidden" name="sourceHTML_<?php echo $p; ?>" id="sourceHTML_<?php echo $p; ?>" value="<?php echo replaceQuote($champFROM); ?>" />
		<div id="div_<?php echo $p; ?>" class="arbo"><table><?php echo $champFROM; ?></table></div>
<?php	
		}
		
		// file
		else if ($sTypeChamp == "FILE") {
?>
		<input type="hidden" name="valdefaut_champ_<?php echo $p; ?>" id="valdefaut_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getValdefaut_champ(); ?>" />
		<input type="hidden" name="largeur_champ_<?php echo $p; ?>" id="largeur_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getLargeur_champ(); ?>" />
		<input type="hidden" name="taillemax_champ_<?php echo $p; ?>" id="taillemax_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getTaillemax_champ(); ?>" />
		<input type="hidden" name="obligatoire_champ_<?php echo $p; ?>" id="obligatoire_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getObligatoire_champ(); ?>" />
		<input type="hidden" name="sourceHTML_<?php echo $p; ?>" id="sourceHTML_<?php echo $p; ?>" value="<?php echo replaceQuote($champFILE); ?>" />
		<div id="div_<?php echo $p; ?>" class="arbo"><table><?php echo $champFILE; ?></table></div>
<?php	
		}
		// Captcha
		else if ($sTypeChamp == "CAPTCHA") {			 
			
?>
		<input type="hidden" name="valdefaut_champ_<?php echo $p; ?>" id="valdefaut_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getValdefaut_champ(); ?>" />
		<input type="hidden" name="largeur_champ_<?php echo $p; ?>" id="largeur_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getLargeur_champ(); ?>" />
		<input type="hidden" name="taillemax_champ_<?php echo $p; ?>" id="taillemax_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getTaillemax_champ(); ?>" />
		<input type="hidden" name="obligatoire_champ_<?php echo $p; ?>" id="obligatoire_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getObligatoire_champ(); ?>" />
		<input type="hidden" name="sourceHTML_<?php echo $p; ?>" id="sourceHTML_<?php echo $p; ?>" value="<?php echo replaceQuote($champCAPTCHA); ?>" />
		<div id="div_<?php echo $p; ?>" class="arbo"><table><?php echo $champCAPTCHA; ?></table></div>
<?php
		} 
		
?>
		</td>
		<td class="arbo">
<?php
		if ($sTypeChamp == "TEXT") {
?>
<a href="javascript:openBrWindow('popup/update_field.php?brique=formulaire&div=<?php echo $p; ?>&id=<?php echo $id; ?>&idchamp=<?php echo $oChamp->getId_champ(); ?>&idForm=<?php echo $oChamp->getId_form(); ?>', 'preview', 500, 500, 'scrollbars=no', 'true')" class="arbo">modifier le champ texte</a>
<?php
		} else if ($oChamp->getType_champ() == "CC" || $oChamp->getType_champ() == "CHECKBOX") {
?>
<a href="javascript:openBrWindow('popup/update_field.php?brique=formulaire&div=<?php echo $p; ?>&id=<?php echo $id; ?>&idchamp=<?php echo $oChamp->getId_champ(); ?>&idForm=<?php echo $oChamp->getId_form(); ?>', 'preview', 500, 500, 'scrollbars=no', 'true')" class="arbo">modifier la case à cocher</a>
<?php
		} else if ($sTypeChamp == "BR" || $sTypeChamp == "RADIO") {
?>
<a href="javascript:openBrWindow('popup/update_field.php?brique=formulaire&div=<?php echo $p; ?>&id=<?php echo $id; ?>&idchamp=<?php echo $oChamp->getId_champ(); ?>&idForm=<?php echo $oChamp->getId_form(); ?>', 'preview', 500, 500, 'scrollbars=no', 'true')" class="arbo">modifier le bouton radio</a>
<?php
		} else if ($sTypeChamp == "COMBO"  || $sTypeChamp == "SELECT" ) {
?>
<a href="javascript:openBrWindow('popup/update_field.php?brique=formulaire&div=<?php echo $p; ?>&id=<?php echo $id; ?>&idchamp=<?php echo $oChamp->getId_champ(); ?>&idForm=<?php echo $oChamp->getId_form(); ?>', 'preview', 500, 500, 'scrollbars=no', 'true')" class="arbo">modifier la liste déroulante</a>
<?php
		} else if ($sTypeChamp == "AREA" || $sTypeChamp == "TEXTAREA") {
?>
<a href="javascript:openBrWindow('popup/update_field.php?brique=formulaire&div=<?php echo $p; ?>&id=<?php echo $id; ?>&idchamp=<?php echo $oChamp->getId_champ(); ?>&idForm=<?php echo $oChamp->getId_form(); ?>', 'preview', 500, 500, 'scrollbars=no', 'true')" class="arbo">modifier la zone de texte</a>
<?php
		} else if ($sTypeChamp == "HTML") {
?>
<a href="javascript:openBrWindow('popup/update_field.php?brique=formulaire&div=<?php echo $p; ?>&idForm=<?php echo $oChamp->getId_form(); ?>&id=<?php echo $id; ?>&idchamp=<?php echo $oChamp->getId_champ(); ?>&source='+tempSource, 'preview', 500, 500, 'scrollbars=no', 'true')" class="arbo" onMouseDown="tempSource=escape(document.getElementById('valeur_champ_<?php echo $p; ?>').value);">modifier la zone html</a>
<?php
		} else 	if ($sTypeChamp == "FROM") {
?>
<a href="javascript:openBrWindow('popup/from2.php?brique=formulaire&div=<?php echo $p; ?>&id=<?php echo $id; ?>&idchamp=<?php echo $oChamp->getId_champ(); ?>&idForm=<?php echo $oChamp->getId_form(); ?>', 'preview', 500, 500, 'scrollbars=no', 'true')" class="arbo">modifier le champ expéditeur</a>
<?php
		} 
		
?>		
		</td>
	</tr>
<?php
	}
}
?>
</table>

</td>
        </tr>
    </table></td>
  </tr>
</table>
<input type="hidden" name="nb_champ" id="nb_champ" value="<?php echo $nb_champ; ?>" />
<input type="hidden" name="sourceHTML" id="sourceHTML" value="" />
<br />
<input type="button" name="generate4" id="generate4" value="Générer le formulaire" onclick="javascript:envoyerLeTout()" class="arbo" />

</form>
<?php
} elseif ( (isset($_POST['nom']) && ($_GET['step']==2)) ) {
// etape 3
	$srcHTML = $_POST['sourceHTML'];
	 
	$srcHTML = "<script src=\"/backoffice/cms/js/validForm.js\"></script>
	<input type=\"hidden\" name=\"formulaireid\" id=\"formulaireid\" value=\"".$_POST["id_form"]."\">
	<input type=\"hidden\" name=\"formulaire\" id=\"formulaire\" value=\"".$_POST['name_form']."\">
	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"awsformtable\">".$srcHTML;

	$srcHTML .= "<tr>
	<td align=\"center\"><input type=\"button\" name=\"".$_POST['nom']."\" id=\"".$_POST['nom']."\" value=\"Envoyer\" onClick=\"javascript:if(validate_form(`MyForm_".$id."`)){submit();}\" class=\"arbo_valid\"></td>
	<td align=\"center\"><input type=\"reset\" class=\"arbo_reset\" value=\"Effacer\"></td>
	</tr>
	</table>";
?>
<table width="450" border="0" cellpadding="0" cellspacing="0" class="arbo" >
  <tr>
    <td><table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="arbo">
        <tr valign="middle">
          <td height="28" colspan="2" nowrap="nowrap"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr class="col_titre">
                <td><strong>Prévisualisation du formulaire "<?php echo $_POST['name_form']; ?>"</strong></td>
              </tr>
          </table></td>
        </tr>
        <tr>
          <td colspan="2" align="right">&nbsp;</td>
        </tr>
		
		 <tr>
          <td colspan="2" align="left"  style="padding-left:20px" > 
		  
		   
<hr width="485" align="left" />
<div style="width: 450px; height: 500px; background-color: #ffffff; overflow: auto;">
<?php 

$nb_champ = $_POST['nb_champ'];
if ($_POST["id_form"] != '' && $_POST["id_form"]!= -1) 
	$oForm = new Cms_form ($_POST["id_form"]);
$id_form = $oForm->getId_form();
 
if ($id_form == -1) { 
	$sql= 'select * from  cms_champform where id_form = -1 order by id_champ DESC LIMIT 0, '.$nb_champ; 
	$aChamps = dbGetObjectsFromRequete("cms_champform", $sql);
 
}
else  {
	$aChamps = dbGetObjectsFromFieldValue("cms_champform", array("getId_form"),  array($id_form), "getId_champ");
}
 
$Params = array();
foreach ($aChamps as $oChamp) {
	//echo $oChamp->getId_form();	
	//echo $oChamp->getName_champ();
	$aItems = array ();
	if ($oChamp->getObligatoire_champ() == 0) $sObligatoire = false; else $sObligatoire = true; 
	if ($oChamp->getValeur_champ() != '') { 
	 
		$aValues = explode (";", $oChamp->getValeur_champ());
		$aOption = array();
		foreach ( $aValues as $value) {
			$aOption["ID"] = noAccent($value);
			$aOption["LIBELLE"] = $value;
			array_push ($aItems, $aOption);
		}
		
	} 
	
	if ( $oChamp->getDesc_champ() == '') $id_champ = strtolower(noAccent($oChamp->getName_champ()));
	else  $id_champ = strtolower($oChamp->getDesc_champ());
	 
	$myField = array (	'name' =>  $oChamp->getName_champ(), 
						'id' => $id_champ, 
						'type' => strtolower($oChamp->getType_champ()), 
						'values' => $aItems,  
						'oblig' => $sObligatoire,   
						//'label_options' => $classLabel, 
						//'class' => ''.$node["attrs"]["CLASS"].'',  //
						//'option' => $option, //
						//'small' => ''.$node["attrs"]["SMALL"].'', //
						//'br' => $node["attrs"]["BR"]  , //
						//'display' => $node["attrs"]["DISPLAY"] , //
						//'pubkey' => $node["attrs"]["PUBKEY"] , // capctha
						//'privkey' => $node["attrs"]["PRIVKEY"] , // capctha
						//'theme' => $node["attrs"]["THEME"] , // capctha
						//'lang' => $node["attrs"]["LANG"] , // capctha
						//'pattern' => $node["attrs"]["PATTERN"]  ,
						//'myClass' => $myClass, 
						//'myClassField' => $myClassField,
						//'myClassOrderBy' => $myClassOrderBy ,
						//'myClassOrderBySens' => $myClassOrderBySens ,
						//'myClassConditions' => $myClassConditions       
						
						);
						
						array_push ( $Params , $myField )	;	
						 
}
 

if ($oForm->getName_form() != '') {
	$sNameForm = strtolower(removeXitiForbiddenChars(noAccent($oForm->getName_form())));
}
else {
	$sNameForm = strtolower(removeXitiForbiddenChars(noAccent($_POST['name_form'])));
} 
$formulaire = new Form();
$formulaire->params = $Params;
$formulaire->setNameform($sNameForm) ;
$formulaire->setParams($Params);

// adresse de post 
if ($oForm->getPost_form() == '') {
	$sAction = "/frontoffice/eformalites/writeformbdd.php"; 
}
else {
	$sAction = $oForm->getPost_form(); 
} 
 
//$srcHTML =  $formulaire->prepareJs();  ;  
$srcHTML= $formulaire->prepareJSSansEcho();  
$srcHTML.= '<form action="'.$sAction.'" method="post" name="'.$sNameForm.'" id="'.$sNameForm.'" enctype="multipart/form-data">';
$srcHTML.= '<input type="hidden"  name="formulaireid" id="formulaireid" value="'.$oForm->getId_form().'" />';  
$srcHTML.= '<div class="content_form">	'; 
$srcHTML.= $formulaire->prepareFormSansEcho(); 
$srcHTML.= '<div class="default submit">
<a id="loginbtn" href="javascript:validate_form();">Envoyer</a>
</div>';
$srcHTML.= '</div>	'; 
$srcHTML.= '</form>	';
$srcHTML = str_replace("`", "\"", stripslashes($srcHTML)); 

$sTypeForm = $_POST['brTypeForm'];

// formulaire mail ou formulaire écrit dans un fichier

//$srcHTML = "<div id=\"awsform\"><form name=\"MyForm_".$id."\" id=\"MyForm_".$id."\" action=\"$sAction\" method=\"post\">".$srcHTML;
//$srcHTML.= "</form></div>";

	echo $srcHTML;
?>
</div>
<hr width="485" align="left" />
<small><u>Dimensions&nbsp;:</u>&nbsp;485 x 464&nbsp;pixels<br />
Vous pourrez modifier ces dimensions à loisir lors de la mise en page.</small>
	<form action="/backoffice/cms/briques/saveComposant.php?id=<?php echo $id; ?>" method="post" name="previewHTML" id="previewHTML">
		<input type="hidden" name="TYPEcontent" id="TYPEcontent" value="formulaireHTML" />
		<input type="hidden" name="WIDTHcontent" id="WIDTHcontent" value="450" />
		<input type="hidden" name="HEIGHTcontent" id="HEIGHTcontent" value="500" />
<!-- la brique aura pour nom le nom du formulaire -->
		<input type="hidden" name="NAMEcontent" id="NAMEcontent" value="<?php echo $_POST['name_form']; ?>" />
		<input type="hidden" name="desc_form" id="desc_form" value="<?php echo $_POST['desc_form']; ?>" />		
		<input type="hidden" name="comm_form" id="comm_form" value="<?php echo $_POST['comm_form']; ?>" />
		<input type="hidden" name="ar_form" id="ar_form" value="<?php echo $_POST['ar_form']; ?>" />
		<input type="hidden" name="post_form" id="post_form" value="<?php echo $_POST['post_form']; ?>" />
		<input type="hidden" name="HTMLcontent" id="HTMLcontent" value='<?php echo stripslashes(replaceQuote($srcHTML)); ?>' />
<!-- cette brique est liée à un objet formulaire -->
		<input type="hidden" name="OBJTABLEcontent" id="OBJTABLEcontent" value="cms_formulaire" />
		<input type="hidden" name="OBJIDcontent" id="OBJIDcontent" value="a_remplir" />
		<input type="hidden" name="VALIDcontent" value="true">
		<input type="hidden" name="ACTIFcontent" value="true">
		<input type="hidden" name="node_id" id="node_id" value="<?php echo $_POST['node_id']; ?>" />
		<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
		<input type="hidden" name="id_form" id="id_form" value="<?php echo $id_form; ?>" />
		<input type="hidden" name="nb_champ" id="nb_champ" value="<?php echo $nb_champ; ?>" />

<!-- champs du formulaire enr à créer : CMS_FORMULAIRE, CMS_CHAMPFORM -->
<?php for ($p=0; $p < DEF_MAXCHAMPSFORM; $p++) { ?>
<!--<input type="hidden" name="name_champ_<?php echo $p; ?>" id="name_champ_<?php echo $p; ?>" value="<?php echo $_POST['name_champ_'.$p]; ?>" />
<input type="hidden" name="type_champ_<?php echo $p; ?>" id="type_champ_<?php echo $p; ?>" value="<?php echo $_POST['type_champ_'.$p]; ?>" />
<input type="hidden" name="valdefaut_champ_<?php echo $p; ?>" id="valdefaut_champ_<?php echo $p; ?>" value="<?php echo $_POST['valdefaut_champ_'.$p]; ?>" />
<input type="hidden" name="largeur_champ_<?php echo $p; ?>" id="largeur_champ_<?php echo $p; ?>" value="<?php echo $_POST['largeur_champ_'.$p]; ?>" />
<input type="hidden" name="taillemax_champ_<?php echo $p; ?>" id="taillemax_champ_<?php echo $p; ?>" value="<?php echo $_POST['taillemax_champ_'.$p]; ?>" />
<input type="hidden" name="obligatoire_champ_<?php echo $p; ?>" id="obligatoire_champ_<?php echo $p; ?>" value="<?php echo $_POST['obligatoire_champ_'.$p]; ?>" />
<input type="hidden" name="valeur_champ_<?php echo $p; ?>" id="valeur_champ_<?php echo $p; ?>" value="<?php echo str_replace("\"", "'", $_POST['valeur_champ_'.$p]); ?>" />
<input type="hidden" name="hauteur_champ_<?php echo $p; ?>" id="hauteur_champ_<?php echo $p; ?>" value="<?php echo $_POST['hauteur_champ_'.$p]; ?>" />-->
<?php } ?>	

		<input type="submit" name="Suite (enregistrer) >>" id="Suite (enregistrer) >>" value="Suite (enregistrer) >>" class="arbo" />
	</form>
	
</table>

</td>
        </tr>
    </table></td>
  </tr>
</table>	
<?php	
} else {
?>
Appel incorrect de la fonctionnalité. Merci de contacter l'administrateur si l'erreur persiste.
<?php
}
?>