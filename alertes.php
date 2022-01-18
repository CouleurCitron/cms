<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: alertes.php,v 1.1 2013-09-30 09:23:49 raphael Exp $
	$Author: raphael $

	$Log: alertes.php,v $
	Revision 1.1  2013-09-30 09:23:49  raphael
	*** empty log message ***

	Revision 1.5  2013-03-01 10:28:03  pierre
	*** empty log message ***

	Revision 1.4  2012-07-31 14:24:46  pierre
	*** empty log message ***

	Revision 1.3  2008-08-28 08:38:12  thao
	*** empty log message ***

	Revision 1.2  2007/11/15 18:08:49  pierre
	*** empty log message ***
	
	Revision 1.5  2007/11/06 15:33:25  remy
	*** empty log message ***
	
	Revision 1.1  2007/08/08 14:26:20  thao
	*** empty log message ***
	
	Revision 1.3  2007/08/08 14:16:14  thao
	*** empty log message ***
	
	Revision 1.2  2007/08/08 13:56:04  thao
	*** empty log message ***
	
	Revision 1.1.1.1  2006/01/25 15:14:32  pierre
	projet CCitron AWS 2006 Nouveau Website
	
	Revision 1.2  2005/10/28 07:53:14  sylvie
	*** empty log message ***
	
	Revision 1.1.1.1  2005/10/20 13:10:53  pierre
	Espace V2
	

*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
activateMenu('gestioncontenu');  //permet de dérouler le menu contextuellemen
$listePage = getListPerim();
?>
<h1>Liste des pages en alerte - Date de péremption</h1>
<table cellpadding="0" cellspacing="0" border="1">
<tr>
 <td align="center">Date péremption</td>
 <td align="center">Nom</td>
 <td align="center">Titre</td>
 <td align="center">Modifier</td>
 <td align="center">Supprimer</td>
</tr>
<?php
foreach($listePage as $k => $page) {
	$infos = getPageById($page['id']);
	$date = explode('-',$page['date']);
	$date = $date[2].'/'.$date[1].'/'.$date[0];
?>
<tr>
 <td <?php echo ($page['date'] <= date('Y-m-d')) ? 'style="color: red;"' : ''?>>&nbsp;<?php echo $date; ?>&nbsp;</td>
 <td>&nbsp;<?php echo $infos['name']; ?>&nbsp;</td>
 <td>&nbsp;<?php echo $infos['titre']; ?>&nbsp;</td>
 <td align="center">&nbsp;<a href="pageModif.php?id=<?php echo $page['id'];?>"><img src="/backoffice/cms/img/2013/icone/modifier.png" border="0"></a>&nbsp;</td>
<td align="center">&nbsp;<a href="#" onClick="if(window.confirm('<?php $translator->echoTransByCode('confirme_suppression'); ?>')){ document.location='deletePage.php?id=<?php echo $page['id'];?>';}"><img src="/backoffice/cms/img/2013/icone/supprimer.png" border="0" onMouseOut='kill();' onMouseOver='popup("Supprimer la page");'></a>&nbsp;</td>
</tr>
<?php
}
?>
</table>
