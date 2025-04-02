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
?>
    <!-- Confirm student Allocation Verification Popup Overlay -->
    <div class="popup-overlay">
        <!-- Alert Popup Container -->
        <div class="alert-popup" id="add-product">
            <!-- Close Button -->
            <button class="popup-close-btn">×</button>

            <!-- Popup Header -->
            <div class="popup-header">
                Add Product
            </div>

            <form id="add-product-dept-room-form" method="post">
                <!-- Popup Content -->
                <div class="popup-content">
                    <div class="row">
                        <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="input-container ">
                                <input type="text" id="item-name" name="item_name" placeholder=" " required>
                                <label class="input-label" for="item-name">Enter The Item Name</label>
                            </div>
                        </div>
                        <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="input-container ">
                                <input type="text" id="unit-of-measure" name="unit_of_measure" placeholder=" " required>
                                <label class="input-label" for="unit-of-measure">Enter The Unit Of Measure</label>
                            </div>
                        </div>
                        <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="input-container ">
                                <input type="text" id="item-quantity" name="item_quantity" placeholder=" " required>
                                <label class="input-label" for="item-quantity">Enter The Quantity Of Item</label>
                            </div>
                        </div>
                        <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="input-container ">
                                <input type="text" id="item-note" name="item_note" placeholder=" " required>
                                <label class="input-label" for="item-note">Enter Any Note For The Item</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Popup Footer -->
                <div class="popup-footer">
                    <button type="submit" class="btn-success">Submit</button>
                    <button type="button" class="btn-error deny-button">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();
                await callAction()
                $('#add-product-dept-room-form').on('submit', async function(e) {
                    e.preventDefault();
                    try {
                        const data = $(this).serialize();
                        await add_product_dept_room_form(data)
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
                //class=popup-close-btn on click
                $('.popup-close-btn, .deny-button').on('click', function() {
                    $('.popup-overlay').remove();
                })
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