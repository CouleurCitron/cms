<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/* 

$Author: raphael $

$Revision: 1.1 $





$Log: pageArbo.php,v $
Revision 1.1  2013-09-30 09:24:11  raphael
*** empty log message ***

Revision 1.11  2013-03-25 14:04:09  pierre
*** empty log message ***

Revision 1.10  2013-03-19 15:49:03  pierre
*** empty log message ***

Revision 1.9  2013-03-01 10:28:04  pierre
*** empty log message ***

Revision 1.8  2012-07-31 14:24:47  pierre
*** empty log message ***

Revision 1.7  2010-03-08 12:10:28  pierre
syntaxe xhtml

Revision 1.6  2008-07-16 10:55:28  pierre
*** empty log message ***

Revision 1.5  2008/07/16 10:48:36  pierre
*** empty log message ***

Revision 1.4  2008/07/16 10:04:38  pierre
*** empty log message ***

Revision 1.3  2008/04/21 09:07:19  pierre
*** empty log message ***

Revision 1.2  2007/11/15 18:08:49  pierre
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

Revision 1.2  2005/10/27 09:25:55  sylvie
*** empty log message ***

Revision 1.1.1.1  2005/10/20 13:10:53  pierre
Espace V2

Revision 1.4  2005/06/08 10:08:19  michael
Impossibilité de toucher à la Racine (Déplacement/Renommage/Suppression)
Suppression des espaces

Revision 1.3  2005/05/30 10:44:36  michael
Réparation de fonctions dans les briques et les pages
Renommer/ Déplacer...
Gestion des caractères spéciaux

Revision 1.2  2005/05/23 16:20:04  pierre
ajout de la gestion des descriptions de node


Revision 1.1.1.1  2005/04/18 13:53:29  pierre

again



Revision 1.1.1.1  2005/04/18 09:04:21  pierre

oremip new



Revision 1.1.1.1  2004/11/03 13:49:54  ddinside

lancement du projet - import de adequat



Revision 1.4  2004/06/18 14:10:21  ddinside

corrections diverses en vu de la demo prevention routiere



Revision 1.3  2004/06/16 15:23:19  ddinside

inclusion corrections



Revision 1.2  2004/04/26 08:07:09  melanie

*** empty log message ***



Revision 1.1.1.1  2004/04/01 09:20:27  ddinside

Création du projet CMS Couleur Citron nom de code : tipunch



Revision 1.1  2003/11/27 14:45:56  ddinside

ajout gestion arbo disque non finie



Revision 1.2  2003/11/26 13:08:42  ddinside

nettoyage des fichiers temporaires commités par erreur

ajout config spaw

corrections bug menu et positionnement des divs



Revision 1.2  2003/10/07 08:15:53  ddinside

ajout gestion de l'arborescence des composants



Revision 1.1  2003/09/25 14:55:16  ddinside

gestion de l'arborescene des composants : ajout

*/



include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');

// site
$oSite = new Cms_site();
//$oSite->getSite();
$idSite = $oSite->get_id();


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

</style><form name="managetree" action="arboaddnode.php" method="post">

  <table cellpadding="0" cellspacing="0" border="0">

    <tr>

      <td colspan="3"><table  border="0" cellspacing="0" cellpadding="0">

          <tr>

            <td width="120"><img src="../../backoffice/cms/img/tt_gestion_page.gif" width="117" height="18"></td>

            <td valign="middle" class="arbo" style="vertical-align:middle">
			 &gt; <small><?php echo getAbsolutePathString($idSite, $db, $virtualPath); ?></small></td>

          </tr>

      </table></td>

    </tr>

    <tr>

      <td colspan="3"><img src="/backoffice/cms/img/vide.gif"></td>

    </tr>

    <tr>

      <td align="left" valign="top" class="arbo2"><br />

          <b><u>Arborescence :</u></b> <br />

          <br />

          <table cellpadding="8" cellspacing="0" border="1" bordercolor="#FFFFFF">

            <tr>

              <td bgcolor="D2D2D2"><?php

print drawCompTree($idSite, $db, $virtualPath, null);

?></td>

            </tr>

        </table></td>

      <td width="15" align="left" valign="top" class="arbo2"><img src="/backoffice/cms/img/vide.gif" width="15"></td>

      <td align="left" valign="top" class="arbo2">

        <!-- contenu des actions -->

        <u><b><br />

      Op&eacute;rations sur le dossier en cours :</b></u><br />

      <br />

      <table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF">

        <ul>

          <tr bgcolor="E6E6E6">

            <td class="arbo"<?php if($virtualPath=="0") echo "title='Impossible de déplacer le dossier Racine' style='color:#b0b0b0'"; ?>><li>D&eacute;placer le dossier</li></td>
            <td<?php if($virtualPath=="0") echo ' title="Impossible de déplacer le dossier Racine"><img border="0" src="'.$URL_ROOT.'/backoffice/cms/img/go_off.gif">'; else { ?>><a href="arbo_movefolderpage.php?v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a><?php } ?></td>

          </tr>

          <tr bgcolor="EEEEEE">

            <td bgcolor="EEEEEE" class="arbo"<?php if($virtualPath=="0") echo "title='Impossible de renommer le dossier Racine' style='color:#b0b0b0'"; ?>><li>Renommer le dossier</li></td>
            <td<?php if($virtualPath=="0") echo ' title="Impossible de renommer le dossier Racine"><img border="0" src="'.$URL_ROOT.'/backoffice/cms/img/go_off.gif">'; else { ?>><a href="arbo_renamefolderpage.php?v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a><?php } ?></td>

          </tr>

          <tr bgcolor="E6E6E6">

            <td bgcolor="E6E6E6" class="arbo"<?php if($virtualPath=="0") echo "title='Impossible de supprimer le dossier Racine' style='color:#b0b0b0'"; ?>><li>Supprimer le dossier</li></td>
            <td<?php if($virtualPath=="0") echo ' title="Impossible de supprimer le dossier Racine"><img border="0" src="'.$URL_ROOT.'/backoffice/cms/img/go_off.gif">'; else { ?>><a href="arbo_deletefolderpage.php?v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a><?php } ?></td>

          </tr>

          <tr bgcolor="EEEEEE">

            <td class="arbo"><li>Cr&eacute;er un sous dossier</li></td>

            <td><a href="arbo_createfolderpage.php?v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>

          </tr>

          <tr bgcolor="E6E6E6">

            <td class="arbo"><li><small>Ordre d'affichage des<br />

&nbsp;&nbsp;              &nbsp;sous dossiers</small></li></td>

            <td><a href="arbo_orderfolderpage.php?v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>

          </tr>

		            <tr bgcolor="E6E6E6">

            <td class="arbo"><li>Description du dossier</li></td>

            <td><a href="arbo_descriptionpage.php?v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>

          </tr>

        </ul>

    </table></td>

    </tr>

  </table>

  </form>


<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/append.php');
?>