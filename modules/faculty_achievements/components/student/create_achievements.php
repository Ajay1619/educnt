<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
?>

    <form action="/upload-achievement" method="POST" enctype="multipart/form-data">
        <h5>Achievements Type</h5>

        <div class="input-container">
            <div class="input-container dropdown-container">
                <input type="text" id="achievement-dummy" name="achievement" class="auto dropdown-input" placeholder=" " readonly required>
                <label class="input-label" for="achievement-dummy">Select student's achievement</label>
                <input type="hidden" name="achievement" id="achievement">
                <span class="dropdown-arrow">&#8964;</span>
                <div class="dropdown-suggestions" id="achievement-suggestions"></div>
            </div>
        </div>

       
        <!-- Topic Field -->
        <div class="input-container">
            <input  type="text" id="topic" class="select" placeholder=" " required>
            <label class="input-label" for="topic">Enter Your Topic</label>
            <!-- <div id="topic-suggestions" class="autocomplete-suggestions"></div> -->
        </div>

        <!-- Date and Venue Location Fields -->
        <div class="row">
            <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <div class="input-container">
                    <input type="date" id="date" name="date" placeholder=" " required aria-required="true">
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
        <div class="row">
            <div class="col col-4">
                <div class="input-container dropdown-container">
                    <input type="text" id="portal-typee" class="auto dropdown-input" placeholder=" " readonly required>
                    <label class="input-label" for="portal-type">Portal Type</label>
                    <input type="hidden" name="portal-type" id="portal-type">
                    <span class="dropdown-arrow">&#8964;</span>
                    <div class="dropdown-suggestions"></div>
                </div>
            </div>
            <div class="input-container">
                <input readonly type="text" id="achievement-type" class="select" placeholder=" " required>
                <label class="input-label" for="achievement-type">Select Achievement Type</label>
                <div id="achievement-type-suggestions" class="autocomplete-suggestions"></div>
            </div>
            <div class="col col-4">
                <div class="input-container">
                    <input type="text" class="auto autocomplete-input" placeholder=" ">
                    <label class="input-label" for="text">Designation</label>
                    <input type="hidden" name="designation" id="designation">
                    <span class="dropdown-arrow">&#8964;</span>
                    <div class="autocomplete-suggestions"></div>
                </div>
            </div>
            <div class="col col-4">
                <div class="input-container">
                    <input type="text" id="account-code" name="account-code" value="SVCET-FAC-0001" placeholder=" " readonly required>
                    <label class="input-label" for="account-code">Account Code</label>
                </div>
            </div>
        </div>
        <!-- File Upload Field -->
        <div class="input-container">
            <input type="file" id="file-upload" name="file-upload" accept=".pdf, .doc, .docx" required aria-required="true">
            <label class="input-label" for="file-upload">Upload File (PDF or DOC)</label>
        </div>

        <!-- Submit Button -->
        <button class="achievements-button" type="submit">Save</button>
    </form>


    <script src="<?= MODULES . '/faculty_achievements/js/create_achievements.js' ?>"></script>
    <script>
        $(document).ready(function() {

            const fetch_achivements = (element) => { // Renamed parameter from `this` to `element`
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: '<?= GLOBAL_PATH . '/json/fetch_achievement.php' ?>',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                        },
                        success: function(response) {
                            response = JSON.parse(response);
                            if (response.code == 200) {
                                const achievement = response.data;
                                showSuggestions(achievement, $('#achievement-suggestions'), $('#achievement'), element);
                            } else {
                                showToast(response.status, response.message)
                            }
                            resolve(response);
                        },
                        error: function(error) {
                            reject(error);
                        }
                    });
                });
            }
            $('#achievement-dummy').on('click focus', async function() {
            await fetch_achivements($(this));
        });
        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>