<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

// sponthus 05/06/2005
// popup utilisée pour l'ajout de champ texte 
// dans une brique formulaire normal
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/utils/chars.lib.php');
?>
	<style type="text/css">
		@import "/backoffice/cms/css/menu.css";
	</style>

<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">

<script type="text/javascript"><!--
	function makeit() {
		
		/*if (document.forms['building'].theme.value == "") {
			alert("Le nom de la newsletter associée est obligatoire\n");
		}
		else {*/
			pattern = "";
			txtLine = "";
			itemOui = document.forms['building'].oui.value;
			itemBDD="";
			pattern = "";
			if (document.forms['building'].must.checked==true) {
				pattern="^.+$";
			}
			if (document.forms['building'].must.checked==true) obligatoire=1; else obligatoire=0;
			window.opener.document.getElementById('obligatoire_champ_<?php echo $_GET["div"]; ?>').value = obligatoire;
			//for (var i=0;i<items.length;i++) {
			valueOui = itemOui.replace(/\r/,'');
			valueOui = valueOui.replace(/\n/,'');
			
			// écriture du libellé la première ligne seulement
			
			txtLine+= '<tr><td class="arbo">';
			txtLine+='<input type="radio" name="<?php echo noAccent($_GET["nomchamp"]);; ?>" id="<?php echo noAccent($_GET["nomchamp"]);; ?>" value="1" class="arbo"'; 
			txtLine+=' onClick="this.form.<?php echo noAccent($_GET["nomchamp"]); ?>[0].checked = true;this.form.<?php echo noAccent($_GET["nomchamp"]);; ?>[1].checked = false;" ';
			txtLine+=' > oui&nbsp;&nbsp;';  
			txtLine+='<input type="radio" name="<?php echo noAccent($_GET["nomchamp"]);; ?>" id="<?php echo noAccent($_GET["nomchamp"]);; ?>" value="0" class="arbo"'; 
			txtLine+=' checked ';
			txtLine+=' onClick="this.form.<?php echo noAccent($_GET["nomchamp"]);; ?>[1].checked = true;this.form.<?php echo noAccent($_GET["nomchamp"]);; ?>[0].checked = false;" ';
			txtLine+=' > non&nbsp;&nbsp;';  
			txtLine+='' + valueOui ;
			if (document.forms['building'].must.checked==true) txtLine+=' * ';
			if (document.forms['building'].must.checked==true) {
					//vérifie si au moins un bouton est coché
				/*txtLine+='<script type="text/javascript">\n';
	
				txtLine+='function fieldchecked() {\n';
				txtLine+='var tab_div = document.getElementsByTagName("INPUT");\n';
				txtLine+='sMessage="";\n';
				txtLine+='bcoche = true;\n';
				txtLine+='cpt =0;\n';
				txtLine+='nbcpt = 0;\n';
				txtLine+='for (i = 0; i < tab_div.length; i++) {\n';
				txtLine+='if(tab_div[i].type == "radio") {\n';
				txtLine+='nbcpt = nbcpt +1;\n';
				txtLine+='var block = ""+tab_div[i].id;   \n';
				txtLine+='if (document.getElementById(block).checked == true) {\n';
				txtLine+='cpt = cpt +1;\n';
				txtLine+='}\n';
				txtLine+='else {\n';
				txtLine+='}\n';
				txtLine+='lastValue = block;  \n';
				txtLine+='}\n';
				txtLine+='}\n';
				txtLine+='if ( cpt == 0 && nbcpt>0) {\n';
				txtLine+='alert(block);\n';
				txtLine+='block = block.split("[");  \n';
				txtLine+='sMessage+= "Le champ "+block[0]+" n\'est pas coché\n";\n';
				txtLine+='bcoche = false;\n';
				txtLine+='alert(sMessage)\n';
				txtLine+='}  \n';
				txtLine+='return (bcoche);\n';
					 
				txtLine+='}\n';
				txtLine+='</script>\n';
				*/
			}
			
			txtLine+='</td></tr>';
				 
					 
				 
			//}
			txtLine += "";
			
			reponse = '<tr><td align="right" class="arbo">';
			items = document.forms['building'].items.value.split("\n");  
			for (var i=0;i<items.length;i++) {
				value = items[i].replace(/\r/,'');
				value = value.replace(/\n/,'');
				reponse+=value+'<br>\n'; 
			} 
			reponse+="</td></tr>";
			
			window.opener.document.getElementById('valeur_champ_<?php echo $_GET["div"]; ?>').value = valueOui;
			window.opener.document.getElementById('div_<?php echo $_GET["div"]; ?>').innerHTML = txtLine;
			window.opener.document.getElementById('sourceHTML_<?php echo $_GET["div"]; ?>').value = txtLine; 
			window.opener.document.getElementById('texte_champ_<?php echo $_GET["div"]; ?>').value = reponse;
			window.opener.document.getElementById('objet_champ_<?php echo $_GET["div"]; ?>').value = "news_theme";
			window.opener.document.getElementById('objetid_champ_<?php echo $_GET["div"]; ?>').value = document.forms['building'].theme.value;
	
			window.close();
		}
	//}

