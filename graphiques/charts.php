<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* new archi
"/modules/graphiques/xmldata/

"/custom/graphiques/".$_SESSION['rep_travail']."/
*/

// charts.php v3.5
// ------------------------------------------------------------------------
// Copyright (c) 2003-2005, maani.us
// ------------------------------------------------------------------------
// This file is part of "PHP/SWF Charts"
//
// PHP/SWF Charts is a shareware. See http://www.maani.us/charts/ for
// more information.
// ------------------------------------------------------------------------

//====================================
function InsertChart( $flash_file, $php_source, $width=400, $height=250, $bg_color="666666", $transparent=false, $license=null ){
	
	$php_source=urlencode($php_source);
		
	$html="<OBJECT classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,1,0,0' ";
	$html.="WIDTH=".$width." HEIGHT=".$height." id='charts' ALIGN=''>";
	$html.="<PARAM NAME=movie VALUE='".$flash_file."?php_source=".$php_source;
	if($license!=null){$html.="&license=".$license;}
	$html.="'> <PARAM NAME=quality VALUE=high> <PARAM NAME=bgcolor VALUE=#".$bg_color."> ";
	if($transparent){$html.="<PARAM NAME=wmode VALUE=transparent> ";}
	else {$html.="<PARAM NAME=wmode VALUE=opaque> ";}
	$html.="<EMBED src='".$flash_file."?php_source=".$php_source;
	if($license!=null){$html.="&license=".$license;}
	$html.="' quality=high bgcolor=#".$bg_color." WIDTH=".$width." HEIGHT=".$height." NAME='charts' ALIGN='' ";
	if($transparent){$html.="wmode=transparent ";}
	else {$html.="wmode=opaque ";}
	$html.="TYPE='application/x-shockwave-flash' PLUGINSPAGE='https://get.adobe.com/flashplayer/'></EMBED></OBJECT>";
	return $html;
}

//====================================
function SendChartData( $chart=array() ){

	if(isset($chart['chart_data'])){
		ksort($chart['chart_data'],SORT_NUMERIC);
		for($r=0;$r<count($chart['chart_data']);$r++){
			ksort($chart['chart_data'][$r],SORT_NUMERIC);
			if(!isset($chart['chart_value_text'][$r])){$chart['chart_value_text'][$r]=array();}
			for($c=0;$c<count($chart['chart_data'][$r]);$c++){
				if(!isset($chart['chart_value_text'][$r][$c])){$chart['chart_value_text'][$r][$c]=null;}
			}
			ksort($chart['chart_value_text'][$r],SORT_NUMERIC);
		}
		ksort($chart['chart_value_text'],SORT_NUMERIC);
	}
	
	$xml="<chart>\r\n";
	
	// api key
	$aKey = dbGetObjectsFromFieldValue('cms_chartskey', array('get_host'), array($_SERVER['HTTP_HOST']), NULL);
	if ((count($aKey) == 1)&&($aKey!=false)){
		$sKey = $aKey[0]->get_key();
		$xml.='<license>'.$sKey.'</license>'."\r\n";
	}
	
	
	$Keys1= array_keys((array) $chart);
	for ($i1=0;$i1<count($Keys1);$i1++){
		if(is_array($chart[$Keys1[$i1]])){
			$Keys2=array_keys($chart[$Keys1[$i1]]);
			if(is_array($chart[$Keys1[$i1]][$Keys2[0]])){
				$xml.="\t<".$Keys1[$i1].">\r\n";
				for($i2=0;$i2<count($Keys2);$i2++){
					$Keys3=array_keys((array) $chart[$Keys1[$i1]][$Keys2[$i2]]);
					switch($Keys1[$i1]){
						case "chart_data":
						$xml.="\t\t<row>\r\n";
						for($i3=0;$i3<count($Keys3);$i3++){
							switch(true){
								case ($chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]]==="" or $chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]]===null):
								$xml.="\t\t\t<null/>\r\n";
								break;
								
								case ($Keys2[$i2]>0 and $Keys3[$i3]>0):
								$xml.="\t\t\t<number>".$chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]]."</number>\r\n";
								break;
								
								default:
								$xml.="\t\t\t<string>".$chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]]."</string>\r\n";
								break;
							}
						}
						$xml.="\t\t</row>\r\n";
						break;
						
						case "chart_value_text":
						$xml.="\t\t<row>\r\n";
						$count=0;
						for($i3=0;$i3<count($Keys3);$i3++){
							if($chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]]===null){$xml.="\t\t\t<null/>\r\n";}
							else{$xml.="\t\t\t<string>".$chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]]."</string>\r\n";}
						}
						$xml.="\t\t</row>\r\n";
						break;
						
						case "draw_text":
						$xml.="\t\t<text";
						for($i3=0;$i3<count($Keys3);$i3++){
							if($Keys3[$i3]=="text"){$text=$chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]];}
							else{$xml.=" ".$Keys3[$i3]."='".$chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]]."'";}
						}
						$xml.=">".$text."</text>\r\n";
						break;
						
						default://draw_circle, etc.
						$xml.="\t\t<value";
						for($i3=0;$i3<count($Keys3);$i3++){
							$xml.=" ".$Keys3[$i3]."='".$chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]]."'";
						}
						$xml.=" />\r\n";
						break;
					}
				}
				$xml.="\t</".$Keys1[$i1].">\r\n";
			}else{
				if($Keys1[$i1]=="chart_type" or $Keys1[$i1]=="series_color" or $Keys1[$i1]=="series_explode"){
					$xml.="\t<".$Keys1[$i1].">\r\n";
					for($i2=0;$i2<count($Keys2);$i2++){
						$xml.="\t\t<value>".$chart[$Keys1[$i1]][$Keys2[$i2]]."</value>\r\n";
					}
					$xml.="\t</".$Keys1[$i1].">\r\n";
				}else{//axis_category, etc.
					$xml.="\t<".$Keys1[$i1];
					for($i2=0;$i2<count($Keys2);$i2++){
						$xml.=" ".$Keys2[$i2]."='".$chart[$Keys1[$i1]][$Keys2[$i2]]."'";
					}
					$xml.=" />\r\n";
				}
			}
		}else{//chart type, etc.
			$xml.="\t<".$Keys1[$i1].">".$chart[$Keys1[$i1]]."</".$Keys1[$i1].">\r\n";
		}
	}
	$xml.="</chart>\r\n";
	return utf8_encode(stripslashes($xml)); // Ne l'crit pas le retourne
}


