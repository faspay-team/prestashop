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

class FaspayPaymentModuleFrontController extends ModuleFrontController {
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
        //print_r($cartProducts);exit;
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
        $my_module_name = Configuration::get('merchant_name');
        $my_module_user = Configuration::get('userid');
        $my_module_password = Configuration::get('userpswd');
        $my_module_id = Configuration::get('merchant_id');
        $expires = Configuration::get('order_expire');
        $free_text_1 = Configuration::get('free_text_1');
        $free_text_2 = Configuration::get('free_text_2');
        $mid_full = Configuration::get('mid_full');
        $mid_3 = Configuration::get('mid_3');
        $mid_6 = Configuration::get('mid_6');
        $mid_12 = Configuration::get('mid_12');
        $mid_24 = Configuration::get('mid_24');
        $custname = $this->context->customer->firstname;
        $custlname = $this->context->customer->lastname;
        $custmail = $this->context->customer->email;
        $custid = $this->context->customer->id;
        //Price without TAX


        $price = Context::getContext()->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING);
        //
        //Get Value form pg
        $pg = Tools::getValue('pg');
        $klikpay = '0';

        $bankValue = $pg;
        $bankName = $pg;

        $query = "SELECT flow FROM "._DB_PREFIX_."banklist WHERE bank_code = '".$bankName."'";
        $result = Db::getInstance()->executeS($query)[0];
        $flow = $result['flow'];
        $flag_net = 0;
        if ($bankValue === "linkaja") {
            $bankValue = "302";
        } else if ($bankValue === "xltunai") {
            $bankValue = "303";

        } else if ($bankValue === "ovo") {
            $bankValue = "812";

        } else if ($bankValue === "bri_mocash") {
            $bankValue = "400";

        } else if ($bankValue === "bri_epay") {
            $bankValue = "401";

        } else if ($bankValue === "permata_va") {
            $bankValue = "402";

        } else if ($bankValue === "permatanet") {
            $bankValue = "402";
            $flag_net = 1;
        } else if ($bankValue === "bca_klikpay") {
            $bankValue = "405";

            $klikpay +=1;
            $klikpay_option = implode(',', Tools::getValue('klikpay_option'));
            $option = $klikpay_option;
        } else if ($bankValue === "mandiri_clickpay") {
            $bankValue = "406";

        } else if ($bankValue === "maybank_va") {
            $bankValue = "408";

        } else if ($bankValue === "cimb_clicks") {
            $bankValue = "700";

        } else if ($bankValue === "dob") {
            $bankValue = "701";

        } else if ($bankValue === "bca_va") {
            $bankValue = "702";

        } else if ($bankValue === "danamon_va") {
            $bankValue = "708";

        } else if ($bankValue === "bri_va") {
            $bankValue = "800";
        } else if ($bankValue === "bni_va") {
            $bankValue = "801";
        } else if ($bankValue === "mandiri_va") {
            $bankValue = "802";
        } else if ($bankValue === "alfa_group") {
            $bankValue = "707";
        } else if ($bankValue === "kredivo") {
            $bankValue = "709";
        } else if ($bankValue === "mandiri_billpayment") {
            $bankValue = "703";
        } else if ($bankValue === "bca_sakuku") {
            $bankValue = "704";
        }
        else if ($bankValue === "indomaret_paymentpoint") {
            $bankValue = "706";
        }
        elseif ($bankValue === "maybank_m2u") {
            $bankValue ="814";
        }
        elseif ($bankValue === "akulaku") {
            $bankValue ="807";
        }


        $sign = $my_module_user . $my_module_password;
        $pay_total = $cart->getOrderTotal(true, Cart::BOTH);
        $tax_display = ($pay_total - $price);
        //$miscfee =  $tax_display.'00';
        $miscfee =  $tax_display*100;
        $datenow = date("Y-m-d h:i:s");
        foreach ($cartProducts as $item => $prod):
            $now = $datenow;
            $exp = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($cart->date_upd)) . "+" . $expires . "hour"));
            $signature = sha1(md5($sign . $id_cart));
            // $bill_total = $pay_total.'00';
            // $bill_gross = $price.'00';
            $bill_total = $pay_total*100;
            $bill_gross = $price*100;

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= "<faspay>" . "\n";
            $xml .= "<request>Transmisi Info Detil Pembelian</request>" . "\n";
            $xml .= "<merchant_id>" . $my_module_id . "</merchant_id>" . "\n";
            $xml .= "<merchant>" . $my_module_name . "</merchant> " . "\n";
            $xml .= "<bill_no>" . $id_cart . "</bill_no> " . "\n";
            $xml .= "<bill_reff></bill_reff>" . "\n";
            $xml .= "<bill_date>" . $cart->date_upd . "</bill_date> " . "\n";
            $xml .= "<bill_expired>" . $exp . "</bill_expired> " . "\n";
            $xml .= "<bill_desc>Pembarayan</bill_desc>" . "\n";
            $xml .= "<bill_currency>IDR</bill_currency>" . "\n";
            $xml .= "<bill_gross>".$bill_gross."</bill_gross>" . "\n";
            $xml .= "<bill_miscfee>" . $miscfee . "</bill_miscfee>" . "\n";
            $xml .= "<bill_total>" . $bill_total . "</bill_total>" . "\n";
            $xml .= "<cust_no>" . $custid . "</cust_no> " . "\n";
            $xml .= "<cust_name>" . $custname . $custlname . "</cust_name>" . "\n";
            $xml .= "<payment_channel>" . $bankValue . "</payment_channel>" . "\n";
            $xml .= "<bank_userid>-</bank_userid> " . "\n";
            $xml .= "<msisdn></msisdn> " . "\n";
            $xml .= "<email>" . $custmail . "</email> " . "\n";
            $xml .= "<terminal>10</terminal> " . "\n";
            $xml .= "<billing_name>" . $custname . "</billing_name> " . "\n";
            $xml .= "<billing_lastname>" . $custlname . "</billing_lastname> " . "\n";
            $xml .= "<billing_address>" . $bil_street . "</billing_address>" . "\n";
            $xml .= "<billing_address_city>" . $bil_city . "</billing_address_city>" . "\n";
            $xml .= "<billing_address_region></billing_address_region> " . "\n";
            $xml .= "<billing_address_state>" . $bil_state . "</billing_address_state> " . "\n";
            $xml .= "<billing_address_poscode>" . $bil_postcode . "</billing_address_poscode>" . "\n";
            $xml .= "<billing_msisdn></billing_msisdn> " . "\n";
            $xml .= "<billing_address_country_code></billing_address_country_code> " . "\n";
            $xml .= "<receiver_name_for_shipping>" . $custname . $custlname . "</receiver_name_for_shipping> " . "\n";
            $xml .= "<shipping_lastname></shipping_lastname> " . "\n";
            $xml .= "<shipping_address>" . $del_street . "</shipping_address> " . "\n";
            $xml .= "<shipping_address_city>" . $del_city . "</shipping_address_city>" . "\n";
            $xml .= "<shipping_address_region>" . $del_state . "</shipping_address_region>" . "\n";
            $xml .= "<shipping_address_state>" . $del_state . "</shipping_address_state> " . "\n";
            $xml .= "<shipping_address_poscode>" . $del_postcode . "</shipping_address_poscode> " . "\n";
            $xml .= "<shipping_msisdn>0</shipping_msisdn> " . "\n";
            $xml .= "<shipping_address_country_code></shipping_address_country_code> " . "\n";


            $flag_type = 00;
            if($bankValue == 405) {
                $srv = $_SERVER['HTTP_HOST'];
                $srv .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
                $tenor = explode(',',$option);
                if(in_array(00,$tenor))
                {
                    $flag_type += 1;
                    if(in_array(03,$tenor))
                    {
                        $flag_type += 2;
                    }
                    elseif(in_array(06,$tenor))
                    {
                        $flag_type += 2;
                    }
                    elseif(in_array(12,$tenor))
                    {
                        $flag_type += 2;
                    }
                    elseif(in_array(24,$tenor))
                    {
                        $flag_type += 2;
                    }
                }
                else
                {
                    $flag_type += 3;
                }
                $xml .= "<pay_type>".$flag_type."</pay_type>" . "\n";
                $countercicilan = 0;
                foreach ($cartProducts as $key => $value):
                    $subtotal = $value['price'];
                    $xml .= "<item> " . "\n";
                    $xml .= "<id>" . $value['id_product'] . "</id>" . "\n";
                    $xml .= "<product>" . $value['name'] . "</product>" . "\n";
                    $xml .= "<qty>" . $value['quantity'] . "</qty> " . "\n";
                    $xml .= "<amount>" . $subtotal*100 . "</amount> " . "\n";
                    $xml .= "<tenor>".$tenor["$key"]."</tenor> " . "\n";
                    if($tenor["$key"] == 00)
                    {
                        $xml .= "<merchant_id>".$mid_full."</merchant_id>" . "\n";
                        $xml .= "<payment_plan>1</payment_plan>" . "\n";
                    }
                    elseif($tenor["$key"] == 03)
                    {
                        $xml .= "<merchant_id>".$mid_3."</merchant_id>" . "\n";
                        $xml .= "<payment_plan>2</payment_plan>" . "\n";
                        $countercicilan++;
                    }
                    elseif($tenor["$key"] == 06)
                    {
                        $xml .= "<merchant_id>".$mid_6."</merchant_id>" . "\n";
                        $xml .= "<payment_plan>2</payment_plan>" . "\n";
                        $countercicilan++;
                    }
                    elseif($tenor["$key"] == 12)
                    {
                        $xml .= "<merchant_id>".$mid_12."</merchant_id>" . "\n";
                        $xml .= "<payment_plan>2</payment_plan>" . "\n";
                        $countercicilan++;
                    }
                    elseif($tenor["$key"] == 24)
                    {
                        $xml .= "<merchant_id>".$mid_24."</merchant_id>" . "\n";
                        $xml .= "<payment_plan>2</payment_plan>" . "\n";
                        $countercicilan++;
                    }
                    $xml .= "</item> " . "\n";
                endforeach;
                if($countercicilan>5) {
                    echo "<script language=\"Javascript\">\n";
                    echo "window.alert('Pembelian dengan Cicilan Tidak Bisa Lebih dari 5 Jenis Barang');";
                    echo "window.location ='$srv"."'</script>";
                    echo "</script>";
                    exit;}
            }


            else{

                $xml .= "<pay_type>1</pay_type>" . "\n";
                foreach ($cartProducts as $item):
                    $subtotal = $item['price'].'00';
                    $xml .= "<item> " . "\n";
                    $xml .= "<id>" . $item['id_product'] . "</id>" . "\n";
                    $xml .= "<product>" . $item['name'] . "</product>" . "\n";
                    $xml .= "<qty>" . $item['quantity'] . "</qty> " . "\n";
                    $xml .= "<amount>" . $subtotal*100 . "</amount> " . "\n";
                    $xml .= "<payment_plan>1</payment_plan>" . "\n";
                    $xml .= "<merchant_id>99999</merchant_id>" . "\n";
                    $xml .= "<tenor>00</tenor> " . "\n";
                    $xml .= "</item> " . "\n";
                endforeach;
            }





            if($bankValue == 702)
            {
                $xml .= "<reserve1>" . $free_text_1 . "</reserve1> " . "\n";
                $xml .= "<reserve2>" . $free_text_2 . "</reserve2> " . "\n";
            }
            else if($bankValue == 709)
            {
                $xml .= "<reserve1></reserve1> " . "\n";
                $xml .= "<reserve2>30_days</reserve2> " . "\n";
            }
            else
            {
                $xml .= "<reserve1></reserve1> " . "\n";
                $xml .= "<reserve2></reserve2> " . "\n";
            }
            $xml .= "<signature>" . $signature . "</signature> " . "\n";
            $xml .= "</faspay>" . "\n";
        endforeach;
        if(Configuration::get('server') == "production")
        {
                $url = 'https://web.faspay.co.id/pws/300011/383xx00010100000';
        }
        else
        {

                $url = 'https://dev.faspay.co.id/pws/300011/183xx00010100000';

        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);


        $result = curl_exec($ch);
        $data = simplexml_load_string($result);

        $VA = $data->trx_id;
        $bill_no = $data->bill_no;
        $merchant = $data->merchant_id;
        $items = $data->bill_items;
        $product = $data->bill_items->product;
        $productqty = $data->bill_items->qty;
        $productprice = $data->bill_items->amount / 100;
        $timer = $data->bill_expired;
        $guide = "modules/faspay/views/templates/front/payment_guide_$bankName.tpl";
        $this->context->smarty->assign([
            'signature' => $signature,
            'params' => $_REQUEST,
            'cartProd' => $cartProducts,
            'prod_id' => $id_cart,
            'total' => $price,
            'merchant_name' => $my_module_name,
            'custname' => $custname,
            'custlname' => $custlname,
            'custmail' => $custmail,
            'custid' => $custid,
            'va' => $VA,
            'bill_no' => $bill_no,
            'merchant' => $merchant,
            'finalprice' => $pay_total,
            'timer' => $timer,
            'product' => $product,
            'productqty' => $productqty,
            'productprice' => $productprice,
            'items' => $items,
            'exp' => $exp,
            'guide' => $guide,
            'tax_display' => $tax_display,
            'bank_name' => $bankName,
            'tpl_dir' => Tools::getHttpHost(true).__PS_BASE_URI__,
        ]);


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
            '{bankwire_owner}'   => Configuration::get('merchant_name'),
            '{bankwire_details}' => nl2br(Configuration::get('merchant_name')),
            '{bankwire_address}' => nl2br(Configuration::get('merchant_name'))
        );

        $bankValue = $bankName." (Via Faspay)";
        //$this->module->validateOrder($cart->id, Configuration::get('PS_OS_BANKWIRE'), $finalprice, $bankValue , $mailVars, (int)$currency->id, false, $customer->secure_key);

        $this->module->validateOrder($cart->id, Configuration::get('PS_OS_BANKWIRE'), $pay_total, $bankName, $bankValue, $mailVars, (int)$currency->id, false, $customer->secure_key);



        $sql = "INSERT INTO "._DB_PREFIX_."order_payment_faspay(order_id, trx_id, payment_channel,signature,amount)
								values(".$id_cart.", '".$VA."','".$bankName."','".$signature."','".$pay_total."')";
        Db::getInstance()->execute($sql);






        if($datenow > $exp)
        {
            $sql_order = "update 	"._DB_PREFIX_."orders 
            set current_state=('8')
	        where id_cart = ('$id_cart')";
            Db::getInstance()->execute($sql_order);


            $sql_history = "update 	"._DB_PREFIX_."order_history 
            set id_order_state=('8')
	        where id_cart = ('$id_cart')";
            Db::getInstance()->execute($sql_history);


            $this->context->smarty->assign([
                'tpl_dir' => Tools::getHttpHost(true).__PS_BASE_URI__,
            ]);
            $this->setTemplate('module:faspay/views/templates/front/payment_exp.tpl');
        }
        if($data->response_error->response_code == 55)
        {
            $this->setTemplate('module:faspay/views/templates/front/payment_error.tpl');
        }
        else
        {

            if($flow == 1 && $klikpay == 1)
            {
                $this->redirect_to_bca(simplexml_load_string($xml),$VA,$price,$tax_display);
            }

            elseif($flow == 1)
            {
                if($flag_net == 1)
                {
                    $this->redirect_to_permata(simplexml_load_string($xml),$VA);
                }
                elseif(Configuration::get('server') == "production")
                {
                    Tools::redirect('https://web.faspay.co.id/pws/100003/2830000010100000/'.$signature.'?trx_id='.$VA.'&merchant_id='.$merchant.'&bill_no='.$bill_no.'');
                }
                else
                {
                    Tools::redirect('https://dev.faspay.co.id/pws/100003/0830000010100000/'.$signature.'?trx_id='.$VA.'&merchant_id='.$merchant.'&bill_no='.$bill_no.'');
                }
            }

            elseif($flow == 2)
            {

                    $this->setTemplate('module:faspay/views/templates/front/payment_return_2.tpl');
                    $output = $this->context->smarty->fetch('module:faspay/views/templates/front/payment_guide_'.$bankName.'.tpl');
                    //Sending Email
                    // Mail::Send($this->context->language->id, 'reply_msg', Mail::l('Order Payment Guide'),
                    //     array(
                    //         '{lastname}' => $custlname,
                    //         '{firstname}' => $custname,
                    //         '{reply}' =>
                    //             "Thank You For Shopping
                    //          Payment will be conduct via $bankName, <br/>
                    //          <b>Your Shipping Address :</b> 
                    //          $del_street $del_city $del_state $del_postcode<br/>
                    //          <b>Please Pay Before</b>
                    //          $exp <br/>
                    //          <b>Payment Details</b>  
                    //          Virtual Account Number : <br /> 
                    //          <b>$VA</b><br /> 
                    //          Nama Merchant : <br /> 
                    //          <b>$my_module_name</b><br /> 
                    //          Nama Bank : <br />
                    //          <b>$bankName</b><br /> 
                    //          <br /> 
                    //          <br /> 
                    //          <br /> 
                    //          <center>Payment Guide</center><br /> 
                    //          $output
                    //     ",
                    //         '{link}' => Tools::getHttpHost(true).__PS_BASE_URI__),
                    //     $custmail , $custmail, $custname.' '.$custlname);

            }
        }

    }

    public function redirect_to_bca($loads,$VA,$gross,$tax)
    {
        $utils = new Faspay();
        $bcaDate = $utils->convertBcaDate($loads->bill_date);
        $clear_key = Configuration::get('clear_key');
        $keyId = $utils->genKeyId($clear_key);
        $post = array(
            "klikPayCode"		=> Configuration::get('klikpay_code'),
            "transactionDate"	=> $bcaDate,
            "transactionNo" 	=> $VA,
            "currency"			=> 'IDR',
            "totalAmount" 		=> $gross.'.00',
            "payType"			=> '0'.$loads->pay_type,
            "signature"			=> $utils->genSignature(Configuration::get('klikpay_code'), $bcaDate, $VA, $gross, 'IDR', $keyId),
            "descp"				=> $loads->bill_desc,
            "callback"			=> Tools::getHttpHost(true).__PS_BASE_URI__."module/faspay/thankyou"."?trx_id=".$VA,
            "miscFee"			=> $tax.'.00'
        );
        if(Configuration::get('server') == "production")
        {
            $string = '<form method="post" name="form" id = "BCAForm" action="https://klikpay.klikbca.com/purchasing/purchase.do?action=loginRequest" >';
        }
        else
        {
            $string = '<form method="post" name="form" id = "BCAForm" action="https://dev.faspay.co.id/bcaklikpay/purchasing" >';
        }
        if ($post != null) {
            foreach ($post as $name => $value) {
                $string .= '<input type="hidden" name="'.$name.'" value="'.$value.'">';
            }
        }
        $string .= '</form>';
        $string .= '<script>document.getElementById("BCAForm").submit();</script>';
        echo $string;
    }





    public function redirect_to_permata($loads,$VA)
    {
        $post = array(
            "amount"		=> $loads->bill_total.'.00',
            "va_number"	=> $VA,
        );
        if(Configuration::get('server') == "production")
        {
            $string = '<form method="post" name="form" id = "PermataForm" action="https://web.faspay.co.id/permatanet/payment" >';
        }
    else
        {
            $string = '<form method="post" name="form" id = "PermataForm" action="https://dev.faspay.co.id/permatanet/payment" >';
        }
        if ($post != null) {
            foreach ($post as $name => $value) {
                $string .= '<input type="hidden" name="'.$name.'" value="'.$value.'">';
            }
        }
        $string .= '</form>';
        $string .= '<script>document.getElementById("PermataForm").submit();</script>';
        echo $string;
    }
}
