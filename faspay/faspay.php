<?php
/**
 * Created by PhpStorm.
 * User: faspaydityaFi
 * Date: 09/03/2018
 * Time: 9:48
 */

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;


if (!defined('_PS_VERSION_'))
{
    exit;
}

class Faspay extends PaymentModule
{

    public function __construct()
    {

        $this->bootstrap = true;
        $this->name = 'faspay';
        $this->tab = 'payments_gateways';
        $this->version = '2.0.0';
        $this->author = 'faspay.co.id';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->currencies = true;
        $this->currencies_mode = 'checkbox';
        parent::__construct();

        $this->displayName = $this->l('Online Payment via Faspay Solution');
        $this->description = $this->l('Faspay Payment Gateways - Transaction secure, anytime, and anywhere');

        $this->confirmUninstall = $this->l('Do you sure you want to uninstall?');

        if (!Configuration::get('MYMODULE_NAME'))
            $this->warning = $this->l('No name provided');
        if (!count(Currency::checkPaymentCurrencies($this->id))) {
            $this->warning = $this->l('No currency has been set for this module.');
        }
    }
    public function install()
    {
        
        if (version_compare(phpversion(), '5.6.0', '<')) {
            return false;
        }

        try
        {
            
            $this->installTables();
            $this->createBank();
        } catch (Exception $e) {
            die('Excepción capturada: ' . $e->getMessage());
        }

        return parent::install()
        && $this->registerHook('paymentOptions');

    }
    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submit'.$this->name))
        {
            $my_module_id = strval(Tools::getValue('merchant_id'));
            $my_module_name = strval(Tools::getValue('merchant_name'));
            $my_module_order = strval(Tools::getValue('order_expire'));
            $my_module_user = strval(Tools::getValue('userid'));
            $my_module_password = strval(Tools::getValue('userpswd'));
            $server = strval(Tools::getValue('server'));
            $_POST['payment'] = implode(',', Tools::getValue('payment'));
            $add_fee = strval(Tools::getValue('add_fee'));
            $linkaja = strval(Tools::getValue('linkaja'));
            $xltunai = strval(Tools::getValue('xltunai'));
            $alfa_group = strval(Tools::getValue('alfa_group'));
            $ovo = strval(Tools::getValue('ovo'));
            $danamon_va = strval(Tools::getValue('danamon_va'));
            $bri_mocash = strval(Tools::getValue('bri_mocash'));
            $bri_epay = strval(Tools::getValue('bri_epay'));
            $permata_va = strval(Tools::getValue('permata_va'));
            $permatanet = strval(Tools::getValue('permatanet'));
            $bca_klikpay = strval(Tools::getValue('bca_klikpay'));
            $mandiri_clickpay = strval(Tools::getValue('mandiri_clickpay'));
            $kredivo = strval(Tools::getValue('kredivo'));
            $maybank_va = strval(Tools::getValue('maybank_va'));
            $cimb_clicks = strval(Tools::getValue('cimb_clicks'));
            $dob = strval(Tools::getValue('dob'));
            $bca_va = strval(Tools::getValue('bca_va'));
            $mandiri_billpayment = strval(Tools::getValue('mandiri_billpayment'));
            $bca_sakuku = strval(Tools::getValue('bca_sakuku'));
            $indomaret_paymentpoint = strval(Tools::getValue('indomaret_paymentpoint'));
            $bri_va = strval(Tools::getValue('bri_va'));
            $bni_va = strval(Tools::getValue('bni_va'));
            $mandiri_va = strval(Tools::getValue('mandiri_va'));
            $maybank_m2u = strval(Tools::getValue('maybank_m2u'));
            $akulaku = strval(Tools::getValue('akulaku'));
            $linkaja_option = strval(Tools::getValue('linkaja_option'));
            $xltunai_option = strval(Tools::getValue('xltunai_option'));
            $alfa_group_option = strval(Tools::getValue('alfa_group_option'));
            $ovo_option = strval(Tools::getValue('ovo_option'));
            $danamon_va_option = strval(Tools::getValue('danamon_va_option'));
            $bri_mocash_option = strval(Tools::getValue('bri_mocash_option'));
            $bri_epay_option = strval(Tools::getValue('bri_epay_option'));
            $permata_va_option = strval(Tools::getValue('permata_va_option'));
            $permatanet_option = strval(Tools::getValue('permatanet_option'));
            $bca_klikpay_option = strval(Tools::getValue('bca_klikpay_option'));
            $mandiri_clickpay_option = strval(Tools::getValue('mandiri_clickpay_option'));
            $kredivo_option = strval(Tools::getValue('kredivo_option'));
            $maybank_va_option = strval(Tools::getValue('maybank_va_option'));
            $cimb_clicks_option = strval(Tools::getValue('cimb_clicks_option'));
            $dob_option = strval(Tools::getValue('dob_option'));
            $bca_va_option = strval(Tools::getValue('bca_va_option'));
            $mandiri_billpayment_option = strval(Tools::getValue('mandiri_billpayment_option'));
            $bca_sakuku_option = strval(Tools::getValue('bca_sakuku_option'));
            $indomaret_paymentpoint_option = strval(Tools::getValue('indomaret_paymentpoint_option'));
            $bri_va_option = strval(Tools::getValue('bri_va_option'));
            $bni_va_option = strval(Tools::getValue('bni_va_option'));
            $mandiri_va_option = strval(Tools::getValue('mandiri_va_option'));
            $maybank_m2u_option = strval(Tools::getValue('maybank_m2u_option'));
            $akulaku_option = strval(Tools::getValue('akulaku_option'));
            $clear_key = strval(Tools::getValue('clear_key'));
            $klikpay_code = strval(Tools::getValue('klikpay_code'));
            $free_text_1 = strval(Tools::getValue('free_text_1'));
            $free_text_2 = strval(Tools::getValue('free_text_2'));
            $mid_full = strval(Tools::getValue('mid_full'));
            $mid_3 = strval(Tools::getValue('mid_3'));
            $min_3 = strval(Tools::getValue('min_3'));
            $status_3 = strval(Tools::getValue('status_3'));
            $mid_6 = strval(Tools::getValue('mid_6'));
            $min_6 = strval(Tools::getValue('min_6'));
            $status_6 = strval(Tools::getValue('status_6'));
            $mid_12 = strval(Tools::getValue('mid_12'));
            $min_12 = strval(Tools::getValue('min_12'));
            $status_12 = strval(Tools::getValue('status_12'));
            $mid_24 = strval(Tools::getValue('mid_24'));
            $min_24 = strval(Tools::getValue('min_24'));
            $status_24 = strval(Tools::getValue('status_24'));
            $status_mix = strval(Tools::getValue('status_mix'));
            if (!$my_module_id
                || empty($my_module_id)
                || empty($my_module_name)
                || empty($my_module_order)
                || empty($my_module_user)
                || empty($server)
            )
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            if (empty($my_module_password))
                $output .= $this->displayError($this->l('Password Can not be Empty'));
            else
            {
                Configuration::updateValue('merchant_id', $my_module_id);
                Configuration::updateValue('merchant_name', $my_module_name);
                Configuration::updateValue('order_expire', $my_module_order);
                Configuration::updateValue('userid', $my_module_user);
                if($my_module_password)
                {
                    Configuration::updateValue('userpswd', $my_module_password);
                }
                Configuration::updateValue('add_fee', $add_fee);
                Configuration::updateValue('server', $server);
                Configuration::updateValue('payment', $_POST['payment']);
                Configuration::updateValue('linkaja',$linkaja);
                Configuration::updateValue('xltunai',$xltunai);
                Configuration::updateValue('alfa_group',$alfa_group);
                Configuration::updateValue('ovo',$ovo);
                Configuration::updateValue('danamon_va',$danamon_va);
                Configuration::updateValue('bri_mocash',$bri_mocash);
                Configuration::updateValue('bri_epay',$bri_epay);
                Configuration::updateValue('permata_va',$permata_va);
                Configuration::updateValue('permatanet',$permatanet);
                Configuration::updateValue('bca_klikpay',$bca_klikpay);
                Configuration::updateValue('mandiri_clickpay',$mandiri_clickpay);
                Configuration::updateValue('kredivo',$kredivo);
                Configuration::updateValue('maybank_va',$maybank_va);
                Configuration::updateValue('cimb_clicks',$cimb_clicks);
                Configuration::updateValue('dob',$dob);
                Configuration::updateValue('bca_va',$bca_va);
                Configuration::updateValue('mandiri_billpayment',$mandiri_billpayment);
                Configuration::updateValue('bca_sakuku',$bca_sakuku);
                Configuration::updateValue('indomaret_paymentpoint',$indomaret_paymentpoint);
                Configuration::updateValue('bri_va',$bri_va);
                Configuration::updateValue('bni_va',$bni_va);
                Configuration::updateValue('mandiri_va',$mandiri_va);
                Configuration::updateValue('maybank_m2u',$maybank_m2u);
                Configuration::updateValue('akulaku',$akulaku);
                Configuration::updateValue('linkaja_option',$linkaja_option);
                Configuration::updateValue('xltunai_option',$xltunai_option);
                Configuration::updateValue('alfa_group_option',$alfa_group_option);
                Configuration::updateValue('ovo_option',$ovo_option);
                Configuration::updateValue('danamon_va_option',$danamon_va_option);
                Configuration::updateValue('bri_mocash_option',$bri_mocash_option);
                Configuration::updateValue('bri_epay_option',$bri_epay_option);
                Configuration::updateValue('permata_va_option',$permata_va_option);
                Configuration::updateValue('permatanet_option',$permatanet_option);
                Configuration::updateValue('bca_klikpay_option',$bca_klikpay_option);
                Configuration::updateValue('mandiri_clickpay_option',$mandiri_clickpay_option);
                Configuration::updateValue('kredivo_option',$kredivo_option);
                Configuration::updateValue('maybank_va_option',$maybank_va_option);
                Configuration::updateValue('cimb_clicks_option',$cimb_clicks_option);
                Configuration::updateValue('dob_option',$dob_option);
                Configuration::updateValue('bca_va_option',$bca_va_option);
                Configuration::updateValue('mandiri_billpayment_option',$mandiri_billpayment_option);
                Configuration::updateValue('bca_sakuku_option',$bca_sakuku_option);
                Configuration::updateValue('indomaret_paymentpoint_option',$indomaret_paymentpoint_option);
                Configuration::updateValue('bri_va_option',$bri_va_option);
                Configuration::updateValue('bni_va_option',$bni_va_option);
                Configuration::updateValue('mandiri_va_option',$mandiri_va_option);
                Configuration::updateValue('maybank_m2u_option',$maybank_m2u_option);
                Configuration::updateValue('akulaku',$akulaku);
                Configuration::updateValue('clear_key',$clear_key);
                Configuration::updateValue('klikpay_code',$klikpay_code);
                Configuration::updateValue('free_text_1',$free_text_1);
                Configuration::updateValue('free_text_2',$free_text_2);
                Configuration::updateValue('mid_full',$mid_full);
                Configuration::updateValue('mid_3',$mid_3);
                Configuration::updateValue('min_3',$min_3);
                Configuration::updateValue('status_3',$status_3);
                Configuration::updateValue('mid_6',$mid_6);
                Configuration::updateValue('min_6',$min_6);
                Configuration::updateValue('status_6',$status_6);
                Configuration::updateValue('mid_12',$mid_12);
                Configuration::updateValue('min_12',$min_12);
                Configuration::updateValue('status_12',$status_12);
                Configuration::updateValue('mid_24',$mid_24);
                Configuration::updateValue('min_24',$min_24);
                Configuration::updateValue('status_24',$status_24);
                Configuration::updateValue('status_mix',$status_mix);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }



        return $output.$this->displayForm();
    }
    protected function displayForm()
    {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        // Init Fields form array
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Account Details'),
                    'image' => "../modules/faspay/contact.gif"
                ),
                'input' => array(
                    array(
                        'label' => $this->l('Please spesify account details :  ')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Merchant ID'),
                        'name' => 'merchant_id',
                        'size' => 10,
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Merchant Name'),
                        'name' => 'merchant_name',
                        'size' => 15,
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Order Expire in'),
                        'name' => 'order_expire',
                        'size' => 2,
                        'suffix' => 'hours',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('User ID'),
                        'name' => 'userid',
                        'size' => 10,
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Password'),
                        'name' => 'userpswd',
                        'size' => 10,
                    ),
                    array(
                        $server = array(
                            array(
                                'id' => 'development',
                                'name' => 'Development'
                            ),
                            array(
                                'id' => 'production',
                                'name' => 'Production'
                            ),
                        ),
                        'type' => 'select',
                        'label' => $this->l('Server :'),
                        'desc' => $this->l('Choose a Server'),
                        'name' => 'server',
                        'required' => true,
                        'options' => array(
                            'query' => $server,
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                    array(
                        $payment = array(
                            array(
                                'id' => 'linkaja',
                                'name' => $this->l('linkaja'),
                                'val' => 'linkaja'
                            ),
                            array(
                                'id' => 'xltunai',
                                'name' => $this->l('XLTunai'),
                                'val' => 'xltunai'
                            ),
                            array(
                                'id' => 'ovo',
                                'name' => $this->l('OVO'),
                                'val' => 'ovo'
                            ),
                            array(
                                'id' => 'bri_mocash',
                                'name' => $this->l('BRI Mobile Cash'),
                                'val' => 'bri_mocash'
                            ),
                            array(
                                'id' => 'bri_epay',
                                'name' => $this->l('BRI ePay'),
                                'val' => 'bri_epay'
                            ),
                            array(
                                'id' => 'permata_va',
                                'name' => $this->l('Permata Virtual Account'),
                                'val' => 'permata_va'
                            ),
                            array(
                                'id' => 'permatanet',
                                'name' => $this->l('PermataNet'),
                                'val' => 'permatanet'
                            ),
                            array(
                                'id' => 'bca_klikpay',
                                'name' => $this->l('BCA KlikPay'),
                                'val' => 'bca_klikpay'
                            ),
                            array(
                                'id' => 'mandiri_clickpay',
                                'name' => $this->l('Mandiri clickPay'),
                                'val' => 'mandiri_clickpay'
                            ),
                            array(
                                'id' => 'maybank_va',
                                'name' => $this->l('ATM/Virtual Account Maybank'),
                                'val' => 'maybank_va'
                            ),
                            array(
                                'id' => 'cimb_clicks',
                                'name' => $this->l('CIMB Clicks'),
                                'val' => 'cimb_clicks'
                            ),
                            array(
                                'id' => 'dob',
                                'name' => $this->l('Danamon Online Banking'),
                                'val' => 'dob'
                            ),
                            array(
                                'id' => 'bca_va',
                                'name' => $this->l('BCA Virtual Account Online'),
                                'val' => 'bca_va'
                            ),
                            array(
                                'id' => 'danamon_va',
                                'name' => $this->l('Danamon Virtual Account Online'),
                                'val' => 'danamon_va'
                            ),
                            array(
                                'id' => 'bri_va',
                                'name' => $this->l('BRI Virtual Account Online'),
                                'val' => 'bri_va'
                            ),
                            array(
                                'id' => 'bni_va',
                                'name' => $this->l('BNI Virtual Account Online'),
                                'val' => 'bni_va'
                            ),
                            array(
                                'id' => 'mandiri_va',
                                'name' => $this->l('Mandiri Virtual Account Online'),
                                'val' => 'mandiri_va'
                            ),
                            array(
                                'id' => 'alfa_group',
                                'name' => $this->l('Alfa Group'),
                                'val' => 'alfa_group'
                            ),
                            array(
                                'id' => 'kredivo',
                                'name' => $this->l('Kredivo'),
                                'val' => 'kredivo'
                            ),
                            array(
                                'id' => 'mandiri_billpayment',
                                'name' => $this->l('Mandiri Bill Payment'),
                                'val' => 'mandiri_billpayment'
                            ),
                            array(
                                'id' => 'bca_sakuku',
                                'name' => $this->l('BCA Sakuku'),
                                'val' => 'bca_sakuku'
                            ),
                            array(
                                'id' => 'indomaret_paymentpoint',
                                'name' => $this->l('Indomaret Payment Point'),
                                'val' => 'indomaret_paymentpoint'
                            ),
                            array(
                                'id' => 'maybank_m2u',
                                'name' => $this->l('Maybank M2U'),
                                'val' => 'maybank_m2u'
                            ),
                            array(
                                'id' => 'akulaku',
                                'name' => $this->l('Akulaku'),
                                'val' => 'akulaku'
                            ),

                        ),

                        'type' => 'checkbox',
                        'label' => $this->l('Payment Gateways'),
                        'desc' => $this->l('Choose Payment Channels.'),
                        'required' => true,
                        'multiple' => true,
                        'name' => 'payment[]',
                        'values' => array(
                            'query' => $payment,
                            'id' => 'id',
                            'name' => 'name',
                        ),
                        'expand' => array(
                            'print_total' => count($payment),
                            'default' => 'show',
                            'show' => array('text' => $this->l('show'), 'icon' => 'plus-sign-alt'),
                            'hide' => array('text' => $this->l('hide'), 'icon' => 'minus-sign-alt')
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save Configuration'),
                    'class' => 'btn btn-default pull-right'
                )
            )
        );
        // Init Fields form array
        $bca_form= array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('BCA klikPay Parameters'),
                    'image' => "../modules/faspay/money.png",
                ),
                'input' => array(
                    array(
                        'label' => $this->l('Need Help?'),
                        'hint' => 'Mandatory if BCA Klikpay is checked'
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'clear_key',
                        'label' => 'Clear Key',
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'klikpay_code',
                        'label' => 'klikPay Code',
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'mid_full',
                        'label' => $this->l('MID FULL'),
                    ),
                    array(
                        'label' => $this->l('Konfigurasi untuk cicilan 3 bulan')
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'mid_3',
                        'label' => $this->l('MID'),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'min_3',
                        'label' => $this->l('Price Minimum'),
                    ),
                    array(
                        $status_3 = array(
                            array(
                                'name' => 'Pilih activasi cicilan'
                            ),
                            array(
                                'id' => 'active',
                                'name' => 'active',
                            ),
                            array(
                                'id' => 'inactive',
                                'name' => 'inactive'
                            ),
                        ),
                        'type' => 'select',
                        'label' => $this->l('Status :'),
                        'name' => 'status_3',
                        'options' => array(
                            'query' => $status_3,
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'label' => $this->l('Konfigurasi untuk cicilan 6 bulan')
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'mid_6',
                        'label' => $this->l('MID'),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'min_6',
                        'label' => $this->l('Price Minimum'),
                    ),
                    array(
                        $status_6 = array(
                            array(
                                'name' => 'Pilih activasi cicilan'
                            ),
                            array(
                                'id' => 'active',
                                'name' => 'active'
                            ),
                            array(
                                'id' => 'inactive',
                                'name' => 'inactive'
                            ),
                        ),
                        'type' => 'select',
                        'label' => $this->l('Status :'),
                        'name' => 'status_6',
                        'options' => array(
                            'query' => $status_6,
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'label' => $this->l('Konfigurasi untuk cicilan 12 bulan')
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'mid_12',
                        'label' => $this->l('MID'),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'min_12',
                        'label' => $this->l('Price Minimum'),
                    ),
                    array(
                        $status_12 = array(
                            array(
                                'name' => 'Pilih activasi cicilan'
                            ),
                            array(
                                'id' => 'active',
                                'name' => 'active'
                            ),
                            array(
                                'id' => 'inactive',
                                'name' => 'inactive'
                            ),
                        ),
                        'type' => 'select',
                        'label' => $this->l('Status :'),
                        'name' => 'status_12',
                        'options' => array(
                            'query' => $status_12,
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'label' => $this->l('Konfigurasi untuk cicilan 24 bulan')
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'mid_24',
                        'label' => $this->l('MID'),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'min_24',
                        'label' => $this->l('Price Minimum'),
                    ),
                    array(
                        $status_24 = array(
                            array(
                                'name' => 'Pilih activasi cicilan'
                            ),
                            array(
                                'id' => 'active',
                                'name' => 'active'
                            ),
                            array(
                                'id' => 'inactive',
                                'name' => 'inactive'
                            ),
                        ),
                        'type' => 'select',
                        'label' => $this->l('Status :'),
                        'name' => 'status_24',
                        'options' => array(
                            'query' => $status_24,
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'label' => $this->l('Konfigurasi Transaksi untuk MIX')
                    ),
                    array(
                        $status_mix = array(
                            array(
                                'name' => 'Pilih Status MIX'
                            ),
                            array(
                                'id' => 'active',
                                'name' => 'active'
                            ),
                            array(
                                'id' => 'inactive',
                                'name' => 'inactive'
                            ),
                        ),
                        'type' => 'select',
                        'label' => $this->l('Status :'),
                        'name' => 'status_mix',
                        'options' => array(
                            'query' => $status_mix,
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save Configuration'),
                    'class' => 'btn btn-default pull-right'
                )
            )
        );
        $bca_va_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('BCA VA Free Text'),
                    'image' => "../modules/faspay/text.png",
                ),
                'input' => array(
                    array(
                        'label' => $this->l('Need Help?'),
                        'hint' => 'Mandatory if BCA Klikpay is checked'
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'free_text_1',
                        'label' => 'Free Text 1',
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'free_text_2',
                        'label' => 'Free Text 2',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save Configuration'),
                    'class' => 'btn btn-default pull-right'
                )
            )
        );
        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' =>
                array(
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                        '&token=' . Tools::getAdminTokenLite('AdminModules'),
                ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );
        // Load current value

        $helper->fields_value['merchant_id'] = Configuration::get('merchant_id');
        $helper->fields_value['merchant_name'] = Configuration::get('merchant_name');
        $helper->fields_value['order_expire'] = Configuration::get('order_expire');
        $helper->fields_value['userid'] = Configuration::get('userid');
        $helper->fields_value['userpswd'] = Configuration::get('userpswd');
        $helper->fields_value['server'] = Configuration::get('server');
        $confs = explode(',', Configuration::get('payment'));
        foreach ($confs as $conf) {
            $helper->fields_value['payment[]_' . $conf] = 'true';
        }
        $helper->fields_value['add_fee'] = Configuration::get('add_fee');
        $helper->fields_value['linkaja'] = Configuration::get('linkaja');
        $helper->fields_value['xltunai'] = Configuration::get('xltunai');
        $helper->fields_value['alfa_group'] = Configuration::get('alfa_group');
        $helper->fields_value['ovo'] = Configuration::get('ovo');
        $helper->fields_value['danamon_va'] = Configuration::get('danamon_va');
        $helper->fields_value['bri_mocash'] = Configuration::get('bri_mocash');
        $helper->fields_value['bri_epay'] = Configuration::get('bri_epay');
        $helper->fields_value['permata_va'] = Configuration::get('permata_va');
        $helper->fields_value['permatanet'] = Configuration::get('permatanet');
        $helper->fields_value['bca_klikpay'] = Configuration::get('bca_klikpay');
        $helper->fields_value['mandiri_clickpay'] = Configuration::get('mandiri_clickpay');
        $helper->fields_value['kredivo'] = Configuration::get('kredivo');
        $helper->fields_value['maybank_va'] = Configuration::get('maybank_va');
        $helper->fields_value['cimb_clicks'] = Configuration::get('cimb_clicks');
        $helper->fields_value['dob'] = Configuration::get('dob');
        $helper->fields_value['bca_va'] = Configuration::get('bca_va');
        $helper->fields_value['mandiri_billpayment'] = Configuration::get('mandiri_billpayment');
        $helper->fields_value['bca_sakuku'] = Configuration::get('bca_sakuku');
        $helper->fields_value['indomaret_paymentpoint'] = Configuration::get('indomaret_paymentpoint');
        $helper->fields_value['bri_va'] = Configuration::get('bri_va');
        $helper->fields_value['bni_va'] = Configuration::get('bni_va');
        $helper->fields_value['mandiri_va'] = Configuration::get('mandiri_va');
        $helper->fields_value['maybank_m2u'] = Configuration::get('maybank_m2u');
        $helper->fields_value['akulaku'] = Configuration::get('akulaku');

        $helper->fields_value['clear_key'] = Configuration::get('clear_key');
        $helper->fields_value['free_text_1'] = Configuration::get('free_text_1');
        $helper->fields_value['free_text_2'] = Configuration::get('free_text_2');
        $helper->fields_value['klikpay_code'] = Configuration::get('klikpay_code');
        $helper->fields_value['mid_full'] = Configuration::get('mid_full');
        $helper->fields_value['mid_3'] = Configuration::get('mid_3');
        $helper->fields_value['min_3'] = Configuration::get('min_3');
        $helper->fields_value['status_3'] = Configuration::get('status_3');
        $helper->fields_value['mid_6'] = Configuration::get('mid_6');
        $helper->fields_value['min_6'] = Configuration::get('min_6');
        $helper->fields_value['status_6'] = Configuration::get('status_6');
        $helper->fields_value['mid_12'] = Configuration::get('mid_12');
        $helper->fields_value['min_12'] = Configuration::get('min_12');
        $helper->fields_value['status_12'] = Configuration::get('status_12');
        $helper->fields_value['mid_24'] = Configuration::get('mid_24');
        $helper->fields_value['min_24'] = Configuration::get('min_24');
        $helper->fields_value['status_24'] = Configuration::get('status_24');
        $helper->fields_value['status_mix'] = Configuration::get('status_mix');

        if($helper->fields_value['payment[]_bca_klikpay'] == true && $helper->fields_value['payment[]_bca_va'] == true){
            return $helper->generateForm(array($fields_form,$bca_form,$bca_va_form));
        }
        elseif($helper->fields_value['payment[]_bca_klikpay'] == true){
            return $helper->generateForm(array($fields_form,$bca_form));
        }
        elseif($helper->fields_value['payment[]_bca_va'] == true){
            return $helper->generateForm(array($fields_form,$bca_va_form));
        }
        else
        {
            return $helper->generateForm(array($fields_form));
        }
    }
    private function createBank() {
        $data = array(
            array(
                'id' => 1,
                'bank_numb' => 302,
                'bank_name' => 'LinkAja',
                'bank_code' => 'linkaja',
                'flow' => '1'
            ),
            array(
                'id' => 2,
                'bank_numb' => 303,
                'bank_name' => 'XL Tunai',
                'bank_code' => 'xltunai',
                'flow' => '1'
            ),
            array(
                'id' => 3,
                'bank_numb' => 708,
                'bank_name' => 'VA Danamon',
                'bank_code' => 'danamon_va',
                'flow' => '2'
            ),
            array(
                'id' => '4',
                'bank_numb' => 812,
                'bank_name' => 'OVO',
                'bank_code' => 'ovo',
                'flow' => '1'

            ),
            array(
                'id' => 5,
                'bank_numb' => 707,
                'bank_name' => 'Alfa Group',
                'bank_code' => 'alfa_group',
                'flow' => '2'
            ),
            array(
                'id' => 6,
                'bank_numb' => 400,
                'bank_name' => 'BRI Mobile Cash',
                'bank_code' => 'bri_mocash',
                'flow' => '2'
            ),
            array(
                'id' => 7,
                'bank_numb' => 401,
                'bank_name' => 'BRI ePay',
                'bank_code' => 'bri_epay',
                'flow' => '1'
            ),
            array(
                'id' => 8,
                'bank_numb' => 402,
                'bank_name' => 'VA Permata',
                'bank_code' => 'permata_va',
                'flow' => '2'
            ),
            array(
                'id' => 9,
                'bank_numb' => 402,
                'bank_name' => 'Permata Net',
                'bank_code' => 'permatanet',
                'flow' => '1'
            ),
            array(
                'id' => 10,
                'bank_numb' => 405,
                'bank_name' => 'BCA Klikpay',
                'bank_code' => 'bca_klikpay',
                'flow' => '1'
            ),
            array(
                'id' => 11,
                'bank_numb' => 406,
                'bank_name' => 'Mandiri Clickpay',
                'bank_code' => 'mandiri_clickpay',
                'flow' => '1'
            ),
            array(
                'id' => 12,
                'bank_numb' => 709,
                'bank_name' => 'Kredivo',
                'bank_code' => 'kredivo',
                'flow' => '1'
            ),
            array(
                'id' => 13,
                'bank_numb' => 408,
                'bank_name' => 'VA Maybank',
                'bank_code' => 'maybank_va',
                'flow' => '2'
            ),
            array(
                'id' => 14,
                'bank_numb' => 700,
                'bank_name' => 'CIMB Clicks',
                'bank_code' => 'cimb_clicks',
                'flow' => '1'
            ),
            array(
                'id' => 15,
                'bank_numb' => 701,
                'bank_name' => 'Danamon Online Banking',
                'bank_code' => 'dob',
                'flow' => '1'
            ),
            array(
                'id' => 16,
                'bank_numb' => 702,
                'bank_name' => 'VA BCA',
                'bank_code' => 'bca_va',
                'flow' => '2'
            ),
            array(
                'id' => 17,
                'bank_numb' => 703,
                'bank_name' => 'Mandiri Bill Payment',
                'bank_code' => 'mandiri_billpayment',
                'flow' => '2'
            ),
            array(
                'id' => 18,
                'bank_numb' => 704,
                'bank_name' => 'BCA Sakuku',
                'bank_code' => 'bca_sakuku',
                'flow' => '1'
            ),
            array(
                'id' => 19,
                'bank_numb' => 706,
                'bank_name' => 'Indomaret Payment Point',
                'bank_code' => 'indomaret_paymentpoint',
                'flow' => '2'
            ),
            array(
                'id' => 20,
                'bank_numb' => 800,
                'bank_name' => 'VA BRI',
                'bank_code' => 'bri_va',
                'flow' => '2'
            ),
            array(
                'id' => 21,
                'bank_numb' => 801,
                'bank_name' => 'VA BNI',
                'bank_code' => 'bni_va',
                'flow' => '2'
            ),
            array(
                'id' => 22,
                'bank_numb' => 802,
                'bank_name' => 'VA MANDIRI',
                'bank_code' => 'mandiri_va',
                'flow' => '2'
            ),
            array(
                'id' => 23,
                'bank_numb' => 814,
                'bank_name' => 'Maybank M2U',
                'bank_code' => 'maybank_m2u',
                'flow' => '1'
            ),
            array(
                'id' => 24,
                'bank_numb' => 807,
                'bank_name' => 'Akulaku',
                'bank_code' => 'akulaku',
                'flow' => '1'
            )

        );
        
        if (!Db::getInstance()->insert('banklist', $data)) {
            return false;
        }
    }


    public function installTables()
    {
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'banklist`');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'order_payment_faspay`');

        Db::getInstance()->Execute(
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'banklist` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `bank_numb` varchar(32) DEFAULT NULL,
            `bank_name` varchar(32) DEFAULT NULL,
            `bank_code` varchar(32) DEFAULT NULL,
            `flow` varchar(32) DEFAULT NULL,
             PRIMARY KEY (`id`)
            )ENGINE=MyISAM DEFAULT CHARSET=utf8  DEFAULT COLLATE utf8_general_ci  AUTO_INCREMENT=1 ;');

        Db::getInstance()->Execute(
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'order_payment_faspay` (
            `order_id` int(11) DEFAULT NULL,
            `trx_id` varchar(32) DEFAULT NULL,
            `payment_channel` varchar(32) DEFAULT NULL,
            `signature` varchar(32) DEFAULT NULL,
            `amount` varchar(12) DEFAULT NULL,
            `payment_status` varchar(32) DEFAULT NULL,
            `payment_date` timestamp NULL DEFAULT NULL,
            `payment_reff` varchar(32) DEFAULT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8  DEFAULT COLLATE utf8_general_ci;');
    }
    



    public function checkCurrency($cart)
    {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);

        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        return false;
    }
    public function hookPaymentOptions($params)
    {
        $cart = $this->context->cart;
        $cartProducts = $cart->getProducts(true);
        if (!$this->active) {
            return;
        }
        if (!$this->checkCurrency($params['cart'])) {
            return;
        }
        $payments = explode(',',Configuration::get('payment'));
        $this->context->smarty->assign([
            'cartProd' => $cartProducts,
            'status_mix' => Configuration::get('status_mix'),
            'min_3' => Configuration::get('min_3'),
            'min_6' => Configuration::get('min_6'),
            'min_12' => Configuration::get('min_12'),
            'min_24' => Configuration::get('min_24'),
            'status_3' => Configuration::get('status_3'),
            'status_6' => Configuration::get('status_6'),
            'status_12' => Configuration::get('status_12'),
            'status_24' => Configuration::get('status_24'),
        ]);
        $payment_options = array();
        foreach ($payments as $payment)
        {
            $externalOption = new PaymentOption();
            if($payment == "bca_klikpay")
            {
                
                    $externalOption->setCallToActionText($this->l(''))
                    ->setForm($this->generateForm())
                    ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/bca_klikpay.png'));
                    
            }
            else
            {
                if($payment =="dob")
                {
                    //$externalOption->setCallToActionText($this->l());
                }
                else
                {
                    //$externalOption->setCallToActionText($this->l(strtoupper(str_replace('_', '  ', $payment))));
                }
                $externalOption->setAction($this->context->link->getModuleLink('faspay', 'payment?pg='.$payment.'',array(), true));
                $externalOption->setLogo(_MODULE_DIR_ .$this->name.'/' . $payment. '.png');
            }
             $payment_options[] = $externalOption;
        }
       
        return $payment_options;
    }


