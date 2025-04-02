<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
?>

    <table>
        <thead>
            <tr>
                <th>SL.NO</th>
                <th>Student Name</th>
                <th>Register Number</th>
                <th>Category</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>John Doe</td>
                <td>123456</td>
                <td>General</td>
                <td>₹ 100</td>
                <td> <span class="alert alert-success">Paid</span> </td>
                <td>
                    <img class="action-button" src="<?= GLOBAL_PATH . '/images/svgs/confirm_success_icon.svg' ?>" alt="">
                    <img class="action-button" src="<?= GLOBAL_PATH . '/images/svgs/confirm_error_icon.svg' ?>" alt="">
                    <img class="action-button" src="<?= GLOBAL_PATH . '/images/svgs/datatable_edit_icon.svg' ?>" alt="">
                    <img class="action-button" src="<?= GLOBAL_PATH . '/images/svgs/eye.svg' ?>" alt="">
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>Jane Doe</td>
                <td>123457</td>
                <td>General</td>
                <td>₹ 100</td>
                <td><span class="alert alert-warning">Pending</span></td>
                <td>
                    <img class="action-button" src="<?= GLOBAL_PATH . '/images/svgs/confirm_success_icon.svg' ?>" alt="">
                    <img class="action-button" src="<?= GLOBAL_PATH . '/images/svgs/confirm_error_icon.svg' ?>" alt="">
                    <img class="action-button" src="<?= GLOBAL_PATH . '/images/svgs/datatable_edit_icon.svg' ?>" alt="">
                    <img class="action-button" src="<?= GLOBAL_PATH . '/images/svgs/eye.svg' ?>" alt="">
                </td>
            </tr>
            <tr>
                <td>3</td>
                <td>John Doe</td>
                <td>123456</td>
                <td>General</td>
                <td>₹ 100</td>
                <td><span class="alert alert-success">Paid</span></td>
                <td>
                    <img class="action-button" src="<?= GLOBAL_PATH . '/images/svgs/confirm_success_icon.svg' ?>" alt="">
                    <img class="action-button" src="<?= GLOBAL_PATH . '/images/svgs/confirm_error_icon.svg' ?>" alt="">
                    <img class="action-button" src="<?= GLOBAL_PATH . '/images/svgs/datatable_edit_icon.svg' ?>" alt="">
                    <img class="action-button" src="<?= GLOBAL_PATH . '/images/svgs/eye.svg' ?>" alt="">
                </td>
            </tr>
            <tr>
                <td>4</td>
                <td>Jane Doe</td>
                <td>123457</td>
                <td>General</td>
                <td>₹ 100</td>
                <td><span class="alert alert-error">Unpaid</span></td>
                <td>
                    <img class="action-button" src="<?= GLOBAL_PATH . '/images/svgs/confirm_success_icon.svg' ?>" alt="">
                    <img class="action-button" src="<?= GLOBAL_PATH . '/images/svgs/confirm_error_icon.svg' ?>" alt="">
                    <img class="action-button" src="<?= GLOBAL_PATH . '/images/svgs/datatable_edit_icon.svg' ?>" alt="">
                    <img class="action-button" src="<?= GLOBAL_PATH . '/images/svgs/eye.svg' ?>" alt="">
                </td>
            </tr>
        </tbody>
    </table>

    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();


            } catch (error) {
                // Get error message
                const errorMessage = error.message || 'An error occurred while loading the page.';
                await insert_error_log(errorMessage);
                await load_error_popup();
                console.error('An error occurred while loading:', error);
            } finally {
                // Hide the loading screen once all operations are complete
                setTimeout(function() {
                    hideComponentLoading(); // Delay hiding loading by 1 second
                }, 100);
            }
        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.'], JSON_THROW_ON_ERROR);
    exit;
}
?>