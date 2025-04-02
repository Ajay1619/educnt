
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDUCNT | Error 403 - Forbidden</title>
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
            <img src="http://svcet.faculty.edu-cnt.com/educnt-svcet-faculty/global/components/error/error_gifs/bad_rec.gif" alt="Loki Error 403">
        </div>
        <div class="error-message">
            <h1>ERROR 403</h1>
            <p id="error-message">Sorry! Access Denied</p>
            <p id="error-quotes" class="quotes"></p>
            <button onclick="goBack()" class="back-button">← Go Back</button>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script>
        function goBack() {
            window.history.back();
        }

        var errorQuotes403 = [
            "\"Error 403: Forbidden! Like Spider-Man, you’ve entered the wrong place... Now you’re stuck in a web of errors!\"",
            "\"Oops! You’ve breached the Chamber of Secrets, but the basilisk is not impressed!\"",
            "\"Access Denied: You’re not worthy to wield Thor's hammer. No entry here!\"",
            "\"403: This page is like a secret room at Hogwarts. Only those with the Marauder's Map can enter!\"",
            "\"Oops! You've entered the Fortress of Solitude. Unauthorized access is not allowed!\"",
            "\"Sorry, access denied! This page is locked tighter than the vault in Avengers Tower.\"",
            "\"403: Forbidden! This page is hidden like Wonder Woman’s lasso of truth. Can't get in!\"",
            "\"Access Denied: Like Doctor Strange, this portal is closed for now. Try again later!\"",
            "\"Access Denied: You don’t have permission to enter. Looks like you've been hit by the Hulk's anger!\"",
            "\"Oops! This page is like a VIP club. You need an invite from Tony Stark to get in!\""
        ];

        
        // Function to get a random error quote
        function getErrorQuote() {
            var error_quotes = errorQuotes403[Math.floor(Math.random() * errorQuotes403.length)];
            $('#error-quotes').text(error_quotes);
        }

        getErrorQuote();
    </script>
</body>

</html>