    protected function generateForm()
    {
        return $this->context->smarty->fetch('module:faspay/views/templates/front/payment_return_klikpay2.tpl');
    }
    public function uninstall()
    {


        //Db::getInstance()->delete('ps_order_payment_faspay');
        //Db::getInstance()->delete('ps_banklist');

        if (!parent::uninstall() ||
            !Configuration::deleteByName('MYMODULE_NAME') ||
            !Configuration::deleteByName('merchant_id') ||
            !Configuration::deleteByName('merchant_name') ||
            !Configuration::deleteByName('order_expire') ||
            !Configuration::deleteByName('userid') ||
            !Configuration::deleteByName('userpswd') ||
            !Configuration::deleteByName('server') ||
            !Configuration::deleteByName('payment') ||
            !Configuration::deleteByName('add_fee') ||
            !Configuration::deleteByName('linkaja')  ||
            !Configuration::deleteByName('xltunai') ||
            !Configuration::deleteByName('alfa_group') ||
            !Configuration::deleteByName('ovo') ||
            !Configuration::deleteByName('danamon_va') ||
            !Configuration::deleteByName('bri_mocash') ||
            !Configuration::deleteByName('bri_epay') ||
            !Configuration::deleteByName('permata_va') ||
            !Configuration::deleteByName('permatanet') ||
            !Configuration::deleteByName('bca_klikpay') ||
            !Configuration::deleteByName('mandiri_clickpay')  ||
            !Configuration::deleteByName('kredivo') ||
            !Configuration::deleteByName('maybank_va') ||
            !Configuration::deleteByName('cimb_clicks') ||
            !Configuration::deleteByName('dob') ||
            !Configuration::deleteByName('bca_va') ||
            !Configuration::deleteByName('mandiri_billpayment') ||
            !Configuration::deleteByName('bca_sakuku') ||
            !Configuration::deleteByName('indomaret_paymentpoint') ||
            !Configuration::deleteByName('bri_va') ||
            !Configuration::deleteByName('bni_va')  ||
            !Configuration::deleteByName('mandiri_va') ||
            !Configuration::deleteByName('maybank_m2u') ||
            !Configuration::deleteByName('akulaku') ||
            !Configuration::deleteByName('linkaja_option') ||
            !Configuration::deleteByName('xltunai_option') ||
            !Configuration::deleteByName('alfa_group_option') ||
            !Configuration::deleteByName('ovo_option') ||
            !Configuration::deleteByName('danamon_va_option') ||
            !Configuration::deleteByName('bri_mocash_option') ||
            !Configuration::deleteByName('bri_epay_option') ||
            !Configuration::deleteByName('permata_va_option')  ||
            !Configuration::deleteByName('permatanet_option') ||
            !Configuration::deleteByName('bca_klikpay_option') ||
            !Configuration::deleteByName('mandiri_clickpay_option') ||
            !Configuration::deleteByName('kredivo_option') ||
            !Configuration::deleteByName('maybank_va_option') ||
            !Configuration::deleteByName('cimb_clicks_option') ||
            !Configuration::deleteByName('bca_sakuku') ||
            !Configuration::deleteByName('indomaret_paymentpoint') ||
            !Configuration::deleteByName('dob_option') ||
            !Configuration::deleteByName('bca_va_option')  ||
            !Configuration::deleteByName('mandiri_billpayment_option') ||
            !Configuration::deleteByName('bca_sakuku_option') ||
            !Configuration::deleteByName('indomaret_paymentpoint_option') ||
            !Configuration::deleteByName('bri_va_option') ||
            !Configuration::deleteByName('bni_va_option') ||
            !Configuration::deleteByName('mandiri_va_option') ||
            !Configuration::deleteByName('maybank_m2u_option') ||
            !Configuration::deleteByName('akulaku') ||
            !Configuration::deleteByName('clear_key') ||
            !Configuration::deleteByName('klikpay_code')  ||
            !Configuration::deleteByName('free_text_1') ||
            !Configuration::deleteByName('free_text_2') ||
            !Configuration::deleteByName('mid_full') ||
            !Configuration::deleteByName('mid_3') ||
            !Configuration::deleteByName('min_3') ||
            !Configuration::deleteByName('status_3') ||
            !Configuration::deleteByName('mid_6') ||
            !Configuration::deleteByName('min_6') ||
            !Configuration::deleteByName('status_6') ||
            !Configuration::deleteByName('mid_12')  ||
            !Configuration::deleteByName('min_12') ||
            !Configuration::deleteByName('status_12') ||
            !Configuration::deleteByName('mid_24') ||
            !Configuration::deleteByName('min_24') ||
            !Configuration::deleteByName('status_24') ||
            !Configuration::deleteByName('status_mix') ||
            !Db::getInstance()->Execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'banklist`') ||
            !Db::getInstance()->Execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'order_payment_faspay`')
        )

            return false;
        return true;
    }



    public function genSignature($klikPayCode, $transactionDate, $transactionNo, $amount, $currency, $keyId) {

        $tempKey1 = $klikPayCode . $transactionNo . $currency . $keyId;
        $hashKey1 = $this->getHash($tempKey1);
        $expDate = explode("/",substr($transactionDate,0,10));

        $strDate = $this->intval32bits($expDate[0] . $expDate[1] . $expDate[2]);
        $amt = $this->intval32bits($amount);
        $tempKey2 = $strDate + $amt;
        $hashKey2 = $this->getHash((string)$tempKey2);

        $signature = abs($hashKey1 + $hashKey2);

        return $signature;
    }
    public function genKeyId($clear_key) {
        return strtoupper(bin2hex($this->str2bin($clear_key)));
    }
    public function genAuthKey($klikPayCode, $transactionNo, $currency, $transactionDate, $keyId) {

        $klikPayCode = str_pad($klikPayCode, 10, "0");
        $transactionNo = str_pad($transactionNo, 18, "A");
        $currency = str_pad($currency, 5, "1");

        $value_1 = $klikPayCode . $transactionNo . $currency . $transactionDate . $keyId;

        $hash_value_1 = strtoupper(md5($value_1));

        if (strlen($keyId) == 32)
            $key = $keyId . substr($keyId,0,16);
        else if (strlen($keyId) == 48)
            $key = $keyId;

        return strtoupper(bin2hex(mcrypt_encrypt(MCRYPT_3DES, hex2bin($key), hex2bin($hash_value_1), MCRYPT_MODE_ECB)));
    }
    public function intval32bits($value) {
        if ($value > 2147483647)
            $value = ($value - 4294967296);
        else if ($value < -2147483648)
            $value = ($value + 4294967296);
        return $value;
    }
    public function getHash($value) {
        $h = 0;
        for ($i = 0;$i < strlen($value);$i++) {
            $h = $this->intval32bits($this->add31T($h) + ord($value{$i}));
        }
        return $h;
    }
    public function add31T($value) {
        $result = 0;
        for($i=1;$i <= 31;$i++) {
            $result = $this->intval32bits($result + $value);
        }
        return $result;
    }
    public function str2bin($data) {
        $len = strlen($data);
        return pack("a" . $len, $data);
    }
    public function convertBcaDate($date){
        $newDate = DateTime::createFromFormat('Y-m-d H:i:s', $date);

        return $newDate->format('d/m/Y H:i:s');
    }

}