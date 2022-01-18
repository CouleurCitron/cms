<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

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


// utilisateur connecté
$oUserLogged = unserialize($_SESSION['BO']['LOGGED']);
$idUser_logged = $oUserLogged->getUser_id();
$sRank = $oUserLogged->getUser_rank();



if ($idSite == "") $idSite = $_SESSION['idSite_travail'];



// statut des briques demandé
// pour afficher la liste des contenus en ligne, la liste des contenus en attente, ...
$idStatut = $_GET['idStatut'];
if ($idStatut == "") $idStatut = $_POST['idStatut'];

// les briques archivées dans CMS_ARCHI_CONTENT sont dites "sauvegardées" pour l'utilisateur
// en réalité il existe 2 sortes de briques archivées : 
// 1. celles archivées dans CMS_CONTENT
// 2. celles archivées dans CMS_ARCHI_CONTENT

if ($idStatut == DEF_ID_STATUT_ARCHI) $sStatut = "Sauvegardé";
else $sStatut = getLibelleDefineWithCode($idStatut);



/////////////////////////////////////
// RESTORE :: maj html du contenu
/////////////////////////////////////

if ($_POST['operation'] == "RESTORE")
{
	//--------------------------------------
	// brique de CMS_ARCHI_CONTENT : COPY
	//--------------------------------------
	$oArchi_content = new Cms_archi_content($_POST['idArchi']);

	//--------------------------------------
	// brique de CMS_CONTENT : PASTE
	//--------------------------------------
	$oContent = new Cms_content($_POST['idContent']);

	// maj statut
	$oContent->setStatut_content(DEF_ID_STATUT_ATTEN);
	$oContent->updateStatut();

	// maj contenu html
	$oContent->setHtml_content($oArchi_content->getHtml_archi());
	
	if (DEF_BDD == "ORACLE" || DEF_BDD == "POSTGRES") {

		$datemep = date("Y/m/d/H:m:s");
		$datemep = explode('/', $datemep);
		$datemep = "to_date('".$datemep[2]."/".$datemep[1]."/".$datemep[0]."', 'dd/mm/yyyy')";

	} else if (DEF_BDD == "MYSQL") {

		$datemep = "str_to_date('".getDateNow()."', '%d/%m/%Y')";

	}

	$oContent->setDateupd_content($datemep);
	
	$return = $oContent->cms_content_update_contenu();
	

	//--------------------------------------
	// page : maj attributs
	//--------------------------------------	

	// on travaille sur un contenu -> mettre à jour les attributs de la page :: bToutenligne_page et bExisteligne_page

	// recherche de la page de cette brique editable
	$aStruct = getObjetWithContentBEdit($oContent->getId_content());
	$oStruct = $aStruct[0];
	$oPage = new Cms_page($oStruct->getId_page());

	// maj de l'attribut "tout en ligne"
	$bToutenligne_page = updateToutenligne($oPage->getId_page());
	$oPage->setToutenligne_page($bToutenligne_page);

	// maj de l'attribut "existe version ligne"
	$bExisteligne_page = updateExisteligne($oPage->getId_page());
	$oPage->setExisteligne_page($bExisteligne_page);


	// petit message de restauration
	$sResultat = "Restauration effectuée<br />";
	$sResultat.= "Remplacement du contenu de ".$oContent->getName_content();
	$sResultat.= " par sa version sauvegardée numéro ".$oArchi_content->getVersion_archi()."<br />";


	$_POST['operation'] = "";
}

?>
<div class="ariane"><span class="arbo2">CONTENU >&nbsp;</span><span class="arbo3">Liste des contenus</span></div>

<div class="arbo"><b>Liste des contenus modifiables <?php
if ($idStatut != "") {
?>"<?php echo $sStatut; ?>"<?php
} ?></b></div>

<?php
// site de travail
if (DEF_MENUS_MINISITES == "ON") print(putAfficheSite());
?>	  

