<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/* 



***************************************************

*** Gestion de l'arbo des cartes                ***

*** Classeur de cartes                          ***

*** Modification d'une carte                    ***

***************************************************



*/



include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbocarte.lib.php');

$idSite = $_GET['idSite'];
if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

activateMenu('gestioncarte');  //permet de dérouler le menu contextuellement



// ID_noeud par défaut auquel est associée la carte

if( $_GET['v_comp_path']!="" ) {

	$id_node = array_pop(explode(',',$_GET['v_comp_path']));

	$infos_node = getNodeInfosCarte($db,$_GET['v_comp_path']);

}



$id = $_GET['id'];
$idnodeold = $_GET['idnode'];
if($_POST['idnode']!="") $idnodenew = $_POST['idnode'];
$carte = null;
if (strlen($id) > 0 ) {

	$carte = getCarteById($id, $idnodeold);

	$infos_node = getNodeInfosCarte($idSite, $db,','.$carte['node_id']);

}


// libelle du noeud


?>

<script language="JavaScript" type="text/javascript">

  function chooseFolder() {

	window.open('chooseFoldercarte.php','browser','scrollbars=yes,width=500,height=500,resizeable=yes,menubar=no');

  }

</script>

<link href="<?php echo $URL_ROOT; ?>/css/bo.css" rel="stylesheet" type="text/css" />

<style type="text/css">

<!--

body {

	margin-left: 0px;

	margin-top: 0px;

	margin-right: 0px;

	margin-bottom: 0px;

}

-->

</style><br />

<br />

<br />

<?php



// MODE EDITION //



