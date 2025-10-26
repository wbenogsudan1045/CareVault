<?php
session_start();
require "conn.php";

function getName()
{
    $conn = connection();
    $username = $_SESSION['username'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    if (!$stmt) {
        die("Error preparing user fetch statement: " . $conn->error);
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

    $stmt = $conn->prepare("SELECT first_name AS name FROM users WHERE id = ?");
    if (!$stmt) {
        die("Error preparing statement for name: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}
function getRecentActivity()
{
    $conn = connection();
    $username = $_SESSION['username'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    if (!$stmt) {
        die("Error preparing user fetch statement: " . $conn->error);
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

    $stmt = $conn->prepare("SELECT
                            r.diagnosis AS diagnosis, 
                            r.visit_date AS date 
                        FROM medical_records r 
                        JOIN medications m ON r.medications = m.medicine_name
                        WHERE r.user_id = ? AND m.status != 'completed'
                        ORDER BY r.visit_date DESC
                        LIMIT 5");

    if (!$stmt) {
        die("Error preparing statement for name: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareVault | Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/home.css">
</head>


<body>
    <?php include 'navbar.php'; ?>

    <main class="container hero">
        <div class="row mb-5">
            <div class="col">
                <?php
                $all_rec = getName();
                while ($rec = $all_rec->fetch_assoc()) {
                    ?>
                    <h2 class="fwlight">Welcome, <strong><?= $rec['name'] ?></strong></h2>
                    <p class="fwlight text-muted">Here’s your health snapshot today:</p>
                <?php } ?>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <a href="medical_record.php">
                    <div class="card-dashboard h-100">
                        <img src="images/image-removebg-preview.png" alt="Health Records Icon">
                        <h5 class="fw-bold">Health Records</h5>
                        <p class="text-muted">Review your past illnesses and current conditions at a glance.</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="medication.php">
                    <div class="card-dashboard h-100">
                        <img src="images/image-removebg-preview (1).png" alt="Medications Icon">
                        <h5 class="fw-bold">Medications</h5>
                        <p class="text-muted">Track your prescribed medications and intake schedule.</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="vaccination.php">
                    <div class="card-dashboard h-100">
                        <img src="images/image-removebg-preview (2).png" alt="Vaccination Icon">
                        <h5 class="fw-bold">Vaccinations</h5>
                        <p class="text-muted">View your vaccination status and upcoming booster schedules.</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="activity-log-container">
            <div class="row mb-4">
                <div class="col">
                    <h2>Recent Health Activity</h2>
                </div>
            </div>
            <table class="table table-hover align-middle border">
                <thead>
                    <tr>
                        <th>Diagnosis</th>
                        <th>Visit Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $all_rec = getRecentActivity();
                    while ($rec = $all_rec->fetch_assoc()) {
                        ?>
                        <tr>
                            <td class="description-cell"><img src="images/image-removebg-preview(3).png" alt=""
                                    style="width: 24px; height: 24px; vertical-align: middle; margin-right: 8px;">
                                <?= $rec['diagnosis'] ?></td>
                            <td class="description-cell"><?= $rec['date'] ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>