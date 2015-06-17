<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

if (is_file($_SERVER['DOCUMENT_ROOT'].'/custom/graphiques/'.$_SESSION['rep_travail'].'/template.xml')){ 
	xmlFileParse($_SERVER['DOCUMENT_ROOT'].'/custom/graphiques/'.$_SESSION['rep_travail'].'/template.xml');
}
else{
	xmlFileParse('template.xml');
}

if (!isset($chart)){
	$chart = array();
}

foreach ($stack[0]['children'] as $k => $child){
	
	if (!isset($chart[strtolower($child['name'])])){
		$chart[strtolower($child['name'])] =  array();	
	}
	
	if (count($child['children']) > 0){
		foreach ($child['children'] as $kk => $grandchild){
			if (count($grandchild['attrs']) > 0){
				
				foreach($grandchild['attrs'] as $attrsName => $attrsVal){
					$chart[strtolower($child['name'])][$kk][strtolower($attrsName)] = $attrsVal;
				}
			}
			else{
				$chart[strtolower($child['name'])][$kk] = $grandchild['cdata'];
			}	
		}
	}
	else{
		//$chart[strtolower($child['name'])] = $child['attrs'];
		foreach($child['attrs'] as $attrsName => $attrsVal){
			$chart[strtolower($child['name'])][strtolower($attrsName)] = $attrsVal;
		}
	}
}

?>