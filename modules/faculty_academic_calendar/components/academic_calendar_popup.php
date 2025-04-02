<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
  isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
  ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
  isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
  $_SERVER['REQUEST_METHOD'] == 'GET'
) {
  // Validate CSRF token
  validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
  checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);

  $designation = isset($_POST['designation']) ? sanitizeInput($_POST['designation'], 'int') : 0;
  $department = isset($_POST['department']) ? sanitizeInput($_POST['department'], 'int') : 0;

?>
<div class="popup-overlay" id="event-modal">
        <div class="alert-popup">
            <div class="popup-header">Create Event</div>
            <button class="popup-close-btn">×</button>
            <div class="popup-content">
                <form id="event-form">
                    <div class="row">
                        <div class="col col-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 ">
                            <div class="input-container dropdown-container">
                                <input type="text" id="event-dummy" class="auto dropdown-input" placeholder=" " readonly required>
                                <label class="input-label" for="event-dummy">Select Your Event</label>
                                <input type="hidden" name="event" id="event">
                                <span class="dropdown-arrow">&#8964;</span>
                                <div class="dropdown-suggestions" id="events-suggestions"></div>
                            </div>
                        </div>
                        <div class="col col-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 ">
                            <div class="input-container time">
                                <input type="time" value="" class="bulmaCalendar from-time" id="from-time" name="from_time" required aria-required="true">
                                <label class="input-label" for="from-time"></label>
                            </div>
                        </div>
                        <div class="col col-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 ">
                            <div class="input-container time">
                                <input type="time" value="" class="bulmaCalendar to-time" id="to-time" name="to_time" required aria-required="true">
                                <label class="input-label" for="to-time"></label>
                            </div>
                        </div>
                    </div>
                    <label>Assign Classes:</label>
                    <div class="row">
                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <label><input type="checkbox" class="class-checkbox" value="2nd Yr A" /> 2nd Yr A</label>
                            <div class="input-container">
                                <input type="text" id="sub-event-2nd-yr-a" name="sub_event_2nd_yr_a" placeholder=" " class="sub-event-name" required aria-required="false">
                                <label id="input_event_sub_name" class="input-label" for="sub-event-2nd-yr-a"></label>
                            </div>
                        </div>
                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <label><input type="checkbox" class="class-checkbox" value="2nd Yr B" /> 2nd Yr B</label>
                            <div class="input-container">
                                <input type="text" id="sub-event-2nd-yr-b" name="sub_event_2nd_yr_b" placeholder=" " class="sub-event-name" required aria-required="false">
                                <label id="input_event_sub_name" class="input-label" for="sub-event-2nd-yr-b"></label>
                            </div>
                        </div>
                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <label><input type="checkbox" class="class-checkbox" value="3rd Yr A" /> 3rd Yr A</label>
                            <div class="input-container">
                                <input type="text" id="sub-event-3rd-yr-a" name="sub_event_3rd_yr_a" placeholder=" " class="sub-event-name" required aria-required="false">
                                <label id="input_event_sub_name" class="input-label" for="sub-event-3rd-yr-a"></label>
                            </div>
                        </div>
                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <label><input type="checkbox" class="class-checkbox" value="3rd Yr B" /> 3rd Yr B</label>
                            <div class="input-container">
                                <input type="text" id="sub-event-3rd-yr-b" name="sub_event_3rd_yr_b" placeholder=" " class="sub-event-name" required aria-required="false">
                                <label id="input_event_sub_name" class="input-label" for="sub-event-3rd-yr-b"></label>
                            </div>
                        </div>
                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <label><input type="checkbox" class="class-checkbox" value="4th Yr A" /> 4th Yr A</label>
                            <div class="input-container">
                                <input type="text" id="sub-event-4th-yr-a" name="sub_event_4th_yr_a" placeholder=" " class="sub-event-name" required aria-required="false">
                                <label id="input_event_sub_name" class="input-label" for="sub-event-4th-yr-a"></label>
                            </div>
                        </div>
                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <label><input type="checkbox" class="class-checkbox" value="4th Yr B" /> 4th Yr B</label>
                            <div class="input-container">
                                <input type="text" id="sub-event-4th-yr-b" name="sub_event_4th_yr_b" placeholder=" " class="sub-event-name" required aria-required="false">
                                <label id="input_event_sub_name" class="input-label" for="sub-event-4th-yr-b"></label>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="popup-footer">
                <button type="button" id="cancel-btn" class="btn-error" >Cancel</button>
                <button type="submit" id="submit-btn" class="btn-success" >Submit</button>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(async function() {
            try {
                await calendar_popup_event();
                await calendar_popup_bulma_time();
            } catch (error) {
                console.error('An error occurred while processing:', error);
            }
        });
    </script>
    <?php
} else {
  echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
  exit;
}
?>