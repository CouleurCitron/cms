<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: multimediaEditor.php,v 1.2 2013-03-01 10:28:05 pierre Exp $
	$Author: pierre $
	
	$Log: multimediaEditor.php,v $
	Revision 1.2  2013-03-01 10:28:05  pierre
	*** empty log message ***

	Revision 1.1  2012-12-12 14:26:36  pierre
	*** empty log message ***

	Revision 1.10  2012-07-31 14:24:47  pierre
	*** empty log message ***

	Revision 1.9  2012-04-13 07:18:38  pierre
	*** empty log message ***

	Revision 1.8  2010-03-08 12:10:28  pierre
	syntaxe xhtml

	Revision 1.7  2009-09-24 08:53:11  pierre
	*** empty log message ***

	Revision 1.6  2008-11-27 18:47:37  pierre
	*** empty log message ***

	Revision 1.5  2008-11-27 11:35:37  pierre
	*** empty log message ***

	Revision 1.4  2008-11-06 12:03:52  pierre
	*** empty log message ***

	Revision 1.3  2008-07-16 10:48:36  pierre
	*** empty log message ***

	Revision 1.2  2007/11/15 18:08:49  pierre
	*** empty log message ***
	
	Revision 1.5  2007/11/06 15:33:25  remy
	*** empty log message ***
	
	Revision 1.1  2007/08/08 14:26:20  thao
	*** empty log message ***
	
	Revision 1.3  2007/08/08 14:16:14  thao
	*** empty log message ***
	
	Revision 1.2  2007/08/08 13:56:04  thao
	*** empty log message ***
	
	Revision 1.1.1.1  2006/01/25 15:14:32  pierre
	projet CCitron AWS 2006 Nouveau Website
	
	Revision 1.1.1.1  2005/10/24 13:37:05  pierre
	re import fusion espace v2 et ADW v2
	
	Revision 1.2  2005/10/21 10:46:46  sylvie
	*** empty log message ***
	
	Revision 1.1.1.1  2005/10/20 13:10:53  pierre
	Espace V2
	
	Revision 1.1.1.1  2005/04/18 13:53:29  pierre
	again
	
	Revision 1.1.1.1  2005/04/18 09:04:21  pierre
	oremip new
	
	Revision 1.1.1.1  2004/11/03 13:49:54  ddinside
	lancement du projet - import de adequat
	
	Revision 1.3  2004/06/16 15:23:19  ddinside
	inclusion corrections
	
	Revision 1.2  2004/04/26 08:07:09  melanie
	*** empty log message ***
	
	Revision 1.1.1.1  2004/04/01 09:20:30  ddinside
	Création du projet CMS Couleur Citron nom de code : tipunch
	
	Revision 1.3  2004/02/05 15:56:25  ddinside
	ajout fonctionnalite de suppression de pages
	ajout des styles dans spaw
	debuggauge prob du nom de fichier limite à 30 caracteres
	
	Revision 1.2  2004/01/20 15:16:38  ddinside
	mise à jour de plein de choses
	ajout de gabarit vie des quartiers
	eclatement gabarits par des includes pour contourner prob des flashs non finalisés
	
	Revision 1.1  2004/01/07 18:27:37  ddinside
	première mise à niveau pour plein de choses
	
	Revision 1.2  2003/11/26 14:25:09  ddinside
	activation du menu
	
*/


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

activateMenu('gestionbrique');

$id = $_GET['id'];


$composant = null;
if (strlen($id) > 0)
	$composant = getComposantById($id);


?><div class="arbo2"><b>Créer une brique</b></div><?php

	// a_voir melanie bandeau d'affichage à faire plus joli ?
	// dans le fichier selectSite.php
if (DEF_MENUS_MINISITES == "ON") print(putAfficheSite());

if(!strlen($_GET['type'])>0) {
	// premiere phase
	$nom = '';
	$larg = '';
	$long = '';
	$type='';
	if ($composant != null) {
		$nom = ' value="'.$composant['name'].'" ';
		$larg = ' value="'.$composant['width'].'" ';
		$long = ' value="'.$composant['height'].'" ';
		$type = ' value="'.$composant['type'].'" ';
		//echo "type :$type";
	}
?>
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
<form name="creation" action="<?php echo $_SERVER['PHP_SELF']; ?>?idSite=<?php echo $idSite; ?>" method="GET">
<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
<input type="hidden" name="minisite" value="<?php echo $_GET['minisite']; ?>">

<table width="369" border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
  <tr>
    <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="arbo">
        <tr valign="middle"  >
          <td height="28" colspan="2" nowrap background="../../backoffice/cms/img/fond_tab.gif"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td class="arbo"><strong>&nbsp;Cr&eacute;ation de la brique Multim&eacute;dia</strong></td>
              </tr>
          </table></td>
        </tr>
        <tr>
          <td colspan="2" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td align="right" style="vertical-align:middle">Type&nbsp;:&nbsp;</td>
          <td align="left"><select name="type" class="arbo">
            <option value="swf">Macromédia flash (.swf)</option>
            <option value="avi">Vidéo format AVI (.avi)</option>
          </select></td>
        </tr>
        <tr>
          <td colspan="2" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td align="right" style="vertical-align:middle">Nom&nbsp;:&nbsp;</td>
          <td align="left"><input name="nom" type="text" class="arbo" id="nom" size="30"  maxlength="30" <?php echo $nom; ?>></td>
        </tr>
        <tr>
          <td  colspan="2" align="center"><br />
              <input name="Suite (création) >>" type="submit" class="arbo" id="Suite (création) >>" value="Suite (cr&eacute;ation) >>" >
              <br />
              <br /></td>
        </tr>
    </table></td>
  </tr>
</table>
</form>
<?php	
} else {

// Chargement de la classe
require_once('cms-inc/lib/fileUpload/upload.class.php');

// Instanciation d'un nouvel objet "upload"
$Upload = new Upload();

// Pour ajouter des attributs aux champs de type file
$Upload-> FieldOptions = 'style="border-color:black;border-width:1px;"';

// Pour indiquer le nombre de champs désiré
$Upload-> Fields = 1;

// Initialisation du formulaire
$Upload-> InitForm();
?>

<form method="post" enctype="multipart/form-data" name="formulaire" id="formulaire" action="uploadMultimedia.php?id=<?php echo $_GET['id']; ?>">
<span class="arbo2"><strong>Upload du composant Multimedia <?php echo $_GET['nom']; ?>:</strong></span>
<?php
// Affichage du champ MAX_FILE_SIZE
print $Upload-> Field[0];

// Affichage du premier champ de type FILE
print $Upload-> Field[1] . '<br />';

1
?>
<br />
<input type="hidden" name="TYPEcontent" value="Multimédia" class="arbo">
<input type="hidden" name="largeur" value="" class="arbo">
<input type="hidden" name="hauteur" value="" class="arbo">
<input type="hidden" name="nom" value="<?php echo $_GET['nom']; ?>" class="arbo">
<input type="hidden" name="multimedia" value="<?php echo $_GET['type']; ?>" class="arbo">
<input type="hidden" name="idSite" value="<?php echo $idSite; ?>">

<input name="submit" type="submit" class="arbo" value="Envoyer">
</form>
<?php	
}
?>
