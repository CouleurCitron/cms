<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 
$Author: quentin $
$Revision: 1.2 $

$Log: formEditor.php,v $
Revision 1.2  2013-10-07 15:09:51  quentin
*** empty log message ***

Revision 1.1  2013-09-30 09:34:15  raphael
*** empty log message ***

Revision 1.2  2013-03-01 10:28:05  pierre
*** empty log message ***

Revision 1.1  2012-12-12 14:26:36  pierre
*** empty log message ***

Revision 1.9  2012-07-31 14:24:47  pierre
*** empty log message ***

Revision 1.8  2012-03-02 09:28:19  pierre
*** empty log message ***

Revision 1.7  2010-03-08 12:10:28  pierre
syntaxe xhtml

Revision 1.6  2008-11-28 15:14:19  pierre
*** empty log message ***

Revision 1.5  2008-11-28 14:09:55  pierre
*** empty log message ***

Revision 1.4  2008-11-06 12:03:52  pierre
*** empty log message ***

Revision 1.3  2008-08-28 08:38:12  thao
*** empty log message ***

Revision 1.2  2007/11/15 18:08:49  pierre
*** empty log message ***

Revision 1.4  2007/11/06 15:33:25  remy
*** empty log message ***

Revision 1.1  2007/08/08 14:26:20  thao
*** empty log message ***

Revision 1.2  2007/08/08 13:56:04  thao
*** empty log message ***

Revision 1.1.1.1  2006/01/25 15:14:32  pierre
projet CCitron AWS 2006 Nouveau Website

Revision 1.2  2005/10/24 15:10:07  pierre
arbo.lib.php >> arbominisite.lib.php

Revision 1.1.1.1  2005/10/24 13:37:05  pierre
re import fusion espace v2 et ADW v2

Revision 1.1.1.1  2005/10/20 13:10:53  pierre
Espace V2

Revision 1.1  2004/05/11 08:16:56  ddinside
suite maj boulogne

*/

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/arbominisite.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');

activateMenu('gestionpage');  //permet de dérouler le menu contextuellement

if($_GET['step']=='init') {
	$step = 0;
}
$bodyform = '<tr id="titre"><td colspan="2"><h1>'.$_POST['nom'].'</h1></td></tr>';
$step++;

?>
<script type="text/javascript"><!--
	var nbLines = 0;
	srcHTML='';
	function initBody() {
		if (document.visuform.document.body.innerHTML==''){
			srcHTML = '<?php echo $bodyform; ?>';
			document.visuform.document.body.innerHTML='<?php echo $bodyform; ?>';
		}
	}

	function addLine(txtobj) {
		if(txtobj!=null) {
			initBody();
			srcHTML += txtobj;
			document.visuform.document.body.innerHTML= '<table>'+ srcHTML + '</table>'
		}
	}

	function closeBody() {
		document.visuform.document.body.innerHTML+='</table>';
	}

	function getNbLines() {
		return nbLines;
	}

	function setNbLines(i){
		nbLines = i;
	}

	function envoyerLeTout(obj) {
		document.getElementById('sourceHTML').value = srcHTML;
	}
	
