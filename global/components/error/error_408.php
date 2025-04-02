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
            <h1>ERROR 408</h1>
            <p id="error-message">Sorry ! Your Server Timeout</p>
            <p id="error-quotes" class="quotes"></p>
            <button class="back-button" onclick="location.reload()">Reload</button>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script>
        function goBack() {
            window.history.back();
        }

        var errorQuotes408 = [
            "\"Error 408: Timeout! Looks like your request went to hyperspace with Han Solo. It should be back soon.\"",
            "\"Oops! The server took a coffee break longer than Ross. 'We were on a break.'\"",
            "\"Yikes! Your request got stuck in a time loop, like Doctor Strange's endless timelines.\"",
            "\"Error 408: The server is still working on your request... It’s currently having a conversation with Groot.\"",
            "\"Sorry! Your request timed out. It's like waiting for the next Game of Thrones episode.\"",
            "\"Error 408: Timeout! Your request is like Harry Potter’s owl. It’s lost somewhere in transit.\"",
            "\"Oops! Your request is lost in the Upside Down. It’ll pop back up soon.\"",
            "\"Error 408: Timeout! This request is taking longer than the Avengers’ time heist.\"",
            "\"Oops! Your request just went on an adventure with Frodo. It'll be back... eventually.\"",
            "\"Error 408: Timeout! The server is probably stuck in the Matrix. No spoon for you.\""
        ];

        // Function to get a random error quote
        function getErrorQuote() {
            var error_quotes = errorQuotes408[Math.floor(Math.random() * errorQuotes408.length)];
            $('#error-quotes').text(error_quotes);
        }

        getErrorQuote();
    </script>
</body>

</html>