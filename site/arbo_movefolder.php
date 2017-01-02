<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/* 

$Author: raphael $

$Revision: 1.1 $





$Log: arbo_movefolder.php,v $
Revision 1.1  2013-09-30 09:43:10  raphael
*** empty log message ***

Revision 1.5  2013-03-01 10:28:20  pierre
*** empty log message ***

Revision 1.4  2012-07-31 14:24:50  pierre
*** empty log message ***

Revision 1.3  2012-04-03 12:23:52  thao
*** empty log message ***

Revision 1.2  2012-04-03 10:34:32  thao
*** empty log message ***

Revision 1.1  2010-08-23 08:29:51  pierre
*** empty log message ***

Revision 1.12  2009-09-24 08:53:13  pierre
*** empty log message ***

Revision 1.11  2008-11-28 14:09:55  pierre
*** empty log message ***

Revision 1.10  2008-11-06 12:03:54  pierre
*** empty log message ***

Revision 1.9  2008-07-16 12:43:07  pierre
*** empty log message ***

Revision 1.8  2008/07/16 10:55:28  pierre
*** empty log message ***

Revision 1.7  2008/07/16 10:48:36  pierre
*** empty log message ***

Revision 1.6  2008/07/16 10:04:38  pierre
*** empty log message ***

Revision 1.5  2007/08/09 13:19:41  thao
*** empty log message ***

Revision 1.2  2007/08/09 10:56:30  thao
*** empty log message ***

Revision 1.1  2007/08/08 14:26:26  thao
*** empty log message ***

Revision 1.4  2007/08/08 14:16:15  thao
*** empty log message ***

Revision 1.3  2007/08/08 13:56:04  thao
*** empty log message ***

Revision 1.2  2007/03/27 15:10:39  pierre
*** empty log message ***

Revision 1.1  2006/12/15 12:30:11  pierre
*** empty log message ***

Revision 1.1.1.1  2006/01/25 15:14:32  pierre
projet CCitron AWS 2006 Nouveau Website

Revision 1.3  2005/12/20 08:23:25  sylvie
application nouveaux styles titre

Revision 1.2  2005/10/27 09:25:55  sylvie
*** empty log message ***

Revision 1.1.1.1  2005/10/20 13:10:53  pierre
Espace V2

Revision 1.4  2005/06/08 10:08:19  michael
Impossibilité de toucher à la Racine (Déplacement/Renommage/Suppression)
Suppression des espaces

Revision 1.3  2005/05/30 10:44:36  michael
Réparation de fonctions dans les briques et les pages
Renommer/ Déplacer...
Gestion des caractères spéciaux

Revision 1.2  2005/05/23 17:09:28  pierre
ajout option annuler


Revision 1.1.1.1  2005/04/18 13:53:29  pierre

again



Revision 1.1.1.1  2005/04/18 09:04:21  pierre

oremip new



Revision 1.1.1.1  2004/11/03 13:49:54  ddinside

lancement du projet - import de adequat



Revision 1.3  2004/06/16 15:23:19  ddinside

inclusion corrections



Revision 1.2  2004/04/26 08:07:09  melanie

*** empty log message ***



Revision 1.1.1.1  2004/04/01 09:20:27  ddinside

Création du projet CMS Couleur Citron nom de code : tipunch



Revision 1.1  2003/11/27 14:45:56  ddinside

ajout gestion arbo disque non finie



Revision 1.2  2003/11/26 13:08:42  ddinside

nettoyage des fichiers temporaires commités par erreur

ajout config spaw

corrections bug menu et positionnement des divs



Revision 1.2  2003/10/10 08:01:52  ddinside

mise à jour modifications

nettoyage fichiers inutiles

ajout gabarits



Revision 1.1  2003/10/07 08:15:53  ddinside

ajout gestion de l'arborescence des composants



Revision 1.1  2003/09/25 14:55:16  ddinside

gestion de l'arborescene des composants : ajout

*/

// sponthus 17/06/05
// arbo des mini sites


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
include_once('backoffice/cms/site/minisite.inc.php');

activateMenu('gestioncontenu');  //permet de dérouler le menu contextuellement




// site connecté
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

// pas de site
if ($idSite == "") die ("Appel incorrect de la fonctionnalité");

$id=0;

$virtualPath = 0;

$nodeInfos=array();

