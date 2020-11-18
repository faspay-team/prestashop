<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class FaspayThankyouModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    public function initContent()
    {
        parent::initContent();
        $my_module_user = Configuration::get('userid');
        $my_module_password = Configuration::get('userpswd');
        $my_module_id = Configuration::get('merchant_id');
        $order_id = Tools::getValue('bill_no');
        $trx_id = Tools::getValue('trx_id');
        $pay_date = Tools::getValue('payment_date');
        $signature = sha1(md5($my_module_user.$my_module_password.$order_id));

        $id_orders=Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'orders` 
            WHERE id_cart = ('.$order_id.')
            ');
        $id_order = $id_orders[0]['id_order'];
        if(Configuration::get('server') == "production")
        {
                $url = 'https://web.faspay.co.id/pws/100004/383xx00010100000';
        }
        else
        {
                $url = 'https://dev.faspay.co.id/pws/100004/183xx00010100000';

        }
            // $url = 'https://dev.faspay.co.id/pws/100004/183xx00010100000';
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<faspay>'."\n";
            $xml .= "<request>Inquiry Status Payment</request>"."\n";
            $xml .= "<trx_id>".$trx_id."</trx_id>"."\n";
            $xml .= "<merchant_id>". $my_module_id ."</merchant_id>"."\n";
            $xml .= "<bill_no>". $order_id ."</bill_no>"."\n";
            $xml .= "<signature>".$signature."</signature>"."\n";
            $xml .= "</faspay>" ."\n";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
            $result = curl_exec($ch);
            $xml = simplexml_load_string($result);
            $status = $xml->payment_status_desc;


            if($status == "Payment Sukses")
            {
                $this->update($id_order, $trx_id,2,$status,$pay_date);
                $message = "Your Payment process with the following order id = $id_order has been succeed";
            }
            elseif($status == "Payment Gagal")
            {
                $this->update($id_order, $trx_id,8,$status,$pay_date);

                $message = "Your Payment process with the following order id = $id_order has been failed, please try again later or contact your merchant if still facing same difficulties";
            }
            elseif($status == "Dalam proses")
            {
                $this->update($id_order, $trx_id,1,$status,$pay_date);

                $message = "Your Payment with the following order id = $id_order still on process";
            }
            elseif($status == "Belum diproses")
            {
                $this->update($id_order, $trx_id,1,$status,$pay_date);

                $message = "Your Payment with the following order id = $id_order still not yet processed";
            }


            $this->context->smarty->assign([
                'message' => $message,
                //'tpl_dir' => Tools::getHttpHost(true).__PS_BASE_URI__,
            ]);

            $this->setTemplate('module:faspay/views/templates/front/thankyou.tpl');
    }

    public function update($order,$trx,$stat,$status,$date)
    {
        $references = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'orders` 
            WHERE id_order = ('.$order.')
            ');
        $reference = $references[0]['reference'];
        $order_state = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT current_state FROM `'._DB_PREFIX_.'orders` 
            WHERE id_order = ('.$order.')
            ');
        
        $history_state = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_order_state FROM `'._DB_PREFIX_.'order_history` 
            WHERE id_order = ('.$order.')
            ');

        if($order_state && $history_state != $stat)
        {

            //Update database order untuk backend admin
            $sql_order = "update 	"._DB_PREFIX_."orders 
            set current_state=('$stat')
	        where id_order = ('$order')";
            Db::getInstance()->execute($sql_order);

            //Update database history untuk frontend user
            $sql_history = "update 	"._DB_PREFIX_."order_history 
            set id_order_state=('$stat')
	        where id_order = ('$order')";
            Db::getInstance()->execute($sql_history);

            //Update database payment untuk faspay DB
            $sql_payment = "update 	"._DB_PREFIX_."order_payment_faspay 
            set payment_status=('$status'), payment_reff=('$reference'), payment_date=('$date')
	        where trx_id = ('$trx')";
            Db::getInstance()->execute($sql_payment);
        }


    }

}
