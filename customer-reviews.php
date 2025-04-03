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
      $table_name = $wpdb->prefix . 'customer_reviews';  // Table name with the prefix
      
        // Check if the table already exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
            return; // Table already exists, no need to create it
        }

      // SQL query to create the reviews table
      $charset_collate = $wpdb->get_charset_collate();
      $sql = "CREATE TABLE $table_name (
          id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          author_name VARCHAR(255) NOT NULL,
          author_email VARCHAR(255) NOT NULL,
          author_phone VARCHAR(20) NOT NULL,
          rating INT(1) NOT NULL CHECK (rating >= 1 AND rating <= 5),
          comments TEXT NOT NULL,
          status VARCHAR(20) DEFAULT 'pending',
          created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
          updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
      ) $charset_collate;";
      
      // Run the query
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
  }
  register_activation_hook(__FILE__, 'wp_customer_reviews_table_create');
  

// Define plugin path
define('CR_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('CR_PLUGIN_ASSETS', plugin_dir_url(__FILE__) . 'assets/');

// Include MVC structure
include_once CR_PLUGIN_PATH . 'includes/views/review-view.php';
include_once CR_PLUGIN_PATH . 'includes/model/review-model.php';
include_once CR_PLUGIN_PATH . 'includes/controller/review-controller.php';

include_once CR_PLUGIN_PATH . 'includes/review-settings.php';


  

