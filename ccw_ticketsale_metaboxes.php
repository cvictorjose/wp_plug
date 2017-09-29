<?php
add_action( 'add_meta_boxes', 'ccwts_metaboxes' );
function ccwts_metaboxes()
{
    if(basename( get_page_template() ) == 'page-biglietteria-2017.php' || basename( get_page_template() ) == 'page-biglietteria-2017-hotel.php' ){
        add_meta_box( 'ccwts_metabox_ticketsale_id', 'Biglietteria', 'ccwts_metabox_ticketsale', 'page', 'side', 'high' );
    }
}

function ccwts_metabox_ticketsale( $post )
{
    $ticketsales = ccwts_get('/ticketsales');
    $ticketsale_id = ccwts_field('ticketsale_id');
    ?>
    <p>
        <label for="ccwts_ticketsale_id">Biglietteria</label>
        <select name="ccwts_ticketsale_id" id="ccwts_ticketsale_id"><option value=""> - - - </option><?php
            foreach($ticketsales as $ticketsale):
            ?><option value="<?= $ticketsale->id ?>"<?= (($ticketsale->id == $ticketsale_id) ? ' selected' : '') ?>><?= $ticketsale->title ?></option><?php
            endforeach; ?>
        </select>
    </p>
    <?php
    if(basename( get_page_template() ) == 'page-biglietteria-2017-hotel.php' ){
        $parkhotel_tickets = ccwts_get('/tickets');
        $parkhotel_ticket_id = ccwts_field('parkhotel_ticket_id');
        ?>
        <p>
            <label for="ccwts_parkhotel_ticket_id">Biglietto Parco+Hotel</label>
            <select name="ccwts_parkhotel_ticket_id" id="ccwts_parkhotel_ticket_id"><option value=""> - - - </option><?php
                foreach($parkhotel_tickets as $parkhotel_ticket):
                ?><option value="<?= $parkhotel_ticket->id ?>"<?= (($parkhotel_ticket->id == $parkhotel_ticket_id) ? ' selected' : '') ?>><?= $parkhotel_ticket->title ?></option><?php
                endforeach; ?>
            </select>
        </p>
        <?php
    }
}

add_action( 'save_post', 'ccwts_metabox_ticketsale_save' );
function ccwts_metabox_ticketsale_save( $post_id )
{
    // Bail if we're doing an auto save
    //if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
     
    // if our nonce isn't there, or we can't verify it, bail
    //if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;
     
    // if our current user can't edit this post, bail
    //if( !current_user_can( 'edit_post' ) ) return;
     
    // now we can actually save the data
    /*$allowed = array( 
        'a' => array( // on allow a tags
            'href' => array() // and those anchors can only have href attribute
        )
    );*/
     
    // Make sure your data is set before trying to save it
    if(basename( get_page_template() ) == 'page-biglietteria-2017.php' || basename( get_page_template() ) == 'page-biglietteria-2017-hotel.php' )
    {
        
        if( isset( $_POST['ccwts_ticketsale_id'] ) && intval($_POST['ccwts_ticketsale_id']) > 0)
            ccwts_field('ticketsale_id', $_POST['ccwts_ticketsale_id']);

        if(basename( get_page_template() ) == 'page-biglietteria-2017-hotel.php' ){
            if( isset( $_POST['ccwts_parkhotel_ticket_id'] ) && intval($_POST['ccwts_parkhotel_ticket_id']) > 0)
            ccwts_field('parkhotel_ticket_id', $_POST['ccwts_parkhotel_ticket_id']);
        }

    }
        //update_post_meta( $post_id, 'my_meta_box_text', wp_kses( $_POST['my_meta_box_text'], $allowed ) );
         
    //if( isset( $_POST['my_meta_box_select'] ) )
        //update_post_meta( $post_id, 'my_meta_box_select', esc_attr( $_POST['my_meta_box_select'] ) );
         
    // This is purely my personal preference for saving check-boxes
    //$chk = isset( $_POST['my_meta_box_check'] ) && $_POST['my_meta_box_select'] ? 'on' : 'off';
    //update_post_meta( $post_id, 'my_meta_box_check', $chk );
}
