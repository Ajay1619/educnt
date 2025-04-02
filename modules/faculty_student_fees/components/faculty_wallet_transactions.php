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

    <h2 class="action-title">Wallet Transactions</h2>
    <div class="action-box-content">
        <img class="action-image" src="<?= GLOBAL_PATH . '/images/svgs/select_action_image.svg' ?>" alt="">
        <p class="action-text">
            Please refine your wallet search by selecting a Year of Study and Section.
        </p>
        <div class="action-hint">
            *Knowledge grows when you seek it—give it another try!*
        </div>
    </div>

    <table id="wallet-transactions-table">

        <thead>
            <tr>
                <th>SL.NO</th>
                <th>Student Name</th>
                <th>Register Number</th>
                <th>Amount</th>
                <th>Transaction Type</th>
                <th>Transaction Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script>
        $(document).ready(async function() {



            try {
                showComponentLoading();
                if ($(".section-filter").val() != 0 && $(".section-filter").val() != null && $(".section-filter").val() != undefined) {
                    load_student_wallet_transactions_table($(".dept-filter").val(), $(".year-of-study-filter").val(), $(".section-filter").val());
                } else {
                    $("#wallet-transactions-table").hide();
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
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.'], JSON_THROW_ON_ERROR);
    exit;
}
?>