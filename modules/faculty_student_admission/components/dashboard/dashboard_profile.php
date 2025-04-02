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

    <h1>Dashboard</h1>
    <p class="welcome-slogan">Welcome back, Shiyam!</p>
    <div class="hod-dashboard">
        <!-- Card 1: Total Number of Students -->
        <div class="card">
            <h3>Total Number of Students</h3>
            <div id="students-chart"></div>
        </div>

        <!-- Card 2: Roles Assigned Students -->
        <div class="card">
            <h3>Roles Assigned Students</h3>
            <div class="numeric-display" id="roles-count">0</div>
        </div>

        <!-- Card 3: Recent Achievements Added -->
        <div class="card">
            <h3>Recent Achievements Added</h3>
            <div class="numeric-display" id="achievements-count">0</div>
        </div>
    </div>
    <script src="<?= MODULES . '/faculty_student_admission/js/dashboard_view_profile_student.js' ?>"></script>


<h1>Dashboard</h1>
<button class="btn prev-btn text-left" id="add_admission" type="button">add</button>
<button class="btn next-btn text-right" id="overall_admission" type="submit">overall</button>
<script>
     $('#add_admission').on('click', async function() {
        // console.log("hi");
        showComponentLoading(1)
                        const params = {
                            action: 'add',
                            route: 'faculty',
                            type: 'personal',
                            tab: 'personal'
                        };

                        // Construct the new URL with query parameters
                        const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
                        const newUrl = window.location.origin + window.location.pathname + queryString;
                        // Use pushState to set the new URL and pass params as the state object
                        window.history.pushState(params, '', newUrl);
                        //load_personal_info_components();
                      await  loadComponentsBasedOnURL()
                      setTimeout(function() {
                        hideComponentLoading();
                    }, 100)
                    });
     $('#overall_admission').on('click',async function() {
        showComponentLoading(1);
                        const params = {
                            action: 'view',
                            route: 'faculty',
                            type: 'overall'
                        };

                        // Construct the new URL with query parameters
                        const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}`;
                        const newUrl = window.location.origin + window.location.pathname + queryString;
                        // Use pushState to set the new URL and pass params as the state object
                        window.history.pushState(params, '', newUrl);
                        //load_personal_info_components();
                      await  loadComponentsBasedOnURL()
                      setTimeout(function() {
                        hideComponentLoading();
                    }, 100)
                    });
</script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
