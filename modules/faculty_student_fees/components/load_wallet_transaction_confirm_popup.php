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
    $confirmation_type = isset($_POST['confirmation_type']) ? sanitizeInput($_POST['confirmation_type'], 'int') : 0;
    $wallet_action = isset($_POST['wallet_action']) ? sanitizeInput($_POST['wallet_action'], 'int') : 0;



    $title = ($confirmation_type == 1) ? 'Confirm Wallet Transaction' : 'Cancel Wallet Transaction';
    $image = ($confirmation_type == 1)
        ? GLOBAL_PATH . '/images/svgs/confirm_action_icon.svg'
        : GLOBAL_PATH . '/images/svgs/cancel_action_icon.svg';

    // Determine wallet action text
    $wallet_action_text = ($wallet_action == 1) ? 'Credit' : (($wallet_action == 2) ? 'Debit' : 'Unknown');

    $confirmation_message = ($confirmation_type == 1)
        ? "Are you sure you want to confirm this {$wallet_action_text} transaction?"
        : "Are you sure you want to cancel this {$wallet_action_text} transaction?";
?>

    <div class="popup-overlay" id="active-popup">
        <div class="alert-popup half-width" id="wallet-confirmation-popup">
            <div class="popup-header"><?= $title ?></div>
            <button class="popup-close-btn">×</button>
            <div class="popup-content" id="wallet-confirmation">
                <div class="confirmation-details">
                    <img class="popup-image" src="<?= $image ?>" alt="Action Icon" class="action-icon">
                    <p><?= $confirmation_message ?></p>
                </div>
            </div>
            <div class="popup-footer">
                <div class="popup-action-buttons">
                    <button class="btn-success wallet-transaction-confirmation-button">Yes, Confirm</button>
                    <button class="btn-error wallet-transaction-cancel-button">No, Cancel</button>
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
                $(".wallet-transaction-cancel-button").click(function() {
                    $("#student-fees-popup").empty();
                });

                $(".wallet-transaction-confirmation-button").click(function() {
                    confirm_wallet_transaction('<?= $student_wallet_id ?>', <?= $confirmation_type ?>);
                });


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