<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

 
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


activateMenu('gestiondusite');  //permet de d�rouler le menu contextuellement

// site sur lequel on est positionn�
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

// pas de site
if ($idSite == "") die ("Appel incorrect de la fonctionnalit�");


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
      Op&eacute;rations sur le dossier en cours :</b><br />
      <br />
      <table width="300" border="0" cellpadding="5" cellspacing="0" bgcolor="#F0F1F3">       
          <tr bgcolor="#FFFFFF"> 
           
			<?php if($virtualPath != "0") { ?>
			 <td>
			<a id="arbo_move" href="/backoffice/cms/site/arbo_movefolder.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>&action=MOVE<?php echo $param; ?>"><img src="/backoffice/cms/img/deplacer.gif" border="0" title="D�placer le noeud"></a></td>
			<?php } ?>
			
            <?php if($virtualPath != "0") { ?><td><a id="arbo_rename" href="/backoffice/cms/site/arbo_renamefolder.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>&action=RENAME<?php echo $param; ?>"><img src="/backoffice/cms/img/renommer.gif" border="0" title="Renommer le noeud"></a></td><?php } ?>
           
            <?php if($virtualPath != "0")  { ?><td><a id="arbo_delete" href="/backoffice/cms/site/arbo_deletefolder.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>&action=DELETE<?php echo $param; ?>"><img src="/backoffice/cms/img/supprimer.gif" border="0" title="Supprimer le noeud" /></a></td><?php } ?>
          
            <td>
			<?php if($virtualPath == "0" && $isMinisite)  { ?>
			<a id="arbo_create"  href="/backoffice/cms/cms_minisite/maj_cms_minisite.php?menuOpen=true<?php echo $param; ?>"">
			<?php } else {?>
			<a id="arbo_create"  href="/backoffice/cms/site/arbo_createfolder.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>&action=CREATE<?php echo $param; ?>">
			<?php } ?>
			<img src="/backoffice/cms/img/add.gif" border="0" title="Cr�er un nouveau dossier"></a></td>
			
          	<td><a id="arbo_order"  href="/backoffice/cms/site/arbo_orderfolder.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>&action=ORDER<?php echo $param; ?>"><img src="/backoffice/cms/img/ordonner.gif" border="0" title="Ordre"></a></td>
		  
            <?php 
			
			if ($isMinisite) echo "minisite"; 
			if(sizeof(explode (",", $virtualPath)) == 2 && $isMinisite) { ?>
			<td><a id="arbo_create"  href="/backoffice/cms/site/arbo_duplicatefolder.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>&action=DUPLICATE<?php echo $param; ?>"><img src="/backoffice/cms/img/dupliquer.gif" border="0" title="Dupliquer"></a></td> 
			<?php } ?>
          
            <td><a id="arbo_description"  href="/backoffice/cms/site/arbo_description.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>&action=DESCRIPTION<?php echo $param; ?>"><img src="/backoffice/cms/img/propriete.gif" border="0" title="Description du dossier"></a></td>
          
		  	<?php if (preg_match ("/TAG/", $sFonct)) {  ?>
            <td><a id="arbo_tag"  href="/backoffice/cms/site/arbo_tag.php?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>&action=TAG<?php echo $param; ?>"><img border="0" src="/backoffice/cms/img/modifier-xml.gif" title="Tag du dossier"></a></td>
			<?php } ?>
          
            <?php if ($nameUser == "ccitron") { ?> <td><a id="arbo_arbo_pages"  href="/backoffice/cms/cms_arbo_pages/maj_cms_arbo_pages.php?id=<?php echo ereg_replace('.*,([0-9]+)', '\\1', $virtualPath); ?>&action=MAJ<?php echo $param; ?>"><img src="/backoffice/cms/img/modifier.gif" border="0" title="Edition avanc�e"></a></td><?php } ?>
         
			<?php 
			$currInfos 	= getNodeInfos($db,$virtualPath); 
			$aPath 		= split ("/", $currInfos["path"]); 
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
            <td><a href="/#<?php echo $currDir;?>" target="_blank" onclick="prompt('Lien direct vers ce dossier:', 'http://<?php echo $_SERVER['HTTP_HOST']; ?>/#<?php echo $currDir;?>'); return false;" title="Lien direct"><img src="/backoffice/cms/img/link.gif" alt="Lien direct vers ce dossier" border="0"  /></a></td>
          </tr>
       
    </table><!--</td>
    </tr>
  </table>
</form>-->