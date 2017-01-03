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

		txtLine = '<tr><td class="class="awsformcols" colspan=2>';
		txtLine += document.forms['building'].content.value;
		txtLine += '</td></tr>';

		window.opener.document.getElementById('valeur_champ_<?php echo $_GET["div"]; ?>').value = document.forms['building'].content.value;

		window.opener.document.getElementById('div_<?php echo $_GET["div"]; ?>').innerHTML = txtLine;
		window.opener.document.getElementById('sourceHTML_<?php echo $_GET["div"]; ?>').value = txtLine;

		self.close();
	}

--></script>
<body topmargin="0" leftmargin="0" marginwidth="0" marginheight="0">
<form name="building">
<input type="button" name="dummy" value="Insérer la mise en forme" onClick="javascript:if(SPAW_Clean_HTML_src('content',this)) makeit();">
<?php
if (!preg_match('/\/$/msi', $HTTP_SERVER_VARS['DOCUMENT_ROOT']))
 $_root = $HTTP_SERVER_VARS['DOCUMENT_ROOT'].'/';
else
  $_root = $HTTP_SERVER_VARS['DOCUMENT_ROOT'];

define('DR', $_root);
unset($_root);
$spaw_root = 'lib/spaw.1.1rc1/';
include $spaw_root.'spaw_control.class.php';
$sw = new SPAW_Wysiwyg('content','','fr','sidetable', 'default','300px','250px');
$sw->show();
?>
</form>
</body>
