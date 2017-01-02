<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 
sponthus 28/06/2005
gestion des contenus
pour les rédacteurs -> vision des contenus à modifier

*/


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/gabarit.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newcontent_write.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newgabarit_write.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newpage_write.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
 include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

activateMenu('gestioncontenu');  //permet de dérouler le menu contextuellement

if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

// mise en session
include("listeSessionContenus.php");

// booléen pour plier / déplier les critères de recherche
//$bChercherOpen = $rech_bChercherOpen;
$bChercherOpen = 1;

// statut des briques demandé
// pour afficher la liste des contenus en ligne, la liste des contenus en attente, ...
if ($_GET['idStatut'] != "") $_SESSION['idStatut'] = $_GET['idStatut'];
else $_SESSION['idStatut'] = "";

// recherche de la liste des contenus
include("listeRechContenus.php"); 

?>

<div class="ariane"><span class="arbo2">CONTENU >&nbsp;</span><span class="arbo3">Liste des contenus</span></div>

<script type="text/javascript">document.title="Gestion du contenu";</script>
<script src="/backoffice/cms/js/preview.js" type="text/javascript" language="javascript"></script>
<script src="listeContenus.js.php" type="text/javascript" language="javascript"></script>
<form name="contenuListForm" id="contenuListForm" method="post">
<input type="hidden" name="id" id="id" value="<?php echo $_POST['id']; ?>">
<input type="hidden" name="operation" id="operation" value="<?php echo $_POST['operation']; ?>">
<input type="hidden" name="node_id" id="node_id" value="<?php echo $rech_node_id; ?>">
<input type="hidden" name="page_id" id="page_id" value="<?php echo $rech_page_id; ?>">
<input type="hidden" name="bChercherOpen" id="bChercherOpen" value="<?php echo $rech_bChercherOpen; ?>">

<?php
// site de travail
//if (DEF_MENUS_MINISITES == "ON") print(putAfficheSite());
?>	  
<div id="filters" class="arbo">
    <div class='search_gauche new_search'>
                        <div class="spacer">&nbsp;</div>
                            <h2 class="titleSearch">Liste des contenus modifiables <?php
if ($_SESSION['idStatut'] != "") {
?>"<?php echo getLibelleDefineWithCode($_SESSION['idStatut']); ?>"<?php
} ?></h2>
    </div>
<table border="0" cellpadding="5" cellspacing="0">
	<tr>
		<td>
<?php
if ($bChercherOpen == "1") {

if ($sRank != DEF_REDACT) {
?>
	<select name="selectUser" id="selectUser" class="arbo">
			<option value="-1">tous les utilisateurs</option><?php
// les utilisateurs + les amdin
for ($k=0; $k<sizeof($aUser); $k++)
{
	$oUser = $aUser[$k];

	// valeur sélectionnée
	if ($_POST['selectUser'] == $oUser->getUser_id()) $user_selected = "selected";
	else $user_selected = "";

?><option value="<?php echo $oUser->getUser_id(); ?>" <?php echo $user_selected; ?> >
(<?php echo getLibelleDefineWithCode($oUser->getUser_rank()); ?>)
&nbsp;<?php echo $oUser->getUser_prenom(); ?>&nbsp;<?php echo $oUser->getUser_nom(); ?>&nbsp;
</option><?php
}
?></select>

</td></tr><tr><td>

<select name="selectGabarit" id="selectGabarit" class="arbo">
			<option value="">tous les gabarits</option><?php
// gabarits
for ($k=0; $k<sizeof($aGabarit); $k++)
{
	$oGab = new Cms_page($aGabarit[$k]['id']);

	// valeur sélectionnée
	if ($rech_selectGabarit == $oGab->getId_page()) $gab_selected = "selected";
	else $gab_selected = "";

?><option value="<?php echo $oGab->getId_page(); ?>" <?php echo $gab_selected; ?> ><?php echo $oGab->getName_page(); ?></option><?php
}
?>
		</select>
<?php
}
?>
</td></tr>
	<?php
// valeur sélectionnée
$oPageSelected = new Cms_page($rech_page_id);
$sPage = $oPageSelected->getName_page();
if ($sPage == "") $sPage = "Page";
?>
		<tr><td>
		<input name="pagename" type="text" disabled class="arbo" id="pagename" value="<?php echo $sPage;?>" size="30">&nbsp;
		<a href="#" class="arbo" onClick="choosePage();"><img src="/backoffice/cms/img/2013/icone/go.png" border="0">&nbsp;choisir</a>&nbsp;
		<a href="#" class="arbo" onClick="effacerPage();"><img src="/backoffice/cms/img/2013/icone/go.png" border="0">&nbsp;effacer</a>
		</td></tr>
<br /><?php
// valeur sélectionnée
$oNoeudSelected = new Cms_arbo_pages($rech_node_id);
$sChemin = $oNoeudSelected->getNode_libelle();
if ($sChemin == "") $sChemin = "Chemin";
?>		
		<tr><td>
		<input name="foldername" type="text" disabled class="arbo" id="foldername" value="<?php echo $sChemin;?>" size="30">&nbsp;
		<a href="#" class="arbo" onClick="chooseFolder();"><img src="/backoffice/cms/img/2013/icone/go.png" border="0">&nbsp;choisir</a>&nbsp;
		<a href="#" class="arbo" onClick="effacerFolder();"><img src="/backoffice/cms/img/2013/icone/go.png" border="0">&nbsp;effacer</a>
		</td></tr>
<?php
}
?>
	<br /><?php
