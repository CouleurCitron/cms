<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/lib.inc.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/htmlUtility.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/utils/dir.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/mail_lib.php');

if (!isset($idSite)){ 
	if ((isset($_REQUEST['refer'])) and ($_REQUEST['refer'] != "")){
		$referUrl = $_REQUEST['refer'];
	}
	else{
		$referUrl = $_SERVER['PHP_SELF']; 
	}
	
	$idSite = path2idside($db, $referUrl); 
} 



// traduction
if (!isset($translator)){
	$translator =& TslManager::getInstance(); 
}	

/**
* Récupération de l'id
*
* @author Dominique Vial
* @return int id de l'enregistrement dans la table cms_rss
*/
function recuperer_id() {
	 
	if (is_get("id")) {
		$id=$_GET['id'];
	}
	if (is_post("id")) {
		$id=$_POST['id'];
	}
 
	return $id;
}

/**
* Récupération des informations du flux RSS associé à cette classe
* @author Dominique Vial
*
* @return objet cms_rss
*/
function get_RSS_data ( $eCmsRSSId ) {
	 
	$infosRSS = dbGetObjectFromPK('cms_rss', $eCmsRSSId);
	
 
	return $infosRSS;
}

/**
* Trier
*
* Récupération du programme de tri de rss.php
**/
function trier_rechercher ($oRes, $oRss) {
	 
	
	if (!isset($idSite)){ 
		if ((isset($_REQUEST['refer'])) and ($_REQUEST['refer'] != "")){
			$referUrl = $_REQUEST['refer'];
		}
		else{
			$referUrl = $_SERVER['PHP_SELF']; 
		}
		
		$idSite = path2idside($db, $referUrl);
	} //------- 
	
	
	global $stack;
	global $db;
	
	
	$resultat_tri = array();
	
	$sXML = $oRes->XML;
	
	xmlStringParse($sXML);
	
	$classeName = $stack[0]["attrs"]["NAME"]; 
 
	$classeLibelle = "";
	if (isset($stack[0]["attrs"]["LIBELLE"])) {
		$classeLibelle =  $stack[0]["attrs"]["LIBELLE"];
	}
	$classePrefixe = $stack[0]["attrs"]["PREFIX"];
	$aNodeToSort = $stack[0]["children"];
	
	$resultat_tri['classeName'] = $classeName;
	$resultat_tri['classePrefixe'] = $classePrefixe;
	$resultat_tri['aNodeToSort']   = $aNodeToSort;
	 /*
	// si on change de page, on reset
	if (strpos($_SERVER['HTTP_REFERER'], $_SERVER['PHP_SELF']) === false) {
		unset($aGetterOrderBy);
		unset($_SESSION['champTri_res']);
		unset($aGetterSensOrderBy);
		unset($_SESSION['sensTri_res']);
		$_SESSION['adodb_curr_page'] = "1";
	}

	$champTri = $_POST['champTri'];
	if ($champTri == "") {
		$champTri = $_GET['champTri'];
	}
	$buggy->dump($champTri,'champTri');
	
	$sensTri = $_POST['sensTri'];
	if ($sensTri == "") {
		$sensTri = $_GET['sensTri'];
	}	
	$buggy->dump($sensTri,'sensTri');

	/////////////////////////
	// SESSION //////////////
	if ($_POST['champTri'] != "") {
		$_SESSION['champTri_res'] = $_POST['champTri'];
	}
	if ($_POST['sensTri'] != "") {
		$_SESSION['sensTri_res'] = $_POST['sensTri'];
	}
*/
	//////////////////////////
	// TRIS

	// le tri utilisateur est fait en premier
	// les autres tris sont faits même si c non visible dans l'interface
	// l'odre des tris est défini ici

	// le premier tri est ôté de la liste pour être placé en premier par la suite
	/*for ($i=0;$i<count($aNodeToSort);$i++){
		if ($aNodeToSort[$i]["name"] == "ITEM"){
			if ($aNodeToSort[$i]["attrs"]["NAME"] == "ordre"){
				 $aListeTri[] = new dbTri($classePrefixe."_".$aNodeToSort[$i]["attrs"]["NAME"], "ASC");		
			}	
		 
			if ($aNodeToSort[$i]["attrs"]["ORDER"] == "true"){
				if ($aNodeToSort[$i]["attrs"]["TYPE"] == "date"){
					$sAscDesc = "DESC";
				}
				else{
					$sAscDesc = "ASC";
				}
				if ($_SESSION['champTri_res'] != $classePrefixe."_ref") $aListeTri[] = new dbTri($classePrefixe."_".$aNodeToSort[$i]["attrs"]["NAME"], $sAscDesc);	
				
			}
			 
		}
	}*/

	// tri numéro 1 => celui demandé dans l'interface
	/*if (strpos($_SERVER['HTTP_REFERER'], $_SERVER['PHP_SELF']) !== false) {

		//if ($_SESSION['champTri_res'] != "") $aGetterOrderBy[] = $_SESSION['champTri_res'];
		//if ($_SESSION['sensTri_res']  != "") $aGetterSensOrderBy[] = $_SESSION['sensTri_res'];
	}
	else{
		// on récupere rien
	}*/

	// autres tris
	/*for ($i=0; $i < sizeof($aListeTri); $i++){

		$oTri = $aListeTri[$i];

		$aGetterOrderBy[] = $oTri->getNom();
		$aGetterSensOrderBy[] = $oTri->getSens();
	}

	// check des doublons dans les tris
	for ($iOrder = 0;$iOrder < count($aGetterOrderBy);$iOrder++){
		//pre_dump(array_slice($aGetterOrderBy,$iOrder+1));
		$key = array_search($aGetterOrderBy[$iOrder], array_slice($aGetterOrderBy,$iOrder+1));	
		if ($key !== false){
			//echo "search : ".$aGetterOrderBy[$iOrder]."<br>found : ";
			//pre_dump($key);
			//echo "on splice";	
			array_splice ($aGetterOrderBy, $key+1, 1);
			array_splice ($aGetterSensOrderBy, $key+1, 1);
		}
	}*/
	//////////////////////////	
	$aRecherche = array();
	$aGetterOrderBy = array();
	$aGetterSensOrderBy = array();
	$sql = "SELECT distinct ".$classeName.".* ";
	
	
	/*$sTexte = $_SESSION['S_BO_sTexte_ref'];
	$eStatut = $_SESSION['S_BO_select3_ref'];
	$eType = $_SESSION['S_BO_select2_ref'];
	$eHomepage = $_SESSION['S_BO_select_ref'];
	$aRecherche = array();

	$oRech = new dbRecherche();*/

	/*//////////////////////////
	// recherche par mot clé
	//////////////////////////
	if($sTexte==""){
		$sTexte=trim($_POST['sTexte']);
		$_SESSION['sTexte']=$sTexte;
	}
	if($sTexte==""){
		$sTexte=trim($_SESSION['sTexte']);
	}
	 
	if ($sTexte != "") {
		$_SESSION['sTexte']=$sTexte;
		$oRech = new dbRecherche();
		
		$oRech->setValeurRecherche("declencher_recherche");
		$oRech->setTableBD($classeName);

		//on compte le nombre de varchar dans la classe
		$cptvarchar=0;
		for ($i=0;$i<count($aNodeToSort);$i++){
			if(($aNodeToSort[$i]["attrs"]["TYPE"] == "varchar") || ($aNodeToSort[$i]["attrs"]["TYPE"] == "text")){
			$cptvarchar++;
			}
		}

		//construction de la requete dynamique
		$cpt=0;
		for ($i=0;$i<count($aNodeToSort);$i++){
			if (($aNodeToSort[$i]["attrs"]["TYPE"] == "varchar") || ($aNodeToSort[$i]["attrs"]["TYPE"] == "text")){
				$cpt++;
				if($cpt==1){
					$sRechercheTexte="(";
				}
				if($cptvarchar!=$cpt){								
					$sRechercheTexte .= ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]." like'%".$sTexte."%' OR ";
				}
				else{	
					$sRechercheTexte .= ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]." like'%".$sTexte."%' )";
					//$sRechercheTexte .= ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]." like'%".$sTexte."%'";
				}
			}//fin if ($aNodeToSort[$i]["attrs"]["TYPE"] == "varchar") || ($aNodeToSort[$i]["attrs"]["TYPE"] == "text")
		}// fin for ($i=0;$i<count($aNodeToSort);$i++)	
		
		$oRech->setJointureBD($sRechercheTexte);
		$oRech->setPureJointure(1);
		$aRecherche[] = $oRech;
		
	}//fin if ($sTexte != "")
 */
	//////////////////////////
	// recherche par statut // only EN LIGNE
	//////////////////////////
	/*if ($oRes->getGetterStatut() != "none"){ // si l'objet a un champs statut
		$oRech2 = new dbRecherche();
		$oRech2->setValeurRecherche("declencher_recherche");
		$oRech2->setTableBD($classeName);
		$oRech2->setJointureBD(" ".ucfirst($classePrefixe)."_statut=".DEF_ID_STATUT_LIGNE." ");
		$oRech2->setPureJointure(1);
		
		$aRecherche[] = $oRech2;				
	}
	else{ // sinon, on s'en fout
		//
	}*/

	/*$sParam = ""; 
	if ($_SERVER["QUERY_STRING"]!="" && ereg("param", $_SERVER["QUERY_STRING"])) {
		$aParam = split('&', $_SERVER["QUERY_STRING"]);
		for ($i = 0; $i<sizeof($aParam) ; $i++) {
			if (!ereg("adodb", $aParam[$i]))
				$sParam.="&".$aParam[$i]; 
		}	 
	}*/
	 
	$sWhere = $oRss->get_param_where();
	$sValue = $oRss->get_value_where();	
	
	if ($sWhere!="" || $sValue!="") {
		if (!ereg("node", $sWhere)) {
			$oRech = new dbRecherche();
			$oRech->setValeurRecherche("declencher_recherche");
			$oRech->setTableBD($classeName);
			$oRech->setJointureBD(" ".$sWhere." = ".$sValue." ");
			$oRech->setPureJointure(1);
			
			$aRecherche[] = $oRech;	
		}
		else {
			if (is_as_get("currPath")){
				$aWhere = split("and", $sWhere);
				$sClasseBD = ""; 
				for ($i = 0; $i<sizeof($aWhere); $i++) {
					$condition = trim($aWhere[$i]); 
					$aWhereEgale = split("=", $condition);
					
					for ($j = 0; $j<sizeof($aWhereEgale); $j++) {
						$sWhereEgale = $aWhereEgale[$j]; 
						if (ereg("\.", $sWhereEgale)) { 
							$aClasseCondition = split("\.", $sWhereEgale); 
							 
							if (!ereg($aClasseCondition[0], $sClasseBD) && $aClasseCondition[0]!= $classeName) $sClasseBD.= $aClasseCondition[0].", "; 
						 
							 
							
						}
					}
					//$condition = 
				}
				
				$sClasseBD = substr(trim($sClasseBD), 0, strlen(trim($sClasseBD))-1);
				
				$currPath = $_GET["currPath"]; 
				$idSite = path2idside($db, $currPath);
				$oNode = getNodeInfosReverse($idSite, $db, $currPath);   
				$sValue = $oNode["id"];
				$oRech = new dbRecherche();
				$oRech->setValeurRecherche("declencher_recherche");
				$oRech->setTableBD($sClasseBD);
				$oRech->setJointureBD(" ".$sWhere." = ".$sValue." ");
				$oRech->setPureJointure(1);
				 
				$aRecherche[] = $oRech;	
			}
		}
	}
	
	
	//tables jointes 
	
	
	$sOrder = $oRss->get_order_by();	
	
	if ($sOrder!="") {
		$aOrder = array();
		if (strpos($sOrder, ",")!="") {
			$aOrder = split ($sOrder, ",");
		}
		else {
			array_push($aOrder, $sOrder);
		} 
		
		foreach($aOrder as $key=>$oOrder) {
			if (strpos($oOrder, "DESC")) {
				$sOrder = trim(str_replace("DESC", "", $oOrder));
				array_push($aGetterOrderBy, $sOrder);
				array_push($aGetterSensOrderBy, "DESC");
			}
			else {
				$sOrder = trim(str_replace("ASC", "", $oOrder));
				array_push($aGetterOrderBy, $sOrder);
				array_push($aGetterSensOrderBy, "ASC");
			}
		} 
	}
		 
	$sql.= dbMakeRequeteWithCriteres2($classeName, $aRecherche, $aGetterOrderBy, $aGetterSensOrderBy); 
	//echo $sql."<br>";
	// LIMIT ---
	$sLimit = $oRss->get_num_item();
	
	if ($sLimit != ""){
		if (eregi('limit [0-9]+, [0-9]', $sLimit)){
			$sql.= ' '.$sLimit; 
		
		}
		elseif (eregi('[0-9]+', $sLimit)){
			$sql.= ' LIMIT 0, '.$sLimit; 
		}	
	}
 
	//$sql.= dbMakeRequeteWithCriteres2($classeName, $aRecherche, $aGetterOrderBy, $aGetterSensOrderBy); 
	 
	$aResults =  dbGetObjectsFromRequete($classeName, $sql); 
	 
/*	unset($aRecherche);
	unset($aGetterOrderBy);
	unset($aGetterSensOrderBy);*/

	/*execution de la requette avec pagination

	$sParam = "";
	
	
	$pager = new Pagination($db, $sql, $sParam);
	$pager->Render($rows_per_page=20000000);

	// tableau d'id renvoyé par la fonction de pagination
	$aId = $pager->aResult;

	// A VOIR sponthus
	// la fonction de pagination devrait renvoyer un tableau d'objet
	// pour l'instant je n'exploite qu'un tableau d'id
	// ce ui m'oblige à re sélectionner mes objets
	// à perfectionner
*/
	// liste des objets
	
	/*	$aListe_res   = $aResultatTri['aListe_res'];
	$classePrefix = $aResultatTri['classePrefixe'];
	$aNodeToSort  = $aResultatTri['aNodeToSort'];
	$classeName   = $aResultatTri['classeName'];
	
	*/
	$aListe_res = array();
	for ($m=0; $m<sizeof($aResults); $m++)
	{
		$oResult = $aResults[$m];
		//eval("$"."aListe_res[] = new ".$classeName."($"."oResult);");
		$aListe_res[] = $oResult;
	}	 
	$resultat_tri['aListe_res'] = $aListe_res;
	//sizeof($resultat_tri);
	//pre_dump($resultat_tri);
	return $resultat_tri;
}


