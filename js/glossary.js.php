<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
header("Content-type: text/javascript");

	 
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');


if (!isset($translator)){
	$translator =& TslManager::getInstance(); 
}

$sVoirGlossaire = $translator->getText('Voir le glossaire', $_SESSION["id_langue"]);
$sGlossaire = $translator->getText('Glossaire', $_SESSION["id_langue"]);

if ($sVoirGlossaire == '') {
	$translator->addTranslation ('Voir le glossaire', array("1" => "Voir le glossaire", "2" => "See glossary"));
	$sVoirGlossaire = $translator->getText('Voir le glossaire', $_SESSION["id_langue"]);
}

if ($sGlossaire == '') {
	$translator->addTranslation ('Glossaire', array("1" => "Glossaire", "2" => "Glossary"));
	$sGlossaire = $translator->getText('Glossaire', $_SESSION["id_langue"]);
}

if (defined("DEF_HREF_FCK_GLOSSAIRE_".strtoupper($_SESSION["site_travail"]))) {
	 eval ( "$"."path_Glossaire = "."DEF_HREF_FCK_GLOSSAIRE_".strtoupper($_SESSION["rep_travail"]).";") ;
}
else {
	$path_Glossaire = "/content/".$_SESSION["rep_travail"]."/".$sGlossaire."/index.php?id=XX-ID-XX";
}

$path_Glossaire = str_replace ("'", "\'", $path_Glossaire) ;

?>	
$(document).ready(function(){
	// Appel fancybox du bloc de création de compte
	
	var idencours ;
	var textencours ;
	  
	$(".tt").bind('mouseenter', function(e) { 
	
	
		var id = ($(this).attr("id"));
		
		
		var id2 ;
		
		if (id != undefined) {
			id2 = id.replace('glo_','');
			
			idencours = id2;
			textencours = ($(this).html()); 
			
			loadDefinition(id2, $(this), textencours);
		}
		
	});
	
	
	$(".tt").bind('mouseleave', function(e) {  
		 $(this).html(textencours);
		 //$(this).attr("href", "#"+idencours );
		
	});
	
	
});

 
function loadDefinition(id, monHref, mot) {  
	//alert(monHref.html());
	//var mot = monHref.html();

	$.ajax({
		//this is the php file that processes the data and send mail
		url: "/include/cms-inc/glossary/get_definition.ajax.php",		
		//POST method is used
		type: "POST",
		//pass the data	
		data: {
			 '_id' : id 
		},					
		//Do not cache the page
		cache: true,			
		//success
		success: function (data) {
			if (id != '') {
				monHref.html(   mot + "<span class=\"tooltip\" id=\"tooltip\"> "+data+"<br /><u><?php echo $sVoirGlossaire;?></u></span>"); 
				
				// on modifie le lien href
				var urlCible = '<?php echo $path_Glossaire;?>';
				urlCible = urlCible.replace('XX-ID-XX', id);
				monHref.attr("href", urlCible );
			}
			 
		},
		error: function (data) {
			alert('Error calling filtering results');
		}
	});
	return false;  
}
