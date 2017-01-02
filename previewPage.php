<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/*
	$Author: raphael $
	$Id: previewPage.php,v 1.1 2013-09-30 09:24:15 raphael Exp $

	$Log: previewPage.php,v $
	Revision 1.1  2013-09-30 09:24:15  raphael
	*** empty log message ***

	Revision 1.11  2013-03-01 10:28:04  pierre
	*** empty log message ***

	Revision 1.10  2012-07-31 14:24:48  pierre
	*** empty log message ***

	Revision 1.9  2008-11-28 15:34:43  pierre
	*** empty log message ***

	Revision 1.8  2008-07-16 10:25:35  pierre
	*** empty log message ***

	Revision 1.7  2008/01/25 14:37:22  pierre
	*** empty log message ***
	
	Revision 1.6  2007/11/15 18:08:49  pierre
	*** empty log message ***
	
	Revision 1.5  2007/11/06 15:33:25  remy
	*** empty log message ***
	
	Revision 1.5  2007/08/28 15:55:28  pierre
	*** empty log message ***
	
	Revision 1.4  2007/08/27 10:13:47  pierre
	*** empty log message ***
	
	Revision 1.3  2007/08/09 13:19:30  thao
	*** empty log message ***
	
	Revision 1.2  2007/08/08 15:47:18  pierre
	*** empty log message ***
	
	Revision 1.1  2007/08/08 14:26:20  thao
	*** empty log message ***
	
	Revision 1.3  2007/08/08 13:56:04  thao
	*** empty log message ***
	
	Revision 1.2  2007/03/20 11:05:21  pierre
	*** empty log message ***
	
	Revision 1.1.1.1  2006/01/25 15:14:32  pierre
	projet CCitron AWS 2006 Nouveau Website
	
	Revision 1.5  2005/11/14 16:35:02  pierre
	div, ze, et bf overflow: visible
	
	Revision 1.4  2005/11/09 14:32:36  pierre
	*** empty log message ***
	
	Revision 1.3  2005/11/07 14:36:11  sylvie
	*** empty log message ***
	
	Revision 1.2  2005/10/28 07:53:14  sylvie
	*** empty log message ***
	
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
	
	Revision 1.2  2004/01/20 15:16:38  ddinside
	mise à jour de plein de choses
	ajout de gabarit vie des quartiers
	eclatement gabarits par des includes pour contourner prob des flashs non finalisés
	
	Revision 1.1  2004/01/07 18:29:22  ddinside
	jout derniers fonctionnalites
	
	Revision 1.2  2003/11/26 13:08:42  ddinside
	nettoyage des fichiers temporaires commités par erreur
	ajout config spaw
	corrections bug menu et positionnement des divs
	
*/
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
 include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

	
	//$DIR_GABARITS = $_SERVER['DOCUMENT_ROOT']."/backoffice/cms/gabarits";
	$DIR_GABARITS = $_SERVER['DOCUMENT_ROOT']."/custom/gabarit/".$_SESSION["site_travail"];
	
	
$divArray = array();
$divArray=unserialize($_SESSION['page']);
$divArray=buildDivArrayPreview($divArray);
$buffer=1024;   // buffer de lecture de 1Ko
$gabarit=$DIR_GABARITS.'/gb_'.$divArray['gabarit'].'.php';
if ($divArray['gabarit'] == "init"){
	$gabarit=$_SERVER['DOCUMENT_ROOT']."/backoffice/cms/site/init.php";
}



$gabfilehandle = fopen($gabarit,'r') or (error_log("previewpage.php - soit lecture impossible dans $gabarit") && die('Erreur irrécupérable. Veuillez contacter l\'administrateur.'));

$tmpfile=$_SERVER['DOCUMENT_ROOT'].'/tmp/'.md5(uniqid(rand())).'.tmp.php';
dirExists("/tmp/");
$tmpfilehandle = fopen($tmpfile,'w') or (error_log("previewpage.php - soit ecriture impossible dans $tmpfile") && die('Erreur irrécupérable. Veuillez contacter l\'administrateur.'));


$stop=0;
while((!feof($gabfilehandle)) && !$stop){
	$tampon=fgets($gabfilehandle,$buffer);
	fputs($tmpfilehandle,$tampon);
	if(preg_match('/<\!--DEBUTCMS--\>/',$tampon))
		$stop=1;
}

$oCms_site = new Cms_site($_SESSION['IDSITE']);

$tampon = '<style type="text/css">
.space {
	position:absolute;
	width: '.$oCms_site->getWidthpage_site().'px;
	height: '.$oCms_site->getHeightpage_site().'px;
	overflow: '.divOverflow().';
	overflow-x: '.divOverflow().';
	overflow-y: '.divOverflow().';
	text-align: left;
}

.content {
	position:absolute;
	width: '.$oCms_site->getWidthpage_site().'px;
	height: '.$oCms_site->getHeightpage_site().'px;
	overflow: '.divOverflow().';
	overflow-x: '.divOverflow().';
	overflow-y: '.divOverflow().';
	text-align: left;
}
';
foreach($divArray as $k => $v) {
	if(is_array($v)){
		$tampon.='
.div'.$v['id'].'{
	position: absolute;
	overflow: visible;
	overflow-x: visible;
	overflow-y: visible;
	text-align: left;
	top:'.$v['top'].';
	left:'.$v['left'].';
	width:'.$v['width'].';
	height:'.$v['height'].';
	filter:'.$v['filter'].';
	-moz-opacity: '.$v['-moz-opacity'].';
	zIndex: '.$v['zIndex'].';
	visibility: visible;
}';
	}
}
$tampon.='
</style>

<div id="space" class="space">
<div id="content" class="content">';
foreach($divArray as $k => $v) {
	if(is_array($v)){
		$divString = '<div id="div'.$v['id'].'" class="div'.$v['id'].'">'.$v['content'].'</div>';
		$tampon.="$divString\n";
	}
}
$tampon.='</div>
</div>

';

fputs($tmpfilehandle,$tampon);

while(!feof($gabfilehandle)) {
	$tampon=fgets($gabfilehandle,$buffer);
	fputs($tmpfilehandle,$tampon);
}
fclose($tmpfilehandle);
include "$tmpfile";
unlink($tmpfile);

?>
