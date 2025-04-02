<?php include_once('../../../../config/sparrow.php');

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
    $faculty = isset($_POST['designation']) ? sanitizeInput($_POST['designation'], 'int') : 0;
    // $achievement = isset($_POST['department']) ? sanitizeInput($_POST['department'], 'int') : 0;

?>
    <div class="main-content-card">
        <table id="achievementTable" class="striped">
            <thead>
                <tr>
                    <th>SL No</th>
                    <th>Faculty Name</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Achievement Holding</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated by DataTables using AJAX -->
            </tbody>
        </table>
    </div>

    <script>
        // $('#table').DataTable({
        //     scrollX: true,
        //     initComplete: function(settings, json) {
        //         // Apply the CSS after DataTable initialization
        //         $('.dt-layout-table .dt-layout-cell').css('width', '100%');
        //         $('.dt-scroll-headInner').css('width', '100%');
        //         $('.dataTable').css('width', '100%');
        //     }
        // });
        $(document).ready(async function() {
            updateUrl({
                        route: 'faculty',
                        action: 'view',
                        type: 'overall'
                    });
            await load_faculty_overall_achievements_table();
        });

    
    </script>
    

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>
