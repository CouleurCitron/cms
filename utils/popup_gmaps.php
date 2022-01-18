<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

//viewArray($_SERVER);

function findAncestor($element) {
	global $mapStack, $aNodeToSort;
	$parent = null;
	for ($i=0;$i<count($aNodeToSort);$i++) {
		if (($aNodeToSort[$i]["attrs"]["OPTION"] == "geocoords" || $aNodeToSort[$i]["attrs"]["OPTION"] == "geomapout") && $aNodeToSort[$i]["attrs"]["NAME"] == $element) {
			if ($aNodeToSort[$i]["attrs"]["GEOPARENT"])
				$parent = $aNodeToSort[$i]["attrs"]["GEOPARENT"];
			break;
		}
	}
	if (!is_null($parent)) {
		for ($i=0;$i<count($aNodeToSort);$i++) {
			if (($aNodeToSort[$i]["attrs"]["OPTION"] == "geocoords" || $aNodeToSort[$i]["attrs"]["OPTION"] == "geomapin") && $aNodeToSort[$i]["attrs"]["NAME"] == $parent) {
				array_unshift($mapStack, Array(	'field'	=> $parent,
								'title'	=> $aNodeToSort[$i]["attrs"]["LIBELLE"]) );
				if ($aNodeToSort[$i]["attrs"]["OPTION"] == "geocoords")
					findAncestor($parent);
				break;
			}
		}
	}
}

function findChild($parent) {
	global $mapStack, $aNodeToSort;
	for ($i=0;$i<count($aNodeToSort);$i++) {
		if (($aNodeToSort[$i]["attrs"]["OPTION"] == "geocoords" || $aNodeToSort[$i]["attrs"]["OPTION"] == "geomapout") && $aNodeToSort[$i]["attrs"]["GEOPARENT"] == $parent) {
			array_push($mapStack, Array(	'field'	=> $aNodeToSort[$i]["attrs"]["NAME"],
						'title'	=> $aNodeToSort[$i]["attrs"]["LIBELLE"]) );
			findChild($aNodeToSort[$i]["attrs"]["NAME"]);
			break;
		}
	}
}

////////////////////////////////////

eval("$"."oRes = new ".$_GET['tableName']."();");

$sXML = $oRes->XML;
xmlStringParse($sXML);

$mapStack = $stack;

$classeName = $mapStack[0]["attrs"]["NAME"];
if (isset($mapStack[0]["attrs"]["LIBELLE"]) && ($mapStack[0]["attrs"]["LIBELLE"] != ""))
	$classeLibelle = stripslashes($mapStack[0]["attrs"]["LIBELLE"]);
else	$classeLibelle = $classeName;


$classePrefixe = $mapStack[0]["attrs"]["PREFIX"];
$aNodeToSort = $mapStack[0]["children"];

$map_bound = false;
$map_pivot = false;
$map_scale = Array();
$mapsize = Array(804, 658);	// default
								
// find reference element and build stack
for ($i=0;$i<count($aNodeToSort);$i++) {
	if ($aNodeToSort[$i]["attrs"]["NAME"] == $_GET['idField']) {
		if ($aNodeToSort[$i]["attrs"]["OPTION"] == "geocoords") {
			// list of coordinates
			$mapStack = Array( Array(	'field'	=> $_GET['idField'],
						'title'	=> $aNodeToSort[$i]["attrs"]["LIBELLE"]) );
			findAncestor($_GET['idField']);
			findChild($_GET['idField']);
		} elseif ($aNodeToSort[$i]["attrs"]["OPTION"] == "geomapcenter") {
			// map pivot point
			$mapStack = Array( Array(	'field'	=> $_GET['idField'],
						'title'	=> $aNodeToSort[$i]["attrs"]["LIBELLE"]) );
			$map_pivot = true;
			if ($aNodeToSort[$i]["attrs"]["GEOMAPSIZE"] == 'DEF_GMAP_SIZE' && DEF_GMAP_SIZE != '')
				$mapsize = explode('x', DEF_GMAP_SIZE);
		} elseif ($aNodeToSort[$i]["attrs"]["OPTION"] == "geomapin") {
			// map top-left bound
			$mapStack = Array( Array(	'field'	=> $_GET['idField'],
						'title'	=> $aNodeToSort[$i]["attrs"]["LIBELLE"]) );
			$map_bound = true;
			findChild($_GET['idField']);
		} elseif ($aNodeToSort[$i]["attrs"]["OPTION"] == "geomapout") {
			// map bottom-right bound
			$mapStack = Array( Array(	'field'	=> $_GET['idField'],
						'title'	=> $aNodeToSort[$i]["attrs"]["LIBELLE"]) );
			$map_bound = true;
			findAncestor($_GET['idField']);
		}
		break;
	} 
}
for ($i=0;$i<count($aNodeToSort);$i++) {
	if ($map_pivot && $aNodeToSort[$i]["attrs"]["OPTION"] == "geomapscale") {
		// map zoom value
		$map_scale = Array(	'field'	=> $aNodeToSort[$i]["attrs"]["NAME"],
					'title'	=> $aNodeToSort[$i]["attrs"]["LIBELLE"] );
	}
}

