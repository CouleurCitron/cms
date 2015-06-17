<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 
$Author: pierre $
$Revision: 1.4 $

$Log: arbo_browse.php,v $
Revision 1.4  2013-03-01 10:28:05  pierre
*** empty log message ***

Revision 1.3  2013-02-11 10:27:06  thao
*** empty log message ***

Revision 1.2  2013-02-08 10:42:32  pierre
*** empty log message ***

Revision 1.1  2012-12-12 14:26:36  pierre
*** empty log message ***

Revision 1.5  2012-07-31 14:24:46  pierre
*** empty log message ***

Revision 1.4  2012-04-13 07:33:11  pierre
*** empty log message ***

Revision 1.3  2012-04-13 07:18:37  pierre
*** empty log message ***

Revision 1.2  2011-07-05 07:55:25  pierre
*** empty log message ***

Revision 1.1  2010-11-02 10:38:14  pierre
*** empty log message ***

Revision 1.18  2010-08-09 10:24:30  thao
*** empty log message ***

Revision 1.17  2010-03-08 12:10:27  pierre
syntaxe xhtml

Revision 1.16  2009-09-24 08:53:11  pierre
*** empty log message ***

Revision 1.15  2009-06-26 08:13:42  thao
*** empty log message ***

Revision 1.14  2008-11-28 15:14:19  pierre
*** empty log message ***

Revision 1.13  2008-11-28 14:09:55  pierre
*** empty log message ***

Revision 1.12  2008-11-27 11:35:37  pierre
*** empty log message ***

Revision 1.11  2008-11-20 14:44:10  pierre
*** empty log message ***

Revision 1.10  2008-11-06 12:03:52  pierre
*** empty log message ***

Revision 1.9  2008-08-20 14:42:12  pierre
*** empty log message ***

Revision 1.8  2008-07-16 10:55:28  pierre
*** empty log message ***

Revision 1.7  2008/07/16 10:04:38  pierre
*** empty log message ***

Revision 1.6  2008/04/30 15:16:45  thao
*** empty log message ***

Revision 1.5  2007/11/16 14:47:53  thao
*** empty log message ***

Revision 1.4  2007/11/16 13:57:28  remy
*** empty log message ***

Revision 1.3  2007/11/15 18:08:49  pierre
*** empty log message ***

Revision 1.7  2007/11/08 16:37:01  remy
*** empty log message ***

Revision 1.6  2007/11/06 15:33:25  remy
*** empty log message ***

Revision 1.2  2007/09/05 12:48:03  pierre
*** empty log message ***

Revision 1.1  2007/08/08 14:26:20  thao
*** empty log message ***

Revision 1.4  2007/08/08 14:16:14  thao
*** empty log message ***

Revision 1.3  2007/08/08 13:56:04  thao
*** empty log message ***

Revision 1.2  2006/12/13 16:01:12  pierre
*** empty log message ***

Revision 1.1.1.1  2006/01/25 15:14:32  pierre
projet CCitron AWS 2006 Nouveau Website

Revision 1.10  2005/12/21 08:36:44  sylvie
*** empty log message ***

Revision 1.9  2005/12/21 07:39:08  sylvie
*** empty log message ***

Revision 1.8  2005/12/20 09:30:43  sylvie
application des styles de titre nouveau design

Revision 1.7  2005/12/19 15:58:22  sylvie
*** empty log message ***

Revision 1.6  2005/11/02 16:44:21  sylvie
*** empty log message ***

Revision 1.5  2005/11/02 13:44:26  sylvie
*** empty log message ***

Revision 1.4  2005/11/02 13:28:29  sylvie
*** empty log message ***

Revision 1.3  2005/11/02 09:02:48  sylvie
*** empty log message ***

Revision 1.2  2005/10/27 07:24:29  sylvie
renommage de brique

Revision 1.1.1.1  2005/10/24 13:37:05  pierre
re import fusion espace v2 et ADW v2

Revision 1.4  2005/10/21 10:46:46  sylvie
*** empty log message ***

Revision 1.3  2005/10/21 09:51:31  sylvie
*** empty log message ***

