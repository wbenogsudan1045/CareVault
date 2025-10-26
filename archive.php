<?php
require 'conn.php';
session_start();

function getAllArchived()
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

    $search = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : "%";

    if ($user_role === 'Doctor') {
        $query = "SELECT
                        m.id AS ID, 
                        r.doctor_name AS doctor, 
                        r.diagnosis AS Diagnosis, 
                        m.medicine_name AS medicine,
                        m.dosage AS dosage,
                        r.visit_date AS visit_date,
                        m.start_date AS start_date,
                        m.end_date AS end_date,
                        r.notes AS doctors_note, 
                        m.status AS status
                    FROM medical_records r
                    JOIN medications m ON r.medications = m.medicine_name
                    WHERE (r.doctor_name LIKE ?
                       OR r.diagnosis LIKE ?
                       OR m.medicine_name LIKE ?
                       OR m.id LIKE ?
                       OR m.status LIKE ?)
                    ORDER BY r.visit_date";

        $stmt = $conn->prepare($query);
        if (!$stmt)
            die("Error preparing doctor query: " . $conn->error);
        $stmt->bind_param("sssss", $search, $search, $search, $search, $search);
    } else {
        $query = "SELECT
                        m.id AS ID, 
                        r.doctor_name AS doctor, 
                        r.diagnosis AS Diagnosis, 
                        m.medicine_name AS medicine,
                        m.dosage AS dosage,
                        r.visit_date AS visit_date,
                        m.start_date AS start_date,
                        m.end_date AS end_date,
                        r.notes AS doctors_note, 
                        m.status AS status
                    FROM medical_records r
                    JOIN medications m ON r.medications = m.medicine_name
                    WHERE m.user_id = ?
                      AND (r.doctor_name LIKE ?
                       OR r.diagnosis LIKE ?
                       OR m.medicine_name LIKE ?
                       OR m.id LIKE ?
                       OR m.status LIKE ?)
                    ORDER BY r.visit_date";

        $stmt = $conn->prepare($query);
        if (!$stmt)
            die("Error preparing patient query: " . $conn->error);
        $stmt->bind_param("isssss", $user_id, $search, $search, $search, $search, $search);
    }

    $stmt->execute();
    return $stmt->get_result();
}

function deleteArchive($medicine_id)
{
    $conn = connection();

    $stmt = $conn->prepare("SELECT medicine_name FROM medications WHERE id = ?");
    if (!$stmt) {
        die("Error preparing select statement: " . $conn->error);
    }

    $stmt->bind_param("i", $medicine_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $medicine_name = $row['medicine_name'];
    } else {
        die("No medication found with the given ID.");
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM medical_records WHERE medications = ?");
    if (!$stmt) {
        die("Error preparing delete statement for medical_records: " . $conn->error);
    }

    $stmt->bind_param("s", $medicine_name);
    if (!$stmt->execute()) {
        die("Error deleting from medical_records: " . $stmt->error);
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM medications WHERE id = ?");
    if (!$stmt) {
        die("Error preparing delete statement for medications: " . $conn->error);
    }

    $stmt->bind_param("i", $medicine_id);
    if ($stmt->execute()) {
        header('Location: ' . $_SERVER['PHP_SELF']); // refresh the page after deletion
        exit();
    } else {
        die("Error deleting from medications: " . $stmt->error);
    }
}



if (isset($_POST['btn_delete'])) {
    $deleteID = $_POST['btn_delete'];
    deleteArchive($deleteID);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archives</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="css/archives.css">
</head>

<body>
    <?php include 'navbar.php' ?>

    <main class="container">
        <div class="row mb-4">
            <div class="col">
                <h2 class="fwlight">Archives</h2>
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

        <!-- 🔍 Search Form -->
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control"
                    placeholder="Search ID, doctor name, diagnosis, medicine, status..."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button class="btn btn-success" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle border">
                <thead class="small table-success">
                    <tr>
                        <th>ID</th>
                        <th>DOCTOR</th>
                        <th>DAIGNOSIS</th>
                        <th>MEDICATION NAME</th>
                        <th>DOSAGE</th>
                        <th>VISIT DATE</th>
                        <th>START DATE</th>
                        <th>END DATE</th>
                        <th>DOCTOR'S NOTE</th>
                        <th>STATUS</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $all_rec = getAllArchived();
                    while ($rec = $all_rec->fetch_assoc()) {
                        ?>
                        <tr>
                            <td class="description-cell"><?= $rec['ID'] ?></td>
                            <td class="description-cell"><?= $rec['doctor'] ?></td>
                            <td class="description-cell"><?= $rec['Diagnosis'] ?></td>
                            <td class="description-cell"><?= $rec['medicine'] ?></td>
                            <td class="description-cell"><?= $rec['dosage'] ?></td>
                            <td class="description-cell"><?= $rec['visit_date'] ?></td>
                            <td class="description-cell"><?= $rec['start_date'] ?></td>
                            <td class="description-cell"><?= $rec['end_date'] ?></td>
                            <td class="description-cell"><?= $rec['doctors_note'] ?></td>
                            <td class="description-cell"><?= $rec['status'] ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="edit-Archive.php?id=<?= $rec['ID'] ?>"
                                        class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <form method="POST" style="margin: 0;">
                                        <input type="hidden" name="btn_delete" value="<?= $rec['ID'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this record?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</html>