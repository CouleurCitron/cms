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
<page style="font-size: 14px">
	<span style="font-weight: bold; font-size: 18pt; color: #FF0000">Bonjour, voici quelques exemples<br></span>
	<br>
	Retours � la ligne divers :<br>
	1 : &lt;br&gt;   <br>
	2 : &lt;br &gt;  <br >
	3 : &lt;br/&gt;  <br/>
	4 : &lt;br /&gt; <br />
	<br>
	Barre horizontale &lt;hr&gt;<hr>
	Exemple de lien : <a href="http://html2pdf.spipu.net/" >le site HTML2PDF</a><br>
	<br>
	Image :<br>
	<img src="../_fpdf/tutoriel/logo.png" alt="Logo" width=150 /><br>
	<br>
	test de tableau imbriqu� :<br>
	<table border="1" bordercolor="#000077" bgcolor="#AAAAAA">
		<tr>
			<td border="1">
				<table style="border: solid 1px #FF0000; background: #FFFFFF;">
					<tr>
						<td style="border: solid 1px #007700;">C1</td>
						<td style="border: solid 1px #007700;">C2</td>
					</tr>
					<tr>
						<td style="border: solid 1px #007700">D1</td>
						<td style="border: solid 1px #007700">D2</td>
					</tr>
				</table>
			</td>
			<td border="1">A2</td>
			<td border="1">AAAAAAAA</td>
		</tr>
		<tr>
			<td border="1">B1</td>
			<td border="1" rowspan="2">
				<table style="border: solid 1px #FF0000; background: #FFFFFF;" >
					<tr>
						<td style="border: solid 2px #007700">E1</td>
						<td style="border: solid 2px #007700; padding: 2mm">
							<table style="border: solid 1px #445500">
								<tr>
									<td>
										<img src="../_fpdf/tutoriel/logo.png" alt="Logo" width=100 />
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="border: solid 2px #007700">F1</td>
						<td style="border: solid 2px #007700">F2</td>
					</tr>
				</table>
			</td>
			<td border="1">A1</td>
		</tr>
		<tr>
			<td border="1">B2</td>
			<td border="1">A2</td>
		</tr>
	</table>
	<br>
	Exemple avec border et padding : <br>
	<table style="border: solid 5mm #770000; padding: 5mm;" cellspacing="0" >
		<tr>
			<td style="border: solid 3mm #007700; padding: 2mm;"><img src="./res/off.png" alt="" style="width: 20mm"></td>
		</tr>
	</table>
	<img src="./res/off.png" style="width: 10mm;"><img src="./res/off.png" style="width: 10mm;"><img src="./res/off.png" style="width: 10mm;"><img src="./res/off.png" style="width: 10mm;"><img src="./res/off.png" style="width: 10mm;"><br>
	<br>
	<table style="border: solid 1px #440000; width: 150px" cellspacing="0"><tr><td >Largeur : 150px</td></tr></table><br>
	<table style="border: solid 1px #440000; width: 150pt" cellspacing="0"><tr><td style="width: 100%">Largeur : 150pt</td></tr></table><br>
	<table style="border: solid 1px #440000; width: 100mm" cellspacing="0"><tr><td style="width: 100%">Largeur : 100mm</td></tr></table><br>
	<table style="border: solid 1px #440000; width:   5in" cellspacing="0"><tr><td style="width: 100%">Largeur :  5in</td></tr></table><br>
	<table style="border: solid 1px #440000; width:  80%"  cellspacing="0"><tr><td style="width: 100%">Largeur :  80% </td></tr></table><br>
	<br>
	Fin de l'exemple<br>
</page>
<?php
	$content = ob_get_clean();
	require_once(dirname(__FILE__).'/../html2pdf.class.php');
	$pdf = new HTML2PDF('P','A4','fr');
	$pdf->WriteHTML($content, isset($_GET['vuehtml']));
	$pdf->Output();
?>