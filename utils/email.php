<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
//error_reporting(E_ALL);

$mess='test de mail sur '.$_SERVER['HTTP_HOST'].'<br /><br />';

if (defined('DEF_USERMAIL')){	
	$mess.='To DEF_USERMAIL : ' .DEF_USERMAIL.'<br />';
}
if (defined('DEF_CONTACT_FROM_EMAIL')){	
	$mess.='From DEF_CONTACT_FROM_EMAIL : ' .htmlentities(DEF_CONTACT_FROM_EMAIL).'<br />';
}
if (defined('DEF_MAIL_HOST')){
	$mess.='DEF_MAIL_HOST : ' .DEF_MAIL_HOST.'<br />';
}
if (defined('DEF_MAIL_ENGINE')){
	$mess.='DEF_MAIL_ENGINE : ' .DEF_MAIL_ENGINE.'<br />';
}
if (defined('DEF_USEPHPMAILFUNCTION')){
	$mess.='DEF_USEPHPMAILFUNCTION : ' .DEF_USEPHPMAILFUNCTION.'<br />';
}
if (defined('DEF_MAIL_LOGIN')){
	$mess.='DEF_MAIL_LOGIN : ' .DEF_MAIL_LOGIN.'<br />';
}
if (defined('DEF_MAIL_PASS')){
	$mess.='DEF_MAIL_PASS : ' .DEF_MAIL_PASS.'<br />';
}
if (defined('DEF_MAIL_PORT')){
	$mess.='DEF_MAIL_PORT : ' .DEF_MAIL_PORT.'<br />';
}
if (defined('DEF_SENDMAILPATH')){
	$mess.='DEF_SENDMAILPATH : ' .DEF_SENDMAILPATH.'<br />';
	if (@is_executable(preg_replace('/([^ ]+) .*/', '$1', DEF_SENDMAILPATH))) {
		$mess.='executable OK<br />';
	} else {
		$mess.='non executable<br />';
	}	
}


if(is_post('to')){
	$to = $_POST['to'];	
}
else{
	$to = DEF_USERMAIL;
}

if(is_post('from')){
	$from = $_POST['from'];	
}
else{
	$from = DEF_CONTACT_FROM_EMAIL;
}

//      multiPartMail($to , $sujet , 							 $html , $text,             $from, $attach='', $typeAttach='text/plain', $host=DEF_MAIL_HOST, $replyto=''){
$bRes = multiPartMail($to, 'test mail sur '.$_SERVER['HTTP_HOST'], $mess, html_entity_decode($mess), $from, '', NULL);
if($bRes===true){
	echo '<p>Envoi OK</p>';
}
else{
	echo '<p>Envoi KO</p>';
}

echo '<br /><br />'.$mess;


?>

<form id="testmail" name="testmail" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
	<fieldset class="arbo" >
    	<label for="from" class="arbo" >From</label>
		<input id="from" name="from" class="arbo" type="text" />
    </fieldset>
    <fieldset class="arbo" >
 	   <label for="to" class="arbo" >To</label>
  	  <input id="to" name="to" class="arbo" type="text" />
	 </fieldset>
	<input type="submit" value="Envoyer"/>
</form>