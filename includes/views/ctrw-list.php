
<?php
if (!defined('ABSPATH')) {
    exit;
}
$reviews = (new Review_Model())->get_reviews('approved');
?>
<div class="customer-reviews-form-container">
    <div id="reviews-container">
<div class="review-list">
    <?php
    $reviews_per_page = get_option('customer_reviews_settings')['reviews_per_page'] ?? 10;
    $reviews = array_slice($reviews, 0, $reviews_per_page);

    $user_id = get_current_user_id();
    $columns = get_user_meta($user_id, 'ctrw_column_visibility', true);

    // Set default columns if not set
    if (empty($columns)) {
        $columns = ['author', 'rating', 'date', 'comment', 'admin_reply'];
    }


    foreach ($reviews as $review) {

        if (get_the_ID() !== (int) $review->positionid && empty($review->positionid)) {
            continue;
        }
        ?>
        <div class="review-item">
            <div class="review-header">
                <?php if (in_array('author', $columns)) : ?>
                    <span class="review-author">
                        Posted By <?= esc_html($review->name); ?> <?= esc_html($review->city); ?> <?= esc_html($review->state); ?>
                    </span>
                <?php endif; ?>

                <?php if (in_array('rating', $columns)) : ?>
                    <span class="stars">
                        <?php
                        $total_stars = 5;
                        $rating = (int) $review->rating;
                        for ($i = 1; $i <= $total_stars; $i++) {
                            if ($i <= $rating) {
                                echo '<span class="star filled">★</span>';
                            } else {
                                echo '<span class="star empty">★</span>';
                            }
                        }
                        ?>
                    </span>
                <?php endif; ?>

                <?php if (in_array('date', $columns)) : ?>
                    <?php
                    $date_format = get_option('customer_reviews_settings')['date_format'] ?? 'MM/DD/YYYY';
                    $include_time = get_option('customer_reviews_settings')['include_time'] ?? 0;
                    $formatted_date = '';
                    if (!empty($review->created_at)) {
                        $timestamp = strtotime($review->created_at);
                        switch ($date_format) {
                            case 'DD/MM/YYYY':
                                $formatted_date = date('d/m/Y', $timestamp);
                                break;
                            case 'YYYY/MM/DD':
                                $formatted_date = date('Y/m/d', $timestamp);
                                break;
                            case 'MM/DD/YYYY':
                            default:
                                $formatted_date = date('m/d/Y', $timestamp);
                                break;
                        }
                        if ($include_time) {
                            $formatted_date .= ' ' . date('H:i', $timestamp);
                        }
                    }
                    ?>
                    <span class="review-date"><?= esc_html($formatted_date); ?></span>
                <?php endif; ?>
            </div>
            <?php if (in_array('comment', $columns)) : ?>
                <div class="review-content">
                    <p><?= esc_html($review->comment); ?></p>
                </div>
            <?php endif; ?>

            <?php if (in_array('admin_reply', $columns) && !empty($review->admin_reply)) : ?>
                <div class="admin-response">
                    <strong><?php esc_html_e('Author Response', 'wp_cr'); ?></strong>
                    <p><?= esc_html($review->admin_reply); ?></p>
                </div>
                <div class="customer-reply">
                    <strong><?php esc_html_e('Your Reply', 'wp_cr'); ?></strong>
                    <form method="post" action="">
                        <textarea name="customer_reply" rows="3" placeholder="Write your reply here..."></textarea>
                        <input type="hidden" name="review_id" value="<?= esc_attr($review->id); ?>">
                        <button type="submit"><?php esc_html_e('Submit Reply', 'wp_cr'); ?></button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    <?php
    }
    ?>
</div>
</div></div>
