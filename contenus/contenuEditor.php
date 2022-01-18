<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
sponthus 01/07/2005
maj d'un contenu pour le rédacteur (contributeur)
== maj d'une brique mais en ne pouvant modifier QUE le contenu de la brique

*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/gabarit.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

activateMenu('gestioncontenu');  //permet de dérouler le menu contextuellement

// id du contenu à sauver
if (is_get('id')){
	$id = $_GET['id'];
}
elseif (is_post('id')){
	$id = $_POST['id'];
}

// url retour
if (is_get('urlRetour')){
	$urlRetour = $_GET['urlRetour'];
}
elseif (is_post('urlRetour')){
	$urlRetour = $_POST['urlRetour'];
}

// idStatut
if (is_get('idStatut')){
	$idStatut = $_GET['idStatut'];
}
elseif (is_post('idStatut')){
	$idStatut = $_POST['idStatut'];
}

// objet Content
$oContent = new Cms_content($id);


$composant = null;
if ((int)($id) > 0) $composant = getComposantById($id);



// site sélectionné
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

// site sur lequel on est positionné
$oSite = new Cms_site($idSite);
$idSite = $oSite->get_id();


// recherche de la page de cette brique editable
$aStruct = getObjetWithContentBEdit($id);
$oStruct = $aStruct[0];
$oPage = new Cms_page($oStruct->getId_page());
unset($_SESSION['idThe']);
$_SESSION['idThe']=$oPage->get_theme();


// operation
if (is_get('operation')){
	$operation = $_GET['operation'];
}
elseif (is_post('operation')){
	$operation = $_POST['operation'];
}

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

	$dMaj = date("Y/m/d/H:m:s");
	$dMaj = explode('/', $dMaj);
	$dMaj = $dMaj[2]."/".$dMaj[1]."/".$dMaj[0];

	$oContent->setDateupd_content($datemep);


	// remplacement des valeurs [] pour le bandeau de bas (appel du print) et date de maj
	$sHtml = $oContent->getHTML_content();
	$sHtml = str_replace("[ID_BRIQUE]", $id, $sHtml);
	$sHtml = str_replace("[DMAJ]", $dMaj, $sHtml); 
	
	// repertoire du minisite
	if ($idSite == $_SESSION["idSite"]){
		$rep = $_SESSION["rep_travail"];
	}
	else{
		$rep = $oSite->get_rep();
	}
        
	if (is_file($_SERVER['DOCUMENT_ROOT'].'/include/modules/'.$rep.'/mod_rewrite.inc.php')){	
		include_once('modules/'.$rep.'/mod_rewrite.inc.php');		
		if (function_exists('do_rewrite_rule')){
			// oui, on a de quoi faire du rewrite		
			$sHtml = do_rewrite_rule($sHtml);		
		}
	}  
	
	$oContent->setHTML_content(compliesFCKhtml($sHtml)); 
	
	
 
 
	// TOUTES LES BRIQUES EDITABLES MODIFIEES AURONT LE STATUS "EN ATTENTE"
	$oContent->setStatut_content(DEF_ID_STATUT_ATTEN);
	$return = $oContent->updateStatut();
	
	$return = $oContent->cms_content_update_contenu();

///////////////////////////////////////////////////

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

///////////////////////////////////////////////////

	if ($return > 0) {
		$sMessage = "Le contenu a été correctement sauvegardé";
	
		$sDebut_Div = "<div style=\"font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 10px;\">";
		$sFin_Div = "</div>";

		print("<br />$sDebut_Div");
		print("$sMessage");
		print("<br />$sFin_Div");

?><script type="text/javascript">
document.location="listeContenus.php";
</script><?php

		// REGENERATION DE PAGE //
		// Il ne faut pas le faire ici, car la brique n'est pas sensée être en ligne
		// puisqu'on est en train de la modifier
		// on doit régénérer à la modification du status => en ligne
		
		// Je suppose ici que toutes les briques éditables ont une zone éditable et donc une page
		// donc pas de test pour afficher la régénération que si la brique est affichée dans une page 
		// (puisque c'est sensé être tout le temps le cas)
?>
</span>
<?php
//		include_once('regeneratePages.php');

		exit();

	}
}

?>
<link href="/backoffice/cms/css/bo.css" rel="stylesheet" type="text/css">
<?php	
// on est en mode conception
// this part determines the physical root of your website
// it's up to you how to do this
if (!preg_match('/\/$/msi', $_SERVER['DOCUMENT_ROOT']))
  $_root = $_SERVER['DOCUMENT_ROOT'].'/';
