<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT']."/include/cms-inc/lib/Mobile-Detect/Mobile_Detect.php");

$detect = new Mobile_Detect();

if(is_get('globalCache')){
    $aFile = explode('/', $_GET['file']);
    $sDirFileCdn = $_SERVER['DOCUMENT_ROOT'];
    $sDirFile = $_SERVER['DOCUMENT_ROOT'];

    //traitement de la taille max
    $widthMax=2800;
    $heightMax=2800;

    $sPreFile = $aFile[0];
    $aParamsFile = explode("_", $sPreFile);

    if($aParamsFile[1]!= "")
        $widthMax = (int)$aParamsFile[1];


    if($aParamsFile[2]!= "")
        $heightMax = (int)$aParamsFile[2];
} else{
    $sDirFile = $_GET['file'];
    $widthMax = 240;
    $heightMax = 2800;
}

for($i=1; $i<count($aFile); $i++){
        $sDirFile .= "/".$aFile[$i];
        
        if($i == count($aFile)-1){
            $aFileData = explode('.',$aFile[$i]);
            $sDirFileCdn .= '/cdn'.'/'.$aFileData[0].'_'.$widthMax.'_'.$heightMax.'.'.$aFileData[1];
        }else{
            $sDirFileCdn .= "/".$aFile[$i];
        }
        
    }



$Fichier_a_telecharger = str_replace('//', '/', $sDirFile);
$chemin = str_replace('//', '/', $_GET['chemin']);

$Fichier_a_telecharger = str_replace($chemin, '', $Fichier_a_telecharger);
	

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

switch(strrchr(basename($fullPathToFile), '.')) {
	case '.gz': $mimeType = 'application/x-gzip'; break;
	case '.tgz': $mimeType = 'application/x-gzip'; break;
	case '.zip': $mimeType = 'application/zip'; break;
	case '.pdf': $mimeType = 'application/pdf'; break;
	case '.png': $mimeType = 'image/png'; break;
	case '.gif': $mimeType = 'image/gif'; break;
	case '.jpg': $mimeType = 'image/jpeg'; break;
	case '.txt': $mimeType = 'text/plain'; break;
	case '.htm': $mimeType = 'text/html'; break;
	case '.html': $mimeType = 'text/html'; break;
	case '.air': $mimeType = 'application/air'; break;
	default: $mimeType = 'application/octet-stream'; break;
}

//pre_dump(preg_match('/cdn/si', $aFile[0]));

