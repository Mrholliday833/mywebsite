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

// Validate phone number
function validatePhone($phone) {
    // Remove all non-digit characters
    $cleaned = preg_replace('/\D/', '', $phone);
    // Check if we have at least 10 digits
    return (strlen($cleaned) >= 10);
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    try {
        // Validate and sanitize inputs
        $full_name = sanitizeInput($_POST['full_name']);
        $vehicle_type = sanitizeInput($_POST['vehicle_type']);
        $email = sanitizeInput($_POST['email']);
        $vehicle_number = sanitizeInput($_POST['vehicle_number']);
        $license_number = sanitizeInput($_POST['license_number']);
        $phone = sanitizeInput($_POST['phone']);

        // Basic validation
        $errors = [];
        
        if (empty($full_name)) {
            $errors[] = "Full name is required";
        }
        
        if (empty($vehicle_type)) {
            $errors[] = "Vehicle type is required";
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Valid email is required";
        }
        
        if (empty($vehicle_number)) {
            $errors[] = "Vehicle number is required";
        }
        
        if (empty($license_number)) {
            $errors[] = "License number is required";
        }
        
        if (empty($phone) || !validatePhone($phone)) {
            $errors[] = "Valid phone number is required (at least 10 digits)";
        }

        // If no errors, proceed with database connection
        if (empty($errors)) {
            // Create connection
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Start transaction
            $conn->beginTransaction();

            try {
                // Check if email already exists
                $checkStmt = $conn->prepare("SELECT id FROM drivers WHERE email = :email");
                $checkStmt->bindParam(':email', $email);
                $checkStmt->execute();
                
                if ($checkStmt->rowCount() > 0) {
                    throw new Exception("Email already exists. Please use a different email.");
                }

                // Check if vehicle number exists
                $checkStmt = $conn->prepare("SELECT id FROM drivers WHERE vehicle_number = :vehicle_number");
                $checkStmt->bindParam(':vehicle_number', $vehicle_number);
                $checkStmt->execute();
                
                if ($checkStmt->rowCount() > 0) {
                    throw new Exception("Vehicle number already registered.");
                }

                // Prepare SQL statement
                $stmt = $conn->prepare("INSERT INTO drivers 
                                     (full_name, vehicle_type, email, vehicle_number, license_number, phone, registration_date) 
                                     VALUES (:full_name, :vehicle_type, :email, :vehicle_number, :license_number, :phone, NOW())");

                // Bind parameters
                $stmt->bindParam(':full_name', $full_name);
                $stmt->bindParam(':vehicle_type', $vehicle_type);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':vehicle_number', $vehicle_number);
                $stmt->bindParam(':license_number', $license_number);
                $stmt->bindParam(':phone', $phone);

                // Execute the statement
                $stmt->execute();

                // Commit transaction
                $conn->commit();

                // Success message
                echo "<!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <title>Registration Successful</title>
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; background-color: #f5f5f5; }
                            .container { max-width: 600px; margin: 30px auto; padding: 30px; background: white; border-radius: 8px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
                            .success { color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
                            .details { margin-top: 20px; }
                            .details p { margin: 10px 0; padding-bottom: 10px; border-bottom: 1px solid #eee; }
                            .print-btn { background-color: #3498db; color: white; border: none; padding: 10px 15px; font-size: 16px; border-radius: 4px; cursor: pointer; margin-top: 20px; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='success'>
                                <h2>Registration Successful!</h2>
                                <p>Thank you for registering with Wasafi Transport Services.</p>
                            </div>
                            
                            <div class='details'>
                                <h3>Your Registration Details:</h3>
                                <p><strong>Full Name:</strong> $full_name</p>
                                <p><strong>Vehicle Type:</strong> $vehicle_type</p>
                                <p><strong>Email:</strong> $email</p>
                                <p><strong>Vehicle Number:</strong> $vehicle_number</p>
                                <p><strong>License Number:</strong> $license_number</p>
                                <p><strong>Phone:</strong> $phone</p>
                                <p><strong>Registration Date:</strong> ".date('F j, Y')."</p>
                            </div>
                            
                            <button class='print-btn' onclick='window.print()'>Print This Receipt</button>
                            <p>Keep this information for your records. We'll contact you if we need any additional information.</p>
                        </div>
                    </body>
                    </html>";
            } catch (Exception $e) {
                // Rollback transaction on error
                $conn->rollBack();
                $errors[] = $e->getMessage();
                throw $e;
            }
        }
    } catch(PDOException $e) {
        $errors[] = "Database error: " . $e->getMessage();
    } catch(Exception $e) {
        $errors[] = $e->getMessage();
    }
}

// Display errors if any occurred
if (!empty($errors)) {
    echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <title>Error in Form Submission</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; background-color: #f5f5f5; }
                .container { max-width: 600px; margin: 30px auto; padding: 30px; background: white; border-radius: 8px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
                .error { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
                ul { margin-top: 10px; }
                .back-btn { background-color: #3498db; color: white; border: none; padding: 10px 15px; font-size: 16px; border-radius: 4px; cursor: pointer; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='error'>
                    <h2>There were errors with your submission:</h2>
                    <ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>
                    <p>Please go back and correct these errors.</p>
                    <button class='back-btn' onclick='history.back()'>Go Back</button>
                </div>
            </div>
        </body>
        </html>";
    exit();
}
?>