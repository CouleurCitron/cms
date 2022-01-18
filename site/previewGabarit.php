<?php
 
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15" />
<script src="/backoffice/cms/js/fojsutils.js" type="text/javascript"></script>
<script src="<?php echo $URL_ROOT;?>/backoffice/cms/js/AC_RunActiveContent.js" type="text/javascript"></script>
<link href="<?php echo  "/custom/css/fo_".strtolower($_SESSION['site']).".css" ; ?>" rel="stylesheet" type="text/css"></link>
</head>
<body style="border:0px; margin:0px; padding:0px;">
<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/gabarit.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newpage_write.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

$idSite = $_SESSION['idSite_travail'];
$DIR_GABARITS = getRepGabarit($idSite);
	
if(strlen($_GET['id'])>0){
// On a une préview qui se base sur un gabarit vide (fichier physique)


	$oGabarit= new Cms_page($_GET['id']);
	
	// choix du rep
	if( $oGabarit->get_id_site()==-1){
		$rep = 'all';
	}
	else{
		$rep = $_SESSION['rep_travail'];
	}
	
	if (is_file($_SERVER['DOCUMENT_ROOT'].'/custom/gabarit/'.$rep.'/'.$oGabarit->get_options_page())){ // vignette
		echo '<img src="/custom/gabarit/'.$rep.'/'.$oGabarit->get_options_page().'" />';	
	}
	elseif ($oGabarit->get_iscustom()==0){ // old style = briques
		echo phpsrcEval($oGabarit->getHtml_page());
	}
	else{ // new style = no briques
		if (is_file($_SERVER['DOCUMENT_ROOT'].'/custom/gabarit/'.$rep.'/'.$oGabarit->get_name_page().'.php')){
			include($_SERVER['DOCUMENT_ROOT'].'/custom/gabarit/'.$rep.'/'.$oGabarit->get_name_page().'.php');
		}
		else{
			echo 'gabarit custom introuvable : /custom/gabarit/'.$rep.'/'.$oGabarit->get_name_page().'.php';
		}
	}

}
else if($_SESSION['divArray']!="") {
// On a une préview qui se base sur un gabarit en cours de montage
// on a donc tous les champs de rempli => affichage complet

	echo getPageHeaderWithBody();
	include "../previewPage.php";
	echo getPageFooter();
	
}
else
	echo "Erreur: gabarit non trouvé!";

?>
</body>
</html>