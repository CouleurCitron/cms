<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
$Action = $_GET['Action'];
$fic = $_GET['fic'];
$newnamefile = $_GET['newnamefile'];

$fileopt=explode('.',$fic);
		
		
 if ($Action=="Valider") {
	if ($newnamefile!="")
		@rename ( "../../medias/".$fic , "../../medias/".$newnamefile.".".$fileopt[1] ) ;
		?>
		<script>opener.location.href="multimediaGestion.php";window.close();</script>
		<?php
}

		
		
		?> 
<html>
<title>Renommer Média</title>
<link href="/backoffice/cms/css/bo.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style><body bgcolor="#E6E6E6">
		<form action="" method="get" name="dialogue">
		<table width="400" border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
  <tr valign="middle">
    <td width="75%">Nouveau nom (sans extension):</td>
    <td>&nbsp;<input name="newnamefile" type="text" class="arbo" value="<?php echo $fileopt[0] ?>" size="10">
    &nbsp;<?php echo ".".$fileopt[1] ?><input name="fic" type="hidden" value="<?php echo $fic ?>"></td>
  </tr>
  <tr>
    <td align="center"><input name="Action" type="submit" class="arbo" value="<?php $translator->echoTransByCode('btValider'); ?>" /></td>
    <td align="center"><input name="Action" type="button" class="arbo" onClick="window.close()" value="<?php $translator->echoTransByCode('btAnnuler'); ?>" /></td>
  </tr>
</table>
</form>
	</body>
	</html>