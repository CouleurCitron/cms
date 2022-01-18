<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	
*****************************************************
*** Ajout automatique et récursif d'une brique    ***
***                                               ***
*** Récupéré du fichier pageEditor2.php           ***
*****************************************************

*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');

activateMenu('gestionpage');  //permet de dérouler le menu contextuellement

if( preg_match('/pageAutoEditor.php/',$_SERVER['HTTP_REFERER'])  ) {
	unset($_SESSION['divArray']);
	unset($_SESSION['serializedData']);
	unset($_SESSION['page']);
}

$virtualPath = 0;

if ($_GET['v_comp_path']=="") { // Si on a pas de dossier par défaut on essaye de charger celui en mémoire
	if($_SESSION['brique_v_comp_path']!="") { // On a justement un dossier favoris en mémoire
		$_GET['v_comp_path'] = $_SESSION['brique_v_comp_path'];
	}
}

if (strlen($_GET['v_comp_path']) > 0)
	$virtualPath = $_GET['v_comp_path'];
	
$_SESSION['brique_v_comp_path'] = $virtualPath;// On enregistre dans le dossier favoris le dossier courant


if(!isset($_POST['displayedComposantList'])) { // Initialisation de la page si premiere visite
	$contenus = getFolderComposants($virtualPath);
	if(!is_array($contenus))
		$contenus = array();
	$displayedComposantList='';
	foreach ($contenus as $k => $composant) {
		if(strlen($displayedComposantList)==0) {
			$displayedComposantList = $composant['id'];
		} else {
			$displayedComposantList .= ','.$composant['id'];
		}
		$_POST['displayedComposantList']=$displayedComposantList;
	}
	if($_SESSION['selectedComposant']!="")
		$_POST['selectedComposant'] = $_SESSION['selectedComposant'];
}

$virtualPath = 0;
if (strlen($_GET['v_comp_path']) > 0)
	$virtualPath = $_GET['v_comp_path'];


if( $_POST['selectedComposant']=="0" ) { // On a supprimé la brique de la sélection
	$_SESSION['selectedComposant'] = "";
	$_POST['selectedComposant'] = "";
}

if( $_POST['selectedComposant']!="" ) {
	$displayedComposantList = explode(',' ,$_POST['displayedComposantList']);
	$selectedComposant = $_POST['selectedComposant'];
	$_SESSION['selectedComposant'] = $_POST['selectedComposant'];
}
?>
<script type="text/javascript">document.title="Pages BB";</script>
<script type="text/javascript">
<!--
	function preview(id,width,height){
		window.open("previewComposant.php?id="+id,"preview","scrollbar=no,menubar=no,width=" + width +",height=" + height);
	}
-->
</script>
<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style><form name="managecomposant" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">

  <table cellpadding="0" cellspacing="0" border="0">
 <tr>
  <td colspan="5"><table  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="120"><img src="../../backoffice/cms/img/tt_gestion_page.gif" width="117" height="18"></td>
      <td valign="middle" class="arbo" style="vertical-align:middle"> &gt; <small><?php echo getAbsolutePathString($db, $virtualPath, $_SERVER['REQUEST_URI']); ?></small></td>
    </tr>
  </table></td>
 </tr>
 <tr>
  <td colspan="5"><img src="/backoffice/cms/img/vide.gif"></td>
 </tr>
 <tr>
   <td width="6" align="left" valign="top" class="arbo2"><br />
       <b><u>Arborescence :</u></b> <br />
       <table cellpadding="8" cellspacing="0" border="1" bordercolor="#FFFFFF">
         <tr>
           <td bgcolor="D2D2D2"><?php
