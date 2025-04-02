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
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);

?>
    <script>
const load_dashboard_admission = () => {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: 'GET',
                url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/dashboard/dashboard_profile.php', ENT_QUOTES, 'UTF-8') ?>',
                headers: {
                   'X-CSRF-Token': '<?= $csrf_token ?>' , // Secure CSRF token
                'X-Requested-Path': window.location.pathname + window.location.search// Secure CSRF token  // Secure CSRF token // Secure CSRF token
                },
                success: function(response) {
                    $('#navigation').html(response);
                    resolve(); // Resolve the promise
                },
                error: function(jqXHR) {
                    const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                    showToast('error', message);
                    reject(); // Reject the promise
                }
            });
        });
    };

       
</script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
