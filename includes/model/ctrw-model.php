<?php
if (!defined('ABSPATH')) {
    exit;
}

class Review_Model {

    private $wpdb;
    private $table;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'customer_reviews';
    }
    public function get_reviews_by_status($status) {
        if ($status === 'all') {
            return $this->wpdb->get_results("SELECT * FROM $this->table");
        } else {
            return $this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM $this->table WHERE status = %s", $status));
        }
    }

    public function get_review_counts() {

        $counts = [
            'all'      => 0,
            'approved' => 0,
            'reject'   => 0,
            'pending'  => 0,
            'trash'    => 0,
        ];

        $query = "SELECT status, COUNT(*) as count FROM $this->table GROUP BY status";
        $results = $this->wpdb->get_results($query);

        foreach ($results as $row) {
            $counts[$row->status] = $row->count;
        }

        $counts['all'] = array_sum($counts);

        return $counts;
    }

    public function update_review_status($review_ids, $status) {

        $this->wpdb->query(
            $this->wpdb->prepare(
                "UPDATE $this->table SET status = %s WHERE id IN (" . implode(',', array_map('intval', $review_ids)) . ")",
                $status
            )
        );
    }

    public function delete_reviews($review_ids) {
        $this->wpdb->query(
            "DELETE FROM $this->table WHERE id IN (" . implode(',', array_map('intval', $review_ids)) . ")"
        );
    }

    public function ctrw_add_review($data) {
        update_option('ccmuucustomer_reviews_settings', $data);
        return $this->wpdb->insert($this->table, $data);
    }

    public function get_reviews($status = 'approved'){
        return $this->wpdb->get_results("SELECT * FROM {$this->table} WHERE status = '$status' ORDER BY created_at DESC");
    }
    public function get_review_by_id($id) {
        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM $this->table WHERE id = %d", $id));
    }
    public function get_review_count_by_positionid($positionid) {
        $count = $this->wpdb->get_var(
            $this->wpdb->prepare("SELECT COUNT(*) FROM $this->table WHERE positionid = %d", $positionid)
        );
        return (int) $count;
    }
    public function get_average_rating_by_positionid($positionid) {
        $avg = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT AVG(rating) FROM $this->table WHERE positionid = %d AND status = %s",
                $positionid,
                'approved'
            )
        );
        return $avg !== null ? round((float)$avg, 2) : 0;
    }


    public function update_review($id, $data) {
     
      return $this->wpdb->update($this->table, $data, ['id' => $id]);
    }

    public function delete_review($id) {
        return $this->wpdb->delete($this->table, ['id' => $id]);
    }

    public function edit_customer_review($post) {
        $data = [
            'name' => sanitize_text_field($post['name']),
            'email' => sanitize_email($post['email']),
            'website' => esc_url($post['website']),
            'phone' => sanitize_text_field($post['phone']),
            'city' => sanitize_text_field($post['city']),
            'state' => sanitize_text_field($post['state']),
            'title' => sanitize_text_field($post['title']),
            'comment' => sanitize_textarea_field($post['comment']),
            'rating' => intval($post['rating']),
            'status' => sanitize_text_field($post['status']),
        ];
        update_option('ccccustomer_reviews_settings', $data);
        $id = intval($post['id']);

        if ($this->wpdb->update($this->table, $data, ['id' => $id])) {
            return true;
        } else {
            return false;
        }
    }

    public function check_replace_woocommerce_reviews() {
        $setting = get_option('customer_reviews_settings');
        if (!empty($setting['replace_woocommerce_reviews'])) {
            return true; // Return early if the setting is not enabled
        }
    }

    public function get_review_count_by_status($status) {
        $count = $this->wpdb->get_var(
            $this->wpdb->prepare("SELECT COUNT(*) FROM $this->table WHERE status = %s", $status)
        );
        return (int) $count;
    }
}
?>
