<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');



include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/htmlUtility.php');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/lib.inc.php');


// include webshop custom lib
if (is_file($_SERVER['DOCUMENT_ROOT'].'/'.WEBSHOP_CUSTOM_LIB)) {
	include_once(WEBSHOP_CUSTOM_LIB);
}

// translation engine
$translator =& TslManager::getInstance();

// permet de dérouler le menu contextuellement
if (strpos($_SERVER['PHP_SELF'], "backoffice") !== false){
	activateMenu("gestionshp_commande"); 
}


// send email locally rather than to destination
$debug = false;
$debug_email = 'luc@couleur-citron.com';

//viewArray($_POST);
// objet 

if (is_as_get("display")) $_POST['display'] = $_GET['display'];
		
 
if ($_POST['todo'] == 'confirm_payment' && $_POST['order_id'] > 0) {

	$order = new shp_commande($_POST['order_id']);
	$customer = new shp_client($order->get_id_client());
	include_once('modules/webshop/custom/class.renderAdminActionEmail.php');
	$render = new renderAdminActionEmail();

	$order->set_id_statut(OR_PAY_OK);
	if (defined('WEBSHOP_ORDER_PREFIX') && WEBSHOP_ORDER_PREFIX != '')
		$prefix = WEBSHOP_ORDER_PREFIX;
	else	$prefix = 'ETPI';
	$order->set_reference($prefix.'_'.date('Ymd-His').'_'.rand(0,1000));
	$order->set_date_paiement(date('Y-m-d H:i:s'));
	$id = dbUpdate($order);

	// FROM CUSTOM WEBSHOP LIBRARY
	// process possible custom action after payment confirmed OK
	if (WEBSHOP_CUSTOM_LIB != '' && function_exists('order_post_process'))
		order_post_process($order, $customer);

	$params = Array(	'action'		=> $_POST['todo'],
			'order'		=> $order,
			'customer'	=> $customer );
	$message = $render->render($params);

	multiPartMail(($debug ? $debug_email : $customer->get_email()) , $message['subject'] , $message['body'], '', SHP_AUTO_EMAIL);

	$params['service'] = true;
	multiPartMail(SHP_ADMIN_ORDER_EMAIL , $message['subject'] , $message['body'] , '', SHP_AUTO_EMAIL);
	if (SHP_ADMIN_PACK_EMAIL != '')
		multiPartMail(SHP_ADMIN_PACK_EMAIL , $message['subject'] , $message['body'] , '', SHP_AUTO_EMAIL);
	if (SHP_ADMIN_SHIP_EMAIL != '')
		multiPartMail(SHP_ADMIN_SHIP_EMAIL , $message['subject'] , $message['body'] , '', SHP_AUTO_EMAIL);

	echo '<br/><br/>'.sprintf($translator->getText("Un avis d'encaissement a été envoyé par email à %s et la commande a été passée en statut 'payée'."), $customer->get_email());

	if (WEBSHOP_STOCK_DEDUCE && WEBSHOP_STOCK_ON_PAY) {
		// do update stock values
		include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/webshop/class.OrderModel.php');
		$model = new OrderModel();
		$model->updateStock($order);

		echo '<br/><br/>'.$translator->getText("Les stocks ont été mis à jour en conséquence.");
	}
	echo "<br/><br/>";

	
} elseif ($_POST['todo'] == 'confirm_shipment' && $_POST['order_id'] > 0) {

	$order = new shp_commande($_POST['order_id']);
	$customer = new shp_client($order->get_id_client());
	include_once('modules/webshop/custom/class.renderAdminActionEmail.php');
	$render = new renderAdminActionEmail();

	$params = Array(	'action'		=> $_POST['todo'],
			'order'		=> $order,
			'customer'	=> $customer );
	$message = $render->render($params);
	multiPartMail(($debug ? $debug_email : $customer->get_email()) , $message['subject'] , $message['body'] , '', SHP_AUTO_EMAIL);

	$params['service'] = true;
	multiPartMail(SHP_ADMIN_ORDER_EMAIL , $message['subject'] , $message['body'] , '', SHP_AUTO_EMAIL);
	if (SHP_ADMIN_SHIP_EMAIL != '')
		multiPartMail(SHP_ADMIN_SHIP_EMAIL , $message['subject'] , $message['body'] , '', SHP_AUTO_EMAIL);

	//déduction du nb de produit dans le stock de chacun
	if (is_file($_SERVER['DOCUMENT_ROOT'].'modules/fonctions.lib.php'))
		include_once('modules/fonctions.lib.php');
	
	$order->set_id_statut(PCK_SHP_OK);
	$id = dbUpdate($order);

	echo '<br/><br/>'.sprintf($translator->getText("Un avis d'expédition a été envoyé par email à %s et la commande a été passée en statut 'expédiée'."), $customer->get_email());

	if (WEBSHOP_STOCK_DEDUCE && !WEBSHOP_STOCK_ON_PAY) {
		// do update stock values
		include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/webshop/class.OrderModel.php');
		$model = new OrderModel();
		$model->updateStock($order);

		echo '<br/><br/>'.$translator->getText("Les stocks ont été mis à jour en conséquence.");
	}
	echo "<br/><br/>";

} else	$order = new shp_commande($_POST['display']);

