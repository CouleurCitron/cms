<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
/*
	$Id: detailgarde.php,v 1.1 2013-09-30 09:42:09 raphael Exp $
	$Author: raphael $

	$Log: detailgarde.php,v $
	Revision 1.1  2013-09-30 09:42:09  raphael
	*** empty log message ***

	Revision 1.5  2012-07-31 14:24:49  pierre
	*** empty log message ***

	Revision 1.4  2009-02-16 16:24:18  pierre
	*** empty log message ***

	Revision 1.3  2009-02-16 15:33:36  pierre
	*** empty log message ***

	Revision 1.2  2009-02-16 15:28:05  pierre
	*** empty log message ***

	Revision 1.1  2009-02-16 14:56:09  pierre
	*** empty log message ***

	Revision 1.2  2008/03/04 14:54:19  pierre
	*** empty log message ***
	
	Revision 1.1  2006/04/12 07:25:22  sylvie
	*** empty log message ***
	
	Revision 1.1  2004/06/02 13:39:08  ddinside
	commit version finale garde partagee, reste petites annonces à faire
	
	Revision 1.1  2004/06/01 14:25:19  ddinside
	ajout petites annonce dont gardes finies
	newslettrer
	

*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/PetitesAnnonces/include_pa.php');

$annonce = new Annonce($_GET['id']);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accueil Gardes partagées - Mairie Boulogne Billancourt</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="/css/pa.css" rel="stylesheet" type="text/css" />
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="495" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="2">
			<img src="/img/fo/PA/garde_coin_haut_gauche.gif" width="330" height="74" alt="" /></td>
		<td colspan="2" rowspan="2">
			<img src="../../img/fo/PA/coin_haut_droit.gif" width="165" height="104" /></td>
	</tr>
	<tr>
		<td>
			<img src="/img/fo/PA/garde_haut_gauche.jpg" width="28" height="30" alt="" /></td>
		<td><table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
			<td><img src="/img/fo/PA/menu.gif" width="99" height="30" /></td>
			<td>
			  <select name="section" class="texte_noir" onchange="javascript:document.location=this.value">
			    <option selected="selected" value="garde.php">Gardes partagées</option>
			    <option value="petitesannonces.php">Petites Annonces</option>
		      </select></td>
			</tr></table></td>
	</tr>
	<tr>
		<td colspan="4">
			<img src="/img/fo/PA/garde_haut.jpg" width="495" height="14" alt="" /></td>
	</tr>
	<tr>
		<td>
			<img src="/img/fo/PA/garde_gauche.jpg" width="28" height="396" alt="" /></td>
		<td colspan="2" valign="top"><table width="442" border="0" cellpadding="0" cellspacing="0">
			<tr>
			  <td height="24" class="champs_texte"><a href="/frontoffice/petites_annonces/garde.php"><img src="/img/fo/PA/consulter.gif" width="442" height="20" border="0" /></a></td>
			</tr>
			<tr>
			 <td class="texte_noir_bold"><b>Ref&nbsp;:&nbsp;GAR<?php echo $annonce->id; ?></b><br /><br />
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
			<tr height="24">
			  <td><a href="addGarde.php"><img border="0" src="/img/fo/PA/deposer.gif" width="442" height="20" /></a></td>
		  </tr>
			<tr height="24">
			  <td><a href="manageProfile.php"><img border="0" src="/img/fo/PA/supprimer.gif" width="442" height="20" /></a></td>
		  </tr>
	  </table></td>
		<td>
			<img src="/img/fo/PA/garde_droite.jpg" width="25" height="396" alt="" /></td>
	</tr>
	<tr>
		<td colspan="4">
			<img src="/img/fo/PA/garde_bas.gif" width="495" height="27" alt="" /></td>
	</tr>
	<tr>
		<td>
			<img src="images/spacer.gif" width="28" height="1" alt="" /></td>
		<td>
			<img src="images/spacer.gif" width="302" height="1" alt="" /></td>
		<td>
			<img src="images/spacer.gif" width="140" height="1" alt="" /></td>
		<td>
			<img src="images/spacer.gif" width="25" height="1" alt="" /></td>
	</tr>
</table>
</body>
</html>
