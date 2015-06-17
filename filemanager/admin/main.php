<table width="100%" border="0" cellpadding="0" cellspacing="1">
  <tr>
    <td align="center"><strong class="arbo2">Global configuration menu ...</strong></td>
  </tr>
  <tr>
    <td class="arbo">
	<?php
	if( isset($_POST['change']) )
	{
	$fp=fopen("globals.php","w");
                
				$out=sprintf("<?php \r\n");
				fwrite($fp,$out,strlen($out));
                $out=sprintf("\$base_dir=\"%s\";\r\n",$_POST['nbase_dir']);
				fwrite($fp,$out,strlen($out));
                $out=sprintf("\$max_uploads=\"%s\";\r\n",$_POST['nmax_uploads']);
				fwrite($fp,$out,strlen($out));
                $out=sprintf("\$upload_size=\"%s\";\r\n",$_POST['nupload_size']);
				fwrite($fp,$out,strlen($out));
                $out=sprintf("\$user=\"%s\";\r\n",$_POST['nusername']);
				fwrite($fp,$out,strlen($out));
                $out=sprintf("\$pass=\"%s\";\r\n",$_POST['npassword']);
				fwrite($fp,$out,strlen($out));
                $out=sprintf("?>\r\n");
				fwrite($fp,$out,strlen($out));
				fclose($fp); 
				require("globals.php"); 	
	echo '<br>
	<br><center> The main configuration file was updated </center><br>';
	}
	?>
	<br>
      <form name="form1" method="post" action="admin.php">
        <input name="action" type="hidden" id="action" value="main">
        <input name="change" type="hidden" id="change" value="true">
        <table width="80%" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
          <tr>
            <td> 
              <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="e3e3e3">
                <tr> 
                  <td bgcolor="D2D2D2" class="arbo"><div align="center" class="med"><strong>Admin settings</strong> 
                  </div></td>
                </tr>
              </table>
              <table width="100%" border="0" align="center" cellpadding="0" cellspacing="10" class="arbo">
                <tr> 
                  <td width="50%"><strong>Usrname :</strong><br>
                    This usename is for the admin purposes .</td>
                  <td width="50%"> <input name="nusername" type="text" class="arbo" id="nusername" value="<?php echo $user; ?>" size="40" maxlength="200"> 
                  </td>
                </tr>
                <tr> 
                  <td><strong>Password :</strong><br>
                    This password is for the admin account .</td>
                  <td><input name="npassword" type="text" class="arbo" id="npassword" value="<?php echo $pass; ?>" size="40" maxlength="200"> 
                  </td>
                </tr>
              </table></td>
          </tr>
        </table>
        <br>
        <table width="80%" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
          <tr> 
            <td> <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="e3e3e3">
                <tr> 
                  <td bgcolor="D2D2D2" class="arbo"><div align="center" class="med"><strong>Script Settings</strong> 
                  </div></td>
                </tr>
              </table>
              <table width="100%" border="0" align="center" cellpadding="0" cellspacing="10" class="arbo">
                <tr> 
                  <td width="50%"><strong>Base Directory :</strong><br>
                    From where the script will start looking </td>
                  <td width="50%"> <input name="nbase_dir" type="text" class="arbo" id="nbase_dir" value="<?php echo $base_dir; ?>" size="40" maxlength="200"> 
                  </td>
                </tr>
                <tr> 
                  <td width="50%"><strong>Max Uploads :</strong><br>
                    The maximum files which can be uploaded at a time ..</td>
                  <td width="50%"> <input name="nmax_uploads" type="text" class="arbo" id="nmax_uploads" value="<?php echo $max_uploads; ?>" size="40" maxlength="200"> 
                  </td>
                </tr>
                <tr> 
                  <td><strong>Upload File size :</strong><br>
                    The maximum size of file which can be uploaded ( <strong>Size 
                    in Bytes</strong> ) .</td>
                  <td><input name="nupload_size" type="text" class="arbo" id="nupload_size" value="<?php echo $upload_size; ?>" size="40" maxlength="200"> 
                  </td>
                </tr>
              </table></td>
          </tr>
        </table>
        <div align="center"><br>
          <input name="Submit" type="submit" class="arbo" value="Make Changes">
        </div>
      </form>
      <div align="center"><font color="#999999"><strong>All these configuration 
        settings will be saved in globals.php</strong></font><br>
    </div></td>
  </tr>
</table>


