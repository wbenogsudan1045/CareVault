<?php
require "conn.php";

function createUser($firstname, $lastname, $email, $username, $password, $birthdate, $bloodtype, $records, $gender, $phone_no, $address, $user)
{
    $conn = connection();
    $password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (first_name, last_name, email, username, password, birth_date, blood_type, records, gender, phone_no, address, user)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssss", $firstname, $lastname, $email, $username, $password, $birthdate, $bloodtype, $records, $gender, $phone_no, $address, $user);

    if ($stmt->execute()) {
        header("location: login.php");
    } else {
        die("Error Signing up: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}

if (isset($_POST['btn_signup'])) {
    $firstname = $_POST['first_name'];
    $lastname = $_POST['last_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirm_password'];
    $birthdate = $_POST['birthdate'];
    $bloodtype = $_POST['bloodtype'];
    $records = $_POST['records'];
    $gender = $_POST['gender'];
    $phone_no = $_POST['phone_no'];
    $address = $_POST['address'];
    $user = $_POST['user'];

    if ($password === $confirmpassword) {
        createUser($firstname, $lastname, $email, $username, $password, $birthdate, $bloodtype, $records, $gender, $phone_no, $address, $user);
    } else {
        echo '<p class="alert alert-danger text-center mt-3">Password and Confirm Password do not match.</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/register.css">
</head>

<body class="bg-dark d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card signup-card shadow">
        <div class="card-header bg-success text-white text-center">
            <h4 class="mb-0">Create Your Account</h4>
        </div>
        <div class="card-body p-4">
            <form action="" method="post">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Birthdate</label>
                        <input type="date" name="birthdate" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Blood Type</label>
                        <input type="text" name="bloodtype" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select" required>
                            <option value="" disabled selected>-- Select Gender --</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone_no" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Medical Records</label>
                        <textarea name="records" rows="3" class="form-control" required></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">User</label>
                        <select name="user" class="form-select" required>
                            <option value="" disabled selected>-- Select Roles --</option>
                            <option value="Doctor">Doctor</option>
                            <option value="Patient">Patient</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                </div>
                <div class="mt-4 d-grid">
                    <button type="submit" name="btn_signup" class="btn btn-success">Sign Up</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none">Already have an account?</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>