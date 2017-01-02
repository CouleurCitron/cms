<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: contentEditor.fck.php,v 1.1 2013-09-30 09:34:07 raphael Exp $
	$Author: raphael $
	
	$Log: contentEditor.fck.php,v $
	Revision 1.1  2013-09-30 09:34:07  raphael
	*** empty log message ***

	Revision 1.2  2013-03-01 10:28:05  pierre
	*** empty log message ***

	Revision 1.1  2012-12-12 14:26:36  pierre
	*** empty log message ***

	Revision 1.15  2012-07-31 14:24:47  pierre
	*** empty log message ***

	Revision 1.14  2012-05-09 12:09:01  pierre
	*** empty log message ***

	Revision 1.13  2010-03-08 12:10:27  pierre
	syntaxe xhtml

	Revision 1.12  2009-09-24 08:53:11  pierre
	*** empty log message ***

	Revision 1.11  2009-03-02 17:41:44  virginie
	*** empty log message ***

	Revision 1.10  2008-11-28 15:14:19  pierre
	*** empty log message ***

	Revision 1.9  2008-11-28 14:09:55  pierre
	*** empty log message ***

	Revision 1.8  2008-11-28 14:03:28  pierre
	*** empty log message ***

	Revision 1.7  2008-11-27 11:35:37  pierre
	*** empty log message ***

	Revision 1.6  2008-11-06 12:03:52  pierre
	*** empty log message ***

	Revision 1.5  2008-07-16 10:48:36  pierre
	*** empty log message ***

	Revision 1.4  2008/07/16 08:42:48  pierre
	*** empty log message ***
	
	Revision 1.3  2007/11/15 18:08:49  pierre
	*** empty log message ***
	
	Revision 1.5  2007/11/06 15:33:25  remy
	*** empty log message ***
	
	Revision 1.2  2007/08/28 15:55:28  pierre
	*** empty log message ***
	
	Revision 1.1  2007/08/08 14:26:20  thao
	*** empty log message ***
	
	Revision 1.3  2007/08/08 14:16:14  thao
	*** empty log message ***
	
	Revision 1.2  2007/08/08 13:56:04  thao
	*** empty log message ***
	
	Revision 1.1.1.1  2006/01/25 15:14:32  pierre
	projet CCitron AWS 2006 Nouveau Website
	
	Revision 1.2  2005/10/28 14:25:35  pierre
	FCK
	
	Revision 1.1  2005/10/28 09:41:49  pierre
	*** empty log message ***
	
	Revision 1.3  2005/10/27 15:45:24  pierre
	*** empty log message ***
	
	Revision 1.2  2005/10/25 15:55:03  melanie
	*** empty log message ***
	
	Revision 1.1.1.1  2005/10/24 13:37:05  pierre
	re import fusion espace v2 et ADW v2
	
	Revision 1.4  2005/10/21 10:46:46  sylvie
	*** empty log message ***
	
	Revision 1.3  2005/10/21 10:24:56  sylvie
	*** empty log message ***
	
	Revision 1.2  2005/10/21 09:37:25  sylvie
	*** empty log message ***
	
	Revision 1.1.1.1  2005/10/20 13:10:53  pierre
	Espace V2
	
	Revision 1.5  2005/06/02 16:27:16  michael
	pb comment
	
	Revision 1.4  2005/06/02 13:47:58  michael
	Correction BUG IE
	ce petit idiot rajoute /lib/spaw.1.rc1 aux liens qui n'ont pas de chemin
	(des liens relatifs vers une page dans le même dossier)
	On supprime donc sauvagement cette chaine /lib/spaw si elle est trouvé dans un lien href
	
	Revision 1.3  2005/05/17 15:48:21  michael
	Moulinette
	
	Revision 1.2  2005/05/13 09:54:41  michael
	Ajout de spaw 1.1rc1
	
	Revision 1.1.1.1  2005/04/18 13:53:29  pierre
	again
	
	Revision 1.1.1.1  2005/04/18 09:04:21  pierre
	oremip new
	
	Revision 1.1.1.1  2004/11/03 13:49:54  ddinside
	lancement du projet - import de adequat
	
	Revision 1.4  2004/06/18 14:10:21  ddinside
	corrections diverses en vu de la demo prevention routiere
	
	Revision 1.3  2004/06/16 15:23:19  ddinside
	inclusion corrections
	
	Revision 1.2  2004/04/26 08:07:09  melanie
	*** empty log message ***
	
	Revision 1.1.1.1  2004/04/01 09:20:27  ddinside
	Création du projet CMS Couleur Citron nom de code : tipunch
	
	Revision 1.5  2004/02/12 15:56:16  ddinside
	mise à jour plein de choses en fait, mais je sais plus quoi parce que ça fait longtemps que je l'avais pas fait.
	Mea Culpa...
	
	Revision 1.4  2004/01/20 15:16:38  ddinside
	mise à jour de plein de choses
	ajout de gabarit vie des quartiers
	eclatement gabarits par des includes pour contourner prob des flashs non finalisés
	
	Revision 1.3  2004/01/07 18:27:37  ddinside
	première mise à niveau pour plein de choses
	
	Revision 1.2  2003/11/26 14:25:09  ddinside
	activation du menu
	
*/


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once 'cms-inc/msohtml_lib.php' ;
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

activateMenu('gestionbrique');


$id = $_GET['id'];


