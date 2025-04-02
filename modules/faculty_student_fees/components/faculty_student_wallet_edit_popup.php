<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request and a POST request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);

    $student_wallet_id = isset($_POST['student_wallet_id']) ? sanitizeInput($_POST['student_wallet_id'], 'string') : 0;
    $wallet_action = isset($_POST['wallet_action']) ? sanitizeInput($_POST['wallet_action'], 'string') : 0;


?>

    <div class="popup-overlay" id="active-popup">
        <div class="alert-popup half-width" id="wallet-edit-popup">
            <div class="popup-header">Wallet Transaction</div>
            <button class="popup-close-btn">×</button>
            <div class="popup-content" id="wallet-edit"></div>
        </div>
    </div>

    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();
                $(".popup-close-btn").click(function() {
                    $("#student-fees-popup").empty();
                });
                if (<?= $wallet_action ?> == 1) {
                    await load_faculty_wallet_credit_single_form('<?= $student_wallet_id ?>');

                } else {
                    await load_faculty_wallet_debit_single_form('<?= $student_wallet_id ?>');

                }


            } catch (error) {
                // Get error message
                const errorMessage = error.message || 'An error occurred while loading the page.';
                await insert_error_log(errorMessage);
                await load_error_popup();
                console.error('An error occurred while loading:', error);
            } finally {
                // Hide the loading screen once all operations are complete
                setTimeout(function() {
                    hideComponentLoading(); // Delay hiding loading by 1 second
                }, 100);
            }
        });
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>