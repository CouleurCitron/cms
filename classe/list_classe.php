<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

$aCustom = array();
$aCustom["JS"] = " // js
function xmledit(id){
		document.##classePrefixe##_list_form.display.value = id;
		document.##classePrefixe##_list_form.actiontodo.value = \"XMLEDIT\";
		document.##classePrefixe##_list_form.action = \"xmledit_classe.php?display=\"+id+\"&\";
		document.##classePrefixe##_list_form.submit();
}
 ";

$aCustom["Action"] = "<a href=\"javascript:xmledit(##id##);\" title=\"XML Edit\"><img src=\"/backoffice/cms/img/modifier-xml.gif\" width=\"16\" height=\"16\" alt=\"XML Edit\" border=\"0\" /></a>";

include('cms-inc/autoClass/list.php'); ?>