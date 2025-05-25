<?php
if (!defined('ABSPATH')) {
    exit;
}

class Review_View {
    
    public function display_reviews($reviews, $counts, $current_status) {
        global $wpdb;

        $per_page = get_user_meta(get_current_user_id(), 'ctrw_reviews_per_page', true);
        $per_page = $per_page ? (int)$per_page : 10;
        $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($page - 1) * $per_page;

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

        // Variables to be used inside included files:
        // $all_reviews, $counts, $current_status, $statuses, $page, $total_pages, $per_page

        include 'admin/admin-reviews-list.php';
        include 'admin/admin-review-reply-popup.php';
        include 'admin/admin-review-edit-popup.php';

        echo '</div>';
    }

    
}
?>
