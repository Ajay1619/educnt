<?php

define('BASEPATH', 'http://localhost/educnt-svcet-faculty');
define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/educnt-svcet-faculty');
define('GLOBAL_PATH', BASEPATH . '/global');
define('DEV_GLOBAL_PATH', 'http://localhost/educnt-svcet-developer/global/');
define('DEV_STUDENT_PATH', 'http://localhost/educnt-svcet-student/global/');
define('PACKAGES', BASEPATH . '/packages');
define('MODULES', BASEPATH . '/modules');
define('FILES', GLOBAL_PATH . '/files');
define('TIMEZONE', 'Asia/Kolkata');
define('COUNTRY', 'India');
define('COUNTRY_CODE', 'IN');
define('STATE', 'Pondicherry');
define('LANG', 'EN');
define('CURRENCY', 'INR');
define('CURRENCY_SYMBOL', '&#8377;');
define("COOKIE_TIME_OUT", 10); //specify cookie timeout in days (default is 10 days)

date_default_timezone_set(TIMEZONE);

//application hosted date
define('HOSTED_DATE', '2021-01-01');
//date format
define('DATE_FORMAT', 'd-m-Y');
//DB date format
define('DB_DATE_FORMAT', 'Y-m-d');
//date format
define('BULMA_DATE_FORMAT', 'dd-MM-yyyy');
//time format
define('TIME_FORMAT', 'h:i:s A');
//date time format
define('DATETIME_FORMAT', 'd-m-Y h:i:s A');
//file timestamp
define('FILE_DATETIME_FORMAT', 'd-m-Y h i s A');


$logged_first_name = isset($_SESSION['svcet_educnt_faculty_first_name']) ? $_SESSION['svcet_educnt_faculty_first_name'] : '';
$logged_middle_name = isset($_SESSION['svcet_educnt_faculty_middle_name']) ? $_SESSION['svcet_educnt_faculty_middle_name'] : '';
$logged_last_name = isset($_SESSION['svcet_educnt_faculty_last_name']) ? $_SESSION['svcet_educnt_faculty_last_name'] : '';
$logged_initial = isset($_SESSION['svcet_educnt_faculty_initial']) ? $_SESSION['svcet_educnt_faculty_initial'] : '';
$logged_faculty_salutation = isset($_SESSION['svcet_educnt_faculty_salutation']) ? $_SESSION['svcet_educnt_faculty_salutation']  : '';
$logged_designation = isset($_SESSION['svcet_educnt_faculty_designation']) ? $_SESSION['svcet_educnt_faculty_designation'] : '';
$logged_portal_type = isset($_SESSION['svcet_educnt_faculty_portal_type']) ? $_SESSION['svcet_educnt_faculty_portal_type'] : 1;
$logged_account_code = isset($_SESSION['svcet_educnt_faculty_account_code']) ? $_SESSION['svcet_educnt_faculty_account_code'] : '';
$logged_role_id = isset($_SESSION['svcet_educnt_faculty_role_id']) ? $_SESSION['svcet_educnt_faculty_role_id'] : 0;
$logged_account_username = isset($_SESSION['svcet_educnt_faculty_account_username']) ? $_SESSION['svcet_educnt_faculty_account_username'] : '';
$logged_login_id = isset($_SESSION['svcet_educnt_faculty_login_id']) ? $_SESSION['svcet_educnt_faculty_login_id'] : 0;
$logged_account_id = isset($_SESSION['svcet_educnt_faculty_account_id']) ? $_SESSION['svcet_educnt_faculty_account_id'] : 0;
$logged_user_id = isset($_SESSION['svcet_educnt_faculty_user_id']) ? $_SESSION['svcet_educnt_faculty_user_id'] : 0;
$logged_profile_status = isset($_SESSION['svcet_educnt_faculty_profile_status']) ? $_SESSION['svcet_educnt_faculty_profile_status'] : 0;
$logged_dept_title = isset($_SESSION['svcet_educnt_faculty_dept_title']) ? $_SESSION['svcet_educnt_faculty_dept_title'] : '';
$logged_dept_id = isset($_SESSION['svcet_educnt_faculty_dept_id']) ? $_SESSION['svcet_educnt_faculty_dept_id'] : 0;
$logged_dept_short_name = isset($_SESSION['svcet_educnt_faculty_dept_short_name']) ? $_SESSION['svcet_educnt_faculty_dept_short_name'] : '';
$logged_profile_pic = isset($_SESSION['svcet_educnt_faculty_profile_pic']) ? $_SESSION['svcet_educnt_faculty_profile_pic'] : '';

$existingAdmissionValue = isset($_SESSION['admission_student_existing']) ? $_SESSION['admission_student_existing'] : 0;
$routing = isset($_SESSION['routing']) ? htmlspecialchars($_SESSION['routing']) : 'Faculty';
$admin_roles = [1, 2];
$primary_roles = [1, 2, 3];
$main_roles = [1, 2, 3, 4, 5, 6];
$secondary_roles = [6];
$tertiary_roles = [8];
$non_teaching_roles = [9];
$higher_official = [3, 4, 5];


$csrf_token = isset($_SESSION['sparrow_faculty_csrf_token']) ? $_SESSION['sparrow_faculty_csrf_token'] : 0;

$faculty_page_access_data = isset($_SESSION['svcet_educnt_faculty_page_access_data']) ? $_SESSION['svcet_educnt_faculty_page_access_data'] : [];
