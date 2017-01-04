<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once('export_news.php');
activateMenu('gestiondesnewsletter');  //permet de dérouler le menu contextuellement
?>
<script type="text/javascript">

// envoie au groupe sélectionné
function doSend()
	{
		document.frm_grp_theme.actiontodo.value = "SEND";
		document.frm_grp_theme.action = "newsletter_envoi0.php";
		document.frm_grp_theme.submit();
	}


	// retour à la page précédente
	function retour()
	{
		document.add_news_form.action = "list_newsletter.php?menuOpen=true"; 
		document.add_news_form.submit(); 
	}
</script>
<?php
// newsletter

$id = $_GET['display'];
if ($id == "") {
	$id = $_POST['id'];
}
$oNews = new newsletter($id);

// envoi
if ($_POST['actiontodo'] == "SEND") {
//	$idNews = $_POST['display']id;
	// vider la table des contacts à envoyer
	//dbDeleteAll("News_select");

	// la remplir avec tous ces contacts
	foreach($_POST as $k=>$v)  
	{
		$tbK = explode("_", $k);

		 // contacts sélectionnés par groupe	
		if ($tbK[0] == 'chkgrp') 
		{
			//--------------------------------------
			// inscrits pour cet intérêt
			$sql = " SELECT news_inscrit.* FROM news_inscrit, news_rel_01 ";
			$sql.= " WHERE ins_id=r01_ins_id AND r01_int_id=".$tbK[1];
			$aInscrit = dbGetObjectsFromRequete("Inscrit", $sql);
			//--------------------------------------

			for ($p=0; $p<sizeof($aInscrit); $p++) {

				$oInscrit = $aInscrit[$p];

				$objNews = new Inscrit($oInscrit->getIns_id());

				if (!$objNews->isSelected()) {
	
					$objNews->addSelected();
					$objNews = null;
	
				} 
			}
		}
	}

	// envoyer
	$_SESSION['exp'] = "select";
	// newsletter à envoyer
	$_SESSION['news'] = $id;
	include_once("newsletter_envoi1.php");
	
	$sRep = $_SERVER['DOCUMENT_ROOT']."/custom/newsletter/";
	//$eFile = export_list_inscrits_envoi($sRep, $id);
	?>
	<form name="add_news_form" method="post" enctype="multipart/form-data">
	<?php
	echo "<span class=\"arbo2\">NEWSLETTER >&nbsp;</span><span class=\"arbo3\">Envoyer une newsletter</span><br><br><span class=\"arbo\">".$sMessage."</span>";
	// rapport inclu dans envoi_list.php

	echo '<br><br><input class="arbo" type="button" value="<< Retour à la liste des newsletter" onclick="retour()">';
	?>
	</form>
	<?php
	exit();
}
?>
<span class="arbo2">NEWSLETTER >&nbsp;</span><span class="arbo3">Envoyer une newsletter</span><br><br>
<?php
if ($oNews->get_statut() == DEF_CODE_STATUT_DEFAUT) { ?>
	<form name="add_news_form" method="post" enctype="multipart/form-data">
	<table class="arbo" cellpadding="5" cellspacing="0" width="700">
	<tr>
		<td><?php
	echo "<br><br>La newsletter n'a pas été validé.<br><br>"; 
	?>
	</td></tr>
	</form>
	<tr><td align="center">
	<?php
	if ($_POST['urlRetour'] != "") {
	echo '<input class="arbo" type="button" value="<< Retour à la liste des newsletter" onclick="retour()">';
	}
	?>
	</td></tr></table>
	<?php
}
else if ($oNews->get_statut() == DEF_ID_STATUT_NEWS_ENVOI) { ?>
	<form name="add_news_form" method="post" enctype="multipart/form-data">
	<table class="arbo" cellpadding="5" cellspacing="0" width="700">
	<tr>
		<td><?php
	echo "<br><br>La newsletter a déjà été envoyé. Vous pouvez modifier son statut pour le renvoyer à nouveau.<br><br>"; 
	?>
	</td></tr>
	</form>
	<tr><td align="center">
	<?php 
	if ($_POST['urlRetour'] != "") {
	echo '<input class="arbo" type="button" value="<< Retour à la liste des newsletter" onclick="retour()">';
	}
	?>
	</td></tr></table>
	<?php
}
else {  // la news est validée
?>
<form name="frm_grp_theme" method="post">
<input type="hidden" name="actiontodo" id="actiontodo">
<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
<input type="hidden" name="typnews" id="typnews" value="4">
<table class="arbo" cellpadding="5" cellspacing="0" >
	<tr>
		<td colspan="2">Vous êtes sur le point d'envoyer la newsletter suivante : </td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td align="right">Titre</td>
		<td><strong><?php echo $oNews->get_titre(); ?></strong>&nbsp;</td>
	</tr>
	<tr>
		<td align="right">Date</td>
		<td><?php
$sDate = $oNews->get_dateenvoi();
if (isDdateVide($sDate)) $sDate = getDateNow();
echo $sDate;
?>&nbsp;</td>
	</tr>
	<tr>
	  <td align="right">Nombre d'inscrits</td>
	  <td><?php
		$eCountInscrit=getCount("news_inscrit", "ins_id", "ins_theme", $oNews->get_theme()." and ins_statut=".DEF_ID_STATUT_LIGNE, NUMBER);
		echo $eCountInscrit." inscrit(s)";
		?> </td>
    </tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2">
<iframe name="newsIframe" title="newsIframe" id="newsIframe" width="600" height="500" frameborder="0" src="newsletter_modele.php?id=<?php echo $id?>" marginheight="0" marginwidth="0" scrolling="yes"></iframe></td>
	</tr>
	<tr>
		<td colspan="2">&nbsp; </td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="button" value="Envoyer la newsletter" class="arbo" onClick="javascript:doSend()"></td>
	</tr>
</table>
</form>
<?php
} //
?>