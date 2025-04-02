<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
?>
    <div class="main-content-card-header">
        <h2 class="action-title">Debit Funds</h2>
        <div id="wallet-debit-contents">
            <img class="action-image" src="<?= GLOBAL_PATH . '/images/svgs/debit_action_image.svg' ?>" alt="">
            <p class="action-text">
                "You're about to Debit funds from student wallets. Do you want to debit <span class="highlight" id="single-debit-form">a single student</span> or <span class="highlight" id="bulk-debit-form">upload in bulk</span>?"
            </p>
            <div class="action-hint">
                *Even heroes sometimes need a reset—handle with care and purpose, like Stark with every suit upgrade.*
            </div>
        </div>
    </div>

    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();
                await tabs_active();
                $('#single-debit-form').click(async function() {
                    updateUrl({
                        route: 'faculty',
                        action: 'add',
                        type: 'wallet',
                        tab: 'debit-single'
                    });
                    await load_faculty_wallet_debit_single_form(0, 1);
                });
                $('#bulk-debit-form').click(async function() {
                    updateUrl({
                        route: 'faculty',
                        action: 'add',
                        type: 'wallet',
                        tab: 'debit-bulk'
                    });
                    await load_faculty_wallet_debit_bulk_form();
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
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.'], JSON_THROW_ON_ERROR);
    exit;
}
?>