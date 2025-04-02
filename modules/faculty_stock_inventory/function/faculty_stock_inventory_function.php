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
        const floor_list = [{
                "title": "Ground Floor",
                "value": 0
            },
            {
                "title": "First Floor",
                "value": 1
            },
            {
                "title": "Second Floor",
                "value": 2
            },
            {
                "title": "Third Floor",
                "value": 3
            },
            {
                "title": "Fourth Floor",
                "value": 4
            },
            {
                "title": "Fifth Floor",
                "value": 5
            },
            {
                "title": "Sixth Floor",
                "value": 6
            },
            {
                "title": "Seventh Floor",
                "value": 7
            }
        ];
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
                    url: '<?= htmlspecialchars(MODULES . '/faculty_stock_inventory/components/bg-card.php', ENT_QUOTES, 'UTF-8') ?>',
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
                    }
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
                            const value = element.siblings(".faculty-dept-filter")
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

        const load_dept_rooms_list = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_stock_inventory/components/dept_rooms_list.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#main-components').html(response);
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

        const load_add_room = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_stock_inventory/components/add_rooms.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#main-components').html(response);
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

        const load_individual_edit_dept_room = (room_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_stock_inventory/components/individual_edit_dept_room.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    data: {
                        'room_id': room_id
                    },
                    success: function(response) {
                        $('#main-components').html(response);
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

        const load_individual_view_dept_room = (room_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_stock_inventory/components/individual_view_dept_room.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    data: {
                        'room_id': room_id
                    },
                    success: function(response) {
                        updateUrl({
                            route: 'faculty',
                            action: 'view',
                            tab: room_id
                        });
                        $('#main-components').html(response);
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

        const load_add_product_stock_inventory_list_popup = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_stock_inventory/components/add_product_stock_inventory_list_popup.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    data: {
                        'room_id': $('#room-id').val()
                    },
                    success: function(response) {
                        $('#faculty-stock-inventory-popup').html(response);
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

        const load_individual_edit_dept_room_item_popup = (item_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_stock_inventory/components/individual_edit_dept_room_item_popup.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    data: {
                        'item_id': item_id
                    },
                    success: function(response) {
                        $('#faculty-stock-inventory-popup').html(response);
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
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action'); // e.g., 'add', 'edit'
            const route = urlParams.get('route'); // e.g., 'add', 'edit'
            const type = urlParams.get('type');
            const tab = urlParams.get('tab');
            // Get the last part of the URL path
            const pathArray = window.location.pathname.split('/');
            const lastPath = pathArray[pathArray.length - 1];
            const routing_link = lastPath + window.location.search
            fetch_bg_card_title(routing_link);

            if (action == 'view' && route == 'faculty' && tab == null) {
                await load_dept_rooms_list();
            } else if (action == 'view' && route == 'faculty' && tab != null) {
                await load_individual_view_dept_room(tab);
            } else if (action == 'add' && route == 'faculty' && type == 'room') {
                await load_add_room();
            } else if (action == 'add' && route == 'faculty' && type == 'product_list') {
                await load_add_product_stock_inventory_list();
            }
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

        const callAction = (element) => {
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action'); // e.g., 'add', 'edit'
            const route = urlParams.get('route'); // e.g., 'add', 'edit'
            const type = urlParams.get('type');
            const tab = urlParams.get('tab');

            // Get the last part of the URL path
            const pathArray = window.location.pathname.split('/');
            const lastPath = pathArray[pathArray.length - 1];
            const routing_link = lastPath + window.location.search
            fetch_bg_card_title(routing_link);

            $("#faculty-stock-add-item-button").hide();
            $("#faculty-stock-add-room-button").hide();
            $("#faculty-stock-view-button").hide();
            $(".bg-card-filter").hide();
            $(".full-width-hr").hide();
            // Capitalize the first letter of the action and format the title
            if (action == 'view' && route == 'faculty' && tab == null) {
                $("#faculty-stock-add-room-button").show();
                $(".bg-card-filter").show();
                $(".full-width-hr").show();
            } else if (action == 'view' && route == 'faculty' && tab != null) {
                $("#faculty-stock-add-item-button").show();
                $(".bg-card-filter").hide();
            } else if (action == 'add' && route == 'faculty' && type == 'room') {
                $("#faculty-stock-view-button").show();
                $(".bg-card-filter").hide();
            }
        }

        const stock_add_rooms_form = (data) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_stock_inventory/ajax/stock_add_rooms_form.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'data': data,
                        'dept_id': $("#faculty-dept-filter").val()
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

        const add_product_dept_room_form = (data) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_stock_inventory/ajax/add_product_dept_room_form.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'data': data,
                        'room_id': $("#room-id").val()
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            showToast(response.status, response.message);
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

        const edit_product_dept_room_form = (data) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_stock_inventory/ajax/edit_product_dept_room_form.php', ENT_QUOTES, 'UTF-8') ?>',
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

        const stock_edit_rooms_form = (data) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_stock_inventory/ajax/stock_edit_rooms_form.php', ENT_QUOTES, 'UTF-8') ?>',
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

        const change_dept_room_status = (room_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_stock_inventory/ajax/change_dept_room_status.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'room_id': room_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            showToast(response.status, response.message);

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

        const delete_individual_view_dept_room = (room_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_stock_inventory/ajax/delete_dept_room.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'room_id': room_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            showToast(response.status, response.message);
                            updateUrl({
                                route: 'faculty',
                                action: 'view'
                            });
                            let dept_id = <?= $logged_dept_id ?>;
                            if ($("#faculty-dept-filter").val() != '') {
                                dept_id = $("#faculty-dept-filter").val();

                            }
                            fetch_dept_rooms_list(dept_id);
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

        const delete_individual_dept_item = (item_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_stock_inventory/ajax/delete_individual_dept_item.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'item_id': item_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            showToast(response.status, response.message);
                            updateUrl({
                                route: 'faculty',
                                action: 'view'
                            });

                            fetch_dept_room_items_list($("#room-id").val());
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

        const fetch_individual_edit_dept_room_details = (room_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_stock_inventory/json/fetch_individual_dept_room_details.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'room_id': room_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            console.log(response)
                            const edit_form = response.edit_form;
                            $('#stock-edit-rooms').html(edit_form);
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

        const fetch_individual_view_dept_room_details = (room_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_stock_inventory/json/fetch_individual_dept_room_details.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'room_id': room_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const view_data = response.view_data;
                            $('#stock-view-rooms').html(view_data);
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

        const fetch_individual_edit_room_item_details = (item_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= htmlspecialchars(MODULES . '/faculty_stock_inventory/json/fetch_individual_edit_room_item_details.php', ENT_QUOTES, 'UTF-8') ?>',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>',
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'item_id': item_id
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const edit_form = response.edit_form;
                            $('#edit-product-dept-room-form').html(edit_form);
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

        const fetch_dept_rooms_list = (dept_id) => {
            // Clear existing content and show the table
            $('.action-box-content').hide();
            $(".stock-dept-rooms-list").show();
            $('#dept-rooms-list').show();

            // Destroy existing DataTable instance (if any)
            $('#dept-rooms-list').DataTable().destroy();

            // Initialize DataTable
            $('#dept-rooms-list').DataTable({
                "serverSide": true,
                "ajax": {
                    "url": "<?= MODULES . '/faculty_stock_inventory/json/fetch_dept_rooms_list.php' ?>",
                    "type": "POST",
                    "headers": {
                        "X-CSRF-TOKEN": '<?= $csrf_token; ?>',
                        "X-Requested-Path": window.location.pathname + window.location.search
                    },
                    "data": {
                        "dept_id": dept_id,
                        "type": 1
                    }
                },
                "columns": [{
                        "data": "s_no",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "room_number"
                    },
                    {
                        "data": "room_name"
                    },
                    {
                        "data": "room_category"
                    },
                    {
                        "data": "status"
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

            // Adjust table layout
            $('.dt-layout-row .dt-layout-table').css('width', '100%');
            $('.dt-layout-table .dt-layout-cell').css('width', '100%');
            $('.dt-scroll-headInner').css('width', '100%');
            $('.dataTable').css('width', '100%');
        };

        const fetch_dept_room_items_list = (room_id) => {
            // Clear existing content and show the table
            $('.action-box-content').hide();
            $("#room-items-list").show();
            $('#room-items-list-table').show();

            // Destroy existing DataTable instance (if any)
            $('#room-items-list-table').DataTable().destroy();

            // Initialize DataTable
            $('#room-items-list-table').DataTable({
                "serverSide": true,
                "ajax": {
                    "url": "<?= MODULES . '/faculty_stock_inventory/json/fetch_dept_room_items_list.php' ?>",
                    "type": "POST",
                    "headers": {
                        "X-CSRF-TOKEN": '<?= $csrf_token; ?>',
                        "X-Requested-Path": window.location.pathname + window.location.search
                    },
                    "data": {
                        "room_id": room_id,
                        "type": 1
                    }
                },
                "columns": [{
                        "data": "s_no",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "item_name"
                    },
                    {
                        "data": "unit_of_measure"
                    },
                    {
                        "data": "quantity"
                    },
                    {
                        "data": "note",
                        "orderable": false,
                        "searchable": false
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

            // Adjust table layout
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
