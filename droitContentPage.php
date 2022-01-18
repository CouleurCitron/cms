<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
// sponthus 07/07/2005
// affectation des droits aux différents contenus de la page


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
 include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


activateMenu('gestionpage');  //permet de dérouler le menu contextuellement

// id de la page
$idPage = $_GET['idPage'];
if ($idPage== "") $idPage= $_POST['idPage'];

// objet page
$oPage = new Cms_page($idPage);

// noeud
$oNoeud = new Cms_arbo_pages($oPage->getNodeid_page());

// site
$idSite = $oPage->getId_site();
$oSite = new Cms_site($idSite);

// tous les content de la page
// les content de la page sont forcément des content editables
$aContent = getContentFromPage($oPage->getId_page());

// liste des utilisateurs + les admin
$aUser = listUsersWidthAdmin($oSite->get_id());




// operation :: maj de la BDD
$operation = $_POST['operation'];

//-------------------------
// maj BDD
$sLogOperation = "";
if ($operation == "VALIDER") {

	// pour tous les content de la page
	for ($i=0; $i<newSizeOf($aContent); $i++)
	{
		// objet content
		$oContent = $aContent[$i];
	
		// sélection du droit de ce content
		// dans cette version d'adequation
		// une brique n'appartient qu'à un et un seul utilisateur
		// rien n'empecherait de créer différents ID_DROIT pour un même ID_CONTENT
		$oDroitContent = getDroit($oContent->getId_content());
		
		// création des nouveaux droits
		$fIdContent = "fIdContent".$i;
		$fIdUser = "fIdUser".$i;

		// suppression des droits de ce content
		if ($oDroitContent->getId_droit() != -1) dbDelete($oDroitContent);
			
		// si utilisateur affecté
		if ($_POST[$fIdUser] != -1) {
			// création du nouveau droit
			$oDroit = new Droit();	
			
			$oDroit->setId_content($_POST[$fIdContent]);
			$oDroit->setUser_id($_POST[$fIdUser]);
	
			dbInsertWithAutoKey($oDroit);
			
			$sLogOperation = "Droits enregistrés";
		} else {
			// droit non affecté, contributeur non sélectionné		
		}
	}
}
//-------------------------


?>
<html>
<head>
<title>Droits de la page</title>
<script>

// validation
function valider()
{
	document.droitForm.operation.value="VALIDER";
	document.droitForm.action="droitContentPage.php";
	document.droitForm.submit();
}

// retour à la page de gestion des droits
function retour()
{
	document.droitForm.action="arboPageDroit_browse.php";
	document.droitForm.submit();
}

</script>
</head>
<body>
<form name="droitForm" method="post">

<input type="hidden" name="operation" value="">
<input type="hidden" name="idPage" value="<?php echo $idPage; ?>">

<span class="arbo2">PAGE&nbsp;>&nbsp;</span><span class="arbo3">Gestion des droits&nbsp;>&nbsp;Affectation des droits</span>
<br /><br />

<div class="arbo"><strong>Page <?php echo $oPage->getName_page(); ?></strong><br />
<?php
// chaine absolute path
// on enlève le nom du site dans l'arbo
// on espace les / de la chaine
// ainsi une chaine trop longue n'explosera pas la colonne
$sChemin = cheminAere($oNoeud->getAbsolute_path_name());
?><?php echo $sChemin;?>

</div><br />

<div align="left" class="arbo"><a href="javascript:retour()">Retour</a></div>
<br />

<div class="arbo">Liste des contenus et affichage de leurs droits : </div><br />
<table  border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo" width="100%">
	<tr class="arbo">
		<td><strong>Zone</strong></td>
		<td><strong>Contenu</strong></td>
		<td><strong>Utilisateur</strong></td>
	</tr>
<?php
for ($i=0; $i<newSizeOf($aContent); $i++)
{
	$oContent = $aContent[$i];
	
	// structure de la page
	$oStruct = new Cms_struct_page();
	$oStruct->getObjet($idPage, $oContent->getId_content());

	// zone editable corresopndante à cette brique
	$oZonedit = new Cms_content($oStruct->getId_zonedit_content());
	
	// objet droit
	$oDroitContent = getDroit($oContent->getId_content());
?>
	<tr class="arbo">
		<td><?php echo $oZonedit->getName_content(); ?>&nbsp;</td>
		<td><?php echo $oContent->getName_content(); ?>&nbsp;
		<input type="hidden" name="fIdContent<?php echo $i; ?>" value="<?php echo $oContent->getId_content(); ?>"></td>
		<td><select name="fIdUser<?php echo $i; ?>" class="arbo">
		<option value="-1">non affecté</option>
<?php
for ($t=0; $t<newSizeOf($aUser); $t++) {
		$oUser = $aUser[$t];
		
		// utilisateur propriétaire du droit
		if ($oDroitContent->getUser_id() == $oUser->id) $selected = "selected";
		else $selected = "";
?>
			<option value="<?php echo $oUser->id; ?>" <?php echo $selected; ?>>
			(<?php echo getLibelleDefineWithCode($oUser->getUser_rank()); ?>)
			&nbsp;<?php echo $oUser->getUser_prenom(); ?>&nbsp;<?php echo $oUser->getUser_nom(); ?>&nbsp;
			</option>
<?php
}			
?>
		</select></td>
	</tr>
<?php
}
?>
</table>

<br /><div align="center" class="arbo"><strong><?php echo $sLogOperation; ?></strong></div><br /><br />

<div align="center" class="arbo"><input type="button" onClick="javascript:valider()" value="<?php $translator->echoTransByCode('btValider'); ?>" class="arbo" /></div>

</form>
</body>
</html>
