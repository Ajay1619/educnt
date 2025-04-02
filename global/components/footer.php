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
    <footer class="footer">
        <div class="footer-content">
            <div class="row justify-center align-center">
                <div class="col col-2 col-lg-2 col-md-2 col-sm-2 col-xs-12">
                    <div class="footer-software-name">EDUCNT</div>
                </div>
                <div class="col col-8 col-lg-8 col-md-7 col-sm-7 col-xs-12">
                    <div class="footer-college-name">SVGI - Sri Venkateshwaraa College of Engineering and Technology</div>
                </div>
                <div class="col col-2 col-lg-2 col-md-2 col-sm-3 col-xs-12">
                    <div class="footer-copyright">© <?= date("Y") ?> All Rights Reserved</div>
                </div>
            </div>
        </div>
    </footer>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>