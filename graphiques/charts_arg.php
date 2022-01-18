<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
header('Content-type: text/xml; charset=utf-8');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
// inclus le bon fichier de donnes suivant l'ID pass en paramtre
// ID est celui de la brique Graphique cre

/* new archi
"/custom/graphiques/".$_SESSION['rep_travail']."/
*/

if (!isset($_SESSION['rep_travail'])){
	/*if (!isset($oSite)){
		$oSite = hostToSite();
	}
	if ($oSite == false){
		$oSite = defaultSite();
	}
	$_SESSION['rep_travail'] = $oSite->get_rep();	*/
	$oSite = detectSite();
	$idSite = $oSite->get_id();
	sitePropsToSession($oSite);
}

if (is_file($_SERVER['DOCUMENT_ROOT'].'/tmp/graphic_'.$_GET['id'].'.php')){
	readfile($_SERVER['DOCUMENT_ROOT'].'/tmp/graphic_'.$_GET['id'].'.php');
}
if (is_file($_SERVER['DOCUMENT_ROOT'].'/custom/graphiques/'.$_SESSION['rep_travail'].'/graphic_'.$_GET['id'].'.php')){
	readfile($_SERVER['DOCUMENT_ROOT'].'/custom/graphiques/'.$_SESSION['rep_travail'].'/graphic_'.$_GET['id'].'.php');
}
else{ // patch de recherche de fichers dans les autres minisites (à cause du bug des sessions qui dégeulent
	$aSite = dbGetObjectsFromRequete("cms_site", "select * from cms_site ");
	if ($aSite){
		foreach($aSite as $sK => $oSite){
			if (is_file($_SERVER['DOCUMENT_ROOT'].'/custom/graphiques/'.$oSite->get_rep().'/graphic_'.$_GET['id'].'.php')){
				readfile($_SERVER['DOCUMENT_ROOT'].'/custom/graphiques/'.$oSite->get_rep().'/graphic_'.$_GET['id'].'.php');
				break;
			}		
		}	
	}
}
?>