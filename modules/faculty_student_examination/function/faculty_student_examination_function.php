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
        const callAction = async () => {
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action'); // e.g., 'add', 'edit'
            const route = urlParams.get('route'); // e.g., 'add', 'edit'
            const type = urlParams.get('type'); // e.g., 'add', 'edit'
            if (route === "faculty") {
    if (action === "view") {
        if (type === null) {
            if (<?= $logged_role_id ?> === 7) {
                load_examination_exam_view_page();
            } else {
                load_examination_fac_view_page();
            }
        } else if (type === "overall") {
            if (<?= $logged_role_id ?> === 7) {
                load_examination_exam_view_page();
            } else {
                load_examination_fac_view_page();
            }
        } else if (type === "exam_time_table") {
            load_examination_exam_time_table_view_page();
        } else if (type === "subject_allotment") {
            load_examination_exam_time_table_view_page();
        } else {
            if (<?= $logged_role_id ?> === 7) {
                load_examination_exam_view_page();
            } else {
                load_examination_fac_view_page();
            }
        }
        
        $('.bg-card-edit-button').show();
        $('.bg-card-view-button').hide();

        if (<?= $logged_role_id ?> === 7) {
            $('#faculty-examination-add-button').show();
            console.log("role id1");
        } else {
            $('#faculty-examination-add-button').hide();
            console.log("role  1");
        }

    } else if (action === "add") {
        if (type === null) {
            load_examination_entry_form();
        } else if (type === "leave_request") {
            load_leave_approved_status();
        } else if (type === "examination_allotment") {
            view_exam_add();
        } else {
            if (<?= $logged_role_id ?> === 7) {
                load_examination_exam_view_page();
            } else {
                load_examination_fac_view_page();
            }
        }
        
        $('#faculty-examination-add-button').hide();
        $('.bg-card-edit-button').show();
        $('.bg-card-view-button').show();

    } else if (action === "edit") {
        $('.bg-card-edit-button').hide();
        $('.bg-card-view-button').show();
        console.log("role id");

        if (<?= $logged_role_id ?> === 7) {
            console.log("role id 2");
            $('#faculty-examination-add-button').show();
        }
    }
}

        }
        //         function showTooltip(row) {
        //   // Get the data-info attribute and parse it from JSON string to array
        //   const depts = JSON.parse(row.getAttribute('data-info'));

        //   // Remove any existing tooltip
        //   $('.tooltip-popup').remove();

        //   // Create the tooltip with department list
        //   const $tooltip = $('<div class="tooltip-popup"><ul>' + 
        //       depts.map(dept => 
        //           `<li data-dept-id="${dept.dept_id}" title="${dept.dept_title}">${dept.dept_short_name}</li>`
        //       ).join('') + 
        //       '</ul></div>');

        //   // Append tooltip to body
        //   console.log($tooltip);
        //   $('.content').append($tooltip);

        //   // Position the tooltip
        //   const $row = $(row);
        //   const offset = $row.offset();
        //   const rowWidth = $row.outerWidth();
        //   const tooltipWidth = $tooltip.outerWidth();
        //   $tooltip.css({
        //       left: offset.left + (rowWidth / 2) - (tooltipWidth / 2),
        //       top: offset.top + $row.outerHeight() + 5
        //   });
        // }

        function showTooltip(row) {
            const depts = JSON.parse(row.getAttribute('data-info') || '[]');
            $('.tooltip-popup').remove();
            const $tooltip = $('<div class="tooltip-popup"><ul>' +
                depts.map(dept =>
                    `<li data-dept-id="${dept.dept_id}" title="${dept.dept_title}">${dept.dept_short_name}</li>`
                ).join('') +
                '</ul></div>');
            $('body').append($tooltip);

            const $row = $(row);
            const offset = $row.offset();
            const rowWidth = $row.outerWidth();
            const tooltipWidth = $tooltip.outerWidth();
            $tooltip.css({
                left: offset.left + (rowWidth / 2) - (tooltipWidth / 2),
                top: offset.top + $row.outerHeight() + 5
            });
        }
        const view_exam = (element) => {
            return new Promise((resolve, reject) => {
                // updateUrl({
                //     route: 'faculty',
                //     action: 'add',
                //     type: 'subject_allotment'
                // });
                const params = {
                    exam_id: element.getAttribute('data-exam_id'),
                    exam_group_id: element.getAttribute('data-exam_group_id'),
                    exam_type_id: element.getAttribute('data-exam_type_id'),
                    exam_duration: element.getAttribute('data-exam_duration'),
                    exam_start_date: element.getAttribute('data-exam_start_date'),
                    exam_end_date: element.getAttribute('data-exam_end_date')
                };
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_examination/components/faculty_student_shadule.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // Secure CSRF token // Secure CSRF token // Secure CSRF token // Secure CSRF token
                    },
                    data: params, // Data to be sent
                    success: function(response) {
                        $('#add-student-examination').html(response);
                        $('#student-examination-list').empty();
                        // $('#info').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        };
        const view_exam_add = (element) => {
            return new Promise((resolve, reject) => {
                updateUrl({
                    route: 'faculty',
                    action: 'add',
                    type: 'subject_allotment'
                });
                const params = {
                    exam_id: element.getAttribute('data-exam_id'),
                    exam_group_id: element.getAttribute('data-exam_group_id'),
                    exam_type_id: element.getAttribute('data-exam_type_id'),
                    exam_duration: element.getAttribute('data-exam_duration'),
                    exam_start_date: element.getAttribute('data-exam_start_date'),
                    exam_end_date: element.getAttribute('data-exam_end_date')
                };
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_examination/components/faculty_student_allot_examination.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // Secure CSRF token // Secure CSRF token // Secure CSRF token // Secure CSRF token
                    },
                    data: params, // Data to be sent
                    success: function(response) {
                        $('#add-student-examination').html(response);
                        $('#student-examination-list').empty();
                        // $('#info').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        };
        const view_exam_result = (element) => {
            return new Promise((resolve, reject) => {
                updateUrl({
                    route: 'faculty',
                    action: 'view',
                    type: 'exam_result'
                });
                const params = {
                    exam_id: element.getAttribute('data-exam_id'),
                    exam_group_id: element.getAttribute('data-exam_group_id'),
                    exam_type_id: element.getAttribute('data-exam_type_id'),
                    exam_duration: element.getAttribute('data-exam_duration'),
                    exam_start_date: element.getAttribute('data-exam_start_date'),
                    exam_end_date: element.getAttribute('data-exam_end_date')
                };
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_examination/components/faculty_student_exam_mark_view.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // Secure CSRF token // Secure CSRF token // Secure CSRF token // Secure CSRF token
                    },
                    data: params, // Data to be sent
                    success: function(response) {
                        $('#add-student-examination').html(response);
                        $('#student-examination-list').empty();
                        // $('#info').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        };
        const exam_mark_entry = (element) => {
            return new Promise((resolve, reject) => {
                updateUrl({
                    route: 'faculty',
                    action: 'add',
                    type: 'mark_entry'
                });
                const params = {
                    exam_id: element.getAttribute('data-exam_id'),
                    exam_group_id: element.getAttribute('data-exam_group_id'),
                    exam_type_id: element.getAttribute('data-exam_type_id'),
                    exam_duration: element.getAttribute('data-exam_duration'),
                    exam_start_date: element.getAttribute('data-exam_start_date'),
                    exam_end_date: element.getAttribute('data-exam_end_date')
                };
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_examination/components/faculty_student_exam_mark.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // Secure CSRF token // Secure CSRF token // Secure CSRF token // Secure CSRF token
                    },
                    data: params, // Data to be sent
                    success: function(response) {
                        $('#add-student-examination').html(response);
                        $('#student-examination-list').empty();
                        // $('#info').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        };
        const exam_mark_entry_layout = (element) => {
            return new Promise((resolve, reject) => {
                updateUrl({
                    route: 'faculty',
                    action: 'add',
                    type: 'mark_entry'
                });
                const params = {
                    exam_id: element.getAttribute('data-exam_id'),
                    exam_group_id: element.getAttribute('data-exam_group_id'),
                    exam_type_id: element.getAttribute('data-exam_type_id'),
                    exam_duration: element.getAttribute('data-exam_duration'),
                    exam_start_date: element.getAttribute('data-exam_start_date'),
                    exam_end_date: element.getAttribute('data-exam_end_date')
                };
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_examination/components/faculty_student_mark_entry.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // Secure CSRF token // Secure CSRF token // Secure CSRF token // Secure CSRF token
                    },
                    data: params, // Data to be sent
                    success: function(response) {
                        $('#add-student-examination').html(response);
                        $('#student-examination-list').empty();
                        // $('#info').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        };

        const exam_time_table_view_page = (element) => {
            return new Promise((resolve, reject) => {
                updateUrl({
                    route: 'faculty',
                    action: 'view',
                    type: 'exam_time_table'
                });
                const params = {
                    exam_id: element.getAttribute('data-exam_id'),
                    exam_group_id: element.getAttribute('data-exam_group_id'),
                    exam_type_id: element.getAttribute('data-exam_type_id'),
                    exam_duration: element.getAttribute('data-exam_duration'),
                    exam_start_date: element.getAttribute('data-exam_start_date'),
                    exam_end_date: element.getAttribute('data-exam_end_date')
                };
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_examination/components/faculty_student_exam_time_table_view.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token  // Secure CSRF token // Secure CSRF token // Secure CSRF token // Secure CSRF token
                    },
                    data: params, // Data to be sent
                    success: function(response) {
                        $('#add-student-examination').html(response);
                        $('#student-examination-list').empty();
                        // $('#info').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        };
        // const fetch_dept_slots_list = (element, dept_id, slot_type, day_id, year_of_study_id, section_id, sem_duration_id) => {
        //     return new Promise((resolve, reject) => {
        //         $.ajax({
        //             url: '<?= htmlspecialchars(GLOBAL_PATH . '/json/fetch_slots_list.php', ENT_QUOTES, 'UTF-8') ?>',
        //             type: 'POST',
        //             headers: {
        //                 'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
        //             },
        //             data: {
        //                 'dept_id': dept_id,
        //                 'slot_type': slot_type,
        //                 'day_id': day_id,
        //                 'year_of_study_id': year_of_study_id,
        //                 'section_id': section_id,
        //                 'sem_duration_id': sem_duration_id
        //             },
        //             success: function(response) {
        //                 response = JSON.parse(response);
        //                 let slots_list = response.data;
        //                 const suggestions = element.siblings(".autocomplete-suggestions")
        //                 const value = element.siblings(".selected-slots")
        //                 const slots_chips = getChipsValues($('#selected-slots-list-chips'))
        //                 slots_list = slots_list.filter(slot => !slots_chips.includes(slot.title));

        //                 showSuggestions(slots_list, suggestions, value, element);
        //                 resolve(slots_list);
        //             },
        //             error: function(error) {
        //                 reject(error);
        //             }
        //         });
        //     });
        // };




        const fetch_year_list = (examid, sel) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= MODULES . '/faculty_student_examination/json/fetch_faculty_year_section.php' ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        const data = response.data;
                        console.log(data);

                        let $htmlContent = $();

                        $.each(data, function(index, item) {
                            let $card = $('<div>')
                                .addClass('popup-card mx-6 my-4 individual-year-list')
                                .attr('data-year-id', item.year_of_study_id)
                                .attr('data-section-id', item.section_id)
                                .attr('data-exam-id', examid)
                                .append(
                                    $('<div>').addClass('row align-center')
                                    .append(
                                        $('<div>').addClass('col col-4 col-lg-4 col-md-4 col-sm-4 col-xs-4 text-left text-color text-l')
                                        .text(item.year_of_study_title)
                                    )
                                    .append(
                                        $('<div>').addClass('col col-8 col-lg-8 col-md-8 col-sm-8 col-xs-8 text-right')
                                        .append(
                                            $('<p>').addClass('text-light')
                                            .text(item.section_title)
                                        )
                                    )
                                );

                            $htmlContent = $htmlContent.add(
                                $('<div>').addClass('row').append($card)
                            );
                        });

                        $('#year-list').empty().append($htmlContent);

                        // Add click handler after content is loaded
                        $('#year-list').on('click', '.individual-year-list', function() {
                            // console.log("click");
                            $('.individual-year-list').removeClass('active');
                            $(this).addClass('active');

                            const yearId = $(this).attr('data-year-id');
                            const sectionId = $(this).attr('data-section-id');
                            if (sel == 0) {
                                fetch_subject_list(yearId, sectionId, examid);
                            } else if (sel == 1) {
                                fetch_eaxm_time_table(yearId, sectionId, examid);
                                // create_date(yearId, sectionId, examid);
                            }

                            $('#exam-list').empty();
                            console.log(`Year ID: ${yearId}, Section ID: ${sectionId}`);
                        });

                        resolve(data);
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };
        const fetch_subject_list = (yearId, sectionId, examid) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= MODULES . '/faculty_student_examination/json/fetch_faculty_subject.php' ?>',
                    type: 'POST',
                    data: {
                        year_of_study_id: yearId,
                        section_id: sectionId
                    },
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        console.log(response['code']);
                        if (response['code'] == 200) {
                        let slots_list = response.data;
                        const data = response.data[0];
                        // if (response.data[0]) {
                        //      // Assign value if condition is true
                        // } else {
                        //     data = ""; // Assign empty string if condition is false
                        // }
                        console.log(data);
                        if (data) {
                            let $htmlContent = $();
                            $.each(data, function(index, item) {
                                ids = item.id;
                                let $card = $('<div>')
                                    // <li class="nav-list tooltip tooltip-right">
                                    .addClass('popup-card mx-6 my-4 individual-subject-list')
                                    .attr('data-subject-id', item.faculty_subjects_id)
                                    .attr('data-year-id', yearId)
                                    .attr('data-section-id', sectionId)
                                    .attr('data-exam-id', examid)
                                    .append(
                                        $('<div>').addClass('row align-center')
                                        .append(
                                            $('<div>').addClass('col col-4 col-lg-4 col-md-4 col-sm-4 col-xs-4 text-left text-color text-l tooltip tooltip-right')
                                            .text(item.subject_short_name)
                                            .append(
                                                $('<span>').addClass('tooltip-text')
                                                .append($('<div>').text(item.subject_name))
                                            ),
                                            $('<div>').addClass('col col-8 col-lg-8 col-md-8 col-sm-8 col-xs-8 text-right')
                                            .append(
                                                $('<p>').addClass('text-light')
                                                .text(item.subject_code)
                                            )
                                        )
                                    );

                                $htmlContent = $htmlContent.add(
                                    $('<div>').addClass('row').append($card)
                                );
                            });
                            $('#subject-list').empty().append($htmlContent);

                        } else {

                            $('#subject-list').empty().append(
                                $('<div>').addClass('individual-subject-list').append(
                                    $('<img>').attr('src', '<?= GLOBAL_PATH . '/images/svgs/no_data_found.svg' ?>').attr('alt', 'No Subjects Icon').css({
                                        'width': '240px', // Adjust size as needed
                                        'height': '240px',
                                        'margin-right': '8px', // Space between image and text
                                        'vertical-align': 'middle'
                                    })
                                ),
                                $('<p class ="action-text">nodata found</p>')
                            );
                        }
                        // $('#subject-list').on('click', '.individual-subject-list', function() {
                        //     // console.log("click");
                        //     $('.individual-subject-list').removeClass('active');
                        //     $(this).addClass('active'); 
                        //     create_date(yearId,sectionId ,examid); 
                        // });
                    }
                    else{
                        $('#subject-list').empty().append(
                            $('<div>').addClass('individual-subject-list').append(
                                $('<img>').attr('src', '<?= GLOBAL_PATH . '/images/svgs/no_data_found.svg' ?>').attr('alt', 'No Subjects Icon').css({
                                    'width': '240px', // Adjust size as needed
                                    'height': '240px',
                                    'margin-right': '8px', // Space between image and text
                                    'vertical-align': 'middle'
                                })
                            ),
                            $('<p class ="action-text">nodata found</p>')
                        );
                    }

                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const fetch_eaxm_time_table = (yearId, sectionId, examid) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= MODULES . '/faculty_student_examination/json/fetch_faculty_subject_with_date.php' ?>',
                    type: 'POST',
                    data: {
                        year_of_study_id: yearId,
                        section_id: sectionId,
                        exam_id: examid
                    },
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        console.log(response);

                        // Check if data exists and populate the timetable
                        if (response.data && response.data[0]) {
                            const data = response.data[0];
                            let $htmlContent = $('<div>').addClass('timetable-container');

                            // Create a table with the dashboard-table class
                            let $table = $('<table>').addClass(' portal-table table-bordered  ');
                            let $thead = $('<thead>').append(
                                $('<tr>').append(
                                    $('<th>').text('Date'),
                                    $('<th>').text('Subject Name'),
                                    $('<th>').text('Short Name'),
                                    $('<th>').text('Subject Code')
                                )
                            );
                            let $tbody = $('<tbody>');

                            // Iterate through the data and create rows
                            $.each(data, function(index, item) {
                                let $row = $('<tr>').append(
                                    $('<td>').text(item.exam_date),
                                    $('<td>').text(item.subject_name),
                                    $('<td>').text(item.subject_short_name),
                                    $('<td>').text(item.subject_code)
                                );
                                $tbody.append($row);
                            });

                            $table.append($thead, $tbody);
                            $htmlContent.append($table);

                            // Insert the timetable into the #time-table div
                            $('#time-table').empty().append($htmlContent);
                        } else {
                            // If no data is found, display a "No Data" message
                            $('#time-table').empty().append(
                                $('<div>').addClass('no-data-found text-center').append(
                                    $('<img>').attr('src', '<?= GLOBAL_PATH . '/images/svgs/no_data_found.svg' ?>').attr('alt', 'No Data Icon').css({
                                        'width': '240px',
                                        'height': '240px',
                                        'margin': '0 auto',
                                        'display': 'block'
                                    }),
                                    $('<p>').addClass('action-text').text('No exam timetable found')
                                )
                            );
                        }
                    },
                    error: function(error) {
                        reject(error);
                        $('#time-table').empty().append(
                            $('<div>').addClass('error-message text-center').append(
                                $('<p>').addClass('action-text').text('Error loading timetable')
                            )
                        );
                    }
                });
            });
        };

        // const fetch_eaxm_time_table = (yearId, sectionId, examid) => {
        //     return new Promise((resolve, reject) => {
        //         $.ajax({
        //             url: '<?= MODULES . '/faculty_student_examination/json/fetch_faculty_subject_with_date.php' ?>',
        //             type: 'POST',
        //             data: {
        //                 year_of_study_id: yearId,
        //                 section_id: sectionId,
        //                 exam_id: examid
        //             },
        //             headers: {
        //                 'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
        //                 'X-Requested-Path': window.location.pathname + window.location.search
        //             },
        //             success: function(response) {
        //                 response = JSON.parse(response);
        //                 console.log(response);
        //                 let slots_list = response.data[0];
        //                 console.log(slots_list);
        //                 const  data = response.data[0];
        //                 if (data && data.length > 0) {
        //             let $htmlContent = $('<div>').addClass('timetable-container');

        //             // Create a table for the timetable
        //             let $table = $('<table>').addClass('exam-timetable table table-bordered');
        //             let $thead = $('<thead>').append(
        //                 $('<tr>').append(
        //                     $('<th>').text('Date'),
        //                     $('<th>').text('Subject Name'),
        //                     $('<th>').text('Short Name'),
        //                     $('<th>').text('Subject Code'),
        //                     $('<th>').text('Faculty Subjects ID')
        //                 )
        //             );
        //             let $tbody = $('<tbody>');

        //             // Iterate through the data and create rows
        //             $.each(data, function(index, item) {
        //                 let $row = $('<tr>').append(
        //                     $('<td>').text(item.exam_date),
        //                     $('<td>').text(item.subject_name),
        //                     $('<td>').text(item.subject_short_name),
        //                     $('<td>').text(item.subject_code),
        //                     $('<td>').text(item.faculty_subjects_id)
        //                 );
        //                 $tbody.append($row);
        //             });

        //             $table.append($thead, $tbody);
        //             $htmlContent.append($table);

        //             // Insert the timetable into the #time-table div
        //             $('#time-table').empty().append($htmlContent);
        //         } else {
        //             // If no data is found, display a "No Data" message
        //             $('#time-table').empty().append(
        //                 $('<div>').addClass('no-data-found text-center').append(
        //                     $('<img>').attr('src', '<?= GLOBAL_PATH . '/images/svgs/no_data_found.svg' ?>').attr('alt', 'No Data Icon').css({
        //                         'width': '240px',
        //                         'height': '240px',
        //                         'margin': '0 auto',
        //                         'display': 'block'
        //                     }),
        //                     $('<p>').addClass('action-text').text('No exam timetable found')
        //                 )
        //             );
        //         }
        //                 resolve(slots_list);


        //             },
        //             error: function(error) {
        //                 reject(error);
        //             }
        //         });
        //     });
        // };

        const create_date = (yId, secid, examid, subid) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= MODULES . '/faculty_student_examination/json/fetch_faculty_exam_date.php' ?>',
                    type: 'POST',
                    data: {

                        year_of_study_id: yId,
                        section_id: secid,
                        exam_id: examid,
                        subjectid: subid
                    },
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        // let slots_list = response.data;
                        const data = response.data[0];
                        let fetch_data; // Declare fetch_data in the outer scope

                        if (response.data[1]) {
                            fetch_data = response.data[1]; // Assign value if condition is true
                        } else {
                            fetch_data = ""; // Assign empty string if condition is false
                        }

                        console.log(response);
                        console.log(data);
                        //  console.log(fetch_data[0]);

                        let $htmlContent = $('<div>')
                            .addClass('main-content-card addlist')
                            .attr('id', 'lists');

                        let $dateInput =
                            $('<div>').addClass('input-container date')
                            .append($('<input>').attr({
                                type: 'date',
                                class: 'bulmaCalendar',
                                id: 'examDate',
                                name: 'examDate',
                                placeholder: 'dd-MM-yyyy',
                                required: true,
                                'aria-required': 'true'
                            }))
                            .append($('<label>').addClass('input-label').attr('for', 'examDate').text('Exam Date'));

                        let $fromtimeInput = $('<div>').addClass('input-container date')
                            .append($('<input>').attr({
                                type: 'time',
                                class: 'bulmaCalendar',
                                id: 'examStartTime',
                                name: 'examStartTime',
                                placeholder: 'dd-MM-yyyy',
                                required: true,
                                'aria-required': 'true'
                            }))
                            .append($('<label>').addClass('input-label').attr('for', 'examStartTime').text('Start Time'));

                        let $totimeInput = $('<div>').addClass('input-container date')
                            .append($('<input>').attr({
                                type: 'time',
                                class: 'bulmaCalendar',
                                id: 'examEndTime',
                                placeholder: 'dd-MM-yyyy',
                                name: 'examEndTime',
                                required: true,
                                'aria-required': 'true'
                            }))
                            .append($('<label>').addClass('input-label').attr('for', 'examEndTime').text('End Time'));

                        let $dateContainer = $('<div>').addClass('col col-12 col-lg-12 col-md-12 col-sm-12 col-xs-12')
                            .append($dateInput);
                        let $fromContainer = $('<div>').addClass('col col-12 col-lg-12 col-md-12 col-sm-12 col-xs-12')
                            .append($fromtimeInput);
                        let $toContainer = $('<div>').addClass('col col-12 col-lg-12 col-md-12 col-sm-12 col-xs-12')
                            .append($totimeInput);


                        let $savbutton = $('<div>').addClass('save-button')
                            .append($('<button>').attr({
                                    type: 'submit',
                                    class: 'primary btn next-btn right text-center full-width',
                                    id: 'save-exam'
                                }).text('Save')
                                .attr('data-exam-id', examid)
                                .attr('data-year-id', yId)
                                .attr('sectionid', secid)
                            );;
                        let $savebutton = $('<div>').addClass('col col-12 col-lg-12 col-md-12 col-sm-12 col-xs-12')
                            .append($savbutton);

                        let $sessionInput = $('<div>').addClass('input-container dropdown-container')
                            .append($('<input>').attr({
                                type: 'text',
                                class: 'auto session-filter-dummy dropdown-input',
                                id: 'session-filter-dummy',
                                placeholder: ' ',
                                value: ''
                            }))
                            .append($('<label>').addClass('input-label').attr('for', 'session-filter-dummy').text('Select The Session'))
                            .append($('<input>').attr({
                                type: 'hidden',
                                name: 'session_filter',
                                class: 'session-filter',
                                id: 'session-filter'
                            }))
                            .append($('<span>').addClass('dropdown-arrow').html('&#8964;'))
                            .append($('<div>').addClass('dropdown-suggestions'));

                        let $sessionContainer = $('<div>').addClass('col col-12 col-lg-12 col-md-12 col-sm-12 col-xs-12')
                            .append($sessionInput);

                        let $row = $('<div>').addClass('row').append($dateContainer, $fromContainer, $toContainer, $sessionContainer, $savebutton);
                        let $form = $('<form>').addClass('exam-list-form').attr('id', 'exam-list-form').append($row);
                        $htmlContent.append($form);

                        $('#exam-list').empty().append($htmlContent);


                        if (fetch_data != "") {
                            console.log("success");
                            let examdate = fetch_data[0]['exam_date'];
                            let exam_end_time = fetch_data[0]['exam_end_time'];
                            let exam_start_time = fetch_data[0]['exam_start_time'];
                            let exam_session = fetch_data[0]['exam_session'];
                            $('#examDate').val(examdate);
                            $('#examStartTime').val(exam_start_time);
                            $('#examEndTime').val(exam_end_time);
                            $('#session-filter').val(exam_session);
                            if (exam_session == 1) {
                                $('#session-filter-dummy').val("Forenoon");
                            } else {
                                $('#session-filter-dummy').val("Afternoon");
                            }
                        }

                        $('#session-filter-dummy').on('click focus', async function() {

                            await fetch_session_call($(this));
                        });
                        $('#exam-list-form').submit(async function(event) {
                            event.preventDefault();
                            const formData = new FormData(this);
                            formData.append('exam_id', examid);
                            formData.append('yid', yId);
                            formData.append('secid', secid);
                            formData.append('subid', subid);

                            $.ajax({
                                type: 'POST',
                                url: '<?= MODULES . '/faculty_student_examination/ajax/faculty_assign_examination.php' ?>',
                                data: formData,
                                processData: false, // Important: Don't process the data
                                contentType: false, // Important: Don't set content type
                                headers: {
                                    'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                                    'X-Requested-Path': window.location.pathname + window.location.search
                                },
                                success: function(response) {
                                    response = JSON.parse(response);
                                    if (response.code == 200) {
                                        showToast(response.status, response.message);
                                    } else {
                                        showToast(response.status, response.message);
                                    }
                                },
                                error: function(error) {
                                    showToast('error', 'Something went wrong. Please try again later.');
                                }
                            });
                        });
                        resolve($htmlContent);


                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        // const insert_the_exam = (formData) => {  
        //        return new Promise((resolve, reject) => {
        //            $.ajax({
        //                type: 'POST',
        //                url: '<?= MODULES . '/faculty_student_examination/ajax/faculty_assign_examination.php' ?>',
        //                headers: {
        //                    'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
        //                },
        //                data: {
        //                    'location': window.location.href,
        //                    'formData': formData
        //                },
        //                success: function(response) {
        //                    response = JSON.parse(response);
        //                    if (response.code == 200) {
        //                        const data = response.data;
        //                        console.log(data);
        //                    } else {
        //                        showToast(response.status, response.message);
        //                    }
        //                    resolve();
        //                },
        //                error: function(jqXHR) {
        //                    const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
        //                    showToast('error', message);
        //                    reject(jqXHR);
        //                }
        //            });
        //        });
        //    };
        const fetch_session_call = (element) => {
            // Array of lateral entry options (No/Yes)
            const session = [{
                    title: "Forenoon",
                    value: 1, // SQL column: 'student_session' - refers to the source of knowledge (1=Friends or Family)
                },
                {
                    title: "Afternoon",
                    value: 2, // SQL column: 'student_session' - refers to the source of knowledge (2=Social Media)
                },

            ];

            // Assign the courses array to a variable (could be renamed if needed)
            const session_data = session;
            // Log the degrees array to check the structure
            // Get the sibling elements for displaying suggestions
            const suggestions = element.siblings(".dropdown-suggestions");
            const value = element.siblings("#session-filter");
            // Call the function to show the suggestions
            showDropdownLoading(element.siblings(".dropdown-suggestions"))
            showSuggestions(session_data, suggestions, value, element);
        };





        

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
        const load_individual_allocated_subject_list = (examid, page) => {
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
                            populate_subject_list(individual_faculty_subjects, examid, page);
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


        const populate_subject_list = (data, examid, page) => {

            // Clear the current content of the subject list
            $('#subject-list').empty();
            console.log(data);
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
                const params = {
                    selectedSubject: $(this).data('subject-id'),
                    selectedDept: $(this).data('dept-id'),
                    selectedYearOfStudyId: $(this).data('year-of-study-id'),
                    selectedSection: $(this).data('section-id'),
                    selectedSemDurationId: $(this).data('sem-duration-id')
                };
                $('.individual-subject-list').removeClass('active'); // Remove active from all
                $(this).addClass('active'); // Add active to clicked subject

                // load_faculty_select_subject_time_slot_popup(selectedSubject, selectedDept, selectedYearOfStudyId, selectedSection, selectedSemDurationId)
                fetch_student_details(params, examid, page);
            });
        }
        const fetch_exam_data = (element) => {
            console.log(element);
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_student_examination/json/fetch_exam_data.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token 
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'location': window.location.href,
                        'data': element
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const data = response.data[0];
                            console.log(data);
                            //                         [
                            //     {
                            //         "exam_id": 72,
                            //         "exam_group_id": 22,
                            //         "exam_group_name": "CIA I",
                            //         "exam_type_id": 327,
                            //         "exam_max_marks": "35.00",
                            //         "exam_min_marks": "75.00",
                            //         "exam_starting_date": "2025-03-08",
                            //         "exam_ending_date": "2025-03-15",
                            //         "exam_duration": "2.00",
                            //         "exam_status": 1,
                            //         "departments": [
                            //             {
                            //                 "dept_id": 1,
                            //                 "dept_title": "Computer Science and Engineering",
                            //                 "dept_short_name": "CSE",
                            //                 "exam_dep_status": 1
                            //             },
                            //             {
                            //                 "dept_id": 2,
                            //                 "dept_title": "Mechanical Engineering",
                            //                 "dept_short_name": "MECH",
                            //                 "exam_dep_status": 1
                            //             },
                            //             {
                            //                 "dept_id": 3,
                            //                 "dept_title": "Electrical and Electronics Engineering",
                            //                 "dept_short_name": "EEE",
                            //                 "exam_dep_status": 1
                            //             },
                            //             {
                            //                 "dept_id": 5,
                            //                 "dept_title": "Bio Medical Engineering",
                            //                 "dept_short_name": "BME",
                            //                 "exam_dep_status": 1
                            //             },
                            //             {
                            //                 "dept_id": 4,
                            //                 "dept_title": "Electronics and Communication Engineering",
                            //                 "dept_short_name": "ECE",
                            //                 "exam_dep_status": 1
                            //             },
                            //             {
                            //                 "dept_id": 6,
                            //                 "dept_title": "Science and Humanities",
                            //                 "dept_short_name": "S&H",
                            //                 "exam_dep_status": 1
                            //             }
                            //         ]
                            //     }
                            // ]
                //             const exam_group = [{
                //         "value": 22,
                //         "title": "Internal Theory"
                //     },
                //     {
                //         "value": 23,
                //         "title": "External Theory"
                //     },
                //     {
                //         "value": 24,
                //         "title": "Internal Practical"
                //     },
                //     {
                //         "value": 25,
                //         "title": "External Practical"
                //     }
                // ];
                exam_group_ids = "";
                if(data.exam_group_id == 22){
                    exam_group_ids = "Internal Theory";  
                }else if(data.exam_group_id == 23){
                    exam_group_ids = "External Theory";
                }else if(data.exam_group_id == 24){
                    exam_group_ids = "Internal Practical";
                }else if(data.exam_group_id == 25){
                    exam_group_ids = "External Practical";
                }
console.log(exam_group_ids);
                            $('#exam_id').val(data.exam_id);
                            $('#exam_group_id').val(data.exam_group_id);
                            $('#selected-exam-group-dummy').val(exam_group_ids);
                            $('#exam_type_id').val(data.exam_type_id);
                            $('#selected-exam-type-dummy').val(data.exam_group_name); // You might want to fetch the actual name
                            $('#exam_max_marks').val(data.exam_max_marks);
                            $('#exam_min_marks').val(data.exam_min_marks);
                            $('#examStartDate').val(data.exam_starting_date);
                            $('#examendDate').val(data.exam_ending_date);
                            $('#exam_duration').val(data.exam_duration);
                            $('#exam_status').val(data.exam_status); 
console.log(data.departments);
                             data.departments.forEach(dept => {
                                // Use createChip function to create and add the chip
                                createChip($('.selected-department').val(dept.dept_title), $('#selected-department-list-chips'), dept.dept_id);
                                $('.selected-department').val("")
                            });
                           
                           


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
        const fetch_student_details = (element, examid, page) => {

            console.log(element);
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_student_examination/json/fetch_faculty_student_name.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'location': window.location.href,
                        'data': element,
                        'examid': examid

                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        $('#student-list').empty();
                        if (response.code === 200) {
                            if (response.message == "No Year Of Study Found For Selected Subject.") {

                                showToast(response.status, response.message);
                            }
                            const data = response.data[0]; // First array of student data
                            const exam = response?.data?.[1]?.[0] || null; // Exam details
                            // console.table(data);
                            // console.table(exam);


                            // console.log("Exam:", exam);
                            // console.log("Data:", data);

                            if (!exam || !data) return; // Exit if critical data is missing
                            console.log(page);
                            if (page == 1) {
                                data.forEach(student => {
                                    const isReexamEnabled = student.total_marks !== null && student.total_marks < exam.exam_min_marks;
                                    // First block: editable inputs
                                    $('#student-list').append(`
            <tr>
                <td>${student.student_first_name}</td>
                <td>${student.student_reg_number}</td>
                <td>
                    <input type="text" 
                           name="marks[]" 
                           class="form-control marks-input" 
                           value="${student.total_marks ?? ''}" 
                           min="0" 
                           max="${exam.exam_max_marks}" 
                           data-regno="${student.student_reg_number}" 
                           placeholder="Enter marks">
                    <input type="hidden" name="student_id[]" value="${student.student_id}">
                    <input type="hidden" name="student_official_id[]" value="${student.student_official_id}">
                    <input type="hidden" name="examid[]" value="${examid}">
                    <input type="hidden" name="yearid[]" value="${element.selectedYearOfStudyId}">
                    <input type="hidden" name="sectionid[]" value="${element.selectedSection}">
                    <input type="hidden" name="subjectid[]" value="${element.selectedSubject}">
                    <input type="hidden" name="deptid[]" value="${element.selectedDept}">
                    <input type="hidden" name="semid[]" value="${element.selectedSemDurationId}">
                </td>
                <td>
                    <input type="text" 
                           name="reexam_marks[]" 
                           class="form-control marks-input" 
                           ${isReexamEnabled ? '' : 'disabled'} 
                           value="${student.reexam_total_marks ?? ''}" 
                           min="0" 
                           max="${exam.exam_max_marks}" 
                           data-regno="${student.student_reg_number}" 
                           placeholder="Enter Retest marks">
                </td>
            </tr>
        `);
                                });
                            } else {
                                data.forEach(student => {
                                    const showWarning = student.total_marks !== null && student.total_marks < exam.exam_min_marks;
                                    const showWarnings = student.reexam_total_marks !== null && student.reexam_total_marks < exam.exam_min_marks && student.reexam_total_marks != 0;
                                    $('#student-list').append(`
            <tr>
                <td>${student.student_first_name}</td>
                <td>${student.student_reg_number}</td>
                <td class="${showWarning ? 'error-background' : 'success-background'}">${student.total_marks ?? ''}</td>
                <td class="${showWarnings ? 'error-background' : ''}">${student.reexam_total_marks ?? ''}</td>
            </tr>
        `);
                                });
                            }
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

        const fetch_dept_list = (element) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/fetch_department_list.php' ?>',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        let dept_list = response.data;
                        const suggestions = element.siblings(".autocomplete-suggestions")
                        const value = element.siblings(".selected-department")
                        const dept_chips = getChipsValues($('#selected-department-list-chips'))
                        dept_list = dept_list.filter(slot => !dept_chips.includes(slot.title));
                        showSuggestions(dept_list, suggestions, value, element);
                        // const data = response.data



                        // data.forEach(slot => {
                        //     // Use createChip function to create and add the chip
                        //     createChip($('.selected-department').val(slot.title), $('#selected-department-list-chips'), slot.value);
                        //     $('.selected-department').val("")
                        // });
                        // const suggestions = element.siblings(".autocomplete-suggestions")
                        // const value = element.siblings(".selected-department")
                        // const slots_chips = getChipsValues($('#selected-department-list-chips'))
                        // slots_list = slots_list.filter(slot => !slots_chips.includes(slot.title));

                        // showSuggestions(slots_list, suggestions, value, element);
                        resolve(dept_list);
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };
        // const fetch_dept_list = (element) => {
        //     return new Promise((resolve, reject) => {
        //         $.ajax({
        //             url: '<?= GLOBAL_PATH . '/json/fetch_department_list.php' ?>',
        //             type: 'GET',
        //             headers: {
        //                 'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
        //                 'X-Requested-Path': window.location.pathname + window.location.search
        //             },


        //             success: function(response) {

        //                 response = JSON.parse(response);
        //                 if (response.code == 200) {
        //                     const subject_list = response.dropdown_data;

        //                     const list = response.data;
        //                     const suggestions = element.siblings(".dropdown-suggestions")
        //                     const value = element.siblings(".selected-department-filter")


        //                     showSuggestions(list, suggestions, value, element);
        //                     // $('.subject-dummy').on('blur', function() {
        //                     //     //settimeout function
        //                     //     setTimeout(() => {
        //                     //         const userInputValue = $('.subject-filter').val();
        //                     //         console.log(userInputValue);
        //                     //         const filteredList = list.filter(item => item.faculty_subjects_id == userInputValue);
        //                     //         $("#class-manager-year-of-study").text(filteredList[0]['year_of_study_title'])
        //                     //         $("#class-manager-academic-year").text(filteredList[0]['academic_year_title'])
        //                     //         $("#class-manager-Department").text(filteredList[0]['dept_short_name'])
        //                     //         $("#class-manager-Section").text(filteredList[0]['section_title'])
        //                     //         $("#subject_id").val(filteredList[0]['subject_id'])
        //                     //         $("#sem_duriation_id").val(filteredList[0]['sem_duration_id'])

        //                     //         console.log(filteredList);


        //                     //     }, 150);

        //                     //     // Log the value to the console

        //                     // });

        //                 } else {
        //                     showToast(response.status, response.message)
        //                 }
        //                 resolve(response);
        //             },
        //             error: function(error) {
        //                 reject(error);
        //             }
        //         });
        //     });
        // }
        const load_exam_list = (element, examid) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/fetch_general_title.php' ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'examid': examid
                    },

                    success: function(response) {

                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const subject_list = response.dropdown_data;

                            const list = response.data;
                            const suggestions = element.siblings(".dropdown-suggestions")
                            const value = element.siblings(".selected-exam-type-filter")


                            showSuggestions(list, suggestions, value, element);
                            // $('.subject-dummy').on('blur', function() {
                            //     //settimeout function
                            //     setTimeout(() => {
                            //         const userInputValue = $('.subject-filter').val();
                            //         console.log(userInputValue);
                            //         const filteredList = list.filter(item => item.faculty_subjects_id == userInputValue);
                            //         $("#class-manager-year-of-study").text(filteredList[0]['year_of_study_title'])
                            //         $("#class-manager-academic-year").text(filteredList[0]['academic_year_title'])
                            //         $("#class-manager-Department").text(filteredList[0]['dept_short_name'])
                            //         $("#class-manager-Section").text(filteredList[0]['section_title'])
                            //         $("#subject_id").val(filteredList[0]['subject_id'])
                            //         $("#sem_duriation_id").val(filteredList[0]['sem_duration_id'])

                            //         console.log(filteredList);


                            //     }, 150);

                            //     // Log the value to the console

                            // });

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
        const loadBgCard = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_examination/components/bg-card.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {

                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
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
        const loadexaminortable = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_examination/components/faculty_student_view_examinor_table.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {

                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('.content').html(response);
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
        const loadfacultytable = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_examination/components/faculty_student_view_faculty_table.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {

                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('.content').html(response);
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
        const load_examination_main_view_page = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_examination/components/faculty_student_add_examination.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('#add-student-examination').html(response);
                        $('#student-examination-list').empty();
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
        const loadeditpage = (element) => {
            return new Promise((resolve, reject) => {
                const params = {
                    exam_id: element.getAttribute('data-exam_id'),
                    exam_group_id: element.getAttribute('data-exam_group_id'),
                    exam_type_id: element.getAttribute('data-exam_type_id'),
                    exam_duration: element.getAttribute('data-exam_duration'),
                    exam_start_date: element.getAttribute('data-exam_start_date'),
                    exam_end_date: element.getAttribute('data-exam_end_date')
                };
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_examination/components/faculty_student_edit_examination.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: params,
                    success: function(response) {
                        $('#add-exam-popup').html(response);
                        // $('#student-examination-list').empty();
                        resolve(); // Resolve the promise
                        $('.popup-close-btn').on('click', function() {
                            $('#add-exam-popup').html('');
                        });
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        console.error('Error loading top navbar:', message);
                        reject(); // Reject the promise
                    },
                });
            });
        };
        const loaddeletepage = (element) => {
            return new Promise((resolve, reject) => {
                const params = {
                    exam_id: element.getAttribute('data-exam_id')
                };
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_examination/components/faculty_student_delete_examination.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: params,
                    success: function(response) {
                        $('#add-exam-popup').html(response);
                        // $('#student-examination-list').empty();
                        resolve(); // Resolve the promise
                        $('.popup-close-btn').on('click', function() {
                            $('#add-exam-popup').html('');
                        });
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        console.error('Error loading top navbar:', message);
                        reject(); // Reject the promise
                    },
                });
            });
        };

        const load_examination_fac_view_page = () => {
            updateUrl({
                route: 'faculty',
                action: 'view',
                type: 'overall'
            });
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_examination/components/faculty_student_view_examination.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('#add-student-examination').html(response);
                        $('#student-examination-list').empty();
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
        const load_examination_exam_view_page = () => {
            return new Promise((resolve, reject) => {
                updateUrl({
                    route: 'faculty',
                    action: 'view',
                    type: 'overall'
                });
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_examination/components/faculty_student_view_examination.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('#add-student-examination').html(response);
                        $('#student-examination-list').empty();
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



        const selected_exam_group = (element) => {
            return new Promise((resolve, reject) => {
                const exam_group = [{
                        "value": 22,
                        "title": "Internal Theory"
                    },
                    {
                        "value": 23,
                        "title": "External Theory"
                    },
                    {
                        "value": 24,
                        "title": "Internal Practical"
                    },
                    {
                        "value": 25,
                        "title": "External Practical"
                    }
                ];

                const suggestions = element.siblings(".dropdown-suggestions");
                const value = element.siblings(".selected-exam-group-filter");

                showSuggestions(exam_group, suggestions, value, element);
            });
        };


        const load_exam_management_table = (examGroupId, examTypeId, examStatus) => {
            $('#examTable').DataTable().destroy();
            $('#examTable').DataTable({
                "serverSide": true,
                "ajax": {
                    "url": "<?= MODULES . '/faculty_student_examination/json/fetch_faculty_examdata.php' ?>",

                    "type": "POST",
                    "data": {
                        "exam_group_id": examGroupId,
                        "exam_type_id": examTypeId,
                        "exam_status": examStatus,
                        "type": 1
                    }
                },
                "columns": [{
                        "data": "s_no",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "exam_type_name"
                    },
                    {
                        "data": "exam_group_name"
                    },
                    // {
                    //     "data": "department_name"
                    // },
                    {
                        "data": "exam_max_marks"
                    },
                    {
                        "data": "exam_min_marks"
                    },
                    {
                        "data": "exam_duration"
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
                }
            });

            $('.dt-layout-row .dt-layout-table').css('width', '100%');
            $('.dt-layout-table .dt-layout-cell').css('width', '100%');
            $('.dt-scroll-headInner').css('width', '100%');
            $('.dataTable').css('width', '100%');
        };
        const load_exam_faculty_table = (examGroupId, examTypeId, examStatus) => {
            $('#examTable').DataTable().destroy();
            $('#examTable').DataTable({
                "serverSide": true,
                "ajax": {
                    "url": "<?= MODULES . '/faculty_student_examination/json/fetch_faculty_regular_examdata.php' ?>",

                    "type": "POST",
                    "data": {
                        "exam_group_id": examGroupId,
                        "exam_type_id": examTypeId,
                        "exam_status": examStatus,
                        "type": 1
                    }
                },
                "columns": [{
                        "data": "s_no",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "exam_type_name"
                    },
                    {
                        "data": "exam_group_name"
                    },
                    {
                        "data": "department_name"
                    },
                    {
                        "data": "exam_max_marks"
                    },
                    {
                        "data": "exam_min_marks"
                    },
                    {
                        "data": "exam_duration"
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
                }
            });

            $('.dt-layout-row .dt-layout-table').css('width', '100%');
            $('.dt-layout-table .dt-layout-cell').css('width', '100%');
            $('.dt-scroll-headInner').css('width', '100%');
            $('.dataTable').css('width', '100%');
        };
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
