<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 

*****************************************************
*** Gestion de l'arbo des cartes                  ***
*** Classeur de cartes                            ***
*** Récupéré du fichier arbo_createfolderpage.php ***
*****************************************************

*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbocarte.lib.php');

activateMenu('gestioncarte');  //permet de dérouler le menu contextuellement

$idSite = $_GET['idSite'];
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];
// pas de site
if ($idSite == "") die ("Appel incorrect de la fonctionnalité");
$id=0;

$virtualPath = 0;

$nodeInfos=array();

if(!strlen($_POST['foldername']) ) {

	if (strlen($_GET['v_comp_path']) > 0)

		$virtualPath = $_GET['v_comp_path'];

	$nodeInfos=getNodeInfosCarte($idSite, $db,$virtualPath);

} else{

	if (strlen($_POST['v_comp_path']) > 0)

		$virtualPath = $_POST['v_comp_path'];

	$nodeInfos=getNodeInfosCarte($idSite, $db,$virtualPath);

	$id=addNodeCarte($idSite, $db,$virtualPath,$_POST['foldername']);

	if($id!=false) {

		$virtualPath.=",$id";

	}

}


// On désactive la création de sous dossiers si l'arbo a déjà 3 niveaux
$toodeep = (substr_count($virtualPath,",")>4);
if($toodeep) $id=false;

?>

<style type="text/css">

.toto {

	color: #ffffff;

	text-decoration: underline;

	background-color: #316AC5;

};

</style>

<link rel="stylesheet" type="text/css" href="<?php echo $URL_ROOT; ?>/css/bo.css">

<style type="text/css">

<!--

body {

	margin-left: 0px;

	margin-top: 0px;

	margin-right: 0px;

	margin-bottom: 0px;

}

-->

</style><form name="managetree" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
<div class="ariane">
<span class="arbo2">ARBORESCENCE&nbsp;>&nbsp;</span>
<span class="arbo3">Créer un sous dossier&nbsp;>&nbsp;</span>
<span class="arbo4"><?php echo getAbsolutePathStringCarte($db, $virtualPath,'carteArbo.php'); ?></span>
</div>
  <table cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td colspan="3"><img src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/vide.gif"></td>
   </tr>
    <tr>
      <td width="120px" align="left" valign="top" class="arbo"><br>
          <b>Arborescence :</b> <br>

            <table cellpadding="8" cellspacing="0" border="1" bordercolor="#FFFFFF">
            <tr>
              <td bgcolor="D2D2D2"><?php

print drawCompTreeCarte($idSite, $db,$virtualPath);

?></td>

            </tr>

          </table>

          <br>

          <br>

      </td>

      <td width="5"  align="left" valign="top" class="arbo2"><img src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/vide.gif" width="15"></td>

      <td  align="left" valign="top" class="arbo">

        <!-- contenu des actions -->

        <?php

	if( !$toodeep && !strlen($_POST['foldername']) ) {

?>

        <u><b><br>

      Cr&eacute;ation d'un sous dossier :</b></u> <br>

      <br>
      <table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF">

        <tr>

          <td  class="arbo">Dossier P&egrave;re&nbsp;:</td>

          <td align="left"><input name="dummy" type="text" disabled class="arbo" id="dummy" value="<?php echo $nodeInfos['libelle']; ?>" size="14">

              <input type="hidden" name="v_comp_path" value="<?php echo $virtualPath; ?>"></td>

        </tr>

        <tr bgcolor="E6E6E6">

          <td class="arbo" style="vertical-align:middle"><small>Nom du dossier&nbsp;:</small></td>

          <td><input name="foldername" type="text" class="arbo" id="foldername" size="25" maxlength="255"></td>

        </tr>

        <!--tr bgcolor="E6E6E6">

          <td class="arbo" style="vertical-align:middle"><small>Description&nbsp;:</small></td>

          <td><input name="folderdescription" type="text" class="arbo" id="folderdescription" size="25" maxlength="255"></td>

        </tr-->

        <tr align="center" bgcolor="EEEEEE">

          <td colspan="2" bgcolor="D2D2D2" class="arbo"><a href="carteArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><strong>Annuler</strong></a>&nbsp;&nbsp;<a href="carteArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a> 

              - <a href="#" class="arbo" onClick="javascript:document.forms[0].submit();"><strong>Cr&eacute;er le dossier</strong></a>&nbsp;&nbsp;<a href="#" onClick="javascript:document.forms[0].submit();" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>

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

      <b><span class="arbo">Le dossier <?php echo $_POST['foldername']; ?> a bien &eacute;t&eacute; cr&eacute;&eacute; dans le dossier</span></b>

      <?php $nodeInfos['libelle']; ?>

      <?php

		} else {

			if($toodeep) { // On ne peut pas créer une arbo à plus de 3 niveaux!
?>

      <br>

      <br>

      <br>

      <b><span class="arbo">Impossible de créer un nouveau sous-dossier. <br>

      Vous avez atteint les 3 niveaux maximum autorisés.</span></b>

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

	}

?></td>

    </tr>

    <!--

 <tr height="1" bgcolor="#000000">

  <td colspan="5"><img src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/vide.gif"></td>

 </tr>-->

  </table>

  </form>











