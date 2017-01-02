<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/gabarit.lib.php');	
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

function sanitizeHeaderField($str){
	if (preg_match('/(.*)(<\?php.+\?>)(.*)/msi', $str, $aMatches)){
		return htmlentities($aMatches[1]).$aMatches[2].htmlentities($aMatches[3]);
	}
	else{
		return htmlentities($str);
	}
}

// site sur lequel on est positionné
$idSite = $_SESSION['idSite'];
if ($idSite == "") die ("Appel incorrect de la fonctionnalité");
$oSite = new Cms_site($idSite);

activateMenu('gestionpage');  //permet de dérouler le menu contextuellement
	
if (is_post('node_id')){
	// fetch le node 
	$oNode = new cms_arbo_pages($_POST['node_id']);	
		
	$aWorkNode = array($oNode->get_id());
		
	// son path
	$needlePath = $oNode->get_absolute_path_name();	
		
	if(is_post('recurs')&&($_POST['recurs']=='true')){
	 	// chercher lesfils par le path
		$sObjetReq = 'cms_arbo_pages';
		$sql = 'SELECT '.$sObjetReq.'.* FROM '.$sObjetReq.' WHERE node_id_site = '.$idSite.' AND node_absolute_path_name LIKE "'.$needlePath.'%";';		

		$aSubs = dbGetObjectsFromRequete($sObjetReq, $sql);
		
		foreach($aSubs as $k => $oSub){
			$aWorkNode[]=$oSub->get_id();		
		}
	}
	$aWorkNode = array_unique($aWorkNode);	
	
	// cherche les pages
	foreach($aWorkNode as $k => $nodeId){
		$sObjetReq = 'cms_page';
		$sql = 'SELECT '.$sObjetReq.'.* FROM '.$sObjetReq.' WHERE nodeid_page = '.$nodeId.' AND id_site = '.$idSite.';';

		$aPages = dbGetObjectsFromRequete($sObjetReq, $sql);

		foreach($aPages as $kk => $oPage){
			echo '<strong>page</strong> : '.$oPage->get_id();
			
			$sObjetReq = 'cms_infos_page';
			$sql = 'SELECT * FROM cms_infos_pages WHERE page_id = '.$oPage->get_id().';';
			$aMetas = dbGetObjectsFromRequete($sObjetReq, $sql);
			
			if ($aMetas!=false){
				foreach($aMetas as $kkk => $oMeta){
					// appliquer
					if(is_post('pagetitle')){
						$oMeta->setPage_titre(sanitizeHeaderField($_POST['pagetitle']));
						echo ' | <strong>update</strong> titre ';
					}
					if(is_post('pagekeywords')){
						$oMeta->setPage_motsclefs(sanitizeHeaderField($_POST['pagekeywords']));
						echo ' | <strong>update</strong> keywords ';
					}
					if(is_post('description')){
						$oMeta->setPage_description(sanitizeHeaderField($_POST['description']));
						echo ' | <strong>update</strong> description ';
					}	
					
					dbSauve($oMeta);		
				}	
			}	
			else{
				$oMeta = new cms_infos_page();
				if(is_post('pagetitle')){
					$oMeta->setPage_titre(sanitizeHeaderField($_POST['pagetitle']));
					echo ' | <strong>create</strong> titre ';
				}
				if(is_post('pagekeywords')){
					$oMeta->setPage_motsclefs(sanitizeHeaderField($_POST['pagekeywords']));
					echo ' | <strong>create</strong> keywords ';
				}
				if(is_post('description')){
					$oMeta->setPage_description(sanitizeHeaderField($_POST['description']));
					echo ' | <strong>create</strong> description ';
				}	
				$oMeta->setPage_id($oPage->get_id());
				dbSauve($oMeta);
			}	
			echo ' <br />';
		}
	}	
	echo '<p><a href="/backoffice/cms/regenerateAll.php?menuOpen=true">re-générer les pages pour appliquer la modification</a></p>';
}	
?>
<script type="text/javascript">
  function chooseFolder() {  
    window.open('chooseFolder.php?idSite=<?php echo $idSite; ?>','browser','scrollbars=yes,width=500,height=500,resizable=yes,menubar=no');
  }
  function previewPage() {
    window.open('previewPage.php','preview','scrollbars=yes,width=640,height=480,resizable=yes,menubar=no');
  }
</script>
<div class="ariane"><span class="arbo2">PAGE >&nbsp;</span><span class="arbo3">Modifier les méta données &gt; Appliquer &agrave; toutes les pages d'un dossier </span></div>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="savePage" class="arbo">
	<input type="hidden" name="node_id" id="node_id" value=""/>
	<div id="virginie">
	  <p>    Titre de la page :&nbsp;
	  <br>
	  <input name="pagetitle" type="text" class="arbo" maxlength="255" value="<?php echo stripslashes($titre); ?>" />
	  <br>
	  <br>
		Mots clefs <small>(séparés par des virgules)</small>:&nbsp;
	  <br>
	  <input name="pagekeywords" id="pagekeywords" type="text" class="arbo" value="<?php echo stripslashes($mots); ?>" size="100" maxlength="768" />
	  <br>
	  <br>
		Description :<br> 
	  <textarea name="description" class="arbo textareaEdit" id="form_description"><?php echo stripslashes($description); ?></textarea>
	  </p>
	  <p><?php echo $dossier; ?><br />
		  <input name="foldername" type="text" disabled="disabled" class="arbo" id="foldername" value="<?php echo $foldername;?>" size="20" pattern="^.+$" errormsg="Veuillez sp&eacute;cifier un dossier d'enregistrement." />
		&nbsp;   
		<a href="#" class="arbo" onclick="chooseFolder();"><img src='/backoffice/cms/img/2013/icone/go.png' border='0'>&nbsp;choisir le dossier</a>
	  </p>
	  <p>
		<input name="recurs" id="recurs" type="checkbox" class="arbo" value="true" />
		R&eacute;cursif (appliquer aux sous-dossiers) </p>
	  <p>&nbsp;</p>
	</div>
	<input name="Enregistrer" type="submit" class="arbo"  value="Suite >>" id="bt_suite" />
</form>