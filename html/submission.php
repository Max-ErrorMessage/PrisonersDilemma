<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'];
    $uname = $_SESSION['uname'];
    $user_id = $_SESSION['user_id'];
    $gameid = $_POST['game_id'];

    $code = nl2br(htmlspecialchars($code));
    var_dump($code);
    exit;

    $user_py_file = fopen("/var/www/Mini_Games/Prisoners_Dilemma/Code_Verification/User_Submitted_Code/user_" . $user_id . ".txt", "w");
    fwrite($user_py_file, $code);

    $output = exec("timeout 1 python3 /var/www/Mini_Games/Prisoners_Dilemma/Code_Verification/verify_submitted_code.py $user_id");

    if ($output == "1") {  // Code is fine
        $file_contents = file_get_contents('/var/www/Mini_Games/Prisoners_Dilemma/Code_Verification/User_Submitted_Code/dwarf_scores_' . $user_id . '.json');
        $dwarf_scores = json_decode($file_contents, true);

        $list = "<ol>";
        foreach ($dwarf_scores as $key => $value) {
            $list .= "<li>$key: $value</li><br>";
        }
        $list .= "</ol>";

        $_SESSION['Error3'] = $list;

        unlink("/var/www/Mini_Games/Prisoners_Dilemma/Code_Verification/User_Submitted_Code/user_" . $user_id . ".txt");
        unlink('/var/www/Mini_Games/Prisoners_Dilemma/Code_Verification/User_Submitted_Code/dwarf_scores_' . $user_id . '.json');

    } else { // Code is not fine: $output is the error provided
        if ($output == "") { // No error provided: most likely the cause of a timeout
            $output = "Your code failed to execute in the required time.";
        }
        $_SESSION['Error3'] = $output;
        unlink("/var/www/Mini_Games/Prisoners_Dilemma/Code_Verification/User_Submitted_Code/user_" . $user_id . ".txt");
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
        $stmt = $pdo->prepare("SELECT User_ID FROM Accounts WHERE Username = :username");
        $stmt->bindParam(':username', $uname);
        $stmt->execute();

        $id = $stmt->fetchColumn();

        if (!$id){
            // Username not found
            $_SESSION['Error3'] = "Username not found.";
            header("Location: /newSubmission.php");
            exit();
        }
    $stmt = $pdo->prepare("DELETE FROM Submission WHERE User_ID= :id AND Game_ID= :gameid");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':gameid', $gameid);
    $stmt->execute();
    // Insert the user into the database
    $stmt = $pdo->prepare("INSERT INTO Submission (User_ID, Game_ID, Code) VALUES (:id, :gameid, :code)");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':code', $code);
    $stmt->bindParam(':gameid', $gameid);

    try {
        $stmt->execute();
        header("Location: /newSubmission.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['Error3'] = "Error during submission: " . $e->getMessage();
        header("Location: /newSubmission.php");
        exit();
    }
}
?>
