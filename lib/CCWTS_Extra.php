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
    }
    global $gschema;
    return $gschema->$plural->sgl;*/
}

function ccwts_label($entity_name){
   $labels = array(
        'ticket'            =>  'biglietto',
        'tickets'           =>  'biglietti',
        'ticketsale'        =>  'biglietteria',
        'ticketsales'       =>  'biglietterie',
        'service'           =>  'servizio',
        'services'          =>  'servizi',
        'users'             =>  'Utenti'
    );
    return $labels[$entity_name];
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



/*function ccwts_global_field($field_name, $value=null){
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
}*/
