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
    <script>
        let selectedSubject = 0;
        let individual_faculty_subjects = [];
        let selected_slots_list_chips = [];
        let route = '';
        let action = '';
        let type = ''; // e.g., 'add', 'edit'

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
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
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
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
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
                    url: '<?= htmlspecialchars(MODULES . '/faculty_class_schedule/components/class_schedule_bg_card.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    success: function(response) {
                        $('#class-schedule-bg-card').html(response);
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
        // Function to update the heading based on the action and last part of the URL path
        const callAction = (element = "") => {
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action'); // e.g., 'add', 'edit'

            // Get the last part of the URL path
            const pathArray = window.location.pathname.split('/');
            const lastPath = pathArray[pathArray.length - 1];
            const routing_link = lastPath + window.location.search
            fetch_bg_card_title(routing_link);
            // Capitalize the first letter of the action and format the title
            let title = "";
            if (action) {
                if (action == 'view') {
                    $('.bg-card-edit-button').hide();
                    $('.bg-card-view-button').hide();
                    <?php if (in_array($logged_role_id, $tertiary_roles)) { ?>
                        $('.bg-card-add-button').show();
                    <?php } else { ?>
                        $('.bg-card-add-button').hide();
                    <?php } ?>

                    $('.bg-card-pdf-button').show();
                    $('.full-width-hr').show();
                    <?php if (in_array($logged_role_id, $main_roles)) { ?>
                        $('.bg-card-filter').show();
                    <?php } else { ?>
                        $('.bg-card-filter').hide();
                    <?php } ?>
                } else if (action == 'add') {


                    $('.bg-card-add-button').hide();
                    $('.bg-card-edit-button').hide();
                    $('.bg-card-view-button').show();
                    $('.bg-card-pdf-button').hide();
                }
            }

            // Remove any file extension if present (e.g., '.php' from 'faculty-roles-responsibilities.php')
            const cleanPath = lastPath.split('.')[0];

            title += cleanPath.replace(/-/g, ' '); // Replace hyphens with spaces for readability

            // Update the <h2> tag with the formatted title
            $("bg-card-title").text(title);
        }


        const fetch_day_list = (element, day_type) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/json/fetch_day_list.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    data: {
                        'day_type': day_type
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        day_list = response.data;
                        const suggestions = element.siblings(".dropdown-suggestions")
                        const value = element.siblings(".selected-day")

                        showSuggestions(day_list, suggestions, value, element);
                        resolve(day_list);
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const fetch_dept_slots_list = (element, dept_id, slot_type, day_id, year_of_study_id, section_id, sem_duration_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/json/fetch_slots_list.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    data: {
                        'dept_id': dept_id,
                        'slot_type': slot_type,
                        'day_id': day_id,
                        'year_of_study_id': year_of_study_id,
                        'section_id': section_id,
                        'sem_duration_id': sem_duration_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        let slots_list = response.data;
                        const suggestions = element.siblings(".autocomplete-suggestions")
                        const value = element.siblings(".selected-slots")
                        const slots_chips = getChipsValues($('#selected-slots-list-chips'))
                        slots_list = slots_list.filter(slot => !slots_chips.includes(slot.title));

                        showSuggestions(slots_list, suggestions, value, element);
                        resolve(slots_list);
                    },
                    error: function(error) {
                        reject(error);
                    }
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
                            const value = element.siblings(".dept-filter")
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
        const fetch_year_list = (element, dept_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/fetch_year_of_study.php' ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    data: {
                        'dept_id': dept_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const year_list = response.data;
                            const suggestions = element.siblings(".dropdown-suggestions")
                            const value = element.siblings(".year-of-study-filter")
                            showSuggestions(year_list, suggestions, value, element);
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
        const fetch_section_list = (element, year_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/fetch_section_list.php' ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    data: {
                        'year_id': year_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const section_list = response.data;
                            const suggestions = element.siblings(".dropdown-suggestions")
                            const value = element.siblings(".section-filter")
                            showSuggestions(section_list, suggestions, value, element);
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

        const add_subject_selected_slots = (data) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_class_schedule/ajax/add_subject_selected_slots.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: data,
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            showToast(response.status, response.message);

                            resolve();
                        } else {
                            showToast(response.status, response.message);
                            reject();
                        }
                        resolve();
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const delete_subject_selected_slots = (timetable_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_class_schedule/ajax/delete_subject_selected_slots.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'timetable_id': timetable_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            showToast(response.status, response.message);
                            resolve();
                        } else {
                            showToast(response.status, response.message);
                            reject();
                        }
                        resolve();
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const load_add_faculty_timetable = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_class_schedule/components/faculty_add_timetable.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $("#main-components").html(response)

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


        const load_individual_allocated_subject_list = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= GLOBAL_PATH . '/json/individual_allocated_subject_list.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            individual_faculty_subjects = response.data;
                            populate_subject_list(individual_faculty_subjects);
                        } else {
                            showToast(response.status, response.message);
                        }
                        resolve();
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(jqXHR);
                    }
                });
            });
        };

        const populate_subject_list = (data) => {

            // Clear the current content of the subject list
            $('#subject-list').empty();

            // Loop through each subject in the data array
            data.forEach((subject) => {
                // Extract necessary details
                const subjectCode = subject.subject_code || "N/A";
                const subject_short_name = subject.subject_short_name || "N/A";
                const yearTitle = subject.year_of_study_title || "N/A";
                const sem_duration_id = subject.sem_duration_id || 0;
                const year_of_study_id = subject.year_of_study_id || 0;
                const section_id = subject.section_id || 0;
                const sectionTitle = subject.section_title || "N/A";
                const department = subject.dept_short_name || "N/A";
                const periodsCompleted = subject.subject_no_of_periods || 0;
                const totalPeriods = subject.number_of_hours || 0;
                const subject_id = subject.faculty_subjects_id || 0;
                const dept_id = subject.dept_id || 0;

                // Calculate progress percentage
                const progressPercentage = totalPeriods > 0 ? Math.min((periodsCompleted / totalPeriods) * 100, 100) : 0;

                // Create the HTML for the subject card
                const subjectCard = `
                    <div class="row">
                        <div class="popup-card mx-6 my-4 individual-subject-list" data-sem-duration-id="${sem_duration_id}"  data-section-id="${section_id}" data-year-of-study-id="${year_of_study_id}" data-subject-id="${subject_id}" data-dept-id="${dept_id}">
                            <div class="row align-center">
                                <div class="col col-3 col-lg-3 col-md-3 col-sm-3 col-xs-3 text-left text-color text-xxl">${subject_short_name}</div>
                                <div class="col col-9 col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right">
                                    <p class="text-light">${yearTitle} YEAR</p>
                                    <p class="text-light">${sectionTitle} Section</p>
                                    <p class="text-light">${department}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    `;

                // Append the card to the subject list container
                $('#subject-list').append(subjectCard);
            });


            $('.individual-subject-list').on('click', function() {
                selectedSubject = $(this).data('subject-id');
                selectedDept = $(this).data('dept-id');
                selectedYearOfStudyId = $(this).data('year-of-study-id');
                selectedSection = $(this).data('section-id');
                selectedSemDurationId = $(this).data('sem-duration-id');
                $('.individual-subject-list').removeClass('active'); // Remove active from all
                $(this).addClass('active'); // Add active to clicked subject
                load_faculty_select_subject_time_slot_popup(selectedSubject, selectedDept, selectedYearOfStudyId, selectedSection, selectedSemDurationId)
            });
        }


        const load_individual_allocated_subject_slot_list = (year_of_study_id = 0, section_id = 0) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_class_schedule/json/individual_allocated_subject_slot_list.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'year_of_study_id': year_of_study_id,
                        'section_id': section_id
                    },
                    success: async function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const individual_faculty_slot_subjects = response.data;
                            const days_list = response.days;
                            await populate_subject_slot_list(individual_faculty_slot_subjects, days_list);
                        } else {
                            $("#timetable-schedule").html(
                                `<div class="subject-assignment">
                                    <p class="action-text">
                                        Select a Subject and Schedule Your Time Slot.
                                    </p>
                                    <div class="action-hint">
                                        *Your destiny is in your hands. Schedule wisely, for the future awaits!*
                                    </div>
                                </div>
                                `
                            )

                        }
                        resolve();
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(jqXHR);
                    }
                });
            });
        };

        const populate_subject_slot_list = async (data, days_list) => {
            const $timetable = $("#timetable-schedule");

            // Create the table structure
            let theadHTML = "<thead><tr>";
            days_list.forEach(day => {
                theadHTML += `<th>${day.day_title}</th>`;
            });
            theadHTML += "</tr></thead>";

            let tbodyHTML = "<tbody><tr>";
            days_list.forEach(day => {
                const dayData = data
                    .flatMap(items => Object.values(items)) // Flatten the nested objects
                    .filter(item => item.day_id === day.day_id); // Now filter the flattened array

                if (dayData.length === 0) {
                    // If no data for the day, add a disabled background cell
                    tbodyHTML += `<td class=""></td>`;
                } else {
                    let dayContent = "";
                    dayData.forEach(item => {
                        // Use Object.values() to access the periods
                        const periods = Object.values(item.periods);

                        periods.forEach(period => {
                            dayContent += `
                            <div class="row added-slots-lists">
                                <div class="col col-12 p-0">
                                    <div class="card p-3 my-3 text-center">
                                        ${action == "add" ? `
                                        <span class="delete-icon" data-timetable-id="${period.timetable_id}">
                                            <img src="<?= GLOBAL_PATH . '/images/svgs/application_icons/delete.svg' ?>" alt="Delete">
                                        </span>` : ''}
                                        <h4>${period.subject_short_name}</h4>
                                        <p class="alert alert-info m-0 text-xsm">${period.period_time}</p>
                                    </div>
                                </div>
                            </div>`;
                        });

                    });


                    // Append the content for that day
                    tbodyHTML += `<td>${dayContent}</td>`;
                }
            });
            tbodyHTML += "</tr></tbody>";

            // Append the constructed HTML to the timetable
            $timetable.html(`<table class="portal-table">${theadHTML}${tbodyHTML}</table>`);

            $('.delete-icon').click(async function() {
                showComponentLoading(2);
                await delete_subject_selected_slots($(this).data('timetable-id'))
                setTimeout(async function() {
                    await load_individual_allocated_subject_list();
                    await load_individual_allocated_subject_slot_list();
                    hideComponentLoading();
                }, 100);
            });
        };

        const load_faculty_select_subject_time_slot_popup = (subject_id, dept_id, year_of_study_id, section_id, sem_duration_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_class_schedule/components/faculty_select_subject_time_slot_popup.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'subject_id': subject_id,
                        'dept_id': dept_id,
                        'year_of_study_id': year_of_study_id,
                        'section_id': section_id,
                        'sem_duration_id': sem_duration_id
                    },
                    success: function(response) {
                        $("#class-schedules-popup").html(response)

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

        const fetch_faculty_selected_slots_for_day = (day_id, subject_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_class_schedule/json/fetch_faculty_selected_slots_for_day.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'day_id': day_id,
                        'subject_id': subject_id
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        if (response.code === 200) {

                            const data = response.data
                            data.forEach(slot => {
                                // Use createChip function to create and add the chip
                                createChip($('.selected-slots').val(slot.title), $('#selected-slots-list-chips'), slot.value);
                                $('.selected-slots').val("")
                            });
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

        const load_view_faculty_timetable = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_class_schedule/components/faculty_view_timetable.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $("#main-components").html(response)

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

        const load_individual_view_timetable = (faculty_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_class_schedule/components/individual_view_timetable.php', ENT_QUOTES, 'UTF-8') ?>',
                    data: {
                        'faculty_id': faculty_id
                    },
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $("#schedule").html(response)

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
        }

        const load_dept_view_timetable = (dept_id = 0, year_of_study_id = 0, section_id = 0) => {
            if (dept_id == 0 || year_of_study_id == 0 || section_id == 0) {
                $("#schedule").html(`
                    <img class="action-image" src="<?= GLOBAL_PATH . '/images/svgs/no-class-timetable.svg' ?>" alt="">
                    <p class="action-text">
                        It looks like no classes are assigned for this department yet.  <br>
                        Please refine your search by selecting a Year of Study and Section.
                    </p>
                    <div class="action-hint">
                        *Knowledge grows when you seek it—give it another try!*
                    </div>  
                `);
            } else {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'POST',
                        url: '<?= htmlspecialchars(MODULES . '/faculty_class_schedule/components/class_view_timetable.php', ENT_QUOTES, 'UTF-8') ?>',
                        data: {
                            'dept_id': dept_id,
                            'year_of_study_id': year_of_study_id,
                            'section_id': section_id
                        },
                        headers: {
                            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                            'X-Requested-Path': window.location.pathname + window.location.search
                        },
                        success: function(response) {
                            $("#schedule").html(response)

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
            }
        }

        const load_class_view_timetable_schedule = (dept_id = 0, year_of_study_id = 0, section_id = 0) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_class_schedule/json/individual_allocated_subject_slot_list.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'dept_id': dept_id,
                        'year_of_study_id': year_of_study_id,
                        'section_id': section_id
                    },
                    success: async function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const table = response.table;
                            const tableHTML = response.tableHTML;
                            $("#timetable-schedule").html(table)
                            $("#individual-timetable-summary").html(tableHTML)
                        } else {
                            $("#timetable-schedule").html(
                                `<div class="subject-assignment">
                                    <img class="action-image" src="<?= GLOBAL_PATH . '/images/svgs/no-timetable.svg' ?>" alt="">
                                    <p class="action-text">
                                        It looks like no classes are assigned for this department yet. <br>
                                        Please refine your search by selecting a Year of Study and Section.
                                    </p>
                                    <div class="action-hint">
                                        *Knowledge grows when you seek it—give it another try!*
                                    </div>
                                </div>
                                `
                            )

                        }
                        resolve();
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(jqXHR);
                    }
                });
            });
        };

        const load_individual_view_timetable_schedule = (dept_id = 0, year_of_study_id = 0, section_id = 0) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_class_schedule/json/individual_allocated_subject_slot_list.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'dept_id': dept_id,
                        'year_of_study_id': year_of_study_id,
                        'section_id': section_id
                    },
                    success: async function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const individual_faculty_slot_subjects = response.data;
                            const days_list = response.days;
                            const subject_summary = response.subject_summary;
                            await populate_subject_slot_list(individual_faculty_slot_subjects, days_list);
                            await populate_individual_subject_summary(subject_summary);
                        } else {
                            $("#timetable-schedule").html(
                                `<div class="subject-assignment">
                                    <p class="action-text">
                                        No scheduled classes at the moment. Stay tuned for updates!
                                    </p>
                                    <div class="action-hint">
                                        *The best way to predict the future is to create it.*
                                    </div>
                                </div>
                                `
                            )

                        }
                        resolve();
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(jqXHR);
                    }
                });
            });
        };

        const populate_individual_subject_summary = (subject_summary) => {
            const container = $("#individual-timetable-summary"); // Parent container
            container.empty(); // Clear existing content

            // Create the table structure
            const table = $(`
                <table class="portal-table">
                    <thead>
                        <tr>
                            <th>Subject-Code</th>
                            <th>Subject Name</th>
                            <th>Department</th>
                            <th>Year Of Study</th>
                            <th>Section</th>
                            <th>No of Periods</th>
                            <th>Alloted Room</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            `);

            container.append(table);

            const tbody = table.find("tbody");

            const subjectTypes = {
                1: "Theory",
                2: "Practical",
                3: "Projects",
                4: "Extra Curricular"
            };

            Object.keys(subjectTypes).forEach(typeId => {
                const filteredSubjects = subject_summary.filter(sub => sub.subject_type_id == typeId);

                if (filteredSubjects.length > 0) {
                    // Add Section Title Row
                    tbody.append(`<tr><td colspan="7" class="text-center text-lg portal-background white-text">${subjectTypes[typeId]}</td></tr>`);

                    // Add Subject Rows
                    filteredSubjects.forEach(sub => {
                        tbody.append(`
                            <tr>
                                <td>${sub.subject_code}</td>
                                <td>${sub.subject_name}</td>
                                <td>${sub.dept_short_name}</td>
                                <td>${sub.year_of_study_title}</td>
                                <td>${sub.section_title}</td>
                                <td>${sub.no_of_periods}</td>
                                <td>${sub.alloted_room}</td>
                            </tr>
                        `);
                    });
                }
            });
        };



        const load_error_timetable_popup = (timetable_data) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_class_schedule/components/error_timetable_popup.php', ENT_QUOTES, 'UTF-8') ?>',
                    data: {
                        'timetable_data': timetable_data
                    },
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        // Inject popup content into the placeholder

                        $('#error_timetable').empty();
                        $('#error_timetable').html(response);

                        // Show the popup and bind the close button
                        $('#error_timetable').show();

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

        const load_success_timetable_popup = (timetable_data) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_class_schedule/components/success_timetable_popup.php', ENT_QUOTES, 'UTF-8') ?>',
                    data: {
                        'timetable_data': timetable_data
                    },
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        // Inject popup content into the placeholder

                        $('#success_timetable').empty();
                        $('#success_timetable').html(response);

                        // Show the popup and bind the close button
                        $('#success_timetable').show();
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
        const load_main_components = async () => {
            await callAction();
            const urlParams = new URLSearchParams(window.location.search);
            route = urlParams.get('route') || '';
            action = urlParams.get('action') || '';
            type = urlParams.get('type') || ''; // e.g., 'add', 'edit'

            switch (route) {
                case 'faculty':
                    switch (action) {
                        case 'add':
                            await load_add_faculty_timetable();
                            break;
                        case 'view':
                            await load_view_faculty_timetable();
                            break;
                        default:
                            await load_view_faculty_timetable();
                            break;
                    }
                    break;
                default:
                    break;
            }
        }
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>