<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
require('../fpdf.php');

$pdf=new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,'Hello World !');
$pdf->Output();
?>
