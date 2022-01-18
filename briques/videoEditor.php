<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/lib/getid3/getid3.php');

if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

activateMenu('gestionbrique');

$id = $_GET['id'];

$composant = null;
if (strlen($id) > 0)
	$composant = getComposantById($id);
?>
<div class="ariane"><span class="arbo2">Créer une brique vidéo</span></div>
<?php
if(!is_post('video')) {
	// premiere phase
	$nom = '';
	$larg = '';
	$long = '';
	$type='';
	$svideoUrl='';
	$svideo='';
	$sParams='';
	if ($composant != null) {
		$nom = ' value="'.$composant['name'].'" ';
		$larg = ' value="'.$composant['width'].'" ';
		$long = ' value="'.$composant['height'].'" ';
		$type = ' value="'.$composant['type'].'" ';
		//echo "type :$type";		
		$sBodyHTML = $composant['html'];	
		
		$sCues = preg_replace("/.*_cues=1&(.+)&embedUrl.*/msi", "$1", $sBodyHTML);	
		
		$svideoUrl = preg_replace("/.*_vidURL=(.+)&_phpURL.*/msi", "$1", $sBodyHTML);
		$svideoUrl = preg_replace("/http:\/\/[^\/]+(\/.*)/msi", "$1", $svideoUrl);
		$vignette = flashurl_decode(preg_replace("/.*_vidThb=([^&]+)&.*/msi", "$1", $sBodyHTML));
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
		
		
	}	
	
// Chargement de la classe
require_once('cms-inc/lib/fileUpload/upload.class.php');

// Instanciation d'un nouvel objet "upload"
$Upload = new Upload();

// Pour ajouter des attributs aux champs de type file
$Upload-> FieldOptions = 'style="border-color:black;border-width:1px;"';

// Pour indiquer le nombre de champs désiré
$Upload-> Fields = 2;

// Initialisation du formulaire
$Upload-> InitForm();

echo '<form method="post" enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'?idSite='.$idSite.'&id='.$id.'" name="creation" id="creation">';

// Affichage du champ MAX_FILE_SIZE
print $Upload-> Field[0];
?>
<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
<input type="hidden" name="pickvideo" id="pickvideo" value="pickvideo" />
<input type="hidden" name="vignette" id="vignette" value='<?php echo $vignette; ?>' />
<input type="hidden" name="_start" id="_start" value='<?php echo $paramStart; ?>' />
<input type="hidden" name="_end" id="_end" value="<?php echo $paramEnd; ?>" />
<input type="hidden" name="_autostart" id="_autostart" value="<?php echo $paramAutostart; ?>" />
<input type="hidden" name="_onEnd" id="_onEnd" value='<?php echo ($paramOnEnd); ?>' />
<input type="hidden" name="_showUI" id="_showUI" value="<?php echo $paramShowUI; ?>" />
<input type="hidden" name="_loop" id="_loop" value="<?php echo $paramLoop; ?>" />
<input type="hidden" name="_volume" id="_volume" value="<?php echo $paramVolume; ?>" />
<input type="hidden" name="_onRollOver" id="_onRollOver" value="<?php echo $paramOnRollOver; ?>" />
<input type="hidden" name="_cues" id="_cues" value="<?php echo $sCues; ?>" />
<input type="hidden" name="minisite" id="minisite" value="<?php echo $_GET['minisite']; ?>" />
<input type="hidden" name="nom" id="nom" value="<?php echo $composant['name']; ?>" />

<div class="arbo" id="briqueVideoEditor">
        <div class="col_titre">
            Cr&eacute;ation de la brique Video
        </div>
        <div>
          <label for="video">choix du video :</label>
              
		  <select name="video" id="video" class="arbo">
		  	<option value="-1">Choisissez un fichier video</option>
		  	<?php
			$aRepertoires = array("/video", "/custom/upload/video"); // CUSTOM			
			$aRepertoires[] = "/custom/video/".$_SESSION["rep_travail"]; // GENERIC
			dirExists("/custom/video/".$_SESSION["rep_travail"]);			
			
			foreach($aRepertoires as $cKey => $sRepertoire){	
				$basedir = $_SERVER['DOCUMENT_ROOT'].$sRepertoire;
				if (is_dir($basedir)){					
					if ($dir = @opendir($basedir)) {
						while (($file = readdir($dir)) !== false) {				
							if ((is_file($basedir."/".$file)) && (preg_match("/.*\.(flv|mov)/i", $file)==1)) {
								$videoPath = str_replace($_SERVER['DOCUMENT_ROOT'], "", $basedir."/".$file);								
								
								 if($videoPath == $svideoUrl){
									$selected = " selected";
								 }
								 else{
									$selected = "";
								}
								echo "<option value=\"".$videoPath."\"".$selected.">".$videoPath."</option>\n";
							} 
						}
					  closedir($dir);
					}	
				}				
			}
			?>  
          </select>
        </div>
        <p>
            ou
        </p>
        <div class="rightContent">
            <div>
              <label>Video :</label><?php echo $Upload->Field[1]; ?><span>.flv ou .mov (codec h264)</span>
            </div>
                    <div>
                        <label>Vignette (optionel) :</label><?php echo $Upload->Field[2]; ?> <span>.png ou .jpg</span>
            </div>
        </div>
        <div>
              <input name="Suite (cr&eacute;ation) &gt;&gt;" type="submit" class="arbo" id="SuiteButton" value="Suite (cr&eacute;ation) &gt;&gt;">
        </div>
    </div>
</form>
<?php	
} elseif (is_post("pickvideo")) {	

if (is_post('_start')){
	$paramStart = $_POST["_start"];
}
else{
	$paramStart = 0;
}
if (is_post('_end')){
	$paramEnd = $_POST["_end"];
}
else{
	$paramEnd = 0;
}
if (is_post('_autostart')){
	$paramAutostart = $_POST["_autostart"];
}
else{
	$paramAutostart = 1;
}
if (is_post('_onEnd')){
	$paramOnEnd = $_POST["_onEnd"];
}
else{
	$paramOnEnd = "";
}
if (is_post('_showUI')){
	$paramShowUI = $_POST["_showUI"];
}
else{
	$paramShowUI = 1;
}
if (is_post('_loop')){
	$paramLoop = $_POST["_loop"];
}
else{
	$paramLoop = 0;
}
if (is_post('_volume')){
	$paramVolume = $_POST["_volume"];
}
else{
	$paramVolume = 100;
}
if (is_post('_onRollOver')){
	$paramOnRollOver = $_POST["_onRollOver"];
}
else{
	$paramOnRollOver = '';
}
if (is_post('_cues')){
	$sCues = $_POST["_cues"];
}
else{
	$sCues = '';
}


// Chargement de la classe
require_once('cms-inc/lib/fileUpload/upload.class.php');

// Instanciation d'un nouvel objet "upload"
$Upload = new Upload();

// Pour ne pas écraser un fichier existant
$Upload->  WriteMode  = '1';

 // Définition du répertoire de destination
dirExists("/custom/video/".$_SESSION["rep_travail"]);		
$Upload-> DirUpload    = $_SERVER['DOCUMENT_ROOT']."/custom/video/".$_SESSION["rep_travail"];

// Pour limiter la taille d'un  fichier (exprimée en ko)
$Upload-> MaxFilesize  = '10000';

// On lance la procédure d'upload
$Upload-> Execute();

if (is_file($Upload->Infos[1]['chemin'])){
	// trick avec * à cause de DOCUMENT_ROOT sur lien symbolique (hephaistos)
	$selectedFile = preg_replace('/.*\*/', '', str_replace($_SERVER['DOCUMENT_ROOT'], '*', $Upload->Infos[1]['chemin']));	
}
elseif(is_file($_SERVER['DOCUMENT_ROOT'].$_POST['video'])){
	$selectedFile = $_POST['video'];
}
else{
?>
<script type="text/javascript">
	alert("Choix du fichier video incorrect");
	history.back();
</script>
	<?php
	die();
}


if (is_file($_SERVER['DOCUMENT_ROOT'].$selectedFile)){
	$getID3 = new getID3;			
	$ThisFileInfo = $getID3->analyze($_SERVER['DOCUMENT_ROOT'].$selectedFile);	
	
	if (isset($ThisFileInfo["flv"]["meta"]["onMetaData"]["width"])){
		$width = $ThisFileInfo["flv"]["meta"]["onMetaData"]["width"];
	}
	else{
		$width = $ThisFileInfo["video"]["resolution_x"];
	}
	if (isset($ThisFileInfo["flv"]["meta"]["onMetaData"]["height"])){
		$height = $ThisFileInfo["flv"]["meta"]["onMetaData"]["height"];
	}
	else{
		$height = $ThisFileInfo["video"]["resolution_y"];
	}
}


if (is_post('nom')){
	$nom = $_POST["nom"];
}
elseif($_POST["video"]!="-1"){
	$nom = basename($_POST["video"]);	 
}
else{
	$nom = basename($selectedFile);
}

if (is_file($Upload->Infos[2]['chemin'])){
	// trick avec * à cause de DOCUMENT_ROOT sur lien symbolique (hephaistos)
	$vignette = preg_replace('/.*\*/', '', str_replace($_SERVER['DOCUMENT_ROOT'], '*', $Upload->Infos[2]['chemin']));	
}
elseif(is_file($_SERVER['DOCUMENT_ROOT'].'/custom/video/'.$_SESSION['rep_travail'].'/'.$_POST['vignette'])){
	$vignette = '/custom/video/'.$_SESSION['rep_travail'].'/'.$_POST['vignette'];
}
else{
	// pas de vignette
}

// resize vignette
if (is_file($_SERVER['DOCUMENT_ROOT'].$vignette)){	
	$oIm = imagecreatefromAnyFile($_SERVER['DOCUMENT_ROOT'].$vignette);
	$oIm = resizeImageObjectWidthHeightStrict($oIm, $width, $height);
	imageoutputtoAnyFile($oIm, $_SERVER['DOCUMENT_ROOT'].$vignette);
}
?>
<script type="text/javascript">
function insertRow(){
	nextNum = parseInt(document.getElementById('numcue').value);	
	currentHTML = "";
	for (i=0;i<nextNum;i++){
		currentHTML += '<br /><input id="cue'+i+'" name="cue'+i+'" type="text" value="'+document.getElementById('cue'+i).value+'" /><input id="cuevalue'+i+'" name="cuevalue'+i+'" type="text" value="'+document.getElementById('cuevalue'+i).value+'" />'	
	}
	document.getElementById('cues').innerHTML = currentHTML + '<br /><input id="cue'+nextNum+'" name="cue'+nextNum+'" type="text" value="" /><input id="cuevalue'+nextNum+'" name="cuevalue'+nextNum+'" type="text" value="" />';
	document.getElementById('numcue').value = nextNum + 1;
}

</script>
<form method="post" enctype="multipart/form-data" name="formulaire" id="formulaire" action="<?php echo $_SERVER['PHP_SELF']; ?>?idSite=<?php echo $idSite; ?>&id=<?php echo $id; ?>&">
<span class="arbo2"><strong>Nom:<br /></strong></span>

<input type="text" id="nom" name="nom" value="<?php echo $nom; ?>" class="arbo" size="50" /><br />

<span class="arbo2"><strong>Choix des paramètres pour <?php echo $selectedFile; ?> :</strong></span><br /><br />
<span class="arbo2">Nom paramètre / Valeur</span><br />
<?php

echo "<div id=\"rows\" class=\"arbo\">\n";

$eParamCount = 0;

// params
	
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
echo "<input id=\"param6\" name=\"param6\" type=\"hidden\" value=\"volume\" /><input id=\"value6\" name=\"value6\" type=\"text\" value=\"".$paramVolume."\" /> (entrez un volume de 0 à 100)";
// _onRollOver
echo "<br /><input id=\"__7\" name=\"__7\" type=\"text\" value=\"onRollOver\" disabled=\"1\" />";
echo "<input id=\"param7\" name=\"param7\" type=\"hidden\" value=\"onRollOver\" /><input id=\"value7\" name=\"value7\" type=\"text\" value=\"".$paramOnRollOver."\" /> (entrez une fonction actionScript - ex: trace(this._name) )";

$eParamCount = 8;

echo "</div>";
// fin params ----------------------------------------------------------

// CUES

echo '<br /><br /><span class="arbo2">Cues : Temps (secs.) / Nom</span><br />';

echo "<div id=\"cues\" class=\"arbo\">\n";

$aCues = explode('&', $sCues);
$eCueCount = 0;

if (count($aCues)>0){
	foreach($aCues as $k => $sCueSet){
		if (strlen($sCueSet) > 0){
			$aCueSet = explode("=", $sCueSet);
			echo "<input id=\"cue".$eCueCount."\" name=\"cue".$eCueCount."\" type=\"text\" value=\"".str_replace('_cue', '', $aCueSet[0])."\" />";
			echo "<input id=\"cuevalue".$eCueCount."\" name=\"cuevalue".$eCueCount."\" type=\"text\" value='".flashurl_decode(utf8_decode($aCueSet[1]))."' /><br />";
			$eCueCount++;
		}
	}
}
if ($eCueCount == 0){
	// _start
	echo "<br />";
	echo "<input id=\"cue0\" name=\"cue0\" type=\"text\" value=\"\" /><input id=\"cuevalue0\" name=\"cuevalue0\" type=\"text\" value=\"\" /> (entrez des secondes . millisecondes)";
	$eCueCount = 1;
}

echo "</div>";
echo "<input type=\"button\" name=\"Insert\" value=\"Ajouter\" class=\"arbo\" onclick=\"insertRow();\" /><br />";	

?>


<br />
<img src="<?php echo $vignette; ?>" alt="Vignette" />
<input type="hidden" id="numparam" name="numparam" value="<?php echo $eParamCount; ?>" />
<input type="hidden" id="numcue" name="numcue" value="<?php echo $eCueCount; ?>" />
<input type="hidden" id="TYPEcontent" name="TYPEcontent" value="video" class="arbo" />
<input type="hidden" id="type" name="type" value="<?php echo $_POST['type']; ?>" class="arbo" />
<input type="hidden" id="idSite" name="idSite" value="<?php echo $idSite; ?>" />
<input type="hidden" id="id" name="id" value="<?php echo $id; ?>" />
<input type="hidden" id="video" name="video" value="<?php echo $selectedFile; ?>" />
<input type="hidden" id="vignette" name="vignette" value="<?php echo $vignette; ?>" />
<input type="hidden" id="height" name="height" value="<?php echo $height; ?>" />
<input type="hidden" id="width" name="width" value="<?php echo $width; ?>" />

<input type="hidden" id="pickparam" name="pickparam" value="pickparam" />
<input type="submit" id="submit" name="submit" class="arbo" value="Envoyer" />
</form>
<?php
}
elseif (is_post("pickparam")) {

$svideo = $_POST['video'];
$vignette = $_POST['vignette'];
$width = $_POST["width"];
$height = $_POST["height"];
$vidName = basename($svideo);
$vidURL =  "http://".$_SERVER['HTTP_HOST'].$svideo;
$phpURL = "http://".$_SERVER['HTTP_HOST']."/backoffice/cms/utils/flvprovider.php";
$vidQuery = "_vidName=".$vidName."&amp;_vidURL=".$vidURL."&amp;_phpURL=".$phpURL."&amp;_vidThb=".basename($vignette).'&amp;';
$svideoUrl = "/backoffice/cms/utils/scrubberLarge";
$sFlashVars = $vidQuery;

if (($_POST["numparam"] > 0) && (is_post("param0"))){	
	for ($i=0;$i<$_POST["numparam"];$i++){
		if (strlen($_POST["param".$i]) > 0){
			$sFlashVars .= "_".$_POST["param".$i]."=".(flashurl_encode($_POST["value".$i]))."&amp;";
		}
	}
}


if (($_POST["numcue"] > 0) && (is_post("cue0"))){	
	$sFlashVars .= "_cues=1&amp;";
	for ($i=0;$i<$_POST["numcue"];$i++){		
		if (strlen($_POST["cue".$i]) > 0){
			$sFlashVars .= "_cue".$_POST["cue".$i]."=".(flashurl_encode($_POST["cuevalue".$i]))."&amp;";
			$height+=21;
		}
	}
}




$sBodyHTML = '<script src="/backoffice/cms/js/AC_RunActiveContent.js" type="text/javascript"></script>';
$sBodyHTML .= '<script type="text/javascript">';
$sBodyHTML .= 'flashvars = "'.$sFlashVars.'embedUrl="+escape(document.location.href);';

$sBodyHTML .= 'swfSrcFull = "'.str_replace("scrubberLarge", "scrubberLarge.swf", $svideoUrl).'";';
$sBodyHTML .= 'swfSrc = "'.$svideoUrl.'";';

$sBodyHTML .= 'AC_FL_RunContent( "codebase","https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,1,0,0",
"width","'.$width.'","height","'.$height.'","src", swfSrc, "quality","high","pluginspage","https://get.adobe.com/flashplayer/",
"align", "middle", "play", "true", "loop", "true", "scale", "showall", "wmode", "opaque", "devicefont", "false",
"id", "awsvid", "name", "awsvid", "menu", "false", "allowFullScreen", "false",
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
				if (a[i].id == "awsvid"){
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

$sBodyHTML .= '</script>';
?>
<span class="arbo2"><strong>Preview de la brique video pour <?php echo $_POST['video']; ?> :</strong></span>
<?php 
echo $sBodyHTML;
?><br />

<form method="post" name="formulaire" id="formulaire" action="saveComposant.php?idSite=<?php echo $idSite; ?>&id=<?php echo $id; ?>&">

<input type="hidden" id="TYPEcontent" name="TYPEcontent" value="video" class="arbo" />
<input type="hidden" id="WIDTHcontent" name="WIDTHcontent" value="<?php echo $width; ?>">
<input type="hidden" id="HEIGHTcontent" name="HEIGHTcontent" value="<?php echo $height; ?>">
<input type="hidden" id="NAMEcontent" name="NAMEcontent" value="<?php echo $_POST['nom']; ?>">
<input type="hidden" id="HTMLcontent" name="HTMLcontent" value='<?php echo $sBodyHTML; ?>'>
<input type="hidden" id="OBJTABLEcontent" name="OBJTABLEcontent" value="<?php echo $_POST['type']; ?>">
<input type="hidden" id="OBJIDcontent" name="OBJIDcontent" value="<?php echo $_POST['fitem']; ?>">
<input type="hidden" id="largeur" name="largeur" value="<?php echo $width; ?>" class="arbo" />
<input type="hidden" id="hauteur" name="hauteur" value="<?php echo $height; ?>" class="arbo" />
<input type="hidden" id="nom" name="nom" value="<?php echo $_POST['nom']; ?>" class="arbo" />
<input type="hidden" id="type" name="type" value="<?php echo $_POST['type']; ?>" class="arbo" />
<input type="hidden" id="idSite" name="idSite" value="<?php echo $idSite; ?>" />
<input type="submit" id="submit" name="submit" class="arbo" value="Sauver" />
<input type="hidden" id="id" name="id" value="<?php echo $id; ?>" />
<input type="hidden" id="video" name="video" value="<?php echo $svideo; ?>" /><br />
<br />
<br />
<span class="arbo2"><strong>Code HTML d'inclusion de la brique vidéo (copier/coller) :</strong></span>

<textarea id="copypaste" name="copypaste" class="textareaEdit" ><?php echo compactJS($sBodyHTML); ?></textarea>
</form>
<?php	
}
?>