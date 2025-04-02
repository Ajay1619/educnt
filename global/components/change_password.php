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

    <!-- Change Password Popup Overlay -->
    <div class="popup-overlay" id="change-password">
        <!-- Alert Popup Container -->
        <div class="alert-popup">
            <!-- Close Button -->
            <button class="popup-close-btn">×</button>

            <!-- Popup Header -->
            <div class="popup-header">
                Change Password
            </div>

            <form id="change-password-form" method="post">
                <!-- Popup Content -->
                <div class="popup-content">
                    <p class="popup-quotes">"Even Sherlock Holmes would need a password this secure. 🕵️‍♂️🔍"</p>
                    <!-- Old Password Input -->
                    <div class="input-container">
                        <input type="password" id="old-password" class="password-input" name="old_password" placeholder=" " required>
                        <label class="input-label" for="old-password">Enter Your Old Password</label>
                    </div>
                    <div id="old-password-validation-error"></div>
                    <!-- New Password Input -->
                    <div class="input-container">
                        <input type="password" id="new-password" class="password-input" name="new_password" placeholder=" " maxlength="100" required>
                        <label class="input-label" for="new-password">Enter Your New Password</label>

                    </div>

                    <div id="password-validation-error"></div>

                    <!-- Confirm New Password Input -->
                    <div class="input-container">
                        <input type="password" id="confirm-new-password" class="password-input" name="confirm_new_password" placeholder=" " maxlength="100" required>
                        <label class="input-label" for="confirm-new-password">Enter Your New Password Again</label>

                    </div>

                    <div id="confirm-password-validation-error"></div>
                </div>

                <!-- Popup Footer -->
                <div class="popup-footer">
                    <button type="submit" class="particulars btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript to Handle Popup Display and Submission -->
    <script>
        $(window).on('click', function(event) {
            if (event.target == document.getElementById('change-password')) {
                $('#change-password-popup').html('');
            }
        });
        $('#old-password').on('blur', function() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/ajax/input_validation.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'old_password': $(this).val(),
                        'type': 1
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code === 200) {
                            $("#old-password").removeClass('warning')
                            $("#old-password").addClass('success')
                            $('#old-password-validation-error').html('');
                            showToast(response.status, response.data)
                        } else {

                            $("#old-password").removeClass('success')
                            $("#old-password").addClass('warning')
                            $('#old-password-validation-error').html('<div class="warning-item warning-text text-left mt-2">' + response.data + '</div>');
                            showToast(response.status, response.data)
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
        })
        // id=new-password on blur
        $('#new-password').on('blur', function() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/ajax/input_validation.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'new_password': $(this).val(),
                        'old_password': $('#old-password').val(),
                        'type': 2
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code === 200) {

                            // Clear any error messages if validation is successful
                            $('#new-password').removeClass("error")
                            $('#new-password').addClass("success")
                            $('#password-validation-error').empty();
                            check_confirm_password()

                        } else {
                            const errors = response.data;
                            showToast(response.status, response.message)
                            $('#new-password').removeClass("success")
                            $('#new-password').addClass("error")
                            // Clear previous error messages
                            $('#password-validation-error').empty();
                            // Append each error as a list item
                            if (Array.isArray(errors)) {
                                // Add list structure for errors
                                $('#password-validation-error').append('<ul class="error-list[ mt-2"></ul>');
                                errors.forEach((error) => {
                                    $('#password-validation-error ul').append(`<li class="error-item error-text text-left">${error}</li>`);
                                });
                            } else {
                                $('#password-validation-error').html(`<div class="error-item error-text text-left mt-2">${errors}</div>`);

                            }

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
        });

        const check_confirm_password = () => {
            if ($('#confirm-new-password').val() !== "" && $('#new-password').val() !== "") {
                if ($('#confirm-new-password').val() !== $('#new-password').val()) {
                    $('#confirm-new-password').removeClass("success")
                    $('#confirm-new-password').addClass("error")
                    //id="confirm-password-validation-error" display your Password Doesn't Match
                    $('#confirm-password-validation-error').empty()
                    $('#confirm-password-validation-error').append('<div class="error-item error-text text-left mt-2">Your Password Doesn\'t match. Please Enter The Correct Password..</div>');
                } else {
                    $('#confirm-new-password').removeClass("error")
                    $('#confirm-new-password').addClass("success")
                    $('#confirm-password-validation-error').empty()
                }
            }
        }
        // id=confirm-new-password on input
        $('#confirm-new-password').on('input', function() {
            check_confirm_password()
        });

        // change-password-form on submit

        $('#change-password-form').on('submit', async function(e) {
            e.preventDefault();
            const password_error = $('#password-validation-error').html()
            const old_password_validation_error = $('#old-password-validation-error').html()
            const confirm_password_error = $('#confirm-password-validation-error').html()
            if (password_error != "" || confirm_password_error != "" || old_password_validation_error != "") {
                showToast("warning", "Your request has been cancelled. Please check your input and try again.");

            } else {

                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/ajax/input_validation.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    data: {
                        'new_password': $('#new-password').val(),
                        'type': 3
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        console.log(response)
                        if (response.code === 200) {
                            showToast(response.status, response.message)
                            $('#change-password-popup').html("");
                        } else {
                            showToast(response.status, response.message)
                        }
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status === 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            }
        })
    </script>



<?php

} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>