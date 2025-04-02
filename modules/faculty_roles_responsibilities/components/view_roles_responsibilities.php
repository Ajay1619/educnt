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
        <div class="tab" id="authorities-tab">Authorities</div>
        <div class="tab" id="committee-tab">Committees</div>
        <div class="tab" id="class-advisors-tab">Class Advisors</div>
        <div class="tab" id="mentors-tab">Mentors</div>
    </div>

    <div class="main-content-card">
        <section id="role-view-section" class="p-4"></section>
    </div>

    <script>
        //document.ready function
        $(document).ready(async function() {
            try {

                showComponentLoading();
                const urlParams = new URLSearchParams(window.location.search);
                const action = urlParams.get('action');
                const route = urlParams.get('route');
                const type = urlParams.get('type');

                if (type == 'committees') {
                    showComponentLoading()
                    await fetch_view_individual_roles(0, <?= $logged_user_id ?>, dept_id);
                    await load_filter(route, type)


                    $('#authorities-tab').removeClass('active')
                    $('#committee-tab').addClass('active')
                    $('#class-advisors-tab').removeClass('active')
                    $('#mentors-tab').removeClass('active')
                    hideComponentLoading();
                } else if (type == 'class_advisors') {
                    await fetch_class_advisors(dept_id, 0, 'view')
                    $('#authorities-tab').removeClass('active')
                    $('#committee-tab').removeClass('active')
                    $('#class-advisors-tab').addClass('active')
                    $('#mentors-tab').removeClass('active')
                } else if (type == 'mentors') {
                    if (<?= json_encode($main_roles) ?>.includes(logged_role_id)) {
                        await fetch_dept_mentor_details(dept_id)
                    } else {
                        await load_faculty_student_mentor(<?= $logged_user_id ?>, dept_id);
                    }

                    await load_filter(route, type)
                    $('#authorities-tab').removeClass('active')
                    $('#committee-tab').removeClass('active')
                    $('#class-advisors-tab').removeClass('active')
                    $('#mentors-tab').addClass('active')
                } else {
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
                    await fetch_faculty_authorities(action);
                    await load_filter(route, type)
                    $('#authorities-tab').addClass('active')
                    $('#committee-tab').removeClass('active')
                    $('#class-advisors-tab').removeClass('active')
                    $('#mentors-tab').removeClass('active')
                }

                await tabs_active()
                await load_filter(route, type)
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

                    await fetch_faculty_authorities(action);
                    await load_filter(params.route, params.type)
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
                    await fetch_view_individual_roles(0, <?= $logged_user_id ?>, dept_id);
                    await load_filter(params.route, params.type)
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
                    await fetch_class_advisors(dept_id, 0, 'view')
                    await load_filter(params.route, params.type)
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
                    if (<?= json_encode($main_roles) ?>.includes(logged_role_id)) {
                        await fetch_dept_mentor_details(dept_id)
                    } else {
                        await load_faculty_student_mentor(<?= $logged_user_id ?>, dept_id);
                    }
                    await load_filter(params.route, params.type)
                    setTimeout(function() {
                        hideComponentLoading();
                    }, 100)

                });
            } catch (error) {
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