<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['patient_id'])) {
    header("Location: login-patient.php");
    exit();
}

// Fetch patient's blood group from session or database
$patient_id = $_SESSION['patient_id'];
$sql = "SELECT blood_group FROM patients WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "No patient found.";
    exit();
}

$patient = $result->fetch_assoc();
$blood_group = $patient['blood_group']; // Get the blood group of the logged-in patient

$stmt->close();

// Fetch donors with the same blood group and status 1
$sql = "SELECT * FROM donors WHERE blood_group = ? AND status = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $blood_group);
$stmt->execute();
$result = $stmt->get_result();

// Check if any donors found
if ($result->num_rows == 0) {
    echo "No donors found with blood group $blood_group.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Donors - Blood Bestow</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: auto;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #e63946;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Available Donors with Blood Group: <?php echo htmlspecialchars($blood_group); ?></h1>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Blood Group</th>
                    <th>Age</th>
                    <th>Address</th>
                    <th>District</th>
                    <th>State</th>
                    <th>Pincode</th>
                    <th>Mobile</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($donor = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($donor['name']); ?></td>
                        <td><?php echo htmlspecialchars($donor['blood_group']); ?></td>
                        <td><?php echo htmlspecialchars($donor['age']); ?></td>
                        <td><?php echo htmlspecialchars($donor['address']); ?></td>
                        <td><?php echo htmlspecialchars($donor['district']); ?></td>
                        <td><?php echo htmlspecialchars($donor['state']); ?></td>
                        <td><?php echo htmlspecialchars($donor['pincode']); ?></td>
                        <td><?php echo htmlspecialchars($donor['mobile']); ?></td>
                        <td><?php echo htmlspecialchars($donor['email']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Close the statement and connection
$stmt->close();
$conn->close();
?>
