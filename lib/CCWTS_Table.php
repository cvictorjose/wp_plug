<?php

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CCWTS_Table extends WP_List_Table {

    protected $entity_name_pl;
    protected $entity_name_sgl;
    protected $schema;
    protected $ccwts_table_caption;

    function __construct($entity_name_pl=null){
        global $status, $page;
        if(!$this->entity_name_pl)
            $this->entity_name_pl = $_REQUEST['page'];
        $this->entity_name_sgl = ccwts_sgl($this->entity_name_pl);
        $this->schema = ccwts_schema($this->entity_name_pl);
        //print_r($this->schema);
        //Set parent defaults
        parent::__construct( array(
            'singular'  => $entity_name_sgl,
            'plural'    => $entity_name_pl,
            'ajax'      => true
        ) );
    }

    function column_default($item, $column_name){
        return $item[$column_name];
    }

    function column_title($item){
        
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&%s=%s">Modifica</a>', $this->entity_name_pl, 'edit', $this->entity_name_sgl, $item['id']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&%s=%s">Elimina</a>', $this->entity_name_pl, 'delete', $this->entity_name_sgl, $item['id']),
        );
        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['title'],
            /*$2%s*/ $item['id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }

    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
        );
    }

    function get_columns(){
        $columns = array();
        foreach($this->schema->parameters as $k=>$v){
            $columns[$k] = $v->description;
        }
        return $columns;
    }

    function get_sortable_columns(){
        $sortable_columns = array();
        foreach($this->schema->parameters as $k=>$v){
            $sortable_columns[$k] = array($k, false);
        }
        return $sortable_columns;
    }

    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action() {
        
        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
        
    }

    function prepareItems($table_values) {
        global $wpdb; //This is used only if making any database queries

        $get_url_query_string = http_build_query($this->where_clauses);
        $get_url = sprintf('/%s%s', $this->entity_name_pl, (($get_url_query_string) ? '?'.$get_url_query_string : ''));

        $paged = $_REQUEST['paged'];
        $per_page = $_REQUEST['page_s'];
        if ($per_page<1){ $per_page = 20; }

        $orderby =$_REQUEST['orderby'];
        $order =$_REQUEST['order'];

        if (empty($orderby)){ $orderby =  $this->schema->orderby; }
        if (empty($order)){ $order =  $this->schema->order; }

        $paginate = ccwts_get_2('/'.$get_url.'?page_s='.$per_page.'&page_n='.$paged.'&sort='.$orderby.'&order='.$order);
       //echo "<br>prepare:.$get_url?page_s=".$per_page.'&page_n='.$paged.'&sort='.$orderby.'&order='.$order;
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->process_bulk_action();
        
        $data = $table_values; //example_data;

        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }

        if ($get_url!="/orders") usort($data, 'usort_reorder');
        
        //$current_page = $this->get_pagenum();
        //$total_items = count($data);

        $current_page = $this->get_pagenum();
        $total_items = $paginate->total;
        
        //$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        $this->items = $data;

        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );

    }

    function setCaption($caption){
        $this->ccwts_table_caption = $caption;
    }

    function getCaption($caption){
        return $this->ccwts_table_caption;
    }

    function render($data, $entity_label_pl=null, $ccwts_table_caption=null){
        
        if(!$entity_label_pl)
            $entity_label_pl = ccwts_label($_REQUEST['page']);
        $this->prepareItems($data);
        
        if(empty($this->getCaption()))
            $this->setCaption("Elenco di tutti i record configurati");
        ?>
        <div class="wrap">
            
            <div id="icon-users" class="icon32"><br/></div>
            <h2><?= ucfirst($entity_label_pl); ?></h2>
            
            <div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
                <p><?= $this->getCaption(); ?></p>
            </div>
            
            <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
            <form id="movies-filter" method="get">
                <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <!-- Now we can render the completed list table -->
                <?php parent::display(); ?>
            </form>
            
        </div>
        <?php
    }

}

function ccwts_table($data, $entity_label_pl=null, $ccwts_table_caption=null){
    
    if(!$entity_label_pl)
            $entity_label_pl = ccwts_label($_REQUEST['page']);
    //Create an instance of our package class...
    $ccwtsTable = new CCWTS_Table();
    //Fetch, prepare, sort, and filter our data...
    $ccwtsTable->prepareItems($data);
    
    if(!$ccwts_table_caption)
        $ccwts_table_caption = "Elenco di tutti i record configurati";
    ?>
    <div class="wrap">
        
        <div id="icon-users" class="icon32"><br/></div>
        <h2><?= ucfirst($entity_label_pl); ?></h2>
        
        <div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
            <p><?= $ccwts_table_caption; ?></p>
        </div>
        
        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="movies-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $ccwtsTable->display(); ?>
        </form>
        
    </div>
    <?php
}
