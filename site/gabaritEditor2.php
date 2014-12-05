<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 
sponthus 07/06/2005
création de gabarit
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/gabarit.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/procedures.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newcontent_write.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newgabarit_write.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newpage_write.php'); 
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
 include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

activateMenu('gestiongabarit');  //permet de dérouler le menu contextuellement


// mode debug
// je laisse ce mode là car cette page n'est pas très bien écrite et difficile à lire
$bDebug = 0;

// site de travail
$idSite = $_SESSION['idSite_travail'];
$oSite = new Cms_site($_SESSION['idSite_travail']);

// id du gabarit
$id = $_GET['id'];
if ($id == "") $id = $_POST['id'];

//--------------------------------------------
// par défaut, tous les gabarits ont un supra gabarit == init
// manuellement et exceptionnellement, on affecte des gabarits à des supra gabarits

// par exemple pour résoudre le problème de scroll pour avoir un content dans les éléments de navigation

// si création gabarit -> supra gabarit == init
// si modif    gabarit -> supra gabarit = celui affecté
if ($id != "") {

	$oThisGab = new cms_page($id); 
	$oSupraGab = new cms_page($oThisGab->getGabarit_page()); 

	$_GET['idGab'] = $oThisGab->getGabarit_page();
	$_GET['fileGab'] = $oSupraGab->getName_page().".php";
}
//--------------------------------------------

// site de travail
//if ($_GET['idGab'] == "") $_GET['idGab'] = -1;
if ($_GET['fileGab'] == "") $_GET['fileGab'] = DEF_GABINIT.".php";


if (DEF_BDD == "ORACLE" || DEF_BDD == "POSTGRES") {

	$datemep = date("Y/m/d/H:m:s");
	$datemep = split('/', $datemep);
	$datemep = "to_date('".$datemep[2]."/".$datemep[1]."/".$datemep[0]."', 'dd/mm/yyyy')";

} else if (DEF_BDD == "MYSQL") {

	$datemep = "str_to_date('".getDateNow()."', '%d/%m/%Y')";
}

// a_voir sponthus
// purge des briques zones éditables qui sont créées 
$aContent_purge = getContentPurge();

for ($a=0; $a<sizeof($aContent_purge); $a++) {
	
	
	
	if ($_POST['selectedComposantList']!="") {
		$aSelectedComposantList = split(',', $_POST['selectedComposantList']);
	}
	else if (isset($_SESSION[$_SESSION['GabaritName']]) && $_SESSION[$_SESSION['GabaritName']]!="") { 
		$aSelectedComposantList = $_SESSION[$_SESSION['GabaritName']];
	} 
	$oContent_purge = $aContent_purge[$a];
	
	if(is_array($aSelectedComposantList)){
		if (!in_array($oContent_purge->getId_content(), $aSelectedComposantList)){		
			// trace au cas où (dans le fichier d'erreur mais ce n'est pas une erreur)
			error_log("PURGE CONTENT ID=".$oContent_purge->getId_content());
			$oContent_purge->realDelete();
		}
	}
}


// les briques zone editable sont créées en BDD
// elles seront créées de type HTML, DEF_ZONEDITWIDTH*DEF_ZONEDITHEIGHT (à modifier après)

// tableau des clés des zones éditables créés pour ce gabarit
$aIdZoneEdit = array();

