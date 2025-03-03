<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uname = trim($_POST['uname']);
    $pword = $_POST['pword'];

    // Input validation
    if (empty($uname) || empty($pword)) {
        $_SESSION['Error'] = "All fields are required.";
        header("Location: /signin.php");
        exit();
    }

    try {
        // Retrieve the stored hashed password for the given username
        $stmt = $pdo->prepare("SELECT Password, User_ID FROM Accounts WHERE username = :username");
        $stmt->bindParam(':username', $uname);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $storedHashedPassword = $row['Password'];
            $userid = $row['User_ID'];
        }

        if (!$storedHashedPassword) {
            // Username not found
            $_SESSION['Error'] = "Username not found.";
            header("Location: /signin.php");
            exit();
        }

        // Verify the password against the stored hash
        if (!password_verify($pword, $storedHashedPassword)) {
            $_SESSION['Error'] = "Incorrect username or password.";
            header("Location: /signin.php");
            exit();
        }

        // Successful login - clear previous session and start new one
        session_unset();
        $_SESSION['Success'] = "Login successful!";
        $_SESSION['uname'] = $uname;
        $_SESSION['user_id'] = $userid;
        
        header("Location: /index.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['Error'] = "An error occurred: " . $e->getMessage();
        header("Location: /signin.php");
        exit();
    }
}
?>
