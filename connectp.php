// connect.php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'wasafi';
$username = 'root';
$password = '';

// Check if payment form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['transaction_id'])) {
    $transaction_id = $_POST['transaction_id'];
    $payment_method = $_POST['payment_method'];
    $amount = $_POST['amount'];

    // Validate data
    if (empty($transaction_id) || empty($payment_method) || empty($amount)) {
        die("Error: All fields are required!");
    }

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if transaction ID already exists
        $stmt = $conn->prepare("SELECT * FROM transactions WHERE transaction_id = ?");
        $stmt->execute([$transaction_id]);
        
        if ($stmt->rowCount() > 0) {
            die("Error: Transaction ID already exists!");
        }

        // Insert new transaction
        $stmt = $conn->prepare("INSERT INTO transactions (transaction_id, payment_method, amount, transaction_date) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$transaction_id, $payment_method, $amount]);

        echo "Payment successful! Transaction ID: $transaction_id";
    } catch(PDOException $e) {
        echo "Database Error: " . $e->getMessage();
    }
} else {
    echo "Error: Form not submitted correctly!";
}
?>