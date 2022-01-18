<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/gabarit.lib.php');	
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

// site sur lequel on est positionné
$idSite = $_SESSION['idSite_travail'];
if ($idSite == "") die ("Appel incorrect de la fonctionnalité");
$oSite = new Cms_site($idSite);


activateMenu('gestionpage');  //permet de dérouler le menu contextuellement

/////////////////////////
// VALEURS POSTEES //////

$idPage = $_GET['idPage'];
$idGab = $_GET['idGab'];
if ($idPage == "" && $idGab == "") die ("Appel incorrect de la fonctionnalité");

/////////////////////////
// couche XML objet cms_page
$oRes = new cms_page();

if(!is_null($oRes->XML_inherited))
	$sXML = $oRes->XML_inherited;
else
	$sXML = $oRes->XML;
	
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

/////////////////////////
// id gabarit
if ($idGab != "") {
	// infos pages
	$oInfos_pages = new Cms_infos_page();
	$oInfos_pages->getCmsinfospages_with_pageid($idGab);

	$titre = $oInfos_pages->getPage_titre();
	$mots = $oInfos_pages->getPage_motsclefs();
	$description = $oInfos_pages->getPage_description();

	// page
	$oPage = new Cms_page($idGab);
}
/////////////////////////	
// id page
if($idPage != "") {
	$oInfos_pages = new Cms_infos_page();
	$oInfos_pages->getCmsinfospages_with_pageid($idPage);

	$titre = $oInfos_pages->getPage_titre();
	$mots = $oInfos_pages->getPage_motsclefs();
	$description = $oInfos_pages->getPage_description();
	$thumb = $oInfos_pages->getPage_thumb();

	// page
	$oPage = new Cms_page($idPage);  
}
/////////////////////////
// SAUVEGARDE BDD ///////
if ($_POST['operation'] == "SAUVE") {

	$titre = $_POST['pagetitle'];
	$mots = $_POST['pagekeywords'];
	$description = $_POST['description'];
	$thumb = $_POST['pagethumb'];
	
	$oInfos_pages->setPage_titre($titre);
	$oInfos_pages->setPage_motsclefs($mots);
	$oInfos_pages->setPage_description($description);
	$oInfos_pages->setPage_thumb($thumb);	
	dbUpdate($oInfos_pages);

	$id = $idPage; // id pour traitement autoClass des assos
	if ($oPage->get_isgabarit_page()==0){ // pages only
		include('cms-inc/autoClass/maj.savepostedassos.php');
	}
	else{
		if (($aNodeToSort[0]["name"] == "ITEM")&&(preg_match('/id/si', $aNodeToSort[0]["attrs"]["NAME"])==1)) { //id
			$aNodeToSort[0]["attrs"]["ASSO"] = 'cms_assoclassepage';
			include('cms-inc/autoClass/maj.savepostedassos.php');
		}
	}	
	
	// page
	$oPage = new Cms_page($idPage); 
	if (DEF_BDD == "ORACLE" || DEF_BDD == "POSTGRES") {

		$datemep = $_POST['dateMep'];
		$datemep = explode('/', $datemep);
		$datemep = "to_date('".$datemep[2]."/".$datemep[1]."/".$datemep[0]."', 'dd/mm/yyyy')";

	} else if (DEF_BDD == "MYSQL") { 
		$datemep = explode('/',$_POST['dateMep']);
		//$datemep = "str_to_date('".$datemep[2]."/".$datemep[1]."/".$datemep[0]."', '%Y/%m/%d %H:%M:%S')";
		$datemep = date('Y-m-d H:M:S', mktime(0, 0, 0, $datemep[1], $datemep[0], $datemep[2]));
	} 
	$oPage->setDatemep_page($datemep);
	dbUpdate($oPage);

	echo '<script type="text/javascript">'."\n";
	if ($oPage->get_isgabarit_page()==1){
		echo 'document.location.href="/backoffice/cms/site/gabaritList.php";'."\n";
	}
	else{
		echo 'document.location.href="arboPage_browse.php";'."\n";
	}
	echo '</script>'."\n";	

}
// date du jour pour le calendrier	
if($idPage != ""){
	$oPage = new Cms_page($idPage);  
	$datemep = explode(' ',$oPage->getDatemep_page());
	$datemep = explode('-',$datemep[0]);
	$date = date('d/m/Y', mktime(0, 0, 0, $datemep[1], $datemep[2], $datemep[0]));
}
else {
	$date = date("d/m/Y");
}
?>
<script type="text/javascript"><!--
function updateDateMep() {
	box = document.getElementById("mepNow");
	field = document.getElementById("dateMep");
	if (box.checked==true) {
		field.value="<?php echo $date; ?>";
	}
}

