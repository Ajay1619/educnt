<?php
include_once('../../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
?>
<div class="form-navigation">
            <button class="btn prev-btn">Previous</button>
            <button class="btn next-btn" id="nxt_btn">Next</button>
        </div>
<script>
    $(document).ready(function() {
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const route = urlParams.get('route');
        const action = urlParams.get('action');
        const type = urlParams.get('type');
        let currentUrl = window.location.href;
        
        $('#nxt_btn').on('click', function(e) {
            e.preventDefault(); // Prevent default button behavior
            
            // Initialize variable for new parameters
            let params = '';
            
            // Logic to update the `params` based on the current type
            if (action == 'add' && route == 'personal') {
                if (!type || type == '') {
                    params = '?action=add&route=personal&type=personal';
                } else if (type == 'personal') {
                    params = '?action=add&route=personal&type=contact';
                } else if (type == 'contact') {
                    params = '?action=add&route=personal&type=address';
                } else if (type == 'address') {
                    // Looping back to personal (optional)
                    params = '?action=add&route=education&type=sslc';
                }
            } else if (action == 'add' && route == 'fees') {
                if (!type || type == '') {
                    params = '?action=add&route=fees&type=fees';
                } else if (type == 'fees') {
                    params = '?action=add&route=fees&type=concession_details';
                } else if (type == 'concession_details') {
                    // Looping back to fees (optional)
                    params = '?action=add&route=fees&type=fees';
                }
            
            } else  {
                console.log('No matching condition for route and action');
                return;
            }
            
            // Construct the new URL by appending parameters
            let newUrl = currentUrl.split('?')[0] + params;
            
            // Redirect to the new URL with the updated parameters
            window.location.href = newUrl;
        });

    } catch (error) {
        console.error('An error occurred while processing:', error);
    }
});





        
</script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}