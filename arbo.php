<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/* 

$Author: raphael $

$Revision: 1.1 $



$Log: arbo.php,v $
Revision 1.1  2013-09-30 09:23:50  raphael
*** empty log message ***

Revision 1.8  2013-03-01 10:28:04  pierre
*** empty log message ***

Revision 1.7  2012-07-31 14:24:46  pierre
*** empty log message ***

Revision 1.6  2010-03-08 12:10:27  pierre
syntaxe xhtml

Revision 1.5  2008-07-16 10:55:28  pierre
*** empty log message ***

Revision 1.4  2008/07/16 10:48:36  pierre
*** empty log message ***

Revision 1.3  2008/07/16 10:04:38  pierre
*** empty log message ***

Revision 1.2  2007/11/15 18:08:49  pierre
*** empty log message ***

Revision 1.6  2007/11/07 16:59:23  remy
*** empty log message ***

Revision 1.5  2007/11/06 15:33:25  remy
*** empty log message ***

Revision 1.1  2007/08/08 14:26:20  thao
*** empty log message ***

Revision 1.3  2007/08/08 14:16:14  thao
*** empty log message ***

Revision 1.2  2007/08/08 13:56:04  thao
*** empty log message ***

Revision 1.1.1.1  2006/01/25 15:14:32  pierre
projet CCitron AWS 2006 Nouveau Website

Revision 1.3  2005/10/27 09:25:55  sylvie
*** empty log message ***

Revision 1.2  2005/10/24 15:10:07  pierre
arbo.lib.php >> arbominisite.lib.php

Revision 1.1.1.1  2005/10/24 13:37:05  pierre
re import fusion espace v2 et ADW v2

Revision 1.1.1.1  2005/10/20 13:10:53  pierre
Espace V2

Revision 1.3  2005/06/08 10:08:19  michael
Impossibilité de toucher à la Racine (Déplacement/Renommage/Suppression)
Suppression des espaces

Revision 1.2  2005/05/30 10:44:36  michael
Réparation de fonctions dans les briques et les pages
Renommer/ Déplacer...
Gestion des caractères spéciaux

Revision 1.1.1.1  2005/04/18 13:53:29  pierre
again

Revision 1.1.1.1  2005/04/18 09:04:21  pierre
oremip new


Revision 1.1.1.1  2004/11/03 13:49:54  ddinside

lancement du projet - import de adequat



Revision 1.3  2004/06/16 15:23:19  ddinside

inclusion corrections



Revision 1.2  2004/04/26 08:07:09  melanie

*** empty log message ***



Revision 1.1.1.1  2004/04/01 09:20:27  ddinside

Création du projet CMS Couleur Citron nom de code : tipunch



Revision 1.2  2003/11/26 13:08:42  ddinside

nettoyage des fichiers temporaires commités par erreur

ajout config spaw

corrections bug menu et positionnement des divs



Revision 1.2  2003/10/07 08:15:53  ddinside

ajout gestion de l'arborescence des composants



Revision 1.1  2003/09/25 14:55:16  ddinside

gestion de l'arborescene des composants : ajout

*/

// sopnthus 17/06/05
// ajout id_site

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');

// site sur lequel on est connecté
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];


activateMenu('gestioncontenu');  //permet de dérouler le menu contextuellement


$virtualPath = 0;

if ($_GET['v_comp_path']=="") { // Si on a pas de dossier par défaut on essaye de charger celui en mémoire
	if($_SESSION['brique_v_comp_path']!="") { // On a justement un dossier favoris en mémoire
		$_GET['v_comp_path'] = $_SESSION['brique_v_comp_path'];
	}
}

if (strlen($_GET['v_comp_path']) > 0)
	$virtualPath = $_GET['v_comp_path'];
	$_SESSION['brique_v_comp_path'] = $virtualPath;// On enregistre dans le dossier favoris le dossier courant

?>
<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<style type="text/css">

body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}

</style>
<form name="managetree" action="arboaddnode.php" method="post">

<table cellpadding="0" cellspacing="0" border="0">

 <tr>

  <td colspan="3"><table  border="0" cellspacing="0" cellpadding="0">

    <tr>

      <td width="130"><img src="../../backoffice/cms/img/tt_gestion_brique.gif" width="127" height="18"></td>

      <td valign="middle" class="arbo" style="vertical-align:middle">
	   &gt; <?php echo getAbsolutePathString($idSite, $db, $virtualPath); ?></td>

    </tr>

  </table></td>

 </tr>

 <tr>

  <td colspan="3"><img src="/backoffice/cms/img/vide.gif"></td>

 </tr>

 <tr>

  <td align="left" valign="top" class="arbo2"><br />

   <b><u>Arborescence :</u></b>

   <br />

   <br />

<table cellpadding="8" cellspacing="0" border="0">

<tr>

<td bgcolor="D2D2D2"><?php

print drawCompTree($idSite, $db, $virtualPath, null);

?></td>

</tr></table></td>

  <td width="15" align="left" valign="top" class="arbo2"><img src="/backoffice/cms/img/vide.gif" width="15"></td>

  <td align="left" valign="top" class="arbo2">

  <!-- contenu des actions -->   <u><b><br />

   Opérations sur le dossier en cours :</b></u><br />

   <br />

   <table border="0" cellpadding="5" cellspacing="0">

	<ul>   

  <tr bgcolor="E6E6E6">

       <td class="arbo"<?php if($virtualPath=="0") echo "title='Impossible de déplacer le dossier Racine' style='color:#b0b0b0'"; ?>><li>Déplacer le dossier</li></td>

       <td<?php if($virtualPath=="0") echo ' title="Impossible de déplacer le dossier Racine"><img border="0" src="'.$URL_ROOT.'/backoffice/cms/img/go_off.gif">'; else { ?>><a href="arbo_movefolder.php?v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a><?php } ?></td>

     </tr>

  <tr bgcolor="EEEEEE">

       <td bgcolor="EEEEEE" class="arbo"<?php if($virtualPath=="0") echo "title='Impossible de renommer le dossier Racine' style='color:#b0b0b0'"; ?>><li>Renommer le dossier</li></td>

       <td<?php if($virtualPath=="0") echo ' title="Impossible de renommer le dossier Racine"><img border="0" src="'.$URL_ROOT.'/backoffice/cms/img/go_off.gif">'; else { ?>><a href="arbo_renamefolder.php?v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a><?php } ?></td>

     </tr>

  <tr bgcolor="E6E6E6">

       <td class="arbo"<?php if($virtualPath=="0") echo "title='Impossible de supprimer le dossier Racine' style='color:#b0b0b0'"; ?>><li>Supprimer le dossier</li></td>

       <td<?php if($virtualPath=="0") echo ' title="Impossible de supprimer le dossier Racine"><img border="0" src="'.$URL_ROOT.'/backoffice/cms/img/go_off.gif">'; else { ?>><a href="arbo_deletefolder.php?v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a><?php } ?></td>

     </tr>

  <tr bgcolor="EEEEEE">

       <td class="arbo"><li>Créer un sous dossier</li></td>

       <td><a href="arbo_createfolder.php?v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>

     </tr>

	</ul>
   </table>

   </td>

 </tr>

</table>

</form>











