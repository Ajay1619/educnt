<?php
include_once('../../../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    checkPageAccess($faculty_page_access_data, $_SERVER['HTTP_X_REQUESTED_PATH']);

    $designation = isset($_POST['designation']) ? sanitizeInput($_POST['designation'], 'int') : 0;
    $department = isset($_POST['department']) ? sanitizeInput($_POST['department'], 'int') : 0;
    $matched = isset($_POST['matched']) ? sanitizeInput($_POST['matched'], 'string') : '';
    $unmatched = isset($_POST['unmatched']) ? sanitizeInput($_POST['unmatched'], 'string') : '';
    $orgin = isset($_POST['orgin']) ? sanitizeInput($_POST['orgin'], 'string') : '';
    //    $un = json_decode($matched[0]);
    //     print_r($un);

    // if (!$unmatched) {
    //     print_r($matched);
    //     # code...
    // }

?>
    <div class="popup-overlay" id="event_bulk_upload_preview">
        <div class="alert-popup" id="alert-pop">
            <div class="popup-header">Preview Event</div>
            <button class="popup-close-btn">×</button>
            <div class="popup-content" id="popup-cont">
                <p class="action-hint">"Even Doctor Strange needed to tweak the timeline – your edits will surely save the day!"</p>
                <p class="action-hint">"Oop's Please select the proper list in the excel"</p>
                <div class="event-table-container" id="event-table-preview">
                    <table class="event-table">
                        <?php
                        if (!empty($unmatched)) {
                             // Define the table headers
                             $headers = ["event name", "event description", "event start date", "event end date", "event type"];
                             echo "<h2>Unmatched Event type Data</h2>";
                             // Generate the table header
                             echo "<table border='1'>"; // Add table border for better readability
                             echo "<thead><tr>";
                             foreach ($headers as $header) {
                                 echo "<th>" . htmlspecialchars($header) . "</th>";
                             }
                             echo "</tr></thead>";
 
                             // Generate the table body
                             echo "<tbody>";
                             // Create a new array with matching indices
                             $filtered = array_map(fn($index) => $orgin[$index], $unmatched);
 
                             
                             
                             foreach ($filtered as $row) {
                                 if (is_string($row)) {
                                     // Decode the JSON string into a PHP array
                                     
                                     $row = json_decode($row, true);
                                     if (json_last_error() !== JSON_ERROR_NONE) {
                                         // Handle invalid JSON
                                         echo "<tr><td colspan='5'>Invalid data: " . htmlspecialchars($row) . "</td></tr>";
                                         continue;
                                     }
                                 }
 
                                 if (is_array($row)) {
                                     // Process the array and output its content
                                     echo "<tr>";
                                     foreach ($row as $cell) {
                                         echo "<td>" . htmlspecialchars($cell) . "</td>";
                                     }
                                     echo "</tr>";
                                 } else {
                                     // Handle unexpected types
                                     echo "<tr><td colspan='5'>Unexpected type: " . htmlspecialchars(print_r($row, true)) . "</td></tr>";
                                 }
                             }
                             
                             echo "</tbody>";
                             echo "</table>";
                            echo "<p>You cant submit this until you currect this </p>";

                        } elseif (!empty($matched)) {
                            // Define the table headers
                            echo "<h2>Matched Data</h2>";

                            $headers = ["event_name", "event_description", "event_start_date", "event_end_date", "event_type"];

                            // Generate the table header
                            echo "<table border='1'>"; // Add table border for better readability
                            echo "<thead><tr>";
                            foreach ($headers as $header) {
                                echo "<th>" . htmlspecialchars($header) . "</th>";
                            }
                            echo "</tr></thead>";

                            // Generate the table body
                            echo "<tbody>";
                            // Create a new array with matching indices
                            $filtered = array_map(fn($index) => $orgin[$index], $matched);

                            
                            
                            foreach ($filtered as $row) {
                                if (is_string($row)) {
                                    // Decode the JSON string into a PHP array
                                    $row = json_decode($row, true);
                                    if (json_last_error() !== JSON_ERROR_NONE) {
                                        // Handle invalid JSON
                                        echo "<tr><td colspan='5'>Invalid data: " . htmlspecialchars($row) . "</td></tr>";
                                        continue;
                                    }
                                }

                                if (is_array($row)) {
                                    // Process the array and output its content
                                    echo "<tr>";
                                    foreach ($row as $cell) {
                                        echo "<td>" . htmlspecialchars($cell) . "</td>";
                                    }
                                    echo "</tr>";
                                } else {
                                    // Handle unexpected types
                                    echo "<tr><td colspan='5'>Unexpected type: " . htmlspecialchars(print_r($row, true)) . "</td></tr>";
                                }
                            }
                            
                            echo "</tbody>";
                            echo "</table>";
                            echo "<p>Now You can submit</p>";

                        } else {
                            echo "No matched data available.";
                        }
                        ?>
                    </table>
                </div>

            </div>
            <div class="popup-footer">
                <button type="button" id="cancel-btn" class="btn-error">Cancel</button>
                <button type="submit" id="submit-btn" class="btn-success">Submit</button>
            </div>
        </div>
    </div>
    <script>
        $(window).on('click', function(event) {
            if (event.target == document.getElementById('event_bulk_upload_preview')) {
                $('#academic-calendar-event-popup-view').html('');
            }
        });
        $('#submit-btn').on('click', function(e) {
            console.log("hello");
            showComponentLoading("Updating...")

            e.preventDefault();
            // const formData = new FormData($('#faculty_student_admission_personal_details_form')[0]); // Corrected to reference the form
            const data = <?= json_encode($filtered) ?>; 
            $.ajax({
                type: 'POST',
                url: '<?= htmlspecialchars(MODULES . '/faculty_academic_calendar/ajax/bulk_event_upload.php', ENT_QUOTES, 'UTF-8') ?>',
                data: {
                    'events_data': data
                },

                headers: {
                    'X-CSRF-Token': '<?= $csrf_token ?>' // Secure CSRF token
                },

                success: function(response) {

                    response = JSON.parse(response);
                    if (response.code == 200) {
                        showToast('success', response.message);
                        location.reload();
                        // load_student_contact_profile_info_form();
                        // $('.tab-btn.contact').addClass('active');
                        // $('.tab-btn.personal').removeClass('active');
                        // params = '?action=add&route=faculty&type=personal&tab=contact';
                        // const newUrl = window.location.origin + window.location.pathname + params;

                        // // Use history.pushState to update the URL without refreshing the page
                        // history.pushState({
                        //     action: 'add',
                        //     route: 'faculty'
                        // }, '', newUrl);

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
            setTimeout(function() {
                hideComponentLoading();
            }, 100)
        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
