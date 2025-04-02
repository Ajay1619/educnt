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
        let sem_duration_id = 0;
        let year_of_study_id = 0;
        let section_id = 0;
        const att_permissions = [{
            "title": "None",
            "value": 1
        }, {
            "title": "Authorized",
            "value": 2
        }, {
            "title": "Unauthorized",
            "value": 3
        }];
        const load_main_components = async () => {
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action');
            const route = urlParams.get('route');
            const type = urlParams.get('type');
            // Get the last part of the URL path
            const pathArray = window.location.pathname.split('/');
            const lastPath = pathArray[pathArray.length - 1];
            const routing_link = lastPath + window.location.search
            fetch_bg_card_title(routing_link);
            switch (route) {
                case "faculty":
                    switch (action) {
                        case "view":
                            await load_subjectwise_attendance_view();
                            $('.bg-card-view-button').hide();
                            $('.bg-card-add-button').show();
                            $('.full-width-hr').show();
                            $('.bg-card-filter').show();
                            break;

                        case "add":
                            load_attendance_entry_form();
                            $('.bg-card-add-button').hide();
                            $('.bg-card-view-button').show();
                            $('.full-width-hr').hide();
                            $('.bg-card-filter').hide();


                            break;
                        default:
                            window.location.href = '<?= htmlspecialchars(BASEPATH . '/not-found', ENT_QUOTES, 'UTF-8') ?>';
                            break;
                    }
                    break;
                default:
                    window.location.href = '<?= htmlspecialchars(BASEPATH . '/not-found', ENT_QUOTES, 'UTF-8') ?>';
                    break;
            }

        }
        const fetch_bg_card_title = (routing_link) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= GLOBAL_PATH . '/ajax/fetch_bg_card_title.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'routing_link': routing_link
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        const data = response.data;

                        $('#bg-card-title').text(data);
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                    }
                });
            });
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
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_attendance/components/bg-card.php', ENT_QUOTES, 'UTF-8') ?>',

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

        const load_subjectwise_attendance_view = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_attendance/components/student_subjectwise_attendance_view.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {

                        $('#main-components').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        console.error('Error loading top navbar:', message);
                        reject(); // Reject the promise
                    }

                });
            });
        };


        const load_attendance_entry_form = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_attendance/components/student_attendance_entry_form.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('#main-components').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        console.error('Error loading top navbar:', message);
                        reject(); // Reject the promise
                    },

                });
            });
        };

        const load_attendance_edit_form = (faculty_subjects_id, attendance_date, selected_attendance_slot) => {
            showComponentLoading();
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_attendance/components/student_attendance_edit_form.php', ENT_QUOTES, 'UTF-8') ?>',
                    data: {
                        'faculty_subjects_id': faculty_subjects_id,
                        'attendance_date': attendance_date,
                        'selected_attendance_slot': selected_attendance_slot
                    },
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: async function(response) {
                        await updateUrl({
                            route: 'faculty',
                            action: 'add'
                        });
                        await $('#main-components').html(response);
                        hideComponentLoading();
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        console.error('Error loading top navbar:', message);
                        reject(); // Reject the promise
                    },

                });
            });
        };
        const fetch_dept_list = (element) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/fetch_department_list.php' ?>',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const dept_list = response.data;
                            const suggestions = element.siblings(".dropdown-suggestions")
                            const value = element.siblings(".att-dept-filter")
                            showSuggestions(dept_list, suggestions, value, element);
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
        const fetch_individual_faculty_subject = (element, dept_id = 0) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/individual_allocated_subject_list.php' ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'dept_id': dept_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            faculty_subject_list = response.data;
                            const subject_list = response.dropdown_data;
                            const suggestions = element.siblings(".dropdown-suggestions")
                            const value = element.siblings(".selected-attendance-subject-filter")
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

        const fetch_faculty_selected_slots_for_day = (element, date, subject_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/json/fetch_faculty_selected_slots_for_day.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'date': date,
                        'subject_id': subject_id
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        if (response.code === 200) {
                            const slots_list = response.data;
                            const suggestions = element.siblings(".dropdown-suggestions")
                            const value = element.siblings(".selected-attendance-slots")
                            showSuggestions(slots_list, suggestions, value, element);
                        } else if (response.code === 300) {
                            showToast(response.status, response.message)
                        }

                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise

                    },
                });
            });
        }

        const fetch_student_group_list = (element, sem_duration_id, year_of_study_id, section_id) => {
            showDropdownLoading(element);
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= GLOBAL_PATH . '/json/fetch_student_group_list.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'location': window.location.href,
                        'sem_duration_id': sem_duration_id,
                        'year_of_study_id': year_of_study_id,
                        'section_id': section_id

                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        if (response.code == 200) {
                            let student_group_list = response.data;
                            const suggestions = element.siblings(".dropdown-suggestions");
                            const value = element.siblings(".selected-attendance-group");
                            const student_groups = getChipsValues($('#selected-attendance-group-list-chips'))
                            student_group_list = student_group_list.filter(group => !student_groups.includes(group.title));
                            // Pass the filtered list to showSuggestions
                            showSuggestions(student_group_list, suggestions, value, element);
                        } else {
                            showToast(response.status, response.message)
                        }
                        resolve();
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                    }
                });
            });
        }

        const fetch_student_name_list = (year_of_study_id, section_id, group_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_attendance/json/fetch_attendance_student_name_list.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'year_of_study_id': year_of_study_id,
                        'section_id': section_id,
                        'group_id': group_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const data = response.data;
                            const table = response.table;
                            $("#attendance-student-list").html(table)
                            $('.selected-permission-dummy').on('click', function() {
                                const element = $(this);
                                const suggestions = element.siblings(".dropdown-suggestions")
                                const value = element.siblings(".student-attendance-permission")
                                showSuggestions(att_permissions, suggestions, value, element);
                            });
                            resolve();
                        } else {
                            showToast(response.status, response.message);
                            $('#attendance-student-list').html(`
                                <img class="action-image" src="<?= GLOBAL_PATH . '/images/svgs/student-attendance-action-image.svg' ?>" alt="">
                                <p class="action-text">
                                    Select Student's Group To Record The Attendance.
                                </p>
                                <div class="action-hint">
                                    *Every student’s success begins with presence. Inspire, engage, and make every moment count!*
                                </div>
                            `);
                            resolve();

                        }
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const submit_form_student_attendance_entry_form = (data) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_attendance/ajax/submit_form_student_attendance_entry_form.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'data': data,
                        'group_ids': getChipsId($('#selected-attendance-group-list-chips'))
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            showToast(response.status, response.message);
                            updateUrl({
                                route: 'faculty',
                                action: 'view'
                            });
                            location.reload();
                            resolve();
                        } else {
                            showToast(response.status, response.message);
                            resolve();

                        }
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const delete_subject_attendance = async (faculty_subjects_id, date, period_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_attendance/ajax/delete_individual_subject_attendance.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'faculty_subjects_id': faculty_subjects_id,
                        'attendance_date': date,
                        'selected_attendance_slot': period_id
                    },
                    success: async function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            showComponentLoading()
                            showToast(response.status, response.message);
                            await load_subjectwise_attendance_table($('#selected-attendance-subject-filter').val());
                            setTimeout(function() {
                                hideComponentLoading(); // Delay hiding loading by 1 second
                            }, 100)
                        } else {
                            showToast(response.status, response.message);

                        }
                        resolve();

                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const load_individual_student_subjectwise_attendance_view_table = async (faculty_subjects_id, date, period_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_attendance/json/fetch_individual_student_subjectwise_attendance_view_table.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'faculty_subjects_id': faculty_subjects_id,
                        'attendance_date': date,
                        'selected_attendance_slot': period_id
                    },
                    success: async function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            $('#subjectwise-attendance-table').DataTable().destroy()
                            $('#subjectwise-attendance-table').hide();
                            $('#individual-attendance-table').html(response.table);

                        } else {
                            showToast(response.status, response.message);
                        }
                        resolve();

                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const load_individual_student_subjectwise_attendance_edit_table = async (faculty_subjects_id, date, period_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_attendance/json/fetch_individual_student_subjectwise_attendance_edit_table.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'faculty_subjects_id': faculty_subjects_id,
                        'attendance_date': date,
                        'selected_attendance_slot': period_id
                    },
                    success: async function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            $('#form_student_attendance_edit_form').html(response.table);
                            $('.selected-permission-dummy').on('click', function() {
                                const element = $(this);
                                const suggestions = element.siblings(".dropdown-suggestions")
                                const value = element.siblings(".student-attendance-permission")
                                showSuggestions(att_permissions, suggestions, value, element);
                            });
                        } else {
                            showToast(response.status, response.message);
                        }
                        resolve();

                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const load_subjectwise_attendance_table = (faculty_subject_id) => {
            $('.action-box-content').empty();
            $('#individual-attendance-table').empty();
            $(".portal-table-wrapper").show();
            $('#subjectwise-attendance-table').show();

            $('#subjectwise-attendance-table').DataTable().destroy()
            $('#subjectwise-attendance-table').DataTable({
                "serverSide": true,
                "ajax": {
                    "url": "<?= MODULES . '/faculty_student_attendance/json/subjectwise_attendance_table.php' ?>",
                    "type": "POST",
                    "headers": {
                        "X-CSRF-TOKEN": '<?= $csrf_token; ?>',
                        "X-Requested-Path": window.location.pathname + window.location.search
                    },
                    "data": {
                        "faculty_subject_id": faculty_subject_id,
                        "type": 1
                    }
                },
                "columns": [{
                        "data": "s_no",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "attendance_date"
                    },
                    {
                        "data": "period",
                        "width": "25%"
                    },
                    {
                        "data": "present_count"
                    },
                    {
                        "data": "absent_count"
                    },
                    {
                        "data": "percentage"
                    },
                    {
                        "data": "action",
                        "orderable": false,
                        "searchable": false
                    }
                ],
                "scrollX": true,
                "language": {
                    "emptyTable": no_data_html,
                    "loadingRecords": table_loading
                },
            });
            $('.dt-layout-row .dt-layout-table').css('width', '100%');
            $('.dt-layout-table .dt-layout-cell').css('width', '100%');
            $('.dt-scroll-headInner').css('width', '100%');
            $('.dataTable').css('width', '100%');
        }
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
