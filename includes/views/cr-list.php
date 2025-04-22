
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



    foreach ($reviews as $review){

    if (get_the_ID() !== (int) $review->positionid && empty($review->positionid)) {
        continue;
    } 
    ?>
        <div class="review-item">
            <div class="review-header">
            <span class="review-author"> Posted By <?= esc_html($review->name); ?> <?= esc_html($review->city); ?> <?= esc_html($review->state); ?></span>
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
                
                </span>
               
                
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
            </div>
            <div class="review-content">
                <p><?= esc_html($review->comment); ?></p>
            </div>
            <?php if (!empty($review->admin_reply)) : ?>
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
    <?php } ?>
</div>
</div></div>
