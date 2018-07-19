<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/utils/dir.lib.php');

if($_SERVER['HTTPS'] == 'on'){
	/*$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	echo '<script type="text/javascript">';
	echo 'window.location.href="'.$url.'";';
	echo '</script>';
	echo '<noscript>';
	echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
	echo '</noscript>'; exit;*/
}




// on essaie de reconnaitre l'extension pour que le téléchargement corresponde au type de fichier afin d'éviter les erreurs de corruptions
if (is_get('export')) {
	include_once('../newsletter/newsletter_export.php');
	$sRep = $_SERVER['DOCUMENT_ROOT'].'/custom/newsletter/';
	$eFile = export_list_inscrits_envoi($sRep, $_GET['export']);
}
$Fichier_a_telecharger = str_replace('//', '/', $_GET['file']);
$chemin = str_replace('//', '/', $_GET['chemin']);

if (isset($chemin)&&($chemin!='')){
	if (strpos($Fichier_a_telecharger, $chemin)!==false){
		$Fichier_a_telecharger= str_replace($chemin, '', $Fichier_a_telecharger);
	}
}


/* check if iOS device - can't force download on those devices */
$iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");

if($iPod || $iPhone || $iPad){
    header("Location:".$chemin.$Fichier_a_telecharger);
}


if (preg_match ('/.*\.docx$/i',  basename($Fichier_a_telecharger))==1){ // docx
	$type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
}
elseif (preg_match ('/.*\.pptx$/i',  basename($Fichier_a_telecharger))==1){ // pptx
	$type = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
}
elseif (preg_match ('/.*\.xlsx$/i',  basename($Fichier_a_telecharger))==1){ // xlsx
	$type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
}
if (function_exists('finfo_open')){
	$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
	$type = finfo_file($finfo, $_SERVER['DOCUMENT_ROOT'].'/'.$chemin.$Fichier_a_telecharger);
}
else{
	switch(strrchr(basename($Fichier_a_telecharger), '.')) {
		case '.gz': $type = 'application/x-gzip'; break;
		case '.tgz': $type = 'application/x-gzip'; break;
		case '.zip': $type = 'application/zip'; break;
		case '.pdf': $type = 'application/pdf'; break;
		case '.png': $type = 'image/png'; break;
		case '.gif': $type = 'image/gif'; break;
		case '.jpg': $type = 'image/jpeg'; break;
		case '.txt': $type = 'text/plain'; break;
		case '.htm': $type = 'text/html'; break;
		case '.html': $type = 'text/html'; break;
		default: $type = 'application/octet-stream'; break;
	}
}

if($type === false){
    switch(strrchr(basename($Fichier_a_telecharger), '.')) {
            case '.gz': $type = 'application/x-gzip'; break;
            case '.tgz': $type = 'application/x-gzip'; break;
            case '.zip': $type = 'application/zip'; break;
            case '.pdf': $type = 'application/pdf'; break;
            case '.png': $type = 'image/png'; break;
            case '.gif': $type = 'image/gif'; break;
            case '.jpg': $type = 'image/jpeg'; break;
            case '.txt': $type = 'text/plain'; break;
            case '.htm': $type = 'text/html'; break;
            case '.html': $type = 'text/html'; break;
            default: $type = 'application/octet-stream'; break;
    }
}
//pre_dump($Fichier_a_telecharger);
//pre_dump($type); die();

if (is_file($chemin.$Fichier_a_telecharger)){
	// nada tout va bien
	$fullPathToFile = $chemin.$Fichier_a_telecharger;
}
elseif (is_file($_SERVER['DOCUMENT_ROOT'].$chemin.$Fichier_a_telecharger)){
	// nada tout va bien
	$fullPathToFile = $_SERVER['DOCUMENT_ROOT'].$chemin.$Fichier_a_telecharger;
}
elseif (is_file($_SERVER['DOCUMENT_ROOT'].'/'.$chemin.$Fichier_a_telecharger)){
	// nada tout va bien
	$fullPathToFile = $_SERVER['DOCUMENT_ROOT'].'/'.$chemin.$Fichier_a_telecharger;
}
else{
	error_log($chemin.$Fichier_a_telecharger. ' was not found');
	die($chemin.$Fichier_a_telecharger. ' was not found');
}



if (isDownloadable($fullPathToFile)===false){

	header('HTTP/1.1 401 Unauthorized');
	header('Connection: close');
	error_log('Hack attempt : '.$fullPathToFile);
	exit;
}
else{
	// on enleve les slashes après test downloadable
	//

	if (defined('DEF_UPLOAD_REMOVE_SLASHDIR') ) {
		if (DEF_UPLOAD_REMOVE_SLASHDIR)
			$fullPathToFile = removeToSlashDir($fullPathToFile);
	}
	else {
		$fullPathToFile = removeToSlashDir($fullPathToFile);
	}

	ini_set('zlib.output_compression','Off');

	header('HTTP/1.0 200 OK');
	header('Content-Description: File Transfer');
	header('Content-Disposition: attachment; filename="'.basename($Fichier_a_telecharger).'"');
	header("Content-Type: ".$type);
	//header("Content-Transfer-Encoding: ".$type."\n"); // Surtout ne pas enlever le \n
	header('Content-Transfer-Encoding: binary'."\n");

	if (ini_get('zlib.output_compression')==1){
		header('Content-Encoding: gzip');
	}
	else{
		header('Content-Length: '.filesize($fullPathToFile));
	}
	//header('Pragma: no-cache');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0, public');
	header('Cache-Control: private, max-age=0, must-revalidate'); // ajout dans le cas SSL
	header('Pragma: public'); // ajout dans le cas SSL
	header('Expires: 0');
	header('Connection: close');

	ob_end_clean();
	flush();
	set_time_limit(0);
	/*if (function_exists('virtual')&&(strpos($fullPathToFile, '../')===false) &&(strpos($fullPathToFile, $_SERVER['DOCUMENT_ROOT'])===true)) { // virtual si chemin viable en http
		virtual(str_replace($_SERVER['DOCUMENT_ROOT'], '', $fullPathToFile));
	}
	else{*/
		readfile($fullPathToFile);
	/*}*/
	exit;
}
?>