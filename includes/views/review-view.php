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
                    <th>Review Title</th>
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
                        <td>{$review->title}</td>
                        <td>{$review->name}</td>
                        <td>{$stars}</td>
                        <td>{$review->comment}</td>
                        <td>{$review->admin_reply}</td>
                        <td><a href='?page=wp-review-plugin&status={$review->status}' class='review-status-link'>{$review->status}</a></td>";
                if ($review->status !== 'rejected') {
                    
                        echo "<td><button type='button' class='button reply-now' data-review-id='{$review->id}' data-review-author='{$review->name}' 
                        data-reply-message='{$review->admin_reply}'>Reply</button>  
                        <button type='button' class='button edit-review' 
                        data-review-id='{$review->id}' data-review-author='{$review->name}' data-review-email='{$review->email}' 
                        data-review-phone='{$review->phone}' data-review-website='{$review->website}' data-review-title='{$review->title}' 
                        data-review-comment='{$review->comment}' data-review-rating='{$review->rating}' data-review-status='{$review->status}'  
                        data-review-city='{$review->city}' data-review-state='{$review->state}' data-review-positionid='{$review->positionid}'>Edit Review</button></td>";
                    
                    

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
        echo '<div id="cr-reply-popup" style="display:none;position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:#fff; padding:20px; box-shadow:0 0 10px rgba(0,0,0,0.5); z-index:1000;">
                <h2>Reply to Review</h2>
                <form id="reply-form">
                    <input type="hidden" name="review_id" id="reply-review-id" value="">
                    <p><strong>To:</strong> <span id="reply-review-author"></span></p>
                    <textarea name="reply_message" id="reply-message" rows="5" style="width:100%;" placeholder="Write your reply here..."></textarea>
                    <br><br>
                    <button type="submit" class="button button-primary">Send Reply</button>
                    <button type="button" class="button" id="close-reply-popup">Cancel</button>
                </form>
              </div>';

        // Add the edit review popup HTML
        echo '<div id="cr-edit-review-popup" style="width:40%;display:none;position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:#fff; padding:20px; box-shadow:0 0 10px rgba(0,0,0,0.5); z-index:1000;">
                <h2>Edit Review</h2>
                <form id="edit-review-form">
                    <input type="hidden" name="review_id" id="edit-review-id" value="">
                    <p>
                        <label for="edit-review-name"><strong>Review Author:</strong></label><br>
                        <input type="text" name="review_name" id="edit-review-name" style="width:100%;" placeholder="Enter review author name">
                    </p>
                    <p>
                        <label for="edit-review-email"><strong>Review Email:</strong></label><br>
                        <input type="email" name="review_email" id="edit-review-email" style="width:100%;" placeholder="Enter review author email">
                    </p>
                    <p>
                        <label for="edit-review-website"><strong>Review Website:</strong></label><br>
                        <input type="url" name="review_website" id="edit-review-website" style="width:100%;" placeholder="Enter review author website">
                    </p>
                    <p>
                        <label for="edit-review-phone"><strong>Review Phone:</strong></label><br>
                        <input type="tel" name="review_phone" id="edit-review-phone" style="width:100%;" placeholder="Enter review author phone">
                    </p>
                    <p>
                        <label for="edit-review-city"><strong>Review City:</strong></label><br>
                        <input type="text" name="review_city" id="edit-review-city" style="width:100%;" placeholder="Enter review author city">
                    </p>
                    <p>
                        <label for="edit-review-state"><strong>Review State:</strong></label><br>
                        <input type="text" name="review_state" id="edit-review-state" style="width:100%;" placeholder="Enter review author state">
                    </p>

                    <p>
                        <label for="edit-review-title"><strong>Review Title:</strong></label><br>
                        <input type="text" name="review_title" id="edit-review-title" style="width:100%;" placeholder="Enter review title">
                    </p>
                    <p>
                        <label for="edit-review-comment"><strong>Review Comment:</strong></label><br>
                        <textarea name="review_comment" id="edit-review-comment" rows="5" style="width:100%;" placeholder="Enter review comment"></textarea>
                    </p>
                    <p>
                        <label for="edit-review-rating"><strong>Rating:</strong></label><br>
                        <select name="review_rating" id="edit-review-rating" style="width:100%;">
                            <option value="1">1 Star</option>
                            <option value="2">2 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="5">5 Stars</option>
                        </select>
                    </p>
                    <p>
                        <label for="edit-review-status"><strong>Status:</strong></label><br>
                        <select name="review_status" id="edit-review-status" style="width:100%;">
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Rejected</option>
                            <option value="trash">Trash</option>
                        </select>
                    </p>
                    <p>
                        <label for="edit-review-positionid"><strong>Reviewed Display Post/Page:</strong></label><br>
                        <select name="review-positionid" id="edit-review-positionid" style="width:100%;">';
                            
                            $posts = get_posts(['post_type' => ['post', 'page'], 'numberposts' => -1]);
                            foreach ($posts as $post) {
                                echo "<option value='{$post->ID}'>{$post->post_title}</option>";
                            }
                            
                        echo '</select>
                    </p>
                    <button type="submit" id="update-customer-review" class="button button-primary">Update Review</button>
                    <button type="button" class="button" id="close-edit-review-popup">Cancel</button>
                </form>
              </div>';
    
        // Add JavaScript for popup functionality
        echo "<script>
        
        </script>";
    
        echo '</div>';
    }
  
}
?>
