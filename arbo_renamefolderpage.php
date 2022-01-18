<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 
$Author: raphael $
$Revision: 1.1 $

$Log: arbo_renamefolderpage.php,v $
Revision 1.1  2013-09-30 09:23:59  raphael
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

Revision 1.3  2005/05/23 17:09:28  pierre
ajout option annuler

Revision 1.2  2005/05/23 16:20:04  pierre
ajout de la gestion des descriptions de node

Revision 1.1.1.1  2005/04/18 13:53:29  pierre
again

Revision 1.1.1.1  2005/04/18 09:04:21  pierre
oremip new

Revision 1.2  2004/11/12 08:59:19  ddinside
gabarit interieur + moteur de recherche

Revision 1.1.1.1  2004/11/03 13:49:54  ddinside
lancement du projet - import de adequat

Revision 1.3  2004/06/16 15:23:19  ddinside
inclusion corrections

Revision 1.2  2004/04/26 08:07:09  melanie
*** empty log message ***

Revision 1.1.1.1  2004/04/01 09:20:27  ddinside
Création du projet CMS Couleur Citron nom de code : tipunch

Revision 1.2  2004/02/12 15:56:16  ddinside
mise à jour plein de choses en fait, mais je sais plus quoi parce que ça fait longtemps que je l'avais pas fait.
Mea Culpa...

Revision 1.1  2003/11/27 14:45:56  ddinside
ajout gestion arbo disque non finie

Revision 1.2  2003/11/26 13:08:42  ddinside
nettoyage des fichiers temporaires commités par erreur
ajout config spaw
corrections bug menu et positionnement des divs

Revision 1.2  2003/10/07 08:53:28  ddinside
ajout gestionnaire de contenu

Revision 1.1  2003/10/07 08:15:53  ddinside
ajout gestion de l'arborescence des composants

Revision 1.1  2003/09/25 14:55:16  ddinside
gestion de l'arborescene des composants : ajout
*/

// sponthus 17/06/05
// ajout du site dans l'arbo

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');

// site
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

activateMenu('gestioncontenu');  //permet de dérouler le menu contextuellement

$id=0;
$virtualPath = 0;
$nodeInfos=array();
if(!strlen($_POST['foldername'])) {
	if (strlen($_GET['v_comp_path']) > 0)
		$virtualPath = $_GET['v_comp_path'];
	$nodeInfos=getNodeInfos($db, $virtualPath);
} else{
	if (strlen($_POST['v_comp_path']) > 0)
		$virtualPath = $_POST['v_comp_path'];
	$nodeInfos=getNodeInfos($db, $virtualPath);
	$id=renameNode($idSite, $db, $virtualPath, $_POST['foldername']);
}
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
</style><form name="managetree" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
  <table cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td colspan="3"><table  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="120"><img src="../../backoffice/cms/img/tt_gestion_page.gif" width="117" height="18"></td>
            <td valign="middle" class="arbo" style="vertical-align:middle">
			 &gt; <small><?php echo getAbsolutePathString($idSite, $db, $virtualPath,'pageArbo.php'); ?></small></td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td colspan="3"><img src="/backoffice/cms/img/vide.gif"></td>
    </tr>
    <tr>
      <td width="6" align="left" valign="top" class="arbo2"><br />
          <b><u>Arborescence :</u></b> <br />
          <table cellpadding="8" cellspacing="0" border="1" bordercolor="#FFFFFF">
            <tr>
              <td bgcolor="D2D2D2"><?php
print drawCompTree($idSite, $db,$virtualPath);
?></td>
            </tr>
          </table>
          <br />
          <br />
      </td>
      <td width="5"  align="left" valign="top" class="arbo2"><img src="/backoffice/cms/img/vide.gif" width="15"></td>
      <td  align="left" valign="top" class="arbo2">
        <!-- contenu des actions -->
        <?php
	if(!strlen($_POST['foldername'])) {
?>
        <u><b><br />
      D&eacute;placer un dossier :</b></u> <br />
      <br />
      <table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF">
        <tr bgcolor="E6E6E6">
          <td class="arbo" style="vertical-align:middle">Ancien nom&nbsp;:</td>
          <td><input name="dummy" type="text" disabled class="arbo" id="dummy" value="<?php echo $nodeInfos['libelle']; ?>" size="14">
              <input type="hidden" name="v_comp_path" value="<?php echo $virtualPath; ?>"></td>
        </tr>
        <tr bgcolor="EEEEEE">
          <td bgcolor="EEEEEE" class="arbo" style="vertical-align:middle">Nouveau nom :</td>
          <td><input name="foldername" type="text" class="arbo" id="foldername" value="<?php echo $nodeInfos['libelle']; ?>" size="14"></td>
        </tr>
        <tr align="center" bgcolor="EEEEEE">
          <td colspan="2" bgcolor="D2D2D2" class="arbo"><a href="pageArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><strong>Annuler</strong></a>&nbsp;&nbsp;<a href="pageArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a> 
              - <a href="#" class="arbo" onClick="javascript:document.forms[0].submit();"><strong>Renommer le dossier</strong></a>&nbsp;&nbsp;<a href="#" onClick="javascript:document.forms[0].submit();" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>
        </tr>
      </table>
      <br />
      <?php
	} else {
		// renommage d'un dossier
		if($id!=false) {
?>
      <br />
      <br />
      <br />
      <b><span class="arbo">Le dossier <?php echo $nodeInfos['libelle']; ?> 
	  a bien &eacute;t&eacute; renomm&eacute; en <?php echo $_POST['foldername']; ?></span></b>
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
    <!--
 <tr height="1" bgcolor="#000000">
  <td colspan="5"><img src="/backoffice/cms/img/vide.gif"></td>
 </tr>-->
  </table>
  </form>





