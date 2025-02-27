<?php
session_start();

if(isset($_GET['logout'])){    
	
	//Simple exit message 
    $logout_message = "<div class='msgln'><span class='left-info'>User <b class='user-name-left'>". $_SESSION['name'] ."</b> has left the chat session.</span><br></div>";
    file_put_contents("log.html", $logout_message, FILE_APPEND | LOCK_EX);
	
	session_destroy();
	header("Location: index.php"); //Redirect the user 
}
if(isset($_POST['enter'])){
    if($_POST['name'] != ""){
        $_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));
    }
    else{
        echo '<span class="error">Please type in a name</span>';
    }
}
function loginForm(){
    echo 
    '<div id="loginform"> 
<p>Please enter your name to continue!</p> 
<form action="index.php" method="post"> 
<input type="text" name="name" id="name" /> 
<br>
<input type="submit" name="enter" id="enter" value="Enter" /> 
</form> 
</div>';
}
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="style3.css">
        <title>Chess+</title>
        <link rel="shortcut icon" href = "blueRook.png">
    </head>
    <body>
        <div id="main">
            <?php
            if(!isset($_SESSION['name'])){
                loginForm();
            }
            else {
            ?>
            <div id = "board"></div>
            <div id = "buttons">
                <img src = "blueButton.png" onclick = "blue()">
                <img src = "redButton.png" onclick = "red()">
            </div>
            <div id = "sideboard">
                <div id = "turns"></div>
                <div id = chat>
                    <div id="chatbox">
                    <?php
                    if(file_exists("log.html") && filesize("log.html") > 0){
                        $contents = file_get_contents("log.html");          
                        echo $contents;
                    }
                    ?>
                    </div>
                    <form name="message" action="", id = "messageBox">
                        <input name="usermsg" type="text" id="usermsg" />
                        <input name="submitmsg" type="submit" id="submitmsg" value="Send" />
                    </form>
                </div>

            </div>
        </div>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script type="text/javascript">
            // jQuery Document 
            $(document).ready(function () {});
        </script>
        <script src="code3.js" defer></script>
        <script src="networking.js" defer></script>
        <script src="evaluate.js" defer></script>
        <script src="arrow.js" defer></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script type="text/javascript">
            // jQuery Document 
            $(document).ready(function () {
                var newscrollHeight = $("#chatbox")[0].scrollHeight - 20; //Scroll height after the request 
                $("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div 
                $("#submitmsg").click(function () {
                    var clientmsg = $("#usermsg").val();
                    if (clientmsg == "/reset"){
                        recreateBoard()
                    }
                    $.post("post.php", { text: clientmsg });
                    $("#usermsg").val("");
                    return false;
                });
                function loadLog() {
                    loadBoard()
                    var oldscrollHeight = $("#chatbox")[0].scrollHeight - 20; //Scroll height before the request 
                    $.ajax({
                        url: "log.html",
                        cache: false,
                        success: function (html) {
                            $("#chatbox").html(html); //Insert chat log into the #chatbox div 
                            //Auto-scroll 
                            var newscrollHeight = $("#chatbox")[0].scrollHeight - 20; //Scroll height after the request 
                            if(newscrollHeight > oldscrollHeight){
                                $("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div 
                            }	
                        }
                    });
                }
                setInterval (loadLog, 500);
            });
        </script>
    </body>
</html>

<?php
}
?>