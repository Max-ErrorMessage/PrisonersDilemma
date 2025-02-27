<?php
$servername = "127.0.0.1:3306";
$username = "u753770036_DougSantry";
$password = "demorgansL4W?";
$dbname = "u753770036_Chess";
session_start();

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = $_POST["uname"];
    $email = $_POST["email"];
    $pword = $_POST["pword"];
    $pword2 = $_POST["pword2"];

    if (empty($uname) || empty($email) || empty($pword)) {
        $_SESSION['Error2'] = "All fields are required";
        header("Location: login.php");
        exit;
    }else if ($pword != $pword2){
        $_SESSION['Error2'] = "Passwords do not match";
        header("Location: login.php");
        exit;
    } else {
        $statement = $conn->prepare("SELECT * FROM Players Where Username = ?");
        $statement->bind_param("s", $uname);
        if ($statement->execute()) {
            $result = $statement->get_result();
            if ($result->num_rows > 0){
                $_SESSION['Error2'] = "There already exists an account with that name";
                header("Location: login.php");
                exit;
            } else {

                $hashedPassword = password_hash($pword,PASSWORD_BCRYPT);

                $statement = $conn->prepare("INSERT INTO Players (Username, PasswordHash, email, ELO) VALUES (?,?,?,1000)");
                $statement->bind_param("sss", $uname, $hashedPassword, $email);

                if ($statement->execute()) {
                    $statement = $conn->prepare("SELECT * FROM Players Where Username = ?");
                    $statement->bind_param("s", $uname);
                    if ($statement->execute()) {
                        $result = $statement->get_result();
                        while ($row = $result->fetch_assoc()) {
                            echo "we're in boys";
                            session_unset();
                            $_SESSION['PlayerID'] = $row["PlayerID"];
                            $_SESSION['Username'] = $row["Username"];
                            header("Location: main.php");
                            exit;
                        }
                    }
                } else {
                    $_SESSION['Error2'] = "Error: " . $statement->error . "! Dont do that :)";
                    header("Location: login.php");
                    exit;
                }

            }
        }
    }
}
?>