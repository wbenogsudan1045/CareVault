<?php
require 'conn.php';
session_start();

if (!isset($_GET['id'])) {
    die("No ID provided.");
}
$record_id = $_GET['id'];

$conn = connection();
$stmt = $conn->prepare("SELECT * FROM medications m JOIN medical_records r ON r.medications = m.medicine_name WHERE m.id = ?");
$stmt->bind_param("i", $record_id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

if (!$record) {
    die("Record not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_update'])) {
    $doctor_name = $_POST['doctor_name'];
    $diagnosis = $_POST['diagnosis'];
    $medicine_name = $_POST['medicine_name'];
    $dosage = $_POST['dosage'];
    $visit_date = $_POST['visit_date'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $notes = $_POST['notes'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE medications m 
        JOIN medical_records r ON r.medications = m.medicine_name 
        SET r.doctor_name = ?, 
            r.diagnosis = ?, 
            m.medicine_name = ?, 
            m.dosage = ?, 
            r.visit_date = ?, 
            m.start_date = ?, 
            m.end_date = ?, 
            r.notes = ?, 
            m.status = ? 
        WHERE m.id = ?");

    $stmt->bind_param(
        "sssssssssi",
        $doctor_name,
        $diagnosis,
        $medicine_name,
        $dosage,
        $visit_date,
        $start_date,
        $end_date,
        $notes,
        $status,
        $record_id
    );

    if ($stmt->execute()) {
        echo "<script>alert('Record updated successfully!'); window.location.href='archive.php';</script>";
        exit;
    } else {
        die("Update failed: " . $stmt->error);
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Archives</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <main class="container mt-5">
        <h2>Archives</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="doctor_name" class="form-label">Doctor Name</label>
                <input type="text" name="doctor_name" id="doctor_name" class="form-control"
                    value="<?= $record['doctor_name'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="diagnosis" class="form-label">Diagnosis</label>
                <input type="text" name="diagnosis" id="diagnosis" class="form-control"
                    value="<?= $record['diagnosis'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="visit_date" class="form-label">Visit Date</label>
                <input type="date" name="visit_date" id="visit_date" class="form-control"
                    value="<?= $record['visit_date'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="medicine_name" class="form-label">Medicine's Name</label>
                <input type="text" name="medicine_name" id="medicine_name" class="form-control"
                    value="<?= $record['medicine_name'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="dosage" class="form-label">Dosage</label>
                <input type="text" name="dosage" id="dosage" class="form-control" value="<?= $record['dosage'] ?>"
                    required>
            </div>
            <div class="mb-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control"
                    value="<?= $record['start_date'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="<?= $record['end_date'] ?>"
                    required>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea name="notes" id="notes" class="form-control" rows="3"><?= $record['notes'] ?></textarea>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <textarea name="status" id="status" class="form-control" rows="3"><?= $record['status'] ?></textarea>
            </div>
            <button type="submit" name="btn_update" class="btn btn-primary">Update Record</button>
        </form>
    </main>
</body>

</html>