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
                        <?php if (in_array($logged_role_id, $main_roles)) { ?>
                            <button class="outline bg-card-edit-button" id="faculty-roles-edit-button">Edit</button>
                        <?php }  ?>
                        <button class="outline bg-card-view-button" id="faculty-roles-view-button">View</button>
                        <button class="outline bg-card-button">Print</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12" id="breadcrumbs"></div>
                </div>
            </div>
            <hr class="full-width-hr">
            <div class="bg-card-filter">
                <div class="row">
                    <div class="col col-4  col-lg-4 col-md-6 col-sm-6 col-xs-12" id="committee-role-filter">
                        <div class="input-container dropdown-container">
                            <input type="text" class="auto committee-role-filter-dummy dropdown-input" placeholder=" " value="">
                            <label class="input-label">Select The Role</label>
                            <input type="hidden" name="committee_role_filter" class="committee-role-filter">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div>
                    <div class=" col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12" id="committee-dept-filter">
                        <div class="input-container dropdown-container">
                            <input type="text" class="auto committee-dept-filter-dummy dropdown-input" placeholder=" " value="">
                            <label class="input-label">Select The Department</label>
                            <input type="hidden" name="committee_dept_filter" class="committee-dept-filter">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div>
                    <div class=" col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12" id="year-of-study-filter">
                        <div class="input-container dropdown-container">
                            <input type="text" class="auto year-of-study-dummy dropdown-input" placeholder=" " value="">
                            <label class="input-label">Select The Year Of Study</label>
                            <input type="hidden" name="year_of_study_filter" class="year-of-study-filter">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(async function() {
            
            try {
                showComponentLoading()
                await $('#year-of-study-filter').hide();
                // Call the function to update the heading
                await callAction($("#action"));
                if (<?= json_encode($higher_official) ?>.includes(<?= $logged_role_id ?>)) {
                    $('#faculty-roles-edit-button').hide()

                }

                // Dropdown input click/focus event
                $('.committee-role-filter-dummy').on('click focus', async function() {
                    const element = $(this);
                    const suggestions = element.siblings(".dropdown-suggestions");
                    const value = element.siblings(".committee-role-filter");
                    showDropdownLoading(element.siblings(".dropdown-suggestions"))

                    // Call showSuggestions
                    showSuggestions(role_list, suggestions, value, element);
                });

                $('.year-of-study-dummy').on('click', async function() {

                    fetch_year_of_study($(this), $(".committee-dept-filter").val());
                });

                $('.year-of-study-dummy').on('blur', async function() {
                    showComponentLoading()
                    setTimeout(() => {
                        const urlParams = new URLSearchParams(window.location.search);
                        const type = urlParams.get('type'); // e.g., 'add', 'edit'
                        if (type == 'class_advisors') {
                            fetch_class_advisors($('.committee-dept-filter').val(), $('.year-of-study-filter').val(), 'view')

                        } else if (type == 'representatives') {
                            fetch_faculty_student_representatives('view', $('.committee-dept-filter').val(), $('.year-of-study-filter').val())
                        }


                    }, 100);
                    setTimeout(function() {
                        hideComponentLoading(); // Delay hiding loading by 1 second
                    }, 100)
                });
                // Change event for hidden input
                $('.committee-role-filter-dummy').on('blur', async function() {
                    showComponentLoading()
                    //settimeout function
                    const urlParams = new URLSearchParams(window.location.search);
                    const route = urlParams.get('route'); // e.g., 'add', 'edit'
                    setTimeout(() => {
                        if (route == 'faculty') {
                            fetch_view_individual_roles($('.committee-role-filter').val(), <?= $logged_user_id ?>, $('.committee-dept-filter').val());
                        } else if (route == 'student') {
                            fetch_student_commitee_list('view', dept_id, $('.committee-role-filter').val());
                        }
                    }, 100);
                    setTimeout(function() {
                        hideComponentLoading(); // Delay hiding loading by 1 second
                    }, 100)
                });


                $('.committee-dept-filter-dummy').on('click', async function() {
                    fetch_dept_list($(this));
                });

                $('.committee-dept-filter-dummy').on('blur', async function() {
                    showComponentLoading()
                    setTimeout(() => {
                        const urlParams = new URLSearchParams(window.location.search);
                        const type = urlParams.get('type'); // e.g., 'add', 'edit'
                        if (type == 'committees') {
                            fetch_view_individual_roles($('.committee-role-filter').val(), <?= $logged_user_id ?>, $('.committee-dept-filter').val());
                            $('#year-of-study-filter').hide();
                        } else if (type == 'class_advisors') {
                            fetch_class_advisors($('.committee-dept-filter').val(), 0, 'view')
                            if ($('.committee-dept-filter').val()) {
                                $('#year-of-study-filter').show();
                            } else {
                                $('#year-of-study-filter').hide();
                            }

                        } else if (type == 'mentors') {
                            fetch_dept_mentor_details($('.committee-dept-filter').val())
                        } else if (type == 'representatives') {
                            if ($('.committee-dept-filter').val()) {
                                $('#year-of-study-filter').show();
                            } else {
                                $('#year-of-study-filter').hide();
                            }
                            fetch_faculty_student_representatives('view', $('.committee-dept-filter').val(), 0)
                        }


                    }, 100);
                    setTimeout(function() {
                        hideComponentLoading(); // Delay hiding loading by 1 second
                    }, 100)
                });

                $('#faculty-roles-edit-button').on('click', async function(e) {
                    showComponentLoading()
                    e.preventDefault(); // Prevent default button behavior

                    const urlParams = new URLSearchParams(window.location.search);
                    const route = urlParams.get('route');
                    let type = urlParams.get('type'); // e.g., 'add', 'edit'
                    const action = 'edit'
                    if (type == 'authorities') {
                        type = 'committees'
                    }
                    const params = {
                        action: action,
                        route: route,
                        type: type
                    };

                    // Construct the new URL with query parameters
                    const queryString = `?action=${action}&route=${route}&type=${type}`;
                    const newUrl = window.location.origin + window.location.pathname + queryString;

                    // Use pushState to set the new URL and pass params as the state object
                    window.history.pushState(params, '', newUrl);


                    callAction($('#action'));
                    // Manually trigger component rendering based on new URL
                    loadComponentsBasedOnURL();
                    setTimeout(function() {
                        hideComponentLoading(); // Delay hiding loading by 1 second
                    }, 100)
                });
                $('#faculty-roles-view-button').on('click', async function(e) {
                    showComponentLoading()
                    e.preventDefault(); // Prevent default button behavior

                    const urlParams = new URLSearchParams(window.location.search);
                    const route = urlParams.get('route');
                    const type = urlParams.get('type'); // e.g., 'add', 'edit'
                    const action = 'view'
                    const params = {
                        action: action,
                        route: route,
                        type: type
                    };

                    // Construct the new URL with query parameters
                    const queryString = `?action=${action}&route=${route}&type=${type}`;
                    const newUrl = window.location.origin + window.location.pathname + queryString;


                    // Use pushState to set the new URL and pass params as the state object
                    window.history.pushState(params, '', newUrl);
                    callAction($('#action'));
                    // Manually trigger component rendering based on new URL
                    loadComponentsBasedOnURL();
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