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
    <div class="tabs mt-6">
        <div class="tab active" id="credit-tab">Credit</div>
        <div class="tab" id="debit-tab">Debit</div>
        <div class="tab" id="wallet-transactions-tab">Transactions</div>
    </div>
    <div class="main-content-card action-box" id="wallet-contents">

    </div>

    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();

                $('.tab').removeClass('active');



                const urlParams = new URLSearchParams(window.location.search);
                const route = urlParams.get('route');
                const tab = urlParams.get('tab');
                switch (route) {
                    case "faculty":
                        switch (tab) {
                            case "credit":
                                await load_student_wallet_credit();

                                $('#credit-tab').addClass('active');



                                break;
                            case "credit-single":
                                await load_student_wallet_credit();
                                await load_faculty_wallet_credit_single_form();

                                $('#credit-tab').addClass('active');

                                break;
                            case "credit-bulk":
                                await load_student_wallet_credit();
                                await load_faculty_wallet_credit_bulk_form();

                                $('#credit-tab').addClass('active');
                                break;
                            case "debit":
                                await load_student_wallet_debit();

                                $('#debit-tab').addClass('active');
                                break;
                            case "debit-single":
                                await load_student_wallet_debit();
                                await load_faculty_wallet_debit_single_form();

                                $('#debit-tab').addClass('active');
                                break;
                            case "debit-bulk":
                                await load_student_wallet_debit();
                                await load_faculty_wallet_debit_bulk_form();

                                $('#debit-tab').addClass('active');
                                break;
                            case "transactions":
                                await load_faculty_wallet_transactions();

                                $('#wallet-transactions-tab').addClass('active');

                                break;
                            default:
                                window.location.href = '<?= htmlspecialchars(BASEPATH . '/not-found', ENT_QUOTES, 'UTF-8') ?>';
                                break;
                        }
                        break;

                    default:
                        window.location.href = '<?= htmlspecialchars(BASEPATH . '/not-found', ENT_QUOTES, 'UTF-8') ?>';
                        break;
                }

                await tabs_active();
                // Optional functionality
                $('#credit-tab').click(async function() {
                    updateUrl({
                        route: 'faculty',
                        action: 'add',
                        type: 'wallet',
                        tab: 'credit'
                    });
                    await load_student_wallet_credit();
                });
                $('#debit-tab').click(async function() {
                    updateUrl({
                        route: 'faculty',
                        action: 'add',
                        type: 'wallet',
                        tab: 'debit'
                    });
                    await load_student_wallet_debit();
                });
                $('#wallet-transactions-tab').click(async function() {
                    updateUrl({
                        route: 'faculty',
                        action: 'view',
                        type: 'wallet',
                        tab: 'transactions'
                    });
                    await load_faculty_wallet_transactions();
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