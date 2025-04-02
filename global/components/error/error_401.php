<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDUCNT | Error 401 - Unauthorized</title>
    <style>
        body {
            font-family: 'Comic Sans MS', cursive, sans-serif;
            background: linear-gradient(to bottom, #c9c7c7, #ffffff6e);
            color: #fff;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .error-container {
            display: flex;
            align-items: center;
            justify-content: center;
            max-width: 1200px;
            width: 100%;
            padding: 20px;
            border: hidden;
        }

        .error-image img {
            width: 450px;
            height: auto;
            margin-right: 20px;
        }

        .error-message {
            flex: 1;
            text-align: left;
        }

        .error-message h1 {
            font-size: 4rem;
            color: #ff4757;
            margin-bottom: 10px;
        }

        .error-message p {
            font-size: 1.6rem;
            line-height: 1.6;
            color: #4f5764;
            font-weight: bold;
        }

        .error-message .quotes {
            font-size: 1.2rem;
            font-style: italic;
        }

        .back-button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 1.1rem;
            background-color: #50c878;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background-color: #004517;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-image">
            <img src="http://svcet.faculty.edu-cnt.com/educnt-svcet-faculty/global/components/error/error_gifs/401.gif" alt="Loki Error 401">
        </div>
        <div class="error-message">
            <h1>ERROR 401</h1>
            <p id="error-message">Sorry! You Are Unauthorized</p>
            <p id="error-quotes" class="quotes"></p>
            <button onclick="goBack()" class="back-button">← Go Back</button>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script>
        function goBack() {
            window.history.back();
        }

        var errorQuotes401 = [
            "\"Error 401: Unauthorized! You must have a password from Tony Stark to enter this page.\"",
            "\"Oops! Looks like you don't have the keys to the Batcave. Access denied!\"",
            "\"401: You're not worthy to enter. Only Captain America’s shield bearer can pass!\"",
            "\"Oops! You need a password from Professor X to enter this page.\"",
            "\"401: Unauthorized! This page is locked like the Tesseract. Only the worthy may enter.\"",
            "\"Sorry! You need an invite from Nick Fury to enter. Try again later!\"",
            "\"Error 401: Unauthorized! You don't have clearance to enter. This is classified, like Spider-Man's secret identity.\"",
            "\"401 Unauthorized: You must pass the trials of the Jedi to access this page!\"",
            "\"Error 401: You can’t enter! This page is protected like the Philosopher's Stone!\"",
            "\"Oops! Unauthorized access. Looks like you've just been caught by the time loop in the Quantum Realm!\""
        ];

        // Function to get a random error quote
        function getErrorQuote() {
            var error_quotes = errorQuotes401[Math.floor(Math.random() * errorQuotes401.length)];
            $('#error-quotes').text(error_quotes);
        }

        getErrorQuote();
    </script>
</body>

</html>
