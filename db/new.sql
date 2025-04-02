DELIMITER $$
CREATE  PROCEDURE `check_user_login_status`(IN `p_log_id` INT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `delete_new_account_data`(IN `account_id` INT, IN `p_login_id` INT)
BEGIN
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
    DELETE FROM `svcet_tbl_accounts` WHERE `svcet_tbl_accounts`.`account_id` = account_id;

   CALL insert_user_activity_log(p_login_id, 'svcet_tbl_accounts', 4);
   

   -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Canceled Successfully!' AS message;
    END IF;
    END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_allowed_pages_by_role`(IN `p_role_id` INT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_crypt`(IN `input_username` VARCHAR(255), IN `input_portal_type` INT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_login_details`(IN `p_account_id` INT, IN `p_portal_type` INT, IN `login_id` INT)
BEGIN
    -- Declare variables to store faculty and account details
    DECLARE v_first_name VARCHAR(100);
    DECLARE v_middle_name VARCHAR(100);
    DECLARE v_last_name VARCHAR(100);
    DECLARE v_initial VARCHAR(10);
    DECLARE v_user_id INT;
    DECLARE v_designation VARCHAR(100);
    DECLARE v_portal_type TINYINT;
    DECLARE v_account_id INT;
    DECLARE v_faculty_salutation VARCHAR(200);
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
    salutation.general_title AS faculty_salutation,
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
    v_faculty_salutation,
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
    svcet_tbl_dev_general salutation ON fp.faculty_salutation = salutation.general_id
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
    v_faculty_salutation AS faculty_salutation,
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_nav_pages`(IN `p_module_id` INT, IN `p_module_status` TINYINT, IN `p_portal_type` TINYINT, IN `p_role_id` INT)
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
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Set success message
        SELECT 200 AS status_code, 'success' AS status, 'Navigation pages fetched successfully!' AS message;
    END IF;

END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_overall_faculty_profile_table_data`(IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(50), IN `p_order_dir` VARCHAR(4), IN `p_start` INT, IN `p_length` INT, IN `p_designation` INT, IN `p_department` INT, IN `p_login_id` INT)
BEGIN
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
    JOIN svcet_tbl_accounts AS acc ON fpi.faculty_account_id = acc.account_id
    WHERE fpi.faculty_deleted = 0
      AND acc.role_id = 8
      AND (p_department = 0 OR fod.dept_id = p_department);

    -- Get the filtered record count
    SELECT COUNT(*) INTO filtered_records
    FROM svcet_tbl_faculty_personal_info AS fpi
    JOIN svcet_tbl_faculty_official_details AS fod ON fpi.faculty_id = fod.faculty_id
    JOIN svcet_tbl_accounts AS acc ON fpi.faculty_account_id = acc.account_id
    WHERE fpi.faculty_deleted = 0 
      AND acc.role_id = 8
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
    JOIN svcet_tbl_accounts AS acc ON fpi.faculty_account_id = acc.account_id
    LEFT JOIN svcet_tbl_dev_dept AS d ON fod.dept_id = d.dept_id
    LEFT JOIN svcet_tbl_dev_general AS g ON fod.designation = g.general_id
    LEFT JOIN svcet_tbl_dev_general AS salutation ON fpi.faculty_salutation = salutation.general_id
    LEFT JOIN svcet_tbl_faculty_documents AS doc ON fpi.faculty_id = doc.faculty_doc_faculty_id 
         AND doc.faculty_doc_type = 6 AND doc.faculty_doc_status = 1 AND doc.faculty_doc_deleted = 0 OR doc.faculty_doc_deleted IS NULL
    WHERE fpi.faculty_deleted = 0 
      AND acc.role_id = 8
      AND (fpi.faculty_first_name LIKE CONCAT('%', p_search_value, '%') 
           OR fpi.faculty_last_name LIKE CONCAT('%', p_search_value, '%'))
      AND (p_designation = 0 OR fod.designation = p_designation OR fod.designation IS NULL)
      AND (p_department = 0 OR fod.dept_id = p_department OR fod.dept_id IS NULL)
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
    
	-- Check if department exists
SELECT COUNT(*) INTO @dept_exists
FROM svcet_tbl_dev_dept
WHERE dept_id = p_department;

-- Fetch department data if exists
IF @dept_exists > 0 THEN
    SELECT 
        COALESCE(dept_title, '') AS Department
    FROM 
        svcet_tbl_dev_dept
    WHERE 
        dept_id = p_department
        AND (dept_status = 1 OR dept_status IS NULL)
            AND (dept_deleted = 0 OR dept_deleted IS NULL);
ELSE
    -- Return an empty value if no department record exists
    SELECT 
        '' AS Department;
END IF;

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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_overall_student_profile_table_data`(IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(50), IN `p_order_dir` VARCHAR(4), IN `p_start` INT, IN `p_length` INT, IN `p_section_id` INT, IN `p_year_of_study_id` INT, IN `p_department_id` INT, IN `p_login_id` INT)
BEGIN
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
    WHERE spi.student_deleted = 0 ;

    -- Get filtered record count
    SELECT COUNT(*) INTO filtered_records 
    FROM svcet_tbl_student_personal_info AS spi
    JOIN svcet_tbl_student_official_details AS sod ON spi.student_id = sod.student_id
    WHERE spi.student_deleted = 0
      AND (spi.student_first_name LIKE CONCAT('%', p_search_value, '%') 
           OR spi.student_last_name LIKE CONCAT('%', p_search_value, '%'))
      AND (p_department_id = 0 OR sod.dept_id = p_department_id)
      AND (p_section_id = 0 OR sod.section_id = p_section_id)
      AND (p_year_of_study_id = 0 OR sod.year_of_study_id = p_year_of_study_id);

    -- Fetch the data with pagination and sorting
    SELECT spi.student_id, 
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
    WHERE spi.student_deleted = 0 OR spi.student_deleted IS NULL
      AND (spi.student_first_name LIKE CONCAT('%', p_search_value, '%') 
           OR spi.student_last_name LIKE CONCAT('%', p_search_value, '%'))
      AND (p_department_id = 0 OR sod.dept_id = p_department_id)
      AND (p_section_id = 0 OR sod.section_id = p_section_id)
      AND (p_year_of_study_id = 0 OR sod.year_of_study_id = p_year_of_study_id)
      AND (sod.student_official_details_deleted = 0 OR sod.student_official_details_deleted IS NULL)
    ORDER BY 
        CASE 
            WHEN p_sort_column = 'student_first_name' THEN spi.student_first_name
            WHEN p_sort_column = 'student_last_name' THEN spi.student_last_name
            WHEN p_sort_column = 'student_reg_number' THEN sod.student_reg_number
            ELSE spi.student_id
        END 
    LIMIT p_start, p_length;

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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_period_day`(IN `facultyId` INT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_academic_batch`(IN `p_login_id` INT)
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
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Academic Batch Records Fetched Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_achievement_table_data`(IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(50), IN `p_order_dir` VARCHAR(4), IN `p_start` INT, IN `p_length` INT, IN `p_faculty_id` INT, IN `p_achievement_type` INT, IN `p_login_id` INT, IN `p_dept` INT)
BEGIN
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
    LEFT JOIN svcet_tbl_faculty_official_details AS o
    ON p.faculty_id = o.faculty_id
    WHERE a.achievement_deleted = 0
      AND p.faculty_deleted = 0
      AND p.faculty_status = 1
      AND o.faculty_official_deleted = 0
      AND (p_dept = 0 OR o.dept_id = p_dept);

    -- Calculate filtered records based on input conditions
    SELECT COUNT(*) INTO filtered_records
    FROM svcet_tbl_faculty_achievements AS a
    JOIN svcet_tbl_dev_general AS g 
    ON a.achievement_type = g.general_id
    JOIN svcet_tbl_faculty_personal_info AS p
    ON a.faculty_id = p.faculty_id
    LEFT JOIN svcet_tbl_faculty_official_details AS o
    ON p.faculty_id = o.faculty_id
    WHERE a.achievement_deleted = 0 
      AND g.general_status = 1
      AND p.faculty_deleted = 0
      AND p.faculty_status = 1
      AND o.faculty_official_deleted = 0
      AND (p_dept = 0 OR o.dept_id = p_dept)
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
    LEFT JOIN svcet_tbl_faculty_official_details AS o
    ON p.faculty_id = o.faculty_id
    WHERE a.achievement_deleted = 0 
      AND g.general_status = 1
      AND p.faculty_deleted = 0
      AND p.faculty_status = 1
      AND o.faculty_official_deleted = 0
      AND (p_dept = 0 OR o.dept_id = p_dept)
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
        'svcet_tbl_faculty_achievements,svcet_tbl_dev_general,svcet_tbl_faculty_personal_info,svcet_tbl_faculty_official_details', 
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_achievements`(IN `p_login_id` INT)
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
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Achievement Records Fetched Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_admission_course`(IN `p_faculty_id` INT, IN `p_student_user_id` INT)
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
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Contact Details Fetched Successfully!' AS message;
    END IF;

END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_blood_group`(IN `p_login_id` INT)
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
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Blood Group Records Fetched Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_caste`(IN `p_login_id` INT)
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
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Caste Records Fetched Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_class_advisors`(IN `p_dept_id` INT, IN `p_year_of_study_id` INT, IN `p_login_id` INT, IN `p_faculty_id` INT)
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
            AND (fac_adv.faculty_class_advisors_status = 1 OR fac_adv.faculty_class_advisors_status IS NULL)

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
            AND (fac_adv.faculty_class_advisors_status = 1 OR fac_adv.faculty_class_advisors_status IS NULL)
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
    
    AND (
        fac_adv.faculty_class_advisors_deleted = 0 
        OR fac_adv.faculty_class_advisors_deleted IS NULL
    )
    AND (
        p_dept_id = 0 
        OR fac_adv.dept_id = p_dept_id
    )
    AND (
        p_year_of_study_id = 0 
        OR yos.year_of_study_id = p_year_of_study_id
    )

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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_commitee_list`(IN `p_login_id` INT, IN `p_dept_id` INT)
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_community`(IN `p_login_id` INT)
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
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Community Records Fetched Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_department_list`(IN `p_login_id` INT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_dept_sem_list`(IN p_login_id INT, IN p_dept_id INT)
BEGIN

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
        s.sem_id AS id, 
        s.sem_title AS title,
        d.sem_duration_id AS duration_id,
        d.sem_duration_title AS duration_title,
        d.sem_duration_start_date AS sem_duration_start_date,
        d.sem_duration_status AS sem_duration_status,
        yos.year_of_study_id AS year_id,
        yos.year_of_study_title AS year_title,
        ay.academic_year_id AS year_id,
        ay.academic_year_title AS year_title,
        ab.academic_batch_id AS batch_id,
        ab.academic_batch_title AS batch_title
    FROM svcet_tbl_dev_sem s
    LEFT JOIN svcet_tbl_dev_sem_duration d 
        ON s.sem_duration_id = d.sem_duration_id AND d.sem_duration_delete = 0
    LEFT JOIN svcet_tbl_dev_year_of_study yos 
        ON s.year_of_study_id = yos.year_of_study_id AND yos.year_of_study_delete = 0
    LEFT JOIN svcet_tbl_dev_academic_year ay 
        ON s.academic_year_id = ay.academic_year_id AND ay.academic_year_deleted = 0
    LEFT JOIN svcet_tbl_dev_academic_batch ab 
        ON s.academic_batch_id = ab.academic_batch_id AND ab.academic_batch_deleted = 0
    WHERE s.dept_id = p_dept_id 
        AND s.sem_delete = 0 
        AND (s.sem_status = 1 OR s.sem_status = 0)

    ORDER BY s.sem_title; 

    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_sem,svcet_tbl_dev_sem_duration,svcet_tbl_dev_year_of_study,svcet_tbl_dev_academic_year,svcet_tbl_dev_academic_batch', 1);
    -- Return warnings if any were encountered
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Set success message
        SELECT 200 AS status_code, 'success' AS status, 'Semester list fetched successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_dev_account_card_statistics_data`()
BEGIN
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
        SELECT 300 AS status_code, 'warning' AS status, CONCAT(warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Success message when no warnings occur
        SELECT 200 AS status_code, 'success' AS status, 'Data fetched successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_dev_new_account_code_and_roles`(IN `p_portal_type` INT)
BEGIN
    DECLARE last_account_code INT;
    DECLARE prefix_title VARCHAR(255);
    DECLARE combined_code VARCHAR(255);
    DECLARE new_account_id INT;
    DECLARE new_account_username VARCHAR(255);

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Return error details
        SELECT 500 AS status_code, 'error' AS status, 'An error occurred during procedure execution.' AS message;
        ROLLBACK;
    END;

    START TRANSACTION;

    -- Fetch the last account_code for the given portal type
    SELECT 
        MAX(account_code) INTO last_account_code
    FROM 
        svcet_tbl_accounts
    WHERE 
        account_portal_type = p_portal_type
        
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
        AND prefixes_delete = 0;

    -- Concatenate the prefix title with the incremented account code
    SET combined_code = CONCAT(prefix_title, last_account_code);

    -- Insert a new record into svcet_tbl_accounts if p_portal_type = 2
    IF p_portal_type = 2 THEN
        INSERT INTO svcet_tbl_accounts (
            account_username,
            account_password,
            account_portal_type,
            account_code,
            role_id,
            account_status,
            deleted
        )
        VALUES (
            combined_code,                                  -- account_username
            'UUZJSEt4MnRFL1AvTklHODJuUGZVUT09Ojr8wohuZSnuxtTfIFqcSyzT', -- account_password
            p_portal_type,                                 -- account_portal_type
            last_account_code,                             -- account_code
            0,                                             -- role_id (set to 0 or default if undefined)
            2,                                             -- account_status
            0                                              -- deleted
        );

        -- Retrieve the last inserted account_id and username
        SET new_account_id = LAST_INSERT_ID();
        SET new_account_username = combined_code;
    END IF;
SELECT 
        last_account_code AS new_account_code,  -- Incremented account code
        prefix_title AS prefix_title;

    -- Fetch roles and other required data
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
        AND roles.role_deleted = 0
        AND roles.role_status = 1
        AND prefixes.prefixes_status = 1
        AND prefixes.prefixes_delete = 0;

    -- Commit the transaction
    COMMIT;

    -- Return success message along with new account_id and account_username if applicable
    IF p_portal_type = 2 THEN
        SELECT 
            200 AS status_code, 
            'success' AS status, 
            'Data fetched, account code incremented, and new account created successfully!' AS message,
            new_account_id AS account_id,
            new_account_username AS account_username;
    ELSE
        SELECT 
            200 AS status_code, 
            'success' AS status, 
            'Data fetched and account code incremented successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_education_boards`(IN `p_login_id` INT)
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
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Education Board Records Fetched Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_education_degrees`(IN `p_login_id` INT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_education_specializations`(IN `p_login_id` INT)
BEGIN
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
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Specializations Fetched Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty`(IN `p_login_id` INT, IN `p_faculty_id_json` JSON)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty_achievement`(IN `p_faculty_login_id` INT, IN `p_achievement_id` INT)
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty_address_data`(IN `p_faculty_id` INT, IN `p_login_id` INT)
BEGIN
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

    -- Check if faculty_id exists and fetch address details
    SELECT COUNT(*) INTO @record_exists
    FROM svcet_tbl_faculty_personal_info
    WHERE faculty_id = p_faculty_id
      AND (faculty_deleted = 0 OR faculty_deleted IS NULL);  -- Only not deleted or NULL

    -- If faculty_id exists, fetch address details
    IF @record_exists > 0 THEN
        SELECT 
            COALESCE(faculty_address_no, '') AS faculty_address_no,
            COALESCE(faculty_address_street, '') AS faculty_address_street,
            COALESCE(faculty_address_locality, '') AS faculty_address_locality,
            COALESCE(faculty_address_pincode, '') AS faculty_address_pincode,
            COALESCE(faculty_address_city, '') AS faculty_address_city,
            COALESCE(faculty_address_district, '') AS faculty_address_district,
            COALESCE(faculty_address_state, '') AS faculty_address_state,
            COALESCE(faculty_address_country, '') AS faculty_address_country
        FROM 
            svcet_tbl_faculty_personal_info
        WHERE 
            faculty_id = p_faculty_id
            AND (faculty_deleted = 0 OR faculty_deleted IS NULL);
    ELSE
        -- If no record exists, return empty address details
        SELECT 
            '' AS faculty_address_no,
            '' AS faculty_address_street,
            '' AS faculty_address_locality,
            '' AS faculty_address_pincode,
            '' AS faculty_address_city,
            '' AS faculty_address_district,
            '' AS faculty_address_state,
            '' AS faculty_address_country;
    END IF;

    -- Log the activity
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_personal_info', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Address Details Fetched Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty_authorities`(IN `p_login_id` INT, IN `p_faculty_id` INT, IN `p_fetch_type` INT)
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
    COALESCE(d.dept_title, '') AS dept_title -- Fetch dept_title or empty string if no match
FROM
    svcet_tbl_faculty_authorities fa
LEFT JOIN 
    svcet_tbl_dev_dept d ON fa.dept_id = d.dept_id -- Include all rows from faculty authorities
WHERE 
    fa.faculty_authorities_status IN (1, 3) -- Active and Completed
    OR fa.faculty_authorities_status IS NULL

UNION

SELECT 
    NULL AS faculty_authorities_id,
    NULL AS faculty_id,
    NULL AS faculty_authorities_group_id,
    NULL AS authority_title,
    d.dept_id,
    NULL AS effective_from,
    NULL AS effective_to,
    NULL AS faculty_authorities_status,
    NULL AS faculty_authorities_deleted,
    d.dept_title
FROM
    svcet_tbl_dev_dept d
WHERE
    d.dept_id NOT IN (SELECT DISTINCT dept_id FROM svcet_tbl_faculty_authorities)

ORDER BY 
    faculty_authorities_status IS NULL,  -- NULL status at the end
    FIELD(faculty_authorities_status, 1, 3), -- Active first, then Completed
    effective_from;




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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty_by_dept`(IN `p_login_id` INT, IN `p_dept_id` INT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty_contact_data`(IN `p_faculty_id` INT, IN `p_login_id` INT)
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

    -- Check if faculty_id exists and fetch contact details
    SELECT COUNT(*) INTO @record_exists
    FROM svcet_tbl_faculty_personal_info
    WHERE faculty_id = p_faculty_id
      AND (faculty_deleted = 0 OR faculty_deleted IS NULL); -- Only not deleted or NULL

    -- If faculty_id exists, fetch contact details
    IF @record_exists > 0 THEN
        SELECT 
            COALESCE(faculty_mobile_number, '') AS faculty_mobile_number,
            COALESCE(faculty_alternative_contact_number, '') AS faculty_alternative_contact_number,
            COALESCE(faculty_whatsapp_number, '') AS faculty_whatsapp_number,
            COALESCE(faculty_personal_mail_id, '') AS faculty_personal_mail_id,
            COALESCE(faculty_official_mail_id, '') AS faculty_official_mail_id
        FROM 
            svcet_tbl_faculty_personal_info
        WHERE 
            faculty_id = p_faculty_id
            AND (faculty_deleted = 0 OR faculty_deleted IS NULL);
    ELSE
        -- If no record exists, return empty contact details
        SELECT 
            '' AS faculty_mobile_number,
            '' AS faculty_alternative_contact_number,
            '' AS faculty_whatsapp_number,
            '' AS faculty_personal_mail_id,
            '' AS faculty_official_mail_id;
    END IF;

    -- Log the activity
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_personal_info', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Contact Details Fetched Successfully!' AS message;
    END IF;

END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty_designation`(IN `p_login_id` INT)
BEGIN
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
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Designations Fetched Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty_document_data`(IN `p_faculty_id` INT, IN `p_login_id` INT)
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty_documents_prefixes`(IN `p_login_id` INT)
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty_education_degrees_data`(IN `user_id` INT, IN `login_id` INT)
BEGIN
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE record_exists INT;

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = RETURNED_SQLSTATE,
            error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Check if the faculty_id and level 4 data exists
    SELECT COUNT(*) INTO record_exists
    FROM svcet_tbl_faculty_education fe
    WHERE fe.faculty_edu_faculty_id = user_id
    AND fe.faculty_edu_level = 3; 

    -- If no data exists, return with empty values
    IF record_exists = 0 THEN

        SELECT 
            NULL AS faculty_edu_id,
            user_id AS faculty_edu_faculty_id,
            NULL AS faculty_edu_level,
            '' AS faculty_edu_board,
            '' AS faculty_edu_institution_name,
            '' AS faculty_edu_degree,
            '' AS degree_title,
            '' AS faculty_edu_specialization,
            '' AS specialization_title,
            '' AS faculty_edu_passed_out_year,
            '' AS faculty_edu_cgpa,
            '' AS faculty_edu_percentage,
            '' AS faculty_edu_document,
            '' AS faculty_edu_verified_status;
    ELSE
        -- Fetch faculty education degree data with degree and specialization titles
        SELECT 
            fe.faculty_edu_id,
            fe.faculty_edu_faculty_id,
            fe.faculty_edu_level,
            COALESCE(fe.faculty_edu_board, '') AS faculty_edu_board,
            COALESCE(fe.faculty_edu_institution_name, '') AS faculty_edu_institution_name,
            COALESCE(fe.faculty_edu_degree, '') AS faculty_edu_degree,
            COALESCE(d.general_title, '') AS degree_title,
            COALESCE(fe.faculty_edu_specialization, '') AS faculty_edu_specialization,
            COALESCE(s.general_title, '') AS specialization_title,
            COALESCE(fe.faculty_edu_passed_out_year, '') AS faculty_edu_passed_out_year,
            COALESCE(fe.faculty_edu_cgpa, '') AS faculty_edu_cgpa,
            COALESCE(fe.faculty_edu_percentage, '') AS faculty_edu_percentage,
            COALESCE(fe.faculty_edu_document, '') AS faculty_edu_document,
            COALESCE(fe.faculty_edu_verified_status, '') AS faculty_edu_verified_status
        FROM 
            svcet_tbl_faculty_education fe
        LEFT JOIN 
            svcet_tbl_dev_general d 
            ON fe.faculty_edu_degree = d.general_id 
            AND d.general_group_id = 9 
            AND (d.general_status = 1 OR d.general_status IS NULL)  -- Active status or NULL
            AND (d.general_delete = 0 OR d.general_delete IS NULL)  -- Not deleted or NULL
        LEFT JOIN 
            svcet_tbl_dev_general s 
            ON fe.faculty_edu_specialization = s.general_id 
            AND s.general_group_id = 12 
            AND (s.general_status = 1 OR s.general_status IS NULL)  -- Active status or NULL
            AND (s.general_delete = 0 OR s.general_delete IS NULL)  -- Not deleted or NULL
        WHERE 
            fe.faculty_edu_faculty_id = user_id
            AND (fe.faculty_edu_level = 3 OR fe.faculty_edu_level IS NULL)
            AND (fe.faculty_edu_deleted = 0 OR fe.faculty_edu_deleted IS NULL);
    END IF;

    -- Return success message if data was fetched
    SELECT 200 AS status_code, 'success' AS status, 'Faculty education degree data fetched successfully.' AS message;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty_education_schoolings_data`(IN `p_user_id` INT, IN `p_login_id` INT)
BEGIN
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

    -- Check if SSLC education data exists
    SELECT COUNT(*) INTO @sslc_exists
    FROM svcet_tbl_faculty_education fe
    LEFT JOIN svcet_tbl_dev_general dg ON fe.faculty_edu_board = dg.general_id
    WHERE fe.faculty_edu_faculty_id = p_user_id
      AND fe.faculty_edu_level = 1  -- 1 for SSLC
      AND (dg.general_group_id = 11 OR dg.general_group_id IS NULL)
      AND (dg.general_status = 1 OR dg.general_status IS NULL)
      AND (dg.general_delete = 0 OR dg.general_delete IS NULL);

    -- Fetch SSLC education data if exists
    IF @sslc_exists > 0 THEN
        SELECT 
            COALESCE(fe.faculty_edu_institution_name, '') AS sslc_institution_name,
            COALESCE(fe.faculty_edu_board, '') AS education_board,
            COALESCE(dg.general_title, '') AS board_title,
            COALESCE(fe.faculty_edu_passed_out_year, '') AS sslc_passed_out_year,
            COALESCE(fe.faculty_edu_percentage, '') AS sslc_percentage
        FROM 
            svcet_tbl_faculty_education fe
        LEFT JOIN 
            svcet_tbl_dev_general dg ON fe.faculty_edu_board = dg.general_id
        WHERE 
            fe.faculty_edu_faculty_id = p_user_id
            AND fe.faculty_edu_level = 1  -- 1 for SSLC
            AND (dg.general_group_id = 11 OR dg.general_group_id IS NULL)
            AND (dg.general_status = 1 OR dg.general_status IS NULL)
            AND (dg.general_delete = 0 OR dg.general_delete IS NULL);
    ELSE
        -- Return empty values if no SSLC records exist
        SELECT 
            '' AS sslc_institution_name,
            '' AS education_board,
            '' AS board_title,
            '' AS sslc_passed_out_year,
            '' AS sslc_percentage;
    END IF;

    -- Check if HSC education data exists
    SELECT COUNT(*) INTO @hsc_exists
    FROM svcet_tbl_faculty_education fe
    LEFT JOIN svcet_tbl_dev_general dg ON fe.faculty_edu_board = dg.general_id
    LEFT JOIN svcet_tbl_dev_general sg ON fe.faculty_edu_specialization = sg.general_id
    WHERE fe.faculty_edu_faculty_id = p_user_id
      AND fe.faculty_edu_level = 2  -- 2 for HSC
      AND (dg.general_group_id = 11 OR dg.general_group_id IS NULL)
      AND (dg.general_status = 1 OR dg.general_status IS NULL)
      AND (dg.general_delete = 0 OR dg.general_delete IS NULL);

    -- Fetch HSC education data if exists
    IF @hsc_exists > 0 THEN
        SELECT 
            COALESCE(fe.faculty_edu_institution_name, '') AS hsc_institution_name,
            COALESCE(fe.faculty_edu_board, '') AS education_board,
            COALESCE(fe.faculty_edu_specialization, '') AS specialization,
            COALESCE(dg.general_title, '') AS board_title,
            COALESCE(fe.faculty_edu_passed_out_year, '') AS hsc_passed_out_year,
            COALESCE(fe.faculty_edu_percentage, '') AS hsc_percentage,
            COALESCE(sg.general_title, '') AS specialization_title
        FROM 
            svcet_tbl_faculty_education fe
        LEFT JOIN 
            svcet_tbl_dev_general dg ON fe.faculty_edu_board = dg.general_id
        LEFT JOIN 
            svcet_tbl_dev_general sg ON fe.faculty_edu_specialization = sg.general_id
        WHERE 
            fe.faculty_edu_faculty_id = p_user_id
            AND fe.faculty_edu_level = 2  -- 2 for HSC
            AND (dg.general_group_id = 11 OR dg.general_group_id IS NULL)
            AND (dg.general_status = 1 OR dg.general_status IS NULL)
            AND (dg.general_delete = 0 OR dg.general_delete IS NULL);
    ELSE
        -- Return empty values if no HSC records exist
        SELECT 
            '' AS hsc_institution_name,
            '' AS education_board,
            '' AS specialization,
            '' AS board_title,
            '' AS hsc_passed_out_year,
            '' AS hsc_percentage,
            '' AS specialization_title;
    END IF;

    -- Log the user activity
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_education', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Education Data Fetched Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty_experience_data`(IN `p_user_id` INT, IN `p_login_id` INT)
BEGIN
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

    -- Check if faculty experience records exist for the given faculty ID
    SELECT COUNT(*) INTO @experience_exists
    FROM svcet_tbl_faculty_experience
    WHERE faculty_exp_faculty_id = p_user_id
      AND (faculty_exp_status = 1 OR faculty_exp_status IS NULL)  -- Active status or NULL
      AND (faculty_exp_deleted = 0 OR faculty_exp_deleted IS NULL);  -- Not deleted or NULL

    -- If experience records exist, fetch the data
    IF @experience_exists > 0 THEN
        SELECT 
            faculty_exp_id,
            faculty_exp_faculty_id,
            COALESCE(faculty_exp_field_of_experience, '') AS faculty_exp_field_of_experience,
            COALESCE(faculty_exp_industry_name, '') AS faculty_exp_industry_name,
            COALESCE(faculty_exp_designation, '') AS faculty_exp_designation,
            COALESCE(faculty_exp_specialization, '') AS faculty_exp_specialization,
            COALESCE(faculty_exp_start_date, '') AS faculty_exp_start_date,
            COALESCE(faculty_exp_end_date, '') AS faculty_exp_end_date,
            faculty_exp_status,
            faculty_exp_deleted
        FROM 
            svcet_tbl_faculty_experience
        WHERE 
            faculty_exp_faculty_id = p_user_id
            AND (faculty_exp_status = 1 OR faculty_exp_status IS NULL)
            AND (faculty_exp_deleted = 0 OR faculty_exp_deleted IS NULL);
    ELSE
        -- Return empty values if no experience records exist
        SELECT 
            '' AS faculty_exp_id,
            '' AS faculty_exp_faculty_id,
            '' AS faculty_exp_field_of_experience,
            '' AS faculty_exp_industry_name,
            '' AS faculty_exp_designation,
            '' AS faculty_exp_specialization,
            '' AS faculty_exp_start_date,
            '' AS faculty_exp_end_date,
            NULL AS faculty_exp_status,
            NULL AS faculty_exp_deleted;
    END IF;

    -- Log the action as a fetch
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_experience', 1);

    -- Check for warnings and return appropriate message
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Experience Fetched Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty_experience_designation`(IN `p_login_id` INT)
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty_individual_admission_data`(IN `p_faculty_login_id` INT, IN `p_student_user_id` INT)
BEGIN
    -- Declare variables to capture error and warning messages
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Declare variables to capture outputs
    DECLARE personal_data TEXT DEFAULT '';
    DECLARE parent_data TEXT DEFAULT '';
    DECLARE contact_data TEXT DEFAULT '';
    DECLARE address_data TEXT DEFAULT '';
    DECLARE sslc_data TEXT DEFAULT '';
    DECLARE hsc_data TEXT DEFAULT '';
    DECLARE degrees_data TEXT DEFAULT '';
    DECLARE course_data TEXT DEFAULT '';
    DECLARE document_data TEXT DEFAULT '';

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

    -- Call each procedure and handle empty results
    IF warning_count = 0 THEN
        CALL fetch_stu_admission_personal_data(p_faculty_login_id, p_student_user_id);
        IF ROW_COUNT() = 0 THEN
            SET personal_data = '';
        END IF;
    END IF;

    IF warning_count = 0 THEN
        CALL fetch_stu_admission_parent_data(p_faculty_login_id, p_student_user_id);
        IF ROW_COUNT() = 0 THEN
            SET parent_data = '';
        END IF;
    END IF;

    IF warning_count = 0 THEN
        CALL fetch_stu_admission_contact_data(p_faculty_login_id, p_student_user_id);
        IF ROW_COUNT() = 0 THEN
            SET contact_data = '';
        END IF;
    END IF;

    IF warning_count = 0 THEN
        CALL fetch_stu_admission_address_data(p_faculty_login_id, p_student_user_id);
        IF ROW_COUNT() = 0 THEN
            SET address_data = '';
        END IF;
    END IF;
     IF warning_count = 0 THEN
        CALL fetch_stu_admission_education_schoolings_data(p_student_user_id, p_faculty_login_id);
        IF ROW_COUNT() = 0 THEN
            SET sslc_data = '';
        END IF;
    END IF;
    IF warning_count = 0 THEN
        CALL fetch_stu_admission_education_degrees(p_student_user_id, p_faculty_login_id);
         IF ROW_COUNT() = 0 THEN
            SET sslc_data = '';
        END IF;
    END IF;
 IF warning_count = 0 THEN
        CALL fetch_pr_admission_course(p_faculty_login_id, p_student_user_id);
        IF ROW_COUNT() = 0 THEN
            SET course_data = '';
        END IF;
    END IF;
    
 IF warning_count = 0 THEN
        CALL fetch_pr_student_document_data(p_student_user_id, p_faculty_login_id);
        IF ROW_COUNT() = 0 THEN
            SET document_data = '';
        END IF;
    END IF;

    -- Return combined result or status
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Personal Info fetched successfully!' AS message,
               personal_data, parent_data, contact_data, address_data, sslc_data, hsc_data, degrees_data, course_data, document_data;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty_mentor_students`(IN `p_login_id` INT, IN `p_faculty_id` INT, IN `p_dept_id` INT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty_name_list`(IN `input_dept_id` INT, IN `p_login_id` INT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty_official_data`(IN `p_user_id` INT, IN `p_login_id` INT)
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty_personal_data`(IN `p_faculty_login_id` INT, IN `p_faculty_user_id` INT)
BEGIN
    -- Declare variables for error handling and faculty data
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

    -- Check if faculty_id exists
    SELECT COUNT(*) INTO @record_exists
    FROM svcet_tbl_faculty_personal_info
    WHERE faculty_id = p_faculty_user_id AND faculty_deleted = 0;

    -- If faculty_id does not exist, return empty faculty data
    IF @record_exists = 0 THEN
        SELECT 
            NULL AS faculty_account_id,
            '' AS faculty_first_name,
            '' AS faculty_middle_name,
            '' AS faculty_last_name,
            '' AS faculty_initial,
            '' AS faculty_salutation,
            NULL AS faculty_dob,
            '' AS faculty_gender,
            '' AS faculty_blood_group,
            '' AS faculty_religion,
            '' AS faculty_caste,
            '' AS faculty_community,
            '' AS faculty_nationality,
            '' AS faculty_aadhar_number,
            '' AS faculty_salutation_title,
            '' AS faculty_gender_title,
            '' AS faculty_blood_group_title,
            '' AS faculty_religion_title,
            '' AS faculty_caste_title,
            '' AS faculty_community_title,
            '' AS faculty_nationality_title,
            '' AS faculty_marital_status_title,
            '' AS faculty_marital_status;
    ELSE
        -- Fetch faculty personal info data
        SELECT 
            f.faculty_account_id,
            COALESCE(f.faculty_first_name, '') AS faculty_first_name,
            COALESCE(f.faculty_middle_name, '') AS faculty_middle_name,
            COALESCE(f.faculty_last_name, '') AS faculty_last_name,
            COALESCE(f.faculty_initial, '') AS faculty_initial,
            COALESCE(sg_salutation.general_title, '') AS faculty_salutation_title,
            COALESCE(sg_gender.general_title, '') AS faculty_gender_title,
            COALESCE(sg_blood_group.general_title, '') AS faculty_blood_group_title,
            COALESCE(sg_religion.general_title, '') AS faculty_religion_title,
            COALESCE(sg_caste.general_title, '') AS faculty_caste_title,
            COALESCE(sg_community.general_title, '') AS faculty_community_title,
            COALESCE(sg_nationality.general_title, '') AS faculty_nationality_title,
            COALESCE(sg_marital_status.general_title, '') AS faculty_marital_status_title,
            COALESCE(f.faculty_salutation, '') AS faculty_salutation,
            COALESCE(f.faculty_dob, NULL) AS faculty_dob,
            COALESCE(f.faculty_gender, '') AS faculty_gender,
            COALESCE(f.faculty_blood_group, '') AS faculty_blood_group,
            COALESCE(f.faculty_religion, '') AS faculty_religion,
            COALESCE(f.faculty_caste, '') AS faculty_caste,
            COALESCE(f.faculty_community, '') AS faculty_community,
            COALESCE(f.faculty_nationality, '') AS faculty_nationality,
            COALESCE(f.faculty_aadhar_number, '') AS faculty_aadhar_number,
            COALESCE(f.faculty_marital_status, '') AS faculty_marital_status
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
        f.faculty_id = p_faculty_user_id
            AND f.faculty_deleted = 0;
    END IF;

    -- Record user activity log after successful query execution
    CALL insert_user_activity_log(p_faculty_login_id, 'svcet_tbl_faculty_personal_info', 1);

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

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty_personal_single_data`(IN `p_faculty_login_id` INT, IN `p_faculty_user_id` INT)
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty_skill_data`(IN `p_faculty_id` INT, IN `p_login_id` INT)
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

    -- Check if faculty skills records exist for the given faculty ID
    SELECT COUNT(*) INTO @skills_exists
    FROM svcet_tbl_faculty_skills
    WHERE faculty_skill_faculty_id = p_faculty_id
      AND (faculty_skill_status = 1 OR faculty_skill_status IS NULL)  -- Active status or NULL
      AND (faculty_skill_deleted = 0 OR faculty_skill_deleted IS NULL);  -- Not deleted or NULL

    -- If skills records exist, fetch the data
    IF @skills_exists > 0 THEN
        SELECT 
            faculty_skill_id,
            faculty_skill_faculty_id,
            COALESCE(faculty_skill_type, '') AS faculty_skill_type,
            COALESCE(faculty_skill_name, '') AS faculty_skill_name
        FROM 
            svcet_tbl_faculty_skills
        WHERE 
            faculty_skill_faculty_id = p_faculty_id
            AND (faculty_skill_status = 1 OR faculty_skill_status IS NULL)
            AND (faculty_skill_deleted = 0 OR faculty_skill_deleted IS NULL);
    ELSE
        -- Return empty values if no skills records exist
        SELECT 
            '' AS faculty_skill_id,
            '' AS faculty_skill_faculty_id,
            '' AS faculty_skill_type,
            '' AS faculty_skill_name;
    END IF;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_faculty_skills', 1);

    -- Check for warnings and return appropriate message
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Faculty Skills Fetched Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_faculty_skills`(IN `p_faculty_skill_faculty_id` INT, IN `p_login_id` INT)
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_gender`(IN `p_login_id` INT)
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
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Gender Records Fetched Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_institution_logo`(IN `p_login_id` INT)
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

    SELECT institution_logo
    FROM svcet_tbl_dev_institution
    LIMIT 1;

    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_institution', 1);

        -- If there were any warnings, return a warning status
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, CONCAT(warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Success message when no warnings occur
        SELECT 200 AS status_code, 'success' AS status, 'Data fetched successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_marital_status`(IN `p_login_id` INT)
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
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Marital Status Records Fetched Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_nationality`(IN `p_login_id` INT)
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
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Nationality Records Fetched Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_religion`(IN `p_login_id` INT)
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
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Religion Records Fetched Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_salutations`(IN `p_login_id` INT)
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
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Salutations Fetched Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_single_faculty_achievement`(IN `p_login_id` INT, IN `p_achievement_id` INT)
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_student_commitee_list`(IN `p_login_id` INT, IN `p_dept_id` INT, IN `p_role_id` INT)
BEGIN

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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_student_document_data`(IN `p_student_id` INT, IN `p_login_id` INT)
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

    -- Select document data for the specified faculty ID where the document is active and not deleted
    SELECT 
    COALESCE(student_doc_id, '') AS student_doc_id,
    COALESCE(student_doc_type, '') AS student_doc_type,
    COALESCE(student_doc_path, '') AS student_doc_path
    FROM 
        svcet_tbl_student_documents
        WHERE 
        student_doc_student_id = p_student_id
        AND (student_doc_status = 1 OR student_doc_status IS NULL)     -- Active status
    AND (student_doc_deleted = 0 OR student_doc_deleted IS NULL);
    

   

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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_student_name_list`(IN `p_dept_id` INT, IN `p_year_of_study_id` INT, IN `p_section_id` INT, IN `p_group_id` INT, IN `p_login_id` INT)
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_student_representatives_list`(IN `p_login_id` INT, IN `p_year_of_study_id` INT, IN `p_dept_id` INT, IN `p_student_id` INT)
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_student_tables_admission`(IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(50), IN `p_order_dir` VARCHAR(4), IN `p_start` INT, IN `p_length` INT, IN `p_admission_status` INT, IN `p_admission_method` INT, IN `p_admission_date` DATE, IN `p_login_id` INT)
BEGIN
    DECLARE total_records INT DEFAULT 0;
    DECLARE filtered_records INT DEFAULT 0;

    -- Warning handling
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Warning handler
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Get total record count
    SELECT COUNT(*) INTO total_records
    FROM svcet_tbl_student_admission_info AS sai
    LEFT JOIN svcet_tbl_student_personal_info AS spi ON sai.student_admission_student_id = spi.student_id
    WHERE sai.admission_deleted = 0;

    -- Get filtered record count
    SELECT COUNT(*) INTO filtered_records
    FROM svcet_tbl_student_admission_info AS sai
    LEFT JOIN svcet_tbl_student_personal_info AS spi ON sai.student_admission_student_id = spi.student_id
    WHERE sai.admission_deleted = 0
      AND (spi.student_first_name LIKE CONCAT('%', p_search_value, '%') 
           OR spi.student_last_name LIKE CONCAT('%', p_search_value, '%'))
      AND (p_admission_status IS NULL OR sai.admission_status = p_admission_status)
      AND (p_admission_method = 0 OR sai.student_admission_category = p_admission_method)
      AND (p_admission_date IS NULL OR sai.student_admission_date = p_admission_date);

    -- Fetch paginated data with sorting
    SELECT 
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
      AND (spi.student_first_name LIKE CONCAT('%', p_search_value, '%') 
           OR spi.student_last_name LIKE CONCAT('%', p_search_value, '%'))
      AND (p_admission_status IS NULL OR sai.admission_status = p_admission_status)
      AND (p_admission_method = 0 OR sai.student_admission_category = p_admission_method)
      AND (p_admission_date IS NULL OR sai.student_admission_date = p_admission_date)
    ORDER BY 
        CASE 
            WHEN p_sort_column = 'student_first_name' THEN spi.student_first_name
            WHEN p_sort_column = 'student_last_name' THEN spi.student_last_name
            WHEN p_sort_column = 'student_admission_date' THEN sai.student_admission_date
            ELSE sai.student_admission_student_id
        END 
        LIMIT p_start, p_length;

    -- Return total and filtered records
    SELECT total_records AS total_records, filtered_records AS filtered_records;

    -- Insert user activity log
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_admission_info,svcet_tbl_student_personal_info', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Admission Records Fetched Successfully!' AS message;
    END IF;

END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_students`(IN `p_login_id` INT, IN `p_dept_id` INT, IN `p_year_of_study_id` INT, IN `p_section_id` INT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_view_roles`(IN `p_login_id` INT, IN `p_faculty_id` INT, IN `p_dept_id` INT, IN `p_role_id` INT, IN `p_fetch_type` TINYINT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_pr_year_of_study`(IN `p_login_id` INT, IN `p_dept_id` INT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_stu_admission_address_data`(IN `p_faculty_id` INT, IN `p_student_user_id` INT)
BEGIN
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
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Address Details Fetched Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_stu_admission_contact_data`(IN `p_faculty_id` INT, IN `p_student_user_id` INT)
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
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Contact Details Fetched Successfully!' AS message;
    END IF;

END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_stu_admission_education_degrees`(IN `user_id` INT, IN `p_login_id` INT)
BEGIN
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE records_exist INT DEFAULT 0;

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = RETURNED_SQLSTATE,
            error_message = MESSAGE_TEXT;
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Check if records exist for the given student and level 4
    SELECT COUNT(*) INTO records_exist
    FROM svcet_tbl_student_education se
    WHERE se.student_edu_student_id = user_id
      AND se.student_edu_level = 4;

    -- If records exist, fetch the student education data
    IF records_exist > 0 THEN
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
            svcet_tbl_dev_general d ON se.student_edu_degree = d.general_id 
            AND d.general_group_id = 9 AND d.general_status = 1 AND d.general_delete = 0
        LEFT JOIN 
            svcet_tbl_dev_general s ON se.student_edu_specialization = s.general_id 
            AND s.general_group_id = 12 AND s.general_status = 1 AND s.general_delete = 0
        WHERE 
            se.student_edu_student_id = user_id
            AND se.student_edu_level = 4;
    ELSE
        -- If no records found, return empty data
        SELECT 
            NULL AS student_edu_id,
            NULL AS student_edu_student_id,
            NULL AS student_edu_level,
            NULL AS student_edu_board,
            NULL AS student_edu_institution_name,
            NULL AS student_edu_degree,
            NULL AS degree_title,
            NULL AS student_edu_specialization,
            NULL AS specialization_title,
            NULL AS student_edu_passed_out_year,
            NULL AS student_edu_cgpa,
            NULL AS student_edu_percentage,
            NULL AS student_edu_total_mark,
            NULL AS student_edu_status;
    END IF;

    -- Return success message
    SELECT 200 AS status_code, 'success' AS status, 'Student education data fetched successfully.' AS message;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_stu_admission_education_schoolings_data`(IN `p_user_id` INT, IN `p_login_id` INT)
BEGIN
    -- Declare variables for error and warning handling
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Error handling block
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Capture error details
        GET DIAGNOSTICS CONDITION 1 
            error_code = RETURNED_SQLSTATE, 
            error_message = MESSAGE_TEXT;

        -- Return error response
        SELECT 500 AS status_code, 'error' AS status, error_message AS message;
    END;

    -- Warning handling block
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        -- Increment warning count and capture warning details
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    -- Check if SSLC records exist for the given user
    SELECT COUNT(*) INTO @sslc_exists
    FROM svcet_tbl_student_education se
    WHERE se.student_edu_student_id = p_user_id
      AND se.student_edu_level = 1  -- SSLC level
      AND (se.student_edu_status = 1 OR se.student_edu_status IS NULL)
      AND (se.student_edu_deleted = 0 OR se.student_edu_deleted IS NULL);

    -- Fetch SSLC education data if records exist
    IF @sslc_exists > 0 THEN
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
            AND se.student_edu_level = 1  -- SSLC level
            AND dg.general_group_id = 11  -- Group ID for board
            AND dg.general_status = 1     -- Active status
            AND dg.general_delete = 0;    -- Not deleted
    ELSE
        -- Return empty SSLC data if no records exist
        SELECT 
            '' AS sslc_institution_name,
            '' AS education_board,
            '' AS board_title,
            '' AS sslc_passed_out_year,
            '' AS sslc_percentage,
            '' AS sslc_mark;
    END IF;

    -- Check if HSC records exist for the given user
    SELECT COUNT(*) INTO @hsc_exists
    FROM svcet_tbl_student_education se
    WHERE se.student_edu_student_id = p_user_id
      AND se.student_edu_level = 2  -- HSC level
      AND (se.student_edu_status = 1 OR se.student_edu_status IS NULL)
      AND (se.student_edu_deleted = 0 OR se.student_edu_deleted IS NULL);

    -- Fetch HSC education data if records exist
    IF @hsc_exists > 0 THEN
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
            se.student_edu_specialization = sg.general_id
        WHERE 
            se.student_edu_student_id = p_user_id
            AND se.student_edu_level = 2  -- HSC level
            AND dg.general_group_id = 11  -- Group ID for board
            AND dg.general_status = 1     -- Active status
            AND dg.general_delete = 0;    -- Not deleted
    ELSE
        -- Return empty HSC data if no records exist
        SELECT 
            '' AS hsc_institution_name,
            '' AS education_board,
            '' AS specialization,
            '' AS board_title,
            '' AS hsc_passed_out_year,
            '' AS hsc_percentage,
            '' AS specialization_title,
            '' AS hsc_mark;
    END IF;

    -- Log user activity
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_education', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Education Data Fetched Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_stu_admission_official_data`(IN `p_faculty_id` INT, IN `p_student_user_id` INT)
BEGIN
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
        student_reg_number,
        official_mail_id
        
    FROM svcet_tbl_student_official_details
     WHERE 
    student_id = p_student_user_id;

    -- Call to log the user activity
  CALL insert_user_activity_log(p_faculty_id, 'svcet_tbl_student_official_details', 1);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Official Details Fetched Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_stu_admission_parent_data`(IN `p_faculty_id` INT, IN `p_student_user_id` INT)
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
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Contact Details Fetched Successfully!' AS message;
    END IF;

END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `fetch_stu_admission_personal_data`(IN `p_faculty_login_id` INT, IN `p_student_user_id` INT)
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `insert_achievement_record`(IN `p_faculty_id` INT, IN `p_login_id` INT, IN `p_achievement_type` VARCHAR(255), IN `p_achievement_title` VARCHAR(255), IN `p_achievement_date` DATE, IN `p_achievement_venue` VARCHAR(255), IN `p_file_link` VARCHAR(255))
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
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Record inserted with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Achievement record inserted successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `insert_error_log`(IN `p_login_id` INT, IN `p_error_side` TINYINT, IN `p_page_link` TEXT, IN `p_error_message` TEXT)
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `insert_pr_create_addmission_official`(IN `existing_id` INT, IN `official_mailid` VARCHAR(255), IN `register_number` VARCHAR(20))
BEGIN
    DECLARE v_status_code INT DEFAULT 200;
    DECLARE v_status_message VARCHAR(255) DEFAULT 'Official details inserted successfully.';
    
    -- Declare variables to capture error and warning messages
    DECLARE error_code VARCHAR(5);
    DECLARE error_message VARCHAR(255);
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';
    DECLARE record_exists INT;

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
   
    -- Check if a record with the given existing_id exists
    SELECT COUNT(*) INTO record_exists
    FROM svcet_tbl_student_official_details
    WHERE student_id = existing_id
      AND student_official_details_deleted = 0;

    IF record_exists > 0 THEN
        -- Update the record if it exists
        UPDATE svcet_tbl_student_official_details
        SET 
            student_reg_number = register_number,
           official_mail_id = official_mailid
        WHERE student_id = existing_id;

        SELECT 200 AS status_code, 'success' AS status, 'Official Details Updated Successfully!' AS message;
    ELSE
        -- Insert a new record if no match is found
        INSERT INTO svcet_tbl_student_official_details (
            student_id, student_reg_number,official_mail_id, student_official_details_status, student_official_details_deleted
        )
        VALUES (
            existing_id, register_number,official_mailid, 1, 0 -- Active and not deleted
        );

        SELECT 200 AS status_code, 'success' AS status, 'Official Details Inserted Successfully!' AS message;
    END IF;

    -- Check if there are any warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    END IF;

END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `insert_pr_create_parent_account`(IN `p_parent_user_name` VARCHAR(50), IN `p_parent_first_name` VARCHAR(100), IN `p_parent_middle_name` VARCHAR(255), IN `p_parent_last_name` VARCHAR(100), IN `p_parent_initial` VARCHAR(100), IN `p_parent_mobile_number` VARCHAR(15), IN `p_parent_email_id` VARCHAR(100), IN `p_parent_code` INT, IN `p_parent_role` INT, IN `p_parent_type` TINYINT, IN `p_student_id` INT, IN `p_relationship_type` TINYINT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `insert_pr_dev_create_new_bulk_account`(IN `first_name` VARCHAR(255), IN `middle_name` VARCHAR(255), IN `last_name` VARCHAR(255), IN `name_initial` VARCHAR(10), IN `portal_type` INT, IN `role_id` INT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `insert_pr_dev_create_new_single_account`(IN `first_name` VARCHAR(100), IN `middle_name` VARCHAR(100), IN `last_name` VARCHAR(100), IN `name_initial` VARCHAR(10), IN `portal_type` TINYINT(1), IN `new_account_code` INT(11), IN `username` VARCHAR(50), IN `p_role_id` INT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `insert_stu_create_addmission_contact`(IN `existing_student_id` INT, IN `student_mobile_number` VARCHAR(15), IN `student_alternative_contact_number` VARCHAR(15), IN `student_whatsapp_number` VARCHAR(15), IN `student_email_id` VARCHAR(100))
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `insert_stu_create_addmission_profile`(IN `existing_student_id` INT, IN `first_name` VARCHAR(100), IN `middle_name` VARCHAR(100), IN `last_name` VARCHAR(100), IN `name_initial` VARCHAR(10), IN `dob` DATE, IN `gender` INT(1), IN `aadhar` VARCHAR(20), IN `religion` INT(50), IN `caste` INT(50), IN `community` INT(50), IN `nationality` INT(50), IN `blood_group` INT(5), IN `marital_status` INT(20))
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `insert_stu_create_admission_parent`(IN `existing_student_id` INT, IN `student_father_name` VARCHAR(100), IN `student_father_occupation` VARCHAR(100), IN `student_mother_name` VARCHAR(100), IN `student_mother_occupation` VARCHAR(100), IN `student_guardian_name` VARCHAR(255), IN `student_guardian_occupation` VARCHAR(255))
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `insert_student_address_details`(IN `p_existing_id` INT, IN `p_address_pincode` VARCHAR(20), IN `p_address_no` VARCHAR(255), IN `p_address_street` VARCHAR(100), IN `p_address_locality` VARCHAR(100), IN `p_address_city` VARCHAR(100), IN `p_address_district` VARCHAR(100), IN `p_address_state` VARCHAR(100), IN `p_address_country` VARCHAR(100))
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `insert_user_activity_log`(IN `p_login_id` INT, IN `p_db_table_affected` VARCHAR(255), IN `p_action_type` TINYINT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `login_validate`(IN `p_user_id` INT, IN `p_portal_type` INT, IN `p_log_id` INT, IN `p_user_ip_address` VARCHAR(45), IN `p_successful_login` TINYINT, IN `p_login_status` TINYINT, IN `p_logout` TINYINT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `sp_fetch_faculty_subjects`(IN `p_faculty_id` INT)
BEGIN
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
        SELECT 300 AS status_code, 'warning' AS status, CONCAT(warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Success message when no warnings occur
        SELECT 200 AS status_code, 'success' AS status, 'Data fetched successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `svcet_pr_fetch_subject_availability`(IN `in_day_id` INT, IN `in_period_id` INT, IN `in_subject_id` INT, IN `in_dept_id` INT, IN `in_academic_batch_id` INT, IN `in_academic_year_id` INT, IN `in_year_of_study_id` INT, IN `in_sem_id` INT, IN `in_section_id` INT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_achievement_record`(IN `p_achievement_id` INT, IN `p_faculty_id` INT, IN `p_login_id` INT, IN `p_achievement_type` VARCHAR(255), IN `p_achievement_title` VARCHAR(255), IN `p_achievement_date` DATE, IN `p_achievement_venue` VARCHAR(255), IN `p_file_link` VARCHAR(255))
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_confirmation_student_admission`(IN `p_student_id` INT, IN `p_admission_type` VARCHAR(50), IN `last_account_id` INT, IN `p_student_username` VARCHAR(50))
BEGIN
    -- Declare variables
    

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
           
            account_status = 1
            
        WHERE 
            account_username = p_student_username;
            
   
      

        -- Get the last inserted account ID
        
    END IF;

    -- Update svcet_tbl_student_personal_info with last account ID
   
        UPDATE svcet_tbl_student_personal_info
        SET student_account_id = last_account_id
        WHERE student_id = p_student_id;
    

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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_password`(IN `new_password` TEXT, IN `logged_account_id` INT, IN `logged_login_id` INT)
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

    -- Update the password for the specific account
    UPDATE svcet_tbl_accounts
    SET account_password = new_password
    WHERE account_id = logged_account_id
      AND deleted = 0;

          -- Insert activity log entry
    CALL insert_user_activity_log(logged_login_id, 'svcet_tbl_dev_account', 3);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Password Changed Successfully!' AS message;
    END IF;

END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_pr_decline_student_confirmation`(IN `p_login_id` INT, IN `student_id` INT)
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

    -- Main query to fetch academic batch records
    UPDATE svcet_tbl_student_admission_info
        SET admission_status = 5
        WHERE student_admission_student_id = student_id;

    -- Insert activity log entry
    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_student_admission_info', 3);

    -- Return status based on warnings
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Student Declined successfully✅' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_pr_faculty_authorities_roles`(IN `p_login_id` INT, IN `p_faculty_id_json` JSON, IN `p_faculty_authorities_id_json` JSON, IN `p_dept_id_json` JSON, IN `p_authorities_group_id_json` JSON)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_pr_faculty_dept_class_advisors_roles`(IN `p_login_id` INT, IN `p_faculty_id` JSON, IN `p_faculty_class_advisors_id` JSON, IN `p_ca_year_of_study_id` JSON, IN `p_ca_section_id` JSON, IN `p_faculty_dept_id` JSON)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_pr_faculty_dept_committee_roles`(IN `p_login_id` INT, IN `p_dept_id` INT, IN `committee_id_json` JSON, IN `faculty_id_json` JSON, IN `committee_roles_json` JSON, IN `p_type` INT, IN `p_r_r_id` INT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_pr_faculty_document_profile_info`(IN `p_faculty_id` INT, IN `p_login_id` INT, IN `p_faculty_resume_id` INT, IN `p_faculty_sslc_id` INT, IN `p_faculty_hsc_id` INT, IN `p_faculty_highest_qualification_id` INT, IN `p_faculty_experience_id` JSON, IN `p_sslc` TEXT, IN `p_hsc` TEXT, IN `p_highest_qualification` TEXT, IN `p_resume` TEXT, IN `p_experience` JSON, IN `p_profile_pic` TEXT, IN `p_profile_pic_id` INT)
BEGIN
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
    IF p_resume IS NOT NULL AND p_resume != '' THEN
        IF p_faculty_resume_id = 0 THEN
            INSERT INTO svcet_tbl_faculty_documents (faculty_doc_faculty_id, faculty_doc_type, faculty_doc_path, faculty_doc_status, faculty_doc_deleted)
            VALUES (p_faculty_id, 1, p_resume, 1, 0);
        ELSE
            UPDATE svcet_tbl_faculty_documents
            SET faculty_doc_path = p_resume, faculty_doc_status = 1, faculty_doc_deleted = 0
            WHERE faculty_doc_id = p_faculty_resume_id;
        END IF;
    END IF;

    -- Upsert for SSLC Document
    IF p_sslc IS NOT NULL AND p_sslc != '' THEN
        IF p_faculty_sslc_id = 0 THEN
            INSERT INTO svcet_tbl_faculty_documents (faculty_doc_faculty_id, faculty_doc_type, faculty_doc_path, faculty_doc_status, faculty_doc_deleted)
            VALUES (p_faculty_id, 2, p_sslc, 1, 0);
        ELSE
            UPDATE svcet_tbl_faculty_documents
            SET faculty_doc_path = p_sslc, faculty_doc_status = 1, faculty_doc_deleted = 0
            WHERE faculty_doc_id = p_faculty_sslc_id;
        END IF;
    END IF;

    -- Upsert for HSC Document
    IF p_hsc IS NOT NULL AND p_hsc != '' THEN
        IF p_faculty_hsc_id = 0 THEN
            INSERT INTO svcet_tbl_faculty_documents (faculty_doc_faculty_id, faculty_doc_type, faculty_doc_path, faculty_doc_status, faculty_doc_deleted)
            VALUES (p_faculty_id, 3, p_hsc, 1, 0);
        ELSE
            UPDATE svcet_tbl_faculty_documents
            SET faculty_doc_path = p_hsc, faculty_doc_status = 1, faculty_doc_deleted = 0
            WHERE faculty_doc_id = p_faculty_hsc_id;
        END IF;
    END IF;

    -- Upsert for Highest Qualification Document
    IF p_highest_qualification IS NOT NULL AND p_highest_qualification != '' THEN
        IF p_faculty_highest_qualification_id = 0 THEN
            INSERT INTO svcet_tbl_faculty_documents (faculty_doc_faculty_id, faculty_doc_type, faculty_doc_path, faculty_doc_status, faculty_doc_deleted)
            VALUES (p_faculty_id, 4, p_highest_qualification, 1, 0);
        ELSE
            UPDATE svcet_tbl_faculty_documents
            SET faculty_doc_path = p_highest_qualification, faculty_doc_status = 1, faculty_doc_deleted = 0
            WHERE faculty_doc_id = p_faculty_highest_qualification_id;
        END IF;
    END IF;

    -- Upsert for Profile Pic Document
    IF p_profile_pic IS NOT NULL AND p_profile_pic != '' THEN
        IF p_profile_pic_id = 0 THEN
            INSERT INTO svcet_tbl_faculty_documents (faculty_doc_faculty_id, faculty_doc_type, faculty_doc_path, faculty_doc_status, faculty_doc_deleted)
            VALUES (p_faculty_id, 6, p_profile_pic, 1, 0);
        ELSE
            UPDATE svcet_tbl_faculty_documents
            SET faculty_doc_path = p_profile_pic, faculty_doc_status = 1, faculty_doc_deleted = 0
            WHERE faculty_doc_id = p_profile_pic_id;
        END IF;
    END IF;

    -- Insert or Update Experience Certificates (Multiple Entries)
    IF experience_count > 0 THEN
        experience_loop:LOOP
            IF experience_index >= experience_count THEN
                LEAVE experience_loop;
            END IF;

            -- Extract experience path and document ID from JSON arrays
            SET experience_path = REPLACE(REPLACE(REPLACE(JSON_UNQUOTE(JSON_EXTRACT(p_experience, CONCAT('$[', experience_index, ']'))), '[', ''), ']', ''), '"', '');
            SET experience_doc_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_faculty_experience_id, CONCAT('$[', experience_index, ']'))) AS UNSIGNED);

            -- Upsert logic for each experience certificate
            IF experience_path IS NOT NULL AND experience_path != '' THEN
                IF experience_doc_id = 0 OR experience_doc_id IS NULL THEN
                    INSERT INTO svcet_tbl_faculty_documents (faculty_doc_faculty_id, faculty_doc_type, faculty_doc_path, faculty_doc_status, faculty_doc_deleted)
                    VALUES (p_faculty_id, 5, experience_path, 1, 0);
                ELSE
                    UPDATE svcet_tbl_faculty_documents
                    SET faculty_doc_path = experience_path, faculty_doc_status = 1, faculty_doc_deleted = 0
                    WHERE faculty_doc_id = experience_doc_id;
                END IF;
            END IF;

            SET experience_index = experience_index + 1;
        END LOOP;
    END IF;

    -- Update faculty status
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_pr_faculty_education_degree_profile_info`(IN `p_faculty_id` INT, IN `p_login_id` INT, IN `p_degree_institution_name` JSON, IN `p_education_degree` JSON, IN `p_education_degree_specialization` JSON, IN `p_degree_passed_out_year` JSON, IN `p_degree_percentage` JSON, IN `p_degree_cgpa` JSON, IN `p_degree_id` JSON)
BEGIN
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
                3, -- Degree level
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_pr_faculty_education_schoolings_profile_info`(IN `p_sslc_institution_name` VARCHAR(255), IN `p_education_board` INT, IN `p_sslc_passed_out_year` YEAR, IN `p_sslc_percentage` DECIMAL(5,2), IN `p_hsc_institution_name` VARCHAR(255), IN `p_education_hsc_board` INT, IN `p_education_hsc_specialization` INT, IN `p_hsc_passed_out_year` YEAR, IN `p_hsc_percentage` DECIMAL(5,2), IN `p_faculty_id` INT, IN `p_login_id` INT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_pr_faculty_experience_profile_info`(IN `p_experience_id` JSON, IN `p_field_of_experience` JSON, IN `p_experience_industry_name` JSON, IN `p_experience_designation` JSON, IN `p_experience_industry_department` JSON, IN `p_experience_industry_start_date` JSON, IN `p_experience_industry_end_date` JSON, IN `p_faculty_id` INT, IN `p_login_id` INT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_pr_faculty_mentor_role`(IN `p_login_id` INT, IN `p_update_type` INT, IN `p_mentor_details_json` JSON, IN `p_from_faculty_id` INT, IN `p_to_faculty_id` INT, IN `p_dept_id` INT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_pr_faculty_personal_address_profile_info`(IN `p_faculty_id` INT, IN `p_login_id` INT, IN `p_faculty_address_no` VARCHAR(20), IN `p_faculty_address_street` VARCHAR(255), IN `p_faculty_address_locality` VARCHAR(100), IN `p_faculty_address_pincode` VARCHAR(10), IN `p_faculty_address_city` VARCHAR(100), IN `p_faculty_address_district` VARCHAR(100), IN `p_faculty_address_state` VARCHAR(100), IN `p_faculty_address_country` VARCHAR(100))
BEGIN
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
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Address Details Updated Successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_pr_faculty_personal_contact_profile_info`(IN `p_faculty_id` INT, IN `p_login_id` INT, IN `p_official_mail_id` VARCHAR(100), IN `p_personal_mail_id` VARCHAR(100), IN `p_mobile_number` VARCHAR(15), IN `p_alt_mobile_number` VARCHAR(15), IN `p_whatsapp_mobile_number` VARCHAR(15))
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_pr_faculty_personal_official_profile_info`(IN `p_faculty_id` INT, IN `p_login_in_id` INT, IN `p_faculty_designation` INT, IN `p_faculty_dept` INT, IN `p_faculty_salary` DECIMAL(10,2), IN `p_faculty_joining_date` VARCHAR(20))
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_pr_faculty_personal_profile_info`(IN `p_faculty_id` INT, IN `p_login_id` INT, IN `p_first_name` VARCHAR(100), IN `p_middle_name` VARCHAR(100), IN `p_last_name` VARCHAR(100), IN `p_initial` VARCHAR(10), IN `p_salutation` INT, IN `p_date_of_birth` DATE, IN `p_gender` INT, IN `p_blood_group` INT, IN `p_aadhar_number` VARCHAR(15), IN `p_religion` INT, IN `p_caste` INT, IN `p_community` INT, IN `p_nationality` INT, IN `p_marital_status` INT)
BEGIN
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
        SELECT 300 AS status_code, 'warning' AS status, CONCAT(warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Personal Details Updated Successfully!' AS message;
    END IF;

END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_pr_faculty_skill_profile_info`(IN `p_faculty_id` INT, IN `p_login_id` INT, IN `p_skills` JSON, IN `p_software_skills` JSON, IN `p_interest` JSON, IN `p_languages` JSON)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_pr_faculty_status`(IN `p_faculty_id` INT, IN `p_login_id` INT, IN `p_faculty_status` TINYINT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_pr_sem_begin`(IN p_login_id INT, IN p_sem_duration_id INT, IN p_year_of_study_id INT, IN p_sem_id INT, IN p_sem_begin_date DATE)
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
    -- Update sem_duration table
    UPDATE svcet_tbl_dev_sem_duration
    SET sem_duration_status = 1, -- Mark as active
        sem_duration_start_date = p_sem_begin_date
    WHERE sem_duration_id = p_sem_duration_id;

    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_sem_duration', 3);

    -- Update year_of_study table
    UPDATE svcet_tbl_dev_year_of_study
    SET year_of_study_status = 1 -- Mark as active
    WHERE year_of_study_id = p_year_of_study_id
      AND sem_duration_id = p_sem_duration_id;

    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_year_of_study', 3);

    -- Update academic_year table
    UPDATE svcet_tbl_dev_academic_year
    SET academic_year_status = 1 -- Mark as active
    WHERE sem_id = p_sem_id
      AND year_of_study_id = p_year_of_study_id;

    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_academic_year', 3);

    -- Update sem table
    UPDATE svcet_tbl_dev_sem
    SET sem_status = 1 -- Mark as active
    WHERE sem_id = p_sem_id
      AND sem_duration_id = p_sem_duration_id;

    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_sem', 3);

    UPDATE svcet_tbl_dev_section
    SET section_status = 1 -- Mark as active
    WHERE sem_duration_id = p_sem_duration_id;

    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_section', 3);


    -- If there were any warnings, return a warning status
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Set success message
        SELECT 200 AS status_code, 'success' AS status, 'Semeseter Started successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_pr_sem_freeze`(IN p_login_id INT, IN p_sem_duration_id INT, IN p_year_of_study_id INT, IN p_sem_id INT, IN p_sem_freeze_date DATE)
BEGIN
    DECLARE v_academic_year_title VARCHAR(50);
    DECLARE v_sem_duration_title VARCHAR(20);
    DECLARE v_new_academic_year_id INT;
    DECLARE v_new_sem_duration_id INT;
    DECLARE v_new_year_of_study_id INT;
    DECLARE v_new_sem_id INT;

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

    -- Fetch sem_duration_title
    SELECT sem_duration_title
    INTO v_sem_duration_title
    FROM svcet_tbl_dev_sem_duration
    WHERE sem_duration_id = p_sem_duration_id;

    -- Fetch current academic_year_title
    SELECT academic_year_title
    INTO v_academic_year_title
    FROM svcet_tbl_dev_academic_year
    WHERE year_of_study_id = p_year_of_study_id AND sem_id = p_sem_id;

    -- Determine new academic_year_title based on sem_duration_title
    IF v_sem_duration_title LIKE 'Even%' THEN
        SET v_academic_year_title = CONCAT(SUBSTRING_INDEX(v_academic_year_title, '-', 1) + 1, '-', SUBSTRING_INDEX(v_academic_year_title, '-', -1) + 1);
    END IF;

    -- Mark existing academic year as completed
    UPDATE svcet_tbl_dev_academic_year
    SET academic_year_status = 3
    WHERE year_of_study_id = p_year_of_study_id AND sem_id = p_sem_id;

    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_academic_year', 3);

    -- Insert new academic year record
    INSERT INTO svcet_tbl_dev_academic_year (academic_year_title, year_of_study_id, sem_id, academic_year_status)
    VALUES (v_academic_year_title, NULL, NULL, 0);
    SET v_new_academic_year_id = LAST_INSERT_ID();

    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_academic_year', 2);

    -- Update sem_duration table for existing row
    UPDATE svcet_tbl_dev_sem_duration
    SET sem_duration_status = 3, sem_duration_end_date = p_sem_freeze_date
    WHERE sem_duration_id = p_sem_duration_id;

    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_sem_duration', 3);

    -- Insert new sem_duration record with toggled title
    INSERT INTO svcet_tbl_dev_sem_duration (sem_duration_title, sem_duration_status)
    SELECT 
        CASE
            WHEN sem_duration_title LIKE 'Odd%' THEN REPLACE(sem_duration_title, 'Odd', 'Even')
            WHEN sem_duration_title LIKE 'Even%' THEN REPLACE(sem_duration_title, 'Even', 'Odd')
            ELSE sem_duration_title -- Default to existing title if no match
        END AS new_sem_duration_title,
        0
    FROM svcet_tbl_dev_sem_duration
    WHERE sem_duration_id = p_sem_duration_id;
    SET v_new_sem_duration_id = LAST_INSERT_ID();

    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_sem_duration', 2);

    -- Update year_of_study table for existing row
    UPDATE svcet_tbl_dev_year_of_study
    SET year_of_study_status = 3
    WHERE sem_duration_id = p_sem_duration_id;

    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_year_of_study', 3);

    -- Insert new year_of_study record
    INSERT INTO svcet_tbl_dev_year_of_study (sem_duration_id, academic_batch_id, academic_year_id, dept_id, year_of_study_title, year_of_study_status)
    SELECT v_new_sem_duration_id, NULL, v_new_academic_year_id, dept_id, year_of_study_title, 0
    FROM svcet_tbl_dev_year_of_study
    WHERE sem_duration_id = p_sem_duration_id;
    SET v_new_year_of_study_id = LAST_INSERT_ID();

    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_year_of_study', 2);

    -- Update sem table for existing row
    UPDATE svcet_tbl_dev_sem
    SET sem_status = 3
    WHERE sem_duration_id = p_sem_duration_id;

    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_sem', 3);

    -- Insert new sem record with updated sem_title
    INSERT INTO svcet_tbl_dev_sem (sem_duration_id, academic_batch_id, academic_year_id, year_of_study_id, dept_id, sem_title, sem_status)
    SELECT 
        v_new_sem_duration_id, 
        academic_batch_id, 
        v_new_academic_year_id, 
        v_new_year_of_study_id, 
        dept_id,
        CASE
            WHEN sem_title = 'I' THEN 'II'
            WHEN sem_title = 'II' THEN 'I'
            WHEN sem_title = 'III' THEN 'IV'
            WHEN sem_title = 'IV' THEN 'III'
            WHEN sem_title = 'V' THEN 'VI'
            WHEN sem_title = 'VI' THEN 'V'
            WHEN sem_title = 'VII' THEN 'VIII'
            WHEN sem_title = 'VIII' THEN 'VII'
            ELSE sem_title  -- For any other values, keep the existing sem_title
        END AS new_sem_title,
        0
    FROM svcet_tbl_dev_sem
    WHERE sem_duration_id = p_sem_duration_id;

    SET v_new_sem_id = LAST_INSERT_ID();

    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_sem', 2);

    -- Update the newly created academic year record with the new IDs
    UPDATE svcet_tbl_dev_academic_year
    SET year_of_study_id = v_new_year_of_study_id, sem_id = v_new_sem_id
    WHERE academic_year_id = v_new_academic_year_id;

    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_academic_year', 3);

    UPDATE svcet_tbl_dev_section 
    SET section_status= 3
    WHERE sem_duration_id= p_sem_duration_id;

    CALL insert_user_activity_log(p_login_id, 'svcet_tbl_dev_section ', 3);

    -- Insert new section record
    INSERT INTO svcet_tbl_dev_section (
        sem_duration_id, 
        academic_year_id, 
        year_of_study_id, 
        sem_id, 
        dept_id, 
        section_title, 
        section_status, 
        section_delete
    )
    SELECT 
        v_new_sem_duration_id,
        v_new_academic_year_id, 
        v_new_year_of_study_id, 
        v_new_sem_id, 
        dept_id, 
        section_title, 
        0, 
        0
    FROM svcet_tbl_dev_section
    WHERE sem_duration_id = p_sem_duration_id;

    -- Return warnings if any were encountered
    IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        -- Set success message
        SELECT 200 AS status_code, 'success' AS status, 'Semester Freezed successfully!' AS message;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_pr_student_dept_committee_roles`(IN `p_login_id` INT, IN `p_dept_id` INT, IN `committee_id_json` JSON, IN `student_id_json` JSON, IN `committee_roles_json` JSON, IN `p_type` INT, IN `p_r_r_id` INT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_pr_student_document_profile_info`(IN `p_student_id` INT, IN `p_login_id` INT, IN `p_student_sslc_id` INT, IN `p_student_hsc_id` INT, IN `p_student_highest_qualification_id` INT, IN `p_student_transfer_certificate_id` INT, IN `p_student_permanent_integrated_certificate_id` INT, IN `p_student_community_certificate_id` INT, IN `p_student_residence_certificate_id` INT, IN `p_student_profile_pic_id` INT, IN `p_sslc` TEXT, IN `p_hsc` TEXT, IN `p_highest_qualification` TEXT, IN `p_transfer_certificate` TEXT, IN `p_permanent_integrated_certificate` TEXT, IN `p_community_certificate` TEXT, IN `p_residence_certificate` TEXT, IN `p_profile_pic` TEXT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_pr_student_representative_form`(IN `p_login_id` INT, IN `p_student_id` JSON, IN `p_student_representative_id` JSON, IN `p_rep_year_of_study_id` JSON, IN `p_rep_dept_id` JSON, IN `p_rep_section_id` JSON)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_stu_admission_education_schoolings_profile_info`(IN `p_sslc_institution_name` VARCHAR(255), IN `p_education_board` INT, IN `p_sslc_passed_out_year` YEAR, IN `p_sslc_percentage` DECIMAL(5,2), IN `p_hsc_institution_name` VARCHAR(255), IN `p_education_hsc_board` INT, IN `p_education_hsc_specialization` INT, IN `p_hsc_passed_out_year` YEAR, IN `p_hsc_percentage` DECIMAL(5,2), IN `p_sslc_mark` INT, IN `p_hsc_mark` INT, IN `p_student_id` INT, IN `p_login_id` INT)
BEGIN
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_stu_admission_faculty_education_degree_profile_info`(IN `p_student_id` INT, IN `p_login_id` INT, IN `p_degree_institution_name` JSON, IN `p_education_degree` JSON, IN `p_education_degree_specialization` JSON, IN `p_degree_passed_out_year` JSON, IN `p_degree_percentage` JSON, IN `p_degree_cgpa` JSON, IN `p_degree_id` JSON)
BEGIN
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
                3, -- Degree level
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
DELIMITER ;

DELIMITER $$
CREATE  PROCEDURE `update_update_student_admission_info`(IN `p_student_admission_student_id` INT, IN `p_student_admission_type` TINYINT, IN `p_student_admission_category` TINYINT, IN `p_student_hostel` TINYINT, IN `p_student_admission_know_about_us` INT, IN `p_student_transport` TINYINT, IN `p_student_reference` INT, IN `p_student_admission_reg_no` VARCHAR(50), IN `p_student_course_preference1` INT, IN `p_student_course_preference2` INT, IN `p_student_course_preference3` INT, IN `p_student_concession_subject` INT, IN `p_student_concession_body` TEXT, IN `p_admission_deleted` TINYINT, IN `p_lateral_entry_year_of_study` INT, IN `p_login_id` INT)
BEGIN
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
        SELECT 300 AS status_code, 'warning' AS status, 
              'Student Admission Information processed successfully' AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Student Admission Information processed successfully!' AS message;
    END IF;

END$$
DELIMITER ;
