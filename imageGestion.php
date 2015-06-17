<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: imageGestion.php,v 1.8 2013-03-01 10:28:04 pierre Exp $
	$Author: pierre $
	
	$Log: imageGestion.php,v $
	Revision 1.8  2013-03-01 10:28:04  pierre
	*** empty log message ***

	Revision 1.7  2012-07-31 14:24:47  pierre
	*** empty log message ***

	Revision 1.6  2012-04-13 07:18:37  pierre
	*** empty log message ***

	Revision 1.5  2010-03-08 12:10:28  pierre
	syntaxe xhtml

	Revision 1.4  2008-11-06 12:03:52  pierre
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
	
	Revision 1.1  2004/02/12 15:56:16  ddinside
	mise à jour plein de choses en fait, mais je sais plus quoi parce que ça fait longtemps que je l'avais pas fait.
	Mea Culpa...
	
	Revision 1.2  2004/01/20 15:16:38  ddinside
	mise à jour de plein de choses
	ajout de gabarit vie des quartiers
	eclatement gabarits par des includes pour contourner prob des flashs non finalisés
	
	Revision 1.1  2004/01/07 18:29:22  ddinside
	jout derniers fonctionnalites
	
	Revision 1.2  2003/11/26 14:25:09  ddinside
	activation du menu
	
*/


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
activateMenu('gestioncontenu');

$open="../../medias/";

$rep=opendir($open); //open est le répertoire visé 
$action = $_GET['action'];
//$fic = $_GET['fic'];
$newnamefile = $_GET['newnamefile'];
$oldnamefile = $_GET['oldnamefile'];

if ($action<>"") { 
	if ($action=="remove") {
		@unlink("../../medias/$oldnamefile"); 
	}
	else if ($action=="rename") {
		$fileopt=explode('.',$oldnamefile);
		@rename ( "../../medias/".$oldnamefile , "../../medias/".$newnamefile.".".$fileopt[1] ) ;
	}
} 
?>
<script>
function PromptNewName(fic) {
       var saisie = prompt("Saisissez le nouveau nom (sans extension) :", "Renommer")
       if (saisie!=null) {
       	location.href="multimediaGestion.php?action=rename&newnamefile="+saisie+"&oldnamefile="+fic    
       }
   }

function Confirmation(fic) {
	if (confirm("Voulez-vous vraiment supprimer ce Média ?")) {
		location.href="multimediaGestion.php?action=remove&oldnamefile="+fic 
	}
}
</script><style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>
<link href="/backoffice/cms/css/bo.css" rel="stylesheet" type="text/css">

<form action="" method="get" name="listeMedia">
<span class="arbo2"><strong>Gestion des médias :</strong></span>
<br />
<br />
<table width="75%" border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
  <tr align="center" bgcolor="#cccccc">
    <td bgcolor="E6E6E6" ><strong>Média</strong></td>
    <td bgcolor="EEEEEE"><strong>Visualiser</strong></td>
    <td bgcolor="E6E6E6"><strong>Renommer</strong></td>
    <td bgcolor="EEEEEE"><strong>Supprimer</strong></td>
  </tr>
 <?php
  while (false !== ($filename = readdir($rep))) {    
 		if (!is_dir($filename)) $files[] = $filename;
		}
if ($files != null)
	 sort($files);
 for($i=0;$i<sizeof($files);$i++) {
  ?> 
  <tr>
    <td bgcolor="F3F3F3">&nbsp;&nbsp;<?php echo $files[$i] ?></td>
    <td align="center" bgcolor="F7F7F7">
	<a href="#" class="arbo" onClick="javascript:window.open('viewMedia.php?fic=<?php echo $files[$i] ?>','Média','directories=no,menubar=no,location=no,status=no,resizable=yes,width=200,height=200')"><img src="../../backoffice/cms/img/visualiser.gif" alt="Visualiser ce média" border=0 ></a>
	</td>
    <td align="center" bgcolor="F3F3F3">
	<a href="#" class="arbo" onClick="javascript:window.open('dialMedia.php?fic=<?php echo $files[$i] ?>','Média','directories=no,menubar=no,location=no,status=no,resizable=yes,width=330,height=150')"><img src="../../backoffice/cms/img/renommer.gif" alt="Renommer ce média" border=0 ></a>
	</td>
    <td align="center" bgcolor="F7F7F7">
	<a class="arbo" href="javascript:Confirmation('<?php echo $files[$i] ?>')" ><img src="../../backoffice/cms/img/supprimer.gif" alt="Supprimer ce média" border=0 ></a>
	</td>
  </tr>
  <?php
  	}
 if ($files == null) {
?>
  <tr bgcolor="#cccccc">
    <td colspan="4" align="center"><strong>Aucun élément à afficher.</strong></td>
  </tr>
<?php
 }
  ?>
</table>


</form>
<?php
 closedir($rep); 
?>
