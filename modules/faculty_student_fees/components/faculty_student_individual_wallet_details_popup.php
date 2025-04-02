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


?>

    <div class="popup-overlay" id="active-popup">
        <div class="alert-popup half-width" id="wallet-view-popup">
            <div class="popup-header">Wallet Transaction</div>
            <button class="popup-close-btn">×</button>
            <div class="popup-content" id="wallet-view">
                <div class="row">
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 title-value-section">
                        <div class="title">Student Name</div>
                        <div class="value" id="student-name"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 title-value-section">
                        <div class="title">Register Number</div>
                        <div class="value" id="register-number"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 title-value-section">
                        <div class="title">Transaction Type</div>
                        <div class="value" id="transaction-type"><span class="alert alert-info"></span></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 title-value-section">
                        <div class="title">Transaction Date</div>
                        <div class="value" id="transaction-date"><span class="alert alert-info"></span></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 title-value-section">
                        <div class="title">Remarks</div>
                        <div class="value" id="remarks"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 title-value-section">
                        <div class="title">Reference ID</div>
                        <div class="value" id="reference-id"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 title-value-section">
                        <div class="title">Payment Method</div>
                        <div class="value" id="payment-method"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 title-value-section">
                        <div class="title">Transaction Status</div>
                        <div class="value" id="transaction-status"><span class="alert alert-info"></span></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();
                $(".popup-close-btn").click(function() {
                    $("#student-fees-popup").empty();
                });
                await fetch_individual_wallet_details('<?= $student_wallet_id ?>', 1);


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