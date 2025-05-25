<?php
if (!defined('ABSPATH')) {
    exit;
}

class Review_View {
    
    public function display_reviews($reviews, $counts, $current_status) {
    foreach ($reviews as &$review) {
        $post_type = get_post_type($review->positionid);
        $review->review_type = $post_type ? $post_type : 'unknown';
    }

    // Pagination and filtering
    $per_page = get_user_meta(get_current_user_id(), 'ctrw_reviews_per_page', true);
    $per_page = $per_page ? (int)$per_page : 10;
    $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($page - 1) * $per_page;

    $selected_review_type = $_GET['review_type'] ?? '';

    if ($selected_review_type) {
        $reviews = array_filter($reviews, function($review) use ($selected_review_type) {
            return isset($review->review_type) && $review->review_type === $selected_review_type;
        });
    }

    $total_reviews = count($reviews);
    $all_reviews = array_slice($reviews, $offset, $per_page);
    $total_pages = ceil($total_reviews / $per_page);

    $statuses = [
        'all' => 'All',
        'approved' => 'Approve',
        'reject' => 'Reject',
        'pending' => 'Pending',
        'trash' => 'Trash'
    ];

    echo '<div class="wrap"><h1>Customer Reviews</h1>';

    // Provide variables to the included template files
    include 'admin/admin-reviews-list.php';
    include 'admin/admin-review-reply-popup.php';
    include 'admin/admin-review-edit-popup.php';

    echo '</div>';
}

    
}
?>
