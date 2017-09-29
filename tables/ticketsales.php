<?php
class CCWTS_TicketSale_Table extends CCWTS_Table{
    function column_title($item){
        
        //Build row actions
        $actions = array(
            'edit'          => sprintf('<a href="?page=%s&action=%s&ticketsale=%s">Modifica</a>',$_REQUEST['page'], 'edit', $item['id']),
            /*'edit_groups'=> sprintf('<a href="?page=ticketgroups&ticket_sale_id=%s">Gruppi</a>', $item['id']),
            'manage_tickets'=> sprintf('<a href="?page=%s&action=%s&ticketsale=%s">Biglietti</a>',$_REQUEST['page'], 'manage_tickets', $item['id']),
            'manage_services'=> sprintf('<a href="?page=%s&action=%s&ticketsale=%s">Servizi</a>',$_REQUEST['page'], 'manage_services', $item['id']),*/
            'manage_layout_sections'=> sprintf('<a href="?page=%s&action=%s&ticketsale=%s">Layout|Sezioni</a>',$_REQUEST['page'], 'manage_layout_sections', $item['id']),
            'manage_layout_categories'=> sprintf('<a href="?page=%s&action=%s&ticketsale=%s">Layout|Categorie</a>',$_REQUEST['page'], 'manage_layout_categories', $item['id']),
            'manage_layout_items'=> sprintf('<a href="?page=%s&action=%s&ticketsale=%s">Layout|Celle</a>',$_REQUEST['page'], 'manage_layout_items', $item['id']),
            'delete'        => sprintf('<a href="?page=%s&action=%s&ticketsale=%s">Elimina</a>',$_REQUEST['page'], 'delete', $item['id']),
        );
        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['title'],
            /*$2%s*/ $item['id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }
}
