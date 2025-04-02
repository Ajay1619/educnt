<?php require_once('../config/sparrow.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="../global/css/sparrow.css">
    <link rel="stylesheet" href="../packages/bulmacalendar/bulma-calendar.min.css">
</head>

<body>
    <div style="margin: 30%;">
        <div class="">
            <input type="date" class="bulmaCalendar" id="bulmaCalendar" placeholder=" " required>
        </div>

    </div>
    <input type="text" name="" id="">
    <script src="../packages/bulmacalendar/bulma-calendar.min.js"></script>

    <script>
        // var calendars = new bulmaCalendar('.bulmaCalendar', {
        //     type: 'datetime',
        //     dateFormat: '<?= BULMA_DATE_FORMAT ?>',
        //     validateLabel: "",
        //     isRange: true
        // });
    </script>
</body>

</html>