//viewArray($mapStack, 'stack');

$ref_Lat = 43.552218347729465;
$ref_Lng = 1.4900636672973633;
$ref_step = '0.01';

// api key
$aKey = dbGetObjectsFromFieldValue("cms_mapskey", array("get_host"),  array($_SERVER['HTTP_HOST']), NULL);
if (count($aKey) == 1 && $aKey != false)
	$sKey = $aKey[0]->get_key();
//echo "key : ".$sKey."<br/>";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<title>Couleur Citron - Google map</title>
<style type="text/css">

body,html
{margin: 0px; width: 100%; height: 100%; font-family: verdana; font-size: 11px;}
a, span, div, body
	{text-decoration: none;}

/*#message		{height: 22px; font: normal 12px Verdana; color: white; background: navy; width: 100%; text-align: center; vertical-align: bottom;}*/
#map		{float: left; width: <?php echo $mapsize[0] ?>px; height: <?php echo $mapsize[1] ?>px;}
/*#map img		{cursor: crosshair;} */
</style>
<script type="text/javascript" src="http://maps.google.com/maps?file=api&v=2&key=<?php echo $sKey; ?>&lang=fr"></script>
<script type="text/javascript">

<?php

$declare = Array();;
$names = Array();;
foreach ($mapStack as $element) {
	$declare[] = $element['field'];
	$names[] = addslashes($element['title']);
}

?>
// containers for map data
var fields = Array(<?php echo "'".implode("', '", $declare)."'"; ?>);
var names = Array(<?php echo "'".implode("', '", $names)."'"; ?>);
// containers for map elements
var points = Array();
var markers = Array();
// rerefer edition form
var frm = window.opener.document.forms['add_<?php echo $classePrefixe; ?>_form'];

// Call this function when the page has been loaded
function initialize() {
	if (GBrowserIsCompatible()) {
		map = new GMap2(document.getElementById("map"));
		map.disableDragging();
		map.addControl(new GMapTypeControl());
		map.addControl(new GScaleControl());
		map.addControl(new GLargeMapControl());

		map.enableContinuousZoom();
		map.enableScrollWheelZoom();

		//  ======== Add a map overview ==========
		var ovcontrol = new GOverviewMapControl(new GSize(120,120));
		map.addControl(ovcontrol);

		for (var i in fields)
			createMarker(map, i);
<?php

if (count($map_scale) > 0) {
	echo "\n\t\tvar zoom = parseInt(frm.elements['f".ucfirst($classePrefixe)."_{$map_scale['field']}'].value);";
	echo "\n\t\tif (zoom > -1 && zoom != '')";
	echo "\n\t\t\tmap.setCenter(points[0], zoom);";
	echo "\n\t\telse\tmap.setCenter(points[0], 13);";
} else	echo "\n\t\tmap.setCenter(points[0], 13);";

if (count($map_scale) > 0) {
	echo "\n\t\tfields.push('{$map_scale['field']}');";
	echo "\n\t\tnames.push('{$map_scale['title']}');";
}
?>

	}
}


google.setOnLoadCallback(initialize);


