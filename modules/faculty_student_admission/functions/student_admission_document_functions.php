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
        const fetch_faculty_documents = () => {
            
            $.ajax({
                type: 'GET',
                url: '<?= MODULES . '/faculty_student_admission/json/fetch_student_documents_data.php' ?>',
                headers: {
                    'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.code == 200) {
                        const data = response.data;
                        if (data) {



                            // Define preview container variables for each document type
                            const sslcPreviewContainer = $('#student-sslc-certificate-preview-container');
                            const hscPreviewContainer = $('#student-hsc-certificate-preview-container');
                            const qualificationPreviewContainer = $('#student-highest-qualification-certificate-preview-container');
                            const transferCertificatePreviewContainer = $('#student-transfer-certificate-preview-container');
                            const permanentIntegratedCertificatePreviewContainer = $('#student-permanent-integrated-certificate-preview-container');
                            const communityCertificatePreviewContainer = $('#student-community-certificate-preview-container');
                            const residenceCertificatePreviewContainer = $('#student-residence-certificate-preview-container');



                            // Loop through each document
                            data.forEach(document => {
                              
                                const previewCard = $('<div class="preview-card">');
                                const fileName = $('<span>').text(document.student_doc_path);

                                if (document.student_doc_type == 1) { // SSLC
                                    $('#previous-student-sslc').val(document.student_doc_path);
                                    $('#student-sslc-id').val(document.student_doc_id);
                                    let thumbnail = getThumbnail(document.student_doc_path, 'student_sslc/');

                                    previewCard.append(thumbnail).append(fileName);
                                    sslcPreviewContainer.append(previewCard);

                                } else if (document.student_doc_type == 2) { // HSC
                                    $('#previous-student-hsc').val(document.student_doc_path);
                                    $('#student-hsc-id').val(document.student_doc_id);
                                    let thumbnail = getThumbnail(document.student_doc_path, 'student_hsc_certificate/');

                                    previewCard.append(thumbnail).append(fileName);
                                    hscPreviewContainer.append(previewCard);

                                } else if (document.student_doc_type == 3) { // Highest Qualification
                                    $('#previous-student-highest-qualification').val(document.student_doc_path);
                                    $('#student-highest-qualification-id').val(document.student_doc_id);
                                    let thumbnail = getThumbnail(document.student_doc_path, 'student_highest_qualification/');

                                    previewCard.append(thumbnail).append(fileName);
                                    qualificationPreviewContainer.append(previewCard);

                                } else if (document.student_doc_type == 4) { // Transfer Certificate (TC)
                                    $('#previous-student-transfer-certificate-id').val(document.student_doc_path);
                                    $('#student-transfer-certificate-id').val(document.student_doc_id);
                                    let thumbnail = getThumbnail(document.student_doc_path, 'student_transfer_certificate/');

                                    previewCard.append(thumbnail).append(fileName);
                                    transferCertificatePreviewContainer.append(previewCard);

                                } else if (document.student_doc_type == 5) { // Profile Image Certificate (PIC)
                                    $('#previous-student-permanent-integrated-certificate-id').val(document.student_doc_path);
                                    $('#student-permanent-integrated-certificate-id').val(document.student_doc_id);
                                    let thumbnail = getThumbnail(document.student_doc_path, 'student_permanent_integrated_certificate/');

                                    previewCard.append(thumbnail).append(fileName);
                                    permanentIntegratedCertificatePreviewContainer.append(previewCard);

                                } else if (document.student_doc_type == 6) { // Community Certificate
                                    $('#previous-student-community-certificate-id').val(document.student_doc_path);
                                    $('#student-community-certificate-id').val(document.student_doc_id);
                                    let thumbnail = getThumbnail(document.student_doc_path, 'student_community_certificate/');

                                    previewCard.append(thumbnail).append(fileName);
                                    communityCertificatePreviewContainer.append(previewCard);

                                } else if (document.student_doc_type == 7) { // Residence Certificate
                                    

                                    $('#previous-student-residence-certificate-id').val(document.student_doc_path);
                                    $('#student-residence-certificate-id').val(document.student_doc_id);
                                    let thumbnail = getThumbnail(document.student_doc_path, 'student_residence_certificate/');

                                    previewCard.append(thumbnail).append(fileName);
                                    residenceCertificatePreviewContainer.append(previewCard);

                                } else if (document.student_doc_type == 8) { // Profile 
                                  
                                    $('#student-previous-profile-pic').val(document.student_doc_path);
                                    $('#student-profile-pic-id').val(document.student_doc_id);
                                    // Assuming GLOBAL_PATH is correctly passed into JavaScript
                                    const profilePicPath = ' <?= GLOBAL_PATH  ?>' + '/uploads/student_profile_pic/' + document.student_doc_path;
                                    $('#imageDisplay').attr('src', profilePicPath).show();
                                    $('.placeholder-text').hide();
                                }
                            });

                            // $('#hidden-experience-tags').empty(); // Clear any existing hidden inputs
                            // // Append hidden inputs for experience certificates
                            // experiencePaths.forEach((path, index) => {
                            //     $('<input>').attr({
                            //         type: 'hidden',
                            //         id: `student-experience-certificate-${index}`, // Optional: Unique ID for each input
                            //         name: 'previous_faculty_experience[]', // Use [] to create an array in the backend
                            //         value: path
                            //     }).appendTo('#hidden-experience-tags'); // Append to the specified container
                            // });

                            // Optional: If you also want to append IDs
                            // experienceIds.forEach((id, index) => {
                            //     $('<input>').attr({
                            //         type: 'hidden',
                            //         id: `student-experience-id-${index}`, // Optional: Unique ID for each input
                            //         name: 'previous_faculty_experience_id[]', // Use [] to create an array in the backend
                            //         value: id
                            //     }).appendTo('#hidden-experience-tags'); // Append to the specified container
                            // });
                        }
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

        const load_update_admission_document_components = () => {
            const urlParams = new URLSearchParams(window.location.search);
            const route = urlParams.get('route');
            const action = urlParams.get('action');
            const type = urlParams.get('type');
            const tab = urlParams.get('tab');
            const id = urlParams.get('id');

            // Condition to load the correct form based on URL parameters
            if (action == 'add' && route == 'faculty' && type == 'fees') {
                if (tab == 'concession_details') {
                    load_update_admission_profile_components();
                    // $('.tab-btn.schools').addClass('active');
                    // $('.tab-btn.degrees').removeClass('active');
                } else if (action == 'view' && route == 'faculty' && type == 'overall' && !tab && id) {
                    // Add your code for this condition here
                    load_admission_profile(student_id);
                } else if (action == 'view' && route == 'faculty' && type == 'overall' && !tab && !id) {
                    // Add your code for this condition here
                    load_overall_personal_profile();
                } else {
                    console.log('No matching condition for route and action');
                }
            }

        }
        const load_certificate_upload_info_form = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_student_admission/components/update_profile/upload_file_info/upload_files_info_faculty_student_admission.php?action=add&route=student&type=documentupload&tab=document', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token // Secure CSRF token
                    },
                    success: function(response) {
                        $('#upload_info').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    }
                });
            });
        };
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
