<div class="wrap wp-review-settings-wrap">
    <h1>Customer Review Settings</h1>

    <?php
    $active_tab = get_option('customer_reviews_settings')['active_tab'] ?? 'general';
    
    ?>
 
    <h2 class="nav-tab-wrapper">
        <a href="#" id="ctrw-general-tab" class="nav-tab<?= $active_tab === 'general' ? ' nav-tab-active' : '' ?>" onclick="showTab(event, 'general')">General</a>
        <a href="#" id="ctrw-review-form-tab"class="nav-tab <?= $active_tab == 'review_form' ? ' nav-tab-active' : '' ?>" onclick="showTab(event, 'review_form')">Review Form Settings</a>
        <a href="#" id="ctrw-advanced-tab"class="nav-tab <?= $active_tab == 'shortcodes' ? ' nav-tab-active' : '' ?>" onclick="showTab(event, 'display')">Shortcodes</a>
        <a href="#" id="ctrw-advanced-tab" class="nav-tab <?= $active_tab == 'advanced' ? ' nav-tab-active' : '' ?>" onclick="showTab(event, 'advanced')">Advanced Settings</a>

    </h2>

    <form method="post" action="" class="wp-review-settings-form">
        <!-- General Settings -->
        <div class="form-group tab-section" id="tab-general">
            <h3><?php esc_html_e('General Settings', 'wp_cr'); ?></h3>
            <div style="display: flex; flex-wrap: wrap; gap: 24px;">
                <div style="flex: 1 1 0; min-width: 260px;">
                    <label for="reviews_per_page"><?php esc_html_e('Reviews shown per page:', 'wp_cr'); ?></label>
                    <input type="number" name="reviews_per_page" id="reviews_per_page" 
                        value="<?= esc_attr(get_option('customer_reviews_settings')['reviews_per_page'] ?? 12) ?>">

                    <label for="date_format"><?php esc_html_e('Date Format:', 'wp_cr'); ?></label>
                    <select name="date_format" id="date_format">
                        <option value="MM/DD/YYYY" <?= selected(get_option('customer_reviews_settings')['date_format'] ?? '', 'MM/DD/YYYY', false) ?>>MM/DD/YYYY</option>
                        <option value="DD/MM/YYYY" <?= selected(get_option('customer_reviews_settings')['date_format'] ?? '', 'DD/MM/YYYY', false) ?>>DD/MM/YYYY</option>
                        <option value="YYYY/MM/DD" <?= selected(get_option('customer_reviews_settings')['date_format'] ?? '', 'YYYY/MM/DD', false) ?>>YYYY/MM/DD</option>
                    </select>

                    <label for="include_time"><input type="checkbox" name="include_time" id="include_time" value="1" 
                    <?= checked(1, get_option('customer_reviews_settings')['include_time'] ?? 0, false) ?>> 
                    <?php esc_html_e('Include Time', 'wp_cr'); ?>
                    <span class="ctrw-tooltip">
					<span class="dashicons dashicons-editor-help"></span>
					<span class="tooltiptext tooltip-right-msg">Display a time stamp behind the date on each comment.</span>
				    </span>
                    
                
                   </label>
                    <label for="enable_email_notification"><input type="checkbox" name="enable_email_notification" id="enable_email_notification" value="1"
                    <?= checked(1, get_option('customer_reviews_settings')['enable_email_notification'] ?? 1, false) ?>> 
                      <?php esc_html_e('Enable Admin Email Notification', 'wp_cr'); ?>
                    <span class="ctrw-tooltip">
					<span class="dashicons dashicons-editor-help"></span>
					<span class="tooltiptext tooltip-right-msg">Email(s) designated under 'Advanced Settings' tab will receive a notification from each review entered in the plugins form.</span>
				    </span>
                    </label>
                  
                    <label for="enable_customer_email_notification"><input type="checkbox" name="enable_customer_email_notification" id="enable_customer_email_notification" value="1"
                    <?= checked(1, get_option('customer_reviews_settings')['enable_customer_email_notification'] ?? 0, false) ?>> 
                     <?php esc_html_e('Enable Customer Email Receipt', 'wp_cr'); ?>
                    <span class="ctrw-tooltip">
					<span class="dashicons dashicons-editor-help"></span>
					<span class="tooltiptext tooltip-right-msg">Users receive a receipt from their form submission</span>
				    </span>
                   </label>
                    <label for="auto_approve_reviews">
                        <input type="checkbox" name="auto_approve_reviews" id="auto_approve_reviews" value="1"
                        <?= checked(1, get_option('customer_reviews_settings')['auto_approve_reviews'] ?? 0, false) ?>>
                      <?php esc_html_e('Enable Automatic Review Approval', 'wp_cr'); ?>
                        <span class="ctrw-tooltip">
					<span class="dashicons dashicons-editor-help"></span>
					<span class="tooltiptext tooltip-right-msg">New reviews are immediately posted for viewing without admin review.</span>
				    </span>
                       
                    </label>
                    <label for="show_city">
                        <input type="checkbox" name="show_city" id="show_city" value="1"
                        <?= checked(1, get_option('customer_reviews_settings')['show_city'] ?? 0, false) ?>>
                     <?php esc_html_e('Show City in Review List', 'wp_cr'); ?>
                        <span class="ctrw-tooltip">
					<span class="dashicons dashicons-editor-help"></span>
					<span class="tooltiptext tooltip-right-msg">This option allows the city to be displayed with each comment.</span>
				    </span>
                    
                    </label>
                    <label for="show_state">
                        <input type="checkbox" name="show_state" id="show_state" value="1"
                        <?= checked(1, get_option('customer_reviews_settings')['show_state'] ?? 0, false) ?>>
          <?php esc_html_e('Show State in Review List', 'wp_cr'); ?>
                        <span class="ctrw-tooltip">
					<span class="dashicons dashicons-editor-help"></span>
					<span class="tooltiptext tooltip-right-msg">This option allows the state to be displayed with each comment.</span>
				    </span>
                      
                    </label>    <label for="enable_review_title">
                            <input type="checkbox" name="enable_review_title" id="enable_review_title" value="1"
                            <?= checked(1, get_option('customer_reviews_settings')['enable_review_title'] ?? 1, false) ?>>
                               <?php esc_html_e('Enable Review Title', 'wp_cr'); ?>
                            <span class="ctrw-tooltip">
					<span class="dashicons dashicons-editor-help"></span>
					<span class="tooltiptext tooltip-right-msg">This option allows the review title to be displayed with each comment.</span>
				    </span>
                         
                        </label>

                    <label for="name_font_weight"><?php esc_html_e('Name Font Weight:', 'wp_cr'); ?></label>
                    <select name="name_font_weight" id="name_font_weight">
                        <option value="normal" <?= selected(get_option('customer_reviews_settings')['name_font_weight'] ?? '', 'normal', false) ?>>Normal</option>
                        <option value="bold" <?= selected(get_option('customer_reviews_settings')['name_font_weight'] ?? '', 'bold', false) ?>>Bold</option>
                    </select>
                    
                
                </div>
                <div style="flex: 1 1 0; min-width: 260px;">
                    

                   

                    <label for="comment_font_size"><?php esc_html_e('Comment Font Size In Pixels:', 'wp_cr'); ?></label>
                    <input type="number" name="comment_font_size" id="comment_font_size" 
                        value="<?= esc_attr(get_option('customer_reviews_settings')['comment_font_size'] ?? 14) ?>" min="1">
                    
                    
                    <label for="comment_line_height"><?php esc_html_e('Comment Line Height In Pixels:', 'wp_cr'); ?></label>
                    <input type="number" step="0.1" min="1" name="comment_line_height" id="comment_line_height"
                        value="<?= esc_attr(get_option('customer_reviews_settings')['comment_line_height'] ?? 23) ?>">

        <label for="comment_font_style"><?php esc_html_e('Comment Font Style:', 'wp_cr'); ?></label>
                    <select name="comment_font_style" id="comment_font_style">
                        <option value="normal" <?= selected(get_option('customer_reviews_settings')['comment_font_style'] ?? '', 'normal', false) ?>>Normal</option>
                        <option value="italic" <?= selected(get_option('customer_reviews_settings')['comment_font_style'] ?? '', 'italic', false) ?>>Italic</option>
                    </select>

                    <label for="comment_box_fill_color"><?php esc_html_e('Comment Box Fill Color:', 'wp_cr'); ?></label>
                    <?php
                    // Enqueue WordPress color picker scripts/styles
                    if (function_exists('wp_enqueue_style')) {
                        wp_enqueue_style('wp-color-picker');
                        wp_enqueue_script('wp-color-picker');
                    }
                    $comment_box_fill_color = get_option('customer_reviews_settings')['comment_box_fill_color'] ?? '#f5f5f5';
                    ?>
                    <input type="text" name="comment_box_fill_color" id="comment_box_fill_color"
                        value="<?= esc_attr($comment_box_fill_color) ?>" class="wp-color-picker-field" data-default-color="#f5f5f5">

                    <label for="star_color"><?php esc_html_e('Star Color:', 'wp_cr'); ?></label>
                    <?php
                    $star_color = get_option('customer_reviews_settings')['star_color'] ?? '#fbbc04';
                    ?>
                    <input type="text" name="star_color" id="star_color"
                        value="<?= esc_attr($star_color) ?>" class="wp-color-picker-field" data-default-color="#fbbc04">
                    <script>
                    jQuery(document).ready(function($){
                        $('#star_color').wpColorPicker();
                        $('#comment_box_fill_color').wpColorPicker();
                    });
                    </script>
                </div>
            </div>
        </div>

        <!-- Review Form Settings -->
        <div class="tab-section" id="tab-review_form" style="display:none;">
            <h3><?php esc_html_e('Review Form Settings', 'wp_cr'); ?></h3>
            <div style="display: flex; flex-wrap: wrap; gap: 24px;">
            <?php 
            $fields = ['Name', 'Email', 'Website', 'Phone', 'City', 'State', 'Title', 'Comment', 'Rating'];
            $half = ceil(count($fields) / 2);
            $columns = [array_slice($fields, 0, $half), array_slice($fields, $half)];
            foreach ($columns as $colFields): ?>
                <div style="flex: 1 1 0; min-width: 220px;">
                <?php foreach ($colFields as $field): ?>
                    <fieldset style="margin-bottom: 18px;">
                    <legend>
                        <input type="text" name="fields[<?= esc_attr($field) ?>][label]" 
                        value="<?= esc_attr(get_option('customer_reviews_settings')['fields'][$field]['label'] ?? $field) ?>" 
                        placeholder="<?= esc_attr($field) ?>">
                    </legend>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="fields[<?= esc_attr($field) ?>][require]" value="1"
                        <?= checked(1, get_option('customer_reviews_settings')['fields'][$field]['require'] ?? 0, false) ?>> Require</label>
                            <label>
                                <input type="checkbox" name="fields[<?= esc_attr($field) ?>][show]" value="1"
                                <?= checked(1, get_option('customer_reviews_settings')['fields'][$field]['show'] ?? 0, false) ?>> Show
                            </label>

                    </div>
                    </fieldset>
                <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            </div>
        </div>

        <!-- Shortcodes -->
        <div class="form-group tab-section" id="tab-display" style="display:none;">
            <h3><?php esc_html_e('Shortcodes', 'wp_cr'); ?></h3>
            <div class="shortcode-section">
                <label for="shortcode">Form Shortcode:</label>
                <input type="text" id="shortcode" value="[wp_ctrw_form]" readonly>
                <button type="button" class="copy-button" onclick="navigator.clipboard.writeText('[wp_ctrw_form]')">Copy</button>
            </div>
            <div class="shortcode-section">
                <label for="shortcode">Review Lists Shortcode:</label>
                <input type="text" id="shortcode" value="[wp_ctrw_lists]" readonly>
                <button type="button" class="copy-button" onclick="navigator.clipboard.writeText('[wp_ctrw_lists]')">Copy</button>
            </div>
        </div>

        <div class="form-group tab-section" id="tab-advanced" style="display:none;">
        <h3><?php esc_html_e('Advanced Settings', 'wp_cr'); ?></h3>
        <label for="replace_woocommerce_reviews">
                        <input type="checkbox" name="replace_woocommerce_reviews" id="replace_woocommerce_reviews" value="1"
                        <?= checked(1, get_option('customer_reviews_settings')['replace_woocommerce_reviews'] ?? 0, false) ?>>
                        <?php esc_html_e('Replace WooCommerce Default Review System', 'wp_cr'); ?>
                    </label>

        <div style="margin-top: 18px;">
            <label for="notification_admin_emails">
                <?php esc_html_e('Notification Admin Emails', 'wp_cr'); ?>
                <span class="ctrw-tooltip">
					<span class="dashicons dashicons-editor-help"></span>
					<span class="tooltiptext tooltip-right-msg">If more than one email, separate with a comma</span>
				</span>
            </label>
            <?php
                $settings = get_option('customer_reviews_settings');
                $admin_email = get_option('admin_email');
                $emails = isset($settings['notification_admin_emails']) && trim($settings['notification_admin_emails']) !== ''
                    ? $settings['notification_admin_emails']
                    : $admin_email;
            ?>
            <input type="text" name="notification_admin_emails" id="notification_admin_emails"
                value="<?= esc_attr($emails); ?>"
                placeholder="<?= esc_attr($admin_email); ?>"
                style="width: 100%;">
        </div>

        </div>

        <input type="hidden" name="active_tab" id="ctrw-active-tab" value="<?= esc_attr(get_option('customer_reviews_settings')['active_tab'] ?? 'general') ?>">
        
        <?php submit_button('Save Settings', 'primary', 'submit', true); ?>
    </form>
</div>

<script>
function showTab(e, tabId) {
    e.preventDefault();
    document.querySelectorAll('.tab-section').forEach(tab => tab.style.display = 'none');
    document.querySelectorAll('.nav-tab').forEach(tab => tab.classList.remove('nav-tab-active'));
    document.getElementById('tab-' + tabId).style.display = 'block';
    e.currentTarget.classList.add('nav-tab-active');
}
</script>
