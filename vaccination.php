<?php
require 'conn.php';
session_start();

function getAllVaccinationRecords()
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
                    u.first_name AS name,
                    v.id,
                    v.vaccine_name AS vaccine, 
                    v.date_given AS date_given, 
                    v.vaccine_brand AS brand, 
                    v.dose_number AS dose_number, 
                    v.next_sched AS follow_up,
                    v.batch_code AS code,
                    v.administering_prof AS prof,
                    v.location AS location
                FROM vaccinations v JOIN users u ON u.id = v.user_id
                WHERE v.vaccine_name LIKE ? OR v.date_given LIKE ? OR v.vaccine_brand LIKE ? OR v.id LIKE ? OR v.batch_code LIKE ?
                ORDER BY v.date_given";
    } else {
        $query = "SELECT
                    u.first_name AS name,
                    v.id,
                    v.vaccine_name AS vaccine, 
                    v.date_given AS date_given, 
                    v.vaccine_brand AS brand, 
                    v.dose_number AS dose_number, 
                    v.next_sched AS follow_up,
                    v.batch_code AS code,
                    v.administering_prof AS prof,
                    v.location AS location
                FROM vaccinations v JOIN users u ON u.id = v.user_id
                WHERE user_id = ?
                AND (v.vaccine_name LIKE ? OR v.date_given LIKE ? OR v.vaccine_brand LIKE ? OR v.id LIKE ? OR v.batch_code LIKE ?)
                ORDER BY v.date_given";
    }

    $stmt = $conn->prepare($query);
    if (!$stmt)
        die("Error preparing search query: " . $conn->error);

    if ($user_role === 'Doctor') {
        $stmt->bind_param("sssss", $search, $search, $search, $search, $search);
    } else {
        $stmt->bind_param("isssss", $user_id, $search, $search, $search, $search, $search);
    }

    $stmt->execute();

    return $stmt->get_result();
}

function deleteVaccinationRecord($id)
{
    $conn = connection();
    $stmt = $conn->prepare("DELETE FROM vaccinations WHERE id = ?");
    if (!$stmt) {
        die("Error preparing delete statement: " . $conn->error);
    }
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } else {
        die("Error deleting vaccination record: " . $stmt->error);
    }
}

if (isset($_POST['btn_delete'])) {
    deleteVaccinationRecord($_POST['btn_delete']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccinations Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="css/vaccination.css">
    <style>
        .description-cell {
            max-width: 300px;
            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php' ?>

    <main class="container">
        <div class="row mb-4">
            <div class="col">
                <h2 class="fw-light">Vaccination Records</h2>
            </div>
            <div class="col text-end">
                <a href="addVaccinationRecord.php" class="btn btn-success">
                    <i class="fa-solid fa-plus-circle"></i> New Record
                </a>
            </div>
        </div>

        <form method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control"
                    placeholder="Search vaccine, date given, brand, code...."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button class="btn btn-success" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover align-middle border">
                <thead class="small table-success">
                    <tr>
                        <th>USER</th>
                        <th>VACCINE</th>
                        <th>DATE GIVEN</th>
                        <th>BRAND</th>
                        <th>DOSE NUMBER</th>
                        <th>FOLLOW UP</th>
                        <th>BATCH CODE</th>
                        <th>ADMINISTERING PERSON</th>
                        <th>LOCATION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $all_rec = getAllVaccinationRecords();
                    while ($rec = $all_rec->fetch_assoc()) {
                        ?>
                        <tr>
                            <td class="description-cell"><?= $rec['name'] ?></td>
                            <td class="description-cell"><?= $rec['vaccine'] ?></td>
                            <td class="description-cell"><?= $rec['date_given'] ?></td>
                            <td class="description-cell"><?= $rec['brand'] ?></td>
                            <td class="description-cell"><?= $rec['dose_number'] ?></td>
                            <td class="description-cell"><?= $rec['follow_up'] ?></td>
                            <td class="description-cell"><?= $rec['code'] ?></td>
                            <td class="description-cell"><?= $rec['prof'] ?></td>
                            <td class="description-cell"><?= $rec['location'] ?></td>
                            <td>
                                <?php if ($_SESSION['role'] == 'Doctor') { ?>
                                    <a href="edit-Vaccination.php?id=<?= $rec['id'] ?>"
                                        class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="btn_delete" value="<?= $rec['id'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this record?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>