<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/* 

$Author: raphael $

$Revision: 1.1 $



$Log: arbo_createfolder.php,v $
Revision 1.1  2013-09-30 09:43:07  raphael
*** empty log message ***

Revision 1.6  2013-04-15 12:05:42  thao
*** empty log message ***

Revision 1.5  2013-03-01 10:28:20  pierre
*** empty log message ***

Revision 1.4  2012-07-31 14:24:50  pierre
*** empty log message ***

Revision 1.3  2012-04-03 10:34:32  thao
*** empty log message ***

Revision 1.2  2010-08-26 16:14:39  pierre
*** empty log message ***

Revision 1.1  2010-08-23 08:29:51  pierre
*** empty log message ***

Revision 1.8  2009-09-24 08:53:13  pierre
*** empty log message ***

Revision 1.7  2008-07-16 12:43:07  pierre
*** empty log message ***

Revision 1.6  2008/07/16 10:55:28  pierre
*** empty log message ***

Revision 1.5  2008/07/16 10:48:36  pierre
*** empty log message ***

Revision 1.4  2008/07/16 10:04:38  pierre
*** empty log message ***

Revision 1.3  2007/11/30 13:29:54  thao
*** empty log message ***

Revision 1.2  2007/08/09 10:56:30  thao
*** empty log message ***

Revision 1.1  2007/08/08 14:26:26  thao
*** empty log message ***

Revision 1.5  2007/08/08 14:16:15  thao
*** empty log message ***

Revision 1.4  2007/08/08 13:56:04  thao
*** empty log message ***

Revision 1.3  2007/03/27 15:10:39  pierre
*** empty log message ***

Revision 1.2  2007/03/27 13:05:26  pierre
*** empty log message ***

Revision 1.1  2007/01/08 14:29:31  arnaud
*** empty log message ***

Revision 1.1  2006/11/28 13:29:17  pierre
*** empty log message ***

Revision 1.1.1.1  2006/01/25 15:14:32  pierre
projet CCitron AWS 2006 Nouveau Website

Revision 1.3  2005/12/20 08:23:25  sylvie
application nouveaux styles titre

Revision 1.2  2005/10/27 09:25:55  sylvie
*** empty log message ***

Revision 1.1.1.1  2005/10/20 13:10:53  pierre
Espace V2

Revision 1.6  2005/05/30 11:01:11  michael
Réparation de fonctions dans les briques et les pages
Probleme avec la racine (pas très bien géré)
disparition de la description lors de la créa d'un dossier

Revision 1.5  2005/05/23 17:09:28  pierre
ajout option annuler


Revision 1.4  2005/05/23 16:20:04  pierre

ajout de la gestion des descriptions de node



Revision 1.3  2005/04/19 09:25:02  melanie

ajout index + actu



Revision 1.2  2005/04/18 16:21:57  pierre

nom de dossier à 255 cars



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



Revision 1.1  2003/10/07 08:15:53  ddinside

ajout gestion de l'arborescence des composants



Revision 1.1  2003/09/25 14:55:16  ddinside

gestion de l'arborescene des composants : ajout

*/

// sponthus 17/06/05
// arbo des mini sites


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/utils/dir.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
include_once('backoffice/cms/site/minisite.inc.php');

activateMenu('gestionpage');  //permet de dérouler le menu contextuellement

// site connecté
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

// pas de site
if ($idSite == "") die ("Appel incorrect de la fonctionnalité");


$id=0;

$virtualPath = 0;

$nodeInfos=array();
  
if(!strlen($_POST['foldername'])) {

	if (strlen($_GET['v_comp_path']) > 0) $virtualPath = $_GET['v_comp_path'];

	$nodeInfos=getNodeInfos($db, $virtualPath);

} else{

	if (strlen($_POST['v_comp_path']) > 0) 	$virtualPath = $_POST['v_comp_path'];

	$nodeInfos=getNodeInfos($db, $virtualPath);

	$id = addNode($idSite, $db, $virtualPath,$_POST['foldername']);

	if($id!=false) {
		$virtualPath.=",$id";
	}
}

