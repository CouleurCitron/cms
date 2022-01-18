<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
$rel_dir = $_GET['rel_dir'];
$dir_del = $_GET['dir_del'];
$dir = $_GET['dir'];
$del = $_GET['del'];
$file = $_GET['file'];

echo "<font color=\"#ff0000\">";
if( strlen($file)>0)
{
$file=$base_dir.$rel_dir."/".$file;
if(touch($file))
{
echo "<br /><BR><center><span class=\"arbo\">Le fichier ".$file." a été créé .</span></center><br><Br>";
}else
{
echo "<br /><BR><center><span class=\"arbo\">Impossible de créer le fichier ".$file."</span></center><br><Br>";
}
}

if( isset($dir_del))
{
$d_dir=$base_dir.$dir_del;
//if(rmdir($d_dir))
if(killDir($d_dir))
{
echo "<br /><BR><center><span class=\"arbo\">Le dossier ".$d_dir." et tout son contenu ont été supprimés .</span></center><br><Br>";
}else
{
echo "<br /><BR><center><span class=\"arbo\">Erreur lors de la suppression du dossier ".$d_dir."</span></center><br><Br>";
}
}


if( isset($dir))
{
$dir=$base_dir.$rel_dir."/".$dir;
if(mkdir($dir))
{
echo "<br /><BR><center><span class=\"arbo\">Le dossier ".$dir." a été créé .</span></center><br><Br>";
}else
{
echo "<br /><BR><center><span class=\"arbo\">Impossible de créer le dossier ".$dir."</span></center><br><Br>";
}

}
if( isset($del))
{
if(unlink ($del))
{
echo "<br /><BR><center><span class=\"arbo\">Le fichier ".$del." a été supprimé .</span></center><br><Br>";
}else
{
echo "<br /><BR><center><span class=\"arbo\">Impossible de supprimer le fichier ".$del."</span></center><br><Br>";
}

}
echo "</font>";
if ( !isset($rel_dir) ) 
{
$show_dir=$base_dir."/";
$rel_dir="";
}
else if ( $rel_dir == $base_dir)
{
$show_dir=$base_dir."/";
$rel_dir="";
}
else 
{
$rel_dir=urldecode($rel_dir);
$show_dir=$base_dir.$rel_dir."/";
}
$dir_list=array();
$file_list=array();
$dir_count=0;
$file_count=0;

if (!is_dir($show_dir)) 
	{
	echo $show_dir."<span class=\"arbo\"> n'est pas un répertoire !!!</span>";
	return;
	}
if (!( $dir = opendir($show_dir) ) )
	{
	echo $show_dir."<span class=\"arbo\"> Accès refusé. <br>Privilèges insuffisants pour visualiser ce répertoire.</span>";
	return;
	}
	while ( $name = readdir($dir))
	{
	if($name == ".." || $name == "." ) continue;
	if(  is_dir($show_dir.$name)  )$dir_list[$dir_count++] = $name ;
	if(  is_file($show_dir.$name) ) $file_list[$file_count++] = $name ;
	} 
    closedir($dir);
?>
<table class="arbo" id="dossierCourantFileManager">
  <tr> 
    <td>
<div align="left"><strong><img src="./images/up.gif" width="10" height="11"> Dossier parent : <a class="arbo" href="<?php 			
	        $p_ar=explode("/",$rel_dir,strlen($rel_dir));
			$prev_dir="";
			for($i=0;$i<count($p_ar)-1;$i++)
			{
			$prev_dir=$prev_dir.$p_ar[$i];
			if($i!=count($p_ar)-2)$prev_dir=$prev_dir."/";
			}
			if( $prev_dir =="" ){
			echo "admin.php?action=browse";
			$prev_dir="/";
			}
			else { echo "admin.php?action=browse&rel_dir=".$prev_dir; }
?>"> 
        <?php 
//			$prev_dir=substr($prev_dir,0,strlen($prev_dir)-strlen($prev_dir)-1);
			echo $prev_dir; 
			?>
        </a></strong></div></td>
    <td>
<div align="right"><strong>[<a class="arbo" href="javascript:new_file('<?php echo $rel_dir;?>');" >Ajout Fichier</a>] 
[<a href="javascript:new_dir('<?php echo $rel_dir;?>');" class="arbo">Ajout dossier</a>] 
[<a href="javascript:upload('<?php echo $rel_dir;?>');" class="arbo">Upload simple</a>] 
<?php
if (isset($FM_ftpUser)){
?>
[<a href="javascript:zupload('<?php echo $rel_dir;?>');" class="arbo">Upload FTP</a>] 
<?php
}
?>
[<a href="javascript:refresh_page('<?php echo $rel_dir;?>');" class="arbo">Rafraichir</a>] </strong></div></td>
  </tr>
</table>

<?php 
	$curr_path = $rel_dir;
	if(strlen($curr_path)==0) $curr_path='Racine';
?>
<div class="arbo" id="dossierFileManager">
        <p class="first">Dossier courant &nbsp;&nbsp;: <?php echo $curr_path; ?></p>
        <p class="second">Total dossiers <?php echo $dir_count; ?> </p>
        <p class="third last">Total fichiers <?php echo $file_count; ?></p>
  </div>

<div class="arbo" id="dossierCourantFileManager">
   <p><?php include_once ("results.php"); ?></p>
  
    <p>Dossiers : - </p>
</div>

<table class="arbo" id="listDossierFileManager">
        <tr bgcolor="#efefef"> 
          <td width="30" bgcolor="#efefef"> 
            <center>
          </center></td>
          <td width="40%"> <strong>&nbsp;Nom</strong></td>
          <td width="20%" bgcolor="#efefef"> 
            <center>
              <strong>Dernière modification</strong> 
          </center></td>
          <td width="10%" bgcolor="#efefef"> 
            <center>
              <strong>0 Octets</strong>
</center></td>
          <td width="10%" bgcolor="#efefef"> 
            <center>
              <strong>Editer</strong>
            </center></td>
          <td width="10%" bgcolor="#efefef"> 
            <center>
              <strong>Visualiser</strong>
            </center></td>
          <td width="10%" bgcolor="#efefef"> 
            <center>
              <strong>Supprimer</strong>
</center></td>
        </tr>
        <?php sort($dir_list);
$cz=0;

	for($i=0;$i<$dir_count;$i++)
	{
if($cz%2==0){$color="#dee3e7";}
   else { $color="#f7f7f7";}	
include("dbar.php");
$cz++;
	}
sort($file_list);


if ($file_count > $maxCount) {
	
	for($i=$debut;$i<$fin;$i++){
		if($cz%2==0){$color="#dee3e7";}
		   else { $color="#f7f7f7";}	
		include("fbar.php");
		$cz++;
	}

}
else {
	for($i=0;$i<$file_count;$i++)
	{
		if($cz%2==0){$color="#dee3e7";}
		   else { $color="#f7f7f7";}	
		include("fbar.php");
		$cz++;
	}
}
	?>
    </table>
