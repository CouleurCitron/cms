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


		/*pattern = new RegExp(fieldObj.getAttribute("pattern"));
		str = fieldObj.value;	
		finalValue = str.match(pattern); */
	
		pattern = "";
		txtLine = "";
		
		items = document.forms['building'].libelle.value.split("\n");
		itemBDD="";

		for (var i=0;i<items.length;i++) {
			value = items[i].replace(/\r/,'');
			value = value.replace(/\n/,'');
			if (value!="") {
				value_ = value.split("//");
				valueLibelle = value_[0];
				valueDest = value_[1];
				// écriture du libellé la première ligne seulement
				if (i==0) txtLine+= '<tr><td align="right" class="arbo"> <?php echo $_GET["nomchamp"]; ?></td>';
				else txtLine+= '<tr><td align="right" class="arbo"> &nbsp;</td>';
	
				txtLine+= '<td class="arbo">';
	
				txtLine+='<input type="checkbox" name="<?php echo noAccent($_GET["nomchamp"]); ?>_'+i+'" id="<?php echo noAccent($_GET["nomchamp"]); ?>_'+i+'" value="' + valueDest + '" class="form_generic_ccdes"> ' + valueLibelle + '<br/>\n';
				txtLine+="</td></tr>";
				//txtLine+='<input type="hidden" name="hcb_<?php echo $_GET["nomchamp"]; ?>_' + i + '" id="hcb_<?php echo $_GET["nomchamp"]; ?>_' + i + '" value="' + valueDest + '" >' + "\n";
				//txtLine+='<input type="hidden" name="valeurCC_'+ i +'_<?php echo $_GET["div"]; ?>" id="valeurCC_'+ i +'_<?php echo $_GET["div"]; ?>" value="'+valueDest+'" >\n';
				//txtLine+='<input type="hidden" name="libelleCC_'+ i +'_<?php echo $_GET["div"]; ?>" id="libelleCC_'+ i +'_<?php echo $_GET["div"]; ?>" value="'+valueLibelle+'" >\n';
	
				if (itemBDD != "") itemBDD = itemBDD + ";" + value;
				else itemBDD = value;
			}
		} 
					
		window.opener.document.getElementById('valeur_champ_<?php echo $_GET["div"]; ?>').value = itemBDD;
		window.opener.document.getElementById('nb_CC_<?php echo $_GET["div"]; ?>').value = items.length; 
		window.opener.document.getElementById('div_<?php echo $_GET["div"]; ?>').innerHTML = txtLine;
		window.opener.document.getElementById('sourceHTML_<?php echo $_GET["div"]; ?>').value = txtLine;

		window.close();
	}

--></script>
<form name="building">
<div class="arbo"><b><u>Ajout d'une case à cocher (checkbox) :</u></b></div><br/><br/>
<table cellpadding="0" cellspacing="0" border="0">
 <tr>
  <td align="right" colspan="2" class="arbo">
  Saisir par ligne la valeur et l'email de destination séparé par "//"<br />
  Ex : valeur_afficher//email_destination
  </td> 
 </tr>
 <tr>
  <td align="right" nowrap class="arbo">Libellé&nbsp;:&nbsp;</td>
  <td align="left"><textarea name="libelle" id="libelle" class="arbo textareaEdit"></textarea></td>
 </tr>
 
 <tr>
  <td align="center" colspan="2"><input type="button" name="bouton" value="Ajouter le champ au formulaire" onClick="javascript:makeit();" class="arbo"></td>
 </tr>
</table>
</form>
<script type="text/javascript"><!-- 
nb_CC = window.opener.document.getElementById('nb_CC_<?php echo $_GET["div"]; ?>').value;
texte = "";
for (var i=0;i<nb_CC;i++) {
	texte+= window.opener.document.getElementById('libelleCC_'+i+'_<?php echo $_GET["div"]; ?>').value;
	texte+= "//";
	texte+= window.opener.document.getElementById('valeurCC_'+i+'_<?php echo $_GET["div"]; ?>').value;
	texte+= "\n";
} 
/*
items = texte.split("(destinataire : "); 

var aCClibelle = array();
for (var i=0;i<items.length;i++) {
	aLibelle = items[i]; 
	aLibelle_ = aLibelle.split("class='arbo'>");
	libelle = aLibelle_[2]; 
	if (libelle!="" && libelle!= undefined) {
		libelle = libelle.replace(" ",''); 
		libelle = libelle.replace("<br/>",''); 
		aCClibelle[]=libelle;
	}
	
}  
for (var i=1;i<items.length;i++) { 
	texte = items[i]; 
	items2 = texte.split(')');
	valeur = items2[0];
	//texte_ = texte_.replace('>',''); 
	if (valeur!="" && valeur!= undefined) {
		valeur = valeur.replace("<br/>",''); 
		aCCvalue[]=valeur;
	}
}*/
document.building.libelle.value = texte;
 
</script>