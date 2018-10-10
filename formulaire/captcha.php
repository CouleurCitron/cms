<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/lib/recaptcha/recaptchalib.php');

if(defined('DEV_RECAPTCHA_SECRETKEY')){
	$privatekey=DEV_RECAPTCHA_SECRETKEY;
}
else{
	$privatekey=$_POST["privatekey"];
}

if (preg_match('/^192\.168\./si', $_SERVER["REMOTE_ADDR"])){
	$remoteip = '37.1.253.217';
}
else{
	$remoteip = $_SERVER["REMOTE_ADDR"];
}

$resp = recaptcha_check_answer ($privatekey,
							$remoteip,
							$_POST["recaptcha_challenge_field"],
							$_POST["recaptcha_response_field"],
							array(),
							$_POST["recaptcha_version"]);

if (!$resp->is_valid) {
	echo  0;
	/*echo "--------------<br />";
	echo $resp->error;

	echo "<pre> _POST: =========\n";
	print_r($_POST);
	echo "\n=========\n</pre>"; */
}
else {
	echo 1;
}

