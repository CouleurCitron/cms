<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

// sponthus 05/06/2005
// popup utilisée pour l'ajout de champ texte 
// dans une brique formulaire normal
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
if( $_GET["idchamp"] != -1) {
	$oChamp = new Cms_champform($_GET["idchamp"]); 
	$sValeur =  $oChamp->getValdefaut_champ();
	$sLargeur =  $oChamp->getLargeur_champ();
	if ($sLargeur == -1) $sLargeur = "";
	$sHauteur =  $oChamp->getHauteur_champ();
	if ($sHauteur == -1) $sHauteur = "";
	$sTaillemax =  $oChamp->getTaillemax_champ();
	if ($sTaillemax == -1) $sTaillemax = "";
	$sObligatoire =  $oChamp->getObligatoire_champ();
	if ($sObligatoire == "") $sObligatoire = 0;
}
?>
	<style type="text/css">
		@import "/backoffice/cms/css/menu.css";
	</style>

<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<script type="text/javascript"><!--
	function makeit() {
		
		pattern = "";
		if (document.forms['building'].must.checked==true) {
			pattern="^.+$";
		}
		txtLine = '<tr><td class="awsformlabel"> <?php echo $_GET["nomchamp"]; ?> ';
		if (document.forms['building'].must.checked==true) txtLine+= ' * ';
		txtLine+= '</td><td class="awsformfield">';
		txtLine+= '<input type="text" name="<?php echo $_GET["nomchamp"]; ?>" ';
		txtLine+= 'id="<?php echo noAccent($_GET["nomchamp"]); ?>" ';
		txtLine+= 'value="' + document.forms['building'].val.value + '" ';
		txtLine+= 'size="' + document.forms['building'].size.value + '" ';
		txtLine+= 'maxlength="' + document.forms['building'].length.value + '" ';
		txtLine+= 'errorMsg="Le champ <?php echo noAccent($_GET["nomchamp"]); ?>  est obligatoire" pattern="'+ pattern +'" class="form_generic_text">';
		txtLine+= '</td></tr>';	

		window.opener.document.getElementById('valdefaut_champ_<?php echo $_GET["div"]; ?>').value = document.forms['building'].val.value;
		window.opener.document.getElementById('largeur_champ_<?php echo $_GET["div"]; ?>').value = document.forms['building'].size.value;
		window.opener.document.getElementById('taillemax_champ_<?php echo $_GET["div"]; ?>').value = document.forms['building'].length.value;

		if (document.forms['building'].must.checked==true) obligatoire=1; else obligatoire=0;
		window.opener.document.getElementById('obligatoire_champ_<?php echo $_GET["div"]; ?>').value = obligatoire;

		window.opener.document.getElementById('div_<?php echo $_GET["div"]; ?>').innerHTML = txtLine;
		window.opener.document.getElementById('sourceHTML_<?php echo $_GET["div"]; ?>').value = txtLine;
				
		window.close();
	}

--></script>
<form name="building">
<div class="arbo"><b><u>Ajout d'un champ texte :</u></b></div><br />
<table cellpadding="0" cellspacing="0" border="0">
 <tr>
  <td align="right" class="arbo">valeur par défaut :&nbsp;</td>
  <td><input type="text" name="val" size="20" maxlength="50" class="arbo" value="<?php echo $sValeur; ?>"></td>
 </tr>
 <tr>
  <td align="right" class="arbo">largeur (en caractères):&nbsp;</td>
  <td><input type="text" name="size" size="2" value="<?php echo $sLargeur; ?>" maxlength="2" class="arbo"></td>
 </tr>
 <tr>
  <td align="right" class="arbo">Taille Max. (en caractères):&nbsp;</td>
  <td><input type="text" name="length" size="2" value="<?php echo $sTaillemax; ?>" maxlength="2" class="arbo"></td>
 </tr>
 <tr>
  <td align="right" class="arbo">Obligatoire (oui / non):&nbsp;</td>
  <td><input type="checkbox" name="must" value="<?php echo $sObligatoire; ?>" class="arbo"></td>
 </tr>
 <tr>
  <td align="center" colspan="2"><input type="button" name="bouton" value="Ajouter le champ au formulaire" onClick="javascript:makeit()" class="arbo"></td>
 </tr>
</table>
</form>
<script type="text/javascript"><!-- 
if (window.opener.document.getElementById('obligatoire_champ_<?php echo $_GET["div"]; ?>').value == 1) document.building.must.checked = true;

</script>