print drawCompTree($db,$virtualPath,null,null);
?></td>
         </tr>
       </table>
       <br />
       <br />
   </td>
  <td colspan="3" class="arbo2"><img src="/backoffice/cms/img/vide.gif" width="15"></td>
  <td align="left" valign="top" class="arbo2">
  <!-- contenu du dossier --><br />  <b class="arbo2"><u>
  Composants contenus dans le dossier en cours :</u></b>
   <br />
   <br />  <table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
     <tr bgcolor="E6E6E6">
       <td align="center" bgcolor="E6E6E6">&nbsp;&nbsp;<u>Action</u>&nbsp;&nbsp;</td>
       <td width="5" align="center">&nbsp;&nbsp;<u>Nom</u>&nbsp;&nbsp;</td>
       <td width="1" align="center">&nbsp;&nbsp;<u>Dimensions</u>&nbsp;&nbsp;</td>
       <td width="5" align="center">&nbsp;&nbsp;<u>Type</u>&nbsp;&nbsp;</td>
       <td align="center">&nbsp;&nbsp;<u>Vérification</u>&nbsp;&nbsp;</td>
     </tr>
     <?php
	$contenus = getFolderComposants($virtualPath);
	$displayedComposantList = '';
	$selectedComposant = "";
	if($_SESSION['selectedComposant']!='')
		$selectedComposant = $_SESSION['selectedComposant'];
	if(!is_array($contenus)) {
?>
     <tr>
       <td align="center" colspan="3">&nbsp;<strong><small>Aucun élément à afficher</small></strong>&nbsp;   
     </tr>
     <?php
	} else {
		$i=0;
		foreach ($contenus as $k => $composant) {
		if(strlen($displayedComposantList)==0) {
			$displayedComposantList = $composant['id'];
		} else {
			$displayedComposantList .= ','.$composant['id'];
		}
		$i++;
?>
     <tr bgcolor="EEEEEE">
       <td align="center">
           <?php if($composant['id']!=$selectedComposant) { ?>&nbsp;&nbsp;<input type="button" value="Sélectionner" onclick="document.managecomposant.selectedComposant.value='<?php echo $composant['id'];?>'; document.managecomposant.submit()">&nbsp;&nbsp;<?php } else { echo "déjà<br />sélectionné"; } ?>
		</td>
       <td align="center" bgcolor="EEEEEE">&nbsp;<?php echo $composant['name'];?>&nbsp;</td>
       <td align="center">&nbsp;<?php echo $composant['width'];?>x<?php echo $composant['height'];?>&nbsp;</td>
       <td align="center">&nbsp;<?php echo $composant['type'];?>&nbsp;</td>
       <td align="center">&nbsp;<a href="#" class="arbo" onClick="javascript:preview(<?php echo $composant['id'];?>,<?php echo $composant['width'];?>,<?php echo $composant['height'];?>)">Preview</a>&nbsp;</td>
     </tr>
     <?php
		}
	}
  ?>
     <tr bgcolor="D2D2D2">
       <td colspan="5" align="center"></td>
     </tr>
   </table></td>
 </tr>
</table>
<input type="hidden" name="displayedComposantList" value="<?php echo $displayedComposantList; ?>">
<input type="hidden" name="selectedComposant" value="<?php echo $selectedComposant; ?>">
<br />
<hr align="left" width="80%" class="arbo2" height="1">
<span class="arbo"><small><b>Composant selectionné :</b></small><br /><br /></span>
<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF">
  <tr bgcolor="E6E6E6">
	<td align="center" class="arbo">&nbsp;&nbsp;<u>Nom</u>&nbsp;&nbsp;</td>
	<td align="center" class="arbo">&nbsp;&nbsp;<u>Dimensions</u>&nbsp;&nbsp;</td>
	<td align="center" class="arbo">&nbsp;&nbsp;<u>Type</u>&nbsp;&nbsp;</td>
	<td align="center" class="arbo">&nbsp;&nbsp;<u>Prévisualiser</u>&nbsp;&nbsp;</td>
	<td align="center" class="arbo">&nbsp;&nbsp;<u>Action</u>&nbsp;&nbsp;</td>
  </tr>
<?php
	$c=0;
	if ($selectedComposant != "") {
		$c++;
		$composant = getComposantById($selectedComposant);
?>
  <tr bgcolor="EEEEEE">
   <td align="center" class="arbo">&nbsp;<?php echo $composant['name'];?>&nbsp;</td>
   <td align="center" class="arbo">&nbsp;<?php echo $composant['width'];?>x<?php echo $composant['height'];?>&nbsp;</td>
   <td align="center" class="arbo">&nbsp;<?php echo $composant['type'];?>&nbsp;</td>
   <td align="center" class="arbo">&nbsp;<a href="#<?php echo $composant['id'];?>" class="arbo" onClick="javascript:preview(<?php echo $composant['id'];?>,<?php echo $composant['width'];?>,<?php echo $composant['height'];        ?>)">Preview</a>&nbsp;</td>
   <td align="center" class="arbo">&nbsp;<input type="button" value="Enlever de la sélection"  onclick="document.managecomposant.selectedComposant.value='0'; document.managecomposant.submit()">&nbsp;</td>
  </tr>
<?php
	}
	if($c==0) {
?>
<script type="text/javascript"><!-- 
var c = 0; 
--></script>
<tr><td class="arbo" colspan="5" align="center"><strong>Aucun élément sélectionné</strong></td></tr></table>
<span class="arbo">
<?php
	} else {
?>
<script type="text/javascript"><!-- 
var c = 1; 
--></script>
  </span>
</table>
  <span class="arbo">
<?php
	}
?>
  </span><hr align="left" width="80%" class="arbo2" height="1">
<div align="center"><input name="dummy" type="button" class="arbo" onClick="javascript:if(c!=0) {window.location='pageAutoEditor2.php?id=<?php echo $_GET['id']; ?>';} else { window.alert('Vous n\'avez pas selectionné de composant.');}" value="Suite (Paramètre du composant) >>">
</div>
</form>





