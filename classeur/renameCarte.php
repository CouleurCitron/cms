<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 

***************************************************
*** Gestion de l'arbo des cartes                ***
*** Classeur de cartes                          ***
*** Renommage d'une carte                       ***
***************************************************

*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbocarte.lib.php');

activateMenu('gestioncarte');  //permet de dérouler le menu contextuellement

$id = $_GET['id'];
$node_id = 0;
$node_name = 'non défini';
$carte = null;
if (strlen($id) > 0 ) {
	$carte = getCarteById2($id);
}
?>
<script type="text/javascript">
  function chooseFolder() {
	window.open('choosePageFolder.php','browser','scrollbars=yes,width=500,height=500,resizeable=yes,menubar=no');
  }
</script>
<link href="<?php echo $URL_ROOT; ?>/css/bo.css" rel="stylesheet" type="text/css">

<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>
<?php
if(!isset($_POST['newname']) ) {
?>
<span class="arbo2"><strong>Renommage de la page <?php echo $carte['name']; ?></strong></span>
<form name="saveCuBrick" action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $_GET['id']; ?>" method="post">
<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
 <tr>
  <td align="right">Ancien nom&nbsp;:&nbsp;</td>
  <td align="left"><input name="oldname" type="text" disabled class="arbo" value="<?php echo str_replace('"',"''",$carte['nom']); ?>" size="60" maxlength="60"></td>
 </tr>
 <tr>
  <td align="right">Nouveau nom&nbsp;:&nbsp;</td>
  <td align="left"><input name="newname" type="text" class="arbo" value="<?php echo str_replace('"',"''",$carte['nom']); ?>" size="60" maxlength="60"></td>
 </tr>
 <tr bgcolor="D2D2D2">
  <td colspan="2" align="center"><br>    <input name="Déplacer" type="submit" class="arbo" value="Renommer"></td>
 </tr>
</table>
</form>
<br>
<span class="arbo"><br>
<br>
<?php
} else {

	// ok, on enregistre
	$test = null;
	$id = $_GET['id'];
	$carte = getCarteById2($id);
	$oldname = $carte['nom'];
	$test = renameCarte($id,$_POST['newname']);
	if($test) {
	$carte = getCarteById2($id);
?>
<br>
<span class="arbo"><br>
<br>
Ancien nom : <?php echo $oldname; ?><br>
<br>
Nouveau nom : <?php echo $carte['nom']; ?><br>
<br>
<strong>L'association a été renommée.</strong><br>
<br>
<script>bOpen=false; OpenMenu('nav'); </script>
<?php
	} else {
?>
Une erreur s'est produite lors de la sauvegarde de l'association. Veuillez réessayer. Si le problème persiste, veuillez contacter l'administrateur
<?php
	}
}
?>
</span>
