<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);

?>
    <div class="tabs mt-6">
        <div class="tab" id="committee-tab">Committees</div>
        <div class="tab" id="representatives-tab">Representatives</div>
    </div>

    <section id="role-edit-section"></section>

    <script>
        //document.ready function
        $(document).ready(async function() {
            try {
                showComponentLoading()
                const urlParams = new URLSearchParams(window.location.search);
                const type = urlParams.get('type');
                const action = urlParams.get('action');
                if (type == 'representatives') {

                    await fetch_faculty_student_representatives('edit', dept_id, 0)
                    $('#representatives-tab').addClass('active')
                    $('#committee-tab').removeClass('active')

                } else {

                    const params = {
                        action: action,
                        route: 'student',
                        type: 'committees'
                    };
                    // Construct the new URL with query parameters
                    const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}`;
                    const newUrl = window.location.origin + window.location.pathname + queryString;
                    // Use pushState to set the new URL and pass params as the state object
                    window.history.pushState(params, '', newUrl);
                    await fetch_student_commitee_list('edit', dept_id);

                    $('#representatives-tab').removeClass('active')
                    $('#committee-tab').addClass('active')
                }
                tabs_active()

                $('#representatives-tab').on('click', async function() {
                    showComponentLoading()
                    const urlParams = new URLSearchParams(window.location.search);
                    const action = urlParams.get('action');
                    const params = {
                        action: action,
                        route: 'student',
                        type: 'representatives'
                    };

                    // Construct the new URL with query parameters
                    const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}`;
                    const newUrl = window.location.origin + window.location.pathname + queryString;
                    // Use pushState to set the new URL and pass params as the state object
                    window.history.pushState(params, '', newUrl);

                    await fetch_faculty_student_representatives('edit', dept_id, 0)
                    setTimeout(function() {
                        hideComponentLoading(); // Delay hiding loading by 1 second
                    }, 100)
                });
                // id="committee-tab" on click
                $('#committee-tab').on('click', async function() {
                    showComponentLoading()
                    const urlParams = new URLSearchParams(window.location.search);
                    const action = urlParams.get('action');
                    const params = {
                        action: action,
                        route: 'student',
                        type: 'committees'
                    };

                    // Construct the new URL with query parameters
                    const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}`;
                    const newUrl = window.location.origin + window.location.pathname + queryString;
                    // Use pushState to set the new URL and pass params as the state object
                    window.history.pushState(params, '', newUrl);
                    await fetch_student_commitee_list('edit', dept_id);
                    setTimeout(function() {
                        hideComponentLoading(); // Delay hiding loading by 1 second
                    }, 100)
                });
            } catch (error) {
                console.error('An error occurred while loading:', error)
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