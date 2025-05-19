<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$dbname = "wasafi";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($conn->real_escape_string($_POST['password']), PASSWORD_DEFAULT);
    $location = $conn->real_escape_string($_POST['location']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $phone = $conn->real_escape_string($_POST['phone']);

    // Check if email exists
    $email_check = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($email_check);
    
    if ($result->num_rows > 0) {
        die("Error: Email already exists. Please use a different email.");
    }

    // Handle file upload
    $profile_picture = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $imageFileType = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $new_filename;
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $profile_picture = $target_file;
            } else {
                die("Error uploading file. Please try again.");
            }
        } else {
            die("File is not an image.");
        }
    }

    // Insert data into database
    $sql = "INSERT INTO users (full_name, email, password, phone, location, gender, profile_picture) 
            VALUES ('$full_name', '$email', '$password', '$phone', '$location', '$gender', '$profile_picture')";

    if ($conn->query($sql) === TRUE) {
        header("Location: success.html");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
