<html>
    <head>
        <link rel="stylesheet" href="loginstyle.css">
    </head>
    <body style="overflow:hidden;background-image:linear-gradient(to bottom right, rgb(150,150,250), rgb(200,200,255))">
        
        <div id = "1" style = "background-color:white;border-radius:100px;width:70px;height:70px;left:20%;top:20%;position:absolute;"></div>
        <div id = "2" style = "background-color:white;border-radius:100px;width:70px;height:70px;left:60%;top:40%;position:absolute;"></div>
        <div id = "3" style = "background-color:white;border-radius:100px;width:70px;height:70px;left:40%;top:80%;position:absolute;"></div>
        <div id = "4" style = "background-color:white;border-radius:100px;width:70px;height:70px;left:80%;top:60%;position:absolute;"></div>
        <div style = "width:120%;height:120%;top:-20px;left:-20px;position:absolute;backdrop-filter: blur(10px);"></div>
        
        <div id = "1" style = "background-color:white;border-radius:100px;width:30px;height:30px;left:10%;top:70%;position:absolute;"></div>
        <div id = "1" style = "background-color:white;border-radius:100px;width:30px;height:30px;left:30%;top:90%;position:absolute;"></div>
        <div id = "1" style = "background-color:white;border-radius:100px;width:30px;height:30px;left:50%;top:10%;position:absolute;"></div>
        <div id = "1" style = "background-color:white;border-radius:100px;width:30px;height:30px;left:50%;top:65%;position:absolute;"></div>
        <div id = "1" style = "background-color:white;border-radius:100px;width:30px;height:30px;left:70%;top:30%;position:absolute;"></div>
        <div id = "1" style = "background-color:white;border-radius:100px;width:30px;height:30px;left:90%;top:50%;position:absolute;"></div>
        
        <div style = "width:120%;height:120%;top:-20px;left:-20px;position:absolute;backdrop-filter: blur(6px);"></div>

        
        <div id = "1" style = "background-color:white;border-radius:100px;width:20px;height:20px;left:5%;top:15%;position:absolute;"></div>
        <div id = "1" style = "background-color:white;border-radius:100px;width:20px;height:20px;left:15%;top:75%;position:absolute;"></div>
        <div id = "1" style = "background-color:white;border-radius:100px;width:20px;height:20px;left:25%;top:35%;position:absolute;"></div>
        <div id = "1" style = "background-color:white;border-radius:100px;width:20px;height:20px;left:35%;top:45%;position:absolute;"></div>
        <div id = "1" style = "background-color:white;border-radius:100px;width:20px;height:20px;left:45%;top:95%;position:absolute;"></div>
        <div id = "1" style = "background-color:white;border-radius:100px;width:20px;height:20px;left:55%;top:65%;position:absolute;"></div>
        <div id = "1" style = "background-color:white;border-radius:100px;width:20px;height:20px;left:65%;top:85%;position:absolute;"></div>
        <div id = "1" style = "background-color:white;border-radius:100px;width:20px;height:20px;left:75%;top:25%;position:absolute;"></div>
        <div id = "1" style = "background-color:white;border-radius:100px;width:20px;height:20px;left:85%;top:55%;position:absolute;"></div>
        <div id = "1" style = "background-color:white;border-radius:100px;width:20px;height:20px;left:95%;top:35%;position:absolute;"></div>
        <div id = "1" style = "background-color:white;border-radius:100px;width:20px;height:20px;left:55%;top:5%;position:absolute;"></div>

        
        <div style = "width:120%;height:120%;top:-20px;left:-20px;position:absolute;backdrop-filter: blur(4px);"></div>

        <div style = "padding:10px;width:80%;height:80%;left:10%;top:10%;position:absolute;background-color: rgba(255,255,255,0.6); backdrop-filter: blur(6px);border-radius: 20px;border-left: 2px white solid;border-bottom: 5px white solid;">
            <div id = "login">
                <?php
                    session_start();
                    if(isset($_SESSION['Error'])){
                        echo "<h2>" . $_SESSION['Error'] . "</h2>";
                    } 
                ?>
                <h1 style = "text-align: center;"> Log in </h1>
                <form style = "text-align: center;" action="/loginphp.php" method = "post">
                    <label for="uname">Username:</label><br>
                    <input type = "text" id = "uname" name="uname"><br>
                    <label for="pword">Password:</label><br>
                    <input type = "password" id = "pword" name="pword"><br><br>
                    <input class = "butt" type="submit" value="Submit">
                </form>
            </div>
            <div id = "signup">
                <?php
                    session_start();
                    if(isset($_SESSION['Error2'])){
                        echo "<h2>" . $_SESSION['Error2'] . "</h2>";
                    } 
                ?>
                <h1 style = "text-align: center;">Sign up </h1>
                <form style = "text-align: center;" action="/signup.php" method = "post">
                    <label for="uname">Username:</label><br>
                    <input type = "text" id = "uname" name="uname"><br>
                    <label for="email">Email address:</label><br>
                    <input type = "text" id = "email" name="email"><br>
                    <label for="pword">Password:</label><br>
                    <input type = "password" id = "pword" name="pword"><br>
                    <label for="pword2">Confirm password:</label><br>
                    <input type = "password" id = "pword2" name="pword2"><br><br>
                    <input class = "butt" type="submit" value="Submit">
                </form>
            </div>
        </div>

        

        
    </body>
</html>