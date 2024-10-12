<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['patient_id'])) {
    header("Location: login-patient.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];

// Fetch patient details for greeting or further use (optional)
$sql = "SELECT name FROM patients WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - Blood Bestow</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        h1 {
            text-align: center;
            color: #e63946;
        }
        .button {
            display: block;
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            background-color: #e63946;
            color: white;
            border: none;
            border-radius: 5px;
            text-align: center;
            font-size: 18px;
            text-decoration: none;
        }
        .button:hover {
            background-color: #d62839;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($patient['name']); ?>!</h1>
        <a href="edit-profile-patient.php?id=<?php echo $patient_id; ?>" class="button">Edit Profile</a>
        <a href="raise-request.php?id=<?php echo $patient_id; ?>" class="button">Raise Request</a>
        <a href="find-donors.php?id=<?php echo $patient_id; ?>" class="button">Find Donors</a>
        <a href="view-requests.php?id=<?php echo $patient_id; ?>" class="button">View Requests</a>
        <a href="patient-logout.php" class="button" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
    </div>
</body>
</html>
