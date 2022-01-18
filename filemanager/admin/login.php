<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 
if(isset($_POST['login']))
{
if($_POST['password']==$pass && $_POST['username'] == $user )
	{
	$_SESSION['adminname']=$username;
	$_SESSION['relative_dir']="";
	$_SESSION['admin']="yes";
	}
$ret="Location : admin.php";
//if(isset($QUERY_STRING))$ret=sprintf("%sindex.php?%s",$ret,$QUERY_STRING);
header($ret);
}
?>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="1">
  <tr> 
    <td> 
      <form name="form1" method="post" action="admin.php">
        <input name="login" type="hidden" id="login" value="true">
        <input name="action" type="hidden" id="action" value="login">
        <table width="300" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
          <tr>
            <td><table width="100%" border="0" align="center" cellpadding="5" cellspacing="0" class="arbo">
                <tr> 
                  <td>&nbsp;</td>
                </tr>
				<tr> 
                  <td width="300"> 
                    <div align="center"><a href="http://www.ngcoders.com"><img src="admin/images/ezfm.gif" border="0"></a></div></td>
                </tr>
				<tr> 
                  <td>&nbsp;</td>
                </tr>
				<tr> 
                  <td>Please login with user name and password </td>
                </tr>
                <tr> 
                  <td>&nbsp;</td>
                </tr>
                <tr> 
                  <td><table width="95%" border="0" align="center" cellpadding="0" cellspacing="1" class="arbo">
                      <tr> 
                        <td width="45%">Username :</td>
                        <td width="55%"><input name="username" type="text" class="arbo" id="username" maxlength="15"></td>
                      </tr>
                      <tr> 
                        <td>Password :</td>
                        <td><input name="password" type="password" class="arbo" id="password" maxlength="15"></td>
                      </tr>
                    </table></td>
                </tr>
                <tr> 
                  <td>&nbsp;</td>
                </tr>
                <tr> 
                  <td><div align="center"> 
                      <input name="Submit" type="submit" class="arbo" value="Submit">
                    </div></td>
                </tr>
                <tr> 
                  <td>&nbsp;</td>
                </tr>
              </table></td>
          </tr>
        </table>
      </form>
      
    </td>
  </tr>
</table>
