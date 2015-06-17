<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

if (is_get('source')){
	$scoord = $_GET['source'];
}
else{
	$scoord = '';
}


//source
if (is_get('idField')){
	$ssource = $_GET['idField'];
}
else{
	$ssource = '';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<title>Couleur Citron - Objets</title>

<script type="text/javascript">
	function returnCoords(){
		window.opener.document.getElementById('<?php echo $ssource; ?>').value = document.getElementById('fcoords').value;
		window.close();
	} 
  	function init(){
  		document.getElementById('fformat').value = window.opener.document.getElementById('<?php echo $ssource; ?>_object').value;
		
		<?php 
		if ($scoord == ''){
		?>
		// Create a form
		var f = document.createElement("form");
		f.id = "form0";
		// Add it to the document body
		document.getElementById('map').appendChild(f);
		
		var fd = document.createElement("input");
		fd.type = "text";
		document.getElementById('form0').appendChild(fd);
		<?php 
		}
		?>
	}
	window.onload = init;
</script>
</head>

<body style="margin:0px">
<div id="map" style="height:580px;width:800px;">
<?php 
if ($scoord != ''){



}
?>
</div>
<div id="coords" style="height:20px;width:800px;border: no">
<input id="fformat" name="fformat" type="text" value="aa" style="width:690px" disabled="disabled" /><br />
<input id="fcoords" name="fcoords" type="text" value="<?php echo $scoord; ?>" style="width:690px"/>
<input id="fok" name="fok" type="submit" value="OK" style="width:100px" onclick="returnCoords();"/>
</div>
</body>
</html>