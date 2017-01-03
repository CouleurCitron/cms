<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

if (strpos($_SERVER['REQUEST_URI'], 'ls.inc.php')!==false){
	// hack attempt
	error_log('hack attempt on '.$_SERVER['PHP_SELF'].' from '.$_SERVER['REMOTE_ADDR']);
	unset($_SESSION['FO']);
	$sRetour = '';
	die();	
}

function fileToIcon($filepath){
	if (is_dir($filepath)){
		return '/icons/folder.png';	
	}
	else{
		$filename = basename($filepath);		
		$ext = preg_replace('/.*\.([^\.]+)/msi', '$1', $filename);
		if (preg_match('/png|gif|jpeg|jpg|bmp|tif|tiff/msi', $ext)){
			return '/backoffice/cms/img/filemanager/image2.png';			
		}
		elseif (preg_match('/zip|7z|tar|gz|rar/msi', $ext)){
			return '/backoffice/cms/img/filemanager/compressed.png';			
		}
		elseif (preg_match('/swf|mpeg|mpg|avi|mp4|mov|flv/msi', $ext)){
			return '/backoffice/cms/img/filemanager/movie.png';			
		}
		elseif (preg_match('/doc|rtf|html|htm|oft|pdf|xls/msi', $ext)){
			return '/backoffice/cms/img/filemanager/layout.png';			
		}
		else{
			return '/backoffice/cms/img/filemanager/unknown.png';			
		}
	}
}

$baseDir = preg_replace("/(.+\/)[^\.^\/]+\.[^\.^\/]+/msi", "$1", $_SERVER["PHP_SELF"]);


if ($_GET['dir'] == ''){
	echo '<p>Index of \'/\'</p>';
}
else{
	echo '<p>Index of '.$_GET['dir'].'</p>';
}


echo '<table width="100%" cellpadding="0" cellspacing="8">';
echo '<tr>';
echo '<td width="40" >&nbsp;</td>';
echo '<td width="200"><a href="'.$_SERVER["PHP_SELF"].'?dir='.$_GET['dir'].'&amp;N=';
if ($_GET['N'] == 'A'){
	echo 'D';
}
else{
	echo 'A';
}
echo '">Name</a></td>';
echo '<td width="150"><a href="'.$_SERVER["PHP_SELF"].'?dir='.$_GET['dir'].'&amp;M=';
if ($_GET['M'] == 'A'){
	echo 'D';
}
else{
	echo 'A';
}
echo '">Last modified</a></td>';
echo '<td width="50"><a href="'.$_SERVER["PHP_SELF"].'?dir='.$_GET['dir'].'&amp;S=';
if ($_GET['S'] == 'A'){
	echo 'D';
}
else{
	echo 'A';
}
echo '">Size</a></td>';
echo '<td>&nbsp;</td>';
echo '</tr>';
echo '<tr>';
echo '<td colspan="5"><hr/></td>';
echo '</tr>';
if ($_GET['dir'] != ''){
	echo '<tr>';
	echo '<td ><img src="/backoffice/cms/img/filemanager/back.gif" alt="back" /></td>';
	$linkUp = $_SERVER['PHP_SELF'].'?dir='.preg_replace('/(.*)\/[^\/]+/msi', '$1', $_GET['dir']);
	echo '<td id="col2"><a href="'.$linkUp.'" title="Parent Directory">Parent Directory</a></td>';
	echo '<td >&nbsp; </td>';
	echo '<td id="col4">&nbsp;</td>';
	echo '<td>&nbsp;</td>';
	echo '</tr>';
}


//$_localDir = $_SERVER['DOCUMENT_ROOT'].$_GET['dir'];

if ($dir = @opendir($_localDir)) {
	$aFiles = array();	
	$aFiles[] = array(); // files
	$aFiles[] = array(); // mdate
	$aFiles[] = array(); // size
	while (($file = readdir($dir)) !== false) {
		if (is_file($_localDir."/".$file) || is_dir($_localDir."/".$file) && ($file != '.') && ($file != '..') && ($file != 'CVS') && !preg_match('/^_.*$/msi', $file)) {
			$aFiles[0][] = $file;
			$aStat = stat($_localDir."/".$file);
			$aFiles[1][] = $aStat['mtime'];
			$aFiles[2][] = $aStat['size'];
		}
	}

	//sort($aFiles);
	if ($_GET['M'] == 'A'){
		array_multisort($aFiles[1], SORT_ASC, $aFiles[0], $aFiles[2]);
	}
	elseif ($_GET['S'] == 'A'){
		array_multisort($aFiles[2], SORT_ASC, $aFiles[0], $aFiles[1]);
	}
	elseif ($_GET['N'] == 'D'){
		array_multisort($aFiles[0], SORT_DESC, $aFiles[1], $aFiles[2]);
	}
	elseif ($_GET['M'] == 'D'){
		array_multisort($aFiles[1], SORT_DESC, $aFiles[0], $aFiles[2]);
	}
	elseif ($_GET['S'] == 'D'){
		array_multisort($aFiles[2], SORT_DESC, $aFiles[0], $aFiles[1]);
	}
	else { // default Name
		array_multisort($aFiles[0], SORT_ASC, $aFiles[1], $aFiles[2]);
	}
	
	

	for($i=0;$i<count($aFiles[0]);$i++){
		$file = $aFiles[0][$i];
				
		echo '<tr>';
		echo '<td ><img src="'.fileToIcon($_localDir."/".$file).'" alt=""  /></td>';
		
		if (($keyword == $_SERVER['HTTP_HOST']) && is_dir($_localDir."/".$file)){
			$subDo = 'http://'.str_replace($_SERVER['DOCUMENT_ROOT'], '', $_localDir).$file.'.'.$_SERVER['HTTP_HOST'];		
		}
		else{
			$subDo = str_replace($_SERVER['DOCUMENT_ROOT'], '', $_localDir);	
		}
		
		if (is_dir($_localDir."/".$file)){
			echo '<td id="col3"><a href="'.$_SERVER['PHP_SELF'].'?dir='.$_GET['dir'].'/'.rawurlencode($file).'" title="'.$file.'" id="1">'.$file.'</a></td>';
		}
		else{
			//echo '<td id="col3"><a href="'.$subDo.'/'.rawurlencode($file).'" title="'.$file.'" id="2">'.$file.'</a></td>';
			echo '<td id="col3"><a href="/backoffice/cms/filemanager/frontoffice/serve.php?file='.rawurlencode($file).'&chemin='.$subDo.'/" title="'.$file.'" id="2">'.$file.'</a></td>';
		}
	
		echo '<td >'.date('d-M-Y H:i:s', $aFiles[1][$i]).'</td>';
		$size = $aFiles[2][$i];
		if (is_dir($_localDir."/".$file)){
			$size = '';
		}
		elseif ($size > (1024*1024*1024)){
			$size =  round($size/(1024*1024*1024), 2).' G';
		}
		elseif ($size > (1024*1024)){
			$size =  round($size/(1024*1024), 2).' M';
		}
		elseif ($size > 1024){
			$size =  round($size/1024, 2).' k';
		}
		
		echo '<td id="col4">'.$size.'</td>';
		echo '<td>&nbsp;</td>';
		echo '</tr>';

	}	  
	
	closedir($dir);
}


echo '</table>';
?>