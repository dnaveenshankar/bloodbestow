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

// Get the current user's details from the database
$email = $_SESSION['email'];
$sql = "SELECT name, dob, age, address, district, state, pincode, mobile FROM donors WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

// Calculate age from DOB
function calculateAge($dob) {
    $birthDate = new DateTime($dob);
    $today = new DateTime();
    return $today->diff($birthDate)->y;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $district = $_POST['district'];
    $state = $_POST['state'];
    $pincode = $_POST['pincode'];
    $mobile = $_POST['mobile'];

    // Calculate age based on DOB
    $age = calculateAge($dob);

    // Prepare SQL statement to update user details
    $updateSql = "UPDATE donors SET name = ?, dob = ?, age = ?, address = ?, district = ?, state = ?, pincode = ?, mobile = ? WHERE email = ?";
    $updateStmt = $conn->prepare($updateSql);
    // Adjust the type string: s=string, i=integer
    $updateStmt->bind_param("siissssss", $name, $dob, $age, $address, $district, $state, $pincode, $mobile, $email);
    
    if ($updateStmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='donor-dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating profile. Please try again.');</script>";
    }

    $updateStmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 5px 0 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #e63946;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin: 10px 0;
        }
        button:hover {
            background-color: #d62839;
        }
        .back-button {
            background-color: #007bff;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Profile</h1>
        <form method="POST" action="">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>

            <label for="dob">Date of Birth</label>
            <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($row['dob']); ?>" required>

            <label for="address">Address</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($row['address']); ?>" required>

            <label for="district">District</label>
            <input type="text" id="district" name="district" value="<?php echo htmlspecialchars($row['district']); ?>" required>

            <label for="state">State</label>
            <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($row['state']); ?>" required>

            <label for="pincode">Pincode</label>
            <input type="text" id="pincode" name="pincode" value="<?php echo htmlspecialchars($row['pincode']); ?>" required>

            <label for="mobile">Mobile</label>
            <input type="text" id="mobile" name="mobile" value="<?php echo htmlspecialchars($row['mobile']); ?>" required>

            <button type="submit">Update Profile</button>
            <button type="button" class="back-button" onclick="window.location.href='donor-dashboard.php'">Back</button>
        </form>
    </div>
</body>
</html>
