<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

// sponthus 05/06/2005
// popup utilis�e pour l'ajout de champ texte 
// dans une brique formulaire normal
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
if( $_GET["idchamp"] != -1) {
	$oChamp = new Cms_champform($_GET["idchamp"]); 
	$sValeur = str_replace(";", "\n", $oChamp->getValeur_champ());
}
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

			// �criture du libell� la premi�re ligne seulement
			if (i==0) txtLine+= '<tr><td align="left" class="awsformlabel"> <?php echo $_GET["nomchamp"]; ?></td>';
			else txtLine+= '<tr><td align="left" class="awsformlabel"> &nbsp;</td>';

			txtLine+= '<td class="awsformfield">';

			txtLine+='<input type="hidden" name="hcb_<?php echo noAccent($_GET["nomchamp"]); ?>_' + i + '" id="hcb_<?php echo noAccent($_GET["nomchamp"]); ?>_' + i + '" value="' + value + '">' + "\n";
			txtLine+='<input type="checkbox" name="cb_<?php echo noAccent($_GET["nomchamp"]); ?>_' + i + '" id="cb_<?php echo noAccent($_GET["nomchamp"]); ?>_' + i + '" value="' + value + '" class="form_generic_cc">' + value + "<br/>\n";

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
<form name="building" method="post" id="building" action="<?php $_SERVER['PHP_SELF']; ?>">
<div class="arbo"><b><u>Ajout d'une case � cocher (checkbox) :</u></b></div><br/><br/>
<table cellpadding="0" cellspacing="0" border="0">
 <tr>
  <td align="right" nowrap class="arbo">Libell�&nbsp;:&nbsp;</td>
  <td align="right"><textarea name="libelle" class="arbo textareaEdit"><?php echo $sValeur; ?></textarea></td>
 </tr>
 <tr>
  <td align="center" colspan="2"><input type="button" name="bouton" value="Ajouter le champ au formulaire" onClick="javascript:makeit();" class="arbo"></td>
 </tr>
</table>
</form>
