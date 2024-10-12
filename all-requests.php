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

// Get the donor's blood group
$email = $_SESSION['email'];
$sql = "SELECT blood_group FROM donors WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$donor = $result->fetch_assoc();
$blood_group = $donor['blood_group'];
$stmt->close();

// Fetch all requests matching the donor's blood group and with approved status
$sql = "SELECT * FROM blood_requests WHERE blood_group = ? AND status = 'Pending' ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $blood_group);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Requests</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        .request {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
        }
        .request h2 {
            margin: 0;
        }
        .donate-button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #e63946;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .donate-button:hover {
            background-color: #d62839;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>All Requests for Blood Group <?php echo htmlspecialchars($blood_group); ?></h1>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($request = $result->fetch_assoc()): ?>
                <div class="request">
                    <h2>Request ID: <?php echo $request['id']; ?></h2>
                    <p><strong>Patient ID:</strong> <?php echo htmlspecialchars($request['patient_id']); ?></p>
                    <p><strong>Quantity:</strong> <?php echo htmlspecialchars($request['quantity']); ?> units</p>
                    <p><strong>Details:</strong> <?php echo htmlspecialchars($request['details']); ?></p>
                    <p><strong>District:</strong> <?php echo htmlspecialchars($request['district']); ?></p>
                    <p><strong>Hospital Name:</strong> <?php echo htmlspecialchars($request['hospital_name']); ?></p>
                    <p><strong>Created At:</strong> <?php echo htmlspecialchars($request['created_at']); ?></p>
                    <a href="approve-request.php?request_id=<?php echo $request['id']; ?>" class="donate-button">Donate</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No requests available for your blood group.</p>
        <?php endif; ?>

    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