<?php
if ($sResultat != "") {
?><div class="arbo"><?php echo $sResultat; ?></div><?php
	$sResultat="";
}
?>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
</style>
<style type="text/css">
	.ligneContent<?php echo  DEF_ID_STATUT_LIGNE ; ?>, .ligneContent<?php echo  DEF_ID_STATUT_ARCHI ; ?>, {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 12px;
		text-decoration: none;
		font-weight: normal;
		letter-spacing: normal;
	}
	tr.ligneContent<?php echo  DEF_ID_STATUT_ARCHI ; ?> { background-color: #DBDBDB; }
	tr.ligneContent<?php echo  DEF_ID_STATUT_LIGNE ; ?> { background-color: #9DCD36; }
	
	tr.ligneContent<?php echo  DEF_ID_STATUT_ARCHI ; ?>:hover { background-color: #DBDBDB; }
	tr.ligneContent<?php echo  DEF_ID_STATUT_LIGNE ; ?>:hover { background-color: #9DCD36; }
</style>
<script src="/backoffice/cms/js/preview.js" type="text/javascript"></script>
<script>

	// création d'une nouvelle version de travail à partir de la version en ligne
	// un nouvel enregistrement CMS_CONTENT va être créé
	// la version en ligne (CMS_ARCHI_CONTENT) reste jusqu'à ce qu'une nouvelle version en ligne la pousse
	function createNewVersion(idContent, idSite, bEcrase)
	{
		bConfirm=1;
		if (bEcrase) {

			sMessage = "ATTENTION !\n\n";
			sMessage+= "Il existe déjà une version de travail de ce contenu\n\n";
			sMessage+= "En créant une nouvelle version, vous allez écraser la version de travail\n\n";
			sMessage+= "Etes vous sûr ?";
			if (confirm	(sMessage))
			{
				bConfirm=1;
			} else bConfirm=0;
		}

		if (bConfirm) majContenu(idContent, idSite, 'listeContenusSauv.php');
	}

	// mise à jour du contenu de la brique
	function majContenu(id, idSite, urlRetour)
	{
		document.contenusSauvListForm.action = "contenuEditor.php?id="+id+"&idSite="+idSite+"&urlRetour="+urlRetour+"&idStatut=<?php echo $idStatut; ?>";
		document.contenusSauvListForm.submit();
	}

	// restauration d'une version archivée
	// le HTML de cette version (CMS_ARCHI_CONTENT) écrase le html de la version de travail correspondante (CMS_CONTENT)
	// le statut de la version de CMS_CONTENT passe à ATTENTE
	function restoreThisVersion(idArchi, idContent, operation)
	{
		sMessage = "Attention, cette opération est irréversible\n\n";
		sMessage+= "Voulez vous réellement remplacer le contenu par cette version sauvegardée ?";
		
		if (confirm(sMessage)) {
		
			document.contenusSauvListForm.idArchi.value=idArchi;
			document.contenusSauvListForm.idContent.value=idContent;
			document.contenusSauvListForm.operation.value=operation;
			document.contenusSauvListForm.action="listeContenusSauv.php";
			document.contenusSauvListForm.submit();
		}
	}

	function Tri(sChampTri)
	{
		//alert("Fonctionnalité bientôt disponible"); // en cours de dev
	}
	
</script>
<br /><br />
<form name="contenusSauvListForm" method="post">

<input type="hidden" name="idArchi" value="<?php echo $_POST['idArchi']; ?>">
<input type="hidden" name="idContent" value="<?php echo $_POST['idContent']; ?>">
<input type="hidden" name="operation" value="<?php echo $_POST['operation']; ?>">
<input type="hidden" name="idStatut" value="<?php echo $idStatut; ?>">

  <table border="0" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
<tr class="col_titre">
	<td align="center">&nbsp;&nbsp;<strong>Nom zone éditée</strong></td>
<?php
if (DEF_MENUS_MINISITES == "ON") {
?>
	<td width="5" align="center"><strong>&nbsp;&nbsp;Site</strong></td>
<?php
}	
?>
<?php
// si la page affiche les statuts en ligne -> pas d'affichage des versions qui pourrait perturber
// la version d'un content dans cms_archi est forcément le max des versions de cms_archi
// la version est visible dans les contenus sauvegardés
// la version est alors importante pour connaitre la chronologie des sauvegardes
if ($_GET['idStatut'] != DEF_ID_STATUT_LIGNE) {
?>
	<td align="center"><strong>&nbsp;Version&nbsp;</strong></td>
<?php
}
?>	
	<td align="center"><strong>&nbsp;Contributeur&nbsp;</strong></td>
	<td align="center"><strong>&nbsp;Date&nbsp;</strong></td>
	<td align="center">&nbsp;<strong>Statut</strong>&nbsp;</td>
	<td align="center"><strong>&nbsp;Action&nbsp;</strong></td>
	<td align="center"><strong>&nbsp;Pages concernées&nbsp;</strong></td>
</tr>
<?php
// liste des briques de la table CMS_ARCHI_CONTENT

if ($idStatut != "") {
	if ($sRank == DEF_REDACT) $aArchi_content = getArchiListStatut($idStatut, $idUser_logged);
	else $aArchi_content = getArchiListStatut($idStatut);
}
else {
	$aArchi_content = getArchiList();
}

foreach($aArchi_content as $k=>$oArchi_content) {

	// même objet dans cms_content pour avoir son nom, son noeud, ...
	$oContent = new Cms_content($oArchi_content->getId_content_archi());

	// pages utilisant ce composant
	$aPages = getPageUsingComposant($idSite, $oContent->getId_content());
	$nb_pages = newSizeOf($aPages);

	// noeud
	$oNoeud = new Cms_arbo_pages($oContent->getNodeid_content());

	// utilisateur
	$oDroitContent = getDroit($oContent->getId_content());

	if ($oDroitContent->getUser_id() != "-1") {
		$oUser = new bo_users($oDroitContent->getUser_id());
		$sNomUser = $oUser->getUser_nom();
	} else $sNomUser = "non affecté";


	if ($_GET['idStatut'] == DEF_ID_STATUT_LIGNE) $sStatut = getLibelleDefineWithCode($oArchi_content->getStatut_archi());
	else $sStatut = "Sauvegardé";

?>
<tr class="<?php echo htmlImpairePaire($k);?>">
	<td align="left" class="arbo">
<?//=$oArchi_content->getId_archi()?>
<?//=$oArchi_content->getId_content_archi()?>
<a href="#" 
onClick="javascript:preview_content_version(<?php echo $oContent->getId_content();?>,<?php echo $oContent->getWidth_content();?>,<?php echo $oContent->getHeight_content();?>, <?php echo $oArchi_content->getVersion_archi(); ?>)">
<?php echo  $oContent->getName_content() ; ?></a>&nbsp;

<br /><?php echo cheminAere($oNoeud->getAbsolute_path_name());?>
</td>
<?php
if (DEF_MENUS_MINISITES == "ON") {
?>
	<td class="arbo"><?php
$oSite = new Cms_site($oContent->getId_site());	
?><?php echo $oSite->get_name(); ?></td>
<?php
}
?>
<?php
if ($_GET['idStatut'] != DEF_ID_STATUT_LIGNE) {
?>
	<td align="center" class="arbo"><?php echo $oArchi_content->getVersion_archi(); ?></td>
<?php
}
?>
	<td align="center" class="arbo"><?php echo $sNomUser; ?>&nbsp;</td>
	<tD align="left" class="arbo"><?php echo make_date_jjmmaaaa($oArchi_content->getDate_archi()); ?>
	<?//=$oArchi_content->getHtml_archi()?></tD>
	<td class="arbo" nowrap><?php echo $sStatut; ?>&nbsp;</td>
	<td class="arbo"><?php
if ($oArchi_content->getStatut_archi() == DEF_ID_STATUT_LIGNE) {

// statut de la brique équivalente dans CMS_CONTENT
// si la brique cms_content équivalente à celle ci de cms_arhi 
// est au statut attente, redacteur ou gestionnaire, 
// alors la création d'une nouvelle brique écraserait le contenu de cette brique de travail
// on prévient enaffichant un message d'alerte
if ($oContent->getStatut_content() == DEF_ID_STATUT_ATTEN || 
	$oContent->getStatut_content() == DEF_ID_STATUT_REDACT ||
	$oContent->getStatut_content() == DEF_ID_STATUT_GEST) {

	$bEcrase = 1;
} else $bEcrase = 0;

?><input type="button" name="btActionNew" value="Nouvelle" onClick="javascript:createNewVersion(<?php echo $oArchi_content->getId_content_archi(); ?>, <?php echo $oContent->getId_site(); ?>, <?php echo $bEcrase; ?>)" class="arbo" style="width:80px"><?php
} else if ($oArchi_content->getStatut_archi() == DEF_ID_STATUT_ARCHI) {
?><input type="button" name="btActionRestore" value="Restaurer" onClick="javascript:restoreThisVersion(<?php echo $oArchi_content->getId_archi(); ?>, <?php echo $oArchi_content->getId_content_archi(); ?>, 'RESTORE')" class="arbo" style="width:80px"><?php
}
	?>&nbsp;</td>
	<td align="left" class="arbo"><?php
if ($nb_pages > 0) {
?><table>
<?php
	for ($a=0; $a<newSizeOf($aPages); $a++) {
	
		$oPage = new Cms_page($aPages[$a]);
		$oSite = new Cms_site($oPage->getId_site());
		$oArbo = new Cms_arbo_pages($oPage->getNodeid_page());

		// si la page est à la racine d'un mini site
		// son chemin est minisite/ et non /
		if ($oPage->getNodeid_page() == 0) {
			$current_path_page = "/".$oSite->get_rep().$oArbo->getAbsolute_path_name();
		} else {
			$current_path_page = $oArbo->getAbsolute_path_name();			
		}

?><tr>
		<td>
<?//=$oArbo->getAbsolute_path_name()?>
<?php
// affichage des icones de preview des pages
$idContent = -1;
$sNature = "CMS_CONTENT";
$idPage = $oPage->getId_page();
$sNomPage = $oPage->getName_page();
$bToutenligne_page = $oPage->getToutenligne_page();
$bExisteligne_page = $oPage->getExisteligne_page();
$sUrlPageLigne = "/content".$current_path_page.$oPage->getName_page().".php";

afficheIconePreviewPage($idContent, $sNature, $idPage, $sNomPage, $bToutenligne_page, $bExisteligne_page, $sUrlPageLigne);
?>

<?php echo $oPage->getName_page(); ?>.php
		</td>
	</tr><?php
	}
?></table><?php
}
?>&nbsp;	
	</td>
</tr>
<?php
}
?>

</table>

</form>