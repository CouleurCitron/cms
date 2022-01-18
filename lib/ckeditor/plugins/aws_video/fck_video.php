<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
 	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
	 
	
	if ($_POST["action"] && $_POST["action"]!='') {  
		$oObj = getObjectById("cms_content", $_POST["action"]); 
	} 
	else {
		$aObj = dbGetObjectsFromFieldValue("cms_content", array ("getType_content", "getStatut_content", "getActif_content") , array ("video", DEF_ID_STATUT_LIGNE, 1), "getName_content" );
	}
	
	
	
?>
 
 
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
<!--
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2005 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * "Support Open Source software. What about a donation today?"
 * 
 * File Name: fck_checkbox.html
 * 	Checkbox dialog window.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
-->
<html>
	<head>
		<title>Vidéo</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
		<meta content="noindex, nofollow" name="robots">
		<script src="common/fck_dialog_common.js" type="text/javascript"></script>
		<script type="text/javascript">


var oEditor	= window.parent.InnerDialogLoaded() ;
var FCK		= oEditor.FCK ;

// Gets the document DOM
var oDOM = oEditor.FCK.EditorDocument ;
 
 
var oAnchor ;
//alert(oFakeImage+' '+FCK.Selection.getSelectedText());

 

function getSelectedText() 
 { 
    var selection = "";
    if( FCK.EditorDocument.selection != null ) {
      selection = FCK.EditorDocument.selection.createRange().text; // (Internet Explorer)
   } 
    else {
      selection = oEditor.FCK.EditorWindow.getSelection(); // (FireFox) after this, won't be a string 
      selection = "" + selection; // now a string again
    }
    return selection;
 }


if(FCK.Selection.GetType() == 'Text') {
	 
	var textSelected ;
	textSelected = getSelectedText();  
	
}
 

window.onload = function()
{
	// Translate the dialog box texts.
	oEditor.FCKLanguageManager.TranslatePage(document) ;
	oAnchor = null ;
	
	<?php
	if (is_post("pickparam")) {
		echo 'window.parent.SetOkButton( true ) ;';
	}
	else {
		echo 'window.parent.SetOkButton( false ) ;';
	}
	?>
		
		
}


function strip_tags(html)
    {
    //PROCESS STRING
    if(arguments.length < 3) {
     html=html.replace(/<\/?(?!\!)[^>]*>/gi, '');
    } else {
     var allowed = arguments[1];
     var specified = eval("["+arguments[2]+"]" );
     if(allowed){ 
      var regex='</?(?!(' + specified.join('|') + '))\b[^>]*>';
      html=html.replace(new RegExp(regex, 'gi'), '');
     } else{
      var regex='</?(' + specified.join('|') + ')\b[^>]*>';
      html=html.replace(new RegExp(regex, 'gi'), '');
     }
    }
    //CHANGE NAME TO CLEAN JUST BECAUSE  
    var clean_string = html;
    //RETURN THE CLEAN STRING
    return clean_string;
} 

function Ok()
{  
	
	if ( GetE("sBodyHTML").value.length == 0 )
	{
		alert( oEditor.FCKLang.DlgAnchorErrorName ) ;
		return false ;
	}
	
	oEditor.FCKUndo.SaveUndoStep() ;
	//alert(textSelected)
 
	
	//oAnchor		= FCK.EditorDocument.createElement( 'DIV' ) ;
	//oAnchor.innerHTML = '<a name="' + GetE(myname).value + '">'+ textSelected +'<\/a>' ;
	 
	//oAnchor.innerHTML = '<a class="tt" href="#">'+ myText +'<span class="tooltip"> '+ GetE(myvalue).value +'<\/span><\/a>' ;  
	//oAnchor = oAnchor.firstChild ;

	 
	/*oFakeImage	= oEditor.FCKDocumentProcessors_CreateFakeImage( 'FCK__Glossary', oAnchor ) ; 
	oFakeImage.setAttribute( '_fckanchor', 'true', 0 ) ; 
	oFakeImage	= FCK.InsertElementAndGetIt( oFakeImage ) ; */

 	//oEditor.FCK.InsertHtml( '<a name="' + GetE('txtName').value + '">'+ textSelected +'<\/a>' ) ;
	oEditor.FCK.InsertHtml(GetE("sBodyHTML").value) ;
	
	window.parent.close() ;
	 
	return true;
 
}


