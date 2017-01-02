<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: envoi_newsletter.php,v 1.1 2013-09-30 09:41:26 raphael Exp $
	$Author: raphael $

	$Log: envoi_newsletter.php,v $
	Revision 1.1  2013-09-30 09:41:26  raphael
	*** empty log message ***

	Revision 1.6  2013-03-01 10:28:17  pierre
	*** empty log message ***

	Revision 1.5  2012-07-31 14:24:49  pierre
	*** empty log message ***

	Revision 1.4  2012-04-13 07:33:12  pierre
	*** empty log message ***

	Revision 1.3  2012-04-13 07:18:38  pierre
	*** empty log message ***

	Revision 1.2  2011-07-05 12:36:06  pierre
	*** empty log message ***

	Revision 1.1  2010-01-21 10:00:36  thao
	*** empty log message ***

	Revision 1.1  2009-09-09 16:06:20  thao
	*** empty log message ***

	Revision 1.1  2008-11-27 11:11:34  thao
	*** empty log message ***

	Revision 1.2  2008-08-28 08:39:10  thao
	*** empty log message ***

	Revision 1.1  2007/10/30 13:53:21  thao
	*** empty log message ***
	
	Revision 1.5  2007/08/29 07:05:37  pierre
	*** empty log message ***
	
	Revision 1.4  2007/08/28 16:19:04  pierre
	*** empty log message ***
	
	Revision 1.3  2007/08/08 14:16:15  thao
	*** empty log message ***
	
	Revision 1.2  2006/06/22 13:44:49  pierre
	*** empty log message ***
	
	Revision 1.6  2005/12/21 09:30:35  sylvie
	*** empty log message ***
	
	Revision 1.5  2005/12/21 08:22:05  sylvie
	*** empty log message ***
	
	Revision 1.3  2005/12/20 10:22:22  sylvie
	*** empty log message ***
	
	Revision 1.2  2005/12/15 10:42:41  sylvie
	*** empty log message ***
	
	Revision 1.1  2005/12/09 08:38:03  sylvie
	*** empty log message ***
	
	Revision 1.7  2005/11/28 08:22:51  sylvie
	*** empty log message ***
	
	Revision 1.6  2005/11/17 16:09:17  sylvie
	*** empty log message ***
	
	Revision 1.2  2005/11/02 13:29:03  sylvie
	*** empty log message ***
	
	Revision 1.1.1.1  2005/10/20 13:10:53  pierre
	Espace V2
	
	Revision 1.3  2005/09/20 15:23:06  sylvie
	*** empty log message ***
	
	Revision 1.1.1.1  2005/08/16 13:29:56  pierre
	init sisqa d'après espace
	
	Revision 1.1.1.1  2005/06/21 16:17:01  pierre
	init cite espace
	
	Revision 1.3  2005/06/17 14:44:08  pierre
	ajout du mode lite qui envoie /documents/newsletter.html
	
	Revision 1.1.1.1  2005/04/18 13:53:29  pierre
	again
	
	Revision 1.1.1.1  2005/04/18 09:04:21  pierre
	oremip new
	
	Revision 1.1  2004/11/02 13:55:29  tofnet
	Ajout du système d'envoi de la Newsletter
	
	
*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

activateMenu('gestiondesnewsletter');  //permet de dérouler le menu contextuellement

$_SESSION['news'] = '';
$_SESSION['exp'] = '';

$sql = " SELECT * FROM newsletter WHERE news_statut = ".DEF_ID_STATUT_NEWS_VALID;
$sql.= " ORDER BY news_date DESC, news_titre ASC";
$aNewsletter = dbGetObjectsFromRequete("newsletter", $sql);

?>
<script type="text/javascript">
<!--
function envoyer(id){
	document.newsletter.id.value = id;
	document.newsletter.action="avant_envoi.php";
	document.newsletter.submit();
}

function showNewsletter(id){
	openBrWindow("/backoffice/newsletter/modele.php?id="+id, "", 520, 500, "resizable=yes,scrollbars=yes", true);
}
//-->
</script>
<span class="arbo2">NEWSLETTER >&nbsp;</span><span class="arbo3">Envoyer une newsletters</span><br><br>
<?php
if(sizeof($aNewsletter) > 0){
?>
<form name="newsletter" method="post">
<input type="hidden" name="id" id="id" value="" />

<!-- envoi newsletter bloqué sur groupe
-> la newsletter n'est envoyée QU'A SES ABONNES -->
<input type="hidden" name="exp" value="grp_theme">


<table width="100%" border="0" cellpadding="0" cellspacing="0" class="arbo">
 <tr>
    <td><table cellpadding="3" cellspacing="0" width="100%" >
		<tr class="col_titre">
		  <td>&nbsp;</td>
		  <td><strong>Newsletter</strong></td>
		  <td><strong>Date</strong></td>
		  <td><strong>Liste de diffusion</strong></td>
		  <td><strong>Responsable &eacute;ditorial</strong></td>
		  <td><strong>Nombre d'inscrits</strong></td>
	    </tr>
<?php
// liste des newsletter
for ($p=0; $p<sizeof($aNewsletter); $p++) 
{
	$oNews = $aNewsletter[$p];
?>
		<tr class="<?php echo htmlImpairePaire($p);?>">
			<td><input type="button" name="btEnvoyer" value="Sélectionner" class="arbo" onClick="envoyer(<?php echo $oNews->getNews_id()?>)"></td>
			<td><a href="javascript:showNewsletter(<?php echo $oNews->getNews_id()?>)"><?php echo $oNews->getNews_titre()?></a></td>
			<td><?php echo afficheDate($oNews->getNews_date())?></td>
			<td><?php
$oInt = new Interet($oNews->getNews_int_id());
?><?php echo $oInt->getInt_libelle()?></td>			
			<td><?php echo $oInt->getInt_email()?>&nbsp;</td>
			<td><?php
//--------------------------------------
// nombre d'inscrits pour cet intérêt
$aRecherche = array();

$oRech = new dbRecherche();

$oRech->setValeurRecherche("declencher_recherche");
$oRech->setTableBD("news_inscrit;news_rel_01");
$oRech->setJointureBD("ins_id=r01_ins_id AND r01_int_id=".$oInt->getInt_id());
$oRech->setPureJointure(1);

$aRecherche[] = $oRech;

$eInscrit = dbGetCountIdListRech("Inscrit", $aRecherche, "");
//--------------------------------------

?><?php if ($eInscrit > 1) { ?><?php echo $eInscrit?> inscrits<?php } else {
?><?php echo $eInscrit?> inscrit<?php
}?>
&nbsp;</td>
		</tr>	
<?php
}
?>
	</table>
  </tr>
</table>
<?php
} else {
?>  
<div align="center" class="arbo">Vous n'avez aucune newsletter &agrave; envoyer pour le moment.</div>
<?php
}
?>