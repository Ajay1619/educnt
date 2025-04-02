<?php
include_once('../../../../config/sparrow.php');

if (
  isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
  ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
  isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
  $_SERVER['REQUEST_METHOD'] == 'GET'
) {
  // Validate CSRF token
  validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
  checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);

  $designation = isset($_POST['designation']) ? sanitizeInput($_POST['designation'], 'int') : 0;
  $department = isset($_POST['department']) ? sanitizeInput($_POST['department'], 'int') : 0;

?>
  <div class="popup-overlay" id="event_bulk_upload">
    <div class="alert-popup" id="alert-pop">
      <div class="popup-header">Bulk Upload Event</div>
      <button class="popup-close-btn">×</button>
      <div class="popup-content" id="popup-cont">
        <div>
          <p class="action-hint">"An academic calendar is like the blueprint of time itself. Plan today, forge tomorrow."</p>
          <button class="info-icon-btn" id="download-template">
            <span class="ml-3 info tooltip tooltip-right">
              <img src="<?= GLOBAL_PATH . '/images/svgs/info.svg' ?>" alt="Info Icon" class="info-icon">
              <span class="tooltip-text">
                <strong>INFO</strong>
                <div>Academic Calendar Sample Excel file</div>
              </span>
            </span>
          </button>
        </div>
        <img src="<?= GLOBAL_PATH . '/images/svgs/gifs/upload.gif' ?>" alt="Upload GIF" class="popup-image">
        <form id="bull-upload" method="post">
          <div class="row">
        <div class="col col-12">
        <div class="dropzone" id="dropzone">
          <p>Drag & Drop Academic Calendar Bulk upload files here or <span class="browse-text" >Browse</span></p>
          <input type="file" id="file-upload" name="event_file_upload" class="file-input" accept=".excel,.xlsx,.xls,.csv">
          <input type="hidden" class="previous-link" id="previous-event-file-upload" name="previous_event_file_upload" value="">
          <input type="hidden" id="event-file-upload-id" name="event_file_upload_id" value="0">
        </div>
        </div>
        </div>
        <div class="preview-container" id="faculty-experience-certificate-preview-container"></div>

        

      <!-- Footer Section -->
      <div class="popup-footer">
        <button type="button" id="cancel-btn" class="btn-error">Cancel</button>
        <button  id="calendar_upload-btn" class="btn-success">Upload</button>
      </div>
      </form>
    </div>
  </div>

  <script>
    init_dropzones()

    $(window).on('click', function(event) {
      if (event.target == document.getElementById('event_bulk_upload')) {
        updateUrl({route: 'faculty',  action: 'view',  type: 'overall' });
        
        $('#academic-calendar-event-popup-view').html('');
        
      }
    });
    document.getElementById("download-template").addEventListener("click", () => {
      const link = document.createElement("a");
      link.href = "<?= FILES ?>/excel/academic_calendar_events.xlsx"; // Replace with the correct file path
      link.download = "academic_calendar_events.xlsx";
      link.click();
    });
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
    $(document).ready(async function() {
      try {
        $('#calendar_upload-btn').on('click', async function(e) {
          e.preventDefault();
          const formData = new FormData($('#bull-upload')[0]); // Corrected to reference the form
          console.log(formData);

          $.ajax({
            type: 'POST',
            url: '<?= htmlspecialchars(MODULES . '/faculty_academic_calendar/json/fetch_calender_data.php', ENT_QUOTES, 'UTF-8') ?>',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
              'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
            },

            success: function(response) {

               response = JSON.parse(response);
              if (response.code == 200) {
                console.log(response.matched);
                console.log(response.unmatched);
                const unmatched = response.unmatched;
                const matched = response.matched;
                const orgin = response.orgin;
                  updateUrl({
                    route: 'faculty',
                    action: 'add',
                    type: 'bulk_upload_preview'
                  });
                   load_academic_calendar_bulk_event_preview_popup(matched,unmatched,orgin);

                // console.log('sucess');
                // showToast('success', response.message);
                // console.log(response);

                //  $('.tab-btn.parent').addClass('active');
                //  $('.tab-btn.contact').removeClass('active');
                // params = '?action=add&route=faculty&type=personal&tab=parent';
                // const newUrl = window.location.origin + window.location.pathname + params;
                // console.log(window.location.origin);
                // console.log(window.location.pathname);
                // console.log(params);
                // // Use history.pushState to update the URL without refreshing the page
                // history.pushState({
                //     action: 'add',
                //     route: 'faculty'
                // }, '', newUrl);
                // load_student_parent_profile_info_form();
              } else {
                console.log(response.message);
                showToast(response.status, response.message);
              }
            },
            error: function(jqXHR) {
              const message = jqXHR.status == 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
              showToast('error', message);
            }
          });

          // try {
          //   updateUrl({
          //     route: 'faculty',
          //     action: 'add',
          //     type: 'bulk_upload_preview'
          //   });
          //   await load_academic_calendar_bulk_event_preview_popup();
          // } catch (error) {
          //   console.error('Error loading Add Event popup:', error);
          // }
        });
      } catch (error) {
        console.error('An error occurred while loading:', error);
      }
    });
  </script>

<?php
} else {
  echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
  exit;
}
?>