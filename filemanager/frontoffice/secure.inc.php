<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
if(isset($_POST['fpassword'])){
	if (isset($_POST['flogin'])){ // Test login / pwd
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
		$sRetour = 'Mot de passe erroné';
		include($_SERVER['DOCUMENT_ROOT'].$_skinDir.'login.inc.php');
		die();
	}
	
}
elseif (!isset($_SESSION['FO'])&&!isset($_SESSION['FO']['LOGGED'])){
	//echo 'pas loggé en FO';
	$sRetour = '';
	include($_SERVER['DOCUMENT_ROOT'].$_skinDir.'login.inc.php');
	die();
}
else{
	// RAS
}
?>