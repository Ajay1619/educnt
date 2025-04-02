<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    isset($_SERVER['HTTP_X_REQUESTED_PATH']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) { 
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']); 
?>
    <div class="main-content-card">
        <div class="action-box class-schedule-subject-allocation-class-section">
            <div class="action-title">Subject Mark Entery Panel</div>
            <div class="row">
                <!-- Subject List -->
                <div class="col col-4 col-lg-3 col-md-3 col-smy-3 col-xs-3">
                    <div class="main-content-card" id="subject-list"></div>
                </div>
                <!-- Timetable -->
                <div class="col col-4 col-lg-9 col-md-9 col-sm-9 col-xs-9">
    <div class="main-content-card p-6" id="timetable-schedule">
        
        
        <!-- Added Student Details Table -->
        <div class="student-details mt-4">
            <form action="" id="form_mark_submit" method="post">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Registration Number</th>
                        <th>Marks</th>
                        <th>Retest-Marks</th>
                    </tr>
                </thead>
                <tbody id="student-list">
                    <!-- Student data will be populated here dynamically -->
                </tbody>
            </table>
            <button class="primary btn right" id="marksubmit" >Save</button>
            </form>
        </div>
    </div>
</div>

            </div>
        </div>
    </div>

    <script>
        $(document).ready(async function() {


            try {
                showComponentLoading();
                await load_individual_allocated_subject_list(<?=$_GET['exam_id']?>,1);
                // await load_individual_allocated_subject_slot_list();



            } catch (error) {
                // Get error message
                const errorMessage = error.message || 'An error occurred while loading the page.';
                await insert_error_log(errorMessage);
                await load_error_popup();
                console.error('An error occurred while loading:', error);
            } finally {
                // Hide the loading screen once all operations are complete
                setTimeout(function() {
                    hideComponentLoading(); // Delay hiding loading by 1 second
                }, 100);
            }


            $('#form_mark_submit').on('submit', async function(e) {
    e.preventDefault(); 
    const formData = new FormData(this);
    
    console.log(formData);
    
    $.ajax({
        type: 'POST',
        url: '<?= MODULES . '/faculty_student_examination/ajax/faculty_mark_entry_examination.php' ?>',
        data: formData,
        processData: false,  // Add this - tells jQuery not to process the data
        contentType: false,  // Add this - tells jQuery not to set content type
        headers: {
            'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
            'X-Requested-Path': window.location.pathname + window.location.search
        }, 
        success: function(response) {
            response = JSON.parse(response);
            if (response.code == 200) {
                console.log('tost');
                showToast(response.status, response.message);
            } else {
                showToast(response.status, response.message);
            }
        },
        error: function(error) {
            showToast('error', 'Something went wrong. Please try again later.');
        }
    });
});

        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>