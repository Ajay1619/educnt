<?php
include_once('../../../config/sparrow.php');

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

  $designation = isset($_POST['designation']) ? sanitizeInput($_POST['designation'], 'int') : 0;
  $department = isset($_POST['department']) ? sanitizeInput($_POST['department'], 'int') : 0;

?>
<div class="popup-overlay" id="event-modal">
        <div class="alert-popup">
            <div class="popup-header">Create Event</div>
            <button class="popup-close-btn">×</button>
            <div class="popup-content">
                 
            </div>
            <div class="popup-footer">
                <button type="button" id="cancel-btn" class="btn-error" >Cancel</button>
                <button type="submit" id="submit-btn" class="btn-success" >Submit</button>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(async function() {
            try {
                await calendar_popup_event();
                await calendar_popup_bulma_time();
            } catch (error) {
                console.error('An error occurred while processing:', error);
            }
        });
    </script>
    <?php
} else {
  echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
  exit;
}
?>