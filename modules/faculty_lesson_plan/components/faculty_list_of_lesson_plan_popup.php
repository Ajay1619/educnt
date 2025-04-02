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
?>
    <div class="popup-overlay" id="event-modal">
        <div class="alert-popup">
            <div class="popup-header">List of Lesson Plan</div>
            <button class="popup-close-btn">×</button>
            <div class="popup-content">
                <div class="row">
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="section-header-title text-left">Academic Year :
                            <span class="text-light" id="lesson-plan-edit-academic-year"></span>
                        </div>
                    </div>
                    <input type="hidden" name="subject_id" class="subject_id" id="subject_id">
                    <input type="hidden" name="sem_duriation_id" class="sem_duriation_id" id="sem_duriation_id">

                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="section-header-title text-right">Year Of Study :
                            <span class="text-light" id="lesson-plan-edit-year-of-study"></span>
                        </div>
                    </div>
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="section-header-title text-left">Department :
                            <span class="text-light" id="lesson-plan-edit-Department"></span>
                        </div>
                    </div>
                    <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="section-header-title text-right">Section :
                            <span class="text-light" id="lesson-plan-edit-Section"></span>
                        </div>
                    </div>
                </div>

                <!-- Added Table -->
                <div class="table-container" style="margin-top: 20px;">
                    <table class="portal-table portal-table-border ">
                        <thead>
                            <tr>
                                <th>Topic</th>
                                <th>Date</th>
                                <th>Timing Slot</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Algebra Basics</td>
                                <td>2025-02-12</td>
                                <td>10:00 AM - 11:00 AM</td>
                            </tr>
                            <tr>
                                <td>Calculus Introduction</td>
                                <td>2025-02-13</td>
                                <td>11:00 AM - 12:00 PM</td>
                            </tr>
                            <tr>
                                <td>Geometry Fundamentals</td>
                                <td>2025-02-14</td>
                                <td>02:00 PM - 03:00 PM</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="popup-footer">
                <button type="button" id="cancel-btn" class="btn-error">Cancel</button>
                <button type="submit" id="submit-btn" class="btn-success">Submit</button>
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