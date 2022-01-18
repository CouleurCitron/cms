<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
include_once('backoffice/cms/site/minisite.inc.php');

// site
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

// pas de site
if ($idSite == "") die ("Appel incorrect de la fonctionnalité");


activateMenu('gestioncontenu');  //permet de dérouler le menu contextuellement

$result=false;
$virtualPath = 0;
$nodeInfos=array();
$nodeChildren = array();
$orders = array();
if(!strlen($_POST['foldertag'])) {
	if (strlen($_GET['v_comp_path']) > 0) $virtualPath = $_GET['v_comp_path'];
	$nodeInfos=getNodeInfos($db,$virtualPath);
	$nodeChildren=getNodeChildren($idSite, $db,$virtualPath);

} else{
	if (strlen($_POST['v_comp_path']) > 0) $virtualPath = $_POST['v_comp_path'];
		// on recompose la tag si foldertaglength > 0
		if (intval($_POST['foldertaglength']) > 0){
			$foldertag = "";
			for ($i=1;$i<=intval($_POST['foldertaglength']);$i++){
				$foldertag .= rawurlencode($_POST['foldertagvariable'.$i])."=".rawurlencode($_POST['foldertagvalue'.$i]);
				if ($i < intval($_POST['foldertaglength'])){
					$foldertag .= "&";
				}
			}
		}
		else{
			$foldertag = $_POST['foldertag'];
		}
	
		$result = saveNodeTag($idSite, $foldertag, $db, $virtualPath);
		$nodeInfos = getNodeInfos($db,$virtualPath);
		$nodeChildren = getNodeChildren($idSite, $db,$virtualPath);
	}

?>


<form id="managetree" name="managetree" action="<?php echo $_SERVER['PHP_SELF'];?>?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?><?php echo $param; ?>" method="post">
<span class="arbo2">ARBORESCENCE&nbsp;>&nbsp;</span>
<span class="arbo3">Tag d'un dossier&nbsp;>&nbsp;</span>
<span class="arbo4"><?php echo strip_tags(getAbsolutePathString($idSite, $db, $virtualPath,'pageArbo.php')); ?></span><br /><br />
  <table cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td colspan="3"><img src="/backoffice/cms/img/vide.gif"></td>
    </tr>
    <tr>
      <td align="left" valign="top" class="arbo"><div id="arborescence" ><p><b><u>Arborescence :</u></b></p>
          <table cellpadding="8" cellspacing="0" border="1" bordercolor="#FFFFFF">
            <tr>
              <td bgcolor="D2D2D2"><?php
//print drawCompTree($idSite, $db, $virtualPath);
print drawCompTree($idSite, $db, $virtualPath, null, "/backoffice/cms/site/arbo.php");
?></td>
            </tr>
        </table></div></td>
      <td width="15" align="left" valign="top" class="arbo2"><img src="/backoffice/cms/img/vide.gif" width="15"></td>
      <td align="left" valign="top" class="arbo">
        <!-- contenu des actions -->
        <b><br />Tag d'un dossier</b><br /><br /><input type="hidden" id="v_comp_path" name="v_comp_path" value="<?php echo $_REQUEST['v_comp_path']; ?>"><input type="hidden" name="foldertag" id="foldertag" value="<?php echo $nodeInfos['tag']; ?>" />
      <table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF">
        <tr bgcolor="E6E6E6">
		          <td class="arbo" style="vertical-align:middle"><small>Tag&nbsp;:&nbsp;</small></td>
          <td><textarea name="foldertag" class="arbo textareaEdit id="foldertag" type="text"><?php echo $nodeInfos['tag']; ?></textarea></td>
        </tr>
		<?php 
		if(!strlen($_POST['foldertag'])) {
		?>
        <tr align="center" bgcolor="EEEEEE">
            <td colspan="2" bgcolor="D2D2D2" class="arbo"><a href="pageArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><strong>Annuler</strong></a>&nbsp;&nbsp;<a href="pageArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a> 
              - <a href="#" class="arbo" onClick="javascript:document.forms['managetree'].submit();"><strong>Enregistrer</strong></a>&nbsp;&nbsp;<a href="#" onClick="javascript:document.forms['managetree'].submit();" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>
        </tr>
      </table>
      <br />
      <?php
	} else {
		// renommage d'un dossier
		if($result) {
?>
      <br />
      <br />
      <br />
      <b><span class="arbo">La tag du dossier <?php echo $nodeInfos['libelle']; ?> a bien &eacute;t&eacute; renseign&eacute;e</span></b>
      <?php
		} else {
?>
      <br />
      <br />
      <br />
      <b><span class="arbo">Un probl&egrave;me est survenu durant l'op&eacute;ration. <br />
Veuillez r&eacute;essayer ou contacter l'administrateur si l'erreur persiste.</span></b>
      <?php
		}
	}
?></td>
    </tr>
  </table>
  </form>