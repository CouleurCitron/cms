<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

if (strpos($_SERVER['REQUEST_URI'], 'secure.inc.php')!==false){
	// hack attempt
	error_log('hack attempt on '.$_SERVER['PHP_SELF'].' from '.$_SERVER['REMOTE_ADDR']);
	unset($_SESSION['FO']);
	$sRetour = '';
	die();	
}

$aFM = dbGetObjectsFromFieldValue('cms_filemanager', array('get_statut', 'get_id'),  array(DEF_ID_STATUT_LIGNE, $oFM->get_id()), NULL);
if ((count($aFM) == 1)&&($aFM!=false)){

	if(is_post('fpassword')){
		
		if (is_post('flogin')){ // Test login / pwd
		
			if ($_POST['flogin'] != $db->qstr($_POST['flogin'])){
				// hack attempt
				error_log('hack attempt on '.$_SERVER['PHP_SELF'].' from '.$_SERVER['REMOTE_ADDR']. ' login:' .$_POST['flogin']);
				unset($_SESSION['FO']);
				$sRetour = '';
				die();	
			}			
			
		$aFMU = dbGetObjectsFromFieldValue('cms_filemanageruser', array('get_statut', 'get_login', 'get_passwd', 'get_cms_filemanager'),  array(DEF_ID_STATUT_LIGNE, $_POST['flogin'], md5($_POST['fpassword']), $oFM->get_id()), NULL);
	
	}
	else{ // test pwd seul
		$aFMU = dbGetObjectsFromFieldValue('cms_filemanageruser', array('get_statut', 'get_login', 'get_passwd', 'get_cms_filemanager'),  array(DEF_ID_STATUT_LIGNE, '*', md5($_POST['fpassword']), $oFM->get_id()), NULL);
	}
	
	if ((count($aFMU) == 1)&&($aFMU!=false)){	
		$oFMU=$aFMU[0];		
		$_SESSION['FO'] = array();
		$_SESSION['FO']['LOGGED'] = $oFMU;
		if (method_exists('cms_filemanageruser', 'get_chroot')){
			$_SESSION['FO']['chroot'] = $oFMU->get_chroot();
		}
		else{
			$_SESSION['FO']['chroot'] = 0;
		}
		$_SESSION['FO']['login'] = $oFMU->get_login();
		$sRetour = '';
	}
	else{
			unset($_SESSION['FO']);
		$sRetour = 'Mot de passe erroné';
		include($_SERVER['DOCUMENT_ROOT'].$_skinDir.'login.inc.php');
		die();
	}
	
}
	elseif (isset($_SESSION['FO'])	&&	isset($_SESSION['FO']['LOGGED'])){
		//echo 'loggé en FO';
		// RAS
	}
	elseif (!isset($_SESSION['FO'])	||	!isset($_SESSION['FO']['LOGGED'])){
	//echo 'pas loggé en FO';
	$sRetour = '';
	include($_SERVER['DOCUMENT_ROOT'].$_skinDir.'login.inc.php');
	die();
}
else{
		// fallback - logged out par défaut
		$sRetour = '';
		include($_SERVER['DOCUMENT_ROOT'].$_skinDir.'login.inc.php');
		die();
	}
}
else{
	// pas de FM valide
	unset($_SESSION['FO']);
	$sRetour = '';
		die();

}
?>