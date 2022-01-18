<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 
sponthus 03/06/2005

<input type="hidden" name="htmlcontent<?php echo $id_div; ?>" value='<?php echo  str_replace( "'", "`", $oZonedit->getHtml_content() ) ; ?>'>


*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/htmlUtility.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


activateMenu('gestionbrique');  //permet de dérouler le menu contextuellement

// id du formulaire
$id = $_GET['id'];
if ($id == "") $id = $_POST['id'];

// modif d'un composant
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

if($_GET['step']=='init') {
	$step = 0;
}
$bodyform = '<tr id="titre"><td colspan="2"><h1>'.$_POST['nom'].'</h1></td></tr>';
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
<form name="genForm" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?step=1&id=<?php echo $id; ?>">

<div class="arbo2"><b>Créer un formulaire HTML</b></div><br>


<div align="left" class="arbo">Nom du formulaire&nbsp;:&nbsp;</div>
<input type="hidden" name="nom" id="nom" value="<?php echo $_GET['nom']; ?>">
<input type="text" name="name_form" id="nom" pattern="^.+$" errorMsg="Le nom est obligatoire" value="<?php echo $oForm->getName_form(); ?>" size="60" class="arbo"><br/></br>
<input type="hidden" name="desc_form" value="<?php echo $oForm->getDesc_form(); ?>">		
<input type="hidden" name="id" value="<?php echo $id; ?>">
<div align="left" class="arbo">Description &nbsp;:&nbsp;</div>
<textarea name="desc_form" id="desc_form" class="arbo textareaEdit"><?php echo $oForm->getDesc_form(); ?></textarea><br/><br/>

<div align="left" class="arbo">Liste des champs du formulaire : </div><br>
<table>
<?php 
// nombre de champs possibles
for ($p=0; $p < DEF_MAXCHAMPSFORM; $p++) { 
	// objet champ
	if ($p < newSizeOf($aChamp)) $oChamp = $aChamp[$p];
	else $oChamp = new Cms_champform();
?>
	<tr>
		<td class="arbo">champ n° <?php echo $p+1; ?><input type="hidden" name="id_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getId_champ(); ?>"></td>
		<td class="arbo"><input type="text" name="name_champ_<?php echo $p; ?>" class="arbo" value="<?php echo $oChamp->getName_champ(); ?>"></td>
		<td class="arbo">
<?php
if ($oChamp->getType_champ() == "INUTIL") $selectedINUTIL="selected"; else $selectedINUTIL="";
if ($oChamp->getType_champ() == "TEXT") $selectedTEXT="selected"; else $selectedTEXT="";
if ($oChamp->getType_champ() == "CC" || $oChamp->getType_champ() == "CHECKBOX" ) $selectedCC="selected"; else $selectedCC="";
if ($oChamp->getType_champ() == "BR"  || $oChamp->getType_champ() == "RADIO") $selectedBR="selected"; else $selectedBR="";
if ($oChamp->getType_champ() == "COMBO") $selectedCOMBO="selected"; else $selectedCOMBO="";
if ($oChamp->getType_champ() == "AREA" ||  $oChamp->getType_champ() == "TEXTAREA") $selectedAREA="selected"; else $selectedAREA="";
if ($oChamp->getType_champ() == "HTML") $selectedHTML="selected"; else $selectedHTML="";
?>
<select name="type_champ_<?php echo $p; ?>" class="arbo">
	<option value="INUTIL" <?php echo $selectedINUTIL; ?> >champ non utilisé</option>
	<option value="TEXT" <?php echo $selectedTEXT; ?> >champ texte</option>
	<option value="CC" <?php echo $selectedCC; ?> >champ case à cocher</option>
	<option value="BR" <?php echo $selectedBR; ?> >champ bouton radio</option>
	<option value="COMBO" <?php echo $selectedCOMBO; ?> >champ liste déroulante</option>
	<option value="AREA" <?php echo $selectedAREA; ?> >champ zone de texte</option>
	<option value="HTML" <?php echo $selectedHTML; ?> >champ HTML</option>
</select>
		</td>
	</tr>
<?php
}
?>
</table>

<input type="button" name="bouton" value="Suite" pattern="^.+$" onClick="if(validate_form()) { goStep1(); }" class="arbo">
<?php
} elseif ( (isset($_POST['name_form']) && ($_GET['step']==1)) ) {

// contrôle unicité du nom du formulaire
$oForm->setId_form($id);
$oForm->setName_form($_POST['name_form']);
if (checkUniqueName($oForm)) {
	// contrôle unicité nom formulaire ok
} else {
	// contrôle unicité nom formulaire ko

	$sMessage = "<div align='left' class='arbo'>Un formulaire de nom ".$_POST['name_form']." existe déjà.</div><br>";
	$sMessage.= "<div align='left' class='arbo'>Veuillez choisir un nom différent.</div><br>";
	$sMessage.="<a href='formulaireEditor.php?step=init' class='arbo'>retour</a>";
	

	die($sMessage);
}

// etape 2
?><form name="formgeneration" action="<?php echo $_SERVER['PHP_SELF'].'?step=2&id=$id'; ?>" method="post">
<h1>Génération du formulaire <?php echo $_POST['nom'];?></h1>

<input type="hidden" name="nom" value="<?php echo $_POST['name_form']; ?>">
<input type="hidden" name="name_form" value="<?php echo $_POST['name_form']; ?>">
<input type="hidden" name="desc_form" value="<?php echo $_POST['desc_form']; ?>">		
<input type="hidden" name="id" value="<?php echo $id; ?>">

<?php for ($p=0; $p < DEF_MAXCHAMPSFORM; $p++) { ?>
<input type="hidden" name="name_champ_<?php echo $p; ?>" value="<?php echo $_POST['name_champ_'.$p]; ?>">
<input type="hidden" name="type_champ_<?php echo $p; ?>" value="<?php echo $_POST['type_champ_'.$p]; ?>">
<?php } ?>

<br/><br/>
<div align="left" class="arbo">Liste des champs du formulaire : </div><br>
<table cellpadding="2" cellspacing="2">
<?php for ($p=0; $p < DEF_MAXCHAMPSFORM; $p++) { 
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

		if ($sTypeChamp != "INUTIL") {
?>
	<tr>
		<td class="arbo">
<?php
		if ($sTypeChamp == "TEXT") {

			// prépare champ texte
			if ($oChamp->getId_champ() == -1) {
				$champTEXT = "<tr><td class='arbo'>".$oChamp->getName_champ()."</td><td class=\"arbo\">&nbsp;</td></tr>";
			} else {
				if ($oChamp->getObligatoire_champ() == 1) $pattern = "^.+$";
		
				$champTEXT = "<tr><td class='arbo'>".$oChamp->getName_champ()."</td>";
				$champTEXT.= "<td class='arbo'><input type='text' name='".$oChamp->getName_champ()."'";
				$champTEXT.= "value='".$oChamp->getValdefaut_champ()."'";
				$champTEXT.= "size='".$oChamp->getLargeur_champ()."'";
				$champTEXT.= "maxlength='".$oChamp->getTaillemax_champ()."'";
				$champTEXT.= "errorMsg='Ce champ est obligatoire' pattern='".$pattern."' class='arbo'></td></tr>";
			}
?>
		<input type="hidden" name="valdefaut_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getValdefaut_champ(); ?>">
		<input type="hidden" name="largeur_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getLargeur_champ(); ?>">
		<input type="hidden" name="taillemax_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getTaillemax_champ(); ?>">
		<input type="hidden" name="obligatoire_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getObligatoire_champ(); ?>">
		<input type="hidden" name="sourceHTML_<?php echo $p; ?>" value="<?php echo replaceQuote($champTEXT); ?>">
		<div id="div_<?php echo $p; ?>" class="arbo"><table><?php echo $champTEXT; ?></table></div>
<?php
	if ($_POST['obligatoire_champ_'.$p] == 1) { ?><font color="#FF0000">&nbsp;*</font><?php }
?>		
<?php	
		} else if ($oChamp->getType_champ() == "CC" || $oChamp->getType_champ() == "CHECKBOX") {

			// prépare champ case à cocher
			$champCC="";
			if ($oChamp->getValeur_champ() == "") {
				$champCC.= "<tr><td class='arbo'>".$oChamp->getName_champ()."</td><td class=\"arbo\">&nbsp;</td></tr>";
			} else {
				$aCC = explode(";", $oChamp->getValeur_champ());
				for ($t=0; $t<newSizeOf($aCC); $t++)
				{
					$sCC = $aCC[$t];
	
					if ($t == 0) $champCC = "<tr><td class='arbo'>".$oChamp->getName_champ()."</td><td class=\"arbo\">";
					else $champCC = "<tr><td class='arbo'>".$oChamp->getName_champ()."</td><td class=\"arbo\">";
	
					$champCC.= "<input type='checkbox' name='".$oChamp->getName_champ()."' value='Oui' class='arbo'>".$sCC."</td></tr>";
				}
			}
?>
		<input type="hidden" name="valeur_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getValeur_champ(); ?>">
		<input type="hidden" name="sourceHTML_<?php echo $p; ?>" value="<?php echo replaceQuote($champCC); ?>">
		<div id="div_<?php echo $p; ?>"><table><?php echo $champCC; ?></table></div>
<?php	
		} else if ($sTypeChamp == "BR" || $sTypeChamp == "RADIO") {

			// prépare champ bouton radio
			$champBR="";
			if ($oChamp->getValeur_champ() == "") {
				$champBR.= "<tr><td align='right' class='arbo'>".$oChamp->getName_champ()."</td><td class=\"arbo\">&nbsp;</td></tr>";
			} else {
				$aBR = explode(";", $oChamp->getValeur_champ());
				for ($t=0; $t<newSizeOf($aBR); $t++)
				{
					$sBR = $aBR[$t];
					
					if ($t == 0) $champBR.= "<tr><td align='right' class='arbo'>".$oChamp->getName_champ()."</td><td class=\"arbo\">";
					else $champBR.= "<tr><td class='arbo'>&nbsp;</td><td class='arbo'>";
					
					$champBR.= "<input type='radio' name='".$oChamp->getName_champ()."' value='".$sBR."' class='arbo'>".$sBR."</td></tr>";
				}
			}
?>
		<input type="hidden" name="valeur_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getValeur_champ(); ?>">
		<input type="hidden" name="sourceHTML_<?php echo $p; ?>" value="<?php echo replaceQuote($champBR); ?>">
		<div id="div_<?php echo $p; ?>" class="arbo"><table><?php echo $champBR; ?></table></div>		
<?php	
		} else if ($sTypeChamp == "COMBO") {

			// prépare champ combobox
			if ($oChamp->getId_champ() == -1) {
				$champCOMBO.= "<tr><td class='arbo'>".$oChamp->getName_champ()."</td><td class=\"arbo\">&nbsp;</td></tr>";
			} else {
				$aCOMBO = explode(";", $oChamp->getValeur_champ());
	
				$champCOMBO = "<tr><td class='arbo'>".$oChamp->getName_champ()."</td><td class=\"arbo\">";
				$champCOMBO.= "<select name='".$oChamp->getName_champ()."' class='arbo'>";
				for ($t=0; $t<newSizeOf($aCOMBO); $t++)
				{
					$sCOMBO = $aCOMBO[$t];
					$champCOMBO.= "<option value='".$sCOMBO."'>".$sCOMBO;
				}
				$champCOMBO.= "</select>";
				$champCOMBO.= "</td></tr>";
			}
?>
		<input type="hidden" name="valeur_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getValeur_champ(); ?>">
		<input type="hidden" name="sourceHTML_<?php echo $p; ?>" value="<?php echo replaceQuote($champCOMBO); ?>">
		<div id="div_<?php echo $p; ?>" class="arbo"><table><?php echo $champCOMBO; ?></table></div>		
<?php	
		} else if ($sTypeChamp == "AREA" || $sTypeChamp == "TEXTAREA") {

			// prépare champ textarea
			if ($oChamp->getId_champ() == -1) {
				$champAREA = "<tr><td class='arbo'>".$oChamp->getName_champ()."</td><td class=\"arbo\">&nbsp;</td></tr>";
			} else {
				$champAREA = "<tr><td class='arbo'>".$oChamp->getName_champ()."</td><td class=\"arbo\">";
				$champAREA.= "<textarea name='".$oChamp->getName_champ()."' cols='".$oChamp->getHauteur_champ()."' ";
				$champAREA.= "rows='".$oChamp->getLargeur_champ()."' class='arbo'>".$oChamp->getValdefaut_champ();
				$champAREA.= "</textarea>";
				$champAREA.="</td></tr>";
			}		
?>
		<input type="hidden" name="valdefaut_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getValdefaut_champ(); ?>">
		<input type="hidden" name="largeur_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getLargeur_champ(); ?>">
		<input type="hidden" name="hauteur_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getHauteur_champ(); ?>">
		<input type="hidden" name="sourceHTML_<?php echo $p; ?>" value="<?php echo replaceQuote($champAREA); ?>">
		<div id="div_<?php echo $p; ?>" class="arbo"><table><?php echo $champAREA; ?></table></div>
<?php	
		} else if ($sTypeChamp == "HTML") {

			// prépare champ textarea
			$champHTML = "<tr><td class='arbo' colspan=2>".$oChamp->getValeur_champ()."</td></tr>";
?>
		<input type="hidden" name="valeur_champ_<?php echo $p; ?>" value="<?php echo $oChamp->getValeur_champ(); ?>">
		<input type="hidden" name="sourceHTML_<?php echo $p; ?>" value="<?php echo replaceQuote($champHTML); ?>">
		<div id="div_<?php echo $p; ?>" class="arbo"><table><?php echo $champHTML; ?></table></div>
<?php	
		}
?>
		</td>
		<td class="arbo">
<?php
		if ($sTypeChamp == "TEXT") {
?>
<a href="javascript:openBrWindow('popup/text2.php?brique=formulaire&div=<?php echo $p; ?>&nomchamp=<?php echo $_POST['name_champ_'.$p]; ?>', 'preview', 366, 220, 'scrollbars=no', 'true')" class="arbo">modifier le champ texte</a>
<?php
		} else if ($oChamp->getType_champ() == "CC" || $oChamp->getType_champ() == "CHECKBOX") {
?>
<a href="javascript:openBrWindow('popup/checkbox2.php?brique=formulaire&div=<?php echo $p; ?>&nomchamp=<?php echo $_POST['name_champ_'.$p]; ?>', 'preview', 500, 350, 'scrollbars=no', 'true')" class="arbo">modifier la case à cocher</a>
<?php
		} else if ($sTypeChamp == "BR"  || $sTypeChamp == "RADIO") {
?>
<a href="javascript:openBrWindow('popup/radio2.php?brique=formulaire&div=<?php echo $p; ?>&nomchamp=<?php echo $_POST['name_champ_'.$p]; ?>', 'preview', 456, 320, 'scrollbars=no', 'true')" class="arbo">modifier le bouton radio</a>
<?php
		} else if ($sTypeChamp == "COMBO") {
?>
<a href="javascript:openBrWindow('popup/combobox2.php?brique=formulaire&div=<?php echo $p; ?>&nomchamp=<?php echo $_POST['name_champ_'.$p]; ?>', 'preview', 456, 320, 'scrollbars=no', 'true')" class="arbo">modifier la liste déroulante</a>
<?php
		} else if ($sTypeChamp == "AREA" ||  $sTypeChamp == "TEXTAREA") {
?>
<a href="javascript:openBrWindow('popup/textarea2.php?brique=formulaire&div=<?php echo $p; ?>&nomchamp=<?php echo $_POST['name_champ_'.$p]; ?>', 'preview', 366, 220, 'scrollbars=no', 'true')" class="arbo">modifier la zone de texte</a>
<?php
		} else if ($sTypeChamp == "HTML") {
?>
<a href="javascript:openBrWindow('popup/html2.php?brique=formulaire&div=<?php echo $p; ?>&nomchamp=<?php echo $_POST['name_champ_'.$p]; ?>', 'preview', 500, 400, 'scrollbars=no', 'true')" class="arbo">modifier la zone html</a>
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

<input type="hidden" name="sourceHTML" id="sourceHTML" value=""><br>
<input type="button" name="generate4" value="Générer le formulaire" onClick="javascript:envoyerLeTout()" class="arbo">
</script>
</form>
<?php
} elseif ( (isset($_POST['nom']) && ($_GET['step']==2)) ) {
// etape 3
	$srcHTML = $_POST['sourceHTML'];
	
	$srcHTML = "<script src=\"/backoffice/cms/js/validForm.js\"></script>
	<input type=\"hidden\" name=\"formulaire\" value=\"".$_POST['name_form']."\">
	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">".$srcHTML;

	$srcHTML .= "<tr>
	<td align=\"center\"><input type=\"button\" name=\"".$_POST['nom']."\" value=\"Envoyer\" onClick=\"javascript:if(validate_form(".$_POST['name_form'].")){submit();}\" class=\"arbo\"></td>
	<td align=\"center\"><input type=\"reset\" class=\"arbo\"></td>
	</tr>
	</table>";
?>
<h1>Prévisualisation du formulaire "<?php echo $_POST['name_form']; ?>"</h1>
<hr width="485" align="left">
<div style="width: 450px; height: 500px; background-color: #ffffff; overflow: auto;">
<?php 
echo stripslashes($srcHTML); 

$sTypeForm = $_POST['brTypeForm'];

// formulaire mail ou formulaire écrit dans un fichier
$sAction="/frontoffice/eformalites/writeformbdd.php";

$srcHTML = "<form name=\"".preg_replace('/\ /','_',$_POST['name_form'])."MyForm\" action=\"$sAction\" method=\"post\">".$srcHTML;
$srcHTML.= "</form>";
?>
</div>
<hr width="485" align="left">
<small><u>Dimensions&nbsp;:</u>&nbsp;485 x 464&nbsp;pixels<br>Vous pourrez modifier ces dimensions à loisir lors de la mise en page.</small>
	<form name="previewHTML" action="/backoffice/cms/briques/saveComposant.php" method="post">
		<input type="hidden" name="TYPEcontent" value="formulaireHTML">
		<input type="hidden" name="WIDTHcontent" value="450">
		<input type="hidden" name="HEIGHTcontent" value="500">
<!-- la brique aura pour nom le nom du formulaire -->
		<input type="hidden" name="NAMEcontent" value="<?php echo $_POST['name_form']; ?>">
		<input type="hidden" name="desc_form" value="<?php echo $_POST['desc_form']; ?>">		
		<input type="hidden" name="HTMLcontent" value='<?php echo stripslashes($srcHTML); ?>'>
<!-- cette brique est liée à un objet formulaire -->
		<input type="hidden" name="OBJTABLEcontent" value="cms_formulaire">
		<input type="hidden" name="OBJIDcontent" value="a_remplir">

		<input type="hidden" name="id" value="<?php echo $id; ?>">

<!-- champs du formulaire enr à créer : CMS_FORMULAIRE, CMS_CHAMPFORM -->
<?php for ($p=0; $p < DEF_MAXCHAMPSFORM; $p++) { ?>
<input type="hidden" name="name_champ_<?php echo $p; ?>" value="<?php echo $_POST['name_champ_'.$p]; ?>">
<input type="hidden" name="type_champ_<?php echo $p; ?>" value="<?php echo $_POST['type_champ_'.$p]; ?>">
<input type="hidden" name="valdefaut_champ_<?php echo $p; ?>" value="<?php echo $_POST['valdefaut_champ_'.$p]; ?>">
<input type="hidden" name="largeur_champ_<?php echo $p; ?>" value="<?php echo $_POST['largeur_champ_'.$p]; ?>">
<input type="hidden" name="taillemax_champ_<?php echo $p; ?>" value="<?php echo $_POST['taillemax_champ_'.$p]; ?>">
<input type="hidden" name="obligatoire_champ_<?php echo $p; ?>" value="<?php echo $_POST['obligatoire_champ_'.$p]; ?>">
<input type="hidden" name="valeur_champ_<?php echo $p; ?>" value="<?php echo $_POST['valeur_champ_'.$p]; ?>">
<input type="hidden" name="hauteur_champ_<?php echo $p; ?>" value="<?php echo $_POST['hauteur_champ_'.$p]; ?>">
<?php } ?>	

		<input type="submit" name="Suite (enregistrer) >>" value="Suite (enregistrer) >>" class="arbo">
	</form>
<?php	
} else {
?>
Appel incorrect de la fonctionnalité. Merci de contacter l'administrateur si l'erreur persiste.
<?php
}
?>