<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');


if (isset($_SESSION["pageLiteEditor6_redirect"]) && $_SESSION["pageLiteEditor6_redirect"]!="") {
	echo "<script type=\"text/javascript\">"; 
	echo "window.location.href='".$_SESSION["pageLiteEditor6_redirect"]."';";
	echo "</script>";
}

/*
	$Author: pierre $
	$Id: pageLiteEditor6.php,v 1.2 2014-07-16 13:06:01 pierre Exp $

	$Log: pageLiteEditor6.php,v $
	Revision 1.2  2014-07-16 13:06:01  pierre
	*** empty log message ***

	Revision 1.1  2013-09-30 09:43:16  raphael
	*** empty log message ***

	Revision 1.13  2013-03-01 10:28:20  pierre
	*** empty log message ***

	Revision 1.12  2012-12-12 10:45:04  pierre
	*** empty log message ***

	Revision 1.11  2012-12-12 10:26:48  pierre
	*** empty log message ***

	Revision 1.10  2012-07-31 14:24:50  pierre
	*** empty log message ***

	Revision 1.9  2012-07-23 13:56:40  pierre
	*** empty log message ***

	Revision 1.8  2012-07-23 13:03:23  nicolas
	*** empty log message ***

	Revision 1.7  2011-12-02 16:30:45  pierre
	*** empty log message ***

	Revision 1.6  2011-12-02 16:02:39  pierre
	*** empty log message ***

	Revision 1.5  2011-12-02 15:01:49  pierre
	*** empty log message ***

	Revision 1.4  2011-07-04 14:01:32  pierre
	*** empty log message ***

	Revision 1.3  2010-08-26 15:39:28  pierre
	cms_theme

	Revision 1.2  2010-08-25 16:09:23  pierre
	cms_theme

	Revision 1.1  2010-08-23 08:29:52  pierre
	*** empty log message ***

	Revision 1.11  2010-06-04 15:40:25  pierre
	*** empty log message ***

	Revision 1.10  2009-09-24 08:53:14  pierre
	*** empty log message ***

	Revision 1.9  2009-02-25 17:54:10  thao
	*** empty log message ***

	Revision 1.8  2008-11-27 11:34:35  pierre
	*** empty log message ***

	Revision 1.7  2008-11-06 12:03:54  pierre
	*** empty log message ***

	Revision 1.6  2008-07-29 17:08:27  thao
	*** empty log message ***

	Revision 1.5  2008/07/16 10:48:36  pierre
	*** empty log message ***
	
	Revision 1.4  2007/11/26 16:58:27  thao
	*** empty log message ***
	
	Revision 1.3  2007/08/28 15:55:28  pierre
	*** empty log message ***
	
	Revision 1.2  2007/08/27 10:13:47  pierre
	*** empty log message ***
	
	Revision 1.1  2007/08/08 14:26:26  thao
	*** empty log message ***
	
	Revision 1.7  2007/08/08 14:16:15  thao
	*** empty log message ***
	
	Revision 1.6  2007/08/08 13:56:04  thao
	*** empty log message ***
	
	Revision 1.5  2007/08/08 10:13:30  thao
	*** empty log message ***
	
	Revision 1.4  2007/08/07 10:20:35  thao
	*** empty log message ***
	
	Revision 1.2  2007/03/27 15:10:39  pierre
	*** empty log message ***
	
	Revision 1.1  2006/12/15 12:30:11  pierre
	*** empty log message ***
	
	Revision 1.1.1.1  2006/01/25 15:14:32  pierre
	projet CCitron AWS 2006 Nouveau Website
	
	Revision 1.6  2005/12/20 09:12:10  sylvie
	application des styles
	
	Revision 1.5  2005/11/08 14:04:15  sylvie
	*** empty log message ***
	
	Revision 1.4  2005/11/04 08:36:53  sylvie
	*** empty log message ***
	
	Revision 1.3  2005/10/27 09:18:52  sylvie
	*** empty log message ***
	
	Revision 1.2  2005/10/24 15:10:07  pierre
	arbo.lib.php >> arbominisite.lib.php
	
	Revision 1.1.1.1  2005/10/24 13:37:05  pierre
	re import fusion espace v2 et ADW v2
	
	Revision 1.1.1.1  2005/10/20 13:10:53  pierre
	Espace V2
	
	Revision 1.2  2005/06/07 07:48:31  michael
	Vive les espaces!
	
	Revision 1.1.1.1  2005/04/18 13:53:29  pierre
	again
	
	Revision 1.1.1.1  2005/04/18 09:04:21  pierre
	oremip new
	
	Revision 1.1.1.1  2004/11/03 13:49:54  ddinside
	lancement du projet - import de adequat
	
	Revision 1.4  2004/06/18 14:10:21  ddinside
	corrections diverses en vu de la demo prevention routiere
	
	Revision 1.3  2004/06/16 15:23:19  ddinside
	inclusion corrections
	
	Revision 1.2  2004/04/26 08:07:09  melanie
	*** empty log message ***
	
	Revision 1.1.1.1  2004/04/01 09:20:27  ddinside
	Création du projet CMS Couleur Citron nom de code : tipunch
	
	Revision 1.5  2004/02/05 15:56:25  ddinside
	ajout fonctionnalite de suppression de pages
	ajout des styles dans spaw
	debuggauge prob du nom de fichier limite à 30 caracteres
	
	Revision 1.4  2004/01/27 12:12:50  ddinside
	application de dos2unix sur les scripts SQL
	modification des scripts d'ajout pour correction bug lors d'une modif de page
	ajout foncitonnalité de modification de page
	ajout visu d'une page si créée
	
	Revision 1.3  2004/01/21 12:33:51  ddinside
	integration de la DTD XHTML Transitionnelle
	intégration des titresd, description et mots clefs des pages
	
	Revision 1.2  2004/01/12 09:40:38  ddinside
	ajout config auto selon gabarit dans la creation de page
	ajout flashs home
	ajout calendrier
	ajout doc class fileupload
	
	Revision 1.1  2004/01/07 18:29:22  ddinside
	jout derniers fonctionnalites
	
	Revision 1.2  2003/11/26 13:08:42  ddinside
	nettoyage des fichiers temporaires commités par erreur
	ajout config spaw
	corrections bug menu et positionnement des divs
	
*/
//	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php'); inutilisé
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbopage.lib.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/gabarit.lib.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/procedures.php');

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newcontent_write.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newgabarit_write.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newpage_write.php');

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

	activateMenu('gestionpage');  //permet de dérouler le menu contextuellement

	$div_array = unserialize($_SESSION['page']);

	// site sur lequel on est positionné
	$idSite = $_SESSION['idSite_travail'];
	
	if ($idSite == "") die ("Appel incorrect de la fonctionnalité");
	$oSite = new Cms_site($idSite);
	
	// gabarit
	
	$oGab = new Cms_page($div_array['gabarit']);	

	$generateFile = true;
	$error=0;
	/*if($_POST['mepNow']==true)
		$generateFile = true;
	else
	$generateFile = false;*/
	$id_page = 0;	
		
	

	// cms_page
	//====================
	$oPage = new Cms_page();

	$oPage->setName_page($_POST['nomPage']);
	$oPage->setGabarit_page($div_array['gabarit']);
	$oPage->setIsgenerated_page($generateFile);
	$oPage->setValid_page(true);
	$oPage->setNodeid_page($_POST['nodeidPage']);
	$oPage->setIsgabarit_page(0);
	$oPage->setWidth_page($oGab->getWidth_page());
	$oPage->setHeight_page($oGab->getHeight_page());

	if (DEF_BDD == "ORACLE" || DEF_BDD == "POSTGRES") {

		$datemep = date("Y/m/d/H:m:s");
		$datemep = explode('/', $datemep);
		$datemep = "to_date('".$datemep[2]."/".$datemep[1]."/".$datemep[0]."', 'dd/mm/yyyy')";

	} else if (DEF_BDD == "MYSQL") {

		$datemep = "str_to_date('".getDateNow()."', '%d/%m/%Y')";
	}

	$oPage->setDateadd_page($datemep);
	$oPage->setDatemep_page($datemep);
	$oPage->setDateupd_page('');
	$oPage->setDatedlt_page('');
	$oPage->setOptions_page('');

	$oPage->setId_site($idSite);
	// par défaut à la création de la page aucun de ses contenus n'est en ligne
	$oPage->setToutenligne_page(0);
	$oPage->setToutenligne_page(0);
	$oPage->set_theme($_POST['idThe']);


	// cms_infos_pages
	//====================
		
	$oInfopage = new Cms_infos_page();
	$oInfopage->setPage_id($_GET['id']);
	$oInfopage->setPage_titre($_POST['pagetitle']);
	$oInfopage->setPage_motsclefs($_POST['pagekeywords']);
	$oInfopage->setPage_description($_POST['description']);
	$oInfopage->setPage_thumb($_POST['pagethumb']);		

	// sauvegarde de la page
	if($_GET['id'] > 0) {
		$oPage->setId_page($_GET['id']);
	}

	// sauvegarde de la page
	// savePage2 fait un generatePage qui remplace le contenu des divarray par les contenus EN LIGNE
	/*pre_dump($div_array);
	pre_dump($oPage
	pre_dump($oInfopage);*/ 		
	
	$id_page = savePage2($div_array, $oPage, $oInfopage); 
	
	// réobtention de l'objet page après savePage2
	$oPage = new Cms_page($id_page);
	
	/////////////////////////////////////////////////////////////
	// objets liés	
	/*
	$asso = dbGetAssocies($oGab, 'cms_assoclassepage');
	
	foreach($asso['list'] as $kC => $aClasse){
		$assoClassName = $aClasse['display'];
		$assoClassId = $aClasse['ref_id'];		
		
		$aX = dbGetObjectsFromFieldValue3('cms_assoclassepage', array('get_cms_page', 'get_classe'), array('equals', 'equals'), array($id_page, $assoClassId), NULL, NULL);
		if ((count($aX)==0) || ($aX==false)){ // l'asso n'a pas encore été créée
		
			$oTemp = new $assoClassName();
			
			if(!is_null($oTemp->XML_inherited))
				$sXML = $oTemp->XML_inherited;
			else
				$sXML = $oTemp->XML;
				
			xmlClassParse($sXML);			
			$classePrefixe = $stack[0]["attrs"]["PREFIX"];
			$aNodeToSort = $stack[0]["children"];
			
			foreach($stack[0]["children"] as $kI => $item){
				if (isset($item['attrs']['FKEY'])&&($item['attrs']['FKEY']=='cms_page')){
					$setterPage = 'set_'.$item['attrs']['NAME'];
					$oTemp->$setterPage($id_page);
				}				
				elseif (isset($item['attrs']['FKEY'])&&($item['attrs']['FKEY']=='cms_arbo_pages')){
					$setterPage = 'set_'.$item['attrs']['NAME'];
					echo $setterPage;
					$oTemp->$setterPage($oPage->get_nodeid_page());
				}			
			}			
			$idAssoObj = dbSauve($oTemp);			
			$oX = new cms_assoclassepage();
			$oX->set_cms_page($id_page);
			$oX->set_classe($assoClassId);
			$oX->set_objet($idAssoObj);
			dbSauve($oX);	
		
		}
	}*/
	/////////////////////////////////////////////////////////////	

	// taille du div_array pour cette page
	// ensemble des briques de cette page
	// le nombre de briques du divarray est sizeof(div_array[0]['id'])
	if ($div_array[0]['id'] != "") $eIdDivarray = 1;
	else $eIdDivarray = 0;
	
	// sauvegarde de la structure
	if ($eIdDivarray > 0) $bSaveStruct = saveStruct($id_page, $div_array);
	else $bSaveStruct = true;
	
	// sauvegarde des infos de la page
	$bStorePage = storeInfosPage($id_page, $oInfopage);

	// maj de l'attribut "tout en ligne"
	$bToutenligne_page = updateToutenligne($oPage->getId_page());
	$oPage->setToutenligne_page($bToutenligne_page);

	// maj de l'attribut "existe version ligne"
	$bExisteligne_page = updateExisteligne($oPage->getId_page());
	$oPage->setExisteligne_page($bExisteligne_page);

	// maj briques editables + génération du fichier
	if ($id_page > 0 and $bSaveStruct and $bStorePage) 
	{

		// si le div_array de cette page n'est pas vide
		if ($eIdDivarray > 0) {

			// maj des briques editables noeud -2 => node page
			$divArray = buildDivArrayPreview($div_array);

			foreach($divArray as $k => $v) {
				if (is_int($k)) {

					$idContent=$v['id'];

					$oContent = new Cms_content($idContent);

					// si on est sur une brique éditable
					if ($oContent->getIsbriquedit_content() == 1) {
					
						// Rajout d'un cas particulier lié à l'édition d'une page
						// si on a une brique éditable dont il faut réinitialiser le contenu HTML
						// avec le contenu HTML de la zone éditable

						if($v["HTMLdefaut"]!="") {

							$oContentHTMLDefault = new Cms_content($v["zonedit"]);
							$oContent->setHtml_content( $oContentHTMLDefault->getHtml_content() );
							$oContent->updateHTML();
						}

						// les briques editables créées sont pour l'instant à noeud -2
						if ($oContent->getNodeid_content() == -2) {
						
							// si brique editable -> noeud de la page
							$oContent->updateNoeud($oPage->getNodeid_page());
	
							// maj du nom de la brique editable
							// renommage
							updateNomBriqueEdit($oContent->getId_content(), $oPage->getId_page());
	
						} // fin  noeud
						
					} // fin test brique editable
				}
			}

			// enregistrement des briques editables effacées noeud => -2
			$_POST['BeToSave'] = preg_replace("/^[;]*/","",$_POST['BeToSave']);
			foreach(explode(";",$_POST['BeToSave']) as $k => $v) { 
				if ($v!="") {

					$oContent = new Cms_content($v);

					// si brique editable -> -2
					// Ce sont des briques éditables qui ont été remplacées par des fixes
					$oContent->updateNoeud(-2);
				}
			}

			$_SESSION['pageIsSaved'] = true;
			
?>
	<p class="arbo">Les paramètres de la page ont été correctement sauvegardée.</p>
<?php
			} // Fin divarray=0
			
		} else { // PB maj briques editables + génération du fichier
			$error++;
			$generateFile=false;
			die('<b>Un problème est survenu durant l\'enregistrement de la page.</b><br>');	
		}

		//----------------------------------
		// génération du fichier
		if ($generateFile) {

			// teste si toutes les briques sont en ligne
			$bTousEnLigne = $oPage->getToutenligne_page();

			if ($bTousEnLigne) {
				// PAGE GENEREE
				$arrayresult = generateFile($oPage);

				$sMessage_generate = "<span class='arbo'>".$translator->getTransByCode('page_genere')."</span>";
			} else {
				// PAGE NON GENEREE			
				$sMessage_generate = "<span class='arbo'><font color=red>La page n'a pas été générée car tous ses contenus ne sont pas en ligne</font>";
				$sMessage_generate.= "<br>Si vous souhaitez que cette page soit générée, allez dans le menu 'Gestion des contenus'";
				$sMessage_generate.= "<br>Lorsque tous les contenus de cette page seront en ligne, la page sera générée</span>";
			}
			//----------------------------------
			
			// message de génération
			if (is_array($arrayresult)) {
?>
	<p class="arbo"><?php echo $sMessage_generate; ?></p>	
	<p class="arbo"><?php $translator->echoTransByCode('Le_fichier'); ?> <?php echo $arrayresult['filename']; ?> <?php $translator->echoTransByCode('cree_dans_le_dossier'); ?> <?php echo $arrayresult['foldername']; ?>.</p>
	<p class="arbo"><?php $translator->echoTransByCode('chemin_complet'); ?> <b><?php echo $_SERVER['DOCUMENT_ROOT']."/content".$arrayresult['path'].$arrayresult['filename']; ?></b></p>
		<?php
			} else {
				// message de non génération
		?>	
	<p class="arbo"><?php echo $sMessage_generate; ?></p>
		<?php
			}
			// visualiser la page : version de brouillon et version en ligne
		?>
		<p class="arbo"><?php $translator->echoTransByCode('Visualiser_la_page'); ?> <?php echo $oPage->getName_page(); ?>.php</p>
<?php		
global $db;

$aNodeInfos = getNodeInfos($db, $oPage->getNodeid_page());

$sUrl = getRepPage($oPage->getId_site(), $oPage->getNodeid_page(), $aNodeInfos['path']);

// affichage des icones de preview des pages
$idContent = -1;
$sNature = "CMS_CONTENT";
$idPage = $oPage->getId_page();
$sNomPage = $oPage->getName_page();
$bToutenligne_page = $oPage->getToutenligne_page();
$bExisteligne_page = $oPage->getExisteligne_page();
$sUrlPageLigne = "/content".$sUrl.$oPage->getName_page().".php";

afficheIconePreviewPage($idContent, $sNature, $idPage, $sNomPage, $bToutenligne_page, $bExisteligne_page, $sUrlPageLigne);

		} 
		else {
			if($error==0) {
?>
	<p class="arbo">A votre demande, le fichier correspondant n'a pas été généré.</p>
	<p class="arbo">La mise en production a été planifiée pour le <?php echo $_POST['dateMep']; ?> entre 00h01 et 06h00.</p>
<?php
			} else {
				die('<b>Un problème est survenu durant l\'enregistrement de la page.</b><br>');
			}
		}

include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoappend.php');
?>