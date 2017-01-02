<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');

if (is_get('idSite')){
	$idSite = $_GET['idSite'];
	$_POST['idSite'] = $idSite;
	//error_log('++++ GET '.$_POST['idSite']);
	
	// site de travail
	$oSite = new Cms_site($_POST['idSite']);
	$_SESSION['idSite_travail'] = $oSite->get_id();  
	$idSite = $_SESSION['idSite_travail'];
	sitePropsToSession($oSite);
}
//-------------------------------------------------------------------------
// update list class cms en bo

$file = $_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/class/';

if ($subdir = @opendir($file)) {
	while (($subfile = readdir($subdir)) !== false) {
		if (preg_match("/\.class\.php$/",$subfile)==1) {
			$fullpath = $file.$subfile;
			//echo $fullpath;
			$sClasseName = preg_replace("/^(.+)\.class\.php$/", '$1', $subfile);
			
			$aClasse = dbGetObjectsFromFieldValue("classe", array("get_nom"),  array($sClasseName), NULL);
	
			if (count($aClasse) > 0){
				echo $sClasseName." is already registered<br />\n";
			}
			else{
				echo $sClasseName." should be registered<br />\n";
				$oClass = new classe();
				$oClass->set_nom($sClasseName);
				$oClass->set_iscms(1);
				if ((bool)class_exists($sClasseName)==true){
					echo ' with status 4 <br />';
					$oClass->set_statut(DEF_ID_STATUT_LIGNE);
				}
				dbSauve($oClass);
			}			
			echo "<hr />";				
		}
	} 
	closedir($subdir);
}


//-------------------------------------------------------------------------
// creation des dossiers rel/ minisites
$sql = "SELECT * FROM `cms_site` ORDER BY cms_id ASC;";
$aSites = dbGetObjectsFromRequete("Cms_site", $sql);

foreach($aSites as $key => $site){	
	echo "site : ".$site->get_rep()."<br />\n";
	
	if ($site->get_rep()==''){
		break;
	}
	
	if (preg_match('/CMS/si',$site->get_fonct())==1){
		dirExists($_SERVER['DOCUMENT_ROOT'].'/content/'.$site->get_rep());		
	}
	dirExists($_SERVER['DOCUMENT_ROOT'].'/custom/gabarit/'.$site->get_rep());
	dirExists($_SERVER['DOCUMENT_ROOT'].'/custom/img/'.$site->get_rep());
	dirExists($_SERVER['DOCUMENT_ROOT'].'/custom/newsletter/'.$site->get_rep());
	dirExists($_SERVER['DOCUMENT_ROOT'].'/custom/js/'.$site->get_rep());
	dirExists($_SERVER['DOCUMENT_ROOT'].'/custom/swf/'.$site->get_rep());
	dirExists($_SERVER['DOCUMENT_ROOT'].'/custom/upload/');
	dirExists($_SERVER['DOCUMENT_ROOT'].'/tmp');
	
	if (!is_file($_SERVER['DOCUMENT_ROOT']."/custom/css/fo_".$site->get_rep()."_spaw.css")){
		echo "creates /custom/css/fo".$site->get_rep()."_spaw.css\n";
		$css = fopen($_SERVER['DOCUMENT_ROOT']."/custom/css/fo_".$site->get_rep()."_spaw.css", "w");		
		fwrite($css, "/* CSS Document */");
		fclose($css);	
	}
	if (!is_file($_SERVER['DOCUMENT_ROOT']."/custom/css/fo_".$site->get_rep().".css")){
		echo "creates /custom/css/fo".$site->get_rep().".css\n";
		$css = fopen($_SERVER['DOCUMENT_ROOT']."/custom/css/fo_".$site->get_rep().".css", "w");
		fwrite($css, "/* CSS Document */");
		fclose($css);
	}
	if (!is_file($_SERVER['DOCUMENT_ROOT']."/custom/js/".$site->get_rep()."/fojsutils.js")){
		echo "creates /custom/js/".$site->get_rep()."/fojsutils.js\n";
		$js = fopen($_SERVER['DOCUMENT_ROOT']."/custom/js/".$site->get_rep()."/fojsutils.js", "w");
		fwrite($js, "/* JS Document */");
		fclose($js);
	}
	if (!is_file($_SERVER['DOCUMENT_ROOT']."/custom/js/".$site->get_rep()."/styles_ck.js")){
		echo "creates /custom/js/".$site->get_rep()."/styles_ck.js\n";
		$js = fopen($_SERVER['DOCUMENT_ROOT']."/custom/js/".$site->get_rep()."/styles_ck.js", "w");

		$contenuDefaultEditor = "\nCKEDITOR.stylesSet.add( 'styles_ck', [\n
			/* Block Styles */\n\n

			{ name: 'Paragraphe', element: 'p' } //,\n\n
						
		]);\n";

		fwrite($js, "/* JS CK Editor Styles Document */\n".$contenuDefaultEditor);
		fclose($js);		
	}
	if (!is_file($_SERVER['DOCUMENT_ROOT']."/custom/gabarit/".$site->get_rep()."/init.php")){
		echo "creates /custom/gabarit/".$site->get_rep()."/init.php\n";
		$init = fopen($_SERVER['DOCUMENT_ROOT']."/custom/gabarit/".$site->get_rep()."/init.php", "w");
		$initGabHTMl = "<script type=\"text/javascript\">
function init(){
  checkIframe();
}
window.onload = init;
</script>
<!--DEBUTCMS-->
<!--FINCMS-->";
		fwrite($init, $initGabHTMl);
		fclose($init);
	}
}


// -------------------------------------------------------------------------------
/*if (is_file("/root/initAWS.sh")){
	echo passthru("/root/initAWS.sh ".$_SERVER['DOCUMENT_ROOT']."/");
	echo "\nResultat de l'init:\n";
	echo passthru("ls -al  ".$_SERVER['DOCUMENT_ROOT']);
}*/
?>
<script type="text/javascript">
function reloadForSession(){
	document.location.href="<?php echo $_SERVER['PHP_SELF']; ?>?idSite=<?php echo $_SESSION['idSite']; ?>";
}
setTimeout(reloadForSession, 1000*60*15);
</script>