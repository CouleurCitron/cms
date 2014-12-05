<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 
$Author: pierre $
$Revision: 1.17 $

$Log: popup_arbo_browse.php,v $
Revision 1.17  2014-01-05 14:48:58  pierre
*** empty log message ***

Revision 1.16  2013-03-01 10:28:04  pierre
*** empty log message ***

Revision 1.15  2012-09-14 13:15:14  pierre
*** empty log message ***

Revision 1.14  2012-07-31 14:24:48  pierre
*** empty log message ***

Revision 1.13  2012-04-13 07:18:38  pierre
*** empty log message ***

Revision 1.12  2011-06-15 13:28:32  thao
*** empty log message ***

Revision 1.11  2010-03-08 12:10:28  pierre
syntaxe xhtml

Revision 1.10  2009-09-24 08:53:12  pierre
*** empty log message ***

Revision 1.9  2008-11-28 14:09:55  pierre
*** empty log message ***

Revision 1.8  2008-11-06 12:03:53  pierre
*** empty log message ***

Revision 1.7  2008-07-16 10:55:28  pierre
*** empty log message ***

Revision 1.6  2008/07/16 10:48:36  pierre
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

Revision 1.1.1.1  2006/01/25 15:14:32  pierre
projet CCitron AWS 2006 Nouveau Website

Revision 1.6  2005/12/06 15:26:37  sylvie
*** empty log message ***

Revision 1.5  2005/11/18 10:23:15  melanie
*** empty log message ***

Revision 1.4  2005/11/04 15:13:12  sylvie
*** empty log message ***

Revision 1.3  2005/11/04 08:36:52  sylvie
*** empty log message ***

Revision 1.2  2005/10/28 07:53:14  sylvie
*** empty log message ***

Revision 1.1.1.1  2005/10/20 13:10:53  pierre
Espace V2

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
$idSite = $_GET['idSite'];
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

// noms des champs à mettre à jour dans la fenêtre opener
$idDiv = $_GET['idDiv'];
if ($idDiv == "") $idDiv = $_POST['idDiv'];


//==============================================
// le div à mettre à jour est soit : 

// 1. une zone editable : ze
// 2. une brique fixe : bf
// 3. une brique éditable : div (devrait s'appeler be)

// on reconstitrue l'objet Content (oDiv) pour connaitre sa nature

$oDiv = new cms_content($idDiv);
if ($oDiv->getIszonedit_content() == 1) $sDiv="ze";
else if ($oDiv->getIsbriquedit_content() == 1) $sDiv="div";
else $sDiv="bf";

// on appelle donc le div pas son vrai nom : ze1845 ou bf1845 ou div1845

//==============================================



// is minis site ?
$bMinisite = isMinisite($idSite);

//-------------------------------------------------
// dossier parcouru de l'arborescence en mémoire

$virtualPath = 0;

if ($_GET['v_comp_path']=="") { // Si on a pas de dossier par défaut on essaye de charger celui en mémoire
	if($_SESSION['brique_v_comp_path']!="") { // On a justement un dossier favoris en mémoire
		$_GET['v_comp_path'] = $_SESSION['brique_v_comp_path'];
	}
}

if (strlen($_GET['v_comp_path']) > 0)
	$virtualPath = $_GET['v_comp_path'];
	$_SESSION['brique_v_comp_path'] = $virtualPath;// On enregistre dans le dossier favoris le dossier courant

//-------------------------------------------------
?>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<script type="text/javascript">document.title="Arbo - Briques";</script>
<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
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

	function setSelectZoneEdit(eId, sLibelle){ // on a sélectionné une brique
		window.opener.document.forms['assemble'].elements['bex<?php echo $idDiv; ?>'].value = eId;
		document.getElementById("previewBrique").src='/backoffice/cms/getComposant.php?id='+eId
		// En plus du onload, on force la récup du HTML de la frame au bout de 1,2sec.
		// A cause des objets (flash...) qui mettre trop de temps à se charger
		setTimeout('putHtmlBriqueToOpener(document.getElementById("previewBrique"))',1200)
	}

	function putHtmlBriqueToOpener(oFrame) { // le code source de la brique est récupéré
		if(oFrame.contentWindow.document.body.innerHTML != "") {
			window.opener.loadBrique('<?php echo $sDiv; ?>', <?php echo $idDiv; ?>, oFrame.contentWindow.document.body.innerHTML);
			setTimeout("window.close()",100)
		}
		
	}
</script>

<form name="managetree" action="arboaddnode.php" method="post">

