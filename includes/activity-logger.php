<?php
function logActivity($conn, $action_type, $item_type, $item_name, $user_id = null) {
    try {
        $stmt = $conn->prepare("INSERT INTO activity_log (action_type, item_type, item_name, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('sssi', $action_type, $item_type, $item_name, $user_id);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        error_log('Activity log error: ' . $e->getMessage());
    }
}
?>