<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
 
if (!isset($classeName)){
	$classeName = preg_replace('/[^_]*_(.*)\.php/', '$1', basename($_SERVER['PHP_SELF']));
}
//------------------------------------------------------------------------------------------------------

// permet de dérouler le menu contextuellement
if (strpos($_SERVER['PHP_SELF'], "backoffice") !== false){
	activateMenu("gestion".$classeName); 
}
 
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php'); 

// objet 
eval("$"."oRes = new ".$classeName."();");

$sXML = $oRes->XML;
xmlClassParse($sXML);

$classeName = $stack[0]["attrs"]["NAME"];
$classePrefixe = $stack[0]["attrs"]["PREFIX"];
$aNodeToSort = $stack[0]["children"];

$field = "dt_modif";

?>

<script type="text/javascript">    

    function validate_form() { 
		var date_export = document.formFile.<?php echo "f".ucfirst($classePrefixe)."_".$field; ?>.value;
		if (!isDate(date_export)) { 
			alert( "Mauvaise syntaxe (jj/mm/aaaa)" );
		}
		else {
			items = date_export.split("/");	
			date_export = items[2]+"-"+items[1]+"-"+items[0];
			window.open('http://<?php echo $_SERVER['HTTP_HOST']?>/backoffice/cms/news_inscrit/exportcsv_news_inscrit.php?champ0=<?php echo strtolower($classePrefixe)."_".$field; ?>&valeur0='+date_export+'&operateur0==','Export_inscrit','');

		} 
	
	}
</script>    

<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="/backoffice/cms/lib/jscalendar/calendar-win2k-cold-1.css" title="win2k-cold-1" />
<!-- main calendar program -->
<script type="text/javascript" src="/backoffice/cms/lib/jscalendar/calendar.js"></script>
<!-- language for the calendar -->
<script type="text/javascript" src="/backoffice/cms/lib/jscalendar/lang/calendar-fr.js"></script>
<!-- the following script defines the Calendar.setup helper function -->
<script type="text/javascript" src="/backoffice/cms/lib/jscalendar/calendar-setup.js"></script>

<p><span class="arbo2"><strong><?php echo $classeName; ?>  > Export d'un fichier de données</strong></span><br />

  <br />
</p>
<p><strong>Saisissez  une date d'export</strong><br />

  <br />
</p>
<table  border="1" cellpadding="5" cellspacing="0" class="arbo" width="600">
<form name="formFile" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">

 <tr bgcolor="#CCCCCC" valign="middle">
  <td nowrap align="center">&nbsp;<b>Date d'export</b>&nbsp;</td>
  <td nowrap align="left"><?php echo "<input type=\"text\" name=\"f".ucfirst($classePrefixe)."_".$field."\" id=\"f".ucfirst($classePrefixe)."_".$field."\" class=\"arbo\" size=\"80\" value=\"".str_replace('"', '&quot;', $eKeyValue)."\" ".$disabled." />\n<img src=\"/backoffice/cms/lib/jscalendar/img.gif\" id=\"f".ucfirst($classePrefixe)."_".$field."_trigger\" style=\"cursor: pointer; border: 1px solid red;\" title=\"Date selector\" onmouseover=\"this.style.background='red';\" onmouseout=\"this.style.background=''\" /><br />format date (jj/mm/aaaa)";
									?>
									<script type="text/javascript" language="javascript">
									Calendar.setup({
										inputField     :    "<?php echo "f".ucfirst($classePrefixe)."_".$field; ?>",  // id of the input field
										ifFormat       :    "%d/%m/%Y",      // format of the input field
										button         :    "<?php echo "f".ucfirst($classePrefixe)."_".$field."_trigger"; ?>", // trigger ID
										align          :    "Tl",           // alignment (defaults to "Bl")
										singleClick    :    true
									});
									</script>
									
									</td>
 </tr>
 
 <tr bgcolor="#CCCCCC" valign="middle">
   <td nowrap align="center">&nbsp;</td>
   <td nowrap align="left"><input type="button" value="Exporter" name="export" onclick="javascript:if (validate_form(0)) submit();" class="arbo" /></td>
 </tr>
</form>
</table>