for ($a=0; $a<$_POST['nbZoneEdit']; $a++)
{
	// création de la brique en BDD
	$oZonedit = new Cms_content();

	// numéro de la brique que l'on créé
	$eNoBrique = $a+1;

	$sNom = "fName_".$a;
	$eWidth = "fWidth_".$a;
	$eHeight = "fHeight_".$a;
	$fId = "fId_".$a;

	$oZonedit->setName_content($_POST[$sNom]);
	$oZonedit->setType_content("HTML");
	$oZonedit->setWidth_content($_POST[$eWidth]);
	$oZonedit->setHeight_content($_POST[$eHeight]);
	$oZonedit->setDateadd_content(datemep);
	$oZonedit->setDateupd_content("");
	$oZonedit->setDatedlt_content("");
	$oZonedit->setValid_content(1); // ??? a_voir sponthus
	$oZonedit->setActif_content(1);
	$oZonedit->setHtml_content("");
	
	// brique créée à un noeud non valid -> update -1 à la validation du gabarit
	// noeud -1 pour que ces briques n'apparaissent jamais dans l'arbo des briques
	// instaurer une purge des briques de zone -2
	// attention ne pas puger les briques du jour (contexte multi utilisateur)
	$oZonedit->setNodeid_content(-2);
	
	$oZonedit->setObj_table_content("");
	$oZonedit->setObj_id_content(-1);
	$oZonedit->setId_site($idSite);
	$oZonedit->setIszonedit_content(1);

	// nouvelle zone editable à créer
	if ($_POST[$fId] == "") {
	
		// nouvelle valeur de clé
		$oZonedit->setId_content(getNextVal("cms_content", "id_content"));
	
		// clé de la zone éditable créée
		$aIdZoneEdit[] = $oZonedit->getId_content();
		
		// INSERT
		// création des zones dont "id_" est vide
		$result = $oZonedit->cms_content_insert();
	}
	else {
		// zone editable existante, on est en modif de gabarit
		$aIdZoneEdit[] = $_POST[$fId];	
	}
}

// le gabarit 'init' doit être unique en BDD
// le gabarit de départ peut ne pas exister en BDD
// il sera créé de la forme : 
// <!--DEBUTCMS--><!--FINCMS-->
$eGabarit = getCountGabaritByName(DEF_GABINIT, $_SESSION['idSite_travail']);
 
if ($eGabarit == 0)
{ 
	// création du gabarit en BDD

	$oPage = new Cms_page();

	$oPage->setName_page(DEF_GABINIT);
	// le gabarit de départ n'est pas éditable
	$oPage->setGabarit_page(-1);

	$oPage->setIsgenerated_page(1);
	$oPage->setValid_page(1);
	
	// les gabarits sont créés à la racine
	// le noeud du gabarit n'est pas une information exploitée
	$oPage->setNodeid_page(0);

	$oPage->setIsgabarit_page(1);
	$oPage->setWidth_page($oSite->getWidthpage_site());
	$oPage->setHeight_page($oSite->getHeightpage_site());

	$oPage->setDateadd_page($datemep);
	$oPage->setDatemep_page($datemep);

	$oPage->setDateupd_page('');
	$oPage->setDatedlt_page('');

	$oPage->setOptions_page('');

	$oPage->setId_site($_SESSION['idSite_travail']);

	// initialisation, pas de composants pour le gabarit de départ
	$divArray = array();

	$oInfos_page = new Cms_infos_page();

	$oInfos_page->setPage_titre('GABARIT');
	$oInfos_page->setPage_motsclefs('');
	$oInfos_page->setPage_description('');		
	
	// génération du fichier gabarit
	$_GET['idGab'] = generateGabarit($divArray, $oInfos_page, $oPage);

} else if ($eGabarit == 1) {

	// rechercher l'id du gab de départ DEF_GABINIT
	// seul cas où l'on cherche un gabarit avec son nom

	$oGabDepart = getGabaritByName(DEF_GABINIT, $_SESSION['idSite_travail']);
	$_GET['idGab'] = $oGabDepart->getId_page();

	//--------------------------------------------
	// par défaut, tous les gabarits ont un supra gabarit == init
	// manuellement et exceptionnellement, on affecte des gabarits à des supra gabarits
	
	// par exemple pour résoudre le problème de scroll pour avoir un content dans les éléments de navigation
	
	// si création gabarit -> supra gabarit == init
	// si modif    gabarit -> supra gabarit = celui affecté
	if ($id != "") {
	
		$oThisGab = new cms_page($id); 
		$oSupraGab = new cms_page($oThisGab->getGabarit_page()); 
	
		$_GET['idGab'] = $oThisGab->getGabarit_page();
		$_GET['fileGab'] = $oSupraGab->getName_page().".php";
	}
}

