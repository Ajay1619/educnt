<?php
include_once('../../../config/sparrow.php');

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
    if (isset($_GET['exam_id'])) {
        $_GET['exam_id'] = $_GET['exam_id'];
    } else {
        $_GET['exam_id'] = 0;
    }

?>
    <div class="main-content-card action-box">

        <div class="row">
            <!-- Subject List -->

            <div class="col col-4 col-lg-4 col-md-4 col-smy-4 col-xs-4" id="year-listsss">
                <div class="main-content-card" id="year-list">


                </div>
            </div>
            <!-- Timetable -->
            <div class="col col-3 col-lg-3 col-md-3 col-smy-3 col-xs-3">
                <div class="main-content-card " id="subject-list"> 
                   
                </div>
            </div> 
            <div class="col col-4 col-lg-4 col-md-4 col-smy-4 col-xs-4" id="exam-list">

                
            <div class="action-box-content">
            <img class="action-image" src="<?= GLOBAL_PATH . '/images/svgs/no_data_found.svg' ?>" alt="">
            <p class="action-text">
                Select Your Subject To Add the slot.
            </p>
            <!-- <div class="action-hint">
                *Every class attended is a step closer to success. Track, motivate, and make learning count!*
            </div> -->
        </div>
                 
            </div> 

        </div>
    </div>

    <script>
        $(document).ready(async function() {
            window.onbeforeunload = function() {
    return "Are you sure you want to reload? You will be taken to the homepage.";
};

// Optional: Redirect to homepage after reload (this would need to be part of your page logic)
window.onload = function() {
    if (performance.navigation.type === 1) { // 1 indicates a page reload
        window.location.href = "/"; // Replace "/" with your homepage URL
    }
};
            // var startDateCalendar = bulmaCalendar.attach('#examDate', {
            //     type: 'date',
            //     dateFormat: '<?= BULMA_DATE_FORMAT ?>',
            //     validateLabel: "",
            // });
            $('#subject-list').empty().append(
                                $('<div>').addClass('individual-subject-list').append(
                                    $('<img>').attr('src', '<?= GLOBAL_PATH . '/images/svgs/please_select_left.svg' ?>').attr('alt', 'No Subjects Icon').css({
                                        'width': '240px', // Adjust size as needed
                                        'height': '240px',
                                        'margin-right': '8px', // Space between image and text
                                        'vertical-align': 'middle'
                                    }) 
                                ),
                                $('<p class="action-text">No Data Found</p>'),

                            );
            $('#subject-list').on('click', '.individual-subject-list', function() {
                $('.individual-subject-list').removeClass('active');
                $(this).addClass('active');

                let yearId = $(this).data('year-id');
                let sectionId = $(this).data('section-id');
                let examid = $(this).data('exam-id');
                let subid = $(this).data('subject-id');
                console.log(yearId, sectionId, examid);
                create_date(yearId, sectionId, examid,subid);
            });

            $('#examTable').DataTable({
                scrollX: true,
                initComplete: function(settings, json) {
                    $('.dt-layout-table .dt-layout-cell').css('width', '100%');
                    $('.dt-scroll-headInner').css('width', '100%');
                    $('.dataTable').css('width', '100%');
                }

            });
            $('.individual-year-list').on('click', async function() {
                console.log("click");
                // Remove 'active' class from all other .individual-year-list elements
                $('.individual-year-list').removeClass('active');

                // Add 'active' class to the clicked element
                $(this).addClass('active');
            });
            await fetch_year_list(<?= $_GET['exam_id']; ?>,0);

            if(<?= $_GET['exam_id']; ?>!= 0){
                console.log("hi");
                await fetch_subject_list(<?= $_GET['exam_id']; ?>,0,0);
            }else{
                console.log("hi2");

                showToast('error', "Please Select Exam First");
                updateUrl({
                            route: 'faculty',
                            action: 'view' 
                        });
                        callAction();
            }

            window.addEventListener("beforeunload", function (event) {
    event.preventDefault(); 
    event.returnValue = "If you reload, you will be redirected to the home page!";
});
        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>