function createMarker(map, index) {
	//get values for the new marker
	var test = frm.elements['f<?php echo ucfirst($classePrefixe); ?>_'+fields[index]].value;
	if (test.length != '') {
		test = test.split(',');
		var point = new GLatLng(parseFloat(test[0]), parseFloat(test[1]));
	} else	var point = new GLatLng(<?php echo $ref_Lat; ?>-index*<?php echo $ref_step; ?>, <?php echo $ref_Lng; ?>+index*<?php echo $ref_step; ?>);
	var marker = new GMarker(point, {draggable: true});

	//populate fields on page load
	oCoords = marker.getLatLng();
	document.getElementById('f_'+fields[index]).value = oCoords.lat() + ", " + oCoords.lng();

	// marker events
	GEvent.addListener(marker, "dragstart", function() {
		map.closeInfoWindow();
	});

<?php

if ($map_bound) {
	echo "\n\tGEvent.addListener(marker, \"drag\", function() {";
	echo "\n\t\tcheckBoundPosition(marker);";
	echo "\n\t});";
}

if (count($map_scale) > 0) {
	echo "\n\tGEvent.addListener(map,'zoomend',function() {";
	echo "\n\t\tdocument.getElementById('f_{$map_scale['field']}').value = map.getZoom();";
	echo "\n\t});";
	// correct second panTo call inconsistency bug on pivot point drag/drop
	echo "\n\tGEvent.addListener(map,'moveend',function() {";
	echo "\n\t\tmap.setCenter(markers[0].getLatLng());";
	echo "\n\t});";
}



if ($map_pivot) {
?>

	GEvent.addListener(marker, "drag", function() {
		var oCoords = marker.getLatLng();
		document.getElementById('f_'+fields[index]).value = oCoords.lat() + ", " + oCoords.lng();
		//map.panTo(oCoords, map.getZoom());
		//map.setCenter(oCoords);
	});

	GEvent.addListener(marker, "dragend", function() {
		var oCoords = marker.getLatLng();
		map.panTo(oCoords, map.getZoom());
		//marker.openInfoWindowHtml('<b>'+names[index]+'</b><br/>Latitude: '+oCoords.lat()+'<br/>Longitude: '+oCoords.lng());
	});

<?php
} else {
?>

	GEvent.addListener(marker, "dragend", function() {
		var oCoords = marker.getLatLng();
		document.getElementById('f_'+fields[index]).value = oCoords.lat() + ", " + oCoords.lng();
		marker.openInfoWindowHtml('<b>'+names[index]+'</b><br/>Latitude: '+oCoords.lat()+'<br/>Longitude: '+oCoords.lng());
	});

<?php
}
?>

	map.addOverlay(marker);

	// store references in global arrays
	marker.index = index
	markers[index] = marker;
	points[index] = point;
}


function checkBoundPosition(marker){
	var oCoords = marker.getLatLng();
	var newlat = oCoords.lat();
	var newlng = oCoords.lng();
	var corrected = false;
	if (marker.index == 0) {
		// moving entry point (NW)
 		var oVersus = markers[1].getLatLng();
 		if (oCoords.lat()-oVersus.lat() < <?php echo $ref_step; ?>)
 			var newlat = oVersus.lat()+<?php echo $ref_step; ?>;
 		if (oVersus.lng()-oCoords.lng() < <?php echo $ref_step; ?>)
 			var newlng = oVersus.lng()-<?php echo $ref_step; ?>;
	} else {
 		// moving exit point (SW)
 		var oVersus = markers[0].getLatLng();
 		if (oVersus.lat()-oCoords.lat() < <?php echo $ref_step; ?>)
 			var newlat = oVersus.lat()-<?php echo $ref_step; ?>;
  		if (oCoords.lng()-oVersus.lng() < <?php echo $ref_step; ?>)
 			var newlng = oVersus.lng()+<?php echo $ref_step; ?>;
 	}
	marker.setLatLng(new GLatLng(newlat, newlng));
}


function returnCoords(){
	for (var i in fields)
		frm.elements['f<?php echo ucfirst($classePrefixe); ?>_'+fields[i]].value = document.getElementById('f_'+fields[i]).value;

<?php
if (count($map_scale) > 0)
	echo "\n\tfrm.elements['f".ucfirst($classePrefixe)."_{$map_scale['field']}'].value = document.getElementById('f_{$map_scale['field']}').value;";
?>

	window.close();
}

</script>
</head>

<body onload="initialize()" onunload="GUnload()">
<div id="map"></div>
<form>
<?php
if (count($mapStack) > 0){
	foreach ($mapStack as $element) {
		echo "\n<b>".$element['title'].'</b>&nbsp;<input id="f_'.$element['field'].'" name="f_'.$element['field'].'" type="text" value="" style="width:300px"/>';
		if ($map_pivot)
			echo '<span style="width: 40px;">&nbsp;</span>';
		else	echo '<br/>';
	}
}
if (count($map_scale) > 0)
	echo "\n<b>".$map_scale['title'].'</b>&nbsp;<input id="f_'.$map_scale['field'].'" name="f_'.$map_scale['field'].'" type="text" value="" style="width:40px"/><br/>';

?>

<input id="fok" name="fok" type="button" value="OK" style="width:100px" onclick="returnCoords();"/>
</form>

</div>
</body>
</html>