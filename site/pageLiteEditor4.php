<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Author: pierre $
	$Id: pageLiteEditor4.php,v 1.6 2013-03-01 10:28:20 pierre Exp $

	$Log: pageLiteEditor4.php,v $
	Revision 1.6  2013-03-01 10:28:20  pierre
	*** empty log message ***

	Revision 1.5  2012-07-31 14:24:50  pierre
	*** empty log message ***

	Revision 1.4  2012-04-13 07:18:38  pierre
	*** empty log message ***

	Revision 1.3  2012-02-08 16:02:26  pierre
	*** empty log message ***

	Revision 1.2  2010-08-25 16:09:23  pierre
	cms_theme

	Revision 1.1  2010-08-23 08:29:52  pierre
	*** empty log message ***

	Revision 1.12  2009-09-24 08:53:14  pierre
	*** empty log message ***

	Revision 1.11  2009-03-03 10:32:54  thao
	*** empty log message ***

	Revision 1.10  2009-03-02 13:46:39  thao
	*** empty log message ***

	Revision 1.9  2009-03-02 11:26:50  thao
	*** empty log message ***

	Revision 1.8  2008-11-28 14:09:56  pierre
	*** empty log message ***

	Revision 1.7  2008-11-27 11:34:35  pierre
	*** empty log message ***

	Revision 1.6  2008-11-06 12:03:54  pierre
	*** empty log message ***

	Revision 1.5  2008-07-16 10:48:36  pierre
	*** empty log message ***

	Revision 1.4  2007/11/26 16:58:27  thao
	*** empty log message ***
	
	Revision 1.3  2007/11/16 13:57:28  remy
	*** empty log message ***
	
	Revision 1.2  2007/08/28 15:55:28  pierre
	*** empty log message ***
	
	Revision 1.1  2007/08/08 14:26:26  thao
	*** empty log message ***
	
	Revision 1.4  2007/08/08 14:16:15  thao
	*** empty log message ***
	
	Revision 1.3  2007/08/08 13:56:04  thao
	*** empty log message ***
	
	Revision 1.2  2007/03/27 15:10:39  pierre
	*** empty log message ***
	
	Revision 1.1  2006/12/15 12:30:11  pierre
	*** empty log message ***
	
	Revision 1.1.1.1  2006/01/25 15:14:32  pierre
	projet CCitron AWS 2006 Nouveau Website
	
	Revision 1.3  2005/12/20 09:12:10  sylvie
	application des styles
	
	Revision 1.2  2005/10/28 07:53:14  sylvie
	*** empty log message ***
	
	Revision 1.1.1.1  2005/10/20 13:10:53  pierre
	Espace V2
	
	Revision 1.4  2005/06/07 12:16:20  michael
	Créa/ajout page
	Enlève message d'écrasement de page si on est en modif
	
	Revision 1.3  2005/06/07 10:01:15  michael
	Ajout d'un message "voulez-vous écraser la page..."
	
	Revision 1.2  2005/06/07 07:42:43  michael
	Vive les espaces!
	
	Revision 1.1.1.1  2005/04/18 13:53:29  pierre
	again
	
	Revision 1.1.1.1  2005/04/18 09:04:21  pierre
	oremip new
	
	Revision 1.1.1.1  2004/11/03 13:49:54  ddinside
	lancement du projet - import de adequat
	
	Revision 1.3  2004/06/16 15:23:19  ddinside
	inclusion corrections
	
	Revision 1.2  2004/04/26 08:07:09  melanie
	*** empty log message ***
	
	Revision 1.1.1.1  2004/04/01 09:20:27  ddinside
	Création du projet CMS Couleur Citron nom de code : tipunch
	
	Revision 1.6  2004/02/05 15:56:25  ddinside
	ajout fonctionnalite de suppression de pages
	ajout des styles dans spaw
	debuggauge prob du nom de fichier limite à 30 caracteres
	
	Revision 1.5  2004/01/27 12:12:50  ddinside
	application de dos2unix sur les scripts SQL
	modification des scripts d'ajout pour correction bug lors d'une modif de page
	ajout foncitonnalité de modification de page
	ajout visu d'une page si créée
	
	Revision 1.4  2004/01/20 15:16:38  ddinside
	mise à jour de plein de choses
	ajout de gabarit vie des quartiers
	eclatement gabarits par des includes pour contourner prob des flashs non finalisés
	
	Revision 1.3  2004/01/07 18:27:37  ddinside
	première mise à niveau pour plein de choses
	
	Revision 1.2  2003/11/26 13:08:42  ddinside
	nettoyage des fichiers temporaires commités par erreur
	ajout config spaw
	corrections bug menu et positionnement des divs
	
