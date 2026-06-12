<?php
// Include the database connection file
include('db.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    $phone = $_POST['phone'];

    // Validate that passwords match
    if ($password !== $confirm_password) {
        echo "Passwords do not match!";
        exit;
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL query to insert data into the customer table
    $sql = "INSERT INTO customer (customerName, customerEmail, customerPassword, customerPhoneNo) VALUES (?, ?, ?, ?)";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters to the statement
        // 'ssss' = 4 strings: name, email, hashed password, phone
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $phone);
        
        // Execute the query
        if ($stmt->execute()) {
            // Redirect to login or another page
            header("Location: login.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    // Close the connection
    $conn->close();
}
?>
