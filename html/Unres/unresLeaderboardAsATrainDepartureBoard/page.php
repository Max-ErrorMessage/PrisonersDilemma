<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Unres Leaderboard as a Train Station Departure Board</title>
  <style>
    body {
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background: #000;
    }
    .image-container {
      position: relative;
      width: 800px;
    }
    .image-container img {
      width: 100%;
      display: block;
    }

    .overlay{
        position:absolute;
        width:483px;
        height:136px;
        left:142px;
        top:135px;
        background-color:rgba(255,0,0,0.6);
        transform: rotate(-1.6deg);
    }

  </style>
</head>
<body>
    <div class="image-container">
        <img src="bg.jpg" alt="Background">
        <div class="overlay">
                Hi
        </div>
    </div>
</body>
</html>