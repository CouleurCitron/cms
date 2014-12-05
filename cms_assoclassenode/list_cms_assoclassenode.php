<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
$aCustom = array();
$aCustom["JS"] = " // js
function loadnodes(id){
		document.##classePrefixe##_list_form.display.value = id;
		document.##classePrefixe##_list_form.actiontodo.value = \"LOAD\";
		document.##classePrefixe##_list_form.action = \"load_cms_assoclassenode.php?display=\"+id+\"&\";
		document.##classePrefixe##_list_form.submit();
}
 ";
$aCustom["JS"] .= " // js
function savenodes(id){
		document.##classePrefixe##_list_form.display.value = id;
		document.##classePrefixe##_list_form.actiontodo.value = \"SAVE\";
		document.##classePrefixe##_list_form.action = \"save_cms_assoclassenode.php?display=\"+id+\"&\";
		document.##classePrefixe##_list_form.submit();
}
 ";
$aCustom["Action"] = "<a href=\"javascript:loadnodes(##id##);\" title=\"Charger les objets d'après les dossiers\"><img src=\"/backoffice/cms/img/right.gif\" width=\"14\" height=\"14\" alt=\"Charger les objets d'après les dossiers\" border=\"0\" /></a>";
$aCustom["Action"] .= "&nbsp;&nbsp;<a href=\"javascript:savenodes(##id##);\" title=\"Ecrire les dossier d'après les objets\"><img src=\"/backoffice/cms/img/left.gif\" width=\"14\" height=\"14\" alt=\"Ecrire les dossier d'après les objets\" border=\"0\" /></a>";

include('cms-inc/autoClass/list.php'); ?>