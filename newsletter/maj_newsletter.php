<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
// sponthus 16/11/2005

// Formulaire de saisie d'une newsletter
 

include_once('newsletter_export.php');

if (defined("DEF_FCK_VERSION") && DEF_FCK_VERSION == "ckeditor" ) {
	echo '<script src="/backoffice/cms/lib/ckeditor/ckeditor.js"></script>';
} 
else {
	include("backoffice/cms/lib/FCKeditor/fckeditor.php") ;
}

// Chargement de la classe UPLOAD
require_once('cms-inc/lib/fileUpload/upload.class.php');

// Chargement de la manipulation des images (resize)
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/utils/graphic.lib.php');


$actiontodo = ( $_POST['actiontodo']!="SAUVE" ) ? "MODIF" : "SAUVE" ;
if ($_GET['id'] == '') $_GET['id'] = -1;

if (is_get('newid')) {
	$newid=$_GET['newid'];
}
elseif(is_post('newid')) {
	$newid=$_POST['newid'];
}
$_SESSION['listParam']=$_SERVER['QUERY_STRING'];
$listParam=$_SESSION['listParam'];

  
// second controle, si id=X dans l'url on vira l'occurence eventuelle en session
if (preg_match('/id=([0-9]+)/msi', $listParam, $idMatches)==1){
	if ((is_get('id') && ($idMatches[1]!=$_GET['id']))	||	(is_post('id') && ($idMatches[1]!=$_POST['id']))){
		$listParam = str_replace($idMatches[0], '', $listParam);
		$listParam = str_replace('&&', '&', $listParam);
	}
} 

// enregistrement à modifier

$id=$_GET['id'];
if(!isset($id)) $id=$_POST['id'];
// newsletter sélectionnée
if ($_SESSION['S_news_selected'] == "" || $_SESSION['S_news_selected'] == -1) $_SESSION['S_news_selected'] = $id;
if ($id == "") $id = $_SESSION['S_news_selected'] ;
 

// activation du menu : déroulement
activateMenu("gestiondesnewsletter"); 

// objet 
if ( $id > 0 ) $operation = "UPDATE";
else $operation = "INSERT";


// ------------------------------------------------------------
// déclaration de la classe 
// ------------------------------------------------------------

if ( $operation == "INSERT" ) { // Mode ajout
	$oNews = new newsletter();
} else { // Mode mise à jour
	$oNews = new newsletter($id);
}


   
if(!is_null($oNews->XML_inherited))
	$sXML = $oNews->XML_inherited;
else
	$sXML = $oNews->XML;
	
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

// translator

$translator =& TslManager::getInstance();
if (DEF_EDIT_INACTIVE_LANG)
	$langpile = $translator->getLanguages();
else	$langpile = $translator->getLanguages(true);

// ------------------------------------------------------------
// déclaration de la classe 
// ------------------------------------------------------------

	
$sSqlexp = " SELECT * FROM news_expediteur ";
$sSqlexp.= " WHERE exp_statut=".DEF_ID_STATUT_LIGNE." " ;
$aExp = dbGetObjectsFromRequete("news_expediteur", $sSqlexp);

?>
<link rel="stylesheet" type="text/css" media="all" href="/backoffice/cms/lib/jscalendar/calendar-win2k-cold-1.css" title="win2k-cold-1" />
<script type="text/javascript">
	// retour à la page précédente
	function retour()
	{
		document.add_news_form.action = "list_newsletter.php?menuOpen=true&"; 
		document.add_news_form.submit(); 
	}
	
	function annulerForm(){
		<?php if(is_get('noMenu')){?>
			parent.$.fancybox.close(); 
		<?php }else{ ?>
			if (window.name.indexOf("if")==0){	// ifframe fancybox	
				window.parent.$.fancybox.close();
			}
			else{
				history.back();
			}
		<?php } ?>
	}
	
	function loadIframe(ifId){
		url = document.getElementById("ifUrl"+ifId).value;
		if (document.getElementById("fDia_diaporama_type") != null){ //cas diaporama
			url += "&setDiaporamaType="+document.getElementById("fDia_diaporama_type").value;
		}
		document.getElementById("if"+ifId).setAttribute('src', url);	
	}
