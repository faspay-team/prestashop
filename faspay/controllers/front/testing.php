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
class FaspayTestingModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    public function initContent()
    {
        parent::initContent();
        $url = 'http://localhost/newpresta/en/module/faspay/notification';
        $xml = "<faspay>"."\n";
        $xml .= "<request>Payment Notification</request>"."\n";
        $xml .= "<trx_id>8985999900038349</trx_id>"."\n";
        $xml .= "<merchant_id>99999</merchant_id>"."\n";
        $xml .= "<merchant>FASPAY STORE</merchant>"."\n";
        $xml .= "<bill_no>473</bill_no>"."\n";
        $xml .= "<payment_reff>null</payment_reff>"."\n";
        $xml .= "<payment_date>2018-05-08 14:46:45</payment_date>"."\n";
        $xml .= "<payment_status_code>2</payment_status_code>"."\n";
        $xml .= "<payment_status_desc>Payment Sukses</payment_status_desc>"."\n";
        $xml .= "<signature>1f481afadffd1d542172796ca5fb97fd6e1bc138</signature>"."\n";
        $xml .= "</faspay>"."\n";
        $ch = curl_init();
        $curl_options = array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => array('Content-Type: text/xml'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1
        );
        if($xml)
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        }
        else
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS, '');
        }
        curl_setopt_array($ch, $curl_options);
        $result = curl_exec($ch);

        echo $result;
    }
}
