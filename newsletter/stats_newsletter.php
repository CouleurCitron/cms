<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

activateMenu('gestiondesnewsletter');  //permet de dérouler le menu contextuellement

$id = $_GET['id'];

echo '<div class="ariane"><span class="arbo2">MODULE &gt;&nbsp;</span><span class="arbo3">newsletter&nbsp;&gt;&nbsp;Stats</span></div>';

//echo "<h2>statistiques</h2>";

$oNews = new newsletter($id);

echo "<h3>".$oNews->get_libelle()."</h3>";


echo '<table class="arbo" width="100%" cellspacing="0" cellpadding="5" bordercolor="#FFFFFF" border="1">
<tbody>';
echo '<tr>';
echo '<td class="arbo" width="141" bgcolor="#E6E6E6" align="right">&nbsp;<u><b>envoy&eacute;s</b></u></td>';
echo '<td class="arbo" width="535" bgcolor="#EEEEEE" align="left">'.$oNews->get_nbmail().'</td>';
echo '</tr>';
		
echo '<tr>';
echo '<td class="arbo" width="141" bgcolor="#E6E6E6" align="right">&nbsp;<u><b>re&ccedil;us</b></u></td>';
echo '<td class="arbo" width="535" bgcolor="#EEEEEE" align="left">'.$oNews->get_nbrecu().'</td>';
echo '</tr>';

$nbouvert=0;
$sql='SELECT count(*) FROM news_stats WHERE news_newsletter = "'.$id.'" AND news_inscrit != 0 AND news_action ="open" ';
$rs = $db->Execute($sql);
if($rs){
	while(!$rs->EOF) {
		$nbouvert = $rs->fields[0];
		break;
	}
}
echo '<tr>';
echo '<td class="arbo" width="141" bgcolor="#E6E6E6" align="right">&nbsp;<u><b>ouverts</b></u></td>';
echo '<td class="arbo" width="535" bgcolor="#EEEEEE" align="left">'.$nbouvert.'</td>';
echo '</tr>';

echo '<tr>';
echo '<td class="arbo" width="141" bgcolor="#E6E6E6" align="right">&nbsp;<u><b>liens</b></u></td>';
echo '<td class="arbo" width="535" bgcolor="#EEEEEE" align="left">';


$aLinks=array();
$sql='SELECT news_action FROM news_stats WHERE news_newsletter = "'.$id.'" AND news_action !="open" AND news_action !="url"  ';

$rs = $db->Execute($sql);
if($rs){
	while(!$rs->EOF) {
		$link=$rs->fields[0];

		if(!isset($aLinks[$link])){
			$aLinks[$link]=1;				
		}
		else{
			$aLinks[$link]++;
		}
		$rs->MoveNext();
	}
}
arsort ($aLinks);
foreach($aLinks as $link => $nb){		
	echo  $link. ' : ' .$nb.' clics<br />';
}

echo '</td>';
echo '</tr>';
echo '</tbody></table>';

include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoappend.php');
?>