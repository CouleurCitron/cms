<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Formulaire - Bouton Radio</title>
<?php

// sponthus 05/06/2005
// popup utilisée pour l'ajout de champ Bouton Radio 
// dans une brique formulaire normal

?>
	<style type="text/css">
		@import "/backoffice/cms/css/menu.css";
	</style>

<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">

<script language="javascript"><!--
	function makeit() {

		pattern = "";
		txtLine = "";
		items = document.forms['building'].items.value.split("\n");
		itemBDD="";
		
		for (var i=0;i<items.length;i++) {
			value = items[i].replace(/\r/,'');
			value = value.replace(/\n/,'');
			
			// écriture du libellé la première ligne seulement
			if (i==0) txtLine+= '<tr><td align="left" class="awsformlabel"><?php echo $_GET["nomchamp"]; ?>&nbsp;</td>';
			else txtLine+= '<tr><td align="left" class="awsformlabel">&nbsp;</td>';
			
			txtLine+= '<td class="awsformfield">';
			txtLine+='<input type="radio" name="<?php echo $_GET["nomchamp"]; ?>" id="<?php echo $_GET["nomchamp"]; ?>" value="' + value + '" class="awsformfield">' + value;
			txtLine+="</td></tr>";

			if (itemBDD != "") itemBDD = itemBDD + ";" + value;
			else itemBDD = value;

		}
		txtLine += "";

		window.opener.document.getElementById('valeur_champ_<?php echo $_GET["div"]; ?>').value = itemBDD;

		window.opener.document.getElementById('div_<?php echo $_GET["div"]; ?>').innerHTML = txtLine;
		window.opener.document.getElementById('sourceHTML_<?php echo $_GET["div"]; ?>').value = txtLine;

		window.close();
	}
	function loadit() {		
		document.forms['building'].items.value = window.opener.document.getElementById('valeur_champ_<?php echo $_GET["div"]; ?>').value.replace(/;/gi,'\n');
	}
	
	window.onload = loadit;
--></script>
</head>
<body>
<form name="building">
<div class="arbo"><b><u>Ajout de boutons radios (choix) :</u></b></div><br/><br/>
<table cellpadding="0" cellspacing="0" border="0">
 <tr>
  <td align="right" nowrap class="arbo">Liste des choix&nbsp;:&nbsp;<br/>(un par ligne)</td>
  <td align="right"><textarea name="items" class="arbo textareaEdit"></textarea></td>
 </tr>
<tr>
  <td align="center" colspan="2"><input type="button" name="bouton" value="Ajouter le champ au formulaire" onClick="javascript:makeit();" class="arbo"></td>
 </tr>
</table>
</form>
</body>
</html>