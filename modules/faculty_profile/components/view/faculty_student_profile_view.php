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
    <button type="submit" onclick="go_overall()" id="back">&#128281;</button>
    <div class="container">
        <div class="profile_view_card">
            <!-- SVG Background -->
            <div class="svg-background">
                <img src="<?= GLOBAL_PATH . '/images/svgs/pngegg.svg' ?>" alt="SVG Background" class="svg-img">
                <br>
                <h1 class="bg_text">Profile</h1>
            </div>

            <!-- Profile Picture and Name Section -->
            <div class="profile-header">
                <img src="<?= GLOBAL_PATH . '/images/svcet.png' ?>" alt="Profile Picture" class="profile-pic">
                <div class="name-designation">
                    <h1 id="student-fullname"></h1>
                    <p class="role">Student</p>
                </div>
            </div>

            <div class="profile_view_card">
                <h2>Personal Information</h2>
                <br>
                <div class="row">
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>First Name:</strong>
                        <div id="student-first-name">John</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Middle Name:</strong>
                        <div id="student-middle-name">A</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Last Name:</strong>
                        <div id="student-last-name">Doe</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Initial:</strong>
                        <div id="student-initial">J</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Full Name:</strong>
                        <div id="student-fullname">John A Doe J</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Date of Birth:</strong>
                        <div id="student-dob">01/01/1990</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Age:</strong>
                        <div id="student-age">30</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Gender:</strong>
                        <div id="student-gender">Male</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Blood Group:</strong>
                        <div id="student-blood-group">O+</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Email ID:</strong>
                        <div id="student-email">john@example.com</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Official Email ID:</strong>
                        <div id="student-official-email">john.official@example.com</div>
                    </div>
                </div>
            </div>



            <div class="profile_view_card">
                <h2>Address Details</h2>
                <br>
                <h3>Permanent Address</h3>
                <br>
                <div class="row">
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Door No.:</strong>
                        <div id="permanent-door-no">123</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Street:</strong>
                        <div id="permanent-street">Main St.</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Area:</strong>
                        <div id="permanent-area">Downtown</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>District:</strong>
                        <div id="permanent-district">City</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>State:</strong>
                        <div id="permanent-state">State</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Pincode:</strong>
                        <div id="permanent-pincode">123456</div>
                    </div>
                </div>

                <br>
                <!-- <h3>Residential Address</h3>
    <br>
    <div class="row">
        <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
            <strong>Door No.:</strong> <div id="residential-door-no">123</div>
        </div>
        <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
            <strong>Street:</strong> <div id="residential-street">Main St.</div>
        </div>
        <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
            <strong>Area:</strong> <div id="residential-area">Downtown</div>
        </div>
        <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
            <strong>District:</strong> <div id="residential-district">City</div>
        </div>
        <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
            <strong>State:</strong> <div id="residential-state">State</div>
        </div>
        <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
            <strong>Pincode:</strong> <div id="residential-pincode">123456</div>
        </div>
    </div> -->
            </div>


            <!-- Contact Information profile_view_card -->
            <div class="profile_view_card">
                <h2>Contact Information</h2>
                <br>
                <div class="row">
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Phone Number:</strong>
                        <div id="contact-phone-number">123-456-7890</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>WhatsApp Number:</strong>
                        <div id="contact-whatsapp-number">123-456-7890</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Alternative Contact Number:</strong>
                        <div id="contact-alternativenumber-number">123-456-7890</div>
                    </div>



                </div>
                <img src="<?= GLOBAL_PATH . '/images/svgs/sidenavbar_icons/old_icons/edit.svg' ?>" alt="Edit" class="edit-icon">
            </div>


            <!-- Parent/Guardian Information profile_view_card -->
            <div class="profile_view_card">
                <h2>Parent/Guardian Information</h2>
                <br>
                <h3>Father’s Information</h3>
                <br>
                <div class="row">
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Father’s Name:</strong>
                        <div id="father-name">Robert Doe</div>
                    </div>
                    <!-- <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
            <strong>Father’s Mobile Number:</strong> <div id="father-mobile">123-456-7890</div>
        </div> -->
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Father’s Occupation:</strong>
                        <div id="father-occupation">Engineer</div>
                    </div>
                </div>
                <br>
                <h3>Mother’s Information</h3>
                <br>
                <div class="row">
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Mother’s Name:</strong>
                        <div id="mother-name">Jane Doe</div>
                    </div>
                    <!-- <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
            <strong>Mother’s Mobile Number:</strong> <div id="mother-mobile">098-765-4321</div>
        </div> -->
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Mother’s Occupation:</strong>
                        <div id="mother-occupation">Teacher</div>
                    </div>
                </div>
                <br>
                <h3>Guardian’s Information</h3>
                <br>
                <div class="row">
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Guardian’s Name:</strong>
                        <div id="guardian-name">N/A</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Guardian’s Occupation:</strong>
                        <div id="guardian-occupation">N/A</div>
                    </div>
                    <!-- <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
            <strong>Guardian’s Mobile Number:</strong> <div id="guardian-mobile">N/A</div>
        </div> -->
                </div>
            </div>


            <!-- Educational Details profile_view_card -->
            <div class="profile_view_card">
                <h2>Educational Details</h2>
                <br>
                <h3>SSLC</h3>
                <br>
                <div class="row">
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>School Name:</strong>
                        <div id="sslc-school-name">ABC High School</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Board:</strong>
                        <div id="sslc-board">State Board</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Year of Passing:</strong>
                        <div id="sslc-passing-year">2005</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Total Marks:</strong>
                        <div id="sslc-total-marks">500</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Percentage:</strong>
                        <div id="sslc-percentage">85%</div>
                    </div>
                </div>
                <br>
                <h3>HSC</h3>
                <br>
                <div class="row">
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>School Name:</strong>
                        <div id="hsc-school-name">XYZ College</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Board:</strong>
                        <div id="hsc-board">State Board</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Year of Passing:</strong>
                        <div id="hsc-passing-year">2007</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Total Marks:</strong>
                        <div id="hsc-total-marks">600</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Percentage:</strong>
                        <div id="hsc-percentage">90%</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Specialization</strong>
                        <div id="specialization-title">95%</div>
                    </div>
                </div>
                <br>

                <div class="profile-title mt-2">Degrees</div>
                <div id="degrees-container"></div>

                <img src="<?= GLOBAL_PATH . '/images/svgs/sidenavbar_icons/old_icons/edit.svg' ?>" alt="Edit" class="edit-icon">
            </div>

            <!-- Course & Other Preferences profile_view_card -->
            <div class="profile_view_card">
                <h2>Course & Other Preferences</h2>
                <br>
                <div class="row">
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Course Preference 1:</strong>
                        <div id="course_pref1">Loading...</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Course Preference 2:</strong>
                        <div id="course_pref2">Loading...</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Course Preference 3:</strong>
                        <div id="course_pref3">Loading...</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Faculty Reference:</strong>
                        <div id="faculty_reference">Loading...</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>How Did You Hear About Us:</strong>
                        <div id="know_about_us">Loading...</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Admission Type:</strong>
                        <div id="admission_type">Loading...</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Admission Method:</strong>
                        <div id="admission_method">Loading...</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Hostel:</strong>
                        <div id="hostel">Loading...</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Transport:</strong>
                        <div id="transport">Loading...</div>
                    </div>
                    <div class="col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12 detail-item">
                        <strong>Date of Admission:</strong>
                        <div id="contact-date-of-admission">01/09/2020</div>
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
