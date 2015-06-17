<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 
$Author: thao $
$Revision: 1.3 $

$Log: arboPage_browse.php,v $
Revision 1.3  2013-06-26 13:12:33  thao
*** empty log message ***

Revision 1.2  2013-06-20 14:32:18  raphael
*** empty log message ***

Revision 1.1  2013-05-28 13:13:33  thao
*** empty log message ***

Revision 1.28  2013-04-04 15:49:13  thao
*** empty log message ***

Revision 1.27  2013-03-01 10:28:04  pierre
*** empty log message ***

Revision 1.26  2012-07-31 14:24:46  pierre
*** empty log message ***

Revision 1.25  2012-05-15 12:55:32  thao
*** empty log message ***

Revision 1.24  2012-05-14 09:10:09  thao
*** empty log message ***

Revision 1.23  2012-05-07 13:46:13  pierre
*** empty log message ***

Revision 1.22  2012-04-13 07:18:37  pierre
*** empty log message ***

Revision 1.21  2012-04-05 08:42:55  thao
*** empty log message ***

Revision 1.20  2012-04-03 13:45:30  thao
*** empty log message ***

Revision 1.19  2012-04-03 10:34:32  thao
*** empty log message ***

Revision 1.18  2011-12-23 10:42:50  pierre
*** empty log message ***

Revision 1.17  2010-03-08 12:10:27  pierre
syntaxe xhtml

Revision 1.16  2009-09-24 08:53:11  pierre
*** empty log message ***

Revision 1.15  2008-11-28 14:09:55  pierre
*** empty log message ***

Revision 1.14  2008-11-27 11:35:37  pierre
*** empty log message ***

Revision 1.13  2008-11-06 12:03:52  pierre
*** empty log message ***

Revision 1.12  2008-07-18 14:46:56  pierre
*** empty log message ***

Revision 1.11  2008/07/16 10:55:28  pierre
*** empty log message ***

Revision 1.10  2008/07/16 10:48:36  pierre
*** empty log message ***

Revision 1.9  2008/07/16 10:04:38  pierre
*** empty log message ***

Revision 1.8  2008/03/21 15:05:48  pierre
*** empty log message ***

Revision 1.7  2008/03/21 14:46:11  pierre
*** empty log message ***

Revision 1.6  2007/11/26 17:00:43  thao
*** empty log message ***

Revision 1.5  2007/11/16 13:57:28  remy
*** empty log message ***

Revision 1.4  2007/11/15 18:08:49  pierre
*** empty log message ***

Revision 1.7  2007/11/07 16:59:23  remy
*** empty log message ***

Revision 1.6  2007/11/06 15:33:25  remy
*** empty log message ***

Revision 1.3  2007/09/05 15:14:53  thao
*** empty log message ***

Revision 1.2  2007/08/28 15:55:28  pierre
*** empty log message ***

Revision 1.1  2007/08/08 14:26:20  thao
*** empty log message ***

Revision 1.4  2007/08/08 14:16:14  thao
*** empty log message ***

Revision 1.3  2007/08/08 13:56:04  thao
*** empty log message ***

Revision 1.2  2007/08/07 10:24:57  thao
*** empty log message ***

Revision 1.1.1.1  2006/01/25 15:14:32  pierre
projet CCitron AWS 2006 Nouveau Website

Revision 1.12  2005/12/21 08:36:44  sylvie
*** empty log message ***

Revision 1.11  2005/12/20 15:11:50  sylvie
*** empty log message ***

Revision 1.10  2005/12/20 09:12:10  sylvie
application des styles

Revision 1.9  2005/12/20 08:23:25  sylvie
application nouveaux styles titre

Revision 1.8  2005/12/19 15:58:22  sylvie
*** empty log message ***

Revision 1.7  2005/11/04 13:55:09  sylvie
*** empty log message ***

Revision 1.6  2005/11/04 10:07:00  sylvie
*** empty log message ***

Revision 1.5  2005/11/04 08:36:52  sylvie
*** empty log message ***

Revision 1.4  2005/10/28 12:45:54  sylvie
*** empty log message ***

Revision 1.3  2005/10/28 07:53:14  sylvie
*** empty log message ***

Revision 1.2  2005/10/27 09:25:55  sylvie
*** empty log message ***

Revision 1.1.1.1  2005/10/24 13:37:05  pierre
re import fusion espace v2 et ADW v2

