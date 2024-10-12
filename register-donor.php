<?php
include 'db_connection.php';
include 'districts.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $age = $_POST['age'];
    $bloodGroup = $_POST['blood-group'];
    $status = isset($_POST['status']) ? 1 : 0;
    $address = $_POST['address'];
    $district = $_POST['district'];
    $state = $_POST['state'];
    $pincode = $_POST['pincode'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email already exists
    $sql = "SELECT * FROM donors WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered. Please use a different email.');</script>";
    } else {
        // Insert the new donor
        $sql = "INSERT INTO donors (name, dob, age, blood_group, status, address, district, state, pincode, mobile, email, password) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisssssiiss", $name, $dob, $age, $bloodGroup, $status, $address, $district, $state, $pincode, $mobile, $email, $password);

        if ($stmt->execute()) {
            header("Location: donor-dashboard.php?username=" . $email);
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register as Donor - Blood Bestow</title>
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
        }
        button:hover {
            background-color: #d62839;
        }
        .status {
            margin: 20px 0;
        }
        .info-icon {
            font-size: 0.8em;
            color: #888;
        }
    </style>
    <script>
        function calculateAge() {
            const dob = document.getElementById('dob').value;
            const birthDate = new Date(dob);
            const age = new Date().getFullYear() - birthDate.getFullYear();
            const m = new Date().getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && new Date() < birthDate)) {
                age--;
            }
            document.getElementById('age').value = age;
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Register as Donor</h1>
        <form method="POST" action="">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>

            <label for="dob">Date of Birth</label>
            <input type="date" id="dob" name="dob" required onchange="calculateAge()">

            <label for="age">Age</label>
            <input type="text" id="age" name="age" readonly required>

            <label for="blood-group">Blood Group</label>
            <select id="blood-group" name="blood-group" required>
                <option value="">--Select--</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select>

            <div class="status">
                <label>Status:<span class="info-icon">(Click the check box to set your profile Active)</span> </label>
                <input type="checkbox" id="status" name="status"> Profile Active
            </div>

            <label for="address">Address</label>
            <input type="text" id="address" name="address" required>

            <label for="district">District</label>
            <select id="district" name="district" required>
                <option value="">--Select--</option>
                <?php
                $districts = getDistricts();
                foreach ($districts as $district) {
                    echo "<option value='$district'>$district</option>";
                }
                ?>
            </select>

            <label for="state">State</label>
            <input type="text" id="state" name="state" value="Tamil Nadu" readonly>

            <label for="pincode">Pincode</label>
            <input type="text" id="pincode" name="pincode" required pattern="[0-9]{6}" title="Enter a valid 6-digit pincode">

            <label for="mobile">Mobile Number</label>
            <input type="tel" id="mobile" name="mobile" required pattern="[0-9]{10}" title="Enter a valid 10-digit mobile number">

            <label for="email">Email ID <span class="info-icon">(This will be your username)</span></label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
