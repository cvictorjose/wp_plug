<?php
class CCWTS_Order_Table extends CCWTS_Table{
    function column_id($item){
        
        //Build row actions
        $actions = array(
            'view_details'=> sprintf('<a href="?page=orders&order=%s&action=view_details">Visualizza dettagli</a>', $item['id']),
        );
        
        //Return the title contents
        return sprintf('#%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['id'],
            /*$2%s*/ $item['id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }
}