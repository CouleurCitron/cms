<script src="/backoffice/cms/js/openBrWindow.js" type="text/javascript"></script>

        <tr bgcolor="<?php echo $color; ?>"> 

          <?php

 $path = $base_dir.$rel_dir."/".$file_list[$i];

	    $modified  = filemtime($path) ;

	    $size   = filesize($path) ;

		if($size >= 1024*1024 )

		 {

 		 $size=vsprintf("%1.2f Mo", $size/(1024*1024) );

		 }

		 else if ($size >= 1024  )

		  {

		 $size=vsprintf("%1.2f Ko",$size/1024 );

		  }

		  else 

		  {

		  $size = $size . " Octets";

		  }

?>

          

  <td width="60" align="center"><a class="arbo" href="javascript:openWin('<?php

	        

			$path = $base_dir.$rel_dir."/".$file_list[$i];

	        echo "\\n- Chemin :".$path;



		   $fsize = filesize($path) ; 

		   $fmodified = date("d/M/y G:i:s", filemtime($path)) ;

     	   $faccessed = date("d/M/y G:i:s", fileatime($path)) ;

	  echo "\\n\\n- Taille: " . $fsize . " Octets" ;

	  echo "\\n- Dernière modif.: " . $fmodified  ;

	  echo "\\n- Dernier accès: " . $faccessed  ;

	  echo "\\n- Propriétaire: " . fileowner($path)  ;

	  echo "\\n- Groupe: " . filegroup($path)  ;

	  echo "\\n- Permissions  : " ; 

	  echo printf( "%o", fileperms($path) ) ;

			

//			echo "./info.php?file=".$path;

				?>');"><img src="./images/<?php

		 $ext=array();



		 $ext=explode(".",$file_list[$i],strlen($file_list[$i]));

		 $extn=$ext[count($ext)-1];

		 switch (($extn) )

		 {

		 case "css": 

		 echo "css.gif";

		 break;



		 case "csv": 

		 echo "csv.gif";

		 break;

		 

		 case "fla": 

		 case "swf": 

		 echo "fla.gif";

		 break;

		 

		 



		 case "mp3": 

		 case "wav": 

		 echo "mp3.gif";

		 break;



		 case "jpg": 

		 case "gif": 

		 case "png": 

		 echo "jpg.gif";

		 break;



		 case "bmp": 

		 case "dib": 

		 echo "bmp.gif";

		 break;





		 case "txt": 

		 case "log": 

		 echo "txt.gif";

		 break;



		 case "pdf": 

		 echo "pdf.gif";

		 break;



		 case "zip": 

		 case "rar": 

		 case "tgz": 

		 case "gz": 

		 echo "zip.gif";

		 break;



		 case "doc": 

		 case "rtf": 

		 echo "doc.gif";

		 break;



		 case "asp": 

		 case "jsp": 

		 echo "asp.gif";

		 break;

		 

		 



		 case "php": 

		 case "php3": 

		 echo "php.gif";

		 break;



		 case "htm":

		 case "html":

		 echo "htm.gif";

		 break;



         case "ppt":

		 echo "ppt.gif";

		 break;



      

		 case "exe":

		 case "bat":

		 case "com":

		 echo "exe.gif";

		 break;



		 case "wmv":

		 case "mpg":

		 case "mpeg":

		 case "wma":

		 case "asf":

		 echo "wmv.gif";

		 break;



		 case "midi":

		 case "mid":

		 echo "midi.gif";

		 break;



		 case "mov":

		 echo "mov.gif";

		 break;



		 case "psd":

		 echo "psd.gif";

		 break;



		 case "ram":

		 case "rm":

		 echo "rm.gif";

		 break;



		 case "xml":

		 echo "xml.gif";

		 break;



		 default :

		 echo "ukn.gif";

		 break;

		 

		 

		 }		  

		  ?>" border="0"></a>   
  </td> 
  <td width="40%">&nbsp;<a href="/backoffice/cms/utils/telecharger.php?file=<?php echo $file_list[$i]; ?>&chemin=<?php echo $base_dir.$rel_dir."/"; ?>" title="Télécharger"><img src="/backoffice/cms/img/telecharger.gif" border="0"></a>&nbsp;<a href="/backoffice/cms/utils/telecharger.php?file=<?php echo $file_list[$i]; ?>&chemin=<?php echo $base_dir.$rel_dir."/"; ?>" title="Télécharger"><?php echo $file_list[$i];?></a>
  </td>

  <td width="20%"><?php echo date("d/M/y G:i:s",$modified) ?></td>

  <td width="10%"> <?php echo $size; ?></td>

          <td width="10%"> 

            <div align="center"><?php

			if( iseditable($extn) )

			{

//            $path = $base_dir.$rel_dir."/".$file_list[$i];

			echo '<a href="javascript:editWin(\''.$path.'\',\''.$rel_dir.'\');" class="arbo">Editer</a>';

			}

						else echo"---";

			?> </div></td>

          <td width="10%"> 

            <div align="center"><?php

			if( isviewable($extn) || isimage($extn))

			{

//            $path = $base_dir.$rel_dir."/".$file_list[$i];

			echo '<a href="javascript:viewWin(\''.$path.'\',\''.$rel_dir.'\');" class="arbo">Visualiser</a>';

			}

			else echo"---";

			?> </div></td>

          <td width="10%" bgcolor="<?php echo $color; ?>" class="arbo"> 

          <div align="center"><a href="javascript:confirm_del('<?php echo $path; ?>','<?php echo $rel_dir; ?>');" class="arbo">Supprimer</a></div></td>

        </tr>

