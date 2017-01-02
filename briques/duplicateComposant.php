<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: duplicateComposant.php,v 1.1 2013-09-30 09:34:14 raphael Exp $
	$Author: raphael $
	
	$Log: duplicateComposant.php,v $
	Revision 1.1  2013-09-30 09:34:14  raphael
	*** empty log message ***

	Revision 1.2  2013-03-01 10:28:05  pierre
	*** empty log message ***

	Revision 1.1  2012-12-12 14:26:36  pierre
	*** empty log message ***

	Revision 1.10  2012-07-31 14:24:47  pierre
	*** empty log message ***

	Revision 1.9  2012-05-09 12:09:01  pierre
	*** empty log message ***

	Revision 1.8  2010-03-08 12:10:28  pierre
	syntaxe xhtml

	Revision 1.7  2009-09-24 08:53:11  pierre
	*** empty log message ***

	Revision 1.6  2008-12-19 15:39:55  thao
	*** empty log message ***

	Revision 1.5  2008-07-16 10:48:36  pierre
	*** empty log message ***

	Revision 1.4  2008/07/16 08:42:48  pierre
	*** empty log message ***
	
	Revision 1.3  2007/11/15 18:08:49  pierre
	*** empty log message ***
	
	Revision 1.6  2007/11/06 15:33:25  remy
	*** empty log message ***
	
	Revision 1.2  2007/08/28 15:55:28  pierre
	*** empty log message ***
	
	Revision 1.1  2007/08/08 14:26:20  thao
	*** empty log message ***
	
	Revision 1.4  2007/08/08 14:16:14  thao
	*** empty log message ***
	
	Revision 1.3  2007/08/08 13:56:04  thao
	*** empty log message ***
	
	Revision 1.2  2007/08/07 13:16:03  thao
	*** empty log message ***
	
	Revision 1.1  2006/12/15 12:30:11  pierre
	*** empty log message ***
	
	Revision 1.1.1.1  2006/01/25 15:14:32  pierre
	projet CCitron AWS 2006 Nouveau Website
	
	Revision 1.4  2005/11/04 14:51:17  pierre
	*** empty log message ***
	
	Revision 1.2  2005/10/28 07:53:14  sylvie
	*** empty log message ***
	
	Revision 1.1.1.1  2005/10/20 13:10:53  pierre
	Espace V2
	
	Revision 1.2  2005/05/18 08:03:22  michael
	Application des modifs faites sur contentEditor.php
	
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
	
	Revision 1.1.1.1  2004/04/01 09:20:27  ddinside
	Création du projet CMS Couleur Citron nom de code : tipunch
	
	Revision 1.1  2004/01/07 18:27:37  ddinside
	première mise à niveau pour plein de choses
	
	Revision 1.2  2003/11/26 14:25:09  ddinside
	activation du menu
	
*/


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once 'cms-inc/msohtml_lib.php' ;

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


activateMenu('gestioncontenu');

$id = $_GET['id'];
$composant = null;
if (strlen($id) > 0)
	$composant = getComposantById($id);
	

//--------------------
// site
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

if ($idSite == "") die ("Appel incorrect de la fonctionnalité");
//--------------------

