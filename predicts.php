<?php
session_start();
require __DIR__ . '/configuration/app.php';
if (empty($_SESSION['username'])) { header('Location: ./doctors_login.php'); exit(); }
if (!isset($_SESSION['patient_info'])) {
    header('Location: ./add_patient.php');
    exit();
}

// Retrieve patient information from session variables
$patient_info = $_SESSION['patient_info'];

// Prepare data as a JSON object
$data = [
    'age' => $patient_info['age'],
    'sexe' => $patient_info['gender'], // Male binary column
    //'female' => $patient_info['female'], // Female binary column
    'nom_vaccin' => $patient_info['vaccin'],
    'lieu_vaccination' => $patient_info['lieuvac'],
    'saison_vaccination' => $patient_info['season'],
    'medical_history' => $patient_info['atc'],
    'side_effects' => array_fill(0, 20, 0) // Set side effects to 0
];

$json_data = json_encode($data);

// Define paths and algorithms
$algorithms = [
    'KMEANS' => './predictions/Predict_KMeans.py', // Adjust this path
    'Agglomerative' => './predictions/Predict_Agg.py', // Adjust this path
    'GMM' => './predictions/GMM_Predict.py', // Adjust this path
];

// Check if Python interpreter and scripts exist
$python_path = PYTHON_PATH; // set in configuration/app.php
if (strpbrk($python_path, '/\\') !== false && !file_exists($python_path)) {
    die("Python not found at $python_path. Set PYTHON_PATH in configuration/app.php.");
}

foreach ($algorithms as $algorithm => $script_path) {
    if (!file_exists($script_path)) {
        die("Script not found for $algorithm at $script_path");
    }
}

// Initialize array to store predictions
$predicted_side_effects = [];

// Loop through each algorithm
foreach ($algorithms as $algorithm => $script_path) {
    // Build the command
    $command = escapeshellcmd("$python_path $script_path");
    $process = proc_open($command, [
        0 => ["pipe", "r"], // stdin
        1 => ["pipe", "w"], // stdout
        2 => ["pipe", "w"]  // stderr
    ], $pipes);

    if (is_resource($process)) {
        // Write the JSON data to the Python script's stdin
        fwrite($pipes[0], $json_data);
        fclose($pipes[0]);

        // Read the output from the Python script
        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        // Read any error output from the Python script
        $error_output = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        // Close the process
        $return_value = proc_close($process);

        // Check for errors
        if ($return_value != 0) {
            $error = "Error executing Python script for $algorithm: $error_output";
            $_SESSION['error'] = $error;
        } else {
            // Decode the JSON output from the Python script and store predictions
            $predictions = json_decode($output, true);
            $predicted_side_effects[$algorithm] = $predictions;
        }
    } else {
        $_SESSION['error'] = "Failed to open the Python process for $algorithm.";
    }
}

// Store the predicted side effects in the session variable
$_SESSION['predicted_side_effects'] = $predicted_side_effects;

// Redirect to the display prediction page
header('Location: ./prediction_results.php');
exit();
?>
