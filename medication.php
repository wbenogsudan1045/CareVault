<?php
require 'conn.php';
session_start();



function getAllMedications()
{
    $conn = connection();
    $username = $_SESSION['username'];

    $stmt = $conn->prepare("SELECT id, `user` FROM users WHERE username = ?");
    if (!$stmt)
        die("Error preparing user fetch: " . $conn->error);
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

    $updateStmt = $conn->prepare("UPDATE medications 
                                  SET status = 'completed' 
                                  WHERE status = 'ongoing' 
                                  AND end_date < CURDATE()");
    if (!$updateStmt)
        die("Error preparing update: " . $conn->error);
    $updateStmt->execute();
    $updateStmt->close();

    $search = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : "%";

    if ($user_role === 'Doctor') {
        $stmt = $conn->prepare("SELECT 
            id AS ID, 
            medicine_name AS medicine, 
            dosage, 
            start_date, 
            end_date, 
            status 
            FROM medications 
            WHERE status != 'completed' 
            AND (medicine_name LIKE ? OR dosage LIKE ? OR status LIKE ?)
            ORDER BY start_date");
        if (!$stmt)
            die("Error preparing doctor search: " . $conn->error);
        $stmt->bind_param("sss", $search, $search, $search);
    } else {
        $stmt = $conn->prepare("SELECT 
            id AS ID, 
            medicine_name AS medicine, 
            dosage, 
            start_date, 
            end_date, 
            status 
            FROM medications 
            WHERE user_id = ? 
            AND status != 'completed' 
            AND (medicine_name LIKE ? OR dosage LIKE ? OR status LIKE ?)
            ORDER BY start_date");
        if (!$stmt)
            die("Error preparing patient search: " . $conn->error);
        $stmt->bind_param("isss", $user_id, $search, $search, $search);
    }

    $stmt->execute();
    return $stmt->get_result();
}




function deleteMedication($medicine_id)
{
    $conn = connection();
    $stmt = $conn->prepare("DELETE FROM medications WHERE id = ?");
    if (!$stmt) {
        die("Error preparing delete statement: " . $conn->error);
    }
    $stmt->bind_param("i", $medicine_id);
    if ($stmt->execute()) {
        header('Location: ' . $_SERVER['PHP_SELF']); 
        exit();
    } else {
        die("Error deleting medication: " . $stmt->error);
    }
}
if (isset($_POST['btn_delete'])) {
    $deleteMedication = $_POST['btn_delete'];
    deleteMedication($deleteMedication);
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
                <h2 class="fwlight">Medications</h2>
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
                <input type="text" name="search" class="form-control" placeholder="Search medication..."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button class="btn btn-success" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>

        <table class="table table-hover align-middle border">
            <thead class="small table-success">
                <tr>
                    <th>ID</th>
                    <th>MEDICATION NAME</th>
                    <th>DOSAGE</th>
                    <th>START DATE</th>
                    <th>END DATE</th>
                    <th>STATUS</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $all_rec = getAllMedications();
                while ($rec = $all_rec->fetch_assoc()) {
                    ?>
                    <tr>
                        <td class="description-cell"><?= $rec['ID'] ?></td>
                        <td class="description-cell"><?= $rec['medicine'] ?></td>
                        <td class="description-cell"><?= $rec['dosage'] ?></td>
                        <td class="description-cell"><?= $rec['start_date'] ?></td>
                        <td class="description-cell"><?= $rec['end_date'] ?></td>
                        <td class="description-cell"><?= $rec['status'] ?></td>
                        <td>
                            <a href="edit-medication.php?id=<?= $rec['ID'] ?>" class="btn btn-outline-secondary btn-sm">
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