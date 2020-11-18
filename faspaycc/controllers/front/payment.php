<?php
/**
 * Created by PhpStorm.
 * User: ArgadityaFi
 * Date: 18/04/2018
 * Time: 9:59
 */



class FaspayCCPaymentModuleFrontController extends ModuleFrontController
{
    public $display_column_left = false;
    public $ssl = true;

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $server = (Configuration::get('FASPAY_CC_SERVER')) ? 'prod' : 'dev';
        $url    = (Configuration::get('FASPAY_CC_SERVER')) ? 'https://fpg.faspay.co.id/payment' : 'https://fpgdev.faspay.co.id/payment';

        $cart = $this->context->cart;
        $cartProducts = $cart->getProducts(true);
        $id_cart = $cart->id;
        $address = new Address($cart->id_address_delivery);
        $del_street = $address->address1;
        $del_city = $address->city;
        $del_postcode = $address->postcode;
        $del_state = State::getNameById($address->id_state);
        $bil_address = new Address($cart->id_address_invoice);
        $bil_street = $bil_address->address1;
        $bil_city = $bil_address->city;
        $bil_postcode = $bil_address->postcode;
        $bil_state = State::getNameById($bil_address->id_state);
        $custname = $this->context->customer->firstname;
        $custlname = $this->context->customer->lastname;
        $custmail = $this->context->customer->email;

        $pg = Tools::getValue('pg');
        $bankName = $pg; 

