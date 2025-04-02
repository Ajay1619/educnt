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

        $faculty_id = isset($_POST['faculty_id']) ? sanitizeInput($_POST['faculty_id'], 'int')  : [];
        $dept_id = isset($_POST['dept_id']) ? sanitizeInput($_POST['dept_id'], 'int')  : 0;
        $year_of_study_id = isset($_POST['year_of_study_id']) ? sanitizeInput($_POST['year_of_study_id'], 'int')  : 0;
        $section_id = isset($_POST['section_id']) ? sanitizeInput($_POST['section_id'], 'int')  : 0;
        $mentor_faculty_count = count($faculty_id);
        $procedure_params = [
            ['name' => 'login id', 'type' => 'i', 'value' => $logged_login_id],
            ['name' => 'dept_id', 'type' => 'i', 'value' => $dept_id],
            ['name' => 'year_of_study_id', 'type' => 'i', 'value' => $year_of_study_id],
            ['name' => 'section_id', 'type' => 'i', 'value' => $section_id]
        ];

        $result = callProcedure("fetch_pr_students", $procedure_params);

        if ($result['particulars'][0]['status_code'] == 200) {
            if ($result['data']) {
                $total_students = $result['data'][1][0]['total_students'];
                $class_data = $result['data'][2];
                $students_data = $result['data'][0];
                $class_count = count($class_data);

                // Calculate students per faculty
                $students_per_faculty = intdiv($total_students, $mentor_faculty_count);
                $students_per_class_faculty = intdiv($students_per_faculty, $class_count);
                $remaining_students = $total_students % $mentor_faculty_count;

                // Allocate students to faculties with class-based limits
                $faculty_allocation = [];
                $student_index = 0;

                // Initialize allocation array for each faculty
                foreach ($faculty_id as $faculty) {
                    $faculty_allocation[$faculty] = [];
                }

                // Distribute students across faculties
                foreach ($faculty_id as $faculty_index => $faculty) {
                    $students_allocated_to_faculty = 0;

                    foreach ($class_data as $class) {
                        $class_students_allocated = 0;

                        while (
                            $class_students_allocated < $students_per_class_faculty &&
                            $students_allocated_to_faculty < $students_per_faculty &&
                            $student_index < $total_students
                        ) {
                            $faculty_allocation[$faculty][] = $students_data[$student_index]['student_id'];
                            $student_index++;
                            $class_students_allocated++;
                            $students_allocated_to_faculty++;
                        }

                        // Stop allocating from this class if all students are assigned
                        if ($students_allocated_to_faculty >= $students_per_faculty) {
                            break;
                        }
                    }
                }

                // Allocate remaining students to faculties incrementally
                while ($student_index < $total_students) {
                    foreach ($faculty_id as $faculty) {
                        if ($student_index < $total_students) {
                            $faculty_allocation[$faculty][] = $students_data[$student_index]['student_id'];
                            $student_index++;
                        } else {
                            break;
                        }
                    }
                }

                if ($faculty_allocation) {
                    $update_mentor_procedure_params = [
                        ['name' => 'login id', 'type' => 'i', 'value' => $logged_login_id],
                        ['name' => 'update_type', 'type' => 'i', 'value' => 1],
                        ['name' => 'mentor_details_json', 'type' => 's', 'value' => json_encode($faculty_allocation)],
                        ['name' => 'from_faculty_id', 'type' => 'i', 'value' => 0],
                        ['name' => 'to_faculty_id', 'type' => 'i', 'value' => 0],
                        ['name' => 'dept_id', 'type' => 'i', 'value' => $logged_dept_id],
                    ];

                    $update_mentor_result = callProcedure("update_pr_faculty_mentor_role", $update_mentor_procedure_params);

                    if ($update_mentor_result) {
                        if ($update_mentor_result['particulars'][0]['status_code'] == 200) {
                            echo json_encode(['code' => 200, 'status' => 'success', 'message' => 'Students allocated successfully.']);
                            exit;
                        } else {
                            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error in Updating Mentor Details.']);
                            exit;
                        }
                    } else {
                        echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error in Updating Mentor Details.']);
                        exit;
                    }
                } else {
                    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error in allocating students.']);
                    exit;
                }
            } else {
                echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'No students found.']);
                exit;
            }
        } else {
            echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Error in updating personal details.']);
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
