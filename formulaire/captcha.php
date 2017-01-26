<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
	
	require_once('cms-inc/lib/recaptcha/recaptchalib.php');
	
	$resp = recaptcha_check_answer ($_POST["privatekey"],
								$_SERVER["REMOTE_ADDR"],
								$_POST["recaptcha_challenge_field"],
								$_POST["recaptcha_response_field"]);
 
	if (!$resp->is_valid) {
		echo  0;
	}
	else {
		echo 1;
	}

?>