if($_POST['node_id']=="") {

	if (strlen($_GET['v_comp_path']) > 0)

		$virtualPath = $_GET['v_comp_path'];
		
	$nodeInfos=getNodeInfos($db,$virtualPath);
	$parentvirtualPath = ereg_replace(',[^,]+$','',$virtualPath);
	$parentnodeInfos=getNodeInfos($db, $parentvirtualPath);

} else{

	if (strlen($_POST['v_comp_path']) > 0)

		$virtualPath = $_POST['v_comp_path'];

		$virtualPathDest = $_POST['v_comp_path_dest'];

	$nodeInfos=getNodeInfos($db,$virtualPath);
	$virtualPathFinal="";
	$virtualPathFinal.=$_POST['node_id'];
	$new_nodeInfos = getNodeInfos($db, $_POST['node_id']);
	$virtualPathFinal=$new_nodeInfos['parent'].",".$virtualPathFinal;
	while ($new_nodeInfos['parent']!=0) {
		$new_nodeInfos = getNodeInfos($db, $new_nodeInfos['parent']);
		$virtualPathFinal=$new_nodeInfos['parent'].",".$virtualPathFinal;
	}
	
	$array_path = split(',',$virtualPath);
	$virtualPathFinal.=",".$array_path[sizeof($array_path)-1];
	$id = moveNode($idSite, $db,$virtualPath,$_POST['node_id']);
	if ($isMinisite)  $id_minisite =  moveNodeMinisite($idSite, $db,$virtualPath,$_POST['node_id']);
	$virtualPath = $virtualPathFinal;
	$parentvirtualPath = ereg_replace(',[^,]+$','',$virtualPath);
	$parentnodeInfos=getNodeInfos($db,$parentvirtualPath);
	if ($id == true) $id=true;
	else $id=false;
	?>
<script type="text/javascript">
	//document.location.href="<?php echo $_SERVER['PHP_SELF'];?>?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?>&id=true<?php echo $param; ?>";
</script>
<?php
	
}

?>


<script type="text/javascript">
  function chooseFolder() {
	window.open('chooseFolder.php?v_comp_path=<?php echo  preg_replace("/,[^,]*$/","",$virtualPath) ; ?>','browser','scrollbars=yes,width=500,height=500,resizeable=yes,menubar=no');
  }
</script>



<form id="managetree" name="managetree" action="<?php echo $_SERVER['PHP_SELF'];?>?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?><?php echo $param; ?>" method="post">
<div class="ariane">
<span class="arbo2">ARBORESCENCE&nbsp;>&nbsp;</span>
<span class="arbo3">Déplacer un dossier&nbsp;>&nbsp;</span>
<span class="arbo4"><?php echo getAbsolutePathString($idSite, $db, $virtualPath,'pageArbo.php'); ?></span>
</div>
  <table cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td colspan="3"><img src="/backoffice/cms/img/vide.gif"></td>
    </tr>
    <tr>
      <td width="6" align="left" valign="top" class="arbo">
	  <div id="arborescence"><p><b><u>Arborescence :</u></b></p>
          <table cellpadding="8" cellspacing="0" border="1" bordercolor="#FFFFFF">

            <tr>

              <td bgcolor="D2D2D2"><?php

//print drawCompTree($idSite, $db,$virtualPath);
print drawCompTree($idSite, $db, $virtualPath, null, "/backoffice/cms/site/arbo.php");
?></td>

            </tr>

          </table>

         </div>

      </td>

      <td width="5"  align="left" valign="top" class="arbo2"><img src="/backoffice/cms/img/vide.gif" width="15"></td>

      <td  align="left" valign="top" class="arbo">

        <!-- contenu des actions -->

<?php
	if(!strlen($_POST['foldername'])&&   $virtualPath != "0") {
?>
<?php
		if(isset($_GET['id']) && $_GET['id']!=false) {

		?>

      <br />

      <br />

      <br />
 <table cellpadding="0" cellspacing="0" border="0">
    <tr><td>
      <b><span class="arbo">Le dossier <?php echo $_POST['foldername']; ?> a bien &eacute;t&eacute; d&eacute;plac&eacute; dans le dossier en <?php echo $nodeInfos['libelle']; ?></span></b>
	</td></tr></table>
      <?php

		} else {
		?>
        <u><b><br />

      D&eacute;placer un dossier :</b></u> <br />

      <br />

      <table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF">

        <tr bgcolor="E6E6E6">

          <td class="arbo" style="vertical-align:middle">Ancien emplacement&nbsp;:</td>

          <td><input name="dummy" type="text" disabled class="arbo" id="dummy" value="<?php echo path2nodesReverse($idSite, $db, $parentvirtualPath ); ?>" size="50">

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

          <td colspan="2" bgcolor="D2D2D2" class="arbo"><a href="pageArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><strong>Annuler</strong></a>&nbsp;&nbsp;<a href="pageArbo.php?menuOpen=<?php echo $_GET['menuOpen']; ?>" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a> 

              - <a href="#" class="arbo" onClick="javascript:document.forms['managetree'].submit();"><strong>Déplacer le dossier</strong></a>&nbsp;&nbsp;<a href="#" onClick="javascript:document.forms['managetree'].submit();" class="arbo"><img border="0" src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/2013/icone/go.png"></a></td>

        </tr>

      </table>
		
      <br />

      <?php
	   }
		} elseif($virtualPath == "0") { // Si on est à la racine, pas d'opération possible
?>
<u><b><br />
      D&eacute;placer un dossier :</b></u> <br />
	      <br />
      <br />
      <br />
      <b><span class="arbo">Il n'est pas possible d'effectuer cette action sur le dossier Racine.</span></b>
<?php
		}
		else {
		// creation du sous dossier

		if($id!=false) {

?>

      <br />

      <br />

      <br />

      <b><span class="arbo">Le dossier <?php echo $_POST['foldername']; ?> a bien &eacute;t&eacute; d&eacute;plac&eacute; dans le dossier en <?php echo $nodeInfos['libelle']; ?></span></b>

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






