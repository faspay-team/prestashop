<?php
/**
 * Created by PhpStorm.
 * User: faspaydityaFi
 * Date: 09/03/2018
 * Time: 9:48
 */

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

include_once(dirname(__FILE__).'/mid.php');

if (!defined('_PS_VERSION_'))
{
    exit;
}

class FaspayCC extends PaymentModule
{
    public 	$extra_mail_vars;
    public 	$midlist;
    private $_html = '';
    private $_postErrors = array();
    private $status;
    private $urlserver = "https://fpgdev.faspay.co.id/payment";
    private $urlinterface = "https://fpgdev.faspay.co.id/payment";
    private $_faspaycc = array('status' => 0, 'merchant_name' => null, 'auto_void' => false, 'server' => false);
    private $enabled = false;

    public function __construct() {
        $this->name 	= 'faspaycc';
        $this->tab 		= 'payments_gateways';
        $this->version 	= '2.0.0';
        $this->author  	= 'faspay';
        $this->limited_countries = array('id');
        $this->bootstrap = true;
        $this->displayBackOfficeTop = '';
        $this->displayBackOfficeHeader = false;

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        parent::__construct();
        $this->displayName = 'Faspay Credit Card';
        $this->description = 'Faspay Payment Gateways - Transaction secure, anytime, and anywhere';
        $this->confirmUninstall = $this->l('Are you sure you want to delete faspay account ?');

        if(Configuration::get('FASPAY_CC_STATUS') == 1){
            $this->_getMids();
            $this->_setInitialValue();
        }
        if (!count($this->midlist))
            $this->warning = $this->l('There must be at least one merchant id for this module');
        if (!count(Currency::checkPaymentCurrencies($this->id)))
            $this->warning = $this->l('No currency set for this module');
    }
    public function install() {
        Configuration::updateValue('FASPAY_CC_STATUS', 0);
        Configuration::updateValue('FASPAY_CC_MERCHANT_NAME', null);
        Configuration::updateValue('FASPAY_CC_SERVER', 0);
        Configuration::updateValue('FASPAY_CC_AUTO_VOID', 0);

        $orderstate = $this->_addFaspayOS();
        Configuration::updateValue('PS_OS_FASPAY_CC_PENDING', $orderstate);

        if (!parent::install() ||
            !$this->registerHook('paymentOptions') ||
            !$this->_createTabs()) return false;
        return true;
    }
    public function uninstall() {

        if (!$this->_removeFaspaycc() || !$this->_deleteTabs() || !parent::uninstall()) return false;
        else return true;
    }
    private function _updateMid(){

        $mids = array('status' 	 => Tools::getValue('cc_status'),
            'name' 	 => Tools::getValue('cc_title'),
            'logo' 	 => Tools::getValue('cc_logo'),
            'min_price' 	 => Tools::getValue('cc_min_price'),
            'mid'	 	 => Tools::getValue('cc_mid'),
            'password' => Tools::getValue('cc_pass'),
            'pymt_ind' => Tools::getValue('cc_pymt_ind'),
            'pymy_crt' => Tools::getValue('cc_pymy_crt'));

        Mid::truncate();
        for ($i = 0;$i < count($mids['status']);$i++) {
            $mid = new Mid();

            $mid->status = $mids['status'][$i] == 'on' ? true : false;
            $mid->mid = $mids['mid'][$i];
            $mid->logo = $mids['logo'][$i];
            $mid->min_price = $mids['min_price'][$i];
            $mid->password = $mids['password'][$i];
            $mid->name = $mids['name'][$i];
            $mid->pymt_ind = $mids['pymt_ind'][$i];
            $mid->pymt_crt = $mids['pymy_crt'][$i];

            $mid->add();
        }

        $this->_getMids();
    }
    private function _updateConfig(){
        $enabled 	= Tools::getValue('enabled');
        $name 		= Tools::getValue('merchant_name');
        $server 	= Tools::getValue('server');
        $auto_void 	= Tools::getValue('auto_void') == "on" ? true : false;

        Configuration::updateValue('FASPAY_CC_STATUS', $enabled);
        Configuration::updateValue('FASPAY_CC_MERCHANT_NAME', $name);
        Configuration::updateValue('FASPAY_CC_SERVER', $server);
        Configuration::updateValue('FASPAY_CC_AUTO_VOID', $auto_void);

        $this->_setInitialValue();
    }
    private function _deleteTabs(){
        Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'faspay_cc_config');
        return true;
    }
    private function _createTabs(){
        $query = "CREATE TABLE `"._DB_PREFIX_."faspay_cc_config` (
		  `mid` varchar(32) DEFAULT NULL,
		  `min_price` varchar(32) DEFAULT NULL,
		  `logo` varchar(32) DEFAULT NULL,
		  `password` varchar(32) DEFAULT NULL,
		  `name` varchar(32) DEFAULT NULL,
		  `pymt_ind` varchar(32) DEFAULT NULL,
		  `pymt_crt` varchar(32) NULL DEFAULT NULL,
		  `status` boolean DEFAULT NULL
		) ENGINE = MyISAM";

        Db::getInstance()->Execute($query);
        return true;
    }
    public function getContent() {
        global $smarty;

        if (Tools::isSubmit('btnSubmit')) {
            $this->_updateMid();
            $this->_updateConfig();
        }

        $current_url = $this->context->link->getAdminLink('AdminModules').'&configure=faspaycc&tab_module=payments_gateways&module_name=faspaycc';

        $smarty->assign('config', $this->_faspaycc);
        $smarty->assign('mids', $this->midlist);
        $smarty->assign('current_url', $current_url);

        return $this->display(dirname(__FILE__), '../admin/config.tpl');
    }
    private function _setInitialValue(){
        $config = Configuration::getmultiple(array('FASPAY_CC_STATUS',
            'FASPAY_CC_MERCHANT_NAME',
            'FASPAY_CC_SERVER',
            'FASPAY_CC_AUTO_VOID'));

        if($config['FASPAY_CC_STATUS'])			$this->_faspaycc['status'] = $config['FASPAY_CC_STATUS'];
        if($config['FASPAY_CC_MERCHANT_NAME'])	$this->_faspaycc['merchant_name'] = $config['FASPAY_CC_MERCHANT_NAME'];
        if($config['FASPAY_CC_SERVER'])			$this->_faspaycc['server'] = $config['FASPAY_CC_SERVER'];
        if($config['FASPAY_CC_AUTO_VOID'])		$this->_faspaycc['auto_void'] = $config['FASPAY_CC_AUTO_VOID'];
        if($this->_faspaycc["server"] == 1)
        {
            $this->urlserver = "https://fpg.faspay.co.id/payment";
            $this->urlinterface = "https://fpg.faspay.co.id/payment";
        }
        foreach ($this->midlist as $key => $value) {
            if($value['status'] == 1) $this->enabled = true;
        }
    }
    private function _getMids(){
        $query = "SELECT * FROM "._DB_PREFIX_."faspay_cc_config";
        $this->midlist = Db::getInstance()->Executes($query);

    }
    public function hookPaymentOptions($params) {
        if (!$this->active) {
            return;
        }
        if (!$this->checkCurrency($params['cart'])) {
            return;
        }
        $this->smarty->assign(array(
            'this_path' => $this->_path,
            'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/',
            'midlist' => $this->midlist,
            'pgexist'=> $this->enabled
        ));
        $midlist = $this->midlist;

        $cart = $this->context->cart;

        $pay_total = $cart->getOrderTotal(true, Cart::BOTH);
        $payment_options = array();

        foreach ($midlist as $item => $pg)
        {
            if($pg['status'] == 1 && $pg['min_price'] <= $pay_total)
            {
                $externalOption = new PaymentOption();
                $externalOption->setCallToActionText($this->l($pg['name']));
                $externalOption->setAction($this->context->link->getModuleLink('faspaycc', 'payment?pg='.strtolower($pg['name']).'',array(), true));
                $externalOption->setLogo(_MODULE_DIR_ .$this->name.'/' .$pg['logo']. '.png');
                $payment_options[] = $externalOption;
            }
        }


        return $payment_options;
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
    private function _addFaspayOS(){
        $orderState = new OrderState(14);
        // $orderstate->id = 14;
        foreach (Language::getLanguages() AS $language)
        {
            if (strtolower($language['iso_code']) == 'id')
                $orderState->name[$language['id_lang']] = 'Payment Pending Hubungi Administrator';
            else
                $orderState->name[$language['id_lang']] = 'Payment Pending Contact Administrator';
        }

        $OrderState->unremovable = false;
        $orderState->send_email = false;
        $orderState->color = '#DDEEFF';
        $orderState->hidden = false;
        $orderState->delivery = false;
        $orderState->logable = true;
        $orderState->invoice = true;
        if ($orderState->add())
            return $orderState->id;
    }
    private function _removeFaspaycc(){

        $orderState = new OrderState(Configuration::get('PS_OS_FASPAY_CC_PENDING'));
        !Configuration::deleteByName('FASPAY_CC_STATUS');
        !Configuration::deleteByName('MYMODULE_NAME');

        if ($orderState->delete() ||
            Configuration::deleteByName('FASPAY_CC_STATUS') ||
            Configuration::deleteByName('FASPAY_CC_MERCHANT_NAME') ||
            Configuration::deleteByName('FASPAY_CC_SERVER') ||
            Configuration::deleteByName('FASPAY_CC_AUTO_VOID')
        ) return true;
    }

}