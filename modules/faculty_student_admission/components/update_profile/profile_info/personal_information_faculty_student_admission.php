<?php
include_once('../../../../../config/sparrow.php');

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

<div class="tab-nav">
<button class="tab-btn personal" >Personal Details</button>
    <button class="tab-btn contact" >Contact Details</button>
    <button class="tab-btn parent" >Parents Details</button>
    <button class="tab-btn address" >Address Details</button>
    <button class="tab-btn official" >Official Details</button>
</div>

<section id="update_personal_profile">

    <div class="step-content active" data-step="0">
        <section id="info"></section>
        <!-- <section id="contact_info"></section>
        <section id="address_info"></section> -->
    </div>
</section>


<script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>

<script>
(function() {
    
        const NavigationProfileForm = () => {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: 'GET',
                url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/layout/navigation_update_profile_info_faculty_student_admission.php', ENT_QUOTES, 'UTF-8') ?>',
                headers: {
                   'X-CSRF-Token': '<?= $csrf_token ?>' , // Secure CSRF token
                'X-Requested-Path': window.location.pathname + window.location.search// Secure CSRF token  // Secure CSRF token // Secure CSRF token
                },
                success: function(response) {
                    $('#navigation').html(response);
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
        

         
        $(document).ready(async function() {
    try {
        await NavigationProfileForm();
        const urlParams = new URLSearchParams(window.location.search);
        const route = urlParams.get('route');
        const action = urlParams.get('action');
        const type = urlParams.get('type');
        const tab = urlParams.get('tab');

         console.log(`Route: ${route}, Action: ${action}, Type: ${type}, tab: ${tab}`);
        $('.tab-btn').removeClass('active');
        // Condition to load the correct form based on URL parameters
        

        // Call the function directly, no need for another $(document).ready
        await loadprofileComponentsBasedOnURL();

    } catch (error) {
        console.error('An error occurred while processing:', error);
    }
});
}) ();


</script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}