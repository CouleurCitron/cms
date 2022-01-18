<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/gabarit.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/surveyGen.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


// include the control file "spaw"
//include DEF_SPAW_ROOT.'spaw_control.class.php';


// site sur lequel on est positionné
$idSite = $_SESSION['idSite_travail'];
$oSite = new Cms_site($idSite);


activateMenu('gestionpage');  //permet de dérouler le menu contextuellement


$_SESSION['pageIsSaved'] = false;

	/////////////////////////
	// VALEURS POSTEES //////

	$id = $_POST['idPage'];
	$idPage = $id;
	/////////////////////////

if ($idPage != "") { // MODIF PAGE

	// récupération de l'objet page
	$oPage = new Cms_page($idPage);

	$nomPage = $oPage->getName_page();
	$nodeidPage = $oPage->getNodeid_page();


	// récupération de l'objet gabarit s'il existe
	$oGabarit = getGabaritByPage($oPage) ;
	$idGab = $oGabarit->getId_page();

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

for ($a=0; $a<newSizeOf($aZonedit); $a++)
{
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

/*
// gabarit 
$_GET['idGab'])
// nombre de zone edit dans le gabarit 
newSizeOf($aZonedit))
// zones editables du gabarit
for ($p=0; $p<newSizeOf($aZonedit); $p++) print("<br />ID_CONTENT=>".$aZonedit[$p]->getId_Content());
// nouvelles briques vierges remplaçant les zone editables
for ($p=0; $p<newSizeOf($aIdBrique); $p++) print("<br />ID_CONTENT=>".$aIdBrique[$p]);
*/

$buffer=1024;   // buffer de lecture de 1Ko

$gabarit = getRepGabarit($idSite).$oGabarit->getName_page().".php";
$tmpfile = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.md5(uniqid(rand())).'.tmp.php';
dirExists("/tmp/");

$tmpfilehandle = fopen($tmpfile,'w') or (error_log("page_maj.php - soit ecriture impossible dans $tmpfile") && die('Erreur irrécupérable. Veuillez contacter l\'administrateur.'));
$gabfilehandle = fopen($gabarit,'r') or (error_log("page_maj.php - soit lecture impossible dans $gabarit") && die('Erreur irrécupérable. Veuillez contacter l\'administrateur.'));
?>

<script>
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

<script>

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
	function loadBrique(id, sHTML)
	{
		tab_be_to_save[id] = document.getElementById("be"+id).value; // enregistrement de l'id de la "brique éditable"
		document.getElementById("BeToSave").value = replace_all( tab_be_to_save.join(';'), ";;", ";" );
		document.getElementById("div"+id).innerHTML =  replace_all( sHTML, "`", "'" );
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
		document.getElementById("div"+id).innerHTML = replace_all( document.getElementById("htmlcontent"+id).value, "`", "'" );
	}
	
	// rééutilisation d'une brique existante à la place de la zone éditable
	function briqueExistante (id, idSite)
	{
		toggleComposant(id);
		if(confirm("Attention opération irréversible:"+"\n"+"Souhaitez-vous réellement réutiliser une brique existante?"+"\n"+"Le contenu de l'ancienne brique éditable sera perdu !")) {
			openBrWindow('../popup_arbo_browse.php?idSite='+idSite+'&idDiv='+id+'', '', 600, 400, 'scrollbars=yes', 'true');
		}
	}
	
</script>

<script type="text/javascript">document.title="Pages BB";</script><style type="text/css">
<!--
body, html {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>
<script src="<?php echo  $URL_ROOT; ?>/backoffice/cms/js/openBrWindow.js" type="text/javascript"></script>
<style>

.space {
	background-color: #ffffff;
}

</style>


<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<div class="arbo2"><strong>Assemblage de la page</strong></div>
<?php
// affichage du site de travail
putAfficheSite();
?>

<input name="H" type="hidden" id="H">
<input name="G" type="hidden" id="G" >

<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" class="arbo">
<tr>
	<td class="arbo" style="height:<?php echo $heightGab; ?>px;width=<?php echo $widthGab; ?>px">

<?php
$stop=0;
$contenu_par_defaut_a_suppr=0;
while((!feof($gabfilehandle)) && !$stop){
	$tampon=fgets($gabfilehandle, $buffer);

	fputs($tmpfilehandle, $tampon);
	
		
	// on cherche la chaine <!--DEBUTCMS;ID=
	if (preg_match('/<\!--DEBUTCMS;/', $tampon)) {
//		$stop=1;

		$aTampon=explode(";", $tampon);

		// découpage de la ligne en tableau pour extraire l'ID
		$id_div = "";
		for ($p=0; $p<newSizeOf($aTampon); $p++) {

			$ligneTampon = $aTampon[$p];
			
			if (substr($ligneTampon, 0, 2) == "ID") 
			{
				$aTampon2=explode("=", $ligneTampon);
				$id_div = $aTampon2[1];
			}
		}

		// récupération des objets Brique et Structure pour avoir les bonnes dimensions
		if($idPage == "") { // Mode Création
			$oContent = new Cms_content($id_div);
		}
		else { // Mode modification

			$oBrique = getOneObjetWithPageZonedit($idPage, $id_div);
			$oContent = new Cms_content($oBrique->id_content);
		}
		$oStruct = new Cms_struct_page();
		$oStruct->getObjet($idGab, $id_div);

		$zoneDiv.= '<input name="Width'.$id_div.'" id="Width'.$id_div.'" type="hidden" value="'.$oStruct->getWidth_content().'">';
		$zoneDiv.= '<input name="Height'.$id_div.'" id="Height'.$id_div.'" type="hidden" value="'.$oStruct->getHeight_content().'">';
		$zoneDiv.= '<input name="Opacity'.$id_div.'" id="Opacity'.$id_div.'" type="hidden" value="'.$oStruct->getOpacity_content().'">'."\n";
		$zoneDiv.= '<input name="Index'.$id_div.'" id="Index'.$id_div.'" type="hidden" value="'.$oStruct->getZindex_content().'">'."\n";
		$zoneDiv.= '<input name="Top'.$id_div.'" id="Top'.$id_div.'" type="hidden" value="'.$oStruct->getTop_content().'">';
		$zoneDiv.= '<input name="Left'.$id_div.'" id="Left'.$id_div.'" type="hidden" value="'.$oStruct->getLeft_content().'">';
		$zoneDiv.= $oContent->getHtml_content();

		$tampon = $zoneDiv;
		$zoneDiv = "";

		//while((!feof($gabfilehandle)) && !preg_match('/<\!--FINCMS;ID='.$id_div.'/', $tampon_temp)){
			//$tampon_temp=fgets($gabfilehandle, $buffer);
		//}
		//if(preg_match('/<\!--FINCMS;ID='.$id_div.'/', $tampon_temp))
			//$tampon.="\n".$tampon_temp;
		
		fputs($tmpfilehandle, $tampon);
	}
}

while(!feof($gabfilehandle)) {
	$tampon=fgets($gabfilehandle, $buffer);
	fputs($tmpfilehandle,$tampon);
}

fclose($tmpfilehandle);
include "$tmpfile";
unlink($tmpfile);

?>&nbsp;
</td>
</tr>
<tr>
	<td>
	
	<form name="assemble" method="post" action="page_sauve.php?id=<?php echo $id; ?>&idGab=<?php echo $idGab; ?>">

<input type="hidden" name="nomPage" id="nomPage" value="<?php echo $nomPage; ?>">
<input type="hidden" name="nodeidPage" id="nodeidPage" value="<?php echo $nodeidPage; ?>">

  <table border="1" align="center" cellpadding="3" cellspacing="0" bordercolor="#d2d2d2" class="arbo" width="100%">
  <tr>
     <td bgcolor="E7E7E7" class="arbo">&nbsp;Gestion des zones editables&nbsp;</td>
  </tr>
<?php
// pour les zone editables de la page
for ($p=0; $p<newSizeOf($aZonedit); $p++) {
	
	$oZonedit = $aZonedit[$p];
	$id_div = $oZonedit->getId_content();
	$id_newdiv_edit = $aIdBrique[$p]["be"];
	$id_newdiv_reut = $aIdBrique[$p]["bex"];
	

?>
	<tr>
		<td>
<a href="javascript:toggleComposant(<?php echo $id_div; ?>);" class="arbo">contenu n° <?php echo $p+1; ?></a>&nbsp;
<input type="button" name="btBe<?php echo $id_div; ?>" value="contenu" onClick="javascript:putZonedit(<?php echo $id_div; ?>)" class="arbo"> &nbsp;
<input type="button" name="btBex<?php echo $id_div; ?>" value="réutiliser brique existante" onClick="javascript:briqueExistante(<?php echo $id_div; ?>, <?php echo $idSite; ?>)" class="arbo">

<?php
// pour chaque zone editable nous avons :
// 1- le n° de la zone editable :: champ ze
// 2- le n° de la brique editable (nouvelle brique créée ici) :: champ be
// 3- le n° de la brique existante (réutilisée ici dans cette zone editable) :: champ bex
// 4- le n° de la zone éditable de laquelle on veut récupérer le HTML :: champ bdef
// 5- le source HTML (Utilisé??)
?>
<input type="hidden" name="ze<?php echo $id_div; ?>" value="<?php echo $id_div; ?>">
<input type="hidden" name="be<?php echo $id_div; ?>" value="<?php echo $id_newdiv_edit; ?>">
<input type="hidden" name="bex<?php echo $id_div; ?>" value="<?php echo $id_newdiv_reut; ?>">
<input type="hidden" name="bdef<?php echo $id_div; ?>" value="">

<input type="hidden" name="htmlcontent<?php echo $id_div; ?>" value='<?php echo  str_replace( "'", "`", $oZonedit->getHtml_content() ) ; ?>'>
<br />
<?php
// titre du contenu
// affectation du contenu
// date à laquelle ce contenu doit être rédigé
?>
nom <input type="text" class="arbo" size="30" name="sName_<?php echo $id_div; ?>" id="sName_<?php echo $id_div; ?>">
affectation <select class="arbo">
	<option value="-1">-- sélectionnez un contributeur --</option>
</select>
date <input type="text" class="arbo" size="12" name="sDate_<?php echo $id_div; ?>" id="sdate_<?php echo $id_div; ?>"> 
		</td>
	</tr>
<?php

} // fin for

?>
	<tr>
		<td>
			<input name="button" type="button" class="arbo" onClick="javascript:saveAll();" value="Sauver tout >>">
			<input type="hidden" id="serializedData" name="serializedData" value="">
			<input type="hidden" id="BeToSave" name="BeToSave" value="">
		</td>
	</tr>
	</table></form>
    </td>
  </tr>
</table><script type="text/javascript">
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

<script>
	var divArray = new Array();

	var currentDivStyle = null;
	var leftX=document.getElementById("G");
	var topY=document.getElementById("H");
	var step=document.getElementById("pxStep");

	function updateSize(coord, nameId, obj) {
		toggleComposant(nameId);
		eval("currentDivStyle."+coord+"='"+obj.value+"px'");
	}
	
	function updateOpacity(nameId,obj) {
		toggleComposant(nameId);
		mozTaux = obj.value/100;
		currentDivStyle.filter='alpha(opacity='+obj.value+')';
		currentDivStyle.MozOpacity=obj.value/100;
	}

	function updateIndex(nameId,obj) {
		toggleComposant(nameId);
		currentDivStyle.zIndex=obj.value;
	}

	function toggleComposant(nameId) {
		if(currentDivStyle!=null) {
			currentDivStyle.background="#ffffff";
			currentDivStyle.borderStyle="dotted";
			currentDivStyle.borderWidth="1px";
		}
		currentDivStyle = document.getElementById("div"+nameId).style;
		currentDivStyle.background="#dddddd";
		currentDivStyle.visibility="visible";
		currentDivStyle.borderStyle="dotted";
		currentDivStyle.borderWidth="1px";
		updateFieldsXY();
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

				tmpDiv = document.getElementById("div"+divArray[i]);
			
				tmpDivStyle = tmpDiv.style;

				// choix de la brique qui va remplacer la zone editable
				// 1- une nouvelle brique editable créée pour l'occasion au début de cette page
				// 2- une brique déjà existante
				ze = document.getElementById("ze"+divArray[i]).value;
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

<script>

// Hachure le gabarit par défaut
document.getElementById('divhachures').style.display='';

<?php
// pour les zone editables de la page
for ($p=0; $p<newSizeOf($aZonedit); $p++) {
	
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
</script><?php
// Evite aux contenu des zones éditables d'avoir des assenceurs
?><style>
DIV.space DIV.content DIV DIV { top:0px !important; left:0px !important }
</style>