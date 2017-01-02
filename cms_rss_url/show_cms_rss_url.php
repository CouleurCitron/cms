<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 
	$redirString="scan_cms_rss_url.php?id=".$_GET["id"]."&menuOpen=".$_GET["menuOpen"];
?> 
<script language="javascript" type="text/javascript">
	window.location.href="<?php echo $redirString; ?>";
</script>

<?php
?>