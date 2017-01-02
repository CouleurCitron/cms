<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

// sponthus 05/06/2005
// popup utilisée pour l'ajout de champ texte 
// dans une brique formulaire normal

?>	<style type="text/css">
		@import "/backoffice/cms/css/menu.css";
	</style>

<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">

<script type="text/javascript"><!--
	function makeit() {

		pattern = "";
		txtLine = "";
		
		items = document.forms['building'].libelle.value.split("\n");
		itemBDD="";

		for (var i=0;i<items.length;i++) {
			value = items[i].replace(/\r/,'');
			value = value.replace(/\n/,'');

			// écriture du libellé la première ligne seulement
			if (i==0) txtLine+= '<tr><td align="right" class="arbo"> <?php echo $_GET["nomchamp"]; ?></td>';
			else txtLine+= '<tr><td align="right" class="arbo"> &nbsp;</td>';

			txtLine+= '<td class="arbo">';

			txtLine+='<input type="hidden" name="hcb_<?php echo $_GET["nomchamp"]; ?>_' + i + '" value="' + value + '">' + "\n";
			txtLine+='<input type="checkbox" name="cb_<?php echo $_GET["nomchamp"]; ?>[]" value="' + value + '" class="arbo">' + value + "<br/>\n";

			txtLine+="</td></tr>";

			if (itemBDD != "") itemBDD = itemBDD + ";" + value;
			else itemBDD = value;
		}

		window.opener.document.getElementById('valeur_champ_<?php echo $_GET["div"]; ?>').value = itemBDD;

		window.opener.document.getElementById('div_<?php echo $_GET["div"]; ?>').innerHTML = txtLine;
		window.opener.document.getElementById('sourceHTML_<?php echo $_GET["div"]; ?>').value = txtLine;

		window.close();
	}

--></script>
<form name="building">
<div class="arbo"><b><u>Ajout d'une case à cocher (checkbox) :</u></b></div><br/><br/>
<table cellpadding="0" cellspacing="0" border="0">
 <tr>
  <td align="right" nowrap class="arbo">Libellé&nbsp;:&nbsp;</td>
  <td align="right"><textarea name="libelle" class="arbo textareaEdit"></textarea></td>
 </tr>
 <tr>
  <td align="center" colspan="2"><input type="button" name="bouton" value="Ajouter le champ au formulaire" onClick="javascript:makeit();" class="arbo"></td>
 </tr>
</table>
</form>
