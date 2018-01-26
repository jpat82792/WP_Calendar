<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
 
delete_option('omni-email-version');

 
// drop a custom database table
global $wpdb;
$events_table_name = $wpdb->prefix .'jpcalendar_events';
$categories_table_name = $wpdb->prefix . 'jpcalendar_categories';
$uninstall_events = "DROP TABLE IF EXISTS $events_table_name";
$uninstall_categories = "DROP TABLE IF EXISTS $categories_table_name";
dbDelta($uninstall_events);
dbDelta($uninstall_categories);
