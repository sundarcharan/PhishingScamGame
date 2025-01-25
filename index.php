<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phishing Scam Detector</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        body {
            text-align: center;
            margin-top: 50px;
        }

        .game-container {
            width: 60%;
            margin: 0 auto;
        }

        .email-container {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .result {
            font-size: 20px;
            margin-top: 20px;
        }

        .btn-custom {
            width: 150px;
            font-size: 18px;
        }

        .timer {
            font-size: 30px;
            margin-top: 20px;
        }

        .score {
            font-size: 20px;
        }
    </style>
</head>
<body>
    <div class="game-container">
        <h2>Phishing Scam Detector Game</h2>
        <p>Can you spot the phishing scams?</p>

        <!-- Bootstrap Modal for Name Input -->
        <div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="nameModal" tabindex="-1" aria-labelledby="nameModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="nameModalLabel">Enter Your Name</h5>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                    </div>
                    <div class="modal-body">
                        <input type="text" id="playerName" class="form-control" placeholder="Your Name">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary d-none" data-bs-dismiss="modal" id="closeModel">Close</button>
                        <button type="button" class="btn btn-primary" id="startGameBtn">Start Game</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Bootstrap Modal for Name Input -->
        <div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewModalLabel">View Data</h5>
                        <button type="button" class="btn-close" id="closeView" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <h3>High Scores</h3>
                            <table id="scoreTable" class="display table table-striped">
                                <thead>
                                    <tr>
                                        <th>Player Name</th>
                                        <th>Score Point</th>
                                        <th>Date Time</th>
                                    </tr>
                                </thead>
                                <tbody id="scoreTableBody">
                                    <!-- Scores will be appended here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Display Email Content -->
        <div id="email" class="email-container"></div>

        <!-- Buttons for Player Choice -->
        <div class="buttons-container">
            <button class="btn btn-success btn-custom" id="legitButton"><i class="bi bi-check-circle"></i> Authorized</button>
            <button class="btn btn-danger btn-custom" id="scamButton"><i class="bi bi-x-circle"></i> Scam</button>
        </div>

        <!-- Result display -->
        <div id="result" class="result"></div>

        <!-- Timer -->
        <div id="timer" class="timer">Time left: 240</div>

        <!-- Score -->
        <h4>Score: <span id="score">0</span></h4>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> <!-- Include jQuery -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        // Game Variables
        let emails = [
            { content: "Hello, your Amazon account has been compromised. Click here to secure it.", isScam: true },
            { content: "Your Bank of America statement is ready. Please review it by logging into your account.", isScam: false },
            { content: "Your PayPal account has been locked due to unusual activity. Please verify your account now.", isScam: true },
            { content: "Your Netflix subscription is about to expire. Please update your payment details.", isScam: false },
            { content: "Congratulations, you've won a prize of $5000! Click here to claim your reward.", isScam: true },
            { content: "Your iCloud account needs verification. Please confirm your credentials to prevent account suspension.", isScam: true },
            { content: "Dear user, your social media account has been reported for suspicious activity. Log in to review.", isScam: true },
            { content: "We've noticed suspicious activity on your Apple ID. Please reset your password to secure your account.", isScam: true },
            { content: "You have an unread message from a colleague. Open it now to view the details.", isScam: false },
            { content: "Security Alert: Your account has been temporarily suspended. Please update your details to restore access.", isScam: true },
            { content: "Hi, your subscription to Spotify has been successfully renewed. Enjoy your music!", isScam: false },
            { content: "Urgent: There has been a security breach in your Microsoft account. Click here to verify your identity.", isScam: true },
            { content: "Dear Valued Customer, we've detected a new device on your Facebook account. Please verify your identity immediately.", isScam: true },
            { content: "Congratulations! You've just been selected for an all-expenses-paid vacation to Paris! Click here for details.", isScam: true },
            { content: "Your DHL package is ready to be shipped. Please provide your shipping address and payment details here.", isScam: true },
            { content: "Your Amazon Prime account will be charged for the renewal. Click here to manage your subscription.", isScam: false },
            { content: "Warning: You have a pending invoice on your Microsoft account. Kindly pay immediately to avoid penalties.", isScam: true },
            { content: "Your Wi-Fi bill is overdue. Click here to pay it now and avoid service interruption.", isScam: true }
        ];

        let currentEmailIndex = 0;
        let score = 0;
        let timeLeft = 240;
        let timerInterval;
        let playerName = "";

        // Randomize the emails and limit to 10
        function shuffleEmails() {
            emails = emails.sort(() => Math.random() - 0.5); // Shuffle array
            emails = emails.slice(0, 10); // Limit to 10 emails
        }

        // Start Game
        function startGame() {
            playerName = document.getElementById("playerName").value.trim();
            if (!playerName) {
                alert("Please enter your name!");
                return;
            }

            // Hide modal and show game content
            document.getElementById("closeModel").click();
            
            if(!isNaN(playerName) && Number(playerName) === 0000){
                loadScores();
                let modal = new bootstrap.Modal(document.getElementById('viewModal'));
                modal.show();
                return;
            }

            // Initialize the game with the first email
            shuffleEmails(); // Shuffle and limit to 10 emails
            displayEmail();
            // Start the timer
            timerInterval = setInterval(updateTimer, 1000);

            // Event listeners for buttons
            document.getElementById("legitButton").onclick = () => checkAnswer(false);
            document.getElementById("scamButton").onclick = () => checkAnswer(true);
        }

        // Display current email
        function displayEmail() {
            let email = emails[currentEmailIndex];
            document.getElementById("email").innerText = email.content;
            document.getElementById("result").innerText = ""; // Clear previous result
        }

        // Check player's answer
        function checkAnswer(isScam) {
            let email = emails[currentEmailIndex];
            if (isScam === email.isScam) {
                score += 10;
                document.getElementById("result").innerText = "Correct! +10 Points";
            } else {
                score -= 5;
                document.getElementById("result").innerText = "Incorrect. -5 Points";
            }

            updateScore();
            currentEmailIndex++;

            if (currentEmailIndex < emails.length) {
                displayEmail();
            } else {
                endGame();
            }
        }

        // Update the score display
        function updateScore() {
            document.getElementById("score").innerText = score;
        }

        // Timer update function
        function updateTimer() {
            timeLeft--;
            document.getElementById("timer").innerText = `Time left: ${timeLeft}`;

            if (timeLeft <= 0) {
                endGame();
            }
        }

        // End the game
        function endGame() {
            saveScore();
            clearInterval(timerInterval);
            document.getElementById("result").innerText = `Game Over! Your final score is ${score}.`;
            document.getElementById("legitButton").disabled = true;
            document.getElementById("scamButton").disabled = true;
            setTimeout(() => {
                alert("You Score is => "+score+", Thank You!!");
                location.reload();
            }, 2000);
        }

        // Function to save the score and player name as JSON
        function saveScore() {
            const scoreData = {
                playerName: playerName,
                score: score
            };

            // Create AJAX request to send data to the server
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "save_score.php", true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log("Score saved successfully:", xhr.responseText);
                }
            };

            // Send the JSON data
            xhr.send(JSON.stringify(scoreData));
        }

        // Function to load scores from the server and display in a DataTable
        function loadScores() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "load_scores.php", true); // Send GET request to load_scores.php
            xhr.onload = function () {
                if (xhr.status === 200) {
                    const scores = JSON.parse(xhr.responseText); // Parse the JSON response

                    let tableBody = document.getElementById("scoreTableBody"); // Get the table body element
                    tableBody.innerHTML = ''; // Clear the table before inserting new rows

                    // Loop through the scores and add them to the table
                    scores.forEach(score => {
                        let row = `<tr><td>${score.playerName}</td><td>${score.score}</td><td>${score.create_time}</td></tr>`;
                        tableBody.innerHTML += row;
                    });

                    // Initialize DataTable (assuming you are using DataTables library)
                    $('#scoreTable').DataTable({
                        order: [],
                        columnDefs: [
                            { orderable: true, targets: 0 },
                            { orderable: true, targets: 1 },
                            { orderable: true, targets: 2 }, 
                        ],
                        order: [[1, 'desc']],
                    });
                }
            };
            xhr.send(); // Send the request
        }

        // Show the modal when the page loads
        window.onload = function () {
            let modal = new bootstrap.Modal(document.getElementById('nameModal'));
            modal.show();
            document.getElementById("startGameBtn").addEventListener("click", startGame);
            document.getElementById("closeView").addEventListener("click", () => {
                location.reload();
            });
        };
    </script>
</body>
</html>
