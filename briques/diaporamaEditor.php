<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*
	$Id: diaporamaEditor.php,v 1.2 2013-03-01 10:28:05 pierre Exp $
	$Author: pierre $
	
	$Log: diaporamaEditor.php,v $
	Revision 1.2  2013-03-01 10:28:05  pierre
	*** empty log message ***

	Revision 1.1  2012-12-12 14:26:36  pierre
	*** empty log message ***

	Revision 1.9  2012-07-31 14:24:47  pierre
	*** empty log message ***

	Revision 1.8  2012-04-13 07:18:37  pierre
	*** empty log message ***

	Revision 1.7  2010-03-08 12:10:27  pierre
	syntaxe xhtml

	Revision 1.6  2009-09-24 08:53:11  pierre
	*** empty log message ***

	Revision 1.5  2008-11-27 18:47:37  pierre
	*** empty log message ***

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
	
	Revision 1.2  2005/12/20 09:44:13  sylvie
	*** empty log message ***
	
	Revision 1.1  2005/11/02 15:22:13  pierre
	*** empty log message ***
	
	Revision 1.4  2005/10/12 08:12:08  pierre
	*** empty log message ***
	
	Revision 1.3  2005/10/11 12:41:58  pierre
	stripslashes
	
	Revision 1.2  2005/10/10 10:40:23  pierre
	*** empty log message ***
	
	Revision 1.1  2005/10/06 12:40:19  pierre
	diaporama
	
	Revision 1.1  2005/08/08 11:07:30  pierre
	fichier de l'éditeur de brique Image Animée
	
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
	
	Revision 1.1.1.1  2004/04/01 09:20:30  ddinside
	Création du projet CMS Couleur Citron nom de code : tipunch
	
	Revision 1.3  2004/02/05 15:56:25  ddinside
	ajout fonctionnalite de suppression de pages
	ajout des styles dans spaw
	debuggauge prob du nom de fichier limite à 30 caracteres
	
	Revision 1.2  2004/01/20 15:16:38  ddinside
	mise à jour de plein de choses
	ajout de gabarit vie des quartiers
	eclatement gabarits par des includes pour contourner prob des flashs non finalisés
	
	Revision 1.1  2004/01/07 18:27:37  ddinside
	première mise à niveau pour plein de choses
	
	Revision 1.2  2003/11/26 14:25:09  ddinside
	activation du menu
	
*/


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');

activateMenu('gestionbrique');

$id = $_GET['id'];


$composant = null;
$sTitre = "Créer";
if (strlen($id) > 0) {
	$composant = getComposantById($id);
$sTitre = "Modifier";
}

?>
<span class="arbo2">BRIQUE >&nbsp;</span><span class="arbo3"><?php echo $sTitre; ?> une brique diaporama</span><br /><br />
<?php

