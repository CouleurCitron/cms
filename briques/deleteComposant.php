<link href="/backoffice/cms/css/bo.css" rel="stylesheet" type="text/css">
<body class="arbo">
<span class="arbo2">BRIQUE&nbsp;>&nbsp;</span>
<span class="arbo3">Suppression de Brique</span><br /><br />
<?php
/*

	Michael: ajout de la suppresion de brique graphique => suppression du fichier XML de données
	
	$Id: deleteComposant.php,v 1.1 2013-09-30 09:34:11 raphael Exp $
	$Author: raphael $
	
	$Log: deleteComposant.php,v $
	Revision 1.1  2013-09-30 09:34:11  raphael
	*** empty log message ***

	Revision 1.1  2013-02-11 10:27:06  thao
	*** empty log message ***

	Revision 1.14  2012-07-31 14:24:47  pierre
	*** empty log message ***

	Revision 1.13  2012-04-13 09:32:29  thao
	*** empty log message ***

	Revision 1.12  2012-04-06 13:18:29  thao
	*** empty log message ***

	Revision 1.11  2011-12-14 10:38:27  thao
	*** empty log message ***

	Revision 1.9  2010-03-08 12:10:27  pierre
	syntaxe xhtml

	Revision 1.8  2009-04-29 09:15:00  pierre
	*** empty log message ***

	Revision 1.7  2008-11-28 14:09:55  pierre
	*** empty log message ***

	Revision 1.6  2008-07-16 10:48:36  pierre
	*** empty log message ***

	Revision 1.5  2007/11/26 17:00:43  thao
	*** empty log message ***
	
	Revision 1.4  2007/11/15 18:08:49  pierre
	*** empty log message ***
	
	Revision 1.6  2007/11/06 15:33:25  remy
	*** empty log message ***
	
	Revision 1.3  2007/09/05 16:08:31  thao
	*** empty log message ***
	
	Revision 1.2  2007/08/28 15:55:28  pierre
	*** empty log message ***
	
	Revision 1.1  2007/08/08 14:26:20  thao
	*** empty log message ***
	
	Revision 1.4  2007/08/08 14:16:14  thao
	*** empty log message ***
	
	Revision 1.3  2007/08/08 13:56:04  thao
	*** empty log message ***
	
	Revision 1.2  2007/08/07 13:14:26  thao
	*** empty log message ***
	
	Revision 1.1.1.1  2006/01/25 15:14:32  pierre
	projet CCitron AWS 2006 Nouveau Website
	
	Revision 1.2  2005/10/28 07:53:14  sylvie
	*** empty log message ***
	
	Revision 1.1.1.1  2005/10/20 13:10:53  pierre
	Espace V2
	
	Revision 1.2  2005/05/23 10:15:51  michael
	Ajout suppression des fichiers de données XML
	pour les briques graphiques
	
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
	
	Revision 1.1  2004/01/07 18:27:37  ddinside
	première mise à niveau pour plein de choses
	
	Revision 1.2  2003/11/26 14:25:09  ddinside
	activation du menu
	
*/


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


activateMenu('gestioncontenu');

$id = $_GET['id'];
$composant = null;
$result = false;
if (strlen($id) > 0)
	$composant = getComposantById($id);

$messageError = "";

if ($composant!=null) {

	$sql = "select COUNT(*) from cms_struct_page, cms_page where cms_struct_page.id_page = cms_page.id_page ";
	$sql.= "AND cms_struct_page.id_content = ".$id." ";
	$sql.= "AND cms_page.isgabarit_page = 1";
	$eCount = dbGetUniqueValueFromRequete($sql);
	
	if ($eCount > 0) {
		$gabarits = $eCount == 1 ? " gabarit" : " gabarits";
		$messageError.= "<br /><br />Votre brique est liée à ".$eCount.$gabarits.":<br /><br />";
		$sql = str_replace("COUNT(*)", "*", $sql);
		$aPages = dbGetObjectsFromRequete("cms_page", $sql);
		foreach($aPages as $key => $page){
			$messageError.="- gabarit : <b>".$page->getName_page()."</b><br />\n";
		}	
		
		$messageError.= "<br />Vous devez les supprimer avant de supprimer votre brique. <br /><br />";
		$messageError.= "<a href=\"arbo_browse.php?".$_SERVER['QUERY_STRING'].">Revenir à la liste des briques</a>";
	}
	else { 
	
		// s'il s'agit d'un formulaire HTML, on supprime également le formulaire et les champs associés
		if ($composant["type"] == 'formulaireHTML') {
			$id_form = $composant["obj_id_content"];
			$oForm = new Cms_form ($id_form);
			$r = dbDelete ($oForm); 
			
			$sql= 'select * from  cms_champform where (id_form = '.$id_form.' || id_form = -1)'; 
			$aChamps = dbGetObjectsFromRequete("cms_champform", $sql); 
			if (sizeof ($aChamps) > 0) {
				foreach ($aChamps as $oChamp) { 
					$oR = dbDelete ($oChamp);
				}
			}
			
		}
		$result = deleteComposant($id);	
		
		
	}
	
}

if ($result) {
	// SI type de brique = graphique
	// SUPPRESSION DU FICHIER DE DONNEES XML CORRESPONDANT
	// Dans le dossier /modules/graphiques/xmldata/
	if ($composant['type']=="Graphique") {
		$htmlstring = $composant['html'];
	   if ( ereg ( "<!--CHART_ID ([^µ]*)µ-->", $htmlstring, $tab_result ) ) {
			$chart_id=$tab_result[1];
	   }
		global $HTTP_SERVER_VARS;
		if(file_exists($HTTP_SERVER_VARS['DOCUMENT_ROOT']."/custom/graphiques/".$_SESSION['rep_travail']."/graphic_".$chart_id.".php"))
			unlink($HTTP_SERVER_VARS['DOCUMENT_ROOT']."/custom/graphiques/".$_SESSION['rep_travail']."/graphic_".$chart_id.".php");
	}
	// FIN BLOC GRAPHIQUE
?>
<link href="/backoffice/cms/css/bo.css" rel="stylesheet" type="text/css">
<body class="arbo">
<strong>Suppression effectuée.</strong>
<form name="deleteForm" method="post">
<script type="text/javascript">
	//document.deleteForm.action="arbo_browse.php";
	//document.deleteForm.submit();
	//history.go(-1);
</script>
</form>
<?php
} else {

	if ($messageError=="") {
		echo  "<br /><br />Erreur lors de la suppression de la brique composant. Merci de contacter l'administrateur si l'erreur persiste.<br /><br />";
	}
	else {
		echo $messageError;
	}

}
?>
