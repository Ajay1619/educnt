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
    $item_id = isset($_POST['item_id']) ? sanitizeInput($_POST['item_id'], 'string') : '';
?>
    <!-- Confirm student Allocation Verification Popup Overlay -->
    <div class="popup-overlay">
        <!-- Alert Popup Container -->
        <div class="alert-popup" id="add-product">
            <!-- Close Button -->
            <button class="popup-close-btn">×</button>

            <!-- Popup Header -->
            <div class="popup-header">
                Edit Product
            </div>

            <form id="edit-product-dept-room-form" method="post"></form>
        </div>
    </div>

    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();
                await callAction()
                await fetch_individual_edit_room_item_details('<?= $item_id ?>');

                $('#edit-product-dept-room-form').on('submit', async function(e) {
                    e.preventDefault();
                    try {
                        const data = $(this).serialize();
                        await edit_product_dept_room_form(data)
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