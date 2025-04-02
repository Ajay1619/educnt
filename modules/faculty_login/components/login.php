<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {

?>

    <div class="container">

        <div class="login-card">
            <h2>
                <span class="title-wrapper">
                    <span class="first-line">Login To</span>

                    <span class="second-line">Educate !</span>
                </span>
            </h2>
            <form id="login-form" method="POST">
                <input type="text" placeholder="Username" name="svcet_educnt_user_name" id="svcet_educnt_user_name" required>
                <input type="password" placeholder="Password" name="svcet_educnt_password" id="svcet_educnt_password" required>
                <!-- <p>
                    <a href="#" id="forgot_password">Forgot Password?</a>
                </p> -->
                <button type="submit" class="login-button">Login</button>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function() {



            $('#forgot_password').on('click', function(e) {
                e.preventDefault(); // Prevent default action of the link
                updateUrlWithParams('?action=forgot_password')
            });

            $('#login-form').on('submit', async function(e) {
                e.preventDefault(); // Prevent the default form submission

                // Serialize the form data
                const formData = $(this).serialize();
                // Call the login validation function with the serialized data
                try {
                    await login_validation(formData);
                } catch (error) {
                    showToast('error', error.message)
                }
            });

        });

        const login_validation = (formData) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_login/ajax/login_validation.php', ENT_QUOTES, 'UTF-8') ?>',
                    data: formData, // Send the serialized form data
                    success: function(response) {
                        response = JSON.parse(response)

                        showToast(response.status, response.message)
                        if (response.code == 200) {
                            // Redirect to the dashboard
                            window.location.href = response.redirect_link;
                        }
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        }
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