function updateCheckBox() {
	box = document.getElementById("mepNow");
	field = document.getElementById("dateMep");
	if (field.value=="<?php echo $date; ?>") {
		box.checked=true;
	} else {
		box.checked=false;
	}
}
--></script>
<link rel="stylesheet" type="text/css" media="all" href="/backoffice/cms/lib/jscalendar/calendar-win2k-cold-1.css" title="win2k-cold-1" />
<!-- main calendar program -->
<script type="text/javascript" src="/backoffice/cms/lib/jscalendar/calendar.js"></script>
<!-- language for the calendar -->
<script type="text/javascript" src="/backoffice/cms/lib/jscalendar/lang/calendar-fr.js"></script>
<!-- the following script defines the Calendar.setup helper function -->
<script type="text/javascript" src="/backoffice/cms/lib/jscalendar/calendar-setup.js"></script>
<div id="popCal" style="border-right:2px ridge; border-top: 2px ridge; z-index: 100; visibility: hidden; border-left: 2px; width: 10px; border-bottom: 2px ridge; position: absolute"
onClick="event.cancelBubble=true">
<?php 
 if(is_file($_SERVER['DOCUMENT_ROOT'].'/modules/calendar/calendrier.php')) {
 	echo '<iframe name="popFrame" src="/modules/calendar/calendrier.php" frameBorder="0" width="238" scrolling="no" height="186"></iframe>';
 }
?></div>
<script type="text/javascript">
// enregistrement des fonctions de la page
function enregistrer(){
	if (validate_form()){ 
		//updateCheckBox();
		document.savePage.operation.value="SAUVE";
		document.savePage.action="page_infos.php?idPage=<?php echo $_GET['idPage']; ?>&idGab=<?php echo $_GET['idGab']; ?>";
		document.savePage.submit();			
	}
}

function loadIframe(ifId){
	url = document.getElementById("ifUrl"+ifId).value;
	if (document.getElementById("fDia_diaporama_type") != null){ //cas diaporama
		url += "&setDiaporamaType="+document.getElementById("fDia_diaporama_type").value;
	}
	document.getElementById("if"+ifId).setAttribute('src', url);	
}
	