*/
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
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
	
	// gabarit
	$oGab = new Cms_page($_GET['idGab']);

	// répertoire des gabarits pour ce site
	$DIR_GABARITS = getRepGabarit($idSite);

$_SESSION['divArray']=null;	
$divArray = array();
$datatxt = $_POST['serializedData'];

foreach (split('##', $datatxt) as $k => $v ) {
	if ($k==0)
		$v = preg_replace('/^#/','',$v);
	if ($k==(sizeof(split('##', $datatxt))-1))
		$v = preg_replace('/#$/','',$v);
	$t = split('#',$v);
	$id = $t[0];
	$attr = $t[1];
	$tmparray = array(
		'id' => $id
	);
	foreach(split(',',$attr) as $kk => $vv) {
		$t = split('=',$vv);
		if(strlen($t[0]) >0)
			$tmparray[$t[0]] = $t[1];
		if(strlen($t[2]) >0)
			$tmparray[$t[0]] = $t[1].'='.$t[2];
	}
	$divArray[$k] = $tmparray;
}
$divArray['gabarit'] = $oGab->getId_page();
$divArray['valid'] = 1;
$datas=serialize($divArray);
$_SESSION['page']=$datas;

$array = array();
foreach($divArray as $k => $v) {
	if(is_array($v))
		$array[$v['id']] = $v;
}
$_SESSION['divArray'] = $array;

$disabled="";
$foldername="Racine";
$name="";
$folder_id="0";

$sTitre = "Créer";

if($_GET['id'] > 0) {
	$pageInfos = getPageById($_GET['id']);
	$name=$pageInfos['name'];
	$folder_id=$pageInfos['node_id'];
	$foldername=$pageInfos['libelle'];
	$disabled="disabled";

$sTitre = "Modifier";
}


function drawFileInPath($idSite, $VPath) {
// Les chemin absolu + pages du dossier en cours
// $VPath = Virtual path (0,15,85)
	$contenus = getFolderPages($idSite, $VPath);
	$node_id = preg_replace("/(.*,)/","",$VPath);
	$retour="";
	if((is_array($contenus)) && (sizeof($contenus)!=0)) {

		foreach ($contenus as $k => $page) {
	$retour.='	tab_file[tab_file.length] = "'. $node_id ."§". str_replace('"','\"', str_replace('.php','',$page['name'])) .'";'."\n";
		}
	}
	return $retour;
}

function DrawRecursDir($idSite, $VPath) {
	global $db;
	$current_path = getNodeInfos($db,$VPath);
	$childs = getNodeChildren($idSite, $db, $VPath);
	$htmlchilds = '';
	if (count($childs)!=0) {
		// Il existe encore des sous répertoires
		// => on relance la fonction pour chaque enfant
		foreach($childs as $key => $child) {
			$htmlchilds .= DrawRecursDir($idSite, $VPath.",".$child['id']);
		}
	}
	return drawFileInPath($idSite, $VPath) . $htmlchilds;
}

?>
<script type="text/javascript">
function okfile() { // Vérifie que le fichier n'existe pas déjà
	var tab_file = new Array();
<?php if($_GET['id']=="") // Si on est en créa et pas en modif!
	echo DrawRecursDir($idSite, "0"); ?>
	var trouve=false
	for(var i=0;i<tab_file.length&&!trouve;i++) {
		var nomfic = document.savePage.name.value
		var nodeid = document.savePage.node_id.value
		var nomrep = document.savePage.foldername.value
		if( nodeid+"§"+nomfic==tab_file[i] ) trouve=true
	}
	if(trouve) {
		if(!confirm("Le fichier "+nomfic+".php existe déjà dans le dossier "+nomrep+" !\nVoulez-vous continuer et l'écraser?")) return false
	}
	return validate_form()
}
</script>
<script type="text/javascript">
  function chooseFolder() {
  
    window.open('chooseFolder.php?idSite=<?php echo $idSite; ?>','browser','scrollbars=yes,width=500,height=500,resizable=yes,menubar=no');
  }
  function previewPage() {
    window.open('previewPage.php','preview','scrollbars=yes,width=640,height=480,resizable=yes,menubar=no');
  }
