<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 
// UNIQUEMENT APPELE DEPUIS save_cms_assoclassenode.php ou load_cms_assoclassenode.php
// *****************************************************************************************///

echo '<br /><br /> post traitement et caching<br /><br />  ';

// controler les pages modèles

$classeName = $oClasse->get_nom();

if($oX->get_nodemodele()!=-1 ){	
	echo ' copier les fichiers de :<br />';
	$oNodeModele = new cms_arbo_pages($oX->get_nodemodele());
	pre_dump($oNodeModele->get_absolute_path_name());
	
	$sSrcPath = $_SERVER['DOCUMENT_ROOT'].'/content'.$oNodeModele->get_absolute_path_name();		
	$aSrcFile = array();		
	if ($subdir = @opendir($sSrcPath)) {
		while (($subfile = readdir($subdir)) !== false) {
			if (preg_match('/.+\.php$/', basename($subfile))==1){
				$aSrcFile[]=$sSrcPath.$subfile;				
			}			
		}
	}		
	
	echo '<br />dans:<br />';		
	// chercher tous les nodes qui ont $oParent // $oX->get_arbo_pages() comme parent		
	$aKids = getNodeChildren($oParent->get_id_site(), $db, $oParent->get_id());
	foreach($aKids as $k => $aKid){	
		$bodyClasses = str_replace('/', ' ', preg_replace('/\/[^\/]+\/(.*)\//', '$1', $aKid['path']));
		pre_dump($bodyClasses);
		foreach($aSrcFile as $kF => $sSrcFile){			
			//pre_dump($sSrcFile);				
			$toFile = $_SERVER['DOCUMENT_ROOT'].'/content'.$aKid['path'].basename($sSrcFile);				
			//pre_dump($toFile);			
			$sHtmlSrc = file_get_contents($sSrcFile);			
			$sHtmlSrc = preg_replace('/<body id="([^"]+)" class="[^"]+">/', '<body id="$1" class="'.$bodyClasses.'">', $sHtmlSrc);			
			file_put_contents($toFile, $sHtmlSrc);
		}
	}
}	



		
?>