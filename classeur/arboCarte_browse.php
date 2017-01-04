<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/* 



***************************************************

*** Gestion de l'arbo des cartes                ***

*** Classeur de cartes                          ***

*** Récupéré du fichier arboPage_browse.php     ***

***************************************************



*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbocarte.lib.php');

if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

// pas de site
if ($idSite == "") die ("Appel incorrect de la fonctionnalité");


activateMenu('gestioncarte');  //permet de dérouler le menu contextuellement
if($_GET['origine']!=""){
$_SESSION['provenance']=$_GET['origine'];
}
$virtualPath = 0;
if ($_GET['v_comp_path']=="") { // Si on a pas de dossier par défaut on essaye de charger celui en mémoire
	if($_SESSION['carte_v_comp_path']!="") { // On a justement un dossier favoris en mémoire
		$_GET['v_comp_path'] = $_SESSION['carte_v_comp_path'];
	}
}
if (strlen($_GET['v_comp_path']) > 0)
	$virtualPath = $_GET['v_comp_path'];
$_SESSION['carte_v_comp_path'] = $virtualPath;

if( $_GET['v_comp_path']!="" ) {
	$id_node = array_pop(explode(',',$_GET['v_comp_path']));
	$infos_node = getNodeInfosCarte($idSite,$db,$_GET['v_comp_path']);
}


?>



<script type="text/javascript">document.title="Arbo - Cartes";</script>



<style type="text/css">



.toto {



	color: #ffffff;



	text-decoration: underline;



	background-color: #316AC5;



};



</style>



<link rel="stylesheet" type="text/css" href="<?php echo $URL_ROOT; ?>/css/bo.css">



<script type="text/javascript">
<!--
	function preview(id,width,height){
		window.open("previewComposantCarte.php?id="+id,"preview","scrollbar=no,menubar=no,width=" + width +",height=" + height);
	}

url = document.location.href;
debut = url.indexOf("//") + 2;
xend = url.indexOf("/") + 1;
var base_url = url.substring(debut, xend);
var ajax_get_error = false;

function ajax_do (url) {
   if (url.substring(0, 4) != 'http') {
      url = base_url + url;
   }
   var jsel = document.createElement('SCRIPT');
   jsel.type = 'text/javascript';
   jsel.src = url;
   document.body.appendChild (jsel);
   return true;
}

function ajax_get (url, el) {
   if (typeof(el) == 'string') {
      el = document.getElementById(el);
   }
   if (el == null) { return false; }
   if (url.substring(0, 4) != 'http') {
      url = base_url + url;
   }
   getfile_url = 'http://<?php echo $_SERVER['SERVER_NAME']; ?>/backoffice/cms/classeur/getfile.php?url=' + escape(url) + '&el=' + escape(el.id);
   ajax_do (getfile_url);
   return true;
}

function submit_classe() {
   var idclasse = document.getElementById('fClasse').value;
   url = 'http://<?php echo $_SERVER['SERVER_NAME']; ?>/backoffice/cms/classeur/maj_select.php?idclasse=' + escape(idclasse) ;
   ajax_get (url, 'scatmetier');
}


function validformul() {

	erreur = 0;
	lib="";
	
	if(document.ajoutenregistrement.fClasse.value=="" || document.ajoutenregistrement.fClasse.value==-1) {
		erreur++;
		lib+=" classe";
				
	}
	else if(document.ajoutenregistrement.fEnregistrement.value=="" || document.ajoutenregistrement.fEnregistrement.value==-1) {
		erreur++;
		lib+=" enregistrement";
				
	}
	if (erreur >0 ) {
		alert(lib);
	}
	else {
		document.ajoutenregistrement.action="createAsso.php?idclasse="+document.ajoutenregistrement.fClasse.value+"&id="+document.ajoutenregistrement.fEnregistrement.value;
		document.ajoutenregistrement.submit();
	}
}
-->
</script>



<style type="text/css">



<!--



body {



	margin-left: 0px;



	margin-top: 0px;



	margin-right: 0px;



	margin-bottom: 0px;



}



-->



</style>



<div class="ariane"><span class="arbo2">ARBORESCENCE DES CLASSEURS&nbsp;>&nbsp;</span>
<span class="arbo3">Parcourir l'arborescence&nbsp;>&nbsp;</span>
<span class="arbo4"><?php echo getAbsolutePathStringCarte($db, $virtualPath); ?></span></div>



  <table cellpadding="0" cellspacing="0" border="0">


 <tr>



  <td colspan="5"><img src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/vide.gif"></td>



 </tr>



 <tr>



   <td align="left" valign="top" class="arbo2"><br>



       <b><u>Arborescence :</u></b> <br>



       <br>



       <table cellpadding="8" cellspacing="0" border="1" bordercolor="#FFFFFF">



         <tr>



           <td bgcolor="D2D2D2"><?php
print drawCompTreeCarte($idSite, $db,$virtualPath,null);
?></td>



         </tr>



     </table></td>



  <td colspan="3" class="arbo2"><span class="arbo2"><img src="<?php echo $URL_ROOT; ?>/backoffice/cms/img/vide.gif" width="15"></span></td>



  <td align="left" valign="top" class="arbo2">



  <!-- contenu du dossier -->



   <u><b><br>



   Composants contenus dans le dossier en cours:</b></u>



   <br>



   <br>



  <table  border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">



  <tr>



	<td align="center" bgcolor="E6E6E6"><strong>&nbsp;&nbsp;Nom de l'objet&nbsp;</strong></td>



	<td align="center" bgcolor="EEEEEE"><strong>&nbsp;&nbsp;Valeur de l'enregistrement&nbsp;</strong></td>



	<td align="center" bgcolor="EEEEEE"><strong>&nbsp;&nbsp;Modifier l'enregistrement&nbsp;</strong></td>

<!--

	<td align="center" bgcolor="E6E6E6"><strong>&nbsp;&nbsp;Déplacer&nbsp;</strong></td>



	<td align="center" bgcolor="EEEEEE"><strong>&nbsp;&nbsp;Renommer&nbsp;</strong></td>-->



	<td align="center" bgcolor="E6E6E6"><strong>&nbsp;&nbsp;Supprimer&nbsp;</strong></td>



	<!--td align="center" bgcolor="EEEEEE"><strong>&nbsp;&nbsp;Dupliquer&nbsp;</strong></td-->



  </tr>



  <?php
	$contenus = getEnregistrement($virtualPath);
	
	if((!is_array($contenus)) or (sizeof($contenus)==0)) {
?>



  <tr>



    <td align="center" colspan="7">&nbsp;<strong>Aucun élément à afficher</strong></td>

	

	</tr>



<?php
	} else {
		foreach ($contenus as $k => $carte) {
?>



  <tr>


   <td align="center" bgcolor="F3F3F3">&nbsp;<?php echo $carte['nom_classe'];?>&nbsp;</td>



   <td align="center" bgcolor="F7F7F7">&nbsp;<a href="http://<?php echo $_SERVER["HTTP_HOST"]; ?>/backoffice/<?php echo $carte['nom_classe']; ?>/show_<?php echo $carte['nom_classe']; ?>.php?id=<?php echo  $carte['id_enregistrement'];; ?>" target="_blank"><?php echo $carte['description_enregistrement'];?></a>&nbsp;</td>


   <td align="center" bgcolor="F7F7F7">&nbsp;<a href="http://<?php echo $_SERVER["HTTP_HOST"]; ?>/backoffice/<?php echo $carte['nom_classe']; ?>/maj_<?php echo $carte['nom_classe']; ?>.php?id=<?php echo  $carte['id_enregistrement'];; ?>" title="Modifier l'enregistrement"><img src="/backoffice/cms/img/2013/icone/modifier.png" border="0"></a>&nbsp;</td>

<!--

   <td align="center" bgcolor="F3F3F3">&nbsp;<a href="moveCarte.php?id=<?php echo $carte['id'];?>&idnode=<?php echo $idNode; ?>" title="Déplacer la carte dans le classeur"><img src="/backoffice/cms/img/2013/icone/deplacer.png" border="0"></a>&nbsp;</td>



   <td align="center" bgcolor="F7F7F7">&nbsp;<a href="renameCarte.php?id=<?php echo $carte['id'];?>" title="Renommer la carte"><img src="/backoffice/cms/img/2013/icone/renommer.png" border="0"></a>&nbsp;</td>
-->


   <td align="center" bgcolor="F3F3F3">&nbsp;<a href="#" onclick="if(window.confirm('Etes vous sur(e) de vouloir supprimer cette carte ?')) document.location = 'deleteCarte.php?id=<?php echo $carte['id'];?>&idnode=<?php echo $idNode; ?>'" title="Supprimer l'association"><img src="/backoffice/cms/img/2013/icone/supprimer.png" border="0"></a>&nbsp;</td>



   <!--td align="center" bgcolor="F7F7F7">&nbsp;<a href="duplicateCarte.php?id=<?php echo $carte['id'];?>" title="Dupliquer la carte"><img src="/backoffice/cms/img/2013/icone/dupliquer.png" border="0"></a>&nbsp;</td-->



  </tr>



<?php
		}
	}
  ?>
<tr><td colspan="7">
<form name="ajoutenregistrement" id="ajoutenregistrement" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" ENCTYPE="multipart/form-data">
<input type="hidden" name="node_id" value="<?php echo $id_node; ?>" />
<?php

$sql = "select * from classe where cms_statut = ".DEF_ID_STATUT_LIGNE." order by cms_nom";
$aClasse =  dbGetObjectsFromRequete("classe", $sql);
echo "Sélectionner un objet &nbsp; <select name=\"fClasse\" id=\"fClasse\" class=\"arbo\" onchange=\"javascript:submit_classe();\">\n";
//echo "<select name=\"fClasse\" id=\"fClasse\" class=\"arbo\" >\n";
echo "<option value=\"-1\">- - Objet - -</option>";
for ($i = 0; $i<sizeof($aClasse); $i++) {
	$oClasse= $aClasse[$i];
	echo "<option value=\"".$oClasse->get_id()."\">".$oClasse->get_nom()."</option>\n";
}

echo "</select>\n";

?>

<DIV id="scatmetier">
<input type="hidden" name="fEnregistrement" id="fEnregistrement" value="" />
</DIV>
</td></tr>
  <tr>



    <td align="center" colspan="7">&nbsp;<a href="#" onClick="javascript:validformul();">Associer un enregistrement</a>&nbsp;</td></tr>

</form>

  </table>



  </td>



 </tr>



</table>