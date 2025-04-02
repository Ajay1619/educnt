<?php
include_once('../../../../../config/sparrow.php');

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

    <div class="tab-nav">
        <button class="tab-btn skill" data-tab="0">Skill Expression</button>
    </div>

    <section id="update_personal_profile">
        <div class="step-content active" data-step="3">
            <section id="skills_info">
                <div class="tab-content active" data-tab-content="0">
                    <h2>
                        Skill Knowledge
                        <span class="ml-3 info tooltip tooltip-right">
                            <b class=" info-text">ⓘ</b>
                            <span class="tooltip-text">
                                <strong>INFO</strong>
                                <div>After typing, press Enter to add the tag.</div>
                            </span>
                        </span>
                    </h2>

                    <form id="faculty-skill-profile-info-faculty-form" method="post">
                        <div class="row">
                            <div class="col col-6 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="input-container">
                                    <input type="text" id="skills_input" placeholder=" " name="skills_input">
                                    <label class="input-label" for="skills_input">Enter Your Core Skills</label>

                                </div>
                                <span class="chip-container" id="skills-chips"></span>
                            </div>
                            <div class="col col-6 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="input-container">
                                    <input type="text" id="software_skills_input" name="software_skills_input" placeholder=" ">
                                    <label class="input-label" for="software_skills_input">Enter Your Software Skills</label>

                                </div>
                                <div class="chip-container" id="software-skills-chips"></div>
                            </div>
                            <div class="col col-6 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="input-container">
                                    <input type="text" id="interest_input" name="interest_input" placeholder=" ">
                                    <label class="input-label" for="interest_input">Enter Your Interest</label>

                                </div>
                                <div class="chip-container" id="interest-chips"></div>
                            </div>
                            <div class="col col-6 col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="input-container">
                                    <input type="text" id="languages_input" name="languages_input" placeholder=" ">
                                    <label class="input-label" for="languages_input">Enter Your Languages Known</label>

                                </div>
                                <div class="chip-container" id="languages-chips"></div>
                            </div>
                        </div>
                        <div class="form-navigation">
                            <button class="nav-next text-left" id="education_skills_info_faculty_form_prev_btn" type="button">Previous</button>
                            <button class="nav-back text-right" id="education_skills_info_faculty_form_nxt_btn" type="submit">Next</button>
                        </div>
                    </form>
                </div>

            </section>
        </div>
    </section>

    <script src="<?= PACKAGES . '/jquery/jquery.js' ?>"></script>
    <script>
        $.ajax({
            type: 'GET',
            url: '<?= MODULES . '/faculty_profile/json/fetch_faculty_skills.php' ?>',
            headers: {
                'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
            },
            success: function(response) {
                response = JSON.parse(response);
                if (response.code == 200) {
                    showToast(response.status, response.message);
                    const data = response.data
                    const skillTypeMapping = {
                        1: '#skills-chips',
                        3: '#software-skills-chips',
                        2: '#interest-chips',
                        4: '#languages-chips',
                    };

                    data.forEach(skill => {
                        const containerId = skillTypeMapping[skill.faculty_skill_type];
                        if (containerId) {
                            const chipContainer = $(containerId);
                            // Use createChip function to create and add the chip
                            createChip($('<input>').val(skill.faculty_skill_name), chipContainer, skill.faculty_skill_id);
                        }
                    });


                } else {
                    console.error(response.status, response.message);

                }
            },
            error: function(error) {
                showToast('error', 'Something went wrong. Please try again later.');
            }
        });

        $('#skills_input').on('input', function(e) {

            input_validation($(this))

        });
        $('#software_skills_input').on('input', function(e) {

            input_validation($(this))

        });
        $('#interest_input').on('input', function(e) {

            input_validation($(this))

        });
        $('#languages_input').on('input', function(e) {

            input_validation($(this))

        });

        $('#skills_input').keypress(function(e) {

            if (e.which == 13 && $(this).val().trim() !== "") {
                e.preventDefault();
                createChip($(this), $('#skills-chips'), 0);
                $(this).val(""); // Clear the input field
            }
        });
        $('#software_skills_input').keypress(function(e) {
            if (e.which == 13 && $(this).val().trim() !== "") {
                e.preventDefault();
                createChip($(this), $('#software-skills-chips'), 0);
                $(this).val(""); // Clear the input field
            }
        });
        $('#interest_input').keypress(function(e) {
            if (e.which == 13 && $(this).val().trim() !== "") {
                e.preventDefault();
                createChip($(this), $('#interest-chips'), 0);
                $(this).val(""); // Clear the input field
            }
        });
        $('#languages_input').keypress(function(e) {
            if (e.which == 13 && $(this).val().trim() !== "") {
                e.preventDefault();
                createChip($(this), $('#languages-chips'), 0);
                $(this).val(""); // Clear the input field
            }
        });

        //id="education_skills_info_faculty_form_prev_btn" on click
        $('#education_skills_info_faculty_form_prev_btn').on('click', function() {
            const params = {
                action: 'add',
                route: 'faculty',
                type: 'experience',
                tab: 'industry'
            };

            // Construct the new URL with query parameters
            const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
            const newUrl = window.location.origin + window.location.pathname + queryString;
            // Use pushState to set the new URL and pass params as the state object
            window.history.pushState(params, '', newUrl);
            load_update_profile_components();
        });
        $('#faculty-skill-profile-info-faculty-form').submit(function(e) {
            e.preventDefault();
            const skills = getChipsValues($('#skills-chips'));
            const softwareSkills = getChipsValues($('#software-skills-chips'));
            const interest = getChipsValues($('#interest-chips'));
            const languages = getChipsValues($('#languages-chips'));
            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/faculty_profile/ajax/faculty_skill_profile_info_faculty_form.php' ?>',
                data: {
                    'skills': skills,
                    'software_skills': softwareSkills,
                    'interest': interest,
                    'languages': languages
                },
                headers: {
                    'X-CSRF-TOKEN': '<?= $csrf_token; ?>'
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.code == 200) {
                        showToast(response.status, response.message);
                        const params = {
                            action: 'add',
                            route: 'faculty',
                            type: 'upload',
                            tab: 'document'
                        };

                        // Construct the new URL with query parameters
                        const queryString = `?action=${params.action}&route=${params.route}&type=${params.type}&tab=${params.tab}`;
                        const newUrl = window.location.origin + window.location.pathname + queryString;
                        // Use pushState to set the new URL and pass params as the state object
                        window.history.pushState(params, '', newUrl);
                        load_update_profile_components();

                    } else {
                        showToast(response.status, response.message);
                    }
                },
                error: function(error) {
                    showToast('error', 'Something went wrong. Please try again later.');
                }
            });
        });
    </script>


<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
