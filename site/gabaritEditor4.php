<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/*
sponthus 07/06/2005

gestion des gabarits	
*/
// include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php'); semble inutile
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

activateMenu('gestiongabarit');  //permet de dérouler le menu contextuellement

// site sur lequel on est positionné
$idSite = $_SESSION['idSite_travail'];
if ($idSite == "") die ("Appel incorrect de la fonctionnalité");
$oSite = new Cms_site($idSite);

// objet gabarit
$oGab = new Cms_page($_GET['idGab']);

$DIR_GABARITS = $_SERVER['DOCUMENT_ROOT']."/backoffice/cms/gabarits";
$_SESSION['divArray']=null;	
$divArray = array();
$datatxt = $_POST['serializedData'];

foreach (explode('##', $datatxt) as $k => $v ) {
	if ($k==0)
		$v = preg_replace('/^#/','',$v);
	if ($k==(newSizeOf(explode('##', $datatxt))-1))
		$v = preg_replace('/#$/','',$v);
	$t = explode('#',$v);
	$id = $t[0];
	$attr = $t[1];
	$tmparray = array(
		'id' => $id
	);
	foreach(explode(',',$attr) as $kk => $vv) {
		$t = explode('=',$vv);
		if(strlen($t[0]) >0)
			$tmparray[$t[0]] = $t[1];
		if(strlen($t[2]) >0)
			$tmparray[$t[0]] = $t[1].'='.$t[2];
	}
	$divArray[$k] = $tmparray;
}
$divArray['gabarit'] = $oGab->getName_page();
// stockage dans div_array de l'id du gabarit
$divArray['id_gabarit'] = $oGab->getId_page();
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
if($_GET['id'] > 0) {
	$pageInfos = getPageById($_GET['id']);
	$name=$pageInfos['name'];
	$folder_id=$pageInfos['node_id'];
	$foldername=$pageInfos['libelle'];
	$disabled="disabled";
}

?>
<script language="Javascript" type="text/javascript">document.title="Pages BB";</script>
<script language="javascript" type="text/javascript">
  function chooseFolder() {
    window.open('chooseFolder.php?idSite=<?php echo $idSite; ?>','browser','scrollbars=yes,width=500,height=500,resizable=yes,menubar=no');
  }
  function previewPage() {
    window.open('previewGabarit.php?id=<?php echo $_GET['id']; ?>','preview','scrollbars=yes,resizable=yes,menubar=no');
  }
</script>
<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style><form name="savePage" action="gabaritEditor5.php?id=<?php echo $_GET['id']; ?>&idGab=<?php echo $_GET['idGab']; ?>&widthGab=<?php echo $_GET['widthGab']; ?>&heightGab=<?php echo $_GET['heightGab']?>" method="post">
<div class="ariane"><span class="arbo2">GABARIT&nbsp;>&nbsp;</span>
<span class="arbo3">Etape 4 : Nom du gabarit</span></div>

<?php
// site de travail
//if (DEF_MENUS_MINISITES == "ON") print(putAfficheSite());
?>

<span class="arbo">
<input type="hidden" name="serial" id="serial" value='<?php echo $datas; ?>'>
<input type="hidden" name="node_id" id="node_id" value="<?php echo $folder_id; ?>">
<a href="#" class="arbo" onClick="previewPage();">Visualiser</a> la page assemblée.</span>
<br>
<br>
<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo" cellpading="0">
<tr bgcolor="E6E6E6">
  <td align="right">nom&nbsp;:&nbsp;</td>
  <td><input name="name" type="text" class="arbo" id="nameid" value="<?php echo $name; ?>" size="50" <?php echo $disabled;?> pattern="^([a-z]|[A-Z]|[0-9]|_)+$" errorMsg="Merci de spécifier un nom ne comportant que des caractères alphanumériques sans espace.">
  <input name="foldername" type="hidden" id="foldername" value="<?php echo $foldername;?>"></td>
</tr>
<tr bgcolor="D2D2D2">
  <td align="left"><input name="Retour" type="button" class="arbo" onClick="document.location='gabaritEditor3.php?<?php echo $_SERVER['QUERY_STRING']; ?>';" value="<< Retour"></td>
  <td align="right"><input name="Suite >>" type="button" class="arbo" onClick="if(validate_form()){ document.forms[0].name.disabled=false; submit();}" value="Suite >>"></td>
</tr>
</table>
</form>
