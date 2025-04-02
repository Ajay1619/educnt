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
?>

    <div class="main-content-card action-box">
        <h2 class="action-title">Department Rooms Panel</h2>
        <div class="action-box-content">
            <img class="action-image" src="<?= GLOBAL_PATH . '/images/svgs/stock-dept-rooms.svg' ?>" alt="">
            <p class="action-text">
                Select a Department to View Its Room List.
            </p>
            <div class="action-hint">
                *The foundation of success begins with an organized space. Select a department to proceed!*
            </div>
        </div>

        <div class="stock-dept-rooms-list">
            <table id="dept-rooms-list">
                <thead>
                    <tr>
                        <th>SL.NO</th>
                        <th>Room Number</th>
                        <th>Room Name</th>
                        <th>Room Category</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(async function() {
            try {
                showComponentLoading();
                await callAction()
                await fetch_dept_rooms_list(<?= $logged_dept_id ?>);
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
                }, 1000)
            }
        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>