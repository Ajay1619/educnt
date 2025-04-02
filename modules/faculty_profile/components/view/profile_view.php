<?php
include_once('../../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    isset($_SERVER['HTTP_X_REQUESTED_PATH']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);


    $faculty_id = isset($_GET['faculty_id']) ? sanitizeInput($_GET['faculty_id'], 'string') : '';
?>


    <div class="container">
        <div class="soft-card profile-bg">
            <!-- SVG Background -->
            <div class="svg-background">
                <img src="<?= GLOBAL_PATH . '/images/svgs/pngegg.svg' ?>" alt="SVG Background" class="svg-img">
                <div class="row">
                    <div class="col col-6">
                        <h1 class="bg_text">Profile</h1>
                    </div>
                    <div class="col col-6 text-right ">
                        <button type="button" class="outline">PDF</button>
                    </div>
                </div>
            </div>

            <!-- Profile Picture and Name Section -->
            <div class="profile-header">

                <img src="<?= GLOBAL_PATH . '/images/profile pic placeholder.png' ?>" alt="Profile Picture" id="view-profile-pic" class="profile-pic">

                <div class="name-designation">
                    <h1 id="faculty-Fullname"></h1>
                    <p class="role" id="role"> <?= $logged_designation ?></p>
                </div>
            </div>

            <div class="soft-card p-6 m-4" id="profile-details-section"> <!-- Profile Details in 3 Columns -->
                <h2>Personal Details</h2>
                <div class="row mt-3">
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">First Name </div>
                        <div class="value" id="faculty-first-name"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Middle Name</div>
                        <div class="value" id="faculty-middle-name"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Last Name</div>
                        <div class="value" id="faculty-last-name"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Initial</div>
                        <div class="value" id="faculty-initial"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Salutation</div>
                        <div class="value" id="faculty-salutation"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Date of Birth</div>
                        <div class="value" id="faculty-dob"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Age</div>
                        <div class="value" id="faculty-age"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Gender</div>
                        <div class="value" id="faculty-gender"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Blood Group</div>
                        <div class="value" id="faculty-blood-group"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Aadhar Number</div>
                        <div class="value" id="faculty-aadhar-number"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Religion</div>
                        <div class="value" id="faculty-religion"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Caste</div>
                        <div class="value" id="faculty-caste"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Community</div>
                        <div class="value" id="faculty-community"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Nationality</div>
                        <div class="value" id="faculty-nationality"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Marital Status</div>
                        <div class="value" id="faculty-marital-status"></div>
                    </div>

                </div>
            </div>


            <div class="soft-card p-6 m-4" id="contact-details-section"> <!-- Profile Details in 3 Columns -->
                <h2>Contact Details</h2>
                <div class="row mt-3">
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Personal Mail ID</div>
                        <div class="value" id="faculty-email-id"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Official Mail ID</div>
                        <div class="value" id="faculty-official-email-id"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Mobile Number</div>
                        <div class="value" id="faculty-mobile-number"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Alternative Mobile</div>
                        <div class="value" id="faculty-alernative-contact-number"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">WhatsApp Mobile Number</div>
                        <div class="value" id="faculty-whatsapp-number"></div>
                    </div>
                </div>
            </div>
            <div class="soft-card p-6 m-4" id="address-details-section">
                <h2>Address Details</h2>
                <div class="row mt-3">
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">House Number</div>
                        <div class="value" id="house-number"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Street</div>
                        <div class="value" id="street"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Locality</div>
                        <div class="value" id="locality"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">City</div>
                        <div class="value" id="city"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">District</div>
                        <div class="value" id="district"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">State</div>
                        <div class="value" id="state"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Country</div>
                        <div class="value" id="country"></div>
                    </div>
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Pincode</div>
                        <div class="value" id="pincode"></div>
                    </div>
                </div>
            </div>


            <!-- Educational Details soft-card p-6 m-4 -->
            <div class="soft-card p-6 m-4" id="educational-details-section">
                <h2>Educational Details</h2>
                <div id="sslc-details-section">
                    <div class="profile-title mt-2">SSLC </div>
                    <div class="row mt-3">
                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                            <div class="title">School Name</div>
                            <div class="value" id="school-name"></div>
                        </div>
                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                            <div class="title">Board</div>
                            <div class="value" id="board-title"></div>
                        </div>
                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                            <div class="title">Year of Passing</div>
                            <div class="value" id="year-of-passing"></div>
                        </div>

                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                            <div class="title">Percentage</div>
                            <div class="value" id="percentage"></div>
                        </div>
                    </div>
                </div>
                <div id="hsc-details-section">
                    <div class="profile-title mt-2">HSC</div>
                    <div class="row mt-3">
                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                            <div class="title">School Name</div>
                            <div class="value" id="hsc-school-name"></div>
                        </div>
                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                            <div class="title">Board</div>
                            <div class="value" id="hsc-board-title"></div>
                        </div>
                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                            <div class="title">Year of Passing</div>
                            <div class="value" id="hsc-year-of-passing"></div>
                        </div>

                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                            <div class="title">Percentage</div>
                            <div class="value" id="hsc-percentage"></div>
                        </div>

                    </div>
                </div>
                <div id="degrees-details-section">
                    <div class="profile-title mt-2">Degrees</div>
                    <div id="degrees-container"></div> <!-- Container for dynamic degree rows -->
                </div>
            </div>
            <div class="soft-card p-6 m-4" id="experience-details-section">
                <h2>Experience Details</h2>
                <div id="experience-container"></div>

            </div>


            <!-- Course & Other Preferences soft-card p-6 m-4 -->
            <div class="soft-card p-6 m-4" id="skills-details-section">
                <h2>Skills</h2>
                <div class="row mt-3">
                    <!-- Core Experience Section -->
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Core Experience</div>
                        <div class="value">
                            <div class="chips-container" id="core-experience-container"></div>
                        </div>
                    </div>

                    <!-- Software Skills Section -->
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Software Skills</div>
                        <div class="value">
                            <div class="chips-container" id="software-skills-container"></div>
                        </div>
                    </div>

                    <!-- Interest Section -->
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Interest</div>
                        <div class="value">
                            <div class="chips-container" id="interest-container"></div>
                        </div>
                    </div>

                    <!-- Languages Known Section -->
                    <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                        <div class="title">Languages Known</div>
                        <div class="value">
                            <div class="chips-container" id="languages-known-container"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="soft-card p-6 m-4" id="document-details-section">
                <h2>Documentation</h2>
                <div id="documents-container"></div>
            </div>




        </div>
    </div>
    </div>

    <script>
        $(document).ready(async function() {
            try {
                showComponentLoading()
                await fetch_all_individual_data_profile_faculty('<?= $faculty_id ?>');
            } catch (error) {
                console.error('An error occurred while loading:', error);
            } finally {

                hideComponentLoading();
            }
        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
