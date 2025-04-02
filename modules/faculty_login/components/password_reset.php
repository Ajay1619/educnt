<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {

?>

    <div class="password-reset-card">
        <h2>Set New Password</h2>
        <p class="instruction-text">Must be at least 8 characters.</p>

        <input type="password" id="new-password" placeholder="New Password" class="password-input">
        <input type="password" id="confirm-password" placeholder="Confirm Password" class="password-input">

        <p id="error-message" class="error-message"></p>

        <button class="confirm-btn" onclick="validatePassword()">Ok</button>
        <a href="#" id="back_to_log_in" class="back-to-login">Back To Log In</a>
    </div>

    <script>
        function validatePassword() {
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const errorMessage = document.getElementById('error-message');
            const newPasswordInput = document.getElementById('new-password');
            const confirmPasswordInput = document.getElementById('confirm-password');

            if (newPassword == confirmPassword && newPassword.length >= 8) {
                // If passwords match and are at least 8 characters long, show green border
                errorMessage.style.display = 'none';
                newPasswordInput.classList.remove('error');
                confirmPasswordInput.classList.remove('error');
                newPasswordInput.classList.add('success');
                confirmPasswordInput.classList.add('success');
            } else {
                // Show red border and error message if passwords don't match or are too short
                errorMessage.style.display = 'block';
                errorMessage.innerText = "Passwords do not match or are less than 8 characters.";
                newPasswordInput.classList.remove('success');
                confirmPasswordInput.classList.remove('success');
                newPasswordInput.classList.add('error');
                confirmPasswordInput.classList.add('error');
            }
        }

        $(document).ready(function() {
            $('#back_to_log_in').on('click', function(e) {
                e.preventDefault(); // Prevent default action of the link

                // Get the current URL
                let currentUrl = window.location.href;

                // Define the parameters you want to add
                let params = ''; // Example parameter

                // Construct the new URL by appending parameters
                let newUrl = currentUrl.split('?')[0] + params;

                // Redirect to the new URL with parameters
                window.location.href = newUrl;
            });
        });
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
