<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/* 
$Author: raphael $
$Revision: 1.1 $

$Log: menuHome.php,v $
Revision 1.1  2013-09-30 09:24:10  raphael
*** empty log message ***

Revision 1.7  2013-03-01 10:28:04  pierre
*** empty log message ***

Revision 1.6  2010-03-08 12:10:28  pierre
syntaxe xhtml

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

Revision 1.2  2007/08/08 14:16:14  thao
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

Revision 1.1  2004/01/20 15:16:38  ddinside
mise à jour de plein de choses
ajout de gabarit vie des quartiers
eclatement gabarits par des includes pour contourner prob des flashs non finalisés

Revision 1.1  2003/11/27 14:45:56  ddinside
ajout gestion arbo disque non finie

Revision 1.2  2003/11/26 13:08:42  ddinside
nettoyage des fichiers temporaires commités par erreur
ajout config spaw
corrections bug menu et positionnement des divs

Revision 1.2  2003/10/07 08:15:53  ddinside
ajout gestion de l'arborescence des composants

Revision 1.1  2003/09/25 14:55:16  ddinside
gestion de l'arborescene des composants : ajout
*/


activateMenu('gestionpage');  //permet de dérouler le menu contextuellement
if (isset($_POST['Enregistrer'])) {
	$i=1;

	$sql = "delete from menuhome";
	if (DEF_BDD != "ORACLE") $sql.=";";
	$rs = $db->execute($sql);
		
	while( isset($_POST[$i.'libelle']) and isset($_POST[$i.'lien']) ) {
		$sql=" insert into menuhome(id, libelle, lien) values ($i,'".$_POST[$i.'libelle']."','".$_POST[$i.'lien']."')";
		if (DEF_BDD != "ORACLE") $sql.=";";

		$i++;
	}
	$rs = $db->execute($sql);
	if($rs) {
?><link rel="stylesheet" type="text/css" href="/backoffice/cms/css/bo.css">
<span class="arbo"><b><i>Mise à jour des données effectuée.</i></b><br /><br />
<?php
	} else {
?>
<b><i>Erreur lors de la mise à jour des données.</i></b>
<?php
	}
	
}


$elements = array();
$sql = 'select id, libelle, lien from menuhome order by id';
$rs = $db->execute($sql);
if($rs) {
	while(!$rs->EOF){
		$elements[$rs->fields[n('id')]] = array($rs->fields[n('libelle')],$rs->fields[n('lien')]); 
		$rs->MoveNext();
	}
}
?>
</span><span class="arbo2"><strong>Gestion des entrées du menu gauche de la page d'accueil</strong><br />
<br />
</span>
<script type="text/javascript"><!--
	var currentValue = 0;
	function updateValue(radiofield){
		if(currentValue!=0) {
			document.getElementById(currentValue+"libelle").style.setAttribute("background","#ffffff");
			document.getElementById(currentValue+"lien").style.setAttribute("background","#ffffff");
		}
		currentValue=radiofield.value;
		radiofield.checked=true;
		document.getElementById(currentValue+"libelle").style.setAttribute("background","#c0c8e2");
		document.getElementById(currentValue+"lien").style.setAttribute("background","#c0c8e2");
	}
	//#c0c8e2;

	function upValue() {
		if (currentValue>1){
			// MAJ libelle
			tmpValue = document.getElementById(currentValue+"libelle").value
			document.getElementById(currentValue+"libelle").value = document.getElementById((currentValue-1)+"libelle").value;
			document.getElementById((currentValue-1)+"libelle").value = tmpValue;
			// MAJ lien
			tmpValue = document.getElementById(currentValue+"lien").value
			document.getElementById(currentValue+"lien").value = document.getElementById((currentValue-1)+"lien").value;
			document.getElementById((currentValue-1)+"lien").value = tmpValue;
			// MAJ radio
			document.getElementById("selected"+(currentValue-1)).checked=true;
			document.getElementById(currentValue+"libelle").style.setAttribute("background","#ffffff");
			document.getElementById(currentValue+"lien").style.setAttribute("background","#ffffff");
			currentValue--;
			document.getElementById(currentValue+"libelle").style.setAttribute("background","#c0c8e2");
			document.getElementById(currentValue+"lien").style.setAttribute("background","#c0c8e2");
		}
	}

	function verifLink(id) {
		if( document.getElementById(id+"libelle").value=="---" ) {
			document.getElementById(id+"lien").value="";
		}
	}

	function downValue() {
		if (currentValue<25){
			// MAJ libelle
			tmpValue = document.getElementById(currentValue+"libelle").value
			document.getElementById(currentValue+"libelle").value =  document.getElementById((currentValue-(-1))+"libelle").value;
			document.getElementById((currentValue-(-1))+"libelle").value = tmpValue;
			// MAJ lien
			tmpValue = document.getElementById(currentValue+"lien").value
			document.getElementById(currentValue+"lien").value = document.getElementById((currentValue-(-1))+"lien").value;
			document.getElementById((currentValue-(-1))+"lien").value = tmpValue;
			// MAJ radio
			document.getElementById("selected"+(currentValue-(-1))).checked=true;
			document.getElementById(currentValue+"libelle").style.setAttribute("background","#ffffff");
			document.getElementById(currentValue+"lien").style.setAttribute("background","#ffffff");
			currentValue++;
			document.getElementById(currentValue+"libelle").style.setAttribute("background","#c0c8e2");
			document.getElementById(currentValue+"lien").style.setAttribute("background","#c0c8e2");
		}
	}
	
