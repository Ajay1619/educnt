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
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);

    $_SESSION['admission_student_existing'] = 0;
    $admission_type = isset($_POST['admission_type']) ? sanitizeInput($_POST['admission_type'], 'int') : 0;
    $admission_method = isset($_POST['admission_method']) ? sanitizeInput($_POST['admission_method'], 'int') : 0;
    // echo $_SESSION['admission_student_existing'] ;
?>

    <div class="main-content-card">

        <div class="content">
            <table id="profileTable">
                <thead>
                    <tr>
                        <th>Sl no</th>
                        <th>Name</th>
                        <th>Admission Type</th>
                        <th>Admission Method</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>


    <!-- Popup Modal for Edit -->


    <script>
        const logged_role_id = <?= $logged_role_id ?>;
        if (logged_role_id != 13) {
            $('#action-button').remove();
        }
        $(document).ready(async function() {
            await load_faculty_overall_admission_table();


        });

        function toggle(source) {
            checkboxes = document.getElementsByName('profileCheckbox');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }
        // parent_account_creation();
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>