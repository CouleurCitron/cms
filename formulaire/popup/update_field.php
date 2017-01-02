
<?php

// sponthus 05/06/2005
// popup utilisée pour l'ajout de champ Bouton Radio 
// dans une brique formulaire normal

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


// ------------------------------------------------
// BEGIN getter & post
// ------------------------------------------------

if( $_GET["idchamp"] != -1 ) {
	$id = $_GET["idchamp"];
	
}
if (isset ($_POST["id"]) && $_POST["id"] != "" ) {
	$id = $_POST["id"] ;
}

// id content 
if( $_GET["id"] != -1 ) {
	$id_content = $_GET["id"];
	
}
if (isset ($_POST["id_content"]) && $_POST["id_content"] != "" ) {
	$id_content = $_POST["id_content"] ;
}

if( $_GET["idForm"] != -1 && $_GET["idForm"] !='' ) {
	$idForm = $_GET["idForm"]; 
}
else if( $_POST["idForm"] != -1 && $_POST["idForm"] !='' ) {
	$idForm = $_POST["idForm"];
}
// ------------------------------------------------
// END getter & post
// ------------------------------------------------



 

// ------------------------------------------------
// BEGIN object Champ
// ------------------------------------------------

$oChamp = new Cms_champform($id); 
$nomchamp =  $oChamp->getName_champ();
$type = strtoupper ($oChamp->getType_champ());  
$sValeurDefault =  $oChamp->getValdefaut_champ();
$sLargeur =  $oChamp->getLargeur_champ();
if ($sLargeur == -1) $sLargeur = "";
$sHauteur =  $oChamp->getHauteur_champ();
if ($sHauteur == -1) $sHauteur = "";
$sTaillemax =  $oChamp->getTaillemax_champ();
if ($sTaillemax == -1) $sTaillemax = "";
$sObligatoire =  $oChamp->getObligatoire_champ();
if ($sObligatoire == "") $sObligatoire = 0;
if ($sObligatoire == 0 )  $sObligatoire_checked = "";  else $sObligatoire_checked = "checked" ;
$sPattern =  $oChamp->getPattern_champ();
$params = unserialize ($oChamp->getParams_champ());

if (isset ($params["br"]) && $params["br"] == true ) {
	$sBr = 1; 
	$sBr_checked = "checked";
	
}
else {
	$sBr = 0; 
	$sBr_checked = "";
}

if (isset ($params["class"]) && $params["class"] == true ) {
	$sClass = $params["class"];   
}  
/*echo $oChamp->getParams_champ(); 
pre_dump($params);	*/

// ------------------------------------------------
// END object Champ
// ------------------------------------------------



if (isset ($_POST["action"]) && $_POST["action"] == "update" ) {  
	 
	  

	if ($type == "BR" || $type == "RADIO" || $type == "COMBO" || $type == "SELECT" || $type == "CHECKBOX" ) { 
		$sValeur = str_replace ("\r\n", ";", trim($_POST["items"]));	  
		
	}
	if (isset($_POST["FCKeditor1"])) {
		$sValeur = $_POST["FCKeditor1"];	
		$sValeur = str_replace ( "\n", "" , $sValeur); 
		$sValeur = str_replace ( "\r", "" , $sValeur);  
	} 
	
	if ($_POST["size"] == "") $_POST["size"] = -1;
	if ($_POST["height"] == "") $_POST["height"] = -1;
	if ($_POST["length"] == "") $_POST["length"] = -1;
	if (isset($_POST["must"])) $_POST["must"] = 1; else $_POST["must"] = 0;
	if (isset($_POST["br"])) $_POST["br"] = 1; else $_POST["br"] = 0;
	
	$oChamp->setValdefaut_champ($_POST["val"]); 
	$oChamp->setLargeur_champ($_POST["size"]); 
	$oChamp->setHauteur_champ($_POST["height"]); 
	$oChamp->setTaillemax_champ($_POST["length"]); 
	$oChamp->setObligatoire_champ($_POST["must"]); 
	$oChamp->setValeur_champ($sValeur); 
	
	$oR = dbUpdate ($oChamp);
	
	echo '<script language="javascript"> 
			window.opener.document.forms[0].action = "/backoffice/cms/formulaire/formulaireEditor.php?step=1&id='.$id_content.'&idForm='.$idForm.'";
 			window.opener.document.forms["formgeneration"].submit();
			window.close();
		  </script>';
}
else {
	
}
$sValeur = str_replace(";", "\n", $oChamp->getValeur_champ()); 
?>
	<style type="text/css">
		@import "/css/menu.css";
	</style>

<link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">