?>


<form id="managetree" name="managetree" action="<?php echo $_SERVER['PHP_SELF'];?>?idSite=<?php echo $idSite; ?><?php echo $param; ?>" method="post">

<input type="hidden" name="idSite" value="<?php echo $idSite; ?>">
<div class="ariane">
<span class="arbo2">ARBORESCENCE&nbsp;>&nbsp;</span>
<span class="arbo3">Créer un sous dossier&nbsp;>&nbsp;</span>
<span class="arbo4"><?php echo getAbsolutePathString($idSite, $db, $virtualPath, 'pageArbo.php'); ?></span>
</div>


  <table cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td colspan="3"><img src="/backoffice/cms/img/vide.gif"></td>
   </tr>
    <tr>
      <td width="6" align="left" valign="top" class="arbo"> 
	  	  <div id="arborescence" >	
          <p><b><u>Arborescence :</u></b></p>
          <table cellpadding="8" cellspacing="0" border="1" bordercolor="#FFFFFF">
            <tr>
              <td bgcolor="D2D2D2"><?php
//print drawCompTree($idSite, $db, $virtualPath, null);
print drawCompTree($idSite, $db, $virtualPath, null, "/backoffice/cms/site/arbo.php");
?></td>

            </tr>

          </table>

          </div>

      </td>

      <td width="5"  align="left" valign="top" class="arbo2"><img src="/backoffice/cms/img/vide.gif" width="15"></td>

      <td  align="left" valign="top" class="arbo">

        <!-- contenu des actions -->

        <?php

	if(!strlen($_POST['foldername'])) {

?>

        <b><br />

      Cr&eacute;ation d'un sous dossier :</b> <br />

      <br />

      <table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF">

        <tr>

          <td  class="arbo">Dossier P&egrave;re&nbsp;:</td>

          <td align="left"><input name="dummy" type="text" disabled class="arbo" id="dummy" value="<?php echo $nodeInfos['libelle']; ?>" size="14">

              <input type="hidden" name="v_comp_path" value="<?php echo $virtualPath; ?>"></td>

        </tr>

        <tr bgcolor="E6E6E6">

          <td class="arbo" style="vertical-align:middle"><small>Nom du dossier&nbsp;:</small></td>

          <td><input name="foldername" type="text" class="arbo" id="foldername" size="25" maxlength="255"></td>

        </tr>

        <!--tr bgcolor="E6E6E6">

          <td class="arbo" style="vertical-align:middle"><small>Description&nbsp;:</small></td>

          <td><input name="foldername" type="text" class="arbo" id="folderdescription" size="25" maxlength="255"></td>

        </tr-->

        <tr align="center" bgcolor="EEEEEE">

          <td colspan="2" bgcolor="D2D2D2" class="arbo"><a href="pageArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><strong>Annuler</strong></a>&nbsp;&nbsp;<a href="pageArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a> 

              - <a href="#" class="arbo" onClick="javascript:document.forms['managetree'].submit();"><strong>Cr&eacute;er le dossier</strong></a>&nbsp;&nbsp;<a href="#" onClick="javascript:document.forms['managetree'].submit();" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>

        </tr>

      </table>

      <br />

      <?php

	} else {

		// creation du sous dossier

		if($id!=false) {

?>

      <br />

      <br />

      <br />

      <b><span class="arbo">Le dossier "<?php echo strip_tags($_POST['foldername']); ?>" a bien &eacute;t&eacute; cr&eacute;&eacute; dans le dossier</span></b>

      <?php $nodeInfos['libelle']; ?>

      <?php

		} else {

?>

      <br />

      <br />

      <br />

      <b><span class="arbo">Un probl&egrave;me est survenu durant l'op&eacute;ration. <br />

      Veuillez r&eacute;essayer ou contacter l'administrateur si l'erreur persiste.</span></b>

      <?php

		}

	}

?></td>

    </tr>

  </table>

  </form>











