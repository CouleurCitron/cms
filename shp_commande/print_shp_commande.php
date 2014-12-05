<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');



include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
//include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/htmlUtility.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/lib.inc.php');


if ($_GET['id_order'] > 0) {

	$order = new shp_commande($_GET['id_order']);

	if ($_GET['type'] == 'invoice') {
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/modules/webshop/custom/class.renderAdminInvoicePrintout.php')) {
			include_once('modules/webshop/custom/class.renderAdminInvoicePrintout.php');
			$render = new renderAdminInvoicePrintout();
		} else	die("Custom render class <modules/webshop/custom/class.renderAdminInvoicePrintout.php> is missing...");
	} elseif ($_GET['type'] == 'delivery') {
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/modules/webshop/custom/class.renderAdminDeliveryPrintout.php')) {
			include_once('modules/webshop/custom/class.renderAdminDeliveryPrintout.php');
			$render = new renderAdminDeliveryPrintout();
		} else	die("Custom render class <modules/webshop/custom/class.renderAdminDeliveryPrintout.php> is missing...");
	} else	die("Wrong render type called for printout");
	
	$render->render($order);

} else	die("Wrong order ID given for printout");

?>
