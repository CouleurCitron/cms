<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 
$rel_dir = $_GET['rel_dir'];
//if(!isset($_SESSION['adminname']))return;
?>
<link href="/backoffice/cms/filemanager/admin/style.css" rel="stylesheet" type="text/css">
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
					  $path=$rel_dir;
					  if (strlen($path)==0) $path='/';
					?>
                        <table border="1" cellpadding="5" cellspacing="0" bordercolor="#FFFFFF">
                          <tr class="arbo"> 
                            <td><strong>Répertoire d'upload :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
                            <td><strong><font color="#006699"><?php echo $path; ?></font></strong></td>
                          </tr>
                          <tr class="arbo"> 
                            <td><strong>Nombre maximum de fichiers à uploader :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
                            <td><strong><font color="#006699"><?php echo $max_uploads; ?></font></strong></td>
                          </tr>
                        </table>
                        <br>
						<?php
						if(isset($_GET['upload'])) {
							echo "<br />File Upload Status .. ";
							for($i=0;$i<$max_uploads;$i++) {
								$fupload="uploadfile".$i; 
								  
								//if(isset($HTTP_POST_FILES["$fupload"])) {
								if(isset($_FILES["$fupload"])) {
									/*$fname=$HTTP_POST_FILES["$fupload"]["name"];
									$tname=$HTTP_POST_FILES["$fupload"]["tmp_name"];
									print_r($HTTP_POST_FILES[0]);*/
									$fname=$_FILES["$fupload"]["name"];
									$tname=$_FILES["$fupload"]["tmp_name"];
									print_r($_FILES[0]);
									if($fname=="")continue;
									/*$as=0;$k=array_keys($HTTP_POST_FILES["$fupload"]);
									//						foreach ($k)
															{
									/*						echo $k[0];
															echo $k[1];
															echo $k[2];
															echo $k[3];
															echo $tname;
															$as++;
															}*/
														   // echo "[".$tname."]";                        echo "<br />[".$fname."]<br>";
									echo "<br />$tname => $base_dir$path$fname";
									if(move_uploaded_file($tname, $base_dir.$path."/".$fname))
									{
										echo "<br />".$fname." chargé !!! ";
									} else {
										echo "<br />".$fname." non chargé !!! ";
									}
								}
							}
						}
						?>
                    <br>
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
                      <td><center><strong>
					  <form name="form1" enctype="multipart/form-data" method="post" action="admin.php?action=upload&upload=true&rel_dir=<?php echo $rel_dir; ?>">
                         <input name="MAX_FILE_SIZE" type="hidden" id="MAX_FILE_SIZE" value="<?php echo $upload_size; ?>">
                            <?php
					  for($i=0;$i<$max_uploads;$i++)
					  {
                        echo'<br>Fichier '.$i.' : <input name="uploadfile'.$i.'" type="file" size="40"><BR>';
					  }
					  ?>
                            <br>
                            <input name="Submit" type="submit" class="arbo" value="Upload">
                          </form></strong></center>
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