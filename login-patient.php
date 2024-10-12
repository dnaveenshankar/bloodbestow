<?php
session_start();
include 'db_connection.php';

// Initialize variables
$email = $dob = $phone = '';
$attempts = 0; // Track the number of login attempts
$error_message = '';

// Check if user is already logged in
if (isset($_SESSION['patient_id'])) {
    header("Location: patient-dashboard.php?username=" . $_SESSION['patient_email']);
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $phone = $_POST['phone'] ?? ''; // Optional phone number for verification

    // Check login attempts from the session or database
    if (isset($_SESSION['login_attempts'][$email])) {
        $attempts = $_SESSION['login_attempts'][$email];
    } else {
        $_SESSION['login_attempts'][$email] = 0;
    }

    // If attempts are less than 3, allow normal login
    if ($attempts < 3) {
        // Query to fetch patient details based on email and dob
        $sql = "SELECT * FROM patients WHERE email = ? AND dob = ?";
        $stmt = $conn->prepare($sql);
        
        // Check if preparation failed
        if ($stmt) {
            $stmt->bind_param("ss", $email, $dob);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // User found, create session and redirect to dashboard
                $patient = $result->fetch_assoc();
                $_SESSION['patient_id'] = $patient['id'];
                $_SESSION['patient_email'] = $patient['email'];
                unset($_SESSION['login_attempts'][$email]); // Clear attempts on success
                header("Location: patient-dashboard.php?username=" . $email);
                exit();
            } else {
                $attempts++;
                $_SESSION['login_attempts'][$email] = $attempts; // Update attempts in session
                $error_message = "Invalid email or date of birth. Attempt $attempts/3.";
            }
            $stmt->close();
        } else {
            // SQL statement preparation failed
            $error_message = "Database error. Please try again.";
        }
    } else {
        // If attempts are 3 or more, ask for phone number for verification
        if (empty($phone)) {
            $error_message = "Please provide your phone number for verification.";
        } else {
            // Query to fetch patient details based on email, dob, and phone
            $sql = "SELECT * FROM patients WHERE email = ? AND dob = ? AND phone = ?";
            $stmt = $conn->prepare($sql);

            // Check if preparation failed
            if ($stmt) {
                $stmt->bind_param("sss", $email, $dob, $phone);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // User found, create session and redirect to dashboard
                    $patient = $result->fetch_assoc();
                    $_SESSION['patient_id'] = $patient['id'];
                    $_SESSION['patient_email'] = $patient['email'];
                    unset($_SESSION['login_attempts'][$email]); // Clear attempts on success
                    header("Location: patient-dashboard.php?username=" . $email);
                    exit();
                } else {
                    $error_message = "Invalid email, date of birth, or phone number.";
                }
                $stmt->close();
            } else {
                // SQL statement preparation failed
                $error_message = "Database error. Please try again.";
            }
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login as Patient - Blood Bestow</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 400px;
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
        input {
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
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login as Patient</h1>
        <?php if ($error_message): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="email">Email ID</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <label for="dob">Date of Birth</label>
            <input type="date" id="dob" name="dob" required>

            <div id="phone-div" style="<?php echo $attempts >= 3 ? '' : 'display:none;'; ?>">
                <label for="phone">Phone Number (for verification)</label>
                <input type="text" id="phone" name="phone" placeholder="Enter your phone number">
            </div>

            <button type="submit">Login</button>
        </form>
    </div>

    <script>
        // Show phone number input if attempts are 3 or more
        document.querySelector('form').onsubmit = function () {
            var attempts = <?php echo isset($_SESSION['login_attempts'][$email]) ? $_SESSION['login_attempts'][$email] : 0; ?>;
            if (attempts >= 3) {
                document.getElementById('phone-div').style.display = 'block';
            }
        };
    </script>
</body>
</html>
