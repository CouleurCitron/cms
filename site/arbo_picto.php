<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

 
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


activateMenu('gestiondusite');  //permet de dérouler le menu contextuellement

// site sur lequel on est positionné
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

// pas de site
if ($idSite == "") die ("Appel incorrect de la fonctionnalité");


$virtualPath = 0;
 

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
	
 	jQuery(document).ready(function() {
		$("a#arbo_move,a#arbo_rename,a#arbo_delete,a#arbo_create,a#arbo_order,a#arbo_description,a#arbo_tag").fancybox({
			'width'				: 660,
			'height'			: '75%',
			'showCloseButton'   : true,
	        'autoScale'     	: false,
	        'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'type'				: 'iframe',
			'onClosed': function() {
			   parent.location.reload(true);
			 } 
		});
	}); 
	
	
 

</script>
<!--
<form name="managetree" action="arboaddnode.php" method="post">

<input type="hidden" name="urlRetour" id="urlRetour" value="<?php echo $_POST['urlRetour']; ?>" />
<input type="hidden" name="idSite" id="idSite" value="<?php echo $idSite; ?>" />-->

<?php
// site de travail
//if (DEF_MENUS_MINISITES == "ON") print(putAfficheSite());

if (is_get("source")) {
	$param = "&source=".$_GET["source"];
}

if (is_get("action")) {
	$action = $_GET["action"];
	 
} 





?> 

<!--  </td>
 
     
      <td align="center" valign="top" class="arbo">
        <!-- contenu des actions -->
       <b><br />
      <?php $translator->echoTransByCode('Operations_dossier_courant'); ?></b><br />
      <br />
      <table width="300" border="0" cellpadding="5" cellspacing="0" bgcolor="#F0F1F3">       
          <tr bgcolor="#FFFFFF"> 
           
			<?php if($virtualPath != "0") { ?>
			 <td>
			<a id="arbo_move" href="/backoffice/cms/site/arbo_movefolder.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>&action=MOVE<?php echo $param; ?>"><img src="/backoffice/cms/img/2013/icone/deplacer.png" border="0" title="<?php $translator->echoTransByCode('Deplacer_le_noeud'); ?>"></a></td>
			<?php } ?>
			
            <?php if($virtualPath != "0") { ?><td><a id="arbo_rename" href="/backoffice/cms/site/arbo_renamefolder.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>&action=RENAME<?php echo $param; ?>"><img src="/backoffice/cms/img/2013/icone/renommer.png" border="0" title="<?php $translator->echoTransByCode('Renommer_le_noeud'); ?>"></a></td><?php } ?>
           
            <?php if($virtualPath != "0")  { ?><td><a id="arbo_delete" href="/backoffice/cms/site/arbo_deletefolder.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>&action=DELETE<?php echo $param; ?>"><img src="/backoffice/cms/img/2013/icone/supprimer.png" border="0" title="<?php $translator->echoTransByCode('Supprimer_le_noeud'); ?>" /></a></td><?php } ?>
          
            <td>
			<?php if($virtualPath == "0" && $isMinisite)  { ?>
			<a id="arbo_create"  href="/backoffice/cms/cms_minisite/maj_cms_minisite.php?menuOpen=true<?php echo $param; ?>"">
			<?php } else {?>
			<a id="arbo_create"  href="/backoffice/cms/site/arbo_createfolder.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>&action=CREATE<?php echo $param; ?>">
			<?php } ?>
			<img src="/backoffice/cms/img/2013/icone/add.png" border="0" title="<?php $translator->echoTransByCode('Creer_nouveau_dossier'); ?>"></a></td>
			
          	<td><a id="arbo_order"  href="/backoffice/cms/site/arbo_orderfolder.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>&action=ORDER<?php echo $param; ?>"><img src="/backoffice/cms/img/2013/icone/ordonner.png" border="0" title="<?php $translator->echoTransByCode('Ordre'); ?>"></a></td>
		  
            <?php 
			
			if ($isMinisite) echo "minisite"; 
			if(newSizeOf(explode (",", $virtualPath)) == 2 && $isMinisite) { ?>
			<td><a id="arbo_create"  href="/backoffice/cms/site/arbo_duplicatefolder.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>&action=DUPLICATE<?php echo $param; ?>"><img src="/backoffice/cms/img/2013/icone/dupliquer.png" border="0" title="<?php $translator->echoTransByCode('Dupliquer '); ?>"></a></td> 
			<?php } ?>
          
            <td><a id="arbo_description"  href="/backoffice/cms/site/arbo_description.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>&action=DESCRIPTION<?php echo $param; ?>"><img src="/backoffice/cms/img/2013/icone/propriete.png" border="0" title="<?php $translator->echoTransByCode('Description_du_dossier'); ?>"></a></td>
          
		  	<?php if (preg_match ("/TAG/", $sFonct)) {  ?>
            <td><a id="arbo_tag"  href="/backoffice/cms/site/arbo_tag.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>&action=TAG<?php echo $param; ?>"><img border="0" src="/backoffice/cms/img/2013/icone/modifier-xml.png" title="<?php $translator->echoTransByCode('Tag_du_dossier'); ?>"></a></td>
			<?php } ?>
          
            <?php if ($nameUser == "ccitron") { ?> <td><a id="arbo_arbo_pages"  href="/backoffice/cms/cms_arbo_pages/maj_cms_arbo_pages.php?id=<?php echo preg_replace('/.*,([0-9]+)/msi', '$1', $virtualPath); ?>&action=MAJ<?php echo $param; ?>"><img src="/backoffice/cms/img/2013/icone/modifier.png" border="0" title="<?php $translator->echoTransByCode('Edition_avancee'); ?>"></a></td><?php } ?>
         
			<?php 
			$currInfos 	= getNodeInfos($db,$virtualPath); 
			$aPath 		= explode ("/", $currInfos["path"]); 
			$currDir 	= $aPath[(newSizeOf($aPath)-2)];
			
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
            <td><a href="/#<?php echo $currDir;?>" target="_blank" onclick="prompt('<?php $translator->echoTransByCode('Lien_direct_vers_ce_dossier'); ?>', 'http://<?php echo $_SERVER['HTTP_HOST']; ?>/#<?php echo $currDir;?>'); return false;" title="<?php $translator->echoTransByCode('Lien_direct'); ?>"><img src="/backoffice/cms/img/2013/icone/link.png" alt="<?php $translator->echoTransByCode('Lien_direct_vers_ce_dossier'); ?>" border="0"  /></a></td>
          </tr>
       
    </table><!--</td>
    </tr>
  </table>
</form>-->