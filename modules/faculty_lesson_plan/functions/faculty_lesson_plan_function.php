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

?>
    <script>
        const callAction = (element) => {
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action');
            const route = urlParams.get('route');
            const type = urlParams.get('type');

            switch (route) {
                case "faculty":
                    switch (action) {
                        case "view":
                            switch (type) {
                                case null:
                                    load_view_lesson_plan_list();
                                    break;
                                default:
                                    console.warn("Unexpected type:", type);
                            }
                            $('.bg-card-edit-button').show();
                            $('.bg-card-view-button').hide();
                            $('.bg-card-add-button').show();
                            $('.bg-card-filter').show();
                            $('.full-width-hr').show();
                            break;
                        case "edit":
                            switch (type) {
                                case null:
                                    load_edit_lesson_plan_list();
                                    break;
                                default:
                                    console.warn("Unexpected type:", type);
                            }
                            $('.bg-card-edit-button').show();
                            $('.bg-card-view-button').hide();
                            $('.bg-card-add-button').show();
                            $('.bg-card-filter').show();
                            $('.full-width-hr').show();
                            break;
                        // case "edit":
                        //     switch (type) {
                        //         case "lesson-plan":
                        //             load_lesson_plan_list_popup();
                        //             break;
                        //         default:
                        //             console.warn("Unexpected type:", type);
                        //     }
                        //     $('.bg-card-edit-button').show();
                        //     $('.bg-card-view-button').hide();
                        //     $('.bg-card-add-button').show();
                        //     $('.bg-card-filter').show();
                        //     $('.full-width-hr').show();
                        //     break;

                        case "add":
                            switch (type) {
                                case null:
                                    load_add_lesson_plan_list();
                                    break;
                                default:
                                    console.warn("Unexpected type:", type);
                            }
                            $('.bg-card-add-button').hide();
                            $('.bg-card-edit-button').show();
                            $('.bg-card-view-button').show();
                            $('.bg-card-filter').show();
                            $('.full-width-hr').show();
                            break;

                        default:
                            console.warn("Unexpected action:", action);
                            // ✅ Calls load_view_lesson_plan_list() in default action
                            load_view_lesson_plan_list();
                            $('.bg-card-edit-button').show();
                            $('.bg-card-view-button').hide();
                            $('.bg-card-add-button').show();
                            $('.bg-card-filter').show();
                            $('.full-width-hr').show();
                    }
                    break;

                default:
                    console.warn("Unexpected route:", route);
                    load_view_lesson_plan_list();

                    $('.bg-card-add-button').hide();
                    $('.bg-card-edit-button').hide();
                    $('.bg-card-view-button').hide();
                    $('.bg-card-filter').hide();
                    $('.full-width-hr').hide();
            }
        }
        const loadSidebar = () => {

            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/sidebar.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {

                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#sidebar').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status === 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },
                });
            });
        };


        const fetch_Subject = (element, subid, date) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/individual_allocated_subject_list.php' ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },

                    success: function(response) {

                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const subject_list = response.dropdown_data;

                            const list = response.data;
                            const suggestions = element.siblings(".dropdown-suggestions")
                            const value = element.siblings(".subject-filter")


                            showSuggestions(subject_list, suggestions, value, element);
                            $('.subject-dummy').on('blur', function() {
                                //settimeout function
                                setTimeout(() => {
                                    const userInputValue = $('.subject-filter').val();
                                    console.log(userInputValue);
                                    const filteredList = list.filter(item => item.faculty_subjects_id == userInputValue);
                                    $("#class-manager-year-of-study").text(filteredList[0]['year_of_study_title'])
                                    $("#class-manager-academic-year").text(filteredList[0]['academic_year_title'])
                                    $("#class-manager-Department").text(filteredList[0]['dept_short_name'])
                                    $("#class-manager-Section").text(filteredList[0]['section_title'])
                                    $("#subject_id").val(filteredList[0]['subject_id'])
                                    $("#sem_duriation_id").val(filteredList[0]['sem_duration_id'])

                                    console.log(filteredList);


                                }, 150);

                                // Log the value to the console

                            });

                        } else {
                            showToast(response.status, response.message)
                        }
                        resolve(response);
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        }


        const fetch_slot = (element, subid, date) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/fetch_faculty_selected_slots_for_day.php' ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        "subject_id": subid,
                        "date": date,
                    },
                    success: function(response) {

                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const subject_list = response.data;

                            const slots_list = response.data;
                            console.log(slots_list);
                            // const suggestions = element.siblings(".dropdown-suggestions")
                            // const value = element.siblings(".subject-filter")
                            const suggestions = element.siblings(".dropdown-suggestions")
                            const value = element.siblings(".selected-lesson-slots")
                            showSuggestions(slots_list, suggestions, value, element);

                            // showSuggestions(subject_list, suggestions, value, element);


                        } else {
                            showToast(response.status, response.message)
                        }
                        resolve(response);
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        }


        const loadTopbar = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/topbar.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {

                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#topbar').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status === 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },
                });
            });
        };
        const loadBgCard = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_lesson_plan/components/bg-card.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {

                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#bg_card').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status === 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },
                });
            });
        };
        const loadBreadcrumbs = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/breadcrumbs.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    success: function(response) {
                        $('#breadcrumbs').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },
                });
            });
        };
        const loadFooter = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/footer.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#footer').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },

                });
            });
        };
        const load_add_lesson_plan_list = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_lesson_plan/components/faculty_add_lesson_plan.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#add-lesson-plan').html(response);
                        $('#lesson-plan-list').empty();

                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },

                });
            });
        };
        const load_edit_lesson_plan_list = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_lesson_plan/components/faculty_edit_lesson_plan.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#add-lesson-plan').html(response);
                        $('#lesson-plan-list').empty();

                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },

                });
            });
        };
        const load_view_lesson_plan_list = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_lesson_plan/components/faculty_view_lesson_plan.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#add-lesson-plan').html(response);
                        $('#lesson-plan-list').empty();

                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },

                });
            });
        };
        const load_lesson_plan_list_popup = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_lesson_plan/components/faculty_list_of_lesson_plan_popup.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
                    },
                    success: function(response) {
                        // Inject popup content into the placeholder
                        $('#lesson-plan-list-popup-view').html(response);

                        // Show the popup and bind the close button
                        $('#lesson-plan-list-popup-view').show();
                        $('.popup-close-btn').on('click', function() {
                            $('#lesson-plan-list-popup-view').hide().html('');
                        });
                        resolve();
                    },
                    error: function(jqXHR) {
                        const message =
                            jqXHR.status === 401 ?
                            'Unauthorized access. Please check your credentials.' :
                            'An error occurred. Please try again.';
                        showToast('error', message);
                        reject();
                    }
                });
            });
        };

        const fetch_individual_faculty_subject = (element) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/individual_allocated_subject_list.php' ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            faculty_subject_list = response.data;
                            const subject_list = response.dropdown_data;
                            const suggestions = element.siblings(".dropdown-suggestions")
                            const value = element.siblings(".selected-lesson-plan-subject-filter")
                            showSuggestions(subject_list, suggestions, value, element);
                        } else {
                            showToast(response.status, response.message)
                        }
                        resolve(response);
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        }
        const load_subjectwise_attendance_table = (faculty_subject_id) => {
            $('.action-box-content').empty();
            $('#lessonPlanForm').show();
            // $(".portal-table-wrapper").show();
            $('#topics-table').show();
        }
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
