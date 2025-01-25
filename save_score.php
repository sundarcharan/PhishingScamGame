<?php
// save_score.php
date_default_timezone_set("Asia/Kolkata");

// Get the raw POST data
$data = file_get_contents('php://input');


// Decode the JSON data into an array
$scoreData = json_decode($data, true);
$scoreData["create_time"] = date("y-m-d h:i A");

// Save the data to a JSON file (or database)
$file = 'scores.json';

// Check if file exists and if not, create a new file
if (!file_exists($file)) {
    file_put_contents($file, json_encode([])); // Create an empty array in the file
}

// Get existing scores
$scores = json_decode(file_get_contents($file), true);

// Add the new score
$scores[] = $scoreData;

// Save the updated scores back to the file
file_put_contents($file, json_encode($scores, JSON_PRETTY_PRINT));

// Return a success message
echo json_encode(["message" => "Score saved successfully!"]);
?>
