-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2024 at 08:45 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `svcet_educnt`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `check_user_login_status` (IN `p_log_id` INT)   BEGIN
    DECLARE v_login_status TINYINT(1);
    DECLARE r_login_status INT;
    DECLARE r_status_code INT;
    DECLARE v_status_message VARCHAR(100);
    
    -- Fetch the login_status for the given log_id
    SELECT login_status 
    INTO v_login_status
    FROM svcet_tbl_login_logs
    WHERE log_id = p_log_id;

    -- Check the login status and set the status code and message
    IF v_login_status = 1 THEN
        SET r_login_status = 1;
        SET r_status_code = 200;
        SET v_status_message = 'Login status is active (logged in).';
    ELSEIF v_login_status = 0 THEN
        SET r_login_status = 300;
        SET r_status_code = 0;
        SET v_status_message = 'User is logged out or logged in another device.';
    ELSEIF v_login_status = 2 THEN
        SET r_login_status = 300;
        SET r_status_code = 2;
        SET v_status_message = 'There is a mismatch in login status.';
    ELSE
        SET r_login_status = -1;
        SET r_status_code = 400;
        SET v_status_message = 'Invalid log_id or login status.';
    END IF;

    -- Return the result
    SELECT r_status_code AS status_code,r_login_status AS login_status, v_status_message AS message;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_allowed_pages_by_role` (IN `p_role_id` INT)   BEGIN
    -- Select allowed page titles and page IDs for the role based on the input role_id
    SELECT 
        rp.page_id, 
        p.page_title,
        p.page_link
    FROM 
        svcet_tbl_dev_role_permission rp
    JOIN 
        svcet_tbl_dev_pages p ON rp.page_id = p.page_id
    WHERE 
        rp.role_id = p_role_id  -- Use the role_id passed as parameter
        AND rp.role_perm_status = 1 -- Only active permissions
        AND rp.role_perm_deleted = 0 -- Not deleted permissions
        AND p.page_status = 1  -- Only active pages
        AND p.page_deleted = 0;  -- Not deleted pages;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_crypt` (IN `input_username` VARCHAR(255), IN `input_portal_type` INT)   BEGIN
    DECLARE v_account_id INT;
    DECLARE v_account_password TEXT; -- Change to VARCHAR

	SELECT crypt FROM svcet_tbl_dev_institution WHERE institution_id=1;
    -- Check if input_username is not NULL or empty
    IF input_username IS NOT NULL AND input_username != '' THEN
        -- Fetch the account_id and account_password for the given username
        SELECT account_id, account_password 
        INTO v_account_id, v_account_password
        FROM svcet_tbl_accounts 
        WHERE account_username = input_username AND
        account_portal_type=input_portal_type AND
        account_status=1 AND
        deleted=0;

        -- Check if the username exists
        IF v_account_id IS NULL THEN
            SELECT 'Username does not exist' AS message, 400 AS status_code, 'error' AS status;
        ELSE
            -- Return the account ID and password if the username exists
            SELECT 200 AS status_code,'success' AS status ,v_account_id AS account_id, v_account_password AS account_password, 'Username exists' AS message;
        END IF;
    ELSE
        SELECT 'Invalid username provided' AS message, 400 AS status_code; -- Use consistent naming
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_login_details` (IN `p_account_id` INT, IN `p_portal_type` INT, IN `login_id` INT)   BEGIN
    -- Declare variables to store faculty and account details
    DECLARE v_first_name VARCHAR(100);
    DECLARE v_middle_name VARCHAR(100);
    DECLARE v_last_name VARCHAR(100);
    DECLARE v_initial VARCHAR(10);
    DECLARE v_user_id INT;
    DECLARE v_designation VARCHAR(100);
    DECLARE v_portal_type TINYINT;
    DECLARE v_account_id INT;
    DECLARE v_account_code VARCHAR(50);
    DECLARE v_role_id INT; -- Variable to store the role_id
    DECLARE v_profile_status INT; -- Variable to store the profile_status
    DECLARE v_account_username VARCHAR(100);
    DECLARE v_login_id INT;
    DECLARE v_reg_number VARCHAR(50); -- Declared to store student registration number
    DECLARE v_account_code_prefix VARCHAR(50);
    DECLARE v_dept_id INT;
    DECLARE v_dept_short_name VARCHAR(50);
    DECLARE v_dept VARCHAR(50);
    DECLARE v_profile_pic TEXT;

    -- Check if portal_type is 1 (faculty) before running the query
    IF p_portal_type = 1 THEN
        -- First SELECT to get faculty details and store them into variables

-- First SELECT to get faculty details and store them into variables
SELECT 
    fp.faculty_first_name,
    fp.faculty_middle_name,
    fp.faculty_last_name,
    fp.faculty_initial,
    fp.faculty_id,
    fp.faculty_status,
    dg.general_title,
    fo.dept_id,
    dept.dept_title,
    dept.dept_short_name,
    a.account_portal_type,
    a.account_id,
    a.account_code,
    a.role_id,
    a.account_username,
    login_id,
    fd.faculty_doc_path  -- Add this line to get the profile picture path
INTO 
    v_first_name,
    v_middle_name,
    v_last_name,
    v_initial,
    v_user_id,
    v_profile_status,
    v_designation,
    v_dept_id,
    v_dept,
    v_dept_short_name,
    v_portal_type,
    v_account_id,
    v_account_code,
    v_role_id,
    v_account_username,
    v_login_id,
    v_profile_pic  -- Add this variable to store the profile picture path
FROM 
    svcet_tbl_accounts a
JOIN 
    svcet_tbl_faculty_personal_info fp ON a.account_id = fp.faculty_account_id
LEFT JOIN 
    svcet_tbl_faculty_official_details fo ON fp.faculty_id = fo.faculty_id
LEFT JOIN 
    svcet_tbl_dev_general dg ON fo.designation = dg.general_id
LEFT JOIN 
    svcet_tbl_dev_dept dept ON fo.dept_id = dept.dept_id
LEFT JOIN 
    svcet_tbl_faculty_documents fd ON fp.faculty_id = fd.faculty_doc_faculty_id 
        AND fd.faculty_doc_type = 6  -- Ensure we only get the profile picture
        AND (fd.faculty_doc_status = 1 OR fd.faculty_doc_status IS NULL)  -- Only active documents or null
        AND (fd.faculty_doc_deleted = 0 OR fd.faculty_doc_deleted IS NULL)  -- Not deleted documents or null
WHERE 
    a.account_id = p_account_id
    AND a.account_portal_type = p_portal_type
    AND a.account_status = 1 -- Only active accounts
    AND a.deleted = 0 -- Not deleted accounts
    AND (fo.faculty_official_details_status = 1 OR fo.faculty_official_details_status IS NULL)  -- Active faculty or null
    AND fp.faculty_deleted = 0; -- Not deleted faculty;



-- Retrieve account code prefix
SELECT prefixes_title INTO v_account_code_prefix 
FROM svcet_tbl_dev_prefixes 
WHERE prefixes_group_id = 1;

-- Final output
SELECT 
    v_first_name AS first_name,
    v_middle_name AS middle_name,
    v_last_name AS last_name,
    v_initial AS user_initial,
    v_user_id AS user_id,
    v_profile_status AS profile_status,
    v_designation AS designation,
    v_dept_id AS dept_id,
    v_dept AS dept_title,
    v_dept_short_name AS dept_short_name,
    v_portal_type AS portal_type,
    v_account_id AS account_id,
    v_account_code AS account_code,
    v_role_id AS role_id,
    v_account_username AS account_username,
    v_login_id AS login_id,
    v_account_code_prefix AS account_prefix,
    v_profile_pic AS profile_pic_path; 

       CALL fetch_allowed_pages_by_role(v_role_id);

    ELSEIF p_portal_type = 2 THEN

        -- Query for student details
        SELECT 
            fp.student_first_name,
            fp.student_middle_name,
            fp.student_last_name,
            fp.student_initial,
            fp.student_id,
            fo.student_reg_number,
            fo.dept_id,
            dept.dept_title,
            dept.dept_short_name,
            a.account_portal_type,
            a.account_id,
            a.account_code,
            a.role_id,
            a.account_username,
            login_id
        INTO 
            v_first_name,
            v_middle_name,
            v_last_name,
            v_initial,
            v_user_id,
            v_reg_number,
            v_dept_id,
            v_dept,
            v_dept_short_name,
            v_portal_type,
            v_account_id,
            v_account_code,
            v_role_id,  -- Store role_id into the variable
            v_account_username,
            v_login_id
        FROM 
            svcet_tbl_accounts a
        JOIN 
            svcet_tbl_student_personal_info fp ON a.account_id = fp.student_account_id
        JOIN 
            svcet_tbl_student_official_details fo ON fp.student_id = fo.student_id
        LEFT JOIN 
            svcet_tbl_dev_dept dept ON fo.dept_id= dept.dept_id
        WHERE 
            a.account_id = p_account_id
            AND a.account_portal_type = p_portal_type
            AND a.account_status = 1 -- Only active accounts
            AND a.deleted = 0 -- Not deleted accounts
            AND fo.student_official_details_status = 1 -- Active student
            AND fp.student_deleted = 0; -- Not deleted student;

        
        SELECT prefixes_title INTO v_account_code_prefix  FROM svcet_tbl_dev_prefixes WHERE prefixes_group_id = 2;
        SELECT 
            v_first_name AS first_name,
            v_middle_name AS middle_name,
            v_last_name AS last_name,
            v_initial AS user_initial,
            v_user_id AS user_id,
            v_reg_number AS reg_number,
            v_dept_id AS dept_id,
            v_dept AS dept_title,
            v_dept_short_name AS dept_short_name,
            v_portal_type AS portal_type,
            v_account_id AS account_id,
            v_account_code AS account_code,
            v_role_id AS role_id,
            v_account_username AS account_username,
            v_login_id AS login_id,
            v_account_code_prefix  AS account_prefix;

        CALL fetch_allowed_pages_by_role(v_role_id);

    ELSEIF p_portal_type = 3 THEN

        -- Query for student details
-- Query for parent details
SELECT 
    fp.parent_first_name,
    fp.parent_middle_name,
    fp.parent_last_name,
    fp.parent_initial,
    fp.parent_id,
    a.account_portal_type,
    a.account_id,
    a.account_code,
    a.role_id,
    a.account_username,
    a.log_id
INTO 
    v_first_name,
    v_middle_name,
    v_last_name,
    v_initial,
    v_user_id,
    v_portal_type,
    v_account_id,
    v_account_code,
    v_role_id,  -- Store role_id into the variable
    v_account_username,
    v_login_id
FROM 
    svcet_tbl_accounts a
JOIN 
    svcet_tbl_parent_personal_info fp ON a.account_id = fp.parent_account_id
WHERE 
    a.account_id = p_account_id
    AND a.account_portal_type = p_portal_type
    AND a.account_status = 1 -- Only active accounts
    AND a.deleted = 0 -- Not deleted accounts
    AND fp.parent_deleted = 0; -- Not deleted parent;


        SELECT prefixes_title INTO v_account_code_prefix  FROM svcet_tbl_dev_prefixes WHERE prefixes_group_id = 3;

-- Output parent details
SELECT 
    v_first_name AS first_name,
    v_middle_name AS middle_name,
    v_last_name AS last_name,
    v_initial AS user_initial,
    v_user_id AS user_id,
    v_portal_type AS portal_type,
    v_account_id AS account_id,
    v_account_code AS account_code,
    v_role_id AS role_id,
    v_account_username AS account_username,
    v_login_id AS login_id,
    v_account_code_prefix  AS account_prefix;


-- Query for student details
SELECT 
    spi.student_first_name, 
    spi.student_middle_name, 
    spi.student_last_name, 
    sod.student_reg_number 
FROM 
    svcet_tbl_student_parent_relation spr
JOIN 
    svcet_tbl_student_personal_info spi ON spr.student_id = spi.student_id
JOIN 
    svcet_tbl_student_official_details sod ON spi.student_id = sod.student_id
WHERE 
    spr.parent_id = v_user_id 
    AND spr.relation_status = 1  -- Only active relationships
    AND spi.student_status = 1   -- Only active students
    AND spi.student_deleted = 0  -- Only non-deleted students
    AND sod.student_official_details_status = 1  -- Only active official details
    AND sod.student_official_details_deleted = 0;  -- Only non-deleted official details


        CALL fetch_allowed_pages_by_role(v_role_id);
    ELSEIF p_portal_type = 4 THEN
     SELECT account_username,role_id,login_id,p_account_id FROM svcet_tbl_accounts WHERE account_id=p_account_id;
    ELSE
        -- If portal type is not 1 or 2, return an error message
        SELECT 'Invalid portal type. This procedure only works for portal_type = 1 (faculty) or portal_type = 2 (student).' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_nav_pages` (IN `p_module_id` INT, IN `p_module_status` TINYINT, IN `p_portal_type` TINYINT, IN `p_role_id` INT)   BEGIN
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

    -- Select statement to fetch the navigation pages
    SELECT 
        p.page_id, 
        p.page_title, 
        p.page_link
    FROM 
        svcet_tbl_dev_pages AS p
    INNER JOIN 
        svcet_tbl_dev_role_permission AS rp ON p.page_id = rp.page_id
    WHERE 
        p.module_id = p_module_id
        AND p.module_status = p_module_status
        AND p.portal_type = p_portal_type
        AND p.navbar_status = 1
        AND p.page_type = 2
        AND rp.role_id = p_role_id
        AND rp.role_perm_status = 1
        AND rp.role_perm_deleted = 0
        AND p.page_status = 1
        AND p.page_deleted = 0;

    -- If there were any warnings, return a warning status
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Set success message
        SELECT 200 AS status_code, 'success' AS status, 'Navigation pages fetched successfully!' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_overall_faculty_profile_table_data` (IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(50), IN `p_order_dir` VARCHAR(4), IN `p_start` INT, IN `p_length` INT, IN `p_designation` INT, IN `p_department` INT, IN `p_login_id` INT)   BEGIN
    DECLARE total_records INT DEFAULT 0;
    DECLARE filtered_records INT DEFAULT 0;

    -- Declare variables to capture warning messages
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Get total number of records
    SELECT COUNT(*) INTO total_records 
    FROM svcet_tbl_faculty_personal_info AS fpi
    JOIN svcet_tbl_faculty_official_details AS fod ON fpi.faculty_id = fod.faculty_id
    WHERE fpi.faculty_deleted = 0;

    -- Get the filtered record count
    SELECT COUNT(*) INTO filtered_records
    FROM svcet_tbl_faculty_personal_info AS fpi
    JOIN svcet_tbl_faculty_official_details AS fod ON fpi.faculty_id = fod.faculty_id
    WHERE fpi.faculty_deleted = 0 
      AND (fpi.faculty_first_name LIKE CONCAT('%', p_search_value, '%') 
           OR fpi.faculty_last_name LIKE CONCAT('%', p_search_value, '%'))
      AND (p_designation = 0 OR fod.designation = p_designation)
      AND (p_department = 0 OR fod.dept_id = p_department);

    -- Fetch the data with pagination and sorting
    SELECT fpi.faculty_id, 
           fpi.faculty_first_name, 
           fpi.faculty_middle_name, 
           fpi.faculty_last_name, 
           salutation.general_title AS faculty_salutation, 
           fpi.faculty_status, 
           fod.dept_id, 
           d.dept_short_name, 
           g.general_title AS designation,
           doc.faculty_doc_path AS profile_pic_path
    FROM svcet_tbl_faculty_personal_info AS fpi
    JOIN svcet_tbl_faculty_official_details AS fod ON fpi.faculty_id = fod.faculty_id
    LEFT JOIN svcet_tbl_dev_dept AS d ON fod.dept_id = d.dept_id
    LEFT JOIN svcet_tbl_dev_general AS g ON fod.designation = g.general_id
    LEFT JOIN svcet_tbl_dev_general AS salutation ON fpi.faculty_salutation = salutation.general_id
    LEFT JOIN svcet_tbl_faculty_documents AS doc ON fpi.faculty_id = doc.faculty_doc_faculty_id 
         AND doc.faculty_doc_type = 6 AND doc.faculty_doc_status = 1 AND doc.faculty_doc_deleted = 0
    WHERE fpi.faculty_deleted = 0 
      AND (fpi.faculty_first_name LIKE CONCAT('%', p_search_value, '%') 
           OR fpi.faculty_last_name LIKE CONCAT('%', p_search_value, '%'))
      AND (p_designation = 0 OR fod.designation = p_designation)
      AND (p_department = 0 OR fod.dept_id = p_department)
    ORDER BY 
        CASE 
            WHEN p_sort_column = 'faculty_first_name' THEN fpi.faculty_first_name
            WHEN p_sort_column = 'faculty_last_name' THEN fpi.faculty_last_name
            WHEN p_sort_column = 'dept_id' THEN fod.dept_id
            ELSE fpi.faculty_id
        END 
    LIMIT p_start, p_length;

       -- Return total and filtered record counts
    SELECT total_records AS total_records, filtered_records AS filtered_records;
    -- Insert activity log entry
    CALL insert_user_activity_log(
        p_login_id, 
        'svcet_tbl_dev_general,svcet_tbl_faculty_personal_info,svcet_tbl_faculty_official_details,svcet_tbl_dev_dept,svcet_tbl_faculty_documents', 
        1
    );

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Designation Records Fetched Successfully!' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_overall_student_profile_table_data` (IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(50), IN `p_order_dir` VARCHAR(4), IN `p_start` INT, IN `p_length` INT, IN `p_batch_id` INT, IN `p_section_id` INT, IN `p_year_of_study_id` INT, IN `p_login_id` INT)   BEGIN
    DECLARE total_records INT DEFAULT 0;
    DECLARE filtered_records INT DEFAULT 0;

    -- Declare variables for error handling
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handler block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = RETURNED_SQLSTATE, 
            error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Get total number of student records
    SELECT COUNT(*) INTO total_records 
    FROM svcet_tbl_student_personal_info AS spi
    JOIN svcet_tbl_student_official_details AS sod ON spi.student_id = sod.student_id
    WHERE spi.student_deleted = 0;

    -- Query for filtered record count based on the search and other parameters
    SET @filtered_query = CONCAT(
        'SELECT COUNT(*) FROM svcet_tbl_student_personal_info AS spi 
        JOIN svcet_tbl_student_official_details AS sod ON spi.student_id = sod.student_id
        WHERE spi.student_deleted = 0 
        AND (spi.student_first_name LIKE ? OR spi.student_last_name LIKE ?) ',
        IF(p_batch_id > 0, ' AND sod.academic_batch_id = ? ', ''),
        IF(p_section_id > 0, ' AND sod.section_id = ? ', ''),
        IF(p_year_of_study_id > 0, ' AND sod.year_of_study_id = ? ', '')
    );

    PREPARE stmt_filtered FROM @filtered_query;
    SET @search = CONCAT('%', p_search_value, '%');

    -- Execute prepared statement for filtered record count
    IF p_batch_id > 0 AND p_section_id > 0 AND p_year_of_study_id > 0 THEN
        EXECUTE stmt_filtered USING @search, @search, p_batch_id, p_section_id, p_year_of_study_id;
    ELSEIF p_batch_id > 0 AND p_section_id > 0 THEN
        EXECUTE stmt_filtered USING @search, @search, p_batch_id, p_section_id;
    ELSEIF p_batch_id > 0 THEN
        EXECUTE stmt_filtered USING @search, @search, p_batch_id;
    ELSE
        EXECUTE stmt_filtered USING @search, @search;
    END IF;

    -- Get filtered record count
    SELECT FOUND_ROWS() INTO filtered_records;
    DEALLOCATE PREPARE stmt_filtered;

    -- Fetch the data with pagination and sorting
    SET @data_query = CONCAT(
        'SELECT spi.student_id, 
                spi.student_first_name, 
                spi.student_last_name, 
                sod.student_reg_number, 
                ab.academic_batch_title, 
                s.section_title, 
                yos.year_of_study_title
        FROM svcet_tbl_student_personal_info AS spi
        JOIN svcet_tbl_student_official_details AS sod ON spi.student_id = sod.student_id
        LEFT JOIN svcet_tbl_dev_academic_batch AS ab ON sod.academic_batch_id = ab.academic_batch_id
        LEFT JOIN svcet_tbl_dev_section AS s ON sod.section_id = s.section_id
        LEFT JOIN svcet_tbl_dev_year_of_study AS yos ON sod.year_of_study_id = yos.year_of_study_id
        WHERE spi.student_deleted = 0 
        AND (spi.student_first_name LIKE ? OR spi.student_last_name LIKE ?) ',
        IF(p_batch_id > 0, ' AND sod.academic_batch_id = ? ', ''),
        IF(p_section_id > 0, ' AND sod.section_id = ? ', ''),
        IF(p_year_of_study_id > 0, ' AND sod.year_of_study_id = ? ', ''),
        ' ORDER BY ', p_sort_column, ' ', p_order_dir, ' LIMIT ?, ?'
    );

    PREPARE data_stmt FROM @data_query;

    -- Execute prepared statement for data retrieval with pagination
    IF p_batch_id > 0 AND p_section_id > 0 AND p_year_of_study_id > 0 THEN
        EXECUTE data_stmt USING @search, @search, p_batch_id, p_section_id, p_year_of_study_id, p_start, p_length;
    ELSEIF p_batch_id > 0 AND p_section_id > 0 THEN
        EXECUTE data_stmt USING @search, @search, p_batch_id, p_section_id, p_start, p_length;
    ELSEIF p_batch_id > 0 THEN
        EXECUTE data_stmt USING @search, @search, p_batch_id, p_start, p_length;
    ELSE
        EXECUTE data_stmt USING @search, @search, p_start, p_length;
    END IF;

    DEALLOCATE PREPARE data_stmt;

    -- Return total and filtered record counts
    SELECT total_records AS total_records, filtered_records AS filtered_records;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_personal_info,svcet_tbl_student_official_details,svcet_tbl_dev_academic_batch,svcet_tbl_dev_section,svcet_tbl_dev_year_of_study', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Student Records Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_period_day` (IN `facultyId` INT)   BEGIN
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message TEXT DEFAULT '';
    DECLARE deptId INT;

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Get the error code and message
        GET DIAGNOSTICS CONDITION 1
            error_code = RETURNED_SQLSTATE, 
            error_message = MESSAGE_TEXT;

        -- Return error details
        SELECT 
            500 AS status_code, 
            'error' AS status, 
            CONCAT('Error Code: ', error_code, ', Message: ', error_message) AS message;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        -- Increment the warning count
        SET warning_count = warning_count + 1;

        -- Capture the warning message
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Step 1: Get the department ID from the faculty official details table
    SELECT dept_id INTO deptId
    FROM svcet_tbl_faculty_official_details
    WHERE faculty_id = facultyId AND faculty_official_details_status=1;


    -- Step 2: Fetch unique period titles matching the department ID
    SELECT DISTINCT p.period_title,p.period_hour
    FROM svcet_tbl_dev_period_time AS p
    WHERE p.dept_id = deptId AND period_status=1 AND period_delete=0 AND period_type=1;

    -- Step 3: Fetch dates from the day table where timetable_status is 1
    SELECT d.day_id, d.day_title
    FROM svcet_tbl_dev_day AS d
    WHERE d.timetable_status = 1;

    -- Handle warnings if any
    IF warning_count > 0 THEN
        SELECT 
            200 AS status_code, 
            'warning' AS status, 
            CONCAT(warning_count, ' warning(s) occurred: ', warning_message) AS message;
    ELSE
        -- Success message when no warnings occur
        SELECT 
            200 AS status_code, 
            'success' AS status, 
            'Data fetched successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_academic_batch` (IN `p_login_id` INT)   BEGIN
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

    -- Main query to fetch academic batch records
    SELECT 
        academic_batch_id AS id,
        academic_batch_title AS title
    FROM 
        svcet_tbl_dev_academic_batch
    WHERE 
        academic_batch_status = 1 
        AND academic_batch_deleted = 0 ;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_academic_batch', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Academic Batch Records Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_achievements` (IN `p_login_id` INT)   BEGIN
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

    -- Main query to fetch achievement records
    SELECT 
        general_id AS id,
        general_title AS title
    FROM 
        svcet_tbl_dev_general
    WHERE 
        general_group_id = 14 -- Group ID for Achievements
        AND general_status = 1
        AND general_delete = 0;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_general', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Achievement Records Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_achievement_table_data` (IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(50), IN `p_order_dir` VARCHAR(4), IN `p_start` INT, IN `p_length` INT, IN `p_faculty_id` INT, IN `p_achievement_type` INT, IN `p_login_id` INT)   BEGIN
    DECLARE total_records INT DEFAULT 0;
    DECLARE filtered_records INT DEFAULT 0;
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Warning handler
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Calculate total records without filters
    SELECT COUNT(*) INTO total_records
    FROM svcet_tbl_faculty_achievements AS a
    JOIN svcet_tbl_faculty_personal_info AS p
    ON a.faculty_id = p.faculty_id
    WHERE a.achievement_deleted = 0
      AND p.faculty_deleted = 0
      AND p.faculty_status = 1;

    -- Calculate filtered records based on input conditions
    SELECT COUNT(*) INTO filtered_records
    FROM svcet_tbl_faculty_achievements AS a
    JOIN svcet_tbl_dev_general AS g 
    ON a.achievement_type = g.general_id
    JOIN svcet_tbl_faculty_personal_info AS p
    ON a.faculty_id = p.faculty_id
    WHERE a.achievement_deleted = 0 
      AND g.general_status = 1
      AND p.faculty_deleted = 0
      AND p.faculty_status = 1
      AND (p_faculty_id = 0 OR a.faculty_id = p_faculty_id)
      AND (p_achievement_type = 0 OR a.achievement_type = p_achievement_type)
      AND (a.achievement_title LIKE CONCAT('%', p_search_value, '%') 
           OR a.achievement_venue LIKE CONCAT('%', p_search_value, '%'));

    -- Fetch filtered data with sorting and pagination
    SELECT 
        a.faculty_achievements_id,
        a.faculty_id,
        CONCAT_WS(' ', p.faculty_first_name, p.faculty_middle_name, p.faculty_last_name) AS faculty_name,
        a.achievement_type,
        g.general_title AS achievement_type_title,
        a.achievement_title,
        a.achievement_date,
        a.achievement_venue,
        a.achievement_document,
        a.achievement_status,
        a.achievement_deleted
    FROM svcet_tbl_faculty_achievements AS a
    JOIN svcet_tbl_dev_general AS g 
    ON a.achievement_type = g.general_id
    JOIN svcet_tbl_faculty_personal_info AS p
    ON a.faculty_id = p.faculty_id
    WHERE a.achievement_deleted = 0 
      AND g.general_status = 1
      AND p.faculty_deleted = 0
      AND p.faculty_status = 1
      AND (p_faculty_id = 0 OR a.faculty_id = p_faculty_id)
      AND (p_achievement_type = 0 OR a.achievement_type = p_achievement_type)
      AND (a.achievement_title LIKE CONCAT('%', p_search_value, '%') 
           OR a.achievement_venue LIKE CONCAT('%', p_search_value, '%'))
    ORDER BY 
        CASE 
            WHEN p_sort_column = 'achievement_title' THEN a.achievement_title
            WHEN p_sort_column = 'achievement_date' THEN a.achievement_date
            WHEN p_sort_column = 'achievement_type' THEN a.achievement_type
            ELSE a.faculty_achievements_id
        END 
        LIMIT p_start, p_length;

    -- Return total and filtered record counts
    SELECT total_records AS total_records, filtered_records AS filtered_records;

    -- Log user activity
    CALL insert_user_activity_log(
        p_login_id, 
        'svcet_tbl_faculty_achievements,svcet_tbl_dev_general,svcet_tbl_faculty_personal_info', 
        1
    );

    -- Return success or warning message
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Achievement Records Fetched Successfully!' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_admission_course` (IN `p_faculty_id` INT, IN `p_student_user_id` INT)   BEGIN
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

    -- Fetch faculty contact data
    SELECT 
        sai.admission_id,
        sai.student_admission_student_id,
        sai.student_admission_type,
        sai.student_admission_category,
        sai.student_hostel,
        sai.student_admission_know_about_us,
        sai.student_transport,
        sai.student_admission_reg_no,
        sai.student_course_preference1,
        sai.student_course_preference2,
        sai.student_course_preference3,
        sai.student_reference,
        sai.student_concession_subject,
        sai.student_concession_body,
        sai.admission_status,
        sai.admission_deleted,
        sai.lateral_entry_year_of_study,
        dept1.dept_id AS dept1_id,
        dept1.dept_title AS dept1_title,
        dept1.dept_short_name AS dept1_short_name,
        dept1.dept_status AS dept1_status,
        dept1.dept_deleted AS dept1_deleted,
        dept2.dept_id AS dept2_id,
        dept2.dept_title AS dept2_title,
        dept2.dept_short_name AS dept2_short_name,
        dept2.dept_status AS dept2_status,
        dept2.dept_deleted AS dept2_deleted,
        dept3.dept_id AS dept3_id,
        dept3.dept_title AS dept3_title,
        dept3.dept_short_name AS dept3_short_name,
        dept3.dept_status AS dept3_status,
        dept3.dept_deleted AS dept3_deleted,
        fpi.faculty_id,
        fpi.faculty_salutation,
        fpi.faculty_first_name,
        fpi.faculty_middle_name,
        fpi.faculty_last_name,
        fpi.faculty_initial,
        fpi.faculty_dob,
        fpi.faculty_gender,
        fpi.faculty_mobile_number,
        fpi.faculty_personal_mail_id,
        fpi.faculty_official_mail_id,
        g.general_title
    FROM 
        svcet_tbl_student_admission_info sai
    LEFT JOIN 
        svcet_tbl_dev_dept dept1 ON sai.student_course_preference1 = dept1.dept_id
    LEFT JOIN 
        svcet_tbl_dev_dept dept2 ON sai.student_course_preference2 = dept2.dept_id
    LEFT JOIN 
        svcet_tbl_dev_dept dept3 ON sai.student_course_preference3 = dept3.dept_id
    LEFT JOIN 
        svcet_tbl_faculty_personal_info fpi ON fpi.faculty_id = sai.student_reference
    LEFT JOIN 
        svcet_tbl_dev_general AS g ON g.general_id = fpi.faculty_salutation AND g.general_group_id = 19
    WHERE 
        sai.student_admission_student_id = p_student_user_id
    AND 
        sai.admission_deleted = 0;

    -- Log the activity
    CALL insert_user_activity_log(p_faculty_id, 'svcet_tbl_faculty_contact_info', 1);
        
    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Contact Details Fetched Successfully!' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_blood_group` (IN `p_login_id` INT)   BEGIN
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

    -- Main query to fetch blood group records
    SELECT 
        general_id AS id,
        general_title AS title
    FROM 
        svcet_tbl_dev_general
    WHERE 
        general_group_id = 2 -- Group ID for Blood Group
        AND general_status = 1
        AND general_delete = 0;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_general', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Blood Group Records Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_caste` (IN `p_login_id` INT)   BEGIN
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

    -- Main query to fetch caste records
    SELECT 
        general_id AS id,
        general_title AS title
    FROM 
        svcet_tbl_dev_general
    WHERE 
        general_group_id = 6 -- Group ID for Caste
        AND general_status = 1
        AND general_delete = 0;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_general', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Caste Records Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_class_advisors` (IN `p_dept_id` INT, IN `p_year_of_study_id` INT, IN `p_login_id` INT, IN `p_faculty_id` INT)   BEGIN
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

    -- Check if faculty_id is 0 (i.e., fetch all)
    IF p_faculty_id = 0 THEN
        -- Select query for all class advisors
        SELECT 
            yos.year_of_study_id,
            yos.year_of_study_title,
            sec.section_id,
            sec.section_title,
            fac_adv.faculty_class_advisors_id,
            IFNULL(fac_adv.faculty_id, '') AS faculty_id,
            CONCAT_WS(' ', 
                      IFNULL(fac_personal.faculty_first_name, ''), 
                      IFNULL(fac_personal.faculty_middle_name, ''), 
                      IFNULL(fac_personal.faculty_last_name, ''), 
                      IFNULL(fac_personal.faculty_initial, '')
                     ) AS faculty_full_name,
            IFNULL(sal.general_title, '') AS salutation,
            IFNULL(desg.general_title, '') AS designation,
            IFNULL(doc.faculty_doc_path, '') AS profile_pic,
            yos.dept_id, -- Use dept_id from yos directly
            IFNULL(dept.dept_title, '') AS dept_title,
            ab.academic_batch_title -- Fetch academic batch title
        FROM 
            svcet_tbl_dev_year_of_study AS yos
        LEFT JOIN 
            svcet_tbl_dev_section AS sec 
            ON yos.year_of_study_id = sec.year_of_study_id
        LEFT JOIN 
            svcet_tbl_faculty_class_advisors AS fac_adv 
            ON yos.year_of_study_id = fac_adv.year_of_study_id 
            AND sec.section_id = fac_adv.section_id -- Match section if available
            AND fac_adv.faculty_class_advisors_status = 1
        LEFT JOIN 
            svcet_tbl_faculty_personal_info AS fac_personal 
            ON fac_adv.faculty_id = fac_personal.faculty_id 
            AND fac_personal.faculty_deleted = 0 -- Include only non-deleted faculty
        LEFT JOIN 
            svcet_tbl_faculty_official_details AS fac_official 
            ON fac_adv.faculty_id = fac_official.faculty_id
        LEFT JOIN 
            svcet_tbl_dev_general AS sal 
            ON fac_personal.faculty_salutation = sal.general_id
        LEFT JOIN 
            svcet_tbl_dev_general AS desg 
            ON fac_official.designation = desg.general_id
        LEFT JOIN 
            svcet_tbl_faculty_documents AS doc 
            ON fac_personal.faculty_id = doc.faculty_doc_faculty_id 
            AND doc.faculty_doc_type = 6 -- Profile Pic
        LEFT JOIN 
            svcet_tbl_dev_dept AS dept 
            ON yos.dept_id = dept.dept_id -- Fetch department details directly
        LEFT JOIN 
            svcet_tbl_dev_academic_batch AS ab
            ON yos.academic_batch_id = ab.academic_batch_id -- Fetch academic batch details
        WHERE 
            yos.year_of_study_status = 1 -- Active Year of Study
            AND yos.year_of_study_delete = 0 -- Not Deleted
            AND sec.section_status = 1 -- Active Section
            AND sec.section_delete = 0 -- Not Deleted
            AND (dept.dept_deleted = 0 OR dept.dept_deleted IS NULL) -- Include NULL for unmatched rows
            AND (dept.dept_status = 1 OR dept.dept_status IS NULL) -- Include NULL for unmatched rows
            AND (p_dept_id = 0 OR yos.dept_id = p_dept_id) -- Fetch all departments if p_dept_id is 0
            AND (p_year_of_study_id = 0 OR yos.year_of_study_id = p_year_of_study_id) -- Fetch all years if p_year_of_study_id is 0
            AND fac_adv.faculty_class_advisors_status =1
            AND (fac_adv.faculty_class_advisors_deleted = 0 OR fac_adv.faculty_class_advisors_deleted IS NULL);
    ELSE
        -- Select query for a specific faculty class advisor
        SELECT 
            fac_adv.faculty_class_advisors_id,
            yos.year_of_study_title,
            sec.section_title,
            fac_adv.effective_from,
            fac_adv.effective_to,
            ab.academic_batch_title
        FROM 
            svcet_tbl_faculty_class_advisors AS fac_adv
        LEFT JOIN 
            svcet_tbl_dev_year_of_study AS yos
            ON fac_adv.year_of_study_id = yos.year_of_study_id
        LEFT JOIN 
            svcet_tbl_dev_section AS sec
            ON fac_adv.section_id = sec.section_id
        LEFT JOIN 
            svcet_tbl_faculty_personal_info AS fac_personal
            ON fac_adv.faculty_id = fac_personal.faculty_id
        LEFT JOIN 
            svcet_tbl_dev_academic_batch AS ab
            ON yos.academic_batch_id = ab.academic_batch_id
        WHERE 
            fac_adv.faculty_id = p_faculty_id
            AND fac_adv.faculty_class_advisors_status IN (1, 3)
            AND (fac_adv.faculty_class_advisors_deleted = 0 OR fac_adv.faculty_class_advisors_deleted IS NULL)
            AND (p_dept_id = 0 OR fac_adv.dept_id = p_dept_id)
            AND (p_year_of_study_id = 0 OR yos.year_of_study_id = p_year_of_study_id)
        ORDER BY 
            fac_adv.faculty_class_advisors_status ASC,  -- 1 first, then 3
            fac_adv.effective_to DESC;  -- Order by effective_to date
    END IF;

    -- Record user activity log after successful query execution
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_year_of_study,svcet_tbl_dev_section,svcet_tbl_faculty_class_advisors,svcet_tbl_faculty_personal_info,svcet_tbl_dev_general,svcet_tbl_faculty_official_details,svcet_tbl_faculty_documents,svcet_tbl_dev_dept,svcet_tbl_dev_academic_batch', 1);

    -- If there were any warnings, return a warning status
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Set success message
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Personal Info fetched successfully!' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_commitee_list` (IN `p_login_id` INT, IN `p_dept_id` INT)   BEGIN

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

    SELECT 
        general_id AS value,
        general_title AS title
    FROM 
        svcet_tbl_dev_general
    WHERE 
        general_group_id = 20
        AND general_status = 1
        AND general_delete = 0;

    SELECT 
     r.faculty_roles_and_responsibilities_id ,
     f.faculty_id,
     r.committee_title,
     r.committee_role,
     CONCAT(
        COALESCE(g.general_title, ''), ' ', -- Salutation prefix
        COALESCE(f.faculty_first_name, ''), ' ',
        COALESCE(f.faculty_last_name, ''), ' ',
        COALESCE(f.faculty_initial, '')
     ) AS full_name
    FROM 
      svcet_tbl_faculty_roles_and_responsibilities r
    JOIN 
      svcet_tbl_faculty_personal_info f ON r.faculty_id = f.faculty_id
    LEFT JOIN 
      svcet_tbl_dev_general g ON f.faculty_salutation = g.general_id 
      AND g.general_group_id = 19
    WHERE 
      r.dept_id = p_dept_id
      AND r.roles_and_responsibilities_status = 1
      AND r.roles_and_responsibilities_deleted = 0;

    -- Insert activity log if update was successful
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_general', 1);

     -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Commitees List Fetched Succesfully' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_community` (IN `p_login_id` INT)   BEGIN
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

    -- Main query to fetch community records
    SELECT 
        general_id AS id,
        general_title AS title
    FROM 
        svcet_tbl_dev_general
    WHERE 
        general_group_id = 7 -- Group ID for Community
        AND general_status = 1
        AND general_delete = 0;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_general', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Community Records Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_department_list` (IN `p_login_id` INT)   BEGIN
    -- Declare variables for error handling
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handler
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = RETURNED_SQLSTATE, 
            error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handler
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Fetch department list
    SELECT 
        dept_id AS value,
        dept_title AS title,
        dept_short_name AS code
    FROM 
        svcet_tbl_dev_dept
    WHERE 
        dept_status = 1 AND 
        dept_deleted = 0;

    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_dept', 1);
    -- Return warnings if any were encountered
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Set success message
        SELECT 200 AS status_code, 'success' AS status, 'Department list fetched successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_dev_account_card_statistics_data` ()   BEGIN
    -- Declare variables to capture error and warning messages
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Declare variables for the account counts
    DECLARE student_count INT DEFAULT 0;
    DECLARE faculty_count INT DEFAULT 0;
    DECLARE parent_count INT DEFAULT 0;

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

    -- Fetch the count of students
    SELECT COUNT(*) INTO student_count
    FROM svcet_tbl_accounts
    WHERE account_portal_type = 2 AND account_status = 1 AND deleted = 0;

    -- Fetch the count of faculties
    SELECT COUNT(*) INTO faculty_count
    FROM svcet_tbl_accounts
    WHERE account_portal_type = 1 AND account_status = 1 AND deleted = 0;

    -- Fetch the count of parents
    SELECT COUNT(*) INTO parent_count
    FROM svcet_tbl_accounts
    WHERE account_portal_type = 3 AND account_status = 1 AND deleted = 0;

    -- Return the account counts
    SELECT student_count AS student_accounts, faculty_count AS faculty_accounts, parent_count AS parent_accounts;

    -- If there were any warnings, return a warning status
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, CONCAT(warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Success message when no warnings occur
        SELECT 200 AS status_code, 'success' AS status, 'Data fetched successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_dev_new_account_code_and_roles` (IN `p_portal_type` INT)   BEGIN
    DECLARE last_account_code INT;
    DECLARE prefix_title VARCHAR(255);
    
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

    -- Fetch the last account_code for the given portal type
    SELECT 
        MAX(account_code) INTO last_account_code
    FROM 
        svcet_tbl_accounts
    WHERE 
        account_portal_type = p_portal_type
        AND account_status = 1  -- Active
        AND deleted = 0;        -- Not Deleted;

    -- Increment the last account code by 1
    IF last_account_code IS NULL THEN
        SET last_account_code = 1; -- Start from 1 if no account exists
    ELSE
        SET last_account_code = last_account_code + 1; -- Increment by 1 if an account code exists
    END IF;

    -- Fetch the prefix title based on the portal type
    SELECT 
        prefixes_title INTO prefix_title
    FROM 
        svcet_tbl_dev_prefixes
    WHERE 
        prefixes_group_id = p_portal_type
        AND prefixes_status = 1    -- Active
        AND prefixes_delete = 0;   -- Not Deleted;

    -- Return the results including the incremented account code
    SELECT 
        last_account_code AS new_account_code,  -- Incremented account code
        prefix_title AS prefix_title;

    SELECT 
        roles.role_id,
        roles.role_title,
        roles.role_code,
        prefixes.prefixes_title
    FROM 
        svcet_tbl_dev_roles AS roles
    JOIN 
        svcet_tbl_dev_prefixes AS prefixes
    ON 
        prefixes.prefixes_group_id = 4
    WHERE 
        roles.portal_type = p_portal_type
    AND 
        roles.role_deleted = 0
    AND 
        roles.role_status = 1
    AND 
        prefixes.prefixes_status = 1
    AND 
        prefixes.prefixes_delete = 0;
    -- If there were any warnings, return a warning status
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Set success message
        SELECT 200 AS status_code, 'success' AS status, 'Data fetched and account code incremented successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_education_boards` (IN `p_login_id` INT)   BEGIN
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

    -- Main query to fetch education board records
    SELECT 
        general_id AS id,
        general_title AS title
    FROM 
        svcet_tbl_dev_general
    WHERE 
        general_group_id = 11 -- Group ID for Education Boards
        AND general_status = 1
        AND general_delete = 0;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_general', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Education Board Records Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_education_degrees` (IN `p_login_id` INT)   BEGIN
   -- Error and warning handling variables
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handler block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_code = RETURNED_SQLSTATE, error_message = MESSAGE_TEXT;
        ROLLBACK;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    SELECT 
        general_id AS value, 
        general_title AS title 
    FROM 
        svcet_tbl_dev_general
    WHERE 
        general_group_id = 9
        AND general_status = 1
        AND general_delete = 0;

        CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_general', 1);
            -- Check for warnings and return appropriate message
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Degrees Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_education_specializations` (IN `p_login_id` INT)   BEGIN
    -- Declare variables for error and warning handling
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_code = RETURNED_SQLSTATE, error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handling block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Fetch the specializations from the general table
    SELECT 
        general_id AS id,
        general_title AS title
    FROM 
        svcet_tbl_dev_general
    WHERE 
        general_group_id = 12  -- Group ID for Specialization
        AND general_status = 1   -- Active status
        AND general_delete = 0;  -- Not deleted

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_general', 1);
    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Specializations Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty` (IN `p_login_id` INT, IN `p_faculty_id_json` JSON)   BEGIN
    -- Declare variables for error and warning handling
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';
    DECLARE current_faculty_id INT;
    DECLARE idx INT DEFAULT 0;
    DECLARE total_faculties INT;

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_code = RETURNED_SQLSTATE, error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handling block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Extract the number of faculty IDs in the JSON array
    SET total_faculties = JSON_LENGTH(p_faculty_id_json);

    -- Loop through the JSON array of faculty IDs
    WHILE idx < total_faculties DO
        -- Extract the current faculty ID, removing quotes
        SET current_faculty_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_faculty_id_json, CONCAT('$[', idx, ']'))) AS UNSIGNED);

        -- Fetch the faculty data
        SELECT 
            f.faculty_id,
            CONCAT(
                COALESCE(g.general_title, ''), ' ', -- Salutation prefix
                COALESCE(f.faculty_first_name, ''), ' ',
                COALESCE(f.faculty_middle_name, ''), ' ',
                COALESCE(f.faculty_last_name, ''), ' ',
                COALESCE(f.faculty_initial, '')
            ) AS full_name,
            CONCAT(COALESCE(p.prefixes_title, ''), COALESCE(a.account_code, '')) AS code,
            -- Fetch the designation from the general table (designation group = 13), handle NULL with COALESCE
            COALESCE(dg.general_title, '') AS designation,
            -- Fetch the profile picture path from the faculty_documents table, handle NULL with COALESCE
            COALESCE(fd.faculty_doc_path, '') AS profile_pic
        FROM 
            svcet_tbl_faculty_personal_info AS f
        INNER JOIN 
            svcet_tbl_accounts AS a ON f.faculty_account_id = a.account_id
        INNER JOIN 
            svcet_tbl_dev_general AS g ON g.general_id = f.faculty_salutation AND g.general_group_id = 19
        INNER JOIN 
            svcet_tbl_dev_prefixes AS p ON p.prefixes_group_id = 1
        LEFT JOIN
            svcet_tbl_faculty_official_details AS fo ON fo.faculty_id = f.faculty_id
        LEFT JOIN 
            svcet_tbl_dev_general AS dg ON dg.general_id = fo.designation AND dg.general_group_id = 13 -- Designation group
        LEFT JOIN 
            svcet_tbl_faculty_documents AS fd ON fd.faculty_doc_faculty_id = f.faculty_id 
            AND fd.faculty_doc_type = 6  -- Profile picture type
        WHERE 
            f.faculty_status = 1 
            AND f.faculty_deleted = 0 
            AND a.account_portal_type = 1 
            AND a.account_status = 1 
            AND a.deleted = 0 
            AND p.prefixes_status = 1 
            AND p.prefixes_delete = 0
            AND f.faculty_id = current_faculty_id
            -- Less restrictive condition for official details and documents
    AND (fo.faculty_official_deleted = 0 OR fo.faculty_official_deleted IS NULL) 
            AND (fo.faculty_official_details_status IS NULL OR fo.faculty_official_details_status = 1) 
            AND (fd.faculty_doc_status IS NULL OR fd.faculty_doc_status = 1); 

        -- Increment the index to process the next faculty ID
        SET idx = idx + 1;
    END WHILE;

    -- Insert activity log entry
    CALL insert_user_activity_log(
        p_login_id, 
        'svcet_tbl_faculty_personal_info,svcet_tbl_accounts,svcet_tbl_dev_prefixes,svcet_tbl_faculty_official_details', 
        1
    );

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Name List Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_achievement` (IN `p_faculty_login_id` INT, IN `p_achievement_id` INT)   BEGIN
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

    -- Main Query with conditional filtering
    SELECT 
        a.faculty_achievements_id,
        a.faculty_id,
        a.achievement_type,
        g.general_title AS achievement_type_title,
        a.achievement_title,
        a.achievement_date,
        a.achievement_venue,
        a.achievement_document,
        a.achievement_status,
        a.achievement_deleted
    FROM 
        svcet_tbl_faculty_achievements a
    JOIN 
        svcet_tbl_dev_general g 
    ON 
        a.achievement_type = g.general_id
    WHERE 
        a.achievement_deleted = 0 
        AND g.general_status = 1
        AND (p_achievement_id = 0 OR a.achievement_type = p_achievement_id);

    -- Record user activity log after successful query execution
    CALL insert_user_activity_log(p_faculty_login_id, 'svcet_tbl_faculty_personal_info,svcet_tbl_dev_general', 1);

    -- If there were any warnings, return a warning status
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Set success message
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Personal Info fetched successfully!' AS message;
    END IF;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_address_data` (IN `p_faculty_id` INT, IN `p_login_id` INT)   BEGIN
    -- Error and warning handling declarations
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_code = RETURNED_SQLSTATE, error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
        ROLLBACK;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Fetching address details
    SELECT 
        faculty_address_no,
        faculty_address_street,
        faculty_address_locality,
        faculty_address_pincode,
        faculty_address_city,
        faculty_address_district,
        faculty_address_state,
        faculty_address_country
    FROM svcet_tbl_faculty_personal_info
    WHERE faculty_id = p_faculty_id;

    -- Call to log the user activity
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_personal_info', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Address Details Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_authorities` (IN `p_login_id` INT, IN `p_faculty_id` INT, IN `p_fetch_type` INT)   BEGIN
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

    -- Fetch data based on fetch_type
    IF p_fetch_type = 1 THEN
        -- Original logic for fetch_type = 1
     -- Begin the select query for faculty authorities
    SELECT 
        fa.faculty_authorities_id,
        fa.faculty_id,
        fa.faculty_authorities_group_id,
        CASE faculty_authorities_group_id
            WHEN 1 THEN 'Principal'
            WHEN 2 THEN 'Vice Principal'
            WHEN 3 THEN 'Dean - Academics'
            WHEN 4 THEN 'Head Of the Department'
            WHEN 5 THEN 'Exam Cell Head'
            WHEN 6 THEN 'Admission Cell Head'
            WHEN 7 THEN 'Placement Cell Head'
            ELSE 'Unknown Role'
        END AS authority_title,
        fa.dept_id,
        fa.effective_from,
        fa.effective_to,
        fa.faculty_authorities_status,
        fa.faculty_authorities_deleted,
        -- Full Name of the Faculty
        CONCAT(
            COALESCE(g.general_title, ''), ' ',
            COALESCE(f.faculty_first_name, ''), ' ',
            COALESCE(f.faculty_middle_name, ''), ' ',
            COALESCE(f.faculty_last_name, ''), ' ',
            COALESCE(f.faculty_initial, '')
        ) AS full_name,
        -- Department Title if Group ID is 4 (HODs)
        CASE 
            WHEN fa.faculty_authorities_group_id = 4 THEN 
                COALESCE(d.dept_title, ' ')
            ELSE 
                NULL
        END AS dept_title,
        -- Salutation of the Faculty
        g.general_title AS salutation,
        -- Designation from the most recent active official details
        COALESCE(dg.general_title, '') AS designation,
        -- Profile picture path
        fd.faculty_doc_path AS profile_pic_path
    FROM
        svcet_tbl_faculty_authorities fa
    LEFT JOIN svcet_tbl_faculty_personal_info f ON fa.faculty_id = f.faculty_id
    LEFT JOIN svcet_tbl_dev_general g ON f.faculty_salutation = g.general_id
    LEFT JOIN svcet_tbl_dev_dept d ON fa.dept_id = d.dept_id
    LEFT JOIN (
        SELECT 
            fo.faculty_id, 
            fo.designation, 
            gd.general_title
        FROM 
            svcet_tbl_faculty_official_details fo
        LEFT JOIN svcet_tbl_dev_general gd ON fo.designation = gd.general_id
        WHERE 
            fo.faculty_official_details_status = 1
            AND fo.faculty_official_deleted = 0
            AND fo.effective_from = (
                SELECT MAX(faculty_official_details.effective_from)
                FROM svcet_tbl_faculty_official_details faculty_official_details
                WHERE faculty_official_details.faculty_id = fo.faculty_id
                AND faculty_official_details.faculty_official_details_status = 1
                AND faculty_official_details.faculty_official_deleted = 0
            )
    ) dg ON fa.faculty_id = dg.faculty_id
    LEFT JOIN svcet_tbl_faculty_documents fd ON fa.faculty_id = fd.faculty_doc_faculty_id
        AND fd.faculty_doc_type = 6  -- Profile Pic type
        AND fd.faculty_doc_status = 1  -- Active status
        AND fd.faculty_doc_deleted = 0  -- Not deleted
    WHERE
        fa.faculty_authorities_status = 1 
        AND fa.faculty_authorities_deleted = 0
        AND (
            fa.faculty_authorities_group_id != 4 OR fa.faculty_id IS NOT NULL
        )
    
    UNION ALL

    -- Fetching departments with no HOD for group_id 4 (HODs)
    SELECT 
        NULL AS faculty_authorities_id,
        NULL AS faculty_id,
        4 AS faculty_authorities_group_id,
        'Head Of The Department' AS authority_title,
        d.dept_id,
        NULL AS effective_from,
        NULL AS effective_to,
        1 AS faculty_authorities_status,
        0 AS faculty_authorities_deleted,
        NULL AS full_name,
        d.dept_title AS dept_title,
        NULL AS salutation,
        NULL AS designation,
        NULL AS profile_pic_path
    FROM
        svcet_tbl_dev_dept d
    LEFT JOIN svcet_tbl_faculty_authorities fa ON d.dept_id = fa.dept_id AND fa.faculty_authorities_group_id = 4
    WHERE 
        fa.faculty_authorities_id IS NULL 
        AND d.dept_status = 1 
        AND d.dept_deleted = 0;

    -- Insert activity log if update was successful
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_authorities,svcet_tbl_faculty_personal_info,svcet_tbl_dev_dept,svcet_tbl_faculty_official_details,svcet_tbl_dev_general,svcet_tbl_faculty_documents', 1);

    ELSEIF p_fetch_type = 2 THEN
        -- Fetch all records for fetch_type = 2
SELECT 
    fa.faculty_authorities_id,
    fa.faculty_id,
    fa.faculty_authorities_group_id,
    CASE fa.faculty_authorities_group_id
        WHEN 1 THEN 'Principal'
        WHEN 2 THEN 'Vice Principal'
        WHEN 3 THEN 'Dean - Academics'
        WHEN 4 THEN 'Head Of the Department'
        WHEN 5 THEN 'Exam Cell Head'
        WHEN 6 THEN 'Admission Cell Head'
        WHEN 7 THEN 'Placement Cell Head'
        ELSE 'Unknown Role'
    END AS authority_title,
    fa.dept_id,
    fa.effective_from,
    fa.effective_to,
    fa.faculty_authorities_status,
    fa.faculty_authorities_deleted,
    CASE 
        WHEN fa.dept_id IS NOT NULL THEN COALESCE(d.dept_title, '') 
        ELSE '' 
    END AS dept_title -- If dept_id is not null, fetch dept_title, otherwise return empty string
FROM
    svcet_tbl_faculty_authorities fa
LEFT JOIN 
    svcet_tbl_dev_dept d ON fa.dept_id = d.dept_id  -- Join to fetch dept_title only when dept_id is not null
WHERE 
    fa.faculty_authorities_status IN (1, 3) -- Active and Completed
ORDER BY 
    FIELD(fa.faculty_authorities_status, 1, 3), -- Sort Active first, then Completed
    fa.effective_from;



        -- Insert activity log
        CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_authorities', 1);
    END IF;



    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Authorities Details Fetched Successfully' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_by_dept` (IN `p_login_id` INT, IN `p_dept_id` INT)   BEGIN
    -- Declare variables for error and warning handling
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_code = RETURNED_SQLSTATE, error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handling block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Fetch the faculty details based on dept_id
    SELECT 
        f.faculty_id,
        CONCAT(
            COALESCE(g.general_title, ''), ' ', -- Salutation prefix
            COALESCE(f.faculty_first_name, ''), ' ',
            COALESCE(f.faculty_middle_name, ''), ' ',
            COALESCE(f.faculty_last_name, ''), ' ',
            COALESCE(f.faculty_initial, '')
        ) AS full_name,
        CONCAT(COALESCE(p.prefixes_title, ''), COALESCE(a.account_code, '')) AS code,
        COALESCE(dg.general_title, '') AS designation,
        COALESCE(fd.faculty_doc_path, '') AS profile_pic,
        fo.dept_id
    FROM 
        svcet_tbl_faculty_personal_info AS f
    INNER JOIN 
        svcet_tbl_faculty_official_details AS fo ON f.faculty_id = fo.faculty_id
    INNER JOIN 
        svcet_tbl_accounts AS a ON f.faculty_account_id = a.account_id
    INNER JOIN 
        svcet_tbl_dev_general AS g ON g.general_id = f.faculty_salutation AND g.general_group_id = 19
    INNER JOIN 
        svcet_tbl_dev_prefixes AS p ON p.prefixes_group_id = 1
    LEFT JOIN
        svcet_tbl_dev_general AS dg ON dg.general_id = fo.designation AND dg.general_group_id = 13
    LEFT JOIN 
        svcet_tbl_faculty_documents AS fd ON fd.faculty_doc_faculty_id = f.faculty_id 
        AND fd.faculty_doc_type = 6
WHERE 
    (fo.dept_id = p_dept_id OR p_dept_id = 0) 
    AND f.faculty_status = 1 
    AND f.faculty_deleted = 0 
    AND a.account_portal_type = 1 
    AND a.account_status = 1 
    AND a.deleted = 0 
    AND p.prefixes_status = 1 
    AND p.prefixes_delete = 0
    AND (fo.faculty_official_deleted = 0 OR fo.faculty_official_deleted IS NULL) 
    AND (fo.faculty_official_details_status IS NULL OR fo.faculty_official_details_status = 1) 
    AND (fd.faculty_doc_status IS NULL OR fd.faculty_doc_status = 1);

    -- Insert activity log entry
    CALL insert_user_activity_log(
        p_login_id, 
        'svcet_tbl_faculty_personal_info,svcet_tbl_faculty_official_details,svcet_tbl_accounts,svcet_tbl_dev_general,svcet_tbl_dev_prefixes', 
        1
    );

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Name List Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_contact_data` (IN `p_faculty_id` INT, IN `p_login_id` INT)   BEGIN
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

  
        -- Fetch faculty contact data
        SELECT 
            faculty_mobile_number,
            faculty_alternative_contact_number,
            faculty_whatsapp_number,
            faculty_personal_mail_id,
            faculty_official_mail_id
        FROM 
            svcet_tbl_faculty_personal_info
        WHERE 
            faculty_id = p_faculty_id;

        -- Log the activity
        CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_personal_info', 1);
        
  -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Contact Details Fetched Successfully!' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_designation` (IN `p_login_id` INT)   BEGIN
    -- Declare variables for error and warning handling
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_code = RETURNED_SQLSTATE, error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handling block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Fetch the designations from the general table
    SELECT 
        general_id AS id,
        general_title AS title
    FROM 
        svcet_tbl_dev_general
    WHERE 
        general_group_id = 13  -- Group ID for Faculty Designation
        AND general_status = 1   -- Active status
        AND general_delete = 0;  -- Not deleted

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_general', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Designations Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_documents_prefixes` (IN `p_login_id` INT)   BEGIN
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

   SELECT 
        prefixes_title
    FROM 
        svcet_tbl_dev_prefixes
    WHERE 
        prefixes_status = 1
        AND prefixes_delete = 0
        AND prefixes_group_id IN (7, 8, 9, 10,19,20);

        -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_prefixes', 1);

    -- If there were any warnings, return a warning status
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Set success message
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Documents Prefixes fetched successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_document_data` (IN `p_faculty_id` INT, IN `p_login_id` INT)   BEGIN
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

    -- Select document data for the specified faculty ID where the document is active and not deleted
    SELECT 
        faculty_doc_id,
        faculty_doc_type,
        faculty_doc_path
    FROM 
        svcet_tbl_faculty_documents
    WHERE 
        faculty_doc_faculty_id = p_faculty_id
        AND faculty_doc_status = 1
        AND faculty_doc_deleted = 0;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_documents', 1);

    -- If there were any warnings, return a warning status
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Set success message
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Documents Fetched successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_education_degrees_data` (IN `user_id` INT, IN `login_id` INT)   BEGIN
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = RETURNED_SQLSTATE,
            error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Select query to fetch faculty education degree data with degree and specialization titles
    SELECT 
        fe.faculty_edu_id,
        fe.faculty_edu_faculty_id,
        fe.faculty_edu_level,
        fe.faculty_edu_board,
        fe.faculty_edu_institution_name,
        fe.faculty_edu_degree,
        d.general_title AS degree_title,
        fe.faculty_edu_specialization,
        s.general_title AS specialization_title,
        fe.faculty_edu_passed_out_year,
        fe.faculty_edu_cgpa,
        fe.faculty_edu_percentage,
        fe.faculty_edu_document,
        fe.faculty_edu_verified_status
    FROM 
        svcet_tbl_faculty_education fe
    LEFT JOIN 
        svcet_tbl_dev_general d ON fe.faculty_edu_degree = d.general_id AND d.general_group_id = 9 AND d.general_status = 1 AND d.general_delete = 0
    LEFT JOIN 
        svcet_tbl_dev_general s ON fe.faculty_edu_specialization = s.general_id AND s.general_group_id = 12 AND s.general_status = 1 AND s.general_delete = 0
    WHERE 
        fe.faculty_edu_faculty_id = user_id
        AND fe.faculty_edu_level=4;

    -- Return success message
    SELECT 200 AS status_code, 'success' AS status, 'Faculty education degree data fetched successfully.' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_education_schoolings_data` (IN `p_user_id` INT, IN `p_login_id` INT)   BEGIN
    -- Declare variables for error and warning handling
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_code = RETURNED_SQLSTATE, error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handling block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Fetch the SSLC education data with board title
    SELECT 
        fe.faculty_edu_institution_name AS sslc_institution_name,
        fe.faculty_edu_board AS education_board,
        dg.general_title AS board_title,
        fe.faculty_edu_passed_out_year AS sslc_passed_out_year,
        fe.faculty_edu_percentage AS sslc_percentage
    FROM 
        svcet_tbl_faculty_education fe
    LEFT JOIN 
        svcet_tbl_dev_general dg 
    ON 
        fe.faculty_edu_board = dg.general_id
    WHERE 
        fe.faculty_edu_faculty_id = p_user_id
        AND fe.faculty_edu_level = 1  -- 1 for SSLC
        AND dg.general_group_id = 11  -- Group ID for board
        AND dg.general_status = 1     -- Active status
        AND dg.general_delete = 0;    -- Not deleted

    -- Fetch the HSC education data with specialization title
    SELECT 
        fe.faculty_edu_institution_name AS hsc_institution_name,
        fe.faculty_edu_board AS education_board,
        fe.faculty_edu_specialization  AS specialization,
        dg.general_title AS board_title,
        fe.faculty_edu_passed_out_year AS hsc_passed_out_year,
        fe.faculty_edu_percentage AS hsc_percentage,
        sg.general_title AS specialization_title
    FROM 
        svcet_tbl_faculty_education fe
    LEFT JOIN 
        svcet_tbl_dev_general dg 
    ON 
        fe.faculty_edu_board = dg.general_id
    LEFT JOIN 
        svcet_tbl_dev_general sg
    ON 
        fe.faculty_edu_specialization = sg.general_id  -- Assuming specialization is stored in this column
    WHERE 
        fe.faculty_edu_faculty_id = p_user_id
        AND fe.faculty_edu_level = 2  -- 2 for HSC
        AND dg.general_group_id = 11  -- Group ID for board
        AND dg.general_status = 1     -- Active status
        AND dg.general_delete = 0;    -- Not deleted
    -- Log the user activity
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_education', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'SSLC Data Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_experience_data` (IN `p_user_id` INT, IN `p_login_id` INT)   BEGIN
       -- Error and warning handling variables
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handler block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_code = RETURNED_SQLSTATE, error_message = MESSAGE_TEXT;
        ROLLBACK;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Select faculty experience data for the specified user
    SELECT 
        faculty_exp_id,
        faculty_exp_faculty_id,
        faculty_exp_field_of_experience,
        faculty_exp_industry_name,
        faculty_exp_designation,
        faculty_exp_specialization,
        faculty_exp_start_date,
        faculty_exp_end_date,
        faculty_exp_status,
        faculty_exp_deleted
    FROM 
        svcet_tbl_faculty_experience
    WHERE 
        faculty_exp_faculty_id = p_user_id
        AND faculty_exp_status = 1
        AND faculty_exp_deleted = 0;

    -- Log the action as a fetch
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_experience', 1);

    -- Check for warnings and return appropriate message
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Degrees Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_experience_designation` (IN `p_login_id` INT)   BEGIN
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

    -- Main query to fetch designation records
    SELECT 
        general_id AS value,
        general_title AS title
    FROM 
        svcet_tbl_dev_general
    WHERE 
        general_group_id = 13 -- Group ID for Designation
        AND general_status = 1
        AND general_delete = 0;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_general', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Designation Records Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_individual_admission_data` (IN `p_faculty_login_id` INT, IN `p_student_user_id` INT)   BEGIN
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
     IF warning_count = 0 THEN
        CALL fetch_pr_student_document_data(p_student_user_id, p_faculty_login_id);
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_mentor_students` (IN `p_login_id` INT, IN `p_faculty_id` INT, IN `p_dept_id` INT)   BEGIN
    -- Declare variables for error handling
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handler
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = RETURNED_SQLSTATE, 
            error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handler
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    SELECT 
        ys.year_of_study_id,
        ys.year_of_study_title,
        s.section_id,
        s.section_title
    FROM svcet_tbl_dev_year_of_study ys
    INNER JOIN svcet_tbl_dev_section s
        ON ys.year_of_study_id = s.year_of_study_id
    WHERE 
        ys.dept_id = p_dept_id
        AND ys.year_of_study_status = 1
        AND ys.year_of_study_delete = 0
        AND s.section_status = 1
        AND s.section_delete = 0;


    SELECT 
        CONCAT_WS(' ', s.student_first_name, s.student_middle_name, s.student_last_name, s.student_initial) AS full_name,
        so.student_reg_number,
        so.student_id,
        so.year_of_study_id,
        so.section_id,
        IFNULL(
            (SELECT student_doc_path 
             FROM svcet_tbl_student_documents 
             WHERE student_doc_student_id = so.student_id 
             AND student_doc_type = 8 
             AND student_doc_status = 1 
             AND student_doc_deleted = 0 
             LIMIT 1), 
            ''
        ) AS profile_pic
    FROM svcet_tbl_faculty_mentor fm
    INNER JOIN svcet_tbl_student_official_details so
        ON fm.student_id = so.student_id
    INNER JOIN svcet_tbl_student_personal_info s
        ON s.student_id = so.student_id
    WHERE 
        fm.faculty_id = p_faculty_id
        AND fm.faculty_mentor_status = 1
        AND fm.faculty_mentor_deleted = 0
        AND so.dept_id = 1
        AND so.student_official_details_status = 1
        AND so.student_official_details_deleted = 0;


    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_mentor,svcet_tbl_student_official_details,svcet_tbl_student_personal_info,svcet_tbl_dev_year_of_study,svcet_tbl_dev_section', 1);
    -- Return warnings if any were encountered
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Set success message
        SELECT 200 AS status_code, 'success' AS status, 'Mentor Details fetched successfully!' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_name_list` (IN `input_dept_id` INT, IN `p_login_id` INT)   BEGIN
    -- Declare variables for error and warning handling
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_code = RETURNED_SQLSTATE, error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handling block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Main Query
    SELECT 
        f.faculty_id AS value,
        CONCAT(
            COALESCE(g.general_title, ''), ' ', -- Salutation prefix
            COALESCE(f.faculty_first_name, ''), ' ',
            COALESCE(f.faculty_middle_name, ''), ' ',
            COALESCE(f.faculty_last_name, ''), ' ',
            COALESCE(f.faculty_initial, '')
        ) AS title,
        CONCAT(p.prefixes_title, a.account_code) AS code
    FROM 
        svcet_tbl_faculty_personal_info AS f
    INNER JOIN 
        svcet_tbl_accounts AS a ON f.faculty_account_id = a.account_id
    INNER JOIN 
        svcet_tbl_dev_general AS g ON g.general_id = f.faculty_salutation AND g.general_group_id = 19
    INNER JOIN 
        svcet_tbl_dev_prefixes AS p ON p.prefixes_group_id = 1
    LEFT JOIN
        svcet_tbl_faculty_official_details AS fo ON fo.faculty_id = f.faculty_id
    WHERE 
        f.faculty_status = 1 
        AND f.faculty_deleted = 0 
        AND a.account_portal_type = 1 
        AND a.account_status = 1 
        AND a.deleted = 0 
        AND p.prefixes_status = 1 
        AND p.prefixes_delete = 0
        AND (input_dept_id IS NULL OR input_dept_id = 0 OR fo.dept_id = input_dept_id) -- Department condition
    ORDER BY 
        a.account_code;

    -- Insert activity log entry
    CALL insert_user_activity_log(
        p_login_id, 
        'svcet_tbl_faculty_personal_info,svcet_tbl_accounts,svcet_tbl_dev_prefixes,svcet_tbl_faculty_official_details', 
        1
    );

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Name List Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_official_data` (IN `p_user_id` INT, IN `p_login_id` INT)   BEGIN

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

    -- Fetch the official details of the faculty based on the user_id
    SELECT 
        f.faculty_official_id,
        f.faculty_id,
        f.dept_id,
        f.designation,
        d.dept_title AS department_title,
        g.general_title AS designation_title,
        f.effective_from,
        f.effective_to,
        f.faculty_joining_date,
        f.faculty_salary,
        f.faculty_official_details_status
    FROM 
        svcet_tbl_faculty_official_details f
    JOIN 
        svcet_tbl_dev_dept d ON f.dept_id = d.dept_id
    LEFT JOIN 
        svcet_tbl_dev_general g ON f.designation = g.general_id
    WHERE 
        f.faculty_id = p_user_id
        AND f.faculty_official_deleted = 0
        AND f.faculty_official_details_status = 1; -- Only active, not deleted records
    
    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_official_details', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Official Details have been fetched successfully!' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_personal_data` (IN `p_faculty_login_id` INT, IN `p_faculty_user_id` INT)   BEGIN
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


    -- Main Query
    SELECT 
        f.faculty_account_id,
        f.faculty_first_name,
        f.faculty_middle_name,
        f.faculty_last_name,
        f.faculty_initial,
        f.faculty_salutation,
        sg_salutation.general_title AS faculty_salutation_title,
        f.faculty_dob,
        f.faculty_gender,
        sg_gender.general_title AS faculty_gender_title,
        f.faculty_blood_group,
        sg_blood_group.general_title AS faculty_blood_group_title,
        f.faculty_religion,
        sg_religion.general_title AS faculty_religion_title,
        f.faculty_caste,
        sg_caste.general_title AS faculty_caste_title,
        f.faculty_community,
        sg_community.general_title AS faculty_community_title,
        f.faculty_nationality,
        sg_nationality.general_title AS faculty_nationality_title,
        f.faculty_aadhar_number,
        f.faculty_marital_status,
        sg_marital_status.general_title AS faculty_marital_status_title
    FROM 
        svcet_tbl_faculty_personal_info f
    LEFT JOIN 
        svcet_tbl_dev_general sg_gender ON f.faculty_gender = sg_gender.general_id AND sg_gender.general_group_id = 1
    LEFT JOIN 
        svcet_tbl_dev_general sg_blood_group ON f.faculty_blood_group = sg_blood_group.general_id AND sg_blood_group.general_group_id = 2
    LEFT JOIN 
        svcet_tbl_dev_general sg_nationality ON f.faculty_nationality = sg_nationality.general_id AND sg_nationality.general_group_id = 3
    LEFT JOIN 
        svcet_tbl_dev_general sg_marital_status ON f.faculty_marital_status = sg_marital_status.general_id AND sg_marital_status.general_group_id = 4
    LEFT JOIN 
        svcet_tbl_dev_general sg_religion ON f.faculty_religion = sg_religion.general_id AND sg_religion.general_group_id = 5
    LEFT JOIN 
        svcet_tbl_dev_general sg_caste ON f.faculty_caste = sg_caste.general_id AND sg_caste.general_group_id = 6
    LEFT JOIN 
        svcet_tbl_dev_general sg_community ON f.faculty_community = sg_community.general_id AND sg_community.general_group_id = 7
    LEFT JOIN 
        svcet_tbl_dev_general sg_salutation ON f.faculty_salutation = sg_salutation.general_id AND sg_salutation.general_group_id = 19
    WHERE 
        f.faculty_id = p_faculty_user_id;

    -- Record user activity log after successful query execution
    CALL insert_user_activity_log(p_faculty_login_id, 'svcet_tbl_faculty_personal_info,svcet_tbl_dev_general', 1);

 -- If there were any warnings, return a warning status
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Set success message
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Personal Info fetched successfully!' AS message;
    END IF;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_personal_single_data` (IN `p_faculty_login_id` INT, IN `p_faculty_user_id` INT)   BEGIN
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
        CALL fetch_pr_faculty_personal_data(p_faculty_login_id, p_faculty_user_id);
    END IF;
    IF warning_count = 0 THEN
        CALL fetch_pr_faculty_contact_data(p_faculty_user_id , p_faculty_login_id);
    END IF;

    IF warning_count = 0 THEN
        CALL fetch_pr_faculty_address_data(p_faculty_user_id, p_faculty_login_id);
    END IF;

    IF warning_count = 0 THEN
        CALL fetch_pr_faculty_education_schoolings_data(p_faculty_user_id, p_faculty_login_id);
    END IF;
     
    IF warning_count = 0 THEN
        CALL fetch_pr_faculty_education_degrees_data(p_faculty_user_id, p_faculty_login_id);
    END IF;

    IF warning_count = 0 THEN
        CALL fetch_pr_faculty_document_data(p_faculty_user_id, p_faculty_login_id);
    END IF;
    
   IF warning_count = 0 THEN
        CALL fetch_pr_faculty_experience_data(p_faculty_user_id, p_faculty_login_id);
    END IF;
    
    IF warning_count = 0 THEN
        CALL fetch_pr_faculty_skill_data(p_faculty_user_id, p_faculty_login_id);
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_skills` (IN `p_faculty_skill_faculty_id` INT, IN `p_login_id` INT)   BEGIN

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

    -- Fetch faculty skills based on faculty ID
    SELECT 
        faculty_skill_id,
        faculty_skill_faculty_id,
        faculty_skill_type,
        faculty_skill_name
    FROM 
        svcet_tbl_faculty_skills
    WHERE 
        faculty_skill_faculty_id = p_faculty_skill_faculty_id
        AND faculty_skill_deleted = 0
        AND faculty_skill_status = 1; -- Only active, not deleted records

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_skills', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Faculty skills have been fetched successfully!' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_faculty_skill_data` (IN `p_faculty_id` INT, IN `p_login_id` INT)   BEGIN
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

    -- Select document data for the specified faculty ID where the document is active and not deleted
    SELECT 
       faculty_skill_id ,
        faculty_skill_faculty_id,
        faculty_skill_type,
        faculty_skill_name
    FROM 
        svcet_tbl_faculty_skills
    WHERE 
        faculty_skill_faculty_id = p_faculty_id
        AND faculty_skill_status = 1
        AND faculty_skill_deleted = 0;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_documents', 1);

    -- If there were any warnings, return a warning status
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Set success message
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Documents Fetched successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_gender` (IN `p_login_id` INT)   BEGIN
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

    -- Main query to fetch gender records
    SELECT 
        general_id AS id,
        general_title AS title
    FROM 
        svcet_tbl_dev_general
    WHERE 
        general_group_id = 1
        AND general_status = 1
        AND general_delete = 0;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_general', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Gender Records Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_institution_logo` (IN `p_login_id` INT)   BEGIN

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

    SELECT institution_logo
    FROM svcet_tbl_dev_institution
    LIMIT 1;

    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_institution', 1);

        -- If there were any warnings, return a warning status
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, CONCAT(warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Success message when no warnings occur
        SELECT 200 AS status_code, 'success' AS status, 'Data fetched successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_marital_status` (IN `p_login_id` INT)   BEGIN
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

    -- Main query to fetch marital status records
    SELECT 
        general_id AS id,
        general_title AS title
    FROM 
        svcet_tbl_dev_general
    WHERE 
        general_group_id = 4 -- Group ID for Marital Status
        AND general_status = 1
        AND general_delete = 0;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_general', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Marital Status Records Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_nationality` (IN `p_login_id` INT)   BEGIN
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

    -- Main query to fetch nationality records
    SELECT 
        general_id AS id,
        general_title AS title
    FROM 
        svcet_tbl_dev_general
    WHERE 
        general_group_id = 3 -- Group ID for Nationality
        AND general_status = 1
        AND general_delete = 0;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_general', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Nationality Records Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_religion` (IN `p_login_id` INT)   BEGIN
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

    -- Main query to fetch religion records
    SELECT 
        general_id AS id,
        general_title AS title
    FROM 
        svcet_tbl_dev_general
    WHERE 
        general_group_id = 5 -- Group ID for Religion
        AND general_status = 1
        AND general_delete = 0;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_general', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Religion Records Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_salutations` (IN `p_login_id` INT)   BEGIN
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

    -- Main query to fetch salutations
    SELECT 
        general_id AS id,
        general_title AS title
    FROM 
        svcet_tbl_dev_general
    WHERE 
        general_group_id = 19
        AND general_status = 1
        AND general_delete = 0;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_general', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Salutations Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_single_faculty_achievement` (IN `p_login_id` INT, IN `p_achievement_id` INT)   BEGIN
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

    -- Main Query to fetch achievement details
     SELECT 
        a.faculty_achievements_id,
        a.faculty_id,
        a.achievement_type,
        g.general_title AS achievement_type_title,
        a.achievement_title,
        a.achievement_date,
        a.achievement_venue,
        a.achievement_document,
        a.achievement_status,
        a.achievement_deleted
    FROM 
        svcet_tbl_faculty_achievements a
    JOIN 
        svcet_tbl_dev_general g ON a.achievement_type = g.general_id
    WHERE 
      faculty_achievements_id = p_achievement_id 
        AND achievement_status = 1  -- Only active achievements
        AND achievement_deleted = 0; -- Only non-deleted achievements

    -- Record user activity log after successful query execution
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_achievements', 1);

    -- If there were any warnings, return a warning status
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Set success message
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Achievement fetched successfully!' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_students` (IN `p_login_id` INT, IN `p_dept_id` INT, IN `p_year_of_study_id` INT, IN `p_section_id` INT)   BEGIN
    -- Declare variables for capturing counts
    DECLARE total_students INT DEFAULT 0;

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
        ROLLBACK;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        -- Increment the warning count
        SET warning_count = warning_count + 1;

        -- Capture the warning message
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Fetch student details with department, year, section, batch, and profile picture details
    SELECT 
        sod.student_id,
        sod.dept_id,
        d.dept_title,
        sod.year_of_study_id,
        sod.student_reg_number,
        yos.year_of_study_title,
        sod.section_id,
        sec.section_title,
        CONCAT(spi.student_first_name, ' ', spi.student_middle_name, ' ', spi.student_last_name, ' ', spi.student_initial) AS student_full_name,
        sd.student_doc_path AS profile_picture_path,
        ab.academic_batch_id,
        ab.academic_batch_title
    FROM 
        svcet_tbl_student_official_details sod
    JOIN 
        svcet_tbl_dev_dept d ON d.dept_id = sod.dept_id
    JOIN 
        svcet_tbl_dev_year_of_study yos ON yos.year_of_study_id = sod.year_of_study_id
    JOIN 
        svcet_tbl_dev_section sec ON sec.section_id = sod.section_id
    JOIN 
        svcet_tbl_student_personal_info spi ON spi.student_id = sod.student_id
    LEFT JOIN 
        svcet_tbl_student_documents sd 
        ON sd.student_doc_student_id = sod.student_id 
        AND sd.student_doc_type = 8 
        AND sd.student_doc_status = 1 
        AND sd.student_doc_deleted = 0
    LEFT JOIN 
        svcet_tbl_dev_academic_batch ab 
        ON ab.academic_batch_id = yos.academic_batch_id
        AND ab.academic_batch_status = 1
        AND ab.academic_batch_deleted = 0
    WHERE 
        sod.student_official_details_status = 1 AND 
        sod.student_official_details_deleted = 0
        AND (p_dept_id = 0 OR sod.dept_id = p_dept_id)
        AND (p_year_of_study_id = 0 OR sod.year_of_study_id = p_year_of_study_id)
        AND (p_section_id = 0 OR sod.section_id = p_section_id)

    ORDER BY
        ab.academic_batch_title ASC,
        sod.student_reg_number ASC;

    -- Count total students fetched
    SELECT 
        COUNT(*) 
    INTO 
        total_students
    FROM 
        svcet_tbl_student_official_details sod
    WHERE 
        sod.student_official_details_status = 1 AND 
        sod.student_official_details_deleted = 0
        AND (p_dept_id = 0 OR sod.dept_id = p_dept_id)
        AND (p_year_of_study_id = 0 OR sod.year_of_study_id = p_year_of_study_id)
        AND (p_section_id = 0 OR sod.section_id = p_section_id);

    SELECT total_students;
    -- Fetch total students per year, section, and batch combined
    SELECT 
        sod.year_of_study_id,
        yos.year_of_study_title,
        sod.section_id,
        sec.section_title,
        ab.academic_batch_id,
        ab.academic_batch_title,
        COUNT(*) AS total_students
    FROM 
        svcet_tbl_student_official_details sod
    JOIN 
        svcet_tbl_dev_year_of_study yos ON yos.year_of_study_id = sod.year_of_study_id
    JOIN 
        svcet_tbl_dev_section sec ON sec.section_id = sod.section_id
    LEFT JOIN 
        svcet_tbl_dev_academic_batch ab ON ab.academic_batch_id = yos.academic_batch_id
    WHERE 
        sod.student_official_details_status = 1 AND 
        sod.student_official_details_deleted = 0
        AND (p_dept_id = 0 OR sod.dept_id = p_dept_id)
        AND (p_year_of_study_id = 0 OR sod.year_of_study_id = p_year_of_study_id)
        AND (p_section_id = 0 OR sod.section_id = p_section_id)
    GROUP BY 
        sod.year_of_study_id, 
        yos.year_of_study_title, 
        sod.section_id, 
        sec.section_title, 
        ab.academic_batch_id, 
        ab.academic_batch_title;

    -- Insert activity log
    CALL insert_user_activity_log(
        p_login_id, 
        'svcet_tbl_student_official_details,svcet_tbl_dev_dept,svcet_tbl_dev_year_of_study,svcet_tbl_dev_section,svcet_tbl_student_personal_info,svcet_tbl_student_documents,svcet_tbl_dev_academic_batch', 
        1
    );

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Student Details Fetched Successfully' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_student_commitee_list` (IN `p_login_id` INT, IN `p_dept_id` INT, IN `p_role_id` INT)   BEGIN

    -- Error and warning handling declarations
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_code = RETURNED_SQLSTATE, error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
        ROLLBACK;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Fetch committees from svcet_tbl_dev_general
    SELECT 
        general_id AS value,
        general_title AS title
    FROM 
        svcet_tbl_dev_general
    WHERE 
        general_group_id = 21 
        AND general_status = 1
        AND general_delete = 0;

    -- Fetch student committees and details
SELECT 
    sc.student_committee_id,
    sc.student_id,
    sc.dept_id,
    sc.committee_title As committee_id,
    sc.committee_role As committee_role_id,
    CONCAT(
        COALESCE(spi.student_first_name, ''), ' ',
        COALESCE(spi.student_middle_name, ''), ' ',
        COALESCE(spi.student_last_name, ''), ' ',
        COALESCE(spi.student_initial, '')
    ) AS full_name,
    COALESCE(so.student_reg_number, '') AS register_number,
    COALESCE(doc.student_doc_path, '') AS profile_pic,
    dg_title.general_title AS committee_title,
    CASE 
        WHEN sc.committee_role = 1 THEN 'Head'
        WHEN sc.committee_role = 2 THEN 'Co Ordinator'
        WHEN sc.committee_role = 3 THEN 'Associate Co Ordinator'
        WHEN sc.committee_role = 4 THEN 'Member'
        ELSE 'Unknown Role'
    END AS committee_role,
    sc.effective_from,
    sc.effective_to
FROM 
    svcet_tbl_student_committee sc
LEFT JOIN 
    svcet_tbl_student_personal_info spi ON sc.student_id = spi.student_id
LEFT JOIN 
    svcet_tbl_student_official_details so ON sc.student_id = so.student_id AND so.student_official_details_status = 1 AND so.student_official_details_deleted = 0
LEFT JOIN 
    svcet_tbl_student_documents doc ON sc.student_id = doc.student_doc_student_id AND doc.student_doc_type = 5 AND doc.student_doc_status = 1 AND doc.student_doc_deleted = 0
LEFT JOIN 
    svcet_tbl_dev_general dg_title ON sc.committee_title = dg_title.general_id AND dg_title.general_status = 1 AND dg_title.general_delete = 0
WHERE 
    (sc.dept_id = p_dept_id OR p_dept_id = 0) -- Filter by department if p_dept_id is not 0
    AND (p_role_id = 0 OR p_role_id IS NULL OR sc.committee_role = p_role_id)
    AND sc.committee_status = 1
    AND sc.committee_deleted = 0
ORDER BY
    sc.committee_title,
    sc.committee_role;


    -- Call to log the user activity
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_general,svcet_tbl_student_committee,svcet_tbl_student_personal_info', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Student Committees Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_student_document_data` (IN `p_student_id` INT, IN `p_login_id` INT)   BEGIN
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

    -- Select document data for the specified faculty ID where the document is active and not deleted
    SELECT 
        student_doc_id,
        student_doc_type,
        student_doc_path
    FROM 
        svcet_tbl_student_documents
    WHERE 
        student_doc_student_id = p_student_id
        AND student_doc_status = 1
        AND student_doc_deleted = 0;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_documents', 1);

    -- If there were any warnings, return a warning status
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Set success message
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Documents Fetched successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_student_name_list` (IN `p_dept_id` INT, IN `p_year_of_study_id` INT, IN `p_section_id` INT, IN `p_group_id` INT, IN `p_login_id` INT)   BEGIN

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
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Fetch student name list
    SELECT 
        spi.student_id AS value,
        CONCAT_WS(' ', 
                  IFNULL(spi.student_first_name, ''), 
                  IFNULL(spi.student_middle_name, ''), 
                  IFNULL(spi.student_last_name, ''),
                  IFNULL(spi.student_initial, '')) AS title,
        IFNULL(off.student_reg_number, '') AS code
    FROM 
        svcet_tbl_student_official_details AS off
    LEFT JOIN 
        svcet_tbl_student_personal_info AS spi 
        ON off.student_id = spi.student_id
    WHERE 
        off.student_official_details_status = 1 -- Active records
        AND off.student_official_details_deleted = 0 -- Not Deleted
        AND spi.student_status = 1 -- Active students
        AND spi.student_deleted = 0 -- Not Deleted
        AND (
            p_dept_id = 0 OR off.dept_id = p_dept_id -- Filter by department if dept_id is not 0
        )
        AND (
            p_year_of_study_id = 0 OR off.year_of_study_id = p_year_of_study_id -- Filter by year_of_study if year_of_study_id is not 0
        )
        AND (
            p_section_id = 0 OR off.section_id = p_section_id -- Filter by section if section_id is not 0
        )
        AND (
            p_group_id = 0 OR off.group_id = p_group_id -- Filter by group if group_id is not 0
        )
    ORDER BY 
        off.student_reg_number; -- Sort by register number

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_official_details,svcet_tbl_student_personal_info', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Student Name List Fetched Successfully!' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_student_representatives_list` (IN `p_login_id` INT, IN `p_year_of_study_id` INT, IN `p_dept_id` INT, IN `p_student_id` INT)   BEGIN
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
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Main query to fetch student representatives
SELECT 
    yos.year_of_study_id,
    yos.year_of_study_title,
    yos.dept_id,
    IFNULL(dept.dept_title, '') AS dept_title,
    sec.section_id,
    sec.section_title,
    rep.student_representative_id,
    rep.student_id,
    CONCAT_WS(' ',
              IFNULL(spi.student_first_name, ''),
              IFNULL(spi.student_middle_name, ''),
              IFNULL(spi.student_last_name, ''),
              IFNULL(spi.student_initial, '')) AS student_full_name,
    IFNULL(off.student_reg_number, '') AS student_reg_number,
    IFNULL(doc.student_doc_path, '') AS profile_pic_path, -- Profile picture path
    rep.effective_from,
    rep.effective_to
FROM 
    svcet_tbl_dev_year_of_study AS yos
LEFT JOIN 
    svcet_tbl_dev_section AS sec 
    ON yos.year_of_study_id = sec.year_of_study_id
LEFT JOIN 
    svcet_tbl_student_representative AS rep 
    ON sec.section_id = rep.section_id
LEFT JOIN 
    svcet_tbl_student_personal_info AS spi
    ON rep.student_id = spi.student_id
LEFT JOIN 
    svcet_tbl_student_official_details AS off
    ON rep.student_id = off.student_id
LEFT JOIN 
    svcet_tbl_student_documents AS doc
    ON rep.student_id = doc.student_doc_student_id 
    AND doc.student_doc_type = 8 -- Fetch only Profile Pic
    AND doc.student_doc_status = 1 -- Active document
    AND doc.student_doc_deleted = 0 -- Not Deleted
LEFT JOIN 
    svcet_tbl_dev_dept AS dept 
    ON yos.dept_id = dept.dept_id
WHERE 
    yos.year_of_study_status = 1 -- Active Year of Study
    AND yos.year_of_study_delete = 0 -- Not Deleted
    AND (p_year_of_study_id = 0 OR yos.year_of_study_id = p_year_of_study_id) -- Filter Year of Study
    AND (p_dept_id = 0 OR yos.dept_id = p_dept_id) -- Filter Department
    AND (p_student_id = 0 
         OR (p_student_id != 0 AND rep.student_representative_status IN (1, 3))) -- Conditional status filter
    AND (rep.student_representative_deleted = 0 OR rep.student_representative_deleted IS NULL) 
    AND (p_student_id = 0 OR rep.student_id = p_student_id) -- Filter Specific Student
    AND sec.section_status = 1 -- Active Sections
    AND sec.section_delete = 0 -- Not Deleted Sections
    AND (dept.dept_deleted = 0 OR dept.dept_deleted IS NULL) -- Include NULL for unmatched rows
    AND (dept.dept_status = 1 OR dept.dept_status IS NULL) -- Include NULL for unmatched rows
    AND (off.student_official_details_status = 1 OR off.student_official_details_status IS NULL) -- Active official records
    AND (off.student_official_details_deleted = 0 OR off.student_official_details_deleted IS NULL) -- Not Deleted
ORDER BY 
    yos.year_of_study_title, 
    sec.section_title;


    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Student Representative Details Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_student_tables_admission` (IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(50), IN `p_order_dir` VARCHAR(4), IN `p_start` INT, IN `p_length` INT, IN `p_admission_status` INT, IN `p_admission_method` INT, IN `p_admission_date` DATE, IN `p_login_id` INT)   BEGIN
    DECLARE total_records INT DEFAULT 0;
    DECLARE filtered_records INT DEFAULT 0;
    
    -- Declare variables to capture error and warning messages
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';
    
    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = RETURNED_SQLSTATE, 
            error_message = MESSAGE_TEXT;

        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Get total number of records
    SELECT COUNT(*) INTO total_records 
    FROM svcet_tbl_student_admission_info AS sai
    LEFT JOIN svcet_tbl_student_personal_info AS spi ON sai.student_admission_student_id = spi.student_id
    WHERE sai.admission_deleted = 0;

    -- Query for filtered record count
    SET @filtered_query = CONCAT(
        'SELECT COUNT(*) FROM svcet_tbl_student_admission_info AS sai 
        LEFT JOIN svcet_tbl_student_personal_info AS spi ON sai.student_admission_student_id = spi.student_id 
        WHERE sai.admission_deleted = 0 
        AND (spi.student_first_name LIKE ? OR spi.student_last_name LIKE ?) ',
        IF(p_admission_status IS NOT NULL, ' AND sai.admission_status = ? ', ''),
        IF(p_admission_method > 0, ' AND sai.student_admission_category = ? ', ''),
        IF(p_admission_date IS NOT NULL, ' AND sai.student_admission_date = ? ', '')
    );

    PREPARE stmt_filtered FROM @filtered_query;
    SET @search = CONCAT('%', p_search_value, '%');
    
    -- Execute prepared statement for filtered count
    IF p_admission_status IS NOT NULL AND p_admission_method > 0 AND p_admission_date IS NOT NULL THEN
        EXECUTE stmt_filtered USING @search, @search, p_admission_status, p_admission_method, p_admission_date;
    ELSEIF p_admission_status IS NOT NULL AND p_admission_method > 0 THEN
        EXECUTE stmt_filtered USING @search, @search, p_admission_status, p_admission_method;
    ELSEIF p_admission_status IS NOT NULL THEN
        EXECUTE stmt_filtered USING @search, @search, p_admission_status;
    ELSEIF p_admission_method > 0 THEN
        EXECUTE stmt_filtered USING @search, @search, p_admission_method;
    ELSEIF p_admission_date IS NOT NULL THEN
        EXECUTE stmt_filtered USING @search, @search, p_admission_date;
    ELSE
        EXECUTE stmt_filtered USING @search, @search;
    END IF;

    -- Get the filtered record count
    SELECT FOUND_ROWS() INTO filtered_records;
    DEALLOCATE PREPARE stmt_filtered;

    -- Fetch the data with pagination and sorting
    SET @data_query = CONCAT(
        'SELECT 
                sai.student_admission_student_id,
                spi.student_first_name,
                spi.student_middle_name, 
                spi.student_last_name, 
                sai.student_admission_date, 
                sai.admission_status AS status,
                CASE 
                    WHEN sai.student_admission_type = 1 THEN "New Admission" 
                    ELSE "Lateral Entry" 
                END AS admission_type, 
                CASE 
                    WHEN sai.student_admission_category = 1 THEN "Centac" 
                    ELSE "Management" 
                END AS admission_method, 
                CASE 
                    WHEN sai.admission_status = 0 THEN "Enquired" 
                    WHEN sai.admission_status = 1 THEN "Admitted" 
                    WHEN sai.admission_status = 2 THEN "Active" 
                    WHEN sai.admission_status = 3 THEN "Inactive" 
                    WHEN sai.admission_status = 4 THEN "Discontinued" 
                    ELSE "Declined" 
                END AS admission_status
        FROM svcet_tbl_student_admission_info AS sai
        LEFT JOIN svcet_tbl_student_personal_info AS spi ON sai.student_admission_student_id = spi.student_id
        WHERE sai.admission_deleted = 0
        AND (spi.student_first_name LIKE ? OR spi.student_last_name LIKE ?) ',
        IF(p_admission_status IS NOT NULL, ' AND sai.admission_status = ? ', ''),
        IF(p_admission_method > 0, ' AND sai.student_admission_category = ? ', ''),
        IF(p_admission_date IS NOT NULL, ' AND sai.student_admission_date = ? ', ''),
        ' ORDER BY ', p_sort_column, ' ', p_order_dir, ' LIMIT ?, ?'
    );

    PREPARE data_stmt FROM @data_query;

    -- Execute prepared statement for data retrieval with pagination
    IF p_admission_status IS NOT NULL AND p_admission_method > 0 AND p_admission_date IS NOT NULL THEN
        EXECUTE data_stmt USING @search, @search, p_admission_status, p_admission_method, p_admission_date, p_start, p_length;
    ELSEIF p_admission_status IS NOT NULL AND p_admission_method > 0 THEN
        EXECUTE data_stmt USING @search, @search, p_admission_status, p_admission_method, p_start, p_length;
    ELSEIF p_admission_status IS NOT NULL THEN
        EXECUTE data_stmt USING @search, @search, p_admission_status, p_start, p_length;
    ELSEIF p_admission_method > 0 THEN
        EXECUTE data_stmt USING @search, @search, p_admission_method, p_start, p_length;
    ELSEIF p_admission_date IS NOT NULL THEN
        EXECUTE data_stmt USING @search, @search, p_admission_date, p_start, p_length;
    ELSE
        EXECUTE data_stmt USING @search, @search, p_start, p_length;
    END IF;

    DEALLOCATE PREPARE data_stmt;

    -- Return total and filtered record counts
    SELECT total_records AS total_records, filtered_records AS filtered_records;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_admission_info,svcet_tbl_student_personal_info', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Admission Records Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_view_roles` (IN `p_login_id` INT, IN `p_faculty_id` INT, IN `p_dept_id` INT, IN `p_role_id` INT, IN `p_fetch_type` TINYINT)   BEGIN
    -- Declare variables for error and warning handling
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handler
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = RETURNED_SQLSTATE, 
            error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handler
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Main query
SELECT 
    fr.faculty_roles_and_responsibilities_id,
    CONCAT(
        COALESCE(g.general_title, ''), ' ', 
        COALESCE(f.faculty_first_name, ''), ' ',
        COALESCE(f.faculty_middle_name, ''), ' ',
        COALESCE(f.faculty_last_name, ''), ' ',
        COALESCE(f.faculty_initial, '')
    ) AS full_name,
    d.dept_title AS department_name,
    c.general_title AS committee_title,
    fr.committee_role,
    fr.effective_from,
    fr.effective_to,
    fr.roles_and_responsibilities_status,
    fd.faculty_doc_path AS profile_pic_path,
    dg.general_title AS designation
FROM 
    svcet_tbl_faculty_roles_and_responsibilities AS fr
LEFT JOIN 
    svcet_tbl_faculty_personal_info AS f ON fr.faculty_id = f.faculty_id
LEFT JOIN 
    svcet_tbl_dev_general AS g ON f.faculty_salutation = g.general_id
LEFT JOIN 
    svcet_tbl_dev_dept AS d ON fr.dept_id = d.dept_id
LEFT JOIN 
    svcet_tbl_dev_general AS c ON fr.committee_title = c.general_id
LEFT JOIN 
    svcet_tbl_faculty_documents AS fd 
    ON f.faculty_id = fd.faculty_doc_faculty_id AND fd.faculty_doc_type = 6 -- Profile Pic type
LEFT JOIN 
    svcet_tbl_faculty_official_details AS fod 
    ON f.faculty_id = fod.faculty_id AND fod.faculty_official_deleted = 0 -- Join official details
LEFT JOIN 
    svcet_tbl_dev_general AS dg 
    ON fod.designation = dg.general_id -- Fetch designation from official details
WHERE 
    (p_faculty_id = 0 OR p_faculty_id IS NULL OR fr.faculty_id = p_faculty_id) AND
    (p_dept_id = 0 OR p_dept_id IS NULL OR fr.dept_id = p_dept_id) AND
    (p_role_id = 0 OR p_role_id IS NULL OR fr.committee_role = p_role_id) AND
    fr.roles_and_responsibilities_deleted = 0 AND
    (
        p_fetch_type = 2 OR -- Fetch both active and completed if p_fetch_type is 2
        (p_fetch_type = 1 AND fr.roles_and_responsibilities_status = 1) -- Fetch only active if p_fetch_type is 1
    );



    -- Log the fetch action
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_roles_and_responsibilities', 1);

    -- Return warning details if any
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Set success message
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Roles fetched successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_year_of_study` (IN `p_login_id` INT, IN `p_dept_id` INT)   BEGIN
    -- Declare variables for error and warning handling
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Capture error code and message
        GET DIAGNOSTICS CONDITION 1
            error_code = RETURNED_SQLSTATE,
            error_message = MESSAGE_TEXT;

        -- Return error details
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handling block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        -- Increment warning count
        SET warning_count = warning_count + 1;

        -- Capture warning message
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Query to fetch data
    SELECT 
        yos.year_of_study_id AS value,
        CONCAT(yos.year_of_study_title, ' - Year') AS title,
        ab.academic_batch_title AS code
    FROM 
        svcet_tbl_dev_year_of_study AS yos
    LEFT JOIN 
        svcet_tbl_dev_academic_batch AS ab 
        ON yos.academic_batch_id = ab.academic_batch_id
    WHERE 
        yos.year_of_study_status = 1 -- Active year of study
        AND yos.year_of_study_delete = 0 -- Not deleted
        AND ab.academic_batch_status = 1 -- Active academic batch
        AND ab.academic_batch_deleted = 0 -- Not deleted
        AND yos.dept_id = p_dept_id -- Filter by dept_id
    ORDER BY 
        ab.academic_batch_title DESC; -- Sort by academic_batch_title

    -- Log user activity
    CALL insert_user_activity_log(
        p_login_id, 
        'svcet_tbl_dev_year_of_study,svcet_tbl_dev_academic_batch', 
        1 -- Action code for fetch
    );

    -- Handle warnings, if any
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Return success message
        SELECT 200 AS status_code, 'success' AS status, 'Year of Study fetched successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_stu_admission_address_data` (IN `p_faculty_id` INT, IN `p_student_user_id` INT)   BEGIN
    -- Error and warning handling declarations
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_code = RETURNED_SQLSTATE, error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
        ROLLBACK;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Fetching address details
    SELECT 
        student_address_no,
        student_address_street,
        student_address_locality,
        student_address_pincode,
        student_address_city,
        student_address_district,
        student_address_state,
        student_address_country
    FROM svcet_tbl_student_personal_info
WHERE 
    student_id = p_student_user_id;

    -- Call to log the user activity
  CALL insert_user_activity_log(p_faculty_id, 'svcet_tbl_student_personal_info', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Address Details Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_stu_admission_contact_data` (IN `p_faculty_id` INT, IN `p_student_user_id` INT)   BEGIN
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

  
        -- Fetch faculty contact data
        SELECT 
            student_mobile_number,
            student_alternative_contact_number,
            student_whatsapp_number,
            student_email_id
            
        FROM 
            svcet_tbl_student_personal_info
        WHERE 
            student_id = p_student_user_id;

        -- Log the activity
        
CALL insert_user_activity_log(p_faculty_id, 'svcet_tbl_faculty_contact_info', 1);
        
  -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Contact Details Fetched Successfully!' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_stu_admission_education_degrees` (IN `user_id` INT, IN `p_login_id` INT)   BEGIN
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = RETURNED_SQLSTATE,
            error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Select query to fetch student education data with degree and specialization titles
    SELECT 
        se.student_edu_id,
        se.student_edu_student_id,
        se.student_edu_level,
        se.student_edu_board,
        se.student_edu_institution_name,
        se.student_edu_degree,
        d.general_title AS degree_title,
        se.student_edu_specialization,
        s.general_title AS specialization_title,
        se.student_edu_passed_out_year,
        se.student_edu_cgpa,
        se.student_edu_percentage,
        se.student_edu_total_mark,
        se.student_edu_status
    FROM 
        svcet_tbl_student_education se
    LEFT JOIN 
        svcet_tbl_dev_general d ON se.student_edu_degree = d.general_id AND d.general_group_id = 9 AND d.general_status = 1 AND d.general_delete = 0
    LEFT JOIN 
        svcet_tbl_dev_general s ON se.student_edu_specialization = s.general_id AND s.general_group_id = 12 AND s.general_status = 1 AND s.general_delete = 0
    WHERE 
        se.student_edu_student_id = user_id
        AND se.student_edu_level = 4;

    -- Return success message
    SELECT 200 AS status_code, 'success' AS status, 'Student education data fetched successfully.' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_stu_admission_education_schoolings_data` (IN `p_user_id` INT, IN `p_login_id` INT)   BEGIN
    -- Declare variables for error and warning handling
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_code = RETURNED_SQLSTATE, error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handling block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Fetch the SSLC education data with board title
    SELECT 
        se.student_edu_institution_name AS sslc_institution_name,
        se.student_edu_board AS education_board,
        dg.general_title AS board_title,
        se.student_edu_passed_out_year AS sslc_passed_out_year,
        se.student_edu_percentage AS sslc_percentage,
        se.student_edu_total_mark AS sslc_mark
    FROM 
        svcet_tbl_student_education se
    LEFT JOIN 
        svcet_tbl_dev_general dg 
    ON 
        se.student_edu_board = dg.general_id
    WHERE 
        se.student_edu_student_id = p_user_id
        AND se.student_edu_level = 1  -- 1 for SSLC
        AND dg.general_group_id = 11  -- Group ID for board
        AND dg.general_status = 1     -- Active status
        AND dg.general_delete = 0;    -- Not deleted

    -- Fetch the HSC education data with specialization title
    SELECT 
        se.student_edu_institution_name AS hsc_institution_name,
        se.student_edu_board AS education_board,
        se.student_edu_specialization AS specialization,
        dg.general_title AS board_title,
        se.student_edu_passed_out_year AS hsc_passed_out_year,
        se.student_edu_percentage AS hsc_percentage,
        sg.general_title AS specialization_title,
        se.student_edu_total_mark AS hsc_mark
    FROM 
        svcet_tbl_student_education se
    LEFT JOIN 
        svcet_tbl_dev_general dg 
    ON 
        se.student_edu_board = dg.general_id
    LEFT JOIN 
        svcet_tbl_dev_general sg
    ON 
        se.student_edu_specialization = sg.general_id  -- Assuming specialization is stored in this column
    WHERE 
        se.student_edu_student_id = p_user_id
        AND se.student_edu_level = 2  -- 2 for HSC
        AND dg.general_group_id = 11  -- Group ID for board
        AND dg.general_status = 1     -- Active status
        AND dg.general_delete = 0;    -- Not deleted

    -- Log the user activity
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_education', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'SSLC Data Fetched Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_stu_admission_parent_data` (IN `p_faculty_id` INT, IN `p_student_user_id` INT)   BEGIN
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

  
        -- Fetch faculty contact data
        SELECT 
            student_father_name,
            student_father_occupation,
            student_mother_name,
            student_mother_occupation,
	    student_guardian_name,
	    student_guardian_occupation
        FROM 
            svcet_tbl_student_personal_info
        WHERE 
            student_id = p_student_user_id;

        -- Log the activity
        
CALL insert_user_activity_log(p_faculty_id, 'svcet_tbl_faculty_parent_info', 1);
        
  -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Contact Details Fetched Successfully!' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_stu_admission_personal_data` (IN `p_faculty_login_id` INT, IN `p_student_user_id` INT)   BEGIN
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

    -- Main Query
    SELECT 
        s.student_account_id,
        s.student_first_name,
        s.student_middle_name,
        s.student_last_name,
        s.student_initial,
        
        s.student_dob,
        s.student_gender,
        sg_gender.general_title AS student_gender_title,
        s.student_blood_group,
        sg_blood_group.general_title AS student_blood_group_title,
        s.student_religion,
        sg_religion.general_title AS student_religion_title,
        s.student_caste,
        sg_caste.general_title AS student_caste_title,
        s.student_community,
        sg_community.general_title AS student_community_title,
        s.student_nationality,
        sg_nationality.general_title AS student_nationality_title,
        s.student_aadhar_number,
        s.student_marital_status,
        sg_marital_status.general_title AS student_marital_status_title
    FROM 
        svcet_tbl_student_personal_info s
    LEFT JOIN 
        svcet_tbl_dev_general sg_gender ON s.student_gender = sg_gender.general_id AND sg_gender.general_group_id = 1
    LEFT JOIN 
        svcet_tbl_dev_general sg_blood_group ON s.student_blood_group = sg_blood_group.general_id AND sg_blood_group.general_group_id = 2
    LEFT JOIN 
        svcet_tbl_dev_general sg_nationality ON s.student_nationality = sg_nationality.general_id AND sg_nationality.general_group_id = 3
    LEFT JOIN 
        svcet_tbl_dev_general sg_marital_status ON s.student_marital_status = sg_marital_status.general_id AND sg_marital_status.general_group_id = 4
    LEFT JOIN 
        svcet_tbl_dev_general sg_religion ON s.student_religion = sg_religion.general_id AND sg_religion.general_group_id = 5
    LEFT JOIN 
        svcet_tbl_dev_general sg_caste ON s.student_caste = sg_caste.general_id AND sg_caste.general_group_id = 6
    LEFT JOIN 
        svcet_tbl_dev_general sg_community ON s.student_community = sg_community.general_id AND sg_community.general_group_id = 7
    WHERE 
        s.student_id = p_student_user_id;

    -- Record user activity log after successful query execution
     CALL insert_user_activity_log(p_faculty_login_id, 'svcet_tbl_student_personal_info,svcet_tbl_dev_general', 1);

    -- If there were any warnings, return a warning status
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Set success message
        SELECT 200 AS status_code, 'success' AS status, 'Student Personal Info fetched successfully!' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_achievement_record` (IN `p_faculty_id` INT, IN `p_login_id` INT, IN `p_achievement_type` VARCHAR(255), IN `p_achievement_title` VARCHAR(255), IN `p_achievement_date` DATE, IN `p_achievement_venue` VARCHAR(255), IN `p_file_link` VARCHAR(255))   BEGIN
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
        SELECT 500 AS status_code, 'error' AS status, CONCAT('SQL Error: ', error_message) AS message;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        -- Increment the warning count
        SET warning_count = warning_count + 1;

        -- Capture the warning message
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Insert query to add achievement record
    INSERT INTO svcet_tbl_faculty_achievements (
        faculty_id, 
        achievement_type, 
        achievement_title, 
        achievement_date, 
        achievement_venue, 
        achievement_document
    ) VALUES (
        p_faculty_id, 
        p_achievement_type, 
        p_achievement_title, 
        p_achievement_date, 
        p_achievement_venue, 
        p_file_link
    );

    -- Check for warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Record inserted with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Achievement record inserted successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_error_log` (IN `p_login_id` INT, IN `p_error_side` TINYINT, IN `p_page_link` TEXT, IN `p_error_message` TEXT)   BEGIN

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
    INSERT INTO `svcet_tbl_error_log` (
        `login_id`,
        `error_side`,
        `page_link`,
        `error_message`
    )
    VALUES (
        p_login_id,
        p_error_side,
        p_page_link,
        p_error_message
    );

        -- Record user activity log after successful query execution
     CALL insert_user_activity_log(p_login_id, 'svcet_tbl_error_log', 2);

    -- If there were any warnings, return a warning status
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Set success message
        SELECT 200 AS status_code, 'success' AS status, 'Error Log Updated successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_pr_create_parent_account` (IN `p_parent_user_name` VARCHAR(50), IN `p_parent_type` TINYINT, IN `p_parent_code` INT, IN `p_parent_role` INT, IN `p_parent_first_name` VARCHAR(100), IN `p_parent_middle_name` VARCHAR(255), IN `p_parent_last_name` VARCHAR(100), IN `p_parent_initial` VARCHAR(100), IN `p_parent_mobile_number` VARCHAR(15), IN `p_parent_email_id` VARCHAR(100), IN `p_student_id` INT, IN `p_relationship_type` TINYINT)   BEGIN
    DECLARE new_account_id INT;
    DECLARE new_account_code INT;
    DECLARE last_account_code INT;
    DECLARE prefix VARCHAR(255);
    DECLARE username VARCHAR(255);
    DECLARE v_account_id INT;
    DECLARE v_parent_id INT;

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

    -- Fetch the last account code for the given portal type
    INSERT INTO svcet_tbl_accounts (
        account_username,
        account_portal_type,
        account_code,
        role_id,
        account_status,
        deleted
    ) VALUES (
        p_parent_user_name,
        p_parent_type,
        p_parent_code,
        p_parent_role,
        1, -- Active
        0  -- Not Deleted
    );

    -- Step 2: Retrieve the account_id
    SET v_account_id = LAST_INSERT_ID();

    -- Step 3: Insert into svcet_tbl_parent_personal_info
    INSERT INTO svcet_tbl_parent_personal_info (
        parent_account_id,
        parent_first_name,
        parent_middle_name,
        parent_last_name,
        parent_initial,
        parent_mobile_number,
        parent_email_id,
        parent_status,
        parent_deleted
    ) VALUES (
        v_account_id,
        p_parent_first_name,
        p_parent_middle_name,
        p_parent_last_name,
        p_parent_initial,
        p_parent_mobile_number,
        p_parent_email_id,
        1, -- Active
        0  -- Not Deleted
    );

    -- Step 4: Retrieve the parent_id
    SET v_parent_id = LAST_INSERT_ID();

    -- Step 5: Insert into svcet_tbl_student_parent_relation
    INSERT INTO svcet_tbl_student_parent_relation (
        student_id,
        parent_id,
        relationship_type,
        relation_status,
        relation_deleted
    ) VALUES (
        p_student_id,
        v_parent_id,
        p_relationship_type,
        1, -- Active
        0  -- Not Deleted
    );

    -- Optional: Return the IDs
    SELECT v_account_id AS account_id, v_parent_id AS parent_id;
    
    -- Return warnings if any
     IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Success message
        SELECT 200 AS status_code, 'success' AS status, 
               'Parent Account Created Successfully.' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_pr_dev_create_new_bulk_account` (IN `first_name` VARCHAR(255), IN `middle_name` VARCHAR(255), IN `last_name` VARCHAR(255), IN `name_initial` VARCHAR(10), IN `portal_type` INT, IN `role_id` INT)   BEGIN
    DECLARE new_account_id INT;
    DECLARE new_account_code INT;
    DECLARE last_account_code INT;
    DECLARE prefix VARCHAR(255);
    DECLARE username VARCHAR(255);

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

    -- Fetch the last account code for the given portal type
    SELECT COALESCE(MAX(account_code), 0)
    INTO last_account_code
    FROM svcet_tbl_accounts
    WHERE account_portal_type = portal_type;

    -- Increment the account code
    SET new_account_code = last_account_code + 1;

    -- Fetch the correct prefix based on the portal type
    SELECT prefixes_title
    INTO prefix
    FROM svcet_tbl_dev_prefixes
    WHERE prefixes_group_id = portal_type
    AND prefixes_status = 1
    AND prefixes_delete = 0
    LIMIT 1;

    -- Concatenate the prefix and new account code to generate the username
    SET username = CONCAT(prefix, new_account_code);

    -- Insert into svcet_tbl_accounts
    INSERT INTO `svcet_tbl_accounts` (account_username, account_portal_type, account_code, role_id, account_status, deleted)
    VALUES (username, portal_type, new_account_code, role_id, 1, 0);

    -- Get the last inserted account_id
    SET new_account_id = LAST_INSERT_ID();
    
    -- Insert based on portal type
    IF portal_type = 1 THEN
        -- Faculty portal type
        INSERT INTO `svcet_tbl_faculty_personal_info` 
        (faculty_account_id, faculty_first_name, faculty_middle_name, faculty_last_name, faculty_initial, faculty_status, faculty_deleted)
        VALUES 
        (new_account_id, first_name, middle_name, last_name, name_initial, 0, 0);

-- Retrieve the last inserted faculty ID and assign it to `@new_faculty_id`
SET @new_faculty_id = LAST_INSERT_ID();

        
        SELECT 200 AS status_code, 'success' AS status, 'Faculty account created successfully' AS message;
        
    ELSEIF portal_type = 2 THEN
        -- Student portal type
        INSERT INTO `svcet_tbl_student_personal_info`
        (student_account_id, student_first_name, student_middle_name, student_last_name, student_initial, student_status, student_deleted)
        VALUES 
        (new_account_id, first_name, middle_name, last_name, name_initial, 0, 0);
        
        SELECT 200 AS status_code, 'success' AS status, 'Student account created successfully' AS message;

    ELSEIF portal_type = 3 THEN
        -- Parent portal type
        INSERT INTO `svcet_tbl_parent_personal_info`
        (parent_account_id, parent_first_name, parent_middle_name, parent_last_name, parent_initial, parent_status, parent_deleted)
        VALUES 
        (new_account_id, first_name, middle_name, last_name, name_initial, 0, 0);
        
        SELECT 200 AS status_code, 'success' AS status, 'Parent account created successfully' AS message;

    ELSE
        -- Invalid portal type
        SELECT 500 AS status_code, 'error' AS status, 'Invalid portal type' AS message;
    END IF;
    
    -- Return warnings if any
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_pr_dev_create_new_single_account` (IN `first_name` VARCHAR(100), IN `middle_name` VARCHAR(100), IN `last_name` VARCHAR(100), IN `name_initial` VARCHAR(10), IN `portal_type` TINYINT(1), IN `new_account_code` INT(11), IN `username` VARCHAR(50), IN `p_role_id` INT)   BEGIN
    DECLARE new_account_id INT;
    
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

    -- Insert into svcet_tbl_accounts
    INSERT INTO `svcet_tbl_accounts` (account_username, account_portal_type, account_code, role_id, account_status, deleted)
    VALUES (username, portal_type, new_account_code, p_role_id, 1, 0);
    
    -- Get the last inserted account_id
    SET new_account_id = LAST_INSERT_ID();
    
    -- Insert based on portal type
    IF portal_type = 1 THEN
        -- Faculty portal type
INSERT INTO `svcet_tbl_faculty_personal_info` 
    (faculty_id, faculty_account_id, faculty_first_name, faculty_middle_name, faculty_last_name, faculty_initial, faculty_status, faculty_deleted)
VALUES 
    (NULL, new_account_id, first_name, middle_name, last_name, name_initial, 0, 0);

-- Retrieve the last inserted faculty ID and assign it to `@new_faculty_id`
SET @new_faculty_id = LAST_INSERT_ID();

        
        SELECT 200 AS status_code, 'success' AS status, 'Faculty account created successfully' AS message;
        
    ELSEIF portal_type = 2 THEN
        -- Student portal type
        INSERT INTO `svcet_tbl_student_personal_info`
        (student_account_id, student_first_name, student_middle_name, student_last_name, student_initial, student_status, student_deleted)
        VALUES 
        (new_account_id, first_name, middle_name, last_name, name_initial, 0, 0);
        
        SELECT 200 AS status_code, 'success' AS status, 'Student account created successfully' AS message;

    ELSEIF portal_type = 3 THEN
        -- Parent portal type
        INSERT INTO `svcet_tbl_parent_personal_info`
        (parent_account_id, parent_first_name, parent_middle_name, parent_last_name, parent_initial, parent_status, parent_deleted)
        VALUES 
        (new_account_id, first_name, middle_name, last_name, name_initial, 0, 0);
        
        SELECT 200 AS status_code, 'success' AS status, 'Parent account created successfully' AS message;

    ELSE
        -- Invalid portal type
        SELECT 500 AS status_code, 'error' AS status, 'Invalid portal type' AS message;
    END IF;
    
    -- Return warnings if any
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_student_address_details` (IN `p_existing_id` INT, IN `p_address_pincode` VARCHAR(20), IN `p_address_no` VARCHAR(255), IN `p_address_street` VARCHAR(100), IN `p_address_locality` VARCHAR(10), IN `p_address_city` VARCHAR(100), IN `p_address_district` VARCHAR(100), IN `p_address_state` VARCHAR(100), IN `p_address_country` VARCHAR(100))   BEGIN
    DECLARE v_status_code INT DEFAULT 200;
    DECLARE v_status_message VARCHAR(255) DEFAULT 'Address details inserted successfully.';
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
   
        -- Update existing address details for the student
        UPDATE svcet_tbl_student_personal_info
        SET
            student_address_no = p_address_no,
            student_address_street = p_address_street,
            student_address_locality = p_address_locality,
            student_address_pincode = p_address_pincode,
            student_address_city = p_address_city,
            student_address_district = p_address_district,
            student_address_state = p_address_state,
            student_address_country = p_address_country
        WHERE student_id = p_existing_id;
        
        SET v_status_message = 'Address details updated successfully.';
   

   
   

   -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Contact Details Update Successfully!' AS message,   p_existing_id AS existing_student_id;
    END IF;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_stu_create_addmission_contact` (IN `existing_student_id` INT, IN `student_mobile_number` VARCHAR(15), IN `student_alternative_contact_number` VARCHAR(15), IN `student_whatsapp_number` VARCHAR(15), IN `student_email_id` VARCHAR(100))   BEGIN
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = RETURNED_SQLSTATE, 
            error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Update the student's contact information
    UPDATE svcet_tbl_student_personal_info
    SET 
        student_mobile_number = student_mobile_number,
        student_alternative_contact_number = student_alternative_contact_number,
        student_whatsapp_number = student_whatsapp_number,
        student_email_id = student_email_id
    WHERE student_id = existing_student_id;
    
    -- Return success message
    SELECT 200 AS status_code, 'success' AS status, 
           'Student contact information updated successfully.' AS message,
           existing_student_id AS existing_student_id;

    -- Return warnings if any
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_stu_create_addmission_profile` (IN `existing_student_id` INT, IN `first_name` VARCHAR(100), IN `middle_name` VARCHAR(100), IN `last_name` VARCHAR(100), IN `name_initial` VARCHAR(10), IN `dob` DATE, IN `gender` INT(1), IN `aadhar` VARCHAR(20), IN `religion` INT(50), IN `caste` INT(50), IN `community` INT(50), IN `nationality` INT(50), IN `blood_group` INT(5), IN `marital_status` INT(20))   BEGIN
    DECLARE last_inserted_id INT;
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = RETURNED_SQLSTATE, 
            error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Check if the existing student ID is NULL
   IF existing_student_id = 0 THEN
        -- Insert a new student record
        INSERT INTO svcet_tbl_student_personal_info (
            student_first_name, 
            student_middle_name, 
            student_last_name, 
            student_initial, 
            student_dob, 
            student_gender, 
            student_aadhar_number, 
            student_religion, 
            student_caste, 
            student_community, 
            student_nationality, 
            student_blood_group, 
            student_marital_status
        ) 
        VALUES (
            first_name, 
            middle_name, 
            last_name, 
            name_initial, 
            dob, 
            gender, 
            aadhar, 
            religion, 
            caste, 
            community, 
            nationality, 
            blood_group, 
            marital_status
        );

        -- Retrieve the last inserted ID
        SET last_inserted_id = LAST_INSERT_ID();
        
        -- Return success message with last inserted ID
        SELECT 200 AS status_code, 'success' AS status, 
               'New student record inserted successfully.' AS message, 
               last_inserted_id AS existing_student_id;
    ELSE
        -- Update the existing student record
        UPDATE svcet_tbl_student_personal_info
        SET 
            student_first_name = first_name,
            student_middle_name = middle_name,
            student_last_name = last_name,
            student_gender = gender,
            student_aadhar_number = aadhar,
            student_religion = religion,
            student_caste = caste,
            student_community = community,
            student_nationality = nationality,
            student_blood_group = blood_group,
            student_marital_status = marital_status
        WHERE student_id = existing_student_id;
        
        -- Return success message
        SELECT 200 AS status_code, 'success' AS status, 
               'Student record updated successfully.' AS message,
               existing_student_id AS existing_student_id;
    END IF;

    -- Return warnings if any
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_stu_create_admission_parent` (IN `existing_student_id` INT, IN `student_father_name` VARCHAR(100), IN `student_father_occupation` VARCHAR(100), IN `student_mother_name` VARCHAR(100), IN `student_mother_occupation` VARCHAR(100), IN `student_guardian_name` VARCHAR(255), IN `student_guardian_occupation` VARCHAR(255))   BEGIN
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = RETURNED_SQLSTATE, 
            error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Update only existing student parent and guardian details
    UPDATE svcet_tbl_student_personal_info
    SET 
        student_father_name = student_father_name,
        student_father_occupation = student_father_occupation,
        student_mother_name = student_mother_name,
        student_mother_occupation = student_mother_occupation,
        student_guardian_name = student_guardian_name,
        student_guardian_occupation = student_guardian_occupation
    WHERE student_id = existing_student_id;
    
    -- Return success message for update
    SELECT 200 AS status_code, 'success' AS status, 
           'Student parent and guardian details updated successfully.' AS message,
           existing_student_id AS existing_student_id;

    -- Return warnings if any
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_user_activity_log` (IN `p_login_id` INT, IN `p_db_table_affected` VARCHAR(255), IN `p_action_type` TINYINT)   BEGIN
    DECLARE table_name VARCHAR(255);
    DECLARE pos INT DEFAULT 1;
    DECLARE comma_pos INT DEFAULT 0;
    DECLARE db_string_length INT DEFAULT LENGTH(p_db_table_affected);

    -- Loop through the comma-separated values
    WHILE pos <= db_string_length DO
        -- Find the position of the next comma
        SET comma_pos = LOCATE(',', p_db_table_affected, pos);
        
        -- If no more commas are found, set it to the length of the string
        IF comma_pos = 0 THEN
            SET comma_pos = db_string_length + 1;
        END IF;

        -- Extract the table name from the current position to the next comma
        SET table_name = TRIM(SUBSTRING(p_db_table_affected, pos, comma_pos - pos));

        -- Insert the extracted table name into the log table
        INSERT INTO svcet_tbl_user_activity_log (login_id, db_table_affected, action_type)
        VALUES (p_login_id, table_name, p_action_type);

        -- Move the position to the next character after the comma
        SET pos = comma_pos + 1;
    END WHILE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `login_validate` (IN `p_user_id` INT, IN `p_portal_type` INT, IN `p_log_id` INT, IN `p_user_ip_address` VARCHAR(45), IN `p_successful_login` TINYINT, IN `p_login_status` TINYINT, IN `p_logout` TINYINT)   BEGIN
    DECLARE last_attempt_successful TINYINT;
    DECLARE failed_attempts INT DEFAULT 0;
    DECLARE last_inserted_id INT;

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

    -- If the logout flag is 0, update the user_id and user_ip_address to logout
    IF p_logout = 0 THEN
        UPDATE svcet_tbl_login_logs
        SET login_status = 0, logout_time = NOW()
        WHERE log_id = p_log_id;

        -- Optionally return a message indicating logout
        SELECT 200 AS status_code, 'success' AS status, 'User logged out successfully.' AS message;

    ELSE
        -- Get the last two distinct IP addresses, setting them to 0 if NULL
   -- Get the last two distinct IP addresses
        SELECT log_id, user_ip_address
        INTO @last_log_id_1, @last_two_ip_1
        FROM svcet_tbl_login_logs
        WHERE user_id = p_user_id
        ORDER BY login_time DESC
        LIMIT 1;

        -- If no rows were returned, set to 0 manually
        IF ROW_COUNT() = 0 THEN
            SET @last_log_id_1 = 0;
            -- SET @last_two_ip_1 = '0.0.0.0';
        END IF;

        SELECT log_id, user_ip_address
        INTO @last_log_id_2, @last_two_ip_2
        FROM svcet_tbl_login_logs
        WHERE user_id = p_user_id
          AND user_ip_address != @last_two_ip_1
        ORDER BY login_time DESC
        LIMIT 1;

        -- If no rows were returned, set to 0 manually
        IF ROW_COUNT() = 0 THEN
            SET @last_log_id_2 = 0;
            -- SET @last_two_ip_2 = '0.0.0.0';
        END IF;

        -- Check the status of the last login attempt
        SELECT successful_login INTO last_attempt_successful
        FROM svcet_tbl_login_logs
        WHERE user_id = p_user_id 
          AND user_ip_address = p_user_ip_address 
          AND DATE(login_time) = CURDATE()
        ORDER BY login_time DESC
        LIMIT 1;

            -- Get the count of failed attempts for the last three attempts today
            SELECT COUNT(*) INTO failed_attempts
            FROM (
                SELECT successful_login
                FROM svcet_tbl_login_logs
                WHERE user_id = p_user_id 
                  AND user_ip_address = p_user_ip_address
                  AND DATE(login_time) = CURDATE()
                ORDER BY login_time DESC
                LIMIT 3 -- Limit to the last three attempts
            ) AS last_three_attempts
            WHERE successful_login = 0; -- Count failed attempts




        -- If the last attempt is unsuccessful or does not exist
        IF last_attempt_successful = 0 THEN

            -- If there are 3 failed attempts, return an error
            IF failed_attempts >= 3 THEN
                SELECT 'Password Mismatch. You have exceeded the login attempts. Try again after 24 hours.' AS message, 400 AS status_code, 'error' AS status;

            -- If the login is successful (p_successful_login = 1), insert log record
            ELSEIF p_successful_login = 1 AND failed_attempts < 3 THEN

                INSERT INTO svcet_tbl_login_logs (user_id, login_time, user_ip_address, successful_login, login_status)
                VALUES (p_user_id, NOW(), p_user_ip_address, p_successful_login, p_login_status);

                SET last_inserted_id = LAST_INSERT_ID(); 

                UPDATE svcet_tbl_login_logs
                SET login_status = 0, logout_time = NOW()
                WHERE user_id = p_user_id
                  AND login_status = 1
                  AND successful_login = 1
                  AND log_id NOT IN (@last_log_id_1, @last_log_id_2,last_inserted_id);

                CALL fetch_login_details(p_user_id, p_portal_type,last_inserted_id );

                SELECT 200 AS status_code, 'success' AS status, 'Logged In Successfully' AS message;

            -- If there are 2 failed attempts, return a warning
            ELSEIF failed_attempts = 2 THEN

                INSERT INTO svcet_tbl_login_logs (user_id, login_time, user_ip_address, successful_login, login_status)
                VALUES (p_user_id, NOW(), p_user_ip_address, p_successful_login, p_login_status);

                SET last_inserted_id = LAST_INSERT_ID(); 

                SELECT 'Password Mismatch. You have exceeded the login attempts. Try again after 24 hours.' AS message, 400 AS status_code, 'error' AS status;  

            -- If there is 1 failed attempt, return a warning
            ELSEIF failed_attempts = 1 THEN

                INSERT INTO svcet_tbl_login_logs (user_id, login_time, user_ip_address, successful_login, login_status)
                VALUES (p_user_id, NOW(), p_user_ip_address, p_successful_login, p_login_status);

                SET last_inserted_id = LAST_INSERT_ID(); 

                SELECT 'Password Mismatch: Only One attempts remaining.' AS message, 300 AS status_code, 'warning' AS status;

            END IF;

        ELSE
            -- If the login is successful but the last login was also successful, insert the log record

                INSERT INTO svcet_tbl_login_logs (user_id, login_time, user_ip_address, successful_login, login_status)
                VALUES (p_user_id, NOW(), p_user_ip_address, p_successful_login, p_login_status);

                SET last_inserted_id = LAST_INSERT_ID(); 

            UPDATE svcet_tbl_login_logs
            SET login_status = 0, logout_time = NOW()
            WHERE user_id = p_user_id
              AND login_status = 1
              AND successful_login = 1
              AND log_id NOT IN (@last_log_id_1, @last_log_id_2,last_inserted_id);
            
   

            IF p_successful_login = 1 THEN

                CALL fetch_login_details(p_user_id, p_portal_type,last_inserted_id );

                SELECT 200 AS status_code, 'success' AS status, 'Logged In Successfully' AS message;

            ELSEIF p_successful_login = 0 THEN


                SELECT 'Password Mismatch: Only two attempts remaining.' AS message, 300 AS status_code, 'warning' AS status;
            END IF;
        END IF;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_fetch_faculty_subjects` (IN `p_faculty_id` INT)   BEGIN
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message TEXT DEFAULT '';

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

    -- Fetching the faculty's subjects including room details
    SELECT 
        fsub.faculty_subjects_id,
        fsub.subject_id,
        fsub.room_id,
        fsub.sem_duration_id,
        sub.subject_name,
        sub.subject_code,
        sub.subject_short_name,
        sub.subject_type,
        sub.number_of_hours,
        sub.no_of_periods_per_week,
        sub.no_of_periods_per_day,
        sub.year_of_study_id,
        yos.year_of_study_title,
        sub.section_id,
        sec.section_title,
        sub.dept_id,
        dept.dept_title,
        sub.sem_id,
        sem.sem_title,
        sub.academic_batch_id,
        sub.academic_year_id,
        semdur.sem_duration_start_date,
        semdur.sem_duration_end_date,
        room.room_name -- Added room_name from room management table
    FROM 
        svcet_tbl_faculty_subjects fsub
    JOIN 
        svcet_tbl_dev_subject sub ON fsub.subject_id = sub.subject_id
    JOIN 
        svcet_tbl_dev_year_of_study yos ON sub.year_of_study_id = yos.year_of_study_id
    JOIN 
        svcet_tbl_dev_section sec ON sub.section_id = sec.section_id
    JOIN 
        svcet_tbl_dev_dept dept ON sub.dept_id = dept.dept_id
    JOIN 
        svcet_tbl_dev_sem sem ON sub.sem_id = sem.sem_id
    JOIN 
        svcet_tbl_dev_sem_duration semdur ON fsub.sem_duration_id = semdur.sem_duration_id
    JOIN 
        svcet_tbl_dev_room_management room ON fsub.room_id = room.room_id -- Joining the room management table
    WHERE 
        fsub.faculty_id = p_faculty_id
        AND fsub.faculty_subjects_status = 1
        AND fsub.faculty_subjects_deleted = 0
    ORDER BY 
        yos.year_of_study_title, sec.section_title, sub.subject_name;

    -- If there were any warnings, return a warning status
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, CONCAT(warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Success message when no warnings occur
        SELECT 200 AS status_code, 'success' AS status, 'Data fetched successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `svcet_pr_fetch_subject_availability` (IN `in_day_id` INT, IN `in_period_id` INT, IN `in_subject_id` INT, IN `in_dept_id` INT, IN `in_academic_batch_id` INT, IN `in_academic_year_id` INT, IN `in_year_of_study_id` INT, IN `in_sem_id` INT, IN `in_section_id` INT)   BEGIN
    DECLARE start_date DATE;
    DECLARE end_date DATE;
    DECLARE gen_current_date DATE;
    DECLARE timetable_status VARCHAR(20);
    DECLARE day_title VARCHAR(50);
    DECLARE total_days INT DEFAULT 0;  -- Counter for available days
	DECLARE total_no_of_periods INT DEFAULT 0;
    -- Error and warning handling declarations
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_code = RETURNED_SQLSTATE, error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
        ROLLBACK;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Start a transaction
    START TRANSACTION;

    -- Step 1: Check for timetable availability
    IF EXISTS (
        SELECT 1 
        FROM svcet_tbl_faculty_timetable 
        WHERE day_id = in_day_id 
        AND period_id = in_period_id 
        AND timetable_deleted = 0
        AND faculty_subjects_id = in_subject_id
    ) THEN
        SET timetable_status = 'Slot Taken';
        SELECT 300 AS status_code, 'warning' AS status, timetable_status AS availability;
        COMMIT;
    ELSE
        SET timetable_status = 'Slot Available';

        -- Fetch start and end dates
        SELECT sem_duration_start_date, sem_duration_end_date 
        INTO start_date, end_date
        FROM svcet_tbl_dev_sem_duration 
        WHERE sem_duration_section_id = in_section_id
        AND sem_duration_academic_batch_id = in_academic_batch_id
        AND sem_duration_academic_year_id = in_academic_year_id
        AND sem_duration_year_of_study_id = in_year_of_study_id
        AND sem_duration_sem_id = in_sem_id
        AND sem_duration_dept_id = in_dept_id
        AND sem_duration_status = 1 
        AND sem_duration_delete = 0;

        -- Fetch day title
        SELECT day_title INTO day_title 
        FROM svcet_tbl_dev_day 
        WHERE day_id = in_day_id AND day_status = 1 AND day_deleted = 0;

        SET gen_current_date = start_date;

        -- Adjust to match day
        WHILE DAYOFWEEK(gen_current_date) <> in_day_id + 1 DO
            SET gen_current_date = gen_current_date + INTERVAL 1 DAY;
        END WHILE;

        -- Loop through matching days
        WHILE gen_current_date <= end_date DO
            -- Check conflicts
            IF EXISTS (
                SELECT 1
                FROM svcet_tbl_faculty_events
                WHERE sem_duration_id = (SELECT sem_duration_id FROM svcet_tbl_dev_sem_duration
                                         WHERE sem_duration_section_id = in_section_id
                                         AND sem_duration_academic_batch_id = in_academic_batch_id
                                         AND sem_duration_academic_year_id = in_academic_year_id
                                         AND sem_duration_year_of_study_id = in_year_of_study_id
                                         AND sem_duration_sem_id = in_sem_id
                                         AND sem_duration_dept_id = in_dept_id
                                         AND sem_duration_status = 1 
                                         AND sem_duration_delete = 0)
                AND event_deleted = 0
                AND event_status = 1
                AND gen_current_date BETWEEN event_start_date AND event_end_date
            ) THEN
                SELECT gen_current_date AS event_date, 'Event Conflict' AS event_status;
            ELSE
                SELECT gen_current_date AS available_date, timetable_status AS timetable_status;
                SET total_days = total_days + 1;  -- Increment available days
            END IF;

            -- Move to next week
            SET gen_current_date = gen_current_date + INTERVAL 7 DAY;
        END WHILE;

		SELECT number_of_hours INTO total_no_of_periods FROM svcet_tbl_dev_subject WHERE subject_id = in_subject_id;
        -- Return the total days
        SELECT 200 AS status_code, 'success' AS status, 
               'Operation completed successfully.' AS message, total_days, total_no_of_periods ;

        COMMIT;
    END IF;

    -- Return warnings if any
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_achievement_record` (IN `p_achievement_id` INT, IN `p_faculty_id` INT, IN `p_login_id` INT, IN `p_achievement_type` VARCHAR(255), IN `p_achievement_title` VARCHAR(255), IN `p_achievement_date` DATE, IN `p_achievement_venue` VARCHAR(255), IN `p_file_link` VARCHAR(255))   BEGIN
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
        SELECT 500 AS status_code, 'error' AS status, CONCAT('SQL Error: ', error_message) AS message;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        -- Increment the warning count
        SET warning_count = warning_count + 1;

        -- Capture the warning message
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Update query to modify achievement record
    UPDATE svcet_tbl_faculty_achievements
    SET 
        faculty_id = p_faculty_id,
        achievement_type = p_achievement_type,
        achievement_title = p_achievement_title,
        achievement_date = p_achievement_date,
        achievement_venue = p_achievement_venue,
        achievement_document = CASE 
            WHEN p_file_link IS NOT NULL AND p_file_link <> '' THEN p_file_link 
            ELSE achievement_document 
        END
    WHERE 
        faculty_achievements_id = p_achievement_id;

      -- Check for warnings
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_achievements', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 400 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Achievement Records update Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_confirmation_student_admission` (IN `p_student_id` INT, IN `p_admission_type` VARCHAR(50), IN `p_role_id` INT, IN `p_user_no` INT, IN `p_portal_type` INT, IN `p_student_username` VARCHAR(50))   BEGIN
    -- Declare variables
    DECLARE last_account_id INT;

    -- Check if the student ID and admission type are valid
    IF p_student_id IS NULL OR p_student_id = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Invalid student ID.';
    END IF;

    IF p_admission_type IS NULL OR p_admission_type = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Admission type cannot be empty.';
    END IF;

    -- Update svcet_tbl_student_admission_info table
    UPDATE svcet_tbl_student_admission_info
    SET 
        admission_status = 1, -- Assuming 1 is the confirmed status
        student_admission_date = NOW() 
    WHERE 
        student_admission_student_id = p_student_id
        AND admission_status = 0;

    -- Update or Insert into svcet_tbl_accounts
    IF EXISTS (SELECT 1 FROM svcet_tbl_accounts WHERE account_username = p_student_username) THEN
        UPDATE svcet_tbl_accounts
        SET 
            account_portal_type = p_portal_type,
            account_code = p_user_no,
            role_id = p_role_id,
            account_status = 1,
            deleted = 0
        WHERE 
            account_username = p_student_username;
    ELSE
        INSERT INTO svcet_tbl_accounts (
            account_username, 
            account_portal_type, 
            account_code, 
            role_id, 
            account_status, 
            deleted
        )
        VALUES (
            p_student_username, 
            p_portal_type, 
            p_user_no, 
            p_role_id, 
            1, 
            0
        );

        -- Get the last inserted account ID
        SET last_account_id = LAST_INSERT_ID();
    END IF;

    -- Update svcet_tbl_student_personal_info with last account ID
    IF last_account_id IS NOT NULL THEN
        UPDATE svcet_tbl_student_personal_info
        SET student_account_id = last_account_id
        WHERE student_id = p_student_id;
    END IF;

    -- Update svcet_tbl_student_official_details
    UPDATE svcet_tbl_student_official_details
    SET 
        academic_batch_id = p_admission_type,
        student_official_details_status = 1,
        student_official_details_deleted = 0
    WHERE 
        student_id = p_student_id;

    -- Return success message
    SELECT 200 AS status_code, 'success' AS status, 'Student admission confirmation updated successfully.' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_password` (IN `new_password` TEXT, IN `logged_account_id` INT, IN `logged_login_id` INT)   BEGIN
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

    -- Update the password for the specific account
    UPDATE svcet_tbl_accounts
    SET account_password = new_password
    WHERE account_id = logged_account_id
      AND deleted = 0;

          -- Insert activity log entry
    CALL insert_user_activity_log(logged_login_id, 'svcet_tbl_dev_account', 3);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Password Changed Successfully!' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_pr_decline_student_confirmation` (IN `p_login_id` INT, IN `student_id` INT)   BEGIN
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

    -- Main query to fetch academic batch records
    UPDATE svcet_tbl_student_admission_info
        SET admission_status = 5
        WHERE student_admission_student_id = student_id;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_admission_info', 3);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Student Declined successfully✅' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_pr_faculty_authorities_roles` (IN `p_login_id` INT, IN `p_faculty_id_json` JSON, IN `p_faculty_authorities_id_json` JSON, IN `p_dept_id_json` JSON, IN `p_authorities_group_id_json` JSON)   BEGIN
    DECLARE current_index INT DEFAULT 0;
    DECLARE current_total_count INT;
    DECLARE current_faculty_id INT UNSIGNED;
    DECLARE current_authorities_id INT UNSIGNED;
    DECLARE current_dept_id INT UNSIGNED;
    DECLARE current_authorities_group_id INT UNSIGNED;

    -- Declare variables for error handling
    DECLARE current_error_code VARCHAR(5);
    DECLARE current_error_message VARCHAR(255);

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            current_error_code = RETURNED_SQLSTATE, 
            current_error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, current_error_message AS message;
    END;

    -- Get total number of elements in JSON arrays
    SET current_total_count = JSON_LENGTH(p_faculty_id_json);

    -- Loop through each JSON array element
    WHILE current_index < current_total_count DO
        -- Extract values from JSON
        SET current_faculty_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_faculty_id_json, CONCAT('$[', current_index, ']'))) AS UNSIGNED);
        SET current_authorities_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_faculty_authorities_id_json, CONCAT('$[', current_index, ']'))) AS UNSIGNED);
        SET current_dept_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_dept_id_json, CONCAT('$[', current_index, ']'))) AS UNSIGNED);
        SET current_authorities_group_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_authorities_group_id_json, CONCAT('$[', current_index, ']'))) AS UNSIGNED);

        -- Set NULL for any 0 values
        IF current_faculty_id = 0 THEN
            SET current_faculty_id = NULL;
        END IF;

        IF current_authorities_id = 0 THEN
            SET current_authorities_id = NULL;
        END IF;

        IF current_dept_id = 0 THEN
            SET current_dept_id = NULL;
        END IF;

        IF current_authorities_group_id = 0 THEN
            SET current_authorities_group_id = NULL;
        END IF;

        -- Handle logic based on conditions
        IF current_authorities_id IS NOT NULL THEN
            -- Check if the existing row matches input values
            IF NOT EXISTS (
                SELECT 1 
                FROM svcet_tbl_faculty_authorities
                WHERE faculty_authorities_id = current_authorities_id
                  AND (faculty_id <=> current_faculty_id OR faculty_id IS NULL)
                  AND (dept_id <=> current_dept_id OR dept_id IS NULL)
                  AND (faculty_authorities_group_id <=> current_authorities_group_id OR faculty_authorities_group_id IS NULL)
                  AND faculty_authorities_deleted = 0
            ) THEN
                -- Update the existing row as completed
                UPDATE svcet_tbl_faculty_authorities
                SET effective_to = NOW(),
                    faculty_authorities_status = 3
                WHERE faculty_authorities_id = current_authorities_id;

                -- Log user activity
                CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_authorities', 3);

            -- Insert a new record
            INSERT INTO svcet_tbl_faculty_authorities (
                faculty_id, faculty_authorities_group_id, dept_id, effective_from, faculty_authorities_status, faculty_authorities_deleted
            ) VALUES (
                current_faculty_id, current_authorities_group_id, current_dept_id, NOW(), 1, 0
            );
            
            -- Log user activity
            CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_authorities', 2);
            END IF;
        ELSEIF current_authorities_id IS NULL AND current_faculty_id IS NOT NULL THEN
            -- Insert a new record
            INSERT INTO svcet_tbl_faculty_authorities (
                faculty_id, faculty_authorities_group_id, dept_id, effective_from, faculty_authorities_status, faculty_authorities_deleted
            ) VALUES (
                current_faculty_id, current_authorities_group_id, current_dept_id, NOW(), 1, 0
            );
            
            -- Log user activity
            CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_authorities', 2);
        END IF;

        -- Increment the index to process the next JSON array element
        SET current_index = current_index + 1;
    END WHILE;

    -- Return success
    SELECT 200 AS status_code, 'success' AS status, 'Faculty Authorities Roles Updated Successfully!' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_pr_faculty_dept_class_advisors_roles` (IN `p_login_id` INT, IN `p_faculty_id` JSON, IN `p_faculty_class_advisors_id` JSON, IN `p_ca_year_of_study_id` JSON, IN `p_ca_section_id` JSON, IN `p_faculty_dept_id` JSON)   BEGIN
    -- Declare variables
    DECLARE current_index INT DEFAULT 0;
    DECLARE total_count INT;
    DECLARE v_faculty_id INT;
    DECLARE v_faculty_class_advisors_id INT;
    DECLARE v_ca_year_of_study_id INT;
    DECLARE v_ca_section_id INT;
    DECLARE v_faculty_dept_id INT;

    -- Error handling variables
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handler
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = RETURNED_SQLSTATE, 
            error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
        ROLLBACK;
    END;

    -- Warning handler
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Start transaction
    START TRANSACTION;

    -- Get the total number of JSON array elements
    SET total_count = JSON_LENGTH(p_faculty_id);

    -- Loop through each JSON array element
    WHILE current_index < total_count DO
        -- Extract JSON values
        SET v_faculty_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_faculty_id, CONCAT('$[', current_index, ']'))) AS UNSIGNED);
        SET v_faculty_class_advisors_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_faculty_class_advisors_id, CONCAT('$[', current_index, ']'))) AS UNSIGNED);
        SET v_ca_year_of_study_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_ca_year_of_study_id, CONCAT('$[', current_index, ']'))) AS UNSIGNED);
        SET v_ca_section_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_ca_section_id, CONCAT('$[', current_index, ']'))) AS UNSIGNED);
        SET v_faculty_dept_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_faculty_dept_id, CONCAT('$[', current_index, ']'))) AS UNSIGNED);

        -- Case 1: faculty_class_advisors_id is non-zero
        IF v_faculty_class_advisors_id != 0 THEN
            -- Check for an existing record
            IF NOT EXISTS (
                SELECT 1
                FROM svcet_tbl_faculty_class_advisors
                WHERE faculty_class_advisors_id = v_faculty_class_advisors_id
                  AND faculty_id = v_faculty_id
                  AND dept_id = v_faculty_dept_id
                  AND year_of_study_id = v_ca_year_of_study_id
                  AND section_id = v_ca_section_id
                  AND faculty_class_advisors_deleted = 0
            ) THEN
           
                -- Update old record
                UPDATE svcet_tbl_faculty_class_advisors
                SET effective_to = NOW(),
                    faculty_class_advisors_status = 3
                WHERE faculty_class_advisors_id = v_faculty_class_advisors_id
                  AND faculty_class_advisors_deleted = 0;

                -- Log activity for update
                CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_class_advisors', 3);

                -- Insert new record
                INSERT INTO svcet_tbl_faculty_class_advisors (
                    faculty_id, dept_id, year_of_study_id, section_id, effective_from, faculty_class_advisors_status, faculty_class_advisors_deleted
                ) VALUES (
                    v_faculty_id, v_faculty_dept_id, v_ca_year_of_study_id, v_ca_section_id, NOW(), 1, 0
                );

                -- Log activity for insert
                CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_class_advisors', 2);
            END IF;

        -- Case 2: faculty_class_advisors_id is zero
        ELSE
          IF v_faculty_id IS NOT NULL AND v_faculty_id != 0 THEN

          
                -- Insert new record
                INSERT INTO svcet_tbl_faculty_class_advisors (
                    faculty_id, dept_id, year_of_study_id, section_id, effective_from, faculty_class_advisors_status, faculty_class_advisors_deleted
                ) VALUES (
                    v_faculty_id, v_faculty_dept_id, v_ca_year_of_study_id, v_ca_section_id, NOW(), 1, 0
                );

                -- Log activity for insert
                CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_class_advisors', 2);
            END IF;
        END IF;

        -- Increment index
        SET current_index = current_index + 1;
    END WHILE;

    -- Commit transaction
    COMMIT;

    -- Return success or warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Class Advisors updated successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_pr_faculty_dept_committee_roles` (IN `p_login_id` INT, IN `p_dept_id` INT, IN `committee_id_json` JSON, IN `faculty_id_json` JSON, IN `committee_roles_json` JSON, IN `p_type` INT, IN `p_r_r_id` INT)   BEGIN
    DECLARE p_current_date DATE DEFAULT CURDATE();
    DECLARE i INT DEFAULT 0;
    DECLARE committee_length INT;
    DECLARE v_committee_id INT;
    DECLARE v_faculty_id INT;
    DECLARE v_committee_role INT;

    -- Declare variables for error and warning handling
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message TEXT DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_code = RETURNED_SQLSTATE, error_message = MESSAGE_TEXT;
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

    -- Main logic
    IF p_type = 1 THEN
        UPDATE svcet_tbl_faculty_roles_and_responsibilities
        SET effective_to = p_current_date,
            roles_and_responsibilities_status = 3
        WHERE faculty_roles_and_responsibilities_id = p_r_r_id;

        CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_roles_and_responsibilities', 3);

    ELSE
        -- Extract JSON arrays into individual components
        SET committee_length = JSON_LENGTH(committee_id_json);

        -- Loop through each item in JSON arrays
        committee_loop: WHILE i < committee_length DO
            -- Extract current array elements and cast to integers
            SET v_committee_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(committee_id_json, CONCAT('$[', i, ']'))) AS UNSIGNED);
            SET v_faculty_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(faculty_id_json, CONCAT('$[', i, ']'))) AS UNSIGNED);
            SET v_committee_role = CAST(JSON_UNQUOTE(JSON_EXTRACT(committee_roles_json, CONCAT('$[', i, ']'))) AS UNSIGNED);

            -- Skip iteration if faculty_id is 0
            IF v_faculty_id = 0 THEN
                SET i = i + 1;
                ITERATE committee_loop; -- Skip to the next iteration
            END IF;

            -- Check if there is an existing active record for the faculty in this committee
            IF EXISTS (
                SELECT 1
                FROM svcet_tbl_faculty_roles_and_responsibilities
                WHERE faculty_id = v_faculty_id
                  AND committee_title = v_committee_id
                  AND dept_id = p_dept_id
                  AND roles_and_responsibilities_status = 1
                  AND roles_and_responsibilities_deleted = 0
            ) THEN
                -- Check if the existing record has the same role
                IF NOT EXISTS (
                    SELECT 1
                    FROM svcet_tbl_faculty_roles_and_responsibilities
                    WHERE faculty_id = v_faculty_id
                      AND committee_title = v_committee_id
                      AND committee_role = v_committee_role
                      AND dept_id = p_dept_id
                      AND roles_and_responsibilities_status = 1
                      AND roles_and_responsibilities_deleted = 0
                ) THEN
                    -- Update existing record's effective_to with the current date
                    UPDATE svcet_tbl_faculty_roles_and_responsibilities
                    SET effective_to = p_current_date,
                        roles_and_responsibilities_status = 3
                    WHERE faculty_id = v_faculty_id
                      AND committee_title = v_committee_id
                      AND dept_id = p_dept_id
                      AND roles_and_responsibilities_status = 1
                      AND roles_and_responsibilities_deleted = 0;

                    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_roles_and_responsibilities', 3);

                    -- Insert the new role/committee record
                    INSERT INTO svcet_tbl_faculty_roles_and_responsibilities (
                        faculty_id,
                        dept_id,
                        committee_title,
                        committee_role,
                        effective_from,
                        effective_to,
                        roles_and_responsibilities_status,
                        roles_and_responsibilities_deleted
                    )
                    VALUES (
                        v_faculty_id,
                        p_dept_id,
                        v_committee_id,
                        v_committee_role,
                        p_current_date,
                        NULL, -- New effective_to is set to NULL
                        1, -- Active status
                        0  -- Not deleted
                    );

                    -- Insert user activity log
                    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_roles_and_responsibilities', 2);
                END IF;

            ELSE
                -- No existing active record, insert the new role/committee record
                INSERT INTO svcet_tbl_faculty_roles_and_responsibilities (
                    faculty_id,
                    dept_id,
                    committee_title,
                    committee_role,
                    effective_from,
                    effective_to,
                    roles_and_responsibilities_status,
                    roles_and_responsibilities_deleted
                )
                VALUES (
                    v_faculty_id,
                    p_dept_id,
                    v_committee_id,
                    v_committee_role,
                    p_current_date,
                    NULL, -- New effective_to is set to NULL
                    1, -- Active status
                    0  -- Not deleted
                );

                -- Insert user activity log
                CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_roles_and_responsibilities', 2);
            END IF;

            -- Increment the index after processing the current item
            SET i = i + 1;
        END WHILE committee_loop;
    END IF;

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Updated Faculty Committees and Roles Successfully!' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_pr_faculty_document_profile_info` (IN `p_faculty_id` INT, IN `p_login_id` INT, IN `p_faculty_resume_id` INT, IN `p_faculty_sslc_id` INT, IN `p_faculty_hsc_id` INT, IN `p_faculty_highest_qualification_id` INT, IN `p_faculty_experience_id` JSON, IN `p_sslc` TEXT, IN `p_hsc` TEXT, IN `p_highest_qualification` TEXT, IN `p_resume` TEXT, IN `p_experience` JSON, IN `p_profile_pic` TEXT, IN `p_profile_pic_id` INT)   BEGIN
    -- Declare variables for error and warning handling
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Declare variables for experience link extraction
    DECLARE experience_index INT DEFAULT 0;
    DECLARE experience_count INT DEFAULT JSON_LENGTH(p_experience);
    DECLARE experience_path TEXT;
    DECLARE experience_doc_id INT;

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_code = RETURNED_SQLSTATE, error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Upsert for Resume Document
    IF p_faculty_resume_id = 0 THEN
        INSERT INTO svcet_tbl_faculty_documents (faculty_doc_faculty_id, faculty_doc_type, faculty_doc_path, faculty_doc_status, faculty_doc_deleted)
        VALUES (p_faculty_id, 1, p_resume, 1, 0);
    ELSE
        UPDATE svcet_tbl_faculty_documents
        SET faculty_doc_path = p_resume, faculty_doc_status = 1, faculty_doc_deleted = 0
        WHERE faculty_doc_id = p_faculty_resume_id;
    END IF;

    -- Upsert for SSLC Document
    IF p_faculty_sslc_id = 0 THEN
        INSERT INTO svcet_tbl_faculty_documents (faculty_doc_faculty_id, faculty_doc_type, faculty_doc_path, faculty_doc_status, faculty_doc_deleted)
        VALUES (p_faculty_id, 2, p_sslc, 1, 0);
    ELSE
        UPDATE svcet_tbl_faculty_documents
        SET faculty_doc_path = p_sslc, faculty_doc_status = 1, faculty_doc_deleted = 0
        WHERE faculty_doc_id = p_faculty_sslc_id;
    END IF;

    -- Upsert for HSC Document
    IF p_faculty_hsc_id = 0 THEN
        INSERT INTO svcet_tbl_faculty_documents (faculty_doc_faculty_id, faculty_doc_type, faculty_doc_path, faculty_doc_status, faculty_doc_deleted)
        VALUES (p_faculty_id, 3, p_hsc, 1, 0);
    ELSE
        UPDATE svcet_tbl_faculty_documents
        SET faculty_doc_path = p_hsc, faculty_doc_status = 1, faculty_doc_deleted = 0
        WHERE faculty_doc_id = p_faculty_hsc_id;
    END IF;

    -- Upsert for Highest Qualification Document
    IF p_faculty_highest_qualification_id = 0 THEN
        INSERT INTO svcet_tbl_faculty_documents (faculty_doc_faculty_id, faculty_doc_type, faculty_doc_path, faculty_doc_status, faculty_doc_deleted)
        VALUES (p_faculty_id, 4, p_highest_qualification, 1, 0);
    ELSE
        UPDATE svcet_tbl_faculty_documents
        SET faculty_doc_path = p_highest_qualification, faculty_doc_status = 1, faculty_doc_deleted = 0
        WHERE faculty_doc_id = p_faculty_highest_qualification_id;
    END IF;

    -- Upsert for Profile Pic Document
    IF p_profile_pic_id = 0 THEN
        INSERT INTO svcet_tbl_faculty_documents (faculty_doc_faculty_id, faculty_doc_type, faculty_doc_path, faculty_doc_status, faculty_doc_deleted)
        VALUES (p_faculty_id, 6, p_profile_pic, 1, 0);
    ELSE
        UPDATE svcet_tbl_faculty_documents
        SET faculty_doc_path = p_profile_pic, faculty_doc_status = 1, faculty_doc_deleted = 0
        WHERE faculty_doc_id = p_profile_pic_id ;
    END IF;

    -- Insert or Update Experience Certificates (Multiple Entries)
    IF experience_count > 0 THEN
        experience_loop:LOOP
            IF experience_index >= experience_count THEN
                LEAVE experience_loop;
            END IF;

            -- Extract experience path and document ID from JSON arrays
            SET experience_path = JSON_UNQUOTE(JSON_EXTRACT(p_experience, CONCAT('$[', experience_index, ']')));
            SET experience_doc_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_faculty_experience_id, CONCAT('$[', experience_index, ']'))) AS UNSIGNED);


            -- Upsert logic for each experience certificate
            IF experience_doc_id = 0 THEN
                INSERT INTO svcet_tbl_faculty_documents (faculty_doc_faculty_id, faculty_doc_type, faculty_doc_path, faculty_doc_status, faculty_doc_deleted)
                VALUES (p_faculty_id, 5, experience_path, 1, 0);
            ELSE
                UPDATE svcet_tbl_faculty_documents
                SET faculty_doc_path = experience_path, faculty_doc_status = 1, faculty_doc_deleted = 0
                WHERE faculty_doc_id = experience_doc_id;
            END IF;

            SET experience_index = experience_index + 1;
        END LOOP;
    END IF;

    UPDATE svcet_tbl_faculty_personal_info SET faculty_status = 1 WHERE faculty_id = p_faculty_id;


    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_documents', 2);

   
    -- Handle warnings and success messages
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Documents updated successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_pr_faculty_education_degree_profile_info` (IN `p_faculty_id` INT, IN `p_login_id` INT, IN `p_degree_institution_name` JSON, IN `p_education_degree` JSON, IN `p_education_degree_specialization` JSON, IN `p_degree_passed_out_year` JSON, IN `p_degree_percentage` JSON, IN `p_degree_cgpa` JSON, IN `p_degree_id` JSON)   BEGIN
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';
    DECLARE degree_count INT;
    DECLARE degree_index INT DEFAULT 0;

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

    -- Determine the number of elements in the JSON array
    SET degree_count = JSON_LENGTH(p_degree_id);

    -- Loop through each degree object in the JSON array
    WHILE degree_index < degree_count DO
        -- Extract values from the current degree JSON object
        SET @degree_institution_name = JSON_UNQUOTE(JSON_EXTRACT(p_degree_institution_name, CONCAT('$[', degree_index, ']')));
        SET @education_degree = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_education_degree, CONCAT('$[', degree_index, ']'))) AS UNSIGNED);
        SET @education_degree_specialization = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_education_degree_specialization, CONCAT('$[', degree_index, ']'))) AS UNSIGNED);
        SET @degree_passed_out_year = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_degree_passed_out_year, CONCAT('$[', degree_index, ']'))) AS UNSIGNED);
        SET @degree_percentage = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_degree_percentage, CONCAT('$[', degree_index, ']'))) AS DECIMAL(5,2));
        SET @degree_cgpa = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_degree_cgpa, CONCAT('$[', degree_index, ']'))) AS DECIMAL(5,3));
        SET @degree_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_degree_id, CONCAT('$[', degree_index, ']'))) AS UNSIGNED);

        -- Check if the record exists
        IF EXISTS (
            SELECT 1 
            FROM svcet_tbl_faculty_education 
            WHERE faculty_edu_id = @degree_id AND faculty_edu_faculty_id = p_faculty_id
        ) THEN
            -- Update if the record exists
            UPDATE svcet_tbl_faculty_education
            SET
                faculty_edu_institution_name = @degree_institution_name,
                faculty_edu_degree = @education_degree,
                faculty_edu_specialization = @education_degree_specialization,
                faculty_edu_passed_out_year = @degree_passed_out_year,
                faculty_edu_percentage = @degree_percentage,
                faculty_edu_cgpa = @degree_cgpa
            WHERE 
                faculty_edu_id = @degree_id AND faculty_edu_faculty_id = p_faculty_id;

            -- Log the update action
            CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_education', 3);
            
        ELSE
            -- Insert a new record if it does not exist
            INSERT INTO svcet_tbl_faculty_education (
                faculty_edu_faculty_id,
                faculty_edu_level,
                faculty_edu_institution_name,
                faculty_edu_degree,
                faculty_edu_specialization,
                faculty_edu_passed_out_year,
                faculty_edu_percentage,
                faculty_edu_cgpa
            ) VALUES (
                p_faculty_id,
                4, -- Degree level
                @degree_institution_name,
                @education_degree,
                @education_degree_specialization,
                @degree_passed_out_year,
                @degree_percentage,
                @degree_cgpa
            );

            -- Log the insert action
            CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_education', 2);
        END IF;

        -- Increment the index for the next iteration
        SET degree_index = degree_index + 1;
    END WHILE;

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Degree Education Information Updated Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_pr_faculty_education_schoolings_profile_info` (IN `p_sslc_institution_name` VARCHAR(255), IN `p_education_board` INT, IN `p_sslc_passed_out_year` YEAR, IN `p_sslc_percentage` DECIMAL(5,2), IN `p_hsc_institution_name` VARCHAR(255), IN `p_education_hsc_board` INT, IN `p_education_hsc_specialization` INT, IN `p_hsc_passed_out_year` YEAR, IN `p_hsc_percentage` DECIMAL(5,2), IN `p_faculty_id` INT, IN `p_login_id` INT)   BEGIN
    -- Declare variables for error and warning handling
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Capture error code and message
        GET DIAGNOSTICS CONDITION 1
            error_code = RETURNED_SQLSTATE, 
            error_message = MESSAGE_TEXT;

        -- Return error details
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
        ROLLBACK;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        -- Increment warning count
        SET warning_count = warning_count + 1;

        -- Capture warning message
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Check if SSLC record exists, then update or insert
    IF EXISTS (
        SELECT 1 
        FROM svcet_tbl_faculty_education 
        WHERE faculty_edu_faculty_id = p_faculty_id AND faculty_edu_level = 1
    ) THEN
        -- Update SSLC record
        UPDATE svcet_tbl_faculty_education
        SET
            faculty_edu_institution_name = p_sslc_institution_name,
            faculty_edu_board = p_education_board,
            faculty_edu_passed_out_year = p_sslc_passed_out_year,
            faculty_edu_percentage = p_sslc_percentage
        WHERE 
            faculty_edu_faculty_id = p_faculty_id AND faculty_edu_level = 1;

        -- Log the update action
        CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_education', 3);
        
    ELSE
        -- Insert SSLC record
        INSERT INTO svcet_tbl_faculty_education (
            faculty_edu_faculty_id,
            faculty_edu_level,
            faculty_edu_board,
            faculty_edu_institution_name,
            faculty_edu_passed_out_year,
            faculty_edu_percentage
        ) VALUES (
            p_faculty_id,
            1, -- SSLC level
            p_education_board,
            p_sslc_institution_name,
            p_sslc_passed_out_year,
            p_sslc_percentage
        );

        -- Log the insert action
        CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_education', 2);
    END IF;

    -- Check if HSC record exists, then update or insert
    IF EXISTS (
        SELECT 1 
        FROM svcet_tbl_faculty_education 
        WHERE faculty_edu_faculty_id = p_faculty_id AND faculty_edu_level = 2
    ) THEN
        -- Update HSC record
        UPDATE svcet_tbl_faculty_education
        SET
            faculty_edu_institution_name = p_hsc_institution_name,
            faculty_edu_board = p_education_hsc_board,
            faculty_edu_specialization = p_education_hsc_specialization,
            faculty_edu_passed_out_year = p_hsc_passed_out_year,
            faculty_edu_percentage = p_hsc_percentage
        WHERE 
            faculty_edu_faculty_id = p_faculty_id AND faculty_edu_level = 2;

        -- Log the update action
        CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_education', 3);
        
    ELSE
        -- Insert HSC record
        INSERT INTO svcet_tbl_faculty_education (
            faculty_edu_faculty_id,
            faculty_edu_level,
            faculty_edu_board,
            faculty_edu_institution_name,
            faculty_edu_specialization,
            faculty_edu_passed_out_year,
            faculty_edu_percentage
        ) VALUES (
            p_faculty_id,
            2, -- HSC level
            p_education_hsc_board,
            p_hsc_institution_name,
            p_education_hsc_specialization,
            p_hsc_passed_out_year,
            p_hsc_percentage
        );

        -- Log the insert action
        CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_education', 2);
    END IF;

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Education Information Updated Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_pr_faculty_experience_profile_info` (IN `p_experience_id` JSON, IN `p_field_of_experience` JSON, IN `p_experience_industry_name` JSON, IN `p_experience_designation` JSON, IN `p_experience_industry_department` JSON, IN `p_experience_industry_start_date` JSON, IN `p_experience_industry_end_date` JSON, IN `p_faculty_id` INT, IN `p_login_id` INT)   BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE total_entries INT DEFAULT JSON_LENGTH(p_field_of_experience);
    DECLARE v_experience_id INT;
    DECLARE v_field_of_experience INT;
    DECLARE v_experience_industry_name VARCHAR(255);
    DECLARE v_experience_designation VARCHAR(100);
    DECLARE v_experience_industry_specialization VARCHAR(100);
    DECLARE v_experience_industry_start_date DATE;
    DECLARE v_experience_industry_end_date DATE;
    -- Error and warning handling variables
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handler block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_code = RETURNED_SQLSTATE, error_message = MESSAGE_TEXT;
        ROLLBACK;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Loop through each JSON array element
    WHILE i < total_entries DO
        -- Extract and convert JSON values with appropriate types
        SET v_experience_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_experience_id, CONCAT('$[', i, ']'))) AS UNSIGNED);
        SET v_field_of_experience = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_field_of_experience, CONCAT('$[', i, ']'))) AS UNSIGNED);
        SET v_experience_industry_name = JSON_UNQUOTE(JSON_EXTRACT(p_experience_industry_name, CONCAT('$[', i, ']')));
        SET v_experience_designation = JSON_UNQUOTE(JSON_EXTRACT(p_experience_designation, CONCAT('$[', i, ']')));
        SET v_experience_industry_specialization = JSON_UNQUOTE(JSON_EXTRACT(p_experience_industry_department, CONCAT('$[', i, ']')));
        SET v_experience_industry_start_date = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_experience_industry_start_date, CONCAT('$[', i, ']'))) AS DATE);
        SET v_experience_industry_end_date = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_experience_industry_end_date, CONCAT('$[', i, ']'))) AS DATE);

        -- Check if experience_id is 0 for insert or non-zero for update
        IF v_experience_id = 0 THEN
            -- Insert new experience record
            INSERT INTO svcet_tbl_faculty_experience (
                faculty_exp_faculty_id,
                faculty_exp_field_of_experience,
                faculty_exp_industry_name,
                faculty_exp_designation,
                faculty_exp_specialization,
                faculty_exp_start_date,
                faculty_exp_end_date,
                faculty_exp_status,
                faculty_exp_deleted
            ) VALUES (
                p_faculty_id,
                v_field_of_experience,
                v_experience_industry_name,
                v_experience_designation,
                v_experience_industry_specialization,
                v_experience_industry_start_date,
                v_experience_industry_end_date,
                1, -- Default Active Status
                0  -- Default Not Deleted
            );

            -- Log activity for insert (optional)
            CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_experience', 2);

        ELSE
            -- Update existing experience record
            UPDATE svcet_tbl_faculty_experience
            SET faculty_exp_field_of_experience = v_field_of_experience,
                faculty_exp_industry_name = v_experience_industry_name,
                faculty_exp_designation = v_experience_designation,
                faculty_exp_specialization = v_experience_industry_specialization,
                faculty_exp_start_date = v_experience_industry_start_date,
                faculty_exp_end_date = v_experience_industry_end_date
            WHERE faculty_exp_id = v_experience_id;

            -- Log activity for update (optional)
            CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_experience', 3);
        END IF;

        -- Increment loop counter
        SET i = i + 1;
    END WHILE;

    -- Return appropriate status message based on warnings or success
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Experience Details Updated Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_pr_faculty_mentor_role` (IN `p_login_id` INT, IN `p_update_type` INT, IN `p_mentor_details_json` JSON, IN `p_from_faculty_id` INT, IN `p_to_faculty_id` INT, IN `p_dept_id` INT)   BEGIN
    DECLARE v_current_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

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

    -- Handle Reset (1)
    IF p_update_type = 1 THEN
        -- Extract faculty IDs from the JSON and iterate over them
        SET @json_array = JSON_KEYS(p_mentor_details_json);
        SET @array_length = JSON_LENGTH(@json_array);

        SET @index = 0;
        WHILE @index < @array_length DO
            SET @faculty_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(@json_array, CONCAT('$[', @index, ']'))) AS UNSIGNED);

            -- Check if the faculty has active records
            IF EXISTS (
                SELECT 1 
                FROM svcet_tbl_faculty_mentor 
                WHERE faculty_id = @faculty_id AND faculty_mentor_status = 1
            ) THEN
                -- Update existing records for the faculty
                UPDATE svcet_tbl_faculty_mentor
                SET faculty_mentor_status = 3,
                    effective_to = v_current_timestamp
                WHERE faculty_id = @faculty_id AND faculty_mentor_status = 1;
            END IF;

            -- Extract student IDs for the current faculty ID
            SET @students_json = JSON_UNQUOTE(JSON_EXTRACT(p_mentor_details_json, CONCAT('$."', @faculty_id, '"')));
            SET @students_array_length = JSON_LENGTH(@students_json);

            SET @student_index = 0;
            WHILE @student_index < @students_array_length DO
                SET @student_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(@students_json, CONCAT('$[', @student_index, ']'))) AS UNSIGNED);

                -- Check if the student is already assigned to an active faculty
                IF EXISTS (
                    SELECT 1
                    FROM svcet_tbl_faculty_mentor
                    WHERE student_id = @student_id AND faculty_mentor_status = 1 AND dept_id = p_dept_id
                ) THEN
                    -- Update existing student records
                    UPDATE svcet_tbl_faculty_mentor
                    SET faculty_mentor_status = 3,
                        effective_to = v_current_timestamp
                    WHERE student_id = @student_id AND faculty_mentor_status = 1 AND dept_id = p_dept_id;
                END IF;

                -- Insert the new mapping for the faculty and student
                INSERT INTO svcet_tbl_faculty_mentor (
                    faculty_id,
                    student_id,
                    dept_id,
                    effective_from,
                    faculty_mentor_status,
                    faculty_mentor_deleted
                ) VALUES (
                    @faculty_id,
                    @student_id,
                    p_dept_id,
                    v_current_timestamp,
                    1,
                    0
                );

                SET @student_index = @student_index + 1;
            END WHILE;

            SET @index = @index + 1;
        END WHILE;
    END IF;

    -- Handle Swap (2)
IF p_update_type = 2 THEN
    -- Step 1: Update active records of `to_faculty_id` to `faculty_mentor_status = 3`
    UPDATE svcet_tbl_faculty_mentor
    SET faculty_mentor_status = 3,
        effective_to = v_current_timestamp
    WHERE faculty_id = p_to_faculty_id AND faculty_mentor_status = 1;
    
    -- Step 2: Copy and insert active records of `from_faculty_id` into `to_faculty_id`
    INSERT INTO svcet_tbl_faculty_mentor (faculty_id, student_id, dept_id, effective_from, faculty_mentor_status, faculty_mentor_deleted)
    SELECT p_to_faculty_id, student_id, p_dept_id, v_current_timestamp, 1, 0
    FROM svcet_tbl_faculty_mentor
    WHERE faculty_id = p_from_faculty_id AND faculty_mentor_status = 1;

    -- Step 3: Update active records of `from_faculty_id` to `faculty_mentor_status = 3`
    UPDATE svcet_tbl_faculty_mentor
    SET faculty_mentor_status = 3,
        effective_to = v_current_timestamp
    WHERE faculty_id = p_from_faculty_id AND faculty_mentor_status = 1;

 
END IF;


    -- Log the activity
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_mentor', p_update_type);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Query executed successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_pr_faculty_personal_address_profile_info` (IN `p_faculty_id` INT, IN `p_login_id` INT, IN `p_faculty_address_no` VARCHAR(20), IN `p_faculty_address_street` VARCHAR(255), IN `p_faculty_address_locality` VARCHAR(100), IN `p_faculty_address_pincode` VARCHAR(10), IN `p_faculty_address_city` VARCHAR(100), IN `p_faculty_address_district` VARCHAR(100), IN `p_faculty_address_state` VARCHAR(100), IN `p_faculty_address_country` VARCHAR(100))   BEGIN
    -- Error and warning handling declarations
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_code = RETURNED_SQLSTATE, error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
        ROLLBACK;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    START TRANSACTION;

    UPDATE svcet_tbl_faculty_personal_info
    SET 
        faculty_address_no = p_faculty_address_no,
        faculty_address_street = p_faculty_address_street,
        faculty_address_locality = p_faculty_address_locality,
        faculty_address_pincode = p_faculty_address_pincode,
        faculty_address_city = p_faculty_address_city,
        faculty_address_district = p_faculty_address_district,
        faculty_address_state = p_faculty_address_state,
        faculty_address_country = p_faculty_address_country
    WHERE faculty_id = p_faculty_id;

    -- Call to log the user activity
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_personal_info', 3);

    COMMIT;
     -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Address Details Updated Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_pr_faculty_personal_contact_profile_info` (IN `p_faculty_id` INT, IN `p_login_id` INT, IN `p_official_mail_id` VARCHAR(100), IN `p_personal_mail_id` VARCHAR(100), IN `p_mobile_number` VARCHAR(15), IN `p_alt_mobile_number` VARCHAR(15), IN `p_whatsapp_mobile_number` VARCHAR(15))   BEGIN
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

    -- Update the specified columns in the svcet_tbl_faculty_personal_info table
    UPDATE svcet_tbl_faculty_personal_info
    SET 
        faculty_official_mail_id= p_official_mail_id,
        faculty_personal_mail_id= p_personal_mail_id,
        faculty_mobile_number = p_mobile_number,
        faculty_alternative_contact_number = p_alt_mobile_number,
        faculty_whatsapp_number = p_whatsapp_mobile_number
    WHERE 
        faculty_id = p_faculty_id;

    -- Insert activity log if update was successful
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_personal_info', 3);

 -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Contact Details Updated SUccesfully' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_pr_faculty_personal_official_profile_info` (IN `p_faculty_id` INT, IN `p_login_in_id` INT, IN `p_faculty_designation` INT, IN `p_faculty_dept` INT, IN `p_faculty_salary` DECIMAL(10,2), IN `p_faculty_joining_date` VARCHAR(20))   BEGIN
    DECLARE v_current_timestamp TIMESTAMP;

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
    
    SET v_current_timestamp = CURRENT_TIMESTAMP;
    -- Step 1: Check if an active record with the same details already exists
    IF NOT EXISTS (
        SELECT 1 
        FROM svcet_tbl_faculty_official_details
        WHERE 
            faculty_id = p_faculty_id
            AND faculty_official_details_status = 1 -- Active record
            AND dept_id = p_faculty_dept
            AND designation = p_faculty_designation
            AND faculty_salary = p_faculty_salary
            AND faculty_joining_date = p_faculty_joining_date
    ) THEN
        -- Step 2: Update existing active record if it exists
        UPDATE svcet_tbl_faculty_official_details
        SET 
            effective_to = v_current_timestamp,
            faculty_official_details_status = 3 -- Mark as Completed
        WHERE 
            faculty_id = p_faculty_id 
            AND faculty_official_details_status = 1; -- Only update active records

        CALL insert_user_activity_log(p_login_in_id, 'svcet_tbl_faculty_official_details', 3);
        -- Step 3: Insert a new record with the updated details
        INSERT INTO svcet_tbl_faculty_official_details (
            faculty_id,
            dept_id,
            effective_from,
            designation,
            faculty_salary,
            faculty_joining_date,
            faculty_official_details_status
        ) VALUES (
            p_faculty_id,
            p_faculty_dept,
            v_current_timestamp,
            p_faculty_designation,
            p_faculty_salary,
            p_faculty_joining_date,
            1 -- Set status to Active
        );

        -- Step 4: Log user activity
        CALL insert_user_activity_log(p_login_in_id, 'svcet_tbl_faculty_official_details', 2); -- 2 for Insert
        
    END IF;
        -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Official Details Updated Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_pr_faculty_personal_profile_info` (IN `p_faculty_id` INT, IN `p_login_id` INT, IN `p_first_name` VARCHAR(100), IN `p_middle_name` VARCHAR(100), IN `p_last_name` VARCHAR(100), IN `p_initial` VARCHAR(10), IN `p_salutation` INT, IN `p_date_of_birth` DATE, IN `p_gender` INT, IN `p_blood_group` INT, IN `p_aadhar_number` VARCHAR(15), IN `p_religion` INT, IN `p_caste` INT, IN `p_community` INT, IN `p_nationality` INT, IN `p_marital_status` INT)   BEGIN
    -- Error and warning handling variables
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message TEXT DEFAULT '';

    -- Error handler block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_code = RETURNED_SQLSTATE, error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Start a transaction
    START TRANSACTION;

    -- Update faculty personal info
    UPDATE svcet_tbl_faculty_personal_info
    SET 
        faculty_first_name = p_first_name,
        faculty_middle_name = p_middle_name,
        faculty_last_name = p_last_name,
        faculty_initial = p_initial,
        faculty_salutation = p_salutation,
        faculty_dob = p_date_of_birth,
        faculty_gender = p_gender,
        faculty_blood_group = p_blood_group,
        faculty_aadhar_number = p_aadhar_number,
        faculty_religion = p_religion,
        faculty_caste = p_caste,
        faculty_community = p_community,
        faculty_nationality = p_nationality,
        faculty_marital_status = p_marital_status
    WHERE faculty_id = p_faculty_id;

    -- Log activity
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_personal_info', 3);

    -- Commit transaction and check for warnings
    COMMIT;

    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, CONCAT(warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Personal Details Updated Successfully!' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_pr_faculty_skill_profile_info` (IN `p_faculty_id` INT, IN `p_login_id` INT, IN `p_skills` JSON, IN `p_software_skills` JSON, IN `p_interest` JSON, IN `p_languages` JSON)   BEGIN
    DECLARE v_skill_name VARCHAR(255);
    DECLARE v_count INT DEFAULT 0;
    DECLARE v_total INT DEFAULT 0;
    DECLARE v_existing_count INT DEFAULT 0;
    DECLARE v_skill_type INT DEFAULT 1;
    DECLARE skill_types JSON DEFAULT '[1, 3, 2, 4]';
    DECLARE skill_index INT DEFAULT 0;

    -- Declare variables to capture error and warning messages
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = RETURNED_SQLSTATE, 
            error_message = MESSAGE_TEXT;

        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Process each skill type
    WHILE skill_index < JSON_LENGTH(skill_types) DO
        SET v_skill_type = JSON_UNQUOTE(JSON_EXTRACT(skill_types, CONCAT('$[', skill_index, ']')));
        SET v_count = 0;

        CASE v_skill_type
            WHEN 1 THEN SET v_total = JSON_LENGTH(p_skills);
            WHEN 3 THEN SET v_total = JSON_LENGTH(p_software_skills);
            WHEN 2 THEN SET v_total = JSON_LENGTH(p_interest);
            WHEN 4 THEN SET v_total = JSON_LENGTH(p_languages);
        END CASE;

        -- Mark all existing records of the skill type as deleted
        UPDATE svcet_tbl_faculty_skills 
        SET faculty_skill_deleted = 1 
        WHERE faculty_skill_faculty_id = p_faculty_id 
          AND faculty_skill_type = v_skill_type;

        -- Process the new skills
        WHILE v_count < v_total DO
            CASE v_skill_type
                WHEN 1 THEN SET v_skill_name = JSON_UNQUOTE(JSON_EXTRACT(p_skills, CONCAT('$[', v_count, ']')));
                WHEN 3 THEN SET v_skill_name = JSON_UNQUOTE(JSON_EXTRACT(p_software_skills, CONCAT('$[', v_count, ']')));
                WHEN 2 THEN SET v_skill_name = JSON_UNQUOTE(JSON_EXTRACT(p_interest, CONCAT('$[', v_count, ']')));
                WHEN 4 THEN SET v_skill_name = JSON_UNQUOTE(JSON_EXTRACT(p_languages, CONCAT('$[', v_count, ']')));
            END CASE;

            SELECT COUNT(*) INTO v_existing_count
            FROM svcet_tbl_faculty_skills
            WHERE faculty_skill_faculty_id = p_faculty_id
              AND faculty_skill_type = v_skill_type
              AND faculty_skill_name = v_skill_name;

            IF v_existing_count > 0 THEN
                UPDATE svcet_tbl_faculty_skills 
                SET faculty_skill_deleted = 0 
                WHERE faculty_skill_faculty_id = p_faculty_id
                  AND faculty_skill_type = v_skill_type
                  AND faculty_skill_name = v_skill_name;
            ELSE
                INSERT INTO svcet_tbl_faculty_skills (faculty_skill_faculty_id, faculty_skill_type, faculty_skill_name, faculty_skill_status, faculty_skill_deleted)
                VALUES (p_faculty_id, v_skill_type, v_skill_name, 1, 0);
            END IF;

            SET v_count = v_count + 1;
        END WHILE;

        SET skill_index = skill_index + 1;
    END WHILE;

    -- Log user activity
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_skills', 2);

    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Faculty skills updated successfully!' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_pr_faculty_status` (IN `p_faculty_id` INT, IN `p_login_id` INT, IN `p_faculty_status` TINYINT)   BEGIN
    DECLARE v_status_message VARCHAR(255);
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
    -- Set the status value for active/inactive
    SET @status_value = CASE WHEN p_faculty_status = 1 THEN 1 ELSE 2 END;

    -- Update faculty personal info status
    UPDATE svcet_tbl_faculty_personal_info
    SET faculty_status = @status_value
    WHERE faculty_id = p_faculty_id;

    -- Update account status
    UPDATE svcet_tbl_accounts
    SET account_status = @status_value
    WHERE account_id = (SELECT faculty_account_id FROM svcet_tbl_faculty_personal_info WHERE faculty_id = p_faculty_id);

    -- Log the user activity
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_personal_info, svcet_tbl_accounts', 3);



    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Status Changed Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_pr_student_dept_committee_roles` (IN `p_login_id` INT, IN `p_dept_id` INT, IN `committee_id_json` JSON, IN `student_id_json` JSON, IN `committee_roles_json` JSON, IN `p_type` INT, IN `p_r_r_id` INT)   BEGIN
    DECLARE p_current_date DATE DEFAULT CURDATE();
    DECLARE i INT DEFAULT 0;
    DECLARE committee_length INT;
    DECLARE v_committee_id INT;
    DECLARE v_student_id INT;
    DECLARE v_committee_role INT;

    -- Declare variables for error and warning handling
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message TEXT DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_code = RETURNED_SQLSTATE, error_message = MESSAGE_TEXT;
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

    -- Main logic
    IF p_type = 1 THEN
        UPDATE svcet_tbl_student_committee
        SET effective_to = p_current_date,
            committee_status = 3
        WHERE student_committee_id = p_r_r_id;

        CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_committee', 3);

    ELSE
        -- Extract JSON arrays into individual components
        SET committee_length = JSON_LENGTH(committee_id_json);

        -- Loop through each item in JSON arrays
        committee_loop: WHILE i < committee_length DO
            -- Extract current array elements and cast to integers
            SET v_committee_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(committee_id_json, CONCAT('$[', i, ']'))) AS UNSIGNED);
            SET v_student_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(student_id_json, CONCAT('$[', i, ']'))) AS UNSIGNED);
            SET v_committee_role = CAST(JSON_UNQUOTE(JSON_EXTRACT(committee_roles_json, CONCAT('$[', i, ']'))) AS UNSIGNED);

            -- Skip iteration if student_id is 0
            IF v_student_id = 0 THEN
                SET i = i + 1;
                ITERATE committee_loop; -- Skip to the next iteration
            END IF;

            -- Check if there is an existing active record for the student in this committee
            IF EXISTS (
                SELECT 1
                FROM svcet_tbl_student_committee
                WHERE student_id = v_student_id
                  AND committee_title = v_committee_id
                  AND dept_id = p_dept_id
                  AND committee_status = 1
                  AND committee_deleted = 0
            ) THEN
                -- Check if the existing record has the same role
                IF NOT EXISTS (
                    SELECT 1
                    FROM svcet_tbl_student_committee
                    WHERE student_id = v_student_id
                      AND committee_title = v_committee_id
                      AND committee_role = v_committee_role
                      AND dept_id = p_dept_id
                      AND committee_status = 1
                      AND committee_deleted = 0
                ) THEN
                    -- Update existing record's effective_to with the current date
                    UPDATE svcet_tbl_student_committee
                    SET effective_to = p_current_date,
                        committee_status = 0
                    WHERE student_id = v_student_id
                      AND committee_title = v_committee_id
                      AND dept_id = p_dept_id
                      AND committee_status = 1
                      AND committee_deleted = 0;

                    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_committee', 3);

                    -- Insert the new role/committee record
                    INSERT INTO svcet_tbl_student_committee (
                        student_id,
                        dept_id,
                        committee_title,
                        committee_role,
                        effective_from,
                        effective_to,
                        committee_status,
                        committee_deleted
                    )
                    VALUES (
                        v_student_id,
                        p_dept_id,
                        v_committee_id,
                        v_committee_role,
                        p_current_date,
                        NULL, -- New effective_to is set to NULL
                        1, -- Active status
                        0  -- Not deleted
                    );

                    -- Insert user activity log
                    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_committee', 2);
                END IF;

            ELSE
                -- No existing active record, insert the new role/committee record
                INSERT INTO svcet_tbl_student_committee (
                    student_id,
                    dept_id,
                    committee_title,
                    committee_role,
                    effective_from,
                    effective_to,
                    committee_status,
                    committee_deleted
                )
                VALUES (
                    v_student_id,
                    p_dept_id,
                    v_committee_id,
                    v_committee_role,
                    p_current_date,
                    NULL, -- New effective_to is set to NULL
                    1, -- Active status
                    0  -- Not deleted
                );

                -- Insert user activity log
                CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_committee', 2);
            END IF;

            -- Increment the index after processing the current item
            SET i = i + 1;
        END WHILE committee_loop;
    END IF;

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Updated Student Committees and Roles Successfully!' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_pr_student_document_profile_info` (IN `p_student_id` INT, IN `p_login_id` INT, IN `p_student_sslc_id` INT, IN `p_student_hsc_id` INT, IN `p_student_highest_qualification_id` INT, IN `p_student_transfer_certificate_id` INT, IN `p_student_permanent_integrated_certificate_id` INT, IN `p_student_community_certificate_id` INT, IN `p_student_residence_certificate_id` INT, IN `p_student_profile_pic_id` INT, IN `p_sslc` TEXT, IN `p_hsc` TEXT, IN `p_highest_qualification` TEXT, IN `p_transfer_certificate` TEXT, IN `p_permanent_integrated_certificate` TEXT, IN `p_community_certificate` TEXT, IN `p_residence_certificate` TEXT, IN `p_profile_pic` TEXT)   BEGIN
    -- Declare variables for error and warning handling
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 error_code = RETURNED_SQLSTATE, error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Upsert for SSLC Document
    IF p_student_sslc_id = 0 THEN
        INSERT INTO svcet_tbl_student_documents (student_doc_student_id, student_doc_type, student_doc_path, student_doc_status, student_doc_deleted)
        VALUES (p_student_id, 1, p_sslc, 1, 0);
    ELSE
        UPDATE svcet_tbl_student_documents
        SET student_doc_path = p_sslc, student_doc_status = 1, student_doc_deleted = 0
        WHERE student_doc_id = p_student_sslc_id;
    END IF;

    -- Upsert for HSC Document
    IF p_student_hsc_id = 0 THEN
        INSERT INTO svcet_tbl_student_documents (student_doc_student_id, student_doc_type, student_doc_path, student_doc_status, student_doc_deleted)
        VALUES (p_student_id, 2, p_hsc, 1, 0);
    ELSE
        UPDATE svcet_tbl_student_documents
        SET student_doc_path = p_hsc, student_doc_status = 1, student_doc_deleted = 0
        WHERE student_doc_id = p_student_hsc_id;
    END IF;

    -- Upsert for Highest Qualification Document
    IF p_student_highest_qualification_id = 0 THEN
        INSERT INTO svcet_tbl_student_documents (student_doc_student_id, student_doc_type, student_doc_path, student_doc_status, student_doc_deleted)
        VALUES (p_student_id, 3, p_highest_qualification, 1, 0);
    ELSE
        UPDATE svcet_tbl_student_documents
        SET student_doc_path = p_highest_qualification, student_doc_status = 1, student_doc_deleted = 0
        WHERE student_doc_id = p_student_highest_qualification_id;
    END IF;

    -- Upsert for Transfer Certificate Document
    IF p_student_transfer_certificate_id = 0 THEN
        INSERT INTO svcet_tbl_student_documents (student_doc_student_id, student_doc_type, student_doc_path, student_doc_status, student_doc_deleted)
        VALUES (p_student_id, 4, p_transfer_certificate, 1, 0);
    ELSE
        UPDATE svcet_tbl_student_documents
        SET student_doc_path = p_transfer_certificate, student_doc_status = 1, student_doc_deleted = 0
        WHERE student_doc_id = p_student_transfer_certificate_id;
    END IF;

    -- Upsert for Permanent Integrated Certificate Document
    IF p_student_permanent_integrated_certificate_id = 0 THEN
        INSERT INTO svcet_tbl_student_documents (student_doc_student_id, student_doc_type, student_doc_path, student_doc_status, student_doc_deleted)
        VALUES (p_student_id, 5, p_permanent_integrated_certificate, 1, 0);
    ELSE
        UPDATE svcet_tbl_student_documents
        SET student_doc_path = p_permanent_integrated_certificate, student_doc_status = 1, student_doc_deleted = 0
        WHERE student_doc_id = p_student_permanent_integrated_certificate_id;
    END IF;

    -- Upsert for Community Certificate Document
    IF p_student_community_certificate_id = 0 THEN
        INSERT INTO svcet_tbl_student_documents (student_doc_student_id, student_doc_type, student_doc_path, student_doc_status, student_doc_deleted)
        VALUES (p_student_id, 6, p_community_certificate, 1, 0);
    ELSE
        UPDATE svcet_tbl_student_documents
        SET student_doc_path = p_community_certificate, student_doc_status = 1, student_doc_deleted = 0
        WHERE student_doc_id = p_student_community_certificate_id;
    END IF;

    -- Upsert for Residence Certificate Document
    IF p_student_residence_certificate_id = 0 THEN
        INSERT INTO svcet_tbl_student_documents (student_doc_student_id, student_doc_type, student_doc_path, student_doc_status, student_doc_deleted)
        VALUES (p_student_id, 7, p_residence_certificate, 1, 0);
    ELSE
        UPDATE svcet_tbl_student_documents
        SET student_doc_path = p_residence_certificate, student_doc_status = 1, student_doc_deleted = 0
        WHERE student_doc_id = p_student_residence_certificate_id;
    END IF;

    -- Upsert for Profile Picture Document
    IF p_student_profile_pic_id = 0 THEN
        INSERT INTO svcet_tbl_student_documents (student_doc_student_id, student_doc_type, student_doc_path, student_doc_status, student_doc_deleted)
        VALUES (p_student_id, 8, p_profile_pic, 1, 0);
    ELSE
        UPDATE svcet_tbl_student_documents
        SET student_doc_path = p_profile_pic, student_doc_status = 1, student_doc_deleted = 0
        WHERE student_doc_id = p_student_profile_pic_id;
    END IF;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_documents', 2);

    -- Handle warnings and success messages
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Student Documents updated successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_pr_student_representative_form` (IN `p_login_id` INT, IN `p_student_id` JSON, IN `p_student_representative_id` JSON, IN `p_rep_year_of_study_id` JSON, IN `p_rep_dept_id` JSON, IN `p_rep_section_id` JSON)   BEGIN
    -- Declare variables to capture error and warning messages
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Declare variables for iteration
    DECLARE student_id_val INT;
    DECLARE rep_id_val INT;
    DECLARE year_id_val INT;
    DECLARE dept_id_val INT;
    DECLARE section_id_val INT;

    DECLARE i INT DEFAULT 0;
    DECLARE json_length INT;

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
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Calculate JSON array length
    SET json_length = JSON_LENGTH(p_student_id);

    -- Loop through each value in the JSON arrays
    WHILE i < json_length DO
        -- Extract values from JSON arrays
        SET student_id_val = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_student_id, CONCAT('$[', i, ']'))) AS UNSIGNED);
        SET rep_id_val = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_student_representative_id, CONCAT('$[', i, ']'))) AS UNSIGNED);
        SET year_id_val = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_rep_year_of_study_id, CONCAT('$[', i, ']'))) AS UNSIGNED);
        SET dept_id_val = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_rep_dept_id, CONCAT('$[', i, ']'))) AS UNSIGNED);
        SET section_id_val = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_rep_section_id, CONCAT('$[', i, ']'))) AS UNSIGNED);

        -- Logic to handle `student_representative_id`
        IF rep_id_val = 0 THEN
          IF student_id_val != 0 THEN
            -- Insert a new record
            INSERT INTO `svcet_tbl_student_representative` (
                student_id,
                dept_id,
                year_of_study_id,
                section_id,
                effective_from,
                student_representative_status,
                student_representative_deleted
            )
            VALUES (
                student_id_val,
                dept_id_val,
                year_id_val,
                section_id_val,
                CURRENT_TIMESTAMP,
                1, -- Active
                0  -- Not Deleted
            );
         END IF;
            -- Insert activity log entry
            CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_representative', 1);
        ELSE
            SELECT student_id_val AS 'asdasd';
            -- Check for an existing record with the same parameters
            IF NOT EXISTS (
                SELECT 1
                FROM `svcet_tbl_student_representative`
                WHERE student_id = student_id_val
                  AND dept_id = dept_id_val
                  AND year_of_study_id = year_id_val
                  AND section_id = section_id_val
                  AND student_representative_status = 1 -- Active
                  AND student_representative_deleted = 0 -- Not Deleted
            ) THEN
                -- Update the existing record with status = 3 (Completed) and set effective_to
                UPDATE `svcet_tbl_student_representative`
                SET student_representative_status = 3, -- Completed
                    effective_to = CURRENT_TIMESTAMP
                WHERE student_representative_id = rep_id_val;

                -- Insert activity log entry
                CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_representative', 3);

                -- Insert a new record with the current values
                INSERT INTO `svcet_tbl_student_representative` (
                    student_id,
                    dept_id,
                    year_of_study_id,
                    section_id,
                    effective_from,
                    student_representative_status,
                    student_representative_deleted
                )
                VALUES (
                    student_id_val,
                    dept_id_val,
                    year_id_val,
                    section_id_val,
                    CURRENT_TIMESTAMP,
                    1, -- Active
                    0  -- Not Deleted
                );

                -- Insert activity log entry
                CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_representative', 1);
            END IF;
        END IF;

        -- Increment loop counter
        SET i = i + 1;
    END WHILE;

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Student Representative Has Been Updated Successfully!' AS message;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_stu_admission_education_schoolings_profile_info` (IN `p_sslc_institution_name` VARCHAR(255), IN `p_education_board` INT, IN `p_sslc_passed_out_year` YEAR, IN `p_sslc_percentage` DECIMAL(5,2), IN `p_hsc_institution_name` VARCHAR(255), IN `p_education_hsc_board` INT, IN `p_education_hsc_specialization` INT, IN `p_hsc_passed_out_year` YEAR, IN `p_hsc_percentage` DECIMAL(5,2), IN `p_sslc_mark` INT, IN `p_hsc_mark` INT, IN `p_student_id` INT, IN `p_login_id` INT)   BEGIN
    -- Declare variables for error and warning handling
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Capture error code and message
        GET DIAGNOSTICS CONDITION 1
            error_code = RETURNED_SQLSTATE, 
            error_message = MESSAGE_TEXT;

        -- Return error details
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
        ROLLBACK;
    END;

    -- Warning handler block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        -- Increment warning count
        SET warning_count = warning_count + 1;

        -- Capture warning message
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Check if SSLC record exists, then update or insert
    IF EXISTS (
        SELECT 1 
        FROM svcet_tbl_student_education 
        WHERE student_edu_student_id = p_student_id AND student_edu_level = 1
    ) THEN
        -- Update SSLC record
        UPDATE svcet_tbl_student_education
        SET
            student_edu_institution_name = p_sslc_institution_name,
            student_edu_board = p_education_board,
            student_edu_passed_out_year = p_sslc_passed_out_year,
            student_edu_percentage = p_sslc_percentage,
            student_edu_total_mark = p_sslc_mark
        WHERE 
            student_edu_student_id = p_student_id AND student_edu_level = 1;

        -- Log the update action
        CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_education', 3);
        
    ELSE
        -- Insert SSLC record
        INSERT INTO svcet_tbl_student_education (
            student_edu_student_id,
            student_edu_level,
            student_edu_board,
            student_edu_institution_name,
            student_edu_passed_out_year,
            student_edu_percentage,
            student_edu_total_mark
        ) VALUES (
            p_student_id,
            1, -- SSLC level
            p_education_board,
            p_sslc_institution_name,
            p_sslc_passed_out_year,
            p_sslc_percentage,
            p_sslc_mark
        );

        -- Log the insert action
        CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_education', 2);
    END IF;

    -- Check if HSC record exists, then update or insert
    IF EXISTS (
        SELECT 1 
        FROM svcet_tbl_student_education 
        WHERE student_edu_student_id = p_student_id AND student_edu_level = 2
    ) THEN
        -- Update HSC record
        UPDATE svcet_tbl_student_education
        SET
            student_edu_institution_name = p_hsc_institution_name,
            student_edu_board = p_education_hsc_board,
            student_edu_specialization = p_education_hsc_specialization,
            student_edu_passed_out_year = p_hsc_passed_out_year,
            student_edu_percentage = p_hsc_percentage,
            student_edu_total_mark = p_hsc_mark
        WHERE 
            student_edu_student_id = p_student_id AND student_edu_level = 2;

        -- Log the update action
        CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_education', 3);
        
    ELSE
        -- Insert HSC record
        INSERT INTO svcet_tbl_student_education (
            student_edu_student_id,
            student_edu_level,
            student_edu_board,
            student_edu_institution_name,
            student_edu_specialization,
            student_edu_passed_out_year,
            student_edu_percentage,
            student_edu_total_mark
        ) VALUES (
            p_student_id,
            2, -- HSC level
            p_education_hsc_board,
            p_hsc_institution_name,
            p_education_hsc_specialization,
            p_hsc_passed_out_year,
            p_hsc_percentage,
            p_hsc_mark
        );

        -- Log the insert action
        CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_education', 2);
    END IF;

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Education Information Updated Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_stu_admission_faculty_education_degree_profile_info` (IN `p_student_id` INT, IN `p_login_id` INT, IN `p_degree_institution_name` JSON, IN `p_education_degree` JSON, IN `p_education_degree_specialization` JSON, IN `p_degree_passed_out_year` JSON, IN `p_degree_percentage` JSON, IN `p_degree_cgpa` JSON, IN `p_degree_id` JSON)   BEGIN
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';
    DECLARE degree_count INT;
    DECLARE degree_index INT DEFAULT 0;

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

    -- Determine the number of elements in the JSON array
    SET degree_count = JSON_LENGTH(p_degree_id);

    -- Loop through each degree object in the JSON array
    WHILE degree_index < degree_count DO
        -- Extract values from the current degree JSON object
        SET @degree_institution_name = JSON_UNQUOTE(JSON_EXTRACT(p_degree_institution_name, CONCAT('$[', degree_index, ']')));
        SET @education_degree = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_education_degree, CONCAT('$[', degree_index, ']'))) AS UNSIGNED);
        SET @education_degree_specialization = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_education_degree_specialization, CONCAT('$[', degree_index, ']'))) AS UNSIGNED);
        SET @degree_passed_out_year = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_degree_passed_out_year, CONCAT('$[', degree_index, ']'))) AS UNSIGNED);
        SET @degree_percentage = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_degree_percentage, CONCAT('$[', degree_index, ']'))) AS DECIMAL(5,2));
        SET @degree_cgpa = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_degree_cgpa, CONCAT('$[', degree_index, ']'))) AS DECIMAL(5,3));
        SET @degree_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_degree_id, CONCAT('$[', degree_index, ']'))) AS UNSIGNED);

        -- Check if the record exists
        IF EXISTS (
            SELECT 1 
            FROM svcet_tbl_student_education 
            WHERE student_edu_id = @degree_id AND student_edu_student_id = p_student_id
        ) THEN
            -- Update if the record exists
            UPDATE svcet_tbl_student_education
            SET
                student_edu_institution_name = @degree_institution_name,
                student_edu_degree = @education_degree,
                student_edu_specialization = @education_degree_specialization,
                student_edu_passed_out_year = @degree_passed_out_year,
                student_edu_percentage = @degree_percentage,
                student_edu_cgpa = @degree_cgpa
            WHERE 
                student_edu_id = @degree_id AND student_edu_student_id = p_student_id;

            -- Log the update action
            CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_education', 3);
            
        ELSE
            -- Insert a new record if it does not exist
            INSERT INTO svcet_tbl_student_education (
                student_edu_student_id,
                student_edu_level,
                student_edu_institution_name,
                student_edu_degree,
                student_edu_specialization,
                student_edu_passed_out_year,
                student_edu_percentage,
                student_edu_cgpa
            ) VALUES (
                p_student_id,
                4, -- Degree level
                @degree_institution_name,
                @education_degree,
                @education_degree_specialization,
                @degree_passed_out_year,
                @degree_percentage,
                @degree_cgpa
            );

            -- Log the insert action
            CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_education', 2);
        END IF;

        -- Increment the index for the next iteration
        SET degree_index = degree_index + 1;
    END WHILE;

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Degree Education Information Updated Successfully!' AS message;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_update_student_admission_info` (IN `p_student_admission_student_id` INT, IN `p_student_admission_type` TINYINT, IN `p_student_admission_category` TINYINT, IN `p_student_hostel` TINYINT, IN `p_student_admission_know_about_us` INT, IN `p_student_transport` TINYINT, IN `p_student_reference` INT, IN `p_student_admission_reg_no` VARCHAR(50), IN `p_student_course_preference1` INT, IN `p_student_course_preference2` INT, IN `p_student_course_preference3` INT, IN `p_student_concession_subject` INT, IN `p_student_concession_body` TEXT, IN `p_admission_status` TINYINT, IN `p_admission_deleted` TINYINT, IN `p_lateral_entry_year_of_study` INT, IN `p_login_id` INT)   BEGIN
    -- Declare variables to capture error and warning messages
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';
    DECLARE v_existing_id INT;

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

    -- Declare a variable for existing student admission ID
    

    -- Check if student_admission_student_id already exists
    SELECT admission_id INTO v_existing_id
    FROM svcet_tbl_student_admission_info
    WHERE student_admission_student_id = p_student_admission_student_id;

    IF v_existing_id IS NOT NULL THEN
        -- If exists, update the record
        UPDATE svcet_tbl_student_admission_info
        SET
            student_admission_type = p_student_admission_type,
            student_admission_category = p_student_admission_category,
            student_hostel = p_student_hostel,
            student_admission_know_about_us = p_student_admission_know_about_us,
            student_transport = p_student_transport,
            student_reference = p_student_reference,
            student_admission_reg_no = p_student_admission_reg_no,
            student_course_preference1 = p_student_course_preference1,
            student_course_preference2 = p_student_course_preference2,
            student_course_preference3 = p_student_course_preference3,
            student_concession_subject = p_student_concession_subject,
            student_concession_body = p_student_concession_body,
            admission_deleted = p_admission_deleted,
            lateral_entry_year_of_study = p_lateral_entry_year_of_study
        WHERE student_admission_student_id = p_student_admission_student_id;
    ELSE
        -- If not exists, insert a new record
        INSERT INTO svcet_tbl_student_admission_info (
            student_admission_student_id,
            student_admission_type,
            student_admission_category,
            student_hostel,
            student_admission_know_about_us,
            student_transport,
            student_reference,
            student_admission_reg_no,
            student_course_preference1,
            student_course_preference2,
            student_course_preference3,
            student_concession_subject,
            student_concession_body,
            admission_deleted,
            lateral_entry_year_of_study
        ) VALUES (
            p_student_admission_student_id,
            p_student_admission_type,
            p_student_admission_category,
            p_student_hostel,
            p_student_admission_know_about_us,
            p_student_transport,
            p_student_reference,
            p_student_admission_reg_no,
            p_student_course_preference1,
            p_student_course_preference2,
            p_student_course_preference3,
            p_student_concession_subject,
            p_student_concession_body,
            
            p_admission_deleted,
            p_lateral_entry_year_of_study
        );
    END IF;

    -- Log the activity
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_admission_info', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 200 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Student Admission Information processed successfully!' AS message;
    END IF;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_accounts`
--

CREATE TABLE `svcet_tbl_accounts` (
  `account_id` int(11) NOT NULL,
  `account_username` varchar(50) NOT NULL,
  `account_password` text DEFAULT 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj',
  `account_portal_type` tinyint(1) NOT NULL COMMENT '1 - faculty, 2 - student, 3 - parent, 4 - developer',
  `account_code` int(11) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `account_status` tinyint(1) DEFAULT 1 COMMENT '1 - Active, 2 - Inactive',
  `deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `svcet_tbl_accounts`
--

INSERT INTO `svcet_tbl_accounts` (`account_id`, `account_username`, `account_password`, `account_portal_type`, `account_code`, `role_id`, `account_status`, `deleted`) VALUES
(1, 'SVCET-FAC-1', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 1, 8, 1, 0),
(2, 'SVCET-FAC-2', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 2, 8, 1, 0),
(3, 'SVCET-FAC-3', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 3, 8, 1, 0),
(4, 'SVCET-FAC-4', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 4, 8, 1, 0),
(5, 'SVCET-FAC-5', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 5, 8, 1, 0),
(6, 'SVCET-FAC-6', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 6, 8, 1, 0),
(7, 'SVCET-FAC-7', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 7, 8, 1, 0),
(8, 'SVCET-FAC-8', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 8, 8, 1, 0),
(9, 'SVCET-FAC-9', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 9, 8, 1, 0),
(10, 'SVCET-FAC-10', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 10, 8, 1, 0),
(11, 'SVCET-FAC-11', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 11, 8, 1, 0),
(12, 'SVCET-FAC-12', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 12, 8, 1, 0),
(13, 'SVCET-FAC-13', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 13, 8, 1, 0),
(14, 'SVCET-FAC-14', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 14, 8, 1, 0),
(15, 'SVCET-FAC-15', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 15, 8, 1, 0),
(16, 'SVCET-FAC-16', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 16, 8, 1, 0),
(17, 'SVCET-FAC-17', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 17, 8, 1, 0),
(18, 'SVCET-FAC-18', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 18, 8, 1, 0),
(19, 'SVCET-FAC-19', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 19, 8, 1, 0),
(20, 'SVCET-FAC-20', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 20, 8, 1, 0),
(21, 'SVCET-FAC-21', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 21, 8, 1, 0),
(22, 'SVCET-FAC-22', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 22, 8, 1, 0),
(23, 'SVCET-FAC-23', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 23, 8, 1, 0),
(24, 'SVCET-FAC-24', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 24, 8, 1, 0),
(25, 'SVCET-FAC-25', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 25, 8, 1, 0),
(26, 'SVCET-FAC-26', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 26, 8, 1, 0),
(27, 'SVCET-FAC-27', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 27, 8, 1, 0),
(28, 'SVCET-FAC-28', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 28, 8, 1, 0),
(29, 'SVCET-FAC-29', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 29, 8, 1, 0),
(30, 'SVCET-FAC-30', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 30, 8, 1, 0),
(31, 'SVCET-FAC-31', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 31, 8, 1, 0),
(32, 'SVCET-FAC-32', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 32, 8, 1, 0),
(33, 'SVCET-FAC-33', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 33, 8, 1, 0),
(34, 'SVCET-FAC-34', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 34, 8, 1, 0),
(35, 'SVCET-FAC-35', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 35, 8, 1, 0),
(36, 'SVCET-FAC-36', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 36, 8, 1, 0),
(37, 'SVCET-FAC-37', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 37, 8, 1, 0),
(38, 'SVCET-FAC-38', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 38, 8, 1, 0),
(39, 'SVCET-FAC-39', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 39, 8, 1, 0),
(40, 'SVCET-FAC-40', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 40, 8, 1, 0),
(41, 'SVCET-FAC-41', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 41, 8, 1, 0),
(42, 'SVCET-FAC-42', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 42, 8, 1, 0),
(43, 'SVCET-FAC-43', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 43, 8, 1, 0),
(44, 'SVCET-FAC-44', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 44, 8, 1, 0),
(45, 'SVCET-FAC-45', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 45, 8, 1, 0),
(46, 'SVCET-FAC-46', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 46, 8, 1, 0),
(47, 'SVCET-FAC-47', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 47, 8, 1, 0),
(48, 'SVCET-FAC-48', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 48, 8, 1, 0),
(49, 'SVCET-FAC-49', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 49, 8, 1, 0),
(50, 'SVCET-FAC-50', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 50, 8, 1, 0),
(51, 'SVCET-FAC-51', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 51, 8, 1, 0),
(52, 'SVCET-FAC-52', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 52, 8, 1, 0),
(53, 'SVCET-FAC-53', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 53, 8, 1, 0),
(54, 'SVCET-FAC-54', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 54, 8, 1, 0),
(55, 'SVCET-FAC-55', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 55, 8, 1, 0),
(56, 'SVCET-FAC-56', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 56, 8, 1, 0),
(57, 'SVCET-FAC-57', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 57, 8, 1, 0),
(58, 'SVCET-FAC-58', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 58, 8, 1, 0),
(59, 'SVCET-FAC-59', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 59, 8, 1, 0),
(60, 'SVCET-FAC-60', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 60, 8, 1, 0),
(61, 'SVCET-FAC-61', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 61, 8, 1, 0),
(62, 'SVCET-FAC-62', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 62, 8, 1, 0),
(63, 'SVCET-PRINCIPAL', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 63, 3, 1, 0),
(64, 'SVCET-VICE-PRINCIPAL', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 64, 4, 1, 0),
(65, 'SVCET-DEAN-ACADEMICS', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 65, 5, 1, 0),
(66, 'SVCET-DEAN-IQAC', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 66, 12, 1, 0),
(67, 'SVCET-CSE-HOD', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 67, 6, 1, 0),
(68, 'SVCET-MECH-HOD', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 68, 6, 1, 0),
(69, 'SVCET-ECE-HOD', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 69, 6, 1, 0),
(70, 'SVCET-EEE-HOD', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 70, 6, 1, 0),
(71, 'SVCET-BME-HOD', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 71, 6, 1, 0),
(72, 'SVCET-S&H-HOD', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 72, 6, 1, 0),
(73, 'SVCET-MBA-HOD', 'K0U4dWtkWEptSUpWY21sK1Qrd25zdz09OjqvOpj22m7ka0Xgz1aULZyj', 1, 73, 6, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_academic_batch`
--

CREATE TABLE `svcet_tbl_dev_academic_batch` (
  `academic_batch_id` int(11) NOT NULL,
  `sem_duration_id` int(11) NOT NULL,
  `academic_batch_title` varchar(50) NOT NULL,
  `academic_batch_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `academic_batch_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `svcet_tbl_dev_academic_batch`
--

INSERT INTO `svcet_tbl_dev_academic_batch` (`academic_batch_id`, `sem_duration_id`, `academic_batch_title`, `academic_batch_status`, `academic_batch_deleted`) VALUES
(1, 1, '2020-2024', 1, 0),
(2, 2, '2021-2025', 1, 0),
(3, 3, '2022-2026', 1, 0),
(4, 4, '2023-2027', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_academic_year`
--

CREATE TABLE `svcet_tbl_dev_academic_year` (
  `academic_year_id` int(11) NOT NULL,
  `academic_year_title` varchar(50) NOT NULL,
  `academic_year_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `academic_year_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `svcet_tbl_dev_academic_year`
--

INSERT INTO `svcet_tbl_dev_academic_year` (`academic_year_id`, `academic_year_title`, `academic_year_status`, `academic_year_deleted`) VALUES
(1, '2023-2024', 1, 0),
(2, '2023-2024', 1, 0),
(3, '2023-2024', 1, 0),
(4, '2023-2024', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_attendance`
--

CREATE TABLE `svcet_tbl_dev_attendance` (
  `attendance_dev_id` int(11) NOT NULL,
  `max_acceptance_day` int(11) NOT NULL DEFAULT 2,
  `pass_attendance_percentage` decimal(5,2) NOT NULL DEFAULT 90.00,
  `warning_attendance_percentage` decimal(5,2) NOT NULL DEFAULT 95.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_day`
--

CREATE TABLE `svcet_tbl_dev_day` (
  `day_id` int(11) NOT NULL,
  `day_title` varchar(50) NOT NULL,
  `timetable_status` tinyint(1) NOT NULL COMMENT '1-show | 2-not show',
  `day_status` tinyint(1) NOT NULL DEFAULT 1,
  `day_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `svcet_tbl_dev_day`
--

INSERT INTO `svcet_tbl_dev_day` (`day_id`, `day_title`, `timetable_status`, `day_status`, `day_deleted`) VALUES
(1, 'Monday', 1, 1, 0),
(2, 'Tuesday', 1, 1, 0),
(3, 'Wednesday', 1, 1, 0),
(4, 'Thursday', 1, 1, 0),
(5, 'Friday', 1, 1, 0),
(6, 'Saturday', 0, 1, 0),
(7, 'Sunday', 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_dept`
--

CREATE TABLE `svcet_tbl_dev_dept` (
  `dept_id` int(11) NOT NULL,
  `dept_title` varchar(255) NOT NULL,
  `dept_short_name` varchar(50) NOT NULL,
  `dept_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `dept_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `svcet_tbl_dev_dept`
--

INSERT INTO `svcet_tbl_dev_dept` (`dept_id`, `dept_title`, `dept_short_name`, `dept_status`, `dept_deleted`) VALUES
(1, 'Computer Science and Engineering', 'CSE', 1, 0),
(2, 'Mechanical Engineering', 'MECH', 1, 0),
(3, 'Electrical and Electronics Engineering', 'EEE', 1, 0),
(4, 'Electronics and Communication Engineering', 'ECE', 1, 0),
(5, 'Bio Medical Engineering', 'BME', 1, 0),
(6, 'Science and Humanities', 'S&H', 1, 0),
(7, 'Master of Business Administration', 'MBA', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_exam_management`
--

CREATE TABLE `svcet_tbl_dev_exam_management` (
  `exam_id` int(11) NOT NULL,
  `exam_group_id` tinyint(1) NOT NULL COMMENT '1 - Theory | 2 - Practical | 3 - Project | 4 - University Theory | 5 - University Practical | 6 - University Project',
  `exam_title` varchar(255) NOT NULL,
  `exam_short_name` varchar(10) NOT NULL,
  `exam_max_marks` decimal(10,2) NOT NULL,
  `exam_min_marks` decimal(10,2) NOT NULL,
  `exam_duration` decimal(5,2) NOT NULL,
  `exam_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `exam_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_general`
--

CREATE TABLE `svcet_tbl_dev_general` (
  `general_id` int(11) NOT NULL,
  `general_group_id` int(11) NOT NULL COMMENT '1 - Gender | 2 - Blood Group | 3 - Nationality | 4 - Marital Status |  5 - Religion | 6 - Caste | 7 - Community | 8 - Mother Tongue |9 - Degrees | 10 - Levels | 11 - board | 12 - Specialization | 13 - Designation | 14 - Achievement Type | 15 - Material Type | 16 - Leave Type | 17 - Leave Reason | 18 - Event Type| 19 - Salutation | 20 - Faculty Committees | 21 - Student Committees',
  `general_title` varchar(255) NOT NULL,
  `general_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `general_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `svcet_tbl_dev_general`
--

INSERT INTO `svcet_tbl_dev_general` (`general_id`, `general_group_id`, `general_title`, `general_status`, `general_delete`) VALUES
(1, 1, 'Male', 1, 0),
(2, 1, 'Female', 1, 0),
(3, 1, 'Non-binary', 1, 0),
(4, 1, 'Genderqueer', 1, 0),
(5, 1, 'Genderfluid', 1, 0),
(6, 1, 'Agender', 1, 0),
(7, 1, 'Transgender Male', 1, 0),
(8, 1, 'Transgender Female', 1, 0),
(9, 1, 'Prefer not to say', 1, 0),
(10, 1, 'Other', 1, 0),
(11, 2, 'A+', 1, 0),
(12, 2, 'A-', 1, 0),
(13, 2, 'B+', 1, 0),
(14, 2, 'B-', 1, 0),
(15, 2, 'AB+', 1, 0),
(16, 2, 'AB-', 1, 0),
(17, 2, 'O+', 1, 0),
(18, 2, 'O-', 1, 0),
(19, 3, 'Indian', 1, 0),
(20, 3, 'American', 1, 0),
(21, 3, 'British', 1, 0),
(22, 3, 'Canadian', 1, 0),
(23, 3, 'Australian', 1, 0),
(24, 3, 'Chinese', 1, 0),
(25, 3, 'French', 1, 0),
(26, 3, 'German', 1, 0),
(27, 3, 'Japanese', 1, 0),
(28, 3, 'Russian', 1, 0),
(29, 3, 'Italian', 1, 0),
(30, 3, 'Brazilian', 1, 0),
(31, 3, 'South African', 1, 0),
(32, 3, 'Mexican', 1, 0),
(33, 3, 'Spanish', 1, 0),
(34, 3, 'Pakistani', 1, 0),
(35, 3, 'Bangladeshi', 1, 0),
(36, 3, 'Sri Lankan', 1, 0),
(37, 3, 'Nepali', 1, 0),
(38, 3, 'Malaysian', 1, 0),
(39, 4, 'Single', 1, 0),
(40, 4, 'Married', 1, 0),
(41, 4, 'Divorced', 1, 0),
(42, 4, 'Widowed', 1, 0),
(43, 4, 'Separated', 1, 0),
(44, 4, 'In a relationship', 1, 0),
(45, 4, 'Engaged', 1, 0),
(46, 4, 'Prefer not to say', 1, 0),
(47, 5, 'Hinduism', 1, 0),
(48, 5, 'Christianity', 1, 0),
(49, 5, 'Islam', 1, 0),
(50, 5, 'Buddhism', 1, 0),
(51, 5, 'Sikhism', 1, 0),
(52, 5, 'Jainism', 1, 0),
(53, 5, 'Judaism', 1, 0),
(54, 5, 'Zoroastrianism', 1, 0),
(55, 5, 'Atheism', 1, 0),
(56, 5, 'Agnosticism', 1, 0),
(57, 5, 'Other', 1, 0),
(58, 5, 'Prefer not to say', 1, 0),
(59, 6, 'General', 1, 0),
(60, 6, 'Scheduled Caste (SC)', 1, 0),
(61, 6, 'Scheduled Tribe (ST)', 1, 0),
(62, 6, 'Other Backward Class (OBC)', 1, 0),
(63, 6, 'Economically Weaker Section (EWS)', 1, 0),
(64, 6, 'Prefer not to say', 1, 0),
(65, 6, 'Other', 1, 0),
(66, 7, 'Nadar', 1, 0),
(67, 7, 'Vanniyar', 1, 0),
(68, 7, 'Gounder', 1, 0),
(69, 7, 'Chettiar', 1, 0),
(70, 7, 'Iyer', 1, 0),
(71, 7, 'Iyengar', 1, 0),
(72, 7, 'Mudaliar', 1, 0),
(73, 7, 'Maravar', 1, 0),
(74, 7, 'Thevar', 1, 0),
(75, 7, 'Reddy', 1, 0),
(76, 7, 'Naidu', 1, 0),
(77, 7, 'Yadav', 1, 0),
(78, 7, 'Ezhava', 1, 0),
(79, 7, 'Kuruba', 1, 0),
(80, 7, 'Lingayat', 1, 0),
(81, 7, 'Balija', 1, 0),
(82, 7, 'Kamma', 1, 0),
(83, 7, 'Kapu', 1, 0),
(84, 7, 'Vellalar', 1, 0),
(85, 7, 'Other', 1, 0),
(86, 7, 'Prefer not to say', 1, 0),
(87, 8, 'Tamil', 1, 0),
(88, 8, 'Telugu', 1, 0),
(89, 8, 'Kannada', 1, 0),
(90, 8, 'Malayalam', 1, 0),
(91, 8, 'Hindi', 1, 0),
(92, 8, 'Bengali', 1, 0),
(93, 8, 'Urdu', 1, 0),
(94, 8, 'Gujarati', 1, 0),
(95, 8, 'Marathi', 1, 0),
(96, 8, 'Punjabi', 1, 0),
(97, 8, 'Assamese', 1, 0),
(98, 8, 'Odia', 1, 0),
(99, 8, 'Sanskrit', 1, 0),
(100, 8, 'English', 1, 0),
(101, 8, 'Other', 1, 0),
(102, 8, 'Prefer not to say', 1, 0),
(103, 9, 'Diploma', 1, 0),
(104, 9, 'Bachelor of Arts (BA)', 1, 0),
(105, 9, 'Bachelor of Science (BSc)', 1, 0),
(106, 9, 'Bachelor of Commerce (BCom)', 1, 0),
(107, 9, 'Bachelor of Engineering (BE)', 1, 0),
(108, 9, 'Bachelor of Technology (BTech)', 1, 0),
(109, 9, 'Master of Arts (MA)', 1, 0),
(110, 9, 'Master of Science (MSc)', 1, 0),
(111, 9, 'Master of Commerce (MCom)', 1, 0),
(112, 9, 'Master of Engineering (ME)', 1, 0),
(113, 9, 'Master of Technology (MTech)', 1, 0),
(114, 9, 'Doctor of Philosophy (PhD)', 1, 0),
(115, 9, 'Post Graduate Diploma', 1, 0),
(116, 9, 'Other', 1, 0),
(118, 11, 'State Board', 1, 0),
(119, 11, 'Central Board of Secondary Education (CBSE)', 1, 0),
(120, 11, 'Council for the Indian School Certificate Examinations (CISCE)', 1, 0),
(121, 11, 'National Institute of Open Schooling (NIOS)', 1, 0),
(122, 11, 'International Baccalaureate (IB)', 1, 0),
(123, 11, 'Cambridge International Examinations (CIE)', 1, 0),
(124, 11, 'Matriculation Board', 1, 0),
(125, 11, 'Andhra Pradesh Board of Intermediate Education (APBIE)', 1, 0),
(126, 11, 'Tamil Nadu State Board', 1, 0),
(127, 11, 'Maharashtra State Board', 1, 0),
(128, 11, 'West Bengal Board of Secondary Education (WBBSE)', 1, 0),
(129, 11, 'Gujarat Secondary and Higher Secondary Education Board (GSHSEB)', 1, 0),
(130, 11, 'Other', 1, 0),
(132, 12, 'Computer Science and Engineering', 1, 0),
(133, 12, 'Information Technology', 1, 0),
(134, 12, 'Electrical Engineering', 1, 0),
(135, 12, 'Mechanical Engineering', 1, 0),
(136, 12, 'Civil Engineering', 1, 0),
(137, 12, 'Electronics and Communication Engineering', 1, 0),
(138, 12, 'Chemical Engineering', 1, 0),
(139, 12, 'Biotechnology', 1, 0),
(140, 12, 'Aerospace Engineering', 1, 0),
(141, 12, 'Environmental Engineering', 1, 0),
(142, 12, 'Fine Arts', 1, 0),
(143, 12, 'Graphic Design', 1, 0),
(144, 12, 'Literature', 1, 0),
(145, 12, 'History', 1, 0),
(146, 12, 'Sociology', 1, 0),
(147, 12, 'Psychology', 1, 0),
(148, 12, 'Physics', 1, 0),
(149, 12, 'Chemistry', 1, 0),
(150, 12, 'Biology', 1, 0),
(151, 12, 'Mathematics', 1, 0),
(152, 12, 'Environmental Science', 1, 0),
(153, 12, 'Accounting', 1, 0),
(154, 12, 'Business Administration', 1, 0),
(155, 12, 'Marketing', 1, 0),
(156, 12, 'Finance', 1, 0),
(157, 12, 'Human Resource Management', 1, 0),
(158, 12, 'Medicine', 1, 0),
(159, 12, 'Nursing', 1, 0),
(160, 12, 'Pharmacy', 1, 0),
(161, 12, 'Physiotherapy', 1, 0),
(162, 12, 'Dentistry', 1, 0),
(163, 12, 'Other', 1, 0),
(165, 13, 'Professor', 1, 0),
(166, 13, 'Associate Professor', 1, 0),
(167, 13, 'Assistant Professor', 1, 0),
(168, 13, 'Senior Lecturer', 1, 0),
(169, 13, 'Lecturer', 1, 0),
(170, 13, 'Head of Department (HOD)', 1, 0),
(171, 13, 'Dean', 1, 0),
(172, 13, 'Principal', 1, 0),
(173, 13, 'Research Scholar', 1, 0),
(174, 13, 'Adjunct Faculty', 1, 0),
(175, 13, 'Visiting Faculty', 1, 0),
(176, 13, 'Lab Instructor', 1, 0),
(177, 13, 'Teaching Assistant', 1, 0),
(178, 13, 'Academic Counselor', 1, 0),
(179, 13, 'Director of Studies', 1, 0),
(180, 13, 'Coordinator', 1, 0),
(181, 13, 'Registrar', 1, 0),
(182, 13, 'Administrative Staff', 1, 0),
(183, 13, 'Support Staff', 1, 0),
(184, 13, 'Other', 1, 0),
(208, 15, 'Lecture Notes', 1, 0),
(209, 15, 'Course Syllabus', 1, 0),
(210, 15, 'Presentation Slides', 1, 0),
(211, 15, 'Research Papers', 1, 0),
(212, 15, 'eBooks', 1, 0),
(213, 15, 'Laboratory Manuals', 1, 0),
(214, 15, 'Handouts', 1, 0),
(215, 15, 'PDF Documents', 1, 0),
(216, 15, 'Video Lectures', 1, 0),
(217, 15, 'Online Resources', 1, 0),
(218, 15, 'Case Studies', 1, 0),
(219, 15, 'Assignments', 1, 0),
(220, 15, 'Question Papers', 1, 0),
(221, 15, 'Project Reports', 1, 0),
(222, 15, 'Study Guides', 1, 0),
(223, 15, 'Reference Materials', 1, 0),
(224, 15, 'Educational Software', 1, 0),
(225, 15, 'Other', 1, 0),
(226, 16, 'On Duty', 1, 0),
(227, 16, 'Leave', 1, 0),
(228, 16, 'Other', 1, 0),
(229, 17, 'Medical Reasons', 1, 0),
(230, 17, 'Family Emergency', 1, 0),
(231, 17, 'Personal Reasons', 1, 0),
(232, 17, 'Academic Engagement', 1, 0),
(233, 17, 'Cultural Event Participation', 1, 0),
(234, 17, 'Extracurricular Activities', 1, 0),
(235, 17, 'Travel', 1, 0),
(236, 17, 'Religious Observance', 1, 0),
(237, 17, 'Mental Health Day', 1, 0),
(238, 17, 'Death in the Family', 1, 0),
(239, 17, 'Official College Events', 1, 0),
(240, 17, 'Sports Events', 1, 0),
(241, 17, 'Other', 1, 0),
(242, 18, 'Industrial Visit', 1, 0),
(243, 18, 'Cultural Fest', 1, 0),
(244, 18, 'Farewell Party', 1, 0),
(245, 18, 'Freshers Party', 1, 0),
(246, 18, 'Guest Lecture', 1, 0),
(247, 18, 'Workshops', 1, 0),
(248, 18, 'Technical Seminar', 1, 0),
(249, 18, 'Sports Day', 1, 0),
(250, 18, 'Alumni Meet', 1, 0),
(251, 18, 'Cultural Program', 1, 0),
(252, 18, 'Inter-College Competitions', 1, 0),
(253, 18, 'College Annual Day', 1, 0),
(254, 18, 'Field Trip', 1, 0),
(255, 18, 'Community Service', 1, 0),
(256, 18, 'Other', 1, 0),
(257, 19, 'Mr.', 1, 0),
(258, 19, 'Mrs.', 1, 0),
(259, 19, 'Ms.', 1, 0),
(260, 19, 'Dr.', 1, 0),
(261, 19, 'Prof.', 1, 0),
(262, 19, 'Mx.', 1, 0),
(263, 19, 'Other', 1, 0),
(274, 20, 'ACADEMIC CALENDAR', 1, 0),
(275, 20, 'GENERAL MAINTENENCE / EVENT ORGANIZING or PROGRAMME COMMITTEE', 1, 0),
(276, 20, 'LIBRARY & E-LEARNING COMMITTEE', 1, 0),
(277, 20, 'PUBLIC RELATIONS COMMITTEE', 1, 0),
(278, 20, 'CENTRAL PURCHASE/STORES COMMITTEE', 1, 0),
(279, 20, 'DISCIPLINARY COMMITTEE', 1, 0),
(280, 20, 'FACULTY DEVELOPMENT', 1, 0),
(281, 20, 'HOSTEL COMMITTEE', 1, 0),
(282, 20, 'BOARD MEETING (PLANNING & MONITORING)', 1, 0),
(283, 20, 'PRESS / MEDIA COMMITTEE', 1, 0),
(284, 20, 'STUDENT DEVELOPMENT', 1, 0),
(285, 20, 'STUDENTS SCHOLARSHIP SUPPORT COMMITTEE', 1, 0),
(286, 20, 'TRANSPORT COMMITTEE', 1, 0),
(287, 20, 'WEBSITE / ICT / INTERNET COMMITTEE', 1, 0),
(288, 20, 'ALUMNI ASSOCIATION', 1, 0),
(289, 20, 'DIGI LOCKER COMMITTEE', 1, 0),
(290, 20, 'INNOVATIONS AND ENTREPRENEURSHIP DEVELOPMENT CELL', 1, 0),
(291, 20, 'EXAMINATIONS CELL', 1, 0),
(292, 20, 'IIC COMMITTEE', 1, 0),
(293, 20, 'NSS COMMITTEE', 1, 0),
(294, 20, 'INTERNAL QUALITY ASSURANCE COMMITTEE', 1, 0),
(295, 20, 'R&D', 1, 0),
(296, 20, 'TRAINING & PLACEMENT CELL', 1, 0),
(297, 20, 'SPORTS CLUB', 1, 0),
(298, 20, 'CULTURAL COMMITTEE', 1, 0),
(299, 20, 'ECO CLUB', 1, 0),
(300, 20, 'WOMEN EMPOWERMENT COMMITTEE', 1, 0),
(301, 20, 'MAGAZINE COMMITTEE', 1, 0),
(302, 20, 'INFRASTRUCTURE & LABS MAINTENANCE COMMITTEE', 1, 0),
(303, 20, 'ISO COMMITTEE', 1, 0),
(304, 20, 'ANTI RAGGING COMMITTEE', 1, 0),
(305, 20, 'ANTI-RAGGING SQUAD', 1, 0),
(306, 20, 'GRIEVANCE REDRESSAL COMMITTEE (STUDENTS & FACULTY)', 1, 0),
(307, 20, 'SOCIAL WELFARE (SC/ST GRIEVANCE) COMMITTEE', 1, 0),
(308, 20, 'INTERNAL COMPLAINT COMMITTEE', 1, 0),
(309, 14, 'Journals', 1, 0),
(310, 14, 'Books', 1, 0),
(311, 14, 'National Conference', 1, 0),
(312, 14, 'International Conference', 1, 0),
(313, 14, 'Workshop', 1, 0),
(314, 14, 'Webinar', 1, 0),
(315, 14, 'FDP', 1, 0),
(316, 14, 'PDP', 1, 0),
(317, 14, 'Training Program', 1, 0),
(318, 14, 'Seminar', 1, 0),
(319, 14, 'Sports', 1, 0),
(320, 14, 'Conference', 1, 0),
(321, 14, 'Hackathon', 1, 0),
(322, 14, 'Paper Presentation', 1, 0),
(323, 14, 'Symposium', 1, 0),
(324, 14, 'Ideathon', 1, 0),
(325, 14, 'NSS', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_group`
--

CREATE TABLE `svcet_tbl_dev_group` (
  `group_id` int(11) NOT NULL,
  `sem_duration_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `academic_batch_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `year_of_study_id` int(11) NOT NULL,
  `sem_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `group_title` varchar(255) NOT NULL,
  `group_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `group_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `svcet_tbl_dev_group`
--

INSERT INTO `svcet_tbl_dev_group` (`group_id`, `sem_duration_id`, `section_id`, `academic_batch_id`, `academic_year_id`, `year_of_study_id`, `sem_id`, `dept_id`, `group_title`, `group_status`, `group_delete`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, 'I', 1, 0),
(2, 2, 2, 2, 2, 2, 2, 1, 'I', 1, 0),
(3, 3, 3, 3, 3, 3, 3, 1, 'I', 1, 0),
(4, 4, 4, 4, 4, 4, 4, 1, 'I', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_institution`
--

CREATE TABLE `svcet_tbl_dev_institution` (
  `institution_id` int(11) NOT NULL,
  `institution_name` varchar(255) NOT NULL,
  `institution_short_name` varchar(10) NOT NULL,
  `institution_logo` text NOT NULL,
  `latitude_1` varchar(30) NOT NULL,
  `latitude_2` varchar(30) NOT NULL,
  `latitude_3` varchar(30) NOT NULL,
  `latitude_4` varchar(30) NOT NULL,
  `longitude_1` varchar(30) NOT NULL,
  `longitude_2` varchar(30) NOT NULL,
  `longitude_3` varchar(30) NOT NULL,
  `longitude_4` varchar(30) NOT NULL,
  `crypt` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `svcet_tbl_dev_institution`
--

INSERT INTO `svcet_tbl_dev_institution` (`institution_id`, `institution_name`, `institution_short_name`, `institution_logo`, `latitude_1`, `latitude_2`, `latitude_3`, `latitude_4`, `longitude_1`, `longitude_2`, `longitude_3`, `longitude_4`, `crypt`) VALUES
(1, 'SVCET Engineering College', 'SVCET', 'svcet-logo-potrait.png', '10.2345', '11.2345', '12.2345', '13.2345', '76.3456', '77.3456', '78.3456', '79.3456', 'd��G�By��');

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_pages`
--

CREATE TABLE `svcet_tbl_dev_pages` (
  `page_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `page_title` varchar(255) NOT NULL,
  `page_type` tinyint(1) NOT NULL COMMENT '1 - module | 2 - Page',
  `portal_type` tinyint(1) NOT NULL COMMENT '1 - faculty, 2 - student, 3 - parent, 4 - developer',
  `module_status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 - faculty, 2 - student, 3 - parent',
  `page_link` text DEFAULT NULL,
  `navbar_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 - not show | 1 - show',
  `page_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `page_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `svcet_tbl_dev_pages`
--

INSERT INTO `svcet_tbl_dev_pages` (`page_id`, `module_id`, `page_title`, `page_type`, `portal_type`, `module_status`, `page_link`, `navbar_status`, `page_status`, `page_deleted`) VALUES
(1, 1, 'Profile ', 1, 1, 1, 'faculty-profile?action=view&route=faculty', 0, 1, 0),
(2, 1, 'View Faculty Profile', 2, 1, 1, 'faculty-profile?action=view&route=faculty', 1, 1, 0),
(3, 1, 'Faculty Profile Dashboard', 2, 1, 1, 'faculty-profile?action=view&route=faculty&type=dashboard', 1, 1, 0),
(4, 1, 'Faculty Overall Profile', 2, 1, 1, 'faculty-profile?action=view&route=faculty&type=overall\n', 1, 1, 0),
(5, 1, 'Student Overall Profile || Faculty Perspective', 2, 1, 2, 'faculty-profile?action=view&route=student&type=overall', 1, 1, 0),
(6, 1, 'View Student Profile || Faculty Perspective', 2, 1, 2, 'faculty-profile?action=view&route=student', 1, 1, 0),
(7, 1, 'View Student Profile', 2, 2, 2, 'student-profile?action=view&route=student', 1, 1, 0),
(8, 1, 'View Student Profile || Parent Perspective', 2, 3, 2, 'parent-profile?action=view&route=student', 1, 1, 0),
(9, 1, 'View Parent Profile', 2, 3, 3, 'parent-profile?action=view&route=parent', 1, 1, 0),
(10, 3, 'Roles & Responsibility', 1, 1, 1, '', 0, 1, 0),
(22, 1, 'Faculty Update Profile Info', 2, 1, 1, 'faculty-profile?action=add&route=faculty&type=personal&tab=personal', 0, 1, 0),
(23, 1, 'Faculty Update Contact Info ', 2, 1, 1, 'faculty-profile?action=add&route=faculty&type=personal&tab=contact', 0, 1, 0),
(24, 1, 'Faculty Update Address Info ', 2, 1, 1, 'faculty-profile?action=add&route=faculty&type=personal&tab=address', 0, 1, 0),
(30, 1, 'Faculty Update Experience Info ', 2, 1, 1, 'faculty-profile?action=add&route=faculty&type=experience&tab=industry', 0, 1, 0),
(39, 3, 'View Roles & Responsibility', 2, 2, 2, 'student-roles-responsibilities?action=view&route=student', 1, 1, 0),
(40, 3, 'View Roles & Responsibility', 2, 3, 2, 'parents-roles-responsibilities?action=view&route=student', 1, 1, 0),
(41, 2, 'Add Faculty Achievements', 2, 1, 1, 'faculty-achievements?action=add&route=faculty', 0, 1, 0),
(42, 2, 'Achievements', 1, 1, 1, '', 0, 1, 0),
(43, 2, 'View Faculty Achievements', 2, 1, 1, 'faculty-achievements?action=view&route=faculty', 1, 1, 0),
(45, 2, 'Add Student Achievements', 2, 2, 2, 'student-achievements?action=add&route=student', 0, 1, 0),
(46, 2, 'View Student Achievements ', 2, 2, 2, 'student-achievements?action=view&route=student', 1, 1, 0),
(48, 2, 'View Student Achievements || Faculty Perspective', 2, 1, 2, 'faculty-achievements?action=view&route=student&type=overall', 1, 1, 0),
(49, 1, 'Edit Profile Student', 2, 2, 2, 'student-profile?action=edit&route=student', 0, 1, 0),
(50, 1, 'Edit Profile Faculty', 2, 1, 1, 'faculty-profile?action=edit&route=faculty', 0, 1, 0),
(51, 1, 'Edit Profile Parent', 2, 3, 3, 'parent-profile?action=edit&route=parent', 0, 1, 0),
(52, 1, 'Edit PG Info ', 2, 1, 1, 'faculty-profile?action=edit&route=faculty&type=education&tab=pg', 0, 1, 0),
(53, 1, 'Edit Industry Experience Info ', 2, 1, 1, 'faculty-profile?action=edit&route=faculty&type=experience&tab=industry', 0, 1, 0),
(54, 1, 'Edit Institution Experience Info ', 2, 1, 1, 'faculty-profile?action=edit&route=faculty&type=experience&tab=institution', 0, 1, 0),
(55, 1, 'Edit Skill Expression Info ', 2, 1, 1, 'faculty-profile?action=edit&route=faculty&type=skill&tab=skill', 0, 1, 0),
(56, 1, 'Edit Document Upload Info ', 2, 1, 1, 'faculty-profile?action=edit&route=faculty&type=documentupload&tab=document', 0, 1, 0),
(57, 2, 'View Parent Achievements', 2, 3, 2, 'parent-achievements?action=view&route=student', 1, 1, 0),
(58, 1, 'Update Student Personal Profile ', 2, 1, 2, 'faculty-student-admission?action=add&route=student&type=personal&tab=personal', 0, 1, 0),
(59, 1, 'Update Student Contact Info ', 2, 1, 2, 'faculty-student-admission?action=add&route=student&type=personal&tab=contact', 0, 1, 0),
(60, 1, 'Update Student Address Info ', 2, 1, 2, 'faculty-student-admission?action=add&route=student&type=personal&tab=address', 0, 1, 0),
(62, 1, 'Update Student schooling Info ', 2, 1, 2, 'faculty-student-admission?action=add&route=student&type=education&tab=schooling', 0, 1, 0),
(63, 1, 'Update Student Degree Info ', 2, 1, 2, 'faculty-student-admission?action=add&route=student&type=education&tab=degree', 0, 1, 0),
(66, 1, 'Update Student Course Preference Info ', 2, 1, 2, 'faculty-student-admission?action=add&route=student&type=course&tab=course', 0, 1, 0),
(68, 1, 'Edit Personal Info', 2, 2, 2, 'student-profile?action=edit&route=faculty&type=personal&tab=personal', 0, 1, 0),
(69, 1, 'Edit Address Info', 2, 2, 2, 'student-profile?action=edit&route=student&type=personal&tab=address', 0, 1, 0),
(70, 1, 'Edit Contact Info', 2, 2, 2, 'student-profile?action=edit&route=student&type=personal&tab=contact', 0, 1, 0),
(71, 1, 'Edit SSlC Info', 2, 2, 2, 'student-profile?action=edit&route=student&type=education&tab=sslc', 0, 1, 0),
(72, 1, 'Edit HSC Info ', 2, 2, 2, 'student-profile?action=edit&route=student&type=education&tab=hsc', 0, 1, 0),
(73, 1, 'Edit Diploma Info ', 2, 2, 2, 'student-profile?action=edit&route=student&type=education&tab=diploma', 0, 1, 0),
(74, 1, 'Edit UG Info ', 2, 2, 2, 'student-profile?action=edit&route=student&type=education&tab=ug', 0, 1, 0),
(75, 1, 'Edit PG Info ', 2, 2, 2, 'student-profile?action=edit&route=student&type=education&tab=pg', 0, 1, 0),
(76, 1, 'Edit Document Upload Info ', 2, 2, 2, 'student-profile?action=edit&route=student&type=documentupload&tab=document', 0, 1, 0),
(77, 1, 'Edit Course Preference Info ', 2, 2, 2, 'student-profile?action=add&route=student&type=course&tab=course', 0, 1, 0),
(78, 1, 'Update Student Parent Info', 2, 1, 2, 'faculty-student-admission?action=add&route=student&type=personal&tab=parent', 0, 1, 0),
(79, 1, 'Edit Parent Profile', 2, 2, 2, 'student-profile?action=edit&route=faculty&type=personal&tab=parent', 0, 1, 0),
(80, 4, 'Faculty Admission ', 2, 1, 1, 'faculty-admission?action=add&route=faculty&type=entry', 0, 1, 0),
(81, 4, 'Overall Faculty Admission ', 2, 1, 1, 'faculty-admission?action=view&route=faculty&type=overall', 1, 1, 0),
(82, 4, 'Student Admission', 2, 1, 2, 'faculty-student-admission?action=add&route=student&type=personal&tab=personal', 1, 1, 0),
(83, 4, 'Overall Student Admission ', 2, 1, 2, 'faculty-student-admission?action=view&route=student&type=overall', 1, 1, 0),
(84, 2, 'Overall Faculty achievements', 2, 1, 1, 'faculty-achievements?action=view&route=faculty&type=overall', 1, 1, 0),
(86, 1, 'attendance entry form', 2, 1, 2, 'faculty-student-attendance?action=add&route=student', 0, 1, 0),
(87, 1, 'students leave request view', 2, 1, 2, 'faculty-student-attendance?action=view&route=student&type=leave_request', 0, 1, 0),
(88, 1, 'students leave request approve', 2, 1, 2, 'faculty-student-attendance?action=add&route=student&type=leave_request', 0, 1, 0),
(89, 1, 'students individual view', 2, 1, 2, 'faculty-student-attendance?action=view&route=student', 0, 1, 0),
(90, 1, 'students overall attendance view', 2, 1, 2, 'faculty-student-attendance?action=view&route=student&type=overall', 0, 1, 0),
(91, 1, 'attendance entry form', 2, 1, 2, 'student-attendance?action=add&route=student', 0, 1, 0),
(92, 1, 'attendance overall', 2, 1, 2, 'student-attendance?action=add&route=student&type=overall', 0, 1, 0),
(93, 1, 'attendance status view', 2, 1, 2, 'student-attendance?action=view&route=student', 0, 1, 0),
(94, 1, 'Student Admission Dashboard', 2, 1, 2, 'faculty-student-admission?action=view&route=student&type=dashboard', 0, 1, 0),
(95, 1, 'Overal Individual View Profile', 2, 1, 1, 'faculty-profile?action=view&route=faculty&type=overall&id=*', 1, 1, 0),
(96, 1, 'academic calendar', 2, 1, 2, 'faculty-academic-calendar?action=view&route=faculty', 0, 1, 0),
(97, 1, 'academic add calendar', 2, 1, 2, 'faculty-academic-calendar?action=add&route=faculty', 0, 1, 0),
(98, 1, 'Faculty Main Dashboard', 2, 1, 1, 'faculty-profile', 1, 1, 0),
(99, 3, 'View Faculty Authorities', 2, 1, 1, 'faculty-roles-responsibilities?action=view&route=faculty&type=authorities', 1, 1, 0),
(100, 3, 'View Faculty Committees', 2, 1, 1, 'faculty-roles-responsibilities?action=view&route=faculty&type=committees', 1, 1, 0),
(101, 3, 'View Faculty Class Advisors', 2, 1, 1, 'faculty-roles-responsibilities?action=view&route=faculty&type=class_advisors', 1, 1, 0),
(102, 3, 'View Faculty Mentors', 2, 1, 1, 'faculty-roles-responsibilities?action=view&route=faculty&type=mentors', 1, 1, 0),
(103, 3, 'Edit Faculty Authorities', 2, 1, 1, 'faculty-roles-responsibilities?action=edit&route=faculty&type=authorities', 1, 1, 0),
(104, 3, 'Edit Faculty Committees', 2, 1, 1, 'faculty-roles-responsibilities?action=edit&route=faculty&type=committees', 1, 1, 0),
(105, 3, 'Edit Faculty Class Advisors', 2, 1, 1, 'faculty-roles-responsibilities?action=edit&route=faculty&type=class_advisors', 1, 1, 0),
(106, 3, 'Edit Faculty Mentors', 2, 1, 1, 'faculty-roles-responsibilities?action=edit&route=faculty&type=mentors', 1, 1, 0),
(107, 3, 'View Student Committees || Faculty Perspective', 2, 1, 1, 'faculty-roles-responsibilities?action=view&route=student&type=committees', 1, 1, 0),
(108, 3, 'View Student Representatives || Faculty Perspective', 2, 1, 1, 'faculty-roles-responsibilities?action=view&route=student&type=representatives', 1, 1, 0),
(109, 3, 'Edit Student Committees || Faculty Perspective', 2, 1, 1, 'faculty-roles-responsibilities?action=edit&route=student&type=committees', 1, 1, 0),
(110, 3, 'Edit Student Representatives || Faculty Perspective', 2, 1, 1, 'faculty-roles-responsibilities?action=edit&route=student&type=representatives', 1, 1, 0),
(115, 1, 'Faculty Update Official Info ', 2, 1, 1, 'faculty-profile?action=add&route=faculty&type=personal&tab=official', 0, 1, 0),
(117, 1, 'Faculty Update Degrees Info ', 2, 1, 1, 'faculty-profile?action=add&route=faculty&type=education&tab=degrees', 0, 1, 0),
(119, 1, 'Faculty Update Schoolings Info ', 2, 1, 1, 'faculty-profile?action=add&route=faculty&type=education&tab=schoolings', 0, 1, 0),
(120, 1, 'Faculty Update Skills Info ', 2, 1, 1, 'faculty-profile?action=add&route=faculty&type=skill&tab=knowledge', 0, 1, 0),
(121, 1, 'Faculty Update Document Info ', 2, 1, 1, 'faculty-profile?action=add&route=faculty&type=upload&tab=document', 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_period_time`
--

CREATE TABLE `svcet_tbl_dev_period_time` (
  `period_id` int(11) NOT NULL,
  `sem_duration_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `academic_batch_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `year_of_study_id` int(11) NOT NULL,
  `sem_id` int(11) NOT NULL,
  `period_hour` int(11) NOT NULL,
  `period_title` varchar(20) NOT NULL,
  `period_type` int(11) NOT NULL COMMENT '1 - Class | 2 - Break | 3 - Lunch',
  `period_start_time` time NOT NULL,
  `period_end_time` time NOT NULL,
  `period_session` tinyint(1) NOT NULL COMMENT '1 - morning | 2 - afternoon',
  `period_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `period_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_prefixes`
--

CREATE TABLE `svcet_tbl_dev_prefixes` (
  `prefixes_id` int(11) NOT NULL,
  `prefixes_group_id` int(11) NOT NULL COMMENT '1 - Faculty |2 - Student | 3 - Parent | 4 - Role |  5 - subject | 6 - exam | 7 - SSLC doc | 8 - HSC doc | 9 - highest qualification | exp certificate | 10 - resume | 11 - leave | 12 - event | 13 - material | 14 - achievement | 15 - TC | 16 - PIC | 17 - residence | 18 - community | 19 - Experience certificate | 20 - Profile Pic',
  `prefixes_title` varchar(255) NOT NULL,
  `prefixes_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `prefixes_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `svcet_tbl_dev_prefixes`
--

INSERT INTO `svcet_tbl_dev_prefixes` (`prefixes_id`, `prefixes_group_id`, `prefixes_title`, `prefixes_status`, `prefixes_delete`) VALUES
(1, 1, 'SVCET-FAC-', 1, 0),
(2, 2, 'SVCET-STU-', 1, 0),
(3, 3, 'SVCET-PAR-', 1, 0),
(4, 4, 'ROLE-', 1, 0),
(5, 7, 'SSLC Certificate', 1, 0),
(6, 8, 'HSC Certificate', 1, 0),
(7, 9, 'Highest Qualification Certificate', 1, 0),
(8, 10, 'Resume Certificate', 1, 0),
(9, 19, 'Experience Certificate', 1, 0),
(11, 20, 'Profile Pic', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_roles`
--

CREATE TABLE `svcet_tbl_dev_roles` (
  `role_id` int(11) NOT NULL,
  `portal_type` tinyint(1) NOT NULL COMMENT '1 - faculty, 2 - student, 3 - parent, 4 - developer',
  `role_title` varchar(100) NOT NULL,
  `role_code` int(11) NOT NULL,
  `role_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `role_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `svcet_tbl_dev_roles`
--

INSERT INTO `svcet_tbl_dev_roles` (`role_id`, `portal_type`, `role_title`, `role_code`, `role_status`, `role_deleted`) VALUES
(1, 1, 'Super Admin', 1, 1, 0),
(2, 1, 'Admin', 2, 1, 0),
(3, 1, 'Principal', 3, 1, 0),
(4, 1, 'Vice Principal', 4, 1, 0),
(5, 1, 'Dean - Academics', 5, 1, 0),
(6, 1, 'Head Of The Department', 6, 1, 0),
(7, 1, 'Cell Head', 7, 1, 0),
(8, 1, 'Teaching Faculty', 8, 1, 0),
(9, 1, 'Non Teaching Faculty', 9, 1, 0),
(10, 2, 'Student', 10, 1, 0),
(11, 3, 'Parent', 11, 1, 0),
(12, 1, 'Dean - IQAC', 12, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_role_permission`
--

CREATE TABLE `svcet_tbl_dev_role_permission` (
  `role_perm_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `role_perm_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `role_perm_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `svcet_tbl_dev_role_permission`
--

INSERT INTO `svcet_tbl_dev_role_permission` (`role_perm_id`, `role_id`, `page_id`, `role_perm_status`, `role_perm_deleted`) VALUES
(1, 1, 1, 1, 0),
(2, 1, 2, 1, 0),
(3, 1, 3, 1, 0),
(4, 1, 4, 1, 0),
(5, 1, 5, 1, 0),
(6, 1, 6, 1, 0),
(7, 1, 10, 1, 0),
(8, 1, 22, 1, 0),
(9, 1, 23, 1, 0),
(10, 1, 24, 1, 0),
(11, 1, 30, 1, 0),
(12, 1, 41, 1, 0),
(13, 1, 42, 1, 0),
(14, 1, 43, 1, 0),
(15, 1, 48, 1, 0),
(16, 1, 50, 1, 0),
(17, 1, 52, 1, 0),
(18, 1, 53, 1, 0),
(19, 1, 54, 1, 0),
(20, 1, 55, 1, 0),
(21, 1, 56, 1, 0),
(22, 1, 58, 1, 0),
(23, 1, 59, 1, 0),
(24, 1, 60, 1, 0),
(25, 1, 62, 1, 0),
(26, 1, 63, 1, 0),
(27, 1, 66, 1, 0),
(28, 1, 78, 1, 0),
(29, 1, 80, 1, 0),
(30, 1, 81, 1, 0),
(31, 1, 82, 1, 0),
(32, 1, 83, 1, 0),
(33, 1, 84, 1, 0),
(34, 1, 86, 1, 0),
(35, 1, 87, 1, 0),
(36, 1, 88, 1, 0),
(37, 1, 89, 1, 0),
(38, 1, 90, 1, 0),
(39, 1, 91, 1, 0),
(40, 1, 92, 1, 0),
(41, 1, 93, 1, 0),
(42, 1, 94, 1, 0),
(43, 1, 95, 1, 0),
(44, 1, 96, 1, 0),
(45, 1, 97, 1, 0),
(46, 1, 98, 1, 0),
(47, 1, 99, 1, 0),
(48, 1, 100, 1, 0),
(49, 1, 101, 1, 0),
(50, 1, 102, 1, 0),
(51, 1, 103, 1, 0),
(52, 1, 104, 1, 0),
(53, 1, 105, 1, 0),
(54, 1, 106, 1, 0),
(55, 1, 107, 1, 0),
(56, 1, 108, 1, 0),
(57, 1, 109, 1, 0),
(58, 1, 110, 1, 0),
(59, 1, 115, 1, 0),
(60, 1, 117, 1, 0),
(61, 1, 119, 1, 0),
(62, 1, 120, 1, 0),
(63, 1, 121, 1, 0),
(64, 2, 1, 1, 0),
(65, 2, 2, 1, 0),
(66, 2, 3, 1, 0),
(67, 2, 4, 1, 0),
(68, 2, 5, 1, 0),
(69, 2, 6, 1, 0),
(70, 2, 10, 1, 0),
(71, 2, 22, 1, 0),
(72, 2, 23, 1, 0),
(73, 2, 24, 1, 0),
(74, 2, 30, 1, 0),
(75, 2, 41, 1, 0),
(76, 2, 42, 1, 0),
(77, 2, 43, 1, 0),
(78, 2, 48, 1, 0),
(79, 2, 50, 1, 0),
(80, 2, 52, 1, 0),
(81, 2, 53, 1, 0),
(82, 2, 54, 1, 0),
(83, 2, 55, 1, 0),
(84, 2, 56, 1, 0),
(85, 2, 58, 1, 0),
(86, 2, 59, 1, 0),
(87, 2, 60, 1, 0),
(88, 2, 62, 1, 0),
(89, 2, 63, 1, 0),
(90, 2, 66, 1, 0),
(91, 2, 78, 1, 0),
(92, 2, 80, 1, 0),
(93, 2, 81, 1, 0),
(94, 2, 82, 1, 0),
(95, 2, 83, 1, 0),
(96, 2, 84, 1, 0),
(97, 2, 86, 1, 0),
(98, 2, 87, 1, 0),
(99, 2, 88, 1, 0),
(100, 2, 89, 1, 0),
(101, 2, 90, 1, 0),
(102, 2, 91, 1, 0),
(103, 2, 92, 1, 0),
(104, 2, 93, 1, 0),
(105, 2, 94, 1, 0),
(106, 2, 95, 1, 0),
(107, 2, 96, 1, 0),
(108, 2, 97, 1, 0),
(109, 2, 98, 1, 0),
(110, 2, 99, 1, 0),
(111, 2, 100, 1, 0),
(112, 2, 101, 1, 0),
(113, 2, 102, 1, 0),
(114, 2, 103, 1, 0),
(115, 2, 104, 1, 0),
(116, 2, 105, 1, 0),
(117, 2, 106, 1, 0),
(118, 2, 107, 1, 0),
(119, 2, 108, 1, 0),
(120, 2, 109, 1, 0),
(121, 2, 110, 1, 0),
(122, 2, 115, 1, 0),
(123, 2, 117, 1, 0),
(124, 2, 119, 1, 0),
(125, 2, 120, 1, 0),
(126, 2, 121, 1, 0),
(127, 3, 1, 1, 0),
(128, 3, 2, 1, 0),
(129, 3, 3, 1, 0),
(130, 3, 4, 1, 0),
(131, 3, 5, 1, 0),
(132, 3, 6, 1, 0),
(133, 3, 10, 1, 0),
(134, 3, 22, 1, 0),
(135, 3, 23, 1, 0),
(136, 3, 24, 1, 0),
(137, 3, 30, 1, 0),
(138, 3, 42, 1, 0),
(139, 3, 43, 1, 0),
(140, 3, 48, 1, 0),
(141, 3, 50, 1, 0),
(142, 3, 52, 1, 0),
(143, 3, 53, 1, 0),
(144, 3, 54, 1, 0),
(145, 3, 55, 1, 0),
(146, 3, 56, 1, 0),
(147, 3, 83, 1, 0),
(148, 3, 84, 1, 0),
(149, 3, 86, 1, 0),
(150, 3, 87, 1, 0),
(151, 3, 88, 1, 0),
(152, 3, 89, 1, 0),
(153, 3, 90, 1, 0),
(154, 3, 91, 1, 0),
(155, 3, 92, 1, 0),
(156, 3, 93, 1, 0),
(157, 3, 94, 1, 0),
(158, 3, 95, 1, 0),
(159, 3, 96, 1, 0),
(160, 3, 97, 1, 0),
(161, 3, 98, 1, 0),
(162, 3, 99, 1, 0),
(163, 3, 100, 1, 0),
(164, 3, 101, 1, 0),
(165, 3, 102, 1, 0),
(166, 3, 104, 1, 0),
(167, 3, 107, 1, 0),
(168, 3, 108, 1, 0),
(169, 3, 109, 1, 0),
(170, 3, 110, 1, 0),
(171, 3, 115, 1, 0),
(172, 3, 117, 1, 0),
(173, 3, 119, 1, 0),
(174, 3, 120, 1, 0),
(175, 3, 121, 1, 0),
(176, 4, 1, 1, 0),
(177, 4, 2, 1, 0),
(178, 4, 3, 1, 0),
(179, 4, 4, 1, 0),
(180, 4, 5, 1, 0),
(181, 4, 6, 1, 0),
(182, 4, 10, 1, 0),
(183, 4, 22, 1, 0),
(184, 4, 23, 1, 0),
(185, 4, 24, 1, 0),
(186, 4, 30, 1, 0),
(187, 4, 42, 1, 0),
(188, 4, 43, 1, 0),
(189, 4, 48, 1, 0),
(190, 4, 50, 1, 0),
(191, 4, 52, 1, 0),
(192, 4, 53, 1, 0),
(193, 4, 54, 1, 0),
(194, 4, 55, 1, 0),
(195, 4, 56, 1, 0),
(196, 4, 83, 1, 0),
(197, 4, 84, 1, 0),
(198, 4, 86, 1, 0),
(199, 4, 87, 1, 0),
(200, 4, 88, 1, 0),
(201, 4, 89, 1, 0),
(202, 4, 90, 1, 0),
(203, 4, 91, 1, 0),
(204, 4, 92, 1, 0),
(205, 4, 93, 1, 0),
(206, 4, 94, 1, 0),
(207, 4, 95, 1, 0),
(208, 4, 96, 1, 0),
(209, 4, 97, 1, 0),
(210, 4, 98, 1, 0),
(211, 4, 99, 1, 0),
(212, 4, 100, 1, 0),
(213, 4, 101, 1, 0),
(214, 4, 102, 1, 0),
(215, 4, 104, 1, 0),
(216, 4, 107, 1, 0),
(217, 4, 108, 1, 0),
(218, 4, 109, 1, 0),
(219, 4, 110, 1, 0),
(220, 4, 115, 1, 0),
(221, 4, 117, 1, 0),
(222, 4, 119, 1, 0),
(223, 4, 120, 1, 0),
(224, 4, 121, 1, 0),
(225, 5, 1, 1, 0),
(226, 5, 2, 1, 0),
(227, 5, 3, 1, 0),
(228, 5, 4, 1, 0),
(229, 5, 5, 1, 0),
(230, 5, 6, 1, 0),
(231, 5, 10, 1, 0),
(232, 5, 22, 1, 0),
(233, 5, 23, 1, 0),
(234, 5, 24, 1, 0),
(235, 5, 30, 1, 0),
(236, 5, 42, 1, 0),
(237, 5, 43, 1, 0),
(238, 5, 48, 1, 0),
(239, 5, 50, 1, 0),
(240, 5, 52, 1, 0),
(241, 5, 53, 1, 0),
(242, 5, 54, 1, 0),
(243, 5, 55, 1, 0),
(244, 5, 56, 1, 0),
(245, 5, 83, 1, 0),
(246, 5, 84, 1, 0),
(247, 5, 86, 1, 0),
(248, 5, 87, 1, 0),
(249, 5, 88, 1, 0),
(250, 5, 89, 1, 0),
(251, 5, 90, 1, 0),
(252, 5, 91, 1, 0),
(253, 5, 92, 1, 0),
(254, 5, 93, 1, 0),
(255, 5, 94, 1, 0),
(256, 5, 95, 1, 0),
(257, 5, 96, 1, 0),
(258, 5, 97, 1, 0),
(259, 5, 98, 1, 0),
(260, 5, 99, 1, 0),
(261, 5, 100, 1, 0),
(262, 5, 101, 1, 0),
(263, 5, 102, 1, 0),
(264, 5, 104, 1, 0),
(265, 5, 107, 1, 0),
(266, 5, 108, 1, 0),
(267, 5, 109, 1, 0),
(268, 5, 110, 1, 0),
(269, 5, 115, 1, 0),
(270, 5, 117, 1, 0),
(271, 5, 119, 1, 0),
(272, 5, 120, 1, 0),
(273, 5, 121, 1, 0),
(274, 6, 1, 1, 0),
(275, 6, 2, 1, 0),
(276, 6, 3, 1, 0),
(277, 6, 4, 1, 0),
(278, 6, 5, 1, 0),
(279, 6, 6, 1, 0),
(280, 6, 10, 1, 0),
(281, 6, 22, 1, 0),
(282, 6, 23, 1, 0),
(283, 6, 24, 1, 0),
(284, 6, 30, 1, 0),
(285, 6, 42, 1, 0),
(286, 6, 43, 1, 0),
(287, 6, 48, 1, 0),
(288, 6, 50, 1, 0),
(289, 6, 52, 1, 0),
(290, 6, 53, 1, 0),
(291, 6, 54, 1, 0),
(292, 6, 55, 1, 0),
(293, 6, 56, 1, 0),
(294, 6, 83, 1, 0),
(295, 6, 84, 1, 0),
(296, 6, 86, 1, 0),
(297, 6, 87, 1, 0),
(298, 6, 88, 1, 0),
(299, 6, 89, 1, 0),
(300, 6, 90, 1, 0),
(301, 6, 91, 1, 0),
(302, 6, 92, 1, 0),
(303, 6, 93, 1, 0),
(304, 6, 94, 1, 0),
(305, 6, 95, 1, 0),
(306, 6, 96, 1, 0),
(307, 6, 97, 1, 0),
(308, 6, 98, 1, 0),
(309, 6, 99, 1, 0),
(310, 6, 100, 1, 0),
(311, 6, 102, 1, 0),
(312, 6, 104, 1, 0),
(313, 6, 107, 1, 0),
(314, 6, 108, 1, 0),
(315, 6, 109, 1, 0),
(316, 6, 110, 1, 0),
(317, 6, 115, 1, 0),
(318, 6, 117, 1, 0),
(319, 6, 119, 1, 0),
(320, 6, 120, 1, 0),
(321, 6, 121, 1, 0),
(322, 6, 106, 1, 0),
(323, 6, 101, 1, 0),
(324, 6, 105, 1, 0),
(325, 8, 1, 1, 0),
(326, 8, 2, 1, 0),
(327, 8, 3, 1, 0),
(328, 8, 4, 1, 0),
(329, 8, 5, 1, 0),
(330, 8, 6, 1, 0),
(331, 8, 10, 1, 0),
(332, 8, 22, 1, 0),
(333, 8, 23, 1, 0),
(334, 8, 24, 1, 0),
(335, 8, 30, 1, 0),
(336, 8, 42, 1, 0),
(337, 8, 43, 1, 0),
(338, 8, 48, 1, 0),
(339, 8, 50, 1, 0),
(340, 8, 52, 1, 0),
(341, 8, 53, 1, 0),
(342, 8, 54, 1, 0),
(343, 8, 55, 1, 0),
(344, 8, 56, 1, 0),
(345, 8, 84, 1, 0),
(346, 8, 86, 1, 0),
(347, 8, 87, 1, 0),
(348, 8, 88, 1, 0),
(349, 8, 89, 1, 0),
(350, 8, 90, 1, 0),
(351, 8, 91, 1, 0),
(352, 8, 92, 1, 0),
(353, 8, 93, 1, 0),
(354, 8, 95, 1, 0),
(355, 8, 96, 1, 0),
(356, 8, 97, 1, 0),
(357, 8, 98, 1, 0),
(358, 8, 99, 1, 0),
(359, 8, 100, 1, 0),
(360, 8, 102, 1, 0),
(361, 8, 104, 1, 0),
(362, 8, 107, 1, 0),
(363, 8, 108, 1, 0),
(364, 8, 109, 1, 0),
(365, 8, 110, 1, 0),
(366, 8, 115, 1, 0),
(367, 8, 117, 1, 0),
(368, 8, 119, 1, 0),
(369, 8, 120, 1, 0),
(370, 8, 121, 1, 0),
(371, 12, 1, 1, 0),
(372, 12, 2, 1, 0),
(373, 12, 3, 1, 0),
(374, 12, 4, 1, 0),
(375, 12, 5, 1, 0),
(376, 12, 6, 1, 0),
(377, 12, 10, 1, 0),
(378, 12, 22, 1, 0),
(379, 12, 23, 1, 0),
(380, 12, 24, 1, 0),
(381, 12, 30, 1, 0),
(382, 12, 42, 1, 0),
(383, 12, 43, 1, 0),
(384, 12, 48, 1, 0),
(385, 12, 50, 1, 0),
(386, 12, 52, 1, 0),
(387, 12, 53, 1, 0),
(388, 12, 54, 1, 0),
(389, 12, 55, 1, 0),
(390, 12, 56, 1, 0),
(391, 12, 83, 1, 0),
(392, 12, 84, 1, 0),
(393, 12, 86, 1, 0),
(394, 12, 87, 1, 0),
(395, 12, 88, 1, 0),
(396, 12, 89, 1, 0),
(397, 12, 90, 1, 0),
(398, 12, 91, 1, 0),
(399, 12, 92, 1, 0),
(400, 12, 93, 1, 0),
(401, 12, 94, 1, 0),
(402, 12, 95, 1, 0),
(403, 12, 96, 1, 0),
(404, 12, 97, 1, 0),
(405, 12, 98, 1, 0),
(406, 12, 99, 1, 0),
(407, 12, 100, 1, 0),
(408, 12, 101, 1, 0),
(409, 12, 102, 1, 0),
(410, 12, 104, 1, 0),
(411, 12, 107, 1, 0),
(412, 12, 108, 1, 0),
(413, 12, 109, 1, 0),
(414, 12, 110, 1, 0),
(415, 12, 115, 1, 0),
(416, 12, 117, 1, 0),
(417, 12, 119, 1, 0),
(418, 12, 120, 1, 0),
(419, 12, 121, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_room_management`
--

CREATE TABLE `svcet_tbl_dev_room_management` (
  `room_id` int(11) NOT NULL,
  `room_dept_id` int(11) NOT NULL,
  `room_number` int(11) NOT NULL,
  `room_name` varchar(255) NOT NULL,
  `room_floor` int(11) NOT NULL,
  `room_category` varchar(255) NOT NULL,
  `room_type` tinyint(1) NOT NULL COMMENT '1 - Teaching Use | 2 - Office Use',
  `max_capacity` int(11) NOT NULL,
  `room_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `room_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_section`
--

CREATE TABLE `svcet_tbl_dev_section` (
  `section_id` int(11) NOT NULL,
  `sem_duration_id` int(11) NOT NULL,
  `academic_batch_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `year_of_study_id` int(11) NOT NULL,
  `sem_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `section_title` varchar(255) NOT NULL,
  `section_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - Active, 0 - Inactive, 3 - Completed',
  `section_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `svcet_tbl_dev_section`
--

INSERT INTO `svcet_tbl_dev_section` (`section_id`, `sem_duration_id`, `academic_batch_id`, `academic_year_id`, `year_of_study_id`, `sem_id`, `dept_id`, `section_title`, `section_status`, `section_delete`) VALUES
(1, 1, 1, 1, 1, 1, 1, 'A', 1, 0),
(2, 2, 2, 2, 1, 2, 1, 'B', 1, 0),
(3, 3, 3, 3, 3, 3, 1, 'A', 1, 0),
(4, 4, 4, 4, 4, 4, 1, 'A', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_sem`
--

CREATE TABLE `svcet_tbl_dev_sem` (
  `sem_id` int(11) NOT NULL,
  `sem_duration_id` int(11) NOT NULL,
  `academic_batch_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `year_of_study_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `sem_title` varchar(255) NOT NULL,
  `sem_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `sem_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `svcet_tbl_dev_sem`
--

INSERT INTO `svcet_tbl_dev_sem` (`sem_id`, `sem_duration_id`, `academic_batch_id`, `academic_year_id`, `year_of_study_id`, `dept_id`, `sem_title`, `sem_status`, `sem_delete`) VALUES
(1, 1, 1, 1, 1, 1, 'VII', 1, 0),
(2, 2, 2, 2, 2, 1, 'V', 1, 0),
(3, 3, 3, 3, 3, 1, 'III', 1, 0),
(4, 4, 4, 4, 4, 1, 'I', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_sem_duration`
--

CREATE TABLE `svcet_tbl_dev_sem_duration` (
  `sem_duration_id` int(11) NOT NULL,
  `sem_duration_title` varchar(20) NOT NULL,
  `sem_duration_code` varchar(20) DEFAULT NULL,
  `sem_duration_start_date` date NOT NULL,
  `sem_duration_end_date` date NOT NULL,
  `sem_duration_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `sem_duration_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `svcet_tbl_dev_sem_duration`
--

INSERT INTO `svcet_tbl_dev_sem_duration` (`sem_duration_id`, `sem_duration_title`, `sem_duration_code`, `sem_duration_start_date`, `sem_duration_end_date`, `sem_duration_status`, `sem_duration_delete`) VALUES
(1, 'Odd Sem', NULL, '2024-10-15', '2025-01-15', 1, 0),
(2, 'Odd Sem', NULL, '2024-10-15', '2025-01-15', 1, 0),
(3, 'Odd Sem', NULL, '2024-10-15', '2025-01-15', 1, 0),
(4, 'Odd Sem', NULL, '2024-10-15', '2025-01-15', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_stock_details`
--

CREATE TABLE `svcet_tbl_dev_stock_details` (
  `stock_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_code` varchar(255) NOT NULL,
  `item_category` varchar(255) NOT NULL,
  `item_unit_of_measure` varchar(50) NOT NULL,
  `stock_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `stock_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_subject`
--

CREATE TABLE `svcet_tbl_dev_subject` (
  `subject_id` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `subject_short_name` varchar(10) NOT NULL,
  `subject_code` varchar(50) NOT NULL,
  `subject_type` tinyint(1) NOT NULL COMMENT '1 - Theory | 2 - Practical | 3 - Project ',
  `group_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `academic_batch_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `year_of_study_id` int(11) NOT NULL,
  `sem_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `number_of_hours` int(11) NOT NULL,
  `no_of_periods_per_week` int(11) NOT NULL,
  `no_of_periods_per_day` int(11) NOT NULL,
  `subject_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `subject_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_subject_lessonplan`
--

CREATE TABLE `svcet_tbl_dev_subject_lessonplan` (
  `lessonplan_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `lessonplan_title` text NOT NULL,
  `subject_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `subject_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_dev_year_of_study`
--

CREATE TABLE `svcet_tbl_dev_year_of_study` (
  `year_of_study_id` int(11) NOT NULL,
  `sem_duration_id` int(11) NOT NULL,
  `academic_batch_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `year_of_study_title` varchar(255) NOT NULL,
  `year_of_study_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - Active, 0 - Inactive , 2 - Completed',
  `year_of_study_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `svcet_tbl_dev_year_of_study`
--

INSERT INTO `svcet_tbl_dev_year_of_study` (`year_of_study_id`, `sem_duration_id`, `academic_batch_id`, `academic_year_id`, `dept_id`, `year_of_study_title`, `year_of_study_status`, `year_of_study_delete`) VALUES
(1, 1, 1, 1, 1, 'IV', 1, 0),
(2, 2, 2, 2, 1, 'III', 1, 0),
(3, 3, 3, 3, 1, 'II', 1, 0),
(4, 4, 4, 4, 1, 'I', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_error_log`
--

CREATE TABLE `svcet_tbl_error_log` (
  `error_id` int(11) NOT NULL,
  `login_id` int(11) NOT NULL,
  `error_side` tinyint(1) NOT NULL COMMENT '1 - Client | 2 - Server | 3 - DB',
  `page_link` text NOT NULL,
  `error_message` text NOT NULL,
  `error_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_achievements`
--

CREATE TABLE `svcet_tbl_faculty_achievements` (
  `faculty_achievements_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `achievement_type` varchar(255) NOT NULL,
  `achievement_title` varchar(255) NOT NULL,
  `achievement_date` date NOT NULL,
  `achievement_venue` varchar(255) NOT NULL,
  `achievement_document` varchar(255) DEFAULT NULL,
  `achievement_status` tinyint(1) DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `achievement_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_authorities`
--

CREATE TABLE `svcet_tbl_faculty_authorities` (
  `faculty_authorities_id` int(11) NOT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `faculty_authorities_group_id` int(11) DEFAULT NULL COMMENT '1 - Principal | 2 - Vice Principal | 3 - Dean | 4 - HODs | 5 - Exam Cell Head | 6 - Admission Cell Head | 7 - Placement Cell Head',
  `dept_id` int(11) DEFAULT NULL,
  `effective_from` timestamp NOT NULL DEFAULT current_timestamp(),
  `effective_to` timestamp NULL DEFAULT NULL COMMENT 'Effective until when',
  `faculty_authorities_status` int(11) DEFAULT 1 COMMENT '1 - Active | 2 - Inactive | 3 - Completed',
  `faculty_authorities_deleted` int(11) DEFAULT 0 COMMENT '0 - Not Deleted | 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_class_advisors`
--

CREATE TABLE `svcet_tbl_faculty_class_advisors` (
  `faculty_class_advisors_id` int(11) NOT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `year_of_study_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `effective_from` timestamp NOT NULL DEFAULT current_timestamp(),
  `effective_to` timestamp NULL DEFAULT NULL COMMENT 'Effective until when',
  `faculty_class_advisors_status` int(11) DEFAULT 1 COMMENT '1 - Active | 2 - Inactive | 3 - Completed',
  `faculty_class_advisors_deleted` int(11) DEFAULT 0 COMMENT '0 - Not Deleted | 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_documents`
--

CREATE TABLE `svcet_tbl_faculty_documents` (
  `faculty_doc_id` int(11) NOT NULL,
  `faculty_doc_faculty_id` int(11) NOT NULL,
  `faculty_doc_type` tinyint(1) DEFAULT NULL COMMENT '1 - Resume | 2 - SSLC | 3 - HSC | 4 - Highest Qualification | 5 - Experience Certificate | 6 - Profile Pic',
  `faculty_doc_path` text DEFAULT NULL,
  `faculty_doc_status` tinyint(1) DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `faculty_doc_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_education`
--

CREATE TABLE `svcet_tbl_faculty_education` (
  `faculty_edu_id` int(11) NOT NULL,
  `faculty_edu_faculty_id` int(11) NOT NULL,
  `faculty_edu_level` tinyint(1) DEFAULT NULL COMMENT '1 - SSLC | 2 - HSC | 3 - Degrees',
  `faculty_edu_board` int(11) DEFAULT NULL,
  `faculty_edu_institution_name` varchar(255) DEFAULT NULL,
  `faculty_edu_degree` int(11) DEFAULT NULL,
  `faculty_edu_specialization` int(11) DEFAULT NULL,
  `faculty_edu_passed_out_year` year(4) DEFAULT NULL,
  `faculty_edu_cgpa` decimal(5,2) DEFAULT NULL,
  `faculty_edu_percentage` decimal(5,2) DEFAULT NULL,
  `faculty_edu_document` text DEFAULT NULL,
  `faculty_edu_verified_status` tinyint(1) DEFAULT 0 COMMENT '0 - Not verified, 1 - Verified'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_events`
--

CREATE TABLE `svcet_tbl_faculty_events` (
  `event_id` int(11) NOT NULL,
  `sem_duration_id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_description` text DEFAULT NULL,
  `event_start_date` date DEFAULT NULL,
  `event_end_date` date DEFAULT NULL,
  `event_type` int(11) NOT NULL,
  `event_status` tinyint(1) DEFAULT 1 COMMENT '1 - Active, 2 - Inactive',
  `event_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_event_mapping`
--

CREATE TABLE `svcet_tbl_faculty_event_mapping` (
  `event_mapping_id` int(11) NOT NULL,
  `sem_duration_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `academic_batch_id` int(11) DEFAULT NULL,
  `academic_year_id` int(11) DEFAULT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `year_of_study_id` int(11) DEFAULT NULL,
  `sem_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `event_mapping_status` tinyint(1) DEFAULT 1 COMMENT '1 - Active, 2 - Inactive',
  `event_mapping_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_exam_hall_arrangement`
--

CREATE TABLE `svcet_tbl_faculty_exam_hall_arrangement` (
  `arrangement_id` int(11) NOT NULL,
  `sem_duration_id` int(11) NOT NULL,
  `exam_subject_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `student_reg_number` varchar(20) NOT NULL,
  `seat_number` int(11) NOT NULL,
  `exam_hall_arrangement_status` tinyint(1) DEFAULT 1 COMMENT '1 - Active, 2 - Completed',
  `exam_hall_arrangement_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_exam_marks`
--

CREATE TABLE `svcet_tbl_faculty_exam_marks` (
  `exam_marks_id` int(11) NOT NULL,
  `sem_duration_id` int(11) NOT NULL,
  `exam_subject_id` int(11) NOT NULL,
  `student_reg_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_marks` decimal(5,2) NOT NULL,
  `marks_status` tinyint(1) DEFAULT 1 COMMENT '1 - Entered | 2 - Pending | 3 - Finalized',
  `result_status` tinyint(1) DEFAULT NULL COMMENT '1 - Pass | 2 - Fail',
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_exam_slots`
--

CREATE TABLE `svcet_tbl_faculty_exam_slots` (
  `exam_slots_id` int(11) NOT NULL,
  `sem_duration_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `exam_code` int(11) NOT NULL,
  `exam_starting_date` date NOT NULL,
  `exam_ending_date` date NOT NULL,
  `exam_session` tinyint(1) NOT NULL COMMENT '1 - FN (Forenoon), 2 - AN (Afternoon)',
  `exam_start_time` time NOT NULL,
  `exam_end_time` time NOT NULL,
  `exam_status` tinyint(1) DEFAULT 1 COMMENT '1 - Active, 2 - Completed',
  `exam_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_exam_subject`
--

CREATE TABLE `svcet_tbl_faculty_exam_subject` (
  `exam_subject_id` int(11) NOT NULL,
  `sem_duration_id` int(11) NOT NULL,
  `exam_slots_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `exam_code` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `academic_batch_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `year_of_study_id` int(11) NOT NULL,
  `sem_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `exam_date` date NOT NULL,
  `exam_status` tinyint(1) DEFAULT 1 COMMENT '1 - Active, 2 - Completed',
  `exam_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_experience`
--

CREATE TABLE `svcet_tbl_faculty_experience` (
  `faculty_exp_id` int(11) NOT NULL,
  `faculty_exp_faculty_id` int(11) NOT NULL,
  `faculty_exp_field_of_experience` tinyint(1) DEFAULT NULL COMMENT '1 - Industry | 2 - Institution Type of experience: Industry or Institution',
  `faculty_exp_industry_name` varchar(255) DEFAULT NULL,
  `faculty_exp_designation` varchar(100) DEFAULT NULL,
  `faculty_exp_specialization` varchar(100) DEFAULT NULL,
  `faculty_exp_start_date` date DEFAULT NULL,
  `faculty_exp_end_date` date DEFAULT NULL,
  `faculty_exp_status` tinyint(1) DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `faculty_exp_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_material`
--

CREATE TABLE `svcet_tbl_faculty_material` (
  `material_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `material_type` int(11) NOT NULL,
  `material_path` text NOT NULL,
  `academic_batch_id` int(11) DEFAULT NULL,
  `academic_year_id` int(11) DEFAULT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `year_of_study_id` int(11) DEFAULT NULL,
  `sem_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `material_status` tinyint(1) DEFAULT 1 COMMENT '1 - Active, 2 - Inactive',
  `material_mapping_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_mentor`
--

CREATE TABLE `svcet_tbl_faculty_mentor` (
  `faculty_mentor_id` int(11) NOT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `effective_from` timestamp NOT NULL DEFAULT current_timestamp(),
  `effective_to` timestamp NULL DEFAULT NULL COMMENT 'Effective until when',
  `faculty_mentor_status` int(11) DEFAULT 1 COMMENT '1 - Active | 2 - Inactive | 3 - Completed',
  `faculty_mentor_deleted` int(11) DEFAULT 0 COMMENT '0 - Not Deleted | 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_official_details`
--

CREATE TABLE `svcet_tbl_faculty_official_details` (
  `faculty_official_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `effective_from` timestamp NOT NULL DEFAULT current_timestamp(),
  `effective_to` timestamp NULL DEFAULT NULL,
  `faculty_joining_date` date DEFAULT NULL,
  `faculty_official_deleted` int(11) DEFAULT 0 COMMENT '0 - not deleted | 1 - deleted',
  `designation` int(11) DEFAULT NULL,
  `faculty_salary` decimal(10,2) DEFAULT NULL,
  `faculty_official_details_status` tinyint(1) DEFAULT 1 COMMENT '1 - Active | 2 - Inactive | 3 - Completed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `svcet_tbl_faculty_official_details`
--

INSERT INTO `svcet_tbl_faculty_official_details` (`faculty_official_id`, `faculty_id`, `dept_id`, `effective_from`, `effective_to`, `faculty_joining_date`, `faculty_official_deleted`, `designation`, `faculty_salary`, `faculty_official_details_status`) VALUES
(1, 67, 1, '2024-12-03 07:29:05', NULL, NULL, 0, NULL, NULL, 1),
(2, 68, 2, '2024-12-03 07:30:01', NULL, NULL, 0, NULL, NULL, 1),
(3, 69, 4, '2024-12-03 07:31:22', NULL, NULL, 0, NULL, NULL, 1),
(4, 70, 3, '2024-12-03 07:32:29', NULL, NULL, 0, NULL, NULL, 1),
(5, 71, 5, '2024-12-03 07:33:36', NULL, NULL, 0, NULL, NULL, 1),
(6, 72, 6, '2024-12-03 07:38:27', NULL, NULL, 0, NULL, NULL, 1),
(7, 73, 7, '2024-12-03 07:39:29', NULL, NULL, 0, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_personal_info`
--

CREATE TABLE `svcet_tbl_faculty_personal_info` (
  `faculty_id` int(11) NOT NULL,
  `faculty_account_id` int(11) NOT NULL,
  `faculty_first_name` varchar(100) NOT NULL,
  `faculty_middle_name` varchar(100) NOT NULL,
  `faculty_last_name` varchar(100) NOT NULL,
  `faculty_initial` varchar(10) NOT NULL,
  `faculty_salutation` int(11) DEFAULT NULL,
  `faculty_dob` date DEFAULT NULL,
  `faculty_gender` int(11) DEFAULT NULL,
  `faculty_blood_group` int(11) DEFAULT NULL,
  `faculty_religion` int(11) DEFAULT NULL,
  `faculty_caste` int(11) DEFAULT NULL,
  `faculty_community` int(11) DEFAULT NULL,
  `faculty_nationality` int(11) DEFAULT NULL,
  `faculty_aadhar_number` varchar(15) DEFAULT NULL,
  `faculty_marital_status` int(11) DEFAULT NULL,
  `faculty_mobile_number` varchar(15) DEFAULT NULL,
  `faculty_alternative_contact_number` varchar(15) DEFAULT NULL,
  `faculty_whatsapp_number` varchar(15) DEFAULT NULL,
  `faculty_personal_mail_id` varchar(255) DEFAULT NULL,
  `faculty_official_mail_id` varchar(255) DEFAULT NULL,
  `faculty_address_no` varchar(20) DEFAULT NULL,
  `faculty_address_street` varchar(255) DEFAULT NULL,
  `faculty_address_locality` varchar(100) DEFAULT NULL,
  `faculty_address_pincode` varchar(10) DEFAULT NULL,
  `faculty_address_city` varchar(100) DEFAULT NULL,
  `faculty_address_district` varchar(100) DEFAULT NULL,
  `faculty_address_state` varchar(100) DEFAULT NULL,
  `faculty_address_country` varchar(100) DEFAULT NULL,
  `faculty_reference` int(11) DEFAULT NULL,
  `faculty_status` tinyint(1) DEFAULT 0 COMMENT '0 - Pending |1 - Active, 2 - Inactive | 3 - Relieved',
  `faculty_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `svcet_tbl_faculty_personal_info`
--

INSERT INTO `svcet_tbl_faculty_personal_info` (`faculty_id`, `faculty_account_id`, `faculty_first_name`, `faculty_middle_name`, `faculty_last_name`, `faculty_initial`, `faculty_salutation`, `faculty_dob`, `faculty_gender`, `faculty_blood_group`, `faculty_religion`, `faculty_caste`, `faculty_community`, `faculty_nationality`, `faculty_aadhar_number`, `faculty_marital_status`, `faculty_mobile_number`, `faculty_alternative_contact_number`, `faculty_whatsapp_number`, `faculty_personal_mail_id`, `faculty_official_mail_id`, `faculty_address_no`, `faculty_address_street`, `faculty_address_locality`, `faculty_address_pincode`, `faculty_address_city`, `faculty_address_district`, `faculty_address_state`, `faculty_address_country`, `faculty_reference`, `faculty_status`, `faculty_deleted`) VALUES
(1, 1, 'Pradeep Devaneyan', '', '', 'S', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(2, 2, 'Jayarraman', '', '', 'K B', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(3, 3, 'Balaji', '', '', 'N', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(4, 4, 'Magimai Raj', '', '', 'B', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(5, 5, 'Nataraj', '', '', 'M', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(6, 6, 'Ravindran', '', '', 'S', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(7, 7, 'Kamalanathan', '', '', 'R', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(8, 8, 'Palanivel', '', '', 'G', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(9, 9, 'Karthikeyan', '', '', 'V', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(10, 10, 'Manikandan', '', '', 'C', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(11, 11, 'Andal', '', '', 'K', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(12, 12, 'Vinitha', '', '', 'S', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(13, 13, 'Dhanasekaran', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(14, 14, 'Pavithra', '', '', 'S', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(15, 15, 'Saranya', '', 'Vadivelu', 'V', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(16, 16, 'Sathiya', '', '', 'L', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(17, 17, 'Rajeshwari', '', '', 'G', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(18, 18, 'Nagamani Abirami', '', '', 'D', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(19, 19, 'Lavanya', '', '', 'M', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(20, 20, 'Nagaraj', '', '', 'V', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(21, 21, 'Amuthavalli', '', '', 'G', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(22, 22, 'Mayavathi', '', '', 'K', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(23, 23, 'Sujatha', '', '', 'K', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(24, 24, 'Devanathan', '', '', 'D', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(25, 25, 'Kishore', '', '', 'K', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(26, 26, 'Rajasekar', '', '', 'R', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(27, 27, 'Anandharaj', '', '', 'J', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(28, 28, 'Thamizh Selvi', '', '', 'M', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(29, 29, 'Vengadesan', '', '', 'A', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(30, 30, 'Balaji', '', '', 'S', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(31, 31, 'Rajalakshmi', '', '', 'A', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(32, 32, 'Sowmiya', '', '', 'M', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(33, 33, 'Kavinilavu', '', '', 'A', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(34, 34, 'Jayasubitha', '', '', 'J', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(35, 35, 'Thangapriya', '', '', 'S', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(36, 36, 'Venkedesh', '', '', 'R', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(37, 37, 'Meganathan', '', '', 'P', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(38, 38, 'Murali', '', '', 'M', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(39, 39, 'Sandou Louis Kichor', '', '', 'C', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(40, 40, 'Selvakumar', '', '', 'L', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(41, 41, 'Punitha', '', '', 'S', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(42, 42, 'Bharathan', '', '', 'R', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(43, 43, 'Ganesan', '', '', 'V', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(44, 44, 'Kavitha', '', '', 'A', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(45, 45, 'John Sundar', '', '', 'C', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(46, 46, 'Thirumarran', '', '', 'M', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(47, 47, 'Mythili', '', '', 'N', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(48, 48, 'Ganthimathi', '', '', 'P', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(49, 49, 'Ilamathi', '', '', 'V', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(50, 50, 'Lakshmi Priya', '', '', 'K', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(51, 51, 'Anbukarasi', '', '', 'V', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(52, 52, 'Balaji', '', '', 'B', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(53, 53, 'Parthasaradhy', '', '', 'T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(54, 54, 'Anitha', '', '', 'A', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(55, 55, 'Sasikala', '', '', 'P', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(56, 56, 'Rohini', '', '', 'S', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(57, 57, 'Lalitha', '', '', 'A', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(58, 58, 'Dhanavathy', '', '', 'K', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(59, 59, 'Shanthini', '', '', 'G', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(60, 60, 'Subramanian', '', '', 'V', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(61, 61, 'Santhalakshmi', '', '', 'R', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(62, 62, 'Kumaravel', '', '', 'R', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(63, 63, 'Principal', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(64, 64, 'Vice Principal', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(65, 65, 'Dean - Academics', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(66, 66, 'Dean - IQAC', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(67, 67, 'Head Of The Department', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(68, 68, 'Head Of The Department', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(69, 69, 'Head Of The Department', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(70, 70, 'Head Of The Department', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(71, 71, 'Head Of The Department', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(72, 72, 'Head Of The Department', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(73, 73, 'Head Of The Department', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_roles_and_responsibilities`
--

CREATE TABLE `svcet_tbl_faculty_roles_and_responsibilities` (
  `faculty_roles_and_responsibilities_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `committee_title` int(11) DEFAULT NULL,
  `committee_role` mediumint(9) DEFAULT NULL COMMENT '1 - Head | 2 - Co Ordinator | 3 - Associate Co Ordinator | 4 - Member',
  `effective_from` date DEFAULT current_timestamp(),
  `effective_to` date DEFAULT NULL,
  `roles_and_responsibilities_status` tinyint(1) DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `roles_and_responsibilities_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_room_stock`
--

CREATE TABLE `svcet_tbl_faculty_room_stock` (
  `stock_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_unit_of_measure` varchar(50) NOT NULL,
  `item_quantity` int(11) NOT NULL,
  `item_quality` tinyint(1) DEFAULT 1 COMMENT '1 - Good, 2 - Average, 3 - Bad',
  `item_description` text DEFAULT NULL,
  `stock_status` tinyint(1) DEFAULT 1 COMMENT '1 - Active, 2 - Inactive',
  `stock_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_room_stock_transaction`
--

CREATE TABLE `svcet_tbl_faculty_room_stock_transaction` (
  `stock_transaction_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_unit_of_measure` varchar(50) NOT NULL,
  `item_quantity` int(11) NOT NULL,
  `to_room` int(11) DEFAULT NULL,
  `transaction_date` date NOT NULL,
  `transaction_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `stock_transaction_status` tinyint(1) DEFAULT 1 COMMENT '1 - In, 2 - Out',
  `stock_transaction_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_skills`
--

CREATE TABLE `svcet_tbl_faculty_skills` (
  `faculty_skill_id` int(11) NOT NULL,
  `faculty_skill_faculty_id` int(11) NOT NULL,
  `faculty_skill_type` tinyint(1) DEFAULT NULL COMMENT '1 - Core Expertise | 2 - Interest | 3 - Software Skill | 4 - Language ',
  `faculty_skill_name` varchar(255) DEFAULT NULL,
  `faculty_skill_status` tinyint(1) DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `faculty_skill_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_subjects`
--

CREATE TABLE `svcet_tbl_faculty_subjects` (
  `faculty_subjects_id` int(11) NOT NULL,
  `sem_duration_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `academic_batch_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `year_of_study_id` int(11) NOT NULL,
  `sem_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `subject_no_of_periods` int(11) NOT NULL DEFAULT 0,
  `faculty_subjects_status` tinyint(1) DEFAULT 1 COMMENT 'Status of the faculty_subjectsship (1 - Active, 0 - Inactive)',
  `faculty_subjects_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_subject_attendance`
--

CREATE TABLE `svcet_tbl_faculty_subject_attendance` (
  `attendance_id` int(11) NOT NULL,
  `sem_duration_id` int(11) NOT NULL,
  `attendance_transaction_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `attendance` tinyint(1) NOT NULL COMMENT '1 - Present, 2 - Absent, 3 - On Duty',
  `attendance_confirmation_status` tinyint(1) DEFAULT 1 COMMENT '1 - none, 2 - Authorised, 3 - Unauthorised',
  `attendance_status` tinyint(1) DEFAULT 1 COMMENT '1 - Active, 2 - Inactive',
  `attendance_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_subject_attendance_transaction`
--

CREATE TABLE `svcet_tbl_faculty_subject_attendance_transaction` (
  `attendance_transaction_id` int(11) NOT NULL,
  `sem_duration_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `academic_batch_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `year_of_study_id` int(11) NOT NULL,
  `sem_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `period_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `attendance_transaction_status` tinyint(1) DEFAULT 1 COMMENT '1 - Active, 2 - Inactive',
  `attendance_transaction_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_faculty_timetable`
--

CREATE TABLE `svcet_tbl_faculty_timetable` (
  `timetable_id` int(11) NOT NULL,
  `faculty_subjects_id` int(11) NOT NULL,
  `day_id` int(11) NOT NULL COMMENT '1 - Monday, 2 - Tuesday, 3 - Wednesday, 4 - Thursday, 5 - Friday, 6 - Saturday, 7 - Sunday',
  `period_id` int(11) NOT NULL,
  `period_date` date NOT NULL,
  `timetable_status` tinyint(1) DEFAULT 1 COMMENT '1 - pending, 2 - saved, 3 - finalized, 4 - inactive',
  `timetable_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_login_logs`
--

CREATE TABLE `svcet_tbl_login_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` datetime DEFAULT NULL,
  `logout_time` datetime DEFAULT NULL,
  `user_ip_address` varchar(45) DEFAULT NULL,
  `successful_login` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Failed Login, 1 - Successful Login',
  `login_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - logout | 1 - Login | 2 - mismatches',
  `created_on` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_parent_personal_info`
--

CREATE TABLE `svcet_tbl_parent_personal_info` (
  `parent_id` int(11) NOT NULL,
  `parent_account_id` int(11) NOT NULL,
  `parent_first_name` varchar(100) NOT NULL,
  `parent_middle_name` varchar(255) DEFAULT NULL,
  `parent_last_name` varchar(100) NOT NULL,
  `parent_initial` varchar(100) NOT NULL,
  `parent_mobile_number` varchar(15) NOT NULL,
  `parent_email_id` varchar(100) DEFAULT NULL,
  `parent_address` text DEFAULT NULL,
  `parent_status` tinyint(1) DEFAULT 0 COMMENT '0 - Pending | 1 - Active, 2 - Inactive',
  `parent_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_student_admission_info`
--

CREATE TABLE `svcet_tbl_student_admission_info` (
  `admission_id` int(11) NOT NULL,
  `student_admission_student_id` int(11) NOT NULL,
  `student_admission_type` tinyint(1) DEFAULT 1 COMMENT '1 - New Admission | 2 - Lateral Entry',
  `student_admission_category` tinyint(1) DEFAULT 1 COMMENT '1 - Centac | 2 - Management',
  `student_hostel` tinyint(1) DEFAULT 0 COMMENT '1 - Yes | 0 - No',
  `student_admission_know_about_us` int(20) DEFAULT NULL COMMENT '1 = Friends or Family\r\n-- 2 = Social Media\r\n-- 3 = Website\r\n-- 4 = Advertisement\r\n-- 5 = Events or Workshops\r\n-- 6 = Other',
  `student_transport` tinyint(1) DEFAULT 0 COMMENT '1 - Yes | 0 - No',
  `student_reference` int(11) DEFAULT NULL,
  `student_admission_reg_no` varchar(50) DEFAULT NULL,
  `student_course_preference1` int(11) DEFAULT NULL COMMENT 'First Course Preference',
  `student_course_preference2` int(11) DEFAULT NULL COMMENT 'Second Course Preference',
  `student_course_preference3` int(11) DEFAULT NULL COMMENT 'Third Course Preference',
  `student_concession_subject` int(20) DEFAULT NULL COMMENT 'This column stores the type of concession a student is eligible for. 6 = None, 1 = Scholarship, 2 = Government Subsidy, 3 = Sports Quota, 4 = Cultural Quota, 5 = Financial Aid',
  `student_concession_body` text DEFAULT NULL,
  `admission_status` tinyint(1) DEFAULT 0 COMMENT '0 - Enquiry Form| 1 - Admitted| 2 - Active| 3 - Inactive | 4 - Discontinued',
  `admission_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted | 1 - Deleted',
  `lateral_entry_year_of_study` int(4) DEFAULT NULL COMMENT 'Year of study for lateral entry students',
  `student_admission_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_student_committee`
--

CREATE TABLE `svcet_tbl_student_committee` (
  `student_committee_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `committee_title` int(11) DEFAULT NULL,
  `committee_role` mediumint(9) DEFAULT NULL COMMENT '1 - Head | 2 - Co Ordinator | 3 - Associate Co Ordinator | 4 - Member',
  `effective_from` date DEFAULT current_timestamp(),
  `effective_to` date DEFAULT NULL,
  `committee_status` tinyint(1) DEFAULT 1 COMMENT '1 - Active, 0 - Inactive',
  `committee_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_student_documents`
--

CREATE TABLE `svcet_tbl_student_documents` (
  `student_doc_id` int(11) NOT NULL,
  `student_doc_student_id` int(11) NOT NULL,
  `student_doc_type` tinyint(1) DEFAULT NULL COMMENT '1 - SSLC | 2 - HSC | 3 - Highest Qualification | 4 - TC | 5 - PIC | 6 - Community | 7 - residence ',
  `student_doc_path` text DEFAULT NULL,
  `student_doc_status` tinyint(1) DEFAULT 1 COMMENT 'Status of the document record (1 - Active, 0 - Inactive)',
  `student_doc_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_student_education`
--

CREATE TABLE `svcet_tbl_student_education` (
  `student_edu_id` int(11) NOT NULL,
  `student_edu_student_id` int(11) NOT NULL,
  `student_edu_level` tinyint(1) DEFAULT NULL COMMENT '1 - SSLC | 2 - HSC | 3 - Diploma | 4 - UG | 5 - PG ',
  `student_edu_board` int(11) DEFAULT NULL,
  `student_edu_institution_name` varchar(255) DEFAULT NULL,
  `student_edu_degree` int(11) DEFAULT NULL,
  `student_edu_specialization` int(11) DEFAULT NULL,
  `student_edu_passed_out_year` year(4) DEFAULT NULL,
  `student_edu_percentage` decimal(5,2) DEFAULT NULL,
  `student_edu_cgpa` decimal(3,2) DEFAULT NULL,
  `student_edu_status` tinyint(1) DEFAULT 1 COMMENT 'Status of the education record (1 - Active, 0 - Inactive)',
  `student_edu_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted',
  `student_edu_total_mark` int(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_student_leave_application`
--

CREATE TABLE `svcet_tbl_student_leave_application` (
  `leave_id` int(11) NOT NULL,
  `sem_duration_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `leave_start_date` date NOT NULL,
  `leave_end_date` date DEFAULT NULL,
  `leave_reason` int(11) NOT NULL,
  `mentor_status` tinyint(1) DEFAULT 1 COMMENT '1 - Pending, 2 - Approved, 3 - Rejected, 4 - disabled',
  `mentor_description` text DEFAULT NULL,
  `class_advisor_status` tinyint(1) DEFAULT 1 COMMENT '1 - Pending, 2 - Approved, 3 - Rejected, 4 - disabled',
  `class_advisor_description` text DEFAULT NULL,
  `hod_status` tinyint(1) DEFAULT 1 COMMENT '1 - Pending, 2 - Approved, 3 - Rejected, 4 - disabled',
  `hod_description` text DEFAULT NULL,
  `leave_status` tinyint(1) DEFAULT 1 COMMENT '1 - Authorised, 2 - Unauthorised',
  `document_path` text DEFAULT NULL COMMENT 'Path of the document uploaded by the student',
  `document_status` tinyint(1) DEFAULT 1 COMMENT '1 - Authorised, 2 - Unauthorised',
  `applied_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `leave_application_status` tinyint(1) DEFAULT 1 COMMENT '1 - Active, 2 - Cancelled',
  `leave_application_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_student_official_details`
--

CREATE TABLE `svcet_tbl_student_official_details` (
  `student_official_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `sem_duration_id` int(11) DEFAULT NULL,
  `student_reg_number` varchar(20) DEFAULT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `academic_batch_id` int(11) DEFAULT NULL,
  `academic_year_id` int(11) DEFAULT NULL,
  `year_of_study_id` int(11) DEFAULT NULL,
  `sem_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `class_advisor_id` int(11) DEFAULT NULL,
  `mentor_id` int(11) DEFAULT NULL,
  `hod_id` int(11) DEFAULT NULL,
  `student_official_details_status` tinyint(1) DEFAULT 1 COMMENT '1 - Active, 2 - Inactive | 3 - Completed',
  `student_official_details_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_student_parent_relation`
--

CREATE TABLE `svcet_tbl_student_parent_relation` (
  `relation_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `relationship_type` tinyint(1) NOT NULL COMMENT 'Type of relationship between student and parent (1 - Father, 2 - Mother, 3 - Brother, 4 - Sister)',
  `relation_status` tinyint(1) DEFAULT 1 COMMENT 'Status of the relationship (1 - Active, 0 - Inactive)',
  `relation_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_student_personal_info`
--

CREATE TABLE `svcet_tbl_student_personal_info` (
  `student_id` int(11) NOT NULL,
  `student_account_id` int(11) DEFAULT NULL,
  `student_first_name` varchar(100) NOT NULL,
  `student_middle_name` varchar(100) NOT NULL,
  `student_last_name` varchar(100) NOT NULL,
  `student_initial` varchar(10) NOT NULL,
  `student_dob` date DEFAULT NULL,
  `student_gender` int(11) DEFAULT NULL,
  `student_blood_group` int(11) DEFAULT NULL,
  `student_religion` int(11) DEFAULT NULL,
  `student_caste` int(11) DEFAULT NULL,
  `student_community` int(11) DEFAULT NULL,
  `student_nationality` int(11) DEFAULT NULL,
  `student_aadhar_number` varchar(15) DEFAULT NULL,
  `student_marital_status` int(11) DEFAULT NULL,
  `student_mobile_number` varchar(15) DEFAULT NULL,
  `student_alternative_contact_number` varchar(15) DEFAULT NULL,
  `student_whatsapp_number` varchar(15) DEFAULT NULL,
  `student_email_id` varchar(100) DEFAULT NULL,
  `student_address_no` varchar(20) DEFAULT NULL,
  `student_address_street` varchar(255) DEFAULT NULL,
  `student_address_locality` varchar(100) DEFAULT NULL,
  `student_address_pincode` varchar(10) DEFAULT NULL,
  `student_address_city` varchar(100) DEFAULT NULL,
  `student_address_district` varchar(100) DEFAULT NULL,
  `student_address_state` varchar(100) DEFAULT NULL,
  `student_address_country` varchar(100) DEFAULT NULL,
  `student_father_name` varchar(100) DEFAULT NULL,
  `student_father_occupation` varchar(100) DEFAULT NULL,
  `student_mother_name` varchar(100) DEFAULT NULL,
  `student_mother_occupation` varchar(100) DEFAULT NULL,
  `student_guardian_name` varchar(255) DEFAULT NULL,
  `student_guardian_occupation` varchar(255) DEFAULT NULL,
  `student_admission_type` tinyint(1) DEFAULT 1 COMMENT '1 - New Admission | 2 - Lateral Entry',
  `student_admission_category` tinyint(1) DEFAULT 1 COMMENT '1 - Centac | 2 - Management',
  `student_hostel` tinyint(1) DEFAULT 2 COMMENT '1 - Yes | 2 - No Hostel accommodation status',
  `student_transport` tinyint(1) DEFAULT 2 COMMENT '1 - Yes | 2 - NoTransport requirement status',
  `student_reference` int(11) DEFAULT NULL,
  `student_course_preference` int(11) DEFAULT NULL,
  `student_concession_subject` varchar(255) DEFAULT NULL,
  `student_concession_body` text DEFAULT NULL,
  `student_concession_document` text DEFAULT NULL,
  `student_status` tinyint(1) DEFAULT 0 COMMENT '0 - Pending | 1 - Active | 2 - Inactive | 3 - Discontinued',
  `student_deleted` tinyint(1) DEFAULT 0 COMMENT '0 - Not Deleted, 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_student_representative`
--

CREATE TABLE `svcet_tbl_student_representative` (
  `student_representative_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `year_of_study_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `effective_from` timestamp NOT NULL DEFAULT current_timestamp(),
  `effective_to` timestamp NULL DEFAULT NULL,
  `student_representative_status` int(11) NOT NULL DEFAULT 1 COMMENT '1 - Active | 2 - Inactive | 3 - Completed',
  `student_representative_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - Not Deleted | 1 - Deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `svcet_tbl_user_activity_log`
--

CREATE TABLE `svcet_tbl_user_activity_log` (
  `activity_id` int(11) NOT NULL,
  `login_id` int(11) NOT NULL,
  `db_table_affected` varchar(255) NOT NULL,
  `action_type` tinyint(1) NOT NULL COMMENT '1 - Fetch, 2 - Insert, 3 - Update, 4 - Delete',
  `activity_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `svcet_tbl_accounts`
--
ALTER TABLE `svcet_tbl_accounts`
  ADD PRIMARY KEY (`account_id`);

--
-- Indexes for table `svcet_tbl_dev_academic_batch`
--
ALTER TABLE `svcet_tbl_dev_academic_batch`
  ADD PRIMARY KEY (`academic_batch_id`);

--
-- Indexes for table `svcet_tbl_dev_academic_year`
--
ALTER TABLE `svcet_tbl_dev_academic_year`
  ADD PRIMARY KEY (`academic_year_id`);

--
-- Indexes for table `svcet_tbl_dev_attendance`
--
ALTER TABLE `svcet_tbl_dev_attendance`
  ADD PRIMARY KEY (`attendance_dev_id`);

--
-- Indexes for table `svcet_tbl_dev_day`
--
ALTER TABLE `svcet_tbl_dev_day`
  ADD PRIMARY KEY (`day_id`);

--
-- Indexes for table `svcet_tbl_dev_dept`
--
ALTER TABLE `svcet_tbl_dev_dept`
  ADD PRIMARY KEY (`dept_id`);

--
-- Indexes for table `svcet_tbl_dev_exam_management`
--
ALTER TABLE `svcet_tbl_dev_exam_management`
  ADD PRIMARY KEY (`exam_id`),
  ADD UNIQUE KEY `exam_title` (`exam_title`),
  ADD UNIQUE KEY `exam_short_name` (`exam_short_name`);

--
-- Indexes for table `svcet_tbl_dev_general`
--
ALTER TABLE `svcet_tbl_dev_general`
  ADD PRIMARY KEY (`general_id`);

--
-- Indexes for table `svcet_tbl_dev_group`
--
ALTER TABLE `svcet_tbl_dev_group`
  ADD PRIMARY KEY (`group_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `academic_batch_id` (`academic_batch_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `year_of_study_id` (`year_of_study_id`),
  ADD KEY `sem_id` (`sem_id`),
  ADD KEY `dept_id` (`dept_id`),
  ADD KEY `svcet_tbl_dev_group_ibfk_7` (`sem_duration_id`);

--
-- Indexes for table `svcet_tbl_dev_institution`
--
ALTER TABLE `svcet_tbl_dev_institution`
  ADD PRIMARY KEY (`institution_id`);

--
-- Indexes for table `svcet_tbl_dev_pages`
--
ALTER TABLE `svcet_tbl_dev_pages`
  ADD PRIMARY KEY (`page_id`);

--
-- Indexes for table `svcet_tbl_dev_period_time`
--
ALTER TABLE `svcet_tbl_dev_period_time`
  ADD PRIMARY KEY (`period_id`),
  ADD KEY `dept_id` (`dept_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `academic_batch_id` (`academic_batch_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `year_of_study_id` (`year_of_study_id`),
  ADD KEY `sem_id` (`sem_id`),
  ADD KEY `svcet_tbl_dev_period_time_ibfk_8` (`sem_duration_id`);

--
-- Indexes for table `svcet_tbl_dev_prefixes`
--
ALTER TABLE `svcet_tbl_dev_prefixes`
  ADD PRIMARY KEY (`prefixes_id`);

--
-- Indexes for table `svcet_tbl_dev_roles`
--
ALTER TABLE `svcet_tbl_dev_roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `svcet_tbl_dev_role_permission`
--
ALTER TABLE `svcet_tbl_dev_role_permission`
  ADD PRIMARY KEY (`role_perm_id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `page_id` (`page_id`);

--
-- Indexes for table `svcet_tbl_dev_room_management`
--
ALTER TABLE `svcet_tbl_dev_room_management`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `room_number` (`room_number`),
  ADD KEY `room_dept_id` (`room_dept_id`);

--
-- Indexes for table `svcet_tbl_dev_section`
--
ALTER TABLE `svcet_tbl_dev_section`
  ADD PRIMARY KEY (`section_id`),
  ADD KEY `academic_batch_id` (`academic_batch_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `year_of_study_id` (`year_of_study_id`),
  ADD KEY `sem_id` (`sem_id`),
  ADD KEY `dept_id` (`dept_id`),
  ADD KEY `svcet_tbl_dev_section_ibfk_6` (`sem_duration_id`);

--
-- Indexes for table `svcet_tbl_dev_sem`
--
ALTER TABLE `svcet_tbl_dev_sem`
  ADD PRIMARY KEY (`sem_id`),
  ADD KEY `academic_batch_id` (`academic_batch_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `year_of_study_id` (`year_of_study_id`),
  ADD KEY `dept_id` (`dept_id`),
  ADD KEY `svcet_tbl_dev_sem_ibfk_5` (`sem_duration_id`);

--
-- Indexes for table `svcet_tbl_dev_sem_duration`
--
ALTER TABLE `svcet_tbl_dev_sem_duration`
  ADD PRIMARY KEY (`sem_duration_id`);

--
-- Indexes for table `svcet_tbl_dev_stock_details`
--
ALTER TABLE `svcet_tbl_dev_stock_details`
  ADD PRIMARY KEY (`stock_id`),
  ADD UNIQUE KEY `item_code` (`item_code`);

--
-- Indexes for table `svcet_tbl_dev_subject`
--
ALTER TABLE `svcet_tbl_dev_subject`
  ADD PRIMARY KEY (`subject_id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `academic_batch_id` (`academic_batch_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `year_of_study_id` (`year_of_study_id`),
  ADD KEY `sem_id` (`sem_id`),
  ADD KEY `dept_id` (`dept_id`);

--
-- Indexes for table `svcet_tbl_dev_subject_lessonplan`
--
ALTER TABLE `svcet_tbl_dev_subject_lessonplan`
  ADD PRIMARY KEY (`lessonplan_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `svcet_tbl_dev_year_of_study`
--
ALTER TABLE `svcet_tbl_dev_year_of_study`
  ADD PRIMARY KEY (`year_of_study_id`),
  ADD KEY `academic_batch_id` (`academic_batch_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `dept_id` (`dept_id`),
  ADD KEY `svcet_tbl_dev_year_of_study_ibfk_4` (`sem_duration_id`);

--
-- Indexes for table `svcet_tbl_error_log`
--
ALTER TABLE `svcet_tbl_error_log`
  ADD PRIMARY KEY (`error_id`),
  ADD KEY `login_id` (`login_id`);

--
-- Indexes for table `svcet_tbl_faculty_achievements`
--
ALTER TABLE `svcet_tbl_faculty_achievements`
  ADD PRIMARY KEY (`faculty_achievements_id`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `svcet_tbl_faculty_authorities`
--
ALTER TABLE `svcet_tbl_faculty_authorities`
  ADD PRIMARY KEY (`faculty_authorities_id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `dept_id` (`dept_id`);

--
-- Indexes for table `svcet_tbl_faculty_class_advisors`
--
ALTER TABLE `svcet_tbl_faculty_class_advisors`
  ADD PRIMARY KEY (`faculty_class_advisors_id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `dept_id` (`dept_id`),
  ADD KEY `year_of_study_id` (`year_of_study_id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `svcet_tbl_faculty_documents`
--
ALTER TABLE `svcet_tbl_faculty_documents`
  ADD PRIMARY KEY (`faculty_doc_id`),
  ADD KEY `faculty_doc_faculty_id` (`faculty_doc_faculty_id`);

--
-- Indexes for table `svcet_tbl_faculty_education`
--
ALTER TABLE `svcet_tbl_faculty_education`
  ADD PRIMARY KEY (`faculty_edu_id`),
  ADD KEY `faculty_edu_faculty_id` (`faculty_edu_faculty_id`),
  ADD KEY `faculty_edu_degree` (`faculty_edu_degree`),
  ADD KEY `faculty_edu_specialization` (`faculty_edu_specialization`);

--
-- Indexes for table `svcet_tbl_faculty_events`
--
ALTER TABLE `svcet_tbl_faculty_events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `sem_duration_id` (`sem_duration_id`),
  ADD KEY `event_type` (`event_type`);

--
-- Indexes for table `svcet_tbl_faculty_event_mapping`
--
ALTER TABLE `svcet_tbl_faculty_event_mapping`
  ADD PRIMARY KEY (`event_mapping_id`),
  ADD KEY `sem_duration_id` (`sem_duration_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `academic_batch_id` (`academic_batch_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `dept_id` (`dept_id`),
  ADD KEY `year_of_study_id` (`year_of_study_id`),
  ADD KEY `sem_id` (`sem_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `svcet_tbl_faculty_exam_hall_arrangement`
--
ALTER TABLE `svcet_tbl_faculty_exam_hall_arrangement`
  ADD PRIMARY KEY (`arrangement_id`),
  ADD KEY `sem_duration_id` (`sem_duration_id`),
  ADD KEY `exam_subject_id` (`exam_subject_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `svcet_tbl_faculty_exam_marks`
--
ALTER TABLE `svcet_tbl_faculty_exam_marks`
  ADD PRIMARY KEY (`exam_marks_id`),
  ADD KEY `sem_duration_id` (`sem_duration_id`),
  ADD KEY `exam_subject_id` (`exam_subject_id`);

--
-- Indexes for table `svcet_tbl_faculty_exam_slots`
--
ALTER TABLE `svcet_tbl_faculty_exam_slots`
  ADD PRIMARY KEY (`exam_slots_id`),
  ADD UNIQUE KEY `exam_code` (`exam_code`),
  ADD KEY `sem_duration_id` (`sem_duration_id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `svcet_tbl_faculty_exam_subject`
--
ALTER TABLE `svcet_tbl_faculty_exam_subject`
  ADD PRIMARY KEY (`exam_subject_id`),
  ADD UNIQUE KEY `exam_code` (`exam_code`),
  ADD KEY `sem_duration_id` (`sem_duration_id`),
  ADD KEY `exam_slots_id` (`exam_slots_id`),
  ADD KEY `academic_batch_id` (`academic_batch_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `dept_id` (`dept_id`),
  ADD KEY `year_of_study_id` (`year_of_study_id`),
  ADD KEY `sem_id` (`sem_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `svcet_tbl_faculty_experience`
--
ALTER TABLE `svcet_tbl_faculty_experience`
  ADD PRIMARY KEY (`faculty_exp_id`),
  ADD KEY `faculty_exp_faculty_id` (`faculty_exp_faculty_id`);

--
-- Indexes for table `svcet_tbl_faculty_material`
--
ALTER TABLE `svcet_tbl_faculty_material`
  ADD PRIMARY KEY (`material_id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `academic_batch_id` (`academic_batch_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `dept_id` (`dept_id`),
  ADD KEY `year_of_study_id` (`year_of_study_id`),
  ADD KEY `sem_id` (`sem_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `svcet_tbl_faculty_mentor`
--
ALTER TABLE `svcet_tbl_faculty_mentor`
  ADD PRIMARY KEY (`faculty_mentor_id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `fk_faculty_mentor_dept` (`dept_id`);

--
-- Indexes for table `svcet_tbl_faculty_official_details`
--
ALTER TABLE `svcet_tbl_faculty_official_details`
  ADD PRIMARY KEY (`faculty_official_id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `dept_id` (`dept_id`),
  ADD KEY `svcet_tbl_faculty_official_details_ibfk_10` (`designation`);

--
-- Indexes for table `svcet_tbl_faculty_personal_info`
--
ALTER TABLE `svcet_tbl_faculty_personal_info`
  ADD PRIMARY KEY (`faculty_id`),
  ADD KEY `faculty_account_id` (`faculty_account_id`),
  ADD KEY `faculty_reference` (`faculty_reference`),
  ADD KEY `faculty_gender` (`faculty_gender`),
  ADD KEY `faculty_blood_group` (`faculty_blood_group`),
  ADD KEY `faculty_religion` (`faculty_religion`),
  ADD KEY `faculty_caste` (`faculty_caste`),
  ADD KEY `faculty_community` (`faculty_community`),
  ADD KEY `faculty_nationality` (`faculty_nationality`),
  ADD KEY `faculty_marital_status` (`faculty_marital_status`),
  ADD KEY `fk_faculty_salutation` (`faculty_salutation`);

--
-- Indexes for table `svcet_tbl_faculty_roles_and_responsibilities`
--
ALTER TABLE `svcet_tbl_faculty_roles_and_responsibilities`
  ADD PRIMARY KEY (`faculty_roles_and_responsibilities_id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `committee_title` (`committee_title`) USING BTREE;

--
-- Indexes for table `svcet_tbl_faculty_room_stock`
--
ALTER TABLE `svcet_tbl_faculty_room_stock`
  ADD PRIMARY KEY (`stock_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `svcet_tbl_faculty_room_stock_transaction`
--
ALTER TABLE `svcet_tbl_faculty_room_stock_transaction`
  ADD PRIMARY KEY (`stock_transaction_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `to_room` (`to_room`);

--
-- Indexes for table `svcet_tbl_faculty_skills`
--
ALTER TABLE `svcet_tbl_faculty_skills`
  ADD PRIMARY KEY (`faculty_skill_id`),
  ADD KEY `faculty_skill_faculty_id` (`faculty_skill_faculty_id`);

--
-- Indexes for table `svcet_tbl_faculty_subjects`
--
ALTER TABLE `svcet_tbl_faculty_subjects`
  ADD PRIMARY KEY (`faculty_subjects_id`),
  ADD KEY `sem_duration_id` (`sem_duration_id`),
  ADD KEY `dept_id` (`dept_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `academic_batch_id` (`academic_batch_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `year_of_study_id` (`year_of_study_id`),
  ADD KEY `sem_id` (`sem_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `svcet_tbl_faculty_subject_attendance`
--
ALTER TABLE `svcet_tbl_faculty_subject_attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `sem_duration_id` (`sem_duration_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `attendance_transaction_id` (`attendance_transaction_id`);

--
-- Indexes for table `svcet_tbl_faculty_subject_attendance_transaction`
--
ALTER TABLE `svcet_tbl_faculty_subject_attendance_transaction`
  ADD PRIMARY KEY (`attendance_transaction_id`),
  ADD KEY `sem_duration_id` (`sem_duration_id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `academic_batch_id` (`academic_batch_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `dept_id` (`dept_id`),
  ADD KEY `year_of_study_id` (`year_of_study_id`),
  ADD KEY `sem_id` (`sem_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `period_id` (`period_id`);

--
-- Indexes for table `svcet_tbl_faculty_timetable`
--
ALTER TABLE `svcet_tbl_faculty_timetable`
  ADD PRIMARY KEY (`timetable_id`),
  ADD KEY `faculty_subjects_id` (`faculty_subjects_id`),
  ADD KEY `period_id` (`period_id`);

--
-- Indexes for table `svcet_tbl_login_logs`
--
ALTER TABLE `svcet_tbl_login_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `svcet_tbl_parent_personal_info`
--
ALTER TABLE `svcet_tbl_parent_personal_info`
  ADD PRIMARY KEY (`parent_id`),
  ADD KEY `parent_account_id` (`parent_account_id`);

--
-- Indexes for table `svcet_tbl_student_admission_info`
--
ALTER TABLE `svcet_tbl_student_admission_info`
  ADD PRIMARY KEY (`admission_id`),
  ADD KEY `student_reference` (`student_reference`),
  ADD KEY `fk_student_id` (`student_admission_student_id`),
  ADD KEY `fk_student_course_preference1` (`student_course_preference1`),
  ADD KEY `fk_student_course_preference2` (`student_course_preference2`),
  ADD KEY `fk_student_course_preference3` (`student_course_preference3`);

--
-- Indexes for table `svcet_tbl_student_committee`
--
ALTER TABLE `svcet_tbl_student_committee`
  ADD PRIMARY KEY (`student_committee_id`),
  ADD KEY `committee_title` (`committee_title`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `dept_id` (`dept_id`);

--
-- Indexes for table `svcet_tbl_student_documents`
--
ALTER TABLE `svcet_tbl_student_documents`
  ADD PRIMARY KEY (`student_doc_id`),
  ADD KEY `student_doc_student_id` (`student_doc_student_id`);

--
-- Indexes for table `svcet_tbl_student_education`
--
ALTER TABLE `svcet_tbl_student_education`
  ADD PRIMARY KEY (`student_edu_id`),
  ADD KEY `student_edu_student_id` (`student_edu_student_id`),
  ADD KEY `student_edu_degree` (`student_edu_degree`),
  ADD KEY `student_edu_specialization` (`student_edu_specialization`);

--
-- Indexes for table `svcet_tbl_student_leave_application`
--
ALTER TABLE `svcet_tbl_student_leave_application`
  ADD PRIMARY KEY (`leave_id`),
  ADD KEY `sem_duration_id` (`sem_duration_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `svcet_tbl_student_official_details`
--
ALTER TABLE `svcet_tbl_student_official_details`
  ADD PRIMARY KEY (`student_official_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `sem_duration_id` (`sem_duration_id`),
  ADD KEY `dept_id` (`dept_id`),
  ADD KEY `academic_batch_id` (`academic_batch_id`),
  ADD KEY `academic_year_id` (`academic_year_id`),
  ADD KEY `year_of_study_id` (`year_of_study_id`),
  ADD KEY `sem_id` (`sem_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `class_advisor_id` (`class_advisor_id`),
  ADD KEY `mentor_id` (`mentor_id`),
  ADD KEY `hod_id` (`hod_id`);

--
-- Indexes for table `svcet_tbl_student_parent_relation`
--
ALTER TABLE `svcet_tbl_student_parent_relation`
  ADD PRIMARY KEY (`relation_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `svcet_tbl_student_personal_info`
--
ALTER TABLE `svcet_tbl_student_personal_info`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `student_account_id` (`student_account_id`),
  ADD KEY `student_reference` (`student_reference`),
  ADD KEY `student_gender` (`student_gender`),
  ADD KEY `student_blood_group` (`student_blood_group`),
  ADD KEY `student_religion` (`student_religion`),
  ADD KEY `student_caste` (`student_caste`),
  ADD KEY `student_community` (`student_community`),
  ADD KEY `student_nationality` (`student_nationality`),
  ADD KEY `student_marital_status` (`student_marital_status`);

--
-- Indexes for table `svcet_tbl_student_representative`
--
ALTER TABLE `svcet_tbl_student_representative`
  ADD PRIMARY KEY (`student_representative_id`),
  ADD KEY `dept_id` (`dept_id`),
  ADD KEY `year_of_study_id` (`year_of_study_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `svcet_tbl_user_activity_log`
--
ALTER TABLE `svcet_tbl_user_activity_log`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `fk_login_id` (`login_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `svcet_tbl_accounts`
--
ALTER TABLE `svcet_tbl_accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_academic_batch`
--
ALTER TABLE `svcet_tbl_dev_academic_batch`
  MODIFY `academic_batch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_academic_year`
--
ALTER TABLE `svcet_tbl_dev_academic_year`
  MODIFY `academic_year_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_attendance`
--
ALTER TABLE `svcet_tbl_dev_attendance`
  MODIFY `attendance_dev_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_day`
--
ALTER TABLE `svcet_tbl_dev_day`
  MODIFY `day_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_dept`
--
ALTER TABLE `svcet_tbl_dev_dept`
  MODIFY `dept_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_exam_management`
--
ALTER TABLE `svcet_tbl_dev_exam_management`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_general`
--
ALTER TABLE `svcet_tbl_dev_general`
  MODIFY `general_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=326;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_group`
--
ALTER TABLE `svcet_tbl_dev_group`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_institution`
--
ALTER TABLE `svcet_tbl_dev_institution`
  MODIFY `institution_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_pages`
--
ALTER TABLE `svcet_tbl_dev_pages`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_period_time`
--
ALTER TABLE `svcet_tbl_dev_period_time`
  MODIFY `period_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_prefixes`
--
ALTER TABLE `svcet_tbl_dev_prefixes`
  MODIFY `prefixes_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_roles`
--
ALTER TABLE `svcet_tbl_dev_roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_role_permission`
--
ALTER TABLE `svcet_tbl_dev_role_permission`
  MODIFY `role_perm_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=420;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_room_management`
--
ALTER TABLE `svcet_tbl_dev_room_management`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_section`
--
ALTER TABLE `svcet_tbl_dev_section`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_sem`
--
ALTER TABLE `svcet_tbl_dev_sem`
  MODIFY `sem_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_sem_duration`
--
ALTER TABLE `svcet_tbl_dev_sem_duration`
  MODIFY `sem_duration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_stock_details`
--
ALTER TABLE `svcet_tbl_dev_stock_details`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_subject`
--
ALTER TABLE `svcet_tbl_dev_subject`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_subject_lessonplan`
--
ALTER TABLE `svcet_tbl_dev_subject_lessonplan`
  MODIFY `lessonplan_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_dev_year_of_study`
--
ALTER TABLE `svcet_tbl_dev_year_of_study`
  MODIFY `year_of_study_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `svcet_tbl_error_log`
--
ALTER TABLE `svcet_tbl_error_log`
  MODIFY `error_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_achievements`
--
ALTER TABLE `svcet_tbl_faculty_achievements`
  MODIFY `faculty_achievements_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_authorities`
--
ALTER TABLE `svcet_tbl_faculty_authorities`
  MODIFY `faculty_authorities_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_class_advisors`
--
ALTER TABLE `svcet_tbl_faculty_class_advisors`
  MODIFY `faculty_class_advisors_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_documents`
--
ALTER TABLE `svcet_tbl_faculty_documents`
  MODIFY `faculty_doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_education`
--
ALTER TABLE `svcet_tbl_faculty_education`
  MODIFY `faculty_edu_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_events`
--
ALTER TABLE `svcet_tbl_faculty_events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_event_mapping`
--
ALTER TABLE `svcet_tbl_faculty_event_mapping`
  MODIFY `event_mapping_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_exam_hall_arrangement`
--
ALTER TABLE `svcet_tbl_faculty_exam_hall_arrangement`
  MODIFY `arrangement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_exam_marks`
--
ALTER TABLE `svcet_tbl_faculty_exam_marks`
  MODIFY `exam_marks_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_exam_slots`
--
ALTER TABLE `svcet_tbl_faculty_exam_slots`
  MODIFY `exam_slots_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_exam_subject`
--
ALTER TABLE `svcet_tbl_faculty_exam_subject`
  MODIFY `exam_subject_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_experience`
--
ALTER TABLE `svcet_tbl_faculty_experience`
  MODIFY `faculty_exp_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_material`
--
ALTER TABLE `svcet_tbl_faculty_material`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_mentor`
--
ALTER TABLE `svcet_tbl_faculty_mentor`
  MODIFY `faculty_mentor_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_official_details`
--
ALTER TABLE `svcet_tbl_faculty_official_details`
  MODIFY `faculty_official_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_personal_info`
--
ALTER TABLE `svcet_tbl_faculty_personal_info`
  MODIFY `faculty_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_roles_and_responsibilities`
--
ALTER TABLE `svcet_tbl_faculty_roles_and_responsibilities`
  MODIFY `faculty_roles_and_responsibilities_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_room_stock`
--
ALTER TABLE `svcet_tbl_faculty_room_stock`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_room_stock_transaction`
--
ALTER TABLE `svcet_tbl_faculty_room_stock_transaction`
  MODIFY `stock_transaction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_skills`
--
ALTER TABLE `svcet_tbl_faculty_skills`
  MODIFY `faculty_skill_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_subjects`
--
ALTER TABLE `svcet_tbl_faculty_subjects`
  MODIFY `faculty_subjects_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_subject_attendance`
--
ALTER TABLE `svcet_tbl_faculty_subject_attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_subject_attendance_transaction`
--
ALTER TABLE `svcet_tbl_faculty_subject_attendance_transaction`
  MODIFY `attendance_transaction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_faculty_timetable`
--
ALTER TABLE `svcet_tbl_faculty_timetable`
  MODIFY `timetable_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_login_logs`
--
ALTER TABLE `svcet_tbl_login_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_parent_personal_info`
--
ALTER TABLE `svcet_tbl_parent_personal_info`
  MODIFY `parent_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_student_admission_info`
--
ALTER TABLE `svcet_tbl_student_admission_info`
  MODIFY `admission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_student_committee`
--
ALTER TABLE `svcet_tbl_student_committee`
  MODIFY `student_committee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_student_documents`
--
ALTER TABLE `svcet_tbl_student_documents`
  MODIFY `student_doc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_student_education`
--
ALTER TABLE `svcet_tbl_student_education`
  MODIFY `student_edu_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_student_leave_application`
--
ALTER TABLE `svcet_tbl_student_leave_application`
  MODIFY `leave_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_student_official_details`
--
ALTER TABLE `svcet_tbl_student_official_details`
  MODIFY `student_official_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_student_parent_relation`
--
ALTER TABLE `svcet_tbl_student_parent_relation`
  MODIFY `relation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_student_personal_info`
--
ALTER TABLE `svcet_tbl_student_personal_info`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_student_representative`
--
ALTER TABLE `svcet_tbl_student_representative`
  MODIFY `student_representative_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `svcet_tbl_user_activity_log`
--
ALTER TABLE `svcet_tbl_user_activity_log`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `svcet_tbl_dev_group`
--
ALTER TABLE `svcet_tbl_dev_group`
  ADD CONSTRAINT `svcet_tbl_dev_group_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `svcet_tbl_dev_section` (`section_id`),
  ADD CONSTRAINT `svcet_tbl_dev_group_ibfk_2` FOREIGN KEY (`academic_batch_id`) REFERENCES `svcet_tbl_dev_academic_batch` (`academic_batch_id`),
  ADD CONSTRAINT `svcet_tbl_dev_group_ibfk_3` FOREIGN KEY (`academic_year_id`) REFERENCES `svcet_tbl_dev_academic_year` (`academic_year_id`),
  ADD CONSTRAINT `svcet_tbl_dev_group_ibfk_4` FOREIGN KEY (`year_of_study_id`) REFERENCES `svcet_tbl_dev_year_of_study` (`year_of_study_id`),
  ADD CONSTRAINT `svcet_tbl_dev_group_ibfk_5` FOREIGN KEY (`sem_id`) REFERENCES `svcet_tbl_dev_sem` (`sem_id`),
  ADD CONSTRAINT `svcet_tbl_dev_group_ibfk_6` FOREIGN KEY (`dept_id`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`),
  ADD CONSTRAINT `svcet_tbl_dev_group_ibfk_7` FOREIGN KEY (`sem_duration_id`) REFERENCES `svcet_tbl_dev_sem_duration` (`sem_duration_id`);

--
-- Constraints for table `svcet_tbl_dev_period_time`
--
ALTER TABLE `svcet_tbl_dev_period_time`
  ADD CONSTRAINT `svcet_tbl_dev_period_time_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`),
  ADD CONSTRAINT `svcet_tbl_dev_period_time_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `svcet_tbl_dev_group` (`group_id`),
  ADD CONSTRAINT `svcet_tbl_dev_period_time_ibfk_3` FOREIGN KEY (`section_id`) REFERENCES `svcet_tbl_dev_section` (`section_id`),
  ADD CONSTRAINT `svcet_tbl_dev_period_time_ibfk_4` FOREIGN KEY (`academic_batch_id`) REFERENCES `svcet_tbl_dev_academic_batch` (`academic_batch_id`),
  ADD CONSTRAINT `svcet_tbl_dev_period_time_ibfk_5` FOREIGN KEY (`academic_year_id`) REFERENCES `svcet_tbl_dev_academic_year` (`academic_year_id`),
  ADD CONSTRAINT `svcet_tbl_dev_period_time_ibfk_6` FOREIGN KEY (`year_of_study_id`) REFERENCES `svcet_tbl_dev_year_of_study` (`year_of_study_id`),
  ADD CONSTRAINT `svcet_tbl_dev_period_time_ibfk_7` FOREIGN KEY (`sem_id`) REFERENCES `svcet_tbl_dev_sem` (`sem_id`),
  ADD CONSTRAINT `svcet_tbl_dev_period_time_ibfk_8` FOREIGN KEY (`sem_duration_id`) REFERENCES `svcet_tbl_dev_sem_duration` (`sem_duration_id`);

--
-- Constraints for table `svcet_tbl_dev_role_permission`
--
ALTER TABLE `svcet_tbl_dev_role_permission`
  ADD CONSTRAINT `svcet_tbl_dev_role_permission_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `svcet_tbl_dev_roles` (`role_id`),
  ADD CONSTRAINT `svcet_tbl_dev_role_permission_ibfk_2` FOREIGN KEY (`page_id`) REFERENCES `svcet_tbl_dev_pages` (`page_id`);

--
-- Constraints for table `svcet_tbl_dev_room_management`
--
ALTER TABLE `svcet_tbl_dev_room_management`
  ADD CONSTRAINT `svcet_tbl_dev_room_management_ibfk_1` FOREIGN KEY (`room_dept_id`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`);

--
-- Constraints for table `svcet_tbl_dev_section`
--
ALTER TABLE `svcet_tbl_dev_section`
  ADD CONSTRAINT `svcet_tbl_dev_section_ibfk_1` FOREIGN KEY (`academic_batch_id`) REFERENCES `svcet_tbl_dev_academic_batch` (`academic_batch_id`),
  ADD CONSTRAINT `svcet_tbl_dev_section_ibfk_2` FOREIGN KEY (`academic_year_id`) REFERENCES `svcet_tbl_dev_academic_year` (`academic_year_id`),
  ADD CONSTRAINT `svcet_tbl_dev_section_ibfk_3` FOREIGN KEY (`year_of_study_id`) REFERENCES `svcet_tbl_dev_year_of_study` (`year_of_study_id`),
  ADD CONSTRAINT `svcet_tbl_dev_section_ibfk_4` FOREIGN KEY (`sem_id`) REFERENCES `svcet_tbl_dev_sem` (`sem_id`),
  ADD CONSTRAINT `svcet_tbl_dev_section_ibfk_5` FOREIGN KEY (`dept_id`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`),
  ADD CONSTRAINT `svcet_tbl_dev_section_ibfk_6` FOREIGN KEY (`sem_duration_id`) REFERENCES `svcet_tbl_dev_sem_duration` (`sem_duration_id`);

--
-- Constraints for table `svcet_tbl_dev_sem`
--
ALTER TABLE `svcet_tbl_dev_sem`
  ADD CONSTRAINT `svcet_tbl_dev_sem_ibfk_1` FOREIGN KEY (`academic_batch_id`) REFERENCES `svcet_tbl_dev_academic_batch` (`academic_batch_id`),
  ADD CONSTRAINT `svcet_tbl_dev_sem_ibfk_2` FOREIGN KEY (`academic_year_id`) REFERENCES `svcet_tbl_dev_academic_year` (`academic_year_id`),
  ADD CONSTRAINT `svcet_tbl_dev_sem_ibfk_3` FOREIGN KEY (`year_of_study_id`) REFERENCES `svcet_tbl_dev_year_of_study` (`year_of_study_id`),
  ADD CONSTRAINT `svcet_tbl_dev_sem_ibfk_4` FOREIGN KEY (`dept_id`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`),
  ADD CONSTRAINT `svcet_tbl_dev_sem_ibfk_5` FOREIGN KEY (`sem_duration_id`) REFERENCES `svcet_tbl_dev_sem_duration` (`sem_duration_id`);

--
-- Constraints for table `svcet_tbl_dev_subject`
--
ALTER TABLE `svcet_tbl_dev_subject`
  ADD CONSTRAINT `svcet_tbl_dev_subject_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `svcet_tbl_dev_group` (`group_id`),
  ADD CONSTRAINT `svcet_tbl_dev_subject_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `svcet_tbl_dev_section` (`section_id`),
  ADD CONSTRAINT `svcet_tbl_dev_subject_ibfk_3` FOREIGN KEY (`academic_batch_id`) REFERENCES `svcet_tbl_dev_academic_batch` (`academic_batch_id`),
  ADD CONSTRAINT `svcet_tbl_dev_subject_ibfk_4` FOREIGN KEY (`academic_year_id`) REFERENCES `svcet_tbl_dev_academic_year` (`academic_year_id`),
  ADD CONSTRAINT `svcet_tbl_dev_subject_ibfk_5` FOREIGN KEY (`year_of_study_id`) REFERENCES `svcet_tbl_dev_year_of_study` (`year_of_study_id`),
  ADD CONSTRAINT `svcet_tbl_dev_subject_ibfk_6` FOREIGN KEY (`sem_id`) REFERENCES `svcet_tbl_dev_sem` (`sem_id`),
  ADD CONSTRAINT `svcet_tbl_dev_subject_ibfk_7` FOREIGN KEY (`dept_id`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`);

--
-- Constraints for table `svcet_tbl_dev_subject_lessonplan`
--
ALTER TABLE `svcet_tbl_dev_subject_lessonplan`
  ADD CONSTRAINT `svcet_tbl_dev_subject_lessonplan_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `svcet_tbl_dev_subject` (`subject_id`);

--
-- Constraints for table `svcet_tbl_dev_year_of_study`
--
ALTER TABLE `svcet_tbl_dev_year_of_study`
  ADD CONSTRAINT `svcet_tbl_dev_year_of_study_ibfk_1` FOREIGN KEY (`academic_batch_id`) REFERENCES `svcet_tbl_dev_academic_batch` (`academic_batch_id`),
  ADD CONSTRAINT `svcet_tbl_dev_year_of_study_ibfk_2` FOREIGN KEY (`academic_year_id`) REFERENCES `svcet_tbl_dev_academic_year` (`academic_year_id`),
  ADD CONSTRAINT `svcet_tbl_dev_year_of_study_ibfk_3` FOREIGN KEY (`dept_id`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`),
  ADD CONSTRAINT `svcet_tbl_dev_year_of_study_ibfk_4` FOREIGN KEY (`sem_duration_id`) REFERENCES `svcet_tbl_dev_sem_duration` (`sem_duration_id`);

--
-- Constraints for table `svcet_tbl_error_log`
--
ALTER TABLE `svcet_tbl_error_log`
  ADD CONSTRAINT `svcet_tbl_error_log_ibfk_1` FOREIGN KEY (`login_id`) REFERENCES `svcet_tbl_login_logs` (`log_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `svcet_tbl_faculty_achievements`
--
ALTER TABLE `svcet_tbl_faculty_achievements`
  ADD CONSTRAINT `svcet_tbl_faculty_achievements_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `svcet_tbl_faculty_personal_info` (`faculty_id`);

--
-- Constraints for table `svcet_tbl_faculty_authorities`
--
ALTER TABLE `svcet_tbl_faculty_authorities`
  ADD CONSTRAINT `svcet_tbl_faculty_authorities_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `svcet_tbl_faculty_personal_info` (`faculty_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_authorities_ibfk_2` FOREIGN KEY (`dept_id`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`);

--
-- Constraints for table `svcet_tbl_faculty_class_advisors`
--
ALTER TABLE `svcet_tbl_faculty_class_advisors`
  ADD CONSTRAINT `svcet_tbl_faculty_class_advisors_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `svcet_tbl_faculty_personal_info` (`faculty_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_class_advisors_ibfk_2` FOREIGN KEY (`year_of_study_id`) REFERENCES `svcet_tbl_dev_year_of_study` (`year_of_study_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_class_advisors_ibfk_3` FOREIGN KEY (`section_id`) REFERENCES `svcet_tbl_dev_section` (`section_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_class_advisors_ibfk_4` FOREIGN KEY (`dept_id`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`);

--
-- Constraints for table `svcet_tbl_faculty_documents`
--
ALTER TABLE `svcet_tbl_faculty_documents`
  ADD CONSTRAINT `svcet_tbl_faculty_documents_ibfk_1` FOREIGN KEY (`faculty_doc_faculty_id`) REFERENCES `svcet_tbl_faculty_personal_info` (`faculty_id`);

--
-- Constraints for table `svcet_tbl_faculty_education`
--
ALTER TABLE `svcet_tbl_faculty_education`
  ADD CONSTRAINT `svcet_tbl_faculty_education_ibfk_1` FOREIGN KEY (`faculty_edu_faculty_id`) REFERENCES `svcet_tbl_faculty_personal_info` (`faculty_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_education_ibfk_2` FOREIGN KEY (`faculty_edu_degree`) REFERENCES `svcet_tbl_dev_general` (`general_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_education_ibfk_3` FOREIGN KEY (`faculty_edu_specialization`) REFERENCES `svcet_tbl_dev_general` (`general_id`);

--
-- Constraints for table `svcet_tbl_faculty_events`
--
ALTER TABLE `svcet_tbl_faculty_events`
  ADD CONSTRAINT `svcet_tbl_faculty_events_ibfk_1` FOREIGN KEY (`sem_duration_id`) REFERENCES `svcet_tbl_dev_sem_duration` (`sem_duration_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_events_ibfk_2` FOREIGN KEY (`event_type`) REFERENCES `svcet_tbl_dev_general` (`general_id`);

--
-- Constraints for table `svcet_tbl_faculty_event_mapping`
--
ALTER TABLE `svcet_tbl_faculty_event_mapping`
  ADD CONSTRAINT `svcet_tbl_faculty_event_mapping_ibfk_1` FOREIGN KEY (`sem_duration_id`) REFERENCES `svcet_tbl_dev_sem_duration` (`sem_duration_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_event_mapping_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `svcet_tbl_faculty_events` (`event_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_event_mapping_ibfk_3` FOREIGN KEY (`academic_batch_id`) REFERENCES `svcet_tbl_dev_academic_batch` (`academic_batch_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_event_mapping_ibfk_4` FOREIGN KEY (`academic_year_id`) REFERENCES `svcet_tbl_dev_academic_year` (`academic_year_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_event_mapping_ibfk_5` FOREIGN KEY (`dept_id`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_event_mapping_ibfk_6` FOREIGN KEY (`year_of_study_id`) REFERENCES `svcet_tbl_dev_year_of_study` (`year_of_study_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_event_mapping_ibfk_7` FOREIGN KEY (`sem_id`) REFERENCES `svcet_tbl_dev_sem` (`sem_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_event_mapping_ibfk_8` FOREIGN KEY (`section_id`) REFERENCES `svcet_tbl_dev_section` (`section_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_event_mapping_ibfk_9` FOREIGN KEY (`group_id`) REFERENCES `svcet_tbl_dev_group` (`group_id`);

--
-- Constraints for table `svcet_tbl_faculty_exam_hall_arrangement`
--
ALTER TABLE `svcet_tbl_faculty_exam_hall_arrangement`
  ADD CONSTRAINT `svcet_tbl_faculty_exam_hall_arrangement_ibfk_1` FOREIGN KEY (`sem_duration_id`) REFERENCES `svcet_tbl_dev_sem_duration` (`sem_duration_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_exam_hall_arrangement_ibfk_2` FOREIGN KEY (`exam_subject_id`) REFERENCES `svcet_tbl_faculty_exam_subject` (`exam_subject_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_exam_hall_arrangement_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `svcet_tbl_dev_room_management` (`room_id`);

--
-- Constraints for table `svcet_tbl_faculty_exam_marks`
--
ALTER TABLE `svcet_tbl_faculty_exam_marks`
  ADD CONSTRAINT `svcet_tbl_faculty_exam_marks_ibfk_1` FOREIGN KEY (`sem_duration_id`) REFERENCES `svcet_tbl_dev_sem_duration` (`sem_duration_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_exam_marks_ibfk_2` FOREIGN KEY (`exam_subject_id`) REFERENCES `svcet_tbl_faculty_exam_subject` (`exam_subject_id`);

--
-- Constraints for table `svcet_tbl_faculty_exam_slots`
--
ALTER TABLE `svcet_tbl_faculty_exam_slots`
  ADD CONSTRAINT `svcet_tbl_faculty_exam_slots_ibfk_1` FOREIGN KEY (`sem_duration_id`) REFERENCES `svcet_tbl_dev_sem_duration` (`sem_duration_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_exam_slots_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `svcet_tbl_dev_exam_management` (`exam_id`);

--
-- Constraints for table `svcet_tbl_faculty_exam_subject`
--
ALTER TABLE `svcet_tbl_faculty_exam_subject`
  ADD CONSTRAINT `svcet_tbl_faculty_exam_subject_ibfk_1` FOREIGN KEY (`sem_duration_id`) REFERENCES `svcet_tbl_dev_sem_duration` (`sem_duration_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_exam_subject_ibfk_10` FOREIGN KEY (`exam_id`) REFERENCES `svcet_tbl_dev_exam_management` (`exam_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_exam_subject_ibfk_2` FOREIGN KEY (`exam_slots_id`) REFERENCES `svcet_tbl_faculty_exam_slots` (`exam_slots_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_exam_subject_ibfk_3` FOREIGN KEY (`academic_batch_id`) REFERENCES `svcet_tbl_dev_academic_batch` (`academic_batch_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_exam_subject_ibfk_4` FOREIGN KEY (`academic_year_id`) REFERENCES `svcet_tbl_dev_academic_year` (`academic_year_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_exam_subject_ibfk_5` FOREIGN KEY (`dept_id`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_exam_subject_ibfk_6` FOREIGN KEY (`year_of_study_id`) REFERENCES `svcet_tbl_dev_year_of_study` (`year_of_study_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_exam_subject_ibfk_7` FOREIGN KEY (`sem_id`) REFERENCES `svcet_tbl_dev_sem` (`sem_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_exam_subject_ibfk_8` FOREIGN KEY (`section_id`) REFERENCES `svcet_tbl_dev_section` (`section_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_exam_subject_ibfk_9` FOREIGN KEY (`group_id`) REFERENCES `svcet_tbl_dev_group` (`group_id`);

--
-- Constraints for table `svcet_tbl_faculty_experience`
--
ALTER TABLE `svcet_tbl_faculty_experience`
  ADD CONSTRAINT `svcet_tbl_faculty_experience_ibfk_3` FOREIGN KEY (`faculty_exp_faculty_id`) REFERENCES `svcet_tbl_faculty_personal_info` (`faculty_id`);

--
-- Constraints for table `svcet_tbl_faculty_material`
--
ALTER TABLE `svcet_tbl_faculty_material`
  ADD CONSTRAINT `svcet_tbl_faculty_material_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `svcet_tbl_faculty_personal_info` (`faculty_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_material_ibfk_2` FOREIGN KEY (`academic_batch_id`) REFERENCES `svcet_tbl_dev_academic_batch` (`academic_batch_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_material_ibfk_3` FOREIGN KEY (`academic_year_id`) REFERENCES `svcet_tbl_dev_academic_year` (`academic_year_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_material_ibfk_4` FOREIGN KEY (`dept_id`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_material_ibfk_5` FOREIGN KEY (`year_of_study_id`) REFERENCES `svcet_tbl_dev_year_of_study` (`year_of_study_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_material_ibfk_6` FOREIGN KEY (`sem_id`) REFERENCES `svcet_tbl_dev_sem` (`sem_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_material_ibfk_7` FOREIGN KEY (`section_id`) REFERENCES `svcet_tbl_dev_section` (`section_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_material_ibfk_8` FOREIGN KEY (`group_id`) REFERENCES `svcet_tbl_dev_group` (`group_id`);

--
-- Constraints for table `svcet_tbl_faculty_mentor`
--
ALTER TABLE `svcet_tbl_faculty_mentor`
  ADD CONSTRAINT `fk_faculty_mentor_dept` FOREIGN KEY (`dept_id`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `svcet_tbl_faculty_mentor_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `svcet_tbl_faculty_personal_info` (`faculty_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_mentor_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `svcet_tbl_student_personal_info` (`student_id`);

--
-- Constraints for table `svcet_tbl_faculty_official_details`
--
ALTER TABLE `svcet_tbl_faculty_official_details`
  ADD CONSTRAINT `svcet_tbl_faculty_official_details_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `svcet_tbl_faculty_personal_info` (`faculty_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_official_details_ibfk_10` FOREIGN KEY (`designation`) REFERENCES `svcet_tbl_dev_general` (`general_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `svcet_tbl_faculty_official_details_ibfk_3` FOREIGN KEY (`dept_id`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`);

--
-- Constraints for table `svcet_tbl_faculty_personal_info`
--
ALTER TABLE `svcet_tbl_faculty_personal_info`
  ADD CONSTRAINT `fk_faculty_salutation` FOREIGN KEY (`faculty_salutation`) REFERENCES `svcet_tbl_dev_general` (`general_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_personal_info_ibfk_1` FOREIGN KEY (`faculty_account_id`) REFERENCES `svcet_tbl_accounts` (`account_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_personal_info_ibfk_2` FOREIGN KEY (`faculty_reference`) REFERENCES `svcet_tbl_faculty_personal_info` (`faculty_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_personal_info_ibfk_3` FOREIGN KEY (`faculty_gender`) REFERENCES `svcet_tbl_dev_general` (`general_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_personal_info_ibfk_4` FOREIGN KEY (`faculty_blood_group`) REFERENCES `svcet_tbl_dev_general` (`general_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_personal_info_ibfk_5` FOREIGN KEY (`faculty_religion`) REFERENCES `svcet_tbl_dev_general` (`general_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_personal_info_ibfk_6` FOREIGN KEY (`faculty_caste`) REFERENCES `svcet_tbl_dev_general` (`general_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_personal_info_ibfk_7` FOREIGN KEY (`faculty_community`) REFERENCES `svcet_tbl_dev_general` (`general_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_personal_info_ibfk_8` FOREIGN KEY (`faculty_nationality`) REFERENCES `svcet_tbl_dev_general` (`general_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_personal_info_ibfk_9` FOREIGN KEY (`faculty_marital_status`) REFERENCES `svcet_tbl_dev_general` (`general_id`);

--
-- Constraints for table `svcet_tbl_faculty_roles_and_responsibilities`
--
ALTER TABLE `svcet_tbl_faculty_roles_and_responsibilities`
  ADD CONSTRAINT `fk_committee_title` FOREIGN KEY (`committee_title`) REFERENCES `svcet_tbl_dev_general` (`general_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_roles_and_responsibilities_ibfk_2` FOREIGN KEY (`faculty_id`) REFERENCES `svcet_tbl_faculty_personal_info` (`faculty_id`);

--
-- Constraints for table `svcet_tbl_faculty_room_stock`
--
ALTER TABLE `svcet_tbl_faculty_room_stock`
  ADD CONSTRAINT `svcet_tbl_faculty_room_stock_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `svcet_tbl_dev_room_management` (`room_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_room_stock_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `svcet_tbl_dev_stock_details` (`stock_id`);

--
-- Constraints for table `svcet_tbl_faculty_room_stock_transaction`
--
ALTER TABLE `svcet_tbl_faculty_room_stock_transaction`
  ADD CONSTRAINT `svcet_tbl_faculty_room_stock_transaction_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `svcet_tbl_dev_room_management` (`room_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_room_stock_transaction_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `svcet_tbl_dev_stock_details` (`stock_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_room_stock_transaction_ibfk_3` FOREIGN KEY (`to_room`) REFERENCES `svcet_tbl_dev_room_management` (`room_id`);

--
-- Constraints for table `svcet_tbl_faculty_skills`
--
ALTER TABLE `svcet_tbl_faculty_skills`
  ADD CONSTRAINT `svcet_tbl_faculty_skills_ibfk_1` FOREIGN KEY (`faculty_skill_faculty_id`) REFERENCES `svcet_tbl_faculty_personal_info` (`faculty_id`);

--
-- Constraints for table `svcet_tbl_faculty_subjects`
--
ALTER TABLE `svcet_tbl_faculty_subjects`
  ADD CONSTRAINT `svcet_tbl_faculty_subjects_ibfk_1` FOREIGN KEY (`sem_duration_id`) REFERENCES `svcet_tbl_dev_sem_duration` (`sem_duration_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subjects_ibfk_10` FOREIGN KEY (`faculty_id`) REFERENCES `svcet_tbl_faculty_personal_info` (`faculty_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subjects_ibfk_2` FOREIGN KEY (`dept_id`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subjects_ibfk_3` FOREIGN KEY (`section_id`) REFERENCES `svcet_tbl_dev_section` (`section_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subjects_ibfk_4` FOREIGN KEY (`academic_batch_id`) REFERENCES `svcet_tbl_dev_academic_batch` (`academic_batch_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subjects_ibfk_5` FOREIGN KEY (`academic_year_id`) REFERENCES `svcet_tbl_dev_academic_year` (`academic_year_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subjects_ibfk_6` FOREIGN KEY (`year_of_study_id`) REFERENCES `svcet_tbl_dev_year_of_study` (`year_of_study_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subjects_ibfk_7` FOREIGN KEY (`sem_id`) REFERENCES `svcet_tbl_dev_sem` (`sem_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subjects_ibfk_8` FOREIGN KEY (`subject_id`) REFERENCES `svcet_tbl_dev_subject` (`subject_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subjects_ibfk_9` FOREIGN KEY (`room_id`) REFERENCES `svcet_tbl_dev_room_management` (`room_id`);

--
-- Constraints for table `svcet_tbl_faculty_subject_attendance`
--
ALTER TABLE `svcet_tbl_faculty_subject_attendance`
  ADD CONSTRAINT `svcet_tbl_faculty_subject_attendance_ibfk_1` FOREIGN KEY (`sem_duration_id`) REFERENCES `svcet_tbl_dev_sem_duration` (`sem_duration_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subject_attendance_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `svcet_tbl_student_personal_info` (`student_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subject_attendance_ibfk_3` FOREIGN KEY (`attendance_transaction_id`) REFERENCES `svcet_tbl_faculty_subject_attendance_transaction` (`attendance_transaction_id`);

--
-- Constraints for table `svcet_tbl_faculty_subject_attendance_transaction`
--
ALTER TABLE `svcet_tbl_faculty_subject_attendance_transaction`
  ADD CONSTRAINT `svcet_tbl_faculty_subject_attendance_transaction_ibfk_1` FOREIGN KEY (`sem_duration_id`) REFERENCES `svcet_tbl_dev_sem_duration` (`sem_duration_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subject_attendance_transaction_ibfk_10` FOREIGN KEY (`subject_id`) REFERENCES `svcet_tbl_dev_subject` (`subject_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subject_attendance_transaction_ibfk_11` FOREIGN KEY (`period_id`) REFERENCES `svcet_tbl_dev_period_time` (`period_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subject_attendance_transaction_ibfk_2` FOREIGN KEY (`faculty_id`) REFERENCES `svcet_tbl_faculty_personal_info` (`faculty_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subject_attendance_transaction_ibfk_3` FOREIGN KEY (`academic_batch_id`) REFERENCES `svcet_tbl_dev_academic_batch` (`academic_batch_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subject_attendance_transaction_ibfk_4` FOREIGN KEY (`academic_year_id`) REFERENCES `svcet_tbl_dev_academic_year` (`academic_year_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subject_attendance_transaction_ibfk_5` FOREIGN KEY (`dept_id`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subject_attendance_transaction_ibfk_6` FOREIGN KEY (`year_of_study_id`) REFERENCES `svcet_tbl_dev_year_of_study` (`year_of_study_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subject_attendance_transaction_ibfk_7` FOREIGN KEY (`sem_id`) REFERENCES `svcet_tbl_dev_sem` (`sem_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subject_attendance_transaction_ibfk_8` FOREIGN KEY (`section_id`) REFERENCES `svcet_tbl_dev_section` (`section_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_subject_attendance_transaction_ibfk_9` FOREIGN KEY (`group_id`) REFERENCES `svcet_tbl_dev_group` (`group_id`);

--
-- Constraints for table `svcet_tbl_faculty_timetable`
--
ALTER TABLE `svcet_tbl_faculty_timetable`
  ADD CONSTRAINT `svcet_tbl_faculty_timetable_ibfk_1` FOREIGN KEY (`faculty_subjects_id`) REFERENCES `svcet_tbl_faculty_subjects` (`faculty_subjects_id`),
  ADD CONSTRAINT `svcet_tbl_faculty_timetable_ibfk_2` FOREIGN KEY (`period_id`) REFERENCES `svcet_tbl_dev_period_time` (`period_id`);

--
-- Constraints for table `svcet_tbl_login_logs`
--
ALTER TABLE `svcet_tbl_login_logs`
  ADD CONSTRAINT `svcet_tbl_login_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `svcet_tbl_accounts` (`account_id`);

--
-- Constraints for table `svcet_tbl_parent_personal_info`
--
ALTER TABLE `svcet_tbl_parent_personal_info`
  ADD CONSTRAINT `svcet_tbl_parent_personal_info_ibfk_1` FOREIGN KEY (`parent_account_id`) REFERENCES `svcet_tbl_accounts` (`account_id`);

--
-- Constraints for table `svcet_tbl_student_admission_info`
--
ALTER TABLE `svcet_tbl_student_admission_info`
  ADD CONSTRAINT `fk_student_course_preference1` FOREIGN KEY (`student_course_preference1`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`),
  ADD CONSTRAINT `fk_student_course_preference2` FOREIGN KEY (`student_course_preference2`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`),
  ADD CONSTRAINT `fk_student_course_preference3` FOREIGN KEY (`student_course_preference3`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`),
  ADD CONSTRAINT `fk_student_id` FOREIGN KEY (`student_admission_student_id`) REFERENCES `svcet_tbl_student_personal_info` (`student_id`),
  ADD CONSTRAINT `fk_student_reference` FOREIGN KEY (`student_reference`) REFERENCES `svcet_tbl_faculty_personal_info` (`faculty_id`);

--
-- Constraints for table `svcet_tbl_student_committee`
--
ALTER TABLE `svcet_tbl_student_committee`
  ADD CONSTRAINT `svcet_tbl_student_committee_ibfk_1` FOREIGN KEY (`committee_title`) REFERENCES `svcet_tbl_dev_general` (`general_id`),
  ADD CONSTRAINT `svcet_tbl_student_committee_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `svcet_tbl_student_personal_info` (`student_id`),
  ADD CONSTRAINT `svcet_tbl_student_committee_ibfk_3` FOREIGN KEY (`dept_id`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`);

--
-- Constraints for table `svcet_tbl_student_documents`
--
ALTER TABLE `svcet_tbl_student_documents`
  ADD CONSTRAINT `svcet_tbl_student_documents_ibfk_1` FOREIGN KEY (`student_doc_student_id`) REFERENCES `svcet_tbl_student_personal_info` (`student_id`);

--
-- Constraints for table `svcet_tbl_student_education`
--
ALTER TABLE `svcet_tbl_student_education`
  ADD CONSTRAINT `svcet_tbl_student_education_ibfk_1` FOREIGN KEY (`student_edu_student_id`) REFERENCES `svcet_tbl_student_personal_info` (`student_id`),
  ADD CONSTRAINT `svcet_tbl_student_education_ibfk_2` FOREIGN KEY (`student_edu_degree`) REFERENCES `svcet_tbl_dev_general` (`general_id`),
  ADD CONSTRAINT `svcet_tbl_student_education_ibfk_3` FOREIGN KEY (`student_edu_specialization`) REFERENCES `svcet_tbl_dev_general` (`general_id`);

--
-- Constraints for table `svcet_tbl_student_leave_application`
--
ALTER TABLE `svcet_tbl_student_leave_application`
  ADD CONSTRAINT `svcet_tbl_student_leave_application_ibfk_1` FOREIGN KEY (`sem_duration_id`) REFERENCES `svcet_tbl_dev_sem_duration` (`sem_duration_id`),
  ADD CONSTRAINT `svcet_tbl_student_leave_application_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `svcet_tbl_student_personal_info` (`student_id`);

--
-- Constraints for table `svcet_tbl_student_official_details`
--
ALTER TABLE `svcet_tbl_student_official_details`
  ADD CONSTRAINT `svcet_tbl_student_official_details_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `svcet_tbl_student_personal_info` (`student_id`),
  ADD CONSTRAINT `svcet_tbl_student_official_details_ibfk_10` FOREIGN KEY (`class_advisor_id`) REFERENCES `svcet_tbl_faculty_personal_info` (`faculty_id`),
  ADD CONSTRAINT `svcet_tbl_student_official_details_ibfk_11` FOREIGN KEY (`mentor_id`) REFERENCES `svcet_tbl_faculty_personal_info` (`faculty_id`),
  ADD CONSTRAINT `svcet_tbl_student_official_details_ibfk_12` FOREIGN KEY (`hod_id`) REFERENCES `svcet_tbl_faculty_personal_info` (`faculty_id`),
  ADD CONSTRAINT `svcet_tbl_student_official_details_ibfk_2` FOREIGN KEY (`sem_duration_id`) REFERENCES `svcet_tbl_dev_sem_duration` (`sem_duration_id`),
  ADD CONSTRAINT `svcet_tbl_student_official_details_ibfk_3` FOREIGN KEY (`dept_id`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`),
  ADD CONSTRAINT `svcet_tbl_student_official_details_ibfk_4` FOREIGN KEY (`academic_batch_id`) REFERENCES `svcet_tbl_dev_academic_batch` (`academic_batch_id`),
  ADD CONSTRAINT `svcet_tbl_student_official_details_ibfk_5` FOREIGN KEY (`academic_year_id`) REFERENCES `svcet_tbl_dev_academic_year` (`academic_year_id`),
  ADD CONSTRAINT `svcet_tbl_student_official_details_ibfk_6` FOREIGN KEY (`year_of_study_id`) REFERENCES `svcet_tbl_dev_year_of_study` (`year_of_study_id`),
  ADD CONSTRAINT `svcet_tbl_student_official_details_ibfk_7` FOREIGN KEY (`sem_id`) REFERENCES `svcet_tbl_dev_sem` (`sem_id`),
  ADD CONSTRAINT `svcet_tbl_student_official_details_ibfk_8` FOREIGN KEY (`section_id`) REFERENCES `svcet_tbl_dev_section` (`section_id`),
  ADD CONSTRAINT `svcet_tbl_student_official_details_ibfk_9` FOREIGN KEY (`group_id`) REFERENCES `svcet_tbl_dev_group` (`group_id`);

--
-- Constraints for table `svcet_tbl_student_parent_relation`
--
ALTER TABLE `svcet_tbl_student_parent_relation`
  ADD CONSTRAINT `svcet_tbl_student_parent_relation_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `svcet_tbl_student_personal_info` (`student_id`),
  ADD CONSTRAINT `svcet_tbl_student_parent_relation_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `svcet_tbl_parent_personal_info` (`parent_id`);

--
-- Constraints for table `svcet_tbl_student_personal_info`
--
ALTER TABLE `svcet_tbl_student_personal_info`
  ADD CONSTRAINT `svcet_tbl_student_personal_info_ibfk_1` FOREIGN KEY (`student_account_id`) REFERENCES `svcet_tbl_accounts` (`account_id`),
  ADD CONSTRAINT `svcet_tbl_student_personal_info_ibfk_2` FOREIGN KEY (`student_reference`) REFERENCES `svcet_tbl_accounts` (`account_id`),
  ADD CONSTRAINT `svcet_tbl_student_personal_info_ibfk_3` FOREIGN KEY (`student_gender`) REFERENCES `svcet_tbl_dev_general` (`general_id`),
  ADD CONSTRAINT `svcet_tbl_student_personal_info_ibfk_4` FOREIGN KEY (`student_blood_group`) REFERENCES `svcet_tbl_dev_general` (`general_id`),
  ADD CONSTRAINT `svcet_tbl_student_personal_info_ibfk_5` FOREIGN KEY (`student_religion`) REFERENCES `svcet_tbl_dev_general` (`general_id`),
  ADD CONSTRAINT `svcet_tbl_student_personal_info_ibfk_6` FOREIGN KEY (`student_caste`) REFERENCES `svcet_tbl_dev_general` (`general_id`),
  ADD CONSTRAINT `svcet_tbl_student_personal_info_ibfk_7` FOREIGN KEY (`student_community`) REFERENCES `svcet_tbl_dev_general` (`general_id`),
  ADD CONSTRAINT `svcet_tbl_student_personal_info_ibfk_8` FOREIGN KEY (`student_nationality`) REFERENCES `svcet_tbl_dev_general` (`general_id`),
  ADD CONSTRAINT `svcet_tbl_student_personal_info_ibfk_9` FOREIGN KEY (`student_marital_status`) REFERENCES `svcet_tbl_dev_general` (`general_id`);

--
-- Constraints for table `svcet_tbl_student_representative`
--
ALTER TABLE `svcet_tbl_student_representative`
  ADD CONSTRAINT `svcet_tbl_student_representative_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `svcet_tbl_dev_dept` (`dept_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `svcet_tbl_student_representative_ibfk_2` FOREIGN KEY (`year_of_study_id`) REFERENCES `svcet_tbl_dev_year_of_study` (`year_of_study_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `svcet_tbl_student_representative_ibfk_3` FOREIGN KEY (`section_id`) REFERENCES `svcet_tbl_dev_section` (`section_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `svcet_tbl_student_representative_ibfk_4` FOREIGN KEY (`student_id`) REFERENCES `svcet_tbl_student_personal_info` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `svcet_tbl_user_activity_log`
--
ALTER TABLE `svcet_tbl_user_activity_log`
  ADD CONSTRAINT `fk_login_id` FOREIGN KEY (`login_id`) REFERENCES `svcet_tbl_login_logs` (`log_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
