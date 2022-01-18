<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

if (getenv(HTTP_CLIENT_IP))
{
	$ip=getenv(HTTP_CLIENT_IP);

}
else
{
	$ip=getenv(REMOTE_ADDR);
}

$host=getenv(HTTP_HOST);

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<META HTTP-EQUIV="Expires" CONTENT="0">
<title>Test de Bande Passante</title>
<script language="JavaScript">
function DocumentLoaded() {
	window.document.speedani.src = "speedanimoff.gif";
}

</script>
</head>
<body bgcolor="#FFFFFF" text="#000066" link="#000066" vlink="#000099" alink="#000099" onLoad="DocumentLoaded()">

<font face="Arial" size="4" color="#000066"><B>Test de Bande Passante</B></font><BR><BR>
<table border="0">
  <tr>
     <td><font face="Arial" size="2" color="#000066">
     Ce test mesure le d&eacute;bit descendant entre <b>
<?php

print($host);

?>
	</b><BR>et votre terminal <b>
<?php

print(gethostbyaddr($ip)." </b>(".$ip.")");

?>
</font>

     </td>
  </tr>
  <tr>
     <td valign="top"><font face="Arial" size="2" color="#FF0000">
        <br><img src="speedanim.gif" alt="Speed Test" name="speedani" border="0">
     </td>
  </tr>
</table>


<?php
include("speedtest4.inc");
displayTestResult();
?>


<BR>
<font face="Arial" size="2" color="#000066"><B>
<UL>
<LI><a href="speedtest4.php">Effectuer un nouveau test</a>
</UL>
</B></font>

</body>
</html>
