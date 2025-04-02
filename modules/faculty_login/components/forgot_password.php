<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
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

    <div class="forgot-password-card">
        <h2>Forgot <br> Password ?</h2>
        <p>No worries! Enter your username below, and we’ll send you a link to reset your password</p>

        <form>
            <input type="text" placeholder="Username" class="input-field">
            <button class="otp-btn" id="send_otp">Send OTP</button>
        </form>

        <a href="#" id="back_to_log_in" class="back-link">Back To Log In</a>
    </div>
    <script>
        $(document).ready(function() {
            $('#back_to_log_in').on('click', function(e) {
                e.preventDefault(); // Prevent default action of the link

                updateUrlWithParams('')
            });
            $('#send_otp').on('click', function(e) {
                e.preventDefault(); // Prevent default action of the link
                updateUrlWithParams('?action=send_otp')
            });
        });
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
