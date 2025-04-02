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

    <div class="bg-card">
        <div class="bg-card-content">
            <div class="bg-card-header">
                <div class="row">
                    <div class="col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <h2 id="bg-card-title"></h2>
                    </div>
                    <div class="col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 bg-card-header-right-content ">
                        <button class="outline bg-card-button" id="faculty-stock-add-item-button">Add Products</button>
                        <button class="outline bg-card-button" id="faculty-stock-add-room-button">Add Room</button>
                        <button class="outline bg-card-button" id="faculty-stock-view-button">View Rooms</button>
                        <button class="outline bg-card-back-button" id="bg-card-back-button">Back</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12" id="breadcrumbs"></div>
                </div>
            </div>
            <?php if (in_array($logged_role_id, $primary_roles) || in_array($logged_role_id, $higher_official)) { ?>
                <hr class="full-width-hr">
                <div class="bg-card-filter">
                    <div class="row">
                        <div class=" col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12" id="dept">
                            <div class="input-container dropdown-container">
                                <input type="text" class="auto faculty-dept-filter-dummy dropdown-input" placeholder=" " value="" readonly>
                                <label class="input-label">Select The Department</label>
                                <input type="hidden" name="faculty_dept_filter" id="faculty-dept-filter" class="faculty-dept-filter">
                                <span class="dropdown-arrow">&#8964;</span>
                                <div class="dropdown-suggestions"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <script>
        $(document).ready(async function() {
            try {
                $('#faculty-stock-add-item-button').on('click', async function() {

                    try {
                        await load_add_product_stock_inventory_list_popup();
                    } catch (error) {
                        // get error message
                        const errorMessage = error.message || 'An error occurred while loading the page.';
                        await insert_error_log(errorMessage)
                        await load_error_popup()
                        console.error('An error occurred while loading:', error);
                    }
                });
                $('#faculty-stock-add-room-button').on('click', async function() {

                    try {
                        updateUrl({
                            route: 'faculty',
                            action: 'add',
                            type: 'room'
                        });
                        await load_add_room();
                    } catch (error) {
                        // get error message
                        const errorMessage = error.message || 'An error occurred while loading the page.';
                        await insert_error_log(errorMessage)
                        await load_error_popup()
                        console.error('An error occurred while loading:', error);
                    }
                });
                $('#faculty-stock-view-button').on('click', async function() {

                    try {
                        updateUrl({
                            route: 'faculty',
                            action: 'view',
                        });
                        await load_dept_rooms_list();
                    } catch (error) {
                        // get error message
                        const errorMessage = error.message || 'An error occurred while loading the page.';
                        await insert_error_log(errorMessage)
                        await load_error_popup()
                        console.error('An error occurred while loading:', error);
                    }
                });
                $('.faculty-dept-filter-dummy').on('click focus', function() {
                    fetch_dept_list($(this));
                });
                $('.faculty-dept-filter-dummy').on('blur', function() {
                    //settimeout function
                    setTimeout(() => {
                        if ($("#faculty-dept-filter").val() != '') {
                            fetch_dept_rooms_list($("#faculty-dept-filter").val());
                        }
                    }, 150);

                });
            } catch (error) {
                console.error('An error occurred while loading:', error);
            }
        });
        callAction($("#action"));
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>