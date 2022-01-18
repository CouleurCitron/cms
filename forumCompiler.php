<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
activateMenu('gestioncontenu');  //permet de dérouler le menu contextuellement
// Preview ou Enregistrement de la brique de chat en fonction de $_POST['FORUMsubmitType']

// Initialisation des variable passé en POST
$FORUMtype = $_POST['FORUMtype'];
$FORUMtext = $_POST['FORUMcontent'];
$FORUMbdVerte = $_POST['FORUMbdVerte'];
$FORUMsubmitType = $_POST['FORUMsubmitType'];
$FORUMtopic = $_POST['FORUMtopic'];
$FORUMname = $_POST['FORUMname'];

// Url à ouvrir passer au fichier flash en dynamiquement
if($FORUMtype <>  2){
	$iWidth = 180;
	$iHeight = 100;
	$strUrlToGo = "/modules/forum/viewforum.php?f=".$FORUMtopic; 
}else{
	$iWidth = 173;
	$iHeight = 48;
	$strUrlToGo = "/modules/forum/index.php"; 
}

// Construction de url du fichie flash à insérer
$baseImgUrl = $_SERVER['DOCUMENT_ROOT']."/img/fo/cms/forum/";
$nameImg = "forum_0".$FORUMtype.".swf";

// Récupération des différentes informations du fichier flash (taille, type etc...)
$tbImgInfo = getimagesize($baseImgUrl.$nameImg);

// Construction de l'objet flash à utiliser
$flashObj = "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,1,0,0\" width=\"".$tbImgInfo[0]."\" height=\"".$tbImgInfo[1]."\" name=\"FORUMflash\">\n"
	. "<param name=movie value=\"/img/fo/cms/forum/".$nameImg."?urlToGo=".$strUrlToGo."&strTarget=_blank\">\n"
	. "<param name=quality value=high>\n"
	. "<embed src=\"/img/fo/cms/forum/".$nameImg."?urlToGo=".$strUrlToGo."&strTarget=_blank\" swLiveConnect=\"true\" quality=high pluginspage=\"https://get.adobe.com/flashplayer/\" type=\"application/x-shockwave-flash\" width=\"".$tbImgInfo[0]."\" height=\"".$tbImgInfo[1]."\" name=\"FORUMflash\">\n"
	. "</embed>"
	. "</object>";

if($FORUMtype == 1 || $FORUMtype == 3){
	$strBrick = "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"180\" bgcolor=\"#FFFFFF\">\n"
		. "<tr><td>".$flashObj."</td></tr>\n"
		. "<tr><td><img src=\"/img/vide.gif\" height=\"5\" width=\"10\"></td></tr>\n"
		. "<tr><td>".$FORUMtext."</td></tr>\n"
		. "</table>";
}else{
	$strBrick = "<table border=\"0\" cellpadding=\"1\" cellspacing=\"0\" width=\"173\">\n";
		
	// Gestion de l'affichage de la bande verte à gauche ou à droite
	if($FORUMbdVerte == 'left'){ // Gauche
		$strBrick .= 	"<tr>\n"
			."<td width=\"9\" bgcolor=\"#FFFFFF\">&nbsp;</td>\n"
			."<td bgcolor=\"#6AAE1A\" width=\"162\">\n"
			."<table bgcolor=\"#FFFFFF\" border=\"0\" cellspacing=\"0\" valign=\"middle\" cellpadding=\"0\" width=\"162\" height=\"46\">\n"
			."<tr height=\"46\">\n"
			."<td width=\"20\" bgcolor=\"#6AAE1A\">&nbsp;</td>\n"
			."<td width=\"131\" valign=\"middle\" align=\"right\">".$flashObj."\n</td>\n"
			."<td width=\"9\" bgcolor=\"#FFFFFF\">&nbsp;</td>\n"
			."</tr>\n"
			."</table>\n</td>\n</tr>\n";
	}else{	// Droite
		$strBrick .=  "<tr>\n"
    	."<td bgcolor=\"#6AAE1A\" width=\"162\">\n"
			."<table bgcolor=\"#FFFFFF\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"162\" height=\"46\">\n"
			."<tr height=\"46\">\n"
			."<td width=\"9\" bgcolor=\"#FFFFFF\">&nbsp;</td>\n"
			."<td width=\"131\" align=\"left\" valign=\"middle\">".$flashObj."</td>\n"
			."<td width=\"20\" bgcolor=\"#6AAE1A\">&nbsp;</td>\n</tr>\n"
			."</table>"
			."<td width=\"9\" bgcolor=\"#FFFFFF\">&nbsp;</td>\n</tr>\n";
	}

	$strBrick .= "</table>";
}

if($FORUMsubmitType == 'preview'){
	// Affichage de la preview
	echo "<div style=\"width: ".$iWidth."px; height: ".$iHeight."px; background-color: #ffffff; overflow: auto;\">"
	. $strBrick
	. "</div>";
	
}else{
	// Envoi par formulaire pour stockage en base de données
	echo "<form name=\"FORUMform\" method=\"post\" action=\"briques/saveComposant.php\">\n"
		."<input type=\"hidden\" name=\"HTMLcontent\" value='".$strBrick."'>\n"
		."<input type=\"hidden\" name=\"TYPEcontent\" value=\"forum\">\n"
		."<input type=\"hidden\" name=\"NAMEcontent\" value=\"".$FORUMname."\">\n"
		."<input type=\"hidden\" name=\"WIDTHcontent\" value=\"".$iWidth."\">\n"
		."<input type=\"hidden\" name=\"HEIGHTcontent\" value=\"".$iHeight."\">\n"
		."</form>\n"
		."<script language=\"javascript\" type=\"text/javascript\">\n"
		."<!--\n"
		."document.forms['FORUMform'].submit()\n"
		."//-->\n"
		."</script>";
}
?>