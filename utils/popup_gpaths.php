<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

////////////////////////////////////

eval("$"."oRes = new ".$_GET['tableName']."();");

$sXML = $oRes->XML;
xmlStringParse($sXML);

$classeName = $stack[0]["attrs"]["NAME"];
if (isset($stack[0]["attrs"]["LIBELLE"]) && ($stack[0]["attrs"]["LIBELLE"] != ""))
	$classeLibelle = stripslashes($stack[0]["attrs"]["LIBELLE"]);
else	$classeLibelle = $classeName;

$classePrefixe = $stack[0]["attrs"]["PREFIX"];
$aNodeToSort = $stack[0]["children"];

$pivot_name = '';
// find reference element and map center pivot
for ($i=0;$i<count($aNodeToSort);$i++) {
	if ($aNodeToSort[$i]["attrs"]["NAME"] == $_GET['idField']) {
		if ($aNodeToSort[$i]["attrs"]["OPTION"] == "geopath") {
			// found path field item
			foreach ($aNodeToSort[$i]["children"] as $childKey => $childNode) {
				if ($childNode["name"] == "OPTION" && $childNode["attrs"]["TYPE"] == "if")							
					$pivot_name = $childNode["attrs"]["ITEM"];
			}
			break;
		}
	}
}

// api key
$aKey = dbGetObjectsFromFieldValue("cms_mapskey", array("get_host"),  array($_SERVER['HTTP_HOST']), NULL);
if ((count($aKey) == 1)&&($aKey!=false)) {
	$sKey = $aKey[0]->get_key();
}
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
	{margin: 0px; width: 100%; height: 100%; font-family: verdana;}
a, span, div, body
	{text-decoration: none;}

#message		{height: 22px; font: normal 12px Verdana; color: white; background: navy; width: 100%; text-align: center; vertical-align: bottom;}
#map		{float: left; width: 720px; height: 550px;}
#map img		{cursor: crosshair;} 
#control		{float: left; margin-left: 4px;}
#dist		{font: bold 10px Verdana;}

.wText		{border: 1px solid gray; padding: 5px; margin: 2px; font: normal 10px Verdana; width: 160px;}

