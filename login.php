<?php

require "conn.php";

function login($username, $password)
{
    $conn = connection();

    $sql = "SELECT id, username, password, first_name, last_name, `user` FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {
                session_start();
                $_SESSION["id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["full_name"] = $user["first_name"] . " " . $user["last_name"];
                $_SESSION["role"] = $user["user"];

                if ($user["user"] === "Doctor") {
                    header("Location: dr-tab-home.php");
                } elseif ($user["user"] === "Patient") {
                    header("Location: index.php");
                } else {
                    header("Location: index.php");
                }

                exit;
            } else {
                echo '<div class="alert alert-danger">Incorrect password.</div>';
            }
        } else {
            echo '<div class="alert alert-danger">Username not found.</div>';
        }
    } else {
        die("Error retrieving the user: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}

if (isset($_POST["btn_login"])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    login($username, $password);
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Log in</title>
</head>

<body class="bg-dark">
    <div style="height: 100vh;">
        <div class="row h-100 m-0">
            <div class="w-25 mx-auto my-auto" id="card">
                <div id="carevault-container">
                    <h2 class="text-center mb-0">Care Vault</h2>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <div class="mb-5"><label for="username" class="form-label small fw-bold">Username</label><input
                                type="text" name="username" id="username" maxlenght="15" required
                                class="form-control fw-bold">
                        </div>
                        <div class="mb-5"><label for="password" class="form-label small fw-bold">Password</label>
                            <input type="password" name="password" id="password" class="form-control mb-2" required>
                        </div>
                        <button type="submit" name="btn_login" class="btn w-100" id="login_btn">Log-In</button>
                    </form>
                    <p><a class="register" href="register.php">Register?</a></p>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>