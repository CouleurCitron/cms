<link rel="stylesheet" href="/backoffice/cms/filemanager/admin/style.css" type="text/css" />
<link href="/backoffice/cms/filemanager/style.css" rel="stylesheet" type="text/css" />
<script type="text/JavaScript">
         function editWin(link_open,rel_path) 
		 {
		 document.location="admin.php?action=edit&rel_dir="+rel_path+"&edit="+link_open;
		 //var win1=window.open(build_link,"","");
		 //win1.focus();
		 }
         function upload(rel_path) 
		 {
		 document.location="admin.php?action=upload&rel_dir="+rel_path;
		 //var win1=window.open(build_link,"","");
		 //win1.focus();
		 }
         function zupload(rel_path) 
		 {
		 document.location="admin.php?action=zupload&rel_dir="+rel_path;
		 //var win1=window.open(build_link,"","");
		 //win1.focus();
		 }
         function viewWin(link_open,rel_path) 
		 {
		 document.location="admin.php?action=view&rel_dir="+rel_path+"&view="+link_open;
		 //var win2=window.open(build_link,"","");
		 //win2.focus();
		 }
         function openWin( info) { 
      			 window.alert(info);
         } 

		function confirm_del(link_del,rel_path)
		{
		if( confirm ("Etes vous sur de vouloir supprimer le fichier "+ link_del))
		 {
		 window.location="admin.php?action=browse&rel_dir="+rel_path+"&del="+link_del;
		 }
		}
		function dir_del(dir_del,rel_path)
		{
		if( confirm ("Etes vous sur de vouloir supprimer le dossier "+ dir_del))
		 {
		 window.location="admin.php?action=browse&rel_dir="+rel_path+"&dir_del="+dir_del;
		 }
		}
		function  new_file(rel_path)
		{
		var a= prompt("Saisissez le nom du fichier à créer :",""); 
		 
		 if( a!="" && a!=null )
		 {
		 window.location="admin.php?action=browse&rel_dir="+rel_path+"&file="+a;
		 }
		 
		}
		function new_dir(rel_path)
		{
		var a= prompt("Saisissez le nom du dossier à créer :","");
		 if( a!="" && a!=null )
		 {
		 window.location="admin.php?action=browse&rel_dir="+rel_path+"&dir="+a;
		 }
		}
		function refresh_page(rel_path)
		{
		 window.location="admin.php?action=browse&rel_dir="+rel_path;
		}

</script>
<table width="100%" border="0" cellpadding="0" cellspacing="1">
  <tr> 
    <td> <div align="left"> 
        <hr size="1" class="arbo2">
      </div></td>
  </tr>
  <tr> 
    <td><strong class="arbo">
<?php /*[ <a href="admin.php?action=main">Configuration</a> ]*/?> [ <a href="admin.php?action=browse">Parcourir</a> <?php /*
      ] [ <a href="admin.php?action=vote">Vote</a> ] [ <a href="admin.php?action=help">Help</a> 
      ] [ <a href="admin.php?action=licence">Licence</a> ] [ <a href="admin.php?action=logout">Logout</a> */ ?>
      ]</strong></td>
  </tr>
  <tr> 
    <td><hr size="1" class="arbo2"></td>
  </tr>
</table>
