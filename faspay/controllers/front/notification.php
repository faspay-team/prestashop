<?php
        $xml = simplexml_load_string(file_get_contents('php://input'));

        if($xml)
        {
            $trx_id = $xml->trx_id;
            $order_stat = $xml->payment_status_code;
            $pay_stat = $xml->payment_status_desc;
            $pay_date = $xml->payment_date;
            $signature = $xml->signature;
            $mid = $xml->merchant_id;
            $my_module_user = Configuration::get('userid');
            $my_module_password = Configuration::get('userpswd');
            $bill_no = $xml->bill_no;

            $query=Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'order_payment_faspay` 
            WHERE order_id = ('.$bill_no.')
            ');

            $trx_ids = $query[0]['trx_id'];

            $signature2 = sha1(md5($my_module_user.$my_module_password.$bill_no.$order_stat));
            
                $xml ="<faspay>";
                $xml.="<response>Payment Notification</response>";
                $xml.="<trx_id>".$trx_id."</trx_id>";
                $xml.="<merchant_id>".$mid."</merchant_id>";
                $xml.="<bill_no>".$bill_no."</bill_no>";
                if($trx_id != $trx_ids)
                {
                    $xml.="<response_code>99</response_code>";
                    $xml.="<response_desc>Invalid Signature</response_desc>";
                }
                 else if($signature != $signature2)
                {
                    $xml.="<response_code>99</response_code>";
                    $xml.="<response_desc>Invalid Signature</response_desc>";
                }
                else
                {
                    $xml.="<response_code>00</response_code>";
                    $xml.="<response_desc>Payment Sukses</response_desc>";
                }
                    $xml.="<response_date>".Date("Y-m-d H:i:s")."</response_date>";
                    $xml.="</faspay>";
                    echo $xml;
    
            $reference=Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT reference FROM `'._DB_PREFIX_.'orders`
            WHERE id_cart = ('.$bill_no.')');
                $reference = $reference[0]['reference'];


                //Update database order untuk backend admin
                $sql_order = "update 	"._DB_PREFIX_."orders 
            set current_state=('$order_stat')
	        where id_cart = ('$bill_no')";
                Db::getInstance()->execute($sql_order);

                //Update database history untuk frontend user
                $sql_history = "update 	"._DB_PREFIX_."order_history 
            set id_order_state=('$order_stat')
	        where id_order_history = ('$bill_no')";
                Db::getInstance()->execute($sql_history);
                //Update database payment untuk faspay DB

                $sql_payment = "update 	"._DB_PREFIX_."order_payment_faspay 
            set payment_status=('$pay_stat'), payment_reff=('$reference'), payment_date=('$pay_date')
	        where trx_id = ('$trx_id')";
                Db::getInstance()->execute($sql_payment);

            
        }
        else
        {
            echo "This is for notification only";
        }

    

   
        
        
exit;
  