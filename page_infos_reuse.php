<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
if(isset($_POST['dowhat']) && $_POST['dowhat'] == 'recordReuse'){
	//on enregistre les infos
	// print_r($_POST);
	// die('ici');
	
	// Array
	// (
		// [dowhat] => recordReuse
		// [idPage] => 5
		// [classId] => 135
		// [usedId] => 57
		// [idReuse] => 2
	// )
	if($_POST['usedId'] == '-1'){
		$oX = new cms_assoclassepage();
		$oX->set_cms_page($_POST['idPage']);
		$oX->set_classe($_POST['classId']);
		$oX->set_objet($_POST['idReuse']);
		dbSauve($oX);
	}else{
		$oAssoClassePage = new cms_assoclassepage($_POST['usedId']);
		$oAssoClassePage->set_objet($_POST['idReuse']);
	
		dbUpdate($oAssoClassePage);
	}
	return true;
}else{
	$classeName = $_GET['className'];

	if (is_file($_SERVER['DOCUMENT_ROOT'].'/include/bo/cms/prepend.'.$script))
		require_once('include/bo/cms/prepend.'.$script);
		
	$temps = microtime();
	$temps = explode(' ', $temps);
	$debut = $temps[1] + $temps[0];

	if (!isset($classeName)){
		$classeName = preg_replace('/[^_]*_(.*)\.php/', '$1', basename($_SERVER['PHP_SELF']));
	}
	//------------------------------------------------------------------------------------------------------
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/lib.inc.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');

	unset($_SESSION['BO']['CACHE']);

	// translation engine
	// Added by Luc - 6 oct. 2009
	//if (DEF_APP_USE_TRANSLATIONS) {
		$translator =& TslManager::getInstance();
		$langpile = $translator->getLanguages();
	//}

	/*
	$temps = microtime();
	$temps = explode(' ', $temps);
	$debut = $temps[1] + $temps[0];*/

	/* CACHE */

	// stock en cache le XML de la classe
	// stock en parallèle les infos concernant les différentes classes liées
	cacheClasseXMLAndObjects($classeName);

	// permet de dérouler le menu contextuellement
	if (function_exists('activateMenu')){
	activateMenu('gestion'.$classeName);
} 

	if (preg_match('/\?/', $_SERVER['HTTP_REFERER'])==1) {
		$point = strpos($_SERVER['HTTP_REFERER'], "?");  
	}
	else {
		$point = strlen($_SERVER['HTTP_REFERER']);
	}
	// pre_dump($_POST);
	// pre_dump($_SESSION);
	//  je réinitialise ma recherche  
	if ($_POST['operation'] == 'REINIT' || (substr($_SERVER["HTTP_REFERER"], 0, $point)!="http://".$_SERVER['HTTP_HOST']."".$_SERVER["PHP_SELF"] && $_SESSION['classeName']!=$classeName) )  {   
		
		initFilterSession();
		
	} 

	$rows_per_page = 25;
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/list.process.php');
	?>
	<div class="ariane"><span class="arbo2">MODULE >&nbsp;</span><span class="arbo3"><?php echo $classeLibelle.'&nbsp;>&nbsp;'.$translator->getTransByCode('liste'); ?></span></div>

	<br/>
	<?php
	include('cms-inc/autoClass/list.filters.php');
	?>
	<div class="arbo" align="center"><strong><?php echo $sMessage; ?></strong></div>
	<script src="/backoffice/cms/js/openBrWindow.js" type="text/javascript"></script>
	<script type="text/javascript">
		function reuseThisOne(_idReuse){
			/*
				<input type="hidden" name="idPage" id="idPage" value="<?php echo  $_GET['idPage'] ; ?>" />
				<input type="hidden" name="classId" id="classId" value="<?php echo  $_GET['classId'] ; ?>" />
				<input type="hidden" name="usedId" id="usedId" value="<?php echo  $_GET['usedId'] ; ?>" />	
			*/
			$.ajax({ // ajax
				url			: '<?php echo $_SERVER['PHP_SELF']; ?>', // url de la page à charger
				cache		: false, // pas de mise en cache
				type		: "POST",
				data		: 'dowhat=recordReuse&idPage=<?php echo $_GET['idPage'];?>&classId=<?php echo $_GET['classId'];?>&usedId=<?php echo $_GET['usedId'];?>&idReuse='+_idReuse,
				dataType	: "html",
				success		: function ( donnees ) { // si la requête est un succès
					parent.document.location.reload();
					parent.$.fancybox.close();
				},
				error		: function(XMLHttpRequest, textStatus, errorThrows){

				}
			});
			
			return false;
		}

		function deleteAll(){
			sMessage = "<?php $translator->echoTransByCode('promptsuppr'); ?>";
			if (confirm(sMessage)) {
				document.<?php echo $classePrefixe; ?>_list_form.action = "delete_<?php echo $classeName; ?>.php<?php if($_SERVER['QUERY_STRING']!="") echo "?".$_SERVER['QUERY_STRING'];?>";
				document.<?php echo $classePrefixe; ?>_list_form.submit();
			}
		}
		// fonction de tri
		function doTri(sElementTri, sSensTri) {
			document.<?php echo $classePrefixe; ?>_list_form.champTri.value = sElementTri;

			// on change de tri
			if (document.<?php echo $classePrefixe; ?>_list_form.sensTri.value == "ASC") sSensTri = "DESC";
			else if (document.<?php echo $classePrefixe; ?>_list_form.sensTri.value == "DESC") sSensTri = "ASC";
			else if (document.<?php echo $classePrefixe; ?>_list_form.sensTri.value == "") sSensTri = "ASC";

			document.<?php echo $classePrefixe; ?>_list_form.sensTri.value = sSensTri;
			document.<?php echo $classePrefixe; ?>_list_form.eStatut.value = "<?php echo $_POST['eStatut']; ?>";
			document.<?php echo $classePrefixe; ?>_list_form.sTexte.value = "<?php echo $_POST['sTexte']; ?>";
			document.<?php echo $classePrefixe; ?>_list_form.action = "list_<?php echo $classeName; ?>.php<?php if($_SERVER['QUERY_STRING']!="") echo "?".$_SERVER['QUERY_STRING'];?>";		
			document.<?php echo $classePrefixe; ?>_list_form.submit();
		}

		// ajout d'un enregistrement
		function addEmp(){
			document.<?php echo $classePrefixe; ?>_list_form.actiontodo.value = "MODIF";
			document.<?php echo $classePrefixe; ?>_list_form.display.value = null;
			document.<?php echo $classePrefixe; ?>_list_form.action = "maj_<?php echo $classeName; ?>.php?id=-1<?php if($_SERVER['QUERY_STRING']!="") echo "&".str_replace("id=", "idprev=",ereg_replace("idprev=[^&]*&", "", $_SERVER['QUERY_STRING']));?>";
			document.<?php echo $classePrefixe; ?>_list_form.submit();
		}
		
		// visu de l'enregistrement
		function visuEmp(id) {
			document.<?php echo $classePrefixe; ?>_list_form.id.value = id;
			document.<?php echo $classePrefixe; ?>_list_form.display.value = null;
			document.<?php echo $classePrefixe; ?>_list_form.actiontodo.value = "";
			<?php if ($classeName == "cms_tableau") { ?>
				document.<?php echo $classePrefixe; ?>_list_form.action = "/backoffice/cms/cms_tableau/show_<?php echo $classeName; ?>.php?id="+id+"<?php if($_SERVER['QUERY_STRING']!="") echo "&".str_replace("id=", "idprev=",ereg_replace("idprev=[^&]*&", "", $_SERVER['QUERY_STRING']));?>";
			<?php }
			else { ?>
				document.<?php echo $classePrefixe; ?>_list_form.action = "show_<?php echo $classeName; ?>.php?id="+id+"<?php if($_SERVER['QUERY_STRING']!="") echo "&".str_replace("id=", "idprev=",ereg_replace("idprev=[^&]*&", "", $_SERVER['QUERY_STRING']));?>";
	<?php 		} ?>
			
			document.<?php echo $classePrefixe; ?>_list_form.submit();
		}

		// modification de l'enregistrement
		function modifEmp(id) {
			document.<?php echo $classePrefixe; ?>_list_form.id.value = id;
			document.<?php echo $classePrefixe; ?>_list_form.display.value = null;
			document.<?php echo $classePrefixe; ?>_list_form.actiontodo.value = "MODIF";
			document.<?php echo $classePrefixe; ?>_list_form.action = "maj_<?php echo $classeName; ?>.php?id="+id+"<?php if($_SERVER['QUERY_STRING']!="") echo "&".str_replace("id=", "idprev=",ereg_replace("idprev=[^&]*&", "", $_SERVER['QUERY_STRING']));?>";
			document.<?php echo $classePrefixe; ?>_list_form.submit();
		}

		// suppression de l'enregtistrement
		function deleteEmp(id)	{
			sMessage = "<?php $translator->echoTransByCode('promptsupprun'); ?>";
			if (confirm(sMessage)) {

				document.<?php echo $classePrefixe; ?>_list_form.action = "list_<?php echo $classeName; ?>.php<?php if($_SERVER['QUERY_STRING']!="") echo "?".$_SERVER['QUERY_STRING'];?>";
				document.<?php echo $classePrefixe; ?>_list_form.operation.value = "DELETE";
				document.<?php echo $classePrefixe; ?>_list_form.id.value = id;
				document.<?php echo $classePrefixe; ?>_list_form.display.value = null;
				document.<?php echo $classePrefixe; ?>_list_form.submit();
			}
		}
		
		// change le statut de plusieurs records
		function changeStatut(idStatut)	{
			cbToChange = "";
	<?php
	for ($m=0; $m<sizeof($aListe_res); $m++) {
		$oRes = $aListe_res[$m];	
		$cb = "cb_".ucfirst($classePrefixe)."_".$oRes->get_id();	
	?>
	if (document.getElementById("<?php echo $cb; ?>").checked == true) cbToChange+= "<?php echo $oRes->get_id(); ?>;";
	<?php
	}
	?>
			if (cbToChange != "") {
				document.<?php echo $classePrefixe; ?>_list_form.cbToChange.value = cbToChange;
				document.<?php echo $classePrefixe; ?>_list_form.idStatut.value = idStatut;
				document.<?php echo $classePrefixe; ?>_list_form.action = "list_<?php echo $classeName; ?>.php<?php if($_SERVER['QUERY_STRING']!="") echo "?".$_SERVER['QUERY_STRING'];?>";
				document.<?php echo $classePrefixe; ?>_list_form.operation.value = "CHANGE_STATUT";
				document.<?php echo $classePrefixe; ?>_list_form.submit();
			} else {
				msg = "<?php $translator->echoTransByCode('selectionneraumoins1'); ?>";
				alert(msg);		
			}
		}


	function sel(nomForm, i, l){
		/*if (eval("document.<?php echo $classePrefixe; ?>_rech_form."."+i+".checked"))

		{
			eval("document."+nomForm+"."+l+".className='EnrSelectionne'");
		}
		else
		{   
			var noLigne=l.substring(5, l.length);

			var classe="impair";
			if (noLigne%2==0) classe="pair";   
	   
			eval("document."+nomForm+"."+l+".className='"+classe+"'");
		}   */
	}

	  function reinit(){ 	 
			 document.location.href="list_<?php echo $classeName; ?>.php"; 	 
			 document.<?php echo $classePrefixe; ?>_rech_form.sTexte.value = ""; 	 
			 document.<?php echo $classePrefixe; ?>_rech_form.operation.value = "REINIT"; 	 
			 document.<?php echo $classePrefixe; ?>_rech_form.submit(); 	 
			 }
	</script>
	<?php
	if (isset($aCustom["JS"]) && ($aCustom["JS"] != "")){
		echo "<script type=\"text/javascript\">\n";
		//##classePrefixe## -> $classePrefixe
		//##classeName##    -> $classeName
		$search = array("##classePrefixe##", "##classeName##", "##id##");
		$replace = array($classePrefixe, $classeName, $oRes->get_id());
		echo str_replace($search, $replace, $aCustom["JS"]);
		echo "\n</script>\n";
	}
	?>
	<style type="text/css">
	/*.pair {background-color: #E6E6E6;}
	.impair {background-color: #EEEEEE;}
	.EnrSelectionne {background-color: #FBFBEC;}*/
	</style>
	<form name="<?php echo $classePrefixe; ?>_list_form" id="<?php echo $classePrefixe; ?>_list_form"  method="post">
	<input type="hidden" name="urlRetour" id="urlRetour" value="<?php echo  $_SERVER['REQUEST_URI'] ; ?>" />
	<input type="hidden" name="id" id="id" value="" />
	<input type="hidden" name="display" id="display" value="" />
	<input type="hidden" name="actionUser" id="actionUser" value="" />
	<input type="hidden" name="operation" id="operation" value="<?php echo $operation; ?>" />
	<input type="hidden" name="actiontodo" id="actiontodo" value="" />
	<input type="hidden" name="sensTri" id="sensTri" value="<?php echo $sensTri; ?>" />
	<input type="hidden" name="champTri" id="champTri" value="<?php echo $champTri; ?>" />
	<input type="hidden" name="idStatut" id="idStatut" value="" />
	<input type="hidden" name="cbToChange" id="cbToChange" value="" />
	<input type="hidden" name="eStatut" id="eStatut" value="" />
	<input type="hidden" name="sTexte" id="sTexte" value="<?php echo $_SESSION['sTexte']; ?>" />
	<!-- Début Pagination -->
	<table border="0" align="center" cellpadding="0" cellspacing="0" class="pagination">
		<tr>
			<td><?php print($pager->bandeau); ?></td>
		</tr>
	</table>
	<!-- Fin Pagination -->
	<br />
	<?php
	if(sizeof($aListe_res)>0) {// s'il y a des enregistrements à afficher
		eval("$"."oRes = new ".$classeName."();");
		if(!is_null($oRes->XML_inherited))
			$sXML = $oRes->XML_inherited;
		else
			$sXML = $oRes->XML;
		//$sXML = $oRes->XML;
		unset($stack);
		$stack = array();
		xmlClassParse($sXML);
		
		$classeName = $stack[0]["attrs"]["NAME"];
		$classePrefixe = $stack[0]["attrs"]["PREFIX"];
		$aNodeToSort = $stack[0]["children"];

		$bStatusControl = false;
		$bSelectControl = true;
		$bDeleteButtonControl = false;
		
		$visuLink = false;
		$editLink = false;
		$editLink = false;
		
		// $aCustom["Action"] = '&nbsp;<a href="'.$linkNew.'" title="Nouveau"><img src="/backoffice/cms/img/add.gif" width="16" height="16" alt="Nouveau" border="0" /></a>';
					
		include('cms-inc/autoClass/list.table.php');
	} 
	else {
		echo '<div align="center" class="arbo">'.$translator->echoTransByCode('aucunenregistrement').'</div>';
	}
	/*$temps = microtime();
	$temps = explode(' ', $temps);
	$fin = $temps[1] + $temps[0];
	echo '<br/>Page exécutée en '.round(($fin - $debut),6).' secondes.<br/>';*/
	?>
	</form>
<?php
}
?>