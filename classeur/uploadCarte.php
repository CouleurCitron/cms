<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
//include_once('bo/cms/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbocarte.lib.php');

// Chargement de la classe
require_once('cms-inc/lib/fileUpload/upload.class.php');

activateMenu('gestioncarte');  //permet de dérouler le menu contextuellement

// Instanciation d'un nouvel objet "upload"
$Upload = new Upload();
/**
 * Création du formulaire
 **/
// Pour ne pas écraser un fichier existant
$Upload->  WriteMode  = '1';

 // Définition du répertoire de destination
$Upload-> DirUpload = $CMS_ROOT_CLASSEUR_UPLOADFILE."/";

echo $Upload-> DirUpload;

// Pour limiter la taille d'un  fichier (exprimée en ko)
$Upload-> MaxFilesize  = '10000';

/**
 * Gestion lors de la soumission du formulaire
 **/

if (!Empty($_POST['submit'])) {
    $Upload-> Execute();	// On lance la procédure d'upload
}


$taille=$_FILES['userfile']['size'][0];

?>
<br />
	<?php
	// Gestion erreur / succès
		if ($UploadError) {
			print 'Il y a eu une erreur :'; 
			var_dump($Upload-> GetError());
		} else {
			//print 'Upload effectué avec succès :';
			//pArray($Upload-> GetSummary());
?>
<link href="/css/bo.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style><br />
<br />
<br />
<form name="sendCarte" id="sendCarte" action="saveCarte.php" method="post">
  <input type="hidden" name="node_id" id="node_id" value="<?php echo $_POST['node_id']; ?>" />
  <input type="hidden" name="idnode" id="idnode" value="<?php echo $_POST['idnode']; ; ?>" />
  <input type="hidden" name="id" id="id" value="<?php echo $_POST['id']; ; ?>" />
  <input type="hidden" name="nom" id="nom" value="<?php echo stripslashes($_POST['nom']); ?>" />
  <input type="hidden" name="motscles" id="motscles" value="<?php echo stripslashes($_POST['motscles']); ?>" />
  <input type="hidden" name="description" id="description" value="<?php echo stripslashes($_POST['description']); ?>" />
  <input type="hidden" name="page" id="page" value="<?php echo stripslashes($_POST['page']); ?>" />
  <input type="hidden" name="poids" id="poids" value="<?php echo $taille; ?>" />
  <input type="hidden" name="dateetude" id="dateetude" value="<?php echo stripslashes($_POST['dateetude']); ?>" class="arbo" />
  <input type="hidden" name="datepub" id="datepub" value="<?php echo stripslashes($_POST['datepub']); ?>" class="arbo" />
  <input type="hidden" name="lieu" id="lieu" value="<?php echo stripslashes($_POST['lieu']); ?>" class="arbo" />

  <input type="hidden" name="chemin_relatif" id="chemin_relatif" value="<?php echo $Upload-> Infos[1]['nom']; ?>" />

<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
<tr>
 <td><span class="arbo"><strong>Le fichier a été transféré avec succès.<br />
   <br />
Pour ajouter cette carte au classeur, cliquez sur suite.</strong></span><br /></td>
</tr>
<tr bgcolor="D2D2D2">
 <td align="center"><input name="Suite (stocker la carte) &gt;&gt;" type="button" class="arbo" onclick="submit();" value="Suite (stocker la carte) &gt;&gt;" /></td>
</tr>
</table>
</form>
<script language="JavaScript" type="text/javascript">
document.sendCarte.submit();
</script>
<?php
}
?>