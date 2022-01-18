<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/lib/getid3/getid3.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

include_once($_SERVER['DOCUMENT_ROOT']."/include/cms-inc/lib/Mobile-Detect/Mobile_Detect.php");

$detect = new Mobile_Detect();

$host = $_SERVER['SERVER_NAME'];

$aFile = explode('_', $_GET['file']); 

//$file = $_GET['file']; 

if($aFile[0] == "videoParam"){

    $widthParam = $aFile[1];
    $heightParam = $aFile[2];
    $file = "";

    if(count($aFile) > 4){
        for($i=3; $i < count($aFile); $i++){
            if($i != 3)
                $file .= "_";
            $file .= $aFile[$i];
        }
    } else {
        $file = $aFile[3];
    }
} else{
    $file = $_GET['file'];
}

if (!isset($file) || ($file == '')){
	die("pas de video dans l'appel");
}
elseif (preg_match('/^[0-9]+$/si', $file)==1){ // param is un entier

	$aVideoBriques = dbGetObjectsFromFieldValue3('cms_content', array('getId_content', 'getStatut_content', 'getType_content'), array('equals', 'equals'), array($file, DEF_ID_STATUT_LIGNE, 'video'), NULL, NULL);
	
	if($aVideoBriques[0]){
		if (preg_match('/_vidURL=\/([^&]+)&/msi', $aVideoBriques[0]->getHtml_content(), $aMatches)==1){
			$file = $aMatches[1];
		}	
		elseif (preg_match('/_vidURL=[htps]{4,5}:\/\/[^\/]+(\/[^&]+)&/msi', $aVideoBriques[0]->getHtml_content(), $aMatches)==1){
			$file = $aMatches[1];
		}
		else{
			// aaaaaaaaaah !
		}
	}
	// DEPRECATED == classe video == AWS2006 only
	//elseif (class_exists('video')){ // si la classe video est activée
	//	$oVid = new video(intval($file));		
	//	$file = '/custom/upload/video/'.$oVid->get_flv();
	//}
	else{
		die('video non disponible');
	}	
} // fin if file == entier
else{
	// filename
}

$dir = str_replace(basename($file), '', $file);
$vidName = basename($file);

if ($dir==''){
	$dir='/custom/upload/video/';
}
$file = $dir.basename($file);

if (strpos($file, "/") > 0){
	if (preg_match('/^[htps]{4,5}/si', $file)){
		if (strpos($file, $_SERVER['HTTP_HOST']) > 0){
			$file = preg_replace('/^[htps]{4,5}:\/\/[^\/]+/si', '', $file);	
		}
		else{ // url distantes
		
		}
	}
	else{
		$file = "/".$file;
	}
}
$vidURL = $file;

include('flvmetadata.inc.php');

if ($detect->isMobile() || $detect->isTablet()){ //mobile OU une tablette	
	
	$vidPath = preg_replace('/^[htps]{4,5}:\/\/[^\/]+(\/.+)$/msi', '$1', $vidURL);		
	//pre_dump($vidPath); die();
	$durationHint = 51;
	
	if (class_exists('ffmpeg_movie')){ 
		$movie = new ffmpeg_movie($_SERVER['DOCUMENT_ROOT'].$vidPath);
		//if (preg_match('/vp6/msi', $movie->getVideoCodec())==1){				
			//echo 'pas jouable !';
		//}	
		$durationHint = floor($movie->getDuration());		
	}
	else{
		//echo 'pas ffmpeg_movie !';
	}	
		
	if ($aVideoBriques[0]){
		$width = round($aVideoBriques[0]->getWidth_content()/2);
		$height = round($aVideoBriques[0]->getHeight_content()/2);		
	}
	else{
                //pre_dump($width); pre_dump($width); die();
            if($widthParam != "" && $heightParam != ""){
                
                $width = round($widthParam);
		$height = round($heightParam);
            } else{
                $width = round($width/2);
		$height = round($height/2);
            }
	}
	
	echo '<video id="vid'.$file.'" width="'.$width.'" height="'.$height.'" durationHint="'.$durationHint.'" controls>';
	
	// test MP4
	if (is_file($_SERVER['DOCUMENT_ROOT'].preg_replace('/^(.+)\.[^\.]+$/si', '$1.mp4', $vidPath))){
		$vidPath = preg_replace('/^(.+)\.[^\.]+$/si', '$1.mp4', $vidPath);
		echo "<source src='".$vidPath."' type='video/mp4; codecs=\"avc1.4D401E, mp4a.40.2\"'/>";
	}
	else{
		//echo 'no file '.$_SERVER['DOCUMENT_ROOT'].preg_replace('/^(.+)\.[^\.]+$/si', '$1.mp4', $vidPath);
	}
	// test OGV
	if (is_file($_SERVER['DOCUMENT_ROOT'].preg_replace('/^(.+)\.[^\.]+$/si', '$1.ogv', $vidPath))){
		$vidPath = preg_replace('/^(.+)\.[^\.]+$/si', '$1.ogv', $vidPath);
		echo "<source src='".$vidPath."' type='video/ogg; codecs=\"theora, vorbis\"'/>";
	}
	else{
		//echo 'no file '.$_SERVER['DOCUMENT_ROOT'].preg_replace('/^(.+)\.[^\.]+$/si', '$1.ogv', $vidPath);
	}
	// test WEBM
	if (is_file($_SERVER['DOCUMENT_ROOT'].preg_replace('/^(.+)\.[^\.]+$/si', '$1.webm', $vidPath))){
		$vidPath = preg_replace('/^(.+)\.[^\.]+$/si', '$1.webm', $vidPath);
		echo "<source src='".$vidPath."' type='video/webm; codecs=\"vp8.0, vorbis\"'/>";
	}			
	else{
		//echo 'no file '.$_SERVER['DOCUMENT_ROOT'].preg_replace('/^(.+)\.[^\.]+$/si', '$1.webm', $vidPath);
	}		
	echo '</video>';
	exit;
}
else{	// cas pc
	if ($aVideoBriques[0]){
		echo '<!-- cms_content video -->';
		echo $aVideoBriques[0]->getHtml_content();
		exit;
	}		
}


