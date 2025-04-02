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
    <form id="single_fine-form" method="POST">
        <div class="row">
            <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="input-container">
                    <input type="text" id="student-name" name="student_name" placeholder=" " required>
                    <label class="input-label" for="student-name">Student Name</label>
                </div>
            </div>
            <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="input-container dropdown-container">
                    <input type="text" id="student-fine-category-dummy" class="auto dropdown-input" placeholder=" " required>
                    <label class="input-label" for="student-fine-category-dummy">Select Fine Category</label>
                    <input type="hidden" name="student_fine_category" id="student-fine-category">
                    <span class="dropdown-arrow">⌄</span>
                    <div class="dropdown-suggestions" id="student-fine-category-suggestions"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="input-container">
                    <input type="number" id="fine-amount" name="fine_amount" placeholder=" " min="0" required>
                    <label class="input-label" for="fine-amount">Fine Amount</label>
                </div>
            </div>
            <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="input-container">
                    <input type="text" id="student-payment-fees-remarks" name="student_payment_fees_remarks" placeholder=" " required>
                    <label class="input-label" for="student-payment-fees-remarks">Enter Remarks</label>
                </div>
            </div>
        </div>
        <button type="submit" class="full-button">Charge Fine</button>
    </form>

    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();


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