<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 

***************************************************
*** Gestion de l'arbo des cartes                ***
*** Classeur de cartes                          ***
*** Récupéré du fichier arbo_movefolderpage.php ***
***************************************************

*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbocarte.lib.php');
$idSite = $_GET['idSite'];
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];
activateMenu('gestioncarte');  //permet de dérouler le menu contextuellement
$id=0;
$virtualPath = 0;
$nodeInfos=array();
if($_POST['node_id']=="") {
	if (strlen($_GET['v_comp_path']) > 0)
		$virtualPath = $_GET['v_comp_path'];
		
	$nodeInfos=getNodeInfosCarte($idSite, $db,$virtualPath);
	$parentvirtualPath = ereg_replace(',[^,]+$','',$virtualPath);
	$parentnodeInfos=getNodeInfosCarte($idSite, $db,$parentvirtualPath);
} else{
	if (strlen($_POST['v_comp_path']) > 0)
		$virtualPath = $_POST['v_comp_path'];
		$virtualPathDest = $_POST['v_comp_path_dest'];
	$nodeInfos=getNodeInfosCarte($idSite, $db,$virtualPath);
	$id=moveNodeCarte($db,$virtualPath,$_POST['node_id']);
	$parentvirtualPath = ereg_replace(',[^,]+$','',$virtualPath);
	$parentnodeInfos=getNodeInfosCarte($idSite, $db,$parentvirtualPath);
}
?>
<style type="text/css">
.toto {
	color: #ffffff;
	text-decoration: underline;
	background-color: #316AC5;
}
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
</style>
<script type="text/javascript">
  function chooseFolder() {
	window.open('chooseFoldercarte.php?arbomax=3','browser','scrollbars=yes,width=500,height=500,resizeable=yes,menubar=no');
  }
</script>
<link rel="stylesheet" type="text/css" href="<?php echo $URL_ROOT; ?>/css/bo.css">
<form name="managetree" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
  <table cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td colspan="3"><table  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="120"><img src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/tt_gestion_page.gif" width="117" height="18"></td>
            <td valign="middle" class="arbo" style="vertical-align:middle"> &gt; <small><?php echo getAbsolutePathStringCarte($db, $virtualPath,'carteArbo.php'); ?></small></td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td colspan="3"><img src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/vide.gif"></td>
    </tr>
    <tr>
      <td width="6" align="left" valign="top" class="arbo2"><br>
          <b><u>Arborescence :</u></b> <br>
          <table cellpadding="8" cellspacing="0" border="1" bordercolor="#FFFFFF">
            <tr>
              <td bgcolor="D2D2D2"><?php
print drawCompTreeCarte($db,$virtualPath);
?></td>
            </tr>
          </table>
          <br>
          <br>
      </td>
      <td width="5"  align="left" valign="top" class="arbo2"><img src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/vide.gif" width="15"></td>
      <td  align="left" valign="top" class="arbo2">
        <!-- contenu des actions -->
<?php
	if(!strlen($_POST['foldername'])&&$virtualPath != "0") {
?>
        <u><b><br>
      D&eacute;placer un dossier :</b></u> <br>
      <br>
      <table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF">
        <tr bgcolor="E6E6E6">
          <td class="arbo" style="vertical-align:middle">Ancien emplacement&nbsp;:</td>
          <td><input name="dummy" type="text" disabled class="arbo" id="dummy" value="<?php echo path2nodesReverseCarte( $db, $parentvirtualPath ); ?>" size="50">
              <input type="hidden" name="v_comp_path2" value="<?php echo $virtualPath; ?>"></td>
        </tr>
        <tr bgcolor="EEEEEE">
          <td bgcolor="EEEEEE" class="arbo" style="vertical-align:middle">Nouvel emplacement :</td>
          <td><input name="foldername" type="text" disabled class="arbo" id="foldername" value="<?php echo $parentnodeInfos['libelle']; ?>" size="20">
    &nbsp;&nbsp;<a href="#" class="arbo" onClick="chooseFolder();"><img src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png" border="0">&nbsp;choisir le dossier</a>
	<input name="node_id" id="node_id" type="hidden">
	<input type="hidden" name="v_comp_path" value="<?php echo $virtualPath; ?>"></td>
        </tr>
        <tr align="center" bgcolor="EEEEEE">
          <td colspan="2" bgcolor="D2D2D2" class="arbo"><a href="carteArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><strong>Annuler</strong></a>&nbsp;&nbsp;<a href="carteArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a> 
              - <a href="#" class="arbo" onClick="javascript:document.forms[0].submit();"><strong>Déplacer le dossier</strong></a>&nbsp;&nbsp;<a href="#" onClick="javascript:document.forms[0].submit();" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>
        </tr>
      </table>
      <br>
      <?php
	} else {
		// creation du sous dossier
		if($id!=false) {
?>
      <br>
      <br>
      <br>
      <b><span class="arbo">Le dossier <?php echo $_POST['foldername']; ?> a bien &eacute;t&eacute; d&eacute;plac&eacute; dans le dossier en <?php echo $nodeInfos['libelle']; ?></span></b>
      <?php
		} elseif($virtualPath == "0") { // Si on est à la racine, pas d'opération possible
?>
<u><b><br>
      D&eacute;placer un dossier :</b></u> <br>
	      <br>
      <br>
      <br>
      <b><span class="arbo">Il n'est pas possible d'effectuer cette action sur le dossier Racine.</span></b>
<?php
		}
		else {
?>
      <br>
      <br>
      <br>
      <b><span class="arbo">Un probl&egrave;me est survenu durant l'op&eacute;ration. <br>
      Veuillez r&eacute;essayer ou contacter l'administrateur si l'erreur persiste.</span></b>
      <?php
		}
	}
?></td>
    </tr>
  </table>
  </form>