<script language="javascript"><!--
	function makeit() {
		//document.forms['building'].
		document.forms['building'].submit();
		
		/*pattern = "";
		txtLine = "";
		items = document.forms['building'].items.value.split("\n");
		itemBDD="";
		
		for (var i=0;i<items.length;i++) {
			value = items[i].replace(/\r/,'');
			value = value.replace(/\n/,'');
			
			// écriture du libellé la première ligne seulement
			if (i==0) txtLine+= '<tr><td align="left" class="awsformlabel"><?php echo $_GET["nomchamp"]; ?>&nbsp;</td>';
			else txtLine+= '<tr><td align="left" class="awsformlabel">&nbsp;</td>';
			
			txtLine+= '<td class="awsformfield">';
			txtLine+='<input type="radio" name="<?php echo noAccent($_GET["nomchamp"]); ?>" id="<?php echo noAccent($_GET["nomchamp"]); ?>" value="' + value + '" class="awsformfield">' + value;
			txtLine+="</td></tr>";

			if (itemBDD != "") itemBDD = itemBDD + ";" + value;
			else itemBDD = value;

		}
		txtLine += "";

		window.opener.document.getElementById('valeur_champ_<?php echo $_GET["div"]; ?>').value = itemBDD;

		window.opener.document.getElementById('div_<?php echo $_GET["div"]; ?>').innerHTML = txtLine;
		window.opener.document.getElementById('sourceHTML_<?php echo $_GET["div"]; ?>').value = txtLine;*/

		//window.close();
	}
	/*function loadit() {		
		//document.forms['building'].items.value = window.opener.document.getElementById('valeur_champ_<?php echo $_GET["div"]; ?>').value.replace(/;/gi,'\n');
	}
	
	window.onload = loadit;*/
--></script>
</head>
<body>
<form name="building" id=name="building" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
<input type="hidden" name="action" id="action" value="update" /> 
	<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" /> 
	<input type="hidden" name="id_content" id="id_content" value="<?php echo $id_content; ?>" />
	<input type="hidden" name="idForm" id="idForm" value="<?php echo $idForm; ?>" />
	<input type="hidden" name="type" id="type" value="<?php echo $type; ?>" />
	
<div class="arbo"><b><u>Mise à jour du champ "<?php echo $nomchamp; ?>"</u></b></div><br/><br/>
<table cellpadding="0" cellspacing="5" border="0">

	<?php   
	if ($type == "HTML") {	
		
		include("backoffice/cms/lib/FCKeditor/fckeditor.php") ;
		
		$oFCKeditor = new FCKeditor('FCKeditor1') ;
 
		if (defined("DEF_FCK_TOOLBARSET")) $oFCKeditor->ToolbarSet = DEF_FCK_TOOLBARSET;
		else  $oFCKeditor->ToolbarSet = "Basic";
		$oFCKeditor->BasePath = '/backoffice/cms/lib/FCKeditor/' ;	// '/FCKeditor/' is the default value.		 
		 
		$oFCKeditor->Value = htmlFCKcompliant($sValeur);
		$oFCKeditor->Width  = '95%';
		$oFCKeditor->Height = '550';
		$oFCKeditor->Create();
		 
		$_POST['items'] = str_replace("\"", "&quot;", $_POST['items']); 
		
	 }
	 else {
	
		if ($type == "BR" || $type == "RADIO" || $type == "COMBO" || $type == "SELECT" || $type == "CHECKBOX") {
		
		?>
		 <tr>
			  <td align="right" nowrap class="arbo">Liste des choix&nbsp;:&nbsp;<br/>(un par ligne)</td>
			  <td align="right"><textarea name="items" id="items" class="arbo textareaEdit"><?php echo $sValeur; ?></textarea><br /><br /></td>
		 </tr>
		 <?php
		 }
		  
		 else { 
		 ?>
		<tr>
			<td align="right" class="arbo">valeur par défaut :&nbsp;</td>
			<td><input type="text" name="val" size="50" maxlength="150" class="arbo" value="<?php echo $sValeurDefault; ?>"></td>
		</tr>
		<?php
		 }
		 ?>
		<tr>
			<td align="right" class="arbo">largeur (en caractères):&nbsp;</td>
			<td><input type="text" name="size" size="2" value="<?php echo $sLargeur; ?>" maxlength="2" class="arbo"></td>
		</tr>
		<?php
		 if ($type == "AREA" || $type == "TEXTAREA") {
		 ?>
		<tr>
		  <td align="right" class="arbo">hauteur (en lignes):&nbsp;</td>
		  <td><input type="text" name="rows" size="2" value="5" maxlength="2" class="arbo"></td>
		 </tr>
		<?php } ?>
		
		<tr>
			<td align="right" class="arbo">Taille Max. (en caractères):&nbsp;</td>
			<td><input type="text" name="length" size="2" value="<?php echo $sTaillemax; ?>" maxlength="2" class="arbo"></td>
		</tr>
		<tr>
			<td align="right" class="arbo">Obligatoire (oui / non):&nbsp;</td>
			<td><input type="checkbox" name="must" value="<?php echo $sObligatoire; ?>" class="arbo" <?php echo $sObligatoire_checked; ?> ><br /><br /></td>
		</tr>
		
		<tr>
			<td align="right" class="arbo">Retour à la ligne - balise <br>		  
			  "&lt;br /&gt;&quot; (oui / non):&nbsp;</td>
			<td><input type="checkbox" name="br" value="<?php echo $sBr; ?>" class="arbo" <?php echo $sBr_checked; ?> ></td>
		</tr>
		<tr>
			<td align="right" class="arbo">Classe&nbsp;</td>
			<td><input type="text" name="classe" value="<?php echo $sClass; ?>"  class="arbo" ></td>
		</tr>
		<tr>
			<td align="right" class="arbo">Pattern:&nbsp;</td>
			<td><input type="text" name="pattern" size="80"   value="<?php echo $sPattern; ?>"  class="arbo" ><br /><br /></td>
		</tr>
 	
	<?php } ?>
  
<tr>
  <td align="center" colspan="2"><input type="button" name="bouton" value="Ajouter le champ au formulaire / Modifer le champ" onClick="javascript:makeit();" class="arbo"></td>
 </tr>
</table>
</form>
