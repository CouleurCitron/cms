<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 
//"/home/mre-mip/www";

 

$ccBaseDir = $_SERVER["DOCUMENT_ROOT"];

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/lib.inc.php');
include_once("cms-inc/include_cms.php");
include_once("cms-inc/include_class.php"); 
include_once("backoffice/cms/cms_rss_url/scan.lib.php");

include_once("backoffice/cms/lib/rss/rss_cache.inc");
include_once("backoffice/cms/lib/rss/rss_fetch.inc");
include_once("backoffice/cms/lib/rss/rss_parse.inc");
include_once("backoffice/cms/lib/rss/rss_utils.inc");
include_once("backoffice/cms/lib/rss/extlib/Snoopy.class.inc");
 
$config = array();

$base = $_GET["source"];
if ($_GET["user"]!= '') {
	$user = $_GET["user"];
}
else {
	$user = 'mysql';
}

if ($_GET["pw"]!= '') {
	$pw = $_GET["pw"];
}
else {
	$pw = 'pwdmysql';
}
 

	$config['param_db'] = array(
		'db'		=> 'mysql',
		'server'	=> 'localhost',
		'user'		=> $user,
		'pw'		=> $pw,
		'base'		=> $base,
		'debug'		=> false
	);

 

echo $base;

// Adresses des sites
$config['url_www'] = 'http://'.$_SERVER['HTTP_HOST'].'/'; 
$config['url_comptes'] = 'http://'.$_SERVER['HTTP_HOST'].'/';
$HTTP_EMAIL_FROM = "thao@couleur-citron.com";
// Paramètres envoi e-mail
$config['param_mail'] = array(
	//'server'		=> 'mail.credit-municipal-toulouse.fr',
	'server'		=> 'localhost',
	//'server'		=> 'cmt.couleur-citron.com',
	'port'			=> 25,
//	'user'			=> 'webserver@credit-municipal-toulouse.fr',
//	'password'		=> 'Hbtr4mZ8',
	'user'			=> 'thao',
	'password'		=> 'tzbv2be',
	'from'			=> array('thao@couleur-citron.com', 'Couleur Citron'),
	'organization'	=> 'Couleur Citron', 
	'format'		=> 'txt'
);


// Ouverture de la connexion a la base de données
$dbc = mysql_connect($config['param_db']['server'], $config['param_db']['user'], $config['param_db']['pw']);
if(!$dbc){
	echo 'Connexion au serveur '.$config['param_db']['server'].' impossible';
	exit;
} else {
	echo "connexion mysql OK";
	if(!mysql_select_db($config['param_db']['base'], $dbc)){
		echo 'La base de données n\'existe pas';
		exit;
	}
} 
//boucle sur tous les uses
//$sql = "select * from mre-mipusers where tu_statut=".DEF_ID_STATUT_LIGNE."";
$query = 'select * from cms_rss_url where rssurl_statut= 4';
if (!$result = mysql_db_query($config['param_db']['base'], $query)) {echo $query;exit;}

if(mysql_num_rows($result) == 0){
	echo $query;exit;
} 
else {	 
	while($Donnees=mysql_fetch_array($result))  {
		$classeName = "cms_rss_url";
		$id = $Donnees['rssurl_id'];
		echo "<br />--- ".$id;
		$eNew_entries = set_new_flux ($classeName, $id); 
		echo '<br />'.$eNew_entries.' nouvelle(s) entrée(s).<br>';		  
    }	
} 
?>