--></script>
<small class="arbo"><b>note : </b></small>
<form name="menu" action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
<table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
  <tr bgcolor="E6E6E6">
   <td align="center"><strong>&nbsp;&nbsp;Ordre d'affichage&nbsp;&nbsp;</strong></td>
   <td align="center"><strong>&nbsp;&nbsp;Libellé du lien&nbsp;&nbsp;</strong></td>
   <td align="center"><strong>&nbsp;&nbsp;Lien de destination&nbsp;&nbsp;</strong></td>
   <td align="center"><strong>&nbsp;&nbsp;Selection&nbsp;&nbsp;</strong></td>
  </tr>
<?php
foreach($elements as $id => $arrayLink) {
?>
  <tr bgcolor="EEEEEE" id="<?php echo $id; ?>">
   <td align="center">&nbsp;&nbsp;<?php echo $id; ?>&nbsp;&nbsp;</td>
   <td align="center"><input name="<?php echo $id; ?>libelle" type="text" class="arbo" id="<?php echo $id; ?>libelle" onFocus="updateValue(document.getElementById('selected<?php echo $id; ?>'));" onchange="verifLink(<?php echo $id; ?>);" value="<?php echo $arrayLink[0]; ?>" size="30" maxlength="50"></td>
   <td align="center"><input name="<?php echo $id; ?>lien" type="text" class="arbo" id="<?php echo $id; ?>lien" onFocus="updateValue(document.getElementById('selected<?php echo $id; ?>'));" onChange="verifLink(<?php echo $id;?>);" value="<?php echo $arrayLink[1]; ?>" size="50" maxlength="255"></td>
   <td align="center">&nbsp;&nbsp;<input name="selected" type="radio" class="arbo" id="selected<?php echo $id; ?>" onClick="updateValue(this);" value="<?php echo $id; ?>">
   &nbsp;&nbsp;
   </td>
  </tr>
<?php
}
?>
  <tr><td colspan="4" align="center"><table cellpadding="0" cellspacing="0" border="0" width="100%">
  					<tr>
					  <td align="center"><input name="Haut" type="button" class="arbo" onClick="upValue();" value="Haut"></td>
					  <td align="center"><input name="Bas" type="button" class="arbo" onClick="downValue();" value="Bas"></td>
					</tr>
				     </table>
  </td></tr>
  <tr bgcolor="D2D2D2"><td colspan="4" align="center"><input name="Enregistrer" type="submit" class="arbo" value="Enregistrer"></td></tr>
</table>
   <script type="text/javascript"><!-- 
<?php
foreach($elements as $id => $arrayLink) {
?>
   verifLink(<?php echo $id; ?>);
<?php
}
?>
   --></script>
</form>
