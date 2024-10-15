<?php
session_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    $servername = "localhost";
    $username_db = "root";
    $password_db = "1234"; 
    $dbname = "StarX"; 

    $conn = new mysqli($servername, $username_db, $password_db, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM User WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_email'] = $email; 
            $_SESSION['user_id'] = $row['user_id']; 
            header("Location: HomePage.php");
            exit;
        } else {
            echo "<script>alert('Incorrect password!')</script>";
            echo "<script>window.location.href = 'login page.html';</script>"; 
            exit;
        }
    } else {
        echo "<script>alert('Unvalid Account!')</script>";
        echo "<script>window.location.href = 'login page.html';</script>"; 
        exit;
    }

    $stmt->close();
    $conn->close();
}
?>
