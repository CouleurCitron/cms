<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

/*

************************************************************************

 Fichier:			-	speedtest4.inc
 Auteur:			-	Gilles Lacarriere
 Date:				-	02.10.2001
 Concept Original:	-	Allen Marsalis, Michael L Smith
 
 Description:		-	Test de bande passante en PHP
 
 Principales differences avec le speedtest.inc de Michael L Smith :
 
 -> Reecriture complete de testbw()
 -> Pre-Test en 128Ko (seul test pour les petits debits)
 -> Second Pre-Test avec 128Ko a 512Ko
 -> puis test avec 512Ko a 4096Ko selon la bande passante
 -> Conversion et affichage en Ko/sec
 
 Ce programme est sous licence GNU GPL, v2.0 ou ulterieure
 http://www.gnu.org/copyleft/gpl.html
 
 (C) 2001, Gilles Lacarriere
           http://speedtest.nomai.com

************************************************************************

*/

function testbw($multiplier, $koofdata)
{
	$t1=microtime();
	for ($s=1; $s<=$multiplier; $s++)
	{
		echo "<!--";
		include("./".$koofdata."K.inc");
		echo "-->";
	}
	$t2=microtime();
	$t1=((double)strstr($t1, ' ')+(double)substr($t1,0,strpos($t1,' ')));
	$t2=((double)strstr($t2, ' ')+(double)substr($t2,0,strpos($t2,' ')));
	$kbps = $koofdata*8*$multiplier/($t2 - $t1);
	return(str_pad(round($kbps, 3),5, "0", STR_PAD_RIGHT));
}

function displayTestResult()
{

	print <<<END

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR>
		<TD HEIGHT="1" BGCOLOR="#000000" colspan="3"></TD>
	</TR>
	<TR>
		<TD WIDTH="1" BGCOLOR="#000000"></TD>
		<TD><table border='0' cellpadding='2' cellspacing='0'>

END;


	$results = testbw(1,128);

	if ($results > 3072) $results = testbw(4,128);
	elseif ($results > 1536) $results = testbw(3,128);
	elseif ($results > 768) $results = testbw(2,128);
	elseif ($results > 384) $results = testbw(1,128);

	if ($results > 3072) $results = testbw(4,512);
	elseif ($results > 1536) $results = testbw(3,512);
	elseif ($results > 768) $results = testbw(2,512);
	elseif ($results > 384) $results = testbw(1,512);
	elseif ($results > 128) $results = testbw(1,128);

	include("speedtest4.conf");

	$max_value = max($speed_array);
	$kbitPerPixel = $max_value/$max_width;
	$count = count($speed_array);
	for ($i=0; $i<$count; $i++)
	{
		$pixels = ceil($speed_array[$i]/$kbitPerPixel);
		if ($alt_array[$i] == $you_match)
		{
			$image_use = $you_image;
		}
		else
		{
			$image_use = $image;
		}
		$conversion = round($speed_array[$i]/8, 3);
		echo "<tr valign='".$tr_align."' bgcolor='".$tr_bgcolor."'><TD>&nbsp;</td><td align='right'><font face='Arial' size='1' color='#000000'>".$media_array[$i]."</font>&nbsp;</td><td valign='middle'><IMG SRC='".$image_use."' Height='".$bar_height."' width='".$pixels."' alt=\"".$alt_array[$i]." ".$speed_array[$i]." Kbps (".$conversion." Ko/sec)\">&nbsp;<B><font face='Arial' size='1' color='#000000'>".$speed_array[$i]."&nbsp;Kbps  (".$conversion." Ko/sec)</font></b></td></tr>\n";
	}
	
	print <<<END

			</table>
		</TD>
		<TD WIDTH="1" BGCOLOR="#000000"></TD>
	</TR>
	<TR><TD HEIGHT="1" BGCOLOR="#000000" colspan="3"></TD></TR>
	<TR><TD HEIGHT="3" BGCOLOR="#FFFFFF" colspan="3"></TD></TR>
	<TR><TD HEIGHT="1" BGCOLOR="#BBBBBB" colspan="3"></TD></TR>
	<TR>
		<TD WIDTH="1" BGCOLOR="#BBBBBB"></TD>
		<TD BGCOLOR="#EEEEEE" align="center"><font face="Arial" size="1" color="#333388"><a href="http://speedtest.nomai.com" target="_blank" style="text-decoration: none;"><B>SpeedTest4.PHP (v4.2fr) de Gilles Lacarri&egrave;re - Concept Original: Allen Marsalis, Michael L Smith</b></a></font></TD>
		<TD WIDTH="1" BGCOLOR="#BBBBBB"></TD>
	</TR>
	<TR><TD HEIGHT="1" BGCOLOR="#BBBBBB" colspan="3"></TD></TR>
</TABLE>

END;

}


?>