Revision 1.3  2005/10/21 10:46:46  sylvie
*** empty log message ***

Revision 1.2  2005/10/21 09:51:31  sylvie
*** empty log message ***

Revision 1.1.1.1  2005/10/20 13:10:53  pierre
Espace V2

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

Revision 1.3  2004/02/05 15:56:25  ddinside
ajout fonctionnalite de suppression de pages
ajout des styles dans spaw
debuggauge prob du nom de fichier limite à 30 caracteres

Revision 1.2  2004/01/27 12:12:50  ddinside
application de dos2unix sur les scripts SQL
modification des scripts d'ajout pour correction bug lors d'une modif de page
ajout foncitonnalité de modification de page
ajout visu d'une page si créée

Revision 1.1  2004/01/20 15:16:38  ddinside
mise à jour de plein de choses
ajout de gabarit vie des quartiers
eclatement gabarits par des includes pour contourner prob des flashs non finalisés

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

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php'); 

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

$isMinisite = false;
if (is_get ("source")) {
	if ($_GET["source"] == "minisite") {
		activateMenu('gestionminisite');  //permet de dérouler le menu contextuellement
		$isMinisite = true;
	}
}
else {
	activateMenu('gestionpage');  //permet de dérouler le menu contextuellement
}

if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

$nameUser = $_SESSION['login'];
$sFonct = $_SESSION['fonct'];
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
<script type="text/javascript">document.title="Arbo - Pages";</script>

<script type="text/javascript">
<!--
	// affinage de la liste des pages
	// bandeau de recherche
	function recherche()
	{
		document.managetree.action = "arboPage_browse.php";
		document.managetree.submit();
	}

	// accès simple aux infos de la page : titre et mot clé
	function page_infos(id, idGab)
	{
		document.managetree.idPage.value = id;
		document.managetree.idGab.value = id;
		document.managetree.action = "page_infos.php?idPage="+id+"&idGab="+idGab;
		document.managetree.submit();
	}

	// accès simple à la modification de la page : maj contenu
	function page_maj(id)
	{
		document.managetree.idPage.value = id;
		document.managetree.action = "page_maj.php";
		document.managetree.submit();
	}

-->
</script>
<form name="managetree" action="arboaddnode.php" method="post">

<input type="hidden" name="urlRetour" value="<?php echo $_POST['urlRetour']; ?>">
<input type="hidden" name="idSite" value="<?php echo $idSite; ?>">
<input type="hidden" name="idPage" value="<?php echo $idPage; ?>">
<input type="hidden" name="idGab" value="<?php echo $idGab; ?>">
<?php
// site de travail
//if (DEF_MENUS_MINISITES == "ON") print(putAfficheSite());
?>	  
<div class="ariane"><span class="arbo2">PAGE&nbsp;>&nbsp;</span><span class="arbo3">Arborescence&nbsp;>&nbsp;
<?php echo strip_tags(getAbsolutePathString($idSite, $db, $virtualPath)); ?></span></div>
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
           <td bgcolor="#FFFFFF">
		   <?php
		    if ($isMinisite) 
				print drawCompTreeMinisite($idSite, $db, $virtualPath,null);
			else
				print drawCompTree($idSite, $db, $virtualPath,null);
			?>
		
		</td>
         </tr>
     </table></td>
  <td colspan="3" class="arbo"><span class="arbo"><img src="/backoffice/cms/img/vide.gif" width="15"></span></td>
  <td align="left" valign="top" class="arbo">
  
  
  <?php /*if ($isMinisite)*/ include_once ("backoffice/cms/site/arbo_picto.php"); ?>
  
  <br />
  <!-- contenu du dossier -->
   <b><br />
   Composants contenus dans le dossier en cours:</b>
   <br />
   <br />
