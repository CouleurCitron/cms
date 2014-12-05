/*
sponthus 04/08/2005

fonctions de preview des composants et des pages

*/


function preview_content(id,width,height)
{
	window.open("/backoffice/cms/previewComposant.php?id="+id,"preview","scrollbar=yes, resizable=yes, menubar=no, width=" + width +",height=" + height);
}

function preview_content_version(id, width, height, version){
	window.open("/backoffice/cms/previewComposant.php?id="+id+"&version="+version,"preview","scrollbar=yes, resizable=yes, menubar=no, width=" + width +",height=" + height);
}

function preview_gabarit(id){
	window.open("/backoffice/cms/site/previewGabarit.php?id="+id, "preview")//,scrollbars=no,width=" + width +",height=" + height);
}