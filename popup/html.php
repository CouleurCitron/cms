<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: html.php,v 1.4 2013-03-01 10:28:18 pierre Exp $
	$Author: pierre $
	
	$Log: html.php,v $
	Revision 1.4  2013-03-01 10:28:18  pierre
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
		txtLine = "";
		txtLine+= '<tr><td colspan="2">';
		txtLine += document.forms['building'].content.value;
		txtLine += '</td></tr>';
		txtLine += "";
		window.opener.addLine(txtLine);
		self.close();
	}

	function addit() {
		nbLines = window.dialogArguments;
		nbLines++;
		txtLine = '<tr><td colspan="2">';
		txtLine += document.forms['building'].content.value;
		txtLine += '</td></tr>';
		window.opener.addLine(txtLine);
		self.close();
	}
--></script>
<body topmargin="0" leftmargin="0" marginwidth="0" marginheight="0">
<form name="building">
<?php
if ($_GET['brique'] != "formulaire") {
?>
<input type="button" name="dummy" value="Insérer la mise en forme" onClick="javascript:if(SPAW_Clean_HTML_src('content',this)) addit();">
<?php
} else {
?>
<input type="button" name="dummy" value="Insérer la mise en forme" onClick="javascript:if(SPAW_Clean_HTML_src('content',this)) makeit();">
<?php
}
?>
<?php
if (!ereg('/$', $HTTP_SERVER_VARS['DOCUMENT_ROOT']))
 $_root = $HTTP_SERVER_VARS['DOCUMENT_ROOT'].'/';
else
  $_root = $HTTP_SERVER_VARS['DOCUMENT_ROOT'];

define('DR', $_root);
unset($_root);
$spaw_root = 'spaw/';
include $spaw_root.'spaw_control.class.php';
$sw = new SPAW_Wysiwyg('content','','fr','sidetable', 'default','485px','464px');
$sw->show();
?>
</form>
</body>
