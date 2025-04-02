 

<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'GET'
) {
    //Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);
?>
<div class="achivements_container">
        <div class="header">
            <div class="conference-type">
                NATIONAL CONFERENCEeee
            </div>
            <div class="edit-icon" >
                <img src="<?= GLOBAL_PATH . '/images/svgs/sidenavbar_icons/old_icons/edit.svg' ?>" alt="Edit" data-popup-role-id="1" class="edit-popup text-right"/>
            </div>
        </div>

        <div class="main-content">
            <div class="content">
                <h2>SEMINAR</h2>
                <p>The Event Took Place On <strong>21/04/2024</strong>, And The Venue Was <strong>Ariyur, Pondicherry</strong>.</p>
            </div>

            <div class="file-section">
                <div class="file-icon">
                    <img id="file-icon" src="<?= GLOBAL_PATH . '/images/svgs/application_icons/pdfs.svg' ?>" alt="PDF" />
                </div>
                <div class="file-actions">
                    <a id="preview" href="file.pdf" target="_blank">
                        <img src="<?= GLOBAL_PATH . '/images/svgs/application_icons/preview.svg' ?>" alt="Preview" />
                    </a>
                    <a id="download" href="file.pdf" download>
                        <img src="<?= GLOBAL_PATH . '/images/svgs/application_icons/download.svg' ?>" alt="Download" />
                    </a>
                </div>
            </div>
        </div>
    </div>
<div class="achivements_container">
        <div class="header">
            <div class="conference-type">
                NATIONAL CONFERENCE
            </div>
            <div class="edit-icon" >
                <img src="<?= GLOBAL_PATH . '/images/svgs/sidenavbar_icons/old_icons/edit.svg' ?>" alt="Edit" data-popup-role-id="2" class="edit-popup text-right" />
            </div>
        </div>

        <div class="main-content">
            <div class="content">
                <h2>SEMINAR</h2>
                <p>The Event Took Place On <strong>21/04/2024</strong>, And The Venue Was <strong>Ariyur, Pondicherry</strong>.</p>
            </div>

            <div class="file-section">
                <div class="file-icon">
                    <img id="file-icon" src="<?= GLOBAL_PATH . '/images/svgs/application_icons/pdfs.svg' ?>" alt="PDF" />
                </div>
                <div class="file-actions">
                    <a id="preview" href="file.pdf" target="_blank">
                        <img src="<?= GLOBAL_PATH . '/images/svgs/application_icons/preview.svg' ?>" alt="Preview" />
                    </a>
                    <a id="download" href="file.pdf" download>
                        <img src="<?= GLOBAL_PATH . '/images/svgs/application_icons/download.svg' ?>" alt="Download" />
                    </a>
                </div>
            </div>
        </div>
    </div>
<div class="achivements_container">
        <div class="header">
            <div class="conference-type">
                NATIONAL CONFERENCE
            </div>
            <div class="edit-icon">
                <img src="<?= GLOBAL_PATH . '/images/svgs/sidenavbar_icons/old_icons/edit.svg' ?>" alt="Edit" data-popup-role-id="3" class="edit-popup text-right" />
            </div>
        </div>

        <div class="main-content">
            <div class="content">
                <h2>SEMINAR</h2>
                <p>The Event Took Place On <strong>21/04/2024</strong>, And The Venue Was <strong>Ariyur, Pondicherry</strong>.</p>
            </div>

            <div class="file-section">
                <div class="file-icon">
                    <img id="file-icon" src="<?= GLOBAL_PATH . '/images/svgs/application_icons/pdfs.svg' ?>" alt="PDF" />
                </div>
                <div class="file-actions">
                    <a id="preview" href="file.pdf" target="_blank">
                        <img src="<?= GLOBAL_PATH . '/images/svgs/application_icons/preview.svg' ?>" alt="Preview" />
                    </a>
                    <a id="download" href="file.pdf" download>
                        <img src="<?= GLOBAL_PATH . '/images/svgs/application_icons/download.svg' ?>" alt="Download" />
                    </a>
                </div>
            </div>
        </div>
    </div>


<script src="<?= MODULES . '/faculty_achievements/js/view_achievements.js' ?>"></script>
<script>
      const achievementeditPage = (roleId) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/faculty_achievements/components/student/edit_achievements.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                     'X-CSRF-Token': '<?= $csrf_token ?>' , // Secure CSRF token
                'X-Requested-Path': window.location.pathname + window.location.search// Secure CSRF token // Secure CSRF token
                    },
                    data: {
                        roleId: roleId
                    }, // Send roleId to the server
                    success: function(response) {
                        $('#edit-popup').html(response); // Load response into the edit element
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        // Handle different error messages based on the status code
                        const message = jqXHR.status == 401 ?
                            'Unauthorized access. Please check your credentials.' :
                            'An error occurred. Please try again.';
                        showToast('error', message); // Show error message
                        reject(); // Reject the promise
                    }
                });
            });
        };

        $(document).ready(async function() {
            // Event handler for opening the edit popup on multiple elements
            $('.edit-popup').on('click', async function() {
                console.log("hello");
                
                const roleId = $(this).data('popup-role-id'); // Get the role ID from the clicked element
                try {
                    await achievementeditPage(roleId); // Wait for the AJAX request to complete
                } catch (error) {
                    console.error(error); // Log any errors
                }
            });

            // Event handler for closing the edit popup
            $('#popup-close-btn').on('click', function() {
                $('#editpage').html(''); // Clear the HTML content
            });
        });
</script>
    
    <?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}