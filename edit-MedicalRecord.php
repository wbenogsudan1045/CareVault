<?php
require 'conn.php';
session_start();

if (!isset($_GET['id'])) {
    die("No ID provided.");
}
$record_id = $_GET['id'];

$conn = connection();
$stmt = $conn->prepare("SELECT * FROM medical_records WHERE id = ?");
$stmt->bind_param("i", $record_id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

if (!$record) {
    die("Record not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_update'])) {
    $visit_date = $_POST['visit_date'];
    $doctor_name = $_POST['doctor_name'];
    $diagnosis = $_POST['diagnosis'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("UPDATE medical_records SET visit_date = ?, doctor_name = ?, diagnosis = ?, notes = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $visit_date, $doctor_name, $diagnosis, $notes, $record_id);

    if ($stmt->execute()) {
        echo "<script>alert('Record updated successfully!'); window.location.href='medical_record.php';</script>";
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
    <title>Edit Medical Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <main class="container mt-5">
        <h2>Edit Medical Record</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="visit_date" class="form-label">Visit Date</label>
                <input type="date" name="visit_date" id="visit_date" class="form-control"
                    value="<?= $record['visit_date'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="doctor_name" class="form-label">Doctor's Name</label>
                <input type="text" name="doctor_name" id="doctor_name" class="form-control"
                    value="<?= $record['doctor_name'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="diagnosis" class="form-label">Diagnosis</label>
                <input type="text" name="diagnosis" id="diagnosis" class="form-control"
                    value="<?= $record['diagnosis'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea name="notes" id="notes" class="form-control" rows="3"><?= $record['notes'] ?></textarea>
            </div>
            <button type="submit" name="btn_update" class="btn btn-primary">Update Record</button>
        </form>
    </main>
</body>

</html>