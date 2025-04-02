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
    <div class="curvy-table-container mt-6 flex-column">
        <div class="class-student-assignment m-6 flex-container justify-center">
            <p class="action-text">
                Do you want to assign students <span class="highlight" id="manual-student-assignment">Manually</span> or <span class="highlight" id="auto-student-assignment">Automatically</span>?
            </p>
            <div class="action-hint">
                *"As Dumbledore would say, 'It is our choices, Harry, that show what we truly are, far more than our abilities.' Choose wisely!"*
            </div>
        </div>

    </div>

    <script>
        $(document).ready(async function() {

            try {
                showComponentLoading();

                $('#manual-student-assignment').on('click', async function() {
                    try {
                        showComponentLoading();

                        await updateUrl({
                            route: 'faculty',
                            action: 'edit',
                            type: 'student_allocation',
                            tab: 'manual'
                        })
                        await load_faculty_classes_components()
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

                })

                $('#auto-student-assignment').on('click', async function() {
                    try {
                        showComponentLoading();

                        await updateUrl({
                            route: 'faculty',
                            action: 'edit',
                            type: 'student_allocation',
                            tab: 'auto'
                        })
                        await load_faculty_classes_components()
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