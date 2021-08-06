<?php
/**
 * Trigger this file on plugin uninstall
 * 
 * @package Create-O-Form
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS wp_field_status");
$wpdb->query("DROP TABLE IF EXISTS wp_field_data");