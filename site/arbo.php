<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/* 

$Author: raphael $

$Revision: 1.1 $





$Log: arbo.php,v $
Revision 1.1  2013-09-30 09:43:07  raphael
*** empty log message ***

Revision 1.5  2013-06-26 13:12:33  thao
*** empty log message ***

Revision 1.4  2013-03-01 10:28:20  pierre
*** empty log message ***

Revision 1.3  2012-07-31 14:24:50  pierre
*** empty log message ***

Revision 1.2  2012-04-03 10:34:32  thao
*** empty log message ***

Revision 1.1  2010-08-23 08:29:51  pierre
*** empty log message ***

Revision 1.16  2010-07-16 07:36:45  pierre
lien vers édition maj.php des nodes

Revision 1.15  2010-04-13 15:21:29  thao
*** empty log message ***

Revision 1.14  2010-03-02 15:03:56  thao
*** empty log message ***

Revision 1.13  2010-02-24 14:57:03  thao
*** empty log message ***

Revision 1.12  2009-09-24 08:53:13  pierre
*** empty log message ***

Revision 1.11  2009-07-24 13:45:16  pierre
*** empty log message ***

Revision 1.10  2008-11-27 11:34:35  pierre
*** empty log message ***

Revision 1.9  2008-11-06 12:03:54  pierre
*** empty log message ***

Revision 1.8  2008-07-16 10:55:28  pierre
*** empty log message ***

Revision 1.7  2008/07/16 10:04:38  pierre
*** empty log message ***

Revision 1.6  2007/11/30 13:29:54  thao
*** empty log message ***

Revision 1.5  2007/11/16 13:57:28  remy
*** empty log message ***

Revision 1.4  2007/11/08 08:16:39  remy
*** empty log message ***

Revision 1.3  2007/08/28 15:55:28  pierre
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

Revision 1.2  2005/12/20 08:23:25  sylvie
application nouveaux styles titre

Revision 1.1.1.1  2005/10/24 13:37:05  pierre
re import fusion espace v2 et ADW v2

Revision 1.2  2005/10/21 09:37:25  sylvie
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

// sponthus 17/06/05
// arbo des mini sites
// duplication d'une arbo dans cms_arbo_pages et cms_arbo_composants

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

activateMenu('gestiondusite');  //permet de dérouler le menu contextuellement

// site sur lequel on est positionné
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

// pas de site
if ($idSite == "") die ("Appel incorrect de la fonctionnalité");


$virtualPath = 0;

// probleme lors du retour à l'arborescence init
/*if ($_GET['v_comp_path']=="") { // Si on a pas de dossier par défaut on essaye de charger celui en mémoire
	if($_SESSION['page_v_comp_path']!="") { // On a justement un dossier favoris en mémoire
		$_GET['v_comp_path'] = $_SESSION['page_v_comp_path'];
	}
}*/


if (strlen($_GET['v_comp_path']) > 0)

	$virtualPath = $_GET['v_comp_path'];
	$_SESSION['page_v_comp_path'] = $virtualPath;// On enregistre dans le dossier favoris le dossier courant

?>
<script type="text/javascript">

	// affinage de la liste des utilisateurs
	// bandeau de recherche
	function recherche()
	{
		document.managetree.action = "arbo.php";
		document.managetree.submit();
	}
	

</script>

<form name="managetree" action="arboaddnode.php" method="post">

<input type="hidden" name="urlRetour" id="urlRetour" value="<?php echo $_POST['urlRetour']; ?>" />
<input type="hidden" name="idSite" id="idSite" value="<?php echo $idSite; ?>" />

<?php
// site de travail
//if (DEF_MENUS_MINISITES == "ON") print(putAfficheSite());
  
