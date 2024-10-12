<?php
// Start the session
session_start();

// Include the database connection file
include('db_connection.php'); // Ensure this path is correct

// Check if the patient ID is provided
if (isset($_GET['id'])) {
    $patient_id = intval($_GET['id']); // Sanitize input

    // Fetch patient details from the database
    $sql = "SELECT name, dob, blood_group, email, phone, address, district, pincode 
            FROM patients WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Output the patient details
        $patient = $result->fetch_assoc();
        echo "<strong>Name:</strong> " . htmlspecialchars($patient['name']) . "<br>";
        echo "<strong>Date of Birth:</strong> " . htmlspecialchars($patient['dob']) . "<br>";
        echo "<strong>Blood Group:</strong> " . htmlspecialchars($patient['blood_group']) . "<br>";
        echo "<strong>Email:</strong> " . htmlspecialchars($patient['email']) . "<br>";
        echo "<strong>Phone:</strong> " . htmlspecialchars($patient['phone']) . "<br>";
        echo "<strong>Address:</strong> " . htmlspecialchars($patient['address']) . "<br>";
        echo "<strong>District:</strong> " . htmlspecialchars($patient['district']) . "<br>";
        echo "<strong>Pincode:</strong> " . htmlspecialchars($patient['pincode']) . "<br>";
    } else {
        echo "No patient found with this ID.";
    }

    // Close the statement
    $stmt->close();
} else {
    echo "No patient ID provided.";
}

// Close the database connection
$conn->close();
?>
