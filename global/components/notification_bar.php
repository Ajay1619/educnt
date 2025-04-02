<?php
include_once('../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    if (!validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
        echo json_encode(['code' => 403, 'status' => 'error', 'message' => 'CSRF token validation failed.']);
        exit;
    }
?>

    <div class="notification-bar inactive" id="notificationBar">
        <div class="notification-header">
            <h2>Notifications</h2>
            <span class="close-btn" id="closeNotification">✖</span>
        </div>
        <ul class="notification-list">
            <li>
                <span class="notification-title">Message</span>
                <span class="notification-subject">New message from John</span>
            </li>
            <li>
                <span class="notification-title">Report</span>
                <span class="notification-subject">Your report is ready to download</span>
            </li>
            <li>
                <span class="notification-title">Meeting</span>
                <span class="notification-subject">Meeting scheduled at 3 PM</span>
            </li>
            <li>
                <span class="notification-title">Comment</span>
                <span class="notification-subject">New comment on your post</span>
            </li>
            <li>
                <span class="notification-title">Maintenance</span>
                <span class="notification-subject">System maintenance at midnight</span>
            </li>
        </ul>
    </div>


<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>