Revision 1.2  2005/10/21 08:44:21  sylvie
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
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

activateMenu('gestionbrique');  //permet de dérouler le menu contextuellement

// site connecté
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

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
<script type="text/javascript">document.title="Arbo - Briques";</script>
<script src="/backoffice/cms/js/preview.js" type="text/javascript"></script>
<script type="text/javascript">
<!--

	// renommage des briques
	function renommer(id)
	{
		document.managetree.id.value = id;
		document.managetree.action = "brique_renomme.php";
		document.managetree.submit();
	}

	// dimensions des briques
	function dimensions(id)
	{
		document.managetree.id.value = id;
		document.managetree.action = "brique_dimensions.php";
		document.managetree.submit();
	}

	// modification du contenu seul d'une brique
	function modifier(id)
	{
		document.managetree.id.value = id;
		document.managetree.action = "brique_maj.php";
		document.managetree.submit();
	}

	// affinage de la liste des briques
	// bandeau de recherche
	function recherche()
	{
		document.managetree.action = "arbo_browse.php";
		document.managetree.submit();
	}

-->
</script>
<form name="managetree" id="managetree" action="arboaddnode.php" method="post">
<input type="hidden" name="urlRetour" id="urlRetour" value="<?php echo $_POST['urlRetour']; ?>" />
<input type="hidden" name="idSite" id="idSite" value="<?php echo $idSite; ?>" />
<input type="hidden" name="id" id="id" value="" />

<div class="ariane"><span class="arbo2">BRIQUE >&nbsp;</span><span class="arbo3">Arborescence&nbsp;>&nbsp;
<?php echo getAbsolutePathString($idSite, $db, $virtualPath); ?></span></div>

  <table cellpadding="0" cellspacing="0" border="0">
 <tr>
  <td colspan="5"><img src="/backoffice/cms/img/vide.gif"></td>
 </tr>
 <tr>
   <td align="left" valign="top" class="arbo"><br />
       <b>Arborescence :</b> <br />
       <br />
       <table cellpadding="8" cellspacing="0" border="0">
         <tr>
           <td bgcolor="#FFFFFF"><?php