if(!$_GET['nom']&&!$_POST['nom']) {

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="creation" id="creation">

<input type="hidden" name="id" value="<?php echo  $id ; ?>" />
<input type="hidden" name="node_id" value="<?php echo  $carte['node_id'] ; ?>" />
<input type="hidden" name="idnode" value="<?php echo $_GET['idnode']; ?>" />
<input type="hidden" name="chemin_relatif" value="<?php echo  $carte['chemin_absolu'] ; ?>" />

<br />

<table width="369" border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF" class="arbo">

  <tr>

    <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="arbo">

        <tr valign="middle"  >

          <td height="28" colspan="2" nowrap="nowrap" background="/img/fond_tab.gif"><table width="100%" border="0" cellspacing="0" cellpadding="5">

              <tr>

                <td class="arbo"><strong>&nbsp;Modification d'une étude</strong></td>

              </tr>

          </table></td>

        </tr>

         <tr>

          <td colspan="2" align="right">&nbsp;</td>

        </tr>

        <tr>

          <td align="right" style="vertical-align:middle">Dossier&nbsp;:&nbsp;</td>

          <td align="left"><input name="foldername" type="text" disabled="disabled" class="arbo" value="<?php echo  $infos_node['libelle'] ; ?>" size="20" />

    &nbsp;&nbsp;<a href="#" class="arbo" onclick="chooseFolder();" title="choisir le dossier"><img src="/backoffice/cms/img/2013/icone/go.png" border="0" alt="choisir le dossier" />&nbsp;choisir le dossier</a></td>

        </tr>

        <tr>

          <td colspan="2" align="right">&nbsp;</td>

        </tr>

        <tr>

          <td align="right" style="vertical-align:middle">Nom&nbsp;:&nbsp;</td>

          <td align="left"><input name="nom" type="text" class="arbo" id="nom" size="60"  maxlength="60" value="<?php echo   str_replace('"',"''",$carte['nom']) ; ?>" /></td>

        </tr>

        <tr>

          <td colspan="2" align="right">&nbsp;</td>

        </tr>

        <tr>

          <td align="right" style="vertical-align:middle">Mots-clés&nbsp;:&nbsp;</td>

          <td align="left"><textarea name="motscles" class="arbo" id="motscles"><?php echo   str_replace('"',"''",$carte['motsclefs']) ; ?></textarea></td>

        </tr>

        <tr>

          <td colspan="2" align="right">&nbsp;</td>

        </tr>

        <tr>

          <td align="right" style="vertical-align:middle">Description&nbsp;:&nbsp;</td>

          <td align="left"><textarea name="description" class="arbo" id="description"><?php echo str_replace('"',"''",$carte['description']) ; ?></textarea></td>

        </tr>

		<tr>

		<td align="right" >Date de début&nbsp;:&nbsp;</td><td><input name="dateetude" type="text" class="arbo" id="dateetude" size="60"  value="<?php echo $carte['date_etude']; ?>" maxlength="60" /></td>

		</tr>

		<tr>

		<td align="right" >Date de fin&nbsp;:&nbsp;</td><td><input name="datepub" type="text" class="arbo" id="datepub" size="60"  value="<?php echo $carte['date_publication']; ?>" maxlength="60" />

		  <input name="page" type="hidden" class="arbo" id="page" value="<?php echo $carte['pages']; ?>" />

		  <input name="poids" type="hidden" class="arbo" id="poids" value="<?php echo $carte['poids']; ?>" /></td>

		</tr>

		<tr>

		<td align="right" >Lieu&nbsp;:&nbsp;</td><td><input name="lieu" type="text" class="arbo" id="lieu" size="60"  value="<?php echo $carte['lieu']; ?>" maxlength="60" /></td>

		</tr>
		<tr>

		<td align="right" >Fichier PDF&nbsp;:&nbsp;</td><td><input name="chemin_absolu" type="text" class="arbo" id="chemin_absolu" size="06"  value="<?php echo $carte['chemin_absolu']; ?>" maxlength="60" /></td>

		</tr>
        <tr>		

          <td  colspan="2" align="center"><br />

              <input name="Suite (Enregistrer) &gt;&gt;" type="submit" class="arbo" id="Suite (Enregistrer) &gt;&gt;" value="Suite (Enregistrer) &gt;&gt;">

              <br />

            <br /></td>

        </tr>

    </table></td>

  </tr>

</table>

</form>
<?php	
} else {
// MODE ENREGISTREMENT //



// Chargement de la classe
require_once('cms-inc/lib/fileUpload/upload.class.php');

// Instanciation d'un nouvel objet "upload"
$Upload = new Upload();

// Pour ajouter des attributs aux champs de type file
$Upload-> FieldOptions = 'style="border-color:black;border-width:1px;"';

// Pour indiquer le nombre de champs désiré
$Upload-> Fields = 1;

// Initialisation du formulaire
$Upload-> InitForm();
?>

<form method="post" enctype="multipart/form-data" name="formulaire" id="formulaire" action="uploadCarte.php?id=<?php echo $_GET['id']; ?>">
<span class="arbo2"><strong>Récupération du fichier &quot;<?php echo stripslashes($_GET['nom']); ?>&quot;:</strong></span>
<?php
// Affichage du champ MAX_FILE_SIZE
print $Upload-> Field[0];

// Affichage du premier champ de type FILE
print $Upload-> Field[1] . '<br>';

?>
<br /> 

<input type="hidden" name="node_id" id="node_id" value="<?php echo $_GET['node_id']; ?>" />
<input type="hidden" name="idnode" id="idnode" value="<?php echo $_GET['idnode']; ?>" />
<input type="hidden" name="id" id="id" value="<?php echo $_GET['id']; ?>" />
<input type="hidden" name="nom" id="nom" value="<?php echo stripslashes(str_replace('"',"''",$_GET['nom'])); ?>" class="arbo" />
<input type="hidden" name="motscles" id="motscles" value="<?php echo stripslashes(str_replace('"',"''",$_GET['motscles'])); ?>" class="arbo" />
<input type="hidden" name="description" id="description" value="<?php echo stripslashes(str_replace('"',"''",$_GET['description'])); ?>" class="arbo" />
<input type="hidden" name="page" id="page" value="<?php echo stripslashes(str_replace('"',"''",$_GET['page'])); ?>" class="arbo" />
<input type="hidden" name="poids" id="poids" value="<?php echo stripslashes(str_replace('"',"''",$_GET['poids'])); ?>" class="arbo" />
<input type="hidden" name="dateetude" id="dateetude" value="<?php echo stripslashes(str_replace('"',"''",$_GET['dateetude'])); ?>" class="arbo" />
<input type="hidden" name="datepub" id="datepub" value="<?php echo stripslashes(str_replace('"',"''",$_GET['datepub'])); ?>" class="arbo" />
<input type="hidden" name="lieu" id="lieu" value="<?php echo stripslashes(str_replace('"',"''",$_GET['lieu'])); ?>" class="arbo" />
<input name="submit" type="submit" class="arbo" value="Envoyer" />
</form>
<?php	
}
?>



