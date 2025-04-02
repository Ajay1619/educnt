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
    
        <table id="examTable">
            <thead>
                <tr>
                    <th>Sl no</th>
                    <th>Exam Name</th>
                    <th>Exam Group</th>
                    <th>Department</th>
                    <th>Max Marks</th>
                    <th>Min Marks</th>
                    <th>Duration</th> 
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
     
       
<script>
    $(document).ready(async function() {
        $('#examTable').DataTable({
            scrollX: true,
            initComplete: function(settings, json) {
                $('.dt-layout-table .dt-layout-cell').css('width', '100%');
                $('.dt-scroll-headInner').css('width', '100%');
                $('.dataTable').css('width', '100%');
            }
        });

        
        await load_exam_faculty_table();
        // $('tr').click(function() {
        //     console.log('clicked');
        //     // e.stopPropagation();
        //         showTooltip(this, "departments");
        // });
    });

</script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>