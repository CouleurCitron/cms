<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Formulaire - Zone de Texte</title>
<?php

// sponthus 05/06/2005
// popup utilisée pour l'ajout de champ Zone de Texte 
// dans une brique formulaire normal

?>
	<style type="text/css">
		@import "/backoffice/cms/css/menu.css";
	</style>

<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<script language="javascript"><!--
	function makeit() {
	
		pattern = "";
		if (document.forms['building'].must.checked==true) {
			pattern="^.+$";
			txtLine = '<tr><td align="left" class="awsformlabel"><?php echo $_GET["nomchamp"]; ?>&nbsp;*&nbsp;</td>';
		}
		else {
			pattern="";
			txtLine = '<tr><td align="left" class="awsformlabel"><?php echo $_GET["nomchamp"]; ?>&nbsp;</td>';
		}

		
		txtLine+= '<td arbo="awsformfield">';
		txtLine+= '<textarea name="<?php echo $_GET["nomchamp"]; ?>" id="<?php echo $_GET["nomchamp"]; ?>"';
		txtLine+= 'errorMsg="Le champ <?php echo $_GET["nomchamp"]; ?> est obligatoire" pattern="'+ pattern +'" ';
		txtLine+= 'class="awsformfield textareaEdit">' + document.forms['building'].val.value;
		txtLine+= '</textarea>';
		txtLine+="</td></tr>";

		window.opener.document.getElementById('valdefaut_champ_<?php echo $_GET["div"]; ?>').value = document.forms['building'].val.value;
		window.opener.document.getElementById('largeur_champ_<?php echo $_GET["div"]; ?>').value = document.forms['building'].cols.value;
		window.opener.document.getElementById('hauteur_champ_<?php echo $_GET["div"]; ?>').value = document.forms['building'].rows.value;
		
		if (document.forms['building'].must.checked==true) obligatoire=1; else obligatoire=0;
		window.opener.document.getElementById('obligatoire_champ_<?php echo $_GET["div"]; ?>').value = obligatoire;
				
		window.opener.document.getElementById('div_<?php echo $_GET["div"]; ?>').innerHTML = txtLine;
		window.opener.document.getElementById('sourceHTML_<?php echo $_GET["div"]; ?>').value = txtLine;

		window.close();
	}
	function loadit() {		
		if (window.opener.document.getElementById('obligatoire_champ_<?php echo $_GET["div"]; ?>').value == 1){
			document.forms['building'].must.checked=true;
		}
		document.forms['building'].val.value = window.opener.document.getElementById('valdefaut_champ_<?php echo $_GET["div"]; ?>').value;
		document.forms['building'].cols.value = window.opener.document.getElementById('largeur_champ_<?php echo $_GET["div"]; ?>').value;
		document.forms['building'].rows.value = window.opener.document.getElementById('hauteur_champ_<?php echo $_GET["div"]; ?>').value;
	}
	
	window.onload = loadit;
--></script>
</head>
<body><form name="building">
<div class="arbo"><b><u>Ajout d'un champ TextArea :</u></b></div><br/><br/>
<table cellpadding="0" cellspacing="0" border="0">
 <tr>
  <td align="right" class="arbo">valeur par défaut :&nbsp;</td>
  <td><input type="text" name="val" size="20" maxlength="50" class="arbo"></td>
 </tr>
 <tr>
  <td align="right" class="arbo">largeur (en caractères):&nbsp;</td>
  <td><input type="text" name="cols" size="2" value="25" maxlength="2" class="arbo"></td>
 </tr>
 <tr>
  <td align="right" class="arbo">hauteur (en lignes):&nbsp;</td>
  <td><input type="text" name="rows" size="2" value="5" maxlength="2" class="arbo"></td>
 </tr>
 <tr>
  <td align="right" class="arbo">Obligatoire (oui / non):&nbsp;</td>
  <td><input type="checkbox" name="must" id="must" value="1" class="arbo"></td>
 </tr>
<tr>
  <td align="center" colspan="2"><input type="button" name="bouton" value="Ajouter le champ au formulaire" onClick="javascript:makeit();" class="arbo"></td>
 </tr>
</table>
</form>
</body>
</html>