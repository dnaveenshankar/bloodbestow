<?php
session_start();
include 'db_connection.php';
include 'districts.php'; // Include the districts file

// Check if user is logged in
if (!isset($_SESSION['patient_id'])) {
    header("Location: login-patient.php");
    exit();
}

// Fetch patient details
$patient_id = $_SESSION['patient_id'];
$sql = "SELECT name, dob, blood_group, phone, address, district, pincode FROM patients WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "No patient found.";
    exit();
}

$row = $result->fetch_assoc();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $phone = $_POST['phone']; // Use 'phone' instead of 'phone_number'
    $address = $_POST['address'];
    $district = $_POST['district'];
    $pincode = $_POST['pincode'];

    // Update patient profile without changing blood group
    $update_sql = "UPDATE patients SET name=?, dob=?, phone=?, address=?, district=?, pincode=? WHERE id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssisssi", $name, $dob, $phone, $address, $district, $pincode, $patient_id);
    
    if ($update_stmt->execute()) {
        echo "<script>alert('Profile updated successfully.'); window.location.href='patient-dashboard.php';</script>";
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}

// Close statements
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Blood Bestow</title>
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
        input, select {
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
        .blood-group {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Profile</h1>
        <form method="post">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($row['dob']); ?>" required>
            </div>
            <div class="form-group">
                <label>Blood Group</label>
                <div class="blood-group"><?php echo htmlspecialchars($row['blood_group']); ?></div>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($row['phone']); ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($row['address']); ?>" required>
            </div>
            <div class="form-group">
                <label for="district">District</label>
                <select id="district" name="district" required>
                    <?php
                    $districts = getDistricts();
                    foreach ($districts as $district_option) {
                        echo "<option value=\"$district_option\" " . ($row['district'] == $district_option ? 'selected' : '') . ">$district_option</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="pincode">Pincode</label>
                <input type="text" id="pincode" name="pincode" value="<?php echo htmlspecialchars($row['pincode']); ?>" required>
            </div>
            <button type="submit" class="button">Update Profile</button>
        </form>
    </div>
</body>
</html>