?><div class="ariane"><span class="arbo2">ARBORESCENCE&nbsp;>&nbsp;</span>
<span class="arbo3">Gestion de l'arborescence&nbsp;>&nbsp;</span>
<span class="arbo4"><?php echo getAbsolutePathString($idSite, $db, $virtualPath); ?></span></div>

  <table cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td colspan="3"><img src="/backoffice/cms/img/vide.gif"><br />
      <br /></td>
    </tr>
    <tr>
      <td width="250" align="left" valign="top" class="arbo"><br />
          <b>&nbsp;&nbsp;Arborescence :</b> <br />
          <br />
          <table cellpadding="8" cellspacing="0" border="0">
            <tr>
              <td bgcolor="#FFFFFF"><?php print drawCompTree($idSite, $db, $virtualPath, null);	?>&nbsp;</td>
            </tr>
      </table></td>

      <td width="15" align="left" valign="top" class="arbo2"><img src="/backoffice/cms/img/vide.gif" width="15"></td>

      <td align="center" valign="top" class="arbo">
        <!-- contenu des actions -->
       <b><br />
      Op&eacute;rations sur le dossier en cours :</b><br />
      <br />
      <table width="300" border="0" cellpadding="5" cellspacing="0" bgcolor="#F0F1F3">       
          <tr bgcolor="#FFFFFF">
            <td align="left" class="arbo"<?php if($virtualPath=="0") echo "title='Impossible de déplacer le dossier Racine' style='color:#b0b0b0'"; ?>>D&eacute;placer le dossier</td>
            <td<?php if($virtualPath=="0") echo ' title="Impossible de déplacer le dossier Racine"><img border="0" src="'.$URL_ROOT.'/backoffice/cms/img/go_off.gif">'; else { ?>><a href="arbo_movefolder.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a><?php } ?></td>
          </tr>
          <tr>
            <td align="left" class="arbo"<?php if($virtualPath=="0") echo "title='Impossible de renommer le dossier Racine' style='color:#b0b0b0'"; ?>>Renommer le dossier</td>
            <td<?php if($virtualPath=="0") echo ' title="Impossible de renommer le dossier Racine"><img border="0" src="'.$URL_ROOT.'/backoffice/cms/img/go_off.gif">'; else { ?>><a href="arbo_renamefolder.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a><?php } ?></td>
          </tr>
          <tr bgcolor="#FFFFFF">
            <td align="left" class="arbo"<?php if($virtualPath=="0") echo "title='Impossible de supprimer le dossier Racine' style='color:#b0b0b0'"; ?>>Supprimer le dossier</td>
            <td<?php if($virtualPath=="0") echo ' title="Impossible de supprimer le dossier Racine"><img border="0" src="'.$URL_ROOT.'/backoffice/cms/img/go_off.gif">'; else { ?>><a href="arbo_deletefolder.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a><?php } ?></td>
          </tr>
		  
		  
          <tr>
            <td align="left" class="arbo">Cr&eacute;er un sous dossier</td>
            <td>
			<a href="arbo_createfolder.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"> </a>			
			</td>
          </tr>
		  
		  
		  
          <tr bgcolor="#FFFFFF">
            <td align="left" class="arbo">Ordre d'affichage des sous dossiers</td>
            <td><a href="arbo_orderfolder.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>
          </tr>
		   <tr>
            <td align="left" class="arbo">Description du dossier</td>
            <td><a href="arbo_description.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>
          </tr>
		  <tr bgcolor="#FFFFFF">
            <td align="left" class="arbo">Tag du dossier</td>
            <td><a href="arbo_tag.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>
          </tr>
		  <?php if ($nameUser == "ccitron") { ?> 
		  <tr>
            <td align="left" class="arbo">Edition avancée</td>
            <td><a href="/backoffice/cms/cms_arbo_pages/maj_cms_arbo_pages.php?id=<?php echo preg_replace('/.*,([0-9]+)/msi', '$1', $virtualPath); ?>"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>
          </tr>
		  <?php } ?>
		   <tr bgcolor="#FFFFFF">
            <td align="left" class="arbo">Lien direct pour accéder au dossier</td>
			<?php 
			$currInfos 	= getNodeInfos($db,$virtualPath); 
			$aPath 		= explode ("/", $currInfos["path"]); 
			$currDir 	= $aPath[(sizeof($aPath)-2)];
			
			$currNode = getNodeInfos($db, $virtualPath);  
			$currNodeId = $currNode["id"];
			$currNodeLibelle = $currNode["libelle"];
			$sql_nb_currDir = "SELECT count(*)
								FROM `cms_arbo_pages`
								WHERE `node_libelle` LIKE  '%".str_replace("'", "\'", $currNodeLibelle)."%' ";
								
			$nb = dbGetUniqueValueFromRequete($sql_nb_currDir); 
			
			 
			
			if ($nb > 1) {
				$currDir.="/".$currNodeId;	
			}
			
			 ?>
            <td><a href="/#<?php echo $currDir;?>" target="_blank" onclick="prompt('Lien direct vers ce dossier:', 'http://<?php echo $_SERVER['HTTP_HOST']; ?>/#<?php echo $currDir;?>'); return false;" title="Lien direct"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>
          </tr>
       
    </table></td>
    </tr>
  </table>
</form>