if( preg_match('/gabaritEditor.php/',$_SERVER['HTTP_REFERER'])  ) {
	$_SESSION[$_SESSION['GabaritName']] = null;
	$_SESSION['divArray'] = null;
}

$virtualPath = 0;
if (strlen($_GET['v_comp_path']) > 0)
	$virtualPath = $_GET['v_comp_path'];
if(!isset($_POST['displayedComposantList'])) {
	$contenus = getFolderComposants($idSite, $virtualPath);

	if(!is_array($contenus))
		$contenus = array();
		
	$displayedComposantList=''; // Liste des briques du noeud ouvert
	foreach ($contenus as $k => $composant) {
		if(strlen($displayedComposantList)==0) {
			$displayedComposantList = $composant['id'];
		} else {
			$displayedComposantList .= ','.$composant['id'];
		}
		$_POST['displayedComposantList']=$displayedComposantList;
	}

	if(is_array($_SESSION[$_SESSION['GabaritName']])) {
		$_POST['toSelect'] = array_values($_SESSION[$_SESSION['GabaritName']]);
	}
}
$virtualPath = 0;
if (strlen($_GET['v_comp_path']) > 0)
	$virtualPath = $_GET['v_comp_path'];
$referer = $_SERVER['HTTP_REFERER'];
$strRegExp = addcslashes($_SERVER['PHP_SELF'],'\/.?&');
if(!preg_match('/'.$strRegExp.'/',$referer))
	$_SESSION[$_SESSION['GabaritName']] = array();
	
