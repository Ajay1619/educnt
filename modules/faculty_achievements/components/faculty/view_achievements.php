<?php
include_once('../../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    //Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
    $id = isset($_POST['id']) ? sanitizeInput($_POST['id'], 'string') : '';
    
    
   
?>

<div class="main-content-card action-box" id="college-authorities-list">
         <div class="action-title">Your Achievements</div>
         <div class="authorities-assignment" >
            <div class="row" id="Achievements_container"></div>
         </div>
     </div>

<script src="<?= MODULES . '/faculty_achievements/js/view_achievements.js' ?>"></script>
<script>
   
   
   

        $(document).ready(async function() {
           
            // Event handler for opening the edit popup on multiple elements
         

            // await loadComponentsBasedOnURL();
            fetchFacultyAchivementData(0,'<?= $id?>');
            

            // Event handler for closing the edit popup
            
        });
</script>
    
    <?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}