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

// Get the username from the session
$username = $_SESSION['name']; // Now fetching the name from the session

// Fetch the current donor status from the database
$sql = "SELECT status FROM donors WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['email']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$currentStatus = $row['status'];
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard</title>
    <script>
        function confirmLogout() {
            return confirm("Are you sure you want to logout?");
        }
        
        function toggleStatus(checkbox) {
            const status = checkbox.checked ? 1 : 0; // 1 for ON, 0 for OFF
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "update-status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert("Status updated successfully");
                }
            };
            xhr.send("status=" + status + "&email=<?php echo htmlspecialchars($_SESSION['email']); ?>");
        }
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f8f8;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        .status-toggle {
            margin: 20px 0;
            text-align: center;
        }
        .options {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        button {
            width: 100%; /* Make buttons full-width */
            height: 50px; /* Set a fixed height for the buttons */
            margin: 5px 0;
            border: none;
            background-color: #e63946; /* Red background color */
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 16px; /* Optional: Increase font size for better visibility */
        }
        button:hover {
            background-color: #d62839; /* Darker red on hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        
        <div class="status-toggle">
            <label for="status">Donor Status: </label>
            <input type="checkbox" id="status" name="status" <?php echo $currentStatus ? 'checked' : ''; ?> onclick="toggleStatus(this)">
        </div>

        <div class="options">
            <button onclick="window.location.href='edit-profile.php'">Edit Profile</button>
            <button onclick="window.location.href='all-requests.php'">All Requests</button>
            <button onclick="window.location.href='my-requests.php'">My Requests</button>
            <button onclick="if(confirmLogout()) window.location.href='logout.php'">Logout</button>
        </div>
    </div>
</body>
</html>
