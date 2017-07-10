<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

$script = explode('/',$_SERVER['PHP_SELF']);
$script = $script[sizeof($script)-1];

if (is_file($_SERVER['DOCUMENT_ROOT'].'/include/bo/cms/prepend.'.$_SERVER['PHP_SELF']))
	require_once('include/bo/cms/prepend.'.$_SERVER['PHP_SELF']);

$sRep = '/custom/newsletter/';
dirExists($sRep);
dirExists('/frontoffice/newsletter/template/');

if (!is_file($_SERVER['DOCUMENT_ROOT'].'/frontoffice/newsletter/read_newsletter.php')){
	copy('read_newsletter.php', $_SERVER['DOCUMENT_ROOT'].'/frontoffice/newsletter/read_newsletter.php');
}

$aCustom = array();
$aCustom["JS"] = " // js
function envoyer(id){
		document.##classePrefixe##_list_form.display.value = id;
		document.##classePrefixe##_list_form.actiontodo.value = \"REPONDRE\";
		document.##classePrefixe##_list_form.action = \"newsletter_create.php?display=\"+id+\"&lien=1\";
		document.##classePrefixe##_list_form.submit();
}

 function exporter(id){
		document.##classePrefixe##_list_form.display.value = id;
		document.##classePrefixe##_list_form.actiontodo.value = \"EXPORTER\";
		document.##classePrefixe##_list_form.action = \"/backoffice/cms/utils/telecharger.php?file=export_envoi_newsletter_\"+id+\".csv&chemin=".$sRep."&export=\"+id+\"\";
		document.##classePrefixe##_list_form.submit();			
}
 ";
$aCustom["Action"] = "<a href=\"javascript:envoyer(##id##);\" title=\"Envoyer\"><img src=\"/backoffice/cms/img/mail.gif\" width=\"20\" height=\"17\" alt=\"Envoyer\" border=\"0\" /></a>";

if (class_exists('news_stats')){
	$aCustom["Action"] .= "<a href=\"stats_newsletter.php?id=##id##\" title=\"Stats\"><img src=\"/backoffice/cms/img/modifier-xml.gif\" width=\"16\" height=\"16\" alt=\"Envoyer\" border=\"0\" /></a>";
}


if (defined("DEF_NEWSLETTER_USE_EXPORT") && DEF_NEWSLETTER_USE_EXPORT == "true") {
	$aCustom["Action"].= "&nbsp;&nbsp;&nbsp;<a href=\"javascript:exporter(##id##);\" title=\"Exporter les inscrits\"><img src=\"/backoffice/cms/img/go_off.gif\" width=\"13\" height=\"13\" alt=\"Exporter les inscrits\" border=\"0\" /></a>";
} 

include('cms-inc/autoClass/list.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoappend.php');
?>