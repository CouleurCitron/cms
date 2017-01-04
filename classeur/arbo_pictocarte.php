<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/* 



***************************************************

*** Gestion de l'arbo des cartes                ***

*** Classeur de cartes                          ***

*** Création d'un picto pour chaque dossier     ***

***************************************************



*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbocarte.lib.php');

activateMenu('gestioncarte');  //permet de dérouler le menu contextuellement
// ID_noeud par défaut auquel est associée la carte
if( $_GET['v_comp_path']!="" ) {
	$id_node = array_pop(explode(',',$_GET['v_comp_path']));
	$infos_node = getNodeInfosCarte($idSite,$db,$_GET['v_comp_path']);
}

$idSite = $_GET['idSite'];
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];
// pas de site
if ($idSite == "") die ("Appel incorrect de la fonctionnalité");

$virtualPath = 0;
$nodeInfos=array();
$nodeChildren = array();
if (strlen($_GET['v_comp_path']) > 0)
	$virtualPath = $_GET['v_comp_path'];
$nodeInfos=getNodeInfosCarte($idSite,$db,$virtualPath);
$nodeChildren=getNodeChildrenCarte($idSite, $db,$virtualPath);
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



<?php
// Chargement de la classe
require_once('cms-inc/lib/fileUpload/upload.class.php');
// Instanciation d'un nouvel objet "upload"
$Upload = new Upload();
// Pour ajouter des attributs aux champs de type file
$Upload-> FieldOptions = 'style="border-color:black;border-width:1px;"';
// Pour indiquer le nombre de champs désiré
$Upload-> Fields = 1;
// Initialisation du formulaire
$Upload-> InitForm();
?>

<br>

<form method="post" enctype="multipart/form-data" name="formulaire" id="formulaire" action="arbo_uploadpictoCarte.php">
<div class="ariane">
<span class="arbo2">ARBORESCENCE&nbsp;>&nbsp;</span>
<span class="arbo3">Associer un picto&nbsp;>&nbsp;</span>
<span class="arbo4"><?php echo getAbsolutePathStringCarte($db, $virtualPath,'carteArbo.php'); ?></span>
</div>
<br>

  <table cellpadding="0" cellspacing="0" border="0">


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

        Associer un picto à un dossier</b></u><br>

      <br>

      <table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF">

	  	<?php
		if($picto = getArbopictoCarte($id_node)) {	// On cherche le picto s'il existe
		?>

        <tr bgcolor="E6E6E6">

          <td class="arbo" style="vertical-align:middle"><small>Picto existant&nbsp;:&nbsp;</small></td>

          <td><img src="showpictoCarte.php?img_id=<?php echo  $id_node ; ?>"></td>

        </tr>

		<?php
		}
		?>

        <tr bgcolor="E6E6E6">

          <td class="arbo" style="vertical-align:middle"><small>Fichier image&nbsp;:&nbsp;</small></td>

          <td><input type="hidden" id="v_comp_path" name="v_comp_path" value="<?php echo $_REQUEST['v_comp_path']; ?>"><?php

		  

		  	// Affichage du champ MAX_FILE_SIZE

			print $Upload-> Field[0];



			// Affichage du premier champ de type FILE

			print $Upload-> Field[1] . '<br>';



		  ?></td>

        </tr>

        <tr align="center" bgcolor="EEEEEE">

            <td colspan="2" bgcolor="D2D2D2" class="arbo"><a href="carteArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><strong>Annuler</strong></a>&nbsp;&nbsp;<a href="carteArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a> 

              - <a href="#" class="arbo" onClick="javascript:document.forms[0].submit();"><strong>Enregistrer</strong></a>&nbsp;&nbsp;<a href="#" onClick="javascript:document.forms[0].submit();" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>

        </tr>

      </table>

      <br><br>

	  <b><span class="arbo">Attention le fichier doit être au format &nbsp; gif / png / jpg</span></b>

	</td>

    </tr>

  </table>



<input type="hidden" name="node_id" value="<?php echo  $id_node ; ?>">

</form>

