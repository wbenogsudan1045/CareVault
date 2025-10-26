<?php
require 'conn.php';
session_start();

function createVaccinationRecords($vaccine_name, $date_given, $vaccine_brand, $dose_number, $next_sched, $batch_code, $administering_prof, $location)
{
    $conn = connection();

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Ensure the user is logged in
    if (!isset($_SESSION['username'])) {
        die("User not logged in.");
    }

    $username = $_SESSION['username'];

    // Fetch user ID based on username
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
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

    // Insert medical record
    $sql_medical_record = "INSERT INTO vaccinations (user_id, vaccine_name, date_given, vaccine_brand, dose_number, next_sched, batch_code, administering_prof, location) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_medical_record);
    if (!$stmt) {
        die("Error preparing medical record statement: " . $conn->error);
    }

    $stmt->bind_param(
        "issssssss",
        $user_id,
        $vaccine_name,
        $date_given,
        $vaccine_brand,
        $dose_number,
        $next_sched,
        $batch_code,
        $administering_prof,
        $location
    );
    if (!$stmt->execute()) {
        die("Error adding vaccination record: " . $stmt->error);
    }
    $stmt->close();
    $conn->close();

    echo "<script>alert('vaccination added successfully!'); window.location.href='vaccination.php';</script>";
}

// Form handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_add'])) {
    // Retrieve form data safely
    $vaccine_name = $_POST['vaccine_name'] ?? '';
    $date_given = $_POST['date_given'] ?? '';
    $vaccine_brand = $_POST['vaccine_brand'] ?? '';
    $dose_number = $_POST['dose_number'] ?? '';
    $next_sched = $_POST['next_sched'] ?? '';
    $batch_code = $_POST['batch_code'] ?? '';
    $administering_prof = $_POST['administering_prof'] ?? '';
    $location = $_POST['location'] ?? '';

    // Call the function to insert data
    createVaccinationRecords($vaccine_name, $date_given, $vaccine_brand, $dose_number, $next_sched, $batch_code, $administering_prof, $location);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccination Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <main class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <h2 class="fw-light mb-4 text-center">Add Vaccination Record</h2>
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="vaccine_name" class="form-label">Vaccine Name</label>
                        <input type="text" name="vaccine_name" id="vaccine_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="date_given" class="form-label">Date Given</label>
                        <input type="date" name="date_given" id="date_given" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="vaccine_brand" class="form-label">Vaccine Brand</label>
                        <input type="text" name="vaccine_brand" id="vaccine_brand" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="dose_number" class="form-label">Dose Number</label>
                        <input type="text" name="dose_number" id="dose_number" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="next_sched" class="form-label">Next Scheduled Date</label>
                        <input type="date" name="next_sched" id="next_sched" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="batch_code" class="form-label">Batch Code</label>
                        <input type="text" name="batch_code" id="batch_code" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="administering_prof" class="form-label">Administering Professional</label>
                        <input type="text" name="administering_prof" id="administering_prof" class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" name="location" id="location" class="form-control" required>
                    </div>

                    <button type="submit" name="btn_add" class="btn btn-success w-100">
                        <i class="fa-solid fa-plus"></i> Add Vaccination Record
                    </button>
                </form>
            </div>
        </div>
    </main>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>