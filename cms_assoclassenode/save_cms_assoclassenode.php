<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
//include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/lib.inc.php');
//init_set ('error_reporting', E_ALL);
	
// permet de dérouler le menu contextuellement
activateMenu("gestioncms_assoclassenode");

if (is_post("todo") == false){
	if (is_get("display") == true){
		
		$id = $_GET["display"];
		
		$oX = new cms_assoclassenode($id);		
		
		$oClasse = new classe($oX->get_classe());
		$oParent = new cms_arbo_pages($oX->get_arbo_pages());
		
		//pre_dump( $oClasse->get_nom());
		$classeName = $oClasse->get_nom();
		//pre_dump($oParent->get_id());
		
		// chercher tous les nodes qui ont $oParent // $oX->get_arbo_pages() comme parent		
		$aKids = getNodeChildren($oParent->get_id_site(), $db, $oParent->get_id());
		
		$oSiteCurr = new cms_site($oParent->get_id_site());
		
		$oO = new $classeName();
		$display = $oO->getDisplay();	
		$getter = 'get_'.$display;
		
		$sXML = $oO->XML;
		xmlClassParse($sXML);
		
		$classeName = $stack[0]["attrs"]["NAME"];
		if (isset($stack[0]["attrs"]["LIBELLE"]) && ($stack[0]["attrs"]["LIBELLE"] != "")){
			$classeLibelle = stripslashes($stack[0]["attrs"]["LIBELLE"]);
		}
		else{
			$classeLibelle = $classeName;
		}
		
		$classePrefixe = $stack[0]["attrs"]["PREFIX"];
		$aNodeToSort = $stack[0]["children"];

		// chercher tous les objets
		//$aOs = dbGetObjectsFromFieldValue($classeName, array($getter),  array('%'), NULL);
		
		$sRequete = "SELECT * FROM ".$classeName;
		
		if(($oX->get_iffield() != '')&&($oX->get_ifvalue() != '')){
			$sRequete .=  ' WHERE '.$classePrefixe.'_'.$oX->get_iffield().' = '.$oX->get_ifvalue();
		}
	
		$aOs = dbGetObjectsFromRequete($classeName, $sRequete);

		if ((count($aOs) > 0)&&($aOs!=false)){
				
			foreach($aOs as $k => $oO){					
				// on fetch l'item pour savoir s'il est traduit
				$nNode = getItemByName($aNodeToSort, $display);				

				if (isset($nNode['attrs']['TRANSLATE'])){
					//echo 'use trans';
					$_SESSION['id_langue'] = $oSiteCurr->get_langue();				
					$translator =& TslManager::getInstance(); 
					$needle = $translator->getByID($oO->$getter());
				}
				else{
					$needle = $oO->$getter();
				}		
				
				echo '<strong>search on '.$needle.'</strong><br />';		
				
				// chercher les nodes enfants ayant pour libelle $needle
				$isFound=false;
				foreach($aKids as $k => $aKid){	
					//pre_dump("\t".$aKid["libelle"]);
					if ($aKid["libelle"]==$needle){
						$isFound=true;
						$idFound=$aKid["id"];
						break;
					}					
				}
				
				if (!$isFound){	
					$bDoAdd = false;
					
					// tentative par le cache
					$aCache = dbGetObjectsFromFieldValue('cms_cacheclassenode', array('get_arbo_pages_parent', 'get_classe', 'get_objet'),  array($oX->get_arbo_pages(), $classeName, $oO->get_id()), NULL);
					
					if ((count($aCache) != 0)&&($aCache!=false)){
						$oCache = $aCache[0];						
						$oNode = new cms_arbo_pages($oCache->get_arbo_pages());
						echo ' update le node '.$oNode->get_libelle().' - ' .$oNode->get_id().' >> '.$needle.'<br />';	
						$oNode->set_libelle($needle);
						renameNode($oParent->get_id_site(), $db, $oNode->get_id(), $needle);
					}
					else{// vraiment pas trouvé
					
						// si des if
						if(($oX->get_iffield() != '')&&($oX->get_ifvalue() != '')){
							//echo ' if ';
							$ifGetter = 'get_'.$oX->get_iffield();
							if($oO->$ifGetter()==$oX->get_ifvalue()){
								$bDoAdd = true;						
							}
						}
						else{
							$bDoAdd = true;
						}	
					}
					
					if ($bDoAdd){
						echo ' creer le node '.$needle.'<br />';	
						$newNodeid = addNode($oParent->get_id_site(), $db, $oParent->get_id(), $needle); // anuyway
						
						$oCache = new cms_cacheclassenode();
						$oCache->set_arbo_pages_parent($oX->get_arbo_pages());
						$oCache->set_arbo_pages($newNodeid);
						$oCache->set_classe($classeName);
						$oCache->set_objet($oO->get_id());
						dbSauve($oCache);
					}
				}	
				else{ // found control du cache
					$aCache = dbGetObjectsFromFieldValue('cms_cacheclassenode', array('get_arbo_pages', 'get_classe', 'get_objet'),  array($idFound, $classeName, $oO->get_id()), NULL);
					echo 'FOUND test du cache <br />';
					
					echo 'id = '.$oO->get_id().' // node = '.$idFound.' // count = '.count($aCache).'<br />';
										
					if ((count($aCache) == 0)||($aCache==false)){
						echo 'ecriture en cache <br />';
						$oCache = new cms_cacheclassenode();
						$oCache->set_arbo_pages_parent($oX->get_arbo_pages());
						$oCache->set_arbo_pages($idFound);
						$oCache->set_classe($classeName);
						$oCache->set_objet($oO->get_id());
						dbSauve($oCache);
					}
					else{
						echo 'update du cache <br />';
						$oCache = $aCache[0];
						$oCache->set_arbo_pages_parent($oX->get_arbo_pages());
						$oCache->set_arbo_pages($idFound);
						$oCache->set_classe($classeName);
						$oCache->set_objet($oO->get_id());
						dbSauve($oCache);
					}
				
				}			
			}
		}
		
		include('../cms_cacheclassenode/cache_cms_cacheclassenode.php');
	}
	else{
		echo "no valid id submitted";
	}
}
?>