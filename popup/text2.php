<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

// sponthus 05/06/2005
// popup utilis�e pour l'ajout de champ texte 
// dans une brique formulaire normal

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
		txtLine = '<tr><td align="right" class="arbo"> <?php echo $_GET["nomchamp"]; ?></td>';
		txtLine+= '<td>';
		txtLine+= '<input type="text" name="<?php echo $_GET["nomchamp"]; ?>" ';
		txtLine+= 'value="' + document.forms['building'].val.value + '" ';
		txtLine+= 'size="' + document.forms['building'].size.value + '" ';
		txtLine+= 'maxlength="' + document.forms['building'].length.value + '"';
		txtLine+= 'errorMsg="Ce champ est obligatoire" pattern="'+ pattern +'" class="arbo">';
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
  <td align="right" class="arbo">valeur par d�faut :&nbsp;</td>
  <td><input type="text" name="val" size="20" maxlength="50" class="arbo"></td>
 </tr>
 <tr>
  <td align="right" class="arbo">largeur (en caract�res):&nbsp;</td>
  <td><input type="text" name="size" size="2" value="25" maxlength="2" class="arbo"></td>
 </tr>
 <tr>
  <td align="right" class="arbo">Taille Max. (en caract�res):&nbsp;</td>
  <td><input type="text" name="length" size="2" value="25" maxlength="2" class="arbo"></td>
 </tr>
 <tr>
  <td align="right" class="arbo">Obligatoire (oui / non):&nbsp;</td>
  <td><input type="checkbox" name="must" value="1" class="arbo"></td>
 </tr>
 <tr>
  <td align="center" colspan="2"><input type="button" name="bouton" value="Ajouter le champ au formulaire" onClick="javascript:makeit()" class="arbo"></td>
 </tr>
</table>
</form>
