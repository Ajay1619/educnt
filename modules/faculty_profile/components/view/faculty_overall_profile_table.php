<?php
include_once('../../../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);

    $designation = isset($_POST['designation']) ? sanitizeInput($_POST['designation'], 'int') : 0;
    $department = isset($_POST['department']) ? sanitizeInput($_POST['department'], 'int') : 0;

?>

    <div class="main-content-card">
        <table id="profileTable" class="striped">
            <thead>
                <tr>
                    <th>SL No</th>
                    <th>Faculty Name</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated by DataTables using AJAX -->
            </tbody>
        </table>
    </div>

    <script>
        //document.ready function
        $(document).ready(async function() {
            await load_faculty_overall_profile_table(<?= $designation ?>, <?= $department ?>);
        });
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>