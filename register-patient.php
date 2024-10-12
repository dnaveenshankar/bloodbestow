<?php
session_start();
include 'db_connection.php';

// Fetch all districts from the database or define them in an array
$districts = [
    "Chennai", "Coimbatore", "Madurai", "Tiruchirappalli", "Salem",
    "Tiruppur", "Vellore", "Dindigul", "Dharapuram", "Thanjavur",
    "Kanyakumari", "Virudhunagar", "Ramanathapuram", "Thiruvarur", "Pudukkottai",
    "Nagapattinam", "Namakkal", "Krishnagiri", "Thiruvannamalai", "Dharmapuri",
    "Tenkasi", "Tirunelveli", "Sivagangai", "Erode", "Kanchipuram",
    "Ariyalur", "Perambalur", "Karur", "Kallakurichi", "Cuddalore",
    "Villupuram", "Kanniyakumari", "Nilgiris", "Tirupathur", "Ramanathapuram",
    "Thiruvallur", "Sivagangai"
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $blood_group = $_POST['blood_group'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $district = $_POST['district'];
    $pincode = $_POST['pincode'];

    // Check if the email already exists
    $sql = "SELECT * FROM patients WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists. Please use a different email.');</script>";
    } else {
        // Insert new patient into the database
        $insert_sql = "INSERT INTO patients (name, dob, blood_group, email, phone, address, district, pincode) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ssssssss", $name, $dob, $blood_group, $email, $phone, $address, $district, $pincode);

        if ($insert_stmt->execute()) {
            echo "<script>alert('Registration successful!');</script>";
            header("Location: patient-dashboard.php?username=" . $email);
            exit();
        } else {
            echo "<script>alert('Registration failed. Please try again.');</script>";
        }

        $insert_stmt->close();
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
    <title>Register as Patient - Blood Bestow</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 500px;
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
    </style>
    <script>
        function calculateAge() {
            const dobInput = document.getElementById("dob");
            const ageInput = document.getElementById("age");
            const dob = new Date(dobInput.value);
            const today = new Date();

            if (dob && !isNaN(dob)) {
                let age = today.getFullYear() - dob.getFullYear();
                const monthDifference = today.getMonth() - dob.getMonth();
                if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }
                ageInput.value = age;
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Register as Patient</h1>
        <form method="POST" action="">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>

            <label for="dob">Date of Birth</label>
            <input type="date" id="dob" name="dob" onchange="calculateAge()" required>

            <label for="age">Age</label>
            <input type="number" id="age" name="age" readonly>

            <label for="blood_group">Blood Group</label>
            <select id="blood_group" name="blood_group" required>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select>

            <label for="email">Email ID</label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" required>

            <label for="address">Address</label>
            <input type="text" id="address" name="address" required>

            <label for="district">District</label>
            <select id="district" name="district" required>
                <option value="" disabled selected>Select District</option>
                <?php foreach ($districts as $district): ?>
                    <option value="<?php echo $district; ?>"><?php echo $district; ?></option>
                <?php endforeach; ?>
            </select>

            <label for="pincode">Pincode</label>
            <input type="text" id="pincode" name="pincode" required>

            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
