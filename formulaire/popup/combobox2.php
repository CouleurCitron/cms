<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

// sponthus 05/06/2005
// popup utilisée pour l'ajout de champ texte 
// dans une brique formulaire normal

?>
	<style type="text/css">
		@import "/backoffice/cms/css/menu.css";
	</style>

<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<script type="text/javascript"><!--
	function makeit() {

		pattern = "";
		
		txtLine = '<tr><td align="left" class="awsformlabel"> <?php echo $_GET["nomchamp"]; ?></td>';
		txtLine+= '<td class="awsformfield">';
		txtLine+= '<select name="<?php echo noAccent($_GET["nomchamp"]); ?>" class="form_generic_combo">';
		items = document.forms['building'].items.value.split("\n");
		itemBDD="";

		for (var i=0;i<items.length;i++) {
			value = items[i].replace(/\r/,'');
			value = value.replace(/\n/,'');
			txtLine+='<option value="' + value + '">' + value + "</option>\n";

			if (itemBDD != "") itemBDD = itemBDD + ";" + value;
			else itemBDD = value;
		}

		txtLine+= '</select>';
		txtLine+="</td></tr>";

		window.opener.document.getElementById('valeur_champ_<?php echo $_GET["div"]; ?>').value = itemBDD;

		window.opener.document.getElementById('div_<?php echo $_GET["div"]; ?>').innerHTML = txtLine;
		window.opener.document.getElementById('sourceHTML_<?php echo $_GET["div"]; ?>').value = txtLine;

		window.close();
	}

--></script>
<form name="building">
<div class="arbo"><b><u>Ajout de liste déroulante :</u></b></div><br/><br/>
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