if( (is_array($_SESSION[$_SESSION['GabaritName']])) or (isset($_POST['envoi'])) ) {
	if(!is_array($_SESSION[$_SESSION['GabaritName']]))
		$_SESSION[$_SESSION['GabaritName']] = array();

	$selectedSessionComposant = $_SESSION[$_SESSION['GabaritName']];
	
	$displayedComposantList = split(',' ,$_POST['displayedComposantList']);
	
if ($bDebug) {
print("<font color=black><br>------toSelect--------<br>");
pre_dump($toSelect);
print("<br>--------------<br></font>");
}
	$selectedComposantList = $_POST['toSelect'];
	
	if(!is_array($selectedComposantList)){
		$selectedComposantList = array();
if ($bDebug) {
print("<font color=black><br>------array--------<br>");
pre_dump($toSelect);
print("<br>--------------<br></font>");
}
	}
	
	$deletedComposantList = $_POST['toDelete'];
	if(!is_array($deletedComposantList))
		$deletedComposantList = array();

// tri des composants affichés : on les supprime pour ne remettre que ceux selectionnes		
	foreach ($displayedComposantList as $k => $v) {
		$indice = array_search($v, $selectedSessionComposant);
		
		if ($indice==0) {
			if ($v != $selectedSessionComposant[$indice]) {
				$indice = -1;
			}
		}
		
		if ($indice >=0)   {
			$selectedSessionComposant[$indice] = null;
		}
	}
// on rajoute les composants selectionnes de la page courante
	$tmpArray = array();
	foreach ($selectedComposantList as $k => $v) {
		if(($v !=null) && (!in_array($v, $deletedComposantList)))
			array_push($tmpArray, intval($v));
	}
// on supprime tout de même ceux marqués à la suppression

	foreach ($deletedComposantList as $k => $v) {
		$indice = array_search($v,$selectedSessionComposant);
		
		if ($indice==0) {
			if ($v != $selectedSessionComposant[$indice]) {
				$indice = -1;
			}
		}
		
		if ($indice >=0)   {
			$selectedSessionComposant[$indice] = null;
		}
	}

	// pour toutes les zones éditables de ce gabarit
	for ($a=0; $a<$_POST['nbZoneEdit']; $a++)
	{
		$oZonedit = new Cms_content();

		$oZonedit->initValues($aIdZoneEdit[$a]);

		//-------------------------------------
		// tailles des zones éditables 
		
		// si on est en modif de gabarit
		// afficher les tailles des zones editables de cms_struct_page et non de cms_content

		if ($id != "") {

			// s'il s'agit d'une nouvelle zone editable alors cms_struct_page n'existe pas encore
			$sql = " SELECT count(*) FROM cms_struct_page WHERE id_content=".$aIdZoneEdit[$a];
			$eStruct = dbGetUniqueValueFromRequete($sql);

			if ($eStruct > 0) {
				// objet struct
				$oStruct = new Cms_struct_page();
				$oStruct->getObjet($id, $aIdZoneEdit[$a]);
	
				$height = $oStruct->getHeight_content();
				$width = $oStruct->getWidth_content();
			} else {
				$height = $oZonedit->getHeight_content();
				$width = $oZonedit->getWidth_content();
			}			
		} else {
			$height = $oZonedit->getHeight_content();
			$width = $oZonedit->getWidth_content();
		}
		//-------------------------------------
	
		//---------------------------------------
		// ajout de la brique zone éditable
		$tmpEDIT = array(
		'id' => $oZonedit->getId_content(),
		'name' => $oZonedit->getName_content(),
		'type' => "Contenu",
		'width' => $width,
		'height' => $height);
		
		array_push($tmpArray, intval($oZonedit->getId_content()));
		//---------------------------------------
	}
	
	foreach ($selectedSessionComposant as $k => $v) {
		if($v !=null)
			array_push($tmpArray, $v);
	}
	
	if(count($tmpArray))
		$selectedSessionComposant = sort($tmpArray);
	if(!is_array($selectedSessionComposant))
		$selectedSessionComposant = $tmpArray;
	
	$_SESSION[$_SESSION['GabaritName']] = array_unique($selectedSessionComposant);

}
?>
<script type="text/javascript">document.title="Choix des briques";</script>
<script src="/backoffice/cms/js/preview.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<?php
// si id présent dans l'url -> on est en modif de gabarit
// donc height et width ne sont pas envoyés via la première page
// height et width sont donc alimentés avec l'objet gabarit dont on a l'id

if ($id != "") {

	$oGab = new Cms_page($id);
	
	$sNomGab = $oGab->getName_page();

//	$widthGab = $oGab->getWidth_page();
//	$heightGab = $oGab->getHeight_page();

	$widthGab = $_POST['fWidth_toutelazone'];
	$heightGab = $_POST['fHeight_toutelazone'];

	if ($widthGab == "")  $widthGab = $_GET['widthGab'];
	if ($heightGab == "") $heightGab = $_GET['heightGab'];

	if ($widthGab == "")  $widthGab = $_POST['widthGab'];
	if ($heightGab == "") $heightGab = $_POST['heightGab'];

} else {

	$sNomGab = "";

//	$widthGab = $oSite->getWidthpage_site();
//	$heightGab = $oSite->getHeightpage_site();

	$widthGab = $_POST['fWidth_toutelazone'];
	$heightGab = $_POST['fHeight_toutelazone'];

	if ($widthGab == "")  $widthGab = $_GET['widthGab'];
	if ($heightGab == "") $heightGab = $_GET['heightGab'];

	if ($widthGab == "")  $widthGab = $_POST['widthGab'];
	if ($heightGab == "") $heightGab = $_POST['heightGab'];

}

//----------------------------------------
// action du formulaire

$sAction = $_SERVER['REQUEST_URI'];

// recherche la présence du caractère ?
$ePos = strpos($sAction, "?");

// si le point d'interrogation est présent -> lui susbtituer 
// le caractère & à la place pour les paramètres supplémentaires
if ($ePos > 0) $sAction.= "&";
else $sAction.= "?";

