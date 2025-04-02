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

    <script>
        let year_of_study_data = [];
        let section_data = [];
        let subject_name_list = [];
        let faculty_name_list = [];
        let sem_duration_id = 0;
        let sem_id = 0;
        let academic_year_id = 0;
        let year_of_study_id = 0;
        let section_id = 0;
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
                    url: '<?= htmlspecialchars(MODULES . '/faculty_classes/components/bg_card.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    success: function(response) {
                        $('#bg-card').html(response);
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

        const callAction = (element) => {
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action'); // e.g., 'add', 'edit'

            // Get the last part of the URL path
            const pathArray = window.location.pathname.split('/');
            const lastPath = pathArray[pathArray.length - 1];
            const routing_link = lastPath + window.location.search
            fetch_bg_card_title(routing_link);

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

        const load_faculty_classes_main_content = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_classes/components/faculty_classes_main_content.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href
                    },
                    success: function(response) {
                        $('#faculty-classes-main-content').html(response);
                        resolve();
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                    }
                });
            });
        }

        const fetch_subject_allocation_data = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_classes/json/fetch_subject_allocation_data.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href,
                        'year_of_study_id': year_of_study_id,
                        'section_id': section_id,
                        'sem_duration_id': sem_duration_id
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        if (response.code == 200) {
                            const data = response.data;
                            populateSubjectList(data)
                        } else {
                            console.error(response.message)
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

        const populateSubjectList = (data) => {
            const subjectList = document.getElementById('subject-list');

            // Clear any existing data
            subjectList.innerHTML = '';

            // Loop through each data entry and create the required structure
            data.forEach(item => {
                const row = document.createElement('div');
                row.className = 'row align-center';

                row.innerHTML = `
                    <div class="col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12">
                        <input type="hidden" name="previous_subject_allocation_id[]" value="${item.faculty_subjects_id}">
                        <div class="input-container autocomplete-container">
                            <input type="text" class="auto subject-name-dummy autocomplete-input" placeholder=" " value="${item.subject_name}">
                            <label class="input-label">Select The Subject Name</label>
                            <input type="hidden" name="subject_id[]" class="subject-id" value="${item.subject_id}">
                            <span class="autocomplete-arrow">&#8964;</span>
                            <div class="autocomplete-suggestions"></div>
                        </div>
                    </div>
                    <div class="col col-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container dropdown-container">
                            <input type="text" class="auto room-name-dummy dropdown-input" placeholder=" " value="${item.room_name}" readonly>
                            <label class="input-label">Select The Room Name</label>
                            <input type="hidden" name="room_id[]" class="room-id" value="${item.room_id}">
                            <span class="dropdown-arrow">&#8964;</span>
                            <div class="dropdown-suggestions"></div>
                        </div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12">
                        <div class="input-container autocomplete-container">
                            <input type="text" class="auto faculty-name-dummy autocomplete-input" placeholder=" " value="${item.faculty_name}">
                            <label class="input-label">Select The Faculty Name</label>
                            <input type="hidden" name="faculty_id[]" class="faculty-id" value="${item.faculty_id}">
                            <span class="autocomplete-arrow">&#8964;</span>
                            <div class="autocomplete-suggestions"></div>
                        </div>
                    </div>
                    <div class="col col-1 col-lg-1 col-md-6 col-sm-6 col-xs-12">
                        <button type="button" class="icon tertiary remove-subject-btn" data-previous-allocated-subject-id="${item.faculty_subjects_id}">X</button>
                    </div>
                `;

                // Append the row to the subject list container
                subjectList.appendChild(row);

            });
            $('.remove-subject-btn').on('click', async function(e) {
                if ($(this).data("previous-allocated-subject-id") != 0) {
                    await remove_allocated_subject($(this));
                }
            });
        };


        const add_another_subject = () => {
            const innerHTML = `
                        <div class="row align-center">
                        <input type="hidden" name="previous_subject_allocation_id[]" value="0">
                        <div class="col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12">
                            <div class="input-container autocomplete-container">
                                <input type="text" class="auto subject-name-dummy autocomplete-input" placeholder=" " value="">
                                <label class="input-label">Select The Subject Name</label>
                                <input type="hidden" name="subject_id[]" class="subject-id" value="">
                                <span class="autocomplete-arrow">&#8964;</span>
                                <div class="autocomplete-suggestions"></div>
                            </div>
                        </div>
                        <div class="col col-3 col-lg-3 col-md-6 col-sm-6 col-xs-12">
                            <div class="input-container dropdown-container">
                                <input type="text" class="auto room-name-dummy dropdown-input" placeholder=" " value="" readonly>
                                <label class="input-label">Select The Room Name</label>
                                <input type="hidden" name="room_id[]" class="room-id" value="">
                                <span class="dropdown-arrow">&#8964;</span>
                                <div class="dropdown-suggestions"></div>
                            </div>
                        </div>
                        <div class="col col-4 col-lg-4 col-md-6 col-sm-6 col-xs-12">
                            <div class="input-container autocomplete-container">
                                <input type="text" class="auto faculty-name-dummy autocomplete-input" placeholder=" " value="">
                                <label class="input-label">Select The Faculty Name</label>
                                <input type="hidden" name="faculty_id[]" class="faculty-id" value="">
                                <span class="autocomplete-arrow">&#8964;</span>
                                <div class="autocomplete-suggestions"></div>
                            </div>
                        </div>
                        <div class="col col-1 col-lg-1 col-md-6 col-sm-6 col-xs-12">
                            <button type="button" class="icon tertiary remove-subject-btn" data-previous-allocated-subject-id="0">X</button>
                        </div>
                    </div>
                `;

            // Append the row to the subject list container
            $('#subject-list').append(innerHTML);

            $('.subject-name-dummy').on('click input', async function(e) {
                fetch_subject_name_list($(this), <?= $logged_dept_id ?>, year_of_study_id, sem_id)
            })

            $('.room-name-dummy').on('click ', async function(e) {
                fetch_room_name_list($(this), <?= $logged_dept_id ?>)
            })

            $('.faculty-name-dummy').on('click ', async function(e) {
                fetch_faculty_name_list($(this), <?= $logged_dept_id ?>)
            })

            $('.remove-subject-btn').on('click', async function(e) {
                if ($(this).data("previous-allocated-subject-id") != 0) {
                    await remove_allocated_subject($(this));
                }
            });

        };


        const remove_allocated_subject = (element) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_classes/ajax/remove_allocated_subject.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href,
                        'previous_allocated_subject_id': element.data("previous-allocated-subject-id")
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        if (response.code == 200) {
                            showToast(response.status, response.message);
                            element.closest('.row').remove();
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

        const load_faculty_classes_edit_subject_allocation = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_classes/components/faculty_classes_edit_subject_allocation.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href
                    },
                    success: function(response) {
                        $('#allocation-content').html(response);
                        resolve();
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                    }
                });
            });
        }

        const fetch_subject_name_list = async (element, dept_id, year_of_study_id, sem_id) => {

            showDropdownLoading(element);
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= GLOBAL_PATH . '/json/fetch_subject_name_list.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href,
                        'dept_id': dept_id,
                        'year_of_study_id': year_of_study_id,
                        'sem_id': sem_id
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        if (response.code == 200) {
                            subject_name_list = response.data;

                            const suggestions = element.siblings(".autocomplete-suggestions");
                            const value = element.siblings(".subject-id");

                            // Get the input text
                            const inputText = element.val().toLowerCase();
                            // Filter student_name_list based on the input
                            const filteredSubjectList = subject_name_list.filter(subject =>
                                subject.title.toLowerCase().includes(inputText) ||
                                subject.code.toLowerCase().includes(inputText)
                            );

                            // Pass the filtered list to showSuggestions
                            showSuggestions(filteredSubjectList, suggestions, value, element);
                        } else {
                            showToast(response.status, response.message);
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

        const fetch_room_name_list = (element, dept_id) => {

            showDropdownLoading(element);
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= GLOBAL_PATH . '/json/fetch_room_name_list.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href,
                        'dept_id': dept_id,
                        'room_type': 1
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        if (response.code == 200) {
                            const room_name_list = response.data;

                            const suggestions = element.siblings(".dropdown-suggestions");
                            const value = element.siblings(".room-id");


                            // Pass the filtered list to showSuggestions
                            showSuggestions(room_name_list, suggestions, value, element);
                        } else {
                            showToast(response.status, response.message);
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

        const fetch_faculty_name_list = (element, dept_id) => {

            showDropdownLoading(element);
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/json/fetch_faculty_name_list.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    data: {
                        'faculty_dept_id': dept_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        faculty_name_list = response.data;
                        const suggestions = element.siblings(".autocomplete-suggestions")
                        const value = element.siblings(".faculty-id")

                        // Get the input text
                        const inputText = element.val().toLowerCase();
                        // Filter student_name_list based on the input
                        const filteredFacultyList = faculty_name_list.filter(faculty =>
                            faculty.title.toLowerCase().includes(inputText) ||
                            faculty.code.toLowerCase().includes(inputText)
                        );

                        showSuggestions(filteredFacultyList, suggestions, value, element);
                        resolve(faculty_name_list);
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const fetch_faculty_classes_class_advisors_details = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_classes/json/faculty_classes_class_advisors_details.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        if (response.code == 200) {
                            const data = response.data;
                            $("#class-manager-academic-year").text(data.academic_year_title + ' (' + data.sem_duration_title + ')')
                            $("#class-manager-year-of-study").text(data.year_of_study_title)
                            $("#class-manager-semester").text(data.sem_title)
                            $("#class-manager-section").text(data.section_title)

                            sem_duration_id = data.sem_duration_id;
                            sem_id = data.sem_id;
                            academic_year_id = data.academic_year_id;
                            year_of_study_id = data.year_of_study_id;
                            section_id = data.section_id;
                        } else {
                            window.history.back();
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

        const load_faculty_classes_edit_student_allocation = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_classes/components/faculty_classes_edit_student_allocation.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href
                    },
                    success: function(response) {
                        $('#allocation-content').html(response);
                        resolve();
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                    }
                });
            });
        }

        const fetch_student_batch_list = (element) => {
            showDropdownLoading(element);
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= GLOBAL_PATH . '/json/fetch_student_batch_list.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        if (response.code == 200) {
                            const student_batch_list = response.data;
                            const suggestions = element.siblings(".dropdown-suggestions");
                            const value = element.siblings(".student-batch-id");
                            // Pass the filtered list to showSuggestions
                            showSuggestions(student_batch_list, suggestions, value, element);
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

        const fetch_student_group_list = (element) => {
            showDropdownLoading(element);
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= GLOBAL_PATH . '/json/fetch_student_group_list.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
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
                            const student_group_list = response.data;
                            const suggestions = element.siblings(".dropdown-suggestions");
                            const value = element.siblings(".student-group-id");
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

        const load_student_list_table = (academic_batch_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_classes/json/load_student_list_table.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href,
                        'academic_batch_id': academic_batch_id,
                        'sem_duration_id': sem_duration_id,
                        'sem_id': sem_id,
                        'academic_year_id': academic_year_id,
                        'year_of_study_id': year_of_study_id,
                        'section_id': section_id
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        if (response.code == 200) {
                            const data = response.data;
                            if (data) {
                                populate_student_list(data, response.total_count_of_data, response.selected_count_of_data, response.remaining_count_of_data)
                            } else {
                                $('.curvy-table-container').html(`
                                    <div class="class-student-assignment m-6 flex-container justify-center">
                                        <p class="action-text">
                                            Select a batch to view the list of students for your class.
                                        </p>
                                        <div class="action-hint">
                                            *"As Professor Snape would say, 'Leadership requires strength, whether facing enemies or allies.' Your guidance shapes the future of these students."*
                                        </div>
                                    </div>
                                `)
                            }
                        } else {
                            showToast(response.status, response.message)
                            $("#student-statistics").empty()
                            $('.curvy-table-container').html(`
                                    <div class="class-student-assignment m-6 flex-container justify-center">
                                        <p class="action-text">
                                            Select a batch to view the list of students for your class.
                                        </p>
                                        <div class="action-hint">
                                            *"As Professor Snape would say, 'Leadership requires strength, whether facing enemies or allies.' Your guidance shapes the future of these students."*
                                        </div>
                                    </div>
                                `)
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

        const populate_student_list = (data, total_count_of_data, selected_count_of_data, remaining_count_of_data) => {
            // Create the table header
            let tableHTML = `
                <table class="curvy-table">
                    <thead>
                        <tr>
                            <th class="curvy-th flex-container justify-center">
                                <label class="modern-checkbox">
                                    <input type="checkbox" id="mother-checkbox"/>
                                    <span></span>
                                </label>
                            </th>
                            <th class="curvy-th">Student's Name</th>
                            <th class="curvy-th">Register Number</th>
                            <th class="curvy-th">Group</th>

                        </tr>
                    </thead>
                    <tbody>
            `;
            // Loop through the data array to populate the rows
            data.forEach(student => {
                tableHTML += `
                    <tr>
                        <td class="curvy-td">
                        
                            <input type="hidden" name="allocated_selected_student_id[]" value="${student.student_official_id}"/>
                            <input type="hidden" class="selected-student-id" name="selected_student_id[]" value="${student.student_official_id != 0 ? student.student_id : 0} " />
                            <div class="input-group align-center m-1">
                                <label class="modern-checkbox">
                                    <input type="checkbox" class="student-id" name="student_id[]" value="${student.student_id}" 
                                    ${student.student_official_details_status == 0 ? 'checked' : ''}  />
                                    <span></span>
                                </label>
                            </div>
                        </td>
                        <td class="curvy-td">${student.full_name || 'N/A'}</td>
                        <td class="curvy-td">${student.student_reg_number || 'N/A'}</td>
                        <td class="curvy-td">
                            <div class="input-container dropdown-container">
                                <input type="text" class="auto student-group-dummy dropdown-input" placeholder=" " value="">
                                <label class="input-label">Select The Student's Group</label>
                                <input type="hidden" name="student_group_id[]" class="student-group-id" value="">
                                <span class="dropdown-arrow">&#8964;</span>
                                <div class="dropdown-suggestions"></div>
                            </div>
                        </td>

                    </tr>
                `;
            });

            // Close the table structure
            tableHTML += `
                    </tbody>
                </table>
                <button type="submit" class="primary text-center full-width m-6">SAVE</button>
            `;

            $("#student-statistics").html(`
                
                <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="card full-width">
                        <div class="row">
                            <div class="col-9 text-left">
                                <h1 id="total-count-of-data">${total_count_of_data}</h1>
                                <h6 class="text-light">Total Students</h6>
                            </div>
                            <div class="col-3">
                                <img class="statistics-card-icon" src="<?= GLOBAL_PATH . '/images/svgs/total_students.svg' ?>" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="card full-width">
                        <div class="row">
                            <div class="col-9 text-left">
                                <h1 id="selected-count-of-data">${selected_count_of_data}</h1>
                                <h6 class="text-light">Selected Students</h6>
                            </div>
                             <div class="col-3">
                                <img class="statistics-card-icon" src="<?= GLOBAL_PATH . '/images/svgs/selected_students.svg' ?>" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="card full-width">
                        <div class="row">
                            <div class="col-9 text-left">
                                <h1 id="remaining-count-of-data">${remaining_count_of_data}</h1>
                                <h6 class="text-light">Remaining Students</h6>
                            </div>
                             <div class="col-3">
                                <img class="statistics-card-icon" src="<?= GLOBAL_PATH . '/images/svgs/remaining_students.svg' ?>" alt="">
                            </div>
                        </div>
                    </div>
                </div>
    
            `);
            // Populate the table into the container with id "curvy-table-container"
            $('.curvy-table-container').html(tableHTML);

            // Attach event listener to the mother checkbox after table is populated
            $('#mother-checkbox').on('change', function() {
                toggle_all_checkbox($(this));
                get_checkbox_counts()
            });

            $('.student-id').on('change', function() {
                assign_checkbox_value($(this));
                get_checkbox_counts()
            });

            $('.student-group-dummy').on('click', function() {
                fetch_student_group_list($(this))
            })
        };

        const get_checkbox_counts = () => {
            // Get all checkboxes with the class 'student-id'
            const checkboxes = $('.student-id');

            // Count the checked and unchecked checkboxes
            const checkedCount = checkboxes.filter(':checked').length;
            const uncheckedCount = checkboxes.length - checkedCount;

            $('#selected-count-of-data').text(checkedCount);
            $('#remaining-count-of-data').text(uncheckedCount);
        }

        const toggle_all_checkbox = (selectAllCheckbox) => {
            $('.student-id').prop('checked', selectAllCheckbox.prop('checked'));
            $('.student-id').change();
        };

        const assign_checkbox_value = (element) => {
            if (element.prop('checked') == true) {
                element.closest('td').find('.selected-student-id').val(element.val())
            } else {
                element.closest('td').find('.selected-student-id').val(0)
            }

        };



        const load_faculty_classes_manual_edit_student_allocation = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_classes/components/faculty_classes_manual_edit_student_allocation.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href
                    },
                    success: function(response) {
                        $('#allocation-content').html(response);
                        resolve();
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                    }
                });
            });
        }

        const load_faculty_classes_auto_edit_student_allocation = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_classes/components/faculty_classes_auto_edit_student_allocation.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href
                    },
                    success: function(response) {
                        $('#allocation-content').html(response);
                        resolve();
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                    }
                });
            });
        }

        const assign_section_to_dropdown = (element) => {
            showDropdownLoading(element)
            const suggestions = element.siblings(".dropdown-suggestions");
            const value = element.siblings(".section-id");
            showSuggestions(section_data, suggestions, value, element);
        }

        const fetch_year_of_study_and_section_with_academic_batch_class_allotment = (academic_batch_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= MODULES . '/faculty_classes/json/fetch_year_of_study_and_section_with_academic_batch_class_allotment.php' ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    data: {
                        'location': window.location.href,
                        'academic_batch_id': academic_batch_id,
                        'dept_id': <?= $logged_dept_id ?>
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            year_of_study_data = response.year_of_study_data;
                            section_data = response.section_data;
                            $('#year-of-study-title').val(year_of_study_data.year_of_study_title);
                            $('#year-of-study-id').val(year_of_study_data.year_of_study_id);
                            if (section_data.length > 1) {
                                const element = $(".section-dummy");
                                showDropdownLoading(element)
                                const suggestions = element.siblings(".dropdown-suggestions");
                                const value = element.siblings(".section-id");
                                showSuggestions(section_data, suggestions, value, element);
                            } else {
                                $(".section-dummy").val(section_data[0].title);
                                $(".section-id").val(section_data[0].value);
                            }


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
                            const value = element.siblings(".section-id")
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

        const load_faculty_classes_hod_student_allocation = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_classes/components/faculty_classes_hod_student_allocation.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href
                    },
                    success: function(response) {
                        $('#allocation-content').html(response);
                        resolve();
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                    }
                });
            });
        }

        const load_faculty_classes_hod_subject_allocation = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_classes/components/faculty_classes_hod_subject_allocation.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href
                    },
                    success: function(response) {
                        $('#allocation-content').html(response);
                        resolve();
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                    }
                });
            });
        }

        const fetch_hod_view_subject_allocation = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_classes/json/fetch_hod_view_subject_allocation.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        if (response.code == 200) {
                            const data = response.data
                            populate_subject_allocation(data)
                        } else {
                            showToast(response.status, response.message)
                            $('#subject-allocation-accordian').html(`
                                <div class="verify-subject-allocation-action m-6 flex-container justify-center">
                                    <p class="action-text">
                                        No pending subject allocations to verify. Please check again later.
                                    </p>
                                    <div class="action-hint">
                                        *"As Gandalf said, 'A wizard is never late, he arrives precisely when he means to.'"*
                                    </div>
                                </div>
                            `)
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

        const populate_subject_allocation = (data) => {
            const accordionContainer = $('#subject-allocation-accordian'); // Select the container to append the accordion items

            // Clear existing content
            accordionContainer.empty();

            // Loop through the data and create accordion items
            data.forEach((classItem, index) => {
                // Create the accordion item HTML structure
                let accordionItem = `
                    <div class="accordion-item" data-year-of-study-title="${classItem.year_of_study_title}" data-section-title="${classItem.section_title}" data-year-of-study-id="${classItem.year_of_study_id}" data-section-id="${classItem.section_id}">
                        <div class="accordion-header" onclick="toggleAccordion(this)">
                            <h5>${classItem.year_of_study_title} - Year ${classItem.section_title} - Section</h5>
                            <span class="accordion-icon">+</span>
                        </div>
                        <div class="accordion-content">
                `;

                // Check if the 'details' array has data, if not show the message
                if (!classItem.details || classItem.details.length === 0) {
                    accordionItem += `
                        <p class="action-text">
                            Whoops! Looks like no subjects have been allocated to this class yet. Stay tuned for the updates! 📚
                        </p>
                    `;
                } else {
                    // Add the table structure if details are present
                    accordionItem += `
                        <div class="curvy-table-container flex-column">
                            <table class="curvy-table">
                                <thead>
                                    <tr>
                                        <th class="curvy-th flex-container justify-center">Sl.No</th>
                                        <th class="curvy-th">Subject</th>
                                        <th class="curvy-th">Faculty</th>
                                        <th class="curvy-th">Room</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    // Loop through the details and add rows to the table
                    classItem.details.forEach((detail, detailIndex) => {
                        accordionItem += `
                            <tr>
                                <td class="curvy-td">${detailIndex + 1}</td>
                                <td class="curvy-td">${detail.subject_name || 'N/A'}</td>
                                <td class="curvy-td">${detail.faculty_name || 'N/A'}</td>
                                <td class="curvy-td">${detail.room_name || 'N/A'}</td>
                            </tr>
                        `;
                    });

                    // Close the table
                    accordionItem += `
                        </tbody>
                            </table>
                            <button type="button" class="primary text-center full-width m-6 subject-allocate-verify-button" >Verify</button>
                        </div>
                    `;
                }

                // Close the accordion content and item
                accordionItem += `
                        </div>
                    </div>
                `;

                // Append the accordion item to the container
                accordionContainer.append(accordionItem);

            });
            $('.subject-allocate-verify-button').click(function() {
                const accordian_item = $(this).closest('.accordion-item');
                const verifying_year_of_study_id = accordian_item.data('year-of-study-id')
                const verifying_section_id = accordian_item.data('section-id')
                const verifying_year_of_study_title = accordian_item.data('year-of-study-title')
                const verifying_section_title = accordian_item.data('section-title')
                verify_pending_hod_subject_allocation_popup(verifying_year_of_study_title, verifying_section_title, verifying_year_of_study_id, verifying_section_id)
            })
        };
        const populate_student_allocation = (data) => {
            const accordionContainer = $('#student-allocation-accordian'); // Select the container to append the accordion items

            // Clear existing content
            accordionContainer.empty();

            // Check if data is present
            if (!data || data.length === 0) {
                accordionContainer.append(`
            <p class="action-text">
                No student data available. Please check back later! 👨‍🎓👩‍🎓
            </p>
        `);
                return;
            }

            // Loop through the data and create accordion items
            data.forEach((sectionItem, index) => {
                // Create the accordion item HTML structure
                let accordionItem = `
            <div class="accordion-item" data-year-of-study-title="${sectionItem.year_of_study_title}" data-section-title="${sectionItem.section_title}" data-year-of-study-id="${sectionItem.year_of_study_id}" data-section-id="${sectionItem.section_id}">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    <h5>${sectionItem.year_of_study_title} - Year ${sectionItem.section_title} - Section</h5>
                    <span class="accordion-icon">+</span>
                </div>
                <div class="accordion-content">
        `;

                // Check if the 'students' array has data, if not show the message
                if (!sectionItem.students || sectionItem.students.length === 0) {
                    accordionItem += `
                <p class="action-text">
                    No students have been allocated to this section yet. Stay tuned! 🎓
                </p>
            `;
                } else {
                    // Add the table structure if students are present
                    accordionItem += `
                <div class="curvy-table-container flex-column">
                    <table class="curvy-table">
                        <thead>
                            <tr>
                                <th class="curvy-th flex-container justify-center">Sl.No</th>
                                <th class="curvy-th">Student Name</th>
                                <th class="curvy-th">Registration Number</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

                    // Loop through the students and add rows to the table
                    sectionItem.students.forEach((student, studentIndex) => {
                        accordionItem += `
                    <tr>
                        <td class="curvy-td">${studentIndex + 1}</td>
                        <td class="curvy-td">${student.student_name || 'N/A'}</td>
                        <td class="curvy-td">${student.student_reg_number || 'N/A'}</td>
                    </tr>
                `;
                    });

                    // Close the table
                    accordionItem += `
                        </tbody>
                    </table>
                    <button type="button" class="primary text-center full-width m-6 student-allocate-verify-button" >Verify</button>
                </div>
            `;
                }

                // Close the accordion content and item
                accordionItem += `
                </div>
            </div>
        `;

                // Append the accordion item to the container
                accordionContainer.append(accordionItem);
            });

            $('.student-allocate-verify-button').click(function() {
                const accordian_item = $(this).closest('.accordion-item');
                const verifying_year_of_study_id = accordian_item.data('year-of-study-id')
                const verifying_section_id = accordian_item.data('section-id')
                const verifying_year_of_study_title = accordian_item.data('year-of-study-title')
                const verifying_section_title = accordian_item.data('section-title')
                verify_pending_hod_student_allocation_popup(verifying_year_of_study_title, verifying_section_title, verifying_year_of_study_id, verifying_section_id)
            })
        };




        const verify_pending_hod_subject_allocation = async (verifying_year_of_study_id, verifying_section_id) => {
            showComponentLoading();
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_classes/ajax/verify_pending_hod_subject_allocation.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href,
                        'verifying_year_of_study_id': verifying_year_of_study_id,
                        'verifying_section_id': verifying_section_id
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        if (response.code == 200) {
                            showToast(response.status, response.message)
                            $('.popup-overlay').fadeOut();
                            setTimeout(() => {
                                $('.popup-overlay').remove();
                                setTimeout(() => {
                                    fetch_hod_view_subject_allocation()
                                    hideComponentLoading();
                                }, 500);

                            }, 500);

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


        const verify_pending_hod_subject_allocation_popup = (verifying_year_of_study_title, verifying_section_title, verifying_year_of_study_id, verifying_section_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_classes/components/faculty_classes_hod_subject_allocation_popup.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href,
                        'verifying_year_of_study_id': verifying_year_of_study_id,
                        'verifying_section_id': verifying_section_id,
                        'verifying_year_of_study_title': verifying_year_of_study_title,
                        'verifying_section_title': verifying_section_title
                    },
                    success: function(response) {
                        $('#faculty-classes-popup').html(response)
                        resolve();
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                    }
                });
            });
        }

        const verify_pending_hod_student_allocation_popup = (verifying_year_of_study_title, verifying_section_title, verifying_year_of_study_id, verifying_section_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_classes/components/faculty_classes_hod_student_allocation_popup.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href,
                        'verifying_year_of_study_id': verifying_year_of_study_id,
                        'verifying_section_id': verifying_section_id,
                        'verifying_year_of_study_title': verifying_year_of_study_title,
                        'verifying_section_title': verifying_section_title
                    },
                    success: function(response) {
                        $('#faculty-classes-popup').html(response)
                        resolve();
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                    }
                });
            });
        }

        const load_edit_student_group_popup = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_classes/components/edit_student_group_popup.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href,
                        'sem_duration_id': sem_duration_id,
                        'year_of_study_id': year_of_study_id,
                        'section_id': section_id,
                        'academic_year_id': academic_year_id,
                        'sem_id': sem_id,
                    },
                    success: function(response) {
                        $('#faculty-classes-popup').html(response)
                        resolve();
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                    }
                });
            });
        }

        const load_allocated_class_section_groups = (sem_duration_id, year_of_study_id, section_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_classes/json/allocated_class_section_groups.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
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
                            const data = response.data;
                            data.forEach(group => {
                                createChip(
                                    $("#student_group_input").val(group.group_title),
                                    $('#student-group-chips'),
                                    group.group_id
                                );
                                $("#student_group_input").val("");
                            });

                        } else {
                            showToast("info", "Please Type Your New Batch Title and Press Enter Key")
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

        const verify_pending_hod_student_allocation = (verifying_year_of_study_id, verifying_section_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_classes/ajax/verify_pending_hod_student_allocation.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href,
                        'verifying_year_of_study_id': verifying_year_of_study_id,
                        'verifying_section_id': verifying_section_id
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        if (response.code == 200) {
                            showToast(response.status, response.message)
                            $('.popup-overlay').fadeOut();
                            setTimeout(() => {
                                $('.popup-overlay').remove();
                                setTimeout(() => {
                                    fetch_hod_view_student_allocation()
                                    hideComponentLoading();
                                }, 500);

                            }, 500);

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


        const fetch_hod_view_student_allocation = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_classes/json/fetch_hod_view_student_allocation.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        if (response.code == 200) {
                            const data = response.data
                            populate_student_allocation(data)
                        } else {
                            showToast(response.status, response.message)
                            $('#student-allocation-accordian').html(`
                                <div class="verify-student-allocation-action flex-container justify-center">
                                    <p class="action-text">
                                        All student allocations have been successfully verified. No pending allocations at this moment. Please check back later.
                                    </p>
                                    <div class="action-hint">
                                        *"Leadership is not about being in charge. It is about taking care of those in your charge."* 
                                    </div>
                                </div>
                            `);

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

        const load_faculty_classes_components = async () => {
            try {
                showComponentLoading();
                const urlParams = new URLSearchParams(window.location.search);
                const route = urlParams.get('route');
                const action = urlParams.get('action');
                const type = urlParams.get('type');
                const tab = urlParams.get('tab');
                if (type == 'student_allocation') {

                    if (<?= $logged_role_id ?> == 6) {
                        await load_faculty_classes_hod_student_allocation();
                        $('.action-title').text('Student Allocation Panel')
                        $('#subject-allocation-tab').removeClass("active")
                        $('#student-allocation-tab').addClass("active")
                    } else {
                        await load_faculty_classes_edit_student_allocation();
                        $('.action-title').text('Student Allocation Panel')
                        $('#subject-allocation-tab').removeClass("active")
                        $('#student-allocation-tab').addClass("active")
                        if (tab == 'manual') {
                            await load_faculty_classes_manual_edit_student_allocation();
                        } else if (tab == 'auto') {
                            await load_faculty_classes_auto_edit_student_allocation();
                        }
                    }

                } else {
                    if (<?= $logged_role_id ?> == 6) {
                        await load_faculty_classes_hod_subject_allocation();
                        $('.action-title').text('Subject Allocation Panel')
                        $('#subject-allocation-tab').addClass("active")
                        $('#student-allocation-tab').removeClass("active")
                    } else {
                        await load_faculty_classes_edit_subject_allocation();
                        $('.action-title').text('Subject Allocation Panel')
                        $('#subject-allocation-tab').addClass("active")
                        $('#student-allocation-tab').removeClass("active")
                    }

                }
                await updateUrl({
                    route: route,
                    action: action,
                    type: type,
                    tab: tab
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

        }
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>