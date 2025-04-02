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
    <div class="table">
    <div class="main-content-card">
    <div class="content">
        <!-- <table id="examTable">
            <thead>
                <tr>
                    <th>Sl no</th>
                    <th>Exam Name</th>
                    <th>Exam Group</th> 
                    <th>Max Marks</th>
                    <th>Min Marks</th>
                    <th>Duration</th> 
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table> -->
    </div>
</div>
    </div>
</div>
       
<script>
    $(document).ready(async function() {
        

        if(<?=$logged_role_id?> == 7){
            await loadexaminortable();
        }else{
            await loadfacultytable();
        }
        // $('#examTable').DataTable({
        //     scrollX: true,
        //     initComplete: function(settings, json) {
        //         $('.dt-layout-table .dt-layout-cell').css('width', '100%');
        //         $('.dt-scroll-headInner').css('width', '100%');
        //         $('.dataTable').css('width', '100%');
        //     }
        // });
        // await load_exam_management_table();
        
       
});

</script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>