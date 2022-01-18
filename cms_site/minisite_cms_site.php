<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 

/* 
form : 
nom, url

*/

if (!is_post('snom')){
?>
<div class="ariane">
<span class="arbo3">Remplir le formulaire suivant pour créer un nouveau minisite</span>
</div>
<form id="minisite" name="minisite" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
	<fieldset class="arbo" >
    	<label for="snom" class="arbo" >Nom du minisite</label>
		<input id="snom" name="snom" class="arbo" type="text" />
    </fieldset>
    <fieldset class="arbo" >
 	   <label for="surl" class="arbo" >URL du minisite</label>
  	  <input id="surl" name="surl" class="arbo" type="text" />
	 </fieldset>
	<input type="submit" value="Créer"/>
</form>
<?php	
	
}
else{ // process

	//rechercher un site avec la meme surl
	$iCountUrl = getCount_where('cms_site', array('cms_url'), array($_POST['surl']), array('TEXT'));
	//nom
	$iCountNom = getCount_where('cms_site', array('cms_name'), array($_POST['snom']), array('TEXT'));
	$iCoun = $iCountUrl + $iCountNom;
	
	if(($iCount==0)||	(($_POST['surl']=='')&&($iCountNom==0))){ // on autorise les doublons only sur les url = ''
	
		//new cms_site
		$oSite = new Cms_site($idSite);
		
		$oMini = $oSite;
		
		$oMini->set_url($_POST['surl']);
		$oMini->set_name($_POST['snom']);
		$oMini->set_rep(removeForbiddenChars($_POST['snom']));
		$iId = dbSauveAsNew($oMini);
		
		echo '<p class="arbo">Le site <strong>'.$_POST['snom'].'</strong> est correctement créé, vous pouvez désormais <a href="/backoffice/cms/site/arboPage_browse.php?idSite='.$iId.'"><strong>créer des pages</strong></a>.</p>';		
		
		//creer assos sur ls prepend et js en isall = 1		
		$aJS =	dbGetObjectsFromFieldValue3('cms_js', array('get_isall'), array('equals'), array(1)) ;
		foreach($aJS as $k => $oJS){
			$iCount = getCount_where('cms_assojscmssite', array('xjs_cms_js', 'xjs_cms_site'), array($oJS->get_id(), $iId), array('NUMBER', 'NUMBER'));
			if ($iCount==0){
				$oX = new cms_assojscmssite();
				$oX->set_cms_js($oJS->get_id());
				$oX->set_cms_site($iId);
				dbSauve($oX);
			}			
		}
				
		$aPP =	dbGetObjectsFromFieldValue3('cms_prepend', array('get_isall'), array('equals'), array(1)) ;
		foreach($aPP as $k => $oPP){
			$iCount = getCount_where('cms_assoprependcmssite', array('xps_cms_prepend', 'xps_cms_site'), array($oPP->get_id(), $iId), array('NUMBER', 'NUMBER'));
			if ($iCount==0){
				$oX = new cms_assoprependcmssite();
				$oX->set_cms_prepend($oPP->get_id());
				$oX->set_cms_site($iId);
				dbSauve($oX);
			}
		}
				
		//switch to new cms_site
		$oSite = new Cms_site($iId);
		sitePropsToSession($oSite);
	}
	else {
		echo 'un site existe déj&agrave; &agrave; cette adresse ou ce nom.';		
	}
}

include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoappend.php'); 
?>