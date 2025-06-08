
<?php if (!defined('ABSPATH')) exit; ?>

<div class="customer-reviews-form-container">
    <b class="ctrw-form-heading">Submit Your Review</b>
    <form id="customer-reviews-form">
        <?php 
        $fields = ['Name', 'Email', 'Website', 'Phone', 'City', 'State', 'Title', 'Comment', 'Rating'];
        $settings = get_option('customer_reviews_settings')['fields'] ?? [];

        foreach ($fields as $field): 
            $field_key = strtolower($field);

            // Show all fields by default if no settings
            if (empty($settings)) {
                $is_shown = 1;
                $is_required = 0;
                $label_name = $field;
            } else {
                $is_shown = $settings[$field]['show'] ?? 0;
                $is_required = $settings[$field]['require'] ?? 0;
                $label_name = $settings[$field]['label'] ?? $field;
            }

            if ($is_shown): ?>
                <div class="form-group">
                    <label><?= esc_html($label_name) ?></label>
                    <?php if ($field === 'Comment'): ?>
                        <textarea name="<?= esc_attr($field_key) ?>" <?= $is_required ? 'required' : '' ?>></textarea>
                   <?php elseif ($field === 'Rating'):?>
                        <div class="rating">
                            <input type="radio" name="<?= esc_attr($field_key) ?>" value="5" id="star5"><label for="star5">★</label>
                            <input type="radio" name="<?= esc_attr($field_key) ?>" value="4" id="star4"><label for="star4">★</label>
                            <input type="radio" name="<?= esc_attr($field_key) ?>" value="3" id="star3"><label for="star3">★</label>
                            <input type="radio" name="<?= esc_attr($field_key) ?>" value="2" id="star2"><label for="star2">★</label>
                            <input type="radio" name="<?= esc_attr($field_key) ?>" value="1" id="star1"><label for="star1">★</label>
                        </div>
                    <?php else: ?>
                        <input type="<?= $field === 'Email' ? 'email' : 'text' ?>" name="<?= esc_attr($field_key) ?>" <?= $is_required ? 'required' : '' ?>>
                    <?php endif; ?>
                </div>
            <?php endif; 
        endforeach; ?>

        <button type="submit" class="submit-button"><?php echo esc_html__('Submit Review', 'wp_cr'); ?></button>
    </form>
    <p id="review-message"></p>
</div>

<?php if (!defined('ABSPATH')) exit; ?>


