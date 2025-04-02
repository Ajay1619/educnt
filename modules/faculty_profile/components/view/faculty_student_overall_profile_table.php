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
    $year_of_study  = 0;
    $section = 0;
    $department = 0;

?>

    <table id="facultystudentprofileTable" class="striped">
        <thead>
            <tr>
                <th>SL No</th>
                <th>Student Name</th>
                <th>Register Number</th>
                <th>Academic Batch</th>
                <th>Year Of Study</th>
                <th>Section</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be populated by DataTables using AJAX -->
        </tbody>
    </table>

    <script>
        // Load student profile table with new filters (Year and Section)
        const load_student_overall_profile_table = (year_of_study, section, department) => {
            $('#facultystudentprofileTable').DataTable().destroy()
            $('#facultystudentprofileTable').DataTable({
                "serverSide": true,
                "ajax": {
                    "url": "<?= MODULES . '/faculty_profile/json/overall__student_profile_table_data.php' ?>",
                    "type": "POST",
                    "data": {
                        "year_of_study": year_of_study,
                        "section": section,
                        "department": department
                    }
                },
                "columns": [{
                        "data": "s_no",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "student_name"
                    },
                    {
                        "data": "register_number"
                    },
                    {
                        "data": "academic_batch"
                    },
                    {
                        "data": "year_of_study"
                    },
                    {
                        "data": "section"
                    },
                    {
                        "data": "status"
                    },
                    {
                        "data": "action"
                    }
                ],
                "scrollX": true,
                "language": {
                    "emptyTable": "No data available matching the selected criteria.",
                    "loadingRecords": table_loading
                },
            });


            $('.dt-layout-row .dt-layout-table').css('width', '100%');
            $('.dt-layout-table .dt-layout-cell').css('width', '100%');
            $('.dt-scroll-headInner').css('width', '100%');
            $('.dataTable').css('width', '100%');
        }

        $(document).ready(function() {
            // Modify this to pass the filters for Year of Study and Section
            const year_of_study = <?= $year_of_study ?>;
            const section = <?= $section ?>;
            const department = <?= $department ?>;
            load_student_overall_profile_table(year_of_study, section, department);



        });
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>