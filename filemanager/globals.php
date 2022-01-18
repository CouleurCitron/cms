<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 

$ROOT = str_replace("//", "/", $_SERVER['DOCUMENT_ROOT']."/");
$base_dir="$ROOT"."documents";

if(is_array($_GET)){
	if(is_get('dir_id')) {	
		if(intval($_GET['dir_id'])<0){ // cas cms_filemanager
			$oFM = new cms_filemanager(abs(intval($_GET['dir_id'])));
			// check du rep de base
			$base_dir=$ROOT.'custom/filemanager/'.$_SESSION['rep_travail'].'/files';
			dirExists($base_dir);
			
			if (!is_file($base_dir.'/.htaccess')){
				$f = fopen($base_dir.'/.htaccess','wb');
				fwrite($f,'Options -Indexes ',strlen('Options -Indexes'));
				fclose($f); 
			}		
			
			// check des rep des users
			$aFMusers = dbGetObjectsFromFieldValue3('cms_filemanageruser', array('get_cms_filemanager','get_statut'), array('equals', 'equals'), array($oFM->get_id(), DEF_ID_STATUT_LIGNE), NULL, NULL);
			if (($aFMusers!=false)&&(count($aFMusers) > 0)){
				foreach($aFMusers as $kU => $oFMuser){
					if(($oFMuser->get_login()!='')&&($oFMuser->get_login()!='*')){
						dirExists($base_dir.'/'.$oFMuser->get_login());
					}				
				}
			}
			
			$FM_ftpUser=$oFM->get_login();
		}
		elseif($_GET['dir_id']==1){
			$base_dir="$ROOT"."pdf";
		}
		elseif($_GET['dir_id']==2){
			$base_dir="$ROOT"."medias";
		}
		elseif($_GET['dir_id']==3){
			$base_dir="$ROOT"."img";
		}	
		elseif($_GET['dir_id']==4){
			$base_dir="$ROOT"."custom/img";
		}
		$_SESSION['filemanager'] = $_GET['dir_id'];
	}
	// permet de personnaliser le menu fichier avec n'importe quel dossier du fichier upload
	elseif(isset($_GET['section_id']) && strlen($_GET['section_id'])>0) {
		$oSection = getObjectById("cms_sectionbo", $_GET['section_id']);
		$base_dir="$ROOT"."custom/upload/".$oSection->get_libelle();
		$_SESSION['filemanagersection'] = $_GET['section_id'];
	}	
	elseif(isset($_SESSION['filemanager']) && strlen($_SESSION['filemanager'])>0) {
		if(intval($_SESSION['filemanager'])<0){ // cas cms_filemanager
			$oFM = new cms_filemanager(abs(intval($_SESSION['filemanager'])));
			$base_dir=$ROOT.'custom/filemanager/'.$_SESSION['rep_travail'].'/files';
			dirExists($base_dir);
			// check des rep des users
			$aFMusers = dbGetObjectsFromFieldValue3('cms_filemanageruser', array('get_cms_filemanager','get_statut'), array('equals', 'equals'), array($oFM->get_id(), DEF_ID_STATUT_LIGNE), NULL, NULL);
			if (($aFMusers!=false)&&(count($aFMusers) > 0)){
				foreach($aFMusers as $kU => $oFMuser){
					if(($oFMuser->get_login()!='')&&($oFMuser->get_login()!='*')){
						dirExists($base_dir.'/'.$oFMuser->get_login());
					}				
				}
			}
			$FM_ftpUser=$oFM->get_login();
		}
		elseif($_SESSION['filemanager']==1){
			$base_dir="$ROOT"."pdf";
		}
		elseif($_SESSION['filemanager']==2){
			$base_dir="$ROOT"."medias";
		}
		elseif($_SESSION['filemanager']==3){
			$base_dir="$ROOT"."img";
		}
		elseif($_SESSION['filemanager']==4){
			$base_dir="$ROOT"."custom/img";
		}
	}
	if(isset($_SESSION['filemanagersection']) && strlen($_SESSION['filemanagersection'])>0) {
		$oSection = getObjectById("cms_sectionbo", $_SESSION['filemanagersection']);
		$base_dir="$ROOT"."custom/upload/".$oSection->get_libelle(); 	
	}
	
}

$max_uploads="10";

$upload_size="10000000";

?>