print drawCompTree($idSite, $db, $virtualPath, null);
?></td>
         </tr>
     </table></td>
  <td colspan="3" class="arbo2"><span class="arbo2"><img src="/backoffice/cms/img/vide.gif" width="15"></span></td>
  <td align="left" valign="top" class="arbo">
  <!-- contenu du dossier -->
   <b><small><br />
   Composants contenus dans le dossier en cours:</small></b>
   <br />
   <br />
  <table border="0" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
  <tr class="col_titre">
	<td align="center"><strong>Nom</strong></td>
	<td align="center"><strong>Dimensions</strong></td>
	<td align="center"><strong>Type</strong></td>
	<td align="center"><strong>Pages concernées</strong></td>
	<td align="center"><strong>Actions</strong></td>
  </tr>
  <?php
	$contenus = getFolderComposants($idSite, $virtualPath);
	if(!is_array($contenus)) {
?>
  <tr>
    <td align="center" colspan="6"><strong>&nbsp;Aucun élément à afficher</strong>
  </tr>
<?php
	} else {
		foreach ($contenus as $k => $composant) {
			$nb_pages = sizeof(getPageUsingComposant($idSite, $composant['id']));
			
			// page de modif de la brique
			if ($composant['type'] == "Graphique"){
				$sPageEdit = "graphic";
			}
			else if ($composant['type'] == "database") {
				$sPageEdit = "database";
				$sParam = "step=init&";
			}
			else if ($composant['type'] == "formulaire") {
				$sPageEdit = "form";
				$sParam = "step=init&";
			}
			else if ($composant['type'] == "formulaireHTML") {
				$sPageEdit = "/backoffice/cms/formulaire/formulaire";
				$sParam = "step=init&";
			}
			else if ($composant['type'] == "Sondage") {
				$sPageEdit = "backoffice/cms/survey/survey";
				$sParam = "step=init&";
			}
			else  $sPageEdit = "content";

			// site de la brique
			$oCms_site = new Cms_site($composant['id_site']);
	if ( ($_SESSION['login'] == "ccitron") || ($composant['type'] != "PHP" && $_SESSION['login'] != "ccitron")) {
?>
 <tr class="<?php echo htmlImpairePaire($k);?>">
   <td align="center">&nbsp;<a href="#" onClick="javascript:preview_content(<?php echo $composant['id'];?>,<?php echo $composant['width'];?>,<?php echo $composant['height'];?>)"><?php echo $composant['name'];?></a>&nbsp;</td>
   <td align="center">&nbsp;<?php echo $composant['width'];?>x<?php echo $composant['height'];?>&nbsp;</td>
   <td align="center">&nbsp;<?php echo $composant['type'];?>&nbsp;</td>
   <!-- CC Mkl : Modif si brique Graphique => edition avec la page graphicEditor.php -->
   <td align="center">&nbsp;<?php echo $nb_pages; ?>&nbsp;</td>
   <td align="center">&nbsp;
   <?php 
    
   if (ereg("swf|SWF",$composant['type'])){ ?><a href="flashEditor.php?<?php echo $sParam; ?>id=<?php echo $composant['id'];?>&idSite=<?php echo $idSite; ?>&minisite=<?php echo $_GET['minisite']; ?>"><img src="/backoffice/cms/img/modifier.gif" border="0" title="Modifier la brique"></a><?php } 
   elseif (ereg("video|VIDEO",$composant['type'])){ ?><a href="videoEditor.php?<?php echo $sParam; ?>id=<?php echo $composant['id'];?>&idSite=<?php echo $idSite; ?>&minisite=<?php echo $_GET['minisite']; ?>"><img src="/backoffice/cms/img/modifier.gif" border="0" title="Modifier la brique"></a><?php } 
   elseif ($composant['type'] == "Graphique" &&  (is_file($_SERVER['DOCUMENT_ROOT'].'/backoffice/cms_custom/'.$sPageEdit.'Editor.php'))){ ?><a href="/backoffice/cms_custom/<?php echo $sPageEdit; ?>Editor.php?<?php echo $sParam; ?>id=<?php echo $composant['id'];?>&idSite=<?php echo $idSite; ?>&minisite=<?php echo $_GET['minisite']; ?>"><img src="/backoffice/cms/img/modifier.gif" border="0" title="Modifier la brique"></a><?php } 
   elseif ($composant['type'] != "PHP"){ ?><a href="<?php echo $sPageEdit; ?>Editor.php?<?php echo $sParam; ?>id=<?php echo $composant['id'];?>&idSite=<?php echo $idSite; ?>&minisite=<?php echo $_GET['minisite']; ?>"><img src="/backoffice/cms/img/modifier.gif" border="0" title="Modifier la brique"></a><?php } ?>
&nbsp;<a href="javascript:renommer(<?php echo $composant['id'];?>)"><img src="/backoffice/cms/img/renommer.gif" border="0" title="Renommer la brique"></a>
&nbsp;<a href="javascript:dimensions(<?php echo $composant['id'];?>)"><img src="/backoffice/cms/img/propriete.gif" border="0" title="Dimensions de la brique"></a>
&nbsp;<a href="moveComposant.php?id=<?php echo $composant['id'];?>"><img src="/backoffice/cms/img/deplacer.gif" border="0" title="Déplacer la brique"></a>
<?php if ($composant['type'] != "formulaireHTML"){ ?> &nbsp;<a href="duplicate<?php echo ($composant['type']=="Graphique")?"graphic":""; ?>Composant.php?id=<?php echo $composant['id'];?>"><img src="/backoffice/cms/img/dupliquer.gif" border="0" title="Dupliquer la brique"></a><?php } ?>
&nbsp;<a href="#" onClick="if(window.confirm('Etes vous sur(e) de vouloir supprimer cette brique ?')){ document.location='deleteComposant.php?id=<?php echo $composant['id'];?>';}"><img src="/backoffice/cms/img/supprimer.gif" border="0" title="Supprimer la brique"></a>
</td>
  </tr>
<?php
	}
		}
	}
  ?>
  </table>
  </td>
 </tr>
</table>
</form>