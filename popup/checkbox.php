<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: checkbox.php,v 1.5 2013-03-01 10:28:18 pierre Exp $
	$Author: pierre $
	
	$Log: checkbox.php,v $
	Revision 1.5  2013-03-01 10:28:18  pierre
	*** empty log message ***

	Revision 1.4  2009-09-24 08:53:13  pierre
	*** empty log message ***

	Revision 1.3  2008-11-28 14:09:55  pierre
	*** empty log message ***

	Revision 1.2  2008-11-06 12:03:53  pierre
	*** empty log message ***

	Revision 1.1  2007-08-08 14:26:26  thao
	*** empty log message ***

	Revision 1.1.1.1  2006/01/25 15:14:32  pierre
	projet CCitron AWS 2006 Nouveau Website
	
	Revision 1.2  2005/11/02 08:32:18  sylvie
	*** empty log message ***
	
	Revision 1.1.1.1  2005/10/20 13:10:53  pierre
	Espace V2
	
	Revision 1.1  2004/05/11 08:18:02  ddinside
	suite maj boulogne
	
*/

// sponthus 05/06/2005
// popup utilisée pour l'ajout de champ texte 
// dans une brique formulaire mail et dans une brique formulaire normal

?>
<script type="text/javascript"><!--
	function makeit() {
		nbLines = window.dialogArguments;
		nbLines++;
		pattern = "";
		txtLine = "";
		
		items = document.forms['building'].libelle.value.split("\n");

		for (var i=0;i<items.length;i++) {
			value = items[i].replace(/\r/,'');
			value = value.replace(/\n/,'');
			txtLine+= '<tr><td align="right"> <?php echo $_GET["nomchamp"]; ?></td>';
			txtLine+= '<td>';
			txtLine+='<input type="checkbox" name="<?php echo $_GET["nomchamp"]; ?>" value="Oui">' + value;
			txtLine+="</td></tr>";
		}

		window.returnValue = txtLine;
		window.close();
	}

	function addit() {
		nbLines = window.dialogArguments;
		nbLines++;
		pattern = "";
		txtLine = '<tr id=tr' + nbLines + '><td colspan="2">';
		//items = document.forms['building'].items.value.split("\n");
		//for (var i=0;i<items.length;i++) {
// création d'un champ hidden pour chaque case à cocher
// sponthus 02/06/2005
// car une case à cocher non cochée ne poste rien
			txtLine+='<input type="hidden" name="hcb_' + document.forms['building'].nom.value + '" value="Non">' + "\n";
			txtLine+='<input type="checkbox" name="cb_' + document.forms['building'].nom.value + '" value="Oui">' + document.forms['building'].libelle.value + "<br/>\n";
		//}
		txtLine += '</td></tr>';
		window.returnValue = txtLine;
		window.close();
	}
--></script>
<form name="building">
<b><u>Ajout d'une case à cocher (checkbox) :</u></b><br/><br/>
<table cellpadding="0" cellspacing="0" border="0">
<?php
if ($_GET['brique'] != "formulaire") {
?>
 <tr>
  <td align="right" nowrap>nom :&nbsp;</td>
  <td><input type="text" name="nom" size="20" maxlength="50"></td>
 </tr>
<?php
}
?>
 <tr>
  <td align="right" nowrap>Libellé&nbsp;:&nbsp;</td>
  <td align="right"><textarea name="libelle" cols="40" rows="10"></textarea></td>
 </tr>
<?php
if ($_GET['brique'] != "formulaire") {
?>
 <tr>
  <td align="center" colspan="2"><input type="button" name="bouton" value="Ajouter le champ au formulaire" onClick="javascript:addit();"></td>
 </tr>
<?php
} else {
?>
 <tr>
  <td align="center" colspan="2"><input type="button" name="bouton" value="Ajouter le champ au formulaire" onClick="javascript:makeit();"></td>
 </tr>
<?php
}
?>
</table>
</form>
