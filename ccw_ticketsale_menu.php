<?php

class CCWTS_Menu{

    public function __construct(){
        add_action('admin_menu', array($this, 'register_pages'));
    }

    public function register_pages(){
       add_menu_page( 
            __( 'TicketSale', 'ccwts_text' ),
            'TicketSale',
            'manage_options',
            'ticketsales',
            array($this, 'show_template'),
            'dashicons-chart-pie',
            6
        );
        add_submenu_page( 
            'ticketsales',
            __( 'Users', 'ccwts_text' ),
            'Users',
            'manage_options',
            'users',
            array($this, 'show_template')
        );
       
       
        /* $schema = ccwts_schema();
        foreach($schema as $k => $v){
            if($v->crud){
                if(!$v->parent){
                    add_menu_page( 
                        __( ucfirst($v->label), 'ccwts_text' ),
                        ucfirst($v->label),
                        'manage_options',
                        $k,
                        array($this, 'show_template'),
                        'dashicons-chart-pie',
                        6
                    );
                }
            }
        }
        foreach($schema as $k => $v){
            if($v->crud){
                if($v->parent){
                    $parent = (!$v->hidden) ? $v->parent : null;
                    add_submenu_page( 
                        $parent,
                        __( ucfirst($v->label), 'ccwts_text' ),
                        ucfirst($v->label),
                        'manage_options',
                        $k,
                        array($this, 'show_template')
                    );
                }
            }
        } */
    }

    public function show_template(){
        if(!empty($_REQUEST['page'])){
            if(ccwts_include_page($_REQUEST['page'])){
            // if($_REQUEST['action']=='edit' || $_REQUEST['action']=='create' || $_REQUEST['action']=='delete' || strstr($_REQUEST['action'], 'manage')){
            //     ccwts_include_page(ccwts_sgl($_REQUEST['page']));
            // }else{
            //     ccwts_include_page($_REQUEST['page']);
            // }
                $entities_ctrl_class_name = sprintf('CCWTS_%s_Controller', ccwts_classnames($_REQUEST['page']));
                if(class_exists($entities_ctrl_class_name)){
                    $ctrl = new $entities_ctrl_class_name();
                }else{
                    $ctrl = new CCWTS_Controller();
                }
            }else{
                $ctrl = new CCWTS_Controller();
            }
            $ctrl->listen();
        }
    }

    // public function page_ticketsales(){
    //   if(intval($_REQUEST['ticketsale'])>0){
    //     ccwts_include_page("ticketsale");
    //   }else{
    //     ccwts_include_page("ticketsales");
    //   }
    // }

    // public function page_rates(){
    //     ccwts_include_page('rates');
    // }

    // public function page_tickets($argg){
    //   if(intval($_REQUEST['ticket'])>0){
    //     ccwts_include_page("ticket");
    //   }else{
    //     ccwts_include_page("tickets");
    //   }
    // }

}

if( is_admin() )
    $ccwts_menu = new CCWTS_Menu();





