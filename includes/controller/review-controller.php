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
        add_shortcode('easy_review_form', [$this,'easy_review_form_shortcode']);

        add_action('wp_ajax_submit_review', [$this, 'submit_review']);
        add_action('wp_ajax_nopriv_submit_review', [$this, 'submit_review']);
        add_action('wp_ajax_admin_reply_review', [$this, 'admin_reply_review']);
        
    }

    public function easy_review_form_shortcode() {
        ob_start();
        include CR_PLUGIN_PATH . 'includes/views/customer-reviews-form.php';
        return ob_get_clean();

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
        
        
    
    echo '<div class="wrap">';
    echo '<h1>Review Settings</h1>';
    $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';

    echo '<h2 class="nav-tab-wrapper">';
    echo '<a href="?page=wp-review-settings&tab=general" class="nav-tab ' . ($active_tab === 'general' ? 'nav-tab-active' : '') . '">General</a>';
    echo '<a href="?page=wp-review-settings&tab=advanced" class="nav-tab ' . ($active_tab === 'advanced' ? 'nav-tab-active' : '') . '">Advanced</a>';
    echo '</h2>';

    echo '<form method="post" action="">';
    if ($active_tab === 'general') {
        echo '<h3>General Settings</h3>';
        echo '<label for="enable_reviews">';
        echo '<input type="checkbox" name="enable_reviews" id="enable_reviews" value="1" ' . checked(1, get_option('customer_reviews_settings')['enable_reviews'], false) . '>';
        echo ' Enable Reviews</label><br>';
        echo '<label for="default_status">Default Status:</label>';
        echo '<select name="default_status" id="default_status">';
        echo '<option value="pending" ' . selected('pending', get_option('customer_reviews_settings')['default_status'], false) . '>Pending</option>';
        echo '<option value="approved" ' . selected('approved', get_option('customer_reviews_settings')['default_status'], false) . '>Approved</option>';
        echo '</select>';
    } elseif ($active_tab === 'advanced') {
        echo '<h3>Advanced Settings</h3>';
        echo '<p>Additional advanced settings can be added here.</p>';
    }
    submit_button('Save Settings');
    echo '</form>';
    echo '</div>';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->save_review_settings();
    }

}

    // Save Review Settings
    private function save_review_settings() {
        $settings = [
            'enable_reviews' => isset($_POST['enable_reviews']) ? 1 : 0,
            'default_status' => sanitize_text_field($_POST['default_status']),
        ];

        update_option('customer_reviews_settings', $settings);

        echo '<div class="updated"><p>Settings saved successfully.</p></div>';
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
        wp_enqueue_script('review-script', CR_PLUGIN_ASSETS . 'js/review-script.js', ['jquery'], null, true);
        wp_localize_script('review-script', 'cr_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);
        wp_enqueue_style('review-style', CR_PLUGIN_ASSETS . 'css/review-style.css', [], null);
    }

    private function sanitize_post_data($data) {
        return array_map('sanitize_text_field', $data);
    }

    public function submit_review() {
    $data = $this->sanitize_post_data($_POST);
    $review_data = [
            'author_name' => $data['author_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'city' => $data['city'],
            'state' => $data['state'],
            'rating' => $data['rating'],
            'comments' => $data['comments'],
            'status' => 'pending'
    ];



    $this->model->add_review($review_data);

    wp_send_json([
        'success' => true,
        'message' => 'Review submitted successfully!',
        'reviews' => $this->get_review_list()
    ]);
}

public function get_review_list() {
    ob_start();
    include plugin_dir_path(__FILE__) . '../views/review-list.php';
    return ob_get_clean();
}



public function admin_reply_review() {
    $id = intval($_POST['review_id']);
    $reply = sanitize_textarea_field($_POST['admin_reply']);

    $this->model->update_review($id, ['admin_reply' => $reply]);

    wp_send_json(['success' => true, 'reply' => $reply]);
}


}
new Review_Controller();
?>
