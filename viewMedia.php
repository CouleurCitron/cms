<html>
<title>Visualiser Média</title>
<body>
	<?php 
	$fic = $_GET['fic'];
	$fileopt=explode('.',$fic);
	
			if ($fileopt[1]==".swf" ) { 
	?>
	<OBJECT > 
  	<PARAM NAME=movie VALUE="../../medias/<?php echo $fic ?>"> 
  	<PARAM NAME=quality VALUE=high> 
	<?php
	}
	?>
  	<EMBED src="../../medias/<?php echo $fic ?>" quality=high	 TYPE=\"application/x-shockwave-flash\" >
  	</EMBED> 
	<?php 
		if ($fileopt[1]==".swf") {
	?>
 	</OBJECT>
	<?php
			}
	?> 
	</body></html>