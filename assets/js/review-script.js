jQuery(document).ready(function ($) {
    const $reviewForm = $("#customer-reviews-form");
    const $reviewsContainer = $("#reviews-container");

    // Submit Review Form
    $reviewForm.on("submit", function (e) {
        e.preventDefault();


        let formData = $reviewForm.serialize();
        formData += "&action=submit_review";

        $.ajax({
            url: ctrw_ajax.ajax_url,
            method: "POST",
            data: formData,
            success: function (data) {

                console.log(data);
                let $message = $("#review-message");
                if (data.success) {
                    $message.html("✅ Review submitted successfully!").css("color", "green");
                    $reviewForm[0].reset();
                } else {
                    $message.html("❌ Error submitting review.").css("color", "red");
                }
            }
        });
    });

    // Admin Reply to Review
    $reviewsContainer.on("submit", ".admin-reply-form", function (e) {
        e.preventDefault();

        let $form = $(this);
        let formData = $form.serialize();

        $.ajax({
            url: review_ajax.ajax_url,
            method: "POST",
            data: formData,
            success: function (data) {
                if (data.success) {
                    $form.replaceWith(`<p><strong>Admin Reply:</strong> ${data.reply}</p>`);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
            }
        });
    });

    
$('.floating-reviews-toggle').click(function() {
        $('.floating-reviews-container').toggleClass('active');
    });
    
    $('.floating-reviews-close').click(function() {
        $('.floating-reviews-container').removeClass('active');
    });
    
    // Close when clicking outside
    $(document).click(function(e) {
        if (!$(e.target).closest('.floating-reviews-container').length) {
            $('.floating-reviews-container').removeClass('active');
        }
    });
    
});


const ctrw_slider = document.querySelector('.ctrw_reviews-slider');
        const ctrw_reviews = document.querySelectorAll('.ctrw_review');
        const ctrw_buttons = document.querySelectorAll('.ctrw_control-btn');
        let ctrw_currentIndex = 0;
        
        function ctrw_updateSlider() {
            ctrw_slider.style.transform = `translateX(-${ctrw_currentIndex * 100}%)`;
            
            // Update active button
            ctrw_buttons.forEach((btn, index) => {
                btn.classList.toggle('ctrw_active', index === ctrw_currentIndex);
            });
        }
        
        // Button controls
        ctrw_buttons.forEach((button, index) => {
            button.addEventListener('click', () => {
                ctrw_currentIndex = index;
                ctrw_updateSlider();
            });
        });
        
        // Auto-slide every 5 seconds
        const ctrw_autoSlide = setInterval(() => {
            ctrw_currentIndex = (ctrw_currentIndex + 1) % ctrw_reviews.length;
            ctrw_updateSlider();
        }, 5000);
        
        // Pause on hover
        ctrw_slider.addEventListener('mouseenter', () => {
            clearInterval(ctrw_autoSlide);
        });
        
        ctrw_slider.addEventListener('mouseleave', () => {
            ctrw_autoSlide = setInterval(() => {
                ctrw_currentIndex = (ctrw_currentIndex + 1) % ctrw_reviews.length;
                ctrw_updateSlider();
            }, 5000);
        });
