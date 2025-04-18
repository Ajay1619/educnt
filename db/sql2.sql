DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fetch_pr_achievement_table_data`(IN `p_search_value` VARCHAR(255), IN `p_sort_column` VARCHAR(50), IN `p_order_dir` VARCHAR(4), IN `p_start` INT, IN `p_length` INT, IN `p_faculty_id` INT, IN `p_achievement_type` INT, IN `p_login_id` INT)
BEGIN
    DECLARE total_records INT DEFAULT 0;
    DECLARE filtered_records INT DEFAULT 0;
    DECLARE warning_count INT DEFAULT 0;
    DECLARE warning_message VARCHAR(255) DEFAULT '';

    -- Calculate total records without filters
    DECLARE CONTINUE HANDLER FOR SQLWARNING
    BEGIN
        SET warning_count = warning_count + 1;
        GET DIAGNOSTICS CONDITION 1 warning_message = MESSAGE_TEXT;
    END;

    SELECT COUNT(*) INTO total_records
    FROM svcet_tbl_faculty_achievements AS a
    WHERE a.achievement_deleted = 0;

    -- Calculate filtered records based on input conditions
    SELECT COUNT(*) INTO filtered_records
    FROM svcet_tbl_faculty_achievements AS a
    JOIN svcet_tbl_dev_general AS g 
    ON a.achievement_type = g.general_id
    WHERE a.achievement_deleted = 0 
      AND g.general_status = 1
      AND (p_faculty_id = 0 OR a.faculty_id = p_faculty_id)
      AND (p_achievement_type = 0 OR a.achievement_type = p_achievement_type)
      AND (a.achievement_title LIKE CONCAT('%', p_search_value, '%') 
           OR a.achievement_venue LIKE CONCAT('%', p_search_value, '%'));

    -- Fetch filtered data with sorting and pagination
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
    FROM svcet_tbl_faculty_achievements AS a
    JOIN svcet_tbl_dev_general AS g 
    ON a.achievement_type = g.general_id
    WHERE a.achievement_deleted = 0 
      AND g.general_status = 1
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
        'svcet_tbl_faculty_achievements,svcet_tbl_dev_general', 
        1
    );
  IF warning_count > 0 THEN
        SELECT 300 AS status_code, 'warning' AS status, 
               CONCAT('Query executed with ', warning_count, ' warning(s): ', warning_message) AS message;
    ELSE
        SELECT 200 AS status_code, 'success' AS status, 'Designation Records Fetched Successfully!' AS message;
    END IF;

END$$
DELIMITER ;