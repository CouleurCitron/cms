<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Author: raphael $
	$Id: accueilBackoffice.php,v 1.3 2013-11-06 10:26:32 raphael Exp $
	
	$Log: accueilBackoffice.php,v $
	Revision 1.3  2013-11-06 10:26:32  raphael
	*** empty log message ***

	Revision 1.2  2013-10-02 13:26:19  raphael
	*** empty log message ***

	Revision 1.1  2013-09-30 09:23:49  raphael
	*** empty log message ***

	Revision 1.30  2013-03-25 15:34:02  pierre
	*** empty log message ***

	Revision 1.29  2013-03-01 10:28:03  pierre
	*** empty log message ***

	Revision 1.28  2013-01-15 14:08:00  pierre
	*** empty log message ***

	Revision 1.27  2012-07-31 14:24:46  pierre
	*** empty log message ***

	Revision 1.26  2011-06-29 14:05:09  pierre
	*** empty log message ***

	Revision 1.25  2011-06-29 13:26:57  pierre
	*** empty log message ***

	Revision 1.24  2010-12-15 10:33:35  pierre
	optimisation usage des sessions idSite en BO

	Revision 1.23  2010-12-15 09:01:11  pierre
	*** empty log message ***

	Revision 1.22  2010-10-04 10:11:21  pierre
	*** empty log message ***

	Revision 1.21  2010-05-20 12:55:20  pierre
	*** empty log message ***

	Revision 1.20  2010-03-08 12:10:27  pierre
	syntaxe xhtml

	Revision 1.19  2009-09-24 08:53:11  pierre
	*** empty log message ***

	Revision 1.18  2009-03-03 10:47:53  thao
	*** empty log message ***

	Revision 1.17  2009-02-16 17:26:10  thao
	*** empty log message ***

	Revision 1.16  2008-11-27 19:37:46  pierre
	*** empty log message ***

	Revision 1.15  2008-11-27 11:35:37  pierre
	*** empty log message ***

	Revision 1.14  2008-11-06 12:03:52  pierre
	*** empty log message ***

	Revision 1.13  2008-07-10 13:36:33  thao
	*** empty log message ***

	Revision 1.12  2008/04/29 14:46:04  thao
	*** empty log message ***
	
	Revision 1.11  2008/04/21 09:07:19  pierre
	*** empty log message ***
	
	Revision 1.10  2008/04/16 14:08:04  pierre
	*** empty log message ***
	
	Revision 1.9  2008/04/16 09:42:23  pierre
	*** empty log message ***
	
	Revision 1.8  2008/04/15 14:25:47  pierre
	*** empty log message ***
	
	Revision 1.7  2008/02/08 15:10:45  pierre
	*** empty log message ***
	
	Revision 1.6  2007/11/20 08:41:56  thao
	*** empty log message ***
	
	Revision 1.5  2007/11/16 09:57:39  thao
	*** empty log message ***
	
	Revision 1.4  2007/11/15 18:08:49  pierre
	*** empty log message ***
	
	Revision 1.1  2007/11/06 15:33:25  remy
	*** empty log message ***
	
	Revision 1.2  2007/08/28 15:55:28  pierre
	*** empty log message ***
	
	Revision 1.1  2007/08/08 15:55:21  pierre
	*** empty log message ***
	
	Revision 1.2  2007/08/03 09:17:39  pierre
	*** empty log message ***
	
	Revision 1.1  2007/08/03 09:16:05  pierre
	*** empty log message ***
	
	Revision 1.4  2007/06/05 12:31:39  thao
	*** empty log message ***
	
	Revision 1.3  2007/03/05 10:37:36  pierre
	*** empty log message ***
	
	Revision 1.2  2007/02/07 15:42:36  pierre
	*** empty log message ***
	
	Revision 1.1  2006/12/15 12:30:14  pierre
	*** empty log message ***
	
	Revision 1.1.1.1  2006/01/25 15:14:31  pierre
	projet CCitron AWS 2006 Nouveau Website
	
	Revision 1.2  2005/10/28 07:59:23  sylvie
	site connecté et site de travail affichés si gestion des mini sites
	
	Revision 1.1.1.1  2005/10/20 13:10:53  pierre
	Espace V2
	
	Revision 1.1.1.1  2005/04/18 13:53:29  pierre
	again
	
	Revision 1.1.1.1  2005/04/18 09:04:21  pierre
	oremip new
	
	Revision 1.1.1.1  2004/11/03 13:49:54  ddinside
	lancement du projet - import de adequat
	
	Revision 1.3  2004/06/16 15:23:19  ddinside
	inclusion corrections
	
