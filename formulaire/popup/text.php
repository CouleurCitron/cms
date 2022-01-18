<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: text.php,v 1.1 2013-09-30 09:48:02 raphael Exp $
	$Author: raphael $
	
	$Log: text.php,v $
	Revision 1.1  2013-09-30 09:48:02  raphael
	*** empty log message ***

	Revision 1.7  2013-03-01 10:28:13  pierre
	*** empty log message ***

	Revision 1.6  2009-09-24 08:53:12  pierre
	*** empty log message ***

	Revision 1.5  2009-08-11 14:33:48  thao
	*** empty log message ***

	Revision 1.4  2008-11-28 14:09:55  pierre
	*** empty log message ***

	Revision 1.3  2008-11-06 12:03:53  pierre
	*** empty log message ***

	Revision 1.2  2008-08-28 08:38:12  thao
	*** empty log message ***

	Revision 1.1  2008/02/28 09:24:48  thao
	*** empty log message ***
	
	Revision 1.1  2007/08/08 14:26:26  thao
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
		pattern = "";
		if (document.forms['building'].must.checked==true) {
			pattern="^.+$";
		}
		txtLine = '<tr><td align="right"> <?php echo $_GET["nomchamp"]; ?></td>';
		txtLine+= '<td>';
		txtLine+= '<input type="text" name="<?php echo $_GET["nomchamp"]; ?>" ';
		txtLine+= 'value="' + document.forms['building'].val.value + '" ';
		txtLine+= 'size="' + document.forms['building'].size.value + '" ';
		txtLine+= 'maxlength="' + document.forms['building'].length.value + '"';
		txtLine+= 'errorMsg="Le champ <?php echo html2text($_GET["nomchamp"]); ?> est obligatoire" pattern="'+ pattern +'">';
		txtLine+= '</td></tr>';		
		window.returnValue = txtLine;
		window.close();
	}


	function addit() {
		nbLines = window.dialogArguments;
		nbLines++;
		pattern = "";
		if (document.forms['building'].must.checked==true) {
			pattern="^.+$";
		}
		txtLine = '<tr id=tr' + nbLines + '><td align="right">' + document.forms['building'].nom.value + '&nbsp;:&nbsp;</td><td><input type="text" name="' + document.forms['building'].nom.value + '" value="' + document.forms['building'].val.value + '" size="' + document.forms['building'].size.value + '" maxlength="' + document.forms['building'].length.value + '" errorMsg="Le champ <?php echo html2text($_GET["nomchamp"]); ?> est obligatoire" pattern="'+ pattern +'"></td></tr>'+"\n";
		window.returnValue = txtLine;
		window.close();
	}
--></script>
<form name="building">
<b><u>Ajout d'un champ texte :</u></b><br/><br/>
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
  <td><input type="text" name="size" size="2" value="25" maxlength="2"></td>
 </tr>
 <tr>
  <td align="right">Taille Max. (en caractères):&nbsp;</td>
  <td><input type="text" name="length" size="2" value="25" maxlength="2"></td>
 </tr>
 <tr>
  <td align="right">Obligatoire (oui / non):&nbsp;</td>
  <td><input type="checkbox" name="must" value="1"></td>
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
