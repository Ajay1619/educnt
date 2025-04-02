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
    <form id="debit_bulk_upload-form" method="POST" enctype="multipart/form-data">
        <div class="card">
            <h4>Bulk Upload Debit Records (CSV/Excel)</h4>
            <button class="info-icon-btn" id="debit-download-template">
                <span class="ml-3 info tooltip tooltip-right">
                    <img src="<?= GLOBAL_PATH . '/images/svgs/info.svg' ?>" alt="Info Icon" class="info-icon">
                    <span class="tooltip-text">
                        <strong>INFO</strong>
                        <div>Debit Bulk-upload Sample Excel file</div>
                    </span>
                </span>
            </button>
            <div class="row">
                <div class="col col-12">
                    <div class="dropzone" id="debit-dropzone" data-type="debit">
                        <p>Drag & Drop or <span class="browse-text">Browse</span></p>
                        <input type="file" id="debit-file-upload" name="debit_bulk_upload" class="file-input" accept=".csv,.xlsx">
                        <input type="hidden" class="previous-link" id="previous-debit-bulk" name="previous_debit_bulk" value="">
                        <input type="hidden" id="debit-bulk-id" name="debit_bulk_id" value="0">
                    </div>
                </div>
            </div>
            <div class="preview-container" data-type="debit"></div>
        </div>
        <button type="submit" class="full-button">Upload Debit Records</button>
    </form>

    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();
                await tabs_active();
                $('#single-credit-form').click(async function() {
                    updateUrl({
                        route: 'faculty',
                        action: 'add',
                        type: 'wallet',
                        tab: 'credit-single'
                    });
                    await load_faculty_wallet_credit_single_form();
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