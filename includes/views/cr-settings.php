<div class="wrap wp-review-settings-wrap">
    <h1>Customer Review Settings</h1>

    <h2 class="nav-tab-wrapper">
        <a href="#" class="nav-tab nav-tab-active" onclick="showTab(event, 'general')">General</a>
        <a href="#" class="nav-tab" onclick="showTab(event, 'review_form')">Review Form Settings</a>
        <a href="#" class="nav-tab" onclick="showTab(event, 'display')">Shortcodes</a>
    </h2>

    <form method="post" action="" class="wp-review-settings-form">
        <!-- General Settings -->
        <div class="form-group tab-section" id="tab-general">
            <h3><?php esc_html_e('General Settings', 'wp_cr'); ?></h3>
            <label for="reviews_per_page"><?php esc_html_e('Reviews shown per page:', 'wp_cr'); ?></label>
            <input type="number" name="reviews_per_page" id="reviews_per_page" 
                value="<?= esc_attr(get_option('customer_reviews_settings')['reviews_per_page'] ?? 12) ?>">

                <label for="star_color"><?php esc_html_e('Star Color:', 'wp_cr'); ?></label>
                <input type="color" name="star_color" id="star_color" 
                    value="<?= esc_attr(get_option('customer_reviews_settings')['star_color'] ?? '#fbbc04') ?>">
                
        </div>

        <!-- Review Form Settings -->
        <div class="tab-section" id="tab-review_form" style="display:none;">
            <h3><?php esc_html_e('Review Form Settings', 'wp_cr'); ?></h3>
            <?php 
            $fields = ['Name', 'Email', 'Website', 'Phone', 'City', 'State', 'Comment'];
            foreach ($fields as $field): ?>
                <fieldset>
                    <legend>
                        <input type="text" name="fields[<?= esc_attr($field) ?>][label]" 
                            value="<?= esc_attr(get_option('customer_reviews_settings')['fields'][$field]['label'] ?? $field) ?>" 
                            placeholder="<?= esc_attr($field) ?>">
                    </legend>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="fields[<?= esc_attr($field) ?>][require]" value="1"
                            <?= checked(1, get_option('customer_reviews_settings')['fields'][$field]['require'] ?? 0, false) ?>> Require</label>
                        <label><input type="checkbox" name="fields[<?= esc_attr($field) ?>][show]" value="1"
                            <?= checked(1, get_option('customer_reviews_settings')['fields'][$field]['show'] ?? 0, false) ?>> Show</label>
                    </div>
                </fieldset>
            <?php endforeach; ?>
        </div>

        <!-- Display Settings -->
        <div class="form-group tab-section" id="tab-display" style="display:none;">
            <h3><?php esc_html_e('Display Settings', 'wp_cr'); ?></h3>
            <div class="shortcode-section">
                <label for="shortcode">Form Shortcode:</label>
                <input type="text" id="shortcode" value="[wp_cr_form]" readonly>
                <button type="button" class="copy-button" onclick="navigator.clipboard.writeText('[wp_cr_form]')">Copy</button>
            </div>
            <div class="shortcode-section">
                <label for="shortcode">Review Lists Shortcode:</label>
                <input type="text" id="shortcode" value="[wp_cr_lists]" readonly>
                <button type="button" class="copy-button" onclick="navigator.clipboard.writeText('[wp_cr_lists]')">Copy</button>
            </div>
        </div>

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
