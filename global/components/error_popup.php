<?php
include_once('../../config/sparrow.php');

// Check if the request is an AJAX request and a GET request
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {

    $module_name = isset($_POST['module_name']) ? sanitizeInput($_POST['module_name'], 'string')  : '';
    echo $module_name
?>

    <!-- Error Popup Overlay -->
    <div class="error-popup-overlay">
        <!-- Error Popup Container -->
        <div class="error-popup">
            <!-- Close Button -->
            <button class="error-close-btn">×</button>

            <!-- Popup Header -->
            <div class="error-header">
                <h2 class="error-title">Error Occurred</h2>
            </div>

            <!-- Popup Content -->
            <div class="error-content">
                <div class="row">
                    <div class="col col-8">
                        <!-- Error Message -->
                        <h5 class="error-popup-header">
                            Something went wrong! ⚠️
                        </h5>
                        <!-- Motivational Quote -->
                        <p class="error-quotes"></p>
                    </div>

                    <div class="col col-4">
                        <!-- Error Image -->
                        <div class="error-image-container">
                            <img src="<?= GLOBAL_PATH . '/images/svgs/gifs/error_popup.gif' ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(async function() {
            await getErrorQuote('<?= $module_name ?>');
        });
        var errorQuotes = {
            "faculty-profile": [
                "Error: Your profile just took a dramatic pause—like Ross yelling 'We were on a break!'",
                "Oops! Your profile got stuck in the Upside Down. Stranger things have happened!",
                "Yikes! Your profile just pulled a Homer Simpson—'D'oh!'",
                "Uh-oh! Your profile vanished faster than the Road Runner. Beep beep!",
                "Looks like your profile decided to take a day off. Ferris Bueller approves!",
                "Error: Your profile pulled a plot twist—M. Night Shyamalan would be proud.",
                "Your profile is acting like Loki—causing a little mischief, huh?",
                "Oops! Your profile went on a quest with Frodo. It'll be back... eventually.",
                "Yikes! Your profile just pulled a disappearing act worthy of Harry Potter's cloak!",
                "Error: Your profile hit a wall faster than Wile E. Coyote chasing the Road Runner!"
            ],
            "faculty-roles-responsibilities": [
                "Error: Like Chandler Bing, could your responsibilities BE any more broken?",
                "Oops! Your roles are more scrambled than a Scooby-Doo mystery!",
                "Your responsibilities just said 'Avengers Assemble!' and ran off!",
                "Error: Your roles are stuck in a time loop—Doctor Strange is working on it!",
                "Oops! Your responsibilities just pulled a 'Pikachu shocked face.'",
                "Yikes! Your roles got tangled like Spider-Man’s web. Untangling now...",
                "Error: Your responsibilities just 'Let it go!'—Frozen style.",
                "Oops! Your roles are playing hide-and-seek. Blue's Clues will find them!",
                "Yikes! Your responsibilities went to the dark side. Darth Vader approves.",
                "Error: Like Deadpool, your roles just broke the fourth wall. Meta, huh?"
            ],
            "faculty-student-admission": [
                "Oops! Admissions just pulled a 'Simpsons did it!' moment.",
                "Yikes! Your admissions process is more twisted than a Game of Thrones plotline!",
                "Error: Your student list went on an adventure with Scooby and the gang.",
                "Oops! Admissions decided to go full Matrix—‘There is no spoon.’",
                "Error: The admissions process just hit an iceberg. Titanic vibes incoming!",
                "Your admissions are stuck in 'The Office'—probably because of Michael Scott.",
                "Yikes! Your student list is having an identity crisis like Fight Club.",
                "Oops! Admissions went into hyperspace with Han Solo. Should be back soon!",
                "Error: Admissions are as lost as Nemo right now. Keep swimming!",
                "Yikes! Your admissions process just got 'Rick-rolled.' Never gonna give it up!"
            ],
            "faculty-achievements": [
                "Error: Your achievements are in another castle—Mario is on it!",
                "Oops! Your achievements just pulled a Jon Snow—‘I know nothing.’",
                "Yikes! Your achievements are lost in Bikini Bottom with SpongeBob!",
                "Error: Your achievements are more elusive than Carmen Sandiego!",
                "Oops! Your achievements are on vacation with Dora the Explorer. Can we find them?",
                "Yikes! Your achievements decided to 'Hakuna Matata' their way out of here.",
                "Error: Your achievements are stuck in the Quantum Realm with Ant-Man.",
                "Oops! Your achievements are partying at Moe's Tavern. Homer says hi!",
                "Yikes! Your achievements just joined the Avengers. Saving the world takes time.",
                "Error: Your achievements just got 'snapped' by Thanos. Hold tight!"
            ],
            "faculty-classes": [
                "Oops! Your classes just skipped like a kid on a snow day. Try again!",
                "Yikes! Your classes got stuck in a Jumanji game. Roll the dice to fix it!",
                "Error: Your classes decided to pull a Houdini—now you see them, now you don’t!",
                "Uh-oh! Your classes are taking a coffee break at Central Perk with the Friends gang.",
                "Oops! Your classes got shuffled like a deck of Uno cards. Draw four!",
                "Yikes! Your classes are lost in the Forbidden Forest. Hagrid’s on it!",
                "Error: Your classes are playing hide-and-seek with Waldo. Good luck finding them!",
                "Oops! Your classes just joined the Hogwarts Express. They’ll be back after their magic lesson!",
                "Error: Your classes are on a detour to Jurassic Park. Watch out for dinosaurs!",
                "Yikes! Your classes are having a lightsaber duel with Yoda. May the Force be with you!"
            ]

        };

        // Function to get a random error quote based on module name
        function getErrorQuote(module_name) {
            var quotes = errorQuotes[module_name] || ["Oops! Something went wrong!"]; // Fallback message
            var error_quotes = quotes[Math.floor(Math.random() * quotes.length)];
            $('.error-quotes').text(error_quotes);
        }

        $('.error-close-btn').click(function() {
            // Add slide-up and fade-out classes
            $('.error-popup').addClass('slide-up');
            $('.error-popup-overlay').addClass('fade-out');
            $('#error-popup').html(""); // Clear popup content

            // Go back to the last visited page in history
            window.history.back();
            // Wait for the animation to complete before clearing the popup
            setTimeout(() => {

                location.reload();
            }, 500); // Match CSS animation duration
        });
    </script>

<?php

} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>