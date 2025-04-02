<?php
include_once('../../../../../config/sparrow.php');

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

    <link rel="stylesheet" href="<?= MODULES . '/faculty_student_admission/css/student_admission_info_staff_interview.css' ?>" />
    <link rel="stylesheet" href="<?= MODULES . '/faculty_student_admission/css/profile_view.css' ?>" />
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/sparrow.css' ?>" />
    <link rel="stylesheet" href="<?= PACKAGES . '/datatables/datatables.min.css' ?>">
    <link rel="stylesheet" href="<?= PACKAGES . '/bulmacalendar/bulma-calendar.min.css' ?>">




    <div class="tab-nav">
        <button class="tab-btn upload" data-tab="0">Document Upload</button>
    </div>
    <section id="info">
        <div class="step-content active" data-step="4">
            <section id="update_personal_profile">
                <div class="step-content active" data-step="4">
                    <section id="upload_info">
                        <form id="faculty-document-upload-profile-info-form" method="POST" enctype="multipart/form-data">

                            <div class="upload-wrapper">
                                <div class="profile-pic-container mb-6">
                                    <div class="image-preview" id="imagePreview">
                                        <img src="" class="image-preview__image" id="imageDisplay" alt="">

                                        <span class="placeholder-text">"Upload your profile pic, so your teachers know who's asking for those deadlines!"</span>
                                    </div>
                                    <input type="file" id="profilePicInput" name="student_profile_pic" accept=".jpeg, .jpg, .png, .gif">
                                    <input type="hidden" class="previous-link" id="student-previous-profile-pic" name="student_previous_profile_pic" value="">
                                    <input type="hidden" id="student-profile-pic-id" name="student_profile_pic_id" value="0">
                                </div>


                                <div class="row align-items-center ">
                                    <div class="col col-6">
                                        <div class="dropzone" id="dropzone">
                                            <p>Drag & Drop files here or <span class="browse-text">Browse</span></p>
                                            <input type="file" id="file-upload" name="student_sslc_certificate" class="file-input">
                                            <input type="hidden" class="previous-link" id="previous-student-sslc" name="previous_student_sslc" value="">
                                            <input type="hidden" id="student-sslc-id" name="student_sslc_id" value="0">
                                        </div>
                                    </div>
                                    <div class="col col-6 text-left">
                                        <label for="file-upload" class="upload-instruction">Upload your SSLC Certificate</label>
                                    </div>
                                </div>
                                <div class="preview-container" id="student-sslc-certificate-preview-container"></div>

                                <div class="row align-items-center ">
                                    <div class="col col-6">
                                        <div class="dropzone" id="dropzone">
                                            <p>Drag & Drop files here or <span class="browse-text">Browse</span></p>
                                            <input type="file" id="file-upload" name="student_hsc_certificate" class="file-input">
                                            <input type="hidden" class="previous-link" id="previous-student-hsc" name="previous_student_hsc" value="">
                                            <input type="hidden" id="student-hsc-id" name="student_hsc_id" value="0">
                                        </div>
                                    </div>
                                    <div class="col col-6 text-left">
                                        <label for="file-upload" class="upload-instruction">Upload your HSC Certificate</label>
                                    </div>
                                </div>
                                <div class="preview-container" id="student-hsc-certificate-preview-container"></div>

                                <div class="row align-items-center ">
                                    <div class="col col-6">
                                        <div class="dropzone" id="dropzone">
                                            <p>Drag & Drop files here or <span class="browse-text">Browse</span></p>
                                            <input type="file" id="file-upload" class="file-input" name="student_highest_qualification_certificate">
                                            <input type="hidden" class="previous-link" id="previous-student-highest-qualification" name="previous_student_highest_qualification" value="">
                                            <input type="hidden" id="student-highest-qualification-id" name="student_highest_qualification_id" value="0">
                                        </div>
                                    </div>
                                    <div class="col col-6 text-left">
                                        <label for="file-upload" class="upload-instruction">Upload your Highest Qualification Certificate</label>
                                    </div>
                                </div>
                                <div class="preview-container" id="student-highest-qualification-certificate-preview-container"></div>
                                <!-- student_transfer_certificate -->
                                <div class="row align-items-center ">
                                    <div class="col col-6">
                                        <div class="dropzone" id="dropzone">
                                            <p>Drag & Drop files here or <span class="browse-text">Browse</span></p>
                                            <input type="file" id="file-upload" class="file-input" name="student_transfer_certificate">
                                            <input type="hidden" class="previous-link" id="previous-student-transfer-certificate-id" name="previous_student_transfer_certificate" value="">
                                            <input type="hidden" id="student-transfer-certificate-id" name="student_transfer_certificate_id" value="0">
                                        </div>
                                    </div>
                                    <div class="col col-6 text-left">
                                        <label for="file-upload" class="upload-instruction">Upload your Transfer Certificate</label>
                                    </div>
                                </div>
                                <div class="preview-container" id="student-transfer-certificate-preview-container"></div>

                                <!-- permanent_integrated_certificate -->
                                <div class="row align-items-center ">
                                    <div class="col col-6">
                                        <div class="dropzone" id="dropzone">

                                            <p>Drag & Drop files here or <span class="browse-text">Browse</span></p>
                                            <input type="file" id="file-upload" class="file-input" name="student_permanent_integrated_certificate">
                                            <input type="hidden" class="previous-link" id="previous-student-permanent-integrated-certificate-id" name="previous_student_permanent_integrated_certificate" value="">
                                            <input type="hidden" id="student-permanent-integrated-certificate-id" name="student_permanent_integrated_certificate_id" value="0">
                                        </div>
                                    </div>
                                    <div class="col col-6 text-left">
                                        <label for="file-upload" class="upload-instruction">Upload Student Permanent Integrated Certificate (PIC)</label>
                                    </div>
                                </div>
                                <div class="preview-container" id="student-permanent-integrated-certificate-preview-container"></div>



                                <!--  -->
                                <div class="row align-items-center ">
                                    <div class="col col-6">
                                        <div class="dropzone" id="dropzone">

                                            <p>Drag & Drop files here or <span class="browse-text">Browse</span></p>
                                            <input type="file" id="file-upload" class="file-input" name="student_community_certificate">
                                            <input type="hidden" class="previous-link" id="previous-student-community-certificate-id" name="previous_student_community_certificate" value="">
                                            <input type="hidden" id="student-community-certificate-id" name="student_community_certificate_id" value="0">
                                        </div>
                                    </div>
                                    <div class="col col-6 text-left">
                                        <label for="file-upload" class="upload-instruction">Upload Student Community Certificate</label>
                                    </div>
                                </div>
                                <div class="preview-container" id="student-community-certificate-preview-container"></div>

                                <!--  -->
                                <div class="row align-items-center ">
                                    <div class="col col-6">
                                        <div class="dropzone" id="dropzone">

                                            <p>Drag & Drop files here or <span class="browse-text">Browse</span></p>
                                            <input type="file" id="file-upload" class="file-input" name="student_residence_certificate">
                                            <input type="hidden" class="previous-link" id="previous-student-residence-certificate-id" name="previous_student_residence_certificate" value="">
                                            <input type="hidden" id="student-residence-certificate-id" name="student_residence_certificate_id" value="0">
                                        </div>
                                    </div>
                                    <div class="col col-6 text-left">
                                        <label for="file-upload" class="upload-instruction">Upload Student Residence Certificate</label>
                                    </div>
                                </div>
                                <div class="preview-container" id="student-residence-certificate-preview-container"></div>



                            </div>
                            <div class="form-navigation">
                                <button class="nav-next text-left" id="document_upload_info_faculty_form_prev_btn" type="button">Previous</button>
                                <button class="nav-back text-right" id="document_upload_info_faculty_form_nxt_btn" type="submit">Finish</button>
                            </div>
                        </form>
                    </section>
                </div>
            </section>
        </div>
    </section>


    <script>
        fetch_faculty_documents()

        init_dropzones();
        $('#profilePicInput').on('change', function(event) {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imageDisplay').attr('src', e.target.result).show();
                    $('.placeholder-text').hide(); // Hide the placeholder text
                }
                reader.readAsDataURL(file);
            } else {
                $('#imageDisplay').hide();
                $('.placeholder-text').show(); // Show the placeholder text if no file
            }
        });

        // Trigger file input when clicking on the image preview
        $('#imagePreview').on('click', function() {
            $('#profilePicInput').click();
        });
        $('#document_upload_info_faculty_form_prev_btn').on('click', async function() {
            showComponentLoading(1);

            const params = {
                action: 'add',
                route: 'faculty',
                type: 'fees',
                tab: 'concession_details'
            };

            const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
            const newUrl = window.location.origin + window.location.pathname + queryString;
            window.history.pushState(params, '', newUrl);

            await loadUrlBasedOnURL();
            setTimeout(function() {
                hideComponentLoading();
            }, 100);
        });
        // //id=document_upload_info_faculty_form_prev_btn on click
        // $('#document_upload_info_faculty_form_prev_btn').on('click', function() {
        //     const params = {
        //         action: 'add',
        //         route: 'faculty',
        //         type: 'fees',
        //         tab: 'concession_details'
        //     };

        //     // Construct the new URL with query parameters
        //     const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
        //     const newUrl = window.location.origin + window.location.pathname + queryString;
        //     // Use pushState to set the new URL and pass params as the state object
        //     window.history.pushState(params, '', newUrl);
        //     load_update_admission_document_components();
        // });


        //faculty-document-upload-profile-info-form on submit
        $('#faculty-document-upload-profile-info-form').on('submit', async function(e) {
            try {
                showComponentLoading()

                e.preventDefault();

                const formData = new FormData(this);
                $.ajax({
                    type: 'POST',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/ajax/faculty_document_upload_profile_info_form.php', ENT_QUOTES, 'UTF-8') ?>',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    success: function(response) {
                        const data = JSON.parse(response);

                        if (data.code == 200) {
                            showToast('success', data.message);
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
                            window.location.href = newUrl;
                            location.reload();
                        } else {
                            showToast('error', data.message);
                        }
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                    }
                });
            } catch (error) {
                // get error message
                const errorMessage = error.message || 'An error occurred while loading the page.';
                await insert_error_log(errorMessage)
                await load_error_popup()
                console.error('An error occurred while loading:', error);
            } finally {
                // Hide the loading screen once all operations are complete
                setTimeout(function() {
                    hideComponentLoading(); // Delay hiding loading by 1 second
                }, 100)
            }
        });




        // Bind drag-and-drop events for each dropzone



        $(document).ready(async function() {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const route = urlParams.get('route');
                const action = urlParams.get('action');
                const type = urlParams.get('type');
                const tab = urlParams.get('tab');

                // Condition to load the correct form based on URL parameters
                if (action == 'add' && route == 'faculty' && type == 'documentupload') {
                    if (tab == 'document') {
                        // load_certificate_upload_info_form();
                        $('.tab-btn.upload').addClass('active');
                    }
                } else {
                    console.log('No matching condition for route and action');
                }

            } catch (error) {
                console.error('An error occurred while processing:', error);
            }
        });
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
