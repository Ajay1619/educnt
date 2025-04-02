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
        const fetch_faculty_experience_designation = (element) => { // Renamed parameter from `this` to `element`
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '<?= GLOBAL_PATH . '/json/fetch_faculty_experience_designation.php' ?>',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.code == 200) {
                            const faculty_experience_designation = response.data;
                            const suggestions = element.siblings(".dropdown-suggestions")
                            const value = element.siblings(".experience-designation")
                            showSuggestions(faculty_experience_designation, suggestions, value, element);
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

        const fetch_faculty_experience_data = () => {
            $.ajax({
                type: 'GET',
                url: '<?= MODULES . '/faculty_profile/json/fetch_faculty_experience_data.php' ?>',
                headers: {
                    'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.code == 200) {
                        const data = response.data;
                        let experienceCount = 0; // Initialize diploma count
                        var experienceTemplate = "";
                        var field_of_experience = [{
                                title: 'Teaching',
                                value: 1
                            },
                            {
                                title: 'Industry',
                                value: 2
                            }
                        ];
                        // Loop through each experience entry in the response data
                        data.forEach(experience => {
                            experienceCount++; // Increment experience count
                            $("#experience-count").val(experienceCount);

                            experienceTemplate += `
                            <input type="hidden" name="experience_id[]" class="experience-id" value="${experience.faculty_exp_id}">
                            <div class="row">
                            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                                <div class="input-container">
                                    <div class="input-container dropdown-container">
                                        <input type="text" id="field-of-experience-dummy-${experienceCount}" name="field_of_experience_dummy[]" class="auto field-of-experience-dummy dropdown-input" placeholder=" " value="${experience.faculty_exp_field_of_experience == 1 ? 'Teaching' : experience.faculty_exp_field_of_experience == 2 ? 'Industry' : ''}" readonly>
                                        <label class="input-label" for="field-of-experience-dummy">Select Your Field Of Experience</label>
                                        <input type="hidden" name="field_of_experience[]" value="${experience.faculty_exp_field_of_experience}" class="field-of-experience" id="field-of-experience-${experienceCount}">
                                        <span class="dropdown-arrow">&#8964;</span>
                                        <div class="dropdown-suggestions" id="field-of-experience-suggestions"></div>
                                    </div>

                                </div>
                            </div>
                            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                                <div class="input-container">
                                    <input type="text" id="experience-industry-name-${experienceCount}" value="${experience.faculty_exp_industry_name}" class="experience-industry-name" name="experience_industry_name[]" placeholder=" ">
                                    <label class="input-label" for="experience-industry-name">Enter Your Industry
                                        Name
                                    </label>
                                </div>
                            </div>
                            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                                <div class="input-container">
                                    <input type="text" id="experience-designation-${experienceCount}" class="experience-designation"  name="experience_designation[]" placeholder=" " value="${experience.faculty_exp_designation}">
                                    <label class="input-label" for="experience-designation">Enter Your
                                        Designation</label>
                                </div>
                            </div>
                            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                                <div class="input-container">
                                    <input type="text" id="experience-industry-department-${experienceCount}" name="experience_industry_department[]"  class="experience-industry-department"  placeholder=" " value="${experience.faculty_exp_specialization}">
                                    <label class="input-label" for="experience-industry-department">Enter Your
                                        Department</label>
                                </div>
                            </div>
                            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                                <div class="input-container date">
                                    <input type="date" class="bulmaCalender" id="experience-industry-start-date" name="experience_industry_start_date[]" placeholder="<?= BULMA_DATE_FORMAT ?>" value="${experience.faculty_exp_start_date}">
                                    <label class="input-label" for="experience-industry-start-date">Enter Your Timespan
                                        Start Date</label>
                                </div>
                            </div>
                            <div class="col col-3 col-lg-4 col-md-4 col-sm-6 col-xs-12  ">
                                <div class="input-container date">
                                    <input type="date" class="bulmaCalender" id="experience-industry-end-date" name="experience_industry_end_date[]" placeholder="<?= BULMA_DATE_FORMAT ?>" value="${experience.faculty_exp_end_date}">
                                    <label class="input-label" for="experience-industry-end-date">Enter Your Timespan
                                        End Date</label>
                                </div>
                            </div>
                        </div>
                                <hr>
                            `;
                        });

                        $('.experience-list').html(experienceTemplate);

                        $('.field-of-experience-dummy').on('click', async function() {
                            const element = $(this);
                            const suggestions = element.siblings(".dropdown-suggestions")
                            const value = element.siblings(".field-of-experience")
                            showSuggestions(field_of_experience, suggestions, value, element);

                        });
                        $('.experience-designation').on('input', function() {
                            input_validation($(this))
                        });
                        $('.experience-industry-name').on('input', function() {
                            input_validation($(this))
                        });
                        $('.experience-industry-department').on('input', function() {
                            input_validation($(this))
                        });
                        // const options = {
                        //     type: 'date',
                        //     dateFormat: '<?= BULMA_DATE_FORMAT ?>', // Set your preferred date format
                        //     validateLabel: "",
                        //     closeOnSelect: true
                        // };


                        //const calendar = bulmaCalendar.attach('[type="date"]', options);
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

        const fetch_faculty_documents = () => {
            $.ajax({
                type: 'GET',
                url: '<?= MODULES . '/faculty_profile/json/fetch_faculty_documents_data.php' ?>',
                headers: {
                    'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.code == 200) {
                        const data = response.data;
                        //data foreach function
                        // Initialize an array to hold experience certificates paths and IDs
                        let experiencePaths = [];
                        let experienceIds = [];

                        // Define preview container variables for each document type
                        const resumePreviewContainer = $('#faculty-resume-preview-container');
                        const sslcPreviewContainer = $('#faculty-sslc-certificate-preview-container');
                        const hscPreviewContainer = $('#faculty-hsc-certificate-preview-container');
                        const qualificationPreviewContainer = $('#faculty-highest-qualification-certificate-preview-container');
                        const experiencePreviewContainer = $('#faculty-experience-certificate-preview-container');


                        // Loop through each document
                        data.forEach(document => {
                            const previewCard = $('<div class="preview-card">');
                            const fileName = $('<span>').text(document.faculty_doc_path);


                            if (document.faculty_doc_type == 1) { // Resume
                                $('#previous-faculty-resume').val(document.faculty_doc_path);
                                $('#faculty-resume-id').val(document.faculty_doc_id);
                                let thumbnail = getThumbnail(document.faculty_doc_path, 'faculty_resumes/');

                                // Append the thumbnail and file name to the preview card
                                previewCard.append(thumbnail).append(fileName);
                                resumePreviewContainer.append(previewCard);

                            } else if (document.faculty_doc_type == 2) { // SSLC
                                $('#previous-faculty-sslc').val(document.faculty_doc_path);
                                $('#faculty-sslc-id').val(document.faculty_doc_id);
                                let thumbnail = getThumbnail(document.faculty_doc_path, 'faculty_sslc_certificate/');

                                // Append the thumbnail and file name to the preview card
                                previewCard.append(thumbnail).append(fileName);
                                sslcPreviewContainer.append(previewCard);

                            } else if (document.faculty_doc_type == 3) { // HSC
                                $('#previous-faculty-hsc').val(document.faculty_doc_path);
                                $('#faculty-hsc-id').val(document.faculty_doc_id);
                                let thumbnail = getThumbnail(document.faculty_doc_path, 'faculty_hsc_certificate/');

                                // Append the thumbnail and file name to the preview card
                                previewCard.append(thumbnail).append(fileName);
                                hscPreviewContainer.append(previewCard);

                            } else if (document.faculty_doc_type == 4) { // Highest Qualification
                                $('#previous-faculty-highest-qualification').val(document.faculty_doc_path);
                                $('#faculty-highest-qualification-id').val(document.faculty_doc_id);
                                let thumbnail = getThumbnail(document.faculty_doc_path, 'faculty_highest_qualification_certificate/');

                                // Append the thumbnail and file name to the preview card
                                previewCard.append(thumbnail).append(fileName);
                                qualificationPreviewContainer.append(previewCard);

                            } else if (document.faculty_doc_type == 5) { // Experience Certificates
                                let thumbnail = getThumbnail(document.faculty_doc_path, 'faculty_experience_certificate/');

                                // Append the thumbnail and file name to the preview card
                                previewCard.append(thumbnail).append(fileName);
                                experiencePaths.push(document.faculty_doc_path);
                                experienceIds.push(document.faculty_doc_id);
                                experiencePreviewContainer.append(previewCard);
                            } else if (document.faculty_doc_type == 6) { // Profile Pic Certificates
                                $('#faculty-previous-profile-pic').val(document.faculty_doc_path);
                                $('#faculty-profile-pic-id').val(document.faculty_doc_id);
                                $('#imageDisplay').attr('src', '<?= GLOBAL_PATH . '/uploads/faculty_profile_pic/' ?>' + document.faculty_doc_path);
                                $('#imageDisplay').show()
                                $('.placeholder-text').hide();

                            }
                        });


                        $('#hidden-experience-tags').empty(); // Clear any existing hidden inputs
                        // Append hidden inputs for experience certificates
                        experiencePaths.forEach((path, index) => {
                            $('<input>').attr({
                                type: 'hidden',
                                id: `faculty-experience-certificate-${index}`, // Optional: Unique ID for each input
                                name: 'previous_faculty_experience[]', // Use [] to create an array in the backend
                                value: path
                            }).appendTo('#hidden-experience-tags'); // Append to the specified container
                        });

                        // Optional: If you also want to append IDs
                        experienceIds.forEach((id, index) => {
                            $('<input>').attr({
                                type: 'hidden',
                                id: `faculty-experience-id-${index}`, // Optional: Unique ID for each input
                                name: 'previous_faculty_experience_id[]', // Use [] to create an array in the backend
                                value: id
                            }).appendTo('#hidden-experience-tags'); // Append to the specified container
                        });
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

        function getThumbnail(filePath, location) {
            if (filePath.endsWith('.png') || filePath.endsWith('.svg') || filePath.endsWith('.jpg') || filePath.endsWith('.jpeg')) {
                return $('<img>').attr('src', '<?= GLOBAL_PATH . '/uploads/' ?>' + location + filePath); // Use actual image path for images
            } else if (filePath.endsWith('.pdf')) {
                return $('<img>').attr('src', '<?= GLOBAL_PATH . '/images/svgs/application_icons/pdfs.svg' ?>');
            } else if (filePath.endsWith('.doc') || filePath.endsWith('.docx')) {
                return $('<img>').attr('src', '<?= GLOBAL_PATH . '/images/svgs/application_icons/doc.svg' ?>');
            } else if (filePath.endsWith('.xls') || filePath.endsWith('.xlsx')) {
                return $('<img>').attr('src', '<?= GLOBAL_PATH . '/images/svgs/application_icons/excel.svg' ?>');
            } else {
                return $('<img>').attr('src', '<?= GLOBAL_PATH . '/images/svgs/application_icons/unknown_file.svg' ?>');
            }
        }



        // Function to handle file selection and preview display
        function handleFiles(files, previewContainer) {
            console.log(files)
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
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
