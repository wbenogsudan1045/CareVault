<?php
require 'conn.php';
session_start();



function getAllMedicalRecords()
{
    $conn = connection();
    $username = $_SESSION['username'];

    $stmt = $conn->prepare("SELECT id, `user` FROM users WHERE username = ?");
    if (!$stmt)
        die("Error preparing statement: " . $conn->error);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $user_id = $row['id'];
        $user_role = $row['user'];
    } else {
        die("User not found.");
    }
    $stmt->close();

    $search = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : "%";

    if ($user_role === 'Doctor') {
        $query = "SELECT
                    r.id AS ID, 
                    r.diagnosis AS Diagnosis, 
                    r.notes AS Description, 
                    r.doctor_name AS Doctor, 
                    r.visit_date AS Date 
                FROM medical_records r 
                JOIN medications m ON r.medications = m.medicine_name
                WHERE m.status != 'completed'
                  AND (r.diagnosis LIKE ? OR r.notes LIKE ? OR r.doctor_name LIKE ? OR r.id LIKE ?)
                ORDER BY r.visit_date";

        $stmt = $conn->prepare($query);
        if (!$stmt)
            die("Error preparing doctor query: " . $conn->error);
        $stmt->bind_param("ssss", $search, $search, $search, $search);
    } else {
        $query = "SELECT
                    r.id AS ID, 
                    r.diagnosis AS Diagnosis, 
                    r.notes AS Description, 
                    r.doctor_name AS Doctor, 
                    r.visit_date AS Date 
                FROM medical_records r 
                JOIN medications m ON r.medications = m.medicine_name
                WHERE r.user_id = ? 
                  AND m.status != 'completed'
                  AND (r.diagnosis LIKE ? OR r.notes LIKE ? OR r.doctor_name LIKE ? OR r.id LIKE ?)
                ORDER BY r.visit_date";

        $stmt = $conn->prepare($query);
        if (!$stmt)
            die("Error preparing patient query: " . $conn->error);
        $stmt->bind_param("issss", $user_id, $search, $search, $search, $search);
    }

    $stmt->execute();
    return $stmt->get_result();
}


function deleteMedicalRecord($medical_record)
{
    $conn = connection();
    $stmt = $conn->prepare("DELETE FROM medical_records WHERE id = ?");
    if (!$stmt) {
        die("Error preparing delete statement: " . $conn->error);
    }
    $stmt->bind_param("i", $medical_record);
    if ($stmt->execute()) {
        header('Location: ' . $_SERVER['PHP_SELF']); 
        exit();
    } else {
        die("Error deleting Medical Record: " . $stmt->error);
    }
}
if (isset($_POST['btn_delete'])) {
    $deleteMedicalRecord = $_POST['btn_delete'];
    deleteMedicalRecord($deleteMedicalRecord);
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
    <?php include 'navbar.php' ?>

    <main class="container">
        <div class="row mb-4">
            <div class="col">
                <h2 class="fwlight">Medical Records</h2>
            </div>
            <div class="col text-end">
                <a href="addMedicalRecord.php" class="btn btn-success">
                    <i class="fa-solid fa-plus-circle"></i> New Record
                </a>
            </div>
        </div>
        </div>
        </div>

        <style>
            .description-cell {
                max-width: 300px;
                white-space: normal;
                word-wrap: break-word;
                overflow-wrap: break-word;
            }
        </style>

        <form method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control"
                    placeholder="Search diagnosis, doctor, or notes..."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button class="btn btn-success" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <table class="table table-hover align-middle border">
            <thead class="small table-success">
                <tr>
                    <th>ID</th>
                    <th>DIAGNOSIS</th>
                    <th>DOCTOR</th>
                    <th>VISIT DATE</th>
                    <th>DOCTOR'S NOTE</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $all_rec = getAllMedicalRecords();
                while ($rec = $all_rec->fetch_assoc()) {
                    ?>
                    <tr>
                        <td class="description-cell"><?= $rec['ID'] ?></td>
                        <td class="description-cell"><?= $rec['Diagnosis'] ?></td>
                        <td class="description-cell"><?= $rec['Doctor'] ?></td>
                        <td class="description-cell"><?= $rec['Date'] ?></td>
                        <td class="description-cell"><?= $rec['Description'] ?></td>
                        <td>
                            <a href="edit-MedicalRecord.php?id=<?= $rec['ID'] ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="btn_delete" value="<?= $rec['ID'] ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this record?');">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </main>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</html>