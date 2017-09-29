<?php

function ccwts_sgl($plural){
    /*$singulars = array(
        'ticketsales'       =>  'ticketsale',
        'tickets'           =>  'ticket',
        'servicecategories' =>  'servicecategory',
        'ticketcategories'  =>  'ticketcategory',
    );
    if(!empty($singulars[$plural])){
        return $singulars[$plural];
    }else{
        return preg_replace('/s$/', '', $plural);
    }*/
    global $gschema;
    return $gschema->$plural->sgl;
}

function ccwts_label($entity_name){
   $labels = array(
        'ticket'            =>  'biglietto',
        'tickets'           =>  'biglietti',
        'ticketsale'        =>  'biglietteria',
        'ticketsales'       =>  'biglietterie',
        'service'           =>  'servizio',
        'services'          =>  'servizi',
        'users'   =>  'Utenti',
        'servicecategories' =>  'categorie servizio',
        'ticketcategory'    =>  'categoria biglietto',
        'ticketcategories'  =>  'categorie biglietto',
    );
    return $labels[$entity_name];
    /*global $gschema;
    if($gschema->$entity_name){
        return $gschema->$entity_name->label;
    }else{
        foreach($gschema as $k=>$v){ // FIXME: Crazy - to rewrite!
            if($v->sgl == $entity_name){
                return $gschema->$k->label_sgl;
            }
        }
    }*/
}

function ccwts_classnames($entity_name){
    // $labels = array(
    //     'ticket'        =>  'Ticket',
    //     'tickets'       =>  'Ticket',
    //     'ticketsale'    =>  'TicketSale',
    //     'ticketsales'   =>  'TicketSale',
    //     'service'       =>  'Service',
    //     'services'      =>  'Service',
    //     'orders'        =>  'Order',
    //     'order'         =>  'Order',
    // );
    // return $labels[$entity_name];
    global $gschema;
    return $gschema->$entity_name->classname;
}

function ccwts_include_page($page){
    $filename = plugin_dir_path(__FILE__).'../pages/'.$page.'.php';
    if(file_exists($filename)){
        include($filename);
        return true;
    }else{
        return false;
    }
}

/*function ccwts_include_table($table){
    $filename = plugin_dir_path(__FILE__).'../tables/'.$table.'.php';
    if(file_exists($filename)){
        include($filename);
        return true;
    }else{
        return false;
    }
}*/



function ccwts_notice_success($msg){
    ?><div class="notice notice-success is-dismissible">
        <p><?php _e( $msg, 'ccwts_text' ); ?></p>
    </div><?php
}

function ccwts_notice_failure($msg){
    ?><div class="notice notice-error is-dismissible">
        <p><?php _e( $msg, 'ccwts_text' ); ?></p>
    </div><?php
}

function ccwts_global_field($field_name, $value=null){
    if($value){ // we are saving
        return update_option(sprintf('ccwts_%s', $field_name), $value);
    }else{ // we are retrieving
        return get_option(sprintf('ccwts_%s', $field_name), null);
    }
}

function ccwts_field($field_name, $value=null){
    if($value){ // we are saving
        return update_post_meta(get_the_ID(), sprintf('ccwts_%s', $field_name), $value);
    }else{ // we are retrieving
        $result = get_post_meta(get_the_ID(), sprintf('ccwts_%s', $field_name), true);
        if(!empty($result)){
            return $result;
        }else{
            return null;
        }
    }
}
