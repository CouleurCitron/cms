<link href="/backoffice/cms/css/bo.css" rel="stylesheet" type="text/css">
        <tr bgcolor="<?php echo $color; ?>"> 
  <td width="4%" align="center"><img src="./images/folder.gif"></td>
   <td width="56%" ><a class="arbo" href="<?php 
     $path=$rel_dir."/".$dir_list[$i];
	echo "admin.php?action=browse&rel_dir=".$path;
	?>">/<?php 
	echo $dir_list[$i];
	?>
    </a> </td>

    <td><center>&nbsp;</center></td> 
    <td nowrap class="arbo"><center>0 Octets</center></td>
    <td class="arbo"><center>---</center></td>
    <td class="arbo"><center>---</center></td>
  <td width="22%" class="arbo"><div align="center"><a href="javascript:dir_del('<?php echo $path; ?>','<?php echo $rel_dir; ?>');" class="arbo">Supprimer</a></div></td>
</tr>