/**
* creer_liste_items
* Récupération du programme de création de la liste des items de rss.php
*
* @return string liste des items
**/
function creer_liste_items ($aResultatTri, $oRss) {
	
	global $translator;
	  
	$aListe_res   = $aResultatTri['aListe_res'];
	$classePrefix = $aResultatTri['classePrefixe'];
	$aNodeToSort  = $aResultatTri['aNodeToSort'];
	$classeName   = $aResultatTri['classeName'];
	
	$liste_des_items = '';
	
	if (is_as_get("currPath") ) {
	$idSite = path2idside($db, $_GET["currPath"]);
	}
	else {
		if ((isset($_REQUEST['refer'])) and ($_REQUEST['refer'] != "")){
			$referUrl = $_REQUEST['refer'];
		}
		else{
			$referUrl = $_SERVER['PHP_SELF']; 
		}
		
		$idSite = path2idside($db, $referUrl);
	}
		 
  
	$oS = new Cms_site($idSite); 
	
	//$URL_MEDIA;
	// DEF_RSS_HTML_TOP 
	
	for($k=0; $k<sizeof($aListe_res); $k++) {	
	 
		
		
	
	 
	$oRes = $aListe_res[$k];	
		$RSS = array();
		$RSS['title'] = "";
		$RSS['pubDate'] = "";
		$RSS['pubendDate'] = "";
		$RSS['description'] = "";
		$RSS['link'] = ""; 
		$RSS['type'] = "";
		$RSS['texte'] = "";
		$RSS['site'] = "";
		$RSS['enclosure'] = ""; 
		$RSS['image'] = ""; 
		$RSS['frenchdate'] = ""; 
		$descritionHTML = false; 
	 	
		
		 
		for ($i=0;$i<count($aNodeToSort);$i++){
			
			if ($aNodeToSort[$i]["name"] == "ITEM"){
				
				if ($aNodeToSort[$i]["attrs"]["NAME"] == "id"){ // cas statut 
					$eKeyValue = getItemValue($oRes, $aNodeToSort[$i]["attrs"]["NAME"]); 
					$RSS[$aNodeToSort[$i]["attrs"]["NAME"]] = $eKeyValue;
				}	 
				//$buggy->dump($i,"Numéro item");
				//if ($aNodeToSort[$i]["attrs"]["LIST"] == "true"){  
				if (($aNodeToSort[$i]["attrs"]["RSSEN"] != "") && (isset($aNodeToSort[$i]["attrs"]["RSSEN"])) && ($oS->get_langue() == 2)){
					 
					$eKeyValue = getItemValue($oRes, $aNodeToSort[$i]["attrs"]["NAME"]); 
					 
					 
					if ($aNodeToSort[$i]["attrs"]["FKEY"]){ // cas de foregin key
						$sTempClasse = $aNodeToSort[$i]["attrs"]["FKEY"];
						
						if ($eKeyValue > -1){ 
							eval("$"."oTemp = new ".$sTempClasse."(".$eKeyValue.");");
							$RSS[$aNodeToSort[$i]["attrs"]["RSSEN"]] = getItemValue($oTemp, $oTemp->getDisplay());
						}
						else { 
							$RSS[$aNodeToSort[$i]["attrs"]["RSSEN"]] = "";
						}
					}
					elseif ($aNodeToSort[$i]["attrs"]["OPTION"] == "enum"){ // cas enum		
						if (isset($aNodeToSort[$i]["children"]) && (count($aNodeToSort[$i]["children"]) > 0)){
							foreach ($aNodeToSort[$i]["children"] as $childKey => $childNode){
								if($childNode["name"] == "OPTION"){ // on a un node d'option				
									if ($childNode["attrs"]["TYPE"] == "value"){
										if (intval($eKeyValue) == intval($childNode["attrs"]["VALUE"])){							
											$RSS[$aNodeToSort[$i]["attrs"]["RSSEN"]] = $childNode["attrs"]["LIBELLE"];
											break;
										}
									} //fin type  == value				
								}
							}
						}		
					} // fin cas enum
					else{ // cas typique
						if ($aNodeToSort[$i]["attrs"]["NAME"] == "statut"){ // cas statut
							$RSS[$aNodeToSort[$i]["attrs"]["RSSEN"]] = lib($eKeyValue);
						}
						else{
							if ($eKeyValue > -1){ // cas typique typique
								
								if ($aNodeToSort[$i]["attrs"]["OPTION"] == "file"){ // cas file
									//$RSS[$aNodeToSort[$i]["attrs"]["RSSEN"]] = $eKeyValue; 
									$RSS[$aNodeToSort[$i]["attrs"]["RSSEN"]] = $URL_MEDIA.$eKeyValue; 
								}
								else if ($aNodeToSort[$i]["attrs"]["TYPE"] == "timestamp"){ // cas file
									//$RSS[$aNodeToSort[$i]["attrs"]["RSSEN"]] = $eKeyValue;
									$eKeyValue = str_replace ("//", "", $eKeyValue);
									$eKeyValue = strtotime($eKeyValue); 
									$RSS[$aNodeToSort[$i]["attrs"]["RSSEN"]] = date("r", $eKeyValue);
								}
								else{// cas typique typique typique
									// on converti br en \n et on remove les tags 
									//$RSS[$aNodeToSort[$i]["attrs"]["RSSEN"]] = utf8_encode(html_entity_decode(ereg_replace("<[^<>]+>", "", eregi_replace("<br[^<>]*>", "\n", $eKeyValue))));
									$RSS[$aNodeToSort[$i]["attrs"]["RSSEN"]] = html_to_rss($eKeyValue);
									
									if ($aNodeToSort[$i]["attrs"]["RSSEN"] == "description" && isset ($aNodeToSort[$i]["attrs"]["RSSHTML"]) &&  $aNodeToSort[$i]["attrs"]["RSSHTML"] == true) {
	
										$descritionHTML = true;
										$descritionField = $aNodeToSort[$i]["attrs"]["NAME"];
									}
								}
							}
							else{
								$RSS[$aNodeToSort[$i]["attrs"]["RSSEN"]] = "";  
							}
						}
					}	
					
					//}
				}
				else if (($aNodeToSort[$i]["attrs"]["RSS"] != "") && (isset($aNodeToSort[$i]["attrs"]["RSS"]))){
					 
					$eKeyValue = getItemValue($oRes, $aNodeToSort[$i]["attrs"]["NAME"]); 
					 
					 
					if ($aNodeToSort[$i]["attrs"]["FKEY"]){ // cas de foregin key
						$sTempClasse = $aNodeToSort[$i]["attrs"]["FKEY"];
						
						if ($eKeyValue > -1){ 
							eval("$"."oTemp = new ".$sTempClasse."(".$eKeyValue.");");
							$RSS[$aNodeToSort[$i]["attrs"]["RSS"]] = getItemValue($oTemp, $oTemp->getDisplay());
						}
						else { 
							$RSS[$aNodeToSort[$i]["attrs"]["RSS"]] = "";
						}
					}
					elseif ($aNodeToSort[$i]["attrs"]["OPTION"] == "enum"){ // cas enum		
						if (isset($aNodeToSort[$i]["children"]) && (count($aNodeToSort[$i]["children"]) > 0)){
							foreach ($aNodeToSort[$i]["children"] as $childKey => $childNode){
								if($childNode["name"] == "OPTION"){ // on a un node d'option				
									if ($childNode["attrs"]["TYPE"] == "value"){
										if (intval($eKeyValue) == intval($childNode["attrs"]["VALUE"])){							
											$RSS[$aNodeToSort[$i]["attrs"]["RSS"]] = $childNode["attrs"]["LIBELLE"];
											break;
										}
									} //fin type  == value				
								}
							}
						}		
					} // fin cas enum
					else{ // cas typique
				 
						if ($aNodeToSort[$i]["attrs"]["NAME"] == "statut"){ // cas statut
							$RSS[$aNodeToSort[$i]["attrs"]["RSS"]] = lib($eKeyValue);
						} 
						else{
							 
							if ($eKeyValue > -1){ // cas typique typique 
								if ($aNodeToSort[$i]["attrs"]["OPTION"] == "file"){ // cas file
									//$RSS[$aNodeToSort[$i]["attrs"]["RSS"]] = $eKeyValue; 
									$RSS[$aNodeToSort[$i]["attrs"]["RSS"]] = $URL_MEDIA.$eKeyValue; 
								}
								else if ($aNodeToSort[$i]["attrs"]["TYPE"] == "timestamp" || $aNodeToSort[$i]["attrs"]["TYPE"] == "datetime"){ // cas timestamp
									//$RSS[$aNodeToSort[$i]["attrs"]["RSS"]] = $eKeyValue; 
									$eKeyValue = str_replace ("//", "", $eKeyValue); 
									ereg ("([0-9]{2})[-/]{1}([0-9]{2})[-/]{1}([0-9]{4})[- ]{1}([0-9]{2})[-:]{1}([0-9]{2})[-:]{1}([0-9]{2})", $eKeyValue, $regs);  
									//$eKeyValue = strtotime($eKeyValue);  
									//$RSS[$aNodeToSort[$i]["attrs"]["RSS"]] = date("r", mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[1], $regs[3])); 
									$RSS[$aNodeToSort[$i]["attrs"]["RSS"]] = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[1], $regs[3]); 
								 
									
								}
								else if ($aNodeToSort[$i]["attrs"]["TYPE"] == "date"){ // cas date 
									
									$eKeyValue = str_replace ("//", "", $eKeyValue);  
									ereg ("([0-9]{2})[-/]{1}([0-9]{2})[-/]{1}([0-9]{4})", $eKeyValue, $regs);   
									//$RSS[$aNodeToSort[$i]["attrs"]["RSS"]] = date("r", mktime(0, 0, 0, $regs[2], $regs[1], $regs[3]));
									$RSS[$aNodeToSort[$i]["attrs"]["RSS"]] = mktime(0, 0, 0, $regs[2], $regs[1], $regs[3]);
									
								}
								else{// cas typique typique typique
									// on converti br en \n et on remove les tags 
									//$RSS[$aNodeToSort[$i]["attrs"]["RSS"]] = utf8_encode(html_entity_decode(ereg_replace("<[^<>]+>", "", eregi_replace("<br[^<>]*>", "\n", $eKeyValue))));
									if (isset($aNodeToSort[$i]["attrs"]["TRANSLATE"]) && $aNodeToSort[$i]["attrs"]["TRANSLATE"] == "reference") {
										$eKeyValue = $translator->getByID($eKeyValue, $_SESSION["id_langue"]);
									}
									$RSS[$aNodeToSort[$i]["attrs"]["RSS"]] = html_to_rss($eKeyValue);
									
									if ($aNodeToSort[$i]["attrs"]["RSS"] == "description" && isset ($aNodeToSort[$i]["attrs"]["RSSHTML"]) &&  $aNodeToSort[$i]["attrs"]["RSSHTML"] == true) {
	
										$descritionHTML = true;
										$descritionField = $aNodeToSort[$i]["attrs"]["NAME"];
									}
								}
							}
							else{
								$RSS[$aNodeToSort[$i]["attrs"]["RSS"]] = "";  
							}
						}
					}	
					
					//}
				}
			}
				
		}	
		 
		// s'il existe une date de fin de publication, on n'affiche pas l'info
		/*$RSS['pubDate']=ereg_replace("([0-9]{2})/([0-9]{2})/([0-9]{4})", "\\3/\\2/\\1", $RSS['pubDate']); 
		$RSS['pubendDate']=ereg_replace("([0-9]{2})/([0-9]{2})/([0-9]{4})", "\\3/\\2/\\1", $RSS['pubendDate']); */
		if ($RSS['pubendDate'] == "0000/00/00" ) $RSS['pubendDate'] =""; 
		if ($RSS['pubDate'] == "0000/00/00" ) $RSS['pubDate'] ="";
		
		
		
		  		
		/*$RSS['pubDate'] = date("r", strtotime($RSS['pubDate']));
		$RSS['pubendDate'] = date("r", strtotime($RSS['pubendDate']));*/ 
		/*pre_dump($RSS['pubDate']);
		pre_dump($RSS['pubendDate']);
		pre_dump(date("r"));		
		$id = getItemValue($oRes, "id");*/
		$today =  mktime();
		//echo "---".$id.$RSS['pubDate']."              ".$RSS['pubendDate']."              ".$today."<br>\n";
		
		if ($RSS['pubendDate'] !="" &&  $RSS['pubDate'] =="" && $RSS['pubendDate'] < $today  ) {  
			//echo ' pas de date de deb, et date du fin dans le passé  <br>';
		}
		
		else if ($RSS['pubDate'] !="" && $RSS['pubendDate'] =="" && $RSS['pubDate'] > $today) { 
			//echo ' pas de date de fin, et date du deb dans le futur  <br>';
		}
		else if ($RSS['pubDate'] !="" && $RSS['pubendDate'] !="" && ($RSS['pubendDate'] < $today || $RSS['pubDate'] > $today) ) { 
			/*if ($RSS['pubendDate'] < $today)  echo "date auj <pudbenddate <br>";
			else  if ($RSS['pubDate'] > $today)  echo "date pubdate > auj <br>";
			echo ' date de fin, date du deb - soit date du fin dans le passé  | soit date du deb dans le futur  <br>';*/
		}
		else { 
			
			if ($RSS['title'] == ""){
				if ($RSS['description'] != ""){
					$RSS['title'] = $RSS['description'];
				}
				else{
					$RSS['title'] = "Info du ".date("d-m-Y");
				}
			}
			if ($RSS['pubDate'] == ""){
				$RSS['pubDate'] = date("r", strtotime("now"));
			}
			else{
				//$RSS['pubDate'] = date("r", strtotime(ereg_replace("([0-9]{2})/([0-9]{2})/([0-9]{4})", "\\3/\\2/\\1", $RSS['pubDate'])));
				$RSS['pubDate'] = date("r", $RSS['pubDate']);
				 
			}
			if ($RSS['frenchdate'] == ""){
				$RSS['frenchdate'] = date("d-m-Y");
			}
			if ($RSS['description'] == ""){
				if ($RSS['title'] != ""){
					$RSS['description'] = $RSS['title'];
				}
				else{
					$RSS['description'] = "Info du ".date("d-m-Y");
				}
			}
			if ($RSS['link'] == ""){
				$id = getItemValue($oRes, "id");
				if (is_file ($_SERVER['DOCUMENT_ROOT']."/frontoffice/".$classeName."/foshow_".$classeName.".php")) {
					$RSS['link'] = "http://".$_SERVER['HTTP_HOST']."/frontoffice/".$classeName."/foshow_".$classeName.".php?id=".$id;
				} 
				else {
					// cas où une url est défini dans le flux rss
					if ($oRss->get_url_objet() != '') {
						$url = $oRss->get_url_objet(); 
						$aMatches = array();
						preg_match_all ("/\{([A-Z]+)\}/", $url, $aMatches);  
						foreach ($aMatches[1] as $champ) { 
							$url = preg_replace("/\{".$champ."\}/",  utf8_encode(text2url($RSS[strtolower($champ)])) , $url ); 
						}
						$RSS['link'] = "http://".$_SERVER['HTTP_HOST']."".$url;
					}
					else {
						$RSS['link'] = "http://".$_SERVER['HTTP_HOST']."/";
					}
				}
			}
		 	else {
				$RSS['link'] = "http://".$_SERVER['HTTP_HOST']."/".$RSS['link'];
			}
			if ($RSS['type'] == ""){
			}
			if ($RSS['site'] == ""){
				$RSS['site'] = 1;
			}
			if ($RSS['texte'] == ""){
				
				if ($RSS['description'] != "" && $descritionHTML == true){
					
					$id = getItemValue($oRes, "id");
					
					$fileName = "../../frontoffice/".$classeName."/foshow_".$classeName.".rss.php";
					if (file_exists($fileName)) {
						$fileName = "http://".$_SERVER['HTTP_HOST']."/frontoffice/".$classeName."/foshow_".$classeName.".rss.php";
						$fp = fopen($fileName."?id=".$id."","r"); //lecture du fichier
						$htmlLDJ= "";
						while (!feof($fp)) { //on parcourt toutes les lignes
						  $htmlLDJ.= fgets($fp, 4096); // lecture du contenu de la ligne
						}
						//$RSS['description'] = utf8_encode(accent2Html($htmlLDJ));
						$RSS['texte'] = utf8_encode(htmlcodes2chars($htmlLDJ));
						$RSS['description'] = utf8_encode(htmlcodes2chars($htmlLDJ));
						$RSS['link'] = "http://".$_SERVER['HTTP_HOST']."/frontoffice/".$classeName."/foshow_".$classeName.".php?id=".$id."";
						
					}
					else {
						$RSS['texte'] = $RSS['description'];
					}
					
				}	
				else if ($RSS['description'] != ""){
					$RSS['texte'] = $RSS['description'];
				}
			}
			
			//$description = DEF_RSS_HTML_TOP.$RSS['texte'].DEF_RSS_HTML_TOP;
			$description = $RSS['texte']; 
			$liste_des_items .= "			<item>\n";
			//$liste_des_items .= "<guid isPermaLink=\"false\">".ereg_replace("&([^q]{1})", "&amp;\\1", $RSS['link'])."</guid>\n"; 
			//$liste_des_items .= "				<title>".rawurlencode(($RSS['title']))."</title>\n";
			$liste_des_items .= "				<title><![CDATA[".ereg_replace("&([^q]{1})", "&amp;\\1", $RSS['title'])."]]></title>\n";
			$liste_des_items .= "				<pubDate>".$RSS['pubDate']."</pubDate>\n";
			//$liste_des_items .= "				<description>".rawurlencode(($RSS['description']))."</description>\n";
			$liste_des_items .= "				<description><![CDATA[".ereg_replace("&([^q]{1})", "&amp;\\1", $description)."]]></description>\n";
			 
			
			if ($RSS['image'] != '') {
				$RSS['image'] = "http://".$_SERVER['HTTP_HOST']."/custom/upload/".$classeName."/".$RSS['image'];
				 
				$RSS['image'] = controlLinkValue($RSS['image'], $oRes); 
				$aImg = split (";", $RSS['image']);
				$RSS['image'] = $aImg[0]; 
				if ($RSS['image'] != ""){	
					$sEncFullPath = ereg_replace("http://[^/]+", $_SERVER['DOCUMENT_ROOT'], $RSS['image']); 
					if (is_file($sEncFullPath)) {
						$aEncStats = stat($sEncFullPath); 
				
						$liste_des_items .= "				<enclosure url=\"".$RSS['image']."\" length=\"".$aEncStats["size"]."\" type=\"".get_mimeType(get_extension(basename($sEncFullPath)))."\"/>\n";	
					}
				}
			}
				
		//	if ($RSS['enclosure'] != "" && !ereg(".pdf", $RSS['enclosure']) ){	  
				//$RSS['enclosureurl'] = "http://".$_SERVER['HTTP_HOST']."/modules/utils/telecharger.php?chemin=/custom/upload/".$classeName."/&file=".$RSS['enclosure'];
				$RSS['enclosure'] = "http://".$_SERVER['HTTP_HOST']."/custom/upload/".$classeName."/".$RSS['enclosure'];
				$RSS['enclosure'] = controlLinkValue($RSS['enclosure'], $oRes);
				$sEncFullPath = ereg_replace("http://[^/]+", $_SERVER['DOCUMENT_ROOT'], $RSS['enclosure']); 
			
				if (is_file($sEncFullPath)) {
					$aEncStats = stat($sEncFullPath);  
					$liste_des_items .= "				<enclosure url=\"".$RSS['enclosure']."\" length=\"".$aEncStats["size"]."\" type=\"".get_mimeType(get_extension(basename($sEncFullPath)))."\"/>\n";	
				}
			//}
			$liste_des_items .= "				<link>".ereg_replace("&([^q]{1})", "&amp;\\1", $RSS['link'])."</link>\n";
			$liste_des_items .= "				<guid>".ereg_replace("&([^q]{1})", "&amp;\\1", $RSS['link'])."</guid>\n";
			//$liste_des_items .= "				<frenchdate>".$RSS['frenchdate']."</frenchdate>\n"; 
			 
			 
			//$liste_des_items .= "				<frenchenddate>".$RSS['frenchenddate']."</frenchenddate>\n";
			//if ($RSS['type']!="")$liste_des_items .= "				<type>".rawurlencode(($RSS['type']))."</type>\n";
			//if ($RSS['site']!="")$liste_des_items .= "				<site>".rawurlencode(($RSS['site']))."</site>\n";
			//$liste_des_items .= "				<texte>".rawurlencode(($RSS['texte']))."</texte>\n";
			//$liste_des_items .= "				<texte><![CDATA[".ereg_replace("&([^q]{1})", "&amp;\\1", $RSS['texte'])."]]></texte>\n";
			//$liste_des_items .= "				<description><![CDATA[".ereg_replace("&([^q]{1})", "&amp;\\1", $RSS['description'])."]]></description>\n";
			$liste_des_items .= "			</item>\n";				
			}
			
	} // fin 	if ($RSS['pubendDate'] >= date("Y-m-d"))  
	return $liste_des_items;
}

