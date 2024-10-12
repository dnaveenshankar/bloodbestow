<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['patient_id'])) {
    header("Location: login-patient.php");
    exit();
}

// Fetch patient details including blood group
$patient_id = $_SESSION['patient_id'];
$sql = "SELECT name, email, blood_group FROM patients WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "No patient found.";
    exit();
}

$patient = $result->fetch_assoc();
$stmt->close();

// Handle request submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $blood_group = $patient['blood_group']; // Get blood group from the patient details
    $quantity = $_POST['quantity'];
    $details = $_POST['details'];
    $district = $_POST['district'];
    $hospital_name = $_POST['hospital_name'];
    $hospital_address = $_POST['hospital_address'];
    $donor_id = null; // Initially set to null until a donor is assigned

    // Insert request into the database
    // Insert request into the database
$sql = "INSERT INTO blood_requests (patient_id, blood_group, quantity, details, district, hospital_name, hospital_address, created_at, status, donor_id) 
VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 'Pending', ?)";
$stmt = $conn->prepare($sql);

// Bind parameters
$stmt->bind_param("isssssss", $patient_id, $blood_group, $quantity, $details, $district, $hospital_name, $hospital_address, $donor_id);


    if ($stmt->execute()) {
        echo "<script>alert('Blood request raised successfully.'); window.location.href='patient-dashboard.php';</script>";
    } else {
        echo "Error raising request: " . $conn->error;
    }

    // Close statement
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raise Blood Request - Blood Bestow</title>
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
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .button {
            width: 100%;
            padding: 15px;
            background-color: #e63946;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }
        .button:hover {
            background-color: #d62839;
        }
        .patient-info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f1f1f1;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Raise Blood Request</h1>
        
        <!-- Display Patient Info -->
        <div class="patient-info">
            <strong>Patient Name:</strong> <?php echo htmlspecialchars($patient['name']); ?><br>
            <strong>Blood Group:</strong> <?php echo htmlspecialchars($patient['blood_group']); ?>
        </div>

        <form method="POST">
            <div class="form-group">
                <label>Quantity (in units)</label>
                <input type="number" name="quantity" required>
            </div>
            <div class="form-group">
                <label>Details (optional)</label>
                <textarea name="details" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>District</label>
                <select name="district" required>
                    <option value="">Select District</option>
                    <?php
                    include 'districts.php';
                    $districts = getDistricts();
                    foreach ($districts as $district) {
                        echo "<option value=\"$district\">$district</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Hospital Name</label>
                <input type="text" name="hospital_name" required>
            </div>
            <div class="form-group">
                <label>Hospital Address</label>
                <textarea name="hospital_address" rows="3" required></textarea>
            </div>
            <button type="submit" class="button">Raise Request</button>
        </form>
    </div>
</body>
</html>
