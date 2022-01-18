<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
sponthus 07/06/2005

modification d'un gabarit

*/


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/procedures.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


activateMenu('gestiongabarit');  //permet de dérouler le menu contextuellement

// id du gabarit
$id = $_POST['id'];
if ($id == "") $id = $_GET['id'];

if(strlen($id)>0){
	$divArray = getPageStructure($id);
	$_SESSION['divArray']=$divArray;
	$tmparray = array_keys($divArray);
	$array = array();
	foreach($tmparray as $k => $v) {
		if(is_int($v)) {
			$array[$k] = $v;
		}
	}
	$gabName = $divArray['gabarit'];
		
	// A CHANGER /////////////////////////////
	// on renseigne gentiment $_SESSION['GabaritName'] avec le nom du gabarit courant
	$oTempGab = new Cms_page($id);
	$_SESSION['GabaritName']=$oTempGab->getName_page();
	//////////////////////////////////////////

		$_SESSION[$_SESSION['GabaritName']] = $array;
	}

	// gabarit
	$oGab = new Cms_page($id);

	$widthGab = $oGab->getWidth_page();
	$heightGab = $oGab->getHeight_page();

	// compte le nombre de zones editables de ce gabarit
	$aZonedit = getContentFromPage($id, 1);


?>
<style type="text/css">
<!--
.style1 {
	color: #FF0000;
	font-weight: bold;
}
-->
</style>

<span class="arbo2">GABARIT&nbsp;>&nbsp;</span>
<span class="arbo3">Modifier un gabarit - Etape 1</span><br>
<br><?php


// nombre de zones editables
$nbZoneEdit = $_POST['nbZoneEdit'];
if ($nbZoneEdit == "") $nbZoneEdit = 1;

// nombre de zones editables ajoutées
$nbZoneEditAjout = $_POST['nbZoneEditAjout'];
if ($nbZoneEditAjout == "") $nbZoneEditAjout = 0;


?>
<script type="text/javascript">document.title="Pages BB";</script>
<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
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
<script>

// varification du formulaire
function checkForm()
{
	erreur=0;
	sMessage = "Valeur(s) incorrecte(s) : \n\n";
	// le champ nbZoneEdit doit être numérique
	if (isNaN(document.gabaritForm.nbZoneEdit.value)) {
		sMessage+="- Le nombre de zones éditables doit être numérique\n";
		erreur++;
	}
	
	
	if (document.gabaritForm.nbZoneEditAjout.value!=document.gabaritForm.enumero.value) {
		sMessage+="- Vous devez choisir les dimensions de toutes vos zones éditables.\nPour cela, cliquez sur le bouton AFFICHER";
		erreur++;
	}
	
	// affichage des messages d'erreur
	if (erreur > 0) {
		alert(sMessage);
		return false;
	}
	else return true;

}

// suite de création des gabarits
function suite()
{
	if (checkForm())
	{
		// le nombre final de zones editables est :
		// la somme des zones edit de ce gabarit plus celles que l'on vient d'ajouter
		
		// zones edit de ce gabarit
		nbZoneEditGab = new Number(document.gabaritForm.nbZoneEditGab.value);
		// zones editables ajoutées
		nbZoneEditAjout = new Number(document.gabaritForm.nbZoneEditAjout.value);
		
		document.gabaritForm.nbZoneEdit.value = nbZoneEditAjout + nbZoneEditGab;

		document.gabaritForm.action = "gabaritEditor2.php";
		document.gabaritForm.submit();
	}
}

// rechargement de la première page pour saisir 
// les noms des zones editables + les tailles par défaut
function affichZonedit()
{
	document.gabaritForm.action = "gabaritModif.php";
	document.gabaritForm.submit();
}

</script>
<form name="gabaritForm" method="post">

  <input type="hidden" name="id" value="<?php echo $id; ?>">
    
  <?php
// site de travail
//if (DEF_MENUS_MINISITES == "ON") print(putAfficheSite());
?>
  <span class="arbo">Vous allez modifier le gabarit (modèle de page) <strong>
  <?php echo $oGab->getName_page(); ?>
  </strong>. </span>
  <br /><br />
  <span class="arbo">
  Dans ce gabarit vous allez définir des éléments qui présentent des caractéristiques communes à différentes pages.<br>
  </span>
    <table cellpadding="3" cellspacing="3" class="arbo">
    <tr>
	    <td>&nbsp;</td>
	    <td>Largeur</td>
	    <td>Hauteur</td>
    </tr>
    <tr>
	    <td>Taille de l'espace &eacute;ditable</td>
	    <td><input type="text" class="arbo" name="fWidth_toutelazone" id="fWidth_toutelazone" value="<?php echo $widthGab; ?>" size="4" maxlength="4"></td>
	    <td><input type="text" class="arbo" name="fHeight_toutelazone" id="fHeight_toutelazone" value="<?php echo $heightGab; ?>" size="4" maxlength="4"></td>
    </tr>
