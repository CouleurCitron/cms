<script type="text/javascript">

<?php 

if($_SESSION['listParam']!='') {
	$listParam = $_SESSION['listParam'];
}
else {
	$_SESSION['listParam']=$_SERVER['QUERY_STRING'];
	$listParam=$_SESSION['listParam'];
}

$classeName= "cms_tag";
$classePrefixe = "tag"; 



?>
 /*
function validerTag(){	
	if (validate_form(0) && validerPattern() && validerChampsOblig()){  
		document.add_<?php echo $classePrefixe; ?>_form.action = "maj2_<?php echo $classeName; ?>.php<?php if($listParam!="") echo "?".$listParam; ?><?php if(isset($_GET["id"])&& $_GET["id"]!="") echo "&id=".$_GET["id"];?>";  
		document.add_<?php echo $classePrefixe; ?>_form.actiontodo.value="";
		document.add_<?php echo $classePrefixe; ?>_form.target = "_self";
		document.add_<?php echo $classePrefixe; ?>_form.submit(); 
	}		
}*/

</script> 
	
<?php  
function callback($buffer){ 
	
	$search='"validerForm()"';
	$replace='"validerTag()"';	

	return str_replace($search, $replace, $buffer);
}


//ob_start("callback");
include('cms-inc/autoClass/maj.php');
//ob_end_flush();
 ?>