// mesure de la hauteur du texte (compte les retours chariots \\r) recursivement
// A partir d'un tableau de chaines
function GetNbLig ($Array) {
	if (is_array ($Array)) {
		$max=0;
		foreach($Array as $cellule) {
			$max = max($max,GetNbLig ($cellule));
		}
		return $max;
	}
	else { return substr_count($Array,"\\r")+1; }
}


// Supprimer tous les caractres non numriques recursivement
// Attention cette fonction agit directement sur le tableau pass par rfrence
function KeeponlyAllNumeric (&$ArrayGET) {
	if (is_array ($ArrayGET)) array_walk($ArrayGET, "KeeponlyAllNumeric");
	else {
		$ArrayGET = preg_replace("/\,/",'.',$ArrayGET); // converti , en point
		$ArrayGET = ereg_replace("[^0-9\.]*",'',$ArrayGET); // supprime tous les caractres non numriques et hors "point"
	}
}


function ecrit_chart_data( $id_graph, $chart_write ) {
// Ecriture des donnes du graphique en XML dans
// /modules/graphiques/xmldata/graphic_$id_graph.php
		$fstr = $_SERVER['DOCUMENT_ROOT'].'/custom/graphiques/'.$_SESSION['rep_travail'].'/graphic_'.$id_graph.'.php';
		$fh = fopen($fstr,'w');

		$wReturn = fwrite( $fh, SendChartData ( $chart_write ) );
		
		fclose($fh);
		//error_log('wrote: '.$wReturn.' in '.$fstr);
		return $wReturn;
}

// Supprimer des antislash rcursivement dans des tableaux imbriqus
function StripAllSlashes (&$ArrayGET) {
	if (is_array ($ArrayGET)) array_walk($ArrayGET, "StripAllSlashes");
	else $ArrayGET = stripslashes ($ArrayGET);
}

// spcial!!! si le tableau a demand  tre invers, on l'inverse maintenant
function invert_tab($t){
  if (sizeof($t) >0)
	for($i=0;$i<sizeof($t);$i++) {
		if (sizeof($t[$i]) >0)
			for($j=0;$j<sizeof($t[$i]);$j++) {
				$new_tab[$j][$i]= $t[$i][$j];
			}
	}
	if (sizeof($new_tab)>0) return $new_tab;
	else return false;
}


// spcial: si le tableau est  rcuprer d'un copier coller d'excel
// on le fait maintenant
function import_tabexcel($import) {
	$tab = preg_split("/[\r]?\n/",$import); // Sparation de chaque lignes du tableau
	
	// si la dernire ligne est vide on la supprime
	if(sizeof($tab)>0 && $tab[sizeof($tab)-1]=="") array_pop($tab);
	
	for($i=0;$i<sizeof($tab);$i++) {
		$tab[$i] = preg_split("/\t/",$tab[$i]); // Sparation de chaque lments des lignes du tableau
	}
	if (sizeof($tab)>0) return $tab;
	else return false;
}