$sRech_zone = $rech_zone;
if ($sRech_zone == "") $sRech_zone = "Mots clefs";
?> 
<tr><td>

<input type="text" name="fZone" id="fZone" value="<?php echo $sRech_zone; ?>" class="arbo" size="40"  onclick="javascript:document.getElementById('fZone').value =''" >&nbsp;<input 
type="button" value="Lancer la recherche" class="arbo" onClick="javascript:recherche()" >
		</td>
	</tr>
<?php
// MES CONTENUS
// sinon l'affichage en liste de tous les contenus impressionne ...
?>
<tr><td class="arbo" ><strong>Mes contenus : <?php echo $eContent_ALL_MOI; ?></strong></td></tr>
<?php if ($eContent_ATTEN_MOI > 0) { ?>
	<tr>
		<td class="arbo">en attente <img 
src="/backoffice/cms/img/puce-grisefoncee.gif" border="0"> :  <?php echo $eContent_ATTEN_MOI; ?></td>
	</tr>
<?php } ?>
<?php if ($eContent_REDACT_MOI > 0) { ?>
	<tr>
		<td class="arbo">a valider <img 
src="/backoffice/cms/img/puce-blanche.gif" border="0"> :  <?php echo $eContent_REDACT_MOI; ?></td>
	</tr>
<?php } ?>
<?php if ($eContent_GEST_MOI > 0) { ?>
	<tr>
		<td class="arbo">validé <img 
src="/backoffice/cms/img/puce-orange.gif" border="0"> :  <?php echo $eContent_GEST_MOI; ?></td>
	</tr>
<?php } ?>
<?php if ($eContent_LIGNE_MOI > 0) { ?>
	<tr>
		<td class="arbo">en ligne <img 
src="/backoffice/cms/img/puce-verte.gif" border="0"> :  <?php echo $eContent_LIGNE_MOI; ?></td>
	</tr>
<?php } ?>
<?php if ($eContent_ARCHI_MOI > 0) { ?>
	<tr>
		<td class="arbo">archivé <img 
src="/backoffice/cms/img/puce-grise.gif" border="0"> : <?php echo $eContent_ARCHI_MOI; ?></td>
	</tr>
<?php } ?>
<?php
//----------------------------------
// critères de recherche
//----------------------------------
if ($sRank == DEF_GEST || $sRank == DEF_ADMIN) {

// CONTENUS A VALIDER
?>
<tr><td class="arbo"><strong>Tous les contenus : </strong><?php echo $eContent_ALL; ?></td></tr>
<?php if ($eContent_ATTEN > 0) { ?>
	<tr><td class="arbo">en attente <img 
src="/backoffice/cms/img/puce-grisefoncee.gif" border="0"> : <?php echo $eContent_ATTEN; ?></td></tr>
<?php } ?>
<?php if ($eContent_REDACT > 0) { ?>
	<tr>
		<td class="arbo">a valider <img 
src="/backoffice/cms/img/puce-blanche.gif" border="0"> : <?php echo $eContent_REDACT; ?></td>
	</tr>
<?php } ?>
<?php if ($eContent_GEST > 0) { ?>
	<tr>
		<td class="arbo">validé <img 
src="/backoffice/cms/img/puce-orange.gif" border="0"> : <?php echo $eContent_GEST; ?></td>
	</tr>
<?php } ?>
<?php if ($eContent_LIGNE > 0) { ?>
	<tr>
		<td class="arbo">en ligne <img 
src="/backoffice/cms/img/puce-verte.gif" border="0"> : <?php echo $eContent_LIGNE; ?></td>
	</tr>
<?php } ?>
<?php if ($eContent_ARCHI > 0) { ?>
	<tr>
		<td class="arbo">archivé <img 
src="/backoffice/cms/img/puce-grise.gif" border="0"> : <?php echo $eContent_ARCHI; ?></td>
	</tr>
<?php } ?>

<?php
}
?>
</table></div>
<?php
// nombre d'enregistrements de la liste
if (sizeof($aContenus) == 1 || sizeof($aContenus) == 0) {
	$sMessage = sizeof($aContenus)." contenu";
}
else {
	$sMessage = sizeof($aContenus)." contenus";
}
?>
<div class="pagination"><?php echo $sMessage; ?></div>
<br />
<?php
// trop d'enregistrements : limitation des résultats à DEF_NBDROIT_CONTENT
if ($eContent > sizeof($aContenus)) {
?><div align="left" class="arbo">Il y a <?php echo $eContent; ?> réponses pour votre recherche, 
ce qui est supérieur au seuil autorisé de <?php echo DEF_NBDROIT_CONTENT; ?> réponses. <br />
Vous pouvez préciser la recherche.
</div><br /> <?php
}
?>

