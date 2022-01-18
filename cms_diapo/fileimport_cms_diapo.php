<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/htmlUtility.php'); 

if (is_post('site')){
	$idSte = $_POST['site'];
}
else{
	$idSte = 1;
}

// activation du menu : déroulement
activateMenu(preg_replace("/([^\.]+)\.php/msi", "$1", basename($_SERVER['PHP_SELF']))); 

?>
<p>placer les images dans le dossier /tmp</p>
<form id="dupli" name="dupli" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p>id site dans lequel importer :</p>
<!--<input type="text" value="1" id="sourcesite" name="sourcesite" />-->
<select id="site" name="site">
<?php
$aSite = dbGetObjectsFromRequete("cms_site", "select * from cms_site ");
if ($aSite != false){
	foreach ($aSite as $key => $oSite){
		if ($oSite->get_id() == $idSte){
			$selected = " selected ";
		}
		else{
			$selected = "";
		}
		echo "<option value=\"".$oSite->get_id()."\"".$selected.">".$oSite->get_name()."</option>\n";
	}
}
?>
</select>
<input type="submit" />
</form>
<hr />
<?php

if (is_post('site')){

	dirExists($_SERVER['DOCUMENT_ROOT'].'/custom/upload/cms_diapo/');

	$sDir = $_SERVER['DOCUMENT_ROOT'].'/tmp/';
	echo 'will import '.$sDir.'<br />';


	if ($dir = opendir($sDir)) {
		echo 'reading '.$sDir.'<br />';

		$aFiles = array();		
		while (($file = readdir($dir)) !== false) {
			if (is_file($sDir.$file)) {
				$aFiles[] = $file;
			}
		}
		sort($aFiles);
		
		for($i=0;$i<count($aFiles);$i++){
			$file = $aFiles[$i];
			
			echo "<strong>".$file."</strong><br />\n";				
			
			//
			
			
			$aSli = dbGetObjectsFromFieldValue('cms_diapo', array('get_src', 'get_cms_site'),  array($file, $_POST['site']), NULL);	
			if (($aSli != false) && (count($aSli)==1)){
				//$oSli = $aSli[0];
				
				// skip
			}
			else{
				$oSli = new cms_diapo();
				$oSli->set_cms_site($_POST['site']);
				$oSli->set_src($file);
				//set_titre
				$oSli->set_titre(preg_replace('/([^\.]+)\.[^\.]{2,4}/msi', '$1', $file));
				
				
				//dumpObjLite($oSli);
				
				echo dbSauve($oSli).'<br />';
			
			}
			copy($sDir.$file, $_SERVER['DOCUMENT_ROOT'].'/custom/upload/cms_diapo/'.$file);
			//unlink($sDir.$file);
			
			
		}
		
	}
	else{
		echo 'could not read '.$sDir.'<br />';
	
	}


}
?>