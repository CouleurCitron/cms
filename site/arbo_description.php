<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 
$Author: quentin $
$Revision: 1.2 $

$Log: arbo_description.php,v $
Revision 1.2  2013-10-07 15:09:51  quentin
*** empty log message ***

Revision 1.1  2013-09-30 09:43:09  raphael
*** empty log message ***

Revision 1.6  2013-04-05 14:27:30  thao
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

Revision 1.8  2009-09-24 08:53:13  pierre
*** empty log message ***

Revision 1.7  2008-07-16 12:43:07  pierre
*** empty log message ***

Revision 1.6  2008/07/16 10:55:28  pierre
*** empty log message ***

Revision 1.5  2008/07/16 10:48:36  pierre
*** empty log message ***

Revision 1.4  2008/07/16 10:04:38  pierre
*** empty log message ***

Revision 1.3  2007/08/09 10:56:30  thao
*** empty log message ***

Revision 1.2  2007/08/09 08:37:37  thao
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

Revision 1.4  2005/12/20 08:23:25  sylvie
application nouveaux styles titre

Revision 1.3  2005/11/24 11:43:08  pierre
description des nodes en urlencode (stocke plusieurs variables)

Revision 1.2  2005/10/27 09:25:55  sylvie
*** empty log message ***

Revision 1.1.1.1  2005/10/20 13:10:53  pierre
Espace V2

Revision 1.2  2005/05/23 17:09:28  pierre
ajout option annuler

Revision 1.1  2005/05/23 16:20:04  pierre
ajout de la gestion des descriptions de node

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

Revision 1.2  2004/02/12 15:56:16  ddinside
mise à jour plein de choses en fait, mais je sais plus quoi parce que ça fait longtemps que je l'avais pas fait.
Mea Culpa...

Revision 1.1  2003/11/27 14:45:56  ddinside
ajout gestion arbo disque non finie

Revision 1.2  2003/11/26 13:08:42  ddinside
nettoyage des fichiers temporaires commités par erreur
ajout config spaw
corrections bug menu et positionnement des divs

Revision 1.2  2003/10/07 08:53:28  ddinside
ajout gestionnaire de contenu

Revision 1.1  2003/10/07 08:15:53  ddinside
ajout gestion de l'arborescence des composants

Revision 1.1  2003/09/25 14:55:16  ddinside
gestion de l'arborescene des composants : ajout
*/

// sponthus 17/06/05
// arbo des mini sites

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
if(!strlen($_POST['folderdescription'])) {
	if (strlen($_GET['v_comp_path']) > 0) $virtualPath = $_GET['v_comp_path'];
	$nodeInfos=getNodeInfos($db,$virtualPath);
	$nodeChildren=getNodeChildren($idSite, $db,$virtualPath);

} else{
	if (strlen($_POST['v_comp_path']) > 0) $virtualPath = $_POST['v_comp_path'];
		// on recompose la description si folderdescriptionlength > 0
		
		
		
		if (intval($_POST['folderdescriptionlength']) > 0){
			$folderdescription = "";
			for ($i=1;$i<=intval($_POST['folderdescriptionlength']);$i++){
				$folderdescription .= rawurlencode($_POST['folderdescriptionvariable'.$i])."=".rawurlencode($_POST['folderdescriptionvalue'.$i]);
				if ($i < intval($_POST['folderdescriptionlength'])){
					$folderdescription .= "&";
				}
			}
		}
		else{
			$folderdescription = $_POST['folderdescription'];
		}
		if ($isMinisite) $result_minisite = saveNodeDescriptionMinisite($idSite, $folderdescription, $virtualPath);
		$result = saveNodeDescription($idSite, $folderdescription, $db, $virtualPath);
		$nodeInfos = getNodeInfos($db,$virtualPath);
		$nodeChildren = getNodeChildren($idSite, $db,$virtualPath);
	}

?>


<form id="managetree" name="managetree" action="<?php echo $_SERVER['PHP_SELF'];?>?idSite=<?php echo $idSite; ?>&v_comp_path=<?php echo $virtualPath; ?><?php echo $param; ?>" method="post">
<div class="ariane">
<span class="arbo2">ARBORESCENCE&nbsp;>&nbsp;</span>
<span class="arbo3">Description d'un dossier&nbsp;>&nbsp;</span>
<span class="arbo4"><?php echo getAbsolutePathString($idSite, $db, $virtualPath,'pageArbo.php'); ?></span>
</div>
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
        <b><br />Description d'un dossier</b><br /><br /><input type="hidden" id="v_comp_path" name="v_comp_path" value="<?php echo $_REQUEST['v_comp_path']; ?>"><input type="hidden" name="folderdescription" id="folderdescription" value="<?php echo $nodeInfos['description']; ?>" />
      <table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF">
        
		     <?php 
if(!strlen($_POST['folderdescription'])) {
	/*
	$aFolderDescriptionVariable = array();
	$aFolderDescriptionValue = array();
	
	$aFolderdescription = explode("&", $nodeInfos['description']);
	$ePaireCounter = 0;
	foreach ($aFolderdescription as $paire => $paireAspliter) {
		$aTempPaireSplit = explode("=", $paireAspliter);
		$ePaireCounter++;
		$aFolderDescriptionVariable[$ePaireCounter] = rawurldecode($aTempPaireSplit[0]);
		$aFolderDescriptionValue[$ePaireCounter] = rawurldecode($aTempPaireSplit[1]);
		echo "<tr bgcolor=\"E6E6E6\">\n";
		echo "<td class=\"arbo\" style=\"vertical-align:middle\"><small>";
   		echo $aFolderDescriptionVariable[$ePaireCounter]."<input type=\"hidden\" id=\"folderdescriptionvariable".$ePaireCounter."\" name=\"folderdescriptionvariable".$ePaireCounter."\" value=\"".$aFolderDescriptionVariable[$ePaireCounter]."\">";
		echo "</td>\n";
		echo "<td><textarea name=\"folderdescriptionvalue".$ePaireCounter."\" cols=\"30\" rows=\"5\" class=\"arbo\" id=\"folderdescriptionvalue".$ePaireCounter."\" type=\"text\">".$aFolderDescriptionValue[$ePaireCounter]."</textarea></td>";
		echo "</tr>\n";
	}
	echo "<input type=\"hidden\" id=\"folderdescriptionlength\" name=\"folderdescriptionlength\" value=\"".$ePaireCounter."\">";*/
?>

       <tr bgcolor="E6E6E6">
		          <td class="arbo" style="vertical-align:middle"><small>Description&nbsp;:&nbsp;</small></td>
          <td>
		  
		  <textarea name="folderdescription" class="arbo textareaEdit" id="folderdescription" type="text"><?php   
		  	if (!$isMinisite)  echo $nodeInfos['description']; 
			else if (is_object($oMinisite))  echo trim($oMinisite->get_desc()); 
		 
		  ?></textarea></td>
        </tr>
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
      <b><span class="arbo">La description du dossier <?php echo strip_tags($nodeInfos['libelle']); ?> a bien &eacute;t&eacute; renseign&eacute;e</span></b>
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