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
                        <?php if (!in_array($logged_role_id, $secondary_roles)) { ?>
                            <button class="outline bg-card-edit-button" id="faculty-add-group-button">Add Group</button>
                        <?php }  ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12" id="breadcrumbs"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();
                $('#faculty-add-group-button').on('click', function() {
                    load_edit_student_group_popup();
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