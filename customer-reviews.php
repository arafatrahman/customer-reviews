<?php
/*
Plugin Name: Customer Reviews
Plugin URI: https://webbird.co.uk
Description: A simple WordPress plugin for managing customer reviews.
Version: 1.0.0
Author: Arafat Rahman
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
define('CR_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('CR_PLUGIN_ASSETS', plugin_dir_url(__FILE__) . 'assets/');
define('CR_BASE_NAME', plugin_basename(__FILE__));

// Include MVC structure
include_once CR_PLUGIN_PATH . 'includes/views/review-view.php';
include_once CR_PLUGIN_PATH . 'includes/model/review-model.php';
include_once CR_PLUGIN_PATH . 'includes/controller/review-controller.php';



