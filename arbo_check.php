<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

if (is_get('idSite')){
	$idSite = $_GET['idSite'];
	$_POST['idSite'] = $idSite;
	error_log('++++ GET '.$_POST['idSite']);
	
	// site de travail
	$oSite = new Cms_site($_POST['idSite']);
	$_SESSION['idSite_travail'] = $oSite->get_id();  
	$idSite = $_SESSION['idSite_travail'];
	sitePropsToSession($oSite);
}
else{
	$idSite = $_SESSION['idSite'];	
}

$sSql = "SELECT * FROM cms_arbo_pages WHERE node_id_site = ".$idSite." ORDER BY node_id ASC";

//echo $sSql;

echo "<pre>";

$aNodes = dbGetObjectsFromRequete("Cms_arbo_pages", $sSql);

for ($i=0;$i<count($aNodes);$i++){	

	$oNode = new Cms_arbo_pages($aNodes[$i]->get_id());	
	if ($oNode->get_parent_id()!=-1){
		$oParent = new Cms_arbo_pages($oNode->get_parent_id());			
	}
	else{
		$oParent = false;
	}
	$oTempSite = new Cms_site($oNode->get_id_site());	
	
	if (preg_match('/CMS/s',$oTempSite->get_fonct())){
		
		echo $oNode->get_libelle()."\n";
	
		if ($oNode->get_order() == NULL){
			$oNode->set_order(1);
		}
		
		if($oNode->get_id()==0){
			echo "I'm root\n";
			if ($oNode->get_absolute_path_name()!='/'){
				$oNode->set_absolute_path_name('/');
				echo "** correct path should be : ".$oNode->get_absolute_path_name()."\n";
				dbUpdate($oNode);
			}
		}		
		elseif (strpos($oNode->get_absolute_path_name(), $oParent->get_absolute_path_name()) === false){
			echo "&nbsp;\n";
			echo "i'm ".$oNode->get_id()." (".$oNode->get_libelle().' / '.$oNode->get_absolute_path_name()."),";
			echo " daddy's ".$oParent->get_id()." (".$oParent->get_absolute_path_name().")\n";
			echo "<strong>error</strong> : ".$oParent->get_absolute_path_name()." is not found in ".$oNode->get_absolute_path_name()."\n";
			
			$correctPath = "/";
			if ($oParent->get_absolute_path_name() == '/'){
				$correctPath .= $oTempSite->get_rep().'/';
			}
			else{
				$correctPath = preg_replace('/\/\//','/', '/'.$oParent->get_absolute_path_name());
			}
			$correctPath .= preg_replace('/\/\//','/', removeForbiddenChars($oNode->get_libelle())."/");
			$oNode->set_absolute_path_name($correctPath);
			dbUpdate($oNode);
			echo "** correct path should be : ".$oNode->get_absolute_path_name()."\n";
		}
		elseif (strpos($oNode->get_absolute_path_name(), "//") !== false){
			echo "&nbsp;\n";
			echo "i'm ".$oNode->get_id()." (".$oNode->get_absolute_path_name()."),";
			echo " daddy's ".$oParent->get_id()." (".$oParent->get_absolute_path_name().")\n";
			echo "<strong>error</strong> : double / found \n";
			
			$correctPath = "/";
			if ($oParent->get_absolute_path_name() == "/"){
				$correctPath .= $oTempSite->get_rep()."/";
			}
			else{
				$correctPath = preg_replace('/\/\//','/', "/".$oParent->get_absolute_path_name());
			}
			$correctPath .= preg_replace('/\/\//','/', removeForbiddenChars($oNode->get_libelle())."/");
			$oNode->set_absolute_path_name($correctPath);
			dbUpdate($oNode);
			echo "** correct path should be : ".$oNode->get_absolute_path_name()."\n";
		}
		elseif (str_replace('/', '', $oNode->get_absolute_path_name()) != removeForbiddenChars(str_replace('/', '', $oNode->get_absolute_path_name()))){
			echo "<strong>error</strong> :  des accents dans le path : ".str_replace('/', '', $oNode->get_absolute_path_name())." != ".removeForbiddenChars(str_replace('/', '', $oNode->get_absolute_path_name()))."\n";
			$correctPath = "/";
			if ($oParent->get_absolute_path_name() == "/"){
				$correctPath .= $oTempSite->get_rep()."/";
			}
			else{
				$correctPath = $oParent->get_absolute_path_name();
			}
			$correctPath .= removeForbiddenChars($oNode->get_libelle())."/";
			$oNode->set_absolute_path_name($correctPath);
			dbUpdate($oNode);
			echo "** correct path should be : ".$oNode->get_absolute_path_name()."\n";
		}
		else{ // tout va bien
			$correctPath = $oNode->get_absolute_path_name();
			echo "RAS : ".$correctPath."\n";
		}
		
		// controle file system
		$fullPath = $_SERVER['DOCUMENT_ROOT']."/content".trim($correctPath);
		// spec windows:
		//$fullPath = preg_replace("/\//msi", "\\", $fullPath);
		// --------------
		
		dirExists($fullPath);		
		
		if( is_dir($fullPath)){
			//echo "IS OK\n";
		}
		else{	
			echo $fullPath." ";		
			echo "IS NOT OK\n";
			echo "&nbsp;\n";		
		}
	}
	else{
		echo " no CMS enabled\n"	;
	}
}

//--- test sur deplacement des dossier cms_*
$aSec = dbGetObjects('cms_sectionbo');

if ((count($aSec) > 0)&&($aSec!=false)){
	foreach($aSec as $cKey => $oSec){
		if (preg_match('/\/backoffice\/cms_/s',$oSec->get_url())){
			if ((!is_dir($_SERVER['DOCUMENT_ROOT'].$oSec->get_url()))&&(is_dir($_SERVER['DOCUMENT_ROOT'].str_replace('/backoffice/cms_', '/backoffice/cms/cms_',$oSec->get_url())))){
				$oSec->set_url(str_replace('/backoffice/cms_', '/backoffice/cms/cms_',$oSec->get_url()));
				dbSauve($oSec);
				echo 'corrected '.$oSec->get_url()."\n";
			}	
		}
		if (preg_match('/\/backoffice\/bo_/s',$oSec->get_url())){
			if ((!is_dir($_SERVER['DOCUMENT_ROOT'].$oSec->get_url()))&&(is_dir($_SERVER['DOCUMENT_ROOT'].str_replace('/backoffice/bo_', '/backoffice/cms/bo_',$oSec->get_url())))){
				$oSec->set_url(str_replace('/backoffice/bo_', '/backoffice/cms/bo_',$oSec->get_url()));
				dbSauve($oSec);
				echo 'corrected '.$oSec->get_url()."\n";
			}	
		}
	}
}
?>