<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 13573 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */

class FaspayCCPaymentDetailsModuleFrontController extends ModuleFrontController {
	public $display_column_left = false;
	public $ssl = true;

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
    {
        parent::initContent();
        $cart = $this->context->cart;
        $cartProducts = $cart->getProducts(true);
        $id_cart = $cart->id;
        $my_module_name = Configuration::get('merchant_name');
        $custname = $this->context->customer->firstname;
        $custlname = $this->context->customer->lastname;
        $custmail = $this->context->customer->email;
        $custid = $this->context->customer->id;
        //Price without TAX
        $price = Context::getContext()->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING);
        $pay_total = ceil($cart->getOrderTotal(true, Cart::BOTH));


        $tax_display = ($pay_total - $price);

        //Get Value form pg
        $pg = Tools::getValue('pg');
        $dat = $_POST;
        $concat = "";
        foreach ($dat as $key => $value) {
            $concat = $concat . "&" . $key . "=" . $value;
        }
        $bankName = $pg . $concat;

        $this->context->smarty->assign([
            'bank_name' => $bankName,
            'cartProd' => $cartProducts,
            'prod_id' => $id_cart,
            'total' => $price,
            'merchant_name' => $my_module_name,
            'custname' => $custname,
            'custlname' => $custlname,
            'custmail' => $custmail,
            'custid' => $custid,
            'tax_display' => $tax_display,
            'pay_total' => $pay_total,
            'tpl_dir' => Tools::getHttpHost(true).__PS_BASE_URI__,
        ]);
            $this->setTemplate('module:faspaycc/views/templates/front/payment_details.tpl');
    }
}