<table id="arbo_liste_contenu" class="arbo" border="0" cellpadding="5" cellspacing="0" align="center" width="100%">
  <?php
	if(sizeof($aContenus) == 0) {
?>
  <tr>
    <td align="center" colspan="10"><strong>&nbsp;Aucun élément à afficher</strong>
  </tr>
<?php
	} else {
?>
  <tr class="col_titre">
    <td align="left" nowrap class="col_titre_1">&nbsp;</td>
    <td align="left" class="col_titre_2"><strong>Nom</strong></td>
    <td class="col_titre_3"><strong>Version</strong></td>
    <td class="col_titre_4"><strong>Emplacement</strong></td>
	<td class="col_titre_5"><strong>Pages concernées</strong></td>
<?php
if (DEF_MENUS_MINISITES == "ON") {
?>
    <td align="center" class="col_titre_6"><strong>Site</strong></td>
    <?php
}
?>
    <td align="center" class="col_titre_7"><strong>Derni&egrave;re <br />
      modif. </strong></td>
    <td align="center" class="col_titre_8"><strong>Utilisateur</strong></td>
    <td align="center" class="col_titre_9">&nbsp;</td>
    <td align="center" class="col_titre_10"><strong>Actions</strong></td>

  </tr>
<?php
		for ($p=0; $p<sizeof($aContenus); $p++) {

			$oContenu = $aContenus[$p];
?>
  <tr class="<?php echo htmlImpairePaire($p);?>">
    <td align="left" nowrap class="col_contenu_1">&nbsp; </td>
   <td align="left"  class="col_contenu_2">
<?php
if ($oContenu->getStatut() == DEF_ID_STATUT_ARCHI) {?><img src="/backoffice/cms/img/puce-grise.gif" border="0" title="archivé"><?php }
else if ($oContenu->getStatut() == DEF_ID_STATUT_REDACT) {?><img src="/backoffice/cms/img/puce-blanche.gif" border="0" title="à valider"><?php }
else if ($oContenu->getStatut() == DEF_ID_STATUT_ATTEN) {?><img src="/backoffice/cms/img/puce-grisefoncee.gif" border="0" title="en attente (mode brouillon)"><?php }
else if ($oContenu->getStatut() == DEF_ID_STATUT_GEST) {?><img src="/backoffice/cms/img/puce-orange.gif" border="0" title="validé"><?php }
else if ($oContenu->getStatut() == DEF_ID_STATUT_LIGNE) {?><img src="/backoffice/cms/img/puce-verte.gif" border="0" title="en ligne"><?php }

// preview composant
if ($oContenu->getNature() == "CMS_ARCHI_CONTENT") {
?>&nbsp;<a href="#" onClick="javascript:preview_content_version(<?php echo $oContenu->getId();?>, <?php echo $oContenu->getWidth();?>, <?php echo $oContenu->getHeight();?>, <?php echo $oContenu->getVersion();; ?>)"><?php echo $oContenu->getName(); ?></a><?php
} else {
?>&nbsp;<a href="#" onClick="javascript:preview_content(<?php echo $oContenu->getId();?>, <?php echo $oContenu->getWidth();?>, <?php echo $oContenu->getHeight();?>)"><?php echo $oContenu->getName(); ?></a><?php
}
?>&nbsp;
   </td>
	<td  class="col_contenu_3"><?php
// affichage des icones de preview des pages
$idContent = $oContenu->getId();
$sNature = $oContenu->getNature();
$idPage = $oContenu->getIdPage();
$sNomPage = $oContenu->getPage();
$bToutenligne_page = $oContenu->getToutenligne_page();
$bExisteligne_page = $oContenu->getExisteligne_page();
//$nb_pages = sizeof( getPageUsingComposant( $idSite, $oContent->getId_content() ) );
$nb_pages = sizeof( getPageUsingComposant( $idSite, $oContenu->getId() ) );

// la racine est la même pour tous
// donc ajouter le nom du site après / pour avoir la racine du site en question
if ($oContenu->getNoeud() == "/") {
	$oSite = new Cms_site($idSite);
	$sRepSite = "/".$oSite->get_rep();
} else { 
	$sRepSite =""; 
}

$sUrlPageLigne = "/content".$sRepSite.$oContenu->getNoeud().$oContenu->getPage().".php";

afficheIconePreviewPage($idContent, $sNature, $idPage, $sNomPage, $bToutenligne_page, $bExisteligne_page, $sUrlPageLigne);
?>&nbsp;</td>
	<td class="gris10"  id="col_contenu_4"><?php echo cheminAere($oContenu->getNoeud());?><?php echo $oContenu->getPage(); ?>.php</td>
	<td class="gris10"  id="col_contenu_5">&nbsp;<?php echo $nb_pages; ?></td>
<?php
if (DEF_MENUS_MINISITES == "ON") {
?>
   <td width="5" align="center" class="arbo"  id="col_contenu_6">&nbsp;<?php echo $oContenu->getSite(); ?></td>
<?php
}
?>
<?php
if ($oContenu->getNature() == "CMS_CONTENT") {
?>
   <td align="center" class="arbo"  id="col_contenu_7">&nbsp;<?php
if ($oContenu->getDate_modif() != "") print(make_date_jjmmaaaa($oContenu->getDate_modif()));
else print("".make_date_jjmmaaaa($oContenu->getDate_creat()));
?></td>
<?php
} else if ($oContenu->getNature() == "CMS_ARCHI_CONTENT") {
?>
   <td align="center" class="arbo"  id="col_contenu_7">&nbsp;<?php echo make_date_jjmmaaaa($oContenu->getDate_publi()); ?></td>
<?php
}

?>
   <td width="5" align="center" class="arbo"  id="col_contenu_8">&nbsp;<?php echo $oContenu->getContributeur(); ?></td>
   <td align="center" class="arbo" nowrap  id="col_contenu_9">
<?php
if ($oContenu->getNature() == "CMS_CONTENT") {
?>
&nbsp;<a href="javascript:majContenu(<?php echo $oContenu->getId();?>, <?php echo $idSite; ?>, 'listeContenus.php')"><img src="/backoffice/cms/img/2013/icone/modifier.png" border="0" title="Modifier le contenu"></a>&nbsp;
<?php
}
?>
	</td>
	<td align="left" nowrap class="col_contenu_10">
<?php 
if ($oContenu->getNature() == "CMS_CONTENT") {

	// en attente : REDACT, GEST, ADMIN
	// a valider :  REDACT, GEST, ADMIN
	// valider :            GEST, ADMIN ou REDAC ++ (avec valid auto)
	// en ligne :                 ADMIN ou GEST ++ (avec valid auto)
	// archiver :   REDACT, GEST, ADMIN
	
	if ($sRank == DEF_ADMIN) {
		$bATTEN = 1;
		$bREDACT = 1;
		$bGEST = 1;
		$bLIGNE = 1;
		$bARCHI = 1;
	}
	
	if ($sRank == DEF_GEST && $sValidauto == 0) {
		$bATTEN = 1;
		$bREDACT = 1;
		$bGEST = 1;
		$bLIGNE = 0;
		$bARCHI = 1;
	}
	
	if ($sRank == DEF_GEST && $sValidauto == 1) {
		$bATTEN = 1;
		$bREDACT = 1;
		$bGEST = 1;
		$bLIGNE = 1;
		$bARCHI = 1;
	}
	
	if ($sRank == DEF_REDACT && $sValidauto == 0) {
		$bATTEN = 1;
		$bREDACT = 1;
		$bGEST = 0;
		$bLIGNE = 0;
		$bARCHI = 1;
	}
	
	if ($sRank == DEF_REDACT && $sValidauto == 1) {
		$bATTEN = 1;
		$bREDACT = 1;
		$bGEST = 1;
		$bLIGNE = 0;
		$bARCHI = 1;
	}
	
	

	// les briques en ligne ne peuvent pas changer de statut
	// elles sont archivées par les nouvelles briques en ligne
	// ou alors supprimées si on supprime la zone editable correspondante du gabarit
	if ($oContenu->getStatut() != DEF_ID_STATUT_LIGNE) {

		if ($bATTEN == 1) {
			if($oContenu->getStatut() != DEF_ID_STATUT_ATTEN ) { 
?>
<a href="javascript:majStatut(<?php echo $oContenu->getId(); ?>, <?php echo DEF_ID_STATUT_ATTEN; ?>)" title="en attente (mode brouillon)"><img 
src="/backoffice/cms/img/puce-grisefoncee.gif" border="0"></a>
<?php 
			}
		}
		
		if ($bREDACT == 1) {
			if($oContenu->getStatut() != DEF_ID_STATUT_REDACT ) { 
?>
<a href="javascript:majStatut(<?php echo $oContenu->getId(); ?>, <?php echo DEF_ID_STATUT_REDACT; ?>)" title="à valider"><img 
src="/backoffice/cms/img/puce-blanche.gif" border="0"></a>
<?php 
			} 
		}
		
		if ($bGEST == 1) {
			if($oContenu->getStatut() != DEF_ID_STATUT_GEST ) { 
?>
<a href="javascript:majStatut(<?php echo $oContenu->getId(); ?>, <?php echo DEF_ID_STATUT_GEST; ?>)" title="validé"><img 
src="/backoffice/cms/img/puce-orange.gif" border="0"></a>
<?php 
			} 
		}
		
		if ($bLIGNE == 1) {
			if($oContenu->getStatut() != DEF_ID_STATUT_LIGNE ) { 
?>
<a href="javascript:majStatut(<?php echo $oContenu->getId(); ?>, <?php echo DEF_ID_STATUT_LIGNE; ?>)" title="en ligne"><img 
src="/backoffice/cms/img/puce-verte.gif" border="0"></a>
<?php 
			} 
		}
		
		if ($bARCHI == 1) {
			if($oContenu->getStatut() != DEF_ID_STATUT_ARCHI ) { 
?>
<a href="javascript:majStatut(<?php echo $oContenu->getId(); ?>, <?php echo DEF_ID_STATUT_ARCHI; ?>)" title="archivé"><img 
src="/backoffice/cms/img/puce-grise.gif" border="0"></a>
<?php 
			}
		}
	}
}
else if ($oContenu->getNature() == "CMS_ARCHI_CONTENT") {

// statut de la brique équivalente dans CMS_CONTENT
// si la brique cms_content équivalente à celle ci de cms_arhi 
// est au statut attente, redacteur ou gestionnaire, 
// alors la création d'une nouvelle brique écraserait le contenu de cette brique de travail
// on prévient enaffichant un message d'alerte
if ($oContenu->getStatut_content() == DEF_ID_STATUT_ATTEN || 
	$oContenu->getStatut_content() == DEF_ID_STATUT_REDACT ||
	$oContenu->getStatut_content() == DEF_ID_STATUT_GEST) {

	$bEcrase = 1;
} else $bEcrase = 0;
?>
<a href="javascript:createNewVersion(<?php echo $oContenu->getId(); ?>, <?php echo $oContenu->getIdSite(); ?>, <?php echo $bEcrase; ?>)">
<img src="/backoffice/cms/img/ajouter.gif" border="0" title="Nouvelle version">
</a>
<?php
}
?>
	</td>
  </tr>
<?php
		}
	}
  ?>
  </table>

</form>