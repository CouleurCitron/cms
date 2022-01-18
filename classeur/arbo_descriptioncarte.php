<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/* 



****************************************************

*** Gestion de l'arbo des cartes                 ***

*** Classeur de cartes                           ***

*** Récupéré du fichier arbo_descriptionpage.php ***

****************************************************



*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbocarte.lib.php');

activateMenu('gestioncarte');  //permet de dérouler le menu contextuellement
$result=false;
$virtualPath = 0;
$nodeInfos=array();
$nodeChildren = array();
$orders = array();

if(!strlen($_POST['folderdescription'])) {
	if (strlen($_GET['v_comp_path']) > 0) $virtualPath = $_GET['v_comp_path'];
	$nodeInfos=getNodeInfosCarte($idSite, $db,$virtualPath);
	$nodeChildren=getNodeChildrenCarte($db,$virtualPath);
} else{
	if (strlen($_POST['v_comp_path']) > 0) $virtualPath = $_POST['v_comp_path'];
	$result = saveNodeDescriptionCarte($_POST['folderdescription'],$db,$virtualPath);
	$nodeInfos=getNodeInfosCarte($idSite, $db,$virtualPath);
	$nodeChildren=getNodeChildrenCarte($idSite, $db,$virtualPath);
}
?>



<style type="text/css">



.toto {



	color: #ffffff;



	text-decoration: underline;



	background-color: #316AC5;



};



body {



	margin-left: 0px;



	margin-top: 0px;



	margin-right: 0px;



	margin-bottom: 0px;



}



</style>



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



      <td align="left" valign="top" class="arbo2"><br>



          <b><u>Arborescence :</u></b> <br>



          <br>



          <table cellpadding="8" cellspacing="0" border="1" bordercolor="#FFFFFF">



            <tr>



              <td bgcolor="D2D2D2"><?php
print drawCompTreeCarte($db,$virtualPath);
?></td>



            </tr>



        </table></td>



      <td width="15" align="left" valign="top" class="arbo2"><img src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/vide.gif" width="15"></td>



      <td align="left" valign="top" class="arbo2">



        <!-- contenu des actions -->



        <u><b><br>



        <?php
	if(!strlen($_POST['folderdescription'])) {
?>



        Description d'un dossier</b></u><br>



      <br>



      <table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF">



        <tr bgcolor="E6E6E6">



          <td class="arbo" style="vertical-align:middle"><small>Description&nbsp;:&nbsp;</small></td>



          <td><input type="hidden" id="v_comp_path" name="v_comp_path" value="<?php echo $_REQUEST['v_comp_path']; ?>"><textarea name="folderdescription" class="arbo textareaEdit" id="folderdescription" type="text"><?php echo $nodeInfos['description']; ?></textarea></td>



        </tr>



        <tr align="center" bgcolor="EEEEEE">



            <td colspan="2" bgcolor="D2D2D2" class="arbo"><a href="carteArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><strong>Annuler</strong></a>&nbsp;&nbsp;<a href="carteArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a> 



              - <a href="#" class="arbo" onClick="javascript:document.forms[0].submit();"><strong>Enregistrer</strong></a>&nbsp;&nbsp;<a href="#" onClick="javascript:document.forms[0].submit();" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>



        </tr>



      </table>



      <br>



      <?php
	} else {
		// renommage d'un dossier
		if($result) {
?>



      <br>



      <br>



      <br>



      <b><span class="arbo">La description du dossier <?php echo $nodeInfos['libelle']; ?> a bien &eacute;t&eacute; renseign&eacute;e</span></b>



      <?php
		} else {
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



    <!--



 <tr height="1" bgcolor="#000000">



  <td colspan="5"><img src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/vide.gif"></td>



 </tr>-->



  </table>



  </form>