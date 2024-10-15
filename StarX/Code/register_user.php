<?php

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password)) {
        echo "<script>alert('All fields are required');</script>";
        exit;
    }

    if (strlen($password) < 6) {
        echo "<script>alert('Password must be at least 6 characters long.'); window.location.href = 'register page.html';</script>";
        exit;
    }

    if ($password != $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location.href = 'register page.html';</script>";
        exit;
    }

    if (!validateEmail($email)) {
        echo "<script>alert('Invalid email format!'); window.location.href = 'register page.html';</script>";
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $servername = "localhost";
    $username = "root"; 
    $db_password = "1234"; 
    $dbname = "StarX"; 

    $conn = new mysqli($servername, $username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $check_email_query = "SELECT * FROM User WHERE email=?";
    $check_stmt = $conn->prepare($check_email_query);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.location.href = 'register page.html';</script>";
        exit;
    }

    $sql = "INSERT INTO User (user_id, email, fName, lName, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $user_id, $email, $first_name, $last_name, $hashed_password);

    if ($stmt->execute() === TRUE) {
        echo "<script>alert('User registered successfully!'); window.location.href = 'login page.html';</script>";
    } else {
        echo "<script>alert('Unable to register user. Please try again later.'); window.location.href = 'register page.html';</script>";
    }

    $stmt->close();
    $check_stmt->close();
    $conn->close();
} else {
    header("Location: register page.html");
    exit;
}
?>
