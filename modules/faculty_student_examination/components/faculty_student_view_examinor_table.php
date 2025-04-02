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
                    <!-- <th>Department</th> -->
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

        
        await load_exam_management_table();
        // $('tr').click(function() {
        //     console.log('clicked');
        //     // e.stopPropagation();
        //         showTooltip(this, "departments");
        // });
        $(document).on('mouseenter', '.eye[src*="eye.svg"]', function(e) {
                e.stopPropagation();
                showTooltip(this);
            }).on('mouseleave', '.eye[src*="eye.svg"]', function() {
                setTimeout(() => {
                    if (!$('.tooltip-popup').is(':hover')) {
                        $('.tooltip-popup').remove();
                    }
                }, 200);
            });

            // Hover handling for tooltip
            $(document).on('mouseenter', '.tooltip-popup', function() {
                // Keep tooltip open
            }).on('mouseleave', '.tooltip-popup', function() {
                $(this).remove();
            });
        // $('tbody tr').hover(
        //         function(e) { // Mouse enter    
        //             e.stopPropagation();
        //             showTooltip(this);
        //         },
        //         function() { // Mouse leave
        //             setTimeout(() => {
        //                 if (!$('.tooltip-popup').is(':hover')) {
        //                     $('.tooltip-popup').remove();
        //                 }
        //             }, 200);
        //         }
        //     );

        //     $(document).on('mouseenter', '.tooltip-popup', function() {
        //         // Do nothing, just keep it open
        //     }).on('mouseleave', '.tooltip-popup', function() {
        //         $(this).remove();
        //     });
       
});

</script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>