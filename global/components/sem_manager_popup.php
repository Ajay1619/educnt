<?php
include_once('../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    // checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);

    $designation = isset($_POST['designation']) ? sanitizeInput($_POST['designation'], 'int') : 0;
    $department = isset($_POST['department']) ? sanitizeInput($_POST['department'], 'int') : 0;

?>
    <div class="popup-overlay" id="sem-manager-list">
        <div class="alert-popup" id="sem-manager-popup-list">
            <div class="popup-header">Semester Management</div>
            <button class="popup-close-btn">×</button>
            <div class="popup-content">
                <!-- Invisible Table -->
                <p class="popup-quotes">"Start the clock, freeze the frame—every semester has its own rhythm⏰🎓" </p>
                <div class="list-container"></div>
            </div>
        </div>
    </div>
    <script>
        $(window).on('click', function(event) {
            if (event.target == document.getElementById('freeze-popup-list')) {
                $('#freeze-active-popup').html('');
            }
        });

        //document.ready function
        $(document).ready(async function() {
            try {
                showComponentLoading()
                await load_sem_list();
            } catch (error) {
                // get error message
                const errorMessage = error.message || 'An error occurred while loading the page.';
                await insert_error_log(errorMessage)
                await load_error_popup()
                console.error('An error occurred while loading:', error);
            } finally {
                // Hide the loading screen once all operations are complete
                setTimeout(function() {
                    hideComponentLoading(); // Delay hiding loading by 1 second
                }, 100)
            }
        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>