function annulerForm(){
	if (window.name.indexOf("if")==0){	// ifframe fancybox	
		window.parent.$.fancybox.close();
	}
	else{
		history.back();
	}
}
</script>
<div class="ariane">
<?php
if ($oPage->get_isgabarit_page()==1){
	echo '<span class="arbo2">GABARIT&nbsp;>&nbsp;</span><span class="arbo3">'.$translator->getTransByCode('proprietes_du_gabarit').'</span>';
}
else{
	echo '<span class="arbo2">PAGE&nbsp;>&nbsp;</span><span class="arbo3">'.$translator->getTransByCode('proprietes_de_la_page').'</span>';
}
?>
</div>
<form name="savePage" method="post" class="arbo">
<input type="hidden" name="operation" id="operation">
<input type="hidden" name="idPage" id="idPage" value="<?php echo $idPage; ?>">
<input type="hidden" name="idGab" id="idGab" value="<?php echo $idGab; ?>">
<input type="hidden" name="dateMep" id="dateMep" value="<?php echo $date;?>" />
<!--<p>Veuillez saisir une date de mise en production&nbsp;:&nbsp;<input name="dateMep" type="text" class="arbo" id="dateMep" style="CURSOR: hand;" value="<?php echo $date;?>" size="12" maxlength="10">
<img src="/backoffice/cms/lib/jscalendar/img.gif" id="dateMep_trigger" style="cursor: pointer; border: 1px solid red;" title="Date selector" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />						
<script type="text/javascript">
Calendar.setup({
	inputField     :    "dateMep",  // id of the input field
	ifFormat       :    "%d/%m/%Y",      // format of the input field
	button         :    "dateMep_trigger", // trigger ID
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true
});
</script>
&nbsp;&nbsp;--&nbsp;&nbsp;immédiate&nbsp;<input name="mepNow" type="checkbox" class="arbo" id="mepNow" onClick="updateDateMep();" value="true" checked></p>-->
<!--<p>Veuillez saisir une date de péremption&nbsp;:&nbsp;<input name="datePeremption" type="text" class="arbo" id="datePeremption" style="CURSOR: hand;" onFocus="this.blur();(popFrame.fPopCalendar(this,this,popCal)); document.getElementById('neverPerempt').checked=false;" onClick="this.blur();" value="" size="12" maxlength="10">
<img src="/backoffice/cms/lib/jscalendar/img.gif" id="datePeremption_trigger" style="cursor: pointer; border: 1px solid red;" title="Date selector" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
<script type="text/javascript">
Calendar.setup({
	inputField     :    "datePeremption",  // id of the input field
	ifFormat       :    "%d/%m/%Y",      // format of the input field
	button         :    "datePeremption_trigger", // trigger ID
	align          :    "Tl",           // alignment (defaults to "Bl")
	singleClick    :    true
});
</script>
&nbsp;&nbsp;&nbsp;&nbsp;aucune&nbsp;<input name="neverPerempt" type="checkbox" class="arbo" id="neverPerempt" onClick="if(this.checked) {document.getElementById('datePeremption').value=''}" value="true"></p>
 --> 
<p><strong><?php $translator->echoTransByCode('titre_de_la_page'); ?></strong><br />
<input name="pagetitle" type="text" class="arbo" size="70" maxlength="255" value="<?php echo stripslashes($titre); ?>" /></p>

<p><strong><?php $translator->echoTransByCode('mots_cles'); ?></strong><br />
<textarea name="pagekeywords" class="arbo textareaEdit"><?php echo stripslashes($mots); ?></textarea></p>

<p><strong><?php $translator->echoTransByCode('Description'); ?></strong><br /> 
<textarea name="description" class="arbo textareaEdit"><?php echo stripslashes($description); ?></textarea></p>

<?php if (defined("DEF_PAGE_VIGNETTE") && DEF_PAGE_VIGNETTE == false ) {
}
else  { ?>
<p><strong><?php $translator->echoTransByCode('Vignette'); ?></strong><br />
<script type="text/javascript">
function SetUrl(fileUrl){
	document.getElementById('pagethumb').value=fileUrl;
}
</script>
<input name="pagethumb" id="pagethumb" type="text" class="arbo" value="<?php echo stripslashes($thumb); ?>" size="100" maxlength="768" />
<a href="javascript:openBrWindow('/backoffice/cms/lib/FCKeditor/editor/filemanager/browser/default/browser.html?Connector=connectors/php/connector.php&Type=Image','pickImg',700,600)"><?php $translator->echoTransByCode('choisir'); ?></a></p> 
<?php } ?>

