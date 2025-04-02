<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 400 - Bad Request</title>
    <style>
        /* Basic styling for the error page */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Error container to hold both the image and the message */
        .error-container {
            display: flex;
            justify-content: center; /* Center content horizontally */
            align-items: center; /* Center content vertically */
            height: auto; /* Allow height to adjust based on content */
            width: 90%; /* Responsive width */
            max-width: 900px; /* Max width for larger screens */
            text-align: center; /* Center text inside the message */
        }

        /* Styling for the image section */
        .error-image img {
            max-width: 300px;
            height: auto;
        }

        /* Styling for the message section */
        .error-message {
            padding-left: 20px; /* Reduced padding for balance */
            text-align: left;
        }

        .error-message h1 {
            font-size: 3rem;
            color: #007bff;
            margin-bottom: 20px; /* Space below heading */
        }

        .error-message p {
            font-size: 1.2rem;
            color: #555;
            line-height: 1.5;
        }

        /* Back button styling */
        .back-button {
            margin-top: 20px;
            background-color: #d0cece;
            color: #000000;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            border: #000000;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-image">
            <!-- Replace this with your GIF, SVG, or PNG image -->
            <img src="../images/Group 6.svg" alt="Error 400 - Bad Request" />
        </div>
        <div class="error-message">
            <h1>ERROR 400</h1>
            <p>That the server cannot process the request,<br>
               No worries! Go back and try again with the correct request.
            </p>
            <button onclick="goBack()" class="back-button">
                ← Back
            </button>
        </div>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>
