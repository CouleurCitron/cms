<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');
//init_set ('error_reporting', E_ALL);

$translator =& TslManager::getInstance();
	
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
		
		// chercher tous les nodes qui ont $oParent // $oX->get_arbo_pages() comme pareent		
		$aKids = getNodeChildren($oParent->get_id_site(), $db, $oParent->get_id());	
		$oO = new $classeName();
		$display = $oO->getDisplay();
		$getter = 'get_'.$display;	
		
		pre_dump($aKids);
		
		// creer les objets de type $oClasse->get_nom();
		foreach($aKids as $k => $aKid){		 
			// chercher les objet au display = $aKid["libelle"]	
			if (($oX->get_iffield()!='')&&($oX->get_ifvalue()!='')){
				$aOs = dbGetObjectsFromFieldValue($classeName, array($getter, 'get_'.$oX->get_iffield()),  array($aKid["libelle"], $oX->get_ifvalue()), NULL);
			}
			else{	// pas de condition
				$aOs = dbGetObjectsFromFieldValue($classeName, array($getter),  array($aKid["libelle"]), NULL);
			}

			if ((count($aOs) > 0)&&($aOs!=false)){
				echo '<strong>found</strong>';
				$oO = $aOs[0];
				
				$aCache = dbGetObjectsFromFieldValue('cms_cacheclassenode', array('get_arbo_pages_parent', 'get_arbo_pages', 'get_classe', 'get_objet'),  array($oX->get_arbo_pages(), $aKid['id'], $classeName, $oO->get_id()), NULL);
					
				if ((count($aCache) == 0)||($aCache==false)){
					$oCache = new cms_cacheclassenode();
					$oCache->set_arbo_pages_parent($oX->get_arbo_pages());
					$oCache->set_arbo_pages($aKid['id']);
					$oCache->set_classe($classeName);
					$oCache->set_objet($oO->get_id());
					dbSauve($oCache);
				}
			}
			else{
				echo ' PAS found';				
				$oO = new $classeName();				
				$setter = 'set_'.$display;							
				
				if (isFieldTranslate($oO, $display)){
					//cas translate
					$oO->$setter($translator->getTextId($aKid['libelle'], $_SESSION['id_langue'], NULL));					
				}
				else{
					//cas pas translate :			
					$oO->$setter($aKid['libelle']);	
				}				
				
				if ((intval($aKid['node_order'])>0)	&&	($oO->getGetterStatut() == 'get_statut')){				
					$oO->set_statut(DEF_LIB_STATUT_LIGNE);
				}
				// si des if
				if(($oX->get_iffield() != '')&&($oX->get_ifvalue() != '')){
					//echo ' if ';
					$ifSetter = 'set_'.$oX->get_iffield();
					$oO->$ifSetter($oX->get_ifvalue());
				}				
				//pre_dump($oO);
				$newIdO = dbSauve($oO);	
				
				$oCache = new cms_cacheclassenode();
				$oCache->set_arbo_pages_parent($oX->get_arbo_pages());
				$oCache->set_arbo_pages($aKid['id']);
				$oCache->set_classe($classeName);
				$oCache->set_objet($newIdO);
				dbSauve($oCache);			
			}			
		}		
		
		include('../cms_cacheclassenode/cache_cms_cacheclassenode.php');
	}
	else{
		echo "no valid id submitted";
	}
}
?>