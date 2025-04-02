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

?>
    <section id="bg-card"></section>
    <section id="overall-profile-table"></section>
    <script>
        $('#profileTable').DataTable({
            scrollX: true,
            initComplete: function(settings, json) {
                $('.dt-layout-table .dt-layout-cell').css('width', '100%');
                $('.dt-scroll-headInner').css('width', '100%');
                $('.dataTable').css('width', '100%');
            }
        });

        function toggle(source) {
            checkboxes = document.getElementsByName('profileCheckbox');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }

        $(document).ready(async function() {
            try {
                // await admission_overall_function();
                await loadBgCard();
                // await viewAchievements();
                await overall_faculty_student_admission();
                await load_faculty_overall_admission_table();
                //await tableAchievements();
            } catch (error) {
                console.error('An error occurred while loading:', error);
            }
        });
    </script>
    <style>
        .row {
            display: flex;
            align-items: center;
        }

        .profile_image {
            border-radius: 50%;
            margin-right: 10px;
        }

        .designation {
            font-size: 12px;
            color: #666;
        }

        .actions a {
            margin-right: 25px;
        }

        .status-icons a {
            margin-right: 15px;
        }

        /* Center align table header and data */
        th,
        td {
            text-align: center;
        }

        /* Make sure icons and table content are aligned properly */
        td img {
            vertical-align: middle;
        }
    </style>

<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>