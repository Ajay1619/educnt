<?php
include_once('../../../../config/sparrow.php');

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
    $achievement_single_id = isset($_GET['roleId']) ? sanitizeInput($_GET['roleId'], 'int') : 0;
?>

    <div class="container">

        <div class="popup-overlay" id="faculty-achievement-popup-overlay">
            <div class="edit-achievements-alert-popup" id="faculty-achievement-popups">
                <div class="edit-achievements-popup-content">
                    <div class="edit-achievements-form-section">

                        <form id="achievement_update-forms" method="POST" enctype="multipart/form-data">

                            <h2 class="edit-achievements">Edit Achievements</h2>


                            <div class="input-container">
                                <button type="button" class="popup-close-btn" id="popup-close-btn">
                                    &#10060
                                </button>

                                <div class="input-container dropdown-container">
                                    <input type="text" id="achievement-edit-dummy" name="achievements" class="auto dropdown-input" placeholder=" " readonly required>
                                    <label class="input-label" for="achievement-edit-dummy">Select achievement Type</label>
                                    <input type="hidden" name="achievement_type" id="achievement-edit">
                                    <span class="dropdown-arrow">&#8964;</span>
                                    <div class="dropdown-suggestions" id="achievement-suggestions-edit"></div>
                                </div>
                            </div>




                            <div class="input-container">
                                <input type="text" id="topic" name="achievement_topic" class="select" placeholder=" " required>
                                <label class="input-label" for="topic">Enter Your Topic</label>
                            </div>

                            <!-- Date and Venue Location Fields -->
                            <div class="row">
                                <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                    <div class="input-container date">
                                        <input type="text" class="bulmaCalendar" id="date-of-achievements" name="achievement_date" placeholder="dd-MM-yyyy">
                                        <label class="input-label" for="date-of-achievements">Select Date</label>
                                    </div>
                                </div>

                                <div class="col col-1 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                    <div class="input-container">
                                        <input type="text" id="venue" name="achievement_name" placeholder=" " required aria-required="true">
                                        <label class="input-label" for="venue">Venue Location</label>
                                        <div id="Venue-location-achievement" class="autocomplete-suggestions"></div>
                                    </div>
                                </div>

                                <div class="col col-1 col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="dropzone" id="dropzone">
                                        <p>Drag & Drop files here or <span class="browse-text">Browse</span></p>
                                        <input type="file" id="file-uploads" name="file_uploads" class="file-input" accept=".pdf, .doc, .docx">
                                    </div>
                                </div>

                                <div class="col  col-1 col-xs-12 col-sm-12 col-md-12 col-lg-12 text-left">
                                    <label for="file-uploads" class="upload-instruction">Upload your Document</label>
                                </div>


                            </div>
                            <input type="hidden" name="achievement_id" id="achievement-id">



                            <!-- Submit Button -->
                            <button class="achievements-button" type="submit">Save</button>
                        </form>

                    </div>

                </div>

            </div>
        </div>


    </div>

    <script src="<?= MODULES . '/faculty_achievements/js/create_achievements.js' ?>"></script>
    <script>
        $('#achievement-edit-dummy').on('click focus', async function() {

            await fetch_edit_achivements($(this));
            // console.log('fectch');
        });
        $(document).ready(function() {
            $('.browse-text').on('click', function() {
                $(this).parent().siblings('.file-input').trigger('click')
                console.log()
            })


            $('#achievement_update-forms').on('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                showComponentLoading();

                try {
                    const response = await sendAjaxRequest('<?= MODULES . '/faculty_achievements/ajax/faculty_achievement_update.php' ?>', formData, '<?= $csrf_token; ?>');
                    if (response.code == 200) {
                        showToast(response.status, response.message);
                    } else {
                        showToast(response.status, response.message);
                    }
                } catch (error) {
                    showToast('error', 'Something went wrong. Please try again later.');
                } finally {
                    hideComponentLoading();
                    location.reload();
                }
            });

            // Helper function to wrap $.ajax in a Promise



            // var calendars = bulmaCalendar.attach('#date-of-achievements', {
            //     type: 'date',
            //     dateFormat: '<?php BULMA_DATE_FORMAT; ?>',
            //     validateLabel: "",
            // });

            // Ensure each calendar instance listens for 'select' event to update the input
            calendars.forEach(calendar => {
                calendar.on('select', function(datepicker) {
                    // Update the input with the selected date
                    document.querySelector('#date-of-achievements').value = datepicker.data.value();
                });
            });
            edit_fetch_single_achievement(<?= $achievement_single_id ?>);
            $('#popup-close-btn').on('click', function() {

                $('#faculty-achievement-popup').html(''); // Clear the HTML content
            });
            //         $('#faculty-achievement-popup-overlay').on('click', function (e) {

            //     if (!$(e.target).closest('#faculty-achievement-popups').length) {
            //         console.log('pop');
            //         $('#faculty-achievement-popup').html(''); // Clear the HTML content
            //         $(this).fadeOut(); // Optional: hide the overlay if needed
            //     }
            // });
            $(window).on('click', function(event) {
                if (event.target == document.getElementById('faculty-achievement-popup-overlay')) {
                    $('#faculty-achievement-popup').html("");
                    // history.pushState({}, '', window.location.pathname); // Reset URL to remove ?type=add
                }
            });

            $('#popup-close-btn').on('click', function() {
                $('#faculty-achievement-popup').html(''); // Clear the HTML content
                $('#faculty-achievement-popup-overlay').fadeOut(); // Optional: hide the overlay if needed
            });
        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
