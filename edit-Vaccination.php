<?php
require 'conn.php';
session_start();

if (!isset($_GET['id'])) {
    die("No ID provided.");
}
$record_id = $_GET['id'];

$conn = connection();
$stmt = $conn->prepare("SELECT * FROM vaccinations WHERE id = ?");
$stmt->bind_param("i", $record_id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

if (!$record) {
    die("Record not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_update'])) {

    $vaccine_name = $_POST['vaccine_name'];
    $date_given = $_POST['date_given'];
    $vaccine_brand = $_POST['vaccine_brand'];
    $dose_number = $_POST['dose_number'];
    $next_sched = $_POST['next_sched'];
    $batch_code = $_POST['batch_code'];
    $administering_prof = $_POST['administering_prof'];
    $location = $_POST['location'];


    $stmt = $conn->prepare("UPDATE vaccinations SET vaccine_name = ?, date_given = ?, vaccine_brand = ?, dose_number = ?, next_sched = ?, batch_code = ?, administering_prof = ?, location = ? WHERE id = ?");
    $stmt->bind_param("ssssssssi", $vaccine_name, $date_given, $vaccine_brand, $dose_number, $next_sched, $batch_code, $administering_prof, $location, $record_id);

    if ($stmt->execute()) {
        echo "<script>alert('Record updated successfully!'); window.location.href='vaccination.php';</script>";
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
    <title>Vaccination Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <main class="container mt-5">
        <h2>Edit Vaccination Record</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="vaccine_name" class="form-label">Vaccine Name</label>
                <input type="text" name="vaccine_name" id="vaccine_name" class="form-control"
                    value="<?= htmlspecialchars($record['vaccine_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="date_given" class="form-label">Date Given</label>
                <input type="date" name="date_given" id="date_given" class="form-control"
                    value="<?= htmlspecialchars($record['date_given']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="vaccine_brand" class="form-label">Vaccine Brand</label>
                <input type="text" name="vaccine_brand" id="vaccine_brand" class="form-control"
                    value="<?= htmlspecialchars($record['vaccine_brand']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="dose_number" class="form-label">Dose Number</label>
                <input type="number" name="dose_number" id="dose_number" class="form-control"
                    value="<?= htmlspecialchars($record['dose_number']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="next_sched" class="form-label">Next Scheduled Dose</label>
                <input type="date" name="next_sched" id="next_sched" class="form-control"
                    value="<?= htmlspecialchars($record['next_sched']) ?>">
            </div>
            <div class="mb-3">
                <label for="batch_code" class="form-label">Batch Code</label>
                <input type="text" name="batch_code" id="batch_code" class="form-control"
                    value="<?= htmlspecialchars($record['batch_code']) ?>">
            </div>
            <div class="mb-3">
                <label for="administering_prof" class="form-label">Administering Professional</label>
                <input type="text" name="administering_prof" id="administering_prof" class="form-control"
                    value="<?= htmlspecialchars($record['administering_prof']) ?>">
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" name="location" id="location" class="form-control"
                    value="<?= htmlspecialchars($record['location']) ?>">
            </div>
            <button type="submit" name="btn_update" class="btn btn-primary">Update Record</button>
        </form>
    </main>
</body>

</html>