<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include '/var/www/db.php';
    $code = $_POST['code'];
    $_SESSION['code'] = $code;
    $uname = $_SESSION['uname'];
    $user_id = $_SESSION['user_id'];
    $gameid = $_POST['game_id'];

    //$code = nl2br(htmlspecialchars($code));
    //echo "<pre>" . htmlspecialchars($code) . "</pre>";



    if ($gameid == 1){
        $game = "Prisoners_Dilemma";
        $location = "Location: /newSubmission.php";
    } else if ($gameid == 2){
        $game = "Yahtzee";
        $location = "Location: /newYahtzeeSubmission.php";
        $code2 = $_POST['code2'];
        $code = $code . "$" . $code2;
    }

    $user_py_file = fopen("/var/www/Mini_Games/" . $game . "/Code_Verification/User_Submitted_Code/user_" . $user_id . ".txt", "w");
    fwrite($user_py_file, $code);

    $output = exec("timeout 1 python3 /var/www/Mini_Games/" . $game . "/Code_Verification/verify_submitted_code.py $user_id 2>&1");

    unlink("/var/www/Mini_Games/" . $game . "/Code_Verification/User_Submitted_Code/user_" . $user_id . ".txt");




    if ($output == "1") {  // Code is fine
        $file_contents = file_get_contents('/var/www/Mini_Games/' . $game .'/Code_Verification/User_Submitted_Code/dwarf_scores_' . $user_id . '.json');
        $dwarf_scores = json_decode($file_contents, true);

        $list = "Submission Successful! This is how you performed against the 7 dwarves:<ol>";
        foreach ($dwarf_scores as $key => $value) {
            $list .= "<li>$key: $value</li><br>";
        }
        $list .= "</ol>";

        unlink('/var/www/Mini_Games/' . $game . '/Code_Verification/User_Submitted_Code/dwarf_scores_' . $user_id . '.json');

        $_SESSION['Error3'] = $list;



    } else { // Code is not fine: $output is the error provided
        if ($output == "") { // No error provided: most likely the cause of a timeout
            $output = "Your code failed to execute in the required time.";
        }
        $_SESSION['Error3'] = $output;
            header("$location");
        exit();
    }

    // Input validation
    if (empty($code)) {
        $_SESSION['Error3'] = "All fields are required.";
        header($location);
        exit();
    }

    // Retrieve the id for the given username
        $stmt = $pdo->prepare("SELECT User_ID FROM Accounts WHERE Username = :username");
        $stmt->bindParam(':username', $uname);
        $stmt->execute();

        $id = $stmt->fetchColumn();

        if (!$id){
            // Username not found
            $_SESSION['Error3'] = "Username not found.";
            header($location);
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

            if ($gameid == 2){ //if gameid is 2 (Yahtzee data will be ready to be put into the database after submission)

                $json_file = '/var/www/Mini_Games/Yahtzee/Computer_Generated_Files/scores.json';
                if (!file_exists($json_file)) {
                    die("Error: scores.json file not found.");
                }

                $json_data = file_get_contents($json_file);
                $scores = json_decode($json_data, true);

                if (!is_array($scores)) {
                    die("Error: Invalid JSON format.");
                }

                $score = $scores[$user_id];

                $stmt = $pdo->prepare("UPDATE Submission SET Points = :score WHERE User_ID = :user_id AND Game_ID = 2");

                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':score', $score, PDO::PARAM_STR);

                $stmt->execute();


                unlink('/var/www/Mini_Games/Yahtzee/Computer_Generated_Files/user_codes.json');
                // unlink('/var/www/Mini_Games/Prisoners_Dilemma/Computer_Generated_Files/user_codes.py');
                unlink($json_file);

                echo "Scores successfully updated.\nGame length:" . $game_length ."\n";

            }
        header($location);
        exit();
    } catch (Exception $e) {
        $_SESSION['Error3'] = "Error during submission: " . $e->getMessage();
        header($location);
        exit();
    }

} else {
    die("Access Denied");
}
?>
