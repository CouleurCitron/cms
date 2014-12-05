<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

if (!isset($_SESSION['basedir'])){
	$_SESSION['basedir'] = "/etc";
	$_SESSION['unlock'] = "/etc";
}
if (isset($_GET['dir']) && ($_GET['dir'] != "")){
	$_SESSION['basedir'] = $_SESSION['basedir']."/".$_GET['dir'];
}

if (!is_dir($_SESSION['basedir'])){
	$_SESSION['basedir'] = "/etc";
}

chdir($_SESSION['basedir']);
$_SESSION['basedir'] = getcwd();
/*
if (strpos($_SESSION['basedir'], "/var/cvs") === false){
	$_SESSION['basedir'] = "/var/cvs";
	chdir($_SESSION['basedir']);
}
*/
echo "<h1>".getcwd()."</h1>";

if (isset($_GET['unlock']) && ($_GET['unlock'] != "")){
	$_SESSION['unlock'] = $_SESSION['basedir']."/".$_GET['unlock'];
	
	if (is_dir($_SESSION['unlock']) || is_file($_SESSION['unlock'])){
		$param1 = $_SESSION['unlock'];	
		//$command = "sudo /root/locks.sh ".$param1."&";	
		$view = fopen($param1, "r");
		$contents = fread($view, filesize($param1));
		echo "<pre>".htmlentities($contents)."</pre><hr />";
		fclose($view);
	}
}

$ls = shell_exec ("ls -a");
$aLs = split("\n", $ls);

foreach($aLs as $key => $dir){
	if (($dir != "") && ($dir != ".") && ($dir != "CVSROOT")){
		if (is_dir($dir)){
			echo "<strong><a href=\"?dir=".$dir."\">".$dir."</a></strong>";
		}
		else{
			echo "<strong>".str_replace(",v", "", $dir)."</strong>";
		}
		if ($dir != ".."){
			echo " - <a href=\"?unlock=".$dir."\">view</a>";
		}
		echo "<br />\n";	
	}
}
?>