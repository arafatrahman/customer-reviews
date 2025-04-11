
<?php if (!defined('ABSPATH')) exit; ?>

<div class="customer-reviews-form-container">
    <h3>Submit Your Review</h3>
    <form id="customer-reviews-form">
        <?php 
        $fields = ['Name', 'Email', 'Website', 'Phone', 'City', 'State', 'Comment'];
        $settings = get_option('customer_reviews_settings')['fields'] ?? [];

        foreach ($fields as $field): 

            
            $field_key = strtolower($field);
            $is_required = $settings[$field]['require'] ?? 0;
            $is_shown = $settings[$field]['show'] ?? 0;
            $label_name = $settings[$field]['label'] ?? $field;
            

            if ($is_shown): ?>
                <div class="form-group">
                    <label><?= esc_html($label_name) ?></label>
                    <?php if ($field === 'Comment'): ?>
                        <textarea name="<?= esc_attr($field_key) ?>" <?= $is_required ? 'required' : '' ?>></textarea>
                    <?php else: ?>
                        <input type="<?= $field === 'Email' ? 'email' : 'text' ?>" name="<?= esc_attr($field_key) ?>" <?= $is_required ? 'required' : '' ?>>
                    <?php endif; ?>
                </div>
            <?php endif; 
        endforeach; ?>

        <div class="form-group">
            <label><?php echo esc_html__('Rating', 'wp_cr'); ?></label>
            <div class="rating">
                <input type="radio" name="rating" value="5" id="star5"><label for="star5">★</label>
                <input type="radio" name="rating" value="4" id="star4"><label for="star4">★</label>
                <input type="radio" name="rating" value="3" id="star3"><label for="star3">★</label>
                <input type="radio" name="rating" value="2" id="star2"><label for="star2">★</label>
                <input type="radio" name="rating" value="1" id="star1"><label for="star1">★</label>
            </div>
        </div>

        <button type="submit" class="submit-button"><?php echo esc_html__('Submit Review', 'wp_cr'); ?></button>
    </form>
    <p id="review-message"></p>
</div>

<?php if (!defined('ABSPATH')) exit; ?>


