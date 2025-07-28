<?php
if (!defined('ABSPATH')) {
    exit;
}
$reviews = (new CTRW_Review_Model())->get_reviews('approved');
?>
<style>
    :root {
        --ctrw-primary: #4361ee;
        --ctrw-primary-light: #f0f5ff;
        --ctrw-text: #333;
        --ctrw-text-light: #666;
        --ctrw-border: #e0e0e0;
        --ctrw-bg: #fff;
        --ctrw-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        --ctrw-transition: all 0.3s ease;
    }

    .ctrw-slider-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 20px;
        position: relative;
    }

    .ctrw-slider-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
    }

    .ctrw-slider-slides {
        display: flex;
        transition: var(--ctrw-transition);
        will-change: transform;
    }

    .ctrw-slider-slide {
        min-width: 100%;
        padding: 15px;
        box-sizing: border-box;
        opacity: 0;
        transition: var(--ctrw-transition);
        transform: translateX(100%);
    }

    .ctrw-slider-slide.ctrw-slider-active {
        opacity: 1;
        transform: translateX(0);
    }

    .ctrw-slider-card {
        background: var(--ctrw-bg);
        border-radius: 10px;
        padding: 30px;
        box-shadow: var(--ctrw-shadow);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .ctrw-slider-meta {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .ctrw-reviewer-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: var(--ctrw-primary-light);
        color: var(--ctrw-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 18px;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .ctrw-slider-author-info {
        flex-grow: 1;
    }

    .ctrw-slider-author-name {
        font-weight: 600;
        color: var(--ctrw-text);
        margin-bottom: 3px;
    }

    .ctrw-slider-author-location,
    .ctrw-slider-date {
        font-size: 13px;
        color: var(--ctrw-text-light);
    }

    .ctrw-slider-time {
        margin-left: 5px;
    }

    .ctrw-slider-rating {
        margin-bottom: 15px;
        line-height: 1;
    }

    .ctrw-slider-star {
        font-size: 20px;
        color: #ffc107;
    }

    .ctrw-slider-star.ctrw-slider-empty {
        color: #e0e0e0;
    }

    .ctrw-slider-title {
        font-size: 18px;
        margin: 0 0 15px;
        color: var(--ctrw-text);
    }

    .ctrw-slider-content {
        flex-grow: 1;
        margin-bottom: 20px;
    }

    .ctrw-slider-content p {
        margin: 0;
        color: var(--ctrw-text);
        line-height: 1.6;
    }

    .ctrw-slider-response {
        background: var(--ctrw-primary-light);
        padding: 15px;
        border-radius: 8px;
        margin-top: 20px;
        border-left: 3px solid var(--ctrw-primary);
    }

    .ctrw-slider-response-label {
        font-weight: 600;
        color: var(--ctrw-primary);
        margin-bottom: 8px;
        font-size: 14px;
    }

    .ctrw-slider-response-content {
        color: var(--ctrw-text);
        font-size: 14px;
        line-height: 1.5;
    }

    .ctrw-slider-controls {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 25px;
        gap: 15px;
    }

    .ctrw-slider-prev,
    .ctrw-slider-next {
        background: var(--ctrw-bg);
        border: 1px solid var(--ctrw-border);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--ctrw-transition);
    }

    .ctrw-slider-prev:hover,
    .ctrw-slider-next:hover {
        background: var(--ctrw-primary);
        border-color: var(--ctrw-primary);
        color: #fff;
    }

    .ctrw-slider-prev svg,
    .ctrw-slider-next svg {
        width: 20px;
        height: 20px;
    }

    .ctrw-slider-dots {
        display: flex;
        gap: 8px;
    }

    .ctrw-slider-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #e0e0e0;
        border: none;
        padding: 0;
        cursor: pointer;
        transition: var(--ctrw-transition);
    }

    .ctrw-slider-dot.ctrw-slider-active {
        background: var(--ctrw-primary);
        transform: scale(1.2);
    }

    @media (max-width: 768px) {
        .ctrw-slider-card {
            padding: 20px;
        }
        
        .ctrw-slider-controls {
            margin-top: 15px;
        }
    }
</style>

<div class="ctrw-slider-container">
    <div class="ctrw-slider-wrapper">
        <div class="ctrw-slider-slides">
            <?php
            $reviews_per_page = get_option('customer_reviews_settings')['reviews_per_page'] ?? 10;
            $reviews = array_slice($reviews, 0, $reviews_per_page);

            foreach ($reviews as $index => $review) {
                $page_id = get_queried_object_id();
                $post_id = get_the_ID();
                $product_id = function_exists('wc_get_product') ? get_the_ID() : null;

                $is_product_page = function_exists('is_product') && is_product();
                if($is_product_page && $review->positionid != $is_product_page || $review->positionid != $page_id || $review->positionid != $post_id) {
                    continue;
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
                $include_time = get_option('customer_reviews_settings')['include_time'] ?? false;

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

                // Generate initials for avatar (max 2 letters)
                $name_parts = explode(' ', $review->name);
                $initials = '';
                $count = 0;
                foreach ($name_parts as $part) {
                    if (!empty($part) && $count < 2) {
                        $initials .= strtoupper(substr($part, 0, 1));
                        $count++;
                    }
                }
            ?>
            <div class="ctrw-slider-slide <?php echo $index === 0 ? 'ctrw-slider-active' : ''; ?>" data-index="<?php echo $index; ?>">
                <div class="ctrw-slider-card">
                    <div class="ctrw-slider-meta">
                        <div class="ctrw-reviewer-avatar"><?php echo esc_html($initials); ?></div>

                        <div class="ctrw-slider-author-info">
                            <div class="ctrw-slider-author-name">
                                <?= esc_html($review->name); ?>
                            </div>
                            <div class="ctrw-slider-author-location">
                                <?php if ($show_city && !empty($review->city)) : ?>
                                    <?= esc_html($review->city); ?>
                                    <?php if ($show_state && !empty($review->state)) echo ', '; ?>
                                <?php endif; ?>
                                <?php if ($show_state && !empty($review->state)) : ?>
                                    <?= esc_html($review->state); ?>
                                <?php endif; ?>
                            </div>
                            <div class="ctrw-slider-date">
                                <?= esc_html($formatted_date); ?>
                                <?php if ($include_time) : ?>
                                    <span class="ctrw-slider-time"><?= date('H:i', $timestamp); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="ctrw-slider-rating">
                        <?php
                        $total_stars = 5;
                        $rating = (int) $review->rating;
                        
                        for ($i = 1; $i <= $total_stars; $i++) {
                            if ($i <= $rating) {
                                echo '<span class="ctrw-slider-star ctrw-slider-filled">★</span>';
                            } else {
                                echo '<span class="ctrw-slider-star ctrw-slider-empty">★</span>';
                            }
                        }
                        ?>
                    </div>
                    
                    <?php if ($show_title && !empty($review->title)) : ?>
                        <h3 class="ctrw-slider-title"><?= esc_html($review->title); ?></h3>
                    <?php endif; ?>
                    
                    <div class="ctrw-slider-content">
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
                        <div class="ctrw-slider-response">
                            <div class="ctrw-slider-response-label"><?php esc_html_e('Author Response', 'wp_cr'); ?></div>
                            <div class="ctrw-slider-response-content"><?= esc_html($review->admin_reply); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php } ?>
        </div>
        
        <div class="ctrw-slider-controls">
            <button class="ctrw-slider-prev" aria-label="Previous review">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 18l-6-6 6-6"/>
                </svg>
            </button>
            
            <div class="ctrw-slider-dots">
                <?php for ($i = 0; $i < count($reviews); $i++) : ?>
                    <button class="ctrw-slider-dot <?php echo $i === 0 ? 'ctrw-slider-active' : ''; ?>" data-index="<?php echo $i; ?>" aria-label="Go to review <?php echo $i + 1; ?>"></button>
                <?php endfor; ?>
            </div>
            
            <button class="ctrw-slider-next" aria-label="Next review">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.querySelectorAll('.ctrw-slider-slide');
        const dots = document.querySelectorAll('.ctrw-slider-dot');
        const prevBtn = document.querySelector('.ctrw-slider-prev');
        const nextBtn = document.querySelector('.ctrw-slider-next');
        const slidesContainer = document.querySelector('.ctrw-slider-slides');
        
        let currentSlide = 0;
        const slideCount = slides.length;
        let slideInterval;

        // Initialize slider
        function showSlide(index) {
            // Update slides
            slides.forEach((slide, i) => {
                slide.classList.remove('ctrw-slider-active');
                if (i === index) {
                    slide.classList.add('ctrw-slider-active');
                }
            });
            
            // Update dots
            dots.forEach((dot, i) => {
                dot.classList.remove('ctrw-slider-active');
                if (i === index) {
                    dot.classList.add('ctrw-slider-active');
                }
            });
            
            currentSlide = index;
        }

        // Next slide
        function nextSlide() {
            currentSlide = (currentSlide + 1) % slideCount;
            showSlide(currentSlide);
        }

        // Previous slide
        function prevSlide() {
            currentSlide = (currentSlide - 1 + slideCount) % slideCount;
            showSlide(currentSlide);
        }

        // Auto-rotate slides
        function startAutoSlide() {
            slideInterval = setInterval(nextSlide, 5000);
        }

        // Pause on hover
        const sliderWrapper = document.querySelector('.ctrw-slider-wrapper');
        sliderWrapper.addEventListener('mouseenter', () => clearInterval(slideInterval));
        sliderWrapper.addEventListener('mouseleave', startAutoSlide);

        // Dot navigation
        dots.forEach(dot => {
            dot.addEventListener('click', function() {
                const slideIndex = parseInt(this.getAttribute('data-index'));
                showSlide(slideIndex);
            });
        });

        // Button navigation
        nextBtn.addEventListener('click', nextSlide);
        prevBtn.addEventListener('click', prevSlide);

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowRight') nextSlide();
            if (e.key === 'ArrowLeft') prevSlide();
        });

        // Start auto-sliding
        if (slideCount > 1) {
            startAutoSlide();
        }
    });
</script>