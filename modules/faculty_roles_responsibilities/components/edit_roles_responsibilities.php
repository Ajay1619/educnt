<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    isset($_SERVER['HTTP_X_REQUESTED_PATH']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);

?>
    <div class="tabs mt-6">
        <div class="tab active" id="authorities-tab">Authorities</div>
        <div class="tab" id="committee-tab">Committees</div>
        <div class="tab" id="class-advisors-tab">Class Advisors</div>
        <div class="tab" id="mentors-tab">Mentors</div>
    </div>

    <section id="role-edit-section"></section>

    <script>
        //document.ready function
        $(document).ready(async function() {
            try {
                showComponentLoading()

                if (!<?= json_encode($admin_roles) ?>.includes(<?= $logged_role_id ?>)) {
                    $('#authorities-tab').hide()

                }else{

                }
                const urlParams = new URLSearchParams(window.location.search);
                const type = urlParams.get('type');
                if (type == 'committees') {

                    await fetch_commitee_list();
                    $('#authorities-tab').removeClass("active")
                    $('#committee-tab').addClass("active")
                    $('#class-advisors-tab').removeClass("active")
                    $('#mentors-tab').removeClass("active")
                } else if (type == 'class_advisors') {

                    await fetch_class_advisors(dept_id, 0, 'edit')
                    $('#authorities-tab').removeClass("active")
                    $('#committee-tab').removeClass("active")
                    $('#class-advisors-tab').addClass("active")
                    $('#mentors-tab').removeClass("active")
                } else if (type == 'mentors') {
                    await load_mentor_edit()
                    $("#role-view-section").empty()
                    $('#authorities-tab').removeClass("active")
                    $('#committee-tab').removeClass("active")
                    $('#class-advisors-tab').removeClass("active")
                    $('#mentors-tab').addClass("active")
                } else {
                    if (<?= json_encode($primary_roles) ?>.includes(<?= $logged_role_id ?>)) {

                        const urlParams = new URLSearchParams(window.location.search);
                        const action = urlParams.get('action');
                        const params = {
                            action: action,
                            route: 'faculty',
                            type: 'authorities'
                        };

                        // Construct the new URL with query parameters
                        const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}`;
                        const newUrl = window.location.origin + window.location.pathname + queryString;
                        // Use pushState to set the new URL and pass params as the state object
                        window.history.pushState(params, '', newUrl);
                        await fetch_faculty_authorities('edit')
                        $('#authorities-tab').addClass("active")
                        $('#committee-tab').removeClass("active")
                        $('#class-advisors-tab').removeClass("active")
                        $('#mentors-tab').removeClass("active")
                    } else {
                        const urlParams = new URLSearchParams(window.location.search);
                        const action = urlParams.get('action');
                        const params = {
                            action: action,
                            route: 'faculty',
                            type: 'committees'
                        };

                        // Construct the new URL with query parameters
                        const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}`;
                        const newUrl = window.location.origin + window.location.pathname + queryString;
                        // Use pushState to set the new URL and pass params as the state object
                        window.history.pushState(params, '', newUrl);
                        await fetch_commitee_list();
                        $('#authorities-tab').removeClass("active")
                        $('#committee-tab').addClass("active")
                        $('#class-advisors-tab').removeClass("active")
                        $('#mentors-tab').removeClass("active")
                    }
                }
                await tabs_active()

                setTimeout(function() {
                    hideComponentLoading();
                }, 100)
                // id="authorities-tab" on click
                $('#authorities-tab').on('click', async function() {
                    showComponentLoading()
                    const urlParams = new URLSearchParams(window.location.search);
                    const action = urlParams.get('action');
                    const params = {
                        action: action,
                        route: 'faculty',
                        type: 'authorities'
                    };

                    // Construct the new URL with query parameters
                    const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}`;
                    const newUrl = window.location.origin + window.location.pathname + queryString;
                    // Use pushState to set the new URL and pass params as the state object
                    window.history.pushState(params, '', newUrl);

                    await fetch_faculty_authorities('edit')
                    setTimeout(function() {
                        hideComponentLoading();
                    }, 100)
                });
                // id="committee-tab" on click
                $('#committee-tab').on('click', async function() {
                    showComponentLoading()
                    const urlParams = new URLSearchParams(window.location.search);
                    const action = urlParams.get('action');
                    const params = {
                        action: action,
                        route: 'faculty',
                        type: 'committees'
                    };

                    // Construct the new URL with query parameters
                    const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}`;
                    const newUrl = window.location.origin + window.location.pathname + queryString;
                    // Use pushState to set the new URL and pass params as the state object
                    window.history.pushState(params, '', newUrl);
                    await fetch_commitee_list();
                    setTimeout(function() {
                        hideComponentLoading();
                    }, 100)
                });
                // id="class-advisors-tab" on click
                $('#class-advisors-tab').on('click', async function() {
                    showComponentLoading()
                    const urlParams = new URLSearchParams(window.location.search);
                    const action = urlParams.get('action');
                    const params = {
                        action: action,
                        route: 'faculty',
                        type: 'class_advisors'
                    };

                    // Construct the new URL with query parameters
                    const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}`;
                    const newUrl = window.location.origin + window.location.pathname + queryString;
                    // Use pushState to set the new URL and pass params as the state object
                    window.history.pushState(params, '', newUrl);
                    $("#role-view-section").empty()
                    await fetch_class_advisors(dept_id, 0, 'edit')
                    setTimeout(function() {
                        hideComponentLoading();
                    }, 100)

                });
                // id="mentors-tab" on click
                $('#mentors-tab').on('click', async function() {
                    showComponentLoading()
                    const urlParams = new URLSearchParams(window.location.search);
                    const action = urlParams.get('action');
                    const params = {
                        action: action,
                        route: 'faculty',
                        type: 'mentors'
                    };

                    // Construct the new URL with query parameters
                    const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}`;
                    const newUrl = window.location.origin + window.location.pathname + queryString;
                    // Use pushState to set the new URL and pass params as the state object
                    window.history.pushState(params, '', newUrl);
                    $("#role-view-section").empty()
                    await load_mentor_edit()
                    setTimeout(function() {
                        hideComponentLoading();
                    }, 100)
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