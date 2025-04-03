
<?php if (!defined('ABSPATH')) exit; ?>

<div class="customer-reviews-form-container">
    <h3>Submit Your Review</h3>
    <form id="customer-reviews-form">
        <div class="form-group">
            <label>Your Name</label>
            <input type="text" name="author_name" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Phone (Optional)</label>
            <input type="text" name="phone">
        </div>

        <div class="form-group">
            <label>City</label>
            <input type="text" name="city" required>
        </div>

        <div class="form-group">
            <label>State</label>
            <input type="text" name="state" required>
        </div>

        <div class="form-group">
            <label>Rating</label>
            <div class="rating">
                <input type="radio" name="rating" value="5" id="star5"><label for="star5">★</label>
                <input type="radio" name="rating" value="4" id="star4"><label for="star4">★</label>
                <input type="radio" name="rating" value="3" id="star3"><label for="star3">★</label>
                <input type="radio" name="rating" value="2" id="star2"><label for="star2">★</label>
                <input type="radio" name="rating" value="1" id="star1"><label for="star1">★</label>
            </div>
        </div>


        <div class="form-group">
            <label>Review</label>
            <textarea name="comments" required></textarea>
        </div>

        <button type="submit" class="submit-button">Submit Review</button>
    </form>
    <p id="review-message"></p>
    <h3>Previous Reviews</h3>
    <div id="reviews-container"><?php include CR_PLUGIN_PATH. 'includes/views/review-list.php'; ?></div>
</div>

<?php if (!defined('ABSPATH')) exit; ?>


