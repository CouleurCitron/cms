<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 
$aCustom = array();
$aCustom["JS"] = " // js
function xmledit(id){
		document.##classePrefixe##_list_form.display.value = id;
		document.##classePrefixe##_list_form.actiontodo.value = \"XMLEDIT\";
		document.##classePrefixe##_list_form.action = \"xmledit_classe.php?display=\"+id+\"&\";
		//document.##classePrefixe##_list_form.action = \"xmledit_classe.php\";
		document.##classePrefixe##_list_form.submit();
}
 ";

$aCustom["Action"] = "<a href=\"/backoffice/cms/cms_rss/feed_cms_rss.php?id=##id##\" title=\"Feed URL\" target=\"_blank\" onclick=\"prompt('Permalien vers cette page:', 'http://".$_SERVER['HTTP_HOST']."/backoffice/cms/cms_rss/feed_cms_rss.php?id=##id##'); return false;\"><img src=\"/backoffice/cms/img/2013/icone/link.png\" alt=\"Feed URL\" border=\"0\" /></a>";


include('cms-inc/autoClass/list.php'); ?>