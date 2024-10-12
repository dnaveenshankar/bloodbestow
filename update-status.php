<?php
// Start the session
session_start();

// Include the database connection file
include('db_connection.php');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the status and email from the POST request
    $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
    $email = $_POST['email'];

    // Prepare and bind the SQL statement to update the status
    $sql = "UPDATE donors SET status = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $status, $email);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        echo "Status updated successfully.";
    } else {
        echo "Error updating status: " . $stmt->error;
    }
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
