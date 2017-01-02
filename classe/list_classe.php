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
 /*
$aCustom["JS"] .= " // js
function as2(id){
		document.##classePrefixe##_list_form.display.value = id;
		document.##classePrefixe##_list_form.actiontodo.value = \"XMLEDIT\";
		document.##classePrefixe##_list_form.action = \"as2_classe.php?display=\"+id+\"&\";
		document.##classePrefixe##_list_form.submit();
}
 ";*/
$aCustom["Action"] = "<a href=\"javascript:xmledit(##id##);\" title=\"XML Edit\"><img src=\"/backoffice/cms/img/2013/icone/modifier-xml.png\"  alt=\"XML Edit\" border=\"0\" /></a>";
//$aCustom["Action"] .= "&nbsp;<a href=\"javascript:as2(##id##);\" title=\"XML Edit\"><img src=\"/backoffice/cms/img/generer-as2.gif\" width=\"16\" height=\"16\" alt=\"AS2 Class\" border=\"0\" /></a>";

include('cms-inc/autoClass/list.php'); ?>