if(!strlen($_GET['type'])>0) {
	// premiere phase
	$nom = '';
	$larg = '';
	$long = '';
	$type='';
	if ($composant != null) {
		$nom = ' value="'.$composant['name'].'" ';
		$larg = ' value="'.$composant['width'].'" ';
		$long = ' value="'.$composant['height'].'" ';
		$type = ' value="'.$composant['type'].'" ';
		//echo "type :$type";
	}
?>
<link href="/backoffice/cms/css/bo.css" rel="stylesheet" type="text/css" />
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
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?idSite=<?php echo $idSite; ?>" method="get" name="creation" id="creation">
<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
<input type="hidden" name="minisite" value="<?php echo $_GET['minisite']; ?>" />

<table width="369" border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
  <tr>
    <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="arbo">
        <tr valign="middle"  >
          <td height="28" colspan="2" nowrap="nowrap" background="../../backoffice/cms/img/fond_tab.gif"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td class="arbo"><strong>&nbsp;Cr&eacute;ation de la brique Diaporama </strong></td>
              </tr>
          </table></td>
        </tr>
        <tr>
          <td colspan="2" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td align="right" style="vertical-align:middle">Type&nbsp;:&nbsp;</td>
          <td align="left"><select name="type" class="arbo">
		  <?php
$stack = array();
if ($dir = @opendir("./diaporama")) {
  while (($file = readdir($dir)) !== false) {
  if(substr($file, -3, 3) == "xml"){
	array_push($stack, substr($file, 0, -4));
	}
  }
}
closedir($dir);
array_multisort($stack, SORT_DESC, SORT_STRING);
foreach ($stack as $key => $value) {
   echo "<option value=\"".$value."\">".str_replace("_", " ",$value)."</option>\n";
}		  
		  ?>
          </select>
		  </td>
        </tr>
        <tr>
          <td colspan="2" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td align="right" style="vertical-align:middle">Nom&nbsp;:&nbsp;</td>
          <td align="left"><input name="nom" type="text" class="arbo" id="nom" size="30"  maxlength="30" <?php echo $nom; ?> /></td>
        </tr>
        <tr>
          <td  colspan="2" align="center"><br />
              <input name="Suite (création) >>" type="submit" class="arbo" id="Suite (création) >>" value="Suite (cr&eacute;ation) >>" />
              <br />
            <br /></td>
        </tr>
    </table></td>
  </tr>
</table>
</form>
<?php	
} else {
// --------------------------------------------------------
$_GET['nom'] = stripslashes($_GET['nom']);

//---------------------- XML --------------------------------
$file = 'diaporama/'.$_GET['type'].'.xml';
$stack = array();

function startTag($parser, $name, $attrs) 
{
   global $stack;
   $tag=array("name"=>$name,"attrs"=>$attrs);  
   array_push($stack,$tag);
  
}

function cdata($parser, $cdata)
{
   global $stack,$i;
   
   if(trim($cdata))
   {    
       $stack[count($stack)-1]['cdata']=$cdata;    
   }
}

function endTag($parser, $name) 
{
   global $stack;  
   $stack[count($stack)-2]['children'][] = $stack[count($stack)-1];
   array_pop($stack);
}

$xml_parser = xml_parser_create();
xml_set_element_handler($xml_parser, "startTag", "endTag");
xml_set_character_data_handler($xml_parser, "cdata");

$data = xml_parse($xml_parser,file_get_contents($file));
if(!$data) {
   die(sprintf("XML error: %s at line %d",
xml_error_string(xml_get_error_code($xml_parser)),
xml_get_current_line_number($xml_parser)));
}

xml_parser_free($xml_parser);

$stack = $stack[0];

// test nombre item / nbre spec
if (count($stack['attrs']['children']) < $stack['attrs']['NOMBRE']){

	$nbeLignesManquantes = $stack['attrs']['NOMBRE'] - count($stack['attrs']['children']);
	for ($i=1;$i<$nbeLignesManquantes;$i++){
		array_push ($stack['children'], $stack['children'][0]);
	}
}
/*
print("<pre>\n");
print_r($stack);
print("</pre>\n");
*/
// parcours des item
foreach ($stack['children'] as $key => $value) {
   //echo "Item $key: $value<br />\n";
   // parcours des attribus
   foreach ($value['attrs'] as $innerkey => $innervalue) {
   //echo "______ $innerkey=\"$innervalue\"<br />\n";
	   // lecture  des valeurs des attibuton et stockage en vars
		switch ($innerkey) {
		case "JPG":
		  $tempJpg = $innervalue;
		  break;
		case "TEXT":
		  $tempText = $innervalue;
		  break;
		}  
		// fin du switch		
	}
	// fin du foreach de parcours des attributs
	
}
//---------------------- FIN XML --------------------------------

// Chargement de la classe
require_once('cms-inc/lib/fileUpload/upload.class.php');

// Instanciation d'un nouvel objet "upload"
$Upload = new Upload();

// Pour ajouter des attributs aux champs de type file
$Upload-> FieldOptions = 'style="border-color:black;border-width:1px;"';

// Pour indiquer le nombre de champs désiré
$Upload-> Fields = intval($stack['attrs']['NOMBRE']);

// Initialisation du formulaire
$Upload-> InitForm();
?>

<form method="post" enctype="multipart/form-data" name="formulaire" id="formulaire" action="uploadDiaporama.php?id=<?php echo $_GET['id']; ?>">
<span class="arbo4"><strong>Upload des photos pour le diaporama <?php echo $_GET['nom']; ?>:</strong></span><br /><br />
<span class="arbo">
<?php
// Affichage du champ MAX_FILE_SIZE
print $Upload-> Field[0];

for ($i=1;$i<=$Upload-> Fields;$i++){
	// Affichage du premier champ de type FILE
	echo "<strong>Photo #".$i." :</strong><br />";
	echo $Upload-> Field[$i] . " (fichier JPG optimisé pour la taille d'affichage)<br />";
	echo "Légende :<br />";
	echo '<input type="text" name="texte'.$i.'" value="" class="arbo" size="100" maxlength="255"><br /><br />';
	}
?>
<br />
</span>
<input type="hidden" name="TYPEcontent" value="Diaporama" class="arbo" />
<input type="hidden" name="largeur" value="" class="arbo" />
<input type="hidden" name="hauteur" value="" class="arbo" />
<input type="hidden" name="nom" value="<?php echo $_GET['nom']; ?>" class="arbo" />
<input type="hidden" name="diaporama" value="<?php echo $_GET['type']; ?>" class="arbo" />
<input type="hidden" name="idSite" value="<?php echo $_GET['idSite']; ?>" />
<input name="submit" type="submit" class="arbo" value="Envoyer" /> 
</form>
<?php	
}
?>