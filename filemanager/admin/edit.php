<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 
$rel_dir=$_GET['rel_dir'];
$edit=$_GET['edit'];
//if(!isset($_SESSION['adminname']))return;
?>
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="336699">
  <tr>
    <td><table width="100%" height="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="f7f7f7">
        <tr valign="top">
          <td width="90%"><br> 
            <table width="90%" border="0" align="center" cellpadding="0" cellspacing="1">
              <tr> 
                <td width="90%"> <table width="100%" border="0" cellpadding="5" cellspacing="1">
                    <tr> 
                      <td>
                        <?php
					  if(isset($save))
					  {
					  $path=$filename;
					  }else  $path=$edit;
		
		
					  if ( isset($save))
					  {
					  if($fp=fopen($path,"w"))
					  {
					  fwrite($fp,$filecontent,strlen($filecontent));
					  fclose($fp);
  					  }else $save="false";
					  }

 				   $fsize = filesize($path) ; 
				   $fmodified = date("d/M/y G:i:s", filemtime($path)) ;
		     	   $faccessed = date("d/M/y G:i:s", fileatime($path)) ;

					  ?>
                        <table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF" class="arbo">
                          <tr> 
                            <td><strong>Fichier </strong></td>
                            <td><strong><font color="#006699"><?php echo $path; ?></font></strong></td>
                          </tr>
                          <tr> 
                            <td><strong>Taille </strong></td>
                            <td><strong><font color="#006699"><?php echo $fsize ?> 
                              Bytes</font></strong></td>
                          </tr>
                          <tr> 
                            <td><strong>Dernière modification </strong></td>
                            <td><strong><font color="#006699"><?php echo $fmodified; ?></font></strong></td>
                          </tr>
                          <tr> 
                            <td><strong>Dernier accès &nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
                            <td><strong><font color="#006699"><?php echo $faccessed; ?></font></strong></td>
                          </tr>
                          <tr> 
                            <td><strong>Propriétaire </strong></td>
                            <td><strong><font color="#006699"><?php echo fileowner($path); ?></font></strong></td>
                          </tr>
                          <tr> 
                            <td><strong>Groupe </strong></td>
                            <td><strong><font color="#006699"><?php echo filegroup($path); ?></font></strong></td>
                          </tr>
                          <tr> 
                            <td><strong>Permissions </strong></td>
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
                <td width="90%"> <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="1">
                    <tr> 
                      <td class="arbo"> 
                        <?php
					  if ( isset($save))
					  {
					  $fp=fopen($path,"w");
					  {
					  fwrite($fp,$filecontent,strlen($filecontent));
					  if(isset($save))
					  {
					  if($save==true)echo "<br /><br><center>".$path." was saved successfuly </center><br><br>";
					  else echo "<br /><br><center>".$path." Error saveing !!!</center><br><br>";
					  }
					  
					  }
					  fclose($fp);
					  }
					  ?>
                        <form name="form" method="post" action="admin.php">
                          <div align="center"><strong>Save File as :</strong> 
                            <input name="filename" type="text" class="arbo" id="filename" value="<?php echo $path; ?>" size="70">
                            <input name="Submit" type="submit" class="arbo" value="Save">
                            <br>
                            <input name="action" type="hidden" id="action" value="edit">
                            <input name="edit" type="hidden" id="edit" value="<?php echo $path; ?>">
                            <input name="save" type="hidden" id="save" value="true">
                            <br>
                            <textarea name="filecontent" cols="75" rows="30" class="arbo" id="filecontent"><?php
					  $line_num=0;
					  $fp=fopen($edit,"r");
					  while ( ! feof ($fp ))
						{
				//		$line_num_print=sprintf("&nbsp;<font color=\"#666699\"><strong>%04d</strong></font>&nbsp;&nbsp;",$line_num);
			//			echo $line_num_print;
						$line= fgets($fp,1024); 
//						$line=str_replace(">","&gt;",$line);
	//					$line=str_replace("<","&lt;",$line);
		//				$line=str_replace("\n","<br>",$line);
						print($line);
						
                        $line_num++;
					  	}
					  fclose($fp);
					  ?></textarea>
                          </div>
                      </form> </td>
                    </tr>
                  </table></td>
              </tr>
            </table> 
          </td>
        </tr>
      </table></td>
  </tr>
</table>
