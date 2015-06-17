<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: deletePage.php,v 1.3 2013-03-01 10:28:20 pierre Exp $
	$Author: pierre $
	
	$Log: deletePage.php,v $
	Revision 1.3  2013-03-01 10:28:20  pierre
	*** empty log message ***

	Revision 1.2  2012-07-31 14:24:50  pierre
	*** empty log message ***

	Revision 1.1  2010-08-23 08:29:51  pierre
	*** empty log message ***

	Revision 1.7  2010-01-08 11:34:03  pierre
	*** empty log message ***

	Revision 1.6  2008-11-28 14:09:55  pierre
	*** empty log message ***

	Revision 1.5  2008-11-06 12:03:54  pierre
	*** empty log message ***

	Revision 1.4  2008-07-16 10:48:36  pierre
	*** empty log message ***

	Revision 1.3  2007/09/05 15:15:05  thao
	*** empty log message ***
	
	Revision 1.1  2007/08/08 14:26:26  thao
	*** empty log message ***
	
	Revision 1.5  2007/08/08 14:16:15  thao
	*** empty log message ***
	
	Revision 1.4  2007/08/08 13:56:04  thao
	*** empty log message ***
	
	Revision 1.3  2007/08/06 16:18:30  thao
	*** empty log message ***
	
	Revision 1.2  2007/03/27 15:10:39  pierre
	*** empty log message ***
	
	Revision 1.1  2006/12/15 12:30:11  pierre
	*** empty log message ***
	
	Revision 1.1.1.1  2006/01/25 15:14:32  pierre
	projet CCitron AWS 2006 Nouveau Website
	
	Revision 1.2  2005/10/28 07:53:14  sylvie
	*** empty log message ***
	
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
	
	Revision 1.2  2004/04/26 08:07:09  melanie
	*** empty log message ***
	
	Revision 1.1.1.1  2004/04/01 09:20:27  ddinside
	Création du projet CMS Couleur Citron nom de code : tipunch
	
	Revision 1.2  2004/02/05 15:56:25  ddinside
	ajout fonctionnalite de suppression de pages
	ajout des styles dans spaw
	debuggauge prob du nom de fichier limite à 30 caracteres
	
	Revision 1.1  2004/01/27 12:12:50  ddinside
	application de dos2unix sur les scripts SQL
	modification des scripts d'ajout pour correction bug lors d'une modif de page
	ajout foncitonnalité de modification de page
	ajout visu d'une page si créée
	
	Revision 1.1  2004/01/07 18:27:37  ddinside
	première mise à niveau pour plein de choses
	
	Revision 1.2  2003/11/26 14:25:09  ddinside
	activation du menu
	
*/

?>
<link href="/backoffice/cms/css/bo.css" rel="stylesheet" type="text/css">
<body class="arbo">
<span class="arbo2">GABARIT&nbsp;>&nbsp;</span>
<span class="arbo3">Suppression de Gabarit</span><br><br>
<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
include_once('delete.lib.php');
activateMenu('gestionpage');

$id = $_GET['id'];

$page = null;
$result = false;
$resultCMS = false;
$resultSVR = false;
$deleteValid = false;
if (strlen($id) > 0)
	$page = getPageById($id);

if ($page!=null) {
	
	
	if ($page["isgabarit_page"] ==1) {
		$eCount = getCount_where("cms_page", array("gabarit_page", "valid_page"), array($id, 1), array("NUMBER", "NUMBER"));
		if ($eCount > 0) {
			if (!isset($_GET['action'])) {
				$pages = $eCount == 1 ? " page" : " pages";
				echo "Votre gabarit est lié à ".$eCount.$pages.".<br>";
				echo "Vous avez le choix entre : <br><br>";
				echo "&bull;&nbsp;&nbsp;<a href=\"deletePage.php?".$_SERVER['QUERY_STRING']."&action=delall\">Supprimer toutes les pages du CMS et du serveur</a><br>";
				echo "&bull;&nbsp;&nbsp;<a href=\"deletePage.php?".$_SERVER['QUERY_STRING']."&action=delcms\">Supprimer toutes les pages du CMS mais les conserver sur le serveur</a><br><br>";
				echo "<a href=\"gabaritList.php?".$_SERVER['QUERY_STRING'].">Revenir à la liste des gabarits</a>";
			}
			else {
				$action = $_GET['action'];
				$resultCMS = deletePageByIdGabarit ($id, $action);
				if ($resultCMS) {
					$deleteValid = true;
				}
			}
		}
		else {
			$deleteValid = true;
			echo "Votre gabarit n'est lié à aucune page.<br><br>";
		}
		if ($deleteValid){
			$result = deletePage($id);	
			if ($result) {
				deleteInfosByIdPage($id);
				deleteStructByIdPage($id);
				echo "<br /><br><strong>Suppression effectuée.</strong><br><br>";
				echo "<a href=\"gabaritList.php\">Revenir à la liste des gabarits</a>";
				?>
		
				<script type="text/javascript">
					//history.go(-1);
					//document.location.href = "gabaritList.php";
				</script>
				<?php
			} else { 
				echo "<br /><br>Erreur lors de la suppression de la page. Merci de contacter l'administrateur si l'erreur persiste.";
			}
		}
	}
	else {
		if (!isset($_GET['action'])) {
			echo "Pour supprimer votre page, vous avez le choix entre : <br><br>";
			echo "&bull;&nbsp;&nbsp;<a href=\"deletePage.php?".$_SERVER['QUERY_STRING']."&action=delall\">La supprimer du CMS et du serveur</a><br>";
			echo "&bull;&nbsp;&nbsp;<a href=\"deletePage.php?".$_SERVER['QUERY_STRING']."&action=delcms\">La supprimer du CMS mais la conserver sur le serveur</a><br><br>";
			echo "<a href=\"../arboPage_browse.php?".$_SERVER['QUERY_STRING'].">Revenir à la liste des pages</a>";
		}
		else {
			$action = $_GET['action'];
			$result = deletePageByIdPage ($id, $action);
			if ($result) {
    	echo "<br /><br><strong>Suppression effectuée.</strong><br><br>";
		echo "<a href=\"../arboPage_browse.php\">Revenir à la liste des pages</a>";
		?>

		<script type="text/javascript">
			//history.go(-1);
			//document.location.href = "gabaritList.php";
		</script>
		<?php
	} else { 
		echo "<br /><br>Erreur lors de la suppression de la page. Merci de contacter l'administrateur si l'erreur persiste.";
	}
		}
	}
	
	
	
}
?>
