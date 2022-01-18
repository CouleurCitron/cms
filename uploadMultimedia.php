<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

activateMenu('gestionbrique');

function pArray($array) {
	print '<pre style="background:#EEEEEE">';
	
	if ($array[1]['nom']!="") {
	echo "Nom :";
	echo $array[1]['nom'];
	echo "<br />Poids :";
	echo $array[1]['poids'];
	echo "<br />Type :";
	switch ($_POST['multimedia']) {
		case "swf" : 
		echo "Fichier Flash";
		break;
		case "avi" :
		echo "Fichier AVI";
	}
	}
	else echo"Le fichier n'est pas conforme au type sélectionné";
	print '</pre>';
}

// Chargement de la classe
require_once('cms-inc/lib/fileUpload/upload.class.php');

// Instanciation d'un nouvel objet "upload"
$Upload = new Upload();
/**
 * Création du formulaire
 **/
// Pour ne pas écraser un fichier existant
$Upload->  WriteMode  = '1';

 // Définition du répertoire de destination
$Upload-> DirUpload    = '../../medias/';


// Pour limiter la taille d'un  fichier (exprimée en ko)
$Upload-> MaxFilesize  = '10000';



/**
 * Gestion lors de la soumission du formulaire
 **/

if (!Empty($_POST['submit'])) {
	
    $ext=$_POST['multimedia'];
    // Pour filtrer les fichiers par extension
	switch ($ext) {
		case "swf" : 
	 		$Upload-> Extension = '.swf';
			break;
	 	case "avi" : 
	 		$Upload-> Extension = '.avi';
			break;
		
	}
    //$Upload-> Extension = '.swf;.avi';
    
   
    
    // On lance la procédure d'upload
    $Upload-> Execute();
    
    
}

if ($Upload-> _ext==".".$_POST['multimedia']) {

$DIR_MEDIA = $_SERVER['DOCUMENT_ROOT'].'/medias/';
$largeur="";
$hauteur="";
if($_POST['multimedia']=="swf") {
	$infos = getimagesize($DIR_MEDIA.$Upload-> Infos[1]['nom']);
	$largeur=$infos[0];
	$hauteur=$infos[1];
}
?>
<center>
<table border="0" cellpadding="0" cellspacing="0" class="arbo">
  <tr>
    <td>
	<?php 		
	if ($Upload-> Extension==".swf" ) { 
	?>
	<object type="application/x-shockwave-flash" data="/medias/<?php echo $Upload-> Infos[1]['nom'].'?nodeid=&' ?>&" width="'.$largeur.'" height="'.$hauteur.'" border="0"><param name=movie value="/medias/<?php echo $Upload-> Infos[1]['nom'].'?nodeid=&' ?>&"><param name="quality" value="high"></object>


	<?php
			}
		?> 
	</td>
  </tr>
</table>
</center>
<?php
}
?>
<br />
	<?php
	// Gestion erreur / succès
		if ($UploadError) {
			print 'Il y a eu une erreur :'; 
			pArray($Upload-> GetError());
		} else {
			//print 'Upload effectué avec succès :';
			//pArray($Upload-> GetSummary());
$html = '
<table width="'.($largeur-(-1)).'" height="'.($hauteur-(-1)).'" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>';
if ($Upload-> Extension==".swf" ) { 
	$html.='
	<object type="application/x-shockwave-flash" data="/medias/'.$Upload-> Infos[1]['nom'].'?nodeid=<?php echo $nodeId; ?>&" width="'.$largeur.'" height="'.$hauteur.'" border="0"><param name=movie value="/medias/'.$Upload-> Infos[1]['nom'].'?nodeid=<?php echo $nodeId; ?>&"><param name="quality" value="high"></object>';

}

$html.='
	</td>
  </tr>
</table>
';
?>
    <span class="arbo"><strong>Fichier transféré.</strong></span><br />
<form name="sendMedia" action="briques/saveComposant.php" method="post">

<input type="hidden" name="NAMEcontent" value="<?php echo $_POST['nom']; ?>">
<input type="hidden" name="HTMLcontent" value='<?php echo $html; ?>'>
<input type="hidden" name="TYPEcontent" value="Multimedia">
<input type="hidden" name="idSite" value="<?php echo $idSite; ?>">

<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
<tr>
 <td>&nbsp;Largeur :&nbsp;</td>
 <td>&nbsp;<input name="WIDTHcontent" type="text" class="arbo" value="<?php echo ($largeur-(-9)); ?>"></td>
</tr>
<tr>
 <td>&nbsp;Hauteur :&nbsp;</td>
 <td>&nbsp;<input name="HEIGHTcontent" type="text" class="arbo" value="<?php echo ($hauteur-(-9)); ?>">
</tr>
<tr bgcolor="D2D2D2">
 <td colspan="2" align="center"><input name="Suite (stocker la brique) >>" type="button" class="arbo" onClick="submit();" value="Suite (stocker la brique) >>"></td>
</tr>
</table>
</form>
<?php
}
?>