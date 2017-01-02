<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 
sponthus 14/06/05
génération de page avec le mécanisme gabarit briques
pas de choix de briques lors de la création des pages

sponthus 01/07/05
à la création de la page, le gabarit (ou modèle de page) 
a déjà prédécoupé des zones editables dans lesquelles 
on place de nouvelles briques ou des briques déjà existantes

*/

// include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php'); semble inutile
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/gabarit.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
//include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/surveyGen.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


// include the control file "spaw"
//include DEF_SPAW_ROOT.'spaw_control.class.php';


// site sur lequel on est positionné
$idSite = $_SESSION['idSite_travail'];
$oSite = new Cms_site($idSite);

activateMenu('gestionpage');  //permet de dérouler le menu contextuellement

// modif d'une page (si pas id => créa)
$idPage = $_GET['id'];

// gabarit de la page
$idGab = $_GET['idGab'];
$oGab = new Cms_page($idGab);

$sTitre = "Créer";

if ($idPage != "") { // MODIF PAGE

	// récupération de l'objet page
	$oPage = new Cms_page($idPage);
	
	$_GET['idThe'] = $oPage->get_theme();

	// récupération de l'objet gabarit s'il existe
	$oGabarit = getGabaritByPage($oPage) ;
	$idGab = $oGabarit->getId_page();

	$sTitre = "Modifier";

} else {
	$oGabarit = new Cms_page($idGab);
}

// si gabarit de la page non trouvé
// arrêt d'affichage de la page :: message d'erreur
if ($idGab == "") {
	$sMessage = "<div align='left' class='arbo'>Erreur de fonctionnement interne</div>";
	die($sMessage);
}

// hauteur et largeur du gabarit 
// pour bien afficher la page et les boutons en dessous

$widthGab = $oGabarit->getWidth_page();
$heightGab = $oGabarit->getHeight_page();


// à l'arrivée dans la page on créée autant de briques vierges que de zone editable
// si une brique existante et non une brique vierge est utilisée dans une zone editable 
// alors les briques inutilement créées partiront à la purge
$aZonedit = getContentFromPage($idGab, 1);

