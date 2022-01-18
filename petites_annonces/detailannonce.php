<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
/*
	$Id: detailannonce.php,v 1.1 2013-09-30 09:42:08 raphael Exp $
	$Author: raphael $

	$Log: detailannonce.php,v $
	Revision 1.1  2013-09-30 09:42:08  raphael
	*** empty log message ***

	Revision 1.7  2012-07-31 14:24:49  pierre
	*** empty log message ***

	Revision 1.6  2012-04-13 07:18:38  pierre
	*** empty log message ***

	Revision 1.5  2010-09-27 07:54:27  pierre
	compatibilité https

	Revision 1.4  2009-02-16 16:24:18  pierre
	*** empty log message ***

	Revision 1.3  2009-02-16 15:33:36  pierre
	*** empty log message ***

	Revision 1.2  2009-02-16 15:28:05  pierre
	*** empty log message ***

	Revision 1.1  2009-02-16 14:56:09  pierre
	*** empty log message ***

	Revision 1.3  2008/03/04 14:54:19  pierre
	*** empty log message ***
	
	Revision 1.2  2007/06/27 09:44:45  pierre
	*** empty log message ***
	
	Revision 1.1  2006/04/12 07:25:22  sylvie
	*** empty log message ***
	
	Revision 1.1  2004/06/04 12:35:30  ddinside
	fin petites annonces
	
	Revision 1.1  2004/06/01 14:25:19  ddinside
	ajout petites annonce dont gardes finies
	newslettrer
	

*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/PetitesAnnonces/include_pa.php');

$annonce = new Annonce($_GET['id']);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accueil Gardes partagées - Mairie Boulogne Billancourt</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="/css/pa.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
body {
	background-color: #E1EDF2;
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>

<script src="/backoffice/cms/js/AC_RunActiveContent.js" type="text/javascript"></script>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="456" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><script type="text/javascript">
AC_FL_RunContent( 'codebase','https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,1,0,0','width','456','height','87','src','/img/fo/PA/annonces_bbmix','quality','high','pluginspage','https://get.adobe.com/flashplayer/','movie','/img/fo/PA/annonces_bbmix' ); //end AC code
</script><noscript><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,1,0,0" width="456" height="87">
      <param name="movie" value="/img/fo/PA/annonces_bbmix.swf" />
      <param name="quality" value="high" />
      <embed src="/img/fo/PA/annonces_bbmix.swf" quality="high" pluginspage="https://get.adobe.com/flashplayer/" type="application/x-shockwave-flash" width="456" height="87"></embed>
    </object></noscript></td>
  </tr>
  <tr>
    <td><img src="/img/fo/PA/spacer.gif" width="1" height="5" /></td>
  </tr>
  <tr>
    <td><table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#999B9D">
      <tr>
        <td><table width="100%"  border="0" cellpadding="5" cellspacing="0" bgcolor="#FFFFFF">
          <tr>
            <td><img src="/img/fo/PA/spacer.gif" width="1" height="5" /></td>
          </tr>
          <tr>
            <td>
<?php 
// garde et classqiue : spécifique boulogne
if ($_SESSION['idSite_travail'] == 1) {
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
			<td><img src="/img/fo/PA/menu.gif" width="99" height="30" /></td>
			<td>
			  <select name="section" class="noir10" onchange="javascript:document.location=this.value">
			    <option selected="selected" value="garde.php">Gardes partagées</option>
			    <option value="petitesannonces.php">Petites Annonces</option>
		      </select></td>
			</tr></table>
<?php
}
?>
<table width="442" border="0" cellpadding="0" cellspacing="0">
			<tr>
			 <td class="noir10"><b>Ref&nbsp;:&nbsp;<?php echo ($annonce->getIs_garde()) ? "GAR" : "ANN" ; ?><?php echo $annonce->id; ?></b><br /><br />
<?php
				echo $annonce->categorie->libelle;
?>			 
<?php
                                $array = array();
                                preg_match('/<pre class="pa_corps_db">(.+)<\/pre>/',$annonce->corps,$array);
                                $corps = nl2br($array[1]);
                                $annonce->corps = str_replace('<pre class="pa_corps_db">'.$array[1].'</pre>','<div class="pa_corps_db">'.$corps.'</div>', $annonce->corps);
                                echo $annonce->corps; 
?>			 </td>
			</tr>
	  </table>			</td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
</body>
</html>