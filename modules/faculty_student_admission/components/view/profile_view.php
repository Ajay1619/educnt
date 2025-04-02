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
    $student_id = isset($_GET['student_id']) ? sanitizeInput($_GET['student_id'], 'string') : '';
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
                        <button type="submit" class="outline_black" onclick="go_overall()" id="back">Back</button>
                    </div>
                </div>
            </div>

            <!-- Profile Picture and Name Section -->

            <div class="profile-header">
                <img src="<?= GLOBAL_PATH . '/images/svcet.png' ?>" alt="Profile Picture" class="profile-pic" id="view-profile-pic">
                <div class="name-designation">
                    <h1 id="student-fullname"></h1>
                    <p class="role">Student</p>
                </div>
            </div>

            <div class="soft-card p-6 m-4">
                <h2>Personal Information</h2>
                <br>
                <div class="row mt-3">
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">First Name:</div>
                        <div id="student-first-name"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Middle Name:</div>
                        <div id="student-middle-name"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Last Name:</div>
                        <div id="student-last-name"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Initial:</div>
                        <div id="student-initial"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Full Name:</div>
                        <div id="student-fullname"> </div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Date of Birth:</div>
                        <div id="student-dob"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Age:</div>
                        <div id="student-age"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Gender:</div>
                        <div id="student-gender"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Blood Group:</div>
                        <div id="student-blood-group"></div>
                    </div>
                </div>
            </div>
            <div class="soft-card p-6 m-4">
                <h2>Contact Information</h2>
                <br>
                <div class="row">
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Email ID:</div>
                        <div id="student-email"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Phone Number:</div>
                        <div id="contact-phone-number"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">WhatsApp Number:</div>
                        <div id="contact-whatsapp-number"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Alternative Contact Number:</div>
                        <div id="contact-alternativenumber-number"></div>
                    </div>
                </div>
            </div>
            <div class="soft-card p-6 m-4">
                <h2>Official Information</h2>
                <br>
                <div class="row">
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Register Number:</div>
                        <div id="student-register-number"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Official Mail Id:</div>
                        <div id="student-official-email"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Department:</div>
                        <div id="student-Department"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Year of Study:</div>
                        <div id="student-year"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Section:</div>
                        <div id="student-section"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Academic Year:</div>
                        <div id="student-academic-year"></div>
                    </div>
                </div>
            </div>
            <div class="soft-card p-6 m-4">
                <h2>Address Details</h2>
                <div class="row">
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Door No.:</div>
                        <div id="permanent-door-no"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Street:</div>
                        <div id="permanent-street"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Area:</div>
                        <div id="permanent-area"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">District:</div>
                        <div id="permanent-district"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">State:</div>
                        <div id="permanent-state"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Pincode:</div>
                        <div id="permanent-pincode"></div>
                    </div>
                </div>

                <br>
                <!-- <h3>Residential Address</h3>
    <br>
    <div class="row">
        <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
            <div>Door No.:</div> <div id="residential-door-no">123</div>
        </div>
        <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
            <div>Street:</div> <div id="residential-street">Main St.</div>
        </div>
        <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
            <div>Area:</div> <div id="residential-area">Downtown</div>
        </div>
        <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
            <div>District:</div> <div id="residential-district">City</div>
        </div>
        <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
            <div>State:</div> <div id="residential-state">State</div>
        </div>
        <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
            <div>Pincode:</div> <div id="residential-pincode">123456</div>
        </div>
    </div> -->
            </div>


            <!-- Contact Information soft-card p-6 m-4 -->



            <!-- Parent/Guardian Information soft-card p-6 m-4 -->
            <div class="soft-card p-6 m-4">
                <h2>Parent/Guardian Information</h2>
                <br>
                <div class="profile-title mt-2">Father’s Information</div>
                <br>
                <div class="row">
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Father’s Name:</div>
                        <div id="father-name"> </div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Father’s Occupation:</div>
                        <div id="father-occupation"></div>
                    </div>
                </div>
                <br>
                <div class="profile-title mt-2">Mother’s Information</div>
                <br>
                <div class="row">
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Mother’s Name:</div>
                        <div id="mother-name"> </div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Mother’s Occupation:</div>
                        <div id="mother-occupation"></div>
                    </div>
                </div>
                <br>
                <div class="profile-title mt-2">Guardian’s Information</div>
                <br>
                <div class="row">
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Guardian’s Name:</div>
                        <div id="guardian-name"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Guardian’s Occupation:</div>
                        <div id="guardian-occupation"></div>
                    </div>
                </div>
            </div>


            <!-- Educational Details soft-card p-6 m-4 -->
            <div class="soft-card p-6 m-4">
                <h2>Educational Details</h2>
                <br>
                <div class="profile-title mt-2">SSLC</div>
                <br>
                <div class="row">
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">School Name:</div>
                        <div id="sslc-school-name"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Board:</div>
                        <div id="sslc-board">State Board</div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Year of Passing:</div>
                        <div id="sslc-passing-year"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Total Marks:</div>
                        <div id="sslc-total-marks"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Percentage:</div>
                        <div id="sslc-percentage"></div>
                    </div>
                </div>
                <br>
                <div class="profile-title mt-2">HSC</div>
                <br>
                <div class="row">
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">School Name:</div>
                        <div id="hsc-school-name"> </div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Board:</div>
                        <div id="hsc-board">State Board</div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Year of Passing:</div>
                        <div id="hsc-passing-year"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Total Marks:</div>
                        <div id="hsc-total-marks"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Percentage:</div>
                        <div id="hsc-percentage"></div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Specialization</div>
                        <div id="specialization-title"></div>
                    </div>
                </div>
                <br>

                <div class="profile-title mt-2">Degrees</div>
                <div id="degrees-container"></div>

            </div>

            <!-- Course & Other Preferences soft-card p-6 m-4 -->
            <div class="soft-card p-6 m-4">
                <h2>Course & Other Preferences</h2>
                <br>
                <div class="row">
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">First Course Preference :</div>
                        <div id="course_pref1">Loading...</div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Second Course Preference 2:</div>
                        <div id="course_pref2">Loading...</div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Third Course Preference 3:</div>
                        <div id="course_pref3">Loading...</div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Faculty Reference:</div>
                        <div id="faculty_reference">Loading...</div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">How Did You Hear About Us:</div>
                        <div id="know_about_us">Loading...</div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Admission Type:</div>
                        <div id="admission_type">Loading...</div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Admission Method:</div>
                        <div id="admission_method">Loading...</div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Hostel:</div>
                        <div id="hostel">Loading...</div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Transport:</div>
                        <div id="transport">Loading...</div>
                    </div>
                    <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <div class="title">Date of Admission:</div>
                        <div id="contact-date-of-admission"></div>
                    </div>

                </div>
            </div>
            <div class="soft-card p-6 m-4" id="documents-container">
                <h2>Documentation</h2>
            </div>

        </div>
    </div>
    <script>
        $(document).ready(async function() {
            await fetch_all_individual_admission_data('<?= $student_id ?>');
        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
