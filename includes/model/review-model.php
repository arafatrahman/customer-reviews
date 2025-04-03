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

    public function add_review($data) {
        return $this->wpdb->insert($this->table, $data);
    }

    public function get_reviews($status = 'approved') {
        return $this->wpdb->get_results("SELECT * FROM {$this->table} WHERE status = '$status' ORDER BY created_at DESC");
    }

    public function update_review($id, $data) {
        return $this->wpdb->update($this->table, $data, ['id' => $id]);
    }

    public function delete_review($id) {
        return $this->wpdb->delete($this->table, ['id' => $id]);
    }
}
?>
