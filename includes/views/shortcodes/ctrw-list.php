<?php
if (!defined('ABSPATH')) {
    exit;
}
$reviews = (new CTRW_Review_Model())->get_reviews('approved');
?>
<div class="customer-reviews-form-container">
    <div id="reviews-container">
        <div class="review-list">
            <?php
            $reviews_per_page = get_option('customer_reviews_settings')['reviews_per_page'] ?? 10;
            $reviews = array_slice($reviews, 0, $reviews_per_page);

            foreach ($reviews as $review) {
                
                $page_id = get_queried_object_id();
                $post_id = get_the_ID();
                $product_id = function_exists('wc_get_product') ? get_the_ID() : null;

                $is_product_page = function_exists('is_product') && is_product();
                if($is_product_page && $review->positionid != $is_product_page || $review->positionid != $page_id || $review->positionid != $post_id) {
                    continue; // Skip if the review is not for the current product
                }
                
                $settings = get_option('customer_reviews_settings');
                $show_city = !empty($settings['show_city']);
                $show_state = !empty($settings['show_state']);

                if (!isset($settings['enable_review_title'])) {
                    $show_title = true;
                } else {
                    $show_title = !empty($settings['enable_review_title']);
                }

                $date_format = get_option('customer_reviews_settings')['date_format'] ?? 'MM/DD/YYYY';
              
               
                $include_time = get_option('customer_reviews_settings')['include_time'];

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
                        default:
                            $formatted_date = date('m/d/Y', $timestamp);
                            break;
                    }
                }
            ?>
             <div class="review-author-details">
                    <span class="review-author">
                        Posted By 
                        <?php if (!empty($review->name)) : ?>
                            <?= esc_html($review->name); 
                            if ($show_city && !empty($review->city)) { echo ', '; } ?>
                        <?php endif; ?>
                        
                        <?php if ($show_city && !empty($review->city)) : ?>
                            <?= esc_html($review->city);
                            if ($show_state && !empty($review->state)) { echo ', '; } ?>
                        <?php endif; ?>
                        
                        <?php if ($show_state && !empty($review->state)) : ?>
                            <?= esc_html($review->state); ?>
                        <?php endif; ?>
                    </span>
                    
                    <?php  $include_time = get_option('customer_reviews_settings')['include_time']; if ($include_time) { 
                        $formatted_date .= ' ' . date('H:i', $timestamp); ?>
                        <div class="review-date">Post Date/Time: <?= esc_html($formatted_date); ?></div>
                    <?php } else { ?>
                        <div class="review-date">Post Date: <?= esc_html($formatted_date); ?></div>
                    <?php } ?>
                </div>

            <?php
            $comment_box_fill_color = get_option('customer_reviews_settings')['comment_box_fill_color'] ?? '#f5f5f5';
            ?>

            <div class="review-item" style="background-color: <?php echo esc_attr($comment_box_fill_color); ?>;">
               
                
                <div class="review-header">
                    

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
                    <?php if ($show_title && !empty($review->title)) : ?>
                        <div class="review-title">
                            <?= esc_html($review->title); ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="review-content">
                    <?php
                    $font_size = get_option('customer_reviews_settings')['comment_font_size'] ?? 14;
                    $font_style = get_option('customer_reviews_settings')['comment_font_style'] ?? 'normal';
                    $line_height = get_option('customer_reviews_settings')['comment_line_height'] ?? 23;
                    ?>
                    <p style="font-size: <?php echo esc_attr($font_size); ?>px; font-style: <?php echo esc_attr($font_style); ?>; line-height: <?php echo esc_attr($line_height); ?>px;">
                        <?= esc_html($review->comment); ?>
                    </p>
                </div>
                
                <?php if (!empty($review->admin_reply)) : ?>
                    <div class="admin-response">
                        <strong><?php esc_html_e('Author Response', 'wp_cr'); ?></strong>
                        <p><?= esc_html($review->admin_reply); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            <?php } ?>
        </div>
    </div>
</div>