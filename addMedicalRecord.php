<?php
require 'conn.php';
session_start();

function createMedicalRecords($visit_date, $doctor_name, $diagnosis, $medicine_name, $dosage, $end_date, $start_date, $notes, $status)
{
    $conn = connection();

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (!isset($_SESSION['username'])) {
        die("User not logged in.");
    }

    $username = $_SESSION['username'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $user_id = $row['id'];
    } else {
        die("User not found in the database.");
    }

    $stmt->close();

    $current_date = date('Y-m-d');

    $start_date = date('Y-m-d', strtotime($start_date));
    $end_date = date('Y-m-d', strtotime($end_date));

    if ($current_date > $end_date) {
        $status = 'Completed';
    } else {
        $status = 'Ongoing';
    }



    $sql_medical_record = "INSERT INTO medical_records (user_id, visit_date, doctor_name, diagnosis, medications, notes) 
                           VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_medical_record);
    if (!$stmt) {
        die("Error preparing medical record statement: " . $conn->error);
    }

    $stmt->bind_param("isssss", $user_id, $visit_date, $doctor_name, $diagnosis, $medicine_name, $notes);
    if (!$stmt->execute()) {
        die("Error adding medical record: " . $stmt->error);
    }
    $stmt->close();

    $sql_medications = "INSERT INTO medications (user_id, medicine_name, dosage, start_date, end_date, status) 
                        VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_medications);
    if (!$stmt) {
        die("Error preparing medications statement: " . $conn->error);
    }

    $stmt->bind_param("isssss", $user_id, $medicine_name, $dosage, $start_date, $end_date, $status);
    if (!$stmt->execute()) {
        die("Error adding medication record: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

    echo "<script>alert('Medical record added successfully!'); window.location.href='medical_record.php';</script>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_add'])) {
    $visit_date = $_POST['visit_date'] ?? '';
    $doctor_name = $_POST['doctor_name'] ?? '';
    $diagnosis = $_POST['diagnosis'] ?? '';
    $medicine_name = $_POST['medicine_name'] ?? '';
    $dosage = $_POST['dosage'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $status = $_POST['status'] ?? '';

    createMedicalRecords($visit_date, $doctor_name, $diagnosis, $medicine_name, $dosage, $end_date, $start_date, $notes, $status);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <main class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <h2 class="fw-light mb-4 text-center">Add Medical Record</h2>
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="visit_date" class="form-label">Visit Date</label>
                        <input type="date" name="visit_date" id="visit_date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="doctor_name" class="form-label">Doctor's Name</label>
                        <input type="text" name="doctor_name" id="doctor_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="diagnosis" class="form-label">Diagnosis</label>
                        <input type="text" name="diagnosis" id="diagnosis" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="medicine_name" class="form-label">Medication</label>
                        <input type="text" name="medicine_name" id="medicine_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="dosage" class="form-label">Dosage</label>
                        <input type="text" name="dosage" id="dosage" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                    </div>

                    <button type="submit" name="btn_add" class="btn btn-success w-100">
                        <i class="fa-solid fa-plus"></i> Add Record
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>