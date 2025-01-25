<?php
$file = 'scores.json';

// Check if the file exists
if (file_exists($file)) {
    // Read the contents of the JSON file
    $scores = json_decode(file_get_contents($file), true);

    $scores = is_array($scores) ? $scores : [];
    
    // Return the scores as JSON
    echo json_encode($scores);
} else {
    // If the file doesn't exist, return an empty array
    echo json_encode([]);
}
?>