// Extraction des donnes contenu dans le code html du flash
// Rcupration du type de graphique et des donnes brutes
function extractChartData($htmlstring) {		 
	// Format :
	//<!--CHART_ID 1373518847-->
	//<!--CHART_TYPE bar-->
	//<!--CHART_DATA a:3:{i:0;a:3:{i:0;s:0:"";i:1;s:5:"hgngh";i:2;s:6:"ghnghn";}i:1;a:3:{i:0;s:4:"ghng";i:1;s:2:"10";i:2;s:2:"10";}i:2;a:3:{i:0;s:4:"hghn";i:1;s:2:"10";i:2;s:2:"10";}}-->
	//<!--CHART_TITLE Production+%E9nerg%E9tique+totale+en+Ari%E8ge-->
	//<!--CHART_LEGEND Production+%E9nerg%E9tique+en+1999+%3A+204+ktepg%0D%0ASources+%3A+OREMIP%2C+DGEMP%2C+CEREN-->
	//<!--CHART_TXT bla+bla-->
	//<!--CHART_HIDEDATA show--> // ou hide

	// ID de graphique
   if ( preg_match ( "/<!\-\-CHART_ID ([^\-]*).{1}\-\->/msi", $htmlstring, $tab_result ) ) {
		$_SESSION['chart_id']=$tab_result[1];
   }
   elseif ( preg_match ( "/<!\-\-CHART_ID ([^\-]*)\-\->/msi", $htmlstring, $tab_result ) ) {
		$_SESSION['chart_id']=$tab_result[1];
   }
   
	// Type de graphique
   if ( preg_match ( "/<!\-\-CHART_TYPE ([^\-]*).{1}\-\->/msi", $htmlstring, $tab_result ) ) {
		$_SESSION['chart_type']=$tab_result[1];
   }
   elseif ( preg_match ( "/<!\-\-CHART_TYPE ([^\-]*)\-\->/msi", $htmlstring, $tab_result ) ) {
		$_SESSION['chart_type']=$tab_result[1];
   }
   
   // Extraction des valeurs
   if ( preg_match ( "/<!\-\-CHART_DATA ([^\-]*).{1}\-\->/msi", $htmlstring, $tab_result ) ) {
		$_SESSION['chart_data']=urldecode($tab_result[1]);
   }
   elseif ( preg_match ( "/<!\-\-CHART_TYPE ([^\-]*)\-\->/msi", $htmlstring, $tab_result ) ) {
		$_SESSION['chart_type']=$tab_result[1];
   }
   
   // Extraction du titre
   if ( preg_match ( "/<!\-\-CHART_TITLE ([^\-]*).{1}\-\->/msi", $htmlstring, $tab_result ) ) {
		$_SESSION['chart_title']=urldecode($tab_result[1]);
   }
   elseif ( preg_match ( "/<!\-\-CHART_TYPE ([^\-]*)\-\->/msi", $htmlstring, $tab_result ) ) {
		$_SESSION['chart_type']=$tab_result[1];
   }
      
   // Extraction de la lgende
   if ( preg_match ( "/<!\-\-CHART_LEGEND ([^\-]*).{1}\-\->/msi", $htmlstring, $tab_result ) ) {
		$_SESSION['chart_legend']=urldecode($tab_result[1]);
   }
   elseif ( preg_match ( "/<!\-\-CHART_TYPE ([^\-]*)\-\->/msi", $htmlstring, $tab_result ) ) {
		$_SESSION['chart_type']=$tab_result[1];
   }
      
   // Extraction du texte complmentaire
   if ( preg_match ( "/<!\-\-CHART_TXT ([^\-]*).{1}\-\->/msi", $htmlstring, $tab_result ) ) {
		$_SESSION['chart_txt']=urldecode($tab_result[1]);
   }
   elseif ( preg_match ( "/<!\-\-CHART_TYPE ([^\-]*)\-\->/msi", $htmlstring, $tab_result ) ) {
		$_SESSION['chart_type']=$tab_result[1];
   }
      
   // Extraction de l'affichage des donnes dans le graph (vrai/faux)
   if ( preg_match ( "/<!\-\-CHART_HIDEDATA ([^\-]*).{1}\-\->/msi", $htmlstring, $tab_result ) ) {
		$_SESSION['chart_hidedata']=$tab_result[1];
   }
   elseif ( preg_match ( "/<!\-\-CHART_TYPE ([^\-]*)\-\->/msi", $htmlstring, $tab_result ) ) {
		$_SESSION['chart_type']=$tab_result[1];
   }
   
   return $_SESSION['chart_id'];
}
//====================================
?>