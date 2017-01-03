<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/*
$fw = fopen("test.txt","a");
fwrite($fw,"\n#############\n\n\n\n".$_SERVER["REQUEST_URI"]);
fclose($fw);
*/

$mybuffer = "";

function elementochangetout($buffer)
{
	/*
	$fw = fopen("test.txt","a");
	fwrite($fw,$buffer);
	fclose($fw);
	*/
	global $mybuffer;
	$mybuffer .= $buffer;
}

if ( isset($_GET["filterdescription"]) ) ob_start("elementochangetout");


//header("Content-type: text/plain; charset=utf-8");
header("Content-type: text/xml; charset=utf-8");

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');

$debProcess = time();


if ((isset($_GET['idSite'])) and ($_GET['idSite'] != "")){
	$idSite = $_GET['idSite'];
}

// $idSite ------------------------------------------------------------
// param du referer --------------------------------------------
if ((isset($_REQUEST['refer'])) and ($_REQUEST['refer'] != "")){
	$referUrl = $_REQUEST['refer'];
}
else{
	$referUrl = $_SERVER['PHP_SELF'];
	//$referUrl = "http://pierre.zodiac.hephaistos.interne/content/group-fr/Metiers/AEROSAFETY_AND_TECHNOLOGY/index.php";

}
if (!isset($idSite)){
	$idSite = path2idside($db, $referUrl);
} //-------------------------------------------------------------

if ((isset($_GET['id'])) and ($_GET['id'] != "")){
	$id = $_GET['id'];
}
else{
	$id = 0;
}

// $courant ------------------------------------------------------------
if (!isset($courant)){

	// pour une page donnée, le module retourne
	// la descrition du node courant, sa liste de fils
	if ((isset($_REQUEST['path'])) and ($_REQUEST['path'] != "")){
		$urlToList = urldecode($_REQUEST['path']);
		$pathToList = substr ($urlToList, 0, strrpos ($urlToList, "/") + 1);
	}
	else{
		//$urlToList = '/content/index.php';
		$urlToList = preg_replace("/[htps]{4,5}:\/\/[^\/]+\//msi", "/", $referUrl);
		//$urlToList = $PHP_SELF;
		$pathToList = substr ($urlToList, 0, strrpos ($urlToList, "/") + 1);

	}

	// /content/BBENT/ devient /content/
	if ($pathToList == '/content/'.path2minisiteRepertoire($db, $pathToList).'/'){
		$pathToList = '/content/';
	}

	// calcul du path de niveau 1 correspondant à la page en cours
	$aTempPathToList = explode("/", $pathToList);
	if (count($aTempPathToList) > 2){
		$aTempPathToList = array_slice(explode("/", $pathToList),0,4);
	}
	$basePathToList = join($aTempPathToList, "/")."/";
	$courant = getNodeInfosReverse($idSite,$db,stripslashes($pathToList));


} // -----------------------------------------------------------

// $topLevelCurrentNode ------------------------------------------------------------
if (!isset($topLevelCurrentNode)&&($courant!=false)){
	// calcul du node de top level niveau courant
	$tempNode = $courant;
	if ($courant['parent'] != "0"){
		while($tempNode['parent'] != "0"){
			$tempNode = getNodeInfos($db,$tempNode['parent']);

		}
		$topLevelCurrentNode = $tempNode;
	}
	else{
		$topLevelCurrentNode = $tempNode;
	}

}


// -------------------------------------------------------------
/*
// calcul du node de premir niveau courant
if ($courant['id'] != $topLevelCurrentNode['id']){
	$tempNode = $courant;
	while($tempNode['parent'] != $topLevelCurrentNode['id']){
		$tempNode = getNodeInfos($db,$tempNode['parent']);
	}
	$firstLevelCurrentNode = $tempNode;

	// calcul du node de second niveau courant
	/*
	if ($courant['id'] != $firstLevelCurrentNode['id']){
		$tempNode = $courant;
		while($tempNode['parent'] != $firstLevelCurrentNode['id']){
			$tempNode = getNodeInfos($db,$tempNode['parent']);
		}
		$secondLevelCurrentNode = $tempNode;

		// calcul du node de third niveau courant
		if ($courant['id'] != $secondLevelCurrentNode['id']){
			$tempNode = $courant;
			while($tempNode['parent'] != $secondLevelCurrentNode['id']){
				$tempNode = getNodeInfos($db,$tempNode['parent']);
			}
			$thirdLevelCurrentNode = $tempNode;
		}
	}

}*/
//------------------------------------------------


echo "<?xml version='1.0' encoding='utf8' ?".">\n";

echo "<menu>\n";
echo array2node($courant, "courant", $courant["id"], true, true);
echo "<nodes>\n";

/*
function getNodeInfos($idSite, $db, $absolutePath){
function getNodeChildren($idSite, $db, $path) {
		//array2node($aInfo, "node", $id, $encode=false, $fermer=true, $onlyAttribs=NULL){
*/

function getNodeAsXML($idSite, $db, $path, $currentNodeId=0){
	$aInfo = getNodeInfos($db, $path);
	if (intval($currentNodeId) == intval($aInfo["id"])){
		$aInfo["iscourant"] = 1;
	}
	else{
		$aInfo["iscourant"] = 0;
	}

	$aChidren = getNodeChildren($idSite, $db, $path);

	if ($aChidren != false){
		// write sub node - opened node
		echo array2node($aInfo, "node", $aInfo["id"], true, false);
		for($i=0;$i<count($aChidren);$i++){
			if (intval($aChidren[$i]["order"]) != 0){
				getNodeAsXML($idSite, $db, $aChidren[$i]["id"], $currentNodeId);
			}
		}
		echo "</node>\n";
	}
	else{
		// closed node
		echo array2node($aInfo, "node", $aInfo["id"], true, true);
	}

}

getNodeAsXML($idSite, $db, 0, $courant["id"]);

echo "</nodes>\n";
$endProcess = time();
echo "<process time=\"".($endProcess-$debProcess)."\" />\n";
echo "</menu>\n";

if ( isset($_GET["filterdescription"]) ) {


	ob_end_flush();

//echo $mybuffer;

	$parts = explode("description\=\"",$mybuffer);

	foreach ( array_keys($parts) as $key ) {
		if ( $key == 0 ) continue;
		list($description,$rest) = explode("\"",$parts[$key],2);
		$mydescrition = array();
		$mydescription = @explode("\;",$description,3);
		$mydescription = $mydescription[$_GET["filterdescription"]-1];
		$parts[$key] = $mydescription."\"".$rest;
	}
echo implode("description=\"",$parts);
}


?>