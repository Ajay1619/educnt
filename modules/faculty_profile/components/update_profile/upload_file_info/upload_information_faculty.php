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
    <div class="tab-nav">
        <button class="tab-btn active" data-tab="0">Document Upload</button>
    </div>
    <section id="update_personal_profile">
        <div class="step-content active" data-step="4">
            <section id="upload_info">
                <form id="faculty-document-upload-profile-info-form" method="POST" enctype="multipart/form-data">
                    <div class="upload-wrapper">
                        <div class="profile-pic-container mb-6">
                            <div class="image-preview" id="imagePreview">
                                <img src="" class="image-preview__image" id="imageDisplay" alt="">
                                <span class="placeholder-text">"Upload your profile pic, students want to see who assigns all that homework!"</span>
                            </div>
                            <!-- Accepting only image formats -->
                            <input type="file" id="profilePicInput" name="faculty_profile_pic" accept=".jpeg, .jpg, .png, .gif">
                            <input type="hidden" class="previous-link" id="faculty-previous-profile-pic" name="faculty_previous_profile_pic" value="">
                            <input type="hidden" id="faculty-profile-pic-id" name="faculty_profile_pic_id" value="0">
                        </div>

                        <div class="row align-items-center">
                            <div class="col col-6">
                                <div class="dropzone" id="dropzone">
                                    <p>Drag & Drop files here or <span class="browse-text">Browse</span></p>
                                    <!-- Accepting document and image formats -->
                                    <input type="file" id="file-upload" name="faculty_resume" class="file-input" accept=".pdf, .doc, .docx, .jpeg, .jpg, .png">
                                    <input type="hidden" class="previous-link" id="previous-faculty-resume" name="previous_faculty_resume" value="">
                                    <input type="hidden" id="faculty-resume-id" name="faculty_resume_id" value="0">
                                </div>
                            </div>
                            <div class="col col-6 text-left">
                                <label for="file-upload" class="upload-instruction">Upload your Resume</label>
                            </div>
                        </div>
                        <div class="preview-container" id="faculty-resume-preview-container"></div>

                        <!-- Similar structure for other document upload fields -->
                        <div class="row align-items-center">
                            <div class="col col-6">
                                <div class="dropzone" id="dropzone">
                                    <p>Drag & Drop files here or <span class="browse-text">Browse</span></p>
                                    <input type="file" id="file-upload" name="faculty_sslc_certificate" class="file-input" accept=".pdf, .doc, .docx, .jpeg, .jpg, .png">
                                    <input type="hidden" class="previous-link" id="previous-faculty-sslc" name="previous_faculty_sslc" value="">
                                    <input type="hidden" id="faculty-sslc-id" name="faculty_sslc_id" value="0">
                                </div>
                            </div>
                            <div class="col col-6 text-left">
                                <label for="file-upload" class="upload-instruction">Upload your SSLC Certificate</label>
                            </div>
                        </div>
                        <div class="preview-container" id="faculty-sslc-certificate-preview-container"></div>

                        <div class="row align-items-center">
                            <div class="col col-6">
                                <div class="dropzone" id="dropzone">
                                    <p>Drag & Drop files here or <span class="browse-text">Browse</span></p>
                                    <input type="file" id="file-upload" name="faculty_hsc_certificate" class="file-input" accept=".pdf, .doc, .docx, .jpeg, .jpg, .png">
                                    <input type="hidden" class="previous-link" id="previous-faculty-hsc" name="previous_faculty_hsc" value="">
                                    <input type="hidden" id="faculty-hsc-id" name="faculty_hsc_id" value="0">
                                </div>
                            </div>
                            <div class="col col-6 text-left">
                                <label for="file-upload" class="upload-instruction">Upload your HSC Certificate</label>
                            </div>
                        </div>
                        <div class="preview-container" id="faculty-hsc-certificate-preview-container"></div>

                        <div class="row align-items-center">
                            <div class="col col-6">
                                <div class="dropzone" id="dropzone">
                                    <p>Drag & Drop files here or <span class="browse-text">Browse</span></p>
                                    <input type="file" id="file-upload" class="file-input" name="faculty_highest_qualification_certificate" accept=".pdf, .doc, .docx, .jpeg, .jpg, .png">
                                    <input type="hidden" class="previous-link" id="previous-faculty-highest-qualification" name="previous_faculty_highest_qualification" value="">
                                    <input type="hidden" id="faculty-highest-qualification-id" name="faculty_highest_qualification_id" value="0">
                                </div>
                            </div>
                            <div class="col col-6 text-left">
                                <label for="file-upload" class="upload-instruction">Upload your Highest Qualification Certificate</label>
                            </div>
                        </div>
                        <div class="preview-container" id="faculty-highest-qualification-certificate-preview-container"></div>

                        <div class="row align-items-center">
                            <div class="col col-6">
                                <div class="dropzone" id="dropzone">
                                    <p>Drag & Drop files here or <span class="browse-text">Browse</span></p>
                                    <input type="file" id="faculty-experience-certificate" class="file-input" name="faculty_experience_certificate[]" multiple accept=".pdf, .doc, .docx, .jpeg, .jpg, .png">
                                    <div id="hidden-experience-tags">
                                        <input type="hidden" name="previous_faculty_experience[]" value="">
                                        <input type="hidden" name="previous_faculty_experience_id[]" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col col-6 text-left">
                                <label for="file-upload" class="upload-instruction">Upload your Experience Certificates</label>
                            </div>
                        </div>
                        <div class="preview-container" id="faculty-experience-certificate-preview-container"></div>
                    </div>

                    <div class="form-navigation">
                        <button class="nav-next text-left" id="document_upload_info_faculty_form_prev_btn" type="button">Previous</button>
                        <button class="nav-back text-right" id="document_upload_info_faculty_form_nxt_btn" type="submit">Submit</button>
                    </div>
                </form>
            </section>
        </div>
    </section>

    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>

    <script>
        fetch_faculty_documents()

        init_dropzones()
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

        //id=document_upload_info_faculty_form_prev_btn on click
        $('#document_upload_info_faculty_form_prev_btn').on('click', function() {
            const params = {
                action: 'add',
                route: 'faculty',
                type: 'skill',
                tab: 'knowledge'
            };

            // Construct the new URL with query parameters
            const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
            const newUrl = window.location.origin + window.location.pathname + queryString;
            // Use pushState to set the new URL and pass params as the state object
            window.history.pushState(params, '', newUrl);
            load_update_profile_components();
        });

        //faculty-document-upload-profile-info-form on submit
        $('#faculty-document-upload-profile-info-form').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: '<?= htmlspecialchars(MODULES . '/faculty_profile/ajax/faculty_document_upload_profile_info_form.php', ENT_QUOTES, 'UTF-8') ?>',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.code == 200) {
                        showToast('success', "Your Update is Successfull. Please wait while we redirect you.");

                        setTimeout(() => {
                            window.location.href = '<?= BASEPATH ?>';
                        }, 1000);
                    } else {
                        showToast('error', data.message);
                    }
                },
                error: function(jqXHR) {
                    const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                    showToast('error', message);
                }
            });
        });
    </script>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
