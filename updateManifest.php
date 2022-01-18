<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/gabarit.lib.php');

activateMenu('gestionpage');  //permet de dérouler le menu contextuellement
$generationIsOk = true;


//listing complet
global $aCacheFile;
$aCacheFile = array();

unset($_SESSION['BO']['CACHE']);

function getPagesImages($page_url) {
	$oSite = new cms_site($_SESSION['idSite']);
	$site_url = $oSite->get_url();
	if($site_url == '') {
		$site_url = $_SERVER["HTTP_HOST"];
	}

	$curl_init = curl_init();
	curl_setopt($curl_init, CURLOPT_URL, $page_url);
	curl_setopt($curl_init, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl_init, CURLOPT_FOLLOWLOCATION, 1); 
	curl_setopt($curl_init, CURLOPT_VERBOSE, 0);
	curl_setopt($curl_init, CURLOPT_HEADER, 0);
	$curl_data = curl_exec($curl_init);
	curl_close($curl_init);
	if(!curl_errno($curl_init)){
		$newDom = new domDocument;
		$newDom->loadHTML($curl_data);
		foreach($newDom->getElementsByTagName('img') as $img_tag) {
			$img_src = $img_tag->getAttribute('src');
			$file_headers = @get_headers('http://'.$_SERVER["HTTP_HOST"].$img_src);
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$img_src)) {
				global $aCacheFile;
				$aCacheFile[] = 'http://'.$site_url.str_replace( array('%2F'), array('/'), rawurlencode($img_src));
			}
		}
	}
}

function regenerateManifeste($id_site) {

	global $aCacheFile;

	$oSite = new cms_site($id_site);
	$site_repo = $oSite->get_rep();
	$site_url = $oSite->get_url();
	if($site_url == '') {
		$site_url = $_SERVER["HTTP_HOST"];
	}
	//var_dump($site_url);

	//Liste des répertoires à parcourir et intégrer
	$directories = array(
		"/custom/js/".$site_repo.'/',
		"/custom/css/",
		"/custom/font/",
		"/custom/img/".$site_repo."/"
	);

        $page_url_home = 'http://'.$site_url.'/content/' . $site_repo . '/';
        //$aCacheFile[] = $page_url_home;
        
	//Toutes les pages
	//$contenus = getAllPages($id_site);
    $contenus = getAllPagesNotRecursive($id_site);
	foreach ($contenus as $k => $oPage) {
		foreach ($oPage as $o => $iPage) {
			$page_url = 'http://'.$site_url.'/content'.$iPage->absolute_path_name;
			$file_headers = @get_headers($page_url);
			$InvalidHeaders = array('404', '403', '500');
			$add = true;
			foreach($InvalidHeaders as $HeaderVal){
				if(strstr($file_headers[0], $HeaderVal)) {
					$add = false;
					break;
				}
			}
			if($add) {
				$aCacheFile[] = $page_url;
			}
			getPagesImages($page_url);
		}
	}

	//Tous les dirs images...
	foreach($directories as $k => $dir) {
		$scan_dir = scandir($_SERVER['DOCUMENT_ROOT'].$dir);
		foreach($scan_dir as $k => $file) {
		    if(file_exists($_SERVER['DOCUMENT_ROOT'].$dir.$file)
		            && $file != '.' 
		            && $file != ".." 
		            && $file != "CVS" ) {
		    	$file_parts = pathinfo($_SERVER['DOCUMENT_ROOT'].$dir.$file);
		    	if($file_parts['extension'] != '') {
		        	$aCacheFile[] = 'http://'.$site_url.$dir.rawurlencode($file);
		    	}
		    }
		}
	}

	$aCacheFile = array_unique($aCacheFile);
	// $cache_data = "header('Content-Type: text/cache-manifest');\n";

	//Génère le fichier
	$cache_data = "CACHE MANIFEST\n\n";
	$cache_data .= '# '.time()."\n\n";
	$cache_data .= "CACHE:\n";
	$cache_data .= join("\n", $aCacheFile)."\n";
	$cache_data .= "\nFALLBACK:\n";
	$cache_data .= "\nNETWORK:\n";
	$cache_data .= "*\n";

        if( !dirExists($_SERVER['DOCUMENT_ROOT'].'/custom/manifest/') ){
            mkdir( $_SERVER['DOCUMENT_ROOT'].'/custom/manifest/' );
        }
        
	file_put_contents($_SERVER['DOCUMENT_ROOT'].'/custom/manifest/cache-'.$id_site.'.manifest', $cache_data);

}

