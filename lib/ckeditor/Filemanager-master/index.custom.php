<?php

error_reporting(0);
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php'); // classe de customisation des classes
 
$translator =& TslManager::getInstance(); 	
 

if (!preg_match ("/Filemanager-master/", $_SERVER['HTTP_REFERER'])) {
	unset ($_SESSION["ck_filemanager_path"]); 
}
  
if (!isset($_SESSION["ck_filemanager_path"]) && $_SESSION["ck_filemanager_path"] == '' && $_GET["dir"] != '') {
	$_SESSION["ck_filemanager_path"] = $_GET["dir"]; 
} 
else if (isset($_SESSION["ck_filemanager_path"]) && $_SESSION["ck_filemanager_path"] != '' &&  $_GET["dir"] != '') {
	$_SESSION["ck_filemanager_path"] = $_GET["dir"];
}   
 

$aClasse = dbGetObjectsFromFieldValue("classe", array("get_statut"),  array(DEF_ID_STATUT_LIGNE), NULL); 

$src = '' ;
 
if ( $_GET["source"] == "initvideo") {

 /*
?>

<div id="changeressource">
<select id="typeressource" name="typeressource">
	<option value="" <?php if ($_GET["dir"] == '') echo "selected"; ?>><?php $translator->echoTransByCode('Type_de_fichier'); ?></option>
	
	 
	<?php if (is_dir ($_SERVER['DOCUMENT_ROOT'].'/custom/swf/'.$_SESSION['rep_travail'].'/')) { ?>
	<option value="/custom/swf/<?php echo $_SESSION['rep_travail']; ?>/&langCode=<?php echo $_SESSION["site_langue"];?>&source=initvideo" <?php if ($_GET["dir"] == '/custom/swf/'.$_SESSION['rep_travail'].'/') echo "selected"; ?>>Flash</option>  
	<?php } ?>
	<?php if (is_dir ($_SERVER['DOCUMENT_ROOT'].'/custom/video/'.$_SESSION['rep_travail'].'/')) { ?>
	<option value="/custom/video/<?php echo $_SESSION['rep_travail']; ?>/&langCode=<?php echo $_SESSION["site_langue"];?>&source=initvideo" <?php if ($_GET["dir"] == '/custom/video/'.$_SESSION['rep_travail'].'/') echo "selected"; ?>>Videos</option>  
	<?php } ?>
	 
	
	?>
</select>
</div>

<?php
*/

}
else if ( $_GET["source"] != "init") {

  
$src.= '<div id="changeressource">';
$src.= '<select id="typeressource" name="typeressource">';
	$src.= '<option value="" ';
	if ($_GET["dir"] == '') $src.= "selected"; 
	$src.= '>'.$translator->getTransByCode('Type_de_fichier').' - '.$_GET['CKEditor'].'</option>';
	
	$param = "idField=".$_GET["idField"]."&idForm=".$_GET["idForm"]."&height=".$_GET["height"]."&CKEditor=".$_GET['CKEditor']; 
	
	if (is_dir ($_SERVER['DOCUMENT_ROOT'].'/custom/img/'.$_SESSION['rep_travail'].'/')) { 
		$src.= '<option value="/custom/img/'.$_SESSION['rep_travail'].'/&langCode='.$_SESSION["site_langue"].'&'.$param.'" ';
		if ($_GET["dir"] == '/custom/img/'.$_SESSION['rep_travail'].'/') $src.=  "selected";
		$src.= '>'.$translator->getTransByCode('Images').'</option>';
	}
	if (is_dir ($_SERVER['DOCUMENT_ROOT'].'/content/'.$_SESSION['rep_travail'].'/')) {
		$src.= '<option value="/content/'.$_SESSION['rep_travail'].'/&langCode='.$_SESSION["site_langue"].'&'.$param.'" ';
		if ($_GET["dir"] == '/content/'.$_SESSION['rep_travail'].'/') $src.=  "selected";
		$src.= '>'.$translator->getTransByCode('fichiers').'</option>';
	}
	if (is_dir ($_SERVER['DOCUMENT_ROOT'].'/custom/swf/'.$_SESSION['rep_travail'].'/')) {
		$src.= '<option value="/custom/swf/'.$_SESSION['rep_travail'].'/&langCode='.$_SESSION["site_langue"].'&'.$param.'" ';
		if ($_GET["dir"] == '/custom/swf/'.$_SESSION['rep_travail'].'/') $src.=  "selected";
		$src.= '>Flash</option>'; 
		if (is_dir ($_SERVER['DOCUMENT_ROOT'].'/custom/video/'.$_SESSION['rep_travail'].'/')) { 
			$src.= '<option value="/custom/video/'.$_SESSION['rep_travail'].'/&langCode='.$_SESSION["site_langue"].'&source=initvideo&'.$param.'" ';
			if ($_GET["dir"] == '/custom/video/'.$_SESSION['rep_travail'].'/') $src.=  "selected";
			$src.= '>Videos</option>  ';
		} 
	} 
	if (is_dir ($_SERVER['DOCUMENT_ROOT'].'/pdf/')) {
		$src.= '<option value="/pdf/&langCode='.$_SESSION["site_langue"].'&'.$param.'" ';
		if ($_GET["dir"] == '/pdf/') $src.=  "selected";
		$src.= '>Pdf</option>';
	}
	
	
	
	if (count($aClasse) > 0){
		foreach($aClasse as $cKey => $oClasse){				
			if (($oClasse->get_cms_site() == -1) || ($oClasse->get_cms_site() == $_SESSION['idSite'])){					
				if (is_dir($_SERVER['DOCUMENT_ROOT'].'/custom/upload/'.$oClasse->get_nom())){
					//echo "la classe ".$oClasse->get_nom()." <br />\n";
					//echo '	[\''.$oClasse->get_nom().'\',\''.$oClasse->get_nom().'\'],'; 
					$src.=  '<option value="/custom/upload/'.$oClasse->get_nom().'/&langCode='.$_SESSION["site_langue"].'&'.$param.'" ';
					if ($_GET["dir"] == '/custom/upload/'.$oClasse->get_nom().'/') $src.=  "selected";
					$src.=  '>'.ucfirst($oClasse->get_nom()).'</option>';
				}			
			}		
		}
	}
	
	
$src.= '</select>';
$src.= '</div>';
 
}

?>