$sAction.= "idGab=".$_GET['idGab']."&id=".$id."&widthGab=".$widthGab."&heightGab=".$heightGab;
//----------------------------------------
?><form name="managecomposant" id="managecomposant" action="<?php echo $sAction; ?>" method="post">
<input type="hidden" name="widthGab" id="widthGab" value="<?php echo $widthGab; ?>" />
<input type="hidden" name="heightGab" id="heightGab" value="<?php echo $heightGab; ?>" />

<div class="ariane"><span class="arbo2">GABARIT&nbsp;>&nbsp;</span>
<span class="arbo3">Etape 2 : choix des composants&nbsp;>&nbsp;<?php echo getAbsolutePathString($idSite, $db, $virtualPath); ?></span></div>
<table border="0" cellpadding="0" cellspacing="0">
  <tr><td align="right">
<table cellpadding="0" cellspacing="0" border="0" class="arbo">
 <tr>
  <td colspan="5"><img src="/backoffice/cms/img/vide.gif"></td>
 </tr>
 <tr>
   <td width="6" align="left" valign="top" class="arbo"><br>
       <b>Arborescence :</b> <br>
       <table cellpadding="8" cellspacing="0" border="0">
         <tr>
           <td bgcolor="#FFFFFF"><?php
			print drawCompTree($idSite, $db, $virtualPath, null, null, "&id=".$id."&widthGab=".$widthGab."&heightGab=".$heightGab);
			?></td>
         </tr>
       </table>
       <br>
       <br>
   </td>
  <td colspan="3" class="arbo2"><img src="/backoffice/cms/img/vide.gif" width="15"></td>
  <td align="left" valign="top" class="arbo">
  <!-- contenu du dossier --><br>  <b class="arbo">
  Composants contenus dans le dossier en cours :</b>
   <br>
   <br>  <table width="550" border="0" cellpadding="5" cellspacing="0" class="arbo">
     <tr class="col_titre">
       <td align="center"></td>
       <td width="5" align="center">Nom</td>
       <td width="1" align="center">Dimensions</td>
       <td width="5" align="center">Type</td>
       <td align="center"></td>
     </tr>
     <?php
	$contenus = getFolderComposants($idSite, $virtualPath);
	$displayedComposantList = '';
	$selectedComposantList = array();

	if(isset($_SESSION[$_SESSION['GabaritName']]) and is_array($_SESSION[$_SESSION['GabaritName']])) {
		$selectedComposantList = $_SESSION[$_SESSION['GabaritName']];

if ($bDebug) {
print("<font color=black><br>-------selectedComposantList-------<br>");
pre_dump($selectedComposantList);
print("<br>--------------<br></font>");
}
	}

	if(!is_array($contenus)) {
?>
     <tr>
       <td align="center" colspan="5">&nbsp;<strong><small>Aucun élément à afficher</small></strong>&nbsp;   
     </tr>
     <?php
	} else {
		foreach ($contenus as $k => $composant) {
		if(strlen($displayedComposantList)==0) {
			$displayedComposantList = $composant['id'];
		} else {
			$displayedComposantList .= ','.$composant['id'];
		}
?>
     <tr bgcolor="EEEEEE">
       <td align="center">&nbsp;&nbsp;
           <input type="checkbox" name="toSelect[]" <?php if(in_array($composant['id'],$selectedComposantList)) {echo "checked";}?>  value="<?php echo $composant['id'];?>">
     &nbsp;&nbsp;</td>
       <td align="center" bgcolor="EEEEEE">&nbsp;<?php echo $composant['name'];?>&nbsp;</td>
       <td align="center">&nbsp;<?php echo $composant['width'];?>x<?php echo $composant['height'];?>&nbsp;</td>
       <td align="center">&nbsp;<?php echo $composant['type'];?>&nbsp;</td>
       <td align="center">&nbsp;<a href="#" class="arbo" onClick="javascript:preview_content(<?php echo $composant['id'];?>,<?php echo $composant['width'];?>,<?php echo $composant['height'];?>)">Preview</a>&nbsp;</td>
     </tr>
     <?php
		}
	}
  ?>
     <tr class="col_titre">
       <td colspan="5" align="center"><input name="envoi2" id="envoi2" type="submit" class="arbo" value="Ajouter à la sélection"></td>
     </tr>
   </table></td>
 </tr>
