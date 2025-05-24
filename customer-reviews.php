<?php
/*
 * Plugin Name:          Customer Reviews
 * Plugin URI:           http://wordpress.org/plugins/customer-reviews/
 * Description:          The Customer Review plugin allows you to manage and display customer-submitted reviews for products and services. A short code can be added to any page, post, or custom post type.
 * Version:              1.0.0
 * Author:               Artios Media
 * Author URI:           http://www.artiosmedia.com
 * Developer:            Arafat Rahman
 * Copyright:            © 2019-2025 Artios Media (email : contact@artiosmedia.com).
 * License: GNU          General Public License v3.0
 * License URI:          http://www.gnu.org/licenses/gpl-3.0.html
 * Tested up to:         6.8.1
 * WC requires at least: 6.5.0
 * WC tested up to:      9.8.5
 * PHP tested up to:     8.2.4
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}


// Create the reviews table on plugin activation
function wp_customer_reviews_table_create() {
      global $wpdb;

    $table_name = $wpdb->prefix . 'customer_reviews';
    // Check if the table already exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
        return; // Table already exists, no need to create it
    }


    $charset_collate = $wpdb->get_charset_collate();

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $sql = "CREATE TABLE $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        title VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
        name VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
        email VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
        phone VARCHAR(18) COLLATE utf8mb4_general_ci DEFAULT NULL,
        website VARCHAR(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
        rating INT(1) NOT NULL,
        comment TEXT COLLATE utf8mb4_general_ci NOT NULL,
        city VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
        state VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
        status VARCHAR(20) COLLATE utf8mb4_general_ci DEFAULT 'pending',
        positionid INT(11) DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        admin_reply TEXT COLLATE utf8mb4_general_ci DEFAULT NULL,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    dbDelta($sql);
  }
  register_activation_hook(__FILE__, 'wp_customer_reviews_table_create');
  

// Define plugin path
define('CTRW_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('CTRW_PLUGIN_ASSETS', plugin_dir_url(__FILE__) . 'assets/');
define('CTRW_BASE_NAME', plugin_basename(__FILE__));

// Include MVC structure
include_once CTRW_PLUGIN_PATH . 'includes/views/review-view.php';
include_once CTRW_PLUGIN_PATH . 'includes/model/review-model.php';
include_once CTRW_PLUGIN_PATH . 'includes/controller/review-controller.php';

add_action('load-toplevel_page_wp-review-plugin', 'ctrw_add_screen_option');

function ctrw_add_screen_option() {
    $option = 'per_page';
    $args = [
        'label'   => 'Reviews per page',
        'default' => 10,
        'option'  => 'ctrw_reviews_per_page'
    ];
    add_screen_option($option, $args);
}

add_filter('set-screen-option', 'ctrw_save_screen_option', 10, 3);

function ctrw_save_screen_option($status, $option, $value) {
    if ($option === 'ctrw_reviews_per_page') {
        return $value;
    }
    return $status;
}




