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

?>
    <div class="main-content-card action-box">

        <div class="row">
            <!-- Subject List -->

            <div class="col col-4 col-lg-4 col-md-4 col-smy-4 col-xs-4" id="year-listsss">
                <div class="main-content-card" id="year-list">


                </div>
            </div>
            <!-- Timetable -->
            <div class="col col-8 col-lg-8 col-md-8 col-sm-8 col-xs-8">
                <div class="main-content-card dashboard-table-container" id="time-table">
                    <!-- Timetable will be dynamically inserted here -->
                </div>
            </div>
            <!--  <div class="col col-4 col-lg-4 col-md-4 col-smy-4 col-xs-4" id="exam-list">

                
            <div class="action-box-content"> -->
            <!-- <img class="action-image" src="<?= GLOBAL_PATH . '/images/svgs/no_data_found.svg' ?>" alt="">
            <p class="action-text">
                Select Your Subject To Add the slot.
            </p> -->
            <!-- <div class="action-hint">
                *Every class attended is a step closer to success. Track, motivate, and make learning count!*
            </div> -->
        </div>

    </div>

    </div>
    </div>

    <script>
        $(document).ready(async function() {
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
                $('<p class="action-text">No Data Here ,Please Select the Year</p>'),

            );
            $('#subject-list').on('click', '.individual-subject-list', function() {
                $('.individual-subject-list').removeClass('active');
                $(this).addClass('active');

                let yearId = $(this).data('year-id');
                let sectionId = $(this).data('section-id');
                let examid = $(this).data('exam-id');
                let subid = $(this).data('subject-id');
                console.log(yearId, sectionId, examid);
                create_date(yearId, sectionId, examid, subid);
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
            await fetch_year_list(<?= $_GET['exam_id']; ?>, 1);


        });
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>