<input type="hidden" name="urlRetour" value="<?php echo $_POST['urlRetour']; ?>">
<input type="hidden" name="idSite" value="<?php echo $idSite; ?>">
<input type="hidden" name="idDiv" value="<?php echo $idDiv; ?>">

<h1>Liste des briques</h1>

  <table cellpadding="0" cellspacing="0" border="0">
 <tr>
  <td colspan="5" class="arbo"><table  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="130"><img src="../../backoffice/cms/img/tt_gestion_brique.gif" width="127" height="18"></td>
      <td class="arbo" style="vertical-align:middle"> &gt; <?php echo getAbsolutePathString($idSite, $db, $virtualPath); ?></td>
    </tr>
  </table></td>
 </tr>
 <tr>
  <td colspan="5"><img src="/backoffice/cms/img/vide.gif"></td>
 </tr>
 <tr>
   <td align="left" valign="top" class="arbo2"><br />
       <b><u>Arborescence :</u></b> <br />
       <br />
       <table cellpadding="8" cellspacing="0" border="1" bordercolor="#FFFFFF">
         <tr>
           <td bgcolor="D2D2D2"><?php
print drawCompTree($idSite, $db, $virtualPath, null, null, "&idDiv=$idDiv");
?></td>
         </tr>
     </table></td>
  <td colspan="3" class="arbo2"><span class="arbo2"><img src="/backoffice/cms/img/vide.gif" width="15"></span></td>
  <td align="left" valign="top" class="arbo2">
  <!-- contenu du dossier -->
   <u><b><small><br />
   Composants contenus dans le dossier en cours:</small></b></u>
   <br />
   <br />
  <table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
  <tr>
	<td align="center" bgcolor="E6E6E6"><strong>&nbsp;&nbsp;Nom</strong></td>
<?php
if ($bMinisite) {
?>
	<td width="5" align="center" bgcolor="EEEEEE"><strong>&nbsp;&nbsp;Site</strong></td>
<?php
}
?>
	<td width="5" align="center" bgcolor="EEEEEE"><strong>&nbsp;&nbsp;Dimensions</strong></td>
	<td align="center" bgcolor="E6E6E6"><strong>&nbsp;&nbsp;Type</strong></td>
	<td align="center" bgcolor="EEEEEE"><strong>Pages concernées</strong></td>
  </tr>
  <?php
	$aContent = getFolderContents($idSite, $virtualPath);
	if(!is_array($aContent)) {
?>
  <tr>
    <td align="center" colspan="5"><strong>&nbsp;Aucun élément à afficher</strong>
  </tr>
<?php
	} else {
		foreach ($aContent as $k => $oContent) {
			$nb_pages = sizeof( getPageUsingComposant( $idSite, $oContent->getId_content() ) );
			
			// page de modif de la brique
			if ($oContent->getType_content() == "Graphique") $sPageEdit = "graphic";
			else if ($oContent->getType_content() == "formulaire") {
				$sPageEdit = "form";
				$sParam = "step=init&";
			}
			else  $sPageEdit = "content";

			// site de la brique
			$oCms_site = new Cms_site($oContent->getId_site());
			
?>
  <tr <?php if ($oContent->getIsbriquedit_content()==1) echo 'title="Vous ne pouvez pas sélectionner une brique éditable" class="inactivligne"'; ?>>
   <td align="center" bgcolor="F3F3F3">&nbsp; <?php

   	if($oContent->getIsbriquedit_content()==1)
   		echo $oContent->getName_content();
	else
   		echo "<a href=\"javascript:setSelectZoneEdit(".$oContent->getId_content().", '')\">".$oContent->getName_content()."</a>";
	?>&nbsp;</td>
<?php
if ($bMinisite) {
?>
   <td align="center" bgcolor="F7F7F7">&nbsp;<?php echo  $oCms_site->get_name() ; ?></td>
<?php
}
?>
   <td align="center" bgcolor="F7F7F7">&nbsp;<?php echo $oContent->getWidth_content();?>x<?php echo $oContent->getHeight_content();?>&nbsp;</td>
   <td align="center" bgcolor="F3F3F3">&nbsp;<?php echo $oContent->getType_content();?>&nbsp;</td>
   <!-- CC Mkl : Modif si brique Graphique => edition avec la page graphicEditor.php -->
   <td align="center" bgcolor="F7F7F7">&nbsp;<?php echo $nb_pages; ?>&nbsp;</td>
  </tr>
<?php
		}
	}
  ?>
  </table>
  </td>
 </tr>
</table>
</form>
<iframe id="previewBrique" onload="putHtmlBriqueToOpener(this)" style="display:none"></iframe>