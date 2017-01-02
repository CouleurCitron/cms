 <?php


include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php'); 

activateMenu("list_cms_minisite");

if (is_get("display")) {
	$id = $_GET["display"];
	$oMinisite = new Cms_minisite ($id);
	
	echo '<script type="text/javascript">
	prompt ("Lien direct du site", "http://'.$_SERVER['HTTP_HOST'].'/'.$oMinisite->get_url().'"); 
	</script>';
}

include('cms-inc/autoClass/list.php');
?>