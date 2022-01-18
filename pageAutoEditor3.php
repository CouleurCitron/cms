<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	
*****************************************************
*** Ajout automatique et récursif d'une brique    ***
***                                               ***
*** Récupéré du fichier arboPage_browse.php       ***
*****************************************************
*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');

activateMenu('gestioncontenu');  //permet de dérouler le menu contextuellement

$virtualPath = 0;
if ($_GET['v_comp_path']=="") { // Si on a pas de dossier par défaut on essaye de charger celui en mémoire
	if($_SESSION['page_v_comp_path']!="") { // On a justement un dossier favoris en mémoire
		$_GET['v_comp_path'] = $_SESSION['page_v_comp_path'];
	}
}
if (strlen($_GET['v_comp_path']) > 0)
	$virtualPath = $_GET['v_comp_path'];
	$_SESSION['page_v_comp_path'] = $virtualPath;// On enregistre dans le dossier favoris le dossier courant
?>
<script type="text/javascript">document.title="Arbo - Pages";</script>
<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<script type="text/javascript">
<!--
	function preview(id,width,height){
		window.open("previewComposant.php?id="+id,"preview","scrollbar=no,menubar=no,width=" + width +",height=" + height);
	}
-->
</script>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style><form name="managetree" action="pageAutoEditor4.php" method="post">
<?php
if($_POST['serializedData']=="") { // On a pas d'infos en paramètre sur la brique a intégrer
	// On cherche en mémoire
	if($_SESSION['serializedData']!="")  $serializedData = $_SESSION['serializedData'];
	else { // Là y a un problème!!
		echo '<p class="arbo"><strong>Erreur interne, il manque des informations sur la brique, merci de recommencer !</strong></p>';
		return;
	}
}
else $serializedData = $_POST['serializedData'];
$_SESSION['serializedData'] = $serializedData;

?>
<input type="hidden" name="serializedData" value="<?php echo  $serializedData ; ?>">
<input type="hidden" name="v_comp_path" value="<?php echo  $_GET['v_comp_path'] ; ?>">
<p class="arbo2"><strong>Choix du dossier contenant les pages devant intégrer la brique</strong></p>
  <table cellpadding="0" cellspacing="0" border="0">
 <tr>
   <td align="left" valign="top" class="arbo2">
       <table cellpadding="8" cellspacing="0" border="1" bordercolor="#FFFFFF">
         <tr>
           <td bgcolor="D2D2D2"><?php
print drawCompTree($db,$virtualPath,null);
?></td>
         </tr>
     </table></td>
  <td colspan="3" class="arbo2"><span class="arbo2"><img src="/backoffice/cms/img/vide.gif" width="15"></span></td>
  <td align="left" valign="top" class="arbo2">
  <!-- contenu du dossier -->
  <table  border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
  <tr>
	<td colspan="3" align="center" bgcolor="D2D2D2" style="padding:9px 0px 10px 0px"><strong>Page(s) contenue(s) dans le dossier en cours</strong><br /></td>
  </tr>
  <tr>
	<td align="center" bgcolor="E6E6E6"><strong>&nbsp;&nbsp;Nom&nbsp;</strong></td>
	<td width="5" align="center" bgcolor="EEEEEE"><strong>&nbsp;&nbsp;Gabarit&nbsp;</strong></td>
	<td align="center" bgcolor="E6E6E6"><strong>&nbsp;Dossier&nbsp;contenant&nbsp;</strong></td>
  </tr>
  <?php

	function drawFileInPath($VPath,$CPath) {
	// Affiche toutes les pages du dossier passé en argument
	// $VPath = Virtual path (0,15,85)
	// $CPath = Real path on disk (/dir1/dir2/dir3)
		$contenus = getFolderPages($VPath);
		$retour="";
		if((is_array($contenus)) && (newSizeOf($contenus)!=0)) {

			foreach ($contenus as $k => $page) {
		$retour.='  <tr>
   <td align="center" bgcolor="F3F3F3">&nbsp;<a href="/content'. $CPath . $page['name'] .'" target="_blank">'. $page['name'] .'</a>&nbsp;</td>
   <td align="center" bgcolor="F7F7F7">&nbsp;'. $page['gabarit'] .'&nbsp;</td>
   <td bgcolor="F3F3F3">&nbsp;'. str_replace(" ","&nbsp;",rawurldecode($CPath)) .'&nbsp;</td>
  </tr>';
			}
		}
		return $retour;
	}

	function DrawRecursDir($VPath) {
		global $db;
		$current_path = getNodeInfos($db,$VPath);
		$current_path = rawurlencode($current_path['path']);
		$current_path = preg_replace('/\%2F/','/',$current_path);
		$childs = getNodeChildren($db,$VPath);
		$htmlchilds = '';
		if (count($childs)!=0) {
			// Il existe encore des sous répertoires
			// => on relance la fonction pour chaque enfant
			foreach($childs as $key => $child) {
				$htmlchilds .= DrawRecursDir($VPath.",".$child['id']);
			}
		}
		return drawFileInPath($VPath,$current_path) . $htmlchilds;
	}


	// Faire la liste récursive des fichiers!!!
	$html = DrawRecursDir($virtualPath);
	if($html!="")
		echo $html;
	else
		echo '<tr>
    <td align="center" colspan="3">&nbsp;<strong>Aucun élément à afficher</strong>  </tr>';
	
  ?>
  </table>
  </td>
 </tr>
</table>
<?php
if($html!="") { echo '<input name="button" type="submit" class="arbo" value="Effectuer l\'insertion >>">'; }
?>
</form>





