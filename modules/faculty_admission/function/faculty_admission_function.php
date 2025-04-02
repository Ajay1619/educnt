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
    const loadSidebar = () => {

return new Promise((resolve, reject) => {
    $.ajax({
        type: 'GET',
        url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/sidebar.php', ENT_QUOTES, 'UTF-8') ?>',
        beforeSend: function() {
            showLoading(); // Show loading before request
        },
        headers: {
            'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
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
        complete: function() {
            $('#Loading').html(""); // Hide loading after request completes
        }
    });
});
};



const loadTopbar = () => {
return new Promise((resolve, reject) => {
    $.ajax({
        type: 'GET',
        url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/topbar.php', ENT_QUOTES, 'UTF-8') ?>',
        beforeSend: function() {
            showLoading(); // Show loading before request
        },
        headers: {
            'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
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
        complete: function() {
            $('#Loading').html(""); // Hide loading after request completes
        }
    });
});
};


const loadBgCard = () => {
return new Promise((resolve, reject) => {
$.ajax({
    type: 'GET',
    url: '<?= htmlspecialchars(MODULES . '/faculty_admission/components/bg-card.php', ENT_QUOTES, 'UTF-8') ?>',
    beforeSend: function() {
        showLoading(); // Show loading before request
    },
    headers: {
        'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
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
    complete: function() {

        $("#Loading").html("")


    }
});
});
};


// Load the side navigation bar

// const load_faculty_addmission = () => {
//     return new Promise((resolve, reject) => {
//         $.ajax({
//             type: 'GET',
//             url: '<?= htmlspecialchars(MODULES . '/faculty_admission/components/faculty_admission_form.php', ENT_QUOTES, 'UTF-8') ?>',
//             beforeSend: function() {
//                 showLoading();
//             },
//             headers: {
//                 'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
//                 'X-Requested-Path': window.location.pathname + window.location.search
//             },
//             success: function(response) {
//                 $('#faculty-admission').html(response);
//                 resolve(); // Resolve the promise
//             },
//             error: function(jqXHR) {
//                 const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
//                 console.error('Error loading top navbar:', message);
//                 reject(); // Reject the promise
//             },
//             complete: function() {
//                 $('#Loading').html(""); // Hide loading after the request completes, with delay
//             }
//         });
//     });
// };
const load_overall_faculty_addmission = () => {
return new Promise((resolve, reject) => {
    $.ajax({
        type: 'GET',
        url: '<?= htmlspecialchars(MODULES . '/faculty_admission/components/overall_view_faculty_admission.php', ENT_QUOTES, 'UTF-8') ?>',
        beforeSend: function() {
            showLoading();
        },
        headers: {
            'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
            'X-Requested-Path': window.location.pathname + window.location.search
        },
        success: function(response) {
            $('#overall-faculty-admission').html(response);
            resolve(); // Resolve the promise
        },
        error: function(jqXHR) {
            const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
            console.error('Error loading top navbar:', message);
            reject(); // Reject the promise
        },
        complete: function() {
            $('#Loading').html(""); // Hide loading after the request completes, with delay
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
