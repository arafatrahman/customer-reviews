
<?php
if (!defined('ABSPATH')) {
    exit;
}
$reviews = (new Review_Model())->get_reviews('approved');
?>

<div class="review-list">
    <?php
    $reviews_per_page = get_option('customer_reviews_settings')['reviews_per_page'] ?? 10;
    $reviews = array_slice($reviews, 0, $reviews_per_page);



    foreach ($reviews as $review) : ?>
        <div class="review-item">
            <div class="review-header">
                <span class="stars">
                <?php
                $total_stars = 5; // Total number of stars
                $rating = (int) $review->rating; // Get review rating

                for ($i = 1; $i <= $total_stars; $i++) {
                    if ($i <= $rating) {
                        echo '<span class="star filled">★</span>'; // Yellow filled star
                    } else {
                        echo '<span class="star empty">★</span>'; // Gray empty star
                    }
                }
                ?>   
                
                by <?= esc_html($review->name); ?> </span>
               
                <span class="review-author"></span>
                <span class="review-date"><?= human_time_diff(strtotime($review->created_at), current_time('timestamp')) . ' ago'; ?></span>
            </div>
            <div class="review-content">
                <p><?= esc_html($review->comment); ?></p>
            </div>
            <?php if (!empty($review->admin_reply)) : ?>
                <div class="admin-response">
                    <strong>Author Response</strong>
                    <p><?= esc_html($review->admin_reply); ?></p>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
