<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    //Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
?>

<div class="container">

    <div class="popup-overlay">
        <div class="edit-achievements-alert-popup">
            <div class="edit-achievements-popup-content">
                <div class="edit-achievements-form-section">
                    
                    <form>
                    <button class="popup-close-btn" id="popup-close-btn">
                x
            </button>
                        <h2 class="edit-achievements" >Edit Achievements</h2>
                        <h5>Achievements Type</h5>

                        <!-- Achievement Type Field -->
                        <div class="input-container">
                            <input readonly type="text" id="achievement-type" class="select" placeholder=" " required>
                            <label class="input-label" for="achievement-type">Select Achievement Type</label>
                            <div id="achievement-type-suggestions" class="autocomplete-suggestions"></div>
                        </div>

                        <h5>Topic</h5>
                        <!-- Topic Field -->
                        <div class="input-container">
                            <input readonly type="text" id="topic" class="select" placeholder=" " required>
                            <label class="input-label" for="topic">Select Topic</label>
                            <div id="topic-suggestions" class="autocomplete-suggestions"></div>
                        </div>

                        <!-- Date and Venue Location Fields -->
                        <div class="row">
                            <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="input-container">
                                    <input type="date" id="date" name="date" placeholder=" " required
                                        aria-required="true">
                                    <label class="input-label" for="date">Date</label>
                                </div>
                            </div>

                            <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="input-container">
                                    <input type="text" id="venue" placeholder=" " required aria-required="true">
                                    <label class="input-label" for="venue">Venue Location</label>
                                    <div id="Venue-location-achievement" class="autocomplete-suggestions"></div>
                                </div>
                            </div>
                        </div>

                        <!-- File Upload Field -->
                        <div class="input-container">
                            <input type="file" id="file-upload" name="file-upload" accept=".pdf, .doc, .docx" required
                                aria-required="true">
                            <label class="input-label" for="file-upload">Upload File (PDF or DOC)</label>
                        </div>

                        <!-- Submit Button -->
                        <button class="achievements-button" type="submit">Save</button>
                    </form>

                </div>

            </div>

        </div>
    </div>


</div>

<script  src="<?= MODULES . '/faculty_achievements/js/create_achievements.js' ?>"></script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}