</script>
<script type="text/javascript" src="/backoffice/cms/lib/jscalendar/calendar.js"></script>
<!-- language for the calendar -->
<script type="text/javascript" src="/backoffice/cms/lib/jscalendar/lang/calendar-fr.js"></script>
<!-- the following script defines the Calendar.setup helper function -->

<script type="text/javascript" src="/backoffice/cms/lib/jscalendar/calendar-setup.js"></script>
<form name="add_news_form" method="post" enctype="multipart/form-data">
<input type="hidden" name="postnumberone" value="XXXX">
<input type="hidden" name="postnumber2" value="XXXX2">
<?php

$status = '';
if($actiontodo == "SAUVE") { // MODE ENREGISTREMENT
	
	if (isset($newid)	&&	($newid==-1)){
		$operation = 'INSERT';	
	}

	$bRetourImg = true;
	
	if($bRetourImg) {

		// Récupération des infos saisies dans l'objet
		if($operation == "UPDATE") $oNews->set_id($id);
		else $id = getNextVal("newsletter", "news_id");
		$oNews->set_dateenvoi($_POST['fNews_date']);
		$oNews->set_theme($_POST['fNews_theme']);
		
		foreach ($aNodeToSort as $key => $node){
			if ($node["attrs"]["NAME"] == 'libelle'){
				$i = $key;
				break;
			}
		}
		$form_field = 'f'.ucfirst($classePrefixe).'_'.$aNodeToSort[$i]["attrs"]["NAME"];
		// test les traductions sur libellé
		require($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/maj.saveposteditems.translations.php');
	
		$oNews->set_libelle($_POST['fNews_libelle']);

		
		$oNews->set_expediteur($_POST['fNews_exp']);	
		
		
		
		$themeNews = $oNews->get_theme();
		$oTheme = new news_theme($themeNews);
		
		if (method_exists($oTheme, 'get_abon_criteres')){
			$bUseCriteres = str_replace(array('Y', 'N'), array(1, 0), $oTheme->get_abon_criteres());
		}
		else{
			$bUseCriteres = 0;
		}
		if (method_exists($oTheme, 'get_abon_multiple')){
			$bUseMultiple = str_replace(array('Y', 'N'), array(1, 0), $oTheme->get_abon_multiple());
		}
		else{
			$bUseMultiple = 0;
		}
		if (method_exists($oTheme, 'get_allow_edit')){
			$bAllowEdit = str_replace(array('Y', 'N'), array(1, 0), $oTheme->get_allow_edit());
		}
		else{
			$bAllowEdit = 1;
		}
		
		
		$sBodyHTML = $_POST['fNews_html'];
		$sBodyHTML = preg_replace('/href="\//', 'href="http://'.$_SERVER['HTTP_HOST'].'/', $sBodyHTML);
		$sBodyHTML = preg_replace('/src="\//',  'src="http://'.$_SERVER['HTTP_HOST'].'/', $sBodyHTML);
		$sBodyHTML = preg_replace('/background="\//',  'background="http://'.$_SERVER['HTTP_HOST'].'/', $sBodyHTML);
		$sBodyHTML = preg_replace('/url\(\//',   'url(http://'.$_SERVER['HTTP_HOST'].'/', $sBodyHTML);			
		$sBodyHTML = str_replace("XX-IDNEW-XX", $id , $sBodyHTML);	
		
		$oNews->set_html($sBodyHTML);
		$oNews->set_statut($_POST['fNews_statut']);
		
		// maj BDD
		if ($operation == "UPDATE") {
			$bRetour = dbUpdate($oNews);
		} 
		else if ($operation == "INSERT") {
			$bRetour = true;			
			// recherche si un enr avec même libelle existe déjà
			// $bDeja_present = (getCount2($oNews, "news_libelle", $oNews->getNews_libelle(), "TEXT")>0);
			// pas de contrôle d'unicité de libelle
			// non demandé dans les specs
			$bDeja_present = false;
			if($bDeja_present!=false) {
				$status.="Une newsletter est déjà présente avec cet objet : ".$oNews->get_libelle()."<br>";
				$bRetour = false;
			}
			else { // tout est ok => on enregistre
				$bRetour = dbInsertWithAutoKey($oNews);
			}			
		} //else if ($operation == "INSERT") {
		
		
	}
	else
		$status.= 'Erreur lors de '. ( ($operation == "INSERT") ? "l'ajout" : "la modification" ) .' de la newsletter';

  
	if($bRetour) { 
		$oRes = $oNews;
		include('cms-inc/autoClass/maj.savepostedassos.php');
		$oNews = $oRes;	
		
		// on ne se sauve le source réécrit que dans le cas de newsletter sur critère pour laquelle le wysiwyg est désactivé
		if (($bUseCriteres == 1)&&($bAllowEdit == 0)){
			$sBodyHTML = rewriteNewsletterBody($oNews->get_html(), 0, $id, $themeNews, $bUseCriteres, $bUseMultiple, $_SESSION['id_langue'], $oNews->get_libelle());// langue du site
		}

		$oNews->set_html($sBodyHTML);
		dbSauve($oNews);

		$listParam = '';

  		include ("cms-inc/autoClass/maj.result.php");  
		
	}  
	else {
		echo $status;
	} 
 
}
else { // MODE EDITION

// Gestion de l'Upload de la photo
	
	// Instanciation d'un nouvel objet "upload"
	$Upload = new Upload();
	// Pour ajouter des attributs aux champs de type file
	$Upload-> FieldOptions = 'class="arbo"';
	// Pour indiquer le nombre de champs désiré
	$Upload-> Fields = 1;
	// Initialisation du formulaire
	$Upload-> InitForm();
?>
<span class="arbo2">NEWSLETTER >&nbsp;</span><span class="arbo3"><?php echo  ($operation == "INSERT") ? "Ajouter" : "Modifier" ?> une newsletter</span><br><br>

<script src="/backoffice/cms/js/validForm.js" type="text/javascript"></script>
<script>

	// contrôle la validité du formulaire
	function ifFormValid()
	{
		
		erreur=0;
		sMessage = "Valeur(s) incorrecte(s) : \n\n";


<?php	
foreach ($aNodeToSort as $key => $node){
			if ($node["attrs"]["NAME"] == 'libelle'){
				$i = $key;
				break;
			}
		}	
if (DEF_APP_USE_TRANSLATIONS && $aNodeToSort[$i]["attrs"]["TRANSLATE"]){
	echo "localErreur=0;\n";
	foreach($langpile as $kLang => $aLangue){				
		//$chk_field_name = "f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."_".$langpile[DEF_APP_LANGUE]['libellecourt'];
		$chk_field_name = "f".ucfirst($classePrefixe)."_".$aNodeToSort[$i]["attrs"]["NAME"]."_".$aLangue['libellecourt'];				
		
		echo "if(document.getElementById(\"".$chk_field_name."\") != undefined){\n";
		echo "	if(document.getElementById(\"".$chk_field_name."\").disabled != true){\n";
		echo "		if(document.getElementById(\"".$chk_field_name."\").value== \"\" || document.getElementById(\"".$chk_field_name."\").value==-1){\n";
		echo " 			localErreur++;\n";					
		echo "		}\n";
		echo "	}\n";
		echo "}\n";				
	}
	if (isset ($aNodeToSort[$i]["attrs"]["LIBELLE"]) && $aNodeToSort[$i]["attrs"]["LIBELLE"] != "") $nomChamp = $aNodeToSort[$i]["attrs"]["LIBELLE"];
	else  $nomChamp = $aNodeToSort[$i]["attrs"]["NAME"];
	echo "if(localErreur==".count($langpile)."){\n";
	echo " 	erreur++;\n";
	echo " 	sMessage+=\" - ".$nomChamp." \\n\";\n";
	echo "}\n";
}
else{
?>
		// libelle /////////
		if (document.add_news_form.fNews_libelle.value==0) {
			sMessage+="- Le sujet doit être renseigné\n";
			erreur++;
		}
<?php
}			
?>		
		if (document.add_news_form.fNews_theme.value==-1) {
			sMessage+="- La catégorie doit être renseigné\n";
			erreur++;
		}

// DATE /////////

		// controle validité date

		// date
		/*if (document.add_news_form.fNews_date.value != "") {
			bDate = isDate(document.add_news_form.fNews_date.value);
			if (!bDate) {
				sMessage+="- La date est mal renseignée : veuillez la saisir au format jj/mm/aaaa\n";
				erreur++;
			}
		}*/
		
		if (!checkExpediteur()) {
			sMessage+="- Veuillez sélectionner un expéditeur\n";
			erreur++;
		}


		// affichage des messages d'erreur
		if (erreur > 0) {
			alert(sMessage);
			return false;
		}
		else return true;
	}


	// validation du formulaire
	function validerForm()
	{
		// si le formulaire est valide
		
		if (ifFormValid()) {
			if (validate_form(0)){ 
				document.add_<?php echo $classePrefixe; ?>_form.operation.value = "<?php echo $operation; ?>";
				<?php if(is_get('noMenu')){?>
					document.add_<?php echo $classePrefixe; ?>_form.action = "maj_<?php echo $classeName; ?>.php?noMenu=true<?php if($listParam!="") echo "&".$listParam; ?>"; 
				<?php }else{ ?>
					document.add_<?php echo $classePrefixe; ?>_form.action = "maj_<?php echo $classeName; ?>.php<?php if($listParam!="") echo "?".$listParam;?>"; 
				<?php } ?>
				document.add_<?php echo $classePrefixe; ?>_form.target = "_self";
				document.add_<?php echo $classePrefixe; ?>_form.submit(); 
			}	
		}
	}

	// affichage du calendrier en popup
	function choisir_date(sChamp)
	{
		sUrl = "/backoffice/horaires/popup_calendrier.php?sChamp="+sChamp;
		sNom = "Calendrier";
		sProp = "resizable=yes, scrollbars=no, menubar=no, toolbar=no, status=no, height=200, width=500";

		window.open(sUrl, sNom, sProp);
	}

	// affichage des actualités en popup


	function delete_photo() 
	{
		if (confirm('Etes-vous sûr de vouloir supprimer cette photo?')) 
		{
			document.getElementById("divphoto").style.display="none";
			document.getElementById("framephoto").src="suppPhoto_act.php?id=<?php echo  $id ; ?>";
		}
	}

	function changeTemplate()
	{
		document.add_news_form.actiontodo.value = "MODIF"; 
		document.add_news_form.action = "maj_newsletter.php?<?php echo $_SERVER['QUERY_STRING']; ?>"; 
		document.add_news_form.target = "_self";
		document.add_news_form.submit(); 
			
	}
	
	function checkExpediteur () {

		var mesBoutons=document.add_news_form.fNews_exp;
		var ok=false;
		if(mesBoutons.length==undefined){
		  ok=mesBoutons.checked; 
		}else{
		  for(var i=0;(i<mesBoutons.length)&&(!ok);i++){
			  ok=mesBoutons[i].checked;
		  }
		}
		 
		if(!ok) return false; 
		else  return true;

	}

</script>
<!-- MODE EDITION -->
<?php
//========== DATES =================
// initialisation des dates si vides

// dates de début et de fin de l'actualité

?>

<input type="hidden" name="operation" value="<?php echo $operation; ?>">
<input type="hidden" name="urlRetour" value="<?php echo $_POST['urlRetour']; ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="id_news" value="<?php echo $id_news; ?>">
<input type="hidden" name="actiontodo" value="SAUVE">
<input type="hidden" name="sChamp" value="">
<input type="hidden" name="bPremierChargement" value="">
<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
 <tr>
   <td bgcolor="#E6E6E6"  align="right" class="arbo">&nbsp;<u><b>Date envoi prévue</b></u>&nbsp;</td>
   <td bgcolor="#EEEEEE"  align="left" class="arbo">   
   <input type="text" name="fNews_date" id="fNews_date" class="arbo" value="<?php echo $oNews->get_dateenvoi(); ?>" size="30" maxlength="20" />
   <img src="/backoffice/cms/lib/jscalendar/img.gif" id="f_trigger1" style="cursor: pointer; border: 1px solid red;" title="Date selector" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />   
    <script type="text/javascript">
	Calendar.setup({
		inputField     :    "fNews_date",  // id of the input field
		ifFormat       :    "%d/%m/%Y",      // format of the input field
		button         :    "f_trigger1", // trigger ID
		align          :    "Tl",           // alignment (defaults to "Bl")
		singleClick    :    true
	});
	</script>	 
	 </td>
 </tr> <tr>
  <td width="141" align="right" bgcolor="#E6E6E6" class="arbo">&nbsp;<u><b>Catégorie</b></u>&nbsp;*</td>
  <td width="735" align="left" bgcolor="#EEEEEE" class="arbo">
 <select name="fNews_theme" id="fNews_theme" class="arbo" onchange="javascript:changeTemplate();">
   <!--<select name="fNews_theme" id="fNews_theme" class="arbo">-->
	<option value="-1" >- - Thème- -</option>
	<?php
	$aTheme=dbGetObjectsFromRequete("news_theme","select * from news_theme order by theme_libelle");
	
	for($i=0;$i<newSizeOf($aTheme);$i++){
		$oTheme=$aTheme[$i];
		
		if ($oNews->get_theme() == $oTheme->get_id()) $selected = "selected";
		else if (isset($_POST['fNews_theme']) && $_POST['fNews_theme'] == $oTheme->get_id()) $selected = "selected";
		else  $selected = "";
			
		?>
		<option value="<?php echo $oTheme->get_id(); ?>" <?php echo $selected; ?>><?php echo $oTheme->get_libelle(); ?></option>
		<?php
	}
	 ?></select>
  </td>
 </tr>

  <tr>
  <td width="141" align="right" bgcolor="#E6E6E6" class="arbo">&nbsp;<u><b>Objet du mail</b></u>&nbsp;*</td>
  <td width="535" align="left" bgcolor="#EEEEEE" class="arbo">
<?php
foreach ($aNodeToSort as $key => $node){
	if ($node["attrs"]["NAME"] == 'libelle'){
		$i = $key;
		break;
	}
}


if (DEF_APP_USE_TRANSLATIONS && $aNodeToSort[$i]["attrs"]["TRANSLATE"]) { // cas d'une traduction
	$eKeyValue = $oNews->get_libelle();
	include($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/maj.translation.php');

}
else{
	echo '<input type="text" name="fNews_libelle" class="arbo" size="80" value="'.$oNews->get_libelle().'" />';
}
	?>
	</td>
 </tr>
 <tr>
   <td bgcolor="#E6E6E6"  align="right" class="arbo">&nbsp;<u><b>HTML</b></u>&nbsp;</td>
   <td bgcolor="#EEEEEE"  align="left" class="arbo"><?php 
   
   $bUseCriteres = 0;
   
   if ( $oNews->get_theme() != -1) {
		$themeNews = $oNews->get_theme();
		$oTheme = new news_theme($themeNews);
		
		if (method_exists($oTheme, 'get_abon_criteres')){
			$bUseCriteres = str_replace(array('Y', 'N'), array(1, 0), $oTheme->get_abon_criteres());
		}
		else{
			$bUseCriteres = 0;
		}
		if (method_exists($oTheme, 'get_abon_multiple')){
			$bUseMultiple = str_replace(array('Y', 'N'), array(1, 0), $oTheme->get_abon_multiple());
		}
		else{
			$bUseMultiple = 0;
		}
		if (method_exists($oTheme, 'get_allow_edit')){
			$bAllowEdit = str_replace(array('Y', 'N'), array(1, 0), $oTheme->get_allow_edit());
		}
		else{
			$bAllowEdit = 1;
		}
		
		// on ne se rewrite le source que dans le cas de newsletter sur critère pour laquelle le wysiwyg est désactivé
		if (($bUseCriteres == 1)&&($bAllowEdit == 0)){
			$sBodyHTML = rewriteNewsletterBody($oNews->get_html(), 0, $id, $themeNews, $bUseCriteres, $bUseMultiple, $_SESSION['id_langue'], $oNews->get_libelle()); // langue du site
		}
		else{
			$sBodyHTML = $oNews->get_html();
		}
	}

if (($sBodyHTML == "") && ($bUseCriteres == 0)){
	
	if (is_file('create_lettreinformation.php')){ 
		include_once ('create_lettreinformation.php'); 
	}
	elseif (is_file($_SERVER['DOCUMENT_ROOT'].'modules/newsletter/create_lettreinformation.php') || is_file($_SERVER['DOCUMENT_ROOT'].'include/modules/newsletter/create_lettreinformation.php')){
		include_once ('modules/newsletter/create_lettreinformation.php');
		
	}
	else{
		// n'inclut rien
	}
	$sBodyHTML = str_replace("XX-CLIQUEZ-XX", "Si vous n'arrivez pas à lire cet email, <a href=\"http://".$_SERVER['HTTP_HOST']."/frontoffice/newsletter/create_lettreinformation.php?lien=1\" target=\"_blank\">cliquez ici</a>" , $sBodyHTML);
	$sContentHTML = $sBodyHTML;
} 

if (($bUseCriteres == 0)||($bAllowEdit == 1)){
	if (defined("DEF_FCK_VERSION") && DEF_FCK_VERSION == "ckeditor" ) { 
		
		
	$ck = "CKEDITOR.replace( 'fNews_html', {\n";
	/*if (defined("DEF_FCK_TOOLBARSET")) $ck.=  "toolbar : '".DEF_FCK_TOOLBARSET."',";
	$ck.= "width : '100%',";
	$ck.= "height : '700px',";*/
	$ck.= "customConfig : '/backoffice/cms/lib/ckeditor/config.php'";
	$ck.= "});";
			 
 
		echo " 
	 
		<textarea cols=\"70\" id=\"fNews_html\" name=\"fNews_html\" rows=\"30\" width=\"800\" height=\"800\">".htmlFCKcompliant($sBodyHTML)."</textarea>";
                 ?>
		<script>
                    var roxyFileman = '/backoffice/cms/lib/ckeditor/fileman/index.html';

                    function init_ck () {  
                            CKEDITOR.replace( 'fNews_html', {

                                customConfig : '/backoffice/cms/lib/ckeditor/config.php',
                                <?php if( defined( 'DEF_FILEMANAGER' ) && DEF_FILEMANAGER == 'fileman' ){ ?>
                                filebrowserBrowseUrl:roxyFileman,
                                filebrowserImageBrowseUrl:roxyFileman+'?type=image',
                                removeDialogTabs: 'link:upload;image:upload'
                                <?php } ?>
                            });
                    }
                        <?php
			
			echo "if (navigator.userAgent.toLowerCase().indexOf(\"chrome\") != -1) {
				
				setTimeout(\"init_ck()\", 500); 
			}
			else {
				init_ck();
			}"; ?>
		</script> 
                <?php
	}
	else {
		$oFCKeditor->BasePath = '/backoffice/cms/lib/FCKeditor/' ;	// '/FCKeditor/' is the default value.
		$sBasePath = $_SERVER['PHP_SELF'] ;
		$sBasePath = '/backoffice/cms/lib/FCKeditor'.substr( $sBasePath, 0, strpos( $sBasePath, "backoffice/" ) ) ;
		
		$oFCKeditor = new FCKeditor('fNews_html') ;
		$oFCKeditor->BasePath	= $sBasePath ;
	
		$oFCKeditor->Value = htmlFCKcompliant($sBodyHTML); 
		
		$oFCKeditor->Width  = '700' ;
		$oFCKeditor->Height = '700' ;
		$oFCKeditor->Create() ;
	}
}
else{
	echo $sBodyHTML;
	echo '<input type="hidden" name="fNews_html" id="fNews_html" value="criterias based html" />';
}
?>   
   </td>
 </tr> 
  <tr>
   <td bgcolor="#E6E6E6"  align="right" class="arbo">&nbsp;<u><b>Expéditeur</b></u>&nbsp;</td>
   <td bgcolor="#EEEEEE"  align="left" class="arbo">
   <?php
   // requete pour expéditeur	
	if (newSizeOf($aExp) > 0) {
		for ($i=0; $i<newSizeOf($aExp); $i++){
			$oExp = $aExp[$i];
			if (($oNews->get_expediteur() == $oExp->get_id()) ||($oNews->get_expediteur() == -1 && $i == 0)){
				$checked = "checked";
			}
			else {
				$checked = "";
			}
			echo "<input type=\"radio\" id=\"fNews_exp\" name=\"fNews_exp\" value=\"".$oExp->get_id()."\" ".$checked." />&nbsp;".$oExp->get_prenom()." ".$oExp->get_nom()." - ".$oExp->get_mail()."<br/>";
	
		}
	}
   ?>   
  </td>
  </tr>
 <tr>
   <td bgcolor="#E6E6E6"  align="right" class="arbo">&nbsp;<u><b>Validation</b></u>&nbsp;</td>
   <td bgcolor="#EEEEEE"  align="left" class="arbo">
   <?php
   
if ($oNews->get_statut() == DEF_ID_STATUT_NEWS_ATTEN) $checked_ATTEN = "checked"; else $checked_ATTEN = "";
if ($oNews->get_statut() == DEF_ID_STATUT_NEWS_CREA) $checked_VALID = "checked"; else $checked_VALID = "";
?>
<input type="radio" name="fNews_statut" id="fNews_statut" value="<?php echo DEF_ID_STATUT_NEWS_ATTEN; ?>" <?php echo $checked_ATTEN; ?>  />&nbsp;<?php echo DEF_LIB_STATUT_NEWS_ENCOURS; ?>&nbsp;
<input type="radio" name="fNews_statut" id="fNews_statut" value="<?php echo DEF_ID_STATUT_NEWS_CREA; ?>" <?php echo $checked_VALID; ?> />&nbsp;<?php echo DEF_LIB_STATUT_NEWS_VALID; ?>&nbsp;
&nbsp;&nbsp;&nbsp;<?php
if ($oNews->get_statut() == DEF_ID_STATUT_NEWS_ENVOI) { ?>
<input type="radio" name="fNews_statut" id="fNews_statut" value="<?php echo DEF_ID_STATUT_NEWS_ENVOI; ?>" checked />&nbsp;<strong><?php echo DEF_LIB_STATUT_NEWS_ENVOI; ?></strong>
<?php }
?>
</td>
 </tr>
 
 <?php  
  
 
 
 echo "<tr>\n";
echo "<td width=\"141\" align=\"right\" bgcolor=\"#E6E6E6\" class=\"arbo\" style=\"padding-top: 8px;\">";
echo "<b>".$translator->getTransByCode('Associations')."</b>";
echo "</td>\n";
echo "<td width=\"535\" align=\"left\" bgcolor=\"#EEEEEE\" class=\"arbo\">";


// AJAX delayed call for association list display
// Added by Luc
// first define fields not applying to AJAX display
 

// AJAX delayed process
echo "\n".'<div id="delayed_'.$classePrefixe.'_associations" style="display: inline;"></div>';
$call = '/backoffice/cms/call_maj_association.php?class='.$classeName.'&id='.$_GET['id'].'&field=id';
//echo "test : ".$call."<br/>";
echo "\n".'<script type="text/javascript">';
echo "\n".'function ajaxDelayed_associations(){';
echo "\n".'XHRConnector.sendAndLoad(\''.$call.'\', \'GET\', \'Chargement de la liste...\', \'delayed_'.$classePrefixe.'_associations\');';
echo "\n".'}';
echo "\n".'ajaxDelayed_associations();';
echo "\n".'</script>';
 
echo "</td>\n</tr>\n";
 
 
 ?>
 <tr>
   <td bgcolor="#E6E6E6"  align="right" class="arbo">&nbsp;</td>
   <td bgcolor="#EEEEEE"  align="left" class="arbo">&nbsp;</td>
 </tr>
  <tr>
   <td bgcolor="#E6E6E6"  align="right" class="arbo">&nbsp;</td>
   <td bgcolor="#EEEEEE"  align="left" class="arbo">&nbsp;<small>(* champs obligatoires)</small></td>
 </tr>
 <tr>
   <td bgcolor="#E6E6E6"  align="right" class="arbo">&nbsp;</td>
   <td bgcolor="#EEEEEE"  align="left" class="arbo">&nbsp;</td>
 </tr>
 <tr>
  <td bgcolor="#D2D2D2" colspan="2"  align="center" class="arbo"><?php if ($_POST['urlRetour'] != "") { ?>
     <input name="button" type="button" class="arbo" onClick="retour()" value="<< Retour (annuler)">     &nbsp;&nbsp;&nbsp;&nbsp; <?php } ?>
     <input class="arbo" type="button" name="Ajouter" value="Suite (enregistrer) >>" onClick="validerForm()"></td>
 </tr>
</table>
<br><br>
<?php
} // FIN MODE EDITION
?>
</form>
<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoappend.php');
?>