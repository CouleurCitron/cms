<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 
*****************************************************
*** Gestion de l'arbo des cartes                  ***
*** Classeur de cartes                            ***
*** Récupéré du fichier arbo_deletefolderpage.php ***
*****************************************************
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbocarte.lib.php');
activateMenu('gestioncarte');  //permet de dérouler le menu contextuellement
$id=0;
$path = false;
$virtualPath = 0;
$nodeInfos=array();

//idsite
$idSite = $_GET['idSite'];
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];
// pas de site
if ($idSite == "") die ("Appel incorrect de la fonctionnalité");

if(!strlen($_POST['v_comp_path'])) {

	if (strlen($_GET['v_comp_path']) > 0)
		$virtualPath = $_GET['v_comp_path'];
	$nodeInfos=getNodeInfosCarte($idSite, $db,$virtualPath);
	
} else{

	if (strlen($_POST['v_comp_path']) > 0)
		$virtualPath = $_POST['v_comp_path'];
	// Si le noeud contient au moins une carte, on ne peut pas le supprimer
	// Il faut supprimer les cartes d'abord
	
	$pb_carte_existantes=getFolderComposantsCarte($virtualPath);
	
	if(!is_array($pb_carte_existantes)) {
	
		$path=deleteNodeCarte($idSite, $db,$virtualPath);
	
		if($path!=false) {
	
			if($path=='Racine') $path=0;
	
			$virtualPath=$path;
	
			$path = true;
	
		}
	}
	else {
		$path=false; // pour l'affichage du message d'erreur
	}
	
	$nodeInfos=getNodeInfosCarte($idSite, $db,$virtualPath);
}
?>
<style type="text/css">
.toto {
	color: #ffffff;
	text-decoration: underline;
	background-color: #316AC5;
};
</style>
<link rel="stylesheet" type="text/css" href="<?php echo $URL_ROOT; ?>/css/bo.css">
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style><form name="managetree" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<div class="ariane">
<span class="arbo2">ARBORESCENCE&nbsp;>&nbsp;</span>
<span class="arbo3">Parcourir l'arborescence&nbsp;>&nbsp;</span>
<span class="arbo4"><?php echo getAbsolutePathStringCarte($db, $virtualPath,'carteArbo.php'); ?></span>
</div>
  <table cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td colspan="3"><img src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/vide.gif"></td>
    </tr>
    <tr>
      <td width="20px" align="left" valign="top" class="arbo2"><br>
          <b><u>Arborescence :</u></b> <br>
          <table cellpadding="8" cellspacing="0" border="1" bordercolor="#FFFFFF">
            <tr>
              <td bgcolor="D2D2D2"><?php
print drawCompTreeCarte($idSite, $db,$virtualPath);
?></td>
            </tr>
          </table>
          <br>
          <br>
      </td>
      <td width="5"  align="left" valign="top" class="arbo2"><img src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/vide.gif" width="15"></td>
      <td  align="left" valign="top" class="arbo2">
        <!-- contenu des actions -->
<?php

//	if($virtualPath == "0" ||$virtualPath == "13" ||$virtualPath == "14") { // Si on est à la racine, pas d'opération possible
	

	if(!strlen($_POST['v_comp_path']) && $virtualPath != "0" && $virtualPath != "13" && $virtualPath != "14") {
?>
        <u><b><br>
      Suppression d'un dossier :</b></u> <br>
      <br>
      <table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF">
        <tr bgcolor="E6E6E6">
          <td class="arbo" style="vertical-align:middle">Dossier &agrave; supprimer &nbsp;:</td>
          <td><input name="dummy" type="text" disabled class="arbo" id="dummy" value="<?php echo $nodeInfos['libelle']; ?>" size="14">
              <input type="hidden" name="v_comp_path" value="<?php echo $virtualPath; ?>"></td>
        </tr>
        <tr align="center" bgcolor="EEEEEE">
          <td colspan="2" bgcolor="D2D2D2" class="arbo"><a href="carteArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><strong>Annuler</strong></a>&nbsp;&nbsp;<a href="carteArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/go.gif"></a> 
              - <a href="#" class="arbo" onClick="javascript:document.forms[0].submit();"><strong>Supprimer le dossier</strong></a>&nbsp;&nbsp;<a href="#" onClick="javascript:document.forms[0].submit();" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/go.gif"></a></td>
        </tr>
      </table>
      <br>
      <?php
	  //}
	} else {
		// suppression du sous dossier
		if($path!=false) {
?>
      <br>
      <br>
      <br>
      <b><span class="arbo">Le dossier <?php echo $_POST['foldername']; ?> a bien &eacute;t&eacute; supprim&eacute;</span></b>
      <?php
		} else {
			if(is_array($pb_carte_existantes)) {
?>
      <br>
      <br>
      <br>
      <b><span class="arbo">Vous ne pouvez pas supprimer un dossier contenant des cartes. <br>
      Vous devez supprimer les cartes avant de supprimer le dossier.</span></b>
<?php
			
			}
			
			elseif($virtualPath == "0") { // Si on est à la racine, pas d'opération possible
?>
<u><b><br>
      Suppression d'un dossier :</b></u> <br>
	      <br>
      <br>
      <br>
      <b><span class="arbo">Il n'est pas possible d'effectuer cette action sur le dossier Racine.</span></b>
<?php
		}
		else {
?>
      <br>
      <br>
      <br>
      <b><span class="arbo">Un probl&egrave;me est survenu durant l'op&eacute;ration. <br>
      Veuillez r&eacute;essayer ou contacter l'administrateur si l'erreur persiste.</span></b>
      <?php
			}
		}
	}
?></td>
    </tr>
  </table>
  </form>