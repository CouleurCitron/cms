<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/lib.inc.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');


function set_new_flux ($classeName, $id, $isnew = false) {
	//$isnew = true;
	eval("$"."oClasse = new ".$classeName."($"."id);");
	$url = $oClasse->get_url();
	
	
	// traduction
	if (!isset($translator)){
		$translator =& TslManager::getInstance(); 
	}
	 
	if ( $url ) {
	
		$classeToFeed =  $oClasse->get_classe();
		$oClasseToFeed = getObjectById ("classe", $classeToFeed);
		$sClasseToFeed = $oClasseToFeed->get_nom();
	 
		
		$rss = fetch_rss( $url );
		
		//pre_dump($rss);
		echo "******** ". $oClasseToFeed->get_nom().'<br />';
		echo  'URL : '.$url.'<br />';
		if ( $oClasseToFeed->get_nom() !="") {
			// scan XML pour créer un nouvel objet
			eval("$"."oRes = new ".$sClasseToFeed."();");
			global $stack;
			if(!is_null($oRes->XML_inherited))
				$sXML = $oRes->XML_inherited;
			else
				$sXML = $oRes->XML;
			xmlStringParse($sXML);
			 
			 if(isset($stack[0]['attrs']['NAME'])){	 
				//backup le stack	
				$stackBack = $stack;
				
				$sPathSurcharge = $_SERVER['DOCUMENT_ROOT'].'/include/bo/cms/'.$stack[0]['attrs']['NAME'].'.class.xml';
				
				if(is_file($sPathSurcharge)) xmlFileParse($sPathSurcharge);
			}
			
			$classeLibelle = "";
			if (isset($stack[0]["attrs"]["LIBELLE"])) {
				$classeLibelle =  $stack[0]["attrs"]["LIBELLE"];
			}
			$classePrefixe = $stack[0]["attrs"]["PREFIX"];
			$aNodeToSort = $stack[0]["children"];
			 
			$eNewEntrie=0;  
			foreach ($rss->items as $item) {
				 
				 
				$href = $item['link'];
				$title = $item['title'];
					
				//echo "<br><br>".$title."<br>";
				
				eval("$"."oObjet = new ".$sClasseToFeed."();");
				 
				for ($i=0;$i<count($aNodeToSort);$i++){ 
					if ($aNodeToSort[$i]["name"] == "ITEM"){ 
						$eKeyValue = "";
						//echo "********".$aNodeToSort[$i]["attrs"]["NAME"];
						if ($aNodeToSort[$i]["attrs"]["NAME"] == "statut"){ 
							$eKeyValue = DEF_CODE_STATUT_DEFAUT; 
							eval("$"."oObjet->set_".$aNodeToSort[$i]["attrs"]["NAME"]."($"."eKeyValue);");
						}
						if ($aNodeToSort[$i]["attrs"]["NAME"] == "rsstype"){ 
							$eKeyValue = DEF_CODE_STATUT_DEFAUT;  
							//echo "---------------".$oClasse->get_rsstype();
							eval("$"."oObjet->set_".$aNodeToSort[$i]["attrs"]["NAME"]."(".$oClasse->get_rsstype().");");
						}
						if (($aNodeToSort[$i]["attrs"]["RSS"] != "") && (isset($aNodeToSort[$i]["attrs"]["RSS"]))){ 
							 
							if (strtolower($aNodeToSort[$i]["attrs"]["RSS"])=="pubdate") {  
								if (isset($item[strtolower($aNodeToSort[$i]["attrs"]["RSS"])]) && $item[strtolower($aNodeToSort[$i]["attrs"]["RSS"])]!="" ) {
									$eKeyValue = $item[strtolower($aNodeToSort[$i]["attrs"]["RSS"])]; 
									$date_to_compare = strtotime ($eKeyValue);
									$eKeyValue = date("d/m/Y H:i:s", $date_to_compare);  
									 echo "ici".$eKeyValue;
									
								}
								else if ( isset($item["dc"][$aNodeToSort[$i]["attrs"]["RSS"]]) && $item["dc"][$aNodeToSort[$i]["attrs"]["RSS"]]!="") { 
									$eKeyValue = $item["dc"][$aNodeToSort[$i]["attrs"]["RSS"]]; 
									$date_to_compare = strtotime ($eKeyValue); 
								}
								else if ( isset($item["date"]) && $item["date"]!="") { 
									$eKeyValue = $item["date"];  
									if (preg_match('/^[0-9]{4}[-\/][0-9]{2}[-\/][0-9]{2} [a-z0-9A-Z:]*$/',  $eKeyValue)) {
										preg_match_all('/^([0-9]{4})[-\/]([0-9]{2})[-\/]([0-9]{2}) ([a-z0-9A-Z:]*)$/',  $eKeyValue, $aRes);
										$eKeyValue = $aRes[3][0]."/".$aRes[2][0]."/".$aRes[1][0];  
										$date_to_compare = mktime(0,0,0,$aRes[2][0],$aRes[3][0],$aRes[1][0]); 
									}
									else if (preg_match('/^[0-9]{2}[-\/][0-9]{2}[-\/][0-9]{4} [a-z0-9A-Z:]*$/',  $eKeyValue)) {
										preg_match_all('/^([0-9]{2})[-\/]([0-9]{2})[-\/]([0-9]{4}) ([a-z0-9A-Z:]*)$/',  $eKeyValue, $aRes);
										$eKeyValue = $aRes[1][0]."/".$aRes[2][0]."/".$aRes[3][0]; 
										$date_to_compare = mktime(0,0,0,$aRes[2][0],$aRes[1][0],$aRes[3][0]); 
									} 
									$eKeyValue = date("d/m/Y", $date_to_compare);   
								}
								else {   
									//2009-10-02T18:32:38Z
									 
									$eKeyValue = $item["dc"]["date"]; 
									//(19|20)[0-9]{2}[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])
									if (preg_match('/^[0-9]{4}[-\/][0-9]{2}[-\/][0-9]{2}[a-z0-9A-Z:]*$/',  $eKeyValue)) {
										preg_match_all('/^([0-9]{4})[-\/]([0-9]{2})[-\/]([0-9]{2})([a-z0-9A-Z:]*)$/',  $eKeyValue, $aRes);
										$eKeyValue = $aRes[3][0]."/".$aRes[2][0]."/".$aRes[1][0]; 
										$date_to_compare = mktime(0,0,0,$aRes[2][0],$aRes[3][0],$aRes[1][0]);
									}
									else if (preg_match('/^[0-9]{2}[-\/][0-9]{2}[-\/][0-9]{4}[a-z0-9A-Z:]*$/',  $eKeyValue)) {
										preg_match_all('/^([0-9]{2})[-\/]([0-9]{2})[-\/]([0-9]{4})([a-z0-9A-Z:]*)$/',  $eKeyValue, $aRes);
										$eKeyValue = $aRes[1][0]."/".$aRes[2][0]."/".$aRes[3][0]; 
										$date_to_compare = mktime(0,0,0,$aRes[2][0],$aRes[1][0],$aRes[3][0]); 
									} 
								} 
								$eKeyValueDate =  $eKeyValue;
								$date_field = $aNodeToSort[$i]["attrs"]["NAME"];
							} 
							else if (isset($item[strtolower($aNodeToSort[$i]["attrs"]["RSS"])]) && $item[strtolower($aNodeToSort[$i]["attrs"]["RSS"])]!="" ) {
								$eKeyValue = $item[strtolower($aNodeToSort[$i]["attrs"]["RSS"])];   
								
							}
							else if (strtolower($aNodeToSort[$i]["attrs"]["RSS"])=="pubenddate") { 
								//$eKeyValue = date("d/m/Y", $date_to_compare); 
								$eKeyValue = date("Y-m-d", $date_to_compare); 
								$date = new DateTime($eKeyValue);
								$date->add(new DateInterval('P1Y'));
								$eKeyValue = $date->format('Y-m-d'); // on ajoute un an à la date de début 
								//echo "enddate : " .$eKeyValue." ".$date_to_compare."<br />";
							} 
							 
							if (preg_match ("/<[^<>]+>/", $eKeyValue)) {
								//echo "1".$aNodeToSort[$i]["attrs"]["RSS"]." : ".($eKeyValue)."<br /><br />";
								$eKeyValue = html2text($eKeyValue); 
							}
							else {
								//echo "2".$aNodeToSort[$i]["attrs"]["RSS"]." : ".($eKeyValue)."<br /><br />";
								$eKeyValue = htmlentities($eKeyValue, ENT_QUOTES, "UTF-8"); 
							}
							
							
							//
							
							if (isset ($aNodeToSort[$i]["attrs"]["NOHTML"]) && $aNodeToSort[$i]["attrs"]["NOHTML"] == "false") {
								$eKeyValue = str_replace ('&amp;', '&', $eKeyValue); 
							}
							else {
								//$eKeyValue = htmlentities($eKeyValue, ENT_QUOTES, "UTF-8");
							} 
							
							$eKeyValue = str_replace('\"', '',	$eKeyValue); 
							$eKeyValue = str_replace("'", "\'",	$eKeyValue);  
							$eKeyValue = preg_replace("/\r\n/msi", "",	$eKeyValue);
							//echo $eKeyValue."<br /><br />";
							
							
							if ($aNodeToSort[$i]["attrs"]["TYPE"] == "int"){ // cas des int, ne pas inscrire de value vide dans la base
								if (isset($aNodeToSort[$i]["attrs"]["TRANSLATE"])  &&  $aNodeToSort[$i]["attrs"]["TRANSLATE"] == "reference"){
									
									$eKeyValue = $translator->addTranslation ($eKeyValue, array(DEF_APP_LANGUE => $eKeyValue)); 
									eval("$"."oObjet->set_".$aNodeToSort[$i]["attrs"]["NAME"]."($"."eKeyValue);");

								}
								else {
									if ($eKeyValue == ""){
										$eKeyValue = -1;
									}

								}
							} 
							elseif ($aNodeToSort[$i]["attrs"]["OPTION"] == "url"){ // cas url, on ajoute le protocole http:// si manque
								if (isset($eKeyValue)){
									$tempUrl = trim($eKeyValue);
									if (!preg_match("/^http|ftp|https]:\/\/.*/msi", $tempUrl) && ($tempUrl != "")){
										$tempUrl = "http://".$tempUrl;
									}					 
									eval("$"."oObjet->set_".$aNodeToSort[$i]["attrs"]["NAME"]."($"."tempUrl);");
								}
							}
							else { 
								eval("$"."oObjet->set_".$aNodeToSort[$i]["attrs"]["NAME"]."($"."eKeyValue);");
							} 
							
							/*echo "champs RSS : ".$aNodeToSort[$i]["attrs"]["NAME"]."<br>"; 
							echo "cible RSS &nbsp; &nbsp; : ".$aNodeToSort[$i]["attrs"]["RSS"]."<br>";
							echo "value RSS&nbsp; &nbsp; : ".$item[strtolower($aNodeToSort[$i]["attrs"]["RSS"])]."<br>";
							echo "eKeyValue&nbsp; &nbsp; : ".$eKeyValue. " ".$eKeyValueDate."<br>";*/
						}
						
						 
					}  //if ($aNodeToSort[$i]["name"] == "ITEM"){
				 
				} // for ($i=0;$i<count($aNodeToSort);$i++){ 
				
				
				// cas où on souhaite mettre à jour d'autres valeurs 
				if ($oClasse->get_set()!='' &&  $oClasse->get_value()!= '' ) {
					$set = $oClasse->get_set();
					$value =$oClasse->get_value();
					eval("$"."oObjet->set_".$set."(".$value.");");
				}
				 
				
				//$today =  date("d/m/Y H:i:s", mktime  ( date("H"),  date("i"),  date("s"),  date("m"),  date("j"),  date("Y")  ));  
				//echo ."<br />"; 
				if ($date_field != '') {
					if ($oClasse->get_set()!='' &&  $oClasse->get_value()!= '' ) {
						$sql = "select MAX(".$classePrefixe."_".$date_field.") from ".$sClasseToFeed." where ".$classePrefixe."_".$set." = ".$value." order by ".$classePrefixe."_".$date_field." DESC ";
					}
					else {
						$sql = "select MAX(".$classePrefixe."_".$date_field.") from ".$sClasseToFeed." order by ".$classePrefixe."_".$date_field." DESC ";
					}
					//echo "---".$sql."<br />";
					
					$last_date = dbGetUniqueValueFromRequete($sql);
					// echo "---".$last_date."<br />";
					 $aD = split (" ", $last_date);
					 $aJ = split ("-", $aD[0]);
					 $aH = split (":", $aD[1]);
					 
					// echo $aJ[2]." ".$aJ[1]." ".$aJ[0]." ". $aH[0]." ". $aH[1]." ". $aH[2]." <br />";
					$today =  date("d/m/Y H:i:s", mktime  ( $aH[0],  $aH[1],  $aH[2],  $aJ[1],  $aJ[2],  $aJ[0]  ));  
					$mktime_today =  mktime  ( $aH[0],  $aH[1],  $aH[2],  $aJ[1],  $aJ[2],  $aJ[0]  );
					
					
				}
				else {
					if (date("H") < 12) {
						$today =  date("d/m/Y H:i:s", mktime  ( 0,  0,  0,  date("m"),  date("j"),  date("Y")  ));   
						$mktime_today = mktime  ( 0,  0,  0,  date("m"),  date("j"),  date("Y")  );
					}
					else {
					
						$today =  date("d/m/Y H:i:s", mktime  ( 12,  00,  0,  date("m"),  date("j"),  date("Y")  )); 
						$mktime_today = mktime  ( 12,  0,  0,  date("m"),  date("j"),  date("Y")  );
					}
				}
				
				//pre_dump($oObjet);
					
					 
				if (!$isnew) {  
					//echo "pas new<br >";
					//echo "date_to_compare".$date_to_compare." ".date("d/m/Y H:i:s", $date_to_compare)."<br >";
					//echo "today".$today."<br >";
					//echo "mktime_today".$mktime_today."<br >";
					//echo "eKeyValueDate".$eKeyValueDate."<br >";
					if ($date_to_compare > $mktime_today) { 
						//echo "add ------------------------------<br />"; 
						echo date("H")." ".$eKeyValueDate." ".$today."<br />";
						echo $eKeyValueDate;
						$bRetour = dbInsertWithAutoKey($oObjet); 
						if (!$bRetour) send_mail_error_to_admin ("thao@couleur-citron.com", "thao@couleur-citron.com", $classeName, $id, $item);
						if ($bRetour ) $eNewEntrie++; 
						 
					}
				}
				else {  
					//echo "new<br >";
					$bRetour = dbInsertWithAutoKey($oObjet); 
					if (!$bRetour) send_mail_error_to_admin ("thao@couleur-citron.com", "thao@couleur-citron.com", $classeName, $id, $item);
					if ($bRetour ) $eNewEntrie++; 
				} 
				
			}
			send_mail_success_to_admin ("thao@couleur-citron.com", "thao@couleur-citron.com", $classeName, $id, $item, $eNewEntrie);
			return $eNewEntrie;  
		}
	}
	echo "-------------------------------------------------------------------------------<br>";
}


