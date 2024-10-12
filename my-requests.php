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

// Get the donor's email from the session
$donor_email = $_SESSION['email'];

// Fetch the donor ID based on the email
$sql = "SELECT id FROM donors WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $donor_email);
$stmt->execute();
$result = $stmt->get_result();
$donor = $result->fetch_assoc();
$donor_id = $donor['id'];
$stmt->close();

// Fetch approved requests associated with the donor
$sql = "SELECT br.id, br.blood_group, br.quantity, br.details, br.district, br.hospital_name, br.hospital_address, br.created_at, br.patient_id 
        FROM blood_requests br 
        WHERE br.donor_id = ? AND br.status = 'Approved' 
        ORDER BY br.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Approved Requests</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f8f8;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #e63946; /* Red background color */
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        .options {
            text-align: center;
            margin-top: 20px;
        }
        button {
            padding: 10px 20px;
            background-color: #e63946; /* Red background color */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #d62839; /* Darker red on hover */
        }

        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
            max-width: 600px; /* Set a max width for the modal */
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Approved Requests</h1>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Blood Group</th>
                        <th>Quantity</th>
                        <th>Details</th>
                        <th>District</th>
                        <th>Hospital Name</th>
                        <th>Hospital Address</th>
                        <th>Created At</th>
                        <th>View Patient</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['blood_group']); ?></td>
                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($row['details']); ?></td>
                            <td><?php echo htmlspecialchars($row['district']); ?></td>
                            <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['hospital_address']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <button onclick="viewPatient(<?php echo $row['patient_id']; ?>)">View Patient</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No approved requests found.</p>
        <?php endif; ?>

        <div class="options">
            <button onclick="window.location.href='donor-dashboard.php'">Back to Dashboard</button>
        </div>
    </div>

    <!-- Modal for patient details -->
    <div id="patientModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Patient Details</h2>
            <div id="patientDetails"></div>
        </div>
    </div>

    <script>
        function viewPatient(patientId) {
            // Create an XMLHttpRequest to fetch patient details
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "get_patient_details.php?id=" + patientId, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Display the patient details in the modal
                    document.getElementById("patientDetails").innerHTML = xhr.responseText;
                    document.getElementById("patientModal").style.display = "block"; // Show the modal
                }
            };
            xhr.send();
        }

        function closeModal() {
            document.getElementById("patientModal").style.display = "none"; // Hide the modal
        }

        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById("patientModal");
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };
    </script>

    <?php
    // Close the statement and database connection
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
