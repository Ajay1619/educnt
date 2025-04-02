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
    $wallet_action = isset($_POST['wallet_action']) ? sanitizeInput($_POST['wallet_action'], 'int') : 2;
    $student_wallet_id = isset($_POST['student_wallet_id']) ? sanitizeInput($_POST['student_wallet_id'], 'string') : "";
?>
    <form id="student-wallet-debit-form" method="POST">
        <input type="hidden" name="wallet_action" class="wallet_action" value="<?= $wallet_action ?>" required>
        <input type="hidden" name="student_wallet_id" class="student_wallet_id" value="<?= $student_wallet_id ?>" required>
        <input type="hidden" name="p_type" class="p_type" value="1" required>
        <div class="row">
            <div class="col col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="input-container autocomplete-container">
                    <input type="text" class="auto debit-student-name autocomplete-input" placeholder=" " required>
                    <label class="input-label">Select The Student</label>
                    <input type="hidden" name="student_id" class="student-id" required>
                    <span class="autocomplete-arrow">&#8964;</span>
                    <div class="autocomplete-suggestions"></div>
                </div>
            </div>
            <div class="col col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="input-container date"><input type="date" class="bulmaCalender" id="date-of-debit" name="wallet_date" placeholder="dd-MM-yyyy"><label class="input-label" for="date-of-debit">Date of Debit</label></div>
            </div>
        </div>
        <div class="row">
            <div class="col col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="input-container"><input type="text" id="debit-amount" name="wallet_amount" placeholder=" " required><label class="input-label" for="debit-amount">Amount Paid</label></div>
            </div>
            <div class="col col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="input-container dropdown-container">
                    <input type="text" id="debit-payment-mode-dummy" class="debit-payment-mode-dummy dropdown-input" placeholder=" " readonly required>
                    <label class="input-label" for="debit-payment-mode-dummy">Payment Mode</label>
                    <input type="hidden" name="payment_mode" id="debit-payment-mode" class="debit-payment-mode">
                    <span class="dropdown-arrow">⌄</span>
                    <div class="dropdown-suggestions" id="debit-payment-mode-suggestions"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="input-container"><input type="text" id="debit-remarks" name="wallet_remarks" placeholder=" " required><label class="input-label" for="debit-remarks">Remarks</label></div>
            </div>
        </div>
        <button type="submit" class="black full-width">Debit</button>
    </form>

    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();

                $('.debit-student-name').on('click focus', function() {
                    const element = $(this);
                    fetch_student_name_list(element, 0, 0, 0, 0);

                });

                $('.debit-student-name').on('input', function() {
                    const element = $(this);
                    const suggestions = element.siblings(".autocomplete-suggestions");
                    const value = element.siblings(".student-id");


                    // Get the input text
                    const inputText = element.val().toLowerCase();
                    // Filter faculty_name_list based on the input
                    const filteredStudentList = student_name_list.filter(student =>
                        student.title.toLowerCase().includes(inputText) ||
                        student.code.toLowerCase().includes(inputText)
                    );
                    // Pass the filtered list to showSuggestions
                    showSuggestions(filteredStudentList, suggestions, value, element);
                });

                if (<?= $student_wallet_id != 0 ?>) {
                    await fetch_individual_wallet_details('<?= $student_wallet_id ?>', 2);
                }


                $('.debit-payment-mode-dummy').on('click focus', function() {
                    const element = $(this);
                    const suggestions = element.siblings(".dropdown-suggestions");
                    const value = element.siblings(".debit-payment-mode");
                    showSuggestions(payment_methods_list, suggestions, value, element);

                });

                $('#student-wallet-debit-form').submit(async function(e) {
                    e.preventDefault();
                    showComponentLoading();
                    try {
                        const data = $(this).serialize();
                        await student_wallet_single_form_submit(data)
                    } catch (error) {
                        // POST error message
                        const errorMessage = error.message || 'An error occurred while loading the page.';
                        await insert_error_log(errorMessage)
                        await load_error_popup()
                        console.error('An error occurred while loading:', error);
                    } finally {
                        // Hide the loading screen once all operations are complete
                        setTimeout(function() {
                            hideComponentLoading(); // Delay hiding loading by 1 second
                        }, 1000)
                    }
                });
            } catch (error) {
                // POST error message
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