if((sizeof($aZonedit) == 0)	|| ($aZonedit===false)) {
	// skip
	echo '<script type="text/javascript">document.location.href="pageLiteEditor4.php?idGab='.$_GET['idGab'].'&idThe='.$_GET['idThe'].'&idSite='.$idSite.'";</script>';
}
else{

for ($a=0; $a<sizeof($aZonedit); $a++){
	$oZonedit = $aZonedit[$a];


	if ($idPage == "") { // MODE CREATION DE PAGE
	
		// clé de la zone éditable créée
		$aIdBrique[$a]["be"] = crea_brique_defo($oZonedit);
		$aIdBrique[$a]["bex"] = "";
		
	}
	else { // MODE MODIFICATION DE PAGE
	
		// obtention d'un objet cms_struct à partir de l'id de la zone editable
		$oBrique = getOneObjetWithPageZonedit($idPage, $oZonedit->getId_content());
	
		// Attention il est possible d'avoir une nouvelle zone éditable (rajouté en modif dans le gabarit)
		if(! $oBrique ) { 
			$aIdBrique[$a]["be"] = crea_brique_defo($oZonedit);
			$aIdBrique[$a]["bex"] = "";
		}
		else {
			if( $oBrique->getIsbriquedit_content()) {
				$aIdBrique[$a]["be"] = $oBrique->getId_content();
				$aIdBrique[$a]["bex"] = "";
			}
			else {	// si on a pas a faire à une brique éditable
					// on créé une brique éditable par défaut au cas où
					// pour pouvoir basculer en mode brique éditable
				$aIdBrique[$a]["be"] = crea_brique_defo($oZonedit);
				$aIdBrique[$a]["bex"] = $oBrique->getId_content();
			}
		}
	}

}


$buffer=1024;   // buffer de lecture de 1Ko

?>
<script type="text/javascript">
	temp1=0;
	temp2=0;
	myX=0;
	myY=0;
	myBrowser=navigator.appName
	myVersion=navigator.appVersion
	var version45=(myVersion.indexOf("4.")!=-1||myVersion.indexOf("5.")!=-1)
	var NS=(myBrowser.indexOf("Netscape")!=-1 && version45)
	var IE=(myBrowser.indexOf("Explorer")!=-1 && version45)
	if(!NS&&!IE)alert("This file is intended to run under Netscape versions 4.0+ or Internet Explorer versions 4.0+, and may not run properly on earlier versions")
	if(IE){var hide="hidden"; var show="visible"}
	if(NS){var hide="hide"; var show="show"}

	var divArray = new Array();
	var dragapproved=false;
	var z,x,y,t;
	z=null;
</script>
<script type="text/javascript">

var tab_be_to_save = Array(); // Tableau d'id contenant les ID des briques éditables à mémoriser

function replace_all(sChaine,sOld,sNew){
var i = sChaine.indexOf(sOld);
while(i > -1){
	sChaine = sChaine.replace(sOld, sNew);
	i = sChaine.indexOf(sOld);
}
return sChaine;
} 

	// Chargement d'une brique ré-utilisée dont le html est passé en paramètre
	// la fonction est appelée dans la popup de choix de brique
	function loadBrique(nomdiv, id, sHTML)
	{ 
		tab_be_to_save[id] = document.getElementById("be"+id).value; // enregistrement de l'id de la "brique éditable"
		document.getElementById("BeToSave").value = replace_all( tab_be_to_save.join(';'), ";;", ";" );
		document.getElementById(nomdiv+id).innerHTML =  replace_all( sHTML, "`", "'" );
		document.getElementById("bdef"+id).value = "";
		// document.getElementById("htmlcontent"+id).value
	}
	
	// sélection de la zone éditable comme nouvelle brique
	// vidage de l'id dans "nouvelle brique"
	function putZonedit (id)
	{
		tab_be_to_save[id] = ''; // effacement de l'id de la "brique éditable"
		document.getElementById("BeToSave").value = replace_all( tab_be_to_save.join(';'), ";;", ";" );
		toggleComposant(id);
		document.getElementById("bex"+id).value = "";
		document.getElementById("bdef"+id).value = document.getElementById("be"+id).value;

		// les briques peuvent etre div, bf ou ze
		if (eval(document.getElementById("div"+id)) != null){
			document.getElementById("div"+id).innerHTML = replace_all( document.getElementById("htmlcontent"+id).value, "`", "'" );
		}
		else if (eval(document.getElementById("bf"+id)) != null){
			document.getElementById("bf"+id).innerHTML = replace_all( document.getElementById("htmlcontent"+id).value, "`", "'" );
		}
		else if (eval(document.getElementById("ze"+id)) != null){
			document.getElementById("ze"+id).innerHTML = replace_all( document.getElementById("htmlcontent"+id).value, "`", "'" );
		}
	}
	
	// rééutilisation d'une brique existante à la place de la zone éditable
	function briqueExistante (id, idSite)
	{
		toggleComposant(id);
		if(confirm("Attention opération irréversible:"+"\n"+"Souhaitez-vous réellement réutiliser une brique existante?"+"\n"+"Le contenu de l'ancienne brique éditable sera perdu !")) {
			openBrWindow('/backoffice/cms/popup_arbo_browse.php?idSite='+idSite+'&idDiv='+id+'', '', 600, 400, 'scrollbars=yes', 'true');
		}
	}
	// rééutilisation d'une brique existante à la place de la zone éditable
	function zoneeditExistante (id, idSite)
	{
		toggleComposant(id);
		if(confirm("Attention opération irréversible:"+"\n"+"Souhaitez-vous réellement réutiliser un contenu existant?"+"\n"+"Le contenu de l'ancienne brique éditable sera perdu !")) {
			openBrWindow('/backoffice/cms/popup_arbo_browse_edits.php?idSite='+idSite+'&idDiv='+id+'', '', 600, 400, 'scrollbars=yes', 'true');
		}
	}
</script>
<style type="text/css">
<!--
body, html {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.space {
	background-color: #ffffff;
}
-->
</style>
<script src="<?php echo  $URL_ROOT; ?>/backoffice/cms/js/openBrWindow.js" type="text/javascript"></script>
<script src="<?php echo  $URL_ROOT; ?>/backoffice/cms/js/fojsutils.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<div class="ariane"><span class="arbo2">PAGE > </span><span class="arbo3"><?php echo $sTitre; ?> une page >&nbsp;
Etape 2 : affectation des contenus</span></div>
<?php
// site de travail
//if (DEF_MENUS_MINISITES == "ON") print(putAfficheSite());
?>

<input name="H" type="hidden" id="H">
<input name="G" type="hidden" id="G" >
<?php
//--------------------------------------------
// sponthus 09/11/05
// IMPORTANT
//--------------------------------------------

// la taille de l'espace de travail devrait être celle du gabarit (ou plutôt du supra gabarit)
// ici on ne prend que la taille du content (taile de la zone de scroll)

// il faudrait donner en plus la vraie taille du supra gabarit à reporter ici pour l'espace de travail de construction de la page

// en attendant, on met abusivement ici "600", ce qui couvre l'ensemble des besoins
// d'autant plus que l'affectation des supra gabarits n'est pas interfacée
//--------------------------------------------
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" class="arbo" id="pageLiteEdito3">
<tr>
	<td class="arbo" style="width:<?php echo $widthGab; ?>px">

<?php
$stop=0;
$contenu_par_defaut_a_suppr=0;

$tampon = $oGab->getHtml_page();

	
// on cherche la chaine <!--DEBUTCMS;ID=
if (preg_match('/<\!--DEBUTCMS;/', $tampon)) {
	//		$stop=1;
	$aTampon=split(";", $tampon);
	// découpage de la ligne en tableau pour extraire l'ID
	$id_div = "";
	$idDivArray = array();
	for ($p=0; $p<sizeof($aTampon); $p++) {

		$ligneTampon = $aTampon[$p];
		//echo "---".$ligneTampon."---".substr($ligneTampon, 0, 2)."----<br>";
		if (substr($ligneTampon, 0, 2) == "ID") 
		{
			$aTampon2=split("=", $ligneTampon);
			$id_div = $aTampon2[1];
			if (!in_array ($id_div, $idDivArray)) {
				$idDivArray[] = $id_div;
			}
		}
	}
	$tampon2 = "";
	for ($a=0; $a<sizeof($idDivArray); $a++) {
	// récupération des objets Brique et Structure pour avoir les bonnes dimensions
		$id_div = $idDivArray[$a];
		if($idPage == "") { // Mode Création
			$oContent = new Cms_content($id_div);
		}
		else { // Mode modification
			$oBrique = getOneObjetWithPageZonedit($idPage, $id_div);
			$oContent = new Cms_content($oBrique->id_content);
		}
		$oStruct = new Cms_struct_page();
		$oStruct->getObjet($idGab, $id_div);

		$zoneDiv.= '<input name="Width'.$id_div.'" id="Width'.$id_div.'" type="hidden" value="'.$oStruct->getWidth_content().'" />'."\n";
		$zoneDiv.= '<input name="Height'.$id_div.'" id="Height'.$id_div.'" type="hidden" value="'.$oStruct->getHeight_content().'" />'."\n";
		$zoneDiv.= '<input name="Opacity'.$id_div.'" id="Opacity'.$id_div.'" type="hidden" value="'.$oStruct->getOpacity_content().'" />'."\n";
		$zoneDiv.= '<input name="Index'.$id_div.'" id="Index'.$id_div.'" type="hidden" value="'.$oStruct->getZindex_content().'" />'."\n"."\n";
		$zoneDiv.= '<input name="Top'.$id_div.'" id="Top'.$id_div.'" type="hidden" value="'.$oStruct->getTop_content().'" />'."\n";
		$zoneDiv.= '<input name="Left'.$id_div.'" id="Left'.$id_div.'" type="hidden" value="'.$oStruct->getLeft_content().'" />'."\n";
		$zoneDiv.= $oContent->getHtml_content();
		
		// supprime la redirection lors de l'affichage
		if (ereg ("window.location.href",$zoneDiv) ) {
			$zoneDiv = str_replace("window.location.href", "//window.location.href", $zoneDiv);
		}
		$tampon2.= $zoneDiv;
		$zoneDiv = "";
		
	}

}


echo phpsrcEval($oGab->getHtml_page().$tampon2);
?>&nbsp;
</td>
</tr>
<tr>
	<td>
	
	<form name="assemble" id="assemble" method="post" action="pageLiteEditor4.php?id=<?php echo $_GET['id']; ?>&idGab=<?php echo $_GET['idGab']?>&idThe=<?php echo $_GET['idThe']?>">

  <table border="1" align="center" cellpadding="3" cellspacing="0" bordercolor="#d2d2d2" class="arbo" width="100%">
  <tr>
     <td bgcolor="E7E7E7" class="arbo">&nbsp;Gestion des zones editables&nbsp;</td>
  </tr>
<?php
// pour les zone editables de la page
for ($p=0; $p<sizeof($aZonedit); $p++) {
	
	$oZonedit = $aZonedit[$p];
	$id_div = $oZonedit->getId_content();
	$id_newdiv_edit = $aIdBrique[$p]["be"];
	$id_newdiv_reut = $aIdBrique[$p]["bex"];
	

?>
	<tr>
		<td>
<a href="javascript:toggleComposant(<?php echo $id_div; ?>);" class="arbo">contenu n° <?php echo $p+1; ?></a>&nbsp;
<input type="button" name="btBe<?php echo $id_div; ?>" value="contenu" oncick="javascript:putZonedit(<?php echo $id_div; ?>)" class="arbo" /> &nbsp;
<input type="button" name="btBex<?php echo $id_div; ?>" value="réutiliser brique existante" onclick="javascript:briqueExistante(<?php echo $id_div; ?>, <?php echo $idSite; ?>)" class="arbo" />
<input type="button" name="btZex<?php echo $id_div; ?>" value="réutiliser contenu existant" onclick="javascript:zoneeditExistante(<?php echo $id_div; ?>, <?php echo $idSite; ?>)" class="arbo" />
<?php
// sponthus 18/11/05
// les div zones editables s'appellent ze
// le champ caché numéro de la zone editable s'appelle id_ze

// pour chaque zone editable nous avons :
// 1- le n° de la zone editable :: champ id_ze
// 2- le n° de la brique editable (nouvelle brique créée ici) :: champ be
// 3- le n° de la brique existante (réutilisée ici dans cette zone editable) :: champ bex
// 4- le n° de la zone éditable de laquelle on veut récupérer le HTML :: champ bdef
// 5- le source HTML (Utilisé??)
?>
<input type="hidden" name="id_ze<?php echo $id_div; ?>" id="id_ze<?php echo $id_div; ?>" value="<?php echo $id_div; ?>" />
<input type="hidden" name="ze<?php echo $id_div; ?>" id="ze<?php echo $id_div; ?>" value="<?php echo $id_div; ?>" />
<input type="hidden" name="be<?php echo $id_div; ?>" id="be<?php echo $id_div; ?>" value="<?php echo $id_newdiv_edit; ?>" />
<input type="hidden" name="bex<?php echo $id_div; ?>" id="bex<?php echo $id_div; ?>" value="<?php echo $id_newdiv_reut; ?>" />
<input type="hidden" name="bdef<?php echo $id_div; ?>" id="bdef<?php echo $id_div; ?>" value="" />
<input type="hidden" name="htmlcontent<?php echo $id_div; ?>" id="htmlcontent<?php echo $id_div; ?>" value='<?php echo  str_replace( "'", "`", $oZonedit->getHtml_content() ) ; ?>' />
&nbsp;
<!--<select class="arbo">
	<option value="-1">-- sélectionnez un contributeur --</option>
</select>-->
		</td>
	</tr>
<?php

} // fin for

?>
	<tr>
		<td>
			<input name="button" id="button" type="button" class="arbo" onclick="javascript:saveAll();" value="Sauver tout >>" />
			<input type="hidden" id="serializedData" name="serializedData" value="" />
			<input type="hidden" id="BeToSave" name="BeToSave" value="" />
		</td>
	</tr>
	</table></form>
    </td>
  </tr>
</table>
<script type="text/javascript">
<!--
	function move(e) {
		if(!e) { var e=window.event }
		if (dragapproved) // mareche pas sous moz?? e.button==1 && 
		{
			myX = temp1+e.clientX-x;
			t.left= myX +'px';
			myY = temp2+e.clientY-y;
			t.top= myY + 'px';
			return false
		}
	}
	
	function drags(e)
	{ if(!e) { var e=window.event }
		var testbondiv=(e.target)?e.target:e.srcElement
		if (testbondiv.className=='dragDiv')
		{ 
			dragapproved=true;
			z=testbondiv;
			x=e.clientX;
			y=e.clientY; 
			if(document.captureEvents) document.captureEvents(Event.MOUSEMOVE);
			document.onmousemove=move;
		}
	}

	function infos() {
		dragapproved=false;
		temp1 = myX;
		temp2 = myY;
		if(document.captureEvents) document.releaseEvents(Event.MOUSEMOVE);
	}

	if(IE) {
		document.onmousedown=drags
		document.onmouseup=infos
	}

-->
</script>
<script type="text/javascript">
	var divArray = new Array();

	var currentDivStyle = null;
	var leftX=document.getElementById("G");
	var topY=document.getElementById("H");
	var step=document.getElementById("pxStep");

	function updateSize(coord, nameId, obj) {
		toggleComposant(nameId);
		if(currentDivStyle!=null) eval("currentDivStyle."+coord+"='"+obj.value+"px'");
	}
	
	function updateOpacity(nameId,obj) {
		toggleComposant(nameId);
		mozTaux = obj.value/100;
		if(currentDivStyle!=null) currentDivStyle.filter='alpha(opacity='+obj.value+')';
		if(currentDivStyle!=null) currentDivStyle.MozOpacity=obj.value/100;
	}

	function updateIndex(nameId,obj) {
		toggleComposant(nameId);
		if(currentDivStyle!=null) currentDivStyle.zIndex=obj.value;
	}

	function toggleComposant(nameId) {
		if(currentDivStyle!=null) {
			currentDivStyle.background="#ffffff";
			currentDivStyle.borderStyle="dotted";
			currentDivStyle.borderWidth="1px";
		}
		// les briques peuvent etre div, bf ou ze
		if (eval(document.getElementById("div"+nameId)) != null){
			currentDivStyle = document.getElementById("div"+nameId).style;
		}
		else if (eval(document.getElementById("bf"+nameId)) != null){
			currentDivStyle = document.getElementById("bf"+nameId).style;
		}
		else if (eval(document.getElementById("ze"+nameId)) != null){
			currentDivStyle = document.getElementById("ze"+nameId).style;
		}
		//currentDivStyle = document.getElementById("div"+nameId).style;
		/*if(currentDivStyle!=null)currentDivStyle.background="#dddddd";
		if(currentDivStyle!=null)currentDivStyle.visibility="visible";
		if(currentDivStyle!=null)currentDivStyle.borderStyle="dotted";
		if(currentDivStyle!=null)currentDivStyle.borderWidth="1px";
		updateFieldsXY();*/
	}

	function cleanPosString(str) {
		return str.replace(/px/i,'');
	}
	
	function updateFieldsXY() {
		if(currentDivStyle==null) {
			window.alert("Veuillez sélectionner un composant à positionner");
			return;
		}
		tmpX=currentDivStyle.left;
		tmpY=currentDivStyle.top;
		leftX.value=cleanPosString(tmpX);
		topY.value=cleanPosString(tmpY);
	}

	function updateDivPosition() {
		if(currentDivStyle==null) {
			window.alert("Veuillez sélectionner un composant à positionner");
			return;
		}
		currentDivStyle.left=leftX.value+'px';
		currentDivStyle.top=topY.value+'px';
		height = currentDivStyle.height + currentDivStyle.top;
		globalDivContent = document.getElementById('content').style;
		myH = globalDivContent.height;
	}
	

		
	function saveAll() 
	{
			sf = document.getElementById("serializedData");
			isOk = true;
			sf.value='';

			for(i=0;i<divArray.length;i++) {

				// le div array envoyé via cette page est composé des zones editables uniquement
				// donc nommées ze et non div

				tmpDiv = document.getElementById("ze"+divArray[i]);
			
				tmpDivStyle = tmpDiv.style;

				// choix de la brique qui va remplacer la zone editable
				// 1- une nouvelle brique editable créée pour l'occasion au début de cette page
				// 2- une brique déjà existante
				ze = document.getElementById("id_ze"+divArray[i]).value;
				be = document.getElementById("be"+divArray[i]).value;
				bex = document.getElementById("bex"+divArray[i]).value;
				bdef = document.getElementById("bdef"+divArray[i]).value;

				if (bex != "") newBrique = bex;
				else newBrique = be;

				sf.value = sf.value + '#' + newBrique + '#';
				sf.value = sf.value + 'top='          + tmpDivStyle.top          + ',';
				sf.value = sf.value + 'left='         + tmpDivStyle.left         + ',';
				sf.value = sf.value + 'width='        + tmpDivStyle.width        + ',';
				sf.value = sf.value + 'height='       + tmpDivStyle.height       + ',';
				sf.value = sf.value + 'filter='       + tmpDivStyle.filter       + ',';
				sf.value = sf.value + '-moz-opacity='  + tmpDivStyle.MozOpacity + ',';
				sf.value = sf.value + 'zIndex='        + tmpDivStyle.zIndex       + ',';
				// sponthus 06/07/2005
				// ajout de l'info zone editable
				// car maintenant une brique de contenu est posée dans une zone éditable
				// les briques de gabarit ne sont pas elles créées dans des zones editables
				sf.value = sf.value + 'zonedit='        + ze + ',';
				
				// on a à remplir un contenu HTML par défaut à partir de la brique (zone éditable) spécifiée
				if(bdef!="") bdef=ze;
				sf.value = sf.value + 'HTMLdefaut='        + bdef + ',';

				sf.value = sf.value + '#' + newBrique + '#';
				
				if (tmpDivStyle.visibility == 'hidden') {
					isOk = false;
				}
			}

			if (isOk){

				//alert(sf.value);

				document.assemble.submit();
			} else
				window.alert('Vous n\'avez pas positionné toutes les briques');
	}

</script>
<script type="text/javascript">

// Hachure le gabarit par défaut
document.getElementById('divhachures').style.display='';

<?php
// pour les zone editables de la page
for ($p=0; $p<sizeof($aZonedit); $p++) {
	
	$oZonedit = $aZonedit[$p];
	$eId = $oZonedit->getId_content();
?>

	toggleComposant("<?php echo $eId;?>");

	field = document.getElementById('Width<?php echo $eId;?>');
	updateSize('width', '<?php echo $eId; ?>', field);

	field = document.getElementById('Height<?php echo $eId; ?>');
	updateSize('height','<?php echo $eId; ?>', field);

	field = document.getElementById('Top<?php echo $eId; ?>');
	updateSize('top','<?php echo $eId; ?>', field);

	field = document.getElementById('Left<?php echo $eId; ?>');
	updateSize('left','<?php echo $eId; ?>', field);		

	field = document.getElementById('Opacity<?php echo $eId; ?>');
	updateOpacity('<?php echo $eId; ?>', field);

	field = document.getElementById('Index<?php echo $eId; ?>');
	if(field.value<200) field.value+=200; // Passe les zones éditables au dessus des hachures si pas deja fait
	updateIndex('<?php echo $eId; ?>', field);

	divArray.push('<?php echo $eId; ?>');
<?php
}
?>

</script>
<?php
}// si on a des ZE 
?>
<style>
DIV.space DIV.content DIV DIV { top:0px !important; left:0px !important }
</style>
<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoappend.php');
?>