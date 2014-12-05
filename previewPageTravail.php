<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
// sponthus 08/08/2005
// preview d'une page dans sa version de travail

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/gabarit.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/pages.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newcontent_write.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newgabarit_write.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/newpage_write.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
 include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


/*
print("<br />".$_GET['idPage']);
print("<br />".$_GET['idContent']);
print("<br />".$_GET['nature']);
print("<br />Chemin_travail=>".$sChemin_travail);

print("<br /><br /><br />");
*/

// si on est en présence d'une brique de CMS_CONTENT
// -> on affiche de contenu de cette brique depuis CMS_CONTENT 
// puis toutes les autres briques seront affichées au mieux depuis CMS_ARCHI (version en ligne)
// sinon depuis CMS_CONTENT


if ($_GET['nature'] == "CMS_CONTENT") {
	$contentPage = pageTravail($_GET['idPage'], "TRAVAIL", $_GET['idContent']);
} else {
	$contentPage = pageTravail($_GET['idPage'], "TRAVAIL", -1);
}

echo phpsrcEval($contentPage);
?>