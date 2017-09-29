<?php
/*
Plugin Name:   DYI TicketSale plugin
Plugin URI:   https://developer.wordpress.org/plugins/the-basics/
Description: Plugin DYI TicketSale  2017
Version:      20160911
Author:       WordPress.org
Author URI:   https://developer.wordpress.org/
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/
//add_action('lib/users.php', 'view_details');

/* All other plugin files */
require_once( plugin_dir_path(__FILE__) . '/ccw_ticketsale_config.php' );
require_once( plugin_dir_path(__FILE__) . '/lib/CCWTS_Controller.php' );

// require_once( plugin_dir_path(__FILE__) . '/lib/CCWTS_Table.php' );
// require_once( plugin_dir_path(__FILE__) . '/lib/CCWTS_Form.php' );
require_once( plugin_dir_path(__FILE__) . '/lib/CCWTS_HTTP.php' );
require_once( plugin_dir_path(__FILE__) . '/lib/CCWTS_Extra.php' );
require_once( plugin_dir_path(__FILE__) . '/ccw_ticketsale_menu.php' );
// require_once( plugin_dir_path(__FILE__) . '/ccw_ticketsale_metaboxes.php' );
// require_once( plugin_dir_path(__FILE__) . '/ccw_ticketsale_frontend.php' );
/*ccwts_session();

*/