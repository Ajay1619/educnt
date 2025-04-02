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
        //  if ( !action && !route  ) {
        //                     await load_academic_calendar_main_view_page();
        //                     // await fetch_academic_calendar();
        //                 }
        //                 else if (action == "add" && route == "faculty" && !type) {
        //                     await load_event_sidebar();

        //                     await load_academic_calendar();
        //                     // await fetch_academic_calendar();
        //                 }

        //                 else if (action == "view" && route == "faculty" && !type) {

        //                     await load_academic_calendar_view();
        //                     // await fetch_academic_calendar();
        //                 }
        const fetch_events = (element) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/fetch_events.php' ?>',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const dept_list = response.data;
                            const suggestions = element.siblings(".dropdown-suggestions")
                            const value = element.siblings(".event-filter")
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
        const load_academic_calendar_event_popup = () => {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'GET',
            url: '<?= htmlspecialchars(MODULES . '/faculty_academic_calendar/components/academic_calendar_event_popup_view.php', ENT_QUOTES, 'UTF-8') ?>',
            headers: {
                'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
            },
            success: function (response) {
                // Inject popup content into the placeholder
                $('#academic-calendar-event-popup-view').html(response);

                // Show the popup and bind the close button
                $('#academic-calendar-event-popup-view').show();
                $('.popup-close-btn').on('click', function () {
                    $('#academic-calendar-event-popup-view').hide().html('');
                });
                resolve();
            },
            error: function (jqXHR) {
                const message =
                    jqXHR.status === 401
                        ? 'Unauthorized access. Please check your credentials.'
                        : 'An error occurred. Please try again.';
                showToast('error', message);
                reject();
            }
        });
    });
};
        const fetch_academic_calendar_view = (month,Year) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                     url: '<?= MODULES . '/faculty_academic_calendar/json/fetch_academic_calendar_view.php' ?>',
                    type: 'POST',
                    data: {
                        'Year' :Year,
                        'Month' :month
                    },
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        // console.log(response);
                        $('.cal').html(response);
                        // response = JSON.parse(response);
                        if (1==1) {
                        console.log("Heloo");



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
        const loadComponentsBasedOnURL = async () => {

            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action'); // e.g., 'add', 'edit'
            const route = urlParams.get('route'); // e.g., 'personal', 'faculty'
            const type = urlParams.get('type'); // e.g., 'personal', 'faculty' 
            const id = urlParams.get('id'); // e.g., 'personal', 'faculty' 
            if (action == 'add') {
                if (route == 'faculty' && type == 'bulk_upload' && !id) {
                    await load_academic_calendar_bulk_event_popup();
                }
                // else if (route == 'faculty' && !type && !id) {
                //     await load_add_academic_calendar_events();
                // } 
                else if (route == 'faculty' && type == 'individual_upload' && !id) {
                      await load_add_academic_calendar_events(); 
                }
                else if (route == 'faculty' && type == 'bulk_upload_preview' && !id) { 

                    updateUrl({
                        route: 'faculty',
                        action: 'view',
                        type: 'overall'
                    });
                    await load_academic_calendar_main_view_table();


                }
            } else if (action == 'view') { 

                if (route == 'faculty' && type == 'overall') {
                    await load_academic_calendar_main_view_table();
                } else if (route == 'faculty' && type == 'calendar') {

            // console.log(action,route,type);

                    await load_academic_calendar_view();
                } else if (route == 'faculty' && !type) {
                    updateUrl({
                        route: 'faculty',
                        action: 'view',
                        type: 'overall'
                    });
                    await loadComponentsBasedOnURL();
                }
            } else if (!action && !route) {
                updateUrl({
                    route: 'faculty',
                    action: 'view',
                    type: 'overall'
                });
                await loadComponentsBasedOnURL();
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
        const load_bg_card = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_academic_calendar/components/view_calendar/bg-card.php', ENT_QUOTES, 'UTF-8') ?>',

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
        const load_event_sidebar = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_academic_calendar/components/side_events.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {

                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#event_table').html(response);
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
        const load_academic_calendar = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_academic_calendar/components/academic_calendar.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {

                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#event_table').html(response); // html the response
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
        const load_academic_calendar_view = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_academic_calendar/components/academic_calendar_view.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {

                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#event_table').html(response); // html the response
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
        
        const load_add_academic_calendar_events = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_academic_calendar/components/view_calendar/faculty_add_academic_calendar_events.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {

                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#event_table').html(response); // html the response
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
        // const load_academic_calendar_main_view_page = () => {
        //     return new Promise((resolve, reject) => {
        //         $.ajax({
        //             type: 'GET',
        //             url: '<?= htmlspecialchars(MODULES . '/faculty_academic_calendar/components/view_calendar/academic_calendar_main_view_page.php', ENT_QUOTES, 'UTF-8') ?>',

        //             headers: {

        //                 'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
        //                 'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
        //             },
        //             success: function(response) {
        //                 $('#event_main').html(response); // html the response
        //                 resolve(); // Resolve the promise
        //             },

        //             error: function(jqXHR) {
        //                 const message = jqXHR.status === 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
        //                 showToast('error', message);
        //                 reject(); // Reject the promise
        //             },

        //         });
        //     });
        // };
        const load_academic_calendar_main_view_table = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_academic_calendar/components/view_calendar/academic_calendar_event_table_view.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {

                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#event_table').html(response); // html the response
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

        const load_academic_calendar_bulk_event_popup = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_academic_calendar/components/view_calendar/bulk_upload_event.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token

                    },
                    success: function(response) {
                        // Inject popup content into the placeholder
                        $('#academic-calendar-event-popup-view').html(response);
                        // Show the popup and bind the close button
                        $('#academic-calendar-event-popup-view').show();
                        $('.popup-close-btn').on('click', function() {
                            updateUrl({
                                route: 'faculty',
                                action: 'view',
                                type: 'overall'
                            });

                            $('#academic-calendar-event-popup-view').hide().html('');

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
        const change_event_status = (event_id, event_status) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_academic_calendar/ajax/event_change_status.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token // Secure CSRF token
                    },
                    data: {
                        'event_id': event_id,
                        'event_status': event_status
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        showToast(response.status, response.message)
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        if (jqXHR.status == 401) {
                            // Redirect to the custom 401 error page
                            window.location.href = '<?= htmlspecialchars(GLOBAL_PATH . '/components/error/401.php', ENT_QUOTES, 'UTF-8') ?>';
                        } else {
                            const message = 'An error occurred. Please try again.';
                            showToast('error', message);
                        }
                        reject(); // Reject the promise
                    },

                });
            });
        }
        const fetch_faculty_academic_calendar = (event) => {
            $('#event-table').DataTable().destroy();
            $('#event-table').DataTable({
                "serverSide": true,
                "ajax": {
                    "url": "<?= MODULES . '/faculty_academic_calendar/json/fetch_academic_calendar.php' ?>",
                    "type": "POST",
                    "headers": {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token

                    },
                    "data": {
                        "event_type": event
                    }
                },
                "columns": [{
                        "data": "s_no",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "event_name"
                    },
                    {
                        "data": "event_description"
                    },
                    {
                        "data": "event_start_date"
                    },
                    {
                        "data": "event_end_date"
                    },
                    {
                        "data": "event_type"
                    },
                    {
                        "data": "event_status"
                    }
                ],
                "columnDefs": [{
                    "targets": 2, // event_description column
                    "render": function(data, type, row) {
                        return `<div class="ellipsis-text" title="${data}">${data}</div>`;
                    }
                }],
                "scrollX": true,
                "language": {
                    "emptyTable": "No data available matching the selected criteria.",
                    "loadingRecords": table_loading
                }
            });
            $('.dt-layout-row .dt-layout-table').css('width', '100%');
            $('.dt-layout-table .dt-layout-cell').css('width', '100%');
            $('.dt-scroll-headInner').css('width', '100%');
            $('.dataTable').css('width', '100%');
        };


        const load_academic_calendar_bulk_event_preview_popup = (matched, unmatched, orgin) => {

            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_academic_calendar/components/view_calendar/bulk_upload_events_preview.php', ENT_QUOTES, 'UTF-8') ?>',
                    data: {
                        'matched': matched,
                        'unmatched': unmatched,
                        'orgin': orgin,
                    },
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        // Inject popup content into the placeholder

                        $('#academic-calendar-event-popup-view').empty();
                        $('#academic-calendar-event-popup-view').html(response);

                        // Show the popup and bind the close button
                        $('#academic-calendar-event-popup-view').show();
                        $('.popup-close-btn').on('click', function() {
                            $('#academic-calendar-event-popup-view').hide().html('');
                            updateUrl({
                                route: '',
                                action: '',
                                type: ''
                            });
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


        <?php
    } else {
        echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
        exit;
    }
