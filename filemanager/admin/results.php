<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 
$maxCount = 30;

if ($file_count > $maxCount) {
	$nbPages = $file_count / $maxCount;
	echo "Page ";
	
	if (is_as_get("page")) {
		$page = $_GET["page"];
	}
	else {
		$page= 1;
	}
	
	
	if (is_int($nbPages)) 
		$resultRestant = 0;
	else {
		$nbPages = (int) $nbPages;
		$resultRestant = $file_count - ($maxCount * $nbPages);
	}
	
	$nbPages = (int) $nbPages;
	if ($resultRestant >0) $nbPages = $nbPages+1;
	
	for ($i =0; $i<$nbPages;$i++) {
	
		if ($page == ($i+1))
			echo ($i+1)." " ;
		else 	
			echo "<a href=\"admin.php?".$_SERVER["QUERY_STRING"]."&page=".($i+1)."\">".($i+1)."</a> ";
	}
	$debut = ($maxCount*($page-1)+1);
	
	if ($page == $nbPages) 
		$fin = ($maxCount*($page-1))+$resultRestant;
	else 
		$fin = ($maxCount*$page);
}
?> 