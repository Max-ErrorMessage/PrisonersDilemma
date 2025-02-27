<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection settings
$host = "localhost";
$dbname = "u753770036_Accounts";
$username = "u753770036_TwokieBots";
$password = "TwokieDatabase6";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

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
        $stmt = $pdo->prepare("SELECT password, Id FROM Accounts WHERE username = :username");
        $stmt->bindParam(':username', $uname);
        $stmt->execute();
        $stmt->bind_result($storedHashedPassword, $userid);


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
