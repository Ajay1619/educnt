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
    <form id="bulk_fine_upload-form" method="POST">
        <div class="row">
            <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="input-container dropdown-container">
                    <input type="text" id="student-year-of-study-fine-dummy" class="auto dropdown-input" placeholder=" " required>
                    <label class="input-label" for="student-year-of-study-fine-dummy">Select Year</label>
                    <input type="hidden" name="student_year_of_study_fine" id="student-year-of-study-fine">
                    <span class="dropdown-arrow">⌄</span>
                    <div class="dropdown-suggestions" id="student-year-of-study-fine-suggestions"></div>
                </div>
            </div>
            <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="input-container dropdown-container">
                    <input type="text" id="student-section-fine-dummy" class="auto dropdown-input" placeholder=" " required>
                    <label class="input-label" for="student-section-fine-dummy">Select Section</label>
                    <input type="hidden" name="student_section_fine" id="student-section-fine">
                    <span class="dropdown-arrow">⌄</span>
                    <div class="dropdown-suggestions" id="student-section-fine-suggestions"></div>
                </div>
            </div>
        </div>
        <div class="card mt-3">
            <h4>Student Fine List</h4>
            <div class="curvy-table-container">
                <table class="curvy-table">
                    <thead>
                        <tr>
                            <th class="curvy-th flex-container justify-center">
                                <label class="modern-checkbox">
                                    <input type="checkbox" id="mother-checkbox" />
                                    <span></span>
                                </label>
                            </th>
                            <th>Student Name</th>
                            <th>Fine Category</th>
                            <th>Fine Amount</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="student-table-body">
                        <!-- Student data will be populated via JS -->
                    </tbody>
                </table>
            </div>
            <div class="mt-2">
                Selected: <span id="selected-count-of-data">0</span> |
                Remaining: <span id="remaining-count-of-data">0</span>
            </div>
        </div>
        <button type="submit" class="full-button mt-3">Submit Bulk Fines</button>
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