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

?>
    <script>
        const loadSidebar = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/sidebar.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {

                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#sidebar').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status === 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },
                });
            });
        };

        const loadTopbar = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(GLOBAL_PATH . '/components/topbar.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {

                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                        'X-Requested-Path': window.location.pathname + window.location.search // Secure CSRF token
                    },
                    success: function(response) {
                        $('#topbar').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status === 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },
                });
            });
        };

        const load_performance_chart = () => {
            var performanceValue = 76; // Use dynamic value here

            var options = {
                series: [performanceValue],
                chart: {
                    type: 'radialBar',
                    offsetY: -20,
                    sparkline: {
                        enabled: true
                    }
                },
                plotOptions: {
                    radialBar: {
                        startAngle: -90,
                        endAngle: 90,
                        track: {
                            background: "#e7e7e7",
                            strokeWidth: '97%',
                            margin: 5,
                            dropShadow: {
                                enabled: true,
                                top: 2,
                                left: 0,
                                color: '#444',
                                opacity: 1,
                                blur: 2
                            }
                        },
                        dataLabels: {
                            name: {
                                show: false
                            },
                            value: {
                                offsetY: -2,
                                fontSize: '22px'
                            }
                        }
                    }
                },
                grid: {
                    padding: {
                        top: -10
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        shadeIntensity: 0.4,
                        inverseColors: false,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 50, 53, 91]
                    },
                },
                labels: ['Average Results'],
            };

            var chart = new ApexCharts(document.querySelector("#performance-chart"), options);
            chart.render();

            // Generate random quote based on performance value
            const authorityQuotes = {
                best: [
                    "You're the real MVP! (Like Rocky)",
                    "You’re the Gandalf of this place!",
                    "Like Tony Stark, you’ve got this!",
                    "The Force is strong with you!",
                    "You're the Godfather of education!",
                    "You’ve got the leadership touch, like Mufasa!",
                    "You’re the Captain America of our team!",
                    "The best there is, like Wolverine!",
                    "You’re the real hero, no cape needed!",
                    "Like The Avengers, you’ve got it all covered!"
                ],
                good: [
                    "Keep rocking, like Indiana Jones!",
                    "You're the Professor X of this team!",
                    "You're like Sherlock—figuring it all out!",
                    "You’re the Rocky Balboa of leadership!",
                    "You’re the Obi-Wan Kenobi we needed!",
                    "Like Neo, you’re breaking barriers!",
                    "You’ve got the Iron Man swagger!",
                    "Like Spider-Man, you’re spinning success!",
                    "You’re the Gandalf leading us to victory!",
                    "Leading the way like Captain Marvel!"
                ],
                average: [
                    "Like Luke Skywalker, still learning!",
                    "Channel your inner Bruce Wayne!",
                    "Not bad—like a Jedi in training!",
                    "Keep going—you're the Luke Skywalker of education!",
                    "You're like Thor—just a little more hammer!",
                    "Still better than Jar Jar Binks!",
                    "Like The Hulk—work in progress!",
                    "Not yet, but you're getting there like Spock!",
                    "You're on the right track, like Iron Man!",
                    "Keep it up, you’ll be the next big thing!"
                ],
                bad: [
                    "It’s a setback, but you'll be back like Terminator!",
                    "We need a reboot, like The Matrix!",
                    "Not great—let's make it more like The Avengers!",
                    "Come on, like Batman, you've got this!",
                    "You’ve hit a snag, but like Fast & Furious, you’ll speed up!",
                    "Let’s turn this around like Mission Impossible!",
                    "Like Harry Potter, it’s just a rough chapter!",
                    "We’ll bounce back like Rambo!",
                    "It’s a plot twist, but like James Bond, we adapt!",
                    "Let's make this a feel-good movie like The Pursuit of Happyness!"
                ],
                worst: [
                    "Not your best moment—like a bad sequel!",
                    "Even Batman has his bad days!",
                    "It's like Star Wars—just a little off track!",
                    "We need a massive plot twist here!",
                    "Like a bad rom-com, let’s reset!",
                    "A little off, but we’ll be back like Jurassic Park!",
                    "It’s a rough start, but you'll be our hero, like Spider-Man!",
                    "You’ll be back like The Terminator!",
                    "We’ll find the solution, like Indiana Jones!",
                    "It’s a rough day, but you're still the main character!"
                ]
            };


            // Determine category based on performance value
            let performanceCategory;
            if (performanceValue >= 90) {
                performanceCategory = 'best';
            } else if (performanceValue >= 70) {
                performanceCategory = 'good';
            } else if (performanceValue >= 50) {
                performanceCategory = 'average';
            } else if (performanceValue >= 30) {
                performanceCategory = 'bad';
            } else {
                performanceCategory = 'worst';
            }

            // Randomly select a quote from the chosen category
            const randomQuote = authorityQuotes[performanceCategory][Math.floor(Math.random() * authorityQuotes[performanceCategory].length)];
            document.getElementById("overallperformance-quotes").innerText = randomQuote;
        };

        const load_authorities_dashboard = () => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: '<?= htmlspecialchars(MODULES . '/dashboard/components/faculty_authorities_personal_dashboard.php', ENT_QUOTES, 'UTF-8') ?>',
                    headers: {
                        'X-CSRF-Token': '<?= $csrf_token ?>', // Secure CSRF token
                    },
                    success: function(response) {
                        $('#dashboard').html(response);
                        resolve(); // Resolve the promise
                    },
                    error: function(jqXHR) {
                        const message = jqXHR.status === 401 ? 'Unauthorized access. Please check your credentials.' : 'An error occurred. Please try again.';
                        showToast('error', message);
                        reject(); // Reject the promise
                    },
                });
            });
        }
        const load_dashboard = () => {

            switch (<?= $logged_role_id ?>) {
                case 6:
                    load_authorities_dashboard()
                default:
                    break;
            }

        }

        const load_dashboard_wishes = (name) => {
            const dashboard_wishes_slogans = {
                morning: [
                    "Morning, professor! Time to inspire!",
                    "Rise and shine, teaching legend!",
                    "The faculty hero begins their day!",
                    "Every morning is your blockbuster start!",
                    "Coffee strong, syllabus stronger!",
                    "New dawn, new knowledge to share!",
                    "Channel your inner Gandalf today!",
                    "Teaching: the real Avengers assemble!",
                    "Morning wisdom beats coffee any day!",
                    "Ready to conquer the academic realm!"
                ],
                afternoon: [
                    "Afternoon, star faculty! Keep shining!",
                    "Stay sharp—like a faculty ninja!",
                    "Midday magic: your second act begins!",
                    "Power through! Academic fame awaits!",
                    "Afternoon wisdom is pure gold!",
                    "Lunch break? More like idea fuel!",
                    "Faculty mode: Always on, never off!",
                    "Keep the students guessing, Yoda!",
                    "Plot twist: you're their favorite!",
                    "Afternoon vibes: scholarly and unstoppable!"
                ],
                evening: [
                    "Evening, teaching star! Recharge time!",
                    "Faculty downtime: your spin-off episode!",
                    "Relax—you’re the main character today!",
                    "Evenings: where reflection fuels legends!",
                    "Call it a day, academic hero!",
                    "Plot armor? You’ve got teaching charm!",
                    "Evening vibes: Netflix and pedagogy!",
                    "You’ve earned your cliffhanger moment!",
                    "Faculty life: part inspiration, part legend!",
                    "End the day like a true boss!"
                ],
                night: [
                    "Lights out, teaching icon! Rest up!",
                    "Dream big, professor extraordinaire!",
                    "Faculty sleep = tomorrow’s wisdom drop!",
                    "Good night, Jedi of academia!",
                    "Recharge, like Iron Man’s arc reactor!",
                    "Rest well—students await your brilliance!",
                    "Sleep tight, education's superhero!",
                    "Nighttime is for faculty legends!",
                    "Rest like a king, rule tomorrow!",
                    "Faculty by day, dreamer by night!"
                ]
            };


            function updateWishes(name) {
                const now = new Date();
                const hours = now.getHours();
                let greeting = "";
                let sloganList = [];

                if (hours >= 5 && hours < 12) {
                    greeting = "Good Morning, " + name;
                    sloganList = dashboard_wishes_slogans.morning;
                } else if (hours >= 12 && hours < 17) {
                    greeting = "Good Afternoon, " + name;
                    sloganList = dashboard_wishes_slogans.afternoon;
                } else if (hours >= 17 && hours < 21) {
                    greeting = "Good Evening, " + name;
                    sloganList = dashboard_wishes_slogans.evening;
                } else {
                    greeting = "Good Night, " + name;
                    sloganList = dashboard_wishes_slogans.night;
                }

                // Randomly select a slogan
                const randomSlogan = sloganList[Math.floor(Math.random() * sloganList.length)];

                // Update the DOM
                $("#dashboard-wishes").text(greeting);
                $(".dashboard-wishes-slogans").text(randomSlogan);
            }

            // Call the function on page load
            updateWishes(name);

            // Optionally, you can set it to update periodically (e.g., every hour)
            setInterval(updateWishes, 60 * 60 * 1000);
        }
    </script>
<?php
} else {
    echo json_encode(['code' => 400, 'status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
