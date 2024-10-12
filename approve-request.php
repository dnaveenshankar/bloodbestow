<?php
// Start the session
session_start();

// Include the database connection file
include('db_connection.php'); // Ensure this path is correct

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: donor-login.php"); // Redirect to login page if not logged in
    exit;
}

// Check if request ID is provided
if (isset($_GET['request_id'])) {
    $request_id = $_GET['request_id'];
    $donor_email = $_SESSION['email'];

    // Fetch donor ID based on email
    $sql = "SELECT id FROM donors WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $donor_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $donor = $result->fetch_assoc();
    $donor_id = $donor['id'];
    $stmt->close();

    // Update the blood request to set the donor ID and change the status to 'Approved'
    $sql = "UPDATE blood_requests SET donor_id = ?, status = 'Approved' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $donor_id, $request_id);
    
    if ($stmt->execute()) {
        // Redirect back to all-requests.php with a success message
        header("Location: all-requests.php?success=Request approved successfully.");
    } else {
        // Redirect back with an error message
        header("Location: all-requests.php?error=Failed to approve the request.");
    }
    $stmt->close();
} else {
    // If request ID is not provided, redirect back with an error message
    header("Location: all-requests.php?error=No request ID provided.");
}

// Close the database connection
$conn->close();
?>
