<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 
$rel_dir = $_GET['rel_dir'];
$rel_dir = preg_replace("/\/(.+)/msi", "$1", $rel_dir);
//if(!isset($_SESSION['adminname']))return;
?>
<link href="/backoffice/cms/filemanager/admin/style.css" rel="stylesheet" type="text/css">
<!--<script type="text/javascript" src="/backoffice/cms/filemanager/admin/ZUpload/js.inc.php?rel_dir=<?php echo $rel_dir; ?>"></script>-->


<script type="text/javascript">  
        /** 
         * check to see if the JVM supports the file uploader... 
         */  
        function isValidJVM()  
        {  
            /*
			var checker = document.getElementById("checker");  
			testJVM =  (checker.getVersion != null &&  
                checker.getVendor != null &&  
                checker.getVendor().indexOf("Sun") > -1 &&  
                parseFloat(checker.getVersion().substr(0,3)) >= 1.5) ;
            return testJVM;
		*/
			return true;
        }  
      
        /** 
         * check to see if the file has been uploaded... 
         */  
        function checkFileUploaded()  
        {  
            var uploader = document.getElementById("uploader");  
            if (!uploader.hasUploaded())  
            {  
                alert("Please upload the file before submitting the form");  
                return false;  
            }  
            else  
            {  
				document.getElementById('uploaded').value += "/" + uploader.hasUploaded();
				//alert("le fichier "+document.getElementById('uploaded').value+" a bien été envoyé");
              	document.getElementById('uploadForm').submit();
            }  
        }  
    </script>  
	
	<applet code="/backoffice/cms/lib/uploader/Checker.class" width="0" height="0" id="checker"></applet>  
	    <form action="/backoffice/cms/filemanager/admin.php" id="uploadForm" name="uploadForm" method="get" onsubmit="return checkFileUploaded();">  
	    <div id="error">  
        Please download and enable the latest java runtime environment from www.java.com  
    </div>  
	<script type="text/javascript" src="/backoffice/cms/filemanager/admin/upload.js.php"></script>
    <script type="text/javascript">  
		/* 
        if (isValidJVM())  
        {  
			document.open();  
            document.write('<applet code="Uploader.class" archive="/backoffice/cms/lib/uploader/Uploader.jar,/backoffice/cms/lib/uploader/Net.jar" width="430" height="29" id="uploader">');  
            document.write('    <param name="address" value="cogemip.couleur-citron.com" />');  
            document.write('    <param name="username" value="ftpcogemipFM" />');  
            document.write('    <param name="password" value="M/z0eC$O32" />');  
            document.write('    <param name="uploadedFileName" value="/test.upload" />');  
            document.write('    <param name="backgroundColor" value="#FFFFFF" />');  
            document.write('    <param name="foregroundColor" value="#000000" />');  
            document.write('</applet>');  
			document.close();  
            document.getElementById("error").style.display = "none";  
        }  
		*/
    </script>  
	<input type="hidden" id="uploaded" name="uploaded" value="<?php echo $_GET['rel_dir']; ?>" />
	<input type="hidden" id="rel_dir" name="rel_dir" value="<?php echo $_GET['rel_dir']; ?>" />
	<br />

	<input type="submit" name="OK" value="Valider l'upload " /><br />
	(cliquer lorsque le champ ci-dessus indique "upload complete")
	
	</form>