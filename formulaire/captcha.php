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
	
	
	/*function checkcaptcha ($captchareply) {
	   $captchaok=0; 
	   if ($_COOKIE['captcha']==substr(md5(strtoupper($captchareply)),4,6)&&$_COOKIE['captcha']){$captchaok=1;}
	   
	   if  (isset($_SESSION["captcha"]) && $_SESSION["captcha"] ==substr(md5(strtoupper($captchareply)),4,6) ){
		 $captchaok=1;
	  }
	   return($captchaok);
	}

	if (checkcaptcha ($_POST["captcha"])) echo  1;
	else echo 0; */

	 
	 
	
?> 
