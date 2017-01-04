<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/utils/dir.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
include_once('backoffice/cms/site/minisite.inc.php');
 

// site connecté
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

// pas de site
if ($idSite == "") die ("Appel incorrect de la fonctionnalité");


$id=0;

$virtualPath = 0;

$nodeInfos=array();

if(!strlen($_POST['foldername'])) {

	if (strlen($_GET['v_comp_path']) > 0) $virtualPath = $_GET['v_comp_path'];

	$nodeInfos=getNodeInfos($db, $virtualPath);

} else{

	if (strlen($_POST['v_comp_path']) > 0) 	$virtualPath = $_POST['v_comp_path'];
	
	$nodeInfos=getNodeInfos($db, $virtualPath);
	 
	
	// tester si le nom existe déjà
	
	//$_POST['foldername'];
	
	
	
	
	
	
	
	
	/*$sql = " SELECT node_libelle FROM cms_arbo_pages WHERE node_libelle LIKE '".$_POST['foldername']."'; "; 
	$rs = $db->Execute($sql);
	
	
	if($rs!=false) {
	
		$nb_result = $rs->rowcount();
		
		while ($nb_result > 0) {
			$_POST['foldername'] = $_POST['foldername'].'-copie';
			$sql = " SELECT node_libelle FROM cms_arbo_pages WHERE node_libelle LIKE '".$_POST['foldername']."'; "; 
			$rs = $db->Execute($sql);
			
			if($rs!=false)   
				$nb_result = $rs->rowcount(); 
			
		}
		 
		
	}
	else{
		echo "<p>Erreur.</p>"; 
	}*/
	
	
	$node_id_to_duplicate = array_pop(explode(',',$_POST['v_comp_path']));
	//echo "node_id_to_duplicate ".$node_id_to_duplicate."<br />";
	
	
	//echo $_POST['foldername']."<br />";
	
	/*$aTab = explode(',',$virtualPath);
	array_pop($aTab);
	
	$virtualPath = implode(',', $aTab ); 
	
	$nodeInfos=getNodeInfos($db, $virtualPath); 

	$id = addNode($idSite, $db, $virtualPath,$_POST['foldername']);
	
	*/ 
	 
	
	$node_duplicated = $id;
	//pre_dump($_POST);
	include_once ("dupliMinisiteProcess.php"); 
	
	//pre_dump($_POST);
	
	if($id!=false) {
		$virtualPath.=",$id";
		 
		$oMinisite = new Cms_minisite (); 
		$oMinisite->set_name($_POST['foldername']);
		$oMinisite->set_site($_SESSION["idSite"]);
		$oMinisite->set_theme((int)$_POST['theme']);
		$oMinisite->set_node($id);
		$oMinisite->set_statut(DEF_ID_STATUT_LIGNE);
		dbInsertWithAutoKey($oMinisite);
		//pre_dump($oMinisite);
	}
	
}

?>


<form id="managetree" name="managetree" action="<?php echo $_SERVER['PHP_SELF'];?>?idSite=<?php echo $idSite; ?><?php echo $param; ?>" method="post">

<input type="hidden" name="idSite" value="<?php echo $idSite; ?>">
 


  <table cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td colspan="3"><img src="/backoffice/cms/img/vide.gif"></td>
   </tr>
    <tr>
      <td width="6" align="left" valign="top" class="arbo">&nbsp; </td>

      <td width="5"  align="left" valign="top" class="arbo2"><img src="/backoffice/cms/img/vide.gif" width="15"></td>

      <td  align="left" valign="top" class="arbo">

        <!-- contenu des actions -->

        <?php

	if(!strlen($_POST['foldername'])) {

?>

        <b><br />

      Duplication d'un mini-site :</b> <br />

      <br />

      <table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF">

        <tr>

          <td  class="arbo">Dossier à dupliquer :</td>

          <td align="left"><input name="dummy" type="text" disabled class="arbo" id="dummy" value="<?php echo $nodeInfos['libelle']; ?>" size="14">

              <input type="hidden" name="v_comp_path" value="<?php echo $virtualPath; ?>"></td>

        </tr>

        <tr bgcolor="E6E6E6">

          <td class="arbo" style="vertical-align:middle"><small>Nom du nouveau dossier&nbsp;:</small></td>

          <td><input name="foldername" type="text" class="arbo" id="foldername" size="25" maxlength="255" value="<?php echo $nodeInfos['libelle']."-copie"; ?>"></td>

        </tr> 
        
        
        <tr bgcolor="E6E6E6">

          <td class="arbo" style="vertical-align:middle"><small>Thème&nbsp;:</small></td>

          <td><select name="theme" id="theme">
                  <?php
                  $aTheme = dbGetObjects('cms_theme');
                  ?>
                  <option value="-1">-- garder l'ancien thème --</option>    
                  <?php
                  foreach($aTheme as $oTheme){
                  ?>
                  <option value="<?php echo $oTheme->id; ?>"><?php echo $oTheme->nom; ?></option>
                  <?php } ?>
              </select></td>

        </tr> 
        
		
        <tr align="center" bgcolor="EEEEEE">

          <td colspan="2" bgcolor="D2D2D2" class="arbo"><a href="pageArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><strong>Annuler</strong></a>&nbsp;&nbsp;<a href="pageArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a> 

              - <a href="#" class="arbo" onClick="javascript:document.forms['managetree'.submit();"><strong>Dupliquer le mini-site</strong></a>&nbsp;&nbsp;<a href="#" onClick="javascript:document.forms['managetree'].submit();" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>

        </tr>

      </table>

      <br />

      <?php

	} else {

		// creation du sous dossier

		if($id!=false) {

?>

      <br />

      <br />

      <br />

      <b><span class="arbo">Le dossier <?php echo $_POST['foldername']; ?> a bien &eacute;t&eacute; cr&eacute;&eacute; dans le dossier</span></b>

      <?php $nodeInfos['libelle']; ?>
		
		<br />
		
		<br />
		
		<br />
		
		<b><span class="arbo"> Merci de regénérer les pages pour les créer : <a href="/backoffice/cms/regenerateAll.php?menuOpen=true" target="_parent">regénérer</a></span></b>
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











