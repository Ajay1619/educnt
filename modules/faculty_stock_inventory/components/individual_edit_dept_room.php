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
        <h2 class="action-title">Edit Room</h2>

        <form id="stock-edit-rooms" method="post">
            <div class="row">
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="input-container ">
                        <input type="text" id="room-number" name="room_number" placeholder=" " required>
                        <label class="input-label" for="room-number">Enter The Room Number</label>
                    </div>
                </div>
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="input-container ">
                        <input type="text" id="room-name" name="room_name" placeholder=" " required>
                        <label class="input-label" for="room-name">Enter The Room Name</label>
                    </div>
                </div>
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="input-container dropdown-container">
                        <input type="text" id="room-floor-dummy" name="selected-subjects" class="auto dropdown-input" placeholder=" " readonly>
                        <label class="input-label" for="room-floor-dummy">Select Floor</label>
                        <input type="hidden" name="room_floor" class="room-floor-filter" id="room-floor" required>
                        <span class="dropdown-arrow">&#8964;</span>
                        <div class="dropdown-suggestions"></div>
                    </div>
                </div>
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="input-container ">
                        <input type="text" id="room-category" name="room_category" placeholder=" " required>
                        <label class="input-label" for="room-category">Enter The Room Category</label>
                    </div>
                </div>
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="input-container ">
                        <input type="text" id="room-max-capacity" name="room_max_capacity" placeholder=" " required>
                        <label class="input-label" for="room-max-capacity">Enter The Room Max Capacity</label>
                    </div>
                </div>
                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">

                    <div class='input-group' id="room-type">
                        <p for="room-type">Select The Room Type</p>

                        <label class='modern-radio'>
                            <input type='radio' name='room_type' value='1' checked>
                            <span></span>
                            <div class='modern-label'>Teaching Use</div>
                        </label>

                        <label class='modern-radio'>
                            <input type='radio' name='room_type' value='2'>
                            <span></span>
                            <div class='modern-label'>Office Use</div>
                        </label>
                    </div>

                </div>
            </div>
            <button type='submit' class='primary text-center full-width mt-6'>SUBMIT</button>
        </form>
    </div>

    <script>
        $(document).ready(async function() {
            try {
                showComponentLoading();
                await callAction()
                await fetch_individual_edit_dept_room_details('<?= $room_id ?>');
                $('#room-floor-dummy').on('click focus', async function() {
                    const element = $(this);
                    const suggestions = element.siblings(".dropdown-suggestions")
                    const value = element.siblings(".room-floor-filter")
                    showSuggestions(floor_list, suggestions, value, element);
                });

                $('#stock-edit-rooms').on('submit', async function(e) {
                    e.preventDefault();
                    try {
                        const data = $(this).serialize();
                        await stock_edit_rooms_form(data)
                    } catch (error) {
                        // get error message
                        const errorMessage = error.message || 'An error occurred while loading the page.';
                        await insert_error_log(errorMessage)
                        await load_error_popup()
                        console.error('An error occurred while loading:', error);
                    } finally {
                        // Hide the loading screen once all operations are complete
                        setTimeout(function() {
                            hideLoading(); // Delay hiding loading by 1 second
                        }, 1000)
                    }
                });
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