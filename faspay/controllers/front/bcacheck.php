<?php
        $utils = new Faspay();
        $keyid = $utils->genKeyId(Configuration::get('clear_key'));

        //Ambil order ID sesuai TRX yang didapat
        $ids=Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'order_payment_faspay` 
            WHERE trx_id = ('.$_GET['trx_id'].')
            ');

        $id_order = $ids[0]['order_id'];
        $amount = $ids[0]['amount'];


        //Ambil Date dari Cart Table
        $query2=Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'cart` 
            WHERE id_cart = ('.$id_order.')
            ');

        $date_add = $query2[0]['date_upd'];

        $bcaDate = $utils->convertBcaDate($date_add);

        $total = $amount;
        $klik_code = Configuration::get('klikpay_code');

        if(isset($_GET['trx_id']) && (isset($_GET['signature']) || isset($_GET['authkey'])))
        {
            $reqSignature = isset($_GET['signature']) ? $_GET['signature'] : '';
            $reqAuthkey   = isset($_GET['authkey']) ? $_GET['authkey'] : '';
            $sig        = $utils->genSignature($klik_code,$bcaDate, $_GET['trx_id'], $total, 'IDR', $keyid);
            $authkey    = $utils->genAuthKey($klik_code, $_GET['trx_id'], 'IDR', $bcaDate, $keyid);
            if($sig == $reqSignature || $authkey == $reqAuthkey)
            {
                echo 1;
            }
            else
            {
                echo 0;
            }
        }

        else{
            echo 'THIS PAGE IS FOR CHECK AUTHKEY/SIGNATURE ONLY';
}

exit;
