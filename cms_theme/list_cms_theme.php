<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
$aCustom = array();
$aCustom["JS"] = " // js
function loadtheme(id){
		document.##classePrefixe##_list_form.display.value = id;
		document.##classePrefixe##_list_form.actiontodo.value = \"LOAD\";
		document.##classePrefixe##_list_form.action = \"load_cms_theme.php?display=\"+id+\"&\";
		document.##classePrefixe##_list_form.submit();
}
 ";
$aCustom["JS"] .= " // js
function savetheme(id){
		document.##classePrefixe##_list_form.display.value = id;
		document.##classePrefixe##_list_form.actiontodo.value = \"SAVE\";
		document.##classePrefixe##_list_form.action = \"save_cms_theme.php?display=\"+id+\"&\";
		document.##classePrefixe##_list_form.submit();
}
 ";
$aCustom["Action"] = "<a href=\"javascript:loadtheme(##id##);\" title=\"Charger depuis les fichiers\"><img src=\"/backoffice/cms/img/right.gif\" width=\"14\" height=\"14\" alt=\"Charger depuis les fichiers\" border=\"0\" /></a>";
$aCustom["Action"] .= "&nbsp;&nbsp;<a href=\"javascript:savetheme(##id##);\" title=\"Sauver dans les fichiers\"><img src=\"/backoffice/cms/img/left.gif\" width=\"14\" height=\"14\" alt=\"Sauver dans les fichiers\" border=\"0\" /></a>";

include('cms-inc/autoClass/list.php'); ?>