/**
* Génération du contenu
*
* @author Dominique Vial
*
* @param object	$oInfosRss : infos relatives au flux
*
* @return string contenu du flux
*/
function generer_contenu ($oInfosRSS) {
 

	// Récupération de la classe associée au flux
	$oClasseAssociee = dbGetObjectFromPK('classe', $oInfosRSS->get_classe());
	 
	// Récupération du nom de cette classe
	eval("$"."oRes = new ".$oClasseAssociee->get_nom()."();");
	
	// Tri et recherche
	$resultat_tri = trier_rechercher ($oRes, $oInfosRSS);

	if(sizeof($resultat_tri['aListe_res'])>0) {
		$contenu = creer_liste_items ($resultat_tri,  $oInfosRSS);
	}
	 
	return $contenu;
}

/**
* Génération du flux RSS
*
* @author Dominique Vial
*
* @param int	$eId : id de l'enregistrement cms_rss relatif au flux
* @param object	$oInfosRss : infos relatives au flux
* @param string	$sContenu : le contenu du flux
*
* @return string enteteRSS
*/
function generer_flux ( $eId, $oInfosRSS, $sContenu ) {

	 
	
	define ('BASE_URL_IMAGE_TEMPO','/custom/upload/cms_rss/');
	
	$data = array();
	$sLangue = 'fr-FR';
	$iduser = $oInfosRSS->get_web_master();
	if ($iduser == -1) $iduser = 1;
	$iduser_manager = $oInfosRSS->get_managing_editor();
	if ($iduser_manager == -1) $iduser_manager = 1;
	$oUser = new Bo_users($iduser);
	 
	$oUser_manager = new Bo_users($iduser_manager);
	 
	$data['RSS_LINK']				= "http://".$_SERVER['HTTP_HOST'].'/backoffice/cms/utils/feed_cms_rss.php?id='.$eId;
	//$data['RSS_LINK']				= "http://".$_SERVER['HTTP_HOST'].'/backoffice/cms/utils/feed_cms_rss.php';
	$data['RSS_TITLE'] 				= utf8_encode($oInfosRSS->get_title());
	$data['RSS_DESCRIPTION']		= utf8_encode(apos(html_to_rss(($oInfosRSS->get_description()))));
	$data['RSS_GUID']				= utf8_encode($oInfosRSS->get_guid_base_url());
	$data['RSS_LANGUAGE']			= utf8_encode($sLangue);
	$data['RSS_MANAGING_EDITOR']	= $oUser_manager->get_mail()." (".$oUser_manager->get_nom()." ".$oUser_manager->get_prenom().")";
	$data['RSS_WEBMASTER']			= $oUser->get_mail()." (".$oUser->get_nom()." ".$oUser->get_prenom().")";
	$data['RSS_IMAGE_TITLE']		= $oInfosRSS->get_image_title();
	$data['RSS_IMAGE_URL']			= "http://".$_SERVER['HTTP_HOST'].BASE_URL_IMAGE_TEMPO.$oInfosRSS->get_image_url();
	$data['RSS_IMAGE_LINK']			= $data['RSS_LINK'];
	$data['RSS_CONTENU']			= $sContenu ;


$bloc = <<<FIN_BLOC
<?xml version='1.0' encoding='utf-8' ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">  
<channel>
	<atom:link href="{$data['RSS_LINK']}"   type="application/rss+xml" />
	<title>{$data['RSS_TITLE']}</title>
	<description>{$data['RSS_DESCRIPTION']}</description>
	<link>{$data['RSS_GUID']}</link>
	<language>fr-FR</language>
	<managingEditor>{$data['RSS_MANAGING_EDITOR']}</managingEditor>
	<webMaster>{$data['RSS_WEBMASTER']}</webMaster>
	<image> 
		<title>{$data['RSS_IMAGE_TITLE']}</title>
		<url>{$data['RSS_IMAGE_URL']}</url>
		<link>{$data['RSS_IMAGE_LINK']}</link> 
	</image>
	{$data['RSS_CONTENU']}
</channel>
</rss>
FIN_BLOC;

	header("Content-type: application/rss+xml");
	header("Content-Encoding: utf-8");
	
	echo $bloc;

	 

}

// En-tête du flux

// ---------------------------------------------------------------
// Traitement principal
// ---------------------------------------------------------------

// Récupération de l'id de l'enregistrement concerné
$id = recuperer_id();

// Récupération des informations du flux RSS
$infosFluxRSS = get_RSS_data ($id);
$contenu = generer_contenu ( $infosFluxRSS );
generer_flux( $id, $infosFluxRSS , $contenu);

 

?>