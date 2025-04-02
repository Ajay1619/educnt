<?php
include_once('../../config/sparrow.php');

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
    <form id="loginForm" class="login-form" method="POST">
        <h2>Login</h2>
        <div class="input-group">
            <label for="username">Username <?= GLOBAL_PATH ?></label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
        <p class="message">Don't have an account? <a href="#">Sign up</a></p>
    </form>

    <script>
        $('#loginForm').on('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission
            // AJAX request
            $.ajax({
                type: 'POST',
                url: '<?= htmlspecialchars(GLOBAL_PATH . '/ajax/process_login.php', ENT_QUOTES, 'UTF-8') ?>', // Secure URL
                data: $(this).serialize(), // Serialize form data
                headers: {
                    'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
                },
                dataType: 'json',
                success: (response) => {
                    // Check for success status code
                    const isSuccess = response.code === 200;
                    showToast(isSuccess ? 'success' : 'error', response.message);
                },
                error: (jqXHR) => {
                    const message = jqXHR.status === 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                    showToast('error', message);
                }
            });
        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>