<?php
if (!defined('ABSPATH')) {
    exit;
}

class Review_Controller {
    private $model;
    private $view;

    public function __construct() {
        $this->model = new CTRW_Review_Model();
        $this->view = new Review_View();
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_filter('plugin_action_links_' . CTRW_BASE_NAME, array($this, 'ctrw_plugin_action_links'));

        //Add plugin description link
        add_filter('plugin_row_meta', array($this, 'add_ctrw_description_link'), 10, 2);
        add_filter('plugin_row_meta', array($this, 'add_ctrw_details_link'), 10, 4);

        add_action('wp_enqueue_scripts', [$this,'review_enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this,'wp_review_admin_styles']);

        add_action('wp_ajax_ctrw_save_settings', [$this, 'ctrw_save_settings']);
        
        add_shortcode('wp_ctrw_form', [$this,'customer_reviews_form_shortcode']);
        add_shortcode('wp_ctrw_summary', [$this,'ctrw_display_summary']);
        add_shortcode('wp_ctrw_lists', [$this,'customer_reviews_list_shortcode']);
        add_shortcode('wp_ctrw_widget', [$this,'ctrw_display_floating_widget']);
        add_shortcode('wp_ctrw_slider', [$this,'ctrw_display_slider']);


        add_action('wp_ajax_submit_review', [$this, 'submit_review']);
        add_action('wp_ajax_nopriv_submit_review', [$this, 'submit_review']);
        add_action('wp_ajax_save_review_reply', [$this, 'save_review_reply']);
        add_action('wp_ajax_nopriv_save_review_reply', [$this, 'save_review_reply']);

        add_action('wp_ajax_edit_customer_review', [$this, 'edit_customer_review']);
        add_action('wp_ajax_nopriv_edit_customer_review', [$this, 'edit_customer_review']);
        
        add_action('wp_ajax_ctrw_import_review_from_others', [$this, 'ctrw_import_reviews']);
        add_action('wp_ajax_nopriv_ctrw_import_review_from_others', [$this, 'ctrw_import_reviews']);
        add_action( 'add_meta_boxes', [$this, 'ctrw_add_meta_box' ]);
        add_action( 'save_post', [$this, 'ctrw_save_meta_box_data' ] );

   //     add_filter('the_content', [$this, 'append_customer_reviews_shortcode']);

        // Hook to display under product titles - adjust priority as needed
      add_action('woocommerce_after_shop_loop_item_title', array($this, 'ctrw_display_product_review_info'), 15);
      add_action('woocommerce_single_product_summary', array($this, 'ctrw_display_product_review_info'), 7);

            add_action('wp_head', array($this, 'ctrw_output_schema_markup'));


         
    }

    // Import reviews via AJAX
    public function ctrw_import_reviews() {
        // Check nonce for security if you use one (recommended)
        // if (!isset($_POST['ctrw_import_nonce']) || !wp_verify_nonce($_POST['ctrw_import_nonce'], 'ctrw_import_reviews')) {
        //     wp_send_json_error(['message' => __('Invalid nonce', 'wp_cr')]);
        // }


        $importPlugin = isset($_POST['ctrw_import_review']) ? sanitize_text_field($_POST['ctrw_import_review']) : '';
        if ($importPlugin == 'siteReviews') {
            $reviews = $this->model->import_reviews_from_site_reviews_plugin();
            
        }elseif ($importPlugin == 'wpCustomerReviews') {
                $reviews = $this->model->import_reviews_from_wp_customer_reviews();
            }
        else {
           wp_send_json_error(['message' => __('No reviews to import', 'wp_cr')]);
        }
        wp_send_json_success(['message' => __('Imported  Reviews', 'wp_cr')]);
        
    }

    
 


    public function make_review_checkbox_disabled($settings) {
        if(empty($this->model->check_replace_woocommerce_reviews())) {
            return $settings; // Return early if the setting is not enabled
        }
        foreach ($settings as &$setting) {
            if (isset($setting['id']) && $setting['id'] === 'woocommerce_enable_reviews') {
                $setting['default'] = 'no';
                $setting['custom_attributes'] = array('disabled' => 'disabled');
                $setting['desc'] .= ' <strong>(' . __('This Setting off by Customer Reviews plugin', 'your-plugin-textdomain') . ')</strong>';
            }
        }
        return $settings;
    }



    public function ctrw_reviews_after_title() {
        if(!empty($this->model->check_replace_woocommerce_reviews())) {

            echo '<div class="my-custom-section">';
            global $post;
            $review_count = $this->model->get_review_count_by_positionid($post->ID);
            $average_rating = $this->model->get_average_rating_by_positionid($post->ID);
            // Display average rating as stars
            if ($average_rating > 0) {
                $full_stars = floor($average_rating);
                $half_star = ($average_rating - $full_stars) >= 0.5 ? 1 : 0;
                $empty_stars = 5 - $full_stars - $half_star;

                echo '<div class="ctrw-average-rating-stars" style="font-size:1.2em;">';
                for ($i = 0; $i < $full_stars; $i++) {
                    echo '<span class="ctrw-star">&#9733;</span>'; // filled star
                }
                if ($half_star) {
                    echo '<span class="ctrw-star">&#189;</span>'; // half star (can use SVG or custom CSS for better look)
                }
                for ($i = 0; $i < $empty_stars; $i++) {
                    echo '<span class="ctrw-star" style="color:#ccc;">&#9733;</span>'; // empty star
                }
                 echo '<span> (' . sprintf(__('%d Customer reviews', 'wp_cr'), intval($review_count)) . ')</span>';
                echo '</div>';
            }
           
         
            echo '</div>';
        }
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

    /*
    // Append the customer reviews shortcode to the content
    public function append_customer_reviews_shortcode($content) {
        if (is_singular(['post', 'page', 'product'])) {

            $post_id = get_the_ID();
            $enable_reviews = get_post_meta($post_id, '_ctrw_enable_reviews', true);
            $enable_review_form = get_post_meta($post_id, '_ctrw_enable_review_form', true);
            $enable_review_list = get_post_meta($post_id, '_ctrw_enable_review_list', true);


            if ($enable_reviews) {

                if ($enable_review_list) {
                    $content .= do_shortcode('[wp_ctrw_lists]');
                }
                if ($enable_review_form) {
                    // Only append the form shortcode if it's not already present in the content
                    if (strpos($content, '[wp_ctrw_form]') === false) {
                        $content .= do_shortcode('[wp_ctrw_form]');
                    }
                }
                
            }
        }
    
        return $content;
    }
    */

    // Add meta box to the review post type
    public function ctrw_add_meta_box() {
        add_meta_box(
            'ctrw_meta_box',
            __('Customer Reviews', 'wp_ctrw'),
            [$this, 'render_ctrw_meta_box'],
            ['post', 'page'], // Post types
            'side',                      // Context: 'side' places it on the right
            'high'                       // Priority
        );
    }

    // Render the meta box content
    public function render_ctrw_meta_box($post) {

        // Retrieve current settings
        $settings = [
            'enable_reviews'      => get_post_meta($post->ID, '_ctrw_enable_reviews', true),
          //  'enable_review_form'  => get_post_meta($post->ID, '_ctrw_enable_review_form', true),
          //  'enable_review_list'  => get_post_meta($post->ID, '_ctrw_enable_review_list', true),
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
        /*
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
        */
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
     //   update_post_meta($post_id, '_ctrw_enable_review_form', isset($_POST['enable_review_form']) ? 1 : 0);
      //  update_post_meta($post_id, '_ctrw_enable_review_list', isset($_POST['enable_review_list']) ? 1 : 0);
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

      //  add_submenu_page( 'woocommerce', 'Reviews', 'Reviews', 'manage_options', 'customer-product-reviews', 'my_custom_submenu_page_callback' ); 

    }

    /*
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
    */





    // Display Review Settings page
    public function display_settings_page() {
 
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        include CTRW_PLUGIN_PATH . 'includes/views/admin/ctrw-settings-panel.php';
     //   $this->save_review_settings();
    

    }

    // Save Review Settings
    public function ctrw_save_settings() {
      
        check_ajax_referer('ctrw_nonce', 'security'); 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $settings = [
                'enable_email_notification' => isset($_POST['enable_email_notification']) ? 1 : 0,
                'enable_customer_email_notification' => isset($_POST['enable_customer_email_notification']) ? 1 : 0,
                'auto_approve_reviews' => isset($_POST['auto_approve_reviews']) ? 1 : 0,
                'show_city' => isset($_POST['show_city']) ? 1 : 0,
                'show_state' => isset($_POST['show_state']) ? 1 : 0,
                'enable_review_title' => isset($_POST['enable_review_title']) ? 1 : 0,                'name_font_weight' => sanitize_text_field($_POST['name_font_weight'] ?? 'normal'),
                'comment_font_size' => intval(sanitize_text_field($_POST['comment_font_size'] ?? 14)),
                'comment_line_height' => intval(sanitize_text_field($_POST['comment_line_height'] ?? 1.5)),
                'comment_font_style' => sanitize_text_field($_POST['comment_font_style'] ?? 'normal'),
                'comment_box_fill_color' => sanitize_hex_color($_POST['comment_box_fill_color'] ?? '#f9f9f9'),
                'reviews_per_page' => intval(sanitize_text_field($_POST['reviews_per_page'] ?? 10)),
                'date_format' => sanitize_text_field($_POST['date_format'] ?? 'MM/DD/YYYY'),
                'include_time' => isset($_POST['include_time']) ? 1 : 0,
                'star_color' => sanitize_hex_color($_POST['star_color'] ?? '#fbbc04'),

                'enabled_schema' => isset($_POST['enabled_schema']) ? 1 : 0,
                'business_name' => sanitize_text_field($_POST['business_name'] ?? get_bloginfo('name')),
                'default_description' => sanitize_text_field($_POST['default_description'] ?? get_bloginfo('description')),
                'default_url' => sanitize_text_field($_POST['default_url'] ?? ''),
                'custom_image_url' => sanitize_text_field($_POST['custom_image_url'] ?? ''),
                'business_address' => sanitize_text_field($_POST['business_address'] ?? ''),
                 'business_phone' => sanitize_text_field($_POST['business_phone'] ?? ''),
                 'price_range' => sanitize_text_field($_POST['price_range'] ?? '$'),
                'review_display_type' => sanitize_text_field($_POST['review_display_type'] ?? 'list'),
                'replace_woocommerce_reviews' => isset($_POST['replace_woocommerce_reviews']) ? 1 : 0,
                'notification_admin_emails' => isset($_POST['notification_admin_emails']) ? $_POST['notification_admin_emails'] : '',   
                'active_tab' => sanitize_text_field($_POST['active_tab'] ?? 'general'),
                'fields' => array_map(function($field) {
                    return array_map('sanitize_text_field', $field);
                }, $_POST['fields'] ?? [])
            ];
            update_option('customer_reviews_settings', $settings);
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
            wp_localize_script('cr-admin-script', 'ctrw_admin_ajax', ['ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('ctrw_nonce')]);
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
                    'title' => isset($data['title']) ? sanitize_text_field($data['title']) : '',
                    'comment' => $data['comment'],
                    'status' => $status,
                    'positionid' => intval($data['positionid'])
            ];

            $this->model->ctrw_add_review($review_data);

            // Notify admin via email
            $settings = get_option('customer_reviews_settings');
            if (!isset($settings['enable_email_notification']) || $settings['enable_email_notification']) {
                if (get_option('customer_reviews_settings')['enable_customer_email_notification'] ?? false) {
                    $customerEmail = $data['email'] ?? '';
                }
                else{
                    $customerEmail = '';
                }
                $this->notify_admin_of_pending_review($review_data, $customerEmail);
            }

            /*
            // Notify customer via email if enabled
            if (get_option('customer_reviews_settings')['enable_customer_email_notification'] ?? false) {
            
              $this->notify_customer_of_pending_review($data['email'],$data['name'],$status);
            }
            */

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

    private function notify_admin_of_pending_review($review_data,$customerEmail) {
        $admin_email = get_option('admin_email');
        $subject = sprintf(__('Customer Review Notification - %s', 'wp_cr'), $review_data['name']);

        $site_name = get_bloginfo('name');
        $site_url = get_site_url();
        $time = current_time('H:i:s');
        $date = current_time('Y-m-d');
        $remote_ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
       
        $html_message = '
        <div align="left">
            <p><font size="2" face="Verdana">Customer Review from the website ' . esc_html($site_name) . ':</font></p>
            <table border="0" cellspacing="1" cellpadding="3" bgcolor="silver">
                <tr>
                    <td bgcolor="#f5f5f5" width="193">
                        <div align="right">
                            <font size="2" face="Verdana">Name :</font></div>
                    </td>
                    <td bgcolor="white" width="491"><font size="2" face="Verdana">' . esc_html($review_data['name']) . '</font></td>
                </tr>
                <tr>
                    <td bgcolor="#f5f5f5" width="193">
                        <div align="right">
                            <font size="2" face="Verdana">Email :</font></div>
                    </td>
                    <td bgcolor="white" width="491"><font size="2" face="Verdana">' . esc_html($review_data['email']) . '</font></td>
                </tr>
                <tr>
                    <td bgcolor="#f5f5f5" width="193">
                        <div align="right">
                            <font size="2" face="Verdana">Website :</font></div>
                    </td>
                    <td bgcolor="white" width="491"><font size="2" face="Verdana">' . esc_html($review_data['website']) . '</font></td>
                </tr>
                <tr>
                    <td bgcolor="#f5f5f5" width="193">
                        <div align="right">
                            <font size="2" face="Verdana">Phone :</font></div>
                    </td>
                    <td bgcolor="white" width="491"><font size="2" face="Verdana">' . esc_html($review_data['phone']) . '</font></td>
                </tr>
                <tr>
                    <td bgcolor="#f5f5f5" width="193">
                        <div align="right">
                            <font size="2" face="Verdana">City :</font></div>
                    </td>
                    <td bgcolor="white" width="491"><font size="2" face="Verdana">' . esc_html($review_data['city']) . '</font></td>
                </tr>
                <tr>
                    <td bgcolor="#f5f5f5" width="193">
                        <div align="right"><font size="2" face="Verdana">State :</font></div>
                    </td>
                    <td bgcolor="white" width="491"><font size="2" face="Verdana">' . esc_html($review_data['state']) . '</font></td>
                </tr>
                <tr>
                    <td bgcolor="#f5f5f5" width="193">
                        <div align="right"><font size="2" face="Verdana">Review Title :</font></div>
                    </td>
                    <td bgcolor="white" width="491"><font size="2" face="Verdana">' . (isset($review_data['title']) ? esc_html($review_data['title']) : '') . '</font></td>
                </tr>
                <tr>
                    <td valign="top" bgcolor="#f5f5f5" width="193">
                        <div align="right">
                            <font size="2" face="Verdana">Comment :</font></div>
                    </td>
                    <td valign="top" bgcolor="white" width="491"><font size="2" face="Verdana">' . esc_html($review_data['comment']) . '</font></td>
                </tr>
                <tr>
                    <td valign="top" bgcolor="#f5f5f5" width="193">
                        <div align="right">
                            <font size="2" face="Verdana">Rating :</font></div>
                    </td>
                    <td valign="top" bgcolor="white" width="491"><font size="2" face="Verdana">' . esc_html($review_data['rating']) . '</font></td>
                </tr>
            </table>
            <p><font size="2" face="Verdana">This e-mail was sent from a review form found on ' . esc_html($site_name) . ' website ' . esc_url($site_url) . '</font></p>
            <p><font size="2" face="Verdana">Submission Details: ' . esc_html($time) . ', ' . esc_html($date) . ', ' . esc_html($remote_ip) . ', ' . esc_html($user_agent) . '</font></p>
            <p><font size="2" color="gray" face="Verdana">Notification Form Created by <a href="https://wordpress.org/plugins/customer-comments/" target="_blank">Customer Comments</a></font></p>
        </div>
        ';
        $settings = get_option('customer_reviews_settings');
        $admin_email = $settings['notification_admin_emails'] ?? get_option('admin_email');
        if (empty($admin_email)) {
            return; // No admin email set, exit early
        }

        // Add customer email to admin notification if available and valid
        if (!empty($customerEmail) && is_email($customerEmail)) {
            if (is_array($admin_email)) {
            $admin_email[] = $customerEmail;
            } else {
            $admin_email = [$admin_email, $customerEmail];
            }
        }
        if (is_array($admin_email)) {
            $admin_email = implode(',', $admin_email);
        }
        if (strpos($admin_email, ',') !== false) {
            $admin_email = array_map('trim', explode(',', $admin_email));
        }
        // Use default wp_mail() with standard headers so plugins like WP Mail SMTP work seamlessly
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . esc_html($site_name) . ' <' . esc_html($admin_email) . '>',
            'Reply-To: ' . esc_html($review_data['email']),
        ];
        $headers = implode("\r\n", $headers);
        wp_mail($admin_email, $subject, $html_message, $headers);

        
    }



public function get_review_list() {
    ob_start();
    include plugin_dir_path(__FILE__) . '../views/shortcodes/ctrw-list.php';
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

    $post_id = get_the_ID();
    $enable_reviews = get_post_meta($post_id, '_ctrw_enable_reviews', true);

    if (isset($enable_reviews) && $enable_reviews === '1') {
        // Include the form template
        ob_start();
        include CTRW_PLUGIN_PATH . 'includes/views/shortcodes/ctrw-form.php';
        return ob_get_clean();
    } else {
        return '<p>' . __('The Customer Review Is Currently Disabled For This Page', 'wp_cr') . '</p>';
    }  


}

public function customer_reviews_list_shortcode(){
    $post_id = get_the_ID();
    $enable_reviews = get_post_meta($post_id, '_ctrw_enable_reviews', true);

    if (isset($enable_reviews) && $enable_reviews === '1') {
        // Include the form template
        ob_start();
        include CTRW_PLUGIN_PATH . 'includes/views/shortcodes/ctrw-list.php';
        return ob_get_clean();
    } else {
        return '<p>' . __('The Customer Review Is Currently Disabled For This Page', 'wp_cr') . '</p>';
    }  

}

public function ctrw_display_summary() {

    $post_id = get_the_ID();
    $enable_reviews = get_post_meta($post_id, '_ctrw_enable_reviews', true);

    if (isset($enable_reviews) && $enable_reviews === '1') {
        // Include the form template
        ob_start();
        include CTRW_PLUGIN_PATH . 'includes/views/shortcodes/ctrw-summary.php';
        return ob_get_clean();
    } else {
        return '<p>' . __('The Customer Review Is Currently Disabled For This Page', 'wp_cr') . '</p>';
    }  

}

public function ctrw_display_floating_widget() {

    $post_id = get_the_ID();
    $enable_reviews = get_post_meta($post_id, '_ctrw_enable_reviews', true);

    if (isset($enable_reviews) && $enable_reviews === '1') {
        // Include the form template
        ob_start();
        include CTRW_PLUGIN_PATH . 'includes/views/shortcodes/ctrw-floating.php';
        return ob_get_clean();
    } else {
        return '<p>' . __('The Customer Review Is Currently Disabled For This Page', 'wp_cr') . '</p>';
    }  

}

public function ctrw_display_slider() {

    $post_id = get_the_ID();
    $enable_reviews = get_option('customer_reviews_settings');

    
    if (isset($enable_reviews['replace_woocommerce_reviews']) && $enable_reviews['replace_woocommerce_reviews'] == '1') {
        // Include the form template
        ob_start();
        include CTRW_PLUGIN_PATH . 'includes/views/shortcodes/ctrw-slider.php';
        return ob_get_clean();
    } else {
        return '<p>' . __('The Customer Review Is Currently Disabled For This Page', 'wp_cr') . '</p>';
    }  

}


// Function to display review info under product titles
      public function ctrw_display_product_review_info() {
            global $post;
            
            // Only run for products
            if (!is_a($post, 'WP_Post') || $post->post_type !== 'product') {
                  return;
            }

            $woocommerceSettings = get_option('ctrw_woocommerce_settings', array());
            if(!isset($woocommerceSettings['show_on_product_pages']) && $woocommerceSettings['show_on_product_pages'] != 'on'){
                  return;
            }
            
            $current_id = isset($post->ID) ? $post->ID : 0;

            // Get reviews - ensure it returns an array
            $reviews = (new CTRW_Review_Model())->get_review_by_id($current_id);

            // Calculate rating statistics
            $total_reviews = count($reviews);
            $total_rating = 0;
            
            foreach ($reviews as $review) {
                  $review_data = (array) $review;
                  $rating = (int) $review_data['rating'];
                  if ($rating >= 1 && $rating <= 5) {
                        $total_rating += $rating;
                  }
            }
            
            $average_rating = $total_reviews > 0 ? round($total_rating / $total_reviews, 1) : 0;
            
            // Get display settings
            $displaySettings = get_option('ctrw_display_settings', []);
            $star_color = $displaySettings['star_color'] ?? '#ffb100';
            
            // Generate stars HTML
            $stars_html = '';
            if ($total_reviews > 0) {
                  $full_stars = floor($average_rating);
                  $has_half_star = ($average_rating - $full_stars) >= 0.5;
                  
                  for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $full_stars) {
                        $stars_html .= '<i class="fas fa-star" style="color:' . esc_attr($star_color) . '"></i>';
                        } elseif ($i == $full_stars + 1 && $has_half_star) {
                        $stars_html .= '<i class="fas fa-star-half-alt" style="color:' . esc_attr($star_color) . '"></i>';
                        } else {
                        $stars_html .= '<i class="far fa-star" style="color:' . esc_attr($star_color) . '"></i>';
                        }
                  }
            }
            
            // Output the review info
            if ($total_reviews > 0) {
                  echo '<div class="ctrw-product-review-summary" style="margin: 5px 0 10px; font-size: 14px;">';
                  echo '<div class="ctrw-review-stars" style="display: inline-block; margin-right: 5px;">' . $stars_html . '</div>';
                  echo '<span class="ctrw-review-average" style="font-weight: bold; margin-right: 5px;">' . number_format($average_rating, 1) . '</span>';
                  echo '<span class="ctrw-review-count">(' . $total_reviews . ' ' . ($total_reviews === 1 ? 'review' : 'reviews') . ')</span>';
                  echo '</div>';
            } else {
                  echo '<div class="ctrw-product-review-summary" style="margin: 5px 0 10px; font-size: 14px; color: #666;">';
                  echo 'No reviews yet';
                  echo '</div>';
            }
      }


       public function ctrw_output_schema_markup() {
        $schemaSettings = get_option('customer_reviews_settings');
        if (empty($schemaSettings['enabled_schema'])) {
            return;
        }

        $review_count = $this->model->get_review_counts();
        $average_rating = $this->model->get_average_rating();
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $schemaSettings['business_name'],
            'image' => !empty($schemaSettings['custom_image_url']) ? $schemaSettings['custom_image_url'] : '',
            'telephone' => isset($schemaSettings['business_phone']) ? $schemaSettings['business_phone'] : '',
            'priceRange' => isset($schemaSettings['price_range']) ? $schemaSettings['price_range'] : '',
            'address' => array(
                '@type' => 'PostalAddress',
                'streetAddress' => isset($schemaSettings['business_address']) ? $schemaSettings['business_address'] : ''
            ),
            'aggregateRating' => array(
                  '@type' => 'AggregateRating',
                  'ratingValue' => isset($average_rating) ? $average_rating : '5.0',
                  'reviewCount' => isset($review_count['all']) ? $review_count['all'] : '1'
            ),
        );
        
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
    }




}


new Review_Controller();
?>
