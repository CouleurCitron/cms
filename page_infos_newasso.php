<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once("cms-inc/include_cms.php");
include_once("cms-inc/include_class.php");
// http://pierre.equation.hephaistos.interne/backoffice/cms/page_infos_newasso.php?maj=%2Fbackoffice%2Fcms%2Fnws_content%2Fmaj_nws_content.php&className=http://pierre.equation.hephaistos.interne/backoffice/cms/page_infos_newasso.php?maj=%2Fbackoffice%2Fcms%2Fnws_content%2Fmaj_nws_content.php&className=nws_content&idPage=23&nodeId=1017&classId=89
$className = $_GET['className'];

$oTemp = new $className();
			
if(!is_null($oTemp->XML_inherited))
	$sXML = $oTemp->XML_inherited;
else
	$sXML = $oTemp->XML;
	
xmlClassParse($sXML);
$classePrefixe = $stack[0]["attrs"]["PREFIX"];
$aNodeToSort = $stack[0]["children"];

foreach($stack[0]["children"] as $kI => $item){
	if (isset($item['attrs']['FKEY'])&&($item['attrs']['FKEY']=='cms_page')){
		$setterPage = 'set_'.$item['attrs']['NAME'];
		$oTemp->$setterPage($_GET['idPage']);
	}				
	elseif (isset($item['attrs']['FKEY'])&&($item['attrs']['FKEY']=='cms_arbo_pages')){
		$setterPage = 'set_'.$item['attrs']['NAME'];
		echo $setterPage;
		$oTemp->$setterPage($_GET['nodeId']);
	}			
}			

$idAssoObj = dbSauve($oTemp);
		
$oX = new cms_assoclassepage();
$oX->set_cms_page($_GET['idPage']);
$oX->set_classe($_GET['classId']);
$oX->set_objet($idAssoObj);
dbSauve($oX);

$redirUrl = $_GET['maj'].'?id='.$idAssoObj.'&noMenu=true';

header('Location: '.$redirUrl);
echo $_GET['maj'];
?>