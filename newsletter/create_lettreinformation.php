<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
	
	
if ( isset($_GET['idInslien'])  && $_GET['idInslien'] != "" )  {
	if (isset ($_GET['idnew']) && $_GET['idnew']!= "") {
		$oNews = new Newsletter($_GET['idnew']);
		
		$sBodyHTML = $oNews->get_html();
		$sBodyHTML = str_replace("XX-ID-INSCRIT-XX", $_GET['ins'], $sBodyHTML);
		echo $sBodyHTML; 
	}
}
else{
	$httpDomain = $_SERVER['HTTP_HOST'];
	if (defined("DEF_ROOT_TEMPLATENEWS")){
		$httpDomainFrontTemplate = "http://".$_SERVER['HTTP_HOST']."".DEF_ROOT_TEMPLATENEWS;
		$rootDomainFrontTemplate = $_SERVER['DOCUMENT_ROOT']."".DEF_ROOT_TEMPLATENEWS;
	}
	else{
		$httpDomainFrontTemplate = "http://".$_SERVER['HTTP_HOST']."/frontoffice/newsletter";
		$rootDomainFrontTemplate = $_SERVER['DOCUMENT_ROOT'].""."/frontoffice/newsletter";
	}
	dirExists($rootDomainFrontTemplate);
	
	$sBodyHTML="";	
	
	if(is_post('fNews_theme') && $_POST['fNews_theme'] != -1){
		$oTheme = new News_theme ($_POST['fNews_theme']);
		$nameFile = $oTheme->get_html_fichier();
		
		if (preg_match('/\.htm/', $nameFile)==0){ 
			$nameFile = $nameFile.".html";
		}
		if(file_exists($rootDomainFrontTemplate."/".$nameFile)) {
			$mailAlerte = $rootDomainFrontTemplate."/".$nameFile;
		}
		elseif(file_exists($rootDomainFrontTemplate.'/'.$translator->getLangCodeById($_SESSION['tsl_langue']).'.html')) {
			$mailAlerte = $rootDomainFrontTemplate.'/'.$translator->getLangCodeById($_SESSION['tsl_langue']).'.html';
		}
		else {
			$mailAlerte = $rootDomainFrontTemplate."/default.html";
		}	
	}
	else {
		//$mailAlerte = $httpDomainFrontTemplate."/default.html";
		$mailAlerte=NULL;
	}
	
	
	if ($mailAlerte!=NULL){ // only sur creation ou changement de theme
		$fh = @fopen($mailAlerte,'r');
		if ($fh){
			while(!feof($fh)) {
				$sBodyHTML.=fgets($fh);
			}
		}
		else{
			echo '<p><strong>pas de template disponible</strong> à l\'adresse : <br /><a href="'.$mailAlerte.'">'.$mailAlerte.'</a></p>';
		}
	}
	
	$sBodyHTML = str_replace("\"/frontoffice/", "\"http://".$httpDomain."/frontoffice/", $sBodyHTML);
	$sBodyHTML = str_replace("url(/frontoffice/", "url(http://".$httpDomain."/frontoffice/", $sBodyHTML);
	$sBodyHTML = str_replace("\"/content/", "\"http://".$httpDomain."/content/", $sBodyHTML);
	$sBodyHTML = str_replace('src="img', 'src="'.$httpDomainFrontTemplate.'img', $sBodyHTML);
	
		
	
	 
	if ($_GET['lien'] == 1) {
		$sBodyHTML = str_replace("XX-CLIQUEZ-XX", "" , $sBodyHTML);
		echo $sBodyHTML;
	}
	$sBodyHTML = str_replace("XX-IMG-AR-XX", '<img src="http://'.$httpDomain.'/frontoffice/newsletter/newsletter_AR.php?ins=XX-ID-INSCRIT-XX&idnew=XX-IDNEW-XX&num=1" alt="Accusé de reception" />', $sBodyHTML);
}

		
?>
