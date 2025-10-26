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

    $stmt = $conn->prepare("SELECT first_name AS f_name, last_name AS l_name FROM users WHERE id = ?");
    if (!$stmt) {
        die("Error preparing statement for name: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}
function getBasicInfo()
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
    first_name AS f_name,
    birth_date AS birth_date,
    last_name AS l_name,
    gender AS gender,
    phone_no AS phone,
    email AS email,
    address AS address 
    FROM users WHERE id = ?");

    if (!$stmt) {
        die("Error preparing statement for name: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

function getMedicalId()
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
    id AS ID, user as user FROM users WHERE id = ?");

    if (!$stmt) {
        die("Error preparing statement for name: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

function getHealthDetails()
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
    blood_type AS blood_type,
    records AS records
    FROM users WHERE id = ?");

    if (!$stmt) {
        die("Error preparing statement for name: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareVault | Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/profile.css">
</head>


<body>
    <?php include 'navbar.php'; ?>

    <main class="container hero">
        <div class="text-center mb-4">
            <img src="images/profile-icon-design-free-vector.jpg" class="profile-img" alt="">
            <?php $all_rec = getName();
            while ($rec = $all_rec->fetch_assoc()) { ?>
                <h2 class="fw-bold"><?= $rec['f_name'] ?>     <?= $rec['l_name'] ?></h2>
                <a href="edit-Profile.php" class="btn btn-sm btn-outline-primary edit-link">Edit Profile</a>
            <?php } ?>
        </div>

        <!-- Basic Information -->
        <div class="section-box">
            <h4><i class="bi bi-person-fill me-2"></i>Basic Information</h4>
            <div class="row">
                <?php $basicInfo = getBasicInfo();
                while ($info = $basicInfo->fetch_assoc()) { ?>
                    <div class="col-md-6">
                        <p class="mb-1 info-label">Full Name</p>
                        <p class="info-value"><?= $info['f_name'] ?>     <?= $info['l_name'] ?></p>

                        <p class="mb-1 info-label">Date of Birth</p>
                        <p class="info-value"><?= $info['birth_date'] ?></p>

                        <p class="mb-1 info-label">Gender</p>
                        <p class="info-value"><?= $info['gender'] ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 info-label">Phone</p>
                        <p class="info-value"><?= $info['phone'] ?></p>

                        <p class="mb-1 info-label">Email</p>
                        <p class="info-value"><?= $info['email'] ?></p>

                        <p class="mb-1 info-label">Address</p>
                        <p class="info-value"><?= $info['address'] ?></p>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Medical ID -->
        <div class="section-box">
            <h4><i class="bi bi-hospital me-2"></i>Medical ID</h4>
            <div>
                <?php $medicalId = getMedicalId();
                while ($mid = $medicalId->fetch_assoc()) { ?>
                    <p class="mb-1 info-label">CareVault ID</p>
                    <p class="info-value"><?= $mid['ID'] ?></p>
                    <p class="mb-1 info-label">Role</p>
                    <p class="info-value"><?= str_pad($mid['user'], 6, '0', STR_PAD_LEFT) ?></p>
                <?php } ?>
            </div>
        </div>

        <!-- Health Details -->
        <div class="section-box">
            <h4><i class="bi bi-heart-pulse me-2"></i>Health Details</h4>
            <div class="row">
                <?php $healthDetails = getHealthDetails();
                while ($health = $healthDetails->fetch_assoc()) { ?>
                    <div class="col-md-6">
                        <p class="mb-1 info-label">Blood Type</p>
                        <p class="info-value"><?= $health['blood_type'] ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 info-label">Medical Records</p>
                        <p class="info-value"><?= $health['records'] ?></p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</body>

</html>