$composant = null;
if (strlen($id) > 0) {
	$composant = getComposantById($id);
	$oContent = new Cms_content($id);
} else $oContent = new Cms_content();

?><div class="arbo2"><b>Créer une brique</b></div><?php

// dans le fichier selectSite.php
if (DEF_MENUS_MINISITES == "ON") print(putAfficheSite());

if((!(strlen($_GET['l'])>0 && strlen($_GET['h'])>0)) && !strlen($_POST['FCKeditor1'])>0) {
	// premiere phase
	$nom = '';
	$larg = '';
	$long = '';
	if ($composant != null) {
		$nom = ' value="'.$composant['name'].'" ';
		$larg = ' value="'.$composant['width'].'" ';
		$long = ' value="'.$composant['height'].'" ';
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
//--------------------
// site
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

// objet site sur lequel on est positionné
$oSite = new Cms_site($idSite);
$oSite->getSite();
//--------------------
?>
<form name="creation" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">

<input type="hidden" name="minisite" value="<?php echo $_GET['minisite']; ?>">
<input type="hidden" name="id" value="<?php if (strlen($_GET['id'])>0) echo $_GET['id']; ?>">

<input type="hidden" name="TYPEcontent" value="<?php echo $oContent->getType_content() ?>">

<table width="369" border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
<tr>
<td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="arbo">
 <tr valign="middle"  >
   <td height="28" colspan="2" nowrap background="../../backoffice/cms/img/fond_tab.gif"><table width="100%" border="0" cellspacing="0" cellpadding="5">
     <tr>
       <td class="arbo"><strong>&nbsp;Cr&eacute;ation du composant HTML</strong></td>
     </tr>
   </table> </td>
   </tr>
 <tr>
   <td colspan="2" align="right">&nbsp;</td>
   </tr>
 <tr>
  <td align="right" style="vertical-align:middle">Nom&nbsp;:&nbsp;</td>
  <td align="left"><input name="nom" type="text" class="arbo" size="50"  maxlength="50" <?php echo $nom; ?>></td>
 </tr>
 <tr>
   <td colspan="2" align="right">&nbsp;</td>
   </tr>
 <tr>
  <td align="right" style="vertical-align:middle">Largeur&nbsp;:&nbsp;</td>
  <td align="left"><input name="l" type="text" class="arbo" size="4" maxlength="4" <?php echo $larg; ?>>&nbsp;pixels</td>
 </tr>
 <tr>
   <td colspan="2" align="right">&nbsp;</td>
   </tr>
 <tr>
  <td align="right" style="vertical-align:middle">Hauteur&nbsp;:&nbsp;</td>
  <td align="left"><input name="h" type="text" class="arbo" size="4" maxlength="4" <?php echo $long; ?>>&nbsp;pixels</td>
 </tr>
 <tr>
  <td  colspan="2" align="center"><br />    <input name="suite" type="submit" class="arbo" value="Suite (edition) >>">
    <br />
    <br /></td>
 </tr>
</table></td>
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
	
	
	$_SESSION['HTMLcontent'] = $htmlContent;
	
?>

<script src="/backoffice/cms/js/fojsutils.js">

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
<form name="previewHTML" action="saveComposant.php?idSite=<?php echo $idSite; ?>&minisite=<?php echo $_GET['minisite']; ?>&id=<?php if (strlen($_GET['id'])>0) echo $_GET['id']; ?>" method="post">
<input type="hidden" name="TYPEcontent" value="<?php echo $_POST['TYPEcontent'];?>">
<input type="hidden" name="WIDTHcontent" value="<?php echo $_POST['largeur']; ?>">
<input type="hidden" name="HEIGHTcontent" value="<?php echo $_POST['hauteur']; ?>">
<input type="hidden" name="NAMEcontent" value="<?php echo $_POST['nom']; ?>">
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
if (!ereg('/$', $HTTP_SERVER_VARS['DOCUMENT_ROOT']))
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
<li>type composant : <?php echo $TYPEcontent; ?></li>
<li>nom : <?php echo $_GET['nom']; ?></li>
<li>largeur : <?php echo $_GET['l']; ?></li>
<li>hauteur : <?php echo $_GET['h']; ?></li>
</ul>

<div align="center">
<?php
if(!$_GET['init']==1){
	$_SESSION['HTMLcontent']='';
}

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
$oFCKeditor->Create() ;
?>
</div>
<div align="center"><!--<input name="Suite (preview) >>" type="button" class="arbo" onClick="if(SPAW_Clean_HTML_src('HTMLcontent',this)) submit();" value="Suite (preview) >>">--><input name="Suite (preview) >>" type="button" class="arbo" onClick="submit();" value="Suite (preview) >>">
  <br />
  <br />
</div>
<div class="note_editeur">
<b>Note :</b> A cause de la barre d'outils, l'espace de composition n'a pas forcément la taille indiqué précédemment.
Les tailles seront appliquées définitivement à partir de l'étape suivante (preview).</span></div>
<input type="hidden" name="pageEditor" value="<?php echo $_SERVER['REQUEST_URI'];?>">
<input type="hidden" name="TYPEcontent" value="<?php echo $TYPEcontent;?>">
<input type="hidden" name="largeur" value="<?php echo $_GET['l']; ?>">
<input type="hidden" name="hauteur" value="<?php echo $_GET['h']; ?>">
<input type="hidden" name="nom" value="<?php echo $_GET['nom']; ?>">
<input type="hidden" name="mode_page" value="preview">
</form>
<?php
	}
}
?>