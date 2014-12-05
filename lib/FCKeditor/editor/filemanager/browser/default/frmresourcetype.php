<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 
?>

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
 * File Name: frmresourcetype.html
 * 	This page shows the list of available resource types.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="browser.css" type="text/css" rel="stylesheet">
		<script type="text/javascript" src="js/common.js"></script>
		<script language="javascript">
		
		

function SetResourceType( type )
{
	window.parent.frames["frmFolders"].SetResourceType( type ) ;
}
// cette liste doit devenir dynamique
/*
chercher les classes :
- non liées à un site OU liées au site en cours
- ET ayant un dossier dans custom
*/
var aTypes = [	
	['Image','Image'],
	['File','File'],
	['Flash','Flash'],
	['Media','Media'],
	['PDF','PDF'],
<?php 
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php'); // classe de customisation des classes

$aClasse = dbGetObjectsFromFieldValue("classe", array("get_statut"),  array(DEF_ID_STATUT_LIGNE), NULL);

if (count($aClasse) > 0){
	foreach($aClasse as $cKey => $oClasse){				
		if (($oClasse->get_cms_site() == -1) || ($oClasse->get_cms_site() == $_SESSION['idSite'])){					
			if (is_dir($_SERVER['DOCUMENT_ROOT'].'/custom/upload/'.$oClasse->get_nom())){
				//echo "la classe ".$oClasse->get_nom()." <br />\n";
				echo '	[\''.$oClasse->get_nom().'\',\''.$oClasse->get_nom().'\'],';
			}			
		}		
	}
}
?>
] ;

window.onload = function()
{
	for ( var i = 0 ; i < aTypes.length ; i++ )
	{
		if ( aTypes[i]!=undefined ){
			if ( oConnector.ShowAllTypes || aTypes[i][0] == oConnector.ResourceType )
				AddSelectOption( document.getElementById('cmbType'), aTypes[i][1], aTypes[i][0] ) ;
		}
	}
	
	//SetResourceType('Image'); // default
}

		</script>
	</head>
	<body bottomMargin="0" topMargin="0">
		<table height="100%" cellSpacing="0" cellPadding="0" width="100%" border="0">
			<tr>
				<td nowrap>
					Resource Type<BR>
					<select id="cmbType" style="WIDTH: 100%" onChange="SetResourceType(this.value);">
					</select>
				</td>
			</tr>
		</table>
	</body>
</html>
