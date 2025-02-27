<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'];
    $uname = $_SESSION['uname'];
    $user_id = $_SESSION['user_id'];
    $gameid = $_POST['game_id'];

    $user_py_file = fopen("/var/www/Mini_Games/Prisoners_Dilemma/Code_Verification/User_Submitted_Code/user_" . $user_id . ".txt", "w");
    fwrite($user_py_file, $code);

    $output = exec("timeout 1 /var/www/Mini_Games/Prisoners_Dilemma/Code_Verification/verify_submitted_code.py $user_id");

    if ($output == "1") {  // Code is fine
        $file_contents = file_get_contents('/var/www/Mini_Games/Prisoners_Dilemma/Code_Verification/User_Submitted_Code/dwarf_scores_' . $user_id . '.json');
        $dwarf_scores = json_decode($file_contents, true);
        $_SESSION['Error3'] = $dwarf_scores;
        vardump($dwarf_scores);
    } else { // Code is not fine: $output is the error provided
        $_SESSION['Error3'] = $output;
        header("Location: /newSubmission.php");
        exit();
    }

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
        echo "DEBUG POINT #1"; // TODO: remove
        $_SESSION['Error3'] = "Code submitted!";
        header("Location: /newSubmission.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['Error3'] = "Error during submission: " . $e->getMessage();
        header("Location: /newSubmission.php");
        exit();
    }
}
?>
