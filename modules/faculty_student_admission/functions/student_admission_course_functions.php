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
        const fetch_department_degrees = (element) => { // Renamed parameter from `this` to `element`
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/fetch_department_list.php' ?>',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const degrees = response.data;
                            const suggestions = element.siblings(".dropdown-suggestions")
                            const value = element.siblings("#1st-course,#2nd-course,#3rd-course")
                            showDropdownLoading(element.siblings(".dropdown-suggestions"))
                            showSuggestions(degrees, suggestions, value, element);
                        } else {
                            showToast(response.status, response.message)
                        }
                        resolve(response);
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        }
        const dropzones = $('.dropzone');
        const fileInputs = $('.file-input');


        dropzones.each(function() {
            const dropzone = $(this);
            const previewContainer = dropzone.parent().parent().next('.preview-container'); // Get the corresponding preview container


            dropzone.on('dragover', function(e) {
                e.preventDefault();
                dropzone.addClass('dragover');
            });

            dropzone.on('dragleave', function() {
                dropzone.removeClass('dragover');
            });

            dropzone.on('drop', function(e) {
                e.preventDefault();
                dropzone.removeClass('dragover');
                const files = e.originalEvent.dataTransfer.files;
                handleFiles(files, previewContainer); // Pass the preview container
            });
        });

        // File input change event
        fileInputs.on('change', function(e) {
            const files = e.target.files;
            const previewContainer = $(this).parent().parent().parent().next('.preview-container'); // Find the corresponding preview container

            handleFiles(files, previewContainer); // Pass the preview container
            $(this).siblings('.previous-link').val('');

        });

        function handleFiles(files, previewContainer) {
            previewContainer.empty(); // Clear previous previews
            $.each(files, function(index, file) {
                const previewCard = $('<div class="preview-card">');
                const fileName = $('<span>').text(file.name);

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        let thumbnail;

                        // Check the file type and create a preview
                        if (file.type.startsWith('image/')) {
                            // For image files
                            thumbnail = $('<img>').attr('src', e.target.result);
                        } else if (file.type == 'application/pdf') {
                            // For PDF files
                            thumbnail = $('<img>').attr('src', '<?= GLOBAL_PATH . '/images/svgs/application_icons/pdfs.svg' ?>'); // Use your PDF icon path
                        } else if (file.type == 'application/msword' || file.type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                            // For DOC/DOCX files
                            thumbnail = $('<img>').attr('src', '<?= GLOBAL_PATH . '/images/svgs/application_icons/doc.svg' ?>'); // Use your Word document icon path
                        } else if (file.type == 'application/vnd.ms-excel' || file.type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                            // For XLS/XLSX files
                            thumbnail = $('<img>').attr('src', '<?= GLOBAL_PATH . '/images/svgs/application_icons/excel.svg' ?>'); // Use your Excel icon path
                        } else {
                            // For other file types, you can add more conditions or a generic icon
                            thumbnail = $('<img>').attr('src', '<?= GLOBAL_PATH . '/images/svgs/application_icons/unknown file.svg' ?>'); // Use a generic file icon
                        }

                        // Append the thumbnail and file name to the preview card
                        previewCard.append(thumbnail).append(fileName);
                        previewContainer.append(previewCard);
                    };

                    // For images, read the file as data URL
                    if (file.type.startsWith('image/')) {
                        reader.readAsDataURL(file);
                    } else {
                        // If not an image, you can still read it, or skip to set an icon
                        reader.readAsDataURL(file); // Optional, to trigger loading; just for icon display
                    }
                }
            });
        }
        const fetch_faculty_list_degrees = (element) => { // Renamed parameter from `this` to `element`
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= MODULES . '/faculty_student_admission/json/fetch_faculty_name_list.php' ?>',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const degrees = response.data;
                            const suggestions = element.siblings(".dropdown-suggestions")
                            const value = element.siblings("#student-reference-1")
                            showDropdownLoading(element.siblings(".dropdown-suggestions"))
                            showSuggestions(degrees, suggestions, value, element);
                        } else {
                            showToast(response.status, response.message)
                        }
                        resolve(response);
                    },
                    error: function(error) {
                        reject(error);
                    }
                });
            });
        }
        const fetch_faculty_lateral_entry = (element) => {
            // Array of lateral entry options (No/Yes)
            const lateral_entry = [{
                    title: "No",
                    value: 1
                },
                {
                    title: "Yes",
                    value: 2
                }
            ];
            // Assign the courses array to a variable (could be renamed if needed)
            const degrees = lateral_entry;
            // Log the degrees array to check the structure
            // Get the sibling elements for displaying suggestions
            const suggestions = element.siblings(".dropdown-suggestions");
            const value = element.siblings("#lateral-entry");
            // Call the function to show the suggestions
            showDropdownLoading(element.siblings(".dropdown-suggestions"))
            showSuggestions(degrees, suggestions, value, element);
        };

        const fetch_faculty_residency = (element) => {
            // Array of lateral entry options (No/Yes)
            const residency = [{
                    title: "Hostel",
                    value: 1
                },
                {
                    title: "Days scholar ",
                    value: 0
                }
            ];
            // Assign the courses array to a variable (could be renamed if needed)
            const degrees = residency;
            // Log the degrees array to check the structure
            // Get the sibling elements for displaying suggestions
            const suggestions = element.siblings(".dropdown-suggestions");
            const value = element.siblings("#student-residency-1");
            // Call the function to show the suggestions
            showDropdownLoading(element.siblings(".dropdown-suggestions"))
            showSuggestions(degrees, suggestions, value, element);
        };

        const fetch_faculty_transport = (element) => {
            // Array of lateral entry options (No/Yes)
            const transport = [{
                    title: "Yes",
                    value: 1
                },
                {
                    title: "No ",
                    value: 0
                }
            ];
            // Assign the courses array to a variable (could be renamed if needed)
            const degrees = transport;
            // Log the degrees array to check the structure
            // Get the sibling elements for displaying suggestions
            const suggestions = element.siblings(".dropdown-suggestions");
            const value = element.siblings("#student-transport-1");
            // Call the function to show the suggestions
            showDropdownLoading(element.siblings(".dropdown-suggestions"))
            showSuggestions(degrees, suggestions, value, element);
        };

        const fetch_faculty_admission_type = (element) => {
            // Array of lateral entry options (No/Yes)
            const courses = [{
                    title: "Centac",
                    value: 1
                },
                {
                    title: "Management",
                    value: 2
                }
            ];
            // Assign the courses array to a variable (could be renamed if needed)
            const degrees = courses;
            // Log the degrees array to check the structure
            // Get the sibling elements for displaying suggestions
            const suggestions = element.siblings(".dropdown-suggestions");
            const value = element.siblings("#student-type-of-admission-1");
            // Call the function to show the suggestions
            showDropdownLoading(element.siblings(".dropdown-suggestions"))
            showSuggestions(degrees, suggestions, value, element);
        };

        const fetch_faculty_know_about_us = (element) => {
            // Array of lateral entry options (No/Yes)
            const know_about_us = [{
                    title: "Friends or Family",
                    value: 1, // SQL column: 'student_know_about_us' - refers to the source of knowledge (1=Friends or Family)
                },
                {
                    title: "Social Media",
                    value: 2, // SQL column: 'student_know_about_us' - refers to the source of knowledge (2=Social Media)
                },
                {
                    title: "Website",
                    value: 3, // SQL column: 'student_know_about_us' - refers to the source of knowledge (3=Website)
                },
                {
                    title: "Advertisement",
                    value: 4, // SQL column: 'student_know_about_us' - refers to the source of knowledge (4=Advertisement)
                },
                {
                    title: "Events or Workshops",
                    value: 5, // SQL column: 'student_know_about_us' - refers to the source of knowledge (5=Events or Workshops)
                },
                {
                    title: "Other",
                    value: 6, // SQL column: 'student_know_about_us' - refers to the source of knowledge (6=Other)
                }
            ];

            // Assign the courses array to a variable (could be renamed if needed)
            const know_about_us_data = know_about_us;
            // Log the degrees array to check the structure
            // Get the sibling elements for displaying suggestions
            const suggestions = element.siblings(".dropdown-suggestions");
            const value = element.siblings("#student-know-about-us-1");
            // Call the function to show the suggestions
            showDropdownLoading(element.siblings(".dropdown-suggestions"))
            showSuggestions(know_about_us_data, suggestions, value, element);
        };

        const fetch_faculty_concession = (element) => {
            // Array of lateral entry options (No/Yes)
            const student_concession = [{
                    title: "None",
                    value: 6, // SQL column: 'student_concession' - 0 means no concession
                },
                {
                    title: "Scholarship",
                    value: 1, // SQL column: 'student_concession' - 1 means the student has a scholarship
                },
                {
                    title: "Government Subsidy",
                    value: 2, // SQL column: 'student_concession' - 2 means the student receives a government subsidy
                },
                {
                    title: "Sports Quota",
                    value: 3, // SQL column: 'student_concession' - 3 means the student is getting a sports quota concession
                },
                {
                    title: "Cultural Quota",
                    value: 4, // SQL column: 'student_concession' - 4 means the student is getting a cultural quota concession
                },
                {
                    title: "Financial Aid",
                    value: 5, // SQL column: 'student_concession' - 5 means the student is getting financial aid
                }
            ];


            // Log the degrees array to check the structure
            // Get the sibling elements for displaying suggestions
            const suggestions = element.siblings(".dropdown-suggestions");
            const value = element.siblings("#student-concession-1");
            // Call the function to show the suggestions
            showDropdownLoading(element.siblings(".dropdown-suggestions"))
            showSuggestions(student_concession, suggestions, value, element);
        };
        const fetch_faculty_course = () => {
            $.ajax({
                type: 'GET',
                url: '<?= MODULES . '/faculty_student_admission/json/fetch_faculty_course.php' ?>',
                headers: {
                    'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.code == 200) {
                        const data = response.data;
                        const additionaldata = response.redata;


                        // $('#sslc-institution-name').val(sslc_data.sslc_institution_name);
                        $('#1st-course').val(data.student_course_preference1);
                        $('#1st-course-dummy').val(data.dept1_title);
                        $('#2nd-course').val(data.student_course_preference2);
                        $('#2nd-course-dummy').val(data.dept2_title);
                        $('#3rd-course').val(data.student_course_preference3);
                        $('#3rd-course-dummy').val(data.dept3_title);
                        if (data.faculty_first_name != null) {
                            $('#student-reference-dummy-1').val(data.general_title + ' ' + data.faculty_first_name + ' ' + data.faculty_last_name + ' ' + data.faculty_initial);
                        }
                        $('#student-reference-1').val(data.faculty_id);
                        $('#student-residency-dummy-1').val(additionaldata.residency_status['title']);
                        $('#student-residency-1').val(additionaldata.residency_status['value']);
                        $('#student-transport-dummy-1').val(additionaldata.student_transport['title']);
                        $('#student-transport-1').val(additionaldata.student_transport['value']);
                        $('#student-know-about-us-dummy-1').val(additionaldata.know_about_us['title']);
                        $('#student-know-about-us-1').val(additionaldata.know_about_us['value']);
                        $('#student-concession').val(additionaldata.student_concession['title']);
                        $('#student-concession-1').val(additionaldata.student_concession['value']);
                        $('#student-type-of-admission-dummy-1').val(additionaldata.student_admission_type['title']);
                        $('#student-type-of-admission-1').val(additionaldata.student_admission_type['value']);
                        $('#lateral-entry-dummy').val(additionaldata.lateral_entry_status['title']).trigger('blur');
                        $('#lateral-entry').val(additionaldata.lateral_entry_status['value']);
                        $('#continuing-year').val(data.student_concession_body);
                        $('#register-number').val(data.student_admission_reg_no);



                    } else if (response.code == 302) {
                        console.error("No data found");
                    } else {
                        showToast(response.status, response.message);
                    }
                },
                error: function(error) {
                    showToast('error', 'Something went wrong. Please try again later.');
                }
            });
        }
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
