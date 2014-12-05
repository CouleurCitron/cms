<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Formulaire - Champ Texte</title>
<?php

// sponthus 05/06/2005
// popup utilisée pour l'ajout de champ expéditeur 
// dans une brique formulaire normal

?>
	<style type="text/css">
		@import "/css/menu.css";
	</style>

<link rel="stylesheet" type="text/css" href="/css/bo.css">
<script language="javascript"><!--
	function makeit() {
		
		pattern = "";
		//if (document.forms['building'].must.checked==true) {
			pattern="^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$";
		//}
		if (document.forms['building'].must.checked==true) {
			txtLine = '<tr><td align="right" class="awsformlabel"><?php echo $_GET["nomchamp"]; ?>&nbsp;*&nbsp;</td>';
		}
		else {
			txtLine = '<tr><td align="right" class="awsformlabel"><?php echo $_GET["nomchamp"]; ?>&nbsp;</td>';
		} 
		txtLine+= '<td class="awsformfield">';
		txtLine+= '<input type="text" name="fromperso_<?php echo $_GET["nomchamp"]; ?>" id="fromperso_<?php echo $_GET["nomchamp"]; ?>" ';
		txtLine+= 'value="' + document.forms['building'].val.value + '" ';
		txtLine+= 'size="' + document.forms['building'].size.value + '" ';
		txtLine+= 'maxlength="' + document.forms['building'].length.value + '" ';
		txtLine+= 'errorMsg="Le champ <?php echo $_GET["nomchamp"]; ?> doit respecter la syntaxe d un email" pattern="'+ pattern +'" class="awsformfield">';
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
	function loadit() {
		document.forms['building'].val.value = window.opener.document.getElementById('valdefaut_champ_<?php echo $_GET["div"]; ?>').value;
		document.forms['building'].size.value = window.opener.document.getElementById('largeur_champ_<?php echo $_GET["div"]; ?>').value;
		document.forms['building'].length.value = window.opener.document.getElementById('taillemax_champ_<?php echo $_GET["div"]; ?>').value;
		if (window.opener.document.getElementById('obligatoire_champ_<?php echo $_GET["div"]; ?>').value == 1){
			document.forms['building'].must.checked=true;
		}
		if (document.forms['building'].size.value == 0){
			document.forms['building'].size.value = 50;
		}
		if (document.forms['building'].length.value == 0){
			document.forms['building'].length.value = 50;
		}
	}
	
	window.onload = loadit;
--></script>
</head>
<body>
<form name="building" id="building">
<div class="arbo"><b><u>Ajout d'un champ expéditeur :</u></b></div><br />
<table cellpadding="0" cellspacing="0" border="0">
 <tr>
  <td align="right" class="arbo">valeur par défaut :&nbsp;</td>
  <td><input type="text" name="val" id="val" size="20" maxlength="50" class="arbo"></td>
 </tr>
 <tr>
  <td align="right" class="arbo">largeur (en caractères):&nbsp;</td>
  <td><input type="text" name="size" id="size" size="2" value="25" maxlength="2" class="arbo"></td>
 </tr>
 <tr>
  <td align="right" class="arbo">Taille Max. (en caractères):&nbsp;</td>
  <td><input type="text" name="length" id="length" size="2" value="25" maxlength="2" class="arbo"></td>
 </tr>
 <tr>
  <td align="right" class="arbo">Obligatoire (oui / non):&nbsp;</td>
  <td><input type="checkbox" name="must" id="must" value="1" class="arbo"></td>
 </tr>
 <tr>
  <td align="center" colspan="2"><input type="button" name="bouton" value="Ajouter le champ au formulaire" onClick="javascript:makeit()" class="arbo"></td>
 </tr>
</table>
</form>
</body>
</html>