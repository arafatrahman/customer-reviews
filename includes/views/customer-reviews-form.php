
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

            if ($is_shown): ?>
                <div class="form-group">
                    <label><?= esc_html($field) ?></label>
                    <?php if ($field === 'Comment'): ?>
                        <textarea name="<?= esc_attr($field_key) ?>" <?= $is_required ? 'required' : '' ?>></textarea>
                    <?php else: ?>
                        <input type="<?= $field === 'Email' ? 'email' : 'text' ?>" name="<?= esc_attr($field_key) ?>" <?= $is_required ? 'required' : '' ?>>
                    <?php endif; ?>
                </div>
            <?php endif; 
        endforeach; ?>

        <div class="form-group">
            <label>Rating</label>
            <div class="rating">
                <input type="radio" name="rating" value="5" id="star5"><label for="star5">★</label>
                <input type="radio" name="rating" value="4" id="star4"><label for="star4">★</label>
                <input type="radio" name="rating" value="3" id="star3"><label for="star3">★</label>
                <input type="radio" name="rating" value="2" id="star2"><label for="star2">★</label>
                <input type="radio" name="rating" value="1" id="star1"><label for="star1">★</label>
            </div>
        </div>

        <button type="submit" class="submit-button">Submit Review</button>
    </form>
    <p id="review-message"></p>
    <h3>Previous Reviews</h3>
    <div id="reviews-container"><?php include CR_PLUGIN_PATH . 'includes/views/review-list.php'; ?></div>
</div>

<?php if (!defined('ABSPATH')) exit; ?>


