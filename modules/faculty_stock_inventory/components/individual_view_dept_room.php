<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {

    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    $room_id = isset($_POST['room_id']) ? sanitizeInput($_POST['room_id'], 'string') : "";


?>

    <div class="main-content-card action-box">
        <h2 class="action-title">Room Details</h2>
        <div id="stock-view-rooms"></div>
        <div id="room-items-list">
            <table id="room-items-list-table">
                <thead>
                    <tr>
                        <th>SL No</th>
                        <th>Item Name</th>
                        <th>Unit Of Measure</th>
                        <th>Quantity</th>
                        <th>Note</th>
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
                await fetch_individual_view_dept_room_details('<?= $room_id ?>');
                await fetch_dept_room_items_list($("#room-id").val());

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