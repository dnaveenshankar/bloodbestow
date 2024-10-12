<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['patient_id'])) {
    header("Location: login-patient.php");
    exit();
}

// Fetch patient's blood requests
$patient_id = $_SESSION['patient_id'];
$sql = "SELECT br.*, 
               d.name AS donor_name, 
               d.dob AS donor_dob,
               d.age AS donor_age,
               d.blood_group AS donor_blood_group, 
               d.address AS donor_address, 
               d.district AS donor_district, 
               d.state AS donor_state, 
               d.pincode AS donor_pincode, 
               d.mobile AS donor_mobile, 
               d.email AS donor_email 
        FROM blood_requests br 
        LEFT JOIN donors d ON br.donor_id = d.id 
        WHERE br.patient_id = ? 
        ORDER BY br.created_at DESC";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if any requests found
if ($result->num_rows == 0) {
    echo "No requests found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Blood Requests - Blood Bestow</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .modal-header {
            background-color: #e63946;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Your Blood Requests</h1>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Blood Group</th>
                    <th>Quantity</th>
                    <th>Details</th>
                    <th>District</th>
                    <th>Hospital Name</th>
                    <th>Created At</th>
                    <th>Status</th>
                    <th>View Donor</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($request = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($request['blood_group']); ?></td>
                        <td><?php echo htmlspecialchars($request['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($request['details']); ?></td>
                        <td><?php echo htmlspecialchars($request['district']); ?></td>
                        <td><?php echo htmlspecialchars($request['hospital_name']); ?></td>
                        <td><?php echo htmlspecialchars($request['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($request['status']); ?></td>
                        <td>
                            <?php if ($request['status'] === 'Approved' && $request['donor_id'] !== null): ?>
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#donorModal-<?php echo $request['id']; ?>">
                                    View Donor
                                </button>
                                
                                <!-- Donor Modal -->
                                <div class="modal fade" id="donorModal-<?php echo $request['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="donorModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="donorModalLabel">Donor Details</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
    <p><strong>Name:</strong> <?php echo htmlspecialchars($request['donor_name']); ?></p>
    <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($request['donor_dob']); ?></p>
    <p><strong>Age:</strong> <?php echo htmlspecialchars($request['donor_age']); ?></p>
    <p><strong>Blood Group:</strong> <?php echo htmlspecialchars($request['donor_blood_group']); ?></p>
    <p><strong>Address:</strong> <?php echo htmlspecialchars($request['donor_address']); ?></p>
    <p><strong>District:</strong> <?php echo htmlspecialchars($request['donor_district']); ?></p>
    <p><strong>State:</strong> <?php echo htmlspecialchars($request['donor_state']); ?></p>
    <p><strong>Pincode:</strong> <?php echo htmlspecialchars($request['donor_pincode']); ?></p>
    <p><strong>Mobile:</strong> <?php echo htmlspecialchars($request['donor_mobile']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($request['donor_email']); ?></p>
</div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Close the statement and connection
$stmt->close();
$conn->close();
?>