.buttons		{border: 1px solid gray; padding: 5px; margin: 2px; font: normal 10px Verdana; width: 160px;}
.buttonB		{background: #EEEEEE; color: navy; border: 1px solid navy; text-align:center; vertical-align:middle; cursor: pointer; padding: 2px; margin: 3px;}
.buttonB:hover	{color: red; border: 1px solid red;}

</style>
<script type="text/javascript" src="http://maps.google.com/maps?file=api&v=2&key=<?php echo $sKey; ?>&lang=fr"></script>
<script type="text/javascript">

function trim (_string) {
	return _string.replace(/^\s+/g,'').replace(/\s+$/g,'');
}

// split on line end and ingnore empty lines
function lineSplit (_string) {
	var lines = Array();
	var temp = _string.split("\n");
	for each (test in temp) {
		if (test != "\n")
			lines.push(test);
	}
	return lines;
}

function barycentricPoint (_points) {
	var lats = 0;
	var lngs = 0;
	for (var i=0; i<_points.length; i++) {
		var tmp = _points[i].split(',');
		lats += parseFloat(trim(tmp[0]));
		lngs += parseFloat(trim(tmp[1]));
	}
	return lats/_points.length+','+lngs/_points.length;
}

// rerefer edition form
var frm = window.opener.document.forms['add_<?php echo $classePrefixe; ?>_form'];

var routePoints = new Array();
var routeMarkers = new Array();
var routeOverlays = new Array();
var map;
var totalDistance = 0.0;
var lineIx = 0;

var lineSize = 3;

var baseIcon = new GIcon();
baseIcon.iconSize = new GSize(16,16);
baseIcon.iconAnchor = new GPoint(8,8);
baseIcon.infoWindowAnchor = new GPoint(10,0);

var yellowIcon = (new GIcon(baseIcon, "/backoffice/cms/img/map_paths/yellowSquare.png", null, ""));
var greenIcon = (new GIcon(baseIcon, "/backoffice/cms/img/map_paths/greenCircle.png", null, ""));
var redIcon = (new GIcon(baseIcon, "/backoffice/cms/img/map_paths/redCircle.png", null, ""));
var orangeIcon = (new GIcon(baseIcon, "/backoffice/cms/img/map_paths/orangeCircle.png", null, ""));
var blueIcon = (new GIcon(baseIcon, "/backoffice/cms/img/map_paths/blueCircle.png", null, ""));
var violetIcon = (new GIcon(baseIcon, "/backoffice/cms/img/map_paths/violetCircle.png", null, ""));

var colorList = ['#0000ff', '#00ff00', '#ff0000', '#00ffff', '#ffff00', '#ff00ff'];

<?php

echo "var p_coords = lineSplit(frm.elements['f".ucfirst($classePrefixe)."_{$_GET['idField']}'].value);";

?>

// Call this function when the page has been loaded
function initialize() {
	if (GBrowserIsCompatible()) {
		map = new GMap2(document.getElementById("map"));

<?php

if ($pivot_name != '') {
	echo "\n\t\tvar pivot = frm.elements['f".ucfirst($classePrefixe)."_{$pivot_name}'].value;";
	echo "\n\t\tvar c_coords = pivot.split(',');";
} else	echo "\n\t\tvar c_coords = barycentricPoint(p_coords).split(',');";

?>

		var c_lat = parseFloat(trim(c_coords[0]));
		var c_lng = parseFloat(trim(c_coords[1]));
		var centerPoint = new GLatLng(c_lat, c_lng);
		map.setCenter(centerPoint, 11);

		displayMapProps();

		map.addControl(new GMapTypeControl());
		map.addControl(new GScaleControl());
		map.addControl(new GLargeMapControl());

		map.enableContinuousZoom();
		map.enableScrollWheelZoom();

		GEvent.addListener(map, "moveend", displayMapProps);
		GEvent.addListener(map, "click", mapClick);

		//  ======== Add a map overview ==========
		var ovcontrol = new GOverviewMapControl(new GSize(120,120));
		map.addControl(ovcontrol);

		return;
		var pl = new GPolyline.fromEncoded({
			color: "#0000ff",
			weight: 4,
			opacity: 0.8,
			points: "_gkxEr}|vNcBwBoAoA{Jg@cBcBoA{@S?uBoA}@wBS_DbBwhATg@c[sDiT}\\g@i@{@S{@{@ScBRSnAg@SoAS{Eg@kCkClH?nAoF`LQRU{E~CgJ?g@jCqNrNcGz@?zJyBf@y@~CmC{@zJ?f@mAnA?f@x@z@bBR~HsIRe@jClKRz@`Dh@`Bd@vBRnF_DRSg@S?wBpFcGd@SScBwBvB{@T_D~Cg@?oA`B{@R{E~CqD{EUgJwB{@nAoA?kChJrIdJaBz@i@f@_DdBf@dEgEnFcGoP_N{@SeTvBqKoA{J_SsDoF?_Df@{@vB_XoAoPbLoZrDcBy@{EmC_D{ToAy@g@?_DlAgEgEcLg@g@qDnAqAkR_DyJy@i@mC?g@{@}CqNi@i@_ISoK_g@g@{@{EiCiCd@g@z@yGfOwBnAoARyJ_DuDRoAg@_D_IoKyEcGk\\gOx@cGy@{@}@sI_DcGgEwLbBuBRmCe@{JgOS}Jy@{@kHcBy[_]gEmPnA}Ef@g@vGwBgEa[rDqP|h@w[hMoFzJ{J~HwL{@_DoAkCcGwBuBgJyBoA_SfJiCz@{Jz@yBg@oU{OkCkCf@cLbBsIy@iCyGeB}H{@mCnFoAvB}ReEyGaIpFsSx@iHoAmCkHkCQ{JmM{TS_DvBuG?qFR{@vBoA~Hg@bBoAlCuL}@cG_IcGyEi@?sNUy@{@}@wLvBuB?aIcGgTgOSwLS_DoFoFwQTsDUg^kW}Hwe@U_DrI_l@|@_Dx@g@fTbBjC?j\\sD|OgEdJcGnAwBaB{EuDoAaLcB}J{JqDjCee@wGsD{JoAwB_X?cB{@g@g@kCqSkf@ye@gJkWoi@_]{JgESkHcBkHco@u[{Od@{@e@sIa]{@gJk\\kMkHoF_NgJcG?oAaBoAyGcBoAgESgYvG{@?wB_NcBgO~HoFrI_I~a@_]rNcVzJoAnU_q@nA{@rIsDrDsIwG_Ng@}CjMa]bBcGz@g@nF|@z@i@sDa[vGiYvBoAfJ_Dz@_DgEoKoA_NdBuL`VkWvLmH_Ie@kH?wB|Ce^|TyBx@cL?f@{T{@yEcQyBoFfEoKtDeJPmCoFg@{@gO}Huj@oA}OgOaBeBwQf@}@?mFsIyB_DyE_DaXrDcGrDbBkCvBgE{JsSoKsIqDSuDkHR{@xB?lAbBcBR",
			levels: "P?CFDEADGDHBGIBDCEADCFDECFADFBFGCFCEEBDBCDGBEBFHDEABDFBCCDEBFCEFEDCFFECFGEFFCGEHEDCJFGDEDFGHDFBEGECFCIBEGCEDFFGHEFCGEKFECGEHDEFGCEIFDEFGHGHIFDEFGDFGEFJDFEDGEFDGHFEGEFIDEFBDGDFEFGHBDFDGLEFEGDHGDIDCFGHFGDEDFGDHEFGDBFJFHEFEIFCGKGEHEDFDEGCIEFGHFGICFEGDHJCECFGDHDFEGIFEFDGDHFDGEFGICFHFDGCJDEGEDEMFDGBDEP",
			zoomFactor: 2,
			numLevels: 18
		});
		map.addOverlay(pl);
	}
}


function mapClick (_overlay, _point) {
	if (!_overlay)
		addRoutePoint(_point);
}


function addRoutePoint (_point) {
	if (!routePoints[lineIx]) {
		routePoints[lineIx] = Array();
		routeMarkers[lineIx] = Array();
	}
	routePoints[lineIx].push(_point);
	if (routePoints[lineIx].length == 1) {
		routeMarkers[lineIx][routePoints[lineIx].length-1] = new GMarker(_point,{draggable:true, icon:greenIcon, title:'Start'});
	} else {
		if (routeMarkers[lineIx][routePoints[lineIx].length-1])
			map.removeOverlay(routeMarkers[lineIx][routePoints[lineIx].length-1]);
		routeMarkers[lineIx][routePoints[lineIx].length-1] = new GMarker(_point,{draggable:true, icon:yellowIcon, title:'Point '+ routePoints[lineIx].length-1});
		plotRoute();
	}
	setMarkerEvents(routeMarkers[lineIx][routePoints[lineIx].length-1]);
	routeMarkers[lineIx][routePoints[lineIx].length-1].line = lineIx;
	routeMarkers[lineIx][routePoints[lineIx].length-1].index = routePoints[lineIx].length-1;
	map.addOverlay(routeMarkers[lineIx][routePoints[lineIx].length-1]);
	displayOverlayProps();
}


function addClosing () {
	if (routePoints[lineIx].length > 1) {
		if (routeMarkers[lineIx][routePoints[lineIx].length-1])
			map.removeOverlay(routeMarkers[lineIx][routePoints[lineIx].length-1]);
		routeMarkers[lineIx][routePoints[lineIx].length-1] = new GMarker(routePoints[lineIx][routePoints[lineIx].length-1],{draggable:true, icon:redIcon, title:'End'});
		setMarkerEvents(routeMarkers[lineIx][routePoints[lineIx].length-1]);
		routeMarkers[lineIx][routePoints[lineIx].length-1].line = lineIx;
		routeMarkers[lineIx][routePoints[lineIx].length-1].index = routePoints[lineIx].length-1;
		map.addOverlay(routeMarkers[lineIx][routePoints[lineIx].length-1]);
		lineIx++;
	}
	var divs = document.getElementsByTagName('DIV');
	for (var n=0; n<divs.length; n++) {
		if (divs[n].className == 'gmnoprint')
			divs[n].className = '';
	}
}

function setMarkerEvents (marker) {
	GEvent.addListener(marker, "drag", function() {
		routePoints[marker.line][marker.index] = marker.getLatLng();
		plotRoute(marker.line);
	});
	GEvent.addListener(marker, "dragend", function() {
		displayOverlayProps();
	});
}


function displayMapProps () {
	var center = map.getCenter();
	document.getElementById("coords").innerHTML = 'Pivot central :<br>' + center.y.toFixed(6) + ', ' + center.x.toFixed(6) + '<br>Zoom : ' + map.getZoom();
}


function displayOverlayProps () {
	var display = '';
	var totalDistance = 0;
	for (var i=0; i<=lineIx; i++) {
		if (routePoints[i]) {
			var dist = 0;
			disp_block = '';
			dist_block = 0;
			if (i > 0)
				display += "<br/>";
			for (var n=0; n<routePoints[i].length; n++) {
				if (n > 0)
					dist = routePoints[i][n-1].distanceFrom(routePoints[i][n]) / 1000;
				dist_block+= dist;
				disp_block += routePoints[i][n].y.toFixed(6) + ', ' + routePoints[i][n].x.toFixed(6) + ' : ' + dist.toFixed(3) +"<br/>";
			}
			display += "Trac&eacute; <span style=\"background-color:"+colorList[i]+";\">&nbsp;&nbsp;</span> : "+dist_block.toFixed(3)+"km<br/>"+disp_block;
			totalDistance += dist_block;
		}
	}
	if (totalDistance != '')
		document.getElementById("dist").innerHTML = 'Distance totale : '+totalDistance.toFixed(3)+' km';
	document.getElementById("route").innerHTML = display;
}


function toggleMarkers () {
	for (var i=0; i<=routeMarkers.length; i++) {
		for (var n=0; n<routeMarkers[i].length; n++) {
			if (routeMarkers[i][n].isHidden())
				routeMarkers[i][n].show();
			else	routeMarkers[i][n].hide();
		}
	}
}

function DEC2DMS(dec) {
	var deg = Math.floor(Math.abs(dec));
	var min = Math.floor((Math.abs(dec)-deg)*60);
	var sec = (Math.round((((Math.abs(dec) - deg) - (min/60)) * 60 * 60) * 100) / 100 ) ;
	deg = dec < 0 ? deg * -1 : deg;
	var dms  = deg + '&deg ' + min + '\' ' + sec + '"';
	return dms;
}


function plotRoute(_custom) {
	if (_custom === undefined)
		_custom = lineIx;
	if (routeOverlays[_custom])
		map.removeOverlay(routeOverlays[_custom]);
	routeOverlays[_custom] = new GPolyline(routePoints[_custom], colorList[_custom], lineSize, 1);
	map.addOverlay(routeOverlays[_custom]);
}


function clearAll() {
	while (lineIx > 0)
		resetRoute();
	totalDistance = 0;
	document.getElementById("dist").innerHTML = '';
	document.getElementById("route").innerHTML = '';
}


function resetRoute() {
	if (!routePoints[lineIx] || routePoints[lineIx].length == 0)
		lineIx--;
	routePoints[lineIx] = null;
	if (routeOverlays[lineIx])
		map.removeOverlay(routeOverlays[lineIx]);

	for (var n = 0 ; n < routeMarkers[lineIx].length ; n++ ) {
		var marker = routeMarkers[lineIx][n];
		if (marker)
			map.removeOverlay(marker);
	}
	routeMarkers[lineIx] = null;

	var html = document.getElementById("route").innerHTML;
	html = html.replace(/<br>[^<]+<br>$/,'<br>');
	document.getElementById("route").innerHTML = html;
}

function undoPoint() {
	if (!routePoints[lineIx] || routePoints[lineIx].length == 0)
		lineIx--;
	if (routePoints[lineIx].length > 1) {
		if (routeMarkers[lineIx][routePoints[lineIx].length-1]) {
			var marker = routeMarkers[lineIx].pop();
			if (marker)
				map.removeOverlay(marker);
		}
		routePoints[lineIx].pop();
		plotRoute();
	} else	resetRoute();
	displayOverlayProps();
}


function exportPaths(xml) {
	var html = '';
	if (xml) {
		html = "<"+"?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";

		html += '<root>\n';
		html += '\t<map>\n';
		var center = map.getCenter();
		html += '\t\t<center lat="'+center.y.toFixed(6)+'" lng="'+center.x.toFixed(6)+'"/>\n';
		html += '\t\t<zoom>'+map.getZoom()+'</zoom>\n';
		html += '\t</map>\n';
		html += '\t<routes>\n';
		for (var i = 0 ; i < lineIx ; i++ ) {
			html += '\t\t<route color="'+colorList[i]+'">\n';
			for (var n = 0 ; n < routePoints[i].length ; n++ ) {
				html += '\t\t\t<point lat="' + routePoints[i][n].y.toFixed(8) + '" lng="' + routePoints[i][n].x.toFixed(8) + '"';
				if (routeMarkers[i][n])
					html += ' markerIcon="'+ routeMarkers[i][n].getIcon().image +'"';
				html += ' />\n';
			}
			html += '\t\t</route>\n';
		}
		html += '\t</routes>\n';
		html += '<root>\n';
	} else {
		for (var i = 0 ; i < lineIx ; i++ ) {
			for (var n = 0 ; n < routePoints[i].length ; n++ )
				html += routePoints[i][n].y.toFixed(8) + ', ' + routePoints[i][n].x.toFixed(8) + '\n';
			html += '----- new line ------\n';
		}
	}

	/*
	if (html == '')
		html += 'vous devez ajouter un point de cl&ocirc;ture pour chaque trac&eacute;.\n\n';

	html += '\n\n';
//	html += encodePolyline();
	var nWin = window.open('','nWin','width=780,height=500,left=50,top=50,resizable=1,scrollbars=yes,menubar=no,status=no');
	nWin.focus();
	nWin.document.open ('text/xml\n\n');
	nWin.document.write(html);
	nWin.document.close();
	*/
	return html;
}


//-----------------------------------------
function encodePolyline() {
	var encodedPoints = '';
	var encodedLevels = '';

	var plat = 0;
	var plng = 0;

	for (var n = 0 ; n < routePoints[lineIx].length ; n++ ) {
		var lat = routePoints[lineIx][n].y.toFixed(8);
		var lng = routePoints[lineIx][n].x.toFixed(8);

		var level = (n == 0 || n == routePoints[lineIx].length-1) ? 3 : 1;
		var level = 0;

		var late5 = Math.floor(lat * 1e5);
		var lnge5 = Math.floor(lng * 1e5);

		dlat = late5 - plat;
		dlng = lnge5 - plng;

		plat = late5;
		plng = lnge5;

		encodedPoints += encodeSignedNumber(dlat) + encodeSignedNumber(dlng);
		encodedLevels += encodeNumber(level);
	}

	var html = '';
	html += 'new GPolyline.fromEncoded({\n';
	html += '  color: "'+colorList[lineIx]+'",\n';
	html += '  weight: '+lineSize+',\n';
	html += '  opacity: 0.8,\n';
	html += '  points: "'+encodedPoints+'",\n';
	html += '  levels: "'+encodedLevels+'",\n';
	html += '  zoomFactor: 16,\n';
	html += '  numLevels: 4\n';
	html += '});\n';

	return html;
}

function encodeSignedNumber(num) {
	var sgn_num = num << 1;
	if (num < 0)
		sgn_num = ~(sgn_num);

	return(encodeNumber(sgn_num));
}


// Encode an unsigned number in the encode format.
function encodeNumber(num) {
	var encodeString = "";
	while (num >= 0x20) {
		encodeString += (String.fromCharCode((0x20 | (num & 0x1f)) + 63));
		num >>= 5;
	}
	encodeString += (String.fromCharCode(num + 63));
	return encodeString;
}

function returnCoords(){
	frm.elements['f<?php echo ucfirst($classePrefixe)."_{$_GET['idField']}"?>'].value = exportPaths();
	window.close();
}

</script>
</head>

<body onload="initialize()" onunload="GUnload()">
<div id="message">
	Cliquez dans la carte pour d&eacute;marrer puis poursuivre un trac&eacute;...
</div>
<div id="map"></div>
<div id="control">
	<div class="buttons">
		<div class="buttonB" onclick="toggleMarkers()">Voir/masquer les points</div>
		<div class="buttonB" onclick="addClosing()">Cl&ocirc;turer le trac&eacute; courant</div>
		<div class="buttonB" onclick="undoPoint()">Annuler le dernier point</div>
		<div class="buttonB" onclick="clearAll()">Effacer tout les trac&eacute;s</div>
		<div class="buttonB" onclick="returnCoords()">Enregistrer les trac&eacute;s</div>
		<!--//<div class="buttonB" onclick="exportPaths()">Voir Points TXT</div>
		<div class="buttonB" onclick="exportPaths(1)">Voir Points XML</div>//-->
	</div>
	<div class="wText" id="coords"></div>
	<div class="wText" id="dist"></div>
	<div class="wText" id="route"></div>
</div>

<!--<div id="coords" style="height:20px; width:800px; border: no;">
<form>
<?php

/*
foreach ($stack as $element) {
	echo "\n<b>".$element['title'].'</b>&nbsp;<input id="f_'.$element['field'].'" name="f_'.$element['field'].'" type="text" value="" style="width:490px"/><br/>';
}
if (count($map_scale) > 0)
	echo "\n<b>".$map_scale['title'].'</b>&nbsp;<input id="f_'.$map_scale['field'].'" name="f_'.$map_scale['field'].'" type="text" value="" style="width:40px"/><br/>';
*/
?>

<input id="fok" name="fok" type="button" value="OK" style="width:100px" onclick="returnCoords();"/>
</form>-->

</div>
</body>
</html>
