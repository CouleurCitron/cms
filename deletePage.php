<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: deletePage.php,v 1.7 2013-03-01 10:28:04 pierre Exp $
	$Author: pierre $
	
	$Log: deletePage.php,v $
	Revision 1.7  2013-03-01 10:28:04  pierre
	*** empty log message ***

	Revision 1.6  2012-07-31 14:24:47  pierre
	*** empty log message ***

	Revision 1.5  2010-03-08 12:10:27  pierre
	syntaxe xhtml

	Revision 1.4  2008-11-28 14:09:55  pierre
	*** empty log message ***

	Revision 1.3  2008-07-16 10:48:36  pierre
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
	
	Revision 1.3  2005/11/04 15:13:12  sylvie
	*** empty log message ***
	
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


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
activateMenu('gestionpage');

$id = $_GET['id'];
$page = null;
$result = false;


if (strlen($id) > 0)
	$page = getPageById($id);

if ($page!=null) {
	$result = deletePage($id);	
}

if ($result) {
?>
<link href="/backoffice/cms/css/bo.css" rel="stylesheet" type="text/css">
<body class="arbo">
<form name="deleteForm" method="post">
<strong>Suppression effectuée.</strong>
<script type="text/javascript">
	document.deleteForm.action="arboPage_browse.php";
	document.deleteForm.submit();
</script>
</form>
<?php
} else {
?>

<br />
<br />
Erreur lors de la suppression de la page. 
Merci de contacter l'administrateur si l'erreur persiste.
<?php
}
?>