</table>

  <p><span class="arbo"><br>
    Vous allez aussi définir des zones éditables de contenu qui seront modifiables sur chaque page<br>
    <br>
    <br>
      
Voici la liste des zones editables de ce gabarit : <br>
    <br>
    Vous pourrez supprimer une zone editable dans la page suivante.<br>
    <br>
      
  </span></p>
  <span class="arbo">
<table cellpadding="3" cellspacing="3">
  <?php
if (newSizeOf($aZonedit) != 0) {
?>
    <tr>
	    <tD>&nbsp;</tD>
	    <td>Nom</td>
	    <td>Largeur</td>
	    <td>Hauteur</td>
    </tr>
  <?php
}

for ($t=0; $t < newSizeOf($aZonedit); $t++)
{
	$oZonedit = $aZonedit[$t];
	$eNumero = $t + 1;

	////////////////////////////////////////////////////////
	// ATTENTION au renommage des zones editables
	//
	// A FAIRE :
	// 1. liste toutes les briques editables de cette zone editable
	// 2. pour chaque brique : appel à la fonction updateNomBriqueEdit($idContent, $idPage)
	//    (renommage de toutes les briques editables de la forme nomdelazoneditable_nomdelapage)
	// 
	// pour exemple cette fonction est appelée en pageLiteEditor6.php après création de la page
	// en attendant cette fonctionnalité de renommage est bridée
	////////////////////////////////////////////////////////

	// objet struct
	$oStruct = new Cms_struct_page();
	$oStruct->getObjet($oGab->getId_page(), $oZonedit->getId_content());

	$height = $oStruct->getHeight_content();
	$width = $oStruct->getWidth_content();

?>
    <tr>
	    <td>zone <?php echo $eNumero; ?></td>
	    <td><input type="hidden" name="fId_<?php echo $t; ?>" value="<?php echo $oZonedit->getId_content(); ?>">
		  <input type="text" disabled name="pour_affichage" value="<?php echo $oZonedit->getName_content(); ?>" class="arbo">
	    <input type="hidden" name="fName_<?php echo $t; ?>" value="<?php echo $oZonedit->getName_content(); ?>" class="arbo" size="40"></td>
	    <td><input type="text" name="fWidth_<?php echo $t; ?>" value="<?php echo $width; ?>" class="arbo" size="4" maxlength="4"></td>
	    <td><input type="text" name="fHeight_<?php echo $t; ?>" value="<?php echo $height; ?>" class="arbo" size="4" maxlength="4"></td>
    </tr>
  <?php
}
?>
</table>
<br>
<br>
Combien de zones éditables voulez vous ajouter dans ce gabarit ?&nbsp;
<input type="hidden" name="nbZoneEditGab" value="<?php echo newSizeOf($aZonedit); ?>">
<input type="hidden" name="nbZoneEdit" value="<?php echo $nbZoneEdit; ?>">
<input type="text" class="arbo" name="nbZoneEditAjout" value="<?php echo $nbZoneEditAjout; ?>" size="5">
&nbsp;
<input type="button" class="arbo" value="Afficher" onClick="javascript:affichZonedit()">
<br>
<br>

<table cellpadding="3" cellspacing="3">
  <?php
if ($nbZoneEditAjout != 0) {
?>
    <tr>
	    <tD>&nbsp;</tD>
	    <td>Nom</td>
	    <td>Largeur</td>
	    <td>Hauteur</td>
    </tr>
  <?php
}

for ($t=newSizeOf($aZonedit); $t < newSizeOf($aZonedit)+$nbZoneEditAjout; $t++)
{
	$eNumero = $t + 1;
?>
    <tr>
	    <td>zone <?php echo $eNumero; ?><input type="hidden" name="fId_<?php echo $t; ?>" value=""></td>
	    <td><input type="text" name="fName_<?php echo $t; ?>" value="<?php echo DEF_ZONEDIT; ?>" class="arbo" size="40"></td>
	    <td><input type="text" name="fWidth_<?php echo $t; ?>" value="<?php echo DEF_ZONEDITWIDTH; ?>" class="arbo" size="4" maxlength="4"></td>
	    <td><input type="text" name="fHeight_<?php echo $t; ?>" value="<?php echo DEF_ZONEDITHEIGHT; ?>" class="arbo" size="4" maxlength="4"></td>
    </tr>
  <?php
}
?><input type="hidden" id="enumero" name="enumero" value="<?php echo $eNumero-newSizeOf($aZonedit); ?>">
</table>
<br>
<br>
<div align="left"><input type="button" class="arbo" value="Suite" onClick="suite()">
</div>
  </span>
</form>