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
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);

?>

    <script>
        const loadBgCard = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/view/bg-card.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token // Secure CSRF token
                    },
                    success: function(response) {
                        $('#bg-card').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        if (jqXHR.status == 401) {
                            // Redirect to the custom 401 error page
                            window.location.href = '<?= htmlspecialchars(GLOBAL_PATH . '/components/error/401.php', ENT_QUOTES, 'UTF-8') ?>';
                        } else {
                            const message = 'An error occurred. Please try again.';
                            showToast('error', message);
                        }
                        reject(); // Reject the promise
                    },

                });
            });
        };
        const overall_faculty_student_admission = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/view/overall_faculty_student_admission.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token // Secure CSRF token
                    },
                    success: function(response) {
                        $('#overall-profile-table').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        if (jqXHR.status == 401) {
                            // Redirect to the custom 401 error page
                            window.location.href = '<?= htmlspecialchars(GLOBAL_PATH . '/components/error/401.php', ENT_QUOTES, 'UTF-8') ?>';
                        } else {
                            const message = 'An error occurred. Please try again.';
                            showToast('error', message);
                        }
                        reject(); // Reject the promise
                    },

                });
            });
        };
        const capitalizeFirstLetters = (string) => {
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
                title += capitalizeFirstLetters(action) + " ";
            }

            // Remove any file extension if present (e.g., '.php' from 'faculty-roles-responsibilities.php')
            const cleanPath = lastPath.split('.')[0];

            title += cleanPath.replace(/-/g, ' '); // Replace hyphens with spaces for readability

            // Update the <h2> tag with the formatted title
            $('#action').text(title);
        }

        const view_individual_student_admission = (student_id) => {
            const params = {
                action: 'view',
                route: 'faculty',
                type: 'overall',
                id: student_id
            };

            // Construct the new URL with query parameters
            const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&id=${params.id}`;
            const newUrl = window.location.origin + window.location.pathname + queryString;
            // Use pushState to set the new URL and pass params as the state object
            window.history.pushState(params, '', newUrl);
            loadComponentsBasedOnURL(student_id);
        }

        const go_overall = () => {
            const params = {
                action: 'view',
                route: 'faculty',
                type: 'overall',
            };

            // Construct the new URL with query parameters
            const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}`;
            const newUrl = window.location.origin + window.location.pathname + queryString;
            // Use pushState to set the new URL and pass params as the state object
            window.history.pushState(params, '', newUrl);
            loadComponentsBasedOnURL();
        }

        const loadComponentsBasedOnURL = async (student_id) => {

            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action'); // e.g., 'add', 'edit'
            const route = urlParams.get('route'); // e.g., 'personal', 'faculty'
            const type = urlParams.get('type'); // e.g., 'personal', 'faculty'
            const tab = urlParams.get('tab'); // e.g., 'personal', 'faculty'
            const id = urlParams.get('id'); // e.g., 'personal', 'faculty'

            try {
                if (!action) {
                    // No action specified, load dashboard profile by default
                    await load_dashboard_profile();
                } else if (action == 'add') {
                    await load_update_personal_profile(student_id);
                } else if (action == 'view' && route == 'faculty' && !type) {
                    const params = {
                        action: 'view',
                        route: 'faculty',
                        type: 'overall',
                    };

                    // Construct the new URL with query parameters
                    const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}`;
                    const newUrl = window.location.origin + window.location.pathname + queryString;
                    await load_overall_personal_profile();
                } else if (action == 'view' && route == 'student' && type == 'overall' && !tab && !id) {
                    await load_overall_personal_profile();
                } else if (action == 'view' && route == 'faculty' && type == 'dashboard') {
                    await load_dashboard_profile();
                } else if (action == 'view' && route == 'faculty' && type == 'overall' && !tab && !id) {
                    // console.log("overall");
                    await load_overall_personal_profile();
                } else if (action == 'view' && route == 'faculty' && type == 'overall' && !tab && id) {
                    await load_admission_profile(id);
                } else if (action == 'add' && route == 'student' && type == 'personal' && tab == 'personal') {
                    await load_update_personal_profile(student_id);
                } else if (action == 'edit') {
                    // await logout();
                } else {
                    console.error("Unknown action");
                }


            } catch (error) {
                console.error('An error occurred while loading components:', error);
            }
        };


        const fetch_all_individual_admission_data = (student_id) => {
            $.ajax({
                type: 'GET',
                url: '<?= MODULES . '/faculty_student_admission/json/fetch_all_individual_admission_data.php' ?>',
                headers: {
                    'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                },
                data: {
                    'student_id': student_id
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.code == 200) {


                        const personal_data = response.data[0] ? response.data[0][0] : null;
                        const parent_data = response.data[1] ? response.data[1][0] : null;
                        const contact_data = response.data[2] ? response.data[2][0] : null;
                        const address_data = response.data[3] ? response.data[3][0] : null;
                        const sslc = response.data[4] ? response.data[4][0] : null;
                        const hsc = response.data[5] ? response.data[5][0] : null;
                        const degrees = response.data[6] || null;
                        const course = response.data[7][0] || null;
                        const document = response.data[8] || null;
                        console.log(response.data);

                        if (personal_data) {
                            $('#student-first-name').text(personal_data.student_first_name || '-');
                            $('#student-middle-name').text(personal_data.student_middle_name || '-');
                            $('#student-last-name').text(personal_data.student_last_name || '-');
                            $('#student-initial').text(personal_data.student_initial || '-');
                            $('#student-fullname').text(
                                `${personal_data.student_first_name || ''} ${personal_data.student_middle_name || ''} ${personal_data.student_last_name || ''} ${personal_data.student_initial || ''}`.trim()
                            );

                            $('#student-dob').text(personal_data.student_dob || '-');
                            $('#student-age').text(personal_data.student_age);
                            $('#student-gender').text(personal_data.student_gender_title || '-');
                            $('#student-blood-group').text(personal_data.student_blood_group_title || '-');

                            $('#student-official-email').text(personal_data.student_official_email || '-');
                        } else {
                            console.error('No personal data available to display.');
                        }
                        if (address_data) {
                            // Permanent Address
                            $('#permanent-door-no').text(address_data.student_address_no || '-');
                            $('#permanent-street').text(address_data.student_address_street || '-');
                            $('#permanent-area').text(address_data.student_address_locality || '-');
                            $('#permanent-district').text(address_data.student_address_district || '-');
                            $('#permanent-state').text(address_data.student_address_state || '-');
                            $('#permanent-pincode').text(address_data.student_address_pincode || '-');

                            // // Residential Address (assuming residential data is the same as permanent data in this case)
                            // $('#residential-door-no').text(address_data.student_address_no || '-');
                            // $('#residential-street').text(address_data.student_address_street || '-');
                            // $('#residential-area').text(address_data.student_address_locality || '-');
                            // $('#residential-district').text(address_data.student_address_district || '-');
                            // $('#residential-state').text(address_data.student_address_state || '-');
                            // $('#residential-pincode').text(address_data.student_address_pincode || '-');
                        } else {
                            console.error('No address data available to display.');
                        }
                        if (contact_data) {
                            // Phone Number (assuming mobile number is the same as phone number)
                            $('#contact-phone-number').text(contact_data.student_mobile_number || '-');
                            $('#contact-whatsapp-number').text(contact_data.student_whatsapp_number || '-');
                            $('#contact-alternativenumber-number').text(contact_data.student_alternative_contact_number || '-');
                            $('#student-email').text(contact_data.student_email_id || '-');
                            // Admission Type (If you have this data, you can update it accordingly)
                            $('#contact-admission-type').text(contact_data.student_admission_type || '-');

                            // Date of Admission (Assuming you have this data available)
                            $('#contact-date-of-admission').text(contact_data.student_date_of_admission || '-');

                            // Religion
                            $('#contact-religion').text(contact_data.student_religion || '-');

                            // Community
                            $('#contact-community').text(contact_data.student_community || '-');

                            // Aadhar Number
                            $('#contact-aadhar-number').text(contact_data.student_aadhar_number || '-');

                            // PAN Number (If available in the contact data)
                            $('#contact-pan-number').text(contact_data.student_pan_number || '-');
                        } else {
                            console.error('No contact data available to display.');
                        }
                        if (parent_data) {
                            // Father's Information
                            $('#father-name').text(parent_data.student_father_name || '-');
                            // $('#father-mobile').text(parent_data.student_father_mobile || '-'); // You may need to add mobile number data if available
                            $('#father-occupation').text(parent_data.student_father_occupation || '-');

                            // Mother's Information
                            $('#mother-name').text(parent_data.student_mother_name || '-');
                            // $('#mother-mobile').text(parent_data.student_mother_mobile || '-'); // Same as above, for mobile number
                            $('#mother-occupation').text(parent_data.student_mother_occupation || '-');

                            // Guardian's Information
                            $('#guardian-name').text(parent_data.student_guardian_name || '-');
                            $('#guardian-occupation').text(parent_data.student_guardian_occupation || '-');
                            // $('#guardian-mobile').text(parent_data.student_guardian_mobile || '-'); // If guardian's mobile number is available, update it
                        } else {
                            console.error('No parent data available to display.');
                        }
                        // Check if the SSL data is available and populate the fields
                        if (sslc) {
                            $('#sslc-school-name').text(sslc.sslc_institution_name || '-');
                            $('#sslc-board').text(sslc.board_title || '-');
                            $('#sslc-passing-year').text(sslc.sslc_passed_out_year || '-');
                            $('#sslc-total-marks').text(sslc.sslc_mark || '-');
                            $('#sslc-percentage').text(sslc.sslc_percentage || '-');
                        } else {
                            console.log("No SSLC data available.");
                        }

                        // Check if the HSC data is available and populate the fields
                        if (hsc) {
                            $('#hsc-school-name').text(hsc.hsc_institution_name || '-');
                            $('#hsc-board').text(hsc.board_title || '-');
                            $('#hsc-passing-year').text(hsc.hsc_passed_out_year || '-');
                            $('#hsc-total-marks').text(hsc.hsc_mark || '-');
                            $('#hsc-percentage').text(hsc.hsc_percentage || '-');
                            $('#specialization-title').text(hsc.specialization_title || '-'); // Assuming 'specialization_title' is the cut-off marks
                        } else {
                            console.log("No HSC data available.");
                        }
                        if (degrees) {
                            const degreesContainer = $("#degrees-container");
                            degreesContainer.empty(); // Clear any existing content

                            // Loop through each degree and create HTML for it
                            degrees.forEach(degree => {
                                const degreeRow = $(`
                                    <div class="row mt-3">
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Institution Name</div>
                                            <div class="value">${degree.student_edu_institution_name || '-'}</div>
                                        </div>
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Degree</div>
                                            <div class="value">${degree.degree_title || '-'}</div>
                                        </div>
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Specialization</div>
                                            <div class="value">${degree.specialization_title || '-'}</div>
                                        </div>
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Year of Passing</div>
                                            <div class="value">${degree.student_edu_passed_out_year || '-'}</div>
                                        </div>
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">CGPA</div>
                                            <div class="value">${degree.student_edu_cgpa || '-'}</div>
                                        </div>
                                        <div class="col col-4 col-lg-4 col-md-4 col-sm-6 col-xs-12 committee-section">
                                            <div class="title">Percentage</div>
                                            <div class="value">${degree.student_edu_percentage || '-'}%</div>
                                        </div>
                                    </div>
                                `);

                                // Append the degree row to the degrees container
                                degreesContainer.append(degreeRow);

                                // Append a horizontal line separator after each degree
                                degreesContainer.append('<hr>');
                            });
                        }
                        if (course) {
                            if (course.student_admission_date) {
                                let admissionDate = new Date(course.student_admission_date);


                                // Format the date as YYYY/MM/DD
                                console.log(course.student_admission_date);
                                let formattedDate = admissionDate.getFullYear() + '/' +
                                    (admissionDate.getMonth() + 1).toString().padStart(2, '0') + '/' +
                                    admissionDate.getDate().toString().padStart(2, '0');
                            } else {
                                formattedDate = "";
                            }
                            $('#course_pref1').text(course.dept1_title);
                            $('#course_pref2').text(course.dept2_title);
                            $('#course_pref3').text(course.dept3_title);
                            $('#contact-date-of-admission').text(formattedDate);

                            // Set the faculty reference
                            $('#faculty_reference').text(course.general_title + course.faculty_first_name + ' ' + course.faculty_last_name);

                            // Set the "How Did You Hear About Us"
                            const knowAboutUs = [{
                                    title: "Friends or Family",
                                    value: 1
                                },
                                {
                                    title: "Social Media",
                                    value: 2
                                },
                                {
                                    title: "Website",
                                    value: 3
                                },
                                {
                                    title: "Advertisement",
                                    value: 4
                                },
                                {
                                    title: "Events or Workshops",
                                    value: 5
                                },
                                {
                                    title: "Other",
                                    value: 6
                                }
                            ];
                            $('#know_about_us').text(knowAboutUs.find(item => item.value == course.student_admission_know_about_us)?.title || '-');

                            // Set the admission type
                            const admissionType = [{
                                    title: "Centac",
                                    value: 1
                                },
                                {
                                    title: "Management",
                                    value: 2
                                }
                            ];
                            $('#admission_type').text(admissionType.find(item => item.value == course.student_admission_type)?.title || '-');

                            // Set the admission method (Lateral Entry or New Admission)
                            const lateralEntry = [{
                                    title: "No",
                                    value: 1
                                },
                                {
                                    title: "Yes",
                                    value: 2
                                }
                            ];
                            $('#admission_method').text(lateralEntry.find(item => item.value == course.lateral_entry_year_of_study)?.title || '-');

                            // Set the hostel and transport
                            const residency = [{
                                    title: "YES",
                                    value: 1
                                },
                                {
                                    title: "NO",
                                    value: 0
                                }
                            ];
                            $('#hostel').text(residency.find(item => item.value == course.student_hostel)?.title || '-');

                            const transport = [{
                                    title: "Yes",
                                    value: 1
                                },
                                {
                                    title: "No",
                                    value: 0
                                }
                            ];
                            $('#transport').text(transport.find(item => item.value == course.student_transport)?.title || '-');

                            // Set admission status



                        } else {
                            console.error('No personal data available to display.');
                        }

                        if (document != null) {
                            const documentsContainer = $("#documents-container");

                            // Clear any existing content
                            documentsContainer.empty();

                            // Define icons for different file types
                            const icons = {
                                pdf: "<?= GLOBAL_PATH  . '/images/svgs/application_icons/pdfs.svg' ?>",
                                doc: "<?= GLOBAL_PATH  . '/images/svgs/application_icons/doc.svg' ?>",
                                docx: "<?= GLOBAL_PATH  . '/images/svgs/application_icons/doc.svg' ?>",
                                xls: "<?= GLOBAL_PATH  . '/images/svgs/application_icons/excel.svg' ?>",
                                xlsx: "<?= GLOBAL_PATH  . '/images/svgs/application_icons/excel.svg' ?>",
                                default: "<?= GLOBAL_PATH  . '/images/svgs/application_icons/unknown file.svg' ?>" // for unknown file types
                            };
                            const docPaths = {
                                1: "<?= GLOBAL_PATH . '/uploads/student_sslc/' ?>",
                                2: "<?= GLOBAL_PATH . '/uploads/student_hsc_certificate/' ?>",
                                3: "<?= GLOBAL_PATH . '/uploads/student_highest_qualification/' ?>",
                                4: "<?= GLOBAL_PATH . '/uploads/student_transfer_certificate/' ?>",
                                5: "<?= GLOBAL_PATH . '/uploads/student_permanent_integrated_certificate/' ?>",
                                6: "<?= GLOBAL_PATH . '/uploads/student_community_certificate/' ?>",
                                7: "<?= GLOBAL_PATH . '/uploads/student_residence_certificate/' ?>",
                                8: "<?= GLOBAL_PATH . '/uploads/student_profile_pic/' ?>"
                            };

                            // Iterate through the documents array and dynamically create document cards
                            document.forEach(doc => {

                                if (doc.student_doc_type == 8) {
                                    $('#view-profile-pic').attr('src', '<?= GLOBAL_PATH . '/uploads/student_profile_pic/' ?>' + doc.student_doc_path)
                                }
                                const fileName = doc.student_doc_path.split('/').pop();
                                const fileExtension = fileName.split('.').pop().toLowerCase();

                                const basePath = docPaths[doc.student_doc_type] || "<?= GLOBAL_PATH . '/uploads/' ?>"; // Default path if type is not defined
                                const iconSrc = icons[fileExtension] || icons.default;

                                const documentRow = $(`
                                                    <div class="profile-title mt-2">${fileName.split('-')[0]}</div>
                                                    <div class="row mt-5 document-card">
                                                        <div class="col col-1">
                                                            <div class="icon">
                                                                <img src="${iconSrc}" alt="Document Icon" width="50" height="50" />
                                                            </div>
                                                        </div>
                                                        <div class="col col-9">
                                                            <div class="document-details">
                                                                <h3>Document Name: ${fileName.split('-')[0]}</h3>
                                                                <p>Uploaded on: <span class="date-time">2024-11-01 10:00 AM</span></p>
                                                            </div>
                                                        </div>
                                                        <div class="col col-2">
                                                            <div class="download-option">
                                                                
                                                                <a href="${basePath}${doc.student_doc_path}" download>Download</a>

                                                            </div>
                                                        </div>
                                                    </div>
                                                `);

                                // Append the document row to the container
                                documentsContainer.append(documentRow);

                                // Optional: Add a horizontal line separator after each document row
                                documentsContainer.append('<hr>');
                            });


                        } else {
                            $("#documents-container").remove();
                        }

                    } else {

                        showToast(response.status, response.message);
                    }
                },
                error: function(error) {
                    showToast('error', 'Something went wrong. Please try again later.');
                }

            });
        }
        const load_admission_profile = (student_id) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/view/profile_view.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search
                    },
                    data: {
                        'student_id': student_id
                    },
                    success: function(response) {
                        $('#profile_view').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        console.error('Error loading top navbar:', message);
                        reject(); // Reject the promise
                    },

                });
            });
        };
        const fetch_faculty_status = (element) => {
            // Array of lateral entry options (No/Yes)
            const student_status = [{
                    title: "Enquiry Form",
                    value: 0, // SQL column: 'student_know_about_us' - refers to the source of knowledge (1=Friends or Family)
                },
                {
                    title: "Admitted",
                    value: 1, // SQL column: 'student_know_about_us' - refers to the source of knowledge (2=Social Media)
                },
                {
                    title: "Active",
                    value: 2, // SQL column: 'student_know_about_us' - refers to the source of knowledge (3=Website)
                },
                {
                    title: "Inactive ",
                    value: 3, // SQL column: 'student_know_about_us' - refers to the source of knowledge (4=Advertisement)
                },
                {
                    title: "Discontinued ",
                    value: 4, // SQL column: 'student_know_about_us' - refers to the source of knowledge (5=Events or Workshops)
                },
                {
                    title: "Declined",
                    value: 5, // SQL column: 'student_know_about_us' - refers to the source of knowledge (6=Other)
                }
            ];

            // Assign the courses array to a variable (could be renamed if needed)
            const student_status_data = student_status;
            // Log the degrees array to check the structure
            // Get the sibling elements for displaying suggestions
            const suggestions = element.siblings(".dropdown-suggestions");
            const value = element.siblings(".faculty-status-filter");
            // Call the function to show the suggestions
            showDropdownLoading(element.siblings(".dropdown-suggestions"))
            showSuggestions(student_status_data, suggestions, value, element);
        };

        const print_student_admission_pdf = (status, admissionyear, academicbatch) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/faculty_student_admission/json/overall_admission_table_data.php' ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token // Secure CSRF token
                    },
                    data: {
                        "admission_status": status,
                        "admission_method": admissionyear,
                        "academic_batch": academicbatch,
                        "type": 2
                    },
                    xhrFields: {
                        responseType: 'blob' // Set the response type to blob
                    },
                    success: function(response) {
                        console.log(response);
                        // Create a link to download the PDF
                        var blob = new Blob([response], {
                            type: 'application/pdf'
                        });
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = "Faculty Profile.pdf"; // Set the download filename
                        link.click(); // Trigger the download
                        resolve(); // Resolve the promise
                        showToast('success', "ðŸŽ‰âœ¨ " + link.download + " downloaded successfully! ðŸš€ Keep rocking, professor! ðŸ™Œ");


                    },
                    error: function(jqXHR) {
                        if (jqXHR.status == 401) {
                            // Redirect to the custom 401 error page
                            window.location.href = '<?= htmlspecialchars(GLOBAL_PATH . '/components/error/401.php', ENT_QUOTES, 'UTF-8') ?>';
                        } else {
                            const message = 'An error occurred. Please try again.';
                            showToast('error', message);
                        }
                        reject(); // Reject the promise
                    },
                });
            });
        };

        const load_faculty_overall_admission_table = (status, admissionyear, academicbatch) => {
            $('#profileTable').DataTable().destroy()
            $('#profileTable').DataTable({
                "serverSide": true,
                "ajax": {
                    "url": "<?= MODULES . '/faculty_student_admission/json/overall_admission_table_data.php' ?>",
                    "type": "POST",
                    "data": {
                        "admission_status": status,
                        "admission_year": admissionyear,
                        "academic_batch": academicbatch,
                        "type": 1
                    }

                },
                "columns": [{
                        "data": "s_no",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "student_first_name"
                    },
                    {
                        "data": "student_admission_type"
                    },
                    {
                        "data": "student_admission_category"
                    },
                    {
                        "data": "admission_status"
                    },
                    {
                        "data": "action",
                        "orderable": false,
                        "searchable": false
                    }
                ],
                "scrollX": true,
                "language": {
                    "emptyTable": "No data available matching the selected criteria.",
                    "loadingRecords": table_loading
                }
            });


            $('.dt-layout-row .dt-layout-table').css('width', '100%');
            $('.dt-layout-table .dt-layout-cell').css('width', '100%');
            $('.dt-scroll-headInner').css('width', '100%');
            $('.dataTable').css('width', '100%');

        }
        const loadFooter = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/footer.php', ENT_QUOTES, 'UTF-8') ?>',

                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#footer').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },

                });
            });
        };
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
