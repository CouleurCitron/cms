<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 
//if(!isset($_SESSION['adminname']))return;
$rel_dir = $_GET['rel_dir'];
$view = str_replace("//","/",$_GET['view']);
?>
<style type="text/css">
<!--
.Style1 {color: #006699}
-->
</style>
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="1">
  <tr>
    <td><table width="100%" height="100%" border="0" cellpadding="0" cellspacing="1">
        <tr valign="top">
          <td width="90%"><br> 
            <table width="90%" border="0" align="center" cellpadding="0" cellspacing="1">
              <tr> 
                <td width="90%"> <table width="100%" border="0" cellpadding="5" cellspacing="1">
                    <tr> 
                      <td><?php
					  $path=$view;
		   $fsize = filesize($path) ; 
		   $fmodified = date("d/M/y G:i:s", filemtime($path)) ;
     	   $faccessed = date("d/M/y G:i:s", fileatime($path)) ;

			?>
                        <table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
                          <tr> 
                            <td><strong>File </strong></td>
                            <td><strong><font color="#006699"><?php echo $path; ?>&nbsp;</font></strong></td>
                          </tr>
                          <tr> 
                            <td><strong>File size </strong></td>
                            <td><strong><?php echo $fsize ?> <span class="Style1">Bytes</span></strong></td>
                          </tr>
                          <tr> 
                            <td><strong>Last modified </strong></td>
                            <td><strong><font color="#006699"><?php echo $fmodified; ?></font></strong></td>
                          </tr>
                          <tr> 
                            <td><strong>Last accessed &nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
                            <td><strong><font color="#006699"><?php echo $faccessed; ?></font></strong></td>
                          </tr>
                          <tr> 
                            <td><strong>Owner </strong></td>
                            <td><strong><font color="#006699"><?php echo fileowner($path); ?>&nbsp;</font></strong></td>
                          </tr>
                          <tr> 
                            <td><strong>Group </strong></td>
                            <td><strong><font color="#006699"><?php echo filegroup($path); ?>&nbsp;</font></strong></td>
                          </tr>
                          <tr> 
                            <td><strong>Permission </strong></td>
                            <td><strong><font color="#006699"><?php printf( "%o", fileperms($path) ) ; ?></font></strong></td>
                          </tr>
                        </table>
                        
                      </td>
                    </tr>
                  </table></td>
              </tr>
            </table> 
            <br>
            <table width="90%" border="0" align="center" cellpadding="0" cellspacing="1">
              <tr> 
                <td width="90%"> <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="f7f7f7">
                    <tr> 
                      <td><?php
					   $ext=array();
                	   $ext=explode(".",$view,strlen($view));
  				       $extn=$ext[count($ext)-1];
					  if(isimage($extn))
					  {
					  $iurl=str_replace($_SERVER['DOCUMENT_ROOT'],"",$view);
					  $iurl=str_replace("//","/",$iurl);
					  echo '<center><br><image src="'.$iurl.'"> </center><br><BR>';
					  }
					  else {
					  $line_num=0;
					  $fp=fopen($view,"r");
					  while ( ! feof ($fp ))
						{
						$line_num_print=sprintf("&nbsp;<font color=\"#666699\"><strong>%04d</strong></font>&nbsp;&nbsp;",$line_num);
						echo $line_num_print;
						$line= fgets($fp,1024); 
						$line=str_replace(">","&gt;",$line);
						$line=str_replace("<","&lt;",$line);
						$line=str_replace("\n","<br>",$line);
						print($line);
						
                        $line_num++;
					  	}
					  fclose($fp);
					  }
					  
					  ?>
                      </td>
                    </tr>
                  </table></td>
              </tr>
            </table> 
            <br>
          </td>
        </tr>
      </table></td>
  </tr>
</table>
