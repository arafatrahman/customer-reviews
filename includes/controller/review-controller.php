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

        add_action('wp_enqueue_scripts', [$this,'review_enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this,'wp_review_admin_styles']);


        add_shortcode('wp_cr_form', [$this,'customer_reviews_form_shortcode']);
        add_shortcode('wp_cr_lists', [$this,'customer_reviews_list_shortcode']);

        add_action('wp_ajax_submit_review', [$this, 'submit_review']);
        add_action('wp_ajax_nopriv_submit_review', [$this, 'submit_review']);
        add_action('wp_ajax_save_review_reply', [$this, 'save_review_reply']);
        add_action('wp_ajax_nopriv_save_review_reply', [$this, 'save_review_reply']);

        add_action('wp_ajax_edit_customer_review', [$this, 'edit_customer_review']);
        add_action('wp_ajax_nopriv_edit_customer_review', [$this, 'edit_customer_review']);
        
        add_action( 'add_meta_boxes', [$this, 'cr_add_meta_box' ]);
        add_action( 'save_post', [$this, 'cr_save_meta_box_data' ] );

        add_filter('the_content', [$this, 'append_customer_reviews_shortcode']);

         
    }

    // Append the customer reviews shortcode to the content
    public function append_customer_reviews_shortcode($content) {
        if (is_singular(['post', 'page'])) {
            $cr_meta_settingss = get_option('customer_reviews_settings');
            $enable_review_list = isset($cr_meta_settingss['enable_review_list']) ? $cr_meta_settingss['enable_review_list'] : 1;
            $enable_review_form = isset($cr_meta_settingss['enable_review_form']) ? $cr_meta_settingss['enable_review_form'] : 1;
            $enable_reviews = isset($cr_meta_settingss['enable_reviews']) ? $cr_meta_settingss['enable_reviews'] : 1;

            if ($enable_reviews) {
                
                if ($enable_review_form) {
                    $content .= do_shortcode('[wp_cr_form]');
                }
                if ($enable_review_list) {
                    $content .= do_shortcode('[wp_cr_lists]');
                }
            }
        }
    
        return $content;
    }

    // Add meta box to the review post type
    public function cr_add_meta_box() {
        add_meta_box(
            'cr_meta_box',
            __('Customer Reviews', 'wp_cr'),
            [$this, 'render_cr_meta_box'],
            ['post', 'page'],           // Screen (post types)
            'normal',                   // Context (normal, side, advanced)
            'high'
            );
    }
    // Render the meta box content
    public function render_cr_meta_box($post) {
        // Retrieve current settings
        $settings = get_option('customer_reviews_settings', [
            'enable_reviews' => 1,
            'enable_review_form' => 1,
            'enable_review_list' => 1,
        ]);

        // Add nonce field for security
        wp_nonce_field('cr_meta_box_nonce', 'cr_meta_box_nonce');

        // Enable customer reviews option
        echo '<p>';
        echo '<label for="enable_reviews">';
        echo '<input type="checkbox" id="enable_reviews" name="enable_reviews" value="1" ' . checked(1, $settings['enable_reviews'], false) . ' />';
        echo __('Enable Customer Reviews', 'wp_cr');
        echo '</label>';
        echo '</p>';

        // Enable review form option
        echo '<p>';
        echo '<label for="enable_review_form">';
        echo '<input type="checkbox" id="enable_review_form" name="enable_review_form" value="1" ' . checked(1, $settings['enable_review_form'], false) . ' />';
        echo __('Enable Review Form', 'wp_cr');
        echo '</label>';
        echo '</p>';

        // Enable review list option
        echo '<p>';
        echo '<label for="enable_review_list">';
        echo '<input type="checkbox" id="enable_review_list" name="enable_review_list" value="1" ' . checked(1, $settings['enable_review_list'], false) . ' />';
        echo __('Enable Review List', 'wp_cr');
        echo '</label>';
        echo '</p>';
    }
    // Save meta box data
    public function cr_save_meta_box_data($post_id) {
        // Check nonce for security
        if (!isset($_POST['cr_meta_box_nonce']) || !wp_verify_nonce($_POST['cr_meta_box_nonce'], 'cr_meta_box_nonce')) {
            return;
        }

        // Check if the user has permission to save data
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save the settings
        $settings = [
            'enable_reviews' => isset($_POST['enable_reviews']) ? 1 : 0,
            'enable_review_form' => isset($_POST['enable_review_form']) ? 1 : 0,
            'enable_review_list' => isset($_POST['enable_review_list']) ? 1 : 0,
        ];
        update_option('customer_reviews_settings', $settings);
    }
 

    // Add menu pages
    public function add_admin_menu() {
        add_menu_page(
            'All Reviews',
            'All Reviews',
            'manage_options',
            'wp-review-plugin',
            array($this, 'display_reviews_page'),
            'dashicons-star-filled'
        );

        add_submenu_page(
            'wp-review-plugin',
            'Review Settings',
            'Review Settings',
            'manage_options',
            'wp-review-settings',
            array($this, 'display_settings_page')
        );
    }



    // Display Review Settings page
    public function display_settings_page() {
 
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        include CR_PLUGIN_PATH . 'includes/views/cr-settings.php';
        $this->save_review_settings();
    

}

    // Save Review Settings
    private function save_review_settings() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $settings = [
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
            wp_enqueue_script('review-script', CR_PLUGIN_ASSETS . 'js/review-script.js', ['jquery'], '1.0.0', true);
            wp_localize_script('review-script', 'cr_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);
            wp_enqueue_style('review-style', CR_PLUGIN_ASSETS . 'css/cr-frontend.css', [], '1.0.0');
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
            
            if ($screen && $screen->id === 'all-reviews_page_wp-review-settings') {
                    wp_enqueue_style('wp-review-admin', CR_PLUGIN_ASSETS . 'css/cr-admin.css', [], '1.0.0');
            }
            
            wp_enqueue_script('cr-admin-script', CR_PLUGIN_ASSETS . 'js/cr-admin.js', ['jquery'], '1.0.0', true);
            wp_localize_script('cr-admin-script', 'cradmin_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);
    }

    private function sanitize_post_data($data) {
            return array_map('sanitize_text_field', $data);
    }

    public function submit_review() {
            $data = $this->sanitize_post_data($_POST);
        
            $review_data = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'website' => $data['website'],
                    'city' => $data['city'],
                    'state' => $data['state'],
                    'rating' => intval($data['rating']),
                    'comment' => $data['comment'],
                    'status' => 'pending',
                    'positionid' => intval($data['positionid'])
            ];

            $this->model->add_review($review_data);

            // Notify admin via email
            $this->notify_admin_of_pending_review($review_data);

            // Send success response
            wp_send_json([
                    'success' => true,
                    'message' => 'Review submitted successfully!',
                    'reviews' => $this->get_review_list()
            ]);
    }

    private function notify_admin_of_pending_review($review_data) {
        $admin_email = get_option('admin_email');
        $subject = __('New Pending Review Submitted', 'wp_cr');
        $message = __("A new review has been submitted and is pending approval.\n\n", 'wp_cr');

        $message .= __("Please log in to the admin panel to review and approve it.\n", 'wp_cr');
        $message .= sprintf(
            __("Pending reviews can be viewed here: %s", 'wp_cr'),
            admin_url('admin.php?page=wp-review-plugin&status=pending')
        );

        wp_mail($admin_email, $subject, $message);
    }



public function get_review_list() {
    ob_start();
    include plugin_dir_path(__FILE__) . '../views/cr-list.php';
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
    $this->model->update_review($id, $data);
    wp_send_json(['success' => true, 'data' => $data]);
   
}

public function customer_reviews_form_shortcode() {
    ob_start();
    include CR_PLUGIN_PATH . 'includes/views/cr-form.php';
    return ob_get_clean();

}

public function customer_reviews_list_shortcode() {
    ob_start();
    include CR_PLUGIN_PATH . 'includes/views/cr-list.php';
    return ob_get_clean();
}


}


new Review_Controller();
?>