--></script>
<form name="building" id="building">
<div class="arbo"><b><u>Personnalisation du texte pour l'abonnement :</u></b></div><br/><br/>
<table cellpadding="0" cellspacing="0" border="0">
 <tr>
  <td align="right" nowrap class="arbo">Texte pour OUI :&nbsp; </td>
  <td ><input type="text" name="oui" id="oui" class="arbo" size="60"></td>
 </tr>
  <tr>
  <td align="right" class="arbo">Obligatoire (oui / non):&nbsp;</td>
  <td><input type="checkbox" name="must" value="1" class="arbo"></td>
 </tr>
 <input type="hidden" id="theme" name="theme" value="">
 <!--
 <tr>
 <td align="right" nowrap class="arbo">Type newsletter&nbsp;:&nbsp;<br/></td> 
 <td>

 <?php
 
/* $sRequete="select * from news_theme where theme_statut = ".DEF_ID_STATUT_LIGNE." order by theme_libelle";
 $aTheme= dbGetObjectsFromRequete("news_theme", $sRequete);
 echo "<select class=\"arbo\" id=\"theme\" name=\"theme\">";
 echo "<option value =\"\">Sélectionnez la newsletter</option>";
 for ($i=0; $i<sizeof($aTheme); $i++) {
 	$oTheme = $aTheme[$i];
	echo "<option value=\"".$oTheme->get_id()."\">".$oTheme->get_libelle()."</option>";
 }
 echo "</select>";*/
 ?>
</td>
 </tr>-->
  <tr>
  <td align="right" nowrap class="arbo">Texte retour&nbsp;:&nbsp;<br/></td>
  <td><textarea name="items" cols="40" rows="10" class="arbo"></textarea></td>
 </tr>
<tr>
  <td align="center" colspan="2"><input type="button" name="bouton" value="Ajouter le champ au formulaire" onClick="javascript:makeit();" class="arbo"></td>
 </tr>
</table>
</form>
<script type="text/javascript"><!--
document.building.oui.value = window.opener.document.getElementById('valeur_champ_<?php echo $_GET["div"]; ?>').value;
texte = window.opener.document.getElementById('texte_champ_<?php echo $_GET["div"]; ?>').value;
texte = texte.replace('<tr><td align="right" class="arbo">',''); 
texte = texte.replace('</td></tr>',''); 
texte = texte.replace(/<br>/g,''); 
document.building.items.value = texte;
if (window.opener.document.getElementById('obligatoire_champ_<?php echo $_GET["div"]; ?>').value == 1) document.building.must.checked = true;
document.building.theme.value = window.opener.document.getElementById('type_champ_plus_<?php echo $_GET["div"]; ?>').value;

</script>