<p><?php
if ($oPage->get_isgabarit_page()==0){ // pages only

	// objet liés par cms_assoclassepage
	unset($_SESSION['BO']['CACHE']);

	//$aAssoClasses = dbGetAssocies($oPage, 'cms_assoclassepage', false, true); // cette est bien, mais foire si pas de pré-remplissage : donc se baser sur le template pour connaitre les classes liées
	$oGab = new Cms_page($oPage->get_gabarit_page());
	$aAssoClasses = dbGetAssocies($oGab, 'cms_assoclassepage', false, true);
	
	if ($aAssoClasses['list']){	
		echo "<script language=\"Javascript\">
                                jQuery( function($) {
                                    $('.sortablelist').nestedSortable(
                                      {
                                        accept: 'sortable-element-class',
                                        onChange : function(serialized) {
                                          // Do something with serialized here
                                          // such as sending it to the server via an AJAX call
                                          // or storing it in a JS variable that you can
                                          // send to the server once the user presses a button.
                                          alert('ok');
                                          console.log(serialized);
                                        }
                                      }
                                    );
                                });



				//Permet de rompre l'association entre un objet métier et une page
				function unlinkEmp(xcp_id){
					sMessage = '".$translator->getById(81)."';
					if(confirm(sMessage)){
						$.ajax({
							type		: 'POST',
							url			: '/include/cms-inc/autoClass/list.unlink_assoclassepage.php',
							data		: 'xcp_id='+xcp_id,
							dataType	: 'html',
							success		: function ( donnees ) { // si la requête est un succès
								document.location.reload();
							},
							error		: function (donnees){
								alert('une erreur est survenue, veuillez contacter votre administrateur');
							}
						});
					}
				} 
				
				function fancy_reuse(url){
					$.fancybox({
							'modal'				: false,
							'href' 				: url+'&noMenu=true',
							'width'				: '68%',
							'height'			: '100%',
							'type'				: 'iframe',
							'hideOnOverlayClick': true,
							'titleShow' 		: false,
                                                        'showCloseButton'               : false,
							'onCleanup'			: function(){
								// alert('toto1');
								// window.document.location.href=window.document.location.href.replace('#_','');
								window.document.location.reload();
							}
					});
					return false;	
				}
			</script>";

		echo "<strong>".$translator->getTransByCode('Objets_Lies')."</strong><br />";
		
		$prevSerial = NULL;
			
		foreach($aAssoClasses['list'] as $kX => $aClasse){		
			if (serialize($aClasse)!=$prevSerial){		

				$xClassName = $aClasse['display'];
				$xClassId = $aClasse['ref_id'];			
			
				$aAssoObjets = dbGetObjectsFromFieldValue3('cms_assoclassepage', array('get_cms_page', 'get_classe'), array('equals', 'equals'), array($idPage, $xClassId), array('get_order'), array('ASC'));				

				$aListe_res = array();	
				eval("$"."oRes = new ".$xClassName."();");
				if(!is_null($oRes->XML_inherited))
					$sXML = $oRes->XML_inherited;
				else
					$sXML = $oRes->XML;

				unset($stack);
				$stack = array();
				xmlClassParse($sXML);
				
				$classeName = $stack[0]["attrs"]["NAME"];
				$classePrefixe = $stack[0]["attrs"]["PREFIX"];
				$aNodeToSort = $stack[0]["children"];
				if (isset($stack[0]["attrs"]["LIBELLE"]) && ($stack[0]["attrs"]["LIBELLE"] != "")){
					$classeLibelle = stripslashes($stack[0]["attrs"]["LIBELLE"]);
				}
				else{
					$classeLibelle = $classeName;
				}
				
				// bouton new
				if (isClassCMS($classeName)){
					$linkNew = '/backoffice/cms/'.$classeName.'/maj_'.$classeName.'.php';
				}
				else{
					$linkNew = '/backoffice/'.$classeName.'/maj_'.$classeName.'.php';
				}
				
				$linkNew = '/backoffice/cms/page_infos_newasso.php?maj='.rawurlencode($linkNew).'&className='.$xClassName.'&idPage='.$oPage->get_id().'&nodeId='.$oPage->get_nodeid_page().'&classId='.$xClassId.'&noMenu=true';
				$linkNew = '&nbsp;<a href="#_" onclick="javascript:fancy_reuse(\''.$linkNew.'\');" title="Nouveau"><img src="/backoffice/cms/img/2013/icone/add.png"  alt="Nouveau" border="0" /></a>';					
					
					
				$linkExist = '/backoffice/cms/page_infos_reuse.php?className='.$classeName.'&idPage='.$oPage->get_id().'&nodeId='.$oPage->get_nodeid_page().'&classId='.$xClassId.'&usedId=-1&oMenu=true';
				$linkExist = '&nbsp;<a href="#_" onclick="javascript:fancy_reuse(\''.$linkExist.'\');" title="Utiliser existant"><img src="/backoffice/cms/img/2013/icone/dupliquer.png"   alt="Nouveau" border="0" /></a>';					
					
				echo '<br /><strong> - '.$classeLibelle.'</strong> - '.$linkNew;
				echo '  -  '.$linkExist.'<br />';
				
				foreach($aAssoObjets as $kOx => $oX){	
					$oXres = dbGetObjectFromPK($xClassName, $oX->get_objet(), true);					
					//$oXres = new $xClassName($oX->get_objet());	
					if($oXres){
						$aListe_res[] = $oXres;
					}
					else{
						//echo '<p>asso missing id '.$oX->get_objet().'</p>';
						dbDelete($oX);
					}			
				}	
				$bStatusControl = false;
				$bDeleteButtonControl = false;
				include('cms-inc/autoClass/list.olsortable.php');
				unset($visuLink);
				unset($editLink);
				echo '<br />';
				
				$prevSerial = serialize($aClasse);
			}
		}
	}	
	
	// retour cms_page générique
	$classeName = 'cms_page';
	eval("$"."oRes = new ".$classeName."();");
	if(!is_null($oRes->XML_inherited))
		$sXML = $oRes->XML_inherited;
	else
		$sXML = $oRes->XML;

	unset($stack);
	$stack = array();
	xmlClassParse($sXML);
	
	$classeName = $stack[0]["attrs"]["NAME"];
	$classePrefixe = $stack[0]["attrs"]["PREFIX"];
	
	
	if(isset($stack[0]['attrs']['NAME'])){	 
		//backup le stack	
		$stackBack = $stack;
		
		$sPathSurcharge = $_SERVER['DOCUMENT_ROOT'].'/include/bo/cms/'.$stack[0]['attrs']['NAME'].'.class.xml';
		
		if(is_file($sPathSurcharge)) xmlFileParse($sPathSurcharge);
	}
	
	$aNodeToSort = $stack[0]["children"];
	// recherche d'eventuelles asso
	for ($i=0;$i<count($aNodeToSort);$i++) {
		if ($aNodeToSort[$i]["name"] == "ITEM") {
			if ($aNodeToSort[$i]["attrs"]["ASSO"] || $aNodeToSort[$i]["attrs"]["ASSO_EDIT"]) { // cas d'asso 
				echo "<strong>".$translator->getTransByCode('Associations')."</strong><br />";
				// AJAX delayed call for association list display
				// first define fields not applying to AJAX display
				$excluded = Array('cms_site');
				if (!in_array($aNodeToSort[$i]["attrs"]["NAME"], $excluded)) {
					// AJAX delayed process
					echo "\n".'<div id="delayed_'.$classePrefixe.'_associations" style="display: inline;"></div>';
					$call = '/backoffice/cms/call_maj_association.php?class='.$classeName.'&id='.$_GET['idPage'].'&field='.$aNodeToSort[$i]["attrs"]["NAME"];
					//echo "test : ".$call."<br/>";
					echo "\n".'<script type="text/javascript">';
					echo "\n".'function ajaxDelayed_associations(){';
					echo "\n".'XHRConnector.sendAndLoad(\''.$call.'\', \'GET\', \'Chargement de la liste...\', \'delayed_'.$classePrefixe.'_associations\');';
					echo "\n".'}';
					echo "\n".'ajaxDelayed_associations();';
					echo "\n".'</script>';
				} else {
					// inline process
					include_once("maj.association.php");
				}
			}	
		}
	}
}
else{ // gabarit only
	if (($aNodeToSort[0]["name"] == "ITEM")&&(preg_match('/id/si', $aNodeToSort[0]["attrs"]["NAME"])==1)) { //id
		$aNodeToSort[0]["attrs"]["ASSO"] = 'cms_assoclassepage';
		$id = $idPage;
		include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/maj.association.php');
	}
}
?></p>
<input name="btEnregistrer" type="button" class="arbo" onClick="javascript:enregistrer()" value="<?php $translator->echoTransByCode('Enregistrer'); ?>">
</form>
<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/append.php');
?>