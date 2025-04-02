<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] === 'GET'
) {
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']); 
    $exam_type_id = isset($_GET['exam_type_id']) ? sanitizeInput($_GET['exam_type_id'], 'int') : 0;
    $exam_group_id = isset($_GET['exam_group_id']) ? sanitizeInput($_GET['exam_group_id'], 'int') : 0;
    $exam_id = isset($_GET['exam_id']) ? sanitizeInput($_GET['exam_id'], 'int') : 0;
    $exam_duration = isset($_GET['exam_duration']) ? sanitizeInput($_GET['exam_duration'], 'float') : 0;
    $exam_start_date = isset($_GET['exam_start_date']) ? sanitizeInput($_GET['exam_start_date'], 'string') : '';
    $exam_end_date = isset($_GET['exam_end_date']) ? sanitizeInput($_GET['exam_end_date'], 'string') : '';
?> 
 <div class="main-content-card action-box" id="fine-contents">
    <h2 class="action-title">Manage Exam Records</h2>
    <div id="charge-fine-contents">
        <img class="action-image" src="<?= GLOBAL_PATH . '/images/svgs/exam_shadule_mark.svg' ?>" alt="">
        <p class="action-text">
            You are about to manage exam records for students. Would you like to <span class="highlight" data-exam_id="<?=$exam_id?>" data-exam_group_id="<?=$exam_group_id?>" data-exam_type_id="<?=$exam_type_id?>" data-exam_duration="<?=$exam_duration?>" data-exam_start_date="<?=$exam_start_date?>" data-exam_end_date="<?=$exam_end_date?>" onclick="view_exam_result(this)" id="single-fine-form">View Exam Results</span> or <span class="highlight" data-exam_id="<?=$exam_id?>" data-exam_group_id="<?=$exam_group_id?>" data-exam_type_id="<?=$exam_type_id?>" data-exam_duration="<?=$exam_duration?>" data-exam_start_date="<?=$exam_start_date?>" data-exam_end_date="<?=$exam_end_date?>" onclick="exam_mark_entry(this)" id="bulk-fine-form">Enter Exam Marks</span>?
        </p>
        <div class="action-hint">
            *Effective record management ensures accuracy and supports student progress, much like a well-organized academic system.*
        </div>
    </div>
</div>

    <script>
        // $(document).ready(async function() {

        //     try {
        //         showComponentLoading();

        //         $('.tab').removeClass('active');


        //         const urlParams = new URLSearchParams(window.location.search);
        //         const route = urlParams.get('route');
        //         const tab = urlParams.get('tab');
        //         switch (route) {
        //             case "faculty":
        //                 switch (tab) {
        //                     case "charge":

        //                         $('#fine-charge-tab').addClass('active');
        //                         break;
        //                     case "charge-single":
        //                         await load_student_charge_single_fine();

        //                         $('#fine-charge-tab').addClass('active');
        //                         break;
        //                     case "charge-bulk":
        //                         await load_student_charge_bulk_fine();

        //                         $('#fine-charge-tab').addClass('active');
        //                         break;
        //                     case "log-book":

        //                         await load_student_fine_log_book();

        //                         $('#log-book-tab').addClass('active');
        //                         break;
        //                     default:
        //                         window.location.href = '<?= htmlspecialchars(BASEPATH . '/not-found', ENT_QUOTES, 'UTF-8') ?>';
        //                         break;
        //                 }
        //                 break;

        //             default:
        //                 window.location.href = '<?= htmlspecialchars(BASEPATH . '/not-found', ENT_QUOTES, 'UTF-8') ?>';
        //                 break;
        //         }

        //         await tabs_active();
        //         // Optional functionality
        //         $('#fine-charge-tab').click(async function() {
        //             updateUrl({
        //                 route: 'faculty',
        //                 action: 'add',
        //                 type: 'fine',
        //                 tab: 'charge'
        //             });
        //             await load_student_fine();
        //         });
        //         $('#log-book-tab').click(async function() {
        //             updateUrl({
        //                 route: 'faculty',
        //                 action: 'view',
        //                 type: 'fine',
        //                 tab: 'log-book'
        //             });
        //             await load_student_fine_log_book();
        //         });


        //         $('#single-fine-form').click(async function() {
        //             updateUrl({
        //                 route: 'faculty',
        //                 action: 'add',
        //                 type: 'fine',
        //                 tab: 'charge-single'
        //             });
        //             await load_student_charge_single_fine();
        //         });
        //         $('#bulk-fine-form').click(async function() {
        //             updateUrl({
        //                 route: 'faculty',
        //                 action: 'view',
        //                 type: 'fine',
        //                 tab: 'charge-bulk'
        //             });
        //             await load_student_charge_bulk_fine();
        //         });
        //     } catch (error) {
        //         // Get error message
        //         const errorMessage = error.message || 'An error occurred while loading the page.';
        //         await insert_error_log(errorMessage);
        //         await load_error_popup();
        //         console.error('An error occurred while loading:', error);
        //     } finally {
        //         // Hide the loading screen once all operations are complete
        //         setTimeout(function() {
        //             hideComponentLoading(); // Delay hiding loading by 1 second
        //         }, 100);
        //     }
        // });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.'], JSON_THROW_ON_ERROR);
    exit;
}
?>