//-------------------


( $_SERVER['HTTPS'] == "on" ) ? $protocol = "https" : $protocol = "http";


$vidURL = $protocol."://".$host.$dir.$vidName;
$phpURL = $protocol."://".$host."/backoffice/cms/utils/flvprovider.php";


$vidLocal = $_SERVER['DOCUMENT_ROOT'].$dir.$vidName;
if (!is_file($vidLocal)){
	die("pas de video à cette adresse: ".$dir.$vidName);
}

//autostart
$autostart = $_GET['autostart']; 
if (!isset($autostart)){
	$autostart = 1;
}   
//sizes
if (preg_match('/\.flv|mp4|mov$/si', basename($file))==1){
	$getID3 = new getID3;
	$ThisFileInfo = $getID3->analyze($vidLocal);	
	$w = $ThisFileInfo["video"]["resolution_x"];
	$h = $ThisFileInfo["video"]["resolution_y"];
}
else{ // trivial
	$w = 512;
	$h = 384; 
}

?>
<!-- file based video -->
<script src="/backoffice/cms/js/AC_RunActiveContent.js" type="text/javascript"></script>
<script type="text/javascript">
videoSrcObject = "/backoffice/cms/utils/scrubberLarge?_vidName=<?php echo $vidName; ?>&_vidURL=<?php echo $vidURL; ?>&_phpURL=<?php echo $phpURL; ?>&_start=0&_end=0&_autostart=<?php echo $autostart; ?>&embedUrl="+escape(document.location.href);
videoSrcEmbed = "/backoffice/cms/utils/scrubberLarge?_vidName=<?php echo $vidName; ?>&_vidURL=<?php echo $vidURL; ?>&_phpURL=<?php echo $phpURL; ?>&_start=0&_end=0&_autostart=<?php echo $autostart; ?>&embedUrl="+escape(document.location.href);
flashvars = "_vidName=<?php echo $vidName; ?>&_vidURL=<?php echo $vidURL; ?>&_phpURL=<?php echo $phpURL; ?>&_start=0&_end=0&_autostart=<?php echo $autostart; ?>&embedUrl="+escape(document.location.href);
swfSrcFull = "/backoffice/cms/utils/scrubberLarge.swf";
swfSrc = "/backoffice/cms/utils/scrubberLarge";

AC_FL_RunContent( 'codebase','https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,1,0,0','id', 'awsvid', 'name', 'awsvid', 'width','<?php echo $w; ?>','height','<?php echo $h; ?>','src',swfSrc,'wmode','opaque','quality','high','scale', 'noScale', 'pluginspage','https://get.adobe.com/flashplayer/','movie',swfSrc, "salign", "", "flashvars", flashvars );

function videoSizeUpdate(w, h){
	//alert(w+" / "+h);
	//document.getElementById('awsvid').width = w;
	//document.getElementById('awsvid').height = h;
	
	a = document.getElementsByTagName("embed");
	for (i in a){
		if (a[i].src != undefined){
			if (a[i].src == swfSrcFull){
				a[i].width = w;
				a[i].height = h; 
			}
			
		}
	}
	
	a = document.getElementsByTagName("object");	
	for (i in a){
		if (a[i].id != undefined){
			if (a[i].id == 'awsvid'){
				a[i].width = w;
				a[i].height = h; 		
			}	
		}
	}
}
</script>