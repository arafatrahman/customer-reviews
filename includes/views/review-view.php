<?php
if (!defined('ABSPATH')) {
    exit;
}

class Review_View {
    public function display_reviews($reviews, $counts, $current_status) {
        global $wpdb;
    
        echo '<div class="wrap"><h1>All Reviews</h1>';
    
        $statuses = ['all' => 'All', 'approved' => 'Approve', 'reject' => 'Reject', 'pending' => 'Pending', 'trash' => 'Trash'];
    
        echo '<ul class="subsubsub">';
        $status_links = [];
    
        foreach ($statuses as $key => $label) {
            $count = $counts[$key] ?? 0;
            $class = (isset($_GET['status']) && $_GET['status'] === $key) ? 'current' : '';
            $status_links[] = "<li class='$key'><a href='?page=wp-review-plugin&status=$key' class='$class'>$label <span class='count'>($count)</span></a></li>";
        }
    
        echo implode(' | ', $status_links);
        echo '</ul>';
    
        echo '<form method="post">';
        echo '<div class="tablenav top">';
        echo '<div class="alignleft actions">';
        echo '<select name="bulk_action">';
        echo '<option value="">Bulk Actions</option>';
        echo '<option value="approve">Approve</option>';
        echo '<option value="reject">Reject</option>';
        echo '<option value="trash">Move to Trash</option>';
        if ($current_status === 'trash') {
            echo '<option value="delete_permanently">Delete Permanently</option>';
        }
        echo '</select>';
        echo '<input type="submit" name="apply" id="doaction" class="button action" value="Apply">';
        echo '</div>';
        echo '</div>';
    
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead>
                <tr>
                    <th scope="col" class="check-column"><input type="checkbox" id="select-all" /></th>
                    <th>Author</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Admin Reply</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
              </thead>';
        echo '<tbody>';
    
        if (!empty($reviews)) {
            foreach ($reviews as $review) {
                $stars = str_repeat('⭐', $review->rating);
                echo "<tr>
                        <th scope='row' class='check-column'><input type='checkbox' name='review_ids[]' value='{$review->id}' /></th>
                        <td>{$review->name}</td>
                        <td>{$stars}</td>
                        <td>{$review->comment}</td>
                        <td>{$review->admin_reply}</td>
                        <td><a href='?page=wp-review-plugin&status={$review->status}' class='review-status-link'>{$review->status}</a></td>";
                if ($review->status !== 'rejected') {
                    if (isset($review->admin_reply) && !empty($review->admin_reply)) {
                        echo "<td><button type='button' class='button reply-now' data-review-id='{$review->id}' data-review-author='{$review->name}' data-reply-message='{$review->admin_reply}'>Edit Reply</button></td>";
                    } else {
                        echo "<td><button type='button' class='button reply-now' data-review-id='{$review->id}' data-review-author='{$review->name}' >Reply Now</button></td>";
                    }
                }
                else {
                    echo "<td></td>";
                }
                
                echo "</tr>";
            }
        } else {
            echo '<tr><td colspan="7">No reviews found.</td></tr>';
        }
    
        echo '</tbody></table>';
        echo '</form>';
    
        // Add the popup HTML
        echo $review->admin_reply . '<div id="reply-popup" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:#fff; padding:20px; box-shadow:0 0 10px rgba(0,0,0,0.5); z-index:1000;">
                <h2>Reply to Review</h2>
                <form id="reply-form">
                    <input type="hidden" name="review_id" id="reply-review-id" value="">
                    <p><strong>Author:</strong> <span id="reply-review-author"></span></p>
                    <textarea name="reply_message" id="reply-message" rows="5" style="width:100%;" placeholder="Write your reply here..."></textarea>
                    <br><br>
                    <button type="submit" class="button button-primary">Send Reply</button>
                    <button type="button" class="button" id="close-reply-popup">Cancel</button>
                </form>
              </div>';
    
        // Add JavaScript for popup functionality
        echo "<script>
        document.querySelectorAll('.reply-now').forEach(button => {
            button.addEventListener('click', function() {
            console.log(this.dataset);
                document.getElementById('reply-review-id').value = this.dataset.reviewId;
               document.getElementById('reply-review-author').textContent = this.dataset.reviewAuthor;
            document.getElementById('reply-message').value = this.dataset.replyMessage || '';
                document.getElementById('reply-popup').style.display = 'block';
            });
        });
    
        document.getElementById('close-reply-popup').addEventListener('click', function() {
            document.getElementById('reply-popup').style.display = 'none';
        });
    
        document.getElementById('select-all').addEventListener('click', function() {
            let checkboxes = document.querySelectorAll(\"input[name='review_ids[]']\");
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
        </script>";
    
        echo '</div>';
    }

  

    
}
?>
