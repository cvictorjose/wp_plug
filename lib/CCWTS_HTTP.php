<?php
function ticketServer_remote($uri, $params=array()){
    global $ws_url;
    var_dump($ws_url.$uri);

    $ca_path = plugin_dir_path(__FILE__) . 'cert/ca.crt';
    $key_path= plugin_dir_path(__FILE__) . 'cert/shop_1.key';
    $crt_path= plugin_dir_path(__FILE__) . 'cert/shop_1.crt';

    $ch = curl_init($ws_url.$uri);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSLKEY, $key_path);
    curl_setopt($ch, CURLOPT_CAINFO, $ca_path);
    curl_setopt($ch, CURLOPT_SSLCERT, $crt_path);

    $result = ($result = curl_exec($ch)) ? $result : curl_error($ch);
    if(!$result){
        return array('body'=>json_encode(array('data'=>$result->get_error_message(),'code'=>500)));
    }else{
        return $result;
    }
    curl_close($ch);

    //error_log('RESPONSE: '.print_r($response, true));
//    if(is_wp_error($response)){
//        return array('body'=>json_encode(array('data'=>$response->get_error_message(),'code'=>500)));
//    }else{
//        return $response;
//    }
}


function ticketServer_get($uri){
    try{
        $response = ticketServer_remote($uri);
        $array = json_decode($response['body']);
        return $response;
    }catch(Exception $e){
        return array();
    }
}




/*function ccwts_remote_post($uri, $params){
    global $ws_url;
    global $laravel_session;
    $cookies = array(
        'laravel_session'   =>  $laravel_session
    );
    $response = wp_remote_post($ws_url.$uri, array('timeout'=>1200, 'cookies' => $cookies, 'body' => $params));
    //error_log('RESPONSE: '.print_r($response, true));
    return $response;
}

function ccwts_remote_put($uri, $params){
    global $ws_url;
    global $laravel_session;
    $cookies = array(
        'laravel_session'   =>  $laravel_session
    );
    $args = array(
                'method'    =>  'PUT',
                'headers'   =>  array(
                    'accept'        =>  'application/json',           
                    'content-type'  =>  'application/json',
                ),
                'cookies'   =>  $cookies,
                'body'      =>  json_encode($params),
    );
    $response = wp_remote_request($ws_url.$uri, $args);
    return $response;
}

function ccwts_remote_delete($uri){
    global $ws_url;
    global $laravel_session;
    $cookies = array(
        'laravel_session'   =>  $laravel_session
    );
    $args = array(
                'method'    =>  'DELETE',
                'cookies'   =>  $cookies,
    );
    $response = wp_remote_request($ws_url.$uri, $args);
    return $response;
}*/

function ccwts_get($uri){
    try{
        $response = ccwts_remote_get($uri);
        $array = json_decode($response['body']);
        return $array->data;
    }catch(Exception $e){
        return array();
    }
}



function ccwts_post($uri, $params){
    global $xsrf_token;
    $params['_token'] = $xsrf_token;
    $response = ccwts_remote_post($uri, $params);
    $array = json_decode($response['body']);
    return $array->data;
}

function ccwts_post_empty($uri, $response=false){
    global $xsrf_token;
    $params = array('_token' => $xsrf_token);
    $r = ccwts_remote_post($uri, $params);
    if($response){
        return $r;
    }else{
        if($r['response']['code'] == '200' && $r['response']['message'] == 'OK'){
            return true;
        }else{
            return false;
        }
    }
}

function ccwts_remote_login(){
    global $ws_email;
    global $ws_password;
    $login_response = ccwts_remote_post('/login', array(
        'email'     =>  $ws_email,
        'password'  =>  $ws_password,
    ));
    $response = array();
    foreach($login_response['cookies'] as $cookie){
        if($cookie->name == 'laravel_session'){
            $response[$cookie->name] = $cookie->value;
        }
    }
    $login_response_body = json_decode($login_response['body']);
    $response['xsrf_token'] = $login_response_body->data->xsrf_token;
    return $response;
}


function ccwts_session(){ // new version, using session
    session_start();
    global $laravel_session;
    global $xsrf_token;
    $laravel_session = $_SESSION['ccwts_session'];
    $xsrf_token = $_SESSION['xsrf_token'];
    $login_status = ccwts_get('/login');
    if($login_status->session_cookie!=''){
        $_SESSION['ccwts_session']  =   $login_status->session_cookie;
        $_SESSION['xsrf_token']     =   $login_status->xsrf_token;
    }else{
        $login_response = ccwts_remote_login();
        $_SESSION['ccwts_session']  =   $login_response['laravel_session'];
        $_SESSION['xsrf_token']     =   $login_response['xsrf_token'];
    }
    $laravel_session = $_SESSION['ccwts_session'];
    $xsrf_token = $_SESSION['xsrf_token'];
    // Getting schema
    global $gschema;
    $gschema = ccwts_schema();
}

function ccwts_session_destroy(){
    $logout = ccwts_get('/logout');
    print_r($logout);
}

function ccwts_schema($entity=null){
    if($entity){
        $response = ccwts_remote_get('/schema/'.$entity);
    }else{
        $response = ccwts_remote_get('/schema');
    }
    $array = json_decode($response['body']);
    return $array->meta;
}
