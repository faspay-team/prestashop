<?php


class FaspayCCPaymentReturnModuleFrontController extends ModuleFrontController
{

    public $display_column_left = false;
    public $ssl = true;
    public function initContent()
    {
        parent::initContent();
        $xml = file_get_contents('php://input');
        $lines	= explode('&',$xml);
        $response = array();
        foreach ($lines as $key) {
            list($key, $value) = explode('=', $key);
            $response[trim($key)] = trim($value);
        }

        $signature_respon = $response['SIGNATURE'];
        $server = (Configuration::get('FASPAY_CC_SERVER')) ? 'prod' : 'dev';
        $url    = (Configuration::get('FASPAY_CC_SERVER')) ? 'https://fpg.faspay.co.id/payment/api' : 'https://fpgdev.faspay.co.id/payment/api';

        $query = "SELECT * FROM "._DB_PREFIX_."faspay_cc_config WHERE mid = '".$response['MERCHANTID']."'";
        $result = Db::getInstance()->executeS($query)[0];
        $signaturecc=strtoupper(sha1('##'.strtoupper($result['mid']).'##'.strtoupper($result['password']).'##'.$response["MERCHANT_TRANID"].'##'.$response['AMOUNT'].'##'.''.$response['TXN_STATUS'].''.'##'));


            $MERCHANT_TRANID = $response["MERCHANT_TRANID"];
            $status = $response['TXN_STATUS'];
            
            if($status == "CF" || $status == "P" || $status == "N" || $status == "A")
            {
                $this->processPayment($MERCHANT_TRANID, Configuration::get('PS_OS_FASPAY_CC_PENDING'));
                $status = "Your Payment with the following order id = $MERCHANT_TRANID still on process";
            }
            else if($status == "C" || $status == "S")
            {
                $this->processPayment($MERCHANT_TRANID, Configuration::get('PS_OS_WS_PAYMENT'));
                $status = "Your Payment process with the following order id = $MERCHANT_TRANID has been succeed";
            }
            else if($status == "B" || $status == "F"|| $status == "V")
            {
                $this->processPayment($MERCHANT_TRANID, Configuration::get('PS_OS_CANCELED'));
                $status = "Your Payment process with the following order id = $MERCHANT_TRANID has been failed";
            }
            else
            {
                $this->processPayment($MERCHANT_TRANID, Configuration::get('PS_OS_ERROR'));
                $status = "There has been an error on processing your request with the following order id = $MERCHANT_TRANID ";
            }

            $this->context->smarty->assign([
                'status' => $status,
                'tpl_dir' => Tools::getHttpHost(true).__PS_BASE_URI__,
            ]);
            
            $this->setTemplate('module:faspaycc/views/templates/front/payment_return.tpl');
        }
    private function processPayment($id_order, $id_order_state){
        $order = new Order((int)$id_order);

        if (!Validate::isLoadedObject($order))
        {
            $this->errors[] = sprintf(Tools::displayError('Order #%d cannot be loaded'), $id_order);
        }
        else
        {
            $current_order_state = $order->getCurrentOrderState();
            $order_state = new OrderState($id_order_state);

            if($current_order_state->id == $order_state->id){
                $response['message'] = "This order has been processed";
            }else{

                $history = new OrderHistory();
                $history->id_order = $order->id;
                $history->changeIdOrderState((int)$order_state->id, $order);
                $history->add();
            }
        }
    }

}