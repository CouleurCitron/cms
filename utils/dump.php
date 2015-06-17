<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/lib.inc.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/htmlUtility.php'); 


if(DEF_BDD_SERVER == "localhost"){
	
	$sql = 'mysqldump  -h localhost -u '.DEF_BDD_USER.' --password="'.DEF_BDD_PWD.'" '.DEF_BDD_DBNAME.' > '.$_SERVER['DOCUMENT_ROOT'].'/tmp/'.DEF_BDD_DBNAME.'-'.date('Y-m-d').'.sql';
	echo $sql .'<br>';

	echo exec($sql);
	
	//
	echo '<a href="/tmp/'.DEF_BDD_DBNAME.'-'.date('Y-m-d').'.sql">'.DEF_BDD_DBNAME.'-'.date('Y-m-d').'.sql</a>';
	
}
else{
	echo 'cannot dump remote host';	
}
?>