        //VALIDATE ORDER
        $currency = $this->context->currency;
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }
        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'faspay') {
                $authorized = true;
                break;
            }
        }
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }
        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;

        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'faspay') {
                $authorized = true;
                break;
            }
        }
        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer))
            Tools::redirect('index.php?controller=order&step=1');
        if (!$authorized) {
            die($this->module->l('This payment method is not available.', 'validation'));
        }

        $mailVars = array(
            '{bankwire_owner}'   => Configuration::get('FASPAY_CC_MERCHANT_NAME'),
            '{bankwire_details}' => nl2br(Configuration::get('FASPAY_CC_MERCHANT_NAME')),
            '{bankwire_address}' => nl2br(Configuration::get('FASPAY_CC_MERCHANT_NAME'))
        );




        //Price without TAX
        $price = ceil(Context::getContext()->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING));
        $pay_total = ceil($cart->getOrderTotal(true, Cart::BOTH));
        $finalprice = number_format((float)$pay_total, 2, '.', '');


        $bankValue = $bankName." (Via Faspay)";
       // $this->module->validateOrder($cart->id, Configuration::get('PS_OS_BANKWIRE'), $finalprice, $bankValue , $mailVars, (int)$currency->id, false, $customer->secure_key);
        $this->module->validateOrder($cart->id, Configuration::get('PS_OS_BANKWIRE'), $finalprice, $bankName, $bankValue, $mailVars, (int)$currency->id, false, $customer->secure_key);


        $id_order=Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'orders` 
            WHERE id_cart = ('.$id_cart.') ');

        $id_order = $id_order[0]['id_order'];

        $query = "SELECT * FROM "._DB_PREFIX_."faspay_cc_config WHERE name = '".$bankName."'";
        $result = Db::getInstance()->executeS($query)[0];



        $shipping = $finalprice - $price;
        $ship_cost = number_format((float)$shipping, 2, '.', '');

        $tax_display = ($pay_total - $price);
        $datenow = date("Y-m-d h:i:s");
        $tranid = date("YmdGis");
        $signaturecc=sha1('##'.strtoupper($result['mid']).'##'.strtoupper($result['password']).'##'.$id_order.'##'.$finalprice.'##'.'0'.'##');
        $itemcount = 1;
        $mref = array();
        foreach ($cartProducts as $item):
            $prods = array(
                "MREF".$itemcount => $item['name'].":".$item['price']
            );
            $itemcount++;

            $mref = array_merge($mref,$prods);
        endforeach;


        $post = array(
            "TRANSACTIONTYPE"       => '1',
        //"SHOPPER_IP"          => '192.168.130.130',
            "RESPONSE_TYPE"         => '2',
            "LANG"                  => '',
            "MERCHANTID"            => $result['mid'],
            "PAYMENT_METHOD"        => '1',
            "TXN_PASSWORD"          => $result['password'],
            "MERCHANT_TRANID"       => $id_order,
            "CURRENCYCODE"          => 'IDR',
            "AMOUNT"                => $finalprice,
            "CUSTNAME"              => $custname.$custlname,
            "CUSTEMAIL"             => $custmail,
            "DESCRIPTION"           => $result['name'],
            "RETURN_URL"            => Tools::getHttpHost(true).__PS_BASE_URI__."module/faspaycc/paymentreturn",
            "SIGNATURE"             => $signaturecc,
            "BILLING_ADDRESS"               => $bil_street,
            "BILLING_ADDRESS_CITY"          => $bil_city,
            "BILLING_ADDRESS_REGION"        => '',
            "BILLING_ADDRESS_STATE"         => $bil_state,
            "BILLING_ADDRESS_POSCODE"       => $bil_postcode,
            "BILLING_ADDRESS_COUNTRY_CODE"  => 'ID',
            "RECEIVER_NAME_FOR_SHIPPING"    => $custname . $custlname,
            "SHIPPING_ADDRESS"              => $del_street,
            "SHIPPING_ADDRESS_CITY"         => $del_city,
            "SHIPPING_ADDRESS_REGION"       => '',
            "SHIPPING_ADDRESS_STATE"        => $del_state,
            "SHIPPING_ADDRESS_POSCODE"      => $del_postcode,
            "SHIPPING_ADDRESS_COUNTRY_CODE" => 'ID',
            "SHIPPINGCOST"                  => $ship_cost,
            "PHONE_NO"                      => $address->phone,
            "MPARAM1"                       => '',
            "MPARAM2"                       => '',
            "CUSTOMER_REF"                  => '',
            "PYMT_IND"                      => '',
            "PYMT_CRITERIA"                 => '',
            "PYMT_TOKEN"                    => '',
        //"paymentoption"               => '0',
            "FRISK1"                        => '',
            "FRISK2"                        => '',
            "DOMICILE_ADDRESS"              => '',
            "DOMICILE_ADDRESS_CITY"         => '',
            "DOMICILE_ADDRESS_REGION"       => '',
            "DOMICILE_ADDRESS_STATE"        => '',
            "DOMICILE_ADDRESS_POSCODE"      => '',
            "DOMICILE_ADDRESS_COUNTRY_CODE" => '',
            "DOMICILE_PHONE_NO"             => '',
            "handshake_url"                 => '',
            "handshake_param"               => '',
            "style_merchant_name"         => 'black',
            "style_order_summary"         => 'black',
            "style_order_no"              => 'black',
            "style_order_desc"            => 'black',
            "style_amount"                => 'black',
            "style_background_left"       => '#fff',
            "style_button_cancel"         => 'grey',
            "style_font_cancel"           => 'white',
            //harus url yg lgsg ke gambar
            //"style_image_url"           => 'http://www.pikiran-rakyat.com/sites/files/public/styles/medium/public/image/2017/06/Logo%20HUT%20RI%20ke-72%20yang%20paling%20bener.jpg?itok=RsQpqpqD',
        );

        $post = array_merge($mref,$post);
        //Url PROD ke = https://fpg.faspay.co.id/payment
        $string = '<form method="post" name="form" action="'.$url.'">';
        if ($post != null) {
            foreach ($post as $name=>$value) {
                $string .= '<input type="hidden" name="'.$name.'" value="'.$value.'">';
            }
        }

        $string .= '</form>';
        $string .= '<script> document.form.submit();</script>';
        echo $string;
    }

}