*/

// sponthus 10/06/2005
// pour voir qui est connecté

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/class/bo_users.class.php');

activateMenu('main');  //permet de dérouler le menu contextuellement


// utilisateur connecté
$oUser = unserialize($_SESSION['BO']['LOGGED']);


$aListeSite = listSite("ALL");

if (newSizeOf($aListeSite) == 0) {
	// création du site par défaut
	$oSite = new Cms_site();
	$oSite->set_id(1);
	$oSite->set_name("SITE");
	$oSite->set_desc("");
	$oSite->set_url("");
	$oSite->set_fonct("");
	$oSite->set_isminisite(0);
	$oSite->set_widthpage(DEF_GABINITWIDTH);
	$oSite->set_heightpage(DEF_GABINITHEIGHT);
	$oSite->set_rep("SITE");
	$oSite->set_langue(1);
	dbInsert($oSite);
}
?>
<form name="accueilForm" id="accueilForm" method="post">
	<input type="hidden" name="operation" id="operation" value="<?php if (isset($_POST['operation'])) echo $_POST['operation']; ?>" />
<?php
$bTrace = false;
//$bTrace = true;
if ($bTrace) {
print("<br />==========");
print("<br />_SESSION['idSite']=>".$_SESSION['idSite']);	
print("<br />_SESSION['site']=>".$_SESSION['site']);	
print("<br />_SESSION['minisite']=>".$_SESSION['minisite']);
print("<br />==========");
print("<br />_SESSION['idSite_travail']=>".$_SESSION['idSite_travail']);
print("<br />_SESSION['site_travail']=>".$_SESSION['site_travail']);
print("<br />==========");
print("<br />");
}

?>
<span class="arbo2"><strong><?php echo $translator->echoTransByCode('Bienvenue'); ?></strong></span>
<div id="accueil">
<br /><br />
	<a href="javascript:logoff()" class="arbo" title="<?php echo $translator->echoTransByCode('cliquericipoursedeconnecter'); ?>"><?php echo $translator->echoTransByCode('cliquericipoursedeconnecter'); ?></a>
<br /><br />
<table align="left" cellpadding="5" cellspacing="0" border="1" bordercolor="#FFFFFF" class="arbo">
		<tr>
			<td><?php $translator->echoTransByCode('nom'); ?></td>
			<td><?php echo $oUser->nom; ?>&nbsp;</td>
		</tr>
		<tr>
			<td><?php $translator->echoTransByCode('prenom'); ?></td>
			<td><?php echo $oUser->prenom; ?>&nbsp;</td>
		</tr>
		<tr>
			<td><?php $translator->echoTransByCode('email'); ?></td>
			<td><?php echo $oUser->mail; ?>&nbsp;</td>
		</tr>
		<tr>
			<td><?php $translator->echoTransByCode('telephone'); ?></td>
			<td><?php echo $oUser->get_tel(); ?>&nbsp;</td>
		</tr>
		<tr>
			<td><?php $translator->echoTransByCode('Date_de_creation'); ?></td>
			<td><?php echo from_dbdate($oUser->get_dt()); ?>&nbsp;</td>
		</tr>
		<tr>
			<td><?php $translator->echoTransByCode('rang'); ?></td>
			<td><?php
				$oRank = new Bo_rank($oUser->rank);
				echo $oRank->get_libelle(); ?>&nbsp;</td>
		</tr>
		<tr>
			<td><?php $translator->echoTransByCode('languepreferee'); ?></td>
			<td><?php echo $_SESSION['BO']['site_langue']; ?>&nbsp;</td>
		</tr>
		<tr>
			<td><?php $translator->echoTransByCode('languesite'); ?></td>
			<td><?php echo $_SESSION['site_langue']; ?>&nbsp;</td>
		</tr>
		<tr>
			<td><?php $translator->echoTransByCode('siteselectionne'); ?></td>
			<td><?php echo $_SESSION['site']; ?>&nbsp;<?php
//				if ($oRank->get_libelle() == "ADMIN") {
//					print(putChangeSiteConnected());
//				}			 
			 ?></td>
		</tr>		
	</table></div>
</form>
<!--<img src="/backoffice/cms/init.php?idSite=<?php echo $_SESSION['idSite']; ?>" alt="Fake Image pour run de init" width="1" height="1" border="0" style="display:none" />-->
<img src="/backoffice/cms/arbo_check.php?idSite=<?php echo $_SESSION['idSite']; ?>" alt="Fake Image pour arbo check" width="1" height="1" border="0" style="display:none" />
<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/append.php');
?>