function send_mail_error_to_admin ($from, $to, $classeName, $id, $items) {
	
	eval("$"."oClasse = new ".$classeName."($"."id);");
	//echo $classeName;
	$url = $oClasse->get_url(); 
	
	$headers ='From: '.$from."\n";
	$headers .='Reply-To: '.$to."\n";
	$headers .='Content-Type: text/html; charset="iso-8859-1"'."\n";
	$headers .='Content-Transfer-Encoding: 8bit';

	$message  = "<html><head><title></title></head><body>";
	$message  .= "<br />Site : <b>".$_SERVER["DOCUMENT_ROOT"]."</b>";
	$message  .= "<br />Id flux : <b>".$id."</b>";
	$message  .= "<br />Url flux :<b> ".$url."</b>";
	$message  .= "<br />Date :<b> ".date("r")."</b>"; 
	$message  .= "<br />Objet :"; 
	foreach ($items as $key => $itemValue) {
		$message  .= "<br />$key :<b> ".$itemValue."</b>"; 
	} 
	$message  .= "</body></html>"; 
	mail($to, '[cms] problème import flux rss', $message, $headers);  
}


function send_mail_success_to_admin ($from, $to, $classeName, $id, $items, $eNewEntrie) {
	
	eval("$"."oClasse = new ".$classeName."($"."id);");
	//echo $classeName;
	$url = $oClasse->get_url(); 
	
	$headers ='From: '.$from."\n";
	$headers .='Reply-To: '.$to."\n";
	$headers .='Content-Type: text/html; charset="iso-8859-1"'."\n";
	$headers .='Content-Transfer-Encoding: 8bit';

	$message  = "<html><head><title></title></head><body>";
	$message  .= "<br />Site : <b>".$_SERVER["DOCUMENT_ROOT"]."</b>";
	$message  .= "<br />Id flux : <b>".$id."</b>";
	$message  .= "<br />Url flux :<b> ".$url."</b>";
	$message  .= "<br />Date :<b> ".date("r")."</b>"; 
	$message  .= "<br />Nombre d'imports :<b> ".$eNewEntrie."</b>"; 
	$message  .= "<br />Objet :"; 
	foreach ($items as $key => $itemValue) {
		$message  .= "<br />$key :<b> ".$itemValue."</b>"; 
	} 
	$message  .= "</body></html>"; 
	//mail($to, '[cms] import flux rss', $message, $headers);  
}

?>