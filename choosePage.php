<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

// sopnthus 17/06/05
// ajout id_site

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

// le site de travail
$idSite = $_SESSION['idSite_travail'];

if ($_GET['v_comp_path']=="") { // Si on a pas de dossier par défaut on essaye de charger celui en mémoire
	if($_SESSION['brique_v_comp_path']!="") { // On a justement un dossier favoris en mémoire
		$_GET['v_comp_path'] = $_SESSION['brique_v_comp_path'];
	}
}
$virtualPath=$_GET['v_comp_path'];
	$_SESSION['brique_v_comp_path'] = $virtualPath;// On enregistre dans le dossier favoris le dossier courant
if(strlen($virtualPath)==0)
	$virtualPath=0;
$nodeInfos=getNodeInfos($db, $virtualPath);
?><style type="text/css">
<!--
body {
	margin-left: 3px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style><link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">

<form>
<input type="hidden" name="idPage" value="">
<input type="hidden" name="namePage" value="">
<style type="text/css">
body {
	background-color:#efefef;
};
</style>
<script type="text/javascript">

// sélection d'une page
// enregistrement de l'id et du nom de la page
function selectIdPage(id)
{
	document.forms[0].idPage.value = id;
}
function selectNamePage(sPage)
{
	document.forms[0].namePage.value=sPage;
}

function execChoose() {

	idPageSelected = document.forms[0].idPage.value;
	namePageSelected = document.forms[0].namePage.value;

//	alert("page=>" + idPageSelected);
//	alert("page=>" + namePageSelected);
	
	if (idPageSelected == "") {
		alert("Aucune page sélectionnée");
	} else {
		window.opener.document.forms[0].page_id.value = idPageSelected;
		window.opener.document.forms[0].pagename.disabled = false;
		window.opener.document.forms[0].pagename.value = namePageSelected;
		window.opener.document.forms[0].pagename.disabled = true;
	
		self.close();
	}
}
</script>

<br /><div class="arbo2">&nbsp;<strong>Sélection d'une page</strong></div><br />

<table cellpadding="2" cellspacing="0" border="0">
 <tr>
  <td colspan="5"><table  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td class="arbo">&nbsp;</td>
      <td valign="middle" class="arbo" style="vertical-align:middle">
	   &gt; <small><?php echo getAbsolutePathString($idSite, $db, $virtualPath); ?></small></td>
    </tr>
  </table>    </td>
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
print drawCompTree($idSite, $db, $virtualPath,null);
?></td>
         </tr>
     </table></td>
  <td colspan="3" class="arbo2"><span class="arbo2"><img src="/backoffice/cms/img/vide.gif" width="15"></span></td>
  <td align="left" valign="top" class="arbo2">
  <!-- contenu du dossier -->
   <u><b><br />
   Liste des pages dans le dossier en cours:</b></u>
   <br />
   <br />
  <table  border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
  <tr>
	<td align="center" bgcolor="EEEEEE">&nbsp;</td>
	<td align="center" bgcolor="E6E6E6">&nbsp;</td>
	<td align="center" bgcolor="EEEEEE"><strong>&nbsp;&nbsp;Nom&nbsp;</strong></td>
	<td width="5" align="center" bgcolor="E6E6E6"><strong>&nbsp;&nbsp;Gabarit</strong></td>
	<td align="center" bgcolor="EEEEEE"><strong>&nbsp;Créé<br />
	  le</strong></td>
	<td width="5" align="center" bgcolor="E6E6E6"><strong>Dernière 
	  modif.</strong></td>
  </tr>
  <?php
	$current_path =  getNodeInfos($db,$virtualPath);
	$current_path = rawurlencode($current_path['path']);
	$current_path = preg_replace('/\%2F/','/',$current_path);
	$contenus = getFolderPages($idSite, $virtualPath);
	if((!is_array($contenus)) or (newSizeOf($contenus)==0)) {
?>
  <tr>
    <td align="center" colspan="10">&nbsp;<strong>Aucun élément à afficher</strong>  </tr>
<?php
	} else {
		foreach ($contenus as $k => $page) {
		
			// id de la page
			$oPage = new Cms_page($page['id']);

			// site de la page
			$oSite = new Cms_site($page['id_site']);
			
			// si la page est à la racine d'un mini site
			// son chemin est minisite/ et non /
			if ($oPage->getNodeid_page() == 0) {
				$current_path_page = "/".$oSite->get_rep().$current_path."/";
			} else {
				$current_path_page = $current_path;			
			}
			
			// gabarit
			$oGab = new Cms_page($page['gabarit']);
			
?>
  <tr>
	<td align="left" bgcolor="F7F7F7"><input type="radio" name="brChoosePage" value="<?php echo $page['id']; ?>" style="width:13px" onClick="javascript:selectIdPage(<?php echo $page['id']; ?>);selectNamePage('<?php echo $page['name']; ?>');"></td>
   <td align="center" bgcolor="F3F3F3">
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
   <td align="center" bgcolor="F7F7F7">&nbsp;<?php echo $sNomPage; ?>&nbsp;</td>
   <td align="center" bgcolor="F3F3F3">&nbsp;<?php echo $oGab->getName_page()?>&nbsp;</td>
   <td align="center" bgcolor="F7F7F7">&nbsp;<?php echo $page['creation'];?>&nbsp;</td>
   <td align="center" bgcolor="F3F3F3">&nbsp;<?php echo $page['modification'];?>&nbsp;</td>
  </tr>
<?php
		}
	}
?>
  </table>
  </td>
 </tr>
</table>




<hr width="100%" class="arbo2">
<div align="center"><input name="btChoosePage" type="button" class="arbo" onClick="javascript:execChoose();" value="Choisir cette page"> 
</div>
</form>
