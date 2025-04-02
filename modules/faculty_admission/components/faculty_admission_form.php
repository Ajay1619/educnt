 
<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
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
<div class="faculty_admission_container">
    <h2>Personal Details</h2>
    <div class="row">
        <!-- First Name -->
        <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="input-container">
                <input type="text" id="firstname" placeholder=" " required aria-required="true">
                <label class="input-label" for="firstname">Enter Your Firstname</label>
            </div>
        </div>
        <!-- Middle Name -->
        <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="input-container">
                <input type="text" id="Middlename" placeholder=" " required aria-required="true">
                <label class="input-label" for="Middlename">Enter Your Middlename</label>
            </div>
        </div>
        <!-- Last Name -->
        <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="input-container">
                <input type="text" id="Lastname" placeholder=" " required aria-required="true">
                <label class="input-label" for="Lastname">Enter Your Lastname</label>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
        <div class="row">
            <!-- Initial -->
            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="input-container">
                    <input type="text" id="Intial" placeholder=" " required aria-required="true">
                    <label class="input-label" for="Intial">Enter Your Initial</label>
                </div>
            </div>
            <!-- Date of Birth -->
            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="input-container">
                    <input type="date" id="Date_of_birth" placeholder=" " required aria-required="true">
                    <label class="input-label" for="Date_of_birth">Enter Your DOB</label>
                </div>
            </div>
            <!-- Gender -->
            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="input-container">
                    <input type="text" id="Gender" placeholder=" " required aria-required="true">
                    <label class="input-label" for="Gender">Enter Your Gender</label>
                </div>
            </div>
        </div>

    <!-- Aadhar, Religion, Caste -->
        <div class="row">
            <!-- Aadhar -->
            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="input-container">
                    <input type="text" id="Aadhar" placeholder=" " required aria-required="true">
                    <label class="input-label" for="Aadhar">Enter Your Aadhar number</label>
                </div>
            </div>
            <!-- Religion -->
            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="input-container">
                    <input type="text" id="Religion" placeholder=" " required aria-required="true">
                    <label class="input-label" for="Religion">Enter Your Religion</label>
                </div>
            </div>
            <!-- Caste -->
            <!-- <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="input-container">
                    <input type="text" id="Caste" placeholder=" " required aria-required="true">
                    <label class="input-label" for="Caste">Enter Your Caste</label>
                </div>
            </div> -->
            <!-- Nationality -->
            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="input-container">
                    <input type="text" id="Nationality" placeholder=" " required aria-required="true">
                    <label class="input-label" for="Nationality">Enter Your Nationality</label>
                </div>
            </div>
        </div>

    <!-- Community, Nationality, Blood Group -->
        <div class="row">
            <!-- Community
            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="input-container">
                    <input type="text" id="Community" placeholder=" " required aria-required="true">
                    <label class="input-label" for="Community">Enter Your Community</label>
                </div>
            </div> -->
            
            <!-- Blood Group -->
            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="input-container">
                    <input type="text" id="Blood_group" placeholder=" " required aria-required="true">
                    <label class="input-label" for="Blood_group">Enter Your Blood Group</label>
                </div>
            </div>
            <!-- Marital Status -->
            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="input-container">
                    <input type="text" id="Martial_status" placeholder=" " required aria-required="true">
                    <label class="input-label" for="Martial_status">Enter Your Marital Status</label>
                </div>
            </div>
            <!-- Resume Upload -->
            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="input-container">
                    <input type="file" id="Resume" accept=".pdf,.doc,.docx" required aria-required="true">
                    <label class="input-label" for="Resume">Upload Your Resume</label>
                </div>
            </div>
        </div>

    <!-- Marital Status and Resume Upload -->
        <div class="btn-container">
        <button class="btn submit-btn" id="submit_btn">Submit</button>
        </div>
</div>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>