$status = new shp_commande_statut($order->get_id_statut());



$kart = unserialize($order->get_structure());
//pre_dump($order);
//viewArray($kart, 'shopping kart');

$promo_price_all = '';
$id_promo = '';


// fichierr custom
if (is_file($_SERVER['DOCUMENT_ROOT']."/modules/webshop/custom/shp_commande/detail_shp_commande.php")) {

	include_once ("modules/webshop/custom/shp_commande/detail_shp_commande.php");
	
}
else {
 	echo '<br/><br/><div id="tableau_etape3">
		<table cellspacing="0" cellpadding="0" border="0">
		<tbody>
		<tr>
					<td valign="top" height="20" colspan="3" class="arbo"><strong>'.$translator->getText("COMMANDE N°").' '.$order->get_reference().'</strong></td>
		</tr><tr>
					</td height="20" colspan="3"></td>
		</tr><tr>
					<td width="120" class="arbo">'.$translator->getText("Date de paiement").'</td>
					<td width="20"></td>
					<td class="arbo">'.$order->get_date_paiement().'</td>
		</tr><tr>
					<td class="arbo">'.$translator->getText("Mode de paiement").'</td>
					<td></td>
					<td class="arbo">'.$order->get_mode_paiement().'</td>
		</tr><tr>
					<td class="arbo">'.$translator->getText("Etat").'</td>
					<td></td>
					<td class="arbo">'.$translator->getById($status->get_libelle()).'</td>
		</tr><tr>
					</td height="20" colspan="3"></td>
		</tr>
		</tbody>
		</table>
		<br/><br/>
		<table cellspacing="0" cellpadding="0" border="0">
		<tbody>
		<tr>
					<td valign="top" height="20" class="arbo"><strong>'.$translator->getText("ADRESSE DE FACTURATION").'</strong></td>
					<td width="15" rowspan="2" id="separateur2">&nbsp;</td>
					<td valign="top" class="arbo"><strong>'.$translator->getText("ADRESSE DE LIVRAISON").'</strong></td>
		</tr><tr>
					<td width="320" class="arbo">'.str_replace('/', '<br />',$order->get_pay_adresse()).'</td>
					<td width="320" class="arbo">'.str_replace('/', '<br />',$order->get_exp_adresse()).'</td>
		</tr>
		</tbody>
		</table>
		<br/><br/>
		<table cellspacing="0" cellpadding="2" border="0" id="recap_pdt" style="border: solid 1px grey;">
		<tbody>
		<tr>
			<td width="245" class="arbo"><strong>'.$translator->getText("PRODUIT").'</strong></td>
			<td width="145" align="center" class="arbo"><strong>'.$translator->getText("TARIF").'</strong></td>
			<td width="165" align="center" class="arbo"><strong>'.$translator->getText("QUANTITÉ").'</strong></td>
			<td width="105" align="center" class="arbo"><strong>'.$translator->getText("TOTAL").'</strong></td>
		</tr><tr>
			<td colspan="4" class="separateur3" style="border-top: solid 1px grey;">&nbsp;</td>
		</tr>';
	if (!empty($kart)) {
		foreach ($kart as $product) {
			if (isset($product['name'])) {
				// custom products, packs, cards, other.
				echo '<tr>
		<td width="245" class="arbo"><strong>'.$product['name'].'</td>
		<td width="145"></td>
		<td width="165"></td>
		<td width="105" align="center" class="arbo"><strong>'.$product['price'].'</strong> &euro; '.$translator->getText("TTC").' </td>
	</tr><tr>
		<td colspan="4" class="separateur3">&nbsp;</td>
	</tr>';			
			} else {
				// standard webshop products
				$reference = explode('|', $product['ref']);
				$gamme = explode('_', $reference[0]);
				switch ($product['measure']) {
					case 'sample'	:	$u_disp = $translator->getText("1 échantillon");
								break;
					case 'ml'	:	$u_disp = $translator->getText("Métrage à couvrir").': <strong>'.$product['quantity'].'</strong> '.$product['measure'];
								break;
					case 'ex.'	:	$u_disp = $translator->getText("Unités").': <strong>'.$product['quantity'].'</strong> '.$product['measure'];
								break;
					case 'lot'	:	$u_disp = $translator->getText("Quantité").': <strong>'.$product['quantity'].'</strong>';
								break;
					case 'm&sup2;'	:	
					default		:	$u_disp = $translator->getText("").'<strong>'.$product['quantity'].'</strong>';
								break;
				}
				echo '<tr>
		<td width="245" class="arbo"><strong>'.$gamme[0].(!empty($reference[2]) ? ' ('.$reference[2].'g)' : '').' - '.$gamme[1].'</strong> <br/>'.$reference[1].' '.$reference[3].' '.$reference[4].'</td>
		<td width="145" align="center" class="arbo">';
		
				// promo 
				if (isset($product['promo_price_all']) && $product['promo_price_all'] != '') $promo_price_all = $product['promo_price_all'];
				if (isset($product['id_promo']) && $product['id_promo'] != '') $id_promo = $product['id_promo'];
				 
		
				if ($product['measure'] == 'sample')
					echo '<strong>'.$product['unit_price'].'&euro;</strong> '.$translator->getText("TTC").' '.$translator->getText("l'échantillon");
				else	{
					// promo
					if (isset($product['promo_price']) && $product['promo_price'] != '') {
						echo '<strong>'.number_format($product['unit_price']-$product['promo_price'], 2, '.', '').' &euro;</strong> '.$translator->getText("TTC").' '.(in_array($product['measure'], Array('ex.', '100g', 'kg')) ? $translator->getText("l'unité") : $translator->getText("le").' '.$product['measure']);
					}
					else {
						echo '<strong>'.$product['unit_price'].' &euro;</strong> '.$translator->getText("TTC").' '.(in_array($product['measure'], Array('ex.', '100g', 'kg')) ? $translator->getText("l'unité") : $translator->getText("le").' '.$product['measure']);
					}
					
				}
				echo '</td>
		<td width="165" align="center" class="arbo">'.$u_disp.'</td>
		<td width="105" align="center" class="arbo"><strong>'.$product['total_price'].'</strong> &euro; '.$translator->getText("TTC").' </td>
	</tr><tr>
		<td colspan="4" class="separateur3">&nbsp;</td>
	</tr>';
			}
		}
	}
	 
	if ($promo_price_all !='') 
		echo '<tr>
		<td>&nbsp;</td>
		 
		<td height="20" align="right" class="arbo" colspan="2"><strong>'.$translator->getText("REDUCTION").' </strong></td>
		<td align="right" class="arbo"><strong>- '.$promo_price_all.'</strong> &euro; '.$translator->getText("TTC").'</td>
	</tr>';
	
	 
		
		
	echo '<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td height="20" align="right" class="arbo"><strong>'.$translator->getText("TOTAL HT").'</strong></td>
		<td align="right" class="arbo"><strong>'.$order->get_total_ht().'</strong> &euro; '.$translator->getText("HT").'</td>
	</tr><tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td height="20" align="right" class="arbo"><strong>'.$translator->getText("TOTAL TTC").'</strong></td>
		<td align="right" class="arbo"><strong>'.$order->get_total_ttc().'</strong> &euro; '.$translator->getText("TTC").'</td>
	</tr><tr>
		<td>&nbsp;</td>
		 
		<td height="20" align="right" class="arbo" colspan="2"><strong>'.$translator->getText("FRAIS DE PORT").' ( '.$translator->getText("dont TVA").' '.calculateTVA($order->get_port(), 19.60).' &euro; )</strong></td>
		<td align="right" class="arbo"><strong>'.$order->get_port().'</strong> &euro; '.$translator->getText("TTC").'</td>
	</tr><tr><td>&nbsp;</td>
		<td>&nbsp;</td>
		<td height="20" align="right" class="arbo"><strong>'.$translator->getText("TOTAL A PAYER").'</strong></td>
		<td align="right" class="arbo"><strong>'.$order->get_total_pay().'</strong> &euro; '.$translator->getText("TTC").'</td>
	</tr><tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	</tbody>
	</table>';
	
}

