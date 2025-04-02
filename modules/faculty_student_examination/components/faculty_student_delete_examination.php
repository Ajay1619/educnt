<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']); 
    // print_r($_GET);
?>
<div class="popup-overlay">
    <div class="alert-popup half-width">
        <button class="popup-close-btn">×</button>
        <div class="popup-header">
            Delete Confirmation!
        </div>
        <form id="form_exam_management" method="POST">
            <div class="popup-content">
                <input type="hidden" name="exam_id" id="exam_id" class="exam_id" value="<?=$_GET["exam_id"]?>">
                <p>Are You Really Want To Delete This Examination</p>
            </div>
            <div class="popup-footer">
                <div class="popup-action-buttons">
                    <button type="submit" class="btn-success confirm" id="confirm">Yes</button>
                    <button class="btn-error slot-selection-rest-cancel-btn">No</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {  
    $('#form_exam_management').on('submit', function(e) {
        e.preventDefault(); 
        var examId = $('#exam_id').val();
        $.ajax({
            type: 'POST',
            url: '<?= MODULES . '/faculty_student_examination/ajax/faculty_delete_examination.php' ?>',
            data: { exam_id: examId },  // Fixed: Properly structured data object
            headers: {
                'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                'X-Requested-Path': window.location.pathname + window.location.search
            }, 
            success: function(response) {
                response = JSON.parse(response);
                if (response.code == 200) {
                    showToast(response.status, response.message);
                } else {
                    showToast(response.status, response.message);
                }
                $('#add-exam-popup').html('');
                load_exam_management_table();
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