function Suite(id)
{   
	document.form_video.action.value=id;
	document.form_video.submit();
	 
 
}


  		</script>
		<style type="text/css">
		#btnOk {
			display:none;
		}
		</style>
	</head>
	<body  scroll="yes">
		<table height="100%" width="100%"  >
			<tr>
				<td align="left">
					 <form id="form_video" name="form_video" action="#" method="post">
					 <input type="hidden"   id="action" name="action"  value=""> 
					<?php 
					
						if ($_POST["action"] && $_POST["action"]!='') {
							echo '<strong>'.$oObj->getName_content().'</strong><br/><br/>';
							
							 
							
							$id =  $oObj->getId_content();   
							$svideo = $oObj->getName_content();
							$height = $oObj->getHeight_content();
							$width = $oObj->getWidth_content(); 
							$sBodyHTML = $oObj->getHtml_content();	
							 
							$svideoUrl = preg_replace("/.*_vidURL=(.+)&_phpURL.*/msi", "$1", $sBodyHTML);
							$svideoUrl = preg_replace("/http:\/\/[^\/]+(\/.*)/msi", "$1", $svideoUrl);
							if (preg_match ("/_vidThb/", $sBodyHTML)) $vignette = flashurl_decode(preg_replace("/.*_vidThb=([^&]*)&.*/msi", "$1", $sBodyHTML));
							else $vignette = '';
							$paramStart = flashurl_decode(preg_replace("/.*_start=([^&]+)&.*/msi", "$1", $sBodyHTML));
							$paramEnd = flashurl_decode(preg_replace("/.*_end=([^&]+)&.*/msi", "$1", $sBodyHTML));
							$paramAutostart = flashurl_decode(preg_replace("/.*_autostart=([^&]+)&.*/msi", "$1", $sBodyHTML));
							$paramOnEnd = flashurl_decode(preg_replace("/.*_onEnd=([^&]*)&.*/msi", "$1", $sBodyHTML));
							$paramShowUI = flashurl_decode(preg_replace("/.*_showUI=([^&]+)&.*/msi", "$1", $sBodyHTML));
							if (preg_match('/.*_loop=([^&]+)&.*/', $sBodyHTML)==1){
								$paramLoop = flashurl_decode(preg_replace("/.*_loop=([^&]+)&.*/msi", "$1", $sBodyHTML));
							}
							else{
								$paramLoop = 0;
							}
							if (preg_match('/.*_volume=([^&]+)&.*/', $sBodyHTML)==1){			
								$paramVolume = flashurl_decode(preg_replace("/.*_volume=([^&]+)&.*/msi", "$1", $sBodyHTML));
							}
							else{
								$paramVolume = 100;
							}
							if (preg_match('/.*_onRollOver=([^&]+)&.*/', $sBodyHTML)==1){
								$paramOnRollOver = flashurl_decode(preg_replace("/.*_onRollOver=([^&]+)&.*/msi", "$", $sBodyHTML));
							}
							else{
								$paramOnRollOver = '';
							}
							 
							?>
							 
							 
							<br /> 
							<span class="arbo2"><strong>Choix des param&egrave;tres pour <?php echo $oObj->getName_content(); ?> :</strong></span><br />
							<br />
							<span class="arbo2">Nom param&egrave;tre / Valeur</span><br />
							<?php
							
							echo "<div id=\"rows\" class=\"arbo\">\n";
							
							$eParamCount = 0;
							
							// params
							echo "<br /><input id=\"\" name=\"\" type=\"text\" value=\"height\" disabled=\"1\" />";
							echo '<input type="text" id="height" name="height" value="'.$height.'" /><br />';
							echo "<input id=\"\" name=\"\" type=\"text\" value=\"width\" disabled=\"1\" />";
							echo '<input type="text" id="width" name="width" value="'.$width.'" /> <br />	';
							// _start
							echo "<br /><input id=\"__0\" name=\"__0\" type=\"text\" value=\"start\" disabled=\"1\" />";
							echo "<input id=\"param0\" name=\"param0\" type=\"hidden\" value=\"start\" /><input id=\"value0\" name=\"value0\" type=\"text\" value=\"".$paramStart."\" /> (entrez des secondes . millisecondes)";
							// _end
							echo "<br /><input id=\"__1\" name=\"__1\" type=\"text\" value=\"end\" disabled=\"1\" />";
							echo "<input id=\"param1\" name=\"param1\" type=\"hidden\" value=\"end\" /><input id=\"value1\" name=\"value1\" type=\"text\" value=\"".$paramEnd."\" /> (entrez des secondes . millisecondes)";
							// _autostart
							echo "<br /><input id=\"__2\" name=\"__2\" type=\"text\" value=\"autostart\" disabled=\"1\" />";
							echo "<input id=\"param2\" name=\"param2\" type=\"hidden\" value=\"autostart\" /><input id=\"value2\" name=\"value2\" type=\"text\" value=\"".$paramAutostart."\" /> (entrez 1 ou 0)";
							// _onEnd
							echo "<br /><input id=\"__3\" name=\"__3\" type=\"text\" value=\"onEnd\" disabled=\"1\" />";
							echo "<input id=\"param3\" name=\"param3\" type=\"hidden\" value=\"onEnd\" /><input id=\"value3\" name=\"value3\" type=\"text\" value='".$paramOnEnd."' /> (entrez une fonction actionScript - ex: trace(this._name) )";
							// _showUI
							echo "<br /><input id=\"__4\" name=\"__4\" type=\"text\" value=\"showUI\" disabled=\"1\" />";
							echo "<input id=\"param4\" name=\"param4\" type=\"hidden\" value=\"showUI\" /><input id=\"value4\" name=\"value4\" type=\"text\" value=\"".$paramShowUI."\" /> (entrez 1 ou 0)";
							// _loop
							echo "<br /><input id=\"__5\" name=\"__5\" type=\"text\" value=\"loop\" disabled=\"1\" />";
							echo "<input id=\"param5\" name=\"param5\" type=\"hidden\" value=\"loop\" /><input id=\"value5\" name=\"value5\" type=\"text\" value=\"".$paramLoop."\" /> (entrez 1 ou 0)";
							// _volume
							echo "<br /><input id=\"__6\" name=\"__6\" type=\"text\" value=\"volume\" disabled=\"1\" />";
							echo "<input id=\"param6\" name=\"param6\" type=\"hidden\" value=\"volume\" /><input id=\"value6\" name=\"value6\" type=\"text\" value=\"".$paramVolume."\" /> (entrez un volume de 0 &agrave; 100)";
							// _onRollOver
							echo "<br /><input id=\"__7\" name=\"__7\" type=\"text\" value=\"onRollOver\" disabled=\"1\" />";
							echo "<input id=\"param7\" name=\"param7\" type=\"hidden\" value=\"onRollOver\" /><input id=\"value7\" name=\"value7\" type=\"text\" value=\"".$paramOnRollOver."\" /> (entrez une fonction actionScript - ex: trace(this._name) )";
							
							$eParamCount = 8;
							
							?>
							<input type="hidden" id="numparam" name="numparam" value="<?php echo $eParamCount; ?>" /> 
							<input type="hidden" id="video" name="video" value="<?php echo "/custom/video/".$_SESSION["rep_travail"]."/".$svideo; ?>" />
							<input type="hidden" id="vignette" name="vignette" value="<?php echo $vignette; ?>" /> 
							<input type="hidden" id="pickparam" name="pickparam" value="pickparam" />  <br /><br />
							<input id="submit" class="arbo" type="submit" value="Envoyer" name="submit">
							<?php
							
							echo "</div>";
						
							
							
						}	
						// page de prévisualisation de la vidéo
						// le bouton OK permet d'insérer la vidéo
						elseif (is_post("pickparam")) {
							 
							 
							echo "<strong>Aper&ccedil;u de la vid&eacute;o</strong><br />"; 
							echo "Cliquez sur le bouton \"OK\" pour ins&eacute;rer le code HTML de la vid&eacute;o<br /><br />"; 
							
							$svideo = $_POST['video'];
							$vignette = $_POST['vignette'];
							$width = $_POST["width"];
							$height = $_POST["height"];
							$vidName = basename($svideo);
							$vidURL =  "http://".$_SERVER['HTTP_HOST'].$svideo;
							$phpURL = "http://".$_SERVER['HTTP_HOST']."/backoffice/cms/utils/flvprovider.php";
							$vidQuery = "_vidName=".$vidName."&_vidURL=".$vidURL."&_phpURL=".$phpURL."&_vidThb=".basename($vignette).'&';
							$svideoUrl = "/backoffice/cms/utils/scrubberLarge";
							$sFlashVars = $vidQuery;
							
							$_autostart = $_POST["value2"];
							$_loop = $_POST["value5"];
							($_autostart == 1 ) ? $autostart = "true" : $autostart = "false";
							($_loop == 1 ) ? $loop = "true" : $loop = "false";
							
							if (($_POST["numparam"] > 0) && (is_post("param0"))){	
								for ($i=0;$i<$_POST["numparam"];$i++){
									if (strlen($_POST["param".$i]) > 0){
										$sFlashVars .= "_".$_POST["param".$i]."=".(flashurl_encode($_POST["value".$i]))."&"; 
									}
								}
							}
							
							$id_unique = date ("ymdhm").rand(0,1000);
							
							$sBodyHTML = '<div class="cms_video"><script src="/backoffice/cms/js/AC_RunActiveContent.js" type="text/javascript"></script>';
							$sBodyHTML .= '<script type="text/javascript">';
							$sBodyHTML .= 'flashvars = "'.$sFlashVars.'embedUrl="+escape(document.location.href);';
							
							$sBodyHTML .= 'swfSrcFull = "'.str_replace("scrubberLarge", "scrubberLarge.swf", $svideoUrl).'";';
							$sBodyHTML .= 'swfSrc = "'.$svideoUrl.'";';
							
							$sBodyHTML .= 'AC_FL_RunContent( "codebase","https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,1,0,0",
							"width","'.$width.'","height","'.$height.'","src", swfSrc, "quality","high","pluginspage","https://get.adobe.com/flashplayer/",
							"align", "middle", "play", "true", "loop", "'.$loop.'", "autostart", "'.$autostart.'","scale", "showall", "wmode", "opaque", "devicefont", "false",
							"id", "awsvid_'.$id_unique.'", "name", "awsvid_'.$id_unique.'", "menu", "false", "allowFullScreen", "false",
							"allowScriptAccess","sameDomain","movie", swfSrc, "salign", "", "flashvars", flashvars );';
							
							$sBodyHTML .= 'function videoSizeUpdate(w, h){
									//alert(w+" / "+h);
									//document.getElementById("awsvid").width = w;
									//document.getElementById("awsvid").height = h;
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
											if (a[i].id == "awsvid_'.$id_unique.'"){
												a[i].width = w;
												a[i].height = h;					
											}
										} 
									}		
									if (document.getElementById("WIDTHcontent") != undefined){
										document.getElementById("WIDTHcontent").value = w;
									}
									if (document.getElementById("HEIGHTcontent") != undefined){
										document.getElementById("HEIGHTcontent").value = h;
									}
								}';
							
							$sBodyHTML .= '</script></div>';	
							  
							echo $sBodyHTML;
							 
							?> 
							
							 <input type="hidden" id="sBodyHTML" name="sBodyHTML"  value='<?php echo $sBodyHTML; ?>'> 
							
							<?php
							
							
						} 
						else {
							foreach ($aObj as $oObj) {
								echo '<a href="javascript:Suite('.$oObj->get_id().')">'.$oObj->getName_content().'</a><br/><br/>';
								
								?>
								 
							<input type="hidden"   id="txtId_<?php echo $oObj->get_id(); ?>" name="txtId_<?php echo $oObj->get_id(); ?>"  value="<?php echo $oObj->get_id(); ?>"> 
							<span><?php echo $oObj->getHtml_content(); ?></span><br><br>
							
							<?php
							}
						}
		
						?>
						</form>		 
				</td>
			</tr>
		</table>
	</body>
</html>
