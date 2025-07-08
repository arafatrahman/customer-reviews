<?php
if (!defined('ABSPATH')) {
    exit;
}
$reviews = (new CTRW_Review_Model())->get_reviews('approved');
?>
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

<style>
.ctrw-slider-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
}

.ctrw-slider-wrapper {
    position: relative;
    text-align: center;
}

.ctrw-slider-slides {
    position: relative;
    height: 400px;
    overflow: hidden;
    margin: 0 auto;
}

.ctrw-slider-slide {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 0.5s ease, transform 0.5s ease;
    transform: translateX(20px);
    display: flex;
    justify-content: center;
    align-items: center;
}
.ctrw-slider-slides
 {
    width: 40%;
}

.ctrw-slider-slide.ctrw-slider-active {
    opacity: 1;
    transform: translateX(0);
}

.ctrw-slider-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    padding: 30px;
    max-width: 700px;
    width: 100%;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.ctrw-slider-meta {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 20px;
    text-align: center;
}

.ctrw-reviewer-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background-color:rgb(218, 218, 218);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 15px;
}
.ctrw-slider-author-info {
    text-align: center;
}

.ctrw-slider-author-name {
    font-weight: 600;
    font-size: 18px;
    color: #333;
    margin-bottom: 5px;
}

.ctrw-slider-author-location {
    font-size: 14px;
    color: #666;
    margin-bottom: 5px;
}

.ctrw-slider-date {
    font-size: 13px;
    color: #888;
}

.ctrw-slider-time {
    margin-left: 5px;
}

.ctrw-slider-rating {
    margin-bottom: 15px;
    display: flex;
    justify-content: center;
}

.ctrw-slider-star {
    font-size: 24px;
    margin: 0 3px;
}

.ctrw-slider-star.ctrw-slider-filled {
    color: #FFB300;
}

.ctrw-slider-star.ctrw-slider-empty {
    color: #E0E0E0;
}

.ctrw-slider-title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 15px;
    color: #222;
    text-align: center;
}

.ctrw-slider-content {
    color: #444;
    line-height: 1.6;
    margin-bottom: 20px;
    text-align: center;
    max-width: 600px;
}

.ctrw-slider-response {
    background: #F8F9FA;
    border-radius: 8px;
    padding: 15px;
    margin-top: 20px;
    border-left: 3px solid #4CAF50;
    text-align: center;
    max-width: 600px;
}

.ctrw-slider-response-label {
    font-weight: 600;
    color: #4CAF50;
    margin-bottom: 8px;
    font-size: 14px;
}

.ctrw-slider-response-content {
    color: #555;
    font-size: 14px;
    line-height: 1.5;
}

.ctrw-slider-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 30px;
    margin-top: 30px;
}

.ctrw-slider-prev, .ctrw-slider-next {
    background: none;
    border: none;
    cursor: pointer;
    padding: 10px;
    border-radius: 50%;
    background: #f5f5f5;
    color: #333;
    transition: all 0.3s ease;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.ctrw-slider-prev:hover, .ctrw-slider-next:hover {
    background: #4CAF50;
    color: white;
}

.ctrw-slider-prev svg, .ctrw-slider-next svg {
    width: 24px;
    height: 24px;
}

.ctrw-slider-dots {
    display: flex;
    gap: 10px;
}

.ctrw-slider-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #E0E0E0;
    cursor: pointer;
    border: none;
    padding: 0;
    transition: all 0.3s ease;
}

.ctrw-slider-dot.ctrw-slider-active {
    background: #4CAF50;
    transform: scale(1.2);
}

@media (max-width: 768px) {
    .ctrw-slider-container {
        padding: 15px;
    }
    
    .ctrw-slider-slides {
        height: 450px;
    }
    
    .ctrw-slider-card {
        padding: 20px;
    }
    
    .ctrw-slider-avatar img {
        width: 60px;
        height: 60px;
    }
    
    .ctrw-slider-title {
        font-size: 18px;
    }
    
    .ctrw-slider-content {
        font-size: 15px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.ctrw-slider-slide');
    const dots = document.querySelectorAll('.ctrw-slider-dot');
    const prevBtn = document.querySelector('.ctrw-slider-prev');
    const nextBtn = document.querySelector('.ctrw-slider-next');
    let currentIndex = 0;
    let autoSlideInterval;
    const slideDuration = 5000; // 5 seconds
    
    // Initialize slider
    function showSlide(index) {
        // Hide all slides
        slides.forEach(slide => {
            slide.classList.remove('ctrw-slider-active');
        });
        
        // Show current slide
        slides[index].classList.add('ctrw-slider-active');
        
        // Update dots
        dots.forEach(dot => {
            dot.classList.remove('ctrw-slider-active');
        });
        dots[index].classList.add('ctrw-slider-active');
        
        currentIndex = index;
    }
    
    // Next slide
    function nextSlide() {
        const newIndex = (currentIndex + 1) % slides.length;
        showSlide(newIndex);
        resetAutoSlide();
    }
    
    // Previous slide
    function prevSlide() {
        const newIndex = (currentIndex - 1 + slides.length) % slides.length;
        showSlide(newIndex);
        resetAutoSlide();
    }
    
    // Auto slide
    function startAutoSlide() {
        autoSlideInterval = setInterval(nextSlide, slideDuration);
    }
    
    // Reset auto slide timer
    function resetAutoSlide() {
        clearInterval(autoSlideInterval);
        startAutoSlide();
    }
    
    // Event listeners
    nextBtn.addEventListener('click', nextSlide);
    prevBtn.addEventListener('click', prevSlide);
    
    dots.forEach(dot => {
        dot.addEventListener('click', function() {
            const slideIndex = parseInt(this.getAttribute('data-index'));
            showSlide(slideIndex);
            resetAutoSlide();
        });
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowRight') {
            nextSlide();
        } else if (e.key === 'ArrowLeft') {
            prevSlide();
        }
    });
    
    // Start auto sliding
    if (slides.length > 1) {
        startAutoSlide();
        
        // Pause on hover
        const sliderContainer = document.querySelector('.ctrw-slider-wrapper');
        sliderContainer.addEventListener('mouseenter', () => {
            clearInterval(autoSlideInterval);
        });
        
        sliderContainer.addEventListener('mouseleave', () => {
            startAutoSlide();
        });
    }
    
    // Show first slide initially
    showSlide(0);
});
</script>