<?php

/**
 * Manages new users signing up
 * Authorises or denies the signup based on username and password requirements.
 * If the attempt is authorised, adds the new user to the Accounts table.
 *
 * Author: James Aris
 */

session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uname = trim($_POST['uname']);
    $pword = $_POST['pword'];
    $pword2 = $_POST['pword2'];

    // Input validation
    if (empty($uname) || empty($pword) || empty($pword2)) {
        $_SESSION['Error2'] = "All fields are required.";
        header("Location: /signin.php");
        exit();
    }

    if ($pword !== $pword2) {
        $_SESSION['Error2'] = "Passwords do not match.";
        header("Location: /signin.php");
        exit();
    }

    if (!preg_match('/^[a-zA-Z0-9 ]{3,20}$/', $uname)){
        $_SESSION['Error2'] = "Username must be between 3 and 20 characters long and contain no special characters.";
        header("Location: /signin.php");
        exit();
    }

    // Check if username already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Accounts WHERE username = :username");
    $stmt->bindParam(':username', $uname);
    $stmt->execute();

    if ($stmt->fetchColumn() > 0) {
        $_SESSION['Error2'] = "Username is already taken.";
        header("Location: /signin.php");
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($pword, PASSWORD_DEFAULT);

    // Insert the user into the database
    $stmt = $pdo->prepare("INSERT INTO Accounts (username, password) VALUES (:username, :password)");
    $stmt->bindParam(':username', $uname);
    $stmt->bindParam(':password', $hashedPassword);

    try {
        $stmt->execute();
        $_SESSION['Error2'] = "Signup successful!";
        #header("Location: /signin.php");
    } catch (Exception $e) {
        $_SESSION['Error2'] = "Error during signup: " . $e->getMessage();
        header("Location: /signin.php");
    }

    // Insert the user into the medals table
    $stmt = $pdo->prepare("INSERT INTO Medals (User_ID)
    SELECT User_ID FROM Accounts WHERE Username = :username");
    $stmt->bindParam(':username', $uname);

    try {
        $stmt->execute();
        $_SESSION['Error2'] = "Signup successful!";
        header("Location: /signin.php");
    } catch (Exception $e) {
        $_SESSION['Error2'] = "Major Error during signup: " . $e->getMessage();
        header("Location: /signin.php");
    }
}
?>
