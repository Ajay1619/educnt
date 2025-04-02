<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDUCNT | Server Maintenance</title>
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
            <img src="http://svcet.faculty.edu-cnt.com/educnt-svcet-faculty/global/components/error/error_gifs/500.gif" alt="Maintenance Error">
        </div>
        <div class="error-message">
            <h1>ERROR 503</h1>
            <p id="error-message">Oops! We are currently working on the server.</p>
            <p id="error-quotes" class="quotes"></p>
            <button class="back-button" onclick="location.reload()">Try Again</button>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script>
        function getMaintenanceQuote() {
            var maintenance_quotes = maintenanceQuotes[Math.floor(Math.random() * maintenanceQuotes.length)];
            $('#error-quotes').text(maintenance_quotes);
        }

        var maintenanceQuotes = [
            "\"Oops! The server is currently under construction. We’ll be back soon, like Thor after the snap!\"",
            "\"Maintenance Mode: Even superheroes need a break. The server will be up and running shortly!\"",
            "\"Uh-oh! The server is on a coffee break, but don't worry, it’ll be back before you know it!\"",
            "\"Our server is getting an upgrade. It’s like Iron Man in his lab, working on the next big thing!\"",
            "\"We're currently fixing things behind the scenes. Don't worry, our team is on it like Batman in Gotham!\"",
            "\"Server Maintenance in Progress: Hang tight! Even Wakanda needs to recharge its systems from time to time.\"",
            "\"The server is in the repair shop! Don't worry, just like Spider-Man, it’ll swing back into action soon!\"",
            "\"We're currently powering up the server, like Doctor Strange opening portals to the multiverse!\"",
            "\"It’s a server maintenance mission. We’ll be back online as soon as possible, like the Avengers assembling.\"",
            "\"The server is under maintenance – think of it as our own version of the Infinity Stones coming together!\""
        ];

        getMaintenanceQuote();
    </script>
</body>

</html>
