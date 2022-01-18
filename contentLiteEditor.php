<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
sponthus 01/07/2005
maj d'un contenu pour le rédacteur (contributeur)
== maj d'une brique mais en ne pouvant modifier QUE le contenu de la brique

*/


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


// id du contenu à sauver
$id = $_GET['id'];
if ($id == "") $id = $_POST['id'];

// url retour
$urlRetour = $_GET['urlRetour'];
if ($urlRetour == "") $urlRetour = $_POST['urlRetour'];

// objet Content
$oContent = new Cms_content($id);


$composant = null;
if (strlen($id) > 0) $composant = getComposantById($id);

// par défaut sélection du site principal (pas un mini site)
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

// site sur lequel on est positionné
$oSite = new Cms_site($idSite);
$idSite = $oSite->get_id();


// operation
$operation = $_GET['operation'];
if ($operation == "") $operation = $_POST['operation'];

if ($operation == "UPDATE")
{
	// maj BDD

	$oContent = new Cms_content($id);

	$oContent->setHTML_content(compliesFCKhtml($_POST['FCKeditor1']));
	$oContent->setName_content($_POST['nom']);
	$oContent->setWidth_content($_POST['largeur']);
	$oContent->setHeight_content($_POST['hauteur']);

	if (DEF_BDD == "ORACLE" || DEF_BDD == "POSTGRES") {
	
		$datemep = date("Y/m/d/H:m:s");
		$datemep = explode('/', $datemep);
		$datemep = "to_date('".$datemep[2]."/".$datemep[1]."/".$datemep[0]."', 'dd/mm/yyyy')";
	
	} else if (DEF_BDD == "MYSQL") {
	
		$datemep = "str_to_date('".getDateNow()."', '%d/%m/%Y')";
	}


	$oContent->setDateupd_content($datemep);

	$return = $oContent->cms_content_update_contenu();


	// maj des briques editables inclues dans cette zone editable
	
	// toutes les structures où cette zone editable est remplie
	$aStructBriquedit = getObjetWithZonedit($oContent->getId_content());

	for ($a=0; $a<newSizeOf($aStructBriquedit); $a++)
	{
		$oStructBriquedit = $aStructBriquedit[$a];

		// la brique utilisée dans cette zone editable
		$oBriquedit = new Cms_content($oStructBriquedit->getId_content());

		// la page de cette brique editable
		$oPage = new Cms_page($oStructBriquedit->getId_page());

		// on ne modifie pas le contenu des zoneditables
		// car le contenu des zones editables est juste un pré remplissage
		// les briques editables ont pu être remplies par ailleurs

		$sNom = $oContent->getName_content()."_".$oPage->getName_page();

		$oBriquedit->setName_content($sNom);
		$oBriquedit->setWidth_content($oContent->getWidth_content());
		$oBriquedit->setHeight_content($oContent->getHeight_content());

		if (DEF_BDD == "ORACLE" || DEF_BDD == "POSTGRES") {
		
			$datemep = date("Y/m/d/H:m:s");
			$datemep = explode('/', $datemep);
			$datemep = "to_date('".$datemep[2]."/".$datemep[1]."/".$datemep[0]."', 'dd/mm/yyyy')";
		
		} else if (DEF_BDD == "MYSQL") {
		
			$datemep = "str_to_date('".getDateNow()."', '%d/%m/%Y')";
		}
	
		$oBriquedit->setDateupd_content($datemep);

		$return = $oBriquedit->cms_content_update_contenu();
	}

	// a_voir sponthus maj zone editable-> génération des gabarits ?

	if ($return > 0) {
		$sMessage = "Le contenu a été correctement sauvegardé";
		if ($urlRetour != "") $sRetourListe = "<a href='".$urlRetour."'>retour</a>";
		else $sRetourListe = "";
		
		print("$sMessage");
		print("<br /><br />$sRetourListe");
		exit();
	}
}

?>
<script type="text/javascript">document.title = "Briques BB";</script>
<link href="/backoffice/cms/css/bo.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>
<?php	
// on est en mode conception
// this part determines the physical root of your website
// it's up to you how to do this
if (!preg_match('/\/$/msi', $HTTP_SERVER_VARS['DOCUMENT_ROOT']))
  $_root = $HTTP_SERVER_VARS['DOCUMENT_ROOT'].'/';
else
  $_root = $HTTP_SERVER_VARS['DOCUMENT_ROOT'];

define('DR', $_root);
unset($_root);

include('backoffice/cms/lib/FCKeditor/fckeditor.php') ;

// here we add some styles to styles dropdown
?>
<style type="text/css">
  pre {
    background : #cccccc; 
    padding : 5 5 5 5;
  }
</style>

<span class="arbo2"><strong>
<?php
if ($id == "") {
?>Création <?php
} else { 
?>Modification <?php } ?>du composant HTML <?php echo $oContent->getName_content(); ?>:</strong></span>
<form "createBriqueStep1" action="<?php echo $_SERVER['PHP_SELF']; ?>?idSite=<?php echo $idSite; ?>&minisite=<?php echo $_GET['minisite']; ?>&id=<?php if (strlen($_GET['id'])>0) echo $_GET['id']; ?>&operation=UPDATE" method="post">
<input type="hidden" name="operation" value="<?php echo $operation; ?>">
<span class="arbo">
<p class="caracteristique"><u><strong>Caractéristiques</strong></u></p>

<ul>
<li>type composant : HTML</li>
<input type="hidden" name="nom" value="<?php echo $oContent->getName_content(); ?>">
<input type="hidden" name="largeur" value="<?php echo $oContent->getWidth_content(); ?>">
<input type="hidden" name="hauteur" value="<?php echo $oContent->getHeight_content(); ?>">

</ul>

<div align="center">
<?php
if(!$_GET['init']==1){
	$_SESSION['HTMLcontent']='';
}
$sw = null;
$url = addslashes($_SERVER['HTTP_REFERER']);
$url = str_replace('/','\/',$url);

// Automatically calculates the editor base path based on the _samples directory.
// This is usefull only for these samples. A real application should use something like this:
/*$oFCKeditor->BasePath = '/lib/FCKeditor/' ;	// '/FCKeditor/' is the default value.
$sBasePath = $_SERVER['PHP_SELF'] ;
$sBasePath = '/lib/FCKeditor'.substr( $sBasePath, 0, strpos( $sBasePath, "backoffice/" ) ) ;

$oFCKeditor = new FCKeditor('FCKeditor1') ;
$oFCKeditor->BasePath	= $sBasePath ;*/
$oFCKeditor = new FCKeditor('FCKeditor1') ;
$oFCKeditor->BasePath = '/backoffice/cms/lib/FCKeditor/' ;

$oFCKeditor->Value = htmlFCKcompliant($oContent->getHtml_content());

$oFCKeditor->Width  = '75%' ;
$oFCKeditor->Height = '500' ;
$oFCKeditor->Create() ;
?>
</div>
<div align="center"><input name="Enregistrer" type="button" class="arbo" onClick="submit();" value="Enregistrer">
  <br />
  <br />
</div>

<div class="note_editeur">
<b>Note :</b> A cause de la barre d'outils, l'espace de composition n'a pas forcément la taille indiqué précédemment.
Les tailles seront appliquées définitivement à partir de l'étape suivante (preview).</span></div>
<input type="hidden" name="pageEditor" value="<?php echo $_SERVER['REQUEST_URI'];?>">
<input type="hidden" name="TYPEcontent" value="HTML">
</form>
