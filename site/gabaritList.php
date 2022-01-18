<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 
sponthus 07/06/2005

gestion des gabarits comme une gestion de pages 
MAIS sans arborescence ET AVEC isgabarit_page=1
*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/gabarit.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

activateMenu('gestiongabarit');  //permet de dérouler le menu contextuellement

if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

// affichage de la colonne site si l'on n'est pas sur un mni site
 
if (DEF_MENUS_MINISITES == "ON") $bAffichSite = 1;
else $bAffichSite = 0;

?><div class="ariane"><span class="arbo2">GABARIT&nbsp;>&nbsp;</span>
<span class="arbo3">Liste des gabarits</span></div><?php

$virtualPath = 0;
if (strlen($_GET['v_comp_path']) > 0)
	$virtualPath = $_GET['v_comp_path'];


// liste des gabarits
$contenus = getListGabarits($_SESSION['idSite_travail']);

?>
<script type="text/javascript">document.title="Gabarits";</script>
<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<script src="/backoffice/cms/js/preview.js" type="text/javascript"></script>
<script type="text/javascript">
<!--
	// affinage de la liste des utilisateurs
	// bandeau de recherche
	function recherche()
	{
		document.managetree.action = "gabaritList.php";
		document.managetree.submit();
	}
-->
</script>
<form name="managetree" action="arboaddnode.php" method="post">

<input type="hidden" name="urlRetour" value="<?php echo $_POST['urlRetour']; ?>">
<input type="hidden" name="idSite" value="<?php echo $idSite; ?>">

  <table cellpadding="5" cellspacing="0" border="0" bordercolor="#FFFFFF" class="arbo">
	<tr class="col_titre">
		<td align="center"><strong>&nbsp;&nbsp;<?php $translator->echoTransByCode('Composants_nom'); ?>&nbsp;</strong></td>
<?php
if ($bAffichSite) {
?>
	  <td align="center"><strong>&nbsp;&nbsp;Site&nbsp;</strong></td>
<?php
}
?>
	  <td align="center"><strong>&nbsp;<?php $translator->echoTransByCode('Composants_creation'); ?></strong></td>
	<td align="center"><strong><?php $translator->echoTransByCode('Composants_derniere_modif'); ?></strong></td>
	<td colspan="5" align="center"><strong>&nbsp;&nbsp;<?php $translator->echoTransByCode('Composants_actions'); ?></strong><strong>&nbsp;</strong></td>
	</tr>
  <?php
	// tableau vide
	if((!is_array($contenus)) or (newSizeOf($contenus)==0)) {
?>
  <tr>
    <td align="center" colspan="9">&nbsp;<strong>Aucun élément à afficher</strong>  </tr>
<?php
	} else {
		// pour chaque gabarit
		foreach ($contenus as $k => $page) {

			if ($page['isgabarit']) $sRepFile = "/".DEF_GABARIT_ROOT."/";
			else $sRepFile = "/".DEF_PAGE_ROOT."/";

			// site du gabarit
			$oCms_site = new Cms_site($page['id_site']);
?>
 <tr class="<?php echo htmlImpairePaire($k);?>">
   <td align="left">&nbsp;<a href="javascript:preview_gabarit(<?php echo  $page['id'] ; ?>)"><?php echo $page['name'];?></a>&nbsp;</td>
<?php
if ($bAffichSite) {
?>
   <td align="center">&nbsp;<?php echo $oCms_site->get_name(); ?></td>
<?php
}
?>
   <td align="center">&nbsp;<?php echo $page['creation'];?>&nbsp;</td>
   <td align="center">&nbsp;<?php echo $page['modification'];?>&nbsp;</td>
   <td align="center">&nbsp;<a href="gabaritModif.php?id=<?php echo $page['id'];?>"><img src="/backoffice/cms/img/2013/icone/modifier.png" border="0" title="Modifier le gabarit"></a>&nbsp;</td>
   <td align="center">&nbsp;<a href="renamePage.php?id=<?php echo $page['id'];?>"><img src="/backoffice/cms/img/2013/icone/propriete.png" border="0" title="Propriétés du gabarit"></a>&nbsp;</td>
   <td align="center">&nbsp;<a href="/backoffice/cms/site/page_infos.php?idGab=1&idPage=<?php echo $page['id'];?>"><img src="/backoffice/cms/img/2013/icone/modifier-xml.png" border="0" title="Meta-données du gabarit"></a>&nbsp;</td>   
   <td align="center">&nbsp;<a href="gabaritCopy.php?id=<?php echo $page['id'];?>"><img src="/backoffice/cms/img/2013/icone/dupliquer.png" border="0" title="Dupliquer le gabarit"></a>&nbsp;</td>   
   <td align="center">&nbsp;<a href="#" onClick="if(window.confirm('Etes vous sur(e) de vouloir supprimer ce gabarit ?')){ document.location='deletePage.php?id=<?php echo $page['id'];?>';}"><img src="/backoffice/cms/img/2013/icone/supprimer.png" border="0" title="Supprimer le gabarit"></a>&nbsp;</td>
   </tr>
<?php
		}
	}
  ?>
</table>
<?php
// si une page retour est spécifiée -> lien retour
if ($_POST['urlRetour'] != "") {
?>
<br><div align="center"><a href="<?php echo $_POST['urlRetour']; ?>">retour</a></div>
<?php
}
?>
</form>





