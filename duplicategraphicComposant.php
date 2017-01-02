<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: duplicategraphicComposant.php,v 1.1 2013-09-30 09:24:06 raphael Exp $
	$Author: raphael $

*/


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');

$id = $_GET['id'];
if($_POST['id']!="") $id = $_POST['id'];
$composant = null;
if (strlen($id) > 0)
	$composant = getComposantById($id);
if ($composant != null) {
	$name = ' value="'.$composant['name'].'" ';
	$larg = ' value="'.$composant['width'].'" ';
	$long = ' value="'.$composant['height'].'" ';
	if($composant['type']!="Graphique") { echo "Ce script ne peut pas éditer un type de brique autre que Graphique !"; return; }
	$type = ' value="'.$composant['type'].'" ';
	$contenuhtml=$composant['html'];
	$contenuhtml = preg_replace("/'/",'`',$contenuhtml);
	
	// Remplacement de l'ancien ID graphique par un nouveau
	$id_graph = mt_rand(); 
	while( file_exists( $HTTP_SERVER_VARS['DOCUMENT_ROOT']."/custom/graphiques/".$_SESSION['rep_travail']."/graphic_".$id_graph.".php") ) {
		$id_graph = mt_rand();
	}
	$contenuhtml = preg_replace("`<!--CHART_ID [^µ]+µ-->`","<!--CHART_ID ".$id_graph."µ-->",$contenuhtml);
	
?>
<link href="/backoffice/cms/css/bo.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style><br />
<br />
<br />
<form name="creation" action="graphicEditor.php" method="post">
<input type="hidden" name="contenuhtml" value='<?php if (strlen($contenuhtml)>0) echo $contenuhtml; ?>'>
<br />
<table width="369" border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
  <tr>
    <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="arbo">
        <tr valign="middle"  >
          <td height="28" colspan="2" nowrap background="../../backoffice/cms/img/fond_tab.gif"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td class="arbo"><strong>&nbsp;Cr&eacute;ation de la brique Graphique</strong></td>
              </tr>
          </table></td>
        </tr>
        <tr>
          <td colspan="2" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td align="right" style="vertical-align:middle">Nom&nbsp;:&nbsp;</td>
          <td align="left"><input name="name" type="text" class="arbo" id="name" size="30"  maxlength="30" <?php echo $name; ?>></td>
        </tr>
		<tr>
		  <td colspan="2" align="right">&nbsp;</td>
		  </tr>
		<tr>
		  <td align="right" style="vertical-align:middle">Largeur&nbsp;:&nbsp;</td>
		  <td align="left"><input name="l" type="text" class="arbo" size="4" maxlength="4" <?php echo $larg; ?>>&nbsp;pixels</td>
		</tr>
		<tr>
		  <td colspan="2" align="right">&nbsp;</td>
		  </tr>
		<tr>
		  <td align="right" style="vertical-align:middle">Hauteur&nbsp;:&nbsp;</td>
		  <td align="left"><input name="h" type="text" class="arbo" size="4" maxlength="4" <?php echo $long; ?>>&nbsp;pixels</td>
		</tr>
        <tr>
          <td  colspan="2" align="center"><br />
              <input name="Suite (edition) >>" type="submit" class="arbo" id="Suite (edition) >>" value="Suite (&eacute;dition) >>" >
              <br />
              <br /></td>
        </tr>
    </table></td>
  </tr>
</table>
</form>
<?php
}

?>