if((!(strlen($_GET['l'])>0 && strlen($_GET['h'])>0)) && !strlen($_POST['FCKeditor1'])>0) {
	// premiere phase
	$nom = '';
	$larg = '';
	$long = '';
	if ($composant != null) {
		$nom = ' value="nouveau '.$composant['name'].'" ';
		$larg = ' value="'.$composant['width'].'" ';
		$long = ' value="'.$composant['height'].'" ';

	}
?>
<link href="/backoffice/cms/css/bo.css" rel="stylesheet" type="text/css">

<span class="arbo2"><strong>Duplication du composant HTML :</strong></span>
<br />
<form name="creation" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
<input type="hidden" name="idSite" value="<?php echo $idSite; ?>">
<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
 <tr bgcolor="E6E6E6">
  <td align="right">Nom&nbsp;:&nbsp;</td>
  <td align="left"><input name="nom" type="text" class="arbo" size="30"  maxlength="30" <?php echo $nom; ?>></td>
 </tr>
 <tr bgcolor="EEEEEE">
  <td align="right">Largeur&nbsp;:&nbsp;</td>
  <td align="left"><input name="l" type="text" class="arbo" size="4" maxlength="4" <?php echo $larg; ?>>&nbsp;pixels</td>
 </tr>
 <tr bgcolor="E6E6E6">
  <td align="right">Hauteur&nbsp;:&nbsp;</td>
  <td align="left"><input name="h" type="text" class="arbo" size="4" maxlength="4" <?php echo $long; ?>>&nbsp;pixels</td>
 </tr>
 <tr bgcolor="D2D2D2">
  <td  colspan="2" align="center"><input name="Suite (création) >>" type="submit" class="arbo" value="Suite (création) >>"></td>
 </tr>
</table>
</form>
<?php	
} else {
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
	$htmlContentForm= preg_replace("/\\\`/",'`',$htmlContentForm);
	
	$_SESSION['HTMLcontent'] = $htmlContent;
?>
<span class="arbo2"><strong>Aperçu du composant HTML <?php echo $_POST['nom']; ?>:</strong></span>
<form name="previewHTML" action="saveComposant.php" method="post">
<input type="hidden" name="TYPEcontent" value="<?php echo $_POST['TYPEcontent'];?>">
<input type="hidden" name="WIDTHcontent" value="<?php echo $_POST['largeur']; ?>">
<input type="hidden" name="HEIGHTcontent" value="<?php echo $_POST['hauteur']; ?>">
<input type="hidden" name="NAMEcontent" value="<?php echo $_POST['nom']; ?>">
<input type="hidden" name="HTMLcontent" value='<?php echo $htmlContentForm; ?>'>
<input type="hidden" name="idSite" value="<?php echo $idSite; ?>">

<hr width="<?php echo $_POST['largeur']; ?>" align="left" class="arbo2">
<div style="width: <?php echo $_POST['largeur']; ?>px; height: <?php echo $_POST['hauteur']; ?>px; background-color: #ffffff; overflow: auto;">
<?php 
$htmlContent= preg_replace("/\\\'/",'`',$htmlContent);
$htmlContent = preg_replace('/\<form/i','<uniform',$htmlContent);
$htmlContent = preg_replace('/\<\/form\>/i','</uniform>',$htmlContent);

echo $htmlContent; ?>
</div>
<hr width="<?php echo $_POST['largeur']; ?>" align="left" class="arbo2">
<span class="arbo"><small><u><strong>Dimensions&nbsp;:</strong></u>&nbsp;<?php echo $_POST['largeur']; ?> x <?php echo $_POST['hauteur']; ?>&nbsp;pixels</small>
</span><br />
<br />
<div>
<input name="<< Retour (modifier)" type="button" class="arbo" onClick="javascript:window.location='<?php echo $_SERVER['PHP_SELF']."?l=".$_POST['largeur']."&h=".$_POST['hauteur']."&init=1&nom=".$_POST['nom'] ;?>';" value="<< Retour (modifier)">
&nbsp;&nbsp;&nbsp;&nbsp;
<input name="Suite (enregistrer) >>" type="submit" class="arbo" value="Suite (enregistrer) >>">
</div>
</form>
<?php
} else {
	// on est en mode conception
// this part determines the physical root of your website
// it's up to you how to do this
if (!ereg('/$', $HTTP_SERVER_VARS['DOCUMENT_ROOT']))
  $_root = $HTTP_SERVER_VARS['DOCUMENT_ROOT'].'/';
else
  $_root = $HTTP_SERVER_VARS['DOCUMENT_ROOT'];

define('DR', $_root);
unset($_root);

include("backoffice/cms/lib/FCKeditor/fckeditor.php") ;
?>
<style type="text/css">
  pre {
    background : #cccccc; 
    padding : 5 5 5 5;
  }
</style>
<span class="arbo2"><strong>Edition du composant HTML <?php echo $_GET['nom']; ?>:</strong></span>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?idSite=<?php echo $idSite; ?>&minisite=<?php echo $_GET['minisite']; ?>&nom=<?php echo $_GET['nom']; ?>&id=<?php if (strlen($_GET['id'])>0) echo $_GET['id']; ?>" method="post">
<input type="hidden" name="idSite" value="<?php echo $idSite; ?>">

<span class="arbo">

<u><strong>Caractéristiques</strong></u>
<ul>
<li>type composant : HTML</li>
<li>nom : <?php echo $_GET['nom']; ?></li>
<li>largeur : <?php echo $_GET['l']; ?></li>
<li>hauteur : <?php echo $_GET['h']; ?></li>
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
$oFCKeditor->BasePath = '/backoffice/cms/lib/FCKeditor/' ;	// '/FCKeditor/' is the default value.
$sBasePath = $_SERVER['PHP_SELF'] ;
$sBasePath = '/backoffice/cms/lib/FCKeditor'.substr( $sBasePath, 0, strpos( $sBasePath, "backoffice/" ) ) ;

$oFCKeditor = new FCKeditor('FCKeditor1') ;
$oFCKeditor->BasePath	= $sBasePath ;

$oFCKeditor->Value = htmlFCKcompliant($composant['html']);

$oFCKeditor->Width  = '75%' ;
$oFCKeditor->Height = '500' ;
$oFCKeditor->Create() ;
?>
</div>
<div align="center"><input name="Suite (preview) >>" type="submit" class="arbo" value="Suite (preview) >>">
  <br />
  <br />
</div>

<b><u>Note :</u></b> A cause de la barre d'outils, l'espace de composition n'a pas forcément la taille indiqué précédemment.
Les tailles seront appliquées définitivement à partir de l'étape suivante (preview).</span>
<input type="hidden" name="TYPEcontent" value="HTML">
<input type="hidden" name="largeur" value="<?php echo $_GET['l']; ?>">
<input type="hidden" name="hauteur" value="<?php echo $_GET['h']; ?>">
<input type="hidden" name="nom" value="<?php echo $_GET['nom']; ?>">
<input type="hidden" name="idSite" value="<?php echo $idSite; ?>">
<input type="hidden" name="mode_page" value="preview">
	
</form>
<?php
	}
}
?>
