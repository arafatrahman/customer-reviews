<div class="wrap wp-review-settings-wrap">
    <h1>Customer Review Settings</h1>

    <h2 class="nav-tab-wrapper">
        <a href="?page=wp-review-settings&tab=general" class="nav-tab <?= ($active_tab === 'general' ? 'nav-tab-active' : '') ?>">General</a>
        <a href="?page=wp-review-settings&tab=review_form" class="nav-tab <?= ($active_tab === 'review_form' ? 'nav-tab-active' : '') ?>">Review Form Settings</a>
        <a href="?page=wp-review-settings&tab=display" class="nav-tab <?= ($active_tab === 'display' ? 'nav-tab-active' : '') ?>">Display Settings</a>
    </h2>

    <form method="post" action="" class="wp-review-settings-form">
        <?php if ($active_tab === 'general'): ?>
            <div class="form-group">
                <h3>General Settings</h3>
                <label for="reviews_per_page">Reviews shown per page:</label>
                <input type="number" name="reviews_per_page" id="reviews_per_page" 
                    value="<?= esc_attr(get_option('customer_reviews_settings')['reviews_per_page'] ?? 10) ?>">
            </div>
        <?php elseif ($active_tab === 'review_form'): ?>
            <h3>Review Form Settings</h3>
            <?php 
            $fields = ['Name', 'Email', 'Website', 'Phone', 'City', 'State', 'Comment'];
            foreach ($fields as $field): ?>
                <fieldset>
                    <legend><?= esc_html($field) ?></legend>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="fields[<?= esc_attr($field) ?>][require]" value="1"
                            <?= checked(1, get_option('customer_reviews_settings')['fields'][$field]['require'] ?? 0, false) ?>> Require</label>
                        <label><input type="checkbox" name="fields[<?= esc_attr($field) ?>][show]" value="1"
                            <?= checked(1, get_option('customer_reviews_settings')['fields'][$field]['show'] ?? 0, false) ?>> Show</label>
                    </div>
                </fieldset>
            <?php endforeach; ?>
        <?php elseif ($active_tab === 'display'): ?>
            <div class="form-group">
                  <h3>Display Settings</h3>

                                  
                  <div class="shortcode-section">
                        <label for="shortcode">Shortcode:</label>
                        <input type="text" id="shortcode" value="[customer_reviews]" readonly>
                        <button type="button" class="copy-button" onclick="navigator.clipboard.writeText('[customer_reviews]')">Copy</button>
                       
                  </div>

                  
            </div>
        <?php endif; ?>

        <?php submit_button('Save Settings', 'primary', 'submit', true); ?>
    </form>
</div>
