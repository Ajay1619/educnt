DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_individual_admission_data`(IN `p_faculty_login_id` INT, IN `p_student_user_id` INT)
BEGIN
    -- Declare variables to capture error and warning messages
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Get the error code and message
        GET DIAGNOSTICS CONDITION 1
            error_code = RETURNED_SQLSTATE, 
            error_message = MESSAGE_TEXT;

        -- Return error details
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        -- Increment the warning count
        SET warning_count = warning_count + 1;

        -- Capture the warning message
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Call each procedure and check results
    
    IF warning_count = 0 THEN
        CALL fetch_stu_admission_personal_data(p_faculty_login_id,p_student_user_id);
    END IF;
    IF warning_count = 0 THEN
        CALL fetch_stu_admission_parent_data( p_faculty_login_id,p_student_user_id);
    END IF;

    IF warning_count = 0 THEN
        CALL fetch_pr_student_document_data(p_student_user_id, p_faculty_login_id);
    END IF;

    IF warning_count = 0 THEN
        CALL fetch_stu_admission_contact_data(p_faculty_login_id,p_student_user_id);
    END IF;
    IF warning_count = 0 THEN
        CALL fetch_stu_admission_address_data(p_faculty_login_id,p_student_user_id);
    END IF;
    IF warning_count = 0 THEN
        CALL fetch_stu_admission_education_schoolings_data(p_student_user_id, p_faculty_login_id);
    END IF;
    IF warning_count = 0 THEN
        CALL fetch_stu_admission_education_degrees(p_student_user_id, p_faculty_login_id);
    END IF;
    IF warning_count = 0 THEN
        CALL fetch_pr_admission_course(p_faculty_login_id,p_student_user_id);
    END IF;
    
    
    

    -- Log user activity if no errors occurred
 

    -- If there were any warnings, return a warning status
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Set success message
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Personal Info fetched successfully!' AS message;
    END IF;
    
END$$
DELIMITER ;