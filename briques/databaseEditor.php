<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/selectSite.php');

$aClasse = dbGetObjectsFromFieldValue("classe", array("get_statut"),  array(DEF_ID_STATUT_LIGNE), NULL);

if ($idSite == "") $idSite = $_SESSION['idSite_travail'];

activateMenu('gestionbrique');

$id = $_GET['id'];

$composant = null;
if (strlen($id) > 0)
	$composant = getComposantById($id);
?>
<div class="ariane"><span class="arbo2">Créer une brique</span></div><?php

//if (DEF_MENUS_MINISITES == "ON") print(putAfficheSite());
?>
<link href="/backoffice/cms/css/bo.css" rel="stylesheet" type="text/css" />
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
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?idSite=<?php echo $idSite; ?>&id=<?php echo $id; ?>&" method="get" name="creation" id="creation">
<input type="hidden" id="id" name="id" value="<?php echo $_GET['id']; ?>" />
<input type="hidden" name="picktype" id="picktype" value="picktype" />
<input type="hidden" name="minisite" id="minisite" value="<?php echo $_GET['minisite']; ?>" />
<table width="369" border="0" cellpadding="0" cellspacing="0" class="arbo">
  <tr>
    <td><table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="arbo">
        <tr valign="middle">
          <td height="28" colspan="2" nowrap="nowrap"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr class="col_titre">
                <td><strong>&nbsp;Cr&eacute;ation de la brique Objet Base de donn&eacute;es</strong></td>
              </tr>
          </table></td>
        </tr>
        <tr>
          <td colspan="2" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td align="right" style="vertical-align:middle">Type&nbsp;:&nbsp;</td>
          <td align="left"><select name="type" id="type" class="arbo">
		  	<option value="-1">Choisissez un objet de base de données</option>
		  	<?php
			foreach($aClasse as $cKey => $oClasse){		 
				 if($composant["obj_table_content"] == $oClasse->get_id()){
				 	$selected = " selected";
				 }
				 else{
				 	$selected = "";
				 }
				echo "<option value=\"".$oClasse->get_id()."\"".$selected.">".$oClasse->get_nom()."</option>\n";
			}
			?>          
          </select></td>
        </tr>
        <tr>
          <td colspan="2" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td align="right" style="vertical-align:middle">Nom&nbsp;:&nbsp;</td>
          <td align="left"><input name="nom" type="text" class="arbo" id="nom" size="30"  maxlength="30" <?php echo $nom; ?> /></td>
        </tr>
        <tr>
          <td colspan="2" align="center"><br />
              <input name="Suite (cr&eacute;ation) &gt;&gt;" type="submit" class="arbo" id="Suite (cr&eacute;ation) &gt;&gt;" value="Suite (cr&eacute;ation) &gt;&gt;"><br /><br /></td>
        </tr>
    </table></td>
  </tr>