<table  border="0" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
  <tr class="col_titre">
	<td align="center"><strong>&nbsp;&nbsp;</strong></td>
	<td align="center"><strong>&nbsp;&nbsp;Nom&nbsp;</strong></td>
	<td align="center"><strong>&nbsp;&nbsp;Gabarit</strong></td>
	<td align="center"><strong>&nbsp;Créé<br />le</strong></td>
	<td width="5" align="center"><strong>Dernière 
	  modif.</strong></td>
	<td align="center"><strong>Mise en<br />
	  prod.</strong></td>
	<td align="center"><strong>&nbsp;&nbsp;Actions</strong></td>
  </tr>
  <?php
	$current_path =  getNodeInfos($db,$virtualPath);
	$current_path = rawurlencode($current_path['path']);
	$current_path = preg_replace('/\%2F/','/',$current_path);
	$contenus = getFolderPages($idSite, $virtualPath);
	if((!is_array($contenus)) or (sizeof($contenus)==0)) {
?>
  <tr>
    <td align="center" colspan="7">&nbsp;<strong>Aucun élément à afficher</strong>  </tr>
<?php
	} else {
		foreach ($contenus as $k => $page) {
		
			// id de la page
			$oPage = new Cms_page($page['id']);

			// site de la page
			$oSite = new Cms_site($page['id_site']);
			
			// son chemin est site/ et non /
			//	la racin est la même pour tous donc ajouter /site/ pour la racine d'un site
			if ($current_path == "/") {
				$current_path_page = "/".$oSite->get_rep().$current_path;
			} else $current_path_page = $current_path;
			
			// gabarit
			$oGab = new Cms_page($page['gabarit']);
			
?>
 <tr class="<?php echo htmlImpairePaire($k);?>">
   <td align="center">
<?php
// affichage des icones de preview des pages
$idContent = -1;
$sNature = "CMS_CONTENT";
$idPage = $page['id'];
$sNomPage = $page['name'];

$bToutenligne_page = $oPage->getToutenligne_page();
$bExisteligne_page = $oPage->getExisteligne_page();


$sUrlPageLigne = "/content".$current_path_page.$page['name'];

afficheIconePreviewPage($idContent, $sNature, $idPage, $sNomPage, $bToutenligne_page, $bExisteligne_page, $sUrlPageLigne);
?>
   </td>
   <td align="center">
<?php echo $page['name'];?>&nbsp;
   </td>
   <td align="center">&nbsp;<?php echo $oGab->getName_page()?>&nbsp;</td>
   <td align="center">&nbsp;<?php echo $page['creation'];?>&nbsp;</td>
   <td align="center">&nbsp;<?php echo $page['modification'];?>&nbsp;</td>
   <td align="center">&nbsp;<?php echo $page['mep'];?>&nbsp;</td>
   <td align="center" nowrap>
<table cellpadding="0" cellspacing="1">
<tr>
	<td width="20"><a href="javascript:page_infos(<?php echo $page['id'];?>, <?php echo $page['gabarit'];?>)"><img src="/backoffice/cms/img/modifier.gif" border="0" title="Modifier la page"></a></td>
	<?php if ($nameUser == "ccitron") { ?><td width="20"><a href="pageModif.php?id=<?php echo $page['id'];?>&idGab=<?php echo $page['gabarit'];?>"><img src="/backoffice/cms/img/propriete.gif" border="0" title="Propriétés de la page"></a></td> <?php } ?>
	<td width="20"><?php
// sponthus 31/05
// A VOIR
// pour l'instant on ne peut déplacer que des pages déjà créées 
// le programme essaie de déplacer physiquement un fichier qui peut ne pas exister 
// si la page n'a pas de version  en ligne
// A VOIR
if ($bExisteligne_page == 1) {
?><a href="movePage.php?id=<?php echo $page['id'];?>"><img src="/backoffice/cms/img/deplacer.gif" border="0" title="Déplacer la page"></a><?php
}
?></td>
	<td width="20"><a href="renamePage.php?id=<?php echo $page['id'];?>"><img src="/backoffice/cms/img/renommer.gif" border="0" title="Renommer la page"></a></td>
	<td width="20"><a href="/node.php?page=<?php echo $page['id'];?>&amp;" target="_blank" onclick="prompt('Permalien vers cette page:', 'http://<?php echo $_SERVER['HTTP_HOST']; ?>/node.php?page=<?php echo $page['id'];?>&amp;amp;'); return false;" title="Permalien"><img src="/backoffice/cms/img/link.gif" alt="Permalien" border="0" height="14" width="14" /></a></td>
	<td width="20"><a href="#" onClick="if(window.confirm('Etes vous sur(e) de vouloir supprimer cette page ?')){ document.location='/backoffice/cms/site/deletePage.php?id=<?php echo $page['id'];?>';}"><img src="/backoffice/cms/img/supprimer.gif" border="0" title="Supprimer la page"></a></td>
</tr>
</table>
<!--<a href="javascript:page_maj(<?php echo $page['id'];?>)">modifier</a>&nbsp;--></td>
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





