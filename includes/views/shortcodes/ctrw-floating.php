<?php
if (!defined('ABSPATH')) {
    exit;
}
$reviews = (new CTRW_Review_Model())->get_reviews('approved');
$settings = get_option('customer_reviews_settings');
$reviews_per_page = $settings['reviews_per_page'] ?? 5;
$display_reviews = array_slice($reviews, 0, $reviews_per_page);
?>

<div class="ctrw-floating-widget">
    <div class="ctrw-floating-tab">
        <div class="ctrw-tab-content">
            <span class="ctrw-tab-icon">★</span>
            <span class="ctrw-tab-text">Reviews</span>
            <span class="ctrw-tab-count"><?= count($display_reviews) ?></span>
        </div>
    </div>
    
    <div class="ctrw-floating-content">
        <div class="ctrw-reviews-container">
            <div class="ctrw-reviews-header">
                <h3 class="ctrw-reviews-title">
                    <span class="ctrw-title-icon">★</span>
                    Customer Reviews
                </h3>
                <button class="ctrw-close-btn" aria-label="Close reviews">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13 1L1 13M1 1L13 13" stroke="#666" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
            
            <div class="ctrw-reviews-list">
                <?php foreach ($display_reviews as $review) : 
                    $timestamp = strtotime($review->created_at);
                    $formatted_date = date('m/d/Y', $timestamp);
                    if ($settings['include_time'] ?? false) {
                        $formatted_date .= ' ' . date('H:i', $timestamp);
                    }
                    
                    $show_city = !empty($settings['show_city']);
                    $show_state = !empty($settings['show_state']);
                    $show_title = !isset($settings['enable_review_title']) || !empty($settings['enable_review_title']);
                ?>
                    <div class="ctrw-review-card">
                        <div class="ctrw-review-header">
                            <div class="ctrw-review-rating">
                                <?= str_repeat('★', (int)$review->rating) ?><?= str_repeat('☆', 5 - (int)$review->rating) ?>
                            </div>
                            
                            <div class="ctrw-review-meta">
                                <div class="ctrw-review-author">
                                    <span class="ctrw-author-name"><?= esc_html($review->name) ?></span>
                                    <?php if ($show_city && !empty($review->city)): ?>
                                        <span class="ctrw-review-location">, <?= esc_html($review->city) ?></span>
                                        <?php if ($show_state && !empty($review->state)): ?>
                                            <span class="ctrw-review-location">, <?= esc_html($review->state) ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="ctrw-review-date">
                                    <?= $formatted_date ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ctrw-review-body">
                            <?php if ($show_title && !empty($review->title)): ?>
                                <h4 class="ctrw-review-title"><?= esc_html($review->title) ?></h4>
                            <?php endif; ?>
                            
                            <div class="ctrw-review-content">
                                <?= esc_html($review->comment) ?>
                            </div>
                        </div>
                        
                        <?php if (!empty($review->admin_reply)): ?>
                            <div class="ctrw-admin-reply">
                                <div class="ctrw-reply-header">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#FFB800" stroke-width="2"/>
                                        <path d="M12 8V12" stroke="#FFB800" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M12 16H12.01" stroke="#FFB800" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    <span>Author Response</span>
                                </div>
                                <div class="ctrw-reply-content"><?= esc_html($review->admin_reply) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (count($reviews) > $reviews_per_page): ?>
                <div class="ctrw-reviews-footer">
                    <button class="ctrw-view-all-btn">
                        View All Reviews
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Base Styles */
.ctrw-floating-widget {
    position: fixed;
    right: 20px;
    bottom: 20px;
    z-index: 9999;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    --ctrw-primary: #FFB800;
    --ctrw-primary-hover: #E6A600;
    --ctrw-text: #333333;
    --ctrw-text-light: #666666;
    --ctrw-text-lighter: #999999;
    --ctrw-bg: #FFFFFF;
    --ctrw-border: #F0F0F0;
    --ctrw-card-bg: #F9F9F9;
}

/* Floating Tab */
.ctrw-floating-tab {
    background: var(--ctrw-primary);
    color: white;
    border-radius: 8px 8px 0 0;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
    padding: 0;
    overflow: hidden;
}

.ctrw-floating-tab:hover {
    background: var(--ctrw-primary-hover);
}

.ctrw-tab-content {
    display: flex;
    align-items: center;
    padding: 12px 16px;
}

.ctrw-tab-icon {
    font-size: 18px;
    margin-right: 8px;
    line-height: 1;
}

.ctrw-tab-text {
    font-weight: 600;
    font-size: 15px;
}

.ctrw-tab-count {
    background: rgba(0,0,0,0.15);
    border-radius: 12px;
    padding: 2px 8px;
    font-size: 12px;
    margin-left: 8px;
}

/* Floating Content */
.ctrw-floating-content {
    display: none;
    background: var(--ctrw-bg);
    border-radius: 0px 0 12px 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    width: 380px;
    max-height: 70vh;
    overflow: hidden;
    transform-origin: bottom right;
    animation: ctrw-fadeIn 0.3s ease forwards;
}

