<?php
class CCWTS_Controller {

    public    $extra_actions;
    public    $back_link;
    protected $schema;
    protected $fks;
    protected $where_clauses;

    function __construct($entity_name_pl=null){

        if(!$this->entity_name_pl)
            $this->entity_name_pl = $_REQUEST['page'];
        $this->entity_name_sgl = ccwts_sgl($this->entity_name_pl);

        $this->back_link = sprintf('<a href="admin.php?page=%s">Torna all\'elenco</a>', $this->entity_name_pl);
        $this->extra_actions = array();
    }

    function setExtraAction($method_name){
        $this->extra_actions[] = $method_name;
    }


    function read(){

        ?><h1>Elenco <?= ccwts_label($this->entity_name_pl); ?></h1><?php
        /* Prepare Table data START
        $params = (array) $this->schema->parameters;
        $entities = array();
        $get_url_query_string = http_build_query($this->where_clauses);
        $get_url = sprintf('/%s%s', $this->entity_name_pl, (($get_url_query_string) ? '?'.$get_url_query_string : '')); */


        $get_url= $this->entity_name_pl;
        $paged = $_REQUEST['paged'];
        $per_page = $_REQUEST['page_s'];
        if ($per_page<1){ $per_page = 20; }

        $orderby =$_REQUEST['orderby'];
        $order =$_REQUEST['order'];

        if (empty($orderby)){ $orderby =  $this->schema->orderby; }
        if (empty($order)){ $order =  $this->schema->order; }

        $ee =  ccwts_get_2('/'.$get_url.'/1?page_s='.$per_page.'&page_n='.$paged.'&sort='.$orderby.'&order='.$order);
        //echo "<br>PPag: ".$ee->per_page . " + CurrentPag:".$ee->current_page. " + DA:".$ee->from. " + fino".$ee->to ;
        //$ee=$ee->data;

        $usr= json_decode($ee['body']);

        echo "ID:".$usr->id."<br>NAME:".$usr->name;

        ?>
        <br>
    <a href="admin.php?page=<?= $this->entity_name_pl; ?>&action=create&<?= $get_url_query_string; ?>" class="button button-primary">Crea <?= ccwts_label($this->entity_name_sgl); ?></a><?php

    }

    function listen(){

        if($_REQUEST['action'] == 'edit'){

            $this->edit();

        }elseif($_REQUEST['action'] == 'create'){

            $this->create();

        }elseif($_REQUEST['action'] == 'delete'){

            $this->delete();

        }elseif(in_array($_REQUEST['action'], $this->extra_actions)){

            $extra_action = $_REQUEST['action'];
            $this->$extra_action();

        }else{ // List/Read

            $this->read();

        }

    }

}
