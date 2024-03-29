<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/**
 * Logiciel : exemple d'utilisation de HTML2PDF
 * 
 * Convertisseur HTML => PDF, utilise fpdf de Olivier PLATHEY 
 * Distribu� sous la licence GPL. 
 *
 * @author		Laurent MINGUET <webmaster@spipu.net>
 */

 	ob_start();
?>
<page style="font-size: 10pt">
	<span style="font-size: 16pt ; font-weight: bold">D�monstration des images<br></span>
	<br>
	<br>
	<b>Dans un tableau :</b><br>
	<table style="width: 50%;border: solid 3px #5544DD">
		<tr>
			<td style="width: 30%; text-align: left; ">Text � gauche<br>avec retour �<br>la ligne</td>
			<td style="width: 40%; text-align: center;"><img src="../_fpdf/tutoriel/logo.png" alt="" ><br><i>l�gende</i></td>
			<td style="width: 30%; text-align: right; ">Texte � droite</td>
		</tr>
	</table>
	<br>
	<b>Dans un texte :</b><br>
	texte � la suite d'une image, <img src="../_fpdf/tutoriel/logo.png" alt="" style="height: 10mm">
	texte � la suite d'une image, r�p�titif car besoin d'un retour � la ligne
	texte � la suite d'une image, r�p�titif car besoin d'un retour � la ligne
	texte � la suite d'une image, r�p�titif car besoin d'un retour � la ligne
	texte � la suite d'une image, r�p�titif car besoin d'un retour � la ligne<br>
	<br>
	<br>
	Test diff�rentes tailles texte
	<span style="font-size: 18pt;">Test Size</span>
	<span style="font-size: 16pt;">Test Size</span>
	<span style="font-size: 14pt;">Test Size</span>
	<span style="font-size: 12pt;">Test Size</span>
	Test diff�rentes tailles texte, r�p�titif car besoin d'un retour � la ligne
	Test diff�rentes tailles texte, r�p�titif car besoin d'un retour � la ligne
	Test diff�rentes tailles texte, r�p�titif car besoin d'un retour � la ligne
	Test diff�rentes tailles texte, r�p�titif car besoin d'un retour � la ligne
	<br>
	<br>
</page>
<?php
	$content = ob_get_clean();
	require_once(dirname(__FILE__).'/../html2pdf.class.php');
	$pdf = new HTML2PDF('P','A4');
	$pdf->WriteHTML($content, isset($_GET['vuehtml']));
	$pdf->Output();
?>