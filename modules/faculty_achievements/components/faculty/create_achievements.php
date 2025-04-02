<?php
include_once('../../../../config/sparrow.php');

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

    <form id="achievement-form" method="POST" enctype="multipart/form-data">
        <h5>Achievements Type</h5>


        <div class="input-container">
            <div class="input-container dropdown-container">
                <input type="text" id="achievement-dummy-create" name="achievements" class="auto dropdown-input" placeholder=" " readonly required>
                <label class="input-label" for="achievement-dummy-create">Select Your achievement Type</label>
                <input type="hidden" name="achievement_type" id="achievement-create">
                <span class="dropdown-arrow">&#8964;</span>
                <div class="dropdown-suggestions" id="achievement-suggestions-create"></div>
            </div>
        </div>


        <!-- Topic Field -->
        <div class="input-container">
            <input type="text" id="topic" name="achievement_topic" class="select" placeholder=" " required>
            <label class="input-label" for="topic">Enter Your Topic</label>
            <!-- <div id="topic-suggestions" class="autocomplete-suggestions"></div> -->
        </div>

        <!-- Date and Venue Location Fields -->
        <div class="row">
            <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <div class="input-container date">
                    <input type="date" value="" class="bulmaCalender" id="date-of-achievement" name="achievement_date" placeholder="dd-MM-yyyy">
                    <label class="input-label " for="date-of-achievement">Select Date</label>
                </div>
            </div>

            <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <div class="input-container">
                    <input type="text" id="venue" name="achievement_name" placeholder=" " required aria-required="true">
                    <label class="input-label" for="venue">Venue Location</label>
                    <div id="Venue-location-achievement" class="autocomplete-suggestions"></div>
                </div>
            </div>
        </div>
      
        <!-- File Upload Field -->
        <div class="input-container">
            <!-- <input type="file" id="file-upload" name="file_upload" accept=".pdf, .doc, .docx" required> -->
            <input type="file" id="file-upload" name="file_upload" accept=".pdf, .doc, .docx" required>
            <label class="input-label" for="file-upload">Upload File (PDF or DOC)</label>
        </div>

        <!-- Submit Button -->
        <button class="full-button" type="submit">Save</button>
    </form>


    <script src="<?= MODULES . '/faculty_achievements/js/create_achievements.js' ?>"></script>
    <script>
        $(document).ready(function() {


            // var calendars = new bulmaCalendar('#date-of-achievement', {
            //     type: 'date',
            //     dateFormat: '<?php BULMA_DATE_FORMAT ?>',
            //     validateLabel: ""
            // });
            $('#achievement-dummy-create').on('click focus', async function() {
                await fetch__create_achivements($(this));
            });

            $('#achievement-form').on('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_achievements/ajax/faculty_achievement_create.php' ?>',
                    data: formData,

                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'

                    },

                    processData: false, // Prevent jQuery from processing data
                    contentType: false, // Prevent jQuery from setting content-type header
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            showToast(response.status, response.message);
                        } else {
                            showToast(response.status, response.message);
                        }
                        location.reload();
                    },
                    error: function(error) {
                        showToast('error', 'Something went wrong. Please try again later.');
                    }
                });
            });



        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>