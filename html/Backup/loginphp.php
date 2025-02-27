<?php
$servername = "127.0.0.1:3306";
$username = "u753770036_DougSantry";
$password = "demorgansL4W?";
$dbname = "u753770036_Chess";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = $_POST["uname"];
    $pword = $_POST["pword"];

    echo "Name: " . $uname . "<br>";
    echo "Password: " . $pword . "<br>";

    if (empty($uname) || empty($pword)) {
        session_start();
        $_SESSION['Error'] = "All fields are required!";
        header("Location: login.php");
        exit;
    }else {
        $hashedPassword = password_hash($pword,PASSWORD_BCRYPT);

        $statement = $conn->prepare("SELECT * FROM Players Where Username = ?");
        $statement->bind_param("s", $uname);

        if ($statement->execute()) {
            $result = $statement->get_result();
            if ($result->num_rows > 0){
                while ($row = $result->fetch_assoc()) {
                    if (password_verify($pword, $row["PasswordHash"])){
                        echo "we're in boys";
                        session_start();
                        session_unset();
                        $_SESSION['PlayerID'] = $row["PlayerID"];
                        $_SESSION['Username'] = $row["Username"];
                        header("Location: main.php");
                        exit;
                    } else {
                        session_start();
                        $_SESSION['Error'] = "Incorrect Password";
                        header("Location: login.php");
                        exit;
                    }
                }
            } else {
                
                session_start();
                $_SESSION['Error'] = "No account with that username exists";
                header("Location: login.php");
                exit;
            }
        } else {
            session_start();
            $_SESSION['Error'] = "Error: " . $statement->error . ", Please dont do that :)";
            header("Location: login.php");
            exit;
        }
    }
}
?>

