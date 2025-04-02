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
        let student_name_list = [];
        const load_main_components = async () => {
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action');
            const route = urlParams.get('route');
            const type = urlParams.get('type');
            const tab = urlParams.get('tab');
            // Get the last part of the URL path
            const pathArray = window.location.pathname.split('/');
            const lastPath = pathArray[pathArray.length - 1];
            const routing_link = lastPath + window.location.search;
            fetch_bg_card_title(routing_link);

            $('.bg-card-filter').hide();
            $('.full-width-hr').hide();
            switch (route) {
                case "faculty":
                    switch (type) {
                        case "wallet":
                            await load_student_wallet();

                            $('#faculty-student-smartpay-button').show();
                            $('#faculty-student-feescope-button').show();
                            $('#faculty-student-fees-wallet-button').hide();
                            $('#faculty-student-fees-fines-button').show();
                            break;
                        case "fine":
                            await load_student_fine();

                            $('#faculty-student-smartpay-button').show();
                            $('#faculty-student-feescope-button').show();
                            $('#faculty-student-fees-wallet-button').show();
                            $('#faculty-student-fees-fines-button').hide();
                            break;
                        case "smartpay":
                            await load_student_smartpay_form();

                            $('#faculty-student-smartpay-button').hide();
                            $('#faculty-student-feescope-button').show();
                            $('#faculty-student-fees-wallet-button').show();
                            $('#faculty-student-fees-fines-button').show();


                            break;
                        case "overall":
                            await load_student_fees_scope();

                            $('#faculty-student-smartpay-button').show();
                            $('#faculty-student-feescope-button').hide();
                            $('#faculty-student-fees-wallet-button').show();
                            $('#faculty-student-fees-fines-button').show();

                            $('.bg-card-filter').show();
                            $('.full-width-hr').show();
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
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/components/bg-card.php', ENT_QUOTES, 'UTF-8') ?>',

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
        const load_student_smartpay_form = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/components/student_smartpay_form.php', ENT_QUOTES, 'UTF-8') ?>',

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
        const load_student_fees_scope = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/components/student_feescope.php', ENT_QUOTES, 'UTF-8') ?>',

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
        const load_student_admission_fees_edit = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/components/student_fees_details_edit.php', ENT_QUOTES, 'UTF-8') ?>',

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
        const load_student_wallet_credit = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/components/faculty_student_wallet_credit.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('#wallet-contents').html(response);

                        $('.bg-card-filter').hide();
                        $('.full-width-hr').hide();
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
        const load_student_wallet_debit = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/components/faculty_student_wallet_debit.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('#wallet-contents').html(response);

                        $('.bg-card-filter').hide();
                        $('.full-width-hr').hide();
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
        const load_faculty_wallet_credit_bulk_form = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/components/faculty_wallet_credit_bulk_form.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('#wallet-credit-contents').html(response);

                        $('.bg-card-filter').hide();
                        $('.full-width-hr').hide();
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
        const load_faculty_wallet_debit_bulk_form = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/components/faculty_wallet_debit_bulk_form.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('#wallet-debit-contents').html(response);

                        $('.bg-card-filter').hide();
                        $('.full-width-hr').hide();
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
        const load_faculty_wallet_credit_single_form = (student_wallet_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/components/faculty_wallet_credit_single_form.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'student_wallet_id': student_wallet_id
                    },
                    success: function(response) {
                        if ($('#wallet-credit-contents').length) {
                            $('#wallet-credit-contents').html(response);
                        } else {
                            $('#wallet-edit').html(response);
                        }


                        $('.bg-card-filter').hide();
                        $('.full-width-hr').hide();
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

        const load_faculty_wallet_debit_single_form = (student_wallet_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/components/faculty_wallet_debit_single_form.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'student_wallet_id': student_wallet_id
                    },
                    success: function(response) {
                        if ($('#wallet-debit-contents').length) {
                            $('#wallet-debit-contents').html(response);
                        } else {
                            $('#wallet-edit').html(response);
                        }
                        $('.bg-card-filter').hide();
                        $('.full-width-hr').hide();
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

        const load_faculty_wallet_transactions = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/components/faculty_wallet_transactions.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('#wallet-contents').html(response);

                        $('.bg-card-filter').show();
                        $('.full-width-hr').show();
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

        const load_student_fine = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/components/faculty_student_fine.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('#main-components').html(response);

                        $('.bg-card-filter').hide();
                        $('.full-width-hr').hide();
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

        const load_student_charge_single_fine = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/components/faculty_student_charge_single_fine.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('#charge-fine-contents').html(response);

                        $('.bg-card-filter').hide();
                        $('.full-width-hr').hide();
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

        const load_student_charge_bulk_fine = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/components/faculty_student_charge_bulk_fine.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('#charge-fine-contents').html(response);

                        $('.bg-card-filter').hide();
                        $('.full-width-hr').hide();
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

        const load_student_fine_log_book = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/components/faculty_student_fine_log_book.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    success: function(response) {
                        $('#fine-contents').html(response);

                        $('.bg-card-filter').show();
                        $('.full-width-hr').show();
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
        const load_student_wallet = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/components/faculty_student_wallet.php', ENT_QUOTES, 'UTF-8') ?>',

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
        const load_individual_student_fees_view_table = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/components/student_individual_fees_details.php', ENT_QUOTES, 'UTF-8') ?>',

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

        const fetch_student_name_list = (element, dept_id, year_of_study_id, section_id, group_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/json/fetch_student_name_list.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    data: {
                        'faculty_dept_id': dept_id,
                        'year_of_study_id': year_of_study_id,
                        'section_id': section_id,
                        'group_id': group_id

                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            student_name_list = response.data;
                            const suggestions = element.siblings(".autocomplete-suggestions")
                            const value = element.siblings(".student-id")

                            showSuggestions(student_name_list, suggestions, value, element);
                            resolve(student_name_list);
                        } else {
                            showToast(response.status, response.message);
                            reject(response);
                        }
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const load_wallet_transaction_confirm = (student_wallet_id, wallet_action, confirmation_type) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/components/load_wallet_transaction_confirm_popup.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'student_wallet_id': student_wallet_id,
                        'wallet_action': wallet_action,
                        'confirmation_type': confirmation_type
                    },
                    success: function(response) {
                        $("#student-fees-popup").html(response);
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const student_wallet_single_form_submit = (data) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/ajax/student_wallet_single_form_submit.php', ENT_QUOTES, 'UTF-8') ?>',
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
                            setTimeout(function() {
                                location.reload(); // Delay hiding loading by 1 second
                            }, 500)
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

        const confirm_wallet_transaction = (student_wallet_id, confirmation_type) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/ajax/student_wallet_single_form_submit.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'student_wallet_id': student_wallet_id,
                        'wallet_status': confirmation_type,
                        'p_type': 2
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            showToast(response.status, response.message);
                            if ($(".section-filter").val() != 0 && $(".section-filter").val() != null && $(".section-filter").val() != undefined) {
                                load_student_wallet_transactions_table($(".dept-filter").val(), $(".year-of-study-filter").val(), $(".section-filter").val());
                            } else {
                                $("#wallet-transactions-table").hide();
                            }
                            $("#student-fees-popup").empty();
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

        const load_view_wallet_transaction = (student_wallet_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/components/faculty_student_individual_wallet_details_popup.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'student_wallet_id': student_wallet_id
                    },
                    success: function(response) {
                        $("#student-fees-popup").html(response);
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const load_edit_wallet_transaction = (student_wallet_id, wallet_action) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/components/faculty_student_wallet_edit_popup.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'student_wallet_id': student_wallet_id,
                        'wallet_action': wallet_action
                    },
                    success: function(response) {
                        $("#student-fees-popup").html(response);
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const fetch_individual_wallet_details = (student_wallet_id, client_type) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_fees/json/student_wallet_transaction_table.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'student_wallet_id': student_wallet_id,
                        'type': 2
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const data = response.data;
                            if (client_type == 1) {
                                $("#student-name").html(data.student_name);
                                $("#register-number").html(data.register_number || "-");
                                $("#transaction-type").html(data.wallet_action_badge);
                                $("#transaction-date").html(data.date);
                                $("#remarks").html(data.remarks || "-");
                                $("#reference-id").html(data.reference_id || "-");
                                $("#payment-method").html(data.payment_method_title || "-");
                                $("#transaction-status").html(data.wallet_status_badge);
                            } else {

                                const data = response.data;

                                // Credit Form Population
                                if ($("#date-of-credit").length) {
                                    $("#date-of-credit").val(data.date);
                                }
                                if ($("#credit-amount").length) {
                                    $("#credit-amount").val(data.amount);
                                }
                                if ($("#credit-reference").length) {
                                    $("#credit-reference").val(data.reference_id);
                                }
                                if ($("#credit-remarks").length) {
                                    $("#credit-remarks").val(data.remarks);
                                }

                                // Debit Form Population
                                if ($("#date-of-debit").length) {
                                    $("#date-of-debit").val(data.date);
                                }
                                if ($("#debit-amount").length) {
                                    $("#debit-amount").val(data.amount);
                                }
                                if ($("#debit-payment-mode").length) {
                                    $("#debit-payment-mode").val(data.payment_method);
                                }
                                if ($("#debit-payment-mode-dummy").length) {
                                    $("#debit-payment-mode-dummy").val(data.payment_method_title);
                                }
                                if ($("#debit-remarks").length) {
                                    $("#debit-remarks").val(data.remarks);
                                }

                                // Common student info (if needed)
                                if ($(".student-id").length) {
                                    $(".student-id").val(data.student_id);
                                }
                                if ($(".credit-student-name").length) {
                                    $(".credit-student-name").val(data.student_name);
                                }
                                if ($(".debit-student-name").length) {
                                    $(".debit-student-name").val(data.student_name);
                                }

                            }

                            resolve(response);
                        } else {
                            showToast(response.status, response.message);
                            reject(response);
                        }
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        };

        const load_student_wallet_transactions_table = (dept_id, year_of_study_id, section_id) => {
            $("#wallet-transactions-table").show();
            $(".action-box-content").hide();
            $('#wallet-transactions-table').DataTable().destroy()
            $('#wallet-transactions-table').DataTable({
                "serverSide": true,
                "ajax": {
                    "url": "<?= MODULES . '/faculty_student_fees/json/student_wallet_transaction_table.php' ?>",
                    "type": "POST",
                    "headers": {
                        "X-CSRF-TOKEN": '<?= $csrf_token; ?>',
                        "X-Requested-Path": window.location.pathname + window.location.search
                    },
                    "data": {
                        "dept_id": dept_id,
                        "year_of_study_id": year_of_study_id,
                        "section_id": section_id,
                        "type": 1
                    }
                },
                "columns": [{
                        "data": "s_no",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "student_name"
                    },
                    {
                        "data": "register_number"
                    },
                    {
                        "data": "amount"
                    },
                    {
                        "data": "wallet_action"
                    },
                    {
                        "data": "wallet_status"
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
