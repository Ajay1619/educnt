<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {

?>


    <div class="password-otp-card">
        <h2>Password Reset</h2>
        <p>We sent a code to username or email</p>

        <div class="otp-inputs">
            <input type="text" maxlength="1" class="otp-box" id="otp1">
            <input type="text" maxlength="1" class="otp-box" id="otp2">
            <input type="text" maxlength="1" class="otp-box" id="otp3">
            <input type="text" maxlength="1" class="otp-box" id="otp4">
        </div>

        <button class="enter-btn" id="otp_submit">Enter</button>
        <a href="#" class="resend-link">Resend The OTP</a>
    </div>
    <script>
        const otpInputs = document.querySelectorAll('.otp-box');

        otpInputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                if (input.value.length == 1 && index < otpInputs.length - 1) {
                    // Move to the next input if this isn't the last one
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key == 'Backspace' && input.value.length == 0 && index > 0) {
                    // If Backspace is pressed and the input is empty, go to the previous input
                    otpInputs[index - 1].focus();
                }
            });
        });


        $(document).ready(function() {
            $('#otp_submit').on('click', function(e) {
                e.preventDefault(); // Prevent default action of the link

                // Get the current URL
                let currentUrl = window.location.href;

                // Define the parameters you want to add
                let params = '?action=reset_password'; // Example parameter

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