if ($status->get_id() == OR_PAY_STD && !in_array($order->get_mode_paiement(), Array('CREDIT_CARD', 'PAYPAL')))
	echo "<br/><br/>
		<form id=\"confirm_ship\" name=\"confirm_ship\" action=\"detail_shp_commande.php?menuOpen=true\" method=\"POST\">
		<input type=\"hidden\" id=\"todo\" name=\"todo\" value=\"confirm_payment\">
		<input type=\"hidden\" id=\"order_id\" name=\"order_id\" value=\"".$_POST['display']."\">
		</form>
		<a href=\"#_\" onclick=\"document.forms['confirm_ship'].submit()\">Confirmer l'encaissement du paiement au client</a>";
if ($status->get_id() == PCK_SHP_STD)
	echo "<br/><br/>
		<form id=\"confirm_ship\" name=\"confirm_ship\" action=\"detail_shp_commande.php?menuOpen=true\" method=\"POST\">
		<input type=\"hidden\" id=\"todo\" name=\"todo\" value=\"confirm_shipment\">
		<input type=\"hidden\" id=\"order_id\" name=\"order_id\" value=\"".$_POST['display']."\">
		</form>
		<a href=\"#_\" onclick=\"openBrWindow('print_shp_commande.php?id_order={$_POST['display']}&type=invoice', 'print', 680, 440, 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no', 'true');\" title=\"Print order invoice\">Imprimer la facture</a><br/>
		<a href=\"#_\" onclick=\"openBrWindow('print_shp_commande.php?id_order={$_POST['display']}&type=delivery', 'print', 680, 440, 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no', 'true');\" title=\"Print shipping slip\">Imprimer un bon de livraison</a><br/><br/>
		<a href=\"#_\" onclick=\"document.forms['confirm_ship'].submit()\">Confirmer l'expédition au client</a>";

echo "<br/><br/><a href=\"/backoffice/cms/shp_commande/list_shp_commande.php?menuOpen=true\">Retour à la liste</a>";

?>
