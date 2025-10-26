<?php
require 'conn.php';
session_start();

if (!isset($_SESSION['username'])) {
    die("You must be logged in to access this page.");
}

$conn = connection();
$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_update'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $birth_date = $_POST['birth_date'];
    $blood_type = $_POST['blood_type'];
    $gender = $_POST['gender'];
    $phone_no = $_POST['phone_no'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $records = $_POST['records'];
    $user = $_POST['user'];

    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, birth_date = ?, blood_type = ?, gender = ?, phone_no = ?, email = ?, address = ?, records = ?, user = ? WHERE username = ?");
    $stmt->bind_param("sssssssssss", $first_name, $last_name, $birth_date, $blood_type, $gender, $phone_no, $email, $address, $records, $user, $username);

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
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
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <main class="container mt-5">
        <h2>Edit Profile</h2>
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="form-control"
                        value="<?= $user['first_name'] ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="form-control"
                        value="<?= $user['last_name'] ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="birth_date" class="form-label">Birth Date</label>
                <input type="date" name="birth_date" id="birth_date" class="form-control"
                    value="<?= $user['birth_date'] ?>" required>
            </div>

            <div class="mb-3">
                <label for="blood_type" class="form-label">Blood Type</label>
                <input type="text" name="blood_type" id="blood_type" class="form-control"
                    value="<?= $user['blood_type'] ?>" required>
            </div>

            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select name="gender" id="gender" class="form-control" required>
                    <option value="Male" <?= $user['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $user['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= $user['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="phone_no" class="form-label">Phone</label>
                <input type="text" name="phone_no" id="phone_no" class="form-control" value="<?= $user['phone_no'] ?>"
                    required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= $user['email'] ?>" required>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea name="address" id="address" class="form-control" rows="3"
                    required><?= $user['address'] ?></textarea>
            </div>

            <div class="mb-3">
                <label for="records" class="form-label">Medical Records</label>
                <textarea name="records" id="records" class="form-control" rows="3"
                    required><?= $user['records'] ?></textarea>
            </div>
            <div class="mb-3">
                <label for="user" class="form-label">User</label>
                <select name="user" id="user" class="form-control" required>
                    <option value="Doctor" <?= $user['gender'] == 'Male' ? 'selected' : '' ?>>Doctor</option>
                    <option value="Patient" <?= $user['gender'] == 'Female' ? 'selected' : '' ?>>Patient</option>
                    <option value="Other" <?= $user['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>

            <button type="submit" name="btn_update" class="btn btn-primary">Update Profile</button>
        </form>
    </main>
</body>

</html>