else
  $_root = $_SERVER['DOCUMENT_ROOT'];

define('DR', $_root);
unset($_root);
if (defined("DEF_FCK_VERSION") && DEF_FCK_VERSION == "ckeditor" ) {
	echo '<script src="/backoffice/cms/lib/ckeditor/ckeditor.js"></script>';
} 
else {
	include("backoffice/cms/lib/FCKeditor/fckeditor.php") ;
} 

// here we add some styles to styles dropdown
?>
<style type="text/css">
  pre {
    background : #cccccc; 
    padding : 5 5 5 5;
  }
</style>
<span class="arbo2">CONTENU&nbsp;>&nbsp;</span>
<span class="arbo3">
<?php
if ($id == "") {
?>Création <?php
} else { 
?>Modification <?php } ?>du composant HTML <?php echo $oContent->getName_content(); ?></span>
<form name="createBriqueStep1" id="createBriqueStep1" action="<?php echo $_SERVER['PHP_SELF']; ?>?idSite=<?php echo $idSite; ?>&minisite=<?php echo $_GET['minisite']; ?>&id=<?php if (strlen($_GET['id'])>0) echo $_GET['id']; ?>&operation=UPDATE" method="post">
<input type="hidden" name="operation" value="<?php echo $operation; ?>">
<input type="hidden" name="urlRetour" value="<?php echo $urlRetour; ?>">
<input type="hidden" name="idStatut" value="<?php echo $idStatut; ?>">
<span class="arbo">
<p class="caracteristique"><u><strong>Caractéristiques</strong></u></p>

<ul>
<li>type composant : HTML</li>
<input type="hidden" name="nom" value="<?php echo $oContent->getName_content(); ?>">
<input type="hidden" name="largeur" value="<?php echo $oContent->getWidth_content(); ?>">
<input type="hidden" name="hauteur" value="<?php echo $oContent->getHeight_content(); ?>">

</ul>

<div align="center" id="editor">
<?php



if(!$_GET['init']==1){
	$_SESSION['HTMLcontent']='';
}

if (defined("DEF_FCK_VERSION") && DEF_FCK_VERSION == "ckeditor" ) { 
	
	$ck = "CKEDITOR.replace( 'FCKeditor1', {\n";
	$ck.= "customConfig : '/backoffice/cms/lib/ckeditor/config.php',";
	$ck.= "});";
			
			
	echo " 
	 
		<textarea cols=\"50\" id=\"FCKeditor1\" name=\"FCKeditor1\" rows=\"30\" width=\"500\" height=\"500\">".htmlFCKcompliant($composant['html'])."</textarea>
		<script>

			function init_ck () {  
				".$ck."
			}
			
			if (navigator.userAgent.toLowerCase().indexOf(\"chrome\") != -1) {
				
				setTimeout(\"init_ck()\", 1000); 
			}
			else {
				init_ck();
			}

		</script> 
		";

}
else {
	// Automatically calculates the editor base path based on the _samples directory.
	// This is usefull only for these samples. A real application should use something like this:
	$oFCKeditor->BasePath = '/backoffice/cms/lib/FCKeditor/' ;	// '/FCKeditor/' is the default value.
	$sBasePath = $_SERVER['PHP_SELF'] ;
	$sBasePath = '/backoffice/cms/lib/FCKeditor'.substr( $sBasePath, 0, strpos( $sBasePath, "backoffice/" ) ) ;
	
	$oFCKeditor = new FCKeditor('FCKeditor1') ;
	$oFCKeditor->BasePath	= $sBasePath ;
	
	
	$oFCKeditor->Value = htmlFCKcompliant($composant['html']);
	
	$oFCKeditor->Width  = '75%' ;
	$oFCKeditor->Height = '500' ;
	$oFCKeditor->Create() ;
}

?>
</div>
<div align="center"  id="editor2"><input name="Enregistrer" type="button" class="arbo" onClick="submit();" value="Enregistrer">
  <br />
  <br />
</div>

<div class="note_editeur">
<b>Note :</b> A cause de la barre d'outils, l'espace de composition n'a pas forcément la taille indiqué précédemment.
Les tailles seront appliquées définitivement à partir de l'étape suivante (preview).</span></div>
<input type="hidden" name="pageEditor" value="<?php echo $_SERVER['REQUEST_URI'];?>">
<input type="hidden" name="TYPEcontent" value="HTML">


</form>