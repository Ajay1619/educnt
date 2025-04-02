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
    <div class="main-content-card action-box">
        <h2 class="action-title">Attendance </h2>
        <div class="action-box-content">
            <img class="action-image" src="<?= GLOBAL_PATH . '/images/svgs/individual_attendance_view_icon.svg' ?>" alt="">
            <p class="action-text">
                Select Your Subject To View The Attendance.
            </p>
            <div class="action-hint">
                *Every class attended is a step closer to success. Track, motivate, and make learning count!*
            </div>
        </div>

        <div class="portal-table-wrapper">
            <table id="subjectwise-attendance-table">
                <thead>
                    <tr>
                        <th>SL No</th>
                        <th>Date</th>
                        <th>Period</th>
                        <th>Present</th>
                        <th>Absent</th>
                        <th>Percentage</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div id="individual-attendance-table"></div>
    </div>

    <script>
        $(document).ready(async function() {
            $(".portal-table-wrapper").hide();
        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>