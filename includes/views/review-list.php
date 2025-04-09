
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
                <div class="customer-reply">
                    <strong>Your Reply</strong>
                    <form method="post" action="">
                        <textarea name="customer_reply" rows="3" placeholder="Write your reply here..."></textarea>
                        <input type="hidden" name="review_id" value="<?= esc_attr($review->id); ?>">
                        <button type="submit">Submit Reply</button>
                    </form>
                </div>
                <style>
                    .customer-reply {
                        margin-top: 15px;
                        padding: 10px;
                        border: 1px solid #ddd;
                        border-radius: 5px;
                        background-color: #f9f9f9;
                    }

                    .customer-reply strong {
                        display: block;
                        margin-bottom: 10px;
                        font-size: 14px;
                        color: #333;
                    }

                    .customer-reply form {
                        display: flex;
                        flex-direction: column;
                    }

                    .customer-reply textarea {
                        width: 96%;
                        padding: 8px;
                        margin-bottom: 10px;
                        border: 1px solid #ccc;
                        border-radius: 4px;
                        resize: vertical;
                        font-size: 14px;
                    }

                    .customer-reply button {
                        align-self: flex-start;
                        padding: 8px 15px;
                        background-color: #0073aa;
                        color: #fff;
                        border: none;
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 14px;
                    }

                    .customer-reply button:hover {
                        background-color: #005177;
                    }
                </style>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
