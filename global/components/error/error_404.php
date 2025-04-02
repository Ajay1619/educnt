<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDUCNT | Error 404 - Page Not Found</title>
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
            <img src="http://svcet.faculty.edu-cnt.com/educnt-svcet-faculty/global/components/error/error_gifs/404.gif" alt="Loki Error 404">
        </div>
        <div class="error-message">
            <h1>ERROR 404</h1>
            <p id="error-message">Please Check the URL!</p>
            <p id="error-quotes" class="quotes"></p>
            <button onclick="goBack()" class="back-button">← Go Back</button>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script>
        function goBack() {
            window.history.back();
        }

        var errorQuotes404 = [
            "\"Oops! Looks like Loki stole this page. Try checking the URL or use your magical powers to find it elsewhere.\"",
            "\"Oops! Looks like you've entered the wrong portal... Thanos is making sure no one escapes!\"",
            "\"Oops! Your page is lost in the Multiverse. Doctor Strange is looking for it!\"",
            "\"Oops! The page has vanished quicker than the snap of Thanos' fingers!\"",
            "\"Yikes! This page just went on a quest with Frodo. It'll be back... eventually.\"",
            "\"Uh-oh! Your page is hiding like Harry Potter under an invisibility cloak.\"",
            "\"Oops! Your page has entered the Twilight Zone. Get ready for a strange ride!\"",
            "\"Oops! Your page just disappeared like a ninja. *Poof* – gone!\"",
            "\"Oops! It's like your page fell through the portal into the Upside Down. Sorry, stranger!\"",
            "\"Oops! This page is on a coffee break. Chandler Bing would approve!\"",
            "\"Oops! This page went on a coffee run with Ross. We were on a break!\""
        ];

        // Function to get a random error quote
        function getErrorQuote() {
            var error_quotes = errorQuotes404[Math.floor(Math.random() * errorQuotes404.length)];
            $('#error-quotes').text(error_quotes);
        }

        getErrorQuote();
    </script>
</body>
</html>
