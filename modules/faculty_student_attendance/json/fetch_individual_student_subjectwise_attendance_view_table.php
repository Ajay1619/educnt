<?php
include_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    isset($_SERVER['HTTP_X_CSRF_TOKEN']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    // Validate CSRF token
    validateCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN']);
    try {
        $location_href = $_SERVER['HTTP_X_REQUESTED_PATH'];



        $faculty_subjects_id = isset($_POST['faculty_subjects_id']) ? sanitizeInput(decrypt_data($_POST['faculty_subjects_id']), 'int') : 0;
        $attendance_date = isset($_POST['attendance_date']) ? sanitizeInput(decrypt_data($_POST['attendance_date']), 'string') : 0;
        $selected_attendance_slot = isset($_POST['selected_attendance_slot']) ? sanitizeInput(decrypt_data($_POST['selected_attendance_slot']), 'int') : 0;
        $procedure_params = [
            ['name' => 'faculty_subjects_id', 'value' => $faculty_subjects_id, 'type' => 'i'],
            ['name' => 'attendance_date', 'value' => date(DB_DATE_FORMAT, strtotime($attendance_date)), 'type' => 's'],
            ['name' => 'selected_attendance_slot', 'value' => $selected_attendance_slot, 'type' => 'i'],
            ['name' => 'login_id', 'value' => $logged_login_id, 'type' => 'i']
        ];
        $result = callProcedure("fetch_pr_student_attendance_data", $procedure_params);
        if ($result) {
            if ($result['particulars'][0]['status_code'] === 200) {
                if (isset($result['data'][0][0]['total_students'])) {
                    $data = $result['data'];
                    $total_students = $data[0][0]['total_students'];
                    $subject_details = $data[1][0];
                    $student_details = $data[2];
                    $table = "
                    <div class='row'>
                    <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                        <div class='section-header-title text-left'>Subject :
                            <span class='text-light' id='individual-subjectwise-attendance-subject'>{$subject_details['subject_name']} ({$subject_details['subject_code']})</span>
                        </div>
                    </div>
                    <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                        <div class='section-header-title text-right'>Year Of Study :
                            <span class='text-light' id='individual-subjectwise-year-of-study'>{$subject_details['year_of_study_title']}</span>
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                        <div class='section-header-title text-left'>Section :
                            <span class='text-light' id='individual-subjectwise-section'>{$subject_details['section_title']}</span>
                        </div>
                    </div>
                    <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                        <div class='section-header-title text-right'>Total Strength :
                            <span class='text-light' id='individual-subjectwise-total-strength'>{$total_students}</span>
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                        <div class='section-header-title text-left'>Date :
                            <span class='text-light' id='individual-subjectwise-date'>{$attendance_date}</span>
                        </div>
                    </div>
                    <div class='col col-6 col-lg-6 col-md-6 col-sm-6 col-xs-12'>
                        <div class='section-header-title text-right'>Period :
                            <span class='text-light' id='individual-subjectwise-period'>{$subject_details['period_time']}</span>
                        </div>
                    </div>
                </div>
                    <table id='individual-subjectwise-attendance-table'  class='portal-table'>
                        <thead>
                            <tr>
                                <th>Sl. No.</th>
                                <th>Student's Name</th>
                                <th>Register Number</th>
                                <th>Status</th>
                                <th>Permission</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>";

                    $slNo = 1;
                    foreach ($student_details as $student_detail) {
                        $att_permission = "";
                        if ($student_detail['attendance_confirmation_status'] == 2) {
                            $att_permission = "<p class='alert alert-success text-sm'>{$student_detail['confirmation_status']}</p>";
                        } else if ($student_detail['attendance_confirmation_status'] == 3) {
                            $att_permission = "<p class='alert alert-error text-sm'>{$student_detail['confirmation_status']}</p>";
                        }


                        $table .= "<tr>
                                <td>{$slNo}</td>
                                <td>{$student_detail['student_name']}</td>
                                <td>{$student_detail['student_reg_number']}</td>
                                <td>{$student_detail['attendance_status']}</td>
                                <td>{$att_permission}</td>
                                <td>{$student_detail['attendance_notes']}</td>
                            </tr>";
                        $slNo++;
                    }

                    $table .= "</tbody></table>";
                    echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message'], 'total_students' => $total_students, 'subject_details' => $subject_details, 'table' => $table]);
                    exit;
                } else {
                    echo json_encode(['code' => 300, 'status' => 'warning', 'message' => 'No Student data found with your Filter.']);
                    exit;
                }
            } else {
                echo json_encode(['code' => $result['particulars'][0]['status_code'], 'status' => $result['particulars'][0]['status'], 'message' => $result['particulars'][0]['message']]);
                exit;
            }
        }
    } catch (\Throwable $th) {
        $error_message = $th->getMessage();
        insert_error($error_message, $location_href, 2);
        echo json_encode(['code' => 600, 'status' => 'error', 'message' => 'An error occurred.']);
    }
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