if (isset($_POST['connectSite'])) {
	// Génération des gabarits et pages sélectionnées
	$result = regenerateManifeste($_POST['connectSite']);
?>


<link href="/backoffice/cms/css/bo.css" rel="stylesheet" type="text/css">
<body class="arbo">
<span class="arbo"><u><b>Fichiers regénérés&nbsp;:&nbsp;</b></u>
</span>
<ul>
<?php
	foreach ($result as $k => $aPageRegenere) {

		$urlDisplay = ( ($aPageRegenere['isgabarit_page']!="1") ? $aPageRegenere['url_base_page'] : "" ) .$aPageRegenere['name'];
		$url = rawurlencode($urlDisplay);
		$url = preg_replace('/\%2F/','/',$url);
?>
  <li>
  <?php echo ($aPageRegenere['isgabarit_page']=="1") ? "Gabarit : " : "Page : "; ?>
  [<?php echo $aPageRegenere['generated']; ?>] &lt;= <a href="<?php echo  ($aPageRegenere['isgabarit_page']==1) ? "/backoffice/cms/site/previewGabarit.php?id=".$aPageRegenere['id_page'] : "/content".$url.".php" ?>" target="_blank" class="arbo"><?php echo $aPageRegenere['titre']; ?>&nbsp;(<?php echo $urlDisplay.'.php'; ?>)</a></li>
<?php
	}
?>
</ul>
<?php if (!isset($_GET['pageDisplayed'])) { ?>
<u><b class="arbo">Fichiers non regénérés&nbsp;:&nbsp;</b></u>
<ul>
<?php
	foreach ($pageDisplayed as $k=> $pageId) {
		if (!in_array($pageId,$pageToGenerate)) {
			$page = getPageById($pageId);
			$folder = getFolderInfos($page['node_id']);
			
			if($folder["id"]=="0") // C'est la racine => ajout du minisite
			$folder["path"]= $folder["path"].$_SESSION['site_travail']."/";
			
			$urlDisplay = ( ($page['isgabarit_page']!="1") ? $folder['path'] : "" ) .$page['name'];			
			$url = rawurlencode($urlDisplay);
			$url = preg_replace('/\%2F/','/',$url);
			
?>
 <li>
<?php
echo ($page['isgabarit_page']=="1") ? "Gabarit : " : "Page : ";
echo $page['titre']; ?>&nbsp;(<?php echo $urlDisplay.'.php'; ?>)&nbsp;&nbsp;<small><a href="<?php

if($page['isgabarit_page']=="1")
	echo "site/gabaritModif.php?id=".$pageId;
else
	echo "pageModif.php?id=". $pageId. "&idGab=". $page['gabarit']; 
 
 ?>" target="_blank" class="arbo">Edition manuelle</a></small>&nbsp;|&nbsp;<small><a href="<?php echo  ($page['isgabarit_page']==1) ? "/backoffice/cms/site/previewGabarit.php?id=".$pageId : "/content".$url.".php" ?>" target="_blank" class="arbo">Visualiser</a></small>
<?php
		}
	}
?>
</ul>
<?php
}
else { // si la génération se fait via modif d'infos de pages
?><script type="text/javascript">
document.location.href="arboPage_browse.php";
</script>	
<?php
	}
}
?>