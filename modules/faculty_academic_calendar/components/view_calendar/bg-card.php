<?php
include_once('../../../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {

    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
?>

    <div class="bg-card">
        <div class="bg-card-content">
            <div class="bg-card-header">
                <div class="row">
                    <div class="col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <h2 id="bg-card-title"></h2>
                    </div>
                    <div class="col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 bg-card-header-right-content ">
                        <button class="outline bg-card-button" id="faculty-event-view-button">View Calendar</button>
                        <button class="outline bg-card-button" id="faculty-event-overall-button">Overall</button>
                        <button class="outline bg-card-bulk-upload-button" id="faculty-event-bulk-upload-button">Bulk Upload</button>
                        <button class="outline bg-card-add-button" id="faculty-event-add-button">ADD</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12" id="breadcrumbs"></div>
                </div>
            </div>
            <hr class="full-width-hr">
            <div class="bg-card-filter">
                <div class="row">
                    <div class="col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12" id="event-filter">
                        <div class="input-container dropdown-container">
                            <input type="text" class="auto event-filter-dummy dropdown-input" placeholder=" " value="">
                            <label class="input-label">Select The Event</label>
                            <input type="hidden" name="event_filter" class="event-filter">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(async function() {
            try {
                $('.event-filter-dummy').on('click focus', function() {
                fetch_events($(this));
            });
            $('.event-filter-dummy').on('blur', function() {
                //settimeout function
                setTimeout(() => {
                    var event = 0;
                    if ($('.event-filter').val()) {
                        event = $('.event-filter').val();
                    }

                    //faculty_overall_profile_table($('.faculty-designation-filter').val(), dept);
                    fetch_faculty_academic_calendar(event)
                }, 100);

            });
          
                $('#faculty-event-bulk-upload-button').on('click', async function() {

                    try {
                        updateUrl({
                            route: 'faculty',
                            action: 'add',
                            type: 'bulk_upload'
                        });
                        await load_academic_calendar_bulk_event_popup();
                    } catch (error) {
                        console.error('Error loading Add Event popup:', error);
                    }
                });
                $('#faculty-event-add-button').on('click', async function() {

                    try {
                        updateUrl({
                            route: 'faculty',
                            action: 'add',
                            type: 'individual_upload'
                        });
                        await load_add_academic_calendar_events();
                    } catch (error) {
                        console.error('Error loading Add Event popup:', error);
                    }
                });

                $('#faculty-event-overall-button').on('click', async function() {

                    try {
                        updateUrl({
                            route: 'faculty',
                            action: 'view',
                            type: 'overall'
                        });
                        await loadComponentsBasedOnURL();
                    } catch (error) {
                        console.error('Error loading Add Event popup:', error);
                    }
                });
                $('#faculty-event-view-button').on('click', async function() {

                    try {
                        updateUrl({
                            route: 'faculty',
                            action: 'view',
                            type: 'calendar'
                        });
                        await load_academic_calendar_view();
                    } catch (error) {
                        console.error('Error loading Add Event popup:', error);
                    }
                });
            } catch (error) {
                console.error('An error occurred while loading:', error);
            }
        });
        
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>