if (preg_match('/\/cdn\//si', $fullPathToFile)==1 || preg_match('/cdn/si', $aFile[0])==1){ // cas gestion CDN 
        
	if(!is_get('globalCache')){
		dirExists(str_replace(array('/cdn/', basename($fullPathToFile)), array('/cdn-cache/', ''), $fullPathToFile));
		dirExists(str_replace(array('/cdn/', basename($fullPathToFile)), array('/cdn-mobcache/', ''), $fullPathToFile));
	} else {
		dirExists(str_replace(array('/cdn/', basename($sDirFileCdn)), array('/cdn-cache/', ''), $sDirFileCdn));
		dirExists(str_replace(array('/cdn/', basename($sDirFileCdn)), array('/cdn-mobcache/', ''), $sDirFileCdn));
	}	
        
        
	if ($detect->isMobile() || $detect->isTablet()){ //mobile OU tablette
		//error_log('cdn mobile');
		if(!is_get('globalCache')){
			$fullPathToCache = str_replace('/cdn/', '/cdn-mobcache/', $fullPathToFile);
		   // $aOrigStat = stat($fullPathToFile);
			$mTime = filemtime($fullPathToFile);
		} else {
			$fullPathToCache = str_replace('/cdn/', '/cdn-mobcache/', $sDirFileCdn);
			//$aOrigStat = stat($sDirFile);
			$mTime = filemtime($sDirFile);
		}                
		
		//$aCacheStat = @stat($fullPathToCache);
		$mTimeCache = @filemtime($fullPathToCache);		
	
		//if ($aCacheStat && ($aOrigStat['mtime']<$aCacheStat['mtime'])){ // ok, send !!
		if ($mTimeCache && ($mTime<$mTimeCache)){ // ok, send !!
			// ras	
			//error_log('cdn mobile ras');	
		}
		else{
			if(!is_get('globalCache')){
				list($width, $height, $type, $attr) = getimagesize($fullPathToFile);
			} else {
				list($width, $height, $type, $attr) = getimagesize($sDirFile);
				//pre_dump($width);
			}
		
			if ($width<=$widthMax && $height <= $heightMax){ // no resize needed
                            
				if(!is_get('globalCache')){
					copy($fullPathToFile, $fullPathToCache);
				}else{
					copy($sDirFile, $fullPathToCache);
				}
				//error_log('cdn mobile COPY');
			}
			else{ // resize
				$oIm = imagecreatefromAnyFile($fullPathToFile);			
				$oIm = resizeImageObjectWidthHeightWise($oIm, $widthMax, $heightMax);
				imageoutputtoAnyFile($oIm, $fullPathToCache);	
				//error_log('cdn mobile RESIZE');
			}	
		}		
	}
	else{ //pc
		//error_log('cdn screen');
//		$fullPathToCache = str_replace('/cdn/', '/cdn-cache/', $fullPathToFile);
//		$aOrigStat = stat($fullPathToFile);                
                
		if(!is_get('globalCache')){
			$fullPathToCache = str_replace('/cdn/', '/cdn-cache/', $fullPathToFile);
			//$aOrigStat = stat($fullPathToFile);
			$mTime = filemtime($fullPathToFile);
		} else {
			$fullPathToCache = str_replace('/cdn/', '/cdn-cache/', $sDirFileCdn);
			//$aOrigStat = stat($sDirFile);
			$mTime = filemtime($sDirFile);
		}
                
                
		//$aCacheStat = @stat($fullPathToCache);
		$mTimeCache = @filemtime($fullPathToCache);
	
		//if ($aCacheStat && ($aOrigStat['mtime']<$aCacheStat['mtime'])){ // ok, send !!
		if ($mTimeCache && ($mTime<$mTimeCache)){ // ok, send !!
			// ras		
		}
		else{
			//copy($fullPathToFile, $fullPathToCache);
			if(!is_get('globalCache')){
				copy($fullPathToFile, $fullPathToCache);
			}else{
				copy($sDirFile, $fullPathToCache);
			}	
			//error_log('cdn screen COPY');	
		}
	}	
	$fullPathToFile = $fullPathToCache;
}

if (is_file($fullPathToFile)){

	header('HTTP/1.0 200 OK'); 
	header('Content-Disposition: inline; filename="'.basename($Fichier_a_telecharger).'"'); 
	header("Content-Type: ".$mimeType); 
	header('Content-Transfer-Encoding: binary'."\n");
	header('Content-Length: '.filesize($fullPathToFile)); 
	//header('Pragma: no-cache'); 
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0, public'); 
	header('Expires: 0'); 
	header('Connection: close');  
	ob_clean();
	flush();
	set_time_limit(0);
	if (function_exists('virtual')) {		
		//$aInfo = apache_lookup_uri (str_replace($_SERVER['DOCUMENT_ROOT'], '', $fullPathToFile));
		//error_log('virtual : '.str_replace($_SERVER['DOCUMENT_ROOT'], '', $fullPathToFile).' - '.$aInfo->status.' - '.$mimeType);		
		if (virtual(str_replace($_SERVER['DOCUMENT_ROOT'], '', $fullPathToFile))){
			// ras
			//error_log('virtual ok');
		}
		else{
			//error_log('virtual failed'); 
			readfile($fullPathToFile);
		}
	}
	else{
		readfile($fullPathToFile);
	}
}
else{
	header('HTTP/1.0 404');
}
exit;
?>