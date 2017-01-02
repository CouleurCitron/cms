
<?php

// sponthus 05/06/2005
// popup utilisée pour l'ajout de champ Bouton Radio 
// dans une brique formulaire normal

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
if( $_GET["idchamp"] != -1 ) {
	$id = $_GET["idchamp"];
	
}
if (isset ($_POST["id"]) && $_POST["id"] != "" ) {
	$id = $_POST["id"] ;
}

$oChamp = new Cms_champform($id); 
 
if (isset ($_POST["action"]) && $_POST["action"] == "update" ) {  
	$sValeur = str_replace ("\n", ";", trim($_POST["items"]));	
	//echo $sValeur;	
	 
	 
	$oChamp->setValeur_champ($sValeur); 
	$oR = dbUpdate ($oChamp);
	
	echo '<script language="javascript"> 
			window.opener.document.forms[0].action = "/backoffice/cms/formulaire/formulaireEditor.php?step=1&id=3";
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
<div class="arbo"><b><u>Ajout de boutons radios (choix) :</u></b></div><br/><br/>
<table cellpadding="0" cellspacing="0" border="0">
 <tr>
  <td align="right" nowrap class="arbo">Liste des choix&nbsp;:&nbsp;<br/>(un par ligne)</td>
  <td align="right">
  	<textarea name="items" id="items" class="arbo textareaEdit"><?php echo $sValeur; ?></textarea>
	<input type="hidden" name="action" id="action" value="update" /> 
	<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
	<input type="hidden" name="name_form" id="name_form" value="<?php echo $id; ?>" />
	 
  </td>
 </tr>
<tr>
  <td align="center" colspan="2"><input type="button" name="bouton" value="Ajouter le champ au formulaire" onClick="javascript:makeit();" class="arbo"></td>
 </tr>
</table>
</form>