</table>
<input type="hidden" name="file" id="file" value="<?php echo $_GET['file']; ?>" />
<input type="hidden" name="displayedComposantList" id="displayedComposantList" value="<?php echo $displayedComposantList; ?>" />
<input type="hidden" name="selectedComposantList" id="selectedComposantList" value="<?php echo join(',',$selectedComposantList); ?>" />
<br></td></tr>
<tr><td align="right">
<img src="../img/barre.gif">
</td>
</tr>
<tr><td align="right">

<table width="550" border="0" cellpadding="5" cellspacing="0">
  <tr>
    <td colspan="7"><span class="arbo"><small><b>Résumé :</b></small><br>
<small>Gabarit&nbsp;:&nbsp;<?php echo $sNomGab; ?></small><br>
<small>Composants selectionnés&nbsp;:</small></span></td>
  </tr>
  <tr class="col_titre">
	<td align="center"></td>
	<td align="center">Nom</td>
	<td align="center"></td>
	<td align="center">Dimensions</td>
	<td align="center">Type</td>
	<td align="center">Prévisualiser</td>
	<td align="center">Editer</td>
  </tr>
<?php
if ($bDebug) {
print("<font color=black><br>-------selectedComposantList-------<br>");
pre_dump($selectedComposantList);
print("<br>--------------<br></font>");
}
$c=0;
	foreach($selectedComposantList as $k => $v) {
		if ($v != null) {
			$c++; 
			$composant = getComposantById($v); 
			// objet content 
			$oContent = new Cms_content($composant['id']);

			//-------------------------------------
			// tailles des zones éditables 

			// REMARQUE
			// il ne sert à rien de faire un push bien clean d'un objet avec id, nom et dimensions
			// array_push($tmpArray, $oZonedit->getId_content());
			// EN EFFET ici seul $v (l'id) est exploité
			
			// si on est en modif de gabarit
			// afficher les tailles des zones editables de cms_struct_page et non de cms_content
	
			if ($id != "") {
	

				// existe t il un objet struct pour cette brique pour cette page ?
				// si oui les dimensions seront celles de cms_struct_page
				// si non les dimensions seront celle de cms_content

				$aFieldWhere = array();
				$aValueWhere = array();
				$aTypeWhere  = array();

				$aFieldWhere[] = "id_page";
				$aValueWhere[] = $id;
				$aTypeWhere[] = "ENTIER";

				$aFieldWhere[] = "id_content";
				$aValueWhere[] = $v;
				$aTypeWhere[] = "ENTIER";

				$eStruct = getCount_where("Cms_struct_page", $aFieldWhere, $aValueWhere, $aTypeWhere);

				if ($eStruct > 0) {

					// s'il s'agit d'une nouvelle zone editable alors cms_struct_page n'existe pas encore
					$sql = " SELECT count(*) FROM cms_struct_page WHERE id_content=".$v;
					$eStruct = dbGetUniqueValueFromRequete($sql);

					if ($eStruct > 0) {
						// objet struct
						$oStruct = new Cms_struct_page();
						$oStruct->getObjet($id, $v);
			
						$width = $oStruct->getWidth_content();
						$height = $oStruct->getHeight_content();
					} else {
						$width = $oContent->getWidth_content();
						$height = $oContent->getHeight_content();
					}

				} else {
					// objet content
					$width = $oContent->getWidth_content();
					$height = $oContent->getHeight_content();
				}

				
			} else {
				$width = $composant['width'];;
				$height = $composant['height'];
			}
			//-------------------------------------

	// type 
	if ($oContent->getIszonedit_content() == 1)
		$sType = "Contenu";
	else 
		$sType = $composant['type'];
			
	// chemin
	if ($composant['node_id'] != -1 && $composant['node_id'] != -2) {
		$oArbo = new Cms_arbo_pages($composant['node_id']);
		$sChemin = cheminAere($oArbo->getAbsolute_path_name());
	} else {
		$sChemin = "";
	}
?>
  <tr>
   <td align="center" class="arbo">&nbsp;     <input type="checkbox" name="toDelete[]" value="<?php echo $v; ?>"> 
     &nbsp;</td>
   <td align="center" class="arbo">&nbsp;<?php echo $composant['name'];?>&nbsp;</td>
   <td align="center" class="arbo">&nbsp;<?php echo $sChemin;?>&nbsp;</td>
   <td align="center" class="arbo">&nbsp;<?php echo $width;?>x<?php echo $height;?>&nbsp;</td>
   <td align="center" class="arbo">&nbsp;<?php echo $sType;?>&nbsp;</td>
   <td align="center" class="arbo">&nbsp;<a href="#<?php echo $composant['id'];?>" class="arbo" onClick="javascript:preview_content(<?php echo $composant['id'];?>,<?php echo $composant['width'];?>,<?php echo $composant['height'];        ?>)">Preview</a>&nbsp;</td>
   <td align="center" class="arbo"><?php
// si la brique est une zone editable -> modif contenu seul et non toute la brique
if ($oContent->getIszonedit_content() == 1) {
	$urlModifContent = "contentLiteEditor.php";
?>
<a href="javascript:openBrWindow('../<?php echo $urlModifContent; ?>?id=<?php echo $composant['id']; ?>&idSite=<?php echo $idSite; ?>', 'Modification', 600, 400, 'scrollbars=yes,resizable=1', 'true');" class="arbo">Editer</a>
<?php
}
else {
	$urlModifContent = "contentEditor.php";
?>
<a href="../<?php echo $urlModifContent; ?>?id=<?php echo $composant['id']; ?>&idSite=<?php echo $idSite; ?>" target="#" class="arbo">Editer</a>
<?php
}
?>&nbsp;</td>
  </tr>
<?php
		}
	}
	if($c==0) {
?>
<script type="text/javascript"><!-- 
var c = 0; 
--></script>
<tr><td class="arbo" colspan="7" align="center"><strong>Aucun élément sélectionné</strong></td></tr>
<?php
	} else {
?>
<script type="text/javascript"><!-- 
var c = 1; 
--></script>
  <tr class="col_titre">
   <td colspan="7" align="center">
     <input name="envoi" type="submit" class="arbo" value="Supprimer de la selection">
   </td>
  </tr>

<?php
	}
?>
</table>
</td></tr>
<tr><td align="right">
<br>
<img src="../img/barre.gif">
</td>
</tr>
<tr><td>
<?php
$widthGab == "";
$heightGab == "";

if ($_POST['widthGab']  != "") $widthGab  = $_POST['widthGab'];
if ($_POST['heightGab'] != "") $heightGab = $_POST['heightGab'];

?>
</td></tr>
<tr><td align="right">
<table width="550" border="0" cellpadding="0" cellspacing="0">
  <tr><td align="center">
<input name="dummy" id="dummy" type="button" class="arbo" onClick="javascript:if(c!=0) {window.location='gabaritEditor3.php?idGab=<?php echo $_GET['idGab']; ?>&id=<?php echo $id; ?>&widthGab=<?php echo $widthGab;?>&heightGab=<?php echo $heightGab;?>';} else { window.alert('Vous n\'avez selectionné aucun composant.');}" value="Suite (Disposer les composants) >>" />
</td></tr></table>
</td></tr>
</table>
</form>