--></script>
<?php
if($_GET['step']=='init') {
?>
<form name="genForm" method="post" action="<?php echo $_SERVER['PHP_SELF']."?step=$step"; ?>">
Nom du formulaire&nbsp;:&nbsp;<br/>
<input type="text" name="nom" id="nom" pattern="^.+$" errorMsg="Le nom est obligatoire"><br/></br>
Destinataire(s) / une adresse email par ligne&nbsp;:&nbsp;<br/>
<textarea name="destinataires" class="textareaEdit" id="destinataires"></textarea><br/><br/>
<input type="button" name="bouton" value="Suite" pattern="^.+$" errorMsg="Au moins une adresse email est requise." onClick="if(validate_form()) {submit();}">
<?php
} elseif ( (isset($_POST['nom']) && ($_GET['step']==1)) ) {
$destinataires = join(',',split("\n",$_POST['destinataires']));
?><form name="formgeneration" action="<?php echo $_SERVER['PHP_SELF'].'?step=2'; ?>" method="post">
<h1>Génération du formulaire <?php echo $_POST['nom'];?></h1>
<iframe src="formvierge.php" name="visuform" width="450" height="500"></iframe>
<input type="hidden" name="destinataires" value="<?php echo $destinataires; ?>">
<input type="hidden" name="nom" value="<?php echo $_POST['nom']; ?>">
<br/><br/>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr>
    <td align="center" valign="center"><a href="#" onClick="addLine(showModalDialog('./popup/text.php',getNbLines(),'dialogHeight:220px; dialogWidth:366px; resizable:no; status:no'));">texte</a></td>
    <td align="center" valign="center"><a href="#" onClick="addLine(showModalDialog('./popup/checkbox.php',getNbLines(),'dialogHeight:350px; dialogWidth:500px; resizable:no;status:no'));">case à cocher</a></td>
    <td align="center" valign="center"><a href="#" onClick="addLine(showModalDialog('./popup/radio.php',getNbLines(),'dialogHeight:320px; dialogWidth:456px; resizable:no;status:no'));">boutons radio</a></td>
    <td align="center" valign="center"><a href="#" onClick="addLine(showModalDialog('./popup/combobox.php',getNbLines(),'dialogHeight:320px; dialogWidth:456px; resizable:no;status:no'));">Liste déroulante</a></td>
    <td align="center" valign="center"><a href="#" onClick="addLine(showModalDialog('./popup/textarea.php',getNbLines(),'dialogHeight:220px; dialogWidth:366px; resizable:no;status:no'));">grande zone texte</a></td>
    <td align="center" valign="center"><a href="#" onClick="javascript:window.open('./popup/html.php',getNbLines(),'Height=534,Width=523,resizable=no;status=no');">contenu HTML</a></td>
  </tr>
</table>
<br/><br/>
<b>Destinataires&nbsp;:&nbsp;</b>
<ul>
<?php
foreach (split(',',$destinataires) as $dest) {
?>
<li><?php echo $dest; ?></li>
<?php	
}
?>
</ul>
<input type="hidden" name="sourceHTML" id="sourceHTML" value="">
<input type="button" name="generate2" value="Voir la source IE" onClick="javascript:window.alert(document.visuform.document.body.innerHTML);">
<input type="button" name="generate3" value="Voir la source HTML" onClick="javascript:window.alert(srcHTML);">
<input type="button" name="generate4" value="Générer le formulaire" onClick="javascript:envoyerLeTout(this);submit();">
</script>
</form>
<?php
} elseif ( (isset($_POST['nom']) && ($_GET['step']==2)) ) {
	$srcHTML = $_POST['sourceHTML'];
	
	$srcHTML = "<script src=\"/backoffice/cms/js/validForm.js\"></script>
	<input type=\"hidden\" name=\"destEmails\" value=\"".$_POST['destinataires']."\">
	<input type=\"hidden\" name=\"formulaire\" value=\"".$_POST['nom']."\">
	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">".$srcHTML;
	$srcHTML .= "<tr>
	<td align=\"center\"><input type=\"button\" name=\"".$_POST['nom']."\"value=\"Envoyer\" onClick=\"javascript:if(validate_form()){submit();}\"></td>
	<td align=\"center\"><input type=\"reset\"></td>
	</tr>
	</table>";
?>
<h1>Prévisualisation du formulaire "<?php echo $_POST['nom']; ?>"</h1>
<hr width="485" align="left">
<div style="width: 450px; height: 500px; background-color: #ffffff; overflow: auto;">
<?php 
echo stripslashes($srcHTML); 

$srcHTML = "<form name=\"".preg_replace('/\ /','_',$_POST['nom'])."MyForm\" action=\"/frontoffice/eformalites/sendform.php\" method=\"post\">".$srcHTML;
$srcHTML.= "</form>";
?>
</div>
<hr width="485" align="left">
<small><u>Dimensions&nbsp;:</u>&nbsp;485 x 464&nbsp;pixels<br />Vous pourrez modifier ces dimensions à loisir lors de la mise en page.</small>
	<form name="previewHTML" action="saveComposant.php" method="post">
		<input type="hidden" name="TYPEcontent" value="formulaire">
		<input type="hidden" name="WIDTHcontent" value="450">
		<input type="hidden" name="HEIGHTcontent" value="500">
		<input type="hidden" name="NAMEcontent" value="<?php echo $_POST['nom']; ?>">
		<input type="hidden" name="HTMLcontent" value='<?php echo stripslashes($srcHTML); ?>'>
		<input type="submit" name="Suite (enregistrer) >>" value="Suite (enregistrer) >>">
	</form>
<?php	
} else {
?>
Appel incorrect de la fonctionnalité. Merci de contacter l'administrateur si l'erreur persiste.
<?php
}
?>

