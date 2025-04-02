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
        const view_overall_student_achievements = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_achievements/components/student/overall_achievements.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },

                    success: function(response) {
                        $('#achievements-fetch').html(response);
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
        const logged_role_id = <?= $logged_role_id ?>;
        const encrypt_id = '<?= encrypt_data($logged_user_id) ?>';
        
        const loadComponentsBasedOnURL = async (ids) => {
            
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action'); // e.g., 'add', 'edit'
            const route = urlParams.get('route'); // e.g., 'personal', 'faculty'
            const type = urlParams.get('type'); // e.g., 'personal', 'faculty' 
            const id = urlParams.get('id'); // e.g., 'personal', 'faculty' 

            if (!action) {
                // No action specified, load dashboard profile by default
                await load_dashboard_profile();
            } else if (action == 'add' && route == 'faculty' && !type) {
                await create_faculty_Achievements("?action=add");
            } else if (action == 'view' && route == 'faculty' && !type) {

                if (<?= json_encode($main_roles) ?>.includes(logged_role_id)) {
                    await table_faculty_Achievements();
                } else {
                    
                    view_faculty_Achievements(encrypt_id);
                }


            } else if (action == 'view' && route == 'faculty' && type == 'overall' && !id) { 
                await table_faculty_Achievements();
            }
            else if (action == 'view' && route == 'faculty' && type == 'overall' && id) {
               await loadBgCard ();
                await view_faculty_Achievements(id);
            } else if (action == 'edit') {
                await logout();
            } else if (action == 'add' && route == 'student' && !type) {
                await create_student_Achievements("?action=add");
            } else if (action == 'view' && route == 'student' && !type) {

                await table_student_achievement();

            } else if (action == 'view' && route == 'student' && type == 'overall') {
                await view_overall_student_achievements();
                console.log("hello");
            } else if (action == 'edit') {
                await logout();
            } else {}

        };

        function sendAjaxRequest(url, formData, csrfToken) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    processData: false, // Prevent jQuery from processing data
                    contentType: false, // Prevent jQuery from setting content-type header
                    success: function(response) {
                        try {
                            resolve(JSON.parse(response));
                        } catch (error) {
                            reject(error);
                        }
                    },
                    error: function(xhr, status, error) {
                        reject(error);
                    }
                });
            });
        }

        const achievementeditPage = (roleId) => {
            showComponentLoading();
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_achievements/components/faculty/edit_achievements.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token
                    },
                    data: {
                        roleId: roleId
                    }, // Send roleId to the server
                    success: function(response) {
                        $('#faculty-achievement-popup').html(response); // Load response into the edit element
                        hideComponentLoading();
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        // Handle different error messages based on the status code
                        const message = jqXHR.status == 401 ?
                            'Unauthorized access. Please check your credentials.' :
                            'An error occurred. Please try again.';
                        showToast('error', message); // Show error message
                        reject(); // Reject the promise
                    }
                });
            });
        };


        const edit_fetch_single_achievement = (achievement_single_id) => {
            $.ajax({
                type: 'POST',
                url: '<?= htmlspecialchars(MODULES . '/faculty_achievements/json/fetch_single_achievement.php', ENT_QUOTES, 'UTF-8') ?>',
                data: {
                    achievement_id: achievement_single_id // Send achievement ID to the server
                },
                headers: {
                    'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.code == 200) {
                        const data = response.data[0]; // Assuming response.data is an array with one object


                        // Populate form fields with fetched data
                        $('#achievement-edit-dummy').val(data.achievement_type_title);
                        $('#achievement-edit').val(data.achievement_type);
                        $('#topic').val(data.achievement_title);
                        $('#date-of-achievements').val(data.achievement_date);
                        $('#venue').val(data.achievement_venue);
                        $('#achievement-id').val(data.faculty_achievements_id);

                        // Show the document name if it exists
                        // if (data.achievement_document) {
                        //     const fileLabel = document.querySelector("label[for='file-upload']");
                        //     fileLabel.textContent = data.achievement_document; // Display file name in label
                        // }

                        // Open the popup overlay
                        $('.popup-overlay').show();

                    } else {
                        showToast(response.status, response.message);
                    }
                },
                error: function() {
                    showToast('error', 'Something went wrong. Please try again later.');
                }
            });
        };



        // Function to update the heading based on the action and last part of the URL path
        const callAction = (element) => {
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action'); // e.g., 'add', 'edit'

            // Get the last part of the URL path
            const pathArray = window.location.pathname.split('/');
            const lastPath = pathArray[pathArray.length - 1];
            const routing_link = lastPath + window.location.search
            fetch_bg_card_title(routing_link);
            // Capitalize the first letter of the action and format the title
            if (action) {
                if (action == 'edit') {
                    $('.bg-card-edit-button').hide();
                    $('.bg-card-view-button').show();
                    $('.bg-card-add-button').show();
                    $('.bg-card-filter').hide();
                    $('.full-width-hr').hide();

                } else if (action == 'view') {
                    $('.bg-card-edit-button').show();
                    $('.bg-card-view-button').hide();
                    $('.bg-card-add-button').show();
                    $('.bg-card-filter').show();
                    $('.full-width-hr').show();
                } else if (action == 'add') {
                    $('.bg-card-add-button').hide();
                    $('.bg-card-edit-button').show();
                    $('.bg-card-view-button').show();
                    $('.full-width-hr').hide();
                    $('.bg-card-filter').hide();

                }
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
                            const value = element.siblings(".faculty-dept")
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

        const fetchFacultyAchivementData = (achievement_id, id) => {  
            console.log(id);
            $.ajax({
                type: 'POST',
                data: {
                    'achievement_id': achievement_id,
                    'id': id,
                },
                url: '<?= MODULES . '/faculty_achievements/json/faculty_fetch_achievements.php' ?>',
                headers: {
                    'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.code == 200) {
                        const data = response.data;
                        console.log(response.data);
                        if (!data) {
                            $('#Achievements_container').empty();
                            const achievementHTML = `
                            <div class="main-content-card">
                              <div class=" action-box" id="faculty-achievement-list">
                    <div class="action-title">Your Achivements Panel</div>
                    <div class="achivement">
                        <p class="action-text">
                            No Achievements Found!🔍
                        </p>
                        <div class="action-hint">
                          "No trophies on the shelf yet, but the shelf is ready! 🏆🚀"
                        </div>
                    </div>
                </div>
                </div>
                            
                            
                            
                            
                            `;
                            $('#Achievements_container').html(achievementHTML);
                        } else {
                            // Clear existing achievements
                            $('#Achievements_container').empty();

                            // Loop through each achievement and generate HTML

                            data.forEach((achievement) => {
                                const achievementHTML = `
                                 <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                    <div class="card ">
                                        <div class="row">
                                                <div class="col col-10 col-lg-10 col-md-10 col-sm-6 col-xs-6">
                                                    <div id="achievement-title">
                                                        <div class="section-header-title text-left">${achievement.achievement_type_title || 'Achievement'}</div>
                                                    </div>
                                                </div>
                                                <div class="col col-2 col-lg-2 col-md-2 col-sm-6 col-xs-6">
                                                    <div class="edit-icon" data-popup-role-id="${achievement.faculty_achievements_id}">
                                                        <img src="<?= GLOBAL_PATH . '/images/svgs/sidenavbar_icons/old_icons/edit.svg' ?>" data-popup-role-id="${achievement.faculty_achievements_id}" alt="Edit"  class="edit-popup text-right" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="invisible-card tooltip tooltip-top" >
                                                <div id="achievement-heading">
                                                    <div class="section-header-title ">${achievement.achievement_title}</div>
                                                    <span class="tooltip-text" id="tooltip-achievements">
                                                        <strong>${achievement.achievement_title}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="file-section" id="file-section">
                                                <div class="file-icon">
                                                    <img id="file-icon" src="<?= GLOBAL_PATH . '/images/svgs/application_icons/pdfs.svg' ?>" alt="PDF" />
                                                </div>
                                                <div class="file-actions">
                                                    <a id="preview" href="<?= GLOBAL_PATH . '/uploads/faculty_achievements/' ?>${achievement.achievement_document}" target="_blank">
                                                        <img src="<?= GLOBAL_PATH . '/images/svgs/application_icons/preview.svg' ?>" alt="Preview" />
                                                    </a>
                                                    <a id="download" href="<?= GLOBAL_PATH . '/uploads/faculty_achievements/' ?>${achievement.achievement_document}" download>
                                                        <img src="<?= GLOBAL_PATH . '/images/svgs/application_icons/download.svg' ?>" alt="Download" />
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12  committee-section">
                                                    <div class="title">Conducted On</div>
                                                    <div class="value">${achievement.achievement_date}</div>
                                                </div>
                                                <div class="col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12  committee-section">
                                                    <div class="title">Venue</div>
                                                    <div class="value">${achievement.achievement_venue}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    `;
                                $('#Achievements_container').append(achievementHTML);
                            });
                            $('.edit-icon ').on('click', function() {
                                const roleId = $(this).data('popup-role-id');
                                achievementeditPage(roleId);
                            });
                            // Attach click event to dynamically added edit buttons
                        }


                    } else {
                        showToast(response.status, response.message);
                    }
                },
                error: function() {
                    showToast('error', 'Something went wrong. Please try again later.');
                }
            });
        };
        const view_individual_faculty_achivements = async(facultyid) => {
            console.log(facultyid);
            try {
            updateUrl({
                            route: 'faculty',
                            action: 'view',
                            type: 'overall',
                            id: facultyid
                        });
                          await loadComponentsBasedOnURL(facultyid);
                    } catch (error) {
                        console.error('Error loading Add Event popup:', error);
                    }

        }
        const load_faculty_overall_achievements_table = (faculty, department) => { 
            $('#achievementTable').DataTable().destroy()
            $('#achievementTable').DataTable({
                "serverSide": true,
                "ajax": {
                    "url": "<?= MODULES . '/faculty_achievements/json/faculty_fetch_table_achievement.php' ?>",
                    "type": "POST",
                    "data": { 
                        "department": department,
                        "type": 1
                    }
                },
                "columns": [{
                        "data": "s_no",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "faculty_first_name"
                    },
                    {
                        "data": "dept_short_name"
                    },
                    {
                        "data": "designation"
                    },
                    {
                        "data": "achievement_count"
                    }, 
                    {
                        "data": "action",
                        "orderable": false,
                        "searchable": false
                    }
                ],
                "scrollX": true,
                "language": {
                    "emptyTable": "No data available matching the selected criteria.",
                    "loadingRecords": table_loading
                },
            });
            $('.dt-layout-row .dt-layout-table').css('width', '100%');
            $('.dt-layout-table .dt-layout-cell').css('width', '100%');
            $('.dt-scroll-headInner').css('width', '100%');
            $('.dataTable').css('width', '100%');
        }
        const load_student_overall_achievements_table = (faculty, achievement) => {
            $('#table').DataTable().destroy()
            $('#table').DataTable({
                "serverSide": true,
                "ajax": {
                    "url": "<?= MODULES . '/faculty_achievements/json/faculty_fetch_student_table_achievement.php' ?>",
                    "type": "POST",
                    "data": {
                        "faculty": faculty,
                        "achievement": achievement
                    }
                },
                "columns": [{
                        "data": "sl_no",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "achievement_title"
                    },
                    {
                        "data": "achievement_date"
                    },
                    {
                        "data": "achievement_venue"
                    },
                    {
                        "data": "student_name"
                    },
                    {
                        "data": "department_name"
                    },
                    {
                        "data": "action",
                        "orderable": false,
                        "searchable": false
                    }
                ],
                "scrollX": true,
                "language": {
                    "emptyTable": "No data available matching the selected criteria.",
                    "loadingRecords": table_loading
                },
            });
            $('.dt-layout-row .dt-layout-table').css('width', '100%');
            $('.dt-layout-table .dt-layout-cell').css('width', '100%');
            $('.dt-scroll-headInner').css('width', '100%');
            $('.dataTable').css('width', '100%');
        }
        const loadSidebar = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/sidebar.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token
                    },
                    success: function(response) {
                        $('#sidebar').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        if (jqXHR.status == 404) {
                            // Redirect to the custom 401 error page
                            window.location.href = '<?= htmlspecialchars(GLOBAL_PATH . '/components/error/404.php', ENT_QUOTES, 'UTF-8') ?>';
                        } else {
                            const message = 'An error occurred. Please try again.';
                            showToast('error', message);
                        }
                        reject(); // Reject the promise
                    }
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
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token
                    },
                    success: function(response) {
                        $('#topbar').html(response);
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
                    }
                });
            });
        };

        const loadBgCard = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_achievements/components/bg-card.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token
                    },
                    success: function(response) {
                        $('#bg-card').html(response);
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
                    }
                });
            });
        };

        const view_faculty_Achievements = (id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_achievements/components/faculty/view_achievements.php', ENT_QUOTES, 'UTF-8') ?>',
                    data: {
                        'id' : id
                    },
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token            // Secure CSRF token
                    },
                    success: function(response) {
                        $('#achievements-fetch').html(response);
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
                    }
                });
            });
        };

        const create_faculty_Achievements = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_achievements/components/faculty/create_achievements.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#achievements-fetch').html(response);
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
                    }
                });
            });
        };


        const load_dashboard_profile = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_achievements/components/dashboard/dashboard_profile.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },

                    success: function(response) {
                        $('#achievements-fetch').html(response);
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
        const fetch_achivements = (element) => { // Renamed parameter from `this` to `element`
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/fetch_achievement.php' ?>',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const achievement = response.data;
                            showSuggestions(achievement, $('#achievement-suggestions'), $('#achievement'), element);
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
        const fetch__create_achivements = (element) => { // Renamed parameter from `this` to `element`
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/fetch_achievement.php' ?>',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const achievement = response.data;
                            showSuggestions(achievement, $('#achievement-suggestions-create'), $('#achievement-create'), element);
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
        const fetch_edit_achivements = (element) => { // Renamed parameter from `this` to `element`
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/fetch_achievement.php' ?>',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const achievement = response.data;
                            showSuggestions(achievement, $('#achievement-suggestions-edit'), $('#achievement-edit'), element);
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

        const loadBreadcrumbs = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/breadcrumbs.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#breadcrumbs').html(response);
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
        const table_faculty_Achievements = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_achievements/components/faculty/overall_achievements.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },

                    success: function(response) {
                        $('#achievements-fetch').html(response);
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
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>