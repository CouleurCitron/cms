<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/lib.inc.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/htmlUtility.php'); 

if (is_post("fsql")){
	$aSQL = explode(';', trim($_POST["fsql"]));
	
	$eReturn = 0;
	
	foreach($aSQL as $id => $sSQL){
		if (trim($sSQL)!=''){
			$bReturn = dbExecuteQuery(trim($sSQL).';');
				
			if ($bReturn == true){
				echo '<strong>SQL executed OK</strong><br />';
				$eReturn++;
			}
			else{
				echo '<strong>SQL failed to execute</strong><br />';
			}
		}
	}
	echo '<strong>'.$eReturn.' request(s) executed OK</strong><br />';
}

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="sqlForm" name="sqlForm" method="post">
<textarea id="fsql" name="fsql" class="textareaEdit">
<?php
if (is_post("fsql")){
	echo join(';', $aSQL);
}
?>
</textarea>
<input type="submit" value="go" />
</form>