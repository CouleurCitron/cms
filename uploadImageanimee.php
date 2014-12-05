<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/utils/graphic.lib.php');
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
	switch ($_POST['imageanimee']) {
		case "imageanimee_blur" : 
		echo "Image Animée Net/Flou";
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
$Upload-> DirUpload    = '../../custom/medias/'.$_SESSION['rep_travail'].'/';


// Pour limiter la taille d'un  fichier (exprimée en ko)
$Upload-> MaxFilesize  = '10000';



/**
 * Gestion lors de la soumission du formulaire
 **/

if (!Empty($_POST['submit'])) {
	
	$Upload-> Extension = '.jpg';
    // On lance la procédure d'upload
    $Upload-> Execute();   
    
}

if ($Upload-> _ext==".jpg") {

$DIR_MEDIA = $_SERVER['DOCUMENT_ROOT'].'/custom/medias/'.$_SESSION['rep_travail'].'/';
$URL_MEDIA = '/custom/medias/'.$_SESSION['rep_travail'].'/';
$largeur="";
$hauteur="";
		
	// copier le swf de l'effet
	// le nom du swf a prendre  est $_POST['imageanimee'] plus .swf
	$file = "./imageanimee/".$_POST['imageanimee'].".swf";
	$newImageanimee = $DIR_MEDIA.substr($Upload-> Infos[1]['nom'],0,-4).".swf";

	if (!copy($file, $newImageanimee)) {
	   echo "failed to copy $file...\n";
	}
	$infos = getimagesize($newImageanimee);
	$largeur=$infos[0];
	$hauteur=$infos[1];
	
	// lancer le traitement d'image
	echo "<span class='eta'>Traitement graphique de ".$Upload-> Infos[1]['nom']." en cours</span>";
	$im = imagecreatefromjpeg($DIR_MEDIA.$Upload-> Infos[1]['nom']);
	blur ($im, 2);
	imagejpeg($im, str_replace(".jpg", "_fx.jpg", $DIR_MEDIA.$Upload-> Infos[1]['nom']));
	
	// image sans effet, set à non progressive
	nonProgressiveJPEG($DIR_MEDIA.$Upload-> Infos[1]['nom']);
	

?><style type="text/css">
<!--
.eta {
	display: none;
}
-->
</style>
<center> 
<?php
	// dans le fichier selectSite.php
if (DEF_MENUS_MINISITES == "ON") print(putAfficheSite());
?>
<table border="0" cellpadding="0" cellspacing="0" class="arbo">
  <tr>
    <td>
	
	<script type="text/javascript">
AC_AX_RunContent( 'width','<?php echo $largeur; ?>','height','<?php echo $hauteur; ?>','border','0','src','<?php echo $URL_MEDIA.substr($Upload-> Infos[1]['nom'],0,-4).".swf" ?>','wmode','opaque','quality','high','type','application/x-shockwave-flash','movie','<?php echo $URL_MEDIA.substr($Upload-> Infos[1]['nom'],0,-4).".swf" ?>' ); //end AC code
</script><noscript><object width="<?php echo $largeur; ?>" height="<?php echo $hauteur; ?>" border="0"> 
  	<param name="movie" value="<?php echo $URL_MEDIA.substr($Upload-> Infos[1]['nom'],0,-4).".swf" ?>"> 
  	<param name="quality" value="high">
	<param name="wmode" value="opaque">
  	<embed src="<?php echo $URL_MEDIA.substr($Upload-> Infos[1]['nom'],0,-4).".swf" ?>" wmode="opaque" quality="high" border="0" width="<?php echo $largeur; ?>" height="<?php echo $hauteur; ?>" type="application/x-shockwave-flash" >
  	</embed> 	
 	</object
	></noscript></td>
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

	$html.='
	<object width="'.$largeur.'" height="'.$hauteur.'" border="0"> 
  	<param name="movie" value="'.$URL_MEDIA.substr($Upload-> Infos[1]['nom'],0,-4).'.swf'.'"> 
  	<param name="quality" value="high">
	<param name="wmode" value="opaque">';

  	$html.='
	<embed src="'.$URL_MEDIA.substr($Upload-> Infos[1]['nom'],0,-4).'.swf'.'" wmode="opaque" quality="high"  border="0" width="'.$largeur.'" height="'.$hauteur.'" type="application/x-shockwave-flash" >
  	</embed>';

 	$html.='
	</object>';

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
<input type="hidden" name="TYPEcontent" value="Image Animée">
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