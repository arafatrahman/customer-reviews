<?php
if (!defined('ABSPATH')) {
    exit;
}

class Review_Controller {
    private $model;
    private $view;

    public function __construct() {
        $this->model = new Review_Model();
        $this->view = new Review_View();
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_filter('plugin_action_links_' . CTRW_BASE_NAME, array($this, 'ctrw_plugin_action_links'));

        //Add plugin description link
        add_filter('plugin_row_meta', array($this, 'add_ctrw_description_link'), 10, 2);
        add_filter('plugin_row_meta', array($this, 'add_ctrw_details_link'), 10, 4);

        add_action('wp_enqueue_scripts', [$this,'review_enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this,'wp_review_admin_styles']);


        add_shortcode('wp_ctrw_form', [$this,'customer_reviews_form_shortcode']);
        add_shortcode('wp_ctrw_lists', [$this,'customer_reviews_list_shortcode']);

        add_action('wp_ajax_submit_review', [$this, 'submit_review']);
        add_action('wp_ajax_nopriv_submit_review', [$this, 'submit_review']);
        add_action('wp_ajax_save_review_reply', [$this, 'save_review_reply']);
        add_action('wp_ajax_nopriv_save_review_reply', [$this, 'save_review_reply']);

        add_action('wp_ajax_edit_customer_review', [$this, 'edit_customer_review']);
        add_action('wp_ajax_nopriv_edit_customer_review', [$this, 'edit_customer_review']);
        
        add_action( 'add_meta_boxes', [$this, 'ctrw_add_meta_box' ]);
        add_action( 'save_post', [$this, 'ctrw_save_meta_box_data' ] );

        add_filter('the_content', [$this, 'append_customer_reviews_shortcode']);

         
    }

    // Add settings link to the plugin page
    public function ctrw_plugin_action_links($links) {
        // We shouldn't encourage editing our plugin directly.
      unset($links['edit']);

      // Add our custom links to the returned array value.
      return array_merge(array(
         '<a href="' . admin_url('admin.php?page=wp-review-settings') . '">' . __('Settings', 'wp_cr') . '</a>'
      ), $links);
    }

    public function add_ctrw_description_link($links, $file)
    {

        
        
       if (CTRW_BASE_NAME == $file) {
        // Add a donation link to the plugin row meta
          $row_meta = array(
             'donation' => '<a href="' . esc_url(' https://www.zeffy.com/en-US/donation-form/your-donation-makes-a-difference-6') . '" target="_blank">' . esc_html__('Donation for Homeless', 'wp_cr') . '</a>'
          );
          return array_merge($links, $row_meta);
       }
       return (array) $links;
    }

    public function add_ctrw_details_link($links, $plugin_file, $plugin_data)
   {

      if (isset($plugin_data['PluginURI']) && false !== strpos($plugin_data['PluginURI'], 'http://wordpress.org/plugins/customer-reviews/')) {
        $slug = basename($plugin_data['PluginURI']);
         unset($links[2]);
         $links[] = sprintf('<a href="%s" class="thickbox" title="%s">%s</a>', self_admin_url('plugin-install.php?tab=plugin-information&amp;plugin=' . $slug . '&amp;TB_iframe=true&amp;width=772&amp;height=563'), esc_attr(sprintf(__('More information about %s', 'ctyw'), $plugin_data['Name'])), __('View Details', 'wp_cr'));
      }
      return $links;
   }

    // Append the customer reviews shortcode to the content
    public function append_customer_reviews_shortcode($content) {
        if (is_singular(['post', 'page', 'product'])) {

            $post_id = get_the_ID();
            $enable_reviews = get_post_meta($post_id, '_ctrw_enable_reviews', true);
            $enable_review_form = get_post_meta($post_id, '_ctrw_enable_review_form', true);
            $enable_review_list = get_post_meta($post_id, '_ctrw_enable_review_list', true);


            if ($enable_reviews) {
                if ($enable_review_form) {
                    // Only append the form shortcode if it's not already present in the content
                    if (strpos($content, '[wp_ctrw_form]') === false) {
                        $content .= do_shortcode('[wp_ctrw_form]');
                    }
                }
                if ($enable_review_list) {
                    $content .= do_shortcode('[wp_ctrw_lists]');
                }
            }
        }
    
        return $content;
    }

    // Add meta box to the review post type
    public function ctrw_add_meta_box() {
        add_meta_box(
            'ctrw_meta_box',
            __('Customer Reviews', 'wp_ctrw'),
            [$this, 'render_ctrw_meta_box'],
            ['post', 'page', 'product'], // Post types
            'side',                      // Context: 'side' places it on the right
            'high'                       // Priority
        );
    }

    // Render the meta box content
    public function render_ctrw_meta_box($post) {

        // Retrieve current settings
        $settings = [
            'enable_reviews'      => get_post_meta($post->ID, '_ctrw_enable_reviews', true),
            'enable_review_form'  => get_post_meta($post->ID, '_ctrw_enable_review_form', true),
            'enable_review_list'  => get_post_meta($post->ID, '_ctrw_enable_review_list', true),
        ];

        // Add nonce field for security
        wp_nonce_field('ctrw_meta_box_nonce', 'ctrw_meta_box_nonce');

        // Enable customer reviews option
        echo '<p>';
        echo '<label for="enable_reviews">';
        echo '<input type="checkbox" id="enable_reviews" name="enable_reviews" value="1" ' . checked(1, isset($settings['enable_reviews']) ? $settings['enable_reviews'] : 1, false) . ' />';
        echo __('Enable Customer Reviews For This Page', 'wp_cr');
        echo '</label>';
        echo '</p>';

        // Enable review form option
        echo '<p>';
        echo '<label for="enable_review_form">';
        echo '<input type="checkbox" id="enable_review_form" name="enable_review_form" value="1" ' . checked(1, isset($settings['enable_review_form']) ? $settings['enable_review_form'] : 1, false) . ' />';
        echo __('Display Review Form', 'wp_cr');
        echo '</label>';
        echo '</p>';

        // Enable review list option
        echo '<p>';
        echo '<label for="enable_review_list">';
        echo '<input type="checkbox" id="enable_review_list" name="enable_review_list" value="1" ' . checked(1, isset($settings['enable_review_list']) ? $settings['enable_review_list'] : 1, false) . ' />';
        echo __('Display Review List', 'wp_cr');
        echo '</label>';
        echo '</p>';
    }
    // Save meta box data
    public function ctrw_save_meta_box_data($post_id) {
        // Check nonce for security
        if (!isset($_POST['ctrw_meta_box_nonce']) || !wp_verify_nonce($_POST['ctrw_meta_box_nonce'], 'ctrw_meta_box_nonce')) {
            return;
        }

        // Check if the user has permission to save data
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save the settings as post meta for this post/page only
        update_post_meta($post_id, '_ctrw_enable_reviews', isset($_POST['enable_reviews']) ? 1 : 0);
        update_post_meta($post_id, '_ctrw_enable_review_form', isset($_POST['enable_review_form']) ? 1 : 0);
        update_post_meta($post_id, '_ctrw_enable_review_list', isset($_POST['enable_review_list']) ? 1 : 0);
    }
 

    // Add menu pages
    public function add_admin_menu() {
        add_menu_page(
            'Reviews',
            'Reviews',
            'manage_options',
            'customer-reviews',
            array($this, 'display_reviews_page'),
            'dashicons-star-filled'
        );
        

        add_submenu_page(
            'customer-reviews',
            'Review Settings',
            'Review Settings',
            'manage_options',
            'wp-review-settings',
            array($this, 'display_settings_page')
        );

        add_submenu_page( 'woocommerce', 'Reviews', 'Reviews', 'manage_options', 'customer-product-reviews', 'my_custom_submenu_page_callback' ); 

    }

    public function my_custom_submenu_page_callback() {
      // Retrieve the status from the URL, defaulting to 'all' if none is provided
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action']) && isset($_POST['review_ids'])) {
            $this->handle_bulk_action();
        }

        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : 'all';
        $reviews = $this->model->get_reviews_by_status($status);
        $counts = $this->model->get_review_counts();
        $this->view->display_reviews($reviews, $counts, $status);
  }





    // Display Review Settings page
    public function display_settings_page() {
 
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        include CTRW_PLUGIN_PATH . 'includes/views/ctrw-settings.php';
        $this->save_review_settings();
    

}

    // Save Review Settings
    private function save_review_settings() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $settings = [
                'enable_email_notification' => isset($_POST['enable_email_notification']) ? 1 : 0,
                'enable_customer_email_notification' => isset($_POST['enable_customer_email_notification']) ? 1 : 0,
                'auto_approve_reviews' => isset($_POST['auto_approve_reviews']) ? 1 : 0,
                'show_city' => isset($_POST['show_city']) ? 1 : 0,
                'show_state' => isset($_POST['show_state']) ? 1 : 0,
                'name_font_size' => intval(sanitize_text_field($_POST['name_font_size'] ?? 10)),
                'name_font_weight' => sanitize_text_field($_POST['name_font_weight'] ?? 'normal'),
                'comment_font_size' => intval(sanitize_text_field($_POST['comment_font_size'] ?? 9)),
                'comment_font_style' => sanitize_text_field($_POST['comment_font_style'] ?? 'normal'),
                'reviews_per_page' => intval(sanitize_text_field($_POST['reviews_per_page'] ?? 10)),
                'date_format' => sanitize_text_field($_POST['date_format'] ?? 'MM/DD/YYYY'),
                'include_time' => isset($_POST['include_time']) ? 1 : 0,
                'star_color' => sanitize_hex_color($_POST['star_color'] ?? '#fbbc04'),
                'fields' => array_map(function($field) {
                    return array_map('sanitize_text_field', $field);
                }, $_POST['fields'] ?? [])
            ];
            update_option('customer_reviews_settings', $settings);
            echo '<script>location.reload();</script>';
        }
    }


    // Display reviews page
  public function display_reviews_page() {
      // Retrieve the status from the URL, defaulting to 'all' if none is provided
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action']) && isset($_POST['review_ids'])) {
            $this->handle_bulk_action();
        }

        $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
        $reviews = $this->model->get_reviews_by_status($status);
        $counts = $this->model->get_review_counts();
        $this->view->display_reviews($reviews, $counts, $status);
  }

  // Handle bulk actions
  private function handle_bulk_action() {
      $action = sanitize_text_field($_POST['bulk_action']);
      $review_ids = array_map('intval', $_POST['review_ids']);

      if (!empty($review_ids)) {
          if ($action === 'approve') {
              $this->model->update_review_status($review_ids, 'approved');
          } elseif ($action === 'reject') {
              $this->model->update_review_status($review_ids, 'reject');
          } elseif ($action === 'trash') {
              $this->model->update_review_status($review_ids, 'trash');
          } elseif ($action === 'delete_permanently') {
            $this->model->delete_reviews($review_ids);
         }
      }
}

    // Enqueue scripts and styles
    public function review_enqueue_scripts() {
            wp_enqueue_script('review-script', CTRW_PLUGIN_ASSETS . 'js/review-script.js', ['jquery'], '1.0.0', true);
            wp_localize_script('review-script', 'ctrw_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);
            wp_enqueue_style('review-style', CTRW_PLUGIN_ASSETS . 'css/ctrw-frontend.css', [], '1.0.0');
            // Add dynamic CSS
           
           $settings = get_option('customer_reviews_settings');
           $star_color = isset($settings['star_color']) ? sanitize_hex_color($settings['star_color']) : '#fbbc04';
           $custom_css = "
               
               .rating label:hover,
               .rating label:hover ~ label {
                   color: {$star_color};
               }
               .rating input:checked ~ label {
                   color: {$star_color};
               }
           ";
           wp_add_inline_style('review-style', $custom_css);
    }

    public function wp_review_admin_styles() {
            $screen = get_current_screen();
            if ($screen && $screen->id === 'reviews_page_wp-review-settings') {
                    wp_enqueue_style('wp-review-admin', CTRW_PLUGIN_ASSETS . 'css/ctrw-admin.css', [], '1.0.0');
            }
            
            wp_enqueue_script('cr-admin-script', CTRW_PLUGIN_ASSETS . 'js/ctrw-admin.js', ['jquery'], '1.0.0', true);
            wp_localize_script('cr-admin-script', 'cradmin_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);
    }

    private function sanitize_post_data($data) {
            return array_map('sanitize_text_field', $data);
    }

    public function submit_review() {
            $data = $this->sanitize_post_data($_POST);

            //check auto approve reviews setting
            $settings = get_option('customer_reviews_settings');
            if (isset($settings['auto_approve_reviews']) && $settings['auto_approve_reviews'] == 1) {
                $status = 'approved';
            } else {
                $status = 'pending';
            }
        
            $review_data = [
                    
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'website' => $data['website'],
                    'city' => $data['city'],
                    'state' => $data['state'],
                    'rating' => intval($data['rating']),
                    'comment' => $data['comment'],
                    'status' => $status,
                    'positionid' => intval($data['positionid'])
            ];

            $this->model->ctrw_add_review($review_data);

            // Notify admin via email
            if (get_option('customer_reviews_settings')['enable_email_notification'] ?? false) {
                $this->notify_admin_of_pending_review($review_data);
            }
            // Notify customer via email if enabled
            if (get_option('customer_reviews_settings')['enable_customer_email_notification'] ?? false) {
            
              $this->notify_customer_of_pending_review($data['email'],$data['name'],$status);
            }
            // Send success response
            wp_send_json([
                    'success' => true,
                    'message' => 'Review submitted successfully!',
                    'reviews' => $this->get_review_list()
            ]);
    }

    // Notify customer of a new pending review
    private function notify_customer_of_pending_review($email,$name,$status) {
        if (empty($email)) {
            return;
        }
        // check enable_customer_email_notification
        if (!get_option('customer_reviews_settings')['enable_customer_email_notification']) {
            return;
        }
        $subject = __('Thank you for your review', 'wp_cr');
        $message = sprintf(
            __("Thank you %s for your review! It is now currently %s ", 'wp_cr'),
            $name,
            $status
        );

        wp_mail($email, $subject, $message);
        
    }

    private function notify_admin_of_pending_review($review_data) {
        $admin_email = get_option('admin_email');
        $subject = __('New Pending Review Submitted', 'wp_cr');
        $message = __("A new review has been submitted and is pending approval.\n\n", 'wp_cr');

        $message .= __("Please log in to the admin panel to review and approve it.\n", 'wp_cr');
        $message .= sprintf(
            __("Pending reviews can be viewed here: %s", 'wp_cr'),
            admin_url('admin.php?page=customer-reviews&status=pending')
        );

        wp_mail($admin_email, $subject, $message);
    }



public function get_review_list() {
    ob_start();
    include plugin_dir_path(__FILE__) . '../views/ctrw-list.php';
    return ob_get_clean();
}



public function save_review_reply() {
    $id = intval($_POST['review_id']);
    $reply = sanitize_textarea_field($_POST['reply_message']);

    $this->model->update_review($id, ['admin_reply' => $reply]);

    wp_send_json(['success' => true, 'reply' => $reply]);
}

public function edit_customer_review() {
    $id = intval($_POST['id']);
    $update_type = sanitize_text_field($_POST['update_type']);
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $website = sanitize_text_field($_POST['website']);
    $comment = sanitize_textarea_field($_POST['comment']);
    $city = sanitize_text_field($_POST['city']);
    $state = sanitize_text_field($_POST['state']);
    $status = sanitize_text_field($_POST['status']);
    $rating = intval($_POST['rating']);
    $title = sanitize_text_field($_POST['title']);
    $positionid = intval($_POST['positionid']);

    $data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'website' => $website,
        'comment' => $comment,
        'city' => $city,
        'state' => $state,
        'status' => $status,
        'rating' => $rating,
        'title' => $title,
        'positionid' => $positionid,
    ];
    //get old status for notification
    if ($update_type === 'add') {
        $this->model->ctrw_add_review($data);
    } else {

        $old_review = $this->model->get_review_by_id($id);
        $old_status = $old_review->status ?? '';
        if ($old_status != $status) {
         $this->notify_customer_of_pending_review($email,$name,$status);
        }
        $this->model->update_review($id, $data);
    }

    wp_send_json(['success' => true, 'data' => $mydata]);
   
    
   
}

public function customer_reviews_form_shortcode() {
    ob_start();
    include CTRW_PLUGIN_PATH . 'includes/views/ctrw-form.php';
    return ob_get_clean();

}

public function customer_reviews_list_shortcode() {
    ob_start();
    include CTRW_PLUGIN_PATH . 'includes/views/ctrw-list.php';
    return ob_get_clean();
}


}


new Review_Controller();
?>
