<?php
function ccwts_remote_get($uri, $params=array()){
    global $ws_url;
    global $laravel_session;
    $cookies = array(
        'laravel_session'   =>  $laravel_session
    );
    $args = array('timeout'=>1200, 'cookies' => $cookies);
    if(!empty($params)){
        $args['body'] = $params;
    }
    $response = wp_remote_get($ws_url.$uri, $args);
    //error_log('RESPONSE: '.print_r($response, true));
    if(is_wp_error($response)){
        return array('body'=>json_encode(array('data'=>$response->get_error_message(),
                                               'code'=>500)));
    }else{
        return $response;
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

function ccwts_get_2($uri){
    try{
        $response = ccwts_remote_get($uri);
        $array = json_decode($response['body']);
        return $response;
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

/*function ccwts_session(){ // old version, using transient
    global $laravel_session;
    $laravel_session = urlencode(get_transient('ccwts_session'));
    $login_status = ccwts_get('/login');
    //print_r($login_status);
    if($login_status->session_cookie!=''){
        if($laravel_session != $login_status->session_cookie){
            set_transient( 'ccwts_session', $login_status->session_cookie, 60*60*2 );
            $laravel_session = $login_status->session_cookie;
        }
        //print 'SESSION COOKIE: '.$login_status->session_cookie;
    }else{
        $session_cookie = ccwts_remote_login();
        set_transient( 'ccwts_session', $session_cookie, 60*60*2 );
        $laravel_session = $session_cookie;
        //print 'REMOTE LOGIN: '.urlencode($session_cookie);
    }
    //print 'LARAVEL_SESSION: '.$laravel_session;
    // Getting schema
    global $gschema;
    $gschema = ccwts_schema();
}*/

/*function ccwts_session(){ // new version, using cookie
    global $laravel_session;
    global $xsrf_token;
    //$laravel_session = urlencode($_COOKIE['ccwts_session']);
    $laravel_session = $_COOKIE['ccwts_session'];
    $xsrf_token = $_COOKIE['xsrf_token'];
    $login_status = ccwts_get('/login');
    if($login_status->session_cookie!=''){
        //if($laravel_session != $login_status->session_cookie){
            setcookie('ccwts_session', $login_status->session_cookie, time()+7200);
            setcookie('xsrf_token', $login_status->xsrf_token, time()+7200);
            $laravel_session = $login_status->session_cookie;
            $xsrf_token = $login_status->xsrf_token;
        //}
    }else{
        $login_response = ccwts_remote_login();
        setcookie('ccwts_session', $login_response['laravel_session'], time()+7200);
        setcookie('xsrf_token', $login_response['xsrf_token'], time()+7200);
        $laravel_session    = $login_response['laravel_session'];
        $xsrf_token         = $login_response['xsrf_token'];
    }
    // Getting schema
    global $gschema;
    $gschema = ccwts_schema();
}*/

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
