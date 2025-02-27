<?php
include db.php
$user_id = $_SESSION['user_id'];
$code = $_POST['code'];
//
// $user_id = '7';
//
// $user_py_file = fopen("/var/www/Mini_Games/Prisoners_Dilemma/Code_Verification/User_Submitted_Code/user_" . $user_id . ".py", "w");
// fwrite($user_py_file, $code);
//
// echo "File Loaded.</p>";
//
// $testing_output = shell_exec("env 2>&1");
// echo "Python3 Path: " . $testing_output . "</p>";
//
// $output = exec("python3 /var/www/public_html/Testing/hello_world.py 2>&1");
//
// echo "Output of hello world: " . $output;
//
// exit();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'];
    $uname = $_SESSION['uname'];
    $gameid = $_POST['game_id'];
    $_SESSION['Error3'] = $gameid;

    // Input validation
    if (empty($code)) {
        $_SESSION['Error3'] = "All fields are required.";
        header("Location: /newSubmission.php");
        exit();
    }
    if ($gameid == 2){
        $code2 = $_POST['code2'];
        $_SESSION['Error3'] = "yippee";
        
        header("Location: /newYahtzeeSubmission.php");
        // Input validation
        if (empty($code2)) {
            $_SESSION['Error3'] = "All fields are required.";
            header("Location: /newYahtzeeSubmission.php");
            exit();
        }
        
        $code = $code . "$" . $code2;
    }

    // Retrieve the id for the given username
        $stmt = $pdo->prepare("SELECT id FROM Accounts WHERE username = :username");
        $stmt->bindParam(':username', $uname);
        $stmt->execute();

        $id = $stmt->fetchColumn();

        if (!$id){
            // Username not found
            $_SESSION['Error3'] = "Username not found.";
            header("Location: /newSubmission.php");
            exit();
        }
    $stmt = $pdo->prepare("DELETE FROM Submission WHERE UserID= :id AND GameID= :gameid");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':gameid', $gameid);
    $stmt->execute();
    // Insert the user into the database
    $stmt = $pdo->prepare("INSERT INTO Submission (UserID, GameID, code) VALUES (:id, :gameid, :code)");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':code', $code);
    $stmt->bindParam(':gameid', $gameid);

    try {
        $stmt->execute();
        #$_SESSION['Error3'] = "Code submitted!";
        header("Location: /newSubmission.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['Error3'] = "Error during submission: " . $e->getMessage();
        header("Location: /newSubmission.php");
        exit();
    }
}
?>
