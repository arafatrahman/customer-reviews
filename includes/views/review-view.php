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
                    <th>Status</th>
                </tr>
              </thead>';
        echo '<tbody>';

        if (!empty($reviews)) {
            foreach ($reviews as $review) {
                $stars = str_repeat('⭐', $review->rating);
                echo "<tr>
                        <th scope='row' class='check-column'><input type='checkbox' name='review_ids[]' value='{$review->id}' /></th>
                        <td>{$review->author_name}</td>
                        <td>{$stars}</td>
                        <td>{$review->comments}</td>
                        <td><a href='?page=wp-review-plugin&status={$review->status}' class='review-status-link'>{$review->status}</a></td>
                      </tr>";
            }
        } else {
            echo '<tr><td colspan="7">No reviews found.</td></tr>';
        }

        echo '</tbody></table>';
        echo '</form>';

        echo "<script>
        document.getElementById('select-all').addEventListener('click', function() {
            let checkboxes = document.querySelectorAll(\"input[name='review_ids[]']\");
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
        </script>";

        echo '</div>';
    }
    public function display_settings($active_tab) {
        echo '<div class="wrap">';
        echo '<h1>Review Plugin Settings</h1>';
        
        // Tabs Navigation
        echo '<h2 class="nav-tab-wrapper">';
        echo '<a href="?page=wp-review-settings&tab=about" class="nav-tab ' . ($active_tab == 'about' ? 'nav-tab-active' : '') . '">About</a>';
        echo '<a href="?page=wp-review-settings&tab=form_settings" class="nav-tab ' . ($active_tab == 'form_settings' ? 'nav-tab-active' : '') . '">Form Settings</a>';
        echo '<a href="?page=wp-review-settings&tab=display_settings" class="nav-tab ' . ($active_tab == 'display_settings' ? 'nav-tab-active' : '') . '">Display Settings</a>';
        echo '<a href="?page=wp-review-settings&tab=shortcode" class="nav-tab ' . ($active_tab == 'shortcode' ? 'nav-tab-active' : '') . '">Shortcode</a>';
        echo '</h2>';

        echo '<form method="post" action="options.php">';

        if ($active_tab == 'about') {
            $this->about_section();
        }
        elseif ($active_tab == 'form_settings') {
            settings_fields('review_form_settings');
            do_settings_sections('review_form_settings');
            submit_button();
        }
        elseif ($active_tab == 'display_settings') {
            settings_fields('review_display_settings');
            do_settings_sections('review_display_settings');
            submit_button();
        }
        elseif ($active_tab == 'shortcode') {
            $this->shortcode();
        }

        echo '</form>';
        echo '</div>';
    }

    private function about_section() {
        echo '<p>This plugin allows you to manage customer reviews on your WordPress site.</p>';
    }
    private function shortcode() {
        echo '<p>Use the following shortcode to display the review form on any post or page:</p>';
        echo '<pre>[easy_review_form]</pre>';
    }

  

    
}
?>
