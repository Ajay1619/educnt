<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    // Validate CSRF token
    if (!validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
        echo json_encode(['code' => 403, 'status' => 'error', 'message' => 'CSRF token validation failed.']);
        exit;
    }
?>

    <div class="bg-card">
        <div class="bg-card-content">
            <div class="bg-card-header">
                <div class="row">
                    <div class="col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <h2 id="action"></h2>
                    </div>
                    <div class="col-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 bg-card-header-right-content ">
                        <!-- <button class="outline bg-card-button">Print</button> -->
                        <!-- <button class="outline">EFGH</button>
                        <button class="outline">IJKL</button> -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-12" id="breadcrumbs"></div>
                </div>
            </div>
            <hr class="full-width-hr">
            <!-- <div class="bg-card-filter">
                <div class="row">
                     <div class="col-3 col-sm-4 col-xs-6">
                        <select name="year_of_study" id="year_of_study">
                            <option value="1">Role:</option>
                            <option value="2">Year 2</option>
                            <option value="3">Year 3</option>
                            <option value="4">Year 4</option>
                        </select>
                    </div> -->
                    <!-- <div class="col-3 col-sm-4 col-xs-6">
                        <select name="year_of_study" id="">
                            <option value="1">Year Of Study</option>
                            <option value="2">Year 2</option>
                            <option value="3">Year 3</option>
                            <option value="4">Year 4</option>
                        </select>
                    </div>
                    <div class="col-3 col-sm-4 col-xs-6">
                        <select name="year_of_study" id="">
                            <option value="1">Year Of Study</option>
                            <option value="2">Year 2</option>
                            <option value="3">Year 3</option>
                            <option value="4">Year 4</option>
                        </select>
                    </div>
                    <div class="col-3 col-sm-4 col-xs-6">
                        <select name="year_of_study" id="">
                            <option value="1">Year Of Study</option>
                            <option value="2">Year 2</option>
                            <option value="3">Year 3</option>
                            <option value="4">Year 4</option>
                        </select>
                    </div> 
                </div>
            </div> -->
        </div>
    </div>
    <script>
    $(document).ready(function() {
        // Function to capitalize the first letter of the action
    //     function capitalizeFirstLetter(string) {
    //         return string.charAt(0).toUpperCase() + string.slice(1);
    //     }

    //     // Retrieve URL parameters
    //     const urlParams = new URLSearchParams(window.location.search);
        
    //     // Get the 'action' parameter
    //     const action = urlParams.get('action');

    //     // Check if 'action' parameter exists and insert it into the 'action' element
    //     const callaction = () => {
    //     if (action) {
    //         // Prepend the capitalized action to the existing text
    //         $('#action').text(capitalizeFirstLetter(action) + ' ' + $('#action').text());
    //     } else {
    //         $('#action').text("Roles&Responsibilities");  // Default text
    //     }
    // }
    // callaction();
    // Capitalize the first letter of the action
const capitalizeFirstLetter = (string) => {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

// Function to update the heading based on the action and last part of the URL path
const callAction = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const action = urlParams.get('action'); // e.g., 'add', 'edit'

    // Get the last part of the URL path
    const pathArray = window.location.pathname.split('/');
    const lastPath = pathArray[pathArray.length - 1];

    // Capitalize the first letter of the action and format the title
    let title = "";
    if (action) {
        title += capitalizeFirstLetter(action) + " ";
    }

    // Remove any file extension if present (e.g., '.php' from 'faculty-roles-responsibilities.php')
    const cleanPath = lastPath.split('.')[0];

    title += cleanPath.replace(/-/g, ' '); // Replace hyphens with spaces for readability

    // Update the <h2> tag with the formatted title
    $('#action').text(title);
}

// Call the function to update the heading
callAction();

    });
</script>


<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>