<?php
include_once('../../config/sparrow.php');

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
    <link rel="stylesheet" href="<?= PACKAGES . '/bulmacalendar/bulma-calendar.min.css' ?>">
    <section id="change-password-popup"></section>
    <section id="sem-manager-popup"></section>
    <div class="top-bar">
        <div class="hamburger" id="hamburger">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
        <div class="top-bar-content">
            <?php if ($logged_profile_status == 1 && $logged_role_id == 0) { ?>
                <div class="academic-calender">
                    <img src="<?= GLOBAL_PATH . '/images/svgs/academic-calendar.svg' ?>" alt="Notifications" class="academic-calender-icon">
                </div>

                <div class="notification">
                    <img src="<?= GLOBAL_PATH . '/images/svgs/bell.svg' ?>" alt="Notifications" class="notification-icon">
                </div>
            <?php }  ?>
            <div class="user-info" id="user-info">
                <div class="username"><?= $logged_first_name . ' ' . $logged_middle_name . ' ' . $logged_last_name . ' ' . $logged_initial ?></div>
                <div class="designation"><?= $logged_designation ?></div>
                <div class="designation"><?= $logged_dept_short_name ?></div>
            </div>

            <div class="avatar" id="avatar">
                <?php if (!empty($logged_profile_pic) && $logged_profile_pic !== null): ?>
                    <img src="<?= GLOBAL_PATH . '/uploads/faculty_profile_pic/' . $logged_profile_pic ?>" alt="Profile Picture" class="avatar-img">
                <?php else: ?>
                    <img src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Profile Picture" class="avatar-img">
                <?php endif; ?>
            </div>
            <img src="<?= GLOBAL_PATH . '/images/svgs/topbar down arrow.svg' ?>" alt="" id="dropdown-arrow">

            <!-- Dropdown menu -->
            <div class="dropdown-menu" id="dropdown-menu">
                <ul>

                    <?php if ($logged_profile_status === 1) {
                        $logged_role_id = isset($_SESSION['svcet_educnt_faculty_role_id']) ? $_SESSION['svcet_educnt_faculty_role_id'] : 1;
                        if ($logged_role_id == 8) {

                    ?>
                            <a href="<?= BASEPATH . '/faculty-profile?action=view&route=faculty' ?>">
                                <li><img src="<?= GLOBAL_PATH . '/images/svgs/topbar dropdown profile.svg' ?>" alt="Profile Icon"> Profile</li>
                            </a>
                        <?php } ?>
                        <li id="change-password"><img src="<?= GLOBAL_PATH . '/images/svgs/change password.svg' ?>" alt="Settings Icon"> Change Password</li>
                        <li id="routing" data-switch="<?= $routing == 'Faculty' ? 'Student' : 'Faculty' ?>">
                            <img src="<?= GLOBAL_PATH . '/images/svgs/topbar switch.svg' ?>" alt="Settings Icon">
                            Switch -
                            <?= $routing == 'Faculty' ? 'Student' : ($logged_role_id == 8 ? 'Personal' : 'Faculty') ?>
                        </li>
                        <?php if (in_array($logged_role_id, $secondary_roles) && $logged_role_id != 0) { ?>
                            <li id="sem-manager"><img src="<?= GLOBAL_PATH . '/images/svgs/sem manager.svg' ?>" alt="Settings Icon">Sem Manager</li>
                    <?php
                        }
                    }  ?>
                    <li id="class-manager"><img src="<?= GLOBAL_PATH . '/images/svgs/sem manager.svg' ?>" alt="Settings Icon">Class Manager</li>

                    <li id="logout"><img src="<?= GLOBAL_PATH . '/images/svgs/logout.svg' ?>" alt="Logout Icon"> Logout</li>
                </ul>
            </div>
        </div>
    </div>

    <div id="notification-bar"></div>
    <script src="<?= PACKAGES . '/bulmacalendar/bulma-calendar.min.js' ?>"></script>
    <script>
        removeurl = window.location.pathname;
        if (removeurl == "/educnt-svcet-faculty/faculty-student-admission") {
            $('#routing').remove('');
        }
        //change-password on click
        $('#change-password').on('click', async function() {
            try {
                showComponentLoading()
                await load_change_password();
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
        $('#sem-manager').on('click', async function() {
            try {
                await load_sem_manager_popup();
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
        $('#class-manager').on('click', async function() {
            try {
                if (<?= $logged_role_id ?> == 6) {
                    window.location.href = app_url + 'faculty-classes?action=view&route=faculty&type=subject_allocation'
                } else {
                    window.location.href = app_url + 'faculty-classes?action=edit&route=faculty&type=subject_allocation'
                }
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
        $('#hamburger').on('click', function() {
            $('.sidebar').toggleClass('show'); // Toggle sidebar visibility
            $(this).toggleClass('open'); // Toggle hamburger to X
        });
        $('.notification').on('click', async function() {
            await load_notification_bar();

            $('.notification-bar').removeClass('inactive').addClass('active');

            // Close the notification bar when close button is clicked
            $('#closeNotification').click(function() {
                $('.notification-bar').removeClass('active').addClass('inactive');
                $('#notification-bar').html("")
            });


        });

        // topbar dropdown
        // Toggle the dropdown menu and invert the arrow when clicking
        $('#user-info, #avatar, #dropdown-arrow').on('click', function(event) {
            event.stopPropagation(); // Prevent the click from bubbling up
            $('#dropdown-menu').toggle();
            $('#dropdown-arrow').toggleClass('inverted');
        });

        // Hide the dropdown when clicking outside
        $(document).on('click', function(event) {
            if (!$(event.target).closest('#dropdown-menu, #user-info, #avatar, #dropdown-arrow').length) {
                $('#dropdown-menu').hide();
                $('#dropdown-arrow').removeClass('inverted');
            }
        });

        //id=logout on click function
        $('#logout').on('click', async function() {
            try {
                await logout();
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
        //id=routing on click function
        $('#routing').on('click', async function() {
            try {
                await routing($(this).data('switch'));
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

        const load_change_password = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/change_password.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
                    },
                    success: function(response) {
                        $('#change-password-popup').html(response);
                        $('.popup-close-btn').on('click', function() {
                            $('#change-password-popup').html("");
                        })
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status === 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        }
        const load_sem_manager_popup = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/sem_manager_popup.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
                    },
                    success: function(response) {
                        $('#sem-manager-popup').html(response);
                        $('.popup-close-btn').on('click', function() {
                            $('#sem-manager-popup').html("");
                        })
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status === 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        }

        const populateSemList = (data) => {
            // Clear the container before populating
            $(".list-container").empty();

            // Iterate through the data array
            data.forEach(item => {
                // Decide the badge content dynamically
                let badgeText = item.sem_duration_status === 1 ? 'Freeze' : 'Begin';
                let badgeClass = item.sem_duration_status === 1 ? 'freeze' : 'begin';
                let sem_duration_start_date = item.sem_duration_start_date == null ? 'Not set' : item.sem_duration_start_date;
                // Create the HTML structure dynamically
                const listItem = `
                    <div class="list-item">
                        <div class="list-title">${item.sem_title} Sem</div>
                        <div class="list-desc">${sem_duration_start_date}</div>
                        <div class="list-badge ${badgeClass}" data-sem-duration-id="${item.duration_id}" data-year-id="${item.year_id}" data-sem-id="${item.sem_id}">${badgeText}</div>
                    </div>
                `;

                // Append to the container
                $(".list-container").append(listItem);
            });

            // Use event delegation to handle clicks
            $(".list-container").off("click", ".freeze").on("click", ".freeze", async function() {
                try {
                    showComponentLoading();
                    const sem_duration_id = $(this).data("sem-duration-id");
                    const year_of_study_id = $(this).data("year-id");
                    const sem_id = $(this).data("sem-id");
                    await load_freeze_sem_popup(sem_duration_id, year_of_study_id, sem_id);
                } catch (error) {
                    const errorMessage = error.message || "An error occurred while loading the page.";
                    await insert_error_log(errorMessage);
                    await load_error_popup();
                    console.error("An error occurred while loading:", error);
                } finally {
                    setTimeout(function() {
                        hideComponentLoading(); // Delay hiding loading by 1 second
                    }, 100);
                }
            });

            $(".list-container").off("click", ".begin").on("click", ".begin", async function() {
                try {
                    showComponentLoading();
                    const sem_duration_id = $(this).data("sem-duration-id");
                    const year_of_study_id = $(this).data("year-id");
                    const sem_id = $(this).data("sem-id");
                    await load_begin_sem_popup(sem_duration_id, year_of_study_id, sem_id);
                } catch (error) {
                    const errorMessage = error.message || "An error occurred while loading the page.";
                    await insert_error_log(errorMessage);
                    await load_error_popup();
                    console.error("An error occurred while loading:", error);
                } finally {
                    setTimeout(function() {
                        hideComponentLoading(); // Delay hiding loading by 1 second
                    }, 100);
                }
            });
        };


        // Function to load and render data
        const load_sem_list = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/fetch_dept_sem_list.php' ?>',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code === 200) {
                            const data = response.data;
                            populateSemList(data); // Call the function to populate the list
                        } else {
                            showToast(response.status, response.message);
                        }
                        resolve(response);
                    },
                    error: function(error) {
                        console.error("Error fetching semester list:", error);
                        reject(error);
                    }
                });
            });
        };

        const load_freeze_sem_popup = (sem_duration_id, year_of_study_id, sem_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/freeze_sem_popup.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
                    },
                    data: {
                        'sem_duration_id': sem_duration_id,
                        'year_of_study_id': year_of_study_id,
                        'sem_id': sem_id
                    },
                    success: function(response) {
                        $('#sem-manager-popup').html(response);
                        $('.popup-close-btn').on('click', function() {
                            $('#sem-manager-popup').html("");
                        })
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status === 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        }
        const load_begin_sem_popup = (sem_duration_id, year_of_study_id, sem_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/begin_sem_popup.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
                    },
                    data: {
                        'sem_duration_id': sem_duration_id,
                        'year_of_study_id': year_of_study_id,
                        'sem_id': sem_id
                    },
                    success: function(response) {
                        $('#sem-manager-popup').html(response);
                        $('.popup-close-btn').on('click', function() {
                            $('#sem-manager-popup').html("");
                        })
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status === 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        }
        const load_notification_bar = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/notification_bar.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
                    },
                    success: function(response) {
                        $('#notification-bar').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status === 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        };

        const routing = (route) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/ajax/route.php', ENT_QUOTES, 'UTF-8') ?>',
                    data: {
                        'route': route
                    },
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        const data = response.data
                        const urlParams = new URLSearchParams(window.location.search);
                        const action = urlParams.get('action'); // e.g., 'add', 'edit'
                        const route = urlParams.get('route'); // e.g., 'personal', 'faculty'
                        const type = urlParams.get('type'); // e.g., 'personal', 'faculty'
                        const tab = urlParams.get('tab'); // assuming `tab` is also part of your URL parameters

                        // Initialize params object only with non-empty values
                        const params = {};
                        if (action) params.action = action;
                        if (route) params.route = data.toLowerCase();
                        if (type) params.type = type;
                        if (tab) params.tab = tab;

                        // Construct the query string only with the non-empty parameters
                        const queryString = Object.keys(params)
                            .map(key => `${key}=${encodeURIComponent(params[key])}`)
                            .join('&');


                        // Construct the new URL only if there's a query string
                        const newUrl = queryString ?
                            `${window.location.origin}${window.location.pathname}?${queryString}` :
                            `${window.location.origin}${window.location.pathname}`;

                        // Use pushState to set the new URL
                        window.history.pushState(params, '', newUrl);

                        window.location.href = newUrl;
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status === 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        };

        const begin_sem_form_submit = (duration_id, year_of_study_id, sem_id) => {
            return new Promise((resolve, reject) => {
                var location_href = window.location.href;
                const sem_begin_date = $('#sem-begin-date').val();
                const sem_duration_id = duration_id;
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/ajax/begin_sem_form.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
                    },
                    data: {
                        'sem_duration_id': sem_duration_id,
                        'sem_begin_date': sem_begin_date,
                        'year_of_study_id': year_of_study_id,
                        'sem_id': sem_id,
                        'location_href': location_href
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        if (response.code == 200) {
                            showToast(response.status, response.message);
                            $("#sem-manager-popup").empty();
                        } else {
                            showToast(response.status, response.message);
                        }
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status === 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        }

        const freeze_sem_form_submit = (duration_id, year_of_study_id, sem_id) => {
            return new Promise((resolve, reject) => {
                var location_href = window.location.href;
                const sem_freeze_date = $('#sem-freeze-date').val();
                const sem_duration_id = duration_id;
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/ajax/freeze_sem_form.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
                    },
                    data: {
                        'sem_duration_id': sem_duration_id,
                        'sem_freeze_date': sem_freeze_date,
                        'year_of_study_id': year_of_study_id,
                        'sem_id': sem_id,
                        'location_href': location_href
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        if (response.code == 200) {
                            showToast(response.status, response.message);
                            $("#sem-manager-popup").empty();
                        } else {
                            showToast(response.status, response.message);
                        }
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status === 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        }

        const logout = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_login/ajax/logout.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
                    },
                    success: function(response) {
                        response = JSON.parse(response)
                        if (response.code === 200) {
                            setTimeout(() => {
                                $('main').fadeOut(300)
                            }, 500);
                            setTimeout(() => {
                                $('#footer').fadeOut(300)
                            }, 500);
                            setTimeout(() => {
                                $('.top-bar').fadeOut(300)
                            }, 1000);
                            setTimeout(() => {
                                $('.sidebar').fadeOut(300)
                            }, 1500);
                            setTimeout(() => {
                                window.location.href = '<?= BASEPATH ?>';
                            }, 2000);
                        } else {
                            showToast('error', response.message);
                        }
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status === 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
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