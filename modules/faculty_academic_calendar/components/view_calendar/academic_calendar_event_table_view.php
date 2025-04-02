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

  $designation = isset($_POST['designation']) ? sanitizeInput($_POST['designation'], 'int') : 0;
  $department = isset($_POST['department']) ? sanitizeInput($_POST['department'], 'int') : 0;

?>

  <div class="main-content-card action-box">
    <div class="main-content-card-header">
      <h2 class="action-title">Event Lists</h2>
    </div>
    <table id="event-table" class="event-table">
      <thead>
        <tr>
          <th>Slno</th>
          <th>Event Name</th>
          <th>Event Description</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>Event Type</th>
          <th>Status</th>
        </tr>
      </thead>
    </table>
  </div>


  <script>
    $(document).ready(async function() {

      try {
        showComponentLoading();
        await fetch_faculty_academic_calendar();


      } catch (error) {
        // Get error message
        const errorMessage = error.message || 'An error occurred while loading the page.';
        await insert_error_log(errorMessage);
        await load_error_popup();
        console.error('An error occurred while loading:', error);
      } finally {
        //  Hide the loading screen once all operations are complete
        setTimeout(function() {
          hideComponentLoading(); // Delay hiding loading by 1 second
        }, 100);
      }
    });
  </script>
<?php
} else {
  echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
  exit;
}
?>