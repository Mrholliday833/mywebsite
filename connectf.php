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
        $comments = sanitizeInput($_POST['comments']);
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
        $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : null;

        // Basic validation
        $errors = [];
        
        if (empty($comments)) {
            $errors[] = "Comments are required";
        }
        
        if ($rating < 1 || $rating > 5) {
            $errors[] = "Please select a valid rating";
        }
        
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address";
        }

        // If no errors, proceed with database connection
        if (empty($errors)) {
            // Create connection
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare SQL statement
            $stmt = $conn->prepare("INSERT INTO feedback 
                                  (comments, rating, email, submission_date) 
                                  VALUES (:comments, :rating, :email, NOW())");

            // Bind parameters
            $stmt->bindParam(':comments', $comments);
            $stmt->bindParam(':rating', $rating);
            $stmt->bindParam(':email', $email);

            // Execute the statement
            $stmt->execute();

            // Success message
            echo "<!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Thank You</title>
                    <style>
                        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 20px; }
                        .thank-you-container { max-width: 600px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); text-align: center; }
                        h1 { color: #2e8b57; }
                        .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #2e8b57; color: white; text-decoration: none; border-radius: 5px; }
                    </style>
                </head>
                <body>
                    <div class='thank-you-container'>
                        <h1>Asante kwa Maoni Yako!</h1>
                        <p>Thank you for taking the time to share your feedback with us. We appreciate your rating of $rating stars.</p>
                        <p>Your comments help us improve our services.</p>
                        <a href='feedback.html' class='btn'>Return to Feedback Form</a>
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
                        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f5f5f5; }
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
                <a href='feedback.html' style='display: inline-block; margin-top: 10px; padding: 8px 15px; background: #721c24; color: white; text-decoration: none; border-radius: 5px;'>Return to Feedback Form</a>
              </div>";
    }
} else {
    // If someone tries to access this page directly without submitting the form
    header("Location: feedback.html");
    exit();
}
?>