</script>
<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<form name="savePage" action="pageLiteEditor5.php?id=<?php echo $_GET['id']; ?>&idGab=<?php echo $_GET['idGab']?>&idThe=<?php echo $_GET['idThe']?>" method="post">
<input type="hidden" name="serial" id="serial" value="<?php echo $datas; ?>"/>
<input type="hidden" name="node_id" id="node_id" value="<?php echo $folder_id; ?>"/>
<input type="hidden" name="BeToSave" id="BeToSave" value="<?php echo  $_POST['BeToSave'] ; ?>"/>
<input type="hidden" name="idGab" id="idGab" value="<?php echo $_GET['idGab']; ?>"/>
<input type="hidden" name="idThe" id="idThe" value="<?php echo $_GET['idThe']; ?>"/>
<!--span class="arbo">
<a href="#" class="arbo" onClick="previewPage();">Visualiser</a> la page assemblée.</span-->

<div class="ariane"><span class="arbo2">PAGE >&nbsp;</span><span class="arbo3"><?php echo $sTitre; ?> une page&nbsp;>&nbsp;
Etape 3 : Nom et dossier de stockage</span></div>
<?php
 
(isset($_SESSION["pageLiteEditor4_nom"]) && $_SESSION["pageLiteEditor4_nom"]!="") ? $nom = $_SESSION["pageLiteEditor4_nom"] : $nom="nom&nbsp;:&nbsp;";
(isset($_SESSION["pageLiteEditor4_dossier"]) && $_SESSION["pageLiteEditor4_dossier"]!="") ? $dossier = $_SESSION["pageLiteEditor4_dossier"] : $dossier="Dossier de stockage&nbsp;:&nbsp;";
(isset($_SESSION["pageLiteEditor4_racine"]) && $_SESSION["pageLiteEditor4_racine"]!="") ? $foldername = $_SESSION["pageLiteEditor4_choisir_dossier"] : $foldername=$foldername;
(isset($_SESSION["pageLiteEditor4_choisir_dossier"]) && $_SESSION["pageLiteEditor4_choisir_dossier"]!="") ? $choisir_dossier = $_SESSION["pageLiteEditor4_choisir_dossier"] : $choisir_dossier="<img src='/backoffice/cms/img/go.gif' border='0'>&nbsp;choisir le dossier";
  

?>
<div id="tab_stockage">
<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo" cellpading="0">
<tr bgcolor="E6E6E6">
  <td align="right"><?php echo $nom; ?></td>
  <td><input name="name" type="text" class="arbo" id="nameid" value="<?php echo $name; ?>" size="35" maxlength="50" <?php echo $disabled;?> pattern="^([a-z]|[A-Z]|[0-9]|_)+$" errorMsg="Merci de spécifier un nom ne comportant que des caractères alphanumériques sans espace."></td>
</tr>
<tr bgcolor="EEEEEE">
  <td align="right"><?php echo $dossier; ?></td>
  <td><input name="foldername" type="text" disabled class="arbo" id="foldername" value="<?php echo $foldername;?>" size="20" pattern="^.+$" errorMsg="Veuillez spécifier un dossier d'enregistrement.">&nbsp;<?php if(!$_GET['id'] > 0) { ?><a href="#" class="arbo" onClick="chooseFolder();"><?php echo $choisir_dossier; ?></a><?php } ?></td>
</tr>
<tr bgcolor="D2D2D2">
  <td align="left" id="bt_retour"><input name="Retour" type="button" class="arbo" onClick="document.location='pageLiteEditor3.php?<?php echo $_SERVER['QUERY_STRING']; ?>';" value="<< Retour"></td>
  <td align="right" id="bt_suite"><input name="Suite >>" type="button" class="arbo" onClick="if(okfile()){ document.forms[0].name.disabled=false; submit();}" value="Suite >>"></td>
</tr>
</table>
</div>
</form>