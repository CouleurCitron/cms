<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: textarea.php,v 1.2 2013-10-07 15:09:51 quentin Exp $
	$Author: quentin $
	
	$Log: textarea.php,v $
	Revision 1.2  2013-10-07 15:09:51  quentin
	*** empty log message ***

	Revision 1.1  2013-09-30 09:42:26  raphael
	*** empty log message ***

	Revision 1.7  2013-03-01 10:28:18  pierre
	*** empty log message ***

	Revision 1.6  2012-04-13 07:18:38  pierre
	*** empty log message ***

	Revision 1.5  2009-09-24 08:53:13  pierre
	*** empty log message ***

	Revision 1.4  2009-06-10 08:48:20  thao
	*** empty log message ***

	Revision 1.3  2009-06-08 13:40:21  thao
	*** empty log message ***

	Revision 1.2  2008-03-04 14:54:31  pierre
	*** empty log message ***

	Revision 1.1  2006/04/12 07:25:20  sylvie
	*** empty log message ***
	
	Revision 1.1  2004/05/11 08:18:02  ddinside
	suite maj boulogne
	
*/

// sponthus 05/06/2005
// popup utilisée pour l'ajout de champ texte 
// dans une brique formulaire mail et dans une brique formulaire normal

?>
<script language="javascript"><!--
	function makeit() {
		nbLines = window.dialogArguments;
		nbLines++;
		txtLine = '<tr><td align="right" class="awsformlabel"> <?php echo $_GET["nomchamp"]; ?></td>';
		txtLine+= '<td>';
		txtLine+= '<textarea name="<?php echo $_GET["nomchamp"]; ?>" class="textareaEdit">' + document.forms['building'].val.value;
		txtLine+= '</textarea>';
		txtLine+="</td></tr>";
		window.returnValue = txtLine;
		window.close();
	}
	
	function addit() {
		nbLines = window.dialogArguments;
		nbLines++;
		txtLine = '<tr id=tr' + nbLines + '><td align="right">' + document.forms['building'].nom.value + '&nbsp;:&nbsp;</td><td><textarea id="' + document.forms['building'].nom.value + '" name="' + document.forms['building'].nom.value + '" class="textareaEdit">' + document.forms['building'].val.value  + '</textarea></td></tr>'+"\n";
		window.returnValue = txtLine;
		window.close();
	}
--></script>
<form name="building">
<b><u>Ajout d'un champ TextArea :</u></b><br/><br/>
<table cellpadding="0" cellspacing="0" border="0">
<?php
if ($_GET['brique'] != "formulaire") {
?>
 <tr>
  <td align="right">nom :&nbsp;</td>
  <td><input type="text" name="nom" size="20" maxlength="50"></td>
 </tr>
<?php
}
?> 
 <tr>
  <td align="right">valeur par défaut :&nbsp;</td>
  <td><input type="text" name="val" size="20" maxlength="50"></td>
 </tr>
 <tr>
  <td align="right">largeur (en caractères):&nbsp;</td>
  <td><input type="text" name="cols" size="2" value="25" maxlength="2"></td>
 </tr>
 <tr>
  <td align="right">hauteur (en lignes):&nbsp;</td>
  <td><input type="text" name="rows" size="2" value="5" maxlength="2"></td>
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
