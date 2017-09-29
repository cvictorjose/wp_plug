<?php
global $ws_url;
$ws_url = 'https://jsonplaceholder.typicode.com';

global $ws_email;
$ws_email = 'm.cvictorjose@gmail.com';
global $ws_password;
$ws_password = 'diy2017!';
global $xsrf_token;
global $gschema;

/*

$url="https://ts.alligator.ticket/1/reservation/1"; // url API funzione test TS
$ca_path = plugin_dir_path(__FILE__) . '/lib/cert/ca.crt';
$key_path= plugin_dir_path(__FILE__) . '/lib/cert/shop_1.key';
$crt_path= plugin_dir_path(__FILE__) . '/lib/cert/shop_1.crt';

///* config cUrl */
//$ch = curl_init($url);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_SSLKEY, $key_path);
//curl_setopt($ch, CURLOPT_CAINFO, $ca_path);
//curl_setopt($ch, CURLOPT_SSLCERT, $crt_path);
//
//$data= '{"seats":[{"event_id":"30","reservation_id":"600","area_code":"SB","seat_row":"1","seat_num":"8",
//"title_type":"I1"}],"id_cash_desk":"07639182","deadline":600}';
///* config post */
//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//        'Content-Type: application/json',
//        'Content-Length: ' . strlen($data))
//);
//
///* chiamata cUrl */
//$result = ($result = curl_exec($ch)) ? $result : curl_error($ch);
//
//if(!$result)
//{
//    echo "Curl Error: " . curl_error($ch);
//}
//else
//{
//    echo $result;
//}
//curl_close($ch);*/


