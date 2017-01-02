<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/* 

$Author: raphael $

$Revision: 1.1 $



$Log: arbo_orderfolder.php,v $
Revision 1.1  2013-09-30 09:43:11  raphael
*** empty log message ***

Revision 1.5  2013-04-15 12:12:44  thao
*** empty log message ***

Revision 1.4  2013-03-01 10:28:20  pierre
*** empty log message ***

Revision 1.3  2012-07-31 14:24:50  pierre
*** empty log message ***

Revision 1.2  2012-04-03 10:34:32  thao
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

Revision 1.3  2008/07/16 08:42:48  pierre
*** empty log message ***

Revision 1.2  2007/08/09 10:56:30  thao
*** empty log message ***

Revision 1.1  2007/08/08 14:26:26  thao
*** empty log message ***

Revision 1.4  2007/08/08 14:16:15  thao
*** empty log message ***

Revision 1.3  2007/08/08 13:56:04  thao
*** empty log message ***

Revision 1.2  2007/03/27 15:10:39  pierre
*** empty log message ***

Revision 1.1  2006/12/15 12:30:11  pierre
*** empty log message ***

Revision 1.1.1.1  2006/01/25 15:14:32  pierre
projet CCitron AWS 2006 Nouveau Website

Revision 1.3  2005/12/20 08:23:25  sylvie
application nouveaux styles titre

Revision 1.2  2005/10/27 09:25:55  sylvie
*** empty log message ***

Revision 1.1.1.1  2005/10/20 13:10:53  pierre
Espace V2

Revision 1.3  2005/05/30 12:17:12  michael
Réparation de fonctions dans les briques et les pages
Réordonner des dossiers

Revision 1.2  2005/05/23 17:09:28  pierre
ajout option annuler


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
// arbo des mini sites

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
include_once('backoffice/cms/site/minisite.inc.php');

// site connecté
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

// pas de site
if ($idSite == "") die ("Appel incorrect de la fonctionnalité");

activateMenu('gestioncontenu');  //permet de dérouler le menu contextuellement

$result=false;

$virtualPath = 0;

$nodeInfos=array();

$nodeChildren = array();

$orders = array();

if(!preg_match( '/^order_.+$/', join( array_keys($_POST) ,"," ) ) ) { // Première visite

	if (strlen($_GET['v_comp_path']) > 0)

		$virtualPath = $_GET['v_comp_path'];

	$nodeInfos=getNodeInfos($db,$virtualPath);

	$nodeChildren=getNodeChildren($idSite, $db,$virtualPath);

} else{

	if (strlen($_POST['v_comp_path']) > 0)

		$virtualPath = $_POST['v_comp_path'];

	$nodeInfos=getNodeInfos($db,$virtualPath);

	foreach($_POST as $k => $v) {

		if(preg_match('/^order_.+$/',$k)) {

			$id = preg_replace('/^order_/','',$k);

			if(intval($id) > 0) {

				 $orders[$id] = $v; // création du tableau [ [id_noeud1,ordre_noeud1], [id_noeud2,ordre_noeud2], ... ]

			}

		}

	}

	$result = saveNodeOrder($idSite, $orders,$db,$virtualPath);

	$nodeChildren=getNodeChildren($idSite, $db,$virtualPath);

}

?>




<form id="managetree" name="managetree" action="<?php echo $_SERVER['PHP_SELF'];?>?idSite=<?php echo $idSite; ?><?php echo $param; ?>" method="post">
<div class="ariane">
<span class="arbo2">ARBORESCENCE&nbsp;>&nbsp;</span>
<span class="arbo3">Ordonner des dossiers&nbsp;>&nbsp;</span>
<span class="arbo4"><?php echo getAbsolutePathString($idSite, $db, $virtualPath,'pageArbo.php'); ?></span>
</div>
  <table cellpadding="0" cellspacing="0" border="0">
    <tr>

      <td colspan="3"><img src="/backoffice/cms/img/vide.gif"></td>

    </tr>

    <tr>

      <td align="left" valign="top" class="arbo">
	  	<div id="arborescence" ><p><b><u>Arborescence :</u></b></p>

          <table cellpadding="8" cellspacing="0" border="1" bordercolor="#FFFFFF">

            <tr>

              <td bgcolor="D2D2D2"><?php

//print drawCompTree($idSite, $db,$virtualPath);
print drawCompTree($idSite, $db, $virtualPath, null, "/backoffice/cms/site/arbo.php");
?></td>

            </tr>

        </table></div></td>

      <td width="15" align="left" valign="top" class="arbo2"><img src="/backoffice/cms/img/vide.gif" width="15"></td>

      <td align="left" valign="top" class="arbo">

        <!-- contenu des actions -->

        <b><br />

        <?php

	if(!strlen($_POST['foldername'])) {

?>Ordonner un dossier</b><br />

      <br />
<?php
if(count($nodeChildren)>0) { // On a des choses à faire!
      echo '<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF">';
}

// Lister chaque sous-dossier du dossier en cours
foreach($nodeChildren as $elt) {
?>
        <tr bgcolor="E6E6E6">

          <td class="arbo" style="vertical-align:middle"><small>Entr&eacute;e&nbsp;:&nbsp;<?php echo strip_tags($elt['libelle']); ?></small></td>

          <td><input name="order_<?php echo $elt['id']; ?>" type="text" class="arbo" id="order_<?php echo $elt['id']; ?>" value="<?php echo $elt['order']; ?>" size="4" maxlength="4"></td>

        </tr>
<?php
}
// Fin Lister chaque sous-dossier du dossier en cours

if(count($nodeChildren)>0) { // On a des choses à faire!
?>


        <tr align="center" bgcolor="EEEEEE">

          <td colspan="2" bgcolor="D2D2D2" class="arbo"><a href="pageArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><strong>Annuler</strong></a>&nbsp;&nbsp;<a href="pageArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a> 

              - <a href="#" class="arbo" onClick="javascript:document.forms['managetree'].submit();"><strong>Enregistrer</strong></a>&nbsp;&nbsp;<a href="#" onClick="javascript:document.forms['managetree'].submit();" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>

        </tr>
      </table>
<?php
}
else { // Il n'y a rien à ordonner => on le dit!
?>
        <b><span class="arbo">Il n'y a aucun sous-dossier à ordonner dans ce dossier</span></b>
<?php
}
?>

      <br />

      <?php

	} else {

		// renommage d'un dossier

		if($result) {

?>

      <br />

      <br />

      <br />

      <b><span class="arbo">Le dossier <?php echo $nodeInfos['libelle']; ?> a bien &eacute;t&eacute; param&eacute;tr&eacute;</span></b>

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


