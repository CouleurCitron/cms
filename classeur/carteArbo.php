<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 

********************************************
*** Gestion de l'arbo des cartes         ***
*** Classeur de cartes                   ***
*** Récupéré du fichier pageArbo.lib.php ***
********************************************

*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbocarte.lib.php');
activateMenu('gestioncarte');  //permet de dérouler le menu contextuellement

if ($idSite == "") $idSite = $_SESSION['idSite_travail'];
// pas de site
if ($idSite == "") die ("Appel incorrect de la fonctionnalité");

if($_GET['origine']!=""){
$_SESSION['provenance']=$_GET['origine'];
}

$virtualPath = 0;
if ($_GET['v_comp_path']=="") { // Si on a pas de dossier par défaut on essaye de charger celui en mémoire
	if($_SESSION['carte_v_comp_path']!="") { // On a justement un dossier favoris en mémoire
		$_GET['v_comp_path'] = $_SESSION['carte_v_comp_path'];
	}
}
if (strlen($_GET['v_comp_path']) > 0)
	$virtualPath = $_GET['v_comp_path'];
$_SESSION['carte_v_comp_path'] = $virtualPath;// On enregistre dans le dossier favoris le dossier courant

//echo "path=".$_SESSION['carte_v_comp_path'];
//$_SESSION['carte_v_comp_path']=13;
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
</style><form name="managetree" action="arboaddnode.php" method="post">
<div class="ariane"><span class="arbo2">ARBORESCENCE DES CLASSEURS&nbsp;>&nbsp;</span>
<span class="arbo3">Gestion de l'arborescence de classeur&nbsp;>&nbsp;</span>
<span class="arbo4"><?php echo getAbsolutePathStringCarte($db, $virtualPath); ?></span></div>
	<table cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td colspan="3"><img src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/vide.gif"><br>
      <br></td>
    </tr>
    <tr>
      <td width="250" align="left" valign="top" class="arbo"><br>
          <b>&nbsp;&nbsp;Arborescence :</b> <br>
          <br>
         <table cellpadding="8" cellspacing="0" border="0">
            <tr>
              <td bgcolor="#FFFFFF"><?php
print drawCompTreeCarte($idSite, $db,$virtualPath,null);
?></td>
            </tr>
        </table></td>
      <td width="15" align="left" valign="top" class="arbo2"><img src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/vide.gif" width="15"></td>

      <td align="center" valign="top" class="arbo">
        <!-- contenu des actions -->
       <b><br>
      Op&eacute;rations sur le dossier en cours :</b><br>
      <br>
      <table width="300" border="0" cellpadding="5" cellspacing="0" bgcolor="#F0F1F3">
        <ul>
          <tr bgcolor="#FFFFFF">
            <td class="arbo"<?php if($virtualPath=="0") echo "title='Impossible de déplacer le dossier Racine' style='color:#b0b0b0'"; ?>><li>D&eacute;placer le dossier</li></td>
            <td<?php if($virtualPath=="0") echo ' title="Impossible de déplacer le dossier Racine"><img border="0" src="'.$URL_ROOT.'/backoffice/cms/img/go_off.gif">'; else { ?>><a href="arbo_movefoldercarte.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a><?php } ?></td>
          </tr>

          <tr>

            <td bgcolor="EEEEEE" class="arbo"<?php if($virtualPath=="0") echo "title='Impossible de renommer le dossier Racine' style='color:#b0b0b0'"; ?>><li>Renommer le dossier</li></td>
            <td<?php if($virtualPath=="0") echo ' title="Impossible de renommer le dossier Racine"><img border="0" src="'.$URL_ROOT.'/backoffice/cms/img/go_off.gif">'; else { ?>><a href="arbo_renamefoldercarte.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a><?php } ?></td>
           </tr>

          <tr bgcolor="#FFFFFF">
            <td bgcolor="E6E6E6" class="arbo"<?php if($virtualPath=="0") echo "title='Impossible de supprimer le dossier Racine' style='color:#b0b0b0'"; ?>><li>Supprimer le dossier</li></td>
            <td<?php if($virtualPath=="0") echo ' title="Impossible de supprimer le dossier Racine"><img border="0" src="'.$URL_ROOT.'/backoffice/cms/img/go_off.gif">'; else { ?>><a href="arbo_deletefoldercarte.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a><?php } ?></td>
          </tr>
          <tr >
<?php // On désactive la création de sous dossiers si l'arbo a déjà 3 niveaux
$toodeep = (substr_count($virtualPath,",")>3);
?>
            <td class="arbo"<?php if($toodeep) echo "title='Impossible de créer un dossier, vous avez atteint les 3 niveaux maximum autorisés' style='color:#b0b0b0'"; ?>><li>Cr&eacute;er un sous dossier</li></td>
            <td <?php if($toodeep) echo ' title="Impossible de créer un dossier, vous avez atteint les 3 niveaux maximum autorisés"><img border="0" src="'.$URL_ROOT.'/backoffice/cms/img/go_off.gif">'; else { ?>><a href="arbo_createfoldercarte.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a><?php } ?></td>
          </tr>

          <tr bgcolor="#FFFFFF">
            <td class="arbo"><li>Ordre d'affichage des sous dossiers</li></td>
            <td><a href="arbo_orderfoldercarte.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>
          </tr>
		            <tr >
            <td class="arbo"><li>Description du dossier</li></td>
            <td><a href="arbo_descriptioncarte.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>
           </tr>

          <tr bgcolor="#FFFFFF">
            <td class="arbo"><li>Associer un picto</li></td>
            <td><a href="arbo_pictocarte.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>
          </tr>
        </ul>
    </table></td>
    </tr>
  </table>
  </form>