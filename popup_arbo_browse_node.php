<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 
$Author: raphael $
$Revision: 1.1 $

$Log: popup_arbo_browse_node.php,v $
Revision 1.1  2013-09-30 09:24:15  raphael
*** empty log message ***

Revision 1.8  2013-03-01 10:28:04  pierre
*** empty log message ***

Revision 1.7  2012-09-14 13:15:14  pierre
*** empty log message ***

Revision 1.6  2012-07-31 14:24:48  pierre
*** empty log message ***

Revision 1.5  2012-04-13 07:18:38  pierre
*** empty log message ***

Revision 1.4  2012-03-20 08:41:24  thao
on récupère l'idSite en paramètre GET puis en session

Revision 1.3  2009-09-24 08:53:12  pierre
*** empty log message ***

Revision 1.2  2008-11-28 14:09:55  pierre
*** empty log message ***

Revision 1.1  2008-11-27 09:14:07  thao
*** empty log message ***

Revision 1.7  2008-11-06 12:03:53  pierre
*** empty log message ***

Revision 1.6  2008-07-16 10:55:28  pierre
*** empty log message ***

Revision 1.5  2008/07/16 10:04:38  pierre
*** empty log message ***

Revision 1.4  2007/11/15 18:08:49  pierre
*** empty log message ***

Revision 1.5  2007/11/06 15:33:25  remy
*** empty log message ***

Revision 1.3  2007/08/28 15:55:28  pierre
*** empty log message ***

Revision 1.2  2007/08/27 10:13:47  pierre
*** empty log message ***

Revision 1.1  2007/08/08 14:26:20  thao
*** empty log message ***

Revision 1.3  2007/08/08 14:16:14  thao
*** empty log message ***

Revision 1.2  2007/08/08 13:56:04  thao
*** empty log message ***

Revision 1.1  2007/08/06 09:24:42  thao
*** empty log message ***

Revision 1.1  2007/08/06 09:23:31  thao
*** empty log message ***

Revision 1.1  2007/02/27 16:40:33  pierre
*** empty log message ***

Revision 1.1  2006/07/24 14:57:25  pierre
*** empty log message ***

Revision 1.1  2006/04/12 07:25:19  sylvie
*** empty log message ***

Revision 1.2  2005/05/23 10:17:54  michael
Ajout de la brique graphique
Traitement spécial pour la créa/édition/suppression/duplication

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

Revision 1.4  2004/02/12 15:56:16  ddinside
mise à jour plein de choses en fait, mais je sais plus quoi parce que ça fait longtemps que je l'avais pas fait.
Mea Culpa...

Revision 1.3  2004/01/07 18:27:37  ddinside
première mise à niveau pour plein de choses

Revision 1.2  2003/11/26 13:08:42  ddinside
nettoyage des fichiers temporaires commités par erreur
ajout config spaw
corrections bug menu et positionnement des divs

Revision 1.1  2003/10/16 21:19:46  ddinside
suite dev gestio ndes composants
ajout librairies d'images
suppressions fichiers vi
ajout gabarit

Revision 1.2  2003/10/07 08:15:53  ddinside
ajout gestion de l'arborescence des composants

Revision 1.1  2003/09/25 14:55:16  ddinside
gestion de l'arborescene des composants : ajout
*/

// sopnthus 17/06/05
// ajout id_site

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
 include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


// site connecté
if (is_get("idSite") && $_GET["idSite"]!=-1) {
	$idSite = $_GET["idSite"];
}
else {
	$idSite = $_SESSION['idSite_travail'];
}
 
// noms des champs à mettre à jour dans la fenêtre opener
$idDiv = $_GET['idDiv'];
if ($idDiv == "") $idDiv = $_POST['idDiv'];

// is minis site ?
$bMinisite = isMinisite($idSite);

$virtualPath = 0;
if (strlen($_GET['v_comp_path']) > 0)
	$virtualPath = $_GET['v_comp_path'];
?>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<script type="text/javascript">document.title="Arbo - Briques";</script>
<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css" />
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.inactivligne { color:#999999 }
-->
</style>
<script type="text/javascript"> 
	function setSelectZoneEdit(idNode, libelleNode){ // on a sélectionné une brique
	 
		
		
		<?php 
		
		if (is_as_get("source")) {
		?>	
			window.opener.document.<?php echo $_GET['idForm']; ?>.<?php echo $_GET['idField']; ?>_<?php echo $_GET['source']; ?>.value = idNode;
			window.opener.document.<?php echo $_GET['idForm']; ?>.<?php echo $_GET['idField']; ?>_libelle_<?php echo $_GET['source']; ?>.value = libelleNode;
		<?php
		}
		else {
		?>
			window.opener.document.<?php echo $_GET['idForm']; ?>.<?php echo $_GET['idField']; ?>.value = idNode;
			window.opener.document.<?php echo $_GET['idForm']; ?>.<?php echo $_GET['idField']; ?>_libelle.value = libelleNode;
		<?php
		}
		
		?>
		
		self.close();
	} 
</script>

<form name="managetree" action="arboaddnode.php" method="post">

<input type="hidden" name="urlRetour" value="<?php echo $_POST['urlRetour']; ?>">
<input type="hidden" name="idSite" value="<?php echo $idSite; ?>">
<input type="hidden" name="idDiv" value="<?php echo $idDiv; ?>">
 
<div class="ariane"><span class="arbo2">GESTION DE L'ARBORESCENCE&nbsp;>&nbsp;</span>
<span class="arbo3"> <?php echo getAbsolutePathString($idSite, $db, $virtualPath); ?></span></div>
  <table cellpadding="0" cellspacing="0" border="0">
 
 
 <tr>
   <td align="left" valign="top" class="arbo">
       <table cellpadding="8" cellspacing="0" border="1" bordercolor="#FFFFFF">
         <tr>
           <td bgcolor="D2D2D2"><?php
print drawCompTree($idSite, $db, $virtualPath, null, null, "&idField=".$_GET["idField"]."&idForm=".$_GET["idForm"]."&source=".$_GET["source"]."");
?></td>
         </tr>
     </table></td>
  <td colspan="3" class="arbo"><span class="arbo"><img src="/backoffice/cms/img/vide.gif" width="15"></span></td>
  <td align="left" valign="top" class="arbo">
  <!-- contenu du dossier -->
	 <?php
	 $aNode = split (",", $virtualPath);
	 $idNode = $aNode[sizeof($aNode)-1];
	 $infosNode = getNodeInfos($db, $virtualPath);
	 $libelleNode = $infosNode["path"];
   	 echo "<a href=\"javascript:setSelectZoneEdit('".$idNode."', '".$libelleNode."')\">Valider</a>";?>
   </td>
 </tr>
</table>
</form>

 