</table>
</form>
<?php	
} elseif (is_get("picktype")) {	
	$aPickedClasse = dbGetObjectsFromFieldValue("classe", array("get_id"),  array(intval($_GET['type'])), NULL);
	
	if (count($aPickedClasse) == 0){
		?>
		<script language="javascript" type="text/javascript">
		alert("Choix de l'objet incorrect");
		history.back();
		</script>
		<?php
		die();
	}
	
	$oClasse = $aPickedClasse[0];
	$classeName = $oClasse->get_nom();
	
	$oTemp = new $classeName();
	$dispGetter = "get_".$oTemp->getDisplay();
	$absGetter = "get_".$oTemp->getAbstract();
	
	if($oTemp->getGetterStatut() != "none"){// statut
		$aValues = dbGetObjectsFromFieldValue($classeName, array("get_statut"),  array(DEF_ID_STATUT_LIGNE), NULL);	
	}
	else{// pas de statut
		$aValues = dbGetObjectsFromFieldValue($classeName, array(),  array(), NULL);
	}
?>
<form method="get" enctype="multipart/form-data" name="formulaire" id="formulaire" action="<?php echo $_SERVER['PHP_SELF']; ?>?idSite=<?php echo $idSite; ?>&id=<?php echo $id; ?>&">
<span class="arbo2"><strong>Choix de l'enregristrement de base pour <?php echo $_GET['nom']; ?>:</strong></span>
<br />
<select name="fitem" id="fitem" class="arbo">
	<option value="-1">Choisissez un enregistrement dans la base de données</option>
<?php
	foreach($aValues as $vKey => $oValue){	
		if($composant["obj_id_content"] == $oValue->get_id()){
			$selected = " selected";
		 }
		 else{
			$selected = "";
		 }	 
		echo "	<option value=\"".$oValue->get_id()."\" ".$selected.">".$oValue->$dispGetter()." - ".substr($oValue->$absGetter(),0,100)."</option>\n";
	}
?>          
</select>
<input type="hidden" id="TYPEcontent" name="TYPEcontent" value="database" class="arbo" />
<input type="hidden" id="largeur" name="largeur" value="" class="arbo" />
<input type="hidden" id="hauteur" name="hauteur" value="" class="arbo" />
<input type="hidden" id="nom" name="nom" value="<?php echo $_GET['nom']; ?>" class="arbo" />
<input type="hidden" id="classe" name="classe" value="<?php echo $_GET['type']; ?>" class="arbo" />
<input type="hidden" id="type" name="type" value="<?php echo $_GET['type']; ?>" class="arbo" />
<input type="hidden" id="idSite" name="idSite" value="<?php echo $idSite; ?>" />
<input type="hidden" id="id" name="id" value="<?php echo $id; ?>" />
<input type="hidden" id="pickitem" name="pickitem" value="pickitem" />
<input type="submit" id="submit" name="submit" class="arbo" value="Envoyer" />
</form>
<?php	
}elseif (is_get("pickitem")) {
	$aPickedClasse = dbGetObjectsFromFieldValue("classe", array("get_id"),  array(intval($_GET["classe"])), NULL);
	$oClasse = $aPickedClasse[0];
	
	$classeName = $oClasse->get_nom();
	
	$oTemp = new $classeName();
	$dispGetter = "get_".$oTemp->getDisplay();
	$absGetter = "get_".$oTemp->getAbstract();
	
	$aPickedItem = dbGetObjectsFromFieldValue($classeName, array("get_id"),  array(intval($_GET['fitem'])), NULL);
	
	if (count($aPickedItem) == 0){
		?>
		<script language="javascript" type="text/javascript">
		alert("Choix de l'enregistrement incorrect");
		history.back();
		</script>
		<?php
		die();
	}
	
	$file = $_SERVER['DOCUMENT_ROOT'].'/frontoffice/'.$classeName.'/foshow_'.$classeName.'.php';
	$fitem = $_GET['fitem'];
?>
<form method="post" name="formulaire" id="formulaire" action="saveComposant.php?idSite=<?php echo $idSite; ?>&id=<?php echo $id; ?>&">
<span class="arbo2"><strong>Preview de l'enregistrement de base pour <?php echo $_GET['nom']; ?>:</strong></span>
<?php
if (is_file($file)){	
	$fh = fopen(str_replace($_SERVER['DOCUMENT_ROOT'], "http://".$_SERVER['HTTP_HOST'], $file).'?id='.$fitem,'r');
	$sBodyHTML="";
		if ($fh){
		while(!feof($fh)) {
			$sBodyHTML.=fgets($fh);
		}
		fclose($fh);
		echo $sBodyHTML;
	}
	$sBodyHTML = preg_replace("/'/",'`',$sBodyHTML);	
}
else{
	echo "pas de fichier, je fais comment moi?<br />créer : ".$file."<br />please.";
}
?>
<input type="hidden" id="TYPEcontent" name="TYPEcontent" value="database" class="arbo" />
<input type="hidden" id="WIDTHcontent" name="WIDTHcontent" value="384">
<input type="hidden" id="HEIGHTcontent" name="HEIGHTcontent" value="500">
<input type="hidden" id="NAMEcontent" name="NAMEcontent" value="<?php echo $_GET['nom']; ?>">
<input type="hidden" id="HTMLcontent" name="HTMLcontent" value='<?php echo $sBodyHTML; ?>'>
<input type="hidden" id="OBJTABLEcontent" name="OBJTABLEcontent" value="<?php echo $_GET['type']; ?>">
<input type="hidden" id="OBJIDcontent" name="OBJIDcontent" value="<?php echo $_GET['fitem']; ?>">
<input type="hidden" id="largeur" name="largeur" value="" class="arbo" />
<input type="hidden" id="hauteur" name="hauteur" value="" class="arbo" />
<input type="hidden" id="nom" name="nom" value="<?php echo $_GET['nom']; ?>" class="arbo" />
<input type="hidden" id="classe" name="classe" value="<?php echo $_GET['type']; ?>" class="arbo" />
<input type="hidden" id="type" name="type" value="<?php echo $_GET['type']; ?>" class="arbo" />
<input type="hidden" id="idSite" name="idSite" value="<?php echo $idSite; ?>" />
<input type="hidden" id="pickeditem" name="pickeditem" value="<?php echo $_GET['fitem']; ?>" />
<input type="submit" id="submit" name="submit" class="arbo" value="Envoyer" />
<input type="hidden" id="id" name="id" value="<?php echo $id; ?>" />
</form>
<?php	
}
?>