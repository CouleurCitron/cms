<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/* 



****************************************************

*** Gestion de l'arbo des cartes                 ***

*** Classeur de cartes                           ***

*** Récupéré du fichier arbo_orderfolderpage.php ***

****************************************************



*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbocarte.lib.php');

$idSite = $_GET["idSite"];

if ($idSite == "") $idSite = $_SESSION['idSite_travail'];
// pas de site
if ($idSite == "") die ("Appel incorrect de la fonctionnalité");

activateMenu('gestioncarte');  //permet de dérouler le menu contextuellement
$result=false;
$virtualPath = 0;
$nodeInfos=array();
$nodeChildren = array();
$orders = array();

if(!preg_match( '/^order_.+$/', join(",",array_keys($_POST) ) ) ) { // Première visite
	if (strlen($_GET['v_comp_path']) > 0)
		$virtualPath = $_GET['v_comp_path'];
	$nodeInfos=getNodeInfosCarte($idSite,$db,$virtualPath);
	$nodeChildren=getNodeChildrenCarte($idSite, $db,$virtualPath);
} else{
	if (strlen($_POST['v_comp_path']) > 0)
		$virtualPath = $_POST['v_comp_path'];
	$nodeInfos=getNodeInfosCarte($idSite,$db,$virtualPath);
	foreach($_POST as $k => $v) {
		if(preg_match('/^order_.+$/',$k)) {
			$id = preg_replace('/^order_/','',$k);
			if(intval($id) > 0) {
				 $orders[$id] = $v; // création du tableau [ [id_noeud1,ordre_noeud1], [id_noeud2,ordre_noeud2], ... ]
			}
		}
	}
	$result = saveNodeOrderCarte($orders,$db,$virtualPath);
	$nodeChildren=getNodeChildrenCarte($idSite,$db,$virtualPath);
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
print drawCompTreeCarte($idSite, $db,$virtualPath);
?></td>



            </tr>



        </table></td>



      <td width="15" align="left" valign="top" class="arbo2"><img src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/vide.gif" width="15"></td>



      <td align="left" valign="top" class="arbo2">



        <!-- contenu des actions -->



        <u><b><br>



        <?php
	if(!strlen($_POST['foldername'])) {
?>Ordonner un dossier</b></u><br>



      <br>

<?php
if(count($nodeChildren)>0) { // On a des choses à faire!
	 echo '<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF">';
}
// Lister chaque sous-dossier du dossier en cours
foreach($nodeChildren as $elt) {
?>

        <tr bgcolor="E6E6E6">



          <td class="arbo" style="vertical-align:middle"><small>Entr&eacute;e&nbsp;:&nbsp;<?php echo $elt['libelle']; ?></small></td>



          <td><input name="order_<?php echo $elt['id']; ?>" type="text" class="arbo" id="order_<?php echo $elt['id']; ?>" value="<?php echo $elt['order']; ?>" size="4" maxlength="4"></td>
	   </tr>
<?php
}
// Fin Lister chaque sous-dossier du dossier en cours
if(count($nodeChildren)>0) { // On a des choses à faire!
?>





        <tr align="center" bgcolor="EEEEEE">



          <td colspan="2" bgcolor="D2D2D2" class="arbo"><a href="carteArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><strong>Annuler</strong></a>&nbsp;&nbsp;<a href="carteArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a> 



              - <a href="#" class="arbo" onClick="javascript:document.forms[0].submit();"><strong>Enregistrer</strong></a>&nbsp;&nbsp;<a href="#" onClick="javascript:document.forms[0].submit();" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>



        </tr>

      </table>

<?php
}
else { // Il n'y a rien à ordonner => on le dit!
?>

        <b><span class="arbo">Il n'y a aucun sous-dossier à ordonner dans ce dossier</span></b>

<?php
}
?>



      <br>



<?php
	} else {
		// déplacement d'un dossier
		if($result) {
?>



      <br>



      <br>



      <br>



      <b><span class="arbo">Le dossier <?php echo $nodeInfos['libelle']; ?> a bien &eacute;t&eacute; param&eacute;tr&eacute;</span></b>



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