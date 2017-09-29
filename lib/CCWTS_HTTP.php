<?php
function ticketServer_remote($uri, $method){
    global $ws_url,$ca_path,$key_path ,$crt_path;

    $ch = curl_init($ws_url.$uri);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSLKEY, $key_path);
    curl_setopt($ch, CURLOPT_CAINFO, $ca_path);
    curl_setopt($ch, CURLOPT_SSLCERT, $crt_path);

     switch ($method) {
        case "post":
            $data= '{"seats":[{"event_id":"30","reservation_id":"600","area_code":"SB","seat_row":"1","seat_num":"8",
            "title_type":"I1"}],"id_cash_desk":"07639182","deadline":5}';
            /* config post */
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );
            break;
    }

    $result = ($result = curl_exec($ch)) ? $result : curl_error($ch);
    if(!$result){
        return array('body'=>json_encode(array('data'=>$result->get_error_message(),'code'=>500)));
    }else{
        return $result;
    }
    curl_close($ch);

    //error_log('RESPONSE: '.print_r($response, true));
}


function ticketServer($uri){
    try{
        $response = ticketServer_remote($uri,"post");
        //$array = json_decode($response['body']);
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
