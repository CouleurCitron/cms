<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 
$Author: raphael $
$Revision: 1.1 $

$Log: pageModif.php,v $
Revision 1.1  2013-09-30 09:43:16  raphael
*** empty log message ***

Revision 1.1  2013-05-28 13:18:10  thao
*** empty log message ***

Revision 1.8  2013-03-01 10:28:04  pierre
*** empty log message ***

Revision 1.7  2012-07-31 14:24:48  pierre
*** empty log message ***

Revision 1.6  2009-09-24 08:53:11  pierre
*** empty log message ***

Revision 1.5  2008-11-28 14:09:55  pierre
*** empty log message ***

Revision 1.4  2008-08-28 08:38:12  thao
*** empty log message ***

Revision 1.3  2007/11/15 18:08:49  pierre
*** empty log message ***

Revision 1.4  2007/11/06 15:33:25  remy
*** empty log message ***

Revision 1.2  2007/08/28 15:55:28  pierre
*** empty log message ***

Revision 1.1  2007/08/08 14:26:20  thao
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

Revision 1.1.1.1  2004/04/01 09:20:27  ddinside
Création du projet CMS Couleur Citron nom de code : tipunch

Revision 1.2  2004/01/27 12:12:50  ddinside
application de dos2unix sur les scripts SQL
modification des scripts d'ajout pour correction bug lors d'une modif de page
ajout foncitonnalité de modification de page
ajout visu d'une page si créée

Revision 1.1  2004/01/20 15:16:38  ddinside
mise à jour de plein de choses
ajout de gabarit vie des quartiers
eclatement gabarits par des includes pour contourner prob des flashs non finalisés

Revision 1.2  2003/11/26 13:08:42  ddinside
nettoyage des fichiers temporaires commités par erreur
ajout config spaw
corrections bug menu et positionnement des divs

Revision 1.2  2003/10/21 12:18:05  ddinside
interface de placement des briques fonctionnel

Revision 1.1  2003/10/16 21:19:46  ddinside
suite dev gestio ndes composants
ajout librairies d'images
suppressions fichiers vi
ajout gabarit

Revision 1.2  2003/10/07 08:15:53  ddinside
ajout gestion de l'arborescence des composants

Revision 1.1  2003/09/25 14:55:16  ddinside
gestion de l'arborescene des composants : ajout
*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


if(strlen($_GET['id'])>0){
	$id = $_GET['id'];
	$divArray = getPageStructure($id);
	$_SESSION['divArray']=$divArray;
	$tmparray = array_keys($divArray);
	$array = array();
	foreach($tmparray as $k => $v) {
		if(is_int($v)) {
			$array[$k] = $v;
		}
	}
	$gabName = $divArray['gabarit'];
	$_SESSION[$gabName] = $array;


$sUrlModif = "/backoffice/cms/site/pageLiteEditor3.php";

?>
<script type="text/javascript">
	document.location="<?php echo $sUrlModif; ?>?id=<?php echo $_GET['id']?>&idGab=<?php echo $_GET['idGab']; ?>";
</script>
<?php
} else {
?>
<script type="text/javascript">
	document.location="/backoffice/";
</script>
<?php	
}
