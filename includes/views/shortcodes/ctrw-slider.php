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
    document.addEventListener('DOMContentLoaded', function () {
        const slidesContainer = document.querySelector('.ctrw-slider-slides');
        const slides = document.querySelectorAll('.ctrw-slider-slide');
        const prevButton = document.querySelector('.ctrw-slider-prev');
        const nextButton = document.querySelector('.ctrw-slider-next');
        const dots = document.querySelectorAll('.ctrw-slider-dot');
        let currentIndex = 0;

        const slideCount = slides.length;
        const visibleSlides = () => window.innerWidth <= 600 ? 1 : (window.innerWidth <= 992 ? 2 : 3);

        function updateSliderPosition() {
            const slideWidth = slides[0].offsetWidth + 20; // 20 = gap
            const offset = currentIndex * slideWidth;
            slidesContainer.style.transform = `translateX(-${offset}px)`;
            dots.forEach(dot => dot.classList.remove('ctrw-slider-active'));
            if (dots[currentIndex]) {
                dots[currentIndex].classList.add('ctrw-slider-active');
            }
        }

        prevButton.addEventListener('click', () => {
            currentIndex = Math.max(currentIndex - 1, 0);
            updateSliderPosition();
        });

        nextButton.addEventListener('click', () => {
            const maxIndex = slideCount - visibleSlides();
            currentIndex = Math.min(currentIndex + 1, maxIndex);
            updateSliderPosition();
        });

        dots.forEach(dot => {
            dot.addEventListener('click', (e) => {
                const index = parseInt(e.target.dataset.index);
                currentIndex = index;
                updateSliderPosition();
            });
        });

        window.addEventListener('resize', updateSliderPosition);
        updateSliderPosition();
    });
</script>
