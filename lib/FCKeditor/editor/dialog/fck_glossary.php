<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
 	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

	
	
	$aObj = dbGetObjectsFromFieldValue("cms_glossary", array ("get_statut") , array (DEF_ID_STATUT_LIGNE), "get_titrecourt" );
	


 	if (!isset($translator)){
		$translator =& TslManager::getInstance(); 
	}
	
	$sVoirGlossaire = $translator->getText('Voir le glossaire', $_SESSION["id_langue"]);
	$sGlossaire = $translator->getText('Glossaire', $_SESSION["id_langue"]);
	
	if ($sVoirGlossaire == '') {
		$translator->addTranslation ('Voir le glossaire', array("1" => "Voir le glossaire", "2" => "See glossary"));
		$sVoirGlossaire = $translator->getText('Voir le glossaire', $_SESSION["id_langue"]);
	}
	
	if ($sGlossaire == '') {
		$translator->addTranslation ('Glossaire', array("1" => "Glossaire", "2" => "Glossary"));
		$sGlossaire = $translator->getText('Glossaire', $_SESSION["id_langue"]);
	}
	
	if (defined("DEF_HREF_FCK_GLOSSAIRE_".strtoupper($_SESSION["site_travail"]))) {
		 eval ( "$"."path_Glossaire = "."DEF_HREF_FCK_GLOSSAIRE_".strtoupper($_SESSION["rep_travail"]).";") ;
	}
	else {
		$path_Glossaire = "/content/".$_SESSION["rep_travail"]."/".$sGlossaire."/index.php?id=XX-ID-XX";
	}
	
	$path_Glossaire = str_replace ("'", "\'", $path_Glossaire) ;
	
	
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
		<title>Glossaire</title>
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
	window.parent.SetOkButton( true ) ;
}


function strip_tags(html)
    {
    //PROCESS STRING
    if(arguments.length < 3) {
     html=html.replace(/<\/?(?!\!)[^>]*>/gi, '');
    } else {
     var allowed = arguments[1];
     var specified = eval("["+arguments[2]+"]" );
     if(allowed){<strong></strong>
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

function Insert_def(id)
{ 
	var name ;
	var path ;
	myname = 'txtName_'+id; 
	myvalue = 'txtValue_'+id; 
	
	if ( GetE(myname).value.length == 0 )
	{
		alert( oEditor.FCKLang.DlgAnchorErrorName ) ;
		return false ;
	}
	
	oEditor.FCKUndo.SaveUndoStep() ;
	//alert(textSelected)
	if (textSelected != undefined  && textSelected != '' ) myText = strip_tags(textSelected);
	else myText = GetE(myname).value;
	
	//oAnchor		= FCK.EditorDocument.createElement( 'DIV' ) ;
	//oAnchor.innerHTML = '<a name="' + GetE(myname).value + '">'+ textSelected +'<\/a>' ;
	 
	//oAnchor.innerHTML = '<a class="tt" href="#">'+ myText +'<span class="tooltip"> '+ GetE(myvalue).value +'<\/span><\/a>' ;  
	//oAnchor = oAnchor.firstChild ;

	 
	/*oFakeImage	= oEditor.FCKDocumentProcessors_CreateFakeImage( 'FCK__Glossary', oAnchor ) ; 
	oFakeImage.setAttribute( '_fckanchor', 'true', 0 ) ; 
	oFakeImage	= FCK.InsertElementAndGetIt( oFakeImage ) ; */

 	//oEditor.FCK.InsertHtml( '<a name="' + GetE('txtName').value + '">'+ textSelected +'<\/a>' ) ;
	/*path = '<a class="tt" href="<?php //echo $path_Glossaire;?>">'+ myText +'<span class="tooltip"> '+ GetE(myvalue).value +'<br /><u><?php //echo $sVoirGlossaire; ?></u><\/span><\/a>';*/
	path = '<a class="tt" href="#_" id="glo_'+ id +'">'+ myText+'<\/a>';
	path = path.replace('XX-ID-XX', id);

	oEditor.FCK.InsertHtml( path ) ;
	
	window.parent.close() ;
	 
	return true;
 
}


 
function Ok()
{ 
	window.parent.close() ; 
	return true; 
}

		</script>
	</head>
	<body   scroll="yes">
		<table height="100%" width="100%"  >
			<tr>
				<td align="left">
					 
					<?php 
					
						 
						foreach ($aObj as $oObj) {
							echo '<a href="javascript:Insert_def('.$oObj->get_id().')">'.$oObj->get_titrecourt().'</a><br/>';
							
							?>
							 
						<input type="hidden"   id="txtName_<?php echo $oObj->get_id(); ?>" name="txtName_<?php echo $oObj->get_id(); ?>" style="WIDTH: 100%" value="<?php echo $oObj->get_titrecourt(); ?>"> 
						<span><?php echo $oObj->get_soustitre(); ?></span><br><br>
						<input type="hidden"   id="txtValue_<?php echo $oObj->get_id(); ?>" name="txtValue_<?php echo $oObj->get_id(); ?>" style="WIDTH: 100%" value="<?php echo $oObj->get_soustitre(); ?>">
						<?php
						}
		
						?>
								 
				</td>
			</tr>
		</table>
	</body>
</html>
