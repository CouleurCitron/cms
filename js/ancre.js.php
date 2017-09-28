<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');  
header('Content-Type: application/x-javascript');  
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

$sql = "SELECT cms_arbo_pages.* from cms_arbo_pages";

/*$sUrl 	= "";  
$sAncre = "";
$sMinisite = "";*/

echo "function conteneur(nUrl,nAncre,nIdSite, nNode) {\n";
	echo "this.monUrl = nUrl;\n";
	echo "this.monAncre = nAncre;\n";
	echo "this.monIdSite = nIdSite;\n";
	echo "this.monNode = nNode;\n";
echo "}\n";

echo "var tab_Ancre = new Array(";

$aObjets= dbGetObjectsFromRequete("cms_arbo_pages", $sql); 
$cpt = 0;
if (sizeof($aObjets) > 0) {
	foreach ($aObjets as $oObjet) { 

		echo "new conteneur(";

		//$sUrl.= "	\"".$oObjet->get_absolute_path_name()."\""; 
		$sConteneur = "	\"".$oObjet->get_absolute_path_name()."\", "; 
		//if ($cpt < sizeof($aObjets)-1) $sUrl.= ", \n"; 

		$aAncre = explode ("/", $oObjet->get_absolute_path_name());
		$sConteneur.= "	\"".$aAncre[(sizeof($aAncre)-2)]."\", ";
		//if ($cpt < sizeof($aObjets)-1) $sAncre.= ", \n"; 

		$sConteneur.= "	\"".$oObjet->getId_site()."\", ";

		$sConteneur.= "	\"".$oObjet->getNode_Id()."\"";
		//

		echo $sConteneur.") ";

		if ($cpt < sizeof($aObjets)-1) echo", \n"; 
		$cpt++;

	}	
}

echo ");\n";

echo "function parseUrl1(data) {\n"; 
	echo "var e=/^((http|ftp):\/)?\/?([^:\/\s]+)(\/#([\w\-]+))?$/;\n"; 
	echo "var e2=/^((http|ftp):\/)?\/?([^:\/\s]+)(\/\?id=\d+)(#([\w\-]+))?$/;\n";
	echo "var e3=/^((http|ftp):\/)?\/?([^:\/\s]+)(\/#([\w\-^\/]+))\/([\w\-]+)?$/;\n";

	echo "if (data.match(e)) {\n";
	//echo "	alert('trouve_e')\n";
	echo "	return  {url: RegExp['$&'],\n";
	echo "			protocol: RegExp.$2,\n";
	echo "			host:RegExp.$3,\n";
	echo "			path:RegExp.$4,\n";
	echo "			  ancre:RegExp.$5,\n";
	echo "			file:RegExp.$6,\n";
	echo "			hash:RegExp.$7};\n";
	echo "}\n";
	echo "else  if (data.match(e2)) {\n";
	//echo "	alert('trouve_e2')\n";
	echo "	return  {url: RegExp['$&'],\n";
	echo "			protocol: RegExp.$2,\n";
	echo "			host:RegExp.$3,\n";
	echo "			path:RegExp.$4,\n"; 
	echo "			  path3:RegExp.$5,\n";
	echo "			ancre:RegExp.$6,\n";
	echo "			hash:RegExp.$7};\n";
	echo "}\n";
	echo "else  if (data.match(e3)) {\n";
	//echo "	alert('trouve_e3')\n";
	echo "	return  {url: RegExp['$&'],\n";
	echo "			protocol: RegExp.$2,\n";
	echo "			host:RegExp.$3,\n";
	echo "			path:RegExp.$4,\n"; 
	echo "			  path3:RegExp.$5,\n";
	echo "			ancre:RegExp.$6,\n";
	echo "			hash:RegExp.$7};\n";
	echo "}\n";
	echo "else {\n";
		echo "return  {url:\"\", protocol:\"\",host:\"\",path:\"\",file:\"\",hash:\"\"};\n";
	echo "}\n";
echo "}\n";


echo "function test_ancre(){\n";


// cas  
echo "var sUrl = String(window.top.document.location.href);  \n";
echo "var thisUrl = String(this.document.location.href);  \n"; 

echo "var Url_reg = \"\";\n";
echo "Url_reg = parseUrl1(sUrl); \n";

echo "var hostName = Url_reg[\"host\"] \n";
echo "var nameAncre = Url_reg[\"ancre\"] \n";

echo "if(nameAncre  != \"\" && nameAncre  != undefined && thisUrl.indexOf(\"home.php\") > 0){\n";


	echo "var  iAncre = -1;\n";
	//echo "alert('trouve'+tab_Ancre[0].monAncre)\n";
	echo "for (var i in tab_Ancre){\n";
	//echo "	tempStr = tempStr + (obj_name+"."+i+"="+obj[i]+" - ");\n";
		echo "	if (tab_Ancre[i].monNode == nameAncre) {\n";
		//echo "alert('trouve'+tab_Ancre[iAncre].monAncre)\n";
		echo "		iAncre = i\n";	
		echo "	}\n"; 
	echo "}\n";

	//echo "alert('iAncre '+iAncre+' nameAncre '+nameAncre)\n";  
	echo "	if (iAncre == -1) {\n";
		echo "for (var i in tab_Ancre){\n";
		echo "	if (tab_Ancre[i].monAncre == nameAncre) {\n";
			//echo "alert('trouve'+tab_Ancre[i].monAncre)\n";
		echo "		iAncre = i\n";	
		echo "	}\n"; 
			echo "}\n";
	echo "}\n";
	//echo "alert('iAncre '+iAncre)\n";  
	echo "	if (iAncre != -1) {\n";


		echo "items = thisUrl.split(\"/\")\n;";
		echo "var currentSite = items[4] \n;";

		echo "if (tab_Ancre[iAncre].monUrl.indexOf(currentSite) > 0) { \n";

			echo "	var baseURL = 'http://'+hostName+'/content'+tab_Ancre[iAncre].monUrl;\n";
			echo " \n";
			echo "if (window.top.document.getElementById('iframediv') !=null) { \n";
			echo "	if (window.parent.document.location.href == document.location.href){\n";
			echo "		//  \n";
			echo "return false; \n";
			echo "	}\n";
			echo "	else { \n";
			echo "		if (document.location.href != baseURL) {\n";
			echo "			//this.document.location.href = baseURL;\n";
			echo "			return baseURL; \n";
			echo "		}\n";
			echo "	else { \n";
			echo "			return false; \n";
			echo "		} \n"; 
			echo "	} \n";
			echo "\n";
			echo "} \n";



		echo "	} else {";  
			echo "window.top.document.location.href = 'http://'+hostName+'/?id='+tab_Ancre[iAncre].monIdSite+'#'+nameAncre;\n";
			echo "return false; \n";
		echo "	}\n"; // fin 	if (currentSite == tab_Ancre[iAncre].monIdSite) {


	echo "	}\n";
	/////////////////////////

	////////////////////////





echo "}\n";

echo "}\n"; 
?>