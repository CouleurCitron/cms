<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/*
	$Author: pierre $
	$Id: pageMenu.php,v 1.7 2013-03-25 14:04:09 pierre Exp $

	$Log: pageMenu.php,v $
	Revision 1.7  2013-03-25 14:04:09  pierre
	*** empty log message ***

	Revision 1.6  2013-03-01 10:28:04  pierre
	*** empty log message ***

	Revision 1.5  2012-07-31 14:24:48  pierre
	*** empty log message ***

	Revision 1.4  2010-03-08 12:10:28  pierre
	syntaxe xhtml

	Revision 1.3  2008-04-21 12:17:02  pierre
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
	
	Revision 1.2  2005/10/24 15:10:07  pierre
	arbo.lib.php >> arbominisite.lib.php
	
	Revision 1.1.1.1  2005/10/24 13:37:05  pierre
	re import fusion espace v2 et ADW v2
	
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
	
	Revision 1.1.1.1  2004/04/01 09:20:27  ddinside
	Création du projet CMS Couleur Citron nom de code : tipunch
	
	Revision 1.1  2004/01/20 15:16:38  ddinside
	mise à jour de plein de choses
	ajout de gabarit vie des quartiers
	eclatement gabarits par des includes pour contourner prob des flashs non finalisés
	
	Revision 1.2  2003/11/26 13:08:42  ddinside
	nettoyage des fichiers temporaires commités par erreur
	ajout config spaw
	corrections bug menu et positionnement des divs
	
*/
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
	activateMenu('gestionpage');  //permet de dérouler le menu contextuellement
	
	$DIR_GABARITS = $_SERVER['DOCUMENT_ROOT']."/backoffice/cms/gabarits";
?><link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style><span class="arbo2"><strong>Processus de création d'une page - Etape 1</strong></span><br />
<br />

<span class="arbo">Choix du gabarit à utiliser :<br />
<br />

<?php
$list_gab = array();
if(dirExists($DIR_GABARITS)) {
	
	// listons les gabarits
	$nbf=0;
	if($hdir = opendir($DIR_GABARITS)) {
		while ($file=readdir($hdir)) {
			if(preg_match('/^gb_.+\.php$/',$file)) {
				$gab_name = $file;
				$gab_name = preg_replace('/^gb_/','',$gab_name);
				$gab_name = preg_replace('/\.php$/','',$gab_name);
				$tmparray = array(
					'name' => $gab_name,
					'file' => $file
				);
				array_push($list_gab,$tmparray);
				$nbf++;
			}
		}
	 } else {
		error_log("Impossible de lire le répertoire de gabarits");
	 }
	 if($nbf>0) {
?>
</span>
<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
<?php
		foreach($list_gab as $k => $fileObj) {
?>
   <tr>
    <ul><td><li class="arbo">Gabarit <?php echo $fileObj['name']; ?></li></td></ul>
    <td bgcolor="D2D2D2" class="arbo">&nbsp;<a href="pageEditor2.php?name=<?php echo $fileObj['name']; ?>&file=<?php echo $fileObj['file']; ?>" class="arbo">choisir</a>&nbsp;</td>
    <td bgcolor="EEEEEE" class="arbo">&nbsp;<a href="gabarits/<?php echo $fileObj['file']; ?>" target="preview" class="arbo">visualiser</a></td>
   </tr>
<?php
		}
?>
</table>
<span class="arbo">
<?php
	 } else {
?>
Aucun élément à afficher.
<?php
	 }
	
} else {
	// pas de repertoire de gabarits...
	error_log("Répertoire de gabarits absent (".$dir_gabarits."). Veuillez le créer");
?>
<b>Erreur !!!!</b>&nbsp;les gabarits sont actuellement indosponibles. Veuillez réessayer plus tard.<br />
Si le problème persiste, veuillez contacter l'administrateur.
<?php
}
?>
<br />
<br />
<strong><small>Choisissez le gabarit à utiliser pour continuer le processus.</small></strong>
</span>
<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/append.php');
?>