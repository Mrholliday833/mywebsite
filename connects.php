<?php
// Database configuration
$host = 'localhost';
$dbname = 'wasafi';
$username = 'root';
$password = '';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    try {
        // Validate and sanitize inputs
        $special_instructions = sanitizeInput($_POST['special_instructions']);
        $quantity = sanitizeInput($_POST['quantity']);
        $waste_type = sanitizeInput($_POST['waste_type']);
        $collection_time = sanitizeInput($_POST['collection_time']);
        $collection_date = sanitizeInput($_POST['collection_date']);
        $request_date = sanitizeInput($_POST['request_date']);
        $assigned_driver_id = sanitizeInput($_POST['assigned_driver_id']);

        // Basic validation
        $errors = [];
        
        if (empty($quantity) || !is_numeric($quantity) || $quantity <= 0) {
            $errors[] = "Quantity must be a positive number";
        }
        
        if (empty($waste_type)) {
            $errors[] = "Waste type is required";
        }
        
        if (empty($collection_time)) {
            $errors[] = "Collection time is required";
        }
        
        if (empty($collection_date)) {
            $errors[] = "Collection date is required";
        }
        
        if (empty($request_date)) {
            $errors[] = "Request date is required";
        }
        
        if (empty($assigned_driver_id)) {
            $errors[] = "Driver ID is required";
        }

        // If no errors, proceed with database connection
        if (empty($errors)) {
            // Create connection
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare SQL statement
            $stmt = $conn->prepare("INSERT INTO service_requests 
                                  (special_instructions, quantity, waste_type, collection_time, 
                                   collection_date, request_date, assigned_driver_id, status) 
                                  VALUES (:special_instructions, :quantity, :waste_type, :collection_time, 
                                          :collection_date, :request_date, :assigned_driver_id, 'Pending')");

            // Bind parameters
            $stmt->bindParam(':special_instructions', $special_instructions);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':waste_type', $waste_type);
            $stmt->bindParam(':collection_time', $collection_time);
            $stmt->bindParam(':collection_date', $collection_date);
            $stmt->bindParam(':request_date', $request_date);
            $stmt->bindParam(':assigned_driver_id', $assigned_driver_id);

            // Execute the statement
            $stmt->execute();

            // Success message
            echo "<!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Request Submitted</title>
                    <style>
                        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 20px; }
                        .success-container { max-width: 600px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); text-align: center; }
                        h1 { color: #2e8b57; }
                        .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #2e8b57; color: white; text-decoration: none; border-radius: 5px; }
                    </style>
                </head>
                <body>
                    <div class='success-container'>
                        <h1>Request Submitted Successfully!</h1>
                        <p>Your waste collection request has been received. We'll contact you for confirmation.</p>
                        <p><strong>Collection Date:</strong> $collection_date at $collection_time</p>
                        <a href='services_requests.html' class='btn'>Make Another Request</a>
                    </div>
                </body>
                </html>";
        } else {
            // Display errors
            echo "<!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <title>Error in Form Submission</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        .error-container { max-width: 600px; margin: 50px auto; background: #f8d7da; padding: 20px; border-radius: 5px; color: #721c24; }
                        ul { margin: 10px 0; padding-left: 20px; }
                        .btn { display: inline-block; margin-top: 10px; padding: 8px 15px; background: #721c24; color: white; text-decoration: none; border-radius: 5px; }
                    </style>
                </head>
                <body>
                    <div class='error-container'>
                        <h2>There were errors with your submission:</h2>
                        <ul>";
            foreach ($errors as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul>
                        <a href='javascript:history.back()' class='btn'>Go Back and Correct</a>
                    </div>
                </body>
                </html>";
        }
    } catch(PDOException $e) {
        echo "<div style='color: red; padding: 15px; border: 1px solid #f5c6cb; background-color: #f8d7da; max-width: 600px; margin: 50px auto; border-radius: 5px;'>
                <h2>Database Error</h2>
                <p>Error: " . $e->getMessage() . "</p>
                <p>Please try again later or contact support.</p>
                <a href='services_requests.html' style='display: inline-block; margin-top: 10px; padding: 8px 15px; background: #721c24; color: white; text-decoration: none; border-radius: 5px;'>Return to Form</a>
              </div>";
    }
} else {
    // If someone tries to access this page directly without submitting the form
    header("Location: services_requests.html");
    exit();
}
?>