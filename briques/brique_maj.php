<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
sponthus 01/11/2005
mise à jour du contenu d'une brique et seulement du contenu
*/


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once 'cms-inc/msohtml_lib.php' ;
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

activateMenu('gestionbrique');

////////////////////////
// VALEURS POSTEES /////

$id = $_POST['id'];
if ($id == "") $id = $_GET['id'];
////////////////////////

////////////////////////
// objet content
$composant = null;
if (strlen($id) > 0) {
	$composant = getComposantById($id);
	$oContent = new Cms_content($id);
} else $oContent = new Cms_content();
////////////////////////

?><div class="arbo2"><b><?php if ($id > 0) { ?>Modifier<?php } else { ?>Créer<?php } ?> une brique</b></div><?php

// dans le fichier selectSite.php
if (DEF_MENUS_MINISITES == "ON") print(putAfficheSite());

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
//--------------------
// site
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

// objet site sur lequel on est positionné
$oSite = new Cms_site($idSite);
$oSite->getSite();
//--------------------


if($_POST['mode_page'] == "preview") {
	// on est en mode preview
	// on sauvegarde le post en session au càs où...
	$htmlContent=compliesFCKhtml($_POST['FCKeditor1']);

	$bPortPos = strpos($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_PORT']);
	if ($bPortPos === false) {
	   // port 80 non visible dans l'URL
		// ben ça va, on a pas grand chose à faire
	} else {
	   // port exotique
	   $htmlContent = str_replace(":".$_SERVER['SERVER_PORT']."/","/",$htmlContent);
	}
    $htmlContent = preg_replace("`href=\"/lib/spaw[^/]*/`i",'href="',$htmlContent);
	$htmlContent = preg_replace("`src=\"/lib/spaw[^/]*/`i",'src="',$htmlContent);
	

	$htmlContent= preg_replace("/(<|&lt;)§/","<?",$htmlContent);
	$htmlContent= preg_replace("/§(>|&gt;)/","?>",$htmlContent);
	$htmlContentForm = preg_replace("/'/",'`',$htmlContent);
	$htmlContentForm= preg_replace("/<\?/",'<§',$htmlContentForm);
	$htmlContentForm= preg_replace("/\?>/",'§>',$htmlContentForm); 
	
	$_SESSION['HTMLcontent'] = $htmlContent; 

?>
<script src="/backoffice/cms/js/fojsutils.js"></script>

<span class="arbo2"><strong>Aperçu du composant HTML <?php echo $_POST['nom']; ?>:</strong></span>
<?php
//--------------------
// site
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

// objet site sur lequel on est positionné
$oSite = new Cms_site($idSite);
$oSite->getSite();
//--------------------
?>
<form name="previewHTML" action="brique_sauve.php?idSite=<?php echo $idSite; ?>&minisite=<?php echo $_GET['minisite']; ?>&id=<?php if (strlen($_GET['id'])>0) echo $_GET['id']; ?>" method="post">
<input type="hidden" name="TYPEcontent" value="<?php echo $oContent->getType_content(); ?>">
<input type="hidden" name="WIDTHcontent" value="<?php echo $oContent->getWidth_content(); ?>">
<input type="hidden" name="HEIGHTcontent" value="<?php echo $oContent->getHeight_content(); ?>">
<input type="hidden" name="NAMEcontent" value="<?php echo $oContent->getName_content(); ?>">
<input type="hidden" name="HTMLcontent" value='<?php echo $htmlContentForm; ?>'>
<hr width="<?php echo $_POST['largeur']; ?>" align="left" class="arbo2">
<div style="width: <?php echo $_POST['largeur']; ?>px; height: <?php echo $_POST['hauteur']; ?>px; background-color: #ffffff; overflow: auto;">
<?php 
$htmlContent = preg_replace('/\<form/i','<uniform',$htmlContent);
$htmlContent = preg_replace('/\<\/form\>/i','</uniform>',$htmlContent);

echo $htmlContent; ?>
</div>
<hr width="<?php echo $_POST['largeur']; ?>" align="left" class="arbo2">
<span class="arbo"><u>Dimensions&nbsp;:</u>&nbsp;<?php echo $_POST['largeur']; ?> x <?php echo $_POST['hauteur']; ?>&nbsp;pixels</span>
<br />
<br />
<div>
<input name="<< Retour (modifier)" type="button" class="arbo" onClick="javascript:document.location='<?php echo $_SERVER['PHP_SELF']."?l=".$_POST['largeur']."&h=".$_POST['hauteur']."&init=1&nom=".$_POST['nom'] ;?>&id=<?php if (strlen($_GET['id'])>0) echo $_GET['id']; ?>&idSite=<?php echo $idSite; ?>';" value="<< Retour (modifier)">
&nbsp;&nbsp;&nbsp;&nbsp;
<input name="Suite (enregistrer) >>" type="submit" class="arbo" value="Suite (enregistrer) >>">
</div>
</form>
<?php
} else {
	// on est en mode conception
// this part determines the physical root of your website
// it's up to you how to do this
if (!preg_match('/\/msi$', $HTTP_SERVER_VARS['DOCUMENT_ROOT']))
  $_root = $HTTP_SERVER_VARS['DOCUMENT_ROOT'].'/';
else
  $_root = $HTTP_SERVER_VARS['DOCUMENT_ROOT'];

define('DR', $_root);
unset($_root);

include("FCKeditor/fckeditor.php") ;

// here we add some styles to styles dropdown
?>
<style type="text/css">
  pre {
    background : #cccccc; 
    padding : 5 5 5 5;
  }
</style>
<br />
<span class="arbo2"><strong>Création du composant HTML <?php echo $_GET['nom']; ?>:</strong></span>

<?php

//--------------------
// site
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

// objet site sur lequel on est positionné
$oSite = new Cms_site($idSite);
$oSite->getSite();
//--------------------

?>

<form "createBriqueStep1" action="<?php echo $_SERVER['PHP_SELF']; ?>?idSite=<?php echo $idSite; ?>&minisite=<?php echo $_GET['minisite']; ?>&id=<?php if (strlen($_GET['id'])>0) echo $_GET['id']; ?>" method="post">
<?php
// les variables ne sont jamais envoyées de la même manière 
// selon que l'on vienne d'une page, que l'on reposte, que l'on clique sur un lien
// ... alors je teste
// sponthus 
$TYPEcontent = "";
if ($_POST['TYPEcontent'] != "") $TYPEcontent = $_POST['TYPEcontent'];
if ($TYPEcontent == "" && $_GET['TYPEcontent'] != "") $TYPEcontent = $_GET['TYPEcontent'];
if ($TYPEcontent == "") $TYPEcontent = "HTML"; // Par défaut si vraiment on a rien, la brique est HTML
?>
<span class="arbo">
<u><strong>Caractéristiques</strong></u>
<ul>
<li>type composant : <?php echo $oContent->getType_content(); ?></li>
<li>nom : <?php echo $oContent->getName_content(); ?></li>
<li>largeur : <?php echo $oContent->getWidth_content(); ?></li>
<li>hauteur : <?php echo $oContent->getHeight_content(); ?></li>
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
$oFCKeditor->BasePath = '/lib/FCKeditor/' ;	// '/FCKeditor/' is the default value.
$sBasePath = $_SERVER['PHP_SELF'] ;
$sBasePath = '/lib/FCKeditor'.substr( $sBasePath, 0, strpos( $sBasePath, "backoffice/" ) ) ;

$oFCKeditor = new FCKeditor('FCKeditor1') ;
$oFCKeditor->BasePath	= $sBasePath ;

if( preg_match('/'.$url.'/','saveComposant') ) {
	$oFCKeditor->Value = htmlFCKcompliant($_SESSION['HTMLcontent']);
} else {
	$oFCKeditor->Value = htmlFCKcompliant($composant['html']);
}
$oFCKeditor->Width  = '75%' ;
$oFCKeditor->Height = '500' ;
$oFCKeditor->Create();
?>
</div>
<div align="center"><input name="Suite (preview) >>" type="button" class="arbo" onClick="submit();" value="Suite (preview) >>">
  <br />
  <br />
</div>
<b><u>Note :</u></b> A cause de la barre d'outils, l'espace de composition n'a pas forcément la taille indiqué précédemment.
Les tailles seront appliquées définitivement à partir de l'étape suivante (preview).</span>
<input type="hidden" name="pageEditor" value="<?php echo $_SERVER['REQUEST_URI'];?>">
<input type="hidden" name="TYPEcontent" value="<?php echo $oContent->getType_content(); ?>">
<input type="hidden" name="largeur" value="<?php echo $oContent->getWidth_content(); ?>">
<input type="hidden" name="hauteur" value="<?php echo $oContent->getHeight_content(); ?>">
<input type="hidden" name="nom" value="<?php echo $oContent->getName_content(); ?>">
<input type="hidden" name="mode_page" value="preview">
</form>
<?php
}
?>