.ctrw-floating-widget.active .ctrw-floating-content {
    display: flex;
    flex-direction: column;
}

.ctrw-reviews-container {
    display: flex;
    flex-direction: column;
    height: 100%;
}

/* Header */
.ctrw-reviews-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    background: var(--ctrw-bg);
    border-bottom: 1px solid var(--ctrw-border);
    position: relative;
}

.ctrw-reviews-title {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: var(--ctrw-text);
    display: flex;
    align-items: center;
}

.ctrw-title-icon {
    color: var(--ctrw-primary);
    margin-right: 8px;
    font-size: 20px;
}

.ctrw-close-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--ctrw-text-light);
    padding: 4px;
    margin: -4px -4px -4px 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.ctrw-close-btn:hover {
    background: rgba(0,0,0,0.05);
    color: var(--ctrw-text);
}

/* Reviews List */
.ctrw-reviews-list {
    flex: 1;
    overflow-y: auto;
    padding: 0 20px;
}

.ctrw-review-card {
    padding: 16px 0;
    border-bottom: 1px solid var(--ctrw-border);
}

.ctrw-review-card:last-child {
    border-bottom: none;
}

.ctrw-review-header {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
}

.ctrw-review-rating {
    color: var(--ctrw-primary);
    font-size: 16px;
    letter-spacing: 1px;
}

.ctrw-review-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.ctrw-review-author {
    font-size: 14px;
    color: var(--ctrw-text);
    display: flex;
    align-items: center;
    flex-wrap: wrap;
}

.ctrw-author-name {
    font-weight: 600;
    margin-right: 4px;
}

.ctrw-review-location {
    color: var(--ctrw-text-light);
    font-size: 13px;
}

.ctrw-review-date {
    font-size: 12px;
    color: var(--ctrw-text-lighter);
}

/* Review Body */
.ctrw-review-body {
    margin-left: 4px;
}

.ctrw-review-title {
    margin: 0 0 8px 0;
    font-size: 15px;
    color: var(--ctrw-text);
    font-weight: 600;
    line-height: 1.4;
}

.ctrw-review-content {
    font-size: 14px;
    color: var(--ctrw-text-light);
    line-height: 1.5;
    margin-bottom: 12px;
}

/* Admin Reply */
.ctrw-admin-reply {
    padding: 12px;
    background: var(--ctrw-card-bg);
    border-radius: 6px;
    margin-top: 12px;
}

.ctrw-reply-header {
    display: flex;
    align-items: center;
    margin-bottom: 6px;
    font-weight: 600;
    font-size: 13px;
    color: var(--ctrw-text);
}

.ctrw-reply-header svg {
    margin-right: 6px;
}

.ctrw-reply-content {
    font-size: 13px;
    color: var(--ctrw-text-light);
    line-height: 1.5;
}

/* Footer */
.ctrw-reviews-footer {
    padding: 16px 20px;
    border-top: 1px solid var(--ctrw-border);
    text-align: center;
}

.ctrw-view-all-btn {
    background: var(--ctrw-primary);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
}

.ctrw-view-all-btn:hover {
    background: var(--ctrw-primary-hover);
    transform: translateY(-1px);
}

.ctrw-view-all-btn svg {
    margin-left: 6px;
    transition: transform 0.3s ease;
}

.ctrw-view-all-btn:hover svg {
    transform: translateY(2px);
}

/* Animations */
@keyframes ctrw-fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Scrollbar */
.ctrw-reviews-list::-webkit-scrollbar {
    width: 6px;
}

.ctrw-reviews-list::-webkit-scrollbar-track {
    background: transparent;
}

.ctrw-reviews-list::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.1);
    border-radius: 3px;
}

.ctrw-reviews-list::-webkit-scrollbar-thumb:hover {
    background: rgba(0,0,0,0.2);
}

/* Responsive */
@media (max-width: 480px) {
    .ctrw-floating-widget {
        right: 10px;
        bottom: 10px;
        left: 10px;
    }
    
    .ctrw-floating-content {
        width: auto;
        max-height: 60vh;
    }
    
    .ctrw-reviews-header,
    .ctrw-reviews-list {
        padding-left: 16px;
        padding-right: 16px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Toggle widget visibility
    $('.ctrw-floating-tab').on('click', function(e) {
        e.stopPropagation();
        $('.ctrw-floating-widget').toggleClass('active');
    });
    
    // Close widget
    $('.ctrw-close-btn').on('click', function(e) {
        e.stopPropagation();
        $('.ctrw-floating-widget').removeClass('active');
    });
    
    // Close when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.ctrw-floating-widget').length) {
            $('.ctrw-floating-widget').removeClass('active');
        }
    });
    
    // View all reviews button handler
    $('.ctrw-view-all-btn').on('click', function() {
        // Implement your